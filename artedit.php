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

// (1)获取文章id
$art_id = isset($_GET['art_id']) ? $_GET['art_id'] : '';


// (2)先判断用户是否提交了数据
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


    /**
     * ①
     * 只要用户提交的图片不为空
     * 我就将用户的原来的图片删除掉
     * 根本不去判断用户提交的图片是否跟之前一致
     */
    $sql = "select * from arts where art_id = '".$art_id."'";
    $art = mGetRow($sql);
    if (!empty($filename)) {
        // 删除一个文件前,先判断是不是一个文件
        is_file(ROOT.$art['pic']) && unlink(ROOT.$art['pic']);
    } else {
        // 如果用户没有提交新的图片,则更新为原始的图片路径
        $filename = $art['pic'];
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


    $bool = mExec('arts', $data, 'update', "art_id = '".$art_id."'");

    // 判断文章是否修改
    if ($bool) {
        // 文章修改成功后,判断cat_id是否变化,cat_id变化了采取修改对应的栏目下的文章数
        if ($cat_id != $art['cat_id']) {
            $sql_1 = "update cats set artnum = artnum + 1 where cat_id = '".$cat_id."'";
            $sql_2 = "update cats set artnum = artnum - 1 where cat_id = '".$art['cat_id']."'";
            $bool_artnum = mQuery($sql_1) && mQuery($sql_2);
        }


        /**
         * ②
         * 新标签插入之前,先把旧标签删除
         * 我就将用户的原来的标签删除掉
         */
        $sql = "delete from tags where art_id = '".$art_id."'";
        $bool = mQuery($sql);
        if (!$bool) {
            error('旧标签删除失败');
        }

        /**
         * 文章添加成功了,有标签,就添加,没标签,给个TRUE
         */
        $bool_tagname = true;
        // 判断是否有标签
        if (!empty($tags)) {
            $bool_tagname = insertTag($art_id, $tags);
        }

        if ($bool_artnum && $bool_tagname) {
            succ('新文章修改成功');
        } else {
            // 如果栏目表中的文章数更新失败,则提示栏目表更新失败
            $bool_artnum_msg = $bool_artnum ? '' : '栏目对应的文章数修改失败,';
            $bool_tagname_msg = $bool_tagname ? '' : '插入标签失败';
            error($bool_artnum_msg.$bool_tagname_msg);
        }

    } else {
        error('新文章添加失败');
    }
}


// (3)判断该篇文章是否存在
if (empty($art_id)) {
    error('文章id参数不能为空');
}

// 拼接sql语句,判断该文章是否存在
$sql = 'select arts.*,cats.catname from arts left join cats ';
$sql .= "on arts.cat_id = cats.cat_id where art_id = '".$art_id."'";
$art = mGetRow($sql);
if (!$art) {
    error('该篇文章不存在');
}

// (4)查询栏目
$sql = "select * from cats";
$cats = mGetAll($sql);

// (5)查询标签
$tags = '';
$sql = "select tagname from tags where art_id = '".$art_id."'";
$tags_arr = mGetAll($sql);
// 当该文章存在标签时,再foreach拼接标签
if ($tags_arr) {
    $arr = array();
    foreach ($tags_arr as $v) {
        $arr[] = $v['tagname'];
    }
    $tags = implode(',', $arr);
}

require './view/admin/artedit.html';

?>