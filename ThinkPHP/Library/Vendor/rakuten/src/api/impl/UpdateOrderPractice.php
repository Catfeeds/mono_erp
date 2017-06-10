<?php
// Copyright (c) Rakuten, Inc. All Rights Reserved.

//注文種別(オークション)
define("ORDER_TYPE_AUCTION" , 2);
//注文種別(共同購入)
define("ORDER_TYPE_GBUY" , 3);
 //同梱ステータス(親注文)
define("ENCSTATUS_PARENT" , 1);

/**
* <b>受注APIの一括注文情報更新実行の実装クラス。</b><br>
* <pre>
*   このクラスの内容はサンプルとしての処理になります。<br>
*   実際の運用にあわせて処理を実装してください。<br>
* </pre>
*
* @package api.impl
*/
class UpdateOrderPractice extends OrderApiPracticeBase implements OrderApiPractice {

	var $_util = null;

    /**
     * 受注APIの一括注文情報更新実行。<br>
     * <pre>
     * １．一括受注情報取得により、最新の情報を取得します。<br>
     * ２．一括受注情報取得で取得した結果に、リクエスで変更がある項目を設定します。<br>
     * ３．リクエストID取得を実施します。<br>
     * ４．取得したリクエストIDを設定します。<br>
     * ５．受注APIの一括注文情報更新処理を呼出します。<br>
     * </pre>
     *
     * @access public
     * @return 非同期受付結果情報
     */
    public function callOrderApi()
    {

    	//認証リクエストデータ取得
     	$userAuthModel = $this->_getAuthRequest();

     	//認証リクエストデータ取得結果判定
     	if (is_null($userAuthModel) || is_null($this->_srvReq)) {
        	return null;
        }

        //リクエスト連想配列(orderNumber, orderModel))の取得
        $orgOrder = $this->_getRequestOrderData();

        //最新の注文情報取得
		$baseOrder = $this->_getBaseOrderData($userAuthModel, $orgOrder);

        //リクエストID取得
        $reqIdBody = $this->_getReqestId($userAuthModel);
        //リクエストID取得結果判定
        if (is_null($reqIdBody) || !array_key_exists('requestId', $reqIdBody)) {
			print("リクエストID取得失敗。\n");
			return null;
		}
        print("リクエストID:". $reqIdBody->requestId ."\n");

		//リクエストデータ作成
		$this->_util = new SoapOrderModelUtil();
        $updateOrderRequestModel =
        	$this->_getUpdateOrderRequestModel($reqIdBody->requestId, $baseOrder, $orgOrder);

        //リクエストデータ作成結果判定
        if (is_null($updateOrderRequestModel)) {
			print("注文情報更新リクエストデータ不正。\n");
        	return null;
        }

 		//接続用オプション
    	$option = array('timeout' => 20);
        //API接続
    	$this->_connect($option);
        //リクエスト引数の設定
		$param = array('arg0' => $userAuthModel, 'arg1' => $updateOrderRequestModel);
		//API実行
		$body = $this->_client->updateOrder($param);
		//実行結果判定
		if ($this->_checkRespons($body)) {
			$body = $body->return;
		}

        //API切断
		$this->_disconnect();

		return $body;
    }


    /**
     * _getRequestOrderData
     *
     * @access private
     * @return リクエスト注文情報配列
     */
    private function _getRequestOrderData() {

    	$reqOrder = array();

   		//必須項目チェック
    	if (!array_key_exists('orderModel', $this->_srvReq)) {
	    		print("項目[orderModel]が存在しません。\n");
    			return null;
    	}

    	foreach ($this->_srvReq['orderModel'] as $model) {

   			//必須項目チェック
    		if (!array_key_exists('orderNumber', $model)) {
	    		print("項目[orderNumber]が存在しません。\n");
    			return null;
    		}

    		//リクエスト配列の作成
    		$reqOrder[$model['orderNumber']] = $model;
    	}
    	return $reqOrder;
 	}


    /**
     * _getBaseOrderData
     *
     * @access private
     * @param $userAuthModel
     * @param $reqOrder
     * @return 受注情報取得レスポンス情報
     */
    protected function _getBaseOrderData($userAuthModel, $reqOrder)
    {

   		//リクエストデータ作成
  	 	$soapObj = array();

  	 	//受注番号指定で受注情報取得を実施
    	foreach (array_keys($reqOrder) as $orderNumber) {
	    	$soapObj[] = $this->_toSoapVar('orderNumber', XSD_STRING, $orderNumber);
    	}
    	$orderReq = $this->_toSoapVar(null, SOAP_ENC_OBJECT, $soapObj);

   		//API実行
        $body = $this->_getOrder($userAuthModel, $orderReq);

	    //実行結果判定
		if (array_key_exists('return',$body)) {
			print("受注情報取得失敗。\n");
			return null;
		}

		if (MSGID_WARNING === $body->errorCode) {
			//個別エラーありの場合
			foreach ($body->unitError as $unitErr) {
				//更新対象から削除する。
				unset($reqOrder[$unitErr->orderKey]);
			}

			if (count($reqOrder) == 0) {
				System.out.println("全ての受注情報取得に失敗");
				return null;
			}
		}

		//取得結果を配列にする。
		$stdUtil = new StdClassOrderModelUtil();
		$orderList = $stdUtil->toArrayOrderModel($body->orderModel);

		return $orderList;
    }


    /**
     * _getUpdateOrderRequestModel
     *
     * @access private
     * @param $reqestId
     * @param $baseOrder
     * @param $orgOrder
     * @return リクエストデータ
     */
    private function _getUpdateOrderRequestModel($reqestId, $baseOrder, $orgOrder)
    {

    	$reqModel = array();

    	//リクエストデータ作成
		foreach ($baseOrder as &$baseModel){

			$orderNumber = $baseModel['orderNumber'];
    		$orgModel = $orgOrder[$orderNumber];

    		$model = $this->_getOrderModel($baseModel, $orgModel);

    		if (!$this->_util->isEmpty($model)) {

				$model = $this->_util->toSoapOrderModel($model);

				$reqModel[] = new SoapVar($model, SOAP_ENC_OBJECT, null, null, 'orderModel');
    		}
		}

    	//リクエストID設定
    	$reqModel[] = new SoapVar($reqestId, XSD_INTEGER, null, null, 'requestId');

    	return new SoapVar($reqModel, SOAP_ENC_OBJECT);
    }


    /**
     * _getOrderModel
     *
     * @access private
	 * @param $baseModel
	 * @param $orgModel
     * @return リクエストデータ
     */
    private function _getOrderModel(&$baseModel, $orgModel)
    {
		if ($this->_util->isEmpty($orgModel)) {
			return $baseModel;
		}

		//注文種別
		$orderType = $baseModel['orderType'];

		//同梱ステータス
		$enclosureStatus = 0;
		if (array_key_exists('enclosureStatus', $baseModel)) {
			$enclosureStatus = $baseModel['enclosureStatus'];
		}

		//受注ステータス
		$this->_getReqParameter($baseModel, $orgModel, 'status');
		//配送日
		$this->_getReqParameter($baseModel, $orgModel, 'shippingDate');
		//希望時間帯
		$this->_getReqParameter($baseModel, $orgModel, 'shippingTerm');
		//配送希望日
		$this->_getReqParameter($baseModel, $orgModel, 'wishDeliveryDate');
		//コメント
		$this->_getReqParameter($baseModel, $orgModel, 'option');
		//消費税再計算フラグ
		$this->_getReqParameter($baseModel, $orgModel, 'isTaxRecalc');

		//注文種別によって更新可能対象がかわる。
		if(ORDER_TYPE_AUCTION === $orderType) {
			//オークションの場合

			//送付先モデル(オークション用)
 			$this->_mergePackageModelAuction($baseModel, $orgModel);

		} else if(ORDER_TYPE_GBUY === $orderType) {
			//共同購入の場合

			//注文者モデル(共同購入用）
			$this->_mergePersonModelGbuy($baseModel, $orgModel, 'ordererModel');
			//支払方法モデル
			$this->_mergeSettlementModel($baseModel, $orgModel);
			//配送方法モデル
			$this->_mergeDeliveryModel($baseModel, $orgModel);
			//送付先モデル(共同購入用)
 			$this->_mergePackageModelGbuy($baseModel, $orgModel);

		} else {
			//上記以外の場合

			//注文者モデル
			$this->_mergePersonModel($baseModel, $orgModel, 'ordererModel');
			//支払方法モデル
			$this->_mergeSettlementModel($baseModel, $orgModel);
			//配送方法モデル
			$this->_mergeDeliveryModel($baseModel, $orgModel);
			//送付先モデル
			$this->_mergePackageModel($baseModel, $orgModel);
			//ラッピングモデル1
			$this->_mergeWrappingModel($baseModel, $orgModel, 'wrappingModel1');
			//ラッピングモデル2
			$this->_mergeWrappingModel($baseModel, $orgModel, 'wrappingModel2');
			//クーポンモデルリスト
			$this->_mergeCouponModel($baseModel, $orgModel);
		}


		//親注文の場合は同梱の料金関係の情報更新が可能
		if(ENCSTATUS_PARENT === $enclosureStatus) {
			//同梱送料合計
			$this->_getReqParameter($baseModel, $orgModel, 'enclosurePostagePrice');
			//同梱代引料合計
			$this->_getReqParameter($baseModel, $orgModel, 'enclosureDeliveryPrice');
			//同梱消費税合計
			$this->_getReqParameter($baseModel, $orgModel, 'enclosureGoodsTax');
		}

	   	return $baseModel;
    }

    /**
     * 注文者情報モデル、送付者情報モデルのマージ処理。<br>
     * <pre>
     * 一括受注情報取得で取得した結果にリクエスト内容を反映します。<br>
     * リクエスト内容が設定されていない（Null）の場合は、<br>
     * 一括受注情報取得で取得した結果を設定します。<br>
     * </pre>
     *
     * @access private
	 * @param $baseModel
	 * @param $orgModel
     * @param name
     * @return void
     */
    private function _mergePersonModel(&$baseModel, $orgModel, $name)
    {

    	//リクエストがない場合はなにもしない
		if ($this->_util->isEmpty($orgModel) || !array_key_exists($name, $orgModel)) {
				return;
	 	}

		$base = &$baseModel[$name];
		$org = $orgModel[$name];

    	//郵便番号1
   		$this->_getReqParameter($base, $org, 'zipCode1');
		//郵便番号2
   		$this->_getReqParameter($base, $org, 'zipCode2');
		//都道府県
   		$this->_getReqParameter($base, $org, 'prefecture');
		//市区町村
   		$this->_getReqParameter($base, $org, 'city');
		//それ以降の住所
   		$this->_getReqParameter($base, $org,  'subAddress');
		//姓漢字
   		$this->_getReqParameter($base, $org, 'familyName');
		//名漢字
   		$this->_getReqParameter($base, $org, 'firstName');
		//姓カナ
   		$this->_getReqParameter($base, $org, 'familyNameKana');
		//名カナ
   		$this->_getReqParameter($base, $org, 'firstNameKana');
		//電話番号1
   		$this->_getReqParameter($base, $org, 'phoneNumber1');
		//電話番号2
   		$this->_getReqParameter($base, $org, 'phoneNumber2');
		//電話番号3
   		$this->_getReqParameter($base, $org, 'phoneNumber3');

		if($name === 'ordererModel') {
			//メールアドレス
 	  		$this->_getReqParameter($base, $org, 'emailAddress');
			//性別
 	  		$this->_getReqParameter($base, $org,  'sex');
			//誕生日(年)
 	  		$this->_getReqParameter($base, $org, 'birthYear');
			//誕生日(月)
 	  		$this->_getReqParameter($base, $org,  'birthMonth');
			//誕生日(日)
 	  		$this->_getReqParameter($base, $org,  'birthDay');
		}
    }


   	/**
     * 注文種別が共同購入の場合の注文者情報モデル、送付者情報モデルのマージ処理。<br>
     * <pre>
     * 一括受注情報取得で取得した結果にリクエスト内容を反映します。<br>
     * リクエスト内容が設定されていない（Null）の場合は、<br>
     * 一括受注情報取得で取得した結果を設定します。<br>
     * </pre>
     *
     * @access private
	 * @param $baseModel
	 * @param $orgModel
     * @param $name
     * @return void
     */
	private function _mergePersonModelGbuy(&$baseModel, $orgModel, $name)
    {

    	//リクエストがない場合はなにもしない
		if ($this->_util->isEmpty($orgModel) || !array_key_exists($name, $orgModel)) {
				return;
	 	}

		$base = &$baseModel[$name];
		$org = $orgModel[$name];

    	//郵便番号1
   		$this->_getReqParameter($base, $org, 'zipCode1');
		//郵便番号2
   		$this->_getReqParameter($base, $org, 'zipCode2');
		//都道府県
   		$this->_getReqParameter($base, $org, 'prefecture');
		//市区町村
   		$this->_getReqParameter($base, $org, 'city');
		//それ以降の住所
   		$this->_getReqParameter($base, $org,  'subAddress');
		//姓漢字
   		$this->_getReqParameter($base, $org, 'familyName');
		//名漢字
   		$this->_getReqParameter($base, $org, 'firstName');
		//姓カナ
   		$this->_getReqParameter($base, $org, 'familyNameKana');
		//名カナ
   		$this->_getReqParameter($base, $org, 'firstNameKana');
		//電話番号1
   		$this->_getReqParameter($base, $org, 'phoneNumber1');
		//電話番号2
   		$this->_getReqParameter($base, $org, 'phoneNumber2');
		//電話番号3
   		$this->_getReqParameter($base, $org, 'phoneNumber3');

		if($name === 'ordererModel') {
			//メールアドレス
 	  		$this->_getReqParameter($base, $org, 'emailAddress');
		}
	}

	/**
     * 支払方法モデルのマージ処理。
     * <pre>
     * 一括受注情報取得で取得した結果にリクエスト内容を反映します。<br>
     * リクエスト内容が設定されていない（Null）の場合は、<br>
     * 一括受注情報取得で取得した結果を設定します。<br>
     * </pre>
     *
     * @access private
	 * @param $baseModel
	 * @param $orgModel
     * @return void
     */
	private function _mergeSettlementModel(&$baseModel, $orgModel)
    {

    	//リクエストがない場合はなにもしない
		if ($this->_util->isEmpty($orgModel) || !array_key_exists('settlementModel', $orgModel)) {
			return;
		}

		$base = &$baseModel['settlementModel'];
		$org = $orgModel['settlementModel'];

    	//支払方法名
   		$this->_getReqParameter($base, $org, 'settlementName');
	}

	/**
     * 配送方法モデルのマージ処理。
     * <pre>
     * 一括受注情報取得で取得した結果にリクエスト内容を反映します。<br>
     * リクエスト内容が設定されていない（Null）の場合は、<br>
     * 一括受注情報取得で取得した結果を設定します。<br>
     * </pre>
     *
     * @access private
	 * @param $baseList
	 * @param $orgList
     * @return void
     */
	private function _mergeDeliveryModel(&$baseModel, $orgModel)
	{
    	//リクエストがない場合はなにもしない
		if ($this->_util->isEmpty($orgModel) || !array_key_exists('deliveryModel', $orgModel)) {
			return;
		}

		$base = &$baseModel['deliveryModel'];
		$org = $orgModel['deliveryModel'];

    	//配送方法名
    	$this->_getReqParameter($base, $org, 'deliveryName');
	}

	/**
     * ラッピングモデルのマージ処理。
     * <pre>
     * 一括受注情報取得で取得した結果にリクエスト内容を反映します。<br>
     * リクエスト内容が設定されていない（Null）の場合は、<br>
     * 一括受注情報取得で取得した結果を設定します。<br>
     * </pre>
     *
     * @access private
	 * @param $baseModel
	 * @param $orgModel
	 * @param $name
     * @return void
     */
	private function _mergeWrappingModel(&$baseModel, $orgModel, $name)
	{

    	//リクエストがない場合はなにもしない
		if ($this->_util->isEmpty($orgModel) || !array_key_exists($name, $orgModel)) {
			return;
    	} else if ($this->_util->isEmpty($baseModel) || !array_key_exists($name, $baseModel)) {
			//ラッピング追加の場合
			$baseModel[$name] = $orgModel[$name];
    	}

    	$base = &$baseModel[$name];
		$org = $orgModel[$name];

		//ラッピングタイトル
    	$this->_getReqParameter($base, $org, 'title');
		//ラッピング名
    	$this->_getReqParameter($base, $org, 'name');
		//料金
    	$this->_getReqParameter($base, $org, 'price');
		//税込別
    	$this->_getReqParameter($base, $org, 'isIncludedTax');
		//ラッピング削除フラグ
    	$this->_getReqParameter($base, $org, 'deleteWrappingFlg');
	}


	/**
     * 送付先モデルリストのマージ処理。
     * <pre>
     * 一括受注情報取得で取得した結果にリクエスト内容を反映します。<br>
     * リクエスト内容が設定されていない（Null）の場合は、<br>
     * 一括受注情報取得で取得した結果を設定します。<br>
     * なお、送付先の追加はバックオフィスでは出来ない仕様です。
     * </pre>
     *
     * @access private
	 * @param $baseModel
	 * @param $orgModel
     * @return void
     */
	private function _mergePackageModel(&$baseModel, $orgModel)
	{

 	  	//リクエストがない場合はなにもしない
		if ($this->_util->isEmpty($orgModel) || !array_key_exists('packageModel', $orgModel)) {
			return;
		}


    	foreach ($baseModel['packageModel'] as &$base) {
    		foreach ($orgModel['packageModel'] as $org) {
    			if ($base['basketId'] === $org['basketId']) {
					//送料合計
					$this->_getReqParameter($base, $org, 'postagePrice');
					//代引料合計
					$this->_getReqParameter($base, $org, 'deliveryPrice');
					//消費税合計
					$this->_getReqParameter($base, $org, 'goodsTax');
					//発送番号
					$this->_getReqParameter($base, $org, 'shippingNumber');
					//削除フラグ
					$this->_getReqParameter($base, $org, 'deleteFlg');
					//配送業者ID
					$this->_getReqParameter($base, $org, 'deliveryCompanyId');

					//送付先情報
					$this->_mergePersonModel($base, $org, 'senderModel');
					//コンビニ配送情報
					$this->_mergeDeliveryCvsModel($base, $org);
					//商品情報
					$this->_mergeItemModel($base, $org);
					break;
				}
			}
		}
	}

    /**
     *
     * 注文種別がオークションの場合の送付先モデルリストのマージ処理。<br>
     * <pre>
     * 一括受注情報取得で取得した結果にリクエスト内容を反映します。<br>
     * リクエスト内容が設定されていない（Null）の場合は、<br>
     * 一括受注情報取得で取得した結果を設定します。<br>
     * なお、送付先の追加はバックオフィスでは出来ない仕様です。
     * </pre>
     *
     * @access private
	 * @param $baseModel
	 * @param $orgModel
     * @return void
     */
    private function _mergePackageModelAuction(&$baseModel, $orgModel)
    {

 	  	//リクエストがない場合はなにもしない
		if ($this->_util->isEmpty($orgModel) || !array_key_exists('packageModel', $orgModel)) {
			return;
		}

    	foreach ($baseModel['packageModel'] as &$base) {
    		foreach ($orgModel['packageModel'] as $org) {
    			if ($base['basketId'] === $org['basketId']) {
					//送料合計
					$this->_getReqParameter($base, $org, 'postagePrice');
					//代引料合計
					$this->_getReqParameter($base, $org, 'deliveryPrice');
					//消費税合計
					$this->_getReqParameter($base, $org, 'goodsTax');
					//発送番号
					$this->_getReqParameter($base, $org, 'shippingNumber');
					//削除フラグ
					$this->_getReqParameter($base, $org, 'deleteFlg');
					//配送業者ID
					$this->_getReqParameter($base, $org, 'deliveryCompanyId');
					break;
    			}
    		}
    	}
    }

	/**
     * 注文種別が共同購入の場合の送付先モデルリストのマージ処理。
     * <pre>
     * 一括受注情報取得で取得した結果にリクエスト内容を反映します。<br>
     * リクエスト内容が設定されていない（Null）の場合は、<br>
     * 一括受注情報取得で取得した結果を設定します。<br>
     * なお、送付先の追加はバックオフィスでは出来ない仕様です。
     * </pre>
     *
     * @access private
	 * @param $baseModel
	 * @param $orgModel
     * @return void
     */
	private function _mergePackageModelGbuy(&$baseModel, $orgModel)
	{

 	  	//リクエストがない場合はなにもしない
		if ($this->_util->isEmpty($orgModel) || !array_key_exists('packageModel', $orgModel)) {
			return;
		}

		//共同購入は1商品-1送付先なので商品IDで選別する。
		$base = &$baseModel['packageModel'];
		$baseItemId = $orgModel['itemModel']['itemId'];

		$org = $orgModel['packageModel'];
		$orgItemId = $orgModel['itemModel']['itemId'];

		if($baseItemId !== $orgItemId) {
			print("商品IDが一致しませんでした。共同購入は1商品-1送付先です。");
			print("一括受注情報取得で取得した商品ID:" . $baseItemId);
			print("リクエスト内容の商品ID:" . $baseItemId);
			return;
		}

		//送料合計
		$this->_getReqParameter($base, $org, 'postagePrice');
		//代引料合計
		$this->_getReqParameter($base, $org, 'deliveryPrice');
		//消費税合計
		$this->_getReqParameter($base, $org, 'goodsTax');
		//発送番号
		$this->_getReqParameter($base, $org, 'shippingNumber');
		//削除フラグ
		 $this->_getReqParameter($base, $org, 'deleteFlg');
		//配送業者ID
		$this->_getReqParameter($base, $org, 'deliveryCompanyId');

		//送付先情報
		$this->_mergePersonModelGbuy($base, $org, 'senderModel');
		//コンビニ配送情報
		$this->_mergeDeliveryCvsModel($base, $org);
		//商品情報(共同購入用）
		$this->_mergeItemModelGbuy($base, $org);

		}


	/**
     * コンビニ配送情報モデルのマージ処理。
     * <pre>
     * 一括受注情報取得で取得した結果にリクエスト内容を反映します。<br>
     * リクエスト内容が設定されていない（Null）の場合は、<br>
     * 一括受注情報取得で取得した結果を設定します。<br>
     * </pre>
     *
     * @access private
	 * @param $baseModel
	 * @param $orgModel
     * @return void
     */
	private function _mergeDeliveryCvsModel(&$baseModel, $orgModel)
	{

 	  	//リクエストがない場合はなにもしない
		if ($this->_util->isEmpty($orgModel) || !array_key_exists('deliveryCvsModel', $orgModel)) {
			return;
		}
		//一括受注情報取得で取得した結果がない場合はなにもしない
		if ($this->_util->isEmpty($baseModel) || !array_key_exists('deliveryCvsModel', $baseModel)) {
			return;
		}

		$base = &$baseModel['deliveryCvsModel'];
		$org = $orgModel['deliveryCvsModel'];


		//コンビニコード
		$this->_getReqParameter($base, $org, 'cvsCode');
		//ストア分類コード
		$this->_getReqParameter($base, $org, 'storeGenreCode');
		//ストアコード
		$this->_getReqParameter($base, $org, 'storeCode');
		//ストア名称
		$this->_getReqParameter($base, $org, 'storeName');
		//郵便番号
		$this->_getReqParameter($base, $org, 'storeZip');
		//都道府県
		$this->_getReqParameter($base, $org, 'storePrefecture');
		//その他住所
		$this->_getReqParameter($base, $org, 'storeAddress');
		//発注エリアコード
		$this->_getReqParameter($base, $org, 'areaCode');
		//センターデポコード
		$this->_getReqParameter($base, $org, 'depo');
		//開店時間
		$this->_getReqParameter($base, $org, 'cvsOpenTime');
		//閉店時間
		$this->_getReqParameter($base, $org, 'cvsCloseTime');
		//特記事項
		$this->_getReqParameter($base, $org, 'cvsBikou');

    }

	/**
     * 商品モデルリストのマージ処理。
     * <pre>
     * 一括受注情報取得で取得した結果にリクエスト内容を反映します。<br>
     * リクエスト内容が設定されていない（Null）の場合は、<br>
     * 一括受注情報取得で取得した結果を設定します。<br>
     * なお、商品情報の追加はバックオフィスでは出来ない仕様です。
     * </pre>
     *
     * @access private
	 * @param $baseModel
	 * @param $orgModel
     * @return void
     */
	private function _mergeItemModel(&$baseModel, $orgModel)
	{

 	  	//リクエストがない場合はなにもしない
		if ($this->_util->isEmpty($orgModel) || !array_key_exists('itemModel', $orgModel)) {
			return;
		}

  	 	foreach ($baseModel['itemModel'] as &$base) {
    		foreach ($orgModel['itemModel'] as $org) {
    			if ($base['basketId'] === $org['basketId']) {

					//商品名
					$this->_getReqParameter($base, $org, 'itemName');
 					//商品番号
					$this->_getReqParameter($base, $org, 'itemNumber');
					//単価
					$this->_getReqParameter($base, $org, 'price');
					//個数
					$this->_getReqParameter($base, $org, 'units');
					//送料込別
					$this->_getReqParameter($base, $org, 'isIncludedPostage');
					//税込別
					$this->_getReqParameter($base, $org, 'isIncludedTax');
					//代引手数料込別
					$this->_getReqParameter($base, $org, 'isIncludedCashOnDeliveryPostage');
					//項目・選択肢
					$this->_getReqParameter($base, $org, 'selectedChoice');
					//商品削除フラグ
					$this->_getReqParameter($base, $org, 'deleteItemFlg');
					break;
				}
			}

		}
	}


	/**
     * 注文種別が共同購入の場合の商品モデルリストのマージ処理。
     * <pre>
     * 一括受注情報取得で取得した結果にリクエスト内容を反映します。<br>
     * リクエスト内容が設定されていない（Null）の場合は、<br>
     * 一括受注情報取得で取得した結果を設定します。<br>
     * なお、商品情報の追加はバックオフィスでは出来ない仕様です。
     * </pre>
     *
     * @access private
	 * @param $baseModel
	 * @param $orgModel
     * @return void
     */
	private function _mergeItemModelGbuy(&$baseModel, $orgModel)
	{

 	  	//リクエストがない場合はなにもしない
		if ($this->_util->isEmpty($orgModel) || !array_key_exists('itemModel', $orgModel)) {
			return;
		}

		$base = &$baseModel['itemModel'];
		$org = $orgModel['itemModel'];

		//個数
		$this->_getReqParameter($base, $org, 'units');

		//共同購入内訳情報モデルの設定
		$this->_mergeGbuyBidInventoryModel($base, $org);
	}


	/**
     * 共同購入内訳情報モデルリストのマージ処理。
     * <pre>
     * 一括受注情報取得で取得した結果にリクエスト内容を反映します。<br>
     * リクエスト内容が設定されていない（Null）の場合は、<br>
     * 一括受注情報取得で取得した結果を設定します。<br>
     * なお、共同購入内訳情報の追加はバックオフィスでは出来ない仕様です。
     * </pre>
     *
     * @access private
	 * @param $baseModel
	 * @param $orgModel
     * @return void
     */
	private function _mergeGbuyBidInventoryModel(&$baseModel, $orgModel)
	{

		//リクエストがない場合、内訳情報が無い場合はなにもしない。
		if (($this->_util->isEmpty($orgModel))
			|| !array_key_exists('gbuyItemModel', $orgModel)
			|| !array_key_exists('gbuyBidInventoryModel', $orgModel['gbuyItemModel'])) {
			return;
    	}

		if (($this->_util->isEmpty($baseModel))
			|| !array_key_exists('gbuyItemModel', $baseModel)
			|| !array_key_exists('gbuyBidInventoryModel', $baseModel['gbuyItemModel'])) {
			return;
    	}

		foreach ($baseModel['gbuyItemModel']['gbuyBidInventoryModel'] as &$base) {

			foreach ($orgModel['gbuyItemModel']['gbuyBidInventoryModel'] as $org) {

    			if ($base['gchoiceId'] === $org['gchoiceId']) {

					//購入数
					$this->_getReqParameter($base, $org, 'bidUnits');

					break;
				}
			}
		}
	}


	/**
     * クーポンモデルリストのマージ処理。
     * <pre>
     * 一括受注情報取得で取得した結果にリクエスト内容を反映します。<br>
     * リクエスト内容が設定されていない（Null）の場合は、<br>
     * 一括受注情報取得で取得した結果を設定します。<br>
     * なお、クーポンの追加はバックオフィスでは出来ない仕様です。
     * </pre>
     *
     * @access private
	 * @param $baseModel
	 * @param $orgModel
     * @return void
     */
	private function _mergeCouponModel(&$baseModel, $orgModel)
	{

		//リクエストがない場合、クーポン情報が無い場合はなにもしない。
		if ($this->_util->isEmpty($orgModel) || !array_key_exists('couponModel', $orgModel)) {
			return;
    	}

		if ($this->_util->isEmpty($baseModel) || !array_key_exists('couponModel', $baseModel)) {
			return;
    	}

    	foreach ($baseModel['couponModel'] as &$base) {

    		foreach ($orgModel['couponModel'] as $org) {

    			if ($base['couponCode'] === $org['couponCode']) {
    				//クーポン利用数
					$this->_getReqParameter($base, $org, 'couponUnit');
					break;
				}
			}
		}
	}


	/**
     * マージする項目の判定処理。<br>
     * <pre>
     * 一括受注情報取得で取得した値とリクエストの値を判定します。<br>
     * リクエストの値がNullまたは一括受注情報取得で取得した値と一致している場合は、一括受注情報取得で取得した値でリクエストの値を返します。<br>
     * リクエストの値が一括受注情報取得で取得した値と一致していない場合は、リクエストの値を返します。<br>
     * </pre>
     *
     * @access private
	 * @param $base
	 * @param $org
	 * @param $name
     * @return void
     */
    private function _getReqParameter(&$base, array $org, $name)
    {

    	if ($this->_util->isEmpty($base) && $this->_util->isEmpty($org)) {
    		return;
    	}

    	if (!array_key_exists($name, $base) &&
    		!array_key_exists($name, $org)) {
    		return;
    	}

    	if (is_null($base) || !array_key_exists($name, $base)) {

    		if (array_key_exists($name, $org)) {
				//元にない（項目追加）
				$base[$name] = $org[$name];
    		}

    	} else {

    		if (!is_null($org) && array_key_exists($name, $org)) {
    			 if ($base[$name] !== $org[$name]) {
	    			//リクエストがNullでない。かつ変更がある。
	 	 			$base[$name] = $org[$name];
	    		}
 	 		}
    	}
    }
}

?>