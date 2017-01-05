<?php
/**
 * @author DengPeng <3@dengpeng.cc>
 * @since 2016/12/27
 * @copyright copyright (c) 2016 zixue.it GPL
 * @license http://www.zixue.it/
 */


/**
 * MySQL连接函数
 * @return resource 返回连接的资源
 * 2016-12-27T11:20:26+0800
 */
function mConnect()
{
    static $link = null;

    $cfg = require ROOT.'/lib/config.php';

    if ($link === null) {
        $link = mysql_connect($cfg['host'], $cfg['user'], $cfg['pwd']);
        mysql_select_db($cfg['dbname'], $link);
        mysql_query('set names '.$cfg['charset']);
    }

    return $link;
}

/**
 * MySQL查询日志信息
 * @param  string $info 日志信息
 * 2016-12-27T11:38:27+0800
 */
function mLog($info)
{
    $filename = date('Ymd', time()).'.txt';
    $data = date('Y-m-d H:i:s', time())."\n\n".$info;
    $data .= "\n".'===================='."\n\n";
    file_put_contents(ROOT.'/logs/'.$filename, $data, FILE_APPEND);
}

/**
 * MySQL查询函数
 * @param  string $sql sql语句
 * @return resource      查询结果集
 * 2016-12-27T11:41:31+0800
 */
function mQuery($sql)
{
    $link = mConnect();

    $result = mysql_query($sql, $link);
    if (!$result) {
        $info = $sql."\n----------\n".mysql_error();
        mLog($info);
        return false;
    }
    mLog($sql);
    return $result;
}

/**
 * 从结果集中取出一行的一个单元
 * @param  string $sql 字符串
 * @return mixed      成功返回字符串,失败返回FALSE
 * 2016-12-27T11:44:32+0800
 */
function mGetOne($sql)
{
    $result = mQuery($sql);
    if (!$result) {
        return false;
    }

    $one = mysql_fetch_row($result)[0];

    return $one;
}

/**
 * 从结果集中取出一行(一维数组)
 * @param  string $sql 字符串
 * @return mixed      成功返回一维数组,失败返回FALSE
 * 2016-12-27T11:47:10+0800
 */
function mGetRow($sql)
{
    $result = mQuery($sql);
    if (!$result) {
        return false;
    }

    $row = mysql_fetch_assoc($result);

    return $row;
}


/**
 * 获得多行二维数组
 * @param  string $sql SQL字符串
 * @return mixed      成功返回数组失败返回FALSE
 * 2016-12-27T13:58:18+0800
 */
function mGetAll($sql)
{
    $result = mQuery($sql);
    if (!$result) {
        return false;
    }

    $arr = array();
    while ($row = mysql_fetch_assoc($result)) {
        $arr[] = $row;
    }

    return $arr;
}

/**
 * 插入和更新函数
 * @param  string $tablename 操作的表名
 * @param  array  $data      操作的数据
 * @param  string $type      执行的类型
 * @param  string $where     条件,默认为0防止用户不给where条件也执行了
 * @return bool            成功返回TRUE失败返回FALSE
 * 2016-12-27T14:24:46+0800
 */
function mExec($tablename, $data=array(), $type='insert', $where='0')
{
    $arg_num = func_num_args();
    if ($arg_num < 2 || $arg_num > 4) {
        exit('函数参数个数错误');
    }

    if ($type == 'insert') {
        /*拼接插入insert类型语句*/
        $sql = 'insert into '.$tablename.' (';
        $sql .= implode(',', array_keys($data)).') values (';
        $sql .= "'".implode("','", array_values($data))."')";
    } else if ($type == 'update') {
        /*拼接update类型语句*/
        $sql = 'update '.$tablename.' set ';
        foreach ($data as $key => $value) {
            $sql .= $key."='".$value."',";
        }
        $sql = rtrim($sql, ',');
        $sql .= ' where '.$where;
    }

    $bool = mQuery($sql);
    if ($bool) {
        return true;
    } else {
        return false;
    }
}

/**
 * 获取上一步insert产生的id
 * @return int 主键id
 * 2016-12-27T14:28:25+0800
 */
function mGetInsertId()
{
    $link = mConnect();
    return mysql_insert_id($link);
}

?>