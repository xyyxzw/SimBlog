<?php
/**
 * @author DengPeng <3@dengpeng.cc>
 * @since 2017/01/02
 * @copyright copyright (c) 2017 zixue.it GPL
 * @license http://www.zixue.it/
 */

// 用户登陆页
require './lib/init.php';

// (1)判断用户是否提交了登陆信息
if (empty($_POST)) {
    require './view/admin/login.html';
    exit();
}

// (2)用户提交了登陆信息
// 用户登陆信息
$username = isset($_POST['username']) ? trim($_POST['username']) : '';
$password = isset($_POST['password']) ? trim($_POST['password']) : '';

// (3)用户名和密码都不能为空
if (empty($username) || empty($password)) {
    notFound('用户名和密码都不能为空');
}

// (4)拼接SQL语句查询数据库
$sql = "select * from users where username = '".$username."'";
$user = mGetRow($sql);
// 判断该用户是否存在
if (empty($user)) {
    notFound('对不起该用户名不存在');
}

$cfg = require ROOT.'/lib/config.php';
$pwd = md5($cfg['salt'].$password);
// 判断用户提交的密码跟盐值md5后是否跟数据库中的密码一致
if ($pwd != $user['password']) {
    notFound('密码不正确');
}

// (5)用户名和密码校验都正确,设置Cookie
setcookie('username', $username);
$cookie = md5($cfg['salt'].$username);
setcookie('itbool', $cookie);

header('location: artlist.php');

?>