<?php

/*
	[UCenter] (C)2001-2008 Comsenz Inc.
	This is NOT a freeware, use is subject to license terms

	$Id: user.php 12126 2008-01-11 09:40:32Z heyond $
*/

!defined('IN_UC') && exit('Access Denied');

class usermodel {

	var $db;
	var $base;

	function usermodel(&$base) {
		$this->base = $base;
		$this->db = $base->db;
	}

	function get_user_by_uid($uid) {
		$arr = $this->db->fetch_first("SELECT * FROM ".UC_DBTABLEPRE."members WHERE uid='$uid'");
		return $arr;
	}

	function get_user_by_username($username) {
		$arr = $this->db->fetch_first("SELECT * FROM ".UC_DBTABLEPRE."members WHERE username='$username'");
		return $arr;
	}

	function check_username($username) {
		$guestexp = '\xA1\xA1|\xAC\xA3|^Guest|^\xD3\xCE\xBF\xCD|\xB9\x43\xAB\xC8';
		$len = strlen($username);
		if($len > 15 || $len < 3 || preg_match("/\s+|^c:\\con\\con|[%,\*\"\s\<\>\&]|$guestexp/is", $username)) {
			return FALSE;
		} else {
			return TRUE;
		}
	}

	function check_mergeuser($username) {
		$data = $this->db->result_first("SELECT count(*) FROM ".UC_DBTABLEPRE."mergemembers WHERE appid='".UC_APPID."' AND username='$username'");
		return $data;
	}

	function check_usernamecensor($username) {
		$_CACHE = $this->base->cache('badwords');
		$censorusername = $this->base->get_setting('censorusername');
		$censorusername = $censorusername['censorusername'];
		$censorexp = '/^('.str_replace(array('\\*', "\r\n", ' '), array('.*', '|', ''), preg_quote(($censorusername = trim($censorusername)), '/')).')$/i';
		$usernamereplaced = $_CACHE['badwords']['findpattern'] ? @preg_replace($_CACHE['badwords']['findpattern'], $_CACHE['badwords']['replace'], $username) : $username;
		if(($usernamereplaced != $username) || ($censorusername && preg_match($censorexp, $username))) {
			return FALSE;
		} else {
			return TRUE;
		}
	}

	function check_usernameexists($username) {
		$data = $this->db->result_first("SELECT username FROM ".UC_DBTABLEPRE."members WHERE username='$username'");
		return $data;
	}

	function check_emailformat($email) {
		return strlen($email) > 6 && preg_match("/^[\w\-\.]+@[\w\-\.]+(\.\w+)+$/", $email);
	}

	function check_emailaccess($email) {
		$setting = $this->base->get_setting(array('accessemail', 'censoremail'));
		$accessemail = $setting['accessemail'];
		$censoremail = $setting['censoremail'];
		$accessexp = '/('.str_replace("\r\n", '|', preg_quote(trim($accessemail), '/')).')$/i';
		$censorexp = '/('.str_replace("\r\n", '|', preg_quote(trim($censoremail), '/')).')$/i';
		if($accessemail || $censoremail) {
			if(($accessemail && !preg_match($accessexp, $email)) || ($censoremail && preg_match($censorexp, $email))) {
				return FALSE;
			} else {
				return TRUE;
			}
		} else {
			return TRUE;
		}
	}

	function check_emailexists($email) {
		$email = $this->db->result_first("SELECT email FROM  ".UC_DBTABLEPRE."members WHERE email='$email'");
		return $email;
	}

	function check_login($username, $password, &$user) {
		$user = $this->get_user_by_username($username);
		if(empty($user['username'])) {
			return -1;
		} elseif($user['password'] != md5(md5($password).$user['salt'])) {
			return -2;
		}
		return $user['uid'];
	}

	function add_user($username, $password, $email, $nickname, $phone, $qq, $sex, $birthday) {
		$regip = $this->base->onlineip;
		$salt = substr(uniqid(rand()), -6);
		$password = md5(md5($password).$salt);
		$sqladd = '';
		$sex = $sex === 0 ? 2 : $sex;
		$birthyear = "";
		$birthmonth = "";
		$birthday = "";
		if(!empty($birthday)){
			$birthday = explode("-", $birthday);
			$birthyear = $birthyear[0];
			$birthmonth = $birthyear[1];
			$birthday = $birthyear[2];
		}
		$sqladd .= $questionid > 0 ? " secques='".$this->quescrypt($questionid, $answer)."'," : " secques='',";
		$this->db->query("INSERT INTO ".UC_DBTABLEPRE."members SET $sqladd username='$username', password='$password', email='$email', regip='$regip', regdate='".$this->base->time."', salt='$salt'");
		$uid = $this->db->insert_id();
		$this->db->query("INSERT INTO ".UC_DBTABLEPRE."memberfields SET uid='$uid'");

		global $cfg_bbsType;
		//discuz
		if($cfg_bbsType == "discuz"){
			$this->db->query("INSERT INTO `".UC_DBNAME."`.".UC_PRE."common_member SET uid='$uid', username='$username', password='$password', email='$email', adminid='0', groupid='10', regdate='".$this->base->time."', credits='0', timeoffset='9999'");
			$this->db->query("INSERT INTO `".UC_DBNAME."`.".UC_PRE."common_member_status SET uid='$uid', regip='$regip', lastip='$regip', lastvisit='".$this->base->time."', lastactivity='".$this->base->time."', lastpost='0', lastsendmail='0'");
			$this->db->query("INSERT INTO `".UC_DBNAME."`.".UC_PRE."common_member_profile SET uid='$uid', realname='$nickname', mobile='$phone', gender='$sex', qq='$qq', birthyear='$birthyear', birthmonth='$birthmonth', birthday='$birthday'");
			$this->db->query("INSERT INTO `".UC_DBNAME."`.".UC_PRE."common_member_field_forum SET uid='$uid'");
			$this->db->query("INSERT INTO `".UC_DBNAME."`.".UC_PRE."common_member_field_home SET uid='$uid'");
			$this->db->query("INSERT INTO `".UC_DBNAME."`.".UC_PRE."common_member_count SET uid='$uid', extcredits1='0', extcredits2='0', extcredits3='0', extcredits4='0', extcredits5='0', extcredits6='0', extcredits7='0', extcredits8='0'");

		//phpwind
		}elseif($cfg_bbsType == "phpwind"){
			$this->db->query("INSERT INTO `".UC_DBNAME."`.pw_user SET uid='$uid', username='$username', password='$password', email='$email', status='0', groupid='0', memberid='8', regdate='".$this->base->time."'");
			$this->db->query("INSERT INTO `".UC_DBNAME."`.pw_user_data SET uid='$uid'");
			$this->db->query("INSERT INTO `".UC_DBNAME."`.pw_user_info SET uid='$uid'");
			$this->db->query("INSERT INTO `".UC_DBNAME."`.pw_windid_user SET uid='$uid', username='$username', email='$email', password='$password', salt='$salt', regdate='".$this->base->time."', regip='$regip'");
			$this->db->query("INSERT INTO `".UC_DBNAME."`.pw_windid_user_data SET uid='$uid'");
			$this->db->query("INSERT INTO `".UC_DBNAME."`.pw_windid_user_info SET uid='$uid'");

		}
		return $uid;
	}

	function edit_user($username, $oldpw, $newpw, $email, $ignoreoldpw = 0) {
		$data = $this->db->fetch_first("SELECT username, uid, password, salt FROM ".UC_DBTABLEPRE."members WHERE username='$username'");

		if($ignoreoldpw) {
			$isprotected = $this->db->result_first("SELECT COUNT(*) FROM ".UC_DBTABLEPRE."protectedmembers WHERE uid = '$data[uid]'");
			if($isprotected) {
				return -8;
			}
		}

		if(!$ignoreoldpw && $data['password'] != md5(md5($oldpw).$data['salt'])) {
			return -1;
		}

		$sqladd = $newpw ? "password='".md5(md5($newpw).$data['salt'])."'" : '';
		$sqladd .= $email ? ($sqladd ? ',' : '')." email='$email'" : '';
		if($sqladd || $emailadd) {
			$this->db->query("UPDATE ".UC_DBTABLEPRE."members SET $sqladd WHERE username='$username'");

			global $cfg_bbsType;
			//discuz
			if($cfg_bbsType == "discuz"){
				$this->db->query("UPDATE `".UC_DBNAME."`.".UC_PRE."common_member SET $sqladd WHERE username='$username'");

			//phpwind
			}elseif($cfg_bbsType == "phpwind"){
				$this->db->query("UPDATE `".UC_DBNAME."`.pw_user SET $sqladd WHERE username='$username'");

			}

			return $this->db->affected_rows();
		} else {
			return -7;
		}
	}

	function delete_user($uidsarr) {
		$uidsarr = (array)$uidsarr;
		$uids = $this->base->implode($uidsarr);
		$arr = $this->db->fetch_all("SELECT uid FROM ".UC_DBTABLEPRE."protectedmembers WHERE uid IN ($uids)");
		$puids = array();
		foreach((array)$arr as $member) {
			$puids[] = $member['uid'];
		}
		$uids = $this->base->implode(array_diff($uidsarr, $puids));
		if($uids) {
			$this->db->query("DELETE FROM ".UC_DBTABLEPRE."members WHERE uid IN($uids)");
			$this->db->query("DELETE FROM ".UC_DBTABLEPRE."memberfields WHERE uid IN($uids)");
			$this->base->load('note');
			$_ENV['note']->add('deleteuser', "ids=$uids");

			global $cfg_bbsType;
			//discuz
			if($cfg_bbsType == "discuz"){
				$this->db->query("DELETE FROM `".UC_DBNAME."`.".UC_PRE."common_member WHERE uid IN($uids)");
				$this->db->query("DELETE FROM `".UC_DBNAME."`.".UC_PRE."common_member_status WHERE uid IN($uids)");
				$this->db->query("DELETE FROM `".UC_DBNAME."`.".UC_PRE."common_member_profile WHERE uid IN($uids)");
				$this->db->query("DELETE FROM `".UC_DBNAME."`.".UC_PRE."common_member_field_forum WHERE uid IN($uids)");
				$this->db->query("DELETE FROM `".UC_DBNAME."`.".UC_PRE."common_member_field_home WHERE uid IN($uids)");
				$this->db->query("DELETE FROM `".UC_DBNAME."`.".UC_PRE."common_member_count WHERE uid IN($uids)");

			//phpwind
			}elseif($cfg_bbsType == "phpwind"){
				$this->db->query("DELETE FROM `".UC_DBNAME."`.pw_user WHERE uid IN($uids)");
				$this->db->query("DELETE FROM `".UC_DBNAME."`.pw_user_data WHERE uid IN($uids)");
				$this->db->query("DELETE FROM `".UC_DBNAME."`.pw_user_info WHERE uid IN($uids)");
				$this->db->query("DELETE FROM `".UC_DBNAME."`.pw_windid_user WHERE uid IN($uids)");
				$this->db->query("DELETE FROM `".UC_DBNAME."`.pw_windid_user_data WHERE uid IN($uids)");
				$this->db->query("DELETE FROM `".UC_DBNAME."`.pw_windid_user_info WHERE uid IN($uids)");

			}

			return $this->db->affected_rows();
		} else {
			return 0;
		}
	}

	function get_total_num($sqladd = '') {
     		$data = $this->db->result_first("SELECT COUNT(*) FROM ".UC_DBTABLEPRE."members $sqladd");
		return $data;
	}

	function get_list($page, $ppp, $totalnum, $sqladd) {
		$start = $this->base->page_get_start($page, $ppp, $totalnum);
		$data = $this->db->fetch_all("SELECT * FROM ".UC_DBTABLEPRE."members $sqladd LIMIT $start, $ppp");
		return $data;
	}

	function name2id($usernamesarr) {
		$usernamesarr = uc_addslashes($usernamesarr, 1, TRUE);
		$usernames = $this->base->implode($usernamesarr);
		$query = $this->db->query("SELECT uid FROM ".UC_DBTABLEPRE."members WHERE username IN($usernames)");
		$arr = array();
		while($user = $this->db->fetch_array($query)) {
			$arr[] = $user['uid'];
		}
		return $arr;
	}

}

?>
