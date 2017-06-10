<?php
// Copyright (c) Rakuten, Inc. All Rights Reserved.

define("SRC_PATH","/php_work/xampp/htdocs/order-api_sample/src");

require_once(SRC_PATH. "/test/TestDefine.php");
require_once(SRC_PATH. "/api/OrderApiPractice.php");
require_once(SRC_PATH. "/api/impl/OrderApiPracticeBase.php");
require_once(SRC_PATH. "/api/impl/UpdateOrderPractice.php");
require_once(SRC_PATH. "/api/util/SoapOrderModelUtil.php");
require_once(SRC_PATH. "/api/util/StdClassOrderModelUtil.php");

print ("テストSTART\n");

//タイムゾーンの設定
date_default_timezone_set('Asia/Tokyo');

//受注検索モデル情報情報の作成

//商品モデルリストの生成
$iModel = array();
$iModel[] = array(
	'basketId' => 123,						//商品キー
	'itemId' => 123,						//商品ID
	'itemName' => 'XXXXXXXXXXXXXXXXXX',		//商品名
	'itemNumber' => 'XXXXXXXXXXXXXXXXXX',	//商品番号
	'price' => 1234,					//単価
	'units' => 1234,					//個数
	'isIncludedPostage' => false,		//送料込別
	'isIncludedTax' => false,		//税込別
	'isIncludedCashOnDeliveryPostage' => false,		//代引手数料込別
	'selectedChoice' => 'XXXXXXXXXXXXXXXXXX',	//項目・選択肢
	'deleteItemFlg' => false,		//商品モデルの削除フラグ
	'gbuyItemModel' => array(
		//共同購入内訳情報
		'gbuyBidInventoryModel' => 	array(
			array(
				'gchoiceId' => 123,
				'bidUnits' => 123
			)
		)
	)
);

//送付先モデルの生成
$pModel = array();
$pModel[] = array(
	'basketId' => 123,								//送付先キー
	'shippingNumber' => 'XXXXXXXXXXXXXXXXXX',		//発送番号
	'deliveryCompanyId' => 'XXXXXXXXXXXXXXXXXX',	//配送業者ID
	'postagePrice' => 1234,							//送料合計
	'deliveryPrice' => 1234,						//代引料合計
	'goodsTax' => 1234,								//消費税合計
	'deleteFlg' => false,							//送付先モデルの削除フラグ
	'deliveryCvsModel' => array(					//コンビニ配送情報
		'cvsCode' => 1234,								//コンビニコード
		'storeGenreCode' => 'XXXXXXXXXXXXXXXXXX',		//ストア分類コード
		'storeCode' => 'XXXXXXXXXXXXXXXXXX',			//ストアコード
		'storeName' => 'XXXXXXXXXXXXXXXXXX',			//ストア名称
		'storeZip' => 'XXXXXXXXXXXXXXXXXX',				//郵便番号
		'storePrefecture' => 'XXXXXXXXXXXXXXXXXX',		//都道府県
		'storeAddress' => 'XXXXXXXXXXXXXXXXXX',			//その他住所
		'areaCode' => 'XXXXXXXXXXXXXXXXXX',				//発注エリアコード
		'depo' => 'XXXXXXXXXXXXXXXXXX',					//センターデポコード
		'cvsOpenTime' => 'XXXXXXXXXXXXXXXXXX'	,		//開店時間
		'cvsCloseTime' => 'XXXXXXXXXXXXXXXXXX',			//閉店時間
		'cvsBikou' => 'XXXXXXXXXXXXXXXXXX'				//特記事項
	),
	'senderModel' => array(							//送付先情報
		'zipCode1' => 'XXXXXXXXXXXXXXXXXX',				//郵便番号1
		'zipCode2' => 'XXXXXXXXXXXXXXXXXX',				//郵便番号2
		'prefecture' => 'XXXXXXXXXXXXXXXXXX',			//都道府県
		'city' => 'XXXXXXXXXXXXXXXXXX',					//市区町村
		'subAddress' => 'XXXXXXXXXXXXXXXXXX',			//それ以降の住所
		'familyName' => 'XXXXXXXXXXXXXXXXXX',			//姓漢字
		'firstName' => 'XXXXXXXXXXXXXXXXXX',			//名漢字
		'familyNameKana' => 'XXXXXXXXXXXXXXXXXX',		//姓カナ
		'firstNameKana' => 'XXXXXXXXXXXXXXXXXX',		//名カナ
		'phoneNumber1' => 'XXXXXXXXXXXXXXXXXX',			//電話番号1
		'phoneNumber2' => 'XXXXXXXXXXXXXXXXXX',			//電話番号2
		'phoneNumber3' => 'XXXXXXXXXXXXXXXXXX'			//電話番号3
	),
	'itemModel' => $iModel							//商品モデル
);


//受注情報モデルの生成
$oModel = array();
$oModel[] = array(
	'orderNumber' => 'XXXXXXXXXXXX',						//受注番号
	'status' => 'testtest',									//受注ステータス
	'shippingDate' =>  strtotime("YYYY-MM-DD HH:MM:SS"),	//配送日
	'shippingTerm' =>  'XXXXXXXXXXXXXXXXXX',				//希望時間帯
	'wshDeliveryDate' =>  strtotime("YYYY-MM-DD HH:MM:SS"),	//配送希望日
	'isTaxRecalc' => true,									//消費税再計算フラグ
	'enclosurePostagePrice' => 123456789,					//同梱送料合計
	'enclosureDeliveryPrice' => 123456789,					//同梱代引料合計
	'enclosureGoodsTax' => 123456789,						//同梱消費税合計
	'ordererModel' => array(								//注文者情報
		'zipCode1' => 'XXXXXXXXXXXXXXXXXX',						//郵便番号1
		'zipCode2' => 'XXXXXXXXXXXXXXXXXX',						//郵便番号2
		'prefecture' => 'XXXXXXXXXXXXXXXXXX',					//都道府県
		'city' => 'XXXXXXXXXXXXXXXXXX',							//市区町村
		'subAddress' => 'XXXXXXXXXXXXXXXXXX',					//それ以降の住所
		'familyName' => 'XXXXXXXXXXXXXXXXXX',					//姓漢字
		'firstName' => 'XXXXXXXXXXXXXXXXXX',					//名漢字
		'familyNameKana' => 'XXXXXXXXXXXXXXXXXX',				//姓カナ
		'firstNameKana' => 'XXXXXXXXXXXXXXXXXX',				//名カナ
		'phoneNumber1' => 'XXXXXXXXXXXXXXXXXX',					//電話番号1
		'phoneNumber2' => 'XXXXXXXXXXXXXXXXXX',					//電話番号2
		'phoneNumber3' => 'XXXXXXXXXXXXXXXXXX',					//電話番号3
		'emailAddress' => 'XXXXXXXXXXXXXXXXXX',					//メールアドレス
		'sex' => 'XXXXXXXXXXXXXXXXXX',							//性別
		'birthYear' => 'XXXXXXXXXXXXXXXXXX',					//誕生日(年)
		'birthMonth' => 'XXXXXXXXXXXXXXXXXX',					//誕生日(月)
		'birthDay' => 'XXXXXXXXXXXXXXXXXX'					//誕生日(日)
	),
	'wrappingModel1' => array(								//ラッピング1情報
		'title' => 'XXXXXXXXXXXXXXXXXX',						//ラッピングタイトル
		'name' => 'XXXXXXXXXXXXXXXXXX',							//ラッピング名
		'price' => 1234,										//料金
		'isIncludedTax' => false,								//税込別
		'deleteFlg' => false									//ラッピング削除フラグ
	),
	'wrappingModel2' => array(								//ラッピング2情報
		'title' => 'XXXXXXXXXXXXXXXXXX',						//ラッピングタイトル
		'name' => 'XXXXXXXXXXXXXXXXXX',							//ラッピング名
		'price' => 1234,										//料金
		'isIncludedTax' => false,								//税込別
		'deleteFlg' => false									//ラッピング削除フラグ
	),
	'packageModel' => $pModel								//送付先モデル
);

//受注検索モデル情報情報の作成
$serReq = array('orderModel' => $oModel);

$api = new UpdateOrderPractice();

//認証情報の設定
$api->setAuthRequest(
	array(
      	// 認証キー
       'authKey'  => AUTH_KEY,
       // 店舗URL
	   'shopUrl'   => SHOP_URL,
	   // ユーザ名
	   'userName' => USER_NAME));

//受注情報更新リクエスト情報の設定
$api->setServiceRequest($serReq);

//API実行
$res = $api->callOrderApi();

?>