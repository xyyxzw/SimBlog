<?php
/**
 * @author DengPeng <3@dengpeng.cc>
 * @since 2016/12/27
 * @copyright copyright (c) 2016 zixue.it GPL
 * @license http://www.zixue.it/
 */

require './lib/init.php';

// 用户登陆权限鉴定
isAcc();

// 获取当前页码
$curr = isset($_GET['page']) ? trim($_GET['page']) : 1;
list($offset, $step) = getOffset($curr);
// 求出总页数,获得页码数组
$pageNum = mGetOne('select count(*) from cats');
$page = getPage($pageNum, $curr, $step);
$laquo = array_shift($page);
$raquo = array_pop($page);



// (1)拼接查询的SQL语句
$sql = 'select * from cats order by cat_id desc limit '.$offset.','.$step;

// (2)调用封装的MySQL查询函数
$cats = mGetAll($sql);

// (3)加载模板,遍历$cats二维数组
require './view/admin/catlist.html';

?>