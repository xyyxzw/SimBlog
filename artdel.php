<?php
/**
 * @author DengPeng <3@dengpeng.cc>
 * @since 2016/12/28
 * @copyright copyright (c) 2016 zixue.it GPL
 * @license http://www.zixue.it/
 */

/*文章删除*/

/**
 * 思考:删除文章后,需要做哪些后续工作
 * (1)将文章所属栏目下的文章数减一
 * (2)将该文章所关联的图片删除掉
 */

require './lib/init.php';

// 用户登陆权限鉴定
isAcc();

// (1)获取文章id
$art_id = isset($_GET['art_id']) ? $_GET['art_id'] : '';

// (2)判断该文章是否存在
if (empty($art_id)) {
    error('文章id不能为空');
}

// 拼接sql语句查看该篇文章是否存在
$sql = "select * from arts where art_id = '".$art_id."'";
$art = mGetRow($sql);   // 这里查询出文章的所有字段,便于下方删除文章图片
if (!$art) {
    error('该篇文章不存在');
}

// (3)删除该篇文章
$sql = "delete from arts where art_id = '".$art_id."'";
$bool = mQuery($sql);
// 判断是否删除成功
if (!$bool) {
    error('删除失败');
}

// (4)将该文章所属的栏目数减一

// 首先需要我们获取该栏目的id
// 给该栏目的文章数减一
$sql = "update cats set artnum = artnum - 1 where cat_id = '".$art['cat_id']."'";
$bool = mQuery($sql);
if (!$bool) {
    error('该文章所属的栏目下的文章数减一失败');
}

// (5)将该文章所属的标签删除
$sql = "delete from tags where art_id = '".$art_id."'";
$bool = mQuery($sql);
if (!$bool) {
    error('该文章的标签删除失败');
}

// (6)将该文章的图片删除

// 首先判断该文章是否有图片,有则删除,没有则不理睬
if (!empty($art['pic'])) {
    $bool = unlink(ROOT.$art['pic']);
    if (!$bool) {
        error('删除该文章附带的图片失败');
    }
}

succ('该文章删除成功');

?>