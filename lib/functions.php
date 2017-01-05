<?php
/**
 * @author DengPeng <3@dengpeng.cc>
 * @since 2016/12/27
 * @copyright copyright (c) 2016 zixue.it GPL
 * @license http://www.zixue.it/
 */

/**
 * 用户登陆权限鉴定
 * 2017-01-02T23:32:36+0800
 */
function isAcc()
{
    $cfg = require ROOT.'/lib/config.php';
    $username = $_COOKIE['username'];
    $cookie = md5($cfg['salt'].$username);
    if ($cookie != $_COOKIE['itbool']) {
        // 如果用户没有登陆
        header('location: login.php');
    }
}

/**
 * 前台报错信息模板
 * @param  string $msg 提示信息
 * 2017-01-02T23:07:46+0800
 */
function notFound($msg='对不起,你查找的文章去火星了,有问题请联系站长3@dengpeng.cc')
{
    require './view/front/404.html';
    exit;
}


/**
 * 获取用户的真实ip地址
 * @return int ip2long转换的ip地址
 * 2017-01-02T22:58:54+0800
 */
function getRealIp()
{
    $ip = '';
    // 判断服务器是否开启了超全局变量数组权限
    if (isset($_SERVER)) {
        // 判断用户是否使用代理
        if (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
        } else if (isset($_SERVER['HTTP_CLIENT_IP'])) {
            // 判断当前服务器是否是IIS服务器
            $ip = $_SERVER['HTTP_CLIENT_IP'];
        } else {
            // 默认当前服务器为Apache或Nginx
            $ip = $_SERVER['REMOTE_ADDR'];
        }
    } else {
        if (getenv('HTTP_X_FORWARDED_FOR')) {
            $ip = getenv('HTTP_X_FORWARDED_FOR');
        } else if (getenv('HTTP_CLIENT_IP')) {
            $ip = getenv('HTTP_CLIENT_IP');
        } else {
            $ip = getenv('REMOTE_ADDR');
        }
    }

    return ip2long($ip);
}

/*文件上传函数集合开始*/

/**
 * 生成指定长度的随机字符串
 * @param  integer $num 长度
 * @return string       字符串
 * 2016-12-28T11:09:48+0800
 */
function randomStr($num=4)
{
    $str = 'abcdefghjkmnpqrstuvwxyzABCDEFGHJKMNPQRSTUVWXYZ23456789';
    $str = str_shuffle($str);

    // 求该字符串的长度
    $length = strlen($str);
    $num = max(4, $num);
    $num = min($num, $length);

    $str = substr($str, 0, $num);

    return $str;
}

/**
 * 生成日期目录
 * @param  string $path 上传文件的存入目录
 * @return string       存入数据用的路径
 * 2016-12-28T11:14:53+0800
 */
function createDir($path)
{
    // 生成日期
    $timeDir = '/'.date('Y/m', time());
    // 下方生成的路径最后是存库用的
    $path .= $timeDir;

    $realPath = ROOT.$path;
    // 先判断是不是路径,如果不是路径就生成这个路径
    is_dir($realPath) || mkdir($realPath, 0777, true);
    // 返回该路径
    return $path;
}

/**
 * 获得文件的扩展名
 * @param  string $filename 传入的文件名
 * @return string           获得的文件扩展名
 * 2016-12-28T11:23:21+0800
 */
function getExt($filename)
{
    return ltrim(strrchr($filename, '.'), '.');
}

/**
 * 上传文件函数
 * @param  string $name     input的name
 * @param  string $path 绝对路径
 * @return mixed           上传成功返回图片路径失败返回false
 * 2016-12-28T11:58:45+0800
 */
function uploadFile($name, $path)
{
    $file = isset($_FILES[$name]) ? $_FILES[$name] : '';
    if (empty($file)) {
        error('上传的文件不能为空');
    }

    // 判断图片上传是否出错
    if ($file['error'] != '0') {
        error('图片上传出错,错误代码'.$file['error']);
    }

    // 获取文件名
    $filename = $path.'/'.date('YmdHis', time()).randomStr(5).'.'.getExt($file['name']);
    $realFilePath = ROOT.$filename;

    // 判断上传是否成功
    $bool = move_uploaded_file($file['tmp_name'], $realFilePath);
    if ($bool) {
        return $filename;
    } else {
        return false;
    }
}

/*文件上传函数集合结束*/

/**
 * 给文章插入书签
 * @param  int $art_id 文章id
 * @param  string $str    标签字符串
 * @return boolean         成功返回TRUE失败返回FALSE
 * 2016-12-28T10:48:40+0800
 */
function insertTag($art_id, $str)
{
    $arr = explode(',', $str);
    foreach ($arr as $v) {
        $bool = mExec('tags', array('art_id'=>$art_id,'tagname'=>$v));
        if (!$bool) {
            // 标签插入失败
            return false;
        }
    }
    return true;
}

/**
 * 获得分页数组
 * @param  int $artCount 文章总数
 * @param  int $curr     当前页码
 * @param  int $step     步进值
 * @return array           分页页码数组
 * 2016-12-27T16:13:45+0800
 */
function getPage($artCount, $curr, $step)
{
    // 求总页数,使用ceil()函数向上取整
    $pageNum = ceil($artCount/$step);

    $left = max(1, $curr-2);
    $right = min($left+4, $pageNum);
    $left = max(1, $right-4);

    $page = array();
    for ($i=$left; $i <= $right; $i++) {
        $_GET['page'] = $i;
        // 故意给$i加单引号是为了防止后期数组弹出开头和结尾后,索引值变了
        $page[$i] = http_build_query($_GET);
    }

    // 左箭头表示当前页面减一
    $_GET['page'] = max(1, $curr-1);
    array_unshift($page, http_build_query($_GET));
    // 右箭头表示当前页加一
    $_GET['page'] = min($curr+1, $pageNum);
    array_push($page, http_build_query($_GET));

    return $page;
}

/**
 * 获得limit的offset和step
 * @param  int $page 当前页码
 * @return array       数组
 * 2016-12-27T15:04:07+0800
 */
function getOffset($page)
{
    // 每页显示几行
    $step = 5;
    // 跳过几
    $offset = ($page-1)*$step;

    return array($offset, $step);
}

/**
 * 数组中的值转实体
 * @param  array $arr 待转换的数组
 * @return array      返回转换完成的数组
 * 2016-12-27T10:51:10+0800
 */
function _addcslashes($arr)
{
    foreach ($arr as $key => $value) {
        if (is_array($value)) {
            // 判断$value是不是数组，如果是数组则递归调用函数
            $arr[$key] = _addcslashes($value);
        } else if (is_string($value)) {
            $arr[$key] = $value;
        }
    }

    return $arr;
}

/**
 * 成功提示函数
 * @param  string $msg [description]
 * 2016-12-27T11:00:20+0800
 */
function succ($msg='成功')
{
    $status = 'success';
    require './view/admin/info.html';
    exit;
}

function error($msg='失败')
{
    $status = 'error';
    require './view/admin/info.html';
    exit;
}

?>