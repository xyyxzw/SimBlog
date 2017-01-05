<?php
/**
 * @author DengPeng <3@dengpeng.cc>
 * @since 2016/12/28
 * @copyright copyright (c) 2016 zixue.it GPL
 * @license http://www.zixue.it/
 */

/*博客首页*/
require './lib/init.php';

// 获取当前页码
$curr = isset($_GET['page']) ? trim($_GET['page']) : 1;
list($offset, $step) = getOffset($curr);
// 求出总页数,获得页码数组
$pageNum = mGetOne('select count(*) from arts');
$page = getPage($pageNum, $curr, $step);
$laquo = array_shift($page);
$raquo = array_pop($page);

// (1)左连接查询出所以文章字段,昵称,栏目名称

/**
 * 先判断用户是否选择了分类
 */
$m = isset($_GET['m']) ? $_GET['m'] : '';
$cat_id = isset($_GET['cat_id']) ? $_GET['cat_id'] : '';

if ($m == 'cat' && !empty($cat_id)) {
    // 分类下分页
    $sql = 'select arts.*,cats.catname,users.nickname from (arts left join cats ';
    $sql .= 'on arts.cat_id = cats.cat_id) left join users ';
    $sql .= "on arts.user_id = users.user_id where arts.cat_id = '".$cat_id."' ";
    $sql .= 'order by arts.pubtime desc limit '.$offset.','.$step;
} else {
    // 未分类
    $sql = 'select arts.*,cats.catname,users.nickname from (arts left join cats ';
    $sql .= 'on arts.cat_id = cats.cat_id) left join users ';
    $sql .= 'on arts.user_id = users.user_id ';
    $sql .= 'order by arts.pubtime desc limit '.$offset.','.$step;
}

$arts = mGetAll($sql);


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

require './view/front/index.html';

?>