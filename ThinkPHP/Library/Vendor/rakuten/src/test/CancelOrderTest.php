<?php
// Copyright (c) Rakuten, Inc. All Rights Reserved.

define("SRC_PATH","/php_work/xampp/htdocs/order-api_sample/src");

require_once(SRC_PATH. "/test/TestDefine.php");
require_once(SRC_PATH. "/api/OrderApiPractice.php");
require_once(SRC_PATH. "/api/impl/OrderApiPracticeBase.php");
require_once(SRC_PATH. "/api/impl/CancelOrderPractice.php");

print ("テストSTART\n");

//注文キャンセルリクエスト情報の作成
$serReq = array();
$serReq[] = array(
    'orderNumber'  => 'XXXXXXXXXXXXXXXX',
	'reasonId'   => X,
	'restoreInventoryFlag'   => X,
   	);

$api = new CancelOrderPractice();

//認証情報の設定
$api->setAuthRequest(
	array(
      	// 認証キー
       'authKey'  => AUTH_KEY,
       // 店舗URL
	   'shopUrl'   => SHOP_URL,
	   // ユーザ名
	   'userName' => USER_NAME));

//注文キャンセルリクエスト情報の設定
$api->setServiceRequest($serReq);

//API実行
$res = $api->callOrderApi();

?>