<?php
define('IN_ECS', true);

require(dirname(__FILE__) . '/includes/init.php');
require_once(ROOT_PATH . '/' . ADMIN_PATH . '/includes/lib_goods.php');
require(ROOT_PATH . 'includes/lib_order.php');
include_once(dirname(__FILE__) . '/includes/cls_json.php');
if($_POST)
{

	$goods_name=$_POST['goods_name'];
	$shop_price=$_POST['shop_price'];
	$goods_img_url=$_POST['goods_img_url'];
	$express_fee=$_POST['express_fee'];
	$shul=!empty($_POST['shul']) ? $_POST['shul'] : 10;
	$beiz=$_POST['beiz'];
	$detail_url=$_POST['detail_url'];
	$tb_nick=$_POST['tb_nick'];
	$tb_num_iid=$_POST['tb_num_iid'];
	$tb_iid=$_POST['tb_iid'];
	
	
	$shop_price=$shop_price+$express_fee;
	
	$max_id     =  $db->getOne("SELECT MAX(goods_id) + 1 FROM ".$ecs->table('goods'));    
	$goods_sn   = generate_goods_sn($max_id);

    $market_price = !empty($_POST['market_price']) ? $_POST['market_price'] : 0;
    $promote_price = !empty($_POST['promote_price']) ? floatval($_POST['promote_price'] ) : 0;
    $is_promote = empty($promote_price) ? 0 : 1;
    $promote_start_date = ($is_promote && !empty($_POST['promote_start_date'])) ? local_strtotime($_POST['promote_start_date']) : 0;
    $promote_end_date = ($is_promote && !empty($_POST['promote_end_date'])) ? local_strtotime($_POST['promote_end_date']) : 0;
    $goods_weight = !empty($_POST['goods_weight']) ? $_POST['goods_weight'] * $_POST['weight_unit'] : 0;
    $is_best = isset($_POST['is_best']) ? 1 : 0;
    $is_new = isset($_POST['is_new']) ? 1 : 0;
    $is_hot = isset($_POST['is_hot']) ? 1 : 0;
    $is_on_sale = 1;
    $is_alone_sale = 1;
    $is_shipping = isset($_POST['is_shipping']) ? 1 : 0;
    $goods_number = isset($shul) ? $shul : 1;
    $warn_number =0;
    $goods_type = isset($_POST['goods_type']) ? $_POST['goods_type'] : 0;
    $give_integral = isset($_POST['give_integral']) ? intval($_POST['give_integral']) : '-1';
    $rank_integral = isset($_POST['rank_integral']) ? intval($_POST['rank_integral']) : '-1';
    $suppliers_id = isset($_POST['suppliers_id']) ? intval($_POST['suppliers_id']) : '0';

    $goods_name_style = $_POST['goods_name_color'] . '+' . $_POST['goods_name_style'];

    $catgory_id = empty($_POST['cat_id']) ? '' : intval($_POST['cat_id']);
    $brand_id = empty($_POST['brand_id']) ? '' : intval($_POST['brand_id']);

    $goods_img = $goods_img_url;
    $goods_thumb = $goods_img_url;
	$catgory_id=16;
	$sql = "SELECT goods_id FROM " . $ecs->table('goods') ." WHERE detail_url = '$detail_url'";


	
	
	$goods_id=$db->getone($sql);

    if (!$goods_id)
	{
	$sql = "INSERT INTO " . $ecs->table('goods') . " (goods_name, goods_name_style, goods_sn, " .
                    "cat_id, brand_id, shop_price, market_price, is_promote, promote_price, " .
                    "promote_start_date, promote_end_date, goods_img, goods_thumb, original_img, keywords, goods_brief, " .
                    "seller_note, goods_weight, goods_number, warn_number, integral, give_integral, is_best, is_new, is_hot, is_real, " .
                    "is_on_sale, is_alone_sale, is_shipping, goods_desc, add_time, last_update, goods_type, extension_code, rank_integral,detail_url,tb_nick,tb_num_iid,tb_iid)" .
                "VALUES ('$goods_name', '$goods_name_style', '$goods_sn', '$catgory_id', " .
                    "'$brand_id', '$shop_price', '$market_price', '$is_promote','$promote_price', ".
                    "'$promote_start_date', '$promote_end_date', '$goods_img', '$goods_thumb', '$original_img', ".
                    "'$keywords', '$goods_brief', '$seller_note', '$goods_weight', '$goods_number',".
                    " '$warn_number', '$integral', '$give_integral', '$is_best', '$is_new', '$is_hot', 1, '$is_on_sale', '$is_alone_sale', $is_shipping, ".
                    " '$goods_desc', '" . gmtime() . "', '". gmtime() ."', '$goods_type', '$code', '$rank_integral', '$detail_url', '$tb_nick', '$tb_num_iid', '$tb_iid')";

	$db->query($sql);
	
    /* 商品编号 */
    $goods_id = (string)$db->insert_id();
	}

    /*------------------------------------------------------ */
    //-- 添加商品到购物车
    /*------------------------------------------------------ */

//  goods.quick    = 1;
//  goods.spec     = '';
//  goods.goods_id = goodsId;
//  goods.number   = num;
//  goods.parent   =  0 ;



    /* 商品数量是否合法 */
    if (!is_numeric($shul) || intval($shul) <= 0)
    {
        $result['error']   = 1;
        $result['message'] = "商品数量不合法 ";
    }
    else
    {
        /* 添加到购物车 */
        if (addto_cart($goods_id, $shul, array(),0,$beiz))
        {
            if ($_CFG['cart_confirm'] > 2)
            {
                $result['message'] = '';
            }
            else
            {
                $result['message'] = $_CFG['cart_confirm'] == 1 ? $_LANG['addto_cart_success_1'] : $_LANG['addto_cart_success_2'];
            }

            $result['content'] = insert_cart_info();
            $result['one_step_buy'] = $_CFG['one_step_buy'];
        }
        else
        {
            $result['message']  = $err->last_message();
            $result['error']    = $err->error_no;
            $result['goods_id'] = stripslashes($goods->goods_id);
        }
    }
    $result['confirm_type'] = !empty($_CFG['cart_confirm']) ? $_CFG['cart_confirm'] : 2;
    
	$result="{message:导购成功}";
}
else
{
	$result="{message:非法提交}";
}
	echo $result;
	die;
?>