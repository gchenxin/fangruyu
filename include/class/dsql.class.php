<?php   if(!defined('HUONIAOINC')) exit('Request Error!');
/**
 * 数据库操作类
 *
 * @version        $Id: dsql.class.php 2013-7-13 下午18:04:40 $
 * @package        HuoNiao.class
 * @copyright      Copyright (c) 2013 - 2018, HuoNiao, Inc.
 * @link           https://www.ihuoniao.cn/
 */

class dsql extends db_connect{
    /**
	 * 保存或生成一个DB对象，设定盐的长度
	 *
	 * @param object $db 数据库对象
	 * @param int $saltLength 密码盐的长度
	 */
    public $querynum = 0;  //查询的次数
    public $querytime = 0;  //查询的时间

    public $querysql = "";

    function __construct($db=NULL){
		parent::__construct($db);
    }

    function dsql(){
        $this->__construct();
    }

	/**
	 * 取得数据库的表信息
	 * @access function
	 * @return array
	 */
	function getTables() {
		try{
			$stmt = $this->db->prepare("SHOW TABLES");

			$stmt->execute();
			$results = $stmt->fetchAll(PDO::FETCH_NUM);
			$stmt->closeCursor();

			$tabs = array();
			foreach($results as $tab => $tbname){
				$state = $this->getTableState("SHOW TABLE STATUS LIKE '%s'", $tbname[0]);
				$tabs[$tab]['name'] = $tbname[0];
				$tabs[$tab]['Rows'] = $state[0]['Rows'];
				$tabs[$tab]['Data_length'] = sizeformat($state[0]['Data_length']);
				$tabs[$tab]['Comment'] = $state[0]['Comment'];
			}
			return $tabs;

		}catch(Exception $e){
			die($e->getMessage());
		}
	}

	/**
	 * 取得数据表的详细信息
	 * @access function
	 * @return array
	 */
	function getTableState($sql, $table = ''){
		$sql = sprintf($sql, $table);
		$stmt = $this->db->prepare($sql);
		$stmt->execute();
		$results = $stmt->fetchAll(PDO::FETCH_ASSOC);
		$stmt->closeCursor();
		return $results;
	}

	/**
	 * 取得表字段
	 * @access function
	 * @return array
	 */
	function getTableFields($table = ''){
		$stmt = $this->db->prepare("SELECT * FROM `".$table."` LIMIT 1");
		$stmt->execute();
		$fields = array();
		for($i=0; $i<$stmt->columnCount(); $i++) {
			$meta = $stmt->getColumnMeta($i);
    		array_push($fields, $meta['name']);
  		}
		return $fields;
	}

	/**
	 * 优化所有表
	 * @access function
	 * @return string
	 */
	function optimizeAllTables() {
		try{
			$stmt = $this->db->prepare("SHOW TABLES");

			$stmt->execute();
			$results = $stmt->fetchAll(PDO::FETCH_NUM);
			$stmt->closeCursor();

			foreach($results as $tab => $tbname){
				$this->optimizeTables($tbname[0]);
			}
			return json_encode("优化成功！");

		}catch(Exception $e){
			die($e->getMessage());
		}
	}

	/**
	 * 优化表
	 *
	 * @param string $tables table1,table2,table3....
	 * @return tables
	 */
	public function optimizeTables($table) {
		$sql = sprintf('OPTIMIZE TABLE %s', $table);
		$stmt = $this->db->prepare($sql);
		$stmt->execute();
		$results = $stmt->fetchAll(PDO::FETCH_ASSOC);
		$stmt->closeCursor();
		return $results;
	}

	/**
	 * 修复所有表
	 * @access function
	 * @return string
	 */
	function repairAllTables() {
		try{
			$stmt = $this->db->prepare("SHOW TABLES");

			$stmt->execute();
			$results = $stmt->fetchAll(PDO::FETCH_NUM);
			$stmt->closeCursor();

			foreach($results as $tab => $tbname){
				$this->repairTables($tbname[0]);
			}
			return json_encode("修复成功！");

		}catch(Exception $e){
			die($e->getMessage());
		}
	}

	/**
	 * 修复表
	 *
	 * @param string $tables table1,table2,table3....
	 * @return tables
	 */
	public function repairTables($table) {
		$sql = sprintf('REPAIR TABLE %s EXTENDED', $table);
		$stmt = $this->db->prepare($sql);
		$stmt->execute();
		$results = $stmt->fetchAll(PDO::FETCH_ASSOC);
		$stmt->closeCursor();
		return $results;
	}


    /**
     *  获取指定ID的分类
     *
     * @param     int    $id  大类ID
     * @return    array
     */
    function getOptionList($id=0, $action){
		$sql = $this->SetQuery("SELECT `id`, `typename` FROM `#@__".$action."type` WHERE `parentid` = $id ORDER BY 'weight'");
		try{
			$stmt = $this->db->prepare($sql);

			if(!empty($id)){
				$stmt->bindParam(":id", $id, PDO::PARAM_INT);
			}

			$stmt->execute();
			$results = $stmt->fetchAll(PDO::FETCH_ASSOC);
			$stmt->closeCursor();

			return $results;

		}catch(Exception $e){
			die($e->getMessage());
		}
    }

	/**
     *  遍历所有分类
     *
     * @return    array
     */
	function getTypeList($id=0, $tab, $son = true, $page = 1, $pageSize = 100000, $cond = "", $more = "", $hideSameCity = false){
		$page = empty($page) ? 1 : $page;
		$pageSize = empty($pageSize) ? 1000 : $pageSize;
		$atpage = $pageSize*($page-1);
		$where = " LIMIT $atpage, $pageSize";
		$return = array();

		$id = (int)$id;
		$page = (int)$page;
		$pageSize = (int)$pageSize;

		$sql = $this->SetQuery("SELECT * FROM `#@__".$tab."` WHERE `parentid` = $id".$cond." ORDER BY `weight`".$where);

		try{
			$stmt = $this->db->prepare($sql);

			$stmt->execute();
			$results = $stmt->fetchAll(PDO::FETCH_ASSOC);
			$count   = $stmt->rowCount();
			$stmt->closeCursor();

			if($results && $count > 0){//如果有子类

                //如果是获取区域
                global $cfg_sameAddr_state;
                $siteCityArr = array();
                if($id && $tab == 'site_area' && $hideSameCity && !$cfg_sameAddr_state){
                    $siteConfigService = new siteConfig();
                    $siteCity = $siteConfigService->siteCity();

                    foreach ($siteCity as $key => $val){
                        array_push($siteCityArr, $val['cityid']);
                    }
                }

                $kk = 0;
				foreach($results as $k => $v){

                    if($siteCityArr && in_array($v['id'], $siteCityArr)){
                        continue;
                    }

					$return[$kk]['id']       = $v['id'];
					$return[$kk]['parentid'] = $v['parentid'];
					$return[$kk]['typename'] = $v['typename'];
                    $return[$kk]["level"] = $v['level'];
                    $return[$kk]['longitude'] = $v['longitude'];
                    $return[$kk]['latitude'] = $v['latitude'];

					if(isset($v['icon'])){
                        $return[$kk]['iconturl'] = empty($v['icon']) ? '' : getFilePath($v['icon']);
						$return[$kk]['icon'] = defined('HUONIAOADMIN') ? $v['icon'] : $return[$kk]['iconturl'];
					}
					// 返回更多字段
					if($more){
						$moreArr = explode(",", $more);
						foreach ($moreArr as $m_v) {
							if(isset($v[$m_v])){
								$return[$kk][$m_v] = $v[$m_v];
							}
						}
					}

					//区域或地铁信息不需要链接地址
					if(!strpos($tab, "addr") && !strpos($tab, "subway") && !strpos($tab, "site_area")){

            $par = array(
    					"service"     => preg_replace("/_?type/", "", preg_replace("/_?news/", "", preg_replace("/_?newstype/", "", preg_replace("/_?brandtype/", "", $tab)))),
    					"template"    => "list",
    					"typeid"      => $v['id']
    				);

						$return[$kk]["url"]    = getUrlPath($par);
					}

					//区域需要把城市天气ID和城市拼音输出
					if($tab == "site_area"){
						$return[$kk]["pinyin"] = $v['pinyin'];
						$return[$kk]["weather_code"] = $v['weather_code'];
					}

          //新闻、图片特殊字段【拼音、拼音首字母】
					if($tab == "car_brandtype" || $tab == "articletype" || $tab == "imagetype"){
						$return[$kk]["pinyin"] = $v['pinyin'];
						$return[$kk]["py"] = $v['py'];
					}

					//团购特殊用法【热门、文字颜色】
					if($tab == "tuantype" || $tab == "tuanaddr"){
						$return[$kk]['hot'] = $v['hot'];
						if($tab != "tuanaddr"){
							$return[$kk]['color'] = $v['color'];
						}
					}


					//房产特殊用法【区域坐标】
					if($tab == "houseaddr"){
						$return[$kk]['longitude'] = $v['longitude'];
						$return[$kk]['latitude'] = $v['latitude'];
					}


					if($son){
                        if($son === 'once') $son = false;
						$return[$kk]["lower"] = $this->getTypeList($v['id'], $tab, $son, 1, 100000, $cond, $more);
					}else{
						$sql = $this->SetQuery("SELECT `id` FROM `#@__".$tab."` WHERE `parentid` = ".$v['id']);
						$stmt = $this->db->prepare($sql);
						$stmt->execute();
						$count   = $stmt->rowCount();
						$stmt->closeCursor();
						if($count > 0){
							$return[$kk]["lower"] = $count;
						}
					}
					$kk++;
				}

				return $return;
			}else{
				return "";
			}

		}catch(Exception $e){
		    return array(
		        'state' => 200,
                'info' => '分类获取失败！'
            );
//			return '{"state": 200, "info": "分类获取失败！"}';
		}

	}

	/**
     *  获取分类名称
     *
     * @access    public
     * @param     int    $id  大类ID
     * @return    array
     */
    function getTypeName($sql){
		try{
			$stmt = $this->db->prepare($sql);
			$stmt->execute();
			$results = $stmt->fetchAll(PDO::FETCH_ASSOC);
			$stmt->closeCursor();

			return $results;

		}catch(Exception $e){
			die($e->getMessage());
		}
    }

	/**
     * 执行SQL
     *
	 * @param     string $sql 要操作的sql语句
     * @return    json
     */
	public function dsqlOper($sql, $type, $fetch = "ASSOC"){
		try{
            $s = microtime(true);
			$stmt = $this->db->prepare($sql);
			$stmt->execute();
            $t = number_format((microtime(true) - $s), 6);
            
			$this->querynum++;
            if($t >= ($_GET['t'] ? $_GET['t'] : 0.5)){
                $time = '<span style="color:red;">'.$t.' s/per'.'</span>';
            }else{
                $time = '<span style="color:green;">'.$t.' s/per'.'</span>';
            }
			$this->querysql .= $sql.";{$time}<br />";

            $sql_ = $sql;

			//如果是更新会员表，则删除会员缓存
            global $DB_PREFIX;
            global $HN_memory;
            $sql = strtolower($sql);
            $sql_ = $sql;
            $sql = str_replace(" ", "", $sql);
            $sql = str_replace("`", "", $sql);
            $sql = str_replace("'", "", $sql);

            if(strstr($sql, "update") && strstr($sql, "memberset")){
                $strArr = explode("where", $sql);
                if(strstr($strArr[1], 'id=')){
                    $strArr2 = explode('=', $strArr[1]);
                    $lid = $strArr2[1];
                    $HN_memory->rm('member_'. $lid);
                // update条件不是id：member_cleanExpired.php member_cleanOnline.php
                }else{
                    $strArr_ = explode("where", $sql_);
                    $sql2 = $this->SetQuery("SELECT `id` FROM `#@__member` WHERE ".$strArr_[1]);
                    $stmt2 = $this->db->prepare($sql2);
                    $stmt2->execute();
                    $res_2 = $stmt2->fetchAll(PDO::FETCH_ASSOC);
                    $stmt2->closeCursor();
                    if($res_2){
                        foreach ($res_2 as $k => $v) {
                            $HN_memory->rm('member_'. $v['id']);
                        }
                    }
                }
            }
            // if(strstr(strtolower($sql), 'update `'.$DB_PREFIX.'member`')){
            //     $strArr = explode(' ', $sql);
            //     $lid = $strArr[count($strArr)-1];
            //     $HN_memory->rm('member_'. $lid);
            // }

            $res = "";

			//最后一次插入的ID
			if($type == "lastid"){
				// return $this->db->lastInsertId();
                $res = $this->db->lastInsertId();

			//总条数
			}else if($type == "totalCount"){
                // return $stmt->rowCount();
				$res = $stmt->rowCount();

			//数据列表
			}else if($type == "results"){
				if($fetch == "ASSOC"){
                    // return $stmt->fetchAll(PDO::FETCH_ASSOC);
					$res = $stmt->fetchAll(PDO::FETCH_ASSOC);
				}elseif($fetch == "NUM"){
                    // return $stmt->fetchAll(PDO::FETCH_NUM);
					$res = $stmt->fetchAll(PDO::FETCH_NUM);
				}

			//更新数据
			}else if($type == "update"){
                // return "ok";
				$res = "ok";
			}

			$stmt->closeCursor();

            // return $res;

            $this->querytime += $t;
            if($t > 1){
                // echo '<p style="color:red;">'.$sql_.";&nbsp;&nbsp;&nbsp;".$t." s</p>";
            }else{
                // echo '<p>'.$sql_."; ".$t." s</p>";
            }

            return $res;

		}catch(Exception $e){
//			$log_file = HUONIAODATA.'/checkSql_safe.txt';
//			$time = date("Y-m-d H:i:s", time());
			// fputs(fopen($log_file,'a+'),"\r\n".$time." sql操作失败语句：".$sql."\n\r");
            if(HUONIAOBUG){
                return '{"state": 200, "info": '.json_encode($e->getMessage()).'}';
            }else {
                return '{"state": 200, "info": "操作失败！"}';
            }
		}
	}


    /**
     * 获取数据库的版本信息
     * @return array
     */
    public function getDriverVersion(){
        return $this->db->getAttribute(PDO::ATTR_SERVER_VERSION);
    }


    /**
     * 获取数据库的大小尺寸
     * @return array
     */
    public function getDriverSize(){
        return $this->db->getAttribute(PDO::ATTR_PERSISTENT);
    }


    public function my_sort($arrays, $sort_key, $sort_order = SORT_ASC, $sort_type = SORT_NUMERIC){
        if(is_array($arrays)){
            foreach ($arrays as $array){
                if(is_array($array)){
                    $key_arrays[] = $array[$sort_key];
                }else{
                    return false;
                }
            }
        }else{
            return false;
        }
        array_multisort($key_arrays,$sort_order,$sort_type,$arrays);
        return $arrays;
    }

    public function versionCompare($versionA,$versionB) {
        if ($versionA>2147483646 || $versionB>2147483646) {
            throw new Exception('版本号,位数太大暂不支持!','101');
        }
        $dm = '.';
        $verListA = explode($dm, (string)$versionA);
        $verListB = explode($dm, (string)$versionB);

        $len = max(count($verListA),count($verListB));
        $i = -1;
        while ($i++<$len) {
            $verListA[$i] = intval(@$verListA[$i]);
            if ($verListA[$i] <0 ) {
                $verListA[$i] = 0;
            }
            $verListB[$i] = intval(@$verListB[$i]);
            if ($verListB[$i] <0 ) {
                $verListB[$i] = 0;
            }

            if ($verListA[$i]>$verListB[$i]) {
                return 1;
            } else if ($verListA[$i]<$verListB[$i]) {
                return -1;
            } else if ($i==($len-1)) {
                return 0;
            }
        }

    }

    public function compare_database($new, $old){
      $diff = array('table' => array(), 'field' => array(), 'index' => array());
      //table
      foreach ($old['table'] as $table_name => $table_detail) {
        if (!isset($new['table'][$table_name])){
          $diff['table']['drop'][$table_name] = $table_name; //删除表
        }
      }
      foreach ($new['table'] as $table_name => $table_detail) {
        if (!isset($old['table'][$table_name])) {
          //新建表
          $diff['table']['create'][$table_name] = $table_detail;
          $diff['field']['create'][$table_name] = $new['field'][$table_name];
          $diff['index']['create'][$table_name] = $new['index'][$table_name];
        } else {
          //对比表
          $old_detail = $old['table'][$table_name];
          $change = array();
          if ($table_detail['Engine'] !== $old_detail['Engine'])
            $change['Engine'] = $table_detail['Engine'];
          if ($table_detail['Row_format'] !== $old_detail['Row_format'])
            $change['Row_format'] = $table_detail['Row_format'];
          if ($table_detail['Collation'] !== $old_detail['Collation'])
            $change['Collation'] = $table_detail['Collation'];
          //if($table_detail['Create_options']!=$old_detail['Create_options'])
          //	$change['Create_options']=$table_detail['Create_options'];
          if ($table_detail['Comment'] !== $old_detail['Comment'])
            $change['Comment'] = $table_detail['Comment'];
          if (!empty($change))
            $diff['table']['change'][$table_name] = $change;
        }
      }

      //index
      foreach ($old['index'] as $table => $indexs) {
        if (isset($new['index'][$table])) {
          $new_indexs = $new['index'][$table];
          foreach ($indexs as $index_name => $index_detail) {
            if (!isset($new_indexs[$index_name])) {
              //索引不存在，删除索引
              $diff['index']['drop'][$table][$index_name] = $index_name;
            }
          }
        } else {
          if (!isset($diff['table']['drop'][$table])) {
            foreach ($indexs as $index_name => $index_detail) {
              $diff['index']['drop'][$table][$index_name] = $index_name;
            }
          }
        }
      }
      foreach ($new['index'] as $table => $indexs) {
        if (isset($old['index'][$table])) {
          $old_indexs = $old['index'][$table];
          foreach ($indexs as $index_name => $index_detail) {
            if (isset($old_indexs[$index_name])) {
              //存在，对比内容
              if ($index_detail['Non_unique'] !== $old_indexs[$index_name]['Non_unique'] || $index_detail['Column_name'] !== $old_indexs[$index_name]['Column_name'] || $index_detail['Collation'] !== $old_indexs[$index_name]['Collation'] || $index_detail['Index_type'] !== $old_indexs[$index_name]['Index_type']) {
                $diff['index']['drop'][$table][$index_name] = $index_name;
                $diff['index']['add'][$table][$index_name] = $index_detail;
              }
            } else {
              //不存在，新建索引
              $diff['index']['add'][$table][$index_name] = $index_detail;
            }
          }
        } else {
          if (!isset($diff['table']['create'][$table])) {
            foreach ($indexs as $index_name => $index_detail) {
              $diff['index']['add'][$table][$index_name] = $index_detail;
            }
          }
        }
      }

      //fields
      foreach ($old['field'] as $table => $fields) {
        if (isset($new['field'][$table])) {
          $new_fields = $new['field'][$table];
          foreach ($fields as $field_name => $field_detail) {
            if (!isset($new_fields[$field_name])) {
              //字段不存在，删除字段
              $diff['field']['drop'][$table][$field_name] = $field_detail;
            }
          }
        } else {
          //旧数据库中的表在新数据库中不存在，需要删除
        }
      }
      foreach ($new['field'] as $table => $fields) {
        if (isset($old['field'][$table])) {
          $old_fields = $old['field'][$table];
          $last_field = '';
          foreach ($fields as $field_name => $field_detail) {
            if (isset($old_fields[$field_name])) {
              //字段存在，对比内容
              if ($field_detail['Type'] !== $old_fields[$field_name]['Type'] || $field_detail['Collation'] !== $old_fields[$field_name]['Collation'] || $field_detail['Null'] !== $old_fields[$field_name]['Null'] || $field_detail['Default'] !== $old_fields[$field_name]['Default'] || $field_detail['Extra'] !== $old_fields[$field_name]['Extra'] || $field_detail['Comment'] !== $old_fields[$field_name]['Comment']) {
                  $diff['field']['change'][$table][$field_name] = $field_detail;
              }
            } else {
              //字段不存在，添加字段
              $field_detail['After'] = $last_field;
              $diff['field']['add'][$table][$field_name] = $field_detail;
            }
            $last_field = $field_name;
          }
        } else {
            //新数据库中的表在旧数据库中不存在，需要新建
        }
      }

      return $diff;
    }

    public function get_db_detail($server, $username, $password, $database, &$errors = array()){
      $connection = @mysqli_connect($server, $username, $password);
      if($connection === false){
        $errors[] = '无法连接数据库！' . mysqli_connect_error();
        return false;
      }
      $serverset = 'character_set_connection=utf8, character_set_results=utf8, character_set_client=binary';
      $serverset .= @mysqli_get_server_info($connection) > '5.0.1' ? ', sql_mode=\'\'' : '';
      @mysqli_query($connection, "SET $serverset");
      if(!@mysqli_select_db($connection,$database)){
        $errors[] = '无法使用数据库！';
        @mysqli_close($connection);
        return false;
      }

      $detail = array('table' => array(), 'field' => array(), 'index' => array());
      $tables = $this->query($connection, "show table status");
      if($tables){
        foreach ($tables as $key_table => $table) {
          if(!strstr($table['Name'], $GLOBALS['DB_PREFIX']) || $table['Engine'] != 'MyISAM') continue;
          $detail['table'][str_replace($GLOBALS['DB_PREFIX'], '#@__', $table['Name'])] = str_replace($GLOBALS['DB_PREFIX'], '#@__', $table);
          //字段
          $fields = $this->query($connection, "show full fields from `" . $table['Name'] . "`");
          if ($fields) {
            foreach ($fields as $key_field => $field) {
              $fields[$field['Field']] = $field;
              unset($fields[$key_field]);
            }
            $detail['field'][str_replace($GLOBALS['DB_PREFIX'], '#@__', $table['Name'])] = $fields;
          } else {
            $errors[] = '无法获得表的字段:' . $database . ':' . $table['Name'];
          }
          //索引
          $indexes = $this->query($connection, "show index from `" . $table['Name'] . "`");
          if ($indexes) {
            foreach ($indexes as $key_index => $index) {
              $indexes[$index['Key_name']]['Table'] = str_replace($GLOBALS['DB_PREFIX'], '#@__', $index['Table']);
              if (!isset($indexes[$index['Key_name']])) {
                $index['Column_name'] = array($index['Seq_in_index'] => $index['Column_name']);
                $indexes[$index['Key_name']] = $index;
              } else{
                $indexes[$index['Key_name']]['Column_name'][$index['Seq_in_index']] = $index['Column_name'];
              }
              unset($indexes[$key_index]);
            }
            $detail['index'][str_replace($GLOBALS['DB_PREFIX'], '#@__', $table['Name'])] = $indexes;
          } else {
            //$errors[]='无法获得表的索引信息:'.$database.':'.$table['Name'];
            $detail['index'][str_replace($GLOBALS['DB_PREFIX'], '#@__', $table['Name'])] = array();
          }
        }
        @mysqli_close($connection);
        return $detail;
      } else {
        $errors[] = '无法获得数据库的表详情！';
        @mysqli_close($connection);
        return false;
      }
    }

    public function query($connection, $sql){
        if ($connection) {
            $result = @mysqli_query($connection,$sql);
            if ($result) {
                $result_a = array();
                while ($row = @mysqli_fetch_assoc($result))
                    $result_a[] = $row;
                return $result_a;
            }
        }
        return false;
    }

    public function build_query($diff){
        $sqls = array();
        if ($diff) {
            if (isset($diff['table']['drop'])) {
                foreach ($diff['table']['drop'] as $table_name => $table_detail) {
                    $sqls[] = "DROP TABLE `{$table_name}`";
                }
            }
            if (isset($diff['table']['create'])) {
                foreach ($diff['table']['create'] as $table_name => $table_detail) {
                    $fields = $diff['field']['create'][$table_name];
                    $sql = "CREATE TABLE `$table_name` (";
                    $t = array();
                    $k = array();
                    foreach ($fields as $field) {
                        $t[] = "`{$field['Field']}` " . strtoupper($field['Type']) . $this->sqlnull($field['Null']) . $this->sqldefault($field['Default']) . $this->sqlextra($field['Extra']) . $this->sqlcomment($field['Comment']);
                    }
                    if (isset($diff['index']['create'][$table_name]) && !empty($diff['index']['create'][$table_name])) {
                        $indexs = $diff['index']['create'][$table_name];
                        foreach ($indexs as $index_name => $index_detail) {
                            if ($index_name == 'PRIMARY')
                                $k[] = "PRIMARY KEY (`" . implode('`,`', $index_detail['Column_name']) . "`)";
                            else
                                $k[] = ($index_detail['Non_unique'] == 0 ? "INDEX" : "INDEX") . "`$index_name`" . " (`" . implode('`,`', $index_detail['Column_name']) . "`)";
                        }
                    }
                    list($charset) = explode('_', $table_detail['Collation']);
                    $sql .= implode(', ', $t) . (!empty($k) ? ',' . implode(', ', $k) : '') . ') ENGINE = ' . $table_detail['Engine'] . ' DEFAULT CHARSET = ' . $charset;
                    $sqls[] = $sql;
                }
            }
            if (isset($diff['table']['change'])) {
                foreach ($diff['table']['change'] as $table_name => $table_changes) {
                    if (!empty($table_changes)) {
                        $sql = "ALTER TABLE `$table_name`";
                        foreach ($table_changes as $option => $value) {
                            if ($option == 'Collation') {
                                list($charset) = explode('_', $value);
                                $sql .= " DEFAULT CHARACTER SET $charset COLLATE $value";
                            } else{
                                if(strtoupper($option) == 'COMMENT'){
                                  $sql .= " " . strtoupper($option) . " = '$value' ";
                                }else{
                                  $sql .= " " . strtoupper($option) . " = $value ";
                                }
                            }
                        }
                        $sqls[] = $sql;
                    }
                }
            }
            if (isset($diff['index']['drop'])) {
                foreach ($diff['index']['drop'] as $table_name => $indexs) {
                    foreach ($indexs as $index_name => $index_detail) {
                        if ($index_name == 'PRIMARY')
                            $sqls[] = "ALTER TABLE `$table_name` DROP PRIMARY KEY";
                        else
                            $sqls[] = "ALTER TABLE `$table_name` DROP INDEX `$index_name`";
                    }
                }
            }
            if (isset($diff['field']['drop'])) {
                foreach ($diff['field']['drop'] as $table_name => $fields) {
                    foreach ($fields as $field_name => $field_detail) {
                        $sqls[] = "ALTER TABLE `$table_name` DROP `$field_name`";
                    }
                }
            }
            if (isset($diff['field']['add'])) {
                foreach ($diff['field']['add'] as $table_name => $fields) {
                    foreach ($fields as $field_name => $field_detail) {
                        $sqls[] = "ALTER TABLE `$table_name` ADD `{$field_name}` " . strtoupper($field_detail['Type']) . $this->sqlcol($field_detail['Collation']) . $this->sqlnull($field_detail['Null']) . $this->sqldefault($field_detail['Default']) . $this->sqlextra($field_detail['Extra']) . $this->sqlcomment($field_detail['Comment']) . " AFTER `{$field_detail['After']}`";
                    }
                }
            }
            if (isset($diff['index']['add'])) {
                foreach ($diff['index']['add'] as $table_name => $indexs) {
                    foreach ($indexs as $index_name => $index_detail) {
                        if ($index_name == 'PRIMARY')
                            $sqls[] = "ALTER TABLE `$table_name` ADD PRIMARY KEY (`" . implode('`,`', $index_detail['Column_name']) . "`)";
                        else
                            $sqls[] = "ALTER TABLE `$table_name` ADD" . ($index_detail['Non_unique'] == 0 ? " INDEX " : " INDEX ") . "`$index_name`" . " (`" . implode('`,`', $index_detail['Column_name']) . "`)";
                    }
                }
            }
            if (isset($diff['field']['change'])) {
                foreach ($diff['field']['change'] as $table_name => $fields) {
                    foreach ($fields as $field_name => $field_detail) {
                        $sqls[] = "ALTER TABLE `$table_name` CHANGE `{$field_name}` `{$field_name}` " . strtoupper($field_detail['Type']) . $this->sqlcol($field_detail['Collation']) . $this->sqlnull($field_detail['Null']) . $this->sqldefault($field_detail['Default']) . $this->sqlextra($field_detail['Extra']) . $this->sqlcomment($field_detail['Comment']);
                    }
                }
            }
        }

        return $sqls;
    }

    public function sqlkey($val){
      switch ($val) {
          case 'PRI':
              return ' PRIMARY';
          case 'UNI':
              return ' UNIQUE';
          case 'MUL':
              return ' INDEX';
          default:
              return '';
      }
    }

    public function sqlcol($val){
        switch ($val) {
            case null:
                return '';
            default:
                list($charset) = explode('_', $val);
                return ' CHARACTER SET ' . $charset . ' COLLATE ' . $val;
        }
    }

    public function sqldefault($val){
        if($val===null){
            return '';
        }else{
            return " DEFAULT '" . stripslashes($val) . "'";
        }
    }

    public function sqlnull($val){
        switch ($val) {
            case 'NO':
                return ' NOT NULL';
            case 'YES':
                return ' NULL';
            default:
                return '';
        }
    }

    public function sqlextra($val){
        switch ($val) {
            case '':
                return '';
            default:
                return ' ' . strtoupper($val);
        }
    }

    public function sqlcomment($val){
        switch ($val) {
            case '':
                return '';
            default:
                return " COMMENT '" . stripslashes($val) . "'";
        }
    }

}
