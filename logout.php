<?php
/**
 * @author DengPeng <3@dengpeng.cc>
 * @since 2017/01/02
 * @copyright copyright (c) 2017 zixue.it GPL
 * @license http://www.zixue.it/
 */

// 退出登陆页

setcookie('username', null, 0);
setcookie('itbool', null, 0);

header('location: login.php');

?>