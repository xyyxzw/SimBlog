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

// (1)判断用户是否提交了数据
if (empty($_POST)) {
    // 用户没有提交数据,展示栏目添加模板
    require './view/admin/catadd.html';
    exit;
}

// (2)用户提交的数据不为空
// 获取用户提交的栏目名称
$catname = isset($_POST['catname']) ? trim($_POST['catname']) : '';

// (3)判断用户提交的栏目名称是否为空
if (empty($catname)) {
    error('栏目名称不能为空');
}

// (4)判断用户提交的栏目名称是否cats表中已经存在
$sql = "select * from cats where catname = '".$catname."'";
$result = mGetRow($sql);
if ($result) {
    // 如果数组为空,说明该栏目存在
    error('你提交的栏目已经存在');
}

// (5)用户提交的栏目cats没有,就将其写入表中
$data = array(
    'catname' => $catname,
);
$bool = mExec('cats', $data);

// (6)判断添加是否成功
if ($bool) {
    succ('栏目添加成功');
} else {
    error('栏目添加失败');
}

?>