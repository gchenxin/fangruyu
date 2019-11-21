<?php

$str = 'a:8:{s:4:"name";s:16:"1元测试套餐";s:4:"type";i:1543992569000;s:4:"item";i:1543992569000;s:5:"began";i:1573108335;s:7:"expired";i:1581057135;s:5:"house";i:39;s:7:"refresh";i:97;s:6:"settop";i:0;}';
var_dump(unserialize($str));
