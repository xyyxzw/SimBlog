<?php
/**
 * @author DengPeng <3@dengpeng.cc>
 * @since 2016/12/27
 * @copyright copyright (c) 2016 zixue.it GPL
 * @license http://www.zixue.it/
 */

/*栏目编辑页面*/

require './lib/init.php';

// 用户登陆权限鉴定
isAcc();

/**
 * 思路分析:
 * 无论是编辑后提交,还是纯粹的编辑页展示,都需要获取栏目id
 * 所以首先我们获取栏目id
 */

// (1)获取栏目id
$cat_id = isset($_GET['cat_id']) ? $_GET['cat_id'] : '';

// (2)判断该栏目是否存在
$sql = "select * from cats where cat_id = '".$cat_id."'";
$result = mGetRow($sql);
if (!$result) {
    // 如果从结果集中取不出东西,返回FALSE
    error('该栏目不存在');
}

// (3)判断用户当前是否处于提交更改状态
if (!empty($_POST)) {
    // 如果$_POST数组不为空,说明用户提交了修改数据,因此我们要接收
    $catname = isset($_POST['catname']) ? $_POST['catname'] : '';
    // 更改栏目
    $data = array('catname'=>$catname);
    $bool = mExec('cats', $data, 'update', "cat_id = '".$cat_id."'");
    // 判断是否修改成功
    if ($bool) {
        succ('栏目名称修改成功');
    } else {
        error('栏目名称修改失败');
    }
}

// (4)如果用户没有提交数据,我们只需要查询该栏目,展示到模板
$sql = "select * from cats where cat_id = '".$cat_id."'";
$cat = mGetRow($sql);
require './view/admin/catadd.html';

?>