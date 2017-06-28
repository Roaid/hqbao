<?php

/**
 * ECSHOP 首页文件
 * ============================================================================
 * 版权所有 2005-2009 上海商派网络科技有限公司，并保留所有权利。
 * 网站地址: http://www.ecshop.com；
 * ----------------------------------------------------------------------------
 * 这不是一个自由软件！您只能在不用于商业目的的前提下对程序代码进行修改和
 * 使用；不允许对程序代码以任何形式任何目的的再发布。
 * ============================================================================
 * $Author: liubo $
 * $Id: index.php 16881 2009-12-14 09:19:16Z liubo $
*/

define('IN_ECS', true);

require(dirname(__FILE__) . '/includes/init.php');

if ((DEBUG_MODE & 2) != 2)
{
    $smarty->caching = true;
}


/*------------------------------------------------------ */
//-- 判断是否存在缓存，如果存在则调用缓存，反之读取相应内容
/*------------------------------------------------------ */
/* 缓存编号 */
$cache_id = sprintf('%X', crc32($_SESSION['user_rank'] . '-' . $_CFG['lang']));

if (!$smarty->is_cached('zhekou.dwt', $cache_id))
{
    /* 如果页面没有被缓存则重新获得页面的内容 */
	$cat_id=13;
    assign_template('a', array($cat_id));
    $position = assign_ur_here($cat_id);

    $smarty->assign('page_title',           '折扣信息');     // 页面标题
    $smarty->assign('ur_here',         '<a href=".">首页</a> <code>&gt;</code> <a href="zhekou.php">折扣信息</a>');  // 当前位置
	
        
    $sql = 'SELECT article_id, title, author, add_time, file_url, open_type,description' .
               ' FROM ' .$ecs->table('article') .
               ' WHERE (is_open = 1 AND cat_id = 13) ORDER BY article_type DESC, article_id DESC limit 7';

    $res1 = $GLOBALS['db']->getAll($sql);
	$arr = array();
	foreach($res1 as $row)
        {
            $article_id = $row['article_id'];

            $arr[$article_id]['id']          = $article_id;
            $arr[$article_id]['title']       = $row['title'];
			$arr[$article_id]['description']       = sub_str($row['description'], 48);
            $arr[$article_id]['short_title'] = $GLOBALS['_CFG']['article_title_length'] > 0 ? sub_str($row['title'], 12) : $row['title'];
            $arr[$article_id]['author']      = empty($row['author']) || $row['author'] == '_SHOPHELP' ? $GLOBALS['_CFG']['shop_name'] : $row['author'];
            $arr[$article_id]['url']         = $row['open_type'] != 1 ? build_uri('article', array('aid'=>$article_id), $row['title']) : trim($row['file_url']);
            $arr[$article_id]['add_time']    = date($GLOBALS['_CFG']['date_format'], $row['add_time']);
        }


    $sql = 'SELECT article_id, title, author, add_time, file_url, open_type,description' .
               ' FROM ' .$ecs->table('article') .
               ' WHERE (is_open = 1 AND cat_id = 13) ORDER BY article_type DESC, article_id DESC limit 7';

    $res2 = $GLOBALS['db']->getAll($sql);
	$arr2 = array();
	foreach($res2 as $row)
        {
            $article_id = $row['article_id'];

            $arr2[$article_id]['id']          = $article_id;
            $arr2[$article_id]['title']       = $row['title'];
			$arr2[$article_id]['description']       = sub_str($row['description'], 48);
			$arr2[$article_id]['file_url']       = $row['file_url'];
            $arr2[$article_id]['short_title'] = $GLOBALS['_CFG']['article_title_length'] > 0 ? sub_str($row['title'], 14) : $row['title'];
            $arr2[$article_id]['author']      = empty($row['author']) || $row['author'] == '_SHOPHELP' ? $GLOBALS['_CFG']['shop_name'] : $row['author'];
            $arr2[$article_id]['url']         = $row['open_type'] != 1 ? build_uri('article', array('aid'=>$article_id), $row['title']) : trim($row['file_url']);
            $arr2[$article_id]['add_time']    = date($GLOBALS['_CFG']['date_format'], $row['add_time']);
        }
	
    $smarty->assign('artciles_list1',   $arr);
	$smarty->assign('artciles_list2',   $arr2);
    //$smarty->assign('cat_id',    $cat_id);



    assign_dynamic('zhekou');

}

$smarty->display('zhekou.dwt', $cache_id);



?>