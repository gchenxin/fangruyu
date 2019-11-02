<?php if (!defined('HUONIAOINC')) exit('Request Error!');
/**
 * 分表服务类
 * Class SubTable
 */
class SubTable
{
    /**
     * 分表保存的最大记录数
     */
    const MAX_SUBTABLE_COUNT = 100000;

    /**
     * 服务名
     * @var string $service
     */
    private $service = '';

    /**
     * @var dsql $db
     */
    private $db;

    /**
     * 分表字典表
     * @var string $sub_table_list
     */
    private $sub_table_list = '#@__site_sub_tablelist';

    /**
     * 初始表
     * @var string $old_table
     */
    private $old_table;

    /**
     * 最后一张分表
     * @var string $last_table
     */
    private $last_table;

    /**
     * 缓存
     * @var bool
     */
    private $cache;

    /**
     * 要查询的表别名
     * @var string
     */
    private $select_table_alias;

    /**
     * 本次请求所需分表中有效数据总量
     * @var int
     */
    private $othSubEffCountSum;

    /**
     * 本次请求所要查询的分表
     * @var array
     */
    private $selectSubTabs;

    /**
     * 本次请求的where条件，用来查询有效数据
     * @var string
     */
    private $where;

    /**
     * 默认缓存时间
     * @var int
     */
    public $cacheTime = 7200;

    private static $msg = [
        10000 => '没有所要请求的数据',
        10001 => '',
        10002 => '',
    ];

    /**
     * SubTable constructor.
     * @param string $service 服务名
     * @param $oldTable       原始的表
     * @param string $where 获取有效数据的条件
     * @param string $alias 查询主表的别名
     * @param int $open_cache 是否开启缓存
     */
    public function __construct($service = '', $oldTable, $where = '', $alias = '', $open_cache = 0, $cache_time = null)
    {
        global $dsql;
        if (!$service || !$oldTable) return false;
        $this->db                 = $dsql;
        $this->service            = $service;
        $this->old_table          = $oldTable;
        $this->where              = $where;
        $this->select_table_alias = $alias;
        if ($open_cache) {
            global $HN_memory;
            $this->cache = $open_cache == 'redis' && $HN_memory->enable ? $HN_memory : new FileCache();

            if($cache_time != null){
                $this->cacheTime = $cache_time;
            }
        }
    }

    /**
     * 获取所有分表
     * @param string $old_table 初始表
     * @return array
     */
    public function getSubTable()
    {
        if ($this->cache) {
            $cacheRes = $this->cache->get('getSubTable');
            if ($cacheRes !== null && $cacheRes !== false) {
                $this->last_table = $cacheRes[0]['table_name'];
                return $cacheRes;
            }
        }
        $sql = $this->db->SetQuery("SELECT * FROM `" . $this->sub_table_list . "` WHERE `service` = '" . $this->service . "' ");
        $ret = $this->db->dsqlOper($sql, "results");
        $ret = @array_reverse($ret);
        $ret[] = ['service' => $this->service, 'table_name' => $this->old_table];
        $this->last_table = $ret[0]['table_name'];

        if ($this->cache) {
            $this->cache->set('getSubTable', $ret, $this->cacheTime);
        }
        return $ret;
    }

    /**
     * 获取最后一张分表
     * @return string
     */
    public function getLastTable()
    {
        if (!$this->last_table) $this->getSubTable();
        return $this->last_table;
    }

    /**
     * 获取最后一张表有效数据总量
     * @return mixed
     */
    public function getLastTableEffectiveDataNum()
    {
        if ($this->cache) {
            $cacheData = $this->cache->get('getLastTableEffectiveDataNum' . $this->where);
            if ($cacheData !== null && $cacheData !== false) {
                return $cacheData;
            }
        }
        $sql    = $this->db->SetQuery("SELECT COUNT(`id`) count FROM `" . $this->last_table . "` " . $this->select_table_alias . " WHERE `del` = 0 " . $this->where);
        $res = $this->db->dsqlOper($sql, "results");
        $effNum = $res[0]['count'];
        if ($this->cache) {
            $this->cache->set('getLastTableEffectiveDataNum' . $this->where, $effNum, $this->cacheTime);
        }
        return $effNum;
    }

    /**
     * 获取所要查询的分表
     * @param $page 页数
     * @param $pageSize 页数大小
     * @param $addLastTable 是否累加最后一个表，主要为了区分前台接口和后台接口，前台接口需要累加，后台接口如果不累加，数据会重复一条。
     * @return array
     */
    public function getSelectSubTable($page, $pageSize, $addLastTable = 0)
    {
        $subTables  = $this->getSubTable();
        $reqNum     = $page * $pageSize;
        // $lastEffNum = (int)$this->getLastTableEffectiveDataNum();

        // 这里不做验证 最后一张表的数据是否小于要查询的数量，因为当有筛选条件时，最后一张表并没有筛选的结果会导致没有数据
        // if ($reqNum > $lastEffNum) {
            if(!$addLastTable){
            	$this->selectSubTabs[] = $this->last_table;
            }
            $lackEffNum            = $reqNum - $lastEffNum;
            if(count($subTables) > 1){
                unset($subTables[0]);
            }else{
                return array('tables' => '', 'code' => 20000, 'msg' => 'success');
            }

            foreach ($subTables as $k => $table) {
                // if ($this->cache) {
                //     $cacheCount = $this->cache->get($table['table_name'] . '|' . $this->where);
                //     if ($cacheCount !== null && $cacheCount !== false) {
                //         $this->othSubEffCountSum += $cacheCount;
                //     } else {
                //         $sql   = $this->db->SetQuery("SELECT COUNT(`id`) count FROM `" . $table['table_name'] . "` " . $this->select_table_alias . " WHERE `del` = 0");
                //         $res   = $this->db->dsqlOper($sql . $this->where, "results");
                //         $count = $res[0]['count'];
                //         $this->cache->set($table['table_name'] . '|' . $this->where, $count, $this->cacheTime);
                //         $this->othSubEffCountSum += $count;
                //     }
                // } else {
                //     $sql                     = $this->db->SetQuery("SELECT COUNT(`id`) count FROM `" . $table['table_name'] . "` " . $this->select_table_alias . " WHERE `del` = 0");
                //     $res   = $this->db->dsqlOper($sql . $this->where, "results");
                //     $this->othSubEffCountSum += $res[0]['count'];
                // }

                $this->selectSubTabs[] = $table['table_name'];

                //如果做了最后一个表的数据统计大于要查询的数据量，会出现筛选条件的结果在最后一个表中的数量与实际结果不符的问题。
                // if ($this->othSubEffCountSum < $lackEffNum) {
                //     $this->selectSubTabs[] = $table['table_name'];
                // } else {
                //     $this->selectSubTabs[] = $table['table_name'];
                //     break;
                // }

            }
            
            if ($this->othSubEffCountSum == 0) {
                $code = 10000;
                $msg  = self::$msg[$code];
            }
        // }

        return array('tables' => $this->selectSubTabs, 'code' => $code, 'msg' => $msg);
    }

    /**
     * 获取总数（计算分页用）
     * @return int
     */
    public function getReqTotalCount()
    {
        $resCount = 0;
        $subTabs  = $this->getSubTable();
        echo "aaaaa";die;
        if ($this->selectSubTabs) {
            foreach ($subTabs as $subTab) {
                if (!in_array($subTab['table_name'], $this->selectSubTabs)) {
                    if ($this->cache) {
                        $cacheCount = $this->cache->get($subTab['table_name'] . '|' . $this->where);
                        if ($cacheCount !== null && $cacheCount !== false) {
                            $this->othSubEffCountSum += $cacheCount;
                            continue;
                        }
                    }
                    $sql   = $this->db->SetQuery("SELECT COUNT(*) total FROM `" . $subTab['table_name'] . "`" . $this->select_table_alias . " WHERE `del` = 0");
                    $res = $this->db->dsqlOper($sql . $this->where, "results");
                    $count += $res[0]['total'];
                    if ($this->cache) {
                        $this->cache->set($subTab['table_name'] . '|' . $this->where, $count, $this->cacheTime);
                    }
                    $this->othSubEffCountSum += $count;
                }
            }
            $lastEffNum = (int)$this->getLastTableEffectiveDataNum($this->where);
            $resCount   = $this->othSubEffCountSum + $lastEffNum;
        } else {
            foreach ($subTabs as $subTab) {
                if ($this->cache) {
                    $cacheCount = $this->cache->get($subTab['table_name'] . '|' . $this->where);
                    if ($cacheCount !== null && $cacheCount !== false) {
                        $resCount += $cacheCount;
                        continue;
                    }
                }
                $sql      = $this->db->SetQuery("SELECT COUNT(*) total FROM `" . $subTab['table_name'] . "`" . $this->select_table_alias . " WHERE `del` = 0");
                $res = $this->db->dsqlOper($sql . $this->where, "results");
                $count += $res[0]['total'];
                if ($this->cache) {
                    $this->cache->set($subTab['table_name'] . '|' . $this->where, $count, $this->cacheTime);
                }
            }
        }

        return $resCount;
    }

    /**
     * 获取总数
     * @min 分表总数少于min时不缓存结果
     * @return int
     */
    public function getReqTotalCount_v2($sql, $time = 86400, $name='total', $min = 10)
    {
        global $_G;
        if(isset($_G[$this->service]['total_all'][md5($sql)])){
            return $_G[$this->service]['total_all'][md5($sql)];
        }
        $count = 0;
        // $now = time();
        // $base = $GLOBALS['DB_PREFIX'].str_replace('#@__', '', $this->old_table);
        // $full = $base."_all";
        // $service = $this->service;

        // 所有表
        $tab = $this->getSubTable();
        if(count($tab) < $min){
            $ret = $this->db->dsqlOper($sql, "results");
            if($ret){
                $count = $ret[0][$name];
            }
        }else{
            $count = getCache($this->service."_total_all", $sql, $time, array('name' => $name, 'savekey' => 1));
        }
        $_G[$this->service]['total_all'][md5($sql)] = $count;
        return $count;
    }


    /**
     * 查询指定id所在的分表
     * @param $id
     * @return mixed
     */
    public function getSubTableById($id)
    {
        $subTables  = $this->getSubTable();
        $compareArr = array_column($subTables, 'begin_id');
        $index = array_search($id, $compareArr);
        if(is_numeric($index)){
            $table = $subTables[$index]['table_name'];
        } else {
            array_push($compareArr, $id);
            array_push($compareArr, 0);
            sort($compareArr);
            $index           = array_search($id, $compareArr);
            $search_begin_id = $compareArr[$index - 1];
            if ($search_begin_id == 0) {
                $table = $this->old_table;
            } else {
                $table_index = array_search($search_begin_id, array_column($subTables, 'begin_id'));
                $table       = $subTables[$table_index]['table_name'];
            }
        }
        return $table;
    }

    /**
     * 获取分表记录信息
     * @return array
     */
    public function getSubTableList()
    {
        $sql     = $this->db->SetQuery("SELECT * FROM `" . $this->sub_table_list . "` WHERE `service` = " . $this->service);
        $subList = $this->db->dsqlOper($sql, "results");
        return $subList;
    }

    /**
     * 创建分表
     * @param $lastId 上一条记录插入的自增id
     * @return string 新表名
     */
    public function createSubTable($lastId)
    {
        $startId   = $lastId + 1;
        $tableName = $this->old_table . '_' . $startId;
        // $sql       = $this->db->SetQuery("DESC `" . $this->old_table . "`");
        // $ret       = $this->db->dsqlOper($sql, "results");
        // $sql       = $this->db->SetQuery("show keys from `" . $this->old_table . "`");
        // $keysInfo  = $this->db->dsqlOper($sql, "results");
        // $createSql = "CREATE TABLE `$tableName`(";
        // foreach ($ret as $item) {
        //     $Field  = $item['Field'];
        //     $Type   = $item['Type'];
        //     $isNull = $item['Null'] == "NO" ? "NOT NULL" : "";
        //     $Extra  = $item['Extra'];
        //     if (is_null($item['Default'])) {
        //         $Default = '';
        //     } else {
        //         $Default = $item['Default'] == "" ? 'DEFAULT \'\'' : ("DEFAULT " . $item['Default']);
        //     }
        //     $createSql .= "$Field $Type $isNull $Extra $Default , ";
        // }
        // $keyNameAndColumn = [];
        // foreach ($keysInfo as $item) {
        //     if (array_key_exists($item['Key_name'], $keyNameAndColumn)) {
        //         $keyNameAndColumn[$item['Key_name']][] = $item['Column_name'];
        //     } else {
        //         $keyNameAndColumn[$item['Key_name']] = array($item['Column_name']);
        //     }
        // }
        // foreach ($keyNameAndColumn as $key => $value) {
        //     if ($key == 'PRIMARY') {
        //         $createSql .= " PRIMARY KEY (`$value[0]`),";
        //     } else {
        //         $column    = join(',', $value);
        //         $createSql .= " KEY `$key` ($column),";
        //     }
        // }
        // $createSql = substr($createSql, 0, strlen($createSql) - 1);
        // $createSql .= ")ENGINE=MyISAM AUTO_INCREMENT=$startId DEFAULT CHARSET=utf8;";
        // $createSql = $this->db->SetQuery($createSql);
        // $this->db->dsqlOper($createSql, "update");

        $sql = $this->db->SetQuery("show create table #@__articlelist");
        $res = $this->db->dsqlOper($sql, "results");
        $defSql = $res[0]['Create Table'];
        $defSql = str_replace("\r","",$defSql);
        $defSql = str_replace("\n","",$defSql);

        // 创建分表
        $sql = preg_replace("#AUTO_INCREMENT=([0-9]{1,})[ \r\n\t]{1,}#i", "AUTO_INCREMENT=$startId ", $defSql);
        $sql = str_replace($GLOBALS['DB_PREFIX'].'articlelist', "{$tableName}", $sql);
        $sql = $this->db->SetQuery($sql);
        $res = $this->db->dsqlOper($sql, "update");

        $sql = $this->db->SetQuery("SELECT * FROM `".$this->sub_table_list."` WHERE `service` = 'article'");
        $res = $this->db->dsqlOper($sql, "results");
        $un = array();
        $un[] = "`#@__articlelist`";
        foreach ($res as $key => $value) {
            $un[] = "`".$value['table_name']."`";
        }
        $un[] = "`{$tableName}`";

        $sql = $this->db->SetQuery("DROP TABLE IF EXISTS `#@__articlelist_all`");
        $res = $this->db->dsqlOper($sql, "update");

        $sql = preg_replace("#AUTO_INCREMENT=([0-9]{1,})[ \r\n\t]{1,}#i", " ", $defSql);
        $sql = preg_replace("#AUTO_INCREMENT=([0-9]{1,})[ \r\n\t]{1,}#i", "", $sql);
        $sql = str_replace('articlelist', "articlelist_all", $sql);
        $sql = str_replace('ENGINE=MyISAM', 'ENGINE=MRG_MyISAM', $sql);
        $sql .= " UNION=(".join(",", $un).");";
        $sql = $this->db->SetQuery($sql);
        $res = $this->db->dsqlOper($sql, "update");
        // echo $res;die;
        
        $this->saveSubTableToList($tableName, $startId);
        return $tableName;
    }

    /**
     * 保存分表信息
     * @param $tableName
     */
    private function saveSubTableToList($tableName, $startId)
    {
        $sql = $this->db->SetQuery("INSERT INTO `" . $this->sub_table_list . "` (`service`, `table_name`, `begin_id`) VALUES ('" . $this->service . "', '" . $tableName . "', $startId)");
        $this->db->dsqlOper($sql, "update");
    }

}
