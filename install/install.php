<?php
/**
 * @author DengPeng <3@dengpeng.cc>
 * @since 2017/01/02
 * @copyright copyright (c) 2017 zixue.it GPL
 * @license http://www.zixue.it/
 */

// 布尔教育-博客系统安装文件

// (1)引入配置文件
$files = '../lib/config.php';

// (2)判断配置文件是否可以修改
if (!is_writable($files)) {
    $status = 'disabled';
    $info = '配置文件config.php不可写,无法进行安装,请确保./lib/config.php文件可写!';
} else {
    $status = 'enable';
    $info = '配置文件config.php可写,可执行安装!';
}

// (3)判断用户是否提交配置信息
if (!empty($_POST)) {
    $cfg_str = '<?php'."\n";
    $cfg_str .= '/**'."\n";
    $cfg_str .= ' * @author DengPeng <3@dengpeng.cc>'."\n";
    $cfg_str .= ' * @since 2016/12/27'."\n";
    $cfg_str .= ' * @copyright copyright (c) 2016 zixue.it GPL'."\n";
    $cfg_str .= ' * @license http://www.zixue.it/'."\n";
    $cfg_str .= ' */'."\n\n";
    $cfg_str .= '/*数据库配置文件*/'."\n\n";
    $cfg_str .= 'return array('."\n";
    $cfg_str .= "\t'host' => '".trim($_POST['host'])."',\t// 数据库地址\n";
    $cfg_str .= "\t'user' => '".trim($_POST['user'])."',\t\t// 数据库用户名\n";
    $cfg_str .= "\t'pwd' => '".trim($_POST['pwd'])."',\t\t\t// 数据库密码\n";
    $cfg_str .= "\t'dbname' => '".trim($_POST['dbname'])."',\t// 数据库名\n";
    $cfg_str .= "\t'charset' => '".trim($_POST['charset'])."',\t// 数据库连接字符集\n";
    $cfg_str .= "\t'salt' => 'itbool',\t\t// 加密用的盐值\n";
    $cfg_str .= ');'."\n\n";
    $cfg_str .= '?>';

    // 打开配置文件写入
    $ff = fopen($files, 'w+');
    fwrite($ff, $cfg_str);

    // (3.1)引入配置文件
    $cfg = require '../lib/config.php';

    // (3.2)创建数据库连接资源
    @$link = mysql_connect($cfg['host'], $cfg['user'], $cfg['pwd']);
    // 判断连接是否成功
    if (!$link) {
        $status = 'disabled';
        $info = '数据库连接失败,请检测配置信息是否正确!';
    } else {
        // 数据库连接成功
        // (3.3)设定字符集
        mysql_query('set names '.$cfg['charset']);
        // (3.4)创建数据库
        mysql_query('create database '.$cfg['dbname'].' default character set utf8');
        // (3.5)选择数据库
        mysql_select_db($cfg['dbname'], $link);
        // (3.6)执行所有表的创建语句
        $sql = array();
        // 创建文章表语句
        $sql[] = "DROP TABLE IF EXISTS `arts`;";
        $sql[] = "CREATE TABLE `arts` (
`art_id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '文章id',
`user_id` int(10) unsigned NOT NULL COMMENT '用户id',
`cat_id` int(10) unsigned NOT NULL COMMENT '栏目id',
`title` char(25) NOT NULL COMMENT '文章标题',
`content` text NOT NULL COMMENT '文章内容',
`thumb` char(60) NOT NULL COMMENT '文章配图缩略图',
`pic` char(60) NOT NULL COMMENT '文章内容配图',
`commentnum` int(10) unsigned NOT NULL COMMENT '文章评论数',
`pubtime` int(10) unsigned NOT NULL COMMENT '文章发表时间',
PRIMARY KEY (`art_id`)
) ENGINE=MyISAM AUTO_INCREMENT=7 DEFAULT CHARSET=utf8 COMMENT='文章表';";
        // 创建栏目表语句
        $sql[] = "DROP TABLE IF EXISTS `cats`;";
        $sql[] = "CREATE TABLE `cats` (
`cat_id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '栏目id',
`catname` char(25) NOT NULL COMMENT '栏目名称',
`artnum` int(10) unsigned NOT NULL COMMENT '该栏目下的文章数',
PRIMARY KEY (`cat_id`)
) ENGINE=MyISAM AUTO_INCREMENT=16 DEFAULT CHARSET=utf8 COMMENT='栏目表';";
        // 创建评论表语句
        $sql[] = "DROP TABLE IF EXISTS `comments`;";
        $sql[] = "CREATE TABLE `comments` (
`comment_id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '评论id',
`art_id` int(10) unsigned NOT NULL COMMENT '文章id',
`user_id` int(10) unsigned NOT NULL COMMENT '用户id',
`nickname` char(25) NOT NULL COMMENT '评论者昵称',
`email` char(25) DEFAULT NULL COMMENT '评论者邮箱',
`content` varchar(100) NOT NULL COMMENT '用户评论内容',
`pubtime` int(10) unsigned NOT NULL COMMENT '发表时间',
`ip` int(10) unsigned NOT NULL COMMENT '评论者ip',
PRIMARY KEY (`comment_id`)
) ENGINE=MyISAM AUTO_INCREMENT=7 DEFAULT CHARSET=utf8 COMMENT='评论表';";
        // 创建标签表语句
        $sql[] = "DROP TABLE IF EXISTS `tags`;";
        $sql[] = "CREATE TABLE `tags` (
`tag_id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '标签id',
`art_id` int(10) unsigned NOT NULL COMMENT '文章id',
`tagname` char(25) NOT NULL COMMENT '标签名',
PRIMARY KEY (`tag_id`)
) ENGINE=MyISAM AUTO_INCREMENT=6 DEFAULT CHARSET=utf8 COMMENT='标签表';";
        // 创建用户表语句
        $sql[] = "DROP TABLE IF EXISTS `users`;";
        $sql[] = "CREATE TABLE `users` (
`user_id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '用户id',
`username` char(25) NOT NULL COMMENT '用户名',
`nickname` char(25) NOT NULL COMMENT '用户昵称',
`email` char(25) NOT NULL COMMENT '注册邮箱',
`salt` char(10) NOT NULL COMMENT '密码的盐',
`password` char(32) NOT NULL COMMENT '登陆密码',
`logintime` int(10) unsigned NOT NULL COMMENT '登陆时间',
PRIMARY KEY (`user_id`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COMMENT='用户表';";
        // 添加管理员语句
        $sql[] = "insert into users (username,nickname,email,salt,password,logintime) values ('admin','鄧芃','3@dengpeng.cc','itbool','c7e61c1ecaaece185d9034ccdac61736','1481869032')";
        // 循环遍历执行SQL语句数组
        foreach ($sql as $v) {
            $bool = mysql_query($v, $link);
            if (!$bool) {
                echo mysql_error($link);
                exit();
            }
        }

        // (3.7)提示用户安装完成
        echo"<script>alert('安装成功!默认管理员帐号：admin 密码：123456');document.location.href='../login.php';</script>";
        // (3.8)修改安装文件名称
        rename('install.php', 'install.lock');
    }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="generator" content="Sublime Text 3114">
    <meta name="author" content="3@dengpeng.cc">
    <meta name="keywords" content="">
    <meta name="description" content="">
    <link rel="stylesheet" href="../css/bootstrap.min.css">
    <link rel="stylesheet" href="../css/site.min.css">
    <title>布尔教育博客系统v1.0-安装程序</title>
</head>
<body>
    <!-- 博客页面标题 -->
    <div class="jumbotron">
        <div class="container">
            <h1>布尔教育博客系统v1.0-安装程序</h1>
            <p class="pull-right">作者:鄧芃 郵箱:3@dengpeng.cc</p>
        </div>
    </div>

    <div class="container">

        <div class="jumbotron col-md-offset-1 col-md-10">
            <!-- 登录表单 -->
            <form class="form-horizontal" role="form" method="post">
                <!-- 提示信息 -->
                <div class="form-group">
                    <label for="inputEmail3" class="col-sm-2 control-label btn-lg"><span class="glyphicon glyphicon-volume-up"></span></label>
                    <div class="col-sm-6">
                        <div class="alert alert-<?php if ($status == 'disabled') {echo 'warning';} else if ($status == 'enable') {echo 'success';} ?>" role="alert">
                            <?php echo $info; ?>
                        </div>
                    </div>
                </div>
                <!-- 数据库主机地址 -->
                <div class="form-group">
                    <label for="inputEmail3" class="col-sm-2 control-label btn-lg"><span class="glyphicon glyphicon-map-marker"></span></label>
                    <div class="col-sm-6">
                        <input type="text" name="host" class="form-control input-lg" id="inputEmail3" placeholder="数据库主机地址" value="localhost">
                    </div>
                </div>
                <!-- 数据库用户名 -->
                <div class="form-group">
                    <label for="inputEmail3" class="col-sm-2 control-label btn-lg"><span class="glyphicon glyphicon-user"></span></label>
                    <div class="col-sm-6">
                        <input type="text" name="user" class="form-control input-lg" id="inputEmail3" placeholder="数据库用户名" value="root">
                    </div>
                </div>
                <!-- 数据库密码 -->
                <div class="form-group">
                    <label for="inputPassword3" class="col-sm-2 control-label btn-lg"><span class="glyphicon glyphicon-lock"></span></label>
                    <div class="col-sm-6">
                        <input type="password" name="pwd" class="form-control input-lg" id="inputPassword3" placeholder="数据库密码">
                    </div>
                </div>
                <!-- 数据库名 -->
                <div class="form-group">
                    <label for="inputPassword3" class="col-sm-2 control-label btn-lg"><span class="glyphicon glyphicon-folder-open"></span></label>
                    <div class="col-sm-6">
                        <input type="text" name="dbname" class="form-control input-lg" id="inputPassword3" placeholder="数据库名" value="blog26">
                    </div>
                </div>
                <!-- 数据库字符集 -->
                <div class="form-group">
                    <label for="inputPassword3" class="col-sm-2 control-label btn-lg"><span class="glyphicon glyphicon-minus"></span></label>
                    <div class="col-sm-6">
                        <select name="charset" id="" class="form-control input-lg">
                            <option value="utf8">utf8</option>
                            <option value="gbk">gbk</option>
                        </select>
                    </div>
                </div>
                <!-- 登陆重置按钮 -->
                <div class="form-group">
                    <div class="col-sm-offset-2 col-sm-10">
                        <button type="submit" class="btn btn-primary">安装</button>
                        <button type="reset" class="btn btn-default">重 置</button>
                    </div>
                </div>
            </form>
        </div>

    </div>

    <!-- 网站底部 -->
    <?php include ('../view/footer.html'); ?>

</body>
</html>