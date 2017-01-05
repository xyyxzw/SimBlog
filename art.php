<?php
/**
 * @author DengPeng <3@dengpeng.cc>
 * @since 2016/12/28
 * @copyright copyright (c) 2016 zixue.it GPL
 * @license http://www.zixue.it/
 */

/*文章详情展示页*/

require './lib/init.php';


// 获取文章id
$art_id = isset($_GET['art_id']) ? $_GET['art_id'] : '';


// 附加:判断用户是否有POST提交
if (!empty($_POST)) {
    $nickname = isset($_POST['nickname']) ? $_POST['nickname'] : '';
    $email = isset($_POST['email']) ? $_POST['email'] : '';
    $content = isset($_POST['content']) ? $_POST['content'] : '';

    if (empty($nickname) || empty($email) || empty($content)) {
        // 昵称邮箱评论内容都不能为空,为空跳转到当前页
        header('location: art.php?art_id='.$art_id);
    }

    $data = array(
        'art_id' => $art_id,
        'user_id' => 0,
        'nickname' => $nickname,
        'email' => $email,
        'content' => $content,
        'pubtime' => time(),
        'ip' => getRealIp(),
    );

    // 插入评论
    $bool = mExec('comments', $data);
    // 获取插入的评论id,如果评论数更新失败,则删除评论
    $comment_id = mGetInsertId();
    if (!$bool) {
        // 如果评论结果失败,跳转到首页
        header('location: index.php');
    }
    // 更新该文章的评论数
    $bool = mQuery("update arts set commentnum = commentnum + 1 where art_id = '".$art_id."'");
    if (!$bool) {
        // 如果更新失败,将此条评论删除
        mQuery("delete from comments where comment_id = '".$comment_id."'");
        notFound('评论失败');
    }
}


// (1)判断该文章是否存在
if (empty($art_id)) {
    $art = array();
}

// 拼接SQL语句查询表中是否存在该文章
$sql = 'select arts.*,cats.catname,users.nickname from (arts left join cats ';
$sql .= 'on arts.cat_id = cats.cat_id) left join users ';
$sql .= "on arts.user_id = users.user_id where arts.art_id = '".$art_id."'";
// 查询该文章
$art = mGetRow($sql);


// (2)查询最新文章
$sql = 'select art_id, title from arts order by pubtime desc limit 5';
$new_art = mGetAll($sql);

// (3)查询最热门的文章
$sql = 'select art_id, title, commentnum from arts order by commentnum desc ';
$sql .= 'limit 5';
$hot_art = mGetAll($sql);

// (4)查询所以分类
$sql = 'select * from cats';
$cats = mGetAll($sql);

// (5)查询文章评论
$sql = "select * from comments where art_id = '".$art_id."' order by pubtime desc";
$comments = mGetAll($sql);

require './view/front/art.html';

?>