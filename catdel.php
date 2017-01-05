<?php
/**
 * @author DengPeng <3@dengpeng.cc>
 * @since 2016/12/27
 * @copyright copyright (c) 2016 zixue.it GPL
 * @license http://www.zixue.it/
 */

/*栏目删除页*/

require './lib/init.php';

// 用户登陆权限鉴定
isAcc();

// (1)获取栏目id
$cat_id = isset($_GET['cat_id']) ? $_GET['cat_id'] : '';

// (2)判断该栏目是否存在
// 拼接sql语句
$sql = "select * from cats where cat_id = '".$cat_id."'";
$result = mGetRow($sql);
if (!$result) {
    // 如果从结果集中取不出东西,返回FALSE
    error('该栏目不存在');
}

// (3)该栏目存在,进行删除
$sql = "delete from cats where cat_id = '".$cat_id."'";
$bool = mQuery($sql);

// (4)判断该栏目是否删除成功
if ($bool) {
    succ('删除成功');
} else {
    error('删除失败');
}

?>