<?php
define('IN_ECS', true);
require(dirname(__FILE__) . '/includes/init.php');
include_once(ROOT_PATH . '/includes/library/Taoapi.php');
	$taobaoURL=$_POST['taobaourl'];
	//$taobaoURL="http://item.taobao.com/item.htm?id=5388376370&ad_id=&am_id=&cm_id=&pm_id=";

$shangp=taobao_get_nick($taobaoURL);
	if(!empty($shangp))
	{
		$Taoapi_Config = Taoapi_Config::Init();
		//$Taoapi_Config->setCharset('UTF-8');	
		$Taoapi = new Taoapi;
		
		//得到单个商品信息(taobao.item.get)
		$Taoapi->method = 'taobao.item.get';
		$Taoapi->fields = 'iid,detail_url,num_iid,title,nick,pic_url,price,detail_url,freight_payer,express_fee,postage_id';
		$Taoapi->nick = $shangp['nick'];
		if($shangp['iid']){$Taoapi->iid = $shangp['iid'];}
		if($shangp['num_iid']){$Taoapi->num_iid = $shangp['num_iid'];}
		//需要更多的字段可以登陆 taoapi.com 进行配置生成
		$TaobaokeData = $Taoapi->Send('get','JSON')->getJsonData();
		$result = $TaobaokeData;
		$result=json_decode ( $result ,  true );
		$result['item_get_response']['item']['pic_path']=GrabImage($result['item_get_response']['item']['pic_url']);

		if($result['item_get_response']['item']['postage_id'])
		{
			$Taoapi1 = new Taoapi;
			$Taoapi1->method = 'taobao.postage.get';
			$Taoapi1->fields = 'name,post_price,express_price,ems_price';
			$Taoapi1->nick = $result['item_get_response']['item']['nick'];
			$Taoapi1->postage_id = $result['item_get_response']['item']['postage_id'];
			$TaobaokeData1 = $Taoapi1->Send('get','JSON')->getJsonData();
			
			$result1=json_decode ( $TaobaokeData1 ,  true );


			if($result1['postage_get_response']['postage']['ems_price']>$result1['rsp']['postage']['express_price'])
			{
				$express_fee =  $result1['postage_get_response']['postage']['ems_price'];
			}
			else
			{
				$express_fee =  $result1['postage_get_response']['postage']['express_price'];
			}
			
			if($express_fee<$result1['postage_get_response']['postage']['post_price'])
			{
				$express_fee =  $result1['postage_get_response']['postage']['post_price'];
			}
			
			$result['item_get_response']['item']['express_fee'] = $express_fee ;

			
		}
		$result=json_encode($result);
	}
	else
	{
		$result = "{error_rsp:{code:999,msg:'错误的提交'}}";
	}
	echo $result;
	die;

?>