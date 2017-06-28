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

if (!$smarty->is_cached('tuijian.dwt', $cache_id))
{
    assign_template();

    $position = assign_ur_here();
    $smarty->assign('page_title',      "淘宝推荐");    // 页面标题

	$smarty->assign('ur_here',         '<a href=".">首页</a> <code>&gt;</code> <a href="tuijian.php">淘宝推荐</a>');  // 当前位置

	 $smarty->assign('hot_goods',       get_recommend_goods('hot'));     // 热点文章
	 $smarty->assign('best_goods',      get_recommend_goods('best'));    // 推荐商品
	 $smarty->assign('categories',       get_categories_tree($cat_id)); // 分类树
////

    $sql = 'SELECT article_id, title, author, add_time, file_url, open_type,description' .
               ' FROM ' .$ecs->table('article') .
               ' WHERE (is_open = 1 AND cat_id = 13) ORDER BY article_type DESC, article_id DESC limit 4';

    $res1 = $GLOBALS['db']->getAll($sql);
	$arr = array();
	foreach($res1 as $row)
        {
            $article_id = $row['article_id'];

            $arr[$article_id]['id']          = $article_id;
            $arr[$article_id]['title']       = $row['title'];
			$arr[$article_id]['description']       = sub_str($row['description'], 46);
            $arr[$article_id]['short_title'] = $GLOBALS['_CFG']['article_title_length'] > 0 ? sub_str($row['title'], 12) : $row['title'];
            $arr[$article_id]['author']      = empty($row['author']) || $row['author'] == '_SHOPHELP' ? $GLOBALS['_CFG']['shop_name'] : $row['author'];
            $arr[$article_id]['url']         = $row['open_type'] != 1 ? build_uri('article', array('aid'=>$article_id), $row['title']) : trim($row['file_url']);
            $arr[$article_id]['add_time']    = date($GLOBALS['_CFG']['date_format'], $row['add_time']);
        }


	$smarty->assign('artciles_list1',   $arr);

    /* 分类信息 */
    $sql = 'SELECT cat_id,cat_name FROM ' . $GLOBALS['ecs']->table('category') . " where parent_id=0 and is_show=1 limit 10";
    $cat_top10s = $db->getAll($sql);
	foreach($cat_top10s as $k=>$v)
	{
		$cat_top10s[$k]['goods']=get_recommend_goodsa($v['cat_id']);
	}
    $smarty->assign('cat_top10s',     $cat_top10s);       // 商店公告
	$smarty->assign('cat_top10sg',     $cat_top10s);       // 商店公告
   // print_r($cat_top10s);



    /* 页面中的动态内容 */
    assign_dynamic('tuijian');
}

$smarty->display('tuijian.dwt', $cache_id);


?>