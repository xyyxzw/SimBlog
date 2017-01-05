<?php
/**
 * @author DengPeng <3@dengpeng.cc>
 * @since 2016/12/27
 * @copyright copyright (c) 2016 zixue.it GPL
 * @license http://www.zixue.it/
 */

/*初始化文件*/

error_reporting(E_ALL ^ E_DEPRECATED);

define('ROOT', dirname(__DIR__));

require ROOT.'/lib/mysql.php';
require ROOT.'/lib/functions.php';

$_POST = _addcslashes($_POST);

//判断是否安装
if(file_exists(ROOT."/install/") && !file_exists(ROOT."/install/install.lock")){
    header('Location: install/install.php');
    exit();
}

?>