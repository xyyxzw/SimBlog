<?php
/**
 * @author DengPeng <3@dengpeng.cc>
 * @since 2016/12/27
 * @copyright copyright (c) 2016 zixue.it GPL
 * @license http://www.zixue.it/
 */

/*文章添加页*/

require './lib/init.php';

// 用户登陆权限鉴定
isAcc();

// (1)先判断用户是否提交了数据
if (!empty($_POST)) {
    // 获取用户填写的表单
    $title = isset($_POST['title']) ? trim($_POST['title']) : '';
    $cat_id = isset($_POST['cat_id']) ? trim($_POST['cat_id']) : '';
    $content = isset($_POST['content']) ? trim($_POST['content']) : '';
    $tags = isset($_POST['tags']) ? trim($_POST['tags']) : '';

    // 这个三个栏目都不能为空
    if (empty($title) || empty($cat_id) || empty($content)) {
        error('文章标题,栏目名称,内容都不能为空');
    }

    /**
     * 如果图片存在则上传图片,图片初试路径给空
     */
    $filename = '';
    // 判断用户是否选择了图片上传,应该判断error是否为4,如果为4则说明没有图片上传
    if ($_FILES['pic']['error'] != 4) {
        $path = createDir('/uploads');  // 相对根目录的路径
        $filename = uploadFile('pic', $path);
        if (!$filename) {
            error('图片上传出错');
        }
    }

    // 拼接SQL语句,插入文章表
    $data = array(
        'user_id' => '1',
        'title' => $title,
        'cat_id' => $cat_id,
        'content' => $content,
        'pubtime' => time(),
        'pic' => $filename,
    );

    $bool = mExec('arts', $data);
    // 获取插入的文章id,用来给该篇文章添加书签
    $art_id = mGetInsertId();
    // 判断文章是否添加成功
    if ($bool) {
        // 文章添加成功后,需要更新栏目表中的文章数
        $bool_artnum = mQuery("update cats set artnum = artnum + 1 where cat_id = '".$cat_id."'");

        /**
         * 文章添加成功了,有标签,就添加,没标签,给个TRUE
         */
        $bool_tagname = true;
        // 判断是否有标签
        if (!empty($tags)) {
            $bool_tagname = insertTag($art_id, $tags);
        }

        if ($bool_artnum && $bool_tagname) {
            succ('新文章添加成功');
        }

        // 如果栏目表中的文章数更新失败,则提示栏目表更新失败
        error('栏目文章数更新失败');
    } else {
        error('新文章添加失败');
    }
}

// (2)用户没有提交数据,查询栏目
$sql = "select * from cats";
$cats = mGetAll($sql);

require './view/admin/artadd.html';

?>