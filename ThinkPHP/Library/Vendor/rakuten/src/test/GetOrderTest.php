<?php
header("Content-Type: text/plain; charset=UTF-8");
// Copyright (c) Rakuten, Inc. All Rights Reserved.

require_once(dirname(__FILE__)."/TestDefine.php");
require_once(dirname(dirname(__FILE__))."/api/OrderApiPractice.php");
require_once(dirname(dirname(__FILE__))."/api/impl/OrderApiPracticeBase.php");
require_once(dirname(dirname(__FILE__))."/api/impl/GetOrderPractice.php");



//タイムゾーンの設定
date_default_timezone_set('PRC');

$start_time=M("order_plat_form_update_time")->where(array("come_from_id"=>20))->order("time desc")->getField("time");
$start_time_date=date("Y-m-d H:i:s",$start_time);
$end_time=date("Y-m-d H:i:s",time());
//受注検索モデル情報の作成
$orderSearchModel =
	array(
		'dateType' => 1,	 //期間検索種別
		'startDate' => strtotime($start_time_date),	//期間FROM
		'endDate' => strtotime($end_time),	//期間TO
// 	    'ordererPhoneNumber' => '00011112222'	 //注文者電話番号
	);;


//受注情報取得リクエスト情報の作成
$serReq = array(
// 	'orderNumber' => array('XXXXXXXXXXXXXXX'),	//受注番号
// 	'isOrderNumberOnlyFlg' => false,	//注文情報取得フラグ
	'orderSearchModel' => $orderSearchModel	//受注検索モデル
	);

$api = new GetOrderPractice();

//認証情報の設定
$api->setAuthRequest(
	array(
      	// 認証キー
       'authKey'  => AUTH_KEY,
       // 店舗URL
	   'shopUrl'   => SHOP_URL,
	   // ユーザ名
	   'userName' => USER_NAME));

//受注情報取得リクエスト情報の設定
$api->setServiceRequest($serReq);

//API実行
$res = $api->callOrderApi();
?>