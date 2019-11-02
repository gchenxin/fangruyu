<?php
/**
* @file system_dataClean.php
* @brief 定时清理过期数据
* @author chenxin
* @version v1.0
* @date 2019-10-23
 */

$sql = $dsql->SetQuery("delete from #@__phonebind where now()>expire");
$dsql->dsqlOper($sql,'update');
$sql = $dsql->SetQuery("delete from #@__agentpushrecord where now()>expire");
$dsql->dsqlOper($sql,'update');
$sql = $dsql->SetQuery('delete from #@__callrecord where now()>`date`');
$dsql->dsqlOper($sql,'update');
