<?php

/**
*  <b>受注情報モデルのSOAPパラメータ変換用クラス。</b><br>
*    このクラスの内容はサンプルとしての処理になります。<br>
*    実際の運用にあわせて処理を実装してください。<br>
*
*  @package api.util
*/
class SoapOrderModelUtil {


	/**
     * 受注情報モデルのSoapオブジェクト生成処理。
     *
     * @access public
	 * @param $obj array
     * @return Soapオブジェクト array
     */
	public function toSoapOrderModel(array $obj)
	{

		//リクエスト情報が無い場合はなにもしない。
		if ($this->isEmpty($obj)) {
    		return null;
    	}

    	//Soap設定オブジェクト
    	$soapVal = array();

    	//受注番号
		$this->_toSoapVar($soapVal, 'orderNumber', XSD_STRING, $obj);
    	//受注ステータス
		$this->_toSoapVar($soapVal, 'status', XSD_STRING, $obj);
    	//決済ステータス
		$this->_toSoapVar($soapVal, 'cardStatus', XSD_INTEGER, $obj);
    	//入金日
		$this->_toSoapVar($soapVal, 'paymentDate', XSD_DATETIME, $obj);
    	//配送日
		$this->_toSoapVar($soapVal, 'shippingDate', XSD_DATETIME, $obj);
    	//希望時間帯
		$this->_toSoapVar($soapVal, 'shippingTerm', XSD_STRING, $obj);
		//配送希望日
		$this->_toSoapVar($soapVal, 'wishDeliveryDate', XSD_DATETIME, $obj);
		//担当者
		$this->_toSoapVar($soapVal, 'operator', XSD_STRING, $obj);
		//ひとことメモ
		$this->_toSoapVar($soapVal, 'memo', XSD_STRING, $obj);
		//メール差込文(お客様へのメッセージ)
		$this->_toSoapVar($soapVal, 'mailPlugSentence', XSD_STRING, $obj);
		//初期購入合計金額
		$this->_toSoapVar($soapVal, 'firstAmount', XSD_INTEGER, $obj);
		//利用端末
		$this->_toSoapVar($soapVal, 'carrierCode', XSD_INTEGER, $obj);
		//メールキャリアコード
		$this->_toSoapVar($soapVal, 'emailCarrierCode', XSD_INTEGER, $obj);
		//ギフト配送希望
		$this->_toSoapVar($soapVal, 'isGiftCheck', XSD_BOOLEAN, $obj);
		//更新シーケンスID
		$this->_toSoapVar($soapVal, 'seqId', XSD_INTEGER, $obj);
		//コメント
		$this->_toSoapVar($soapVal, 'option', XSD_STRING, $obj);
		//注文日時
		$this->_toSoapVar($soapVal, 'orderDate', XSD_DATETIME, $obj);
		//注文種別
		$this->_toSoapVar($soapVal, 'orderType', XSD_INTEGER, $obj);
		//複数送付先フラグ
		$this->_toSoapVar($soapVal, 'isGift', XSD_BOOLEAN, $obj);
		//ブラック注文
		$this->_toSoapVar($soapVal, 'isBlackUser', XSD_BOOLEAN, $obj);
		//楽天会員フラグ
		$this->_toSoapVar($soapVal, 'isRakutenMember', XSD_BOOLEAN, $obj);
		//消費税再計算フラグ
		$this->_toSoapVar($soapVal, 'isTaxRecalc', XSD_BOOLEAN, $obj);
		//同梱可能フラグ
		$this->_toSoapVar($soapVal, 'canEnclosure', XSD_BOOLEAN, $obj);
		//商品合計金額
		$this->_toSoapVar($soapVal, 'goodsPrice', XSD_LONG, $obj);
		//消費税
		$this->_toSoapVar($soapVal, 'goodsTax', XSD_INTEGER, $obj);
		//送料
		$this->_toSoapVar($soapVal, 'goodsTax', XSD_INTEGER, $obj);
		//代引料
		$this->_toSoapVar($soapVal, 'deliveryPrice', XSD_INTEGER, $obj);
		//請求金額
		$this->_toSoapVar($soapVal, 'requestPrice', XSD_LONG, $obj);
		//合計金額
		$this->_toSoapVar($soapVal, 'totalPrice', XSD_LONG, $obj);
		//同梱ID
		$this->_toSoapVar($soapVal, 'enclosureId', XSD_INTEGER, $obj);
		//同梱商品合計金額
		$this->_toSoapVar($soapVal, 'enclosureGoodsPrice', XSD_LONG, $obj);
		//同梱送料合計
		$this->_toSoapVar($soapVal, 'enclosurePostagePrice', XSD_INTEGER, $obj);
		//同梱代引料合計
		$this->_toSoapVar($soapVal, 'enclosureDeliveryPrice', XSD_INTEGER, $obj);
		//同梱消費税合計
		$this->_toSoapVar($soapVal, 'enclosureGoodsTax', XSD_INTEGER, $obj);
		//同梱ステータス
		$this->_toSoapVar($soapVal, 'enclosureStatus', XSD_INTEGER, $obj);
		//同梱請求金額
		$this->_toSoapVar($soapVal, 'enclosureRequestPrice', XSD_LONG, $obj);
		//同梱合計金額
		$this->_toSoapVar($soapVal, 'enclosureTotalPrice', XSD_LONG, $obj);
		//同梱楽天バンク決済振替手数料
		$this->_toSoapVar($soapVal, 'enclosureRbankTransferCommission', XSD_INTEGER, $obj);
		//同梱ポイント利用合計
		$this->_toSoapVar($soapVal, 'enclosurePointPrice', XSD_INTEGER, $obj);
		//同梱クーポン利用合計
		$this->_toSoapVar($soapVal, 'enclosureCouponPrice', XSD_INTEGER, $obj);
		//購入履歴修正アイコンフラグ
		$this->_toSoapVar($soapVal, 'modify', XSD_BOOLEAN, $obj);
		//あす楽希望フラグ
		$this->_toSoapVar($soapVal, 'asurakuFlg', XSD_STRING, $obj);
		//楽天メンバーID
		$this->_toSoapVar($soapVal, 'rmId', XSD_INTEGER, $obj);
		//クーポン利用総額
		$this->_toSoapVar($soapVal, 'couponAllTotalPrice', XSD_LONG, $obj);
		//店舗発行クーポン利用額
		$this->_toSoapVar($soapVal, 'couponShopPrice', XSD_LONG, $obj);
		//楽天発行クーポン利用額
		$this->_toSoapVar($soapVal, 'couponOtherPrice', XSD_LONG, $obj);
		//クーポン利用数総合計
		$this->_toSoapVar($soapVal, 'couponAllTotalUnit', XSD_INTEGER, $obj);
		//店舗発行クーポン利用数
		$this->_toSoapVar($soapVal, 'couponShopTotalUnit', XSD_INTEGER, $obj);
		//楽天発行クーポン利用数
		$this->_toSoapVar($soapVal, 'couponOtherTotalUnit', XSD_INTEGER, $obj);


		//注文者情報モデル
		if (array_key_exists('ordererModel', $obj)) {
			$soapVal[] = $this->_toSoapPersonModel($obj['ordererModel'], 'ordererModel');
		}

		//通常注文モデル
		if (array_key_exists('normalOrderModel', $obj)) {
			$soapVal[] = $this->_toSoapNormalOrderModel($obj['normalOrderModel']);
		}

		//オークション注文モデル
		if (array_key_exists('saOrderModel', $obj)) {
			$soapVal[] = $this->_toSoapSaOrderModel($obj['saOrderModel']);
		}

		//共同購入注文モデル
		if (array_key_exists('gbuyOrderModel', $obj)) {
			$soapVal[] = $this->_toSoapGbuyOrderModel($obj['gbuyOrderModel']);
		}

		//支払方法モデル
		if (array_key_exists('settlementModel', $obj)) {
			$soapVal[] = $this->_toSoapSettlementModel($obj['settlementModel']);
		}

		//配送方法モデル
		if (array_key_exists('deliveryModel', $obj)) {
			$soapVal[] = $this->_toSoapDeliveryModel($obj['deliveryModel']);
		}

		//ポイントモデル
		if (array_key_exists('pointModel', $obj)) {
			$soapVal[] = $this->_toSoapPointModel($obj['pointModel']);
		}

		//楽天バンクモデル
		if (array_key_exists('rBankModel', $obj)) {
			$soapVal[] = $this->_toSoapRBankModel($obj['rBankModel']);
		}

		//ラッピングモデル1
		if (array_key_exists('wrappingModel1', $obj)) {
			$soapVal[] = $this->_toSoapWrappingModel($obj['wrappingModel1'], 'wrappingModel1');
		}

		//ラッピングモデル2
		if (array_key_exists('wrappingModel2', $obj)) {
			$soapVal[] = $this->_toSoapWrappingModel($obj['wrappingModel2'], 'wrappingModel2');
		}

		//送付先モデルリスト
		if (array_key_exists('packageModel', $obj)) {
			foreach ($obj['packageModel'] as $model) {
				$soapVal[] = $this->_toSoapPackageModel($model);
			}
		}

		//同梱子注文モデルリスト
		if (array_key_exists('childOrderModel', $obj)) {
			foreach ($obj['childOrderModel'] as $model) {
				$soapVal[] = $this->_toSoapChildOrderModel($model);
			}
		}

		//クーポンモデルリスト
		if (array_key_exists('couponModel', $obj)) {
			foreach ($obj['couponModel'] as $model) {
				$soapVal[] = $this->_toSoapCouponModel($obj);
			}
		}

		return $soapVal;
	}


	/**
     * 注文者情報モデルまたは送付者情報モデルのSoapオブジェクト生成処理。
     *
     * @access private
	 * @param $obj array
	 * @param $name
     * @return SoapVarオブジェクト
     */
	private function _toSoapPersonModel(array $obj, $name)
	{

    	//Soap設定オブジェクト
    	$soapVal = array();

    	//郵便番号1
		$this->_toSoapVar($soapVal, 'zipCode1', XSD_STRING, $obj);
    	//郵便番号2
		$this->_toSoapVar($soapVal, 'zipCode2', XSD_STRING, $obj);
    	//都道府県
		$this->_toSoapVar($soapVal, 'prefecture', XSD_STRING, $obj);
    	//市区町村
		$this->_toSoapVar($soapVal, 'city', XSD_STRING, $obj);
    	//それ以降の住所
		$this->_toSoapVar($soapVal, 'subAddress', XSD_STRING, $obj);
    	//姓漢字
		$this->_toSoapVar($soapVal, 'familyName', XSD_STRING, $obj);
    	//名漢字
		$this->_toSoapVar($soapVal, 'firstName', XSD_STRING, $obj);
    	//姓カナ
		$this->_toSoapVar($soapVal, 'familyNameKana', XSD_STRING, $obj);
    	//名カナ
		$this->_toSoapVar($soapVal, 'firstNameKana', XSD_STRING, $obj);
    	//電話番号1
		$this->_toSoapVar($soapVal, 'phoneNumber1', XSD_STRING, $obj);
    	//電話番号2
		$this->_toSoapVar($soapVal, 'phoneNumber2', XSD_STRING, $obj);
    	//電話番号3
		$this->_toSoapVar($soapVal, 'phoneNumber3', XSD_STRING, $obj);
    	//メールアドレス
		$this->_toSoapVar($soapVal, 'emailAddress', XSD_STRING, $obj);
    	//性別
		$this->_toSoapVar($soapVal, 'sex', XSD_STRING, $obj);
    	//誕生日(年)
		$this->_toSoapVar($soapVal, 'birthYear', XSD_STRING, $obj);
    	//誕生日(月)
		$this->_toSoapVar($soapVal, 'birthMonth', XSD_STRING, $obj);
    	//誕生日(日)
		$this->_toSoapVar($soapVal, 'birthDay', XSD_STRING, $obj);
    	//ニックネーム
		$this->_toSoapVar($soapVal, 'nickname', XSD_STRING, $obj);

		return new SoapVar($soapVal, SOAP_ENC_OBJECT, null, null, $name);
	}

	/**
     * 通常注文モデルのSoapオブジェクト生成処理。
     *
     * @access private
	 * @param $obj array
     * @return SoapVarオブジェクト
     */
	private function _toSoapNormalOrderModel(array $obj)
	{

    	//Soap設定オブジェクト
    	$soapVal = array();

    	//定期購入申込番号
		$this->_toSoapVar($soapVal, 'reserveNumber', XSD_STRING, $obj);
    	//定期購入詳細ID
		$this->_toSoapVar($soapVal, 'detailId', XSD_INTEGER, $obj);
    	//定期購入商品種別
		$this->_toSoapVar($soapVal, 'reserveType', XSD_INTEGER, $obj);
    	//定期購入申込日時
		$this->_toSoapVar($soapVal, 'reserveDatetime', XSD_DATETIME, $obj);

    	return new SoapVar($soapVal, SOAP_ENC_OBJECT, null, null, 'normalOrderModel');
	}

	/**
     * オークション注文モデルのSoapオブジェクト生成処理。
     *
     * @access private
	 * @param $obj array
     * @return SoapVarオブジェクト
     */
	private function _toSoapSaOrderModel(array $obj)
	{

    	//Soap設定オブジェクト
    	$soapVal = array();

    	//ビッドID
		$this->_toSoapVar($soapVal, 'bidId', XSD_INTEGER, $obj);
    	//結果発表日
		$this->_toSoapVar($soapVal, 'regDate', XSD_DATETIME, $obj);
    	//コメント
		$this->_toSoapVar($soapVal, 'comment', XSD_STRING, $obj);

    	return new SoapVar($soapVal, SOAP_ENC_OBJECT, null, null, 'saOrderModel');
	}


	/**
     * 共同購入注文モデルのSoapオブジェクト生成処理。
     *
     * @access private
	 * @param $obj array
     * @return SoapVarオブジェクト
     */
	private function _toSoapGbuyOrderModel(array $obj)
	{

    	//Soap設定オブジェクト
    	$soapVal = array();

    	//ビッドID
		$this->_toSoapVar($soapVal, 'bidId', XSD_INTEGER, $obj);
    	//コメント
		$this->_toSoapVar($soapVal, 'comment', XSD_STRING, $obj);

    	return new SoapVar($soapVal, SOAP_ENC_OBJECT, null, null, 'gbuyOrderModel');
	}


	/**
     * 支払方法モデルのSoapオブジェクト生成処理。
     *
     * @access private
	 * @param $obj array
     * @return SoapVarオブジェクト
     */
	private function _toSoapSettlementModel(array $obj)
	{

    	//Soap設定オブジェクト
    	$soapVal = array();

    	//支払方法名
		$this->_toSoapVar($soapVal, 'settlementName', XSD_STRING, $obj);

		//クレジットカードモデル
		if (array_key_exists('cardModel', $obj)) {
			$soapVal[] = $this->_toSoapCardModel($obj['cardModel']);
		}

    	return new SoapVar($soapVal, SOAP_ENC_OBJECT, null, null, 'settlementModel');
	}


	/**
     * クレジットカードモデルのSoapオブジェクト生成処理。
     *
     * @access private
	 * @param $obj array
     * @return SoapVarオブジェクト
     */
	private function _toSoapCardModel(array $obj)
	{

    	//Soap設定オブジェクト
    	$soapVal = array();

    	//ブランド名
		$this->_toSoapVar($soapVal, 'brandName', XSD_STRING, $obj);
    	//カード番号
		$this->_toSoapVar($soapVal, 'cardNo', XSD_STRING, $obj);
    	//名義人
		$this->_toSoapVar($soapVal, 'ownerName', XSD_STRING, $obj);
    	//有効期限
		$this->_toSoapVar($soapVal, 'expYM', XSD_STRING, $obj);
    	//分割選択（支払種別）
		$this->_toSoapVar($soapVal, 'payType', XSD_INTEGER, $obj);
    	//分割備考
		$this->_toSoapVar($soapVal, 'installmentDesc', XSD_STRING, $obj);

    	return new SoapVar($soapVal, SOAP_ENC_OBJECT, null, null, 'cardModel');
	}


	/**
     * 配送方法モデルのSoapオブジェクト生成処理。
     *
     * @access private
	 * @param $obj array
     * @return SoapVarオブジェクト
     */
	private function _toSoapDeliveryModel(array $obj)
	{

    	//Soap設定オブジェクト
    	$soapVal = array();

    	//配送方法名
		$this->_toSoapVar($soapVal, 'deliveryName', XSD_STRING, $obj);
		//配送区分
		$this->_toSoapVar($soapVal, 'deliveryClass', XSD_INTEGER, $obj);

    	return new SoapVar($soapVal, SOAP_ENC_OBJECT, null, null, 'deliveryModel');
	}


	/**
     * ポイントモデルのSoapオブジェクト生成処理。
     *
     * @access private
	 * @param $obj array
     * @return SoapVarオブジェクト
     */
	private function _toSoapPointModel(array $obj)
	{
    	//Soap設定オブジェクト
    	$soapVal = array();

    	//充当ポイント
		$this->_toSoapVar($soapVal, 'usedPoint', XSD_INTEGER, $obj);
		//使用条件
		$this->_toSoapVar($soapVal, 'pointUsage', XSD_INTEGER, $obj);
		//承認状態
		$this->_toSoapVar($soapVal, 'status', XSD_INTEGER, $obj);

    	return new SoapVar($soapVal, SOAP_ENC_OBJECT, null, null, 'pointModel');
	}


	/**
     * 楽天バンクモデルのSoapオブジェクト生成処理。
     *
     * @access private
	 * @param $obj array
     * @return SoapVarオブジェクト
     */
	private function _toSoapRBankModel(array $obj)
	{

    	//Soap設定オブジェクト
    	$soapVal = array();

    	//受注番号
		$this->_toSoapVar($soapVal, 'orderNumber', XSD_STRING, $obj);
		//店舗ID
		$this->_toSoapVar($soapVal, 'shopId', XSD_INTEGER, $obj);
		//バンク決済ステータス
		$this->_toSoapVar($soapVal, 'rbankStatus', XSD_INTEGER, $obj);
		//振替手数料負担区分
		$this->_toSoapVar($soapVal, 'rbCommissionPayer', XSD_INTEGER, $obj);
		//振替手数料
		$this->_toSoapVar($soapVal, 'transferCommission', XSD_INTEGER, $obj);

		return new SoapVar($soapVal, SOAP_ENC_OBJECT, null, null, 'rBankModel');
	}


	/**
     * ラッピングモデルのSoapオブジェクト生成処理。
     *
     * @access private
	 * @param $obj array
	 * @param $name
     * @return SoapVarオブジェクト
     */
	private function _toSoapWrappingModel(array $obj, $name)
	{
    	//Soap設定オブジェクト
    	$soapVal = array();

    	//ラッピングタイトル
		$this->_toSoapVar($soapVal, 'title', XSD_STRING, $obj);
		//ラッピング名
		$this->_toSoapVar($soapVal, 'name', XSD_STRING, $obj);
		//料金
		$this->_toSoapVar($soapVal, 'price', XSD_INTEGER, $obj);
		//税込別
		$this->_toSoapVar($soapVal, 'isIncludedTax', XSD_BOOLEAN, $obj);
		//ラッピング削除フラグ
		$this->_toSoapVar($soapVal, 'deleteWrappingFlg', XSD_BOOLEAN, $obj);

		return new SoapVar($soapVal, SOAP_ENC_OBJECT, null, null, $name);
	}


	/**
     * 送付先モデルのSoapオブジェクト生成処理。
     *
     * @access private
	 * @param $obj array
     * @return SoapVarオブジェクト array
     */
	private function _toSoapPackageModel(array $obj)
	{
		//Soap設定オブジェクト
		$soapVal = array();

	   	//送付先キー
		$this->_toSoapVar($soapVal, 'basketId', XSD_INTEGER, $obj);
		//送料合計
		$this->_toSoapVar($soapVal, 'postagePrice', XSD_INTEGER, $obj);
		//代引料合計
		$this->_toSoapVar($soapVal, 'deliveryPrice', XSD_INTEGER, $obj);
		//消費税合計
		$this->_toSoapVar($soapVal, 'goodsTax', XSD_INTEGER, $obj);
		//商品合計金額
		$this->_toSoapVar($soapVal, 'goodsPrice', XSD_LONG, $obj);
		//のし
		$this->_toSoapVar($soapVal, 'noshi', XSD_STRING, $obj);
		//発送番号
		$this->_toSoapVar($soapVal, 'shippingNumber', XSD_STRING, $obj);
		//削除フラグ
		$this->_toSoapVar($soapVal, 'deleteFlg', XSD_BOOLEAN, $obj);
		//配送業者ID
		$this->_toSoapVar($soapVal, 'deliveryCompanyId', XSD_STRING, $obj);

		//送付先情報
		if (array_key_exists('senderModel', $obj)) {
			$soapVal[] = $this->_toSoapPersonModel($obj['senderModel'], 'senderModel');
		}

		//コンビニ配送情報
		if (array_key_exists('deliveryCvsModel', $obj)) {
			$soapVal[] = $this->_toSoapDeliveryCvsModel($obj['deliveryCvsModel']);
		}

		//商品モデルリスト
		if (array_key_exists('itemModel', $obj)) {
			foreach ($obj['itemModel'] as $model) {
				$soapVal[] = $this->_toSoapItemModel($model);
			}
		}

		return new SoapVar($soapVal, SOAP_ENC_OBJECT, null, null, 'packageModel');
	}


	/**
     * コンビニ配送情報のSoapオブジェクト生成処理。
     *
     * @access private
	 * @param $obj array
     * @return SoapVarオブジェクト
     */
	private function _toSoapDeliveryCvsModel(array $obj)
	{

	   	//Soap設定オブジェクト
    	$soapVal = array();

		//コンビニコード
		$this->_toSoapVar($soapVal, 'cvsCode', XSD_INTEGER, $obj);
		//ストア分類コード
		$this->_toSoapVar($soapVal, 'storeGenreCode', XSD_STRING, $obj);
		//ストアコード
		$this->_toSoapVar($soapVal, 'storeCode', XSD_STRING, $obj);
		//郵便番号
		$this->_toSoapVar($soapVal, 'storeZip', XSD_STRING, $obj);
		//都道府県
		$this->_toSoapVar($soapVal, 'storePrefecture', XSD_STRING, $obj);
		//その他住所
		$this->_toSoapVar($soapVal, 'storeAddress', XSD_STRING, $obj);
		//発注エリアコード
		$this->_toSoapVar($soapVal, 'areaCode', XSD_STRING, $obj);
		//センターデポコード
		$this->_toSoapVar($soapVal, 'depo', XSD_STRING, $obj);
		//開店時間
		$this->_toSoapVar($soapVal, 'cvsOpenTime', XSD_STRING, $obj);
		//閉店時間
		$this->_toSoapVar($soapVal, 'cvsCloseTime', XSD_STRING, $obj);
		//特記事項
		$this->_toSoapVar($soapVal, 'cvsBikou', XSD_STRING, $obj);


    	return new SoapVar($soapVal, SOAP_ENC_OBJECT, null, null, 'deliveryCvsModel');
	}


	/**
     * 商品モデルのSoapオブジェクト生成処理。
     *
     * @access private
	 * @param $obj array
     * @return SoapVarオブジェクト array
     */
	private function _toSoapItemModel(array $obj)
	{

		//Soap設定オブジェクト
    	$soapVal = array();

    	//商品キー
		$this->_toSoapVar($soapVal, 'basketId', XSD_INTEGER, $obj);
		//商品ID
		$this->_toSoapVar($soapVal, 'itemId', XSD_INTEGER, $obj);
		//商品名
		$this->_toSoapVar($soapVal, 'itemName', XSD_STRING, $obj);
		//商品番号
		$this->_toSoapVar($soapVal, 'itemNumber', XSD_STRING, $obj);
		//商品URL
		$this->_toSoapVar($soapVal, 'pageURL', XSD_STRING, $obj);
		//単価
		$this->_toSoapVar($soapVal, 'price', XSD_INTEGER, $obj);
		//個数
		$this->_toSoapVar($soapVal, 'units', XSD_INTEGER, $obj);
		//送料込別
		$this->_toSoapVar($soapVal, 'isIncludedPostage', XSD_BOOLEAN, $obj);
		//税込別
		$this->_toSoapVar($soapVal, 'isIncludedTax', XSD_BOOLEAN, $obj);
		//代引手数料込別
		$this->_toSoapVar($soapVal, 'isIncludedCashOnDeliveryPostage', XSD_BOOLEAN, $obj);
		//項目・選択肢
		$this->_toSoapVar($soapVal, 'selectedChoice', XSD_STRING, $obj);
		//ポイントレート
		$this->_toSoapVar($soapVal, 'pointRate', XSD_INTEGER, $obj);
		//ポイントタイプ
		$this->_toSoapVar($soapVal, 'pointType', XSD_INTEGER, $obj);
		//商品削除フラグ
		$this->_toSoapVar($soapVal, 'deleteItemFlg', XSD_BOOLEAN, $obj);
		//在庫連動オプション
		$this->_toSoapVar($soapVal, 'restoreInventoryFlag', XSD_INTEGER, $obj);

		//通常商品モデル
		if (array_key_exists('normalItemModel', $obj)) {
			$soapVal[] = $this->_toSoapNormalItemModel($obj['normalItemModel']);
		}

		///オークション商品モデル
		if (array_key_exists('saItemModel', $obj)) {
			$soapVal[] = $this->_toSoapSaItemModel($obj['saItemModel']);
		}

		//共同購入商品モデル
		if (array_key_exists('gbuyItemModel', $obj)) {
			$soapVal[] = $this->_toSoapGbuyItemModel($obj['gbuyItemModel']);
		}

		return new SoapVar($soapVal, SOAP_ENC_OBJECT, null, null, 'itemModel');
	}


	/**
     * 通常商品モデルのSoapオブジェクト生成処理。
     *
     * @access private
	 * @param $obj array
     * @return SoapVarオブジェクト
     */
	private function _toSoapNormalItemModel(array $obj)
	{

		//Soap設定オブジェクト
    	$soapVal = array();

    	//納期情報
		$this->_toSoapVar($soapVal, 'delvdateInfo', XSD_STRING, $obj);
		//在庫タイプ
		$this->_toSoapVar($soapVal, 'inventoryType', XSD_STRING, $obj);

    	return new SoapVar($soapVal, SOAP_ENC_OBJECT, null, null, 'normalItemModel');
	}


	/**
     * オークション商品モデルのSoapオブジェクト生成処理。
     *
     * @access private
	 * @param $obj array
     * @return SoapVarオブジェクト
     */
	private function _toSoapSaItemModel(array $obj)
	{

    	//現在は項目が存在しない。
    	return new SoapVar(null, SOAP_ENC_OBJECT, null, null, 'saItemModel');
	}


	/**
     * 共同購入商品モデルのSoapオブジェクト生成処理。
     *
     * @access private
	 * @param $obj array
     * @return SoapVarオブジェクト
     */
	private function _toSoapGbuyItemModel(array $obj)
	{

		//Soap設定オブジェクト
    	$soapVal = array();

		//移行済
		$this->_toSoapVar($soapVal, 'isShiftStatus', XSD_BOOLEAN, $obj);
		//移行日時
		$this->_toSoapVar($soapVal, 'shiftDate', XSD_DATETIME, $obj);
		//商品単位
		$this->_toSoapVar($soapVal, 'unitText', XSD_STRING, $obj);
		//実販売数
		$this->_toSoapVar($soapVal, 'currentSumAmount', XSD_INTEGER, $obj);

		//共同購入商品内訳モデルリスト
		if (array_key_exists('gbuyGchoiceModel', $obj)) {
			foreach ($obj['gbuyGchoiceModel'] as $model) {
				$soapVal[] = $this->_toSoapGbuyGchoiceModel($model);
			}
		}

		//共同購入内訳情報モデルリスト
		if (array_key_exists('gbuyBidInventoryModel', $model)) {
			foreach ($obj['gbuyBidInventoryModel'] as $model) {
				$soapVal[] = $this->_toSoapGbuyBidInventoryModel($model);
			}
		}

		return new SoapVar($soapVal, SOAP_ENC_OBJECT, null, null, 'gbuyItemModel');;
	}


	/**
     * 共同購入商品内訳モデルのSoapオブジェクト生成処理。
     *
     * @access private
	 * @param $obj array
     * @return SoapVarオブジェクト array
     */
	private function _toSoapGbuyGchoiceModel(array $obj)
	{

		//Soap設定オブジェクト
    	$soapVal = array();

    	//商品内訳ID
		$this->_toSoapVar($soapVal, 'gchoiceId', XSD_INTEGER, $obj);
		//商品ID
		$this->_toSoapVar($soapVal, 'itemId', XSD_INTEGER, $obj);
		//表示順序
		$this->_toSoapVar($soapVal, 'orderby', XSD_INTEGER, $obj);
		//項目名
		$this->_toSoapVar($soapVal, 'gchoiceName', XSD_STRING, $obj);
		//取り扱い個数
		$this->_toSoapVar($soapVal, 'gchoiceInvtry', XSD_INTEGER, $obj);
		//最大購入個数
		$this->_toSoapVar($soapVal, 'gchoiceMaxUnits', XSD_INTEGER, $obj);
		//内訳購入数量合計
		$this->_toSoapVar($soapVal, 'sumAmount', XSD_INTEGER, $obj);
		//売り切れフラグ
		$this->_toSoapVar($soapVal, 'soldFlag', XSD_INTEGER, $obj);

		return new SoapVar($soapVal, SOAP_ENC_OBJECT, null, null, 'gbuyGchoiceModel');
	}


	/**
     * 共同購入内訳情報モデルのSoapオブジェクト生成処理。
     *
     * @access private
	 * @param $obj array
     * @return Soapオブジェクト array
     */
	private function _toSoapGbuyBidInventoryModel(array $obj)
	{

		//Soap設定オブジェクト
    	$soapVal = array();

    	//商品内訳ID
		$this->_toSoapVar($soapVal, 'gchoiceId', XSD_INTEGER, $obj);
		//購入数
		$this->_toSoapVar($soapVal, 'bidUnits', XSD_INTEGER, $obj);

		return new SoapVar($soapVal, SOAP_ENC_OBJECT, null, null, 'gbuyBidInventoryModel');
	}

	/**
     * 同梱子注文モデルのSoapオブジェクト生成処理。
     *
     * @access public
	 * @param $obj array
     * @return Soapオブジェクト array
     */
	private function _toSoapChildOrderModel(array $obj) {

	   	$soapUtil = new SoapOrderModelUtil();
	   	$model = $soapUtil->toSoapOrderModel($obj);
		return new SoapVar($model, SOAP_ENC_OBJECT, null, null, 'childOrderModel');
    }

	/**
     * クーポンモデルのSoapオブジェクト生成処理。
     *
     * @access private
	 * @param $obj array
     * @return Soapオブジェクト array
     */
	private function _toSoapCouponModel(array $obj)
	{

		//Soap設定オブジェクト
    	$soapVal = array();

    	//クーポンコード
		$this->_toSoapVar($soapVal, 'couponCode', XSD_STRING, $obj);
		//商品ID
		$this->_toSoapVar($soapVal, 'itemId', XSD_INTEGER, $obj);
		//クーポン名
		$this->_toSoapVar($soapVal, 'couponName', XSD_STRING, $obj);
		//クーポン効果(サマリー)
		$this->_toSoapVar($soapVal, 'couponSummary', XSD_STRING, $obj);
		//クーポン利用方法
		$this->_toSoapVar($soapVal, 'couponUsage', XSD_INTEGER, $obj);
		//クーポン原資コード
		$this->_toSoapVar($soapVal, 'couponCapital', XSD_INTEGER, $obj);
		//クーポン割引単価
		$this->_toSoapVar($soapVal, 'couponPrice', XSD_INTEGER, $obj);
		//割引タイプ
		$this->_toSoapVar($soapVal, 'discountType', XSD_INTEGER, $obj);
		//有効期限
		$this->_toSoapVar($soapVal, 'expiryDate', XSD_DATETIME, $obj);
		//課金フラグ
		$this->_toSoapVar($soapVal, 'feeFlag', XSD_STRING, $obj);
		//クーポン利用金額
		$this->_toSoapVar($soapVal, 'couponTotalPrice', XSD_LONG, $obj);

		return new SoapVar($soapVal, SOAP_ENC_OBJECT, null, null, 'couponModel');
	}

    /**
     * SoapVarオブジェクトを作成します。<br>
     *
     * @access private
     * @param $soapObj
     * @param $name
     * @param $xsdType
     * @param $obj
     * @return SoapVarオブジェクト array
     */
    private function _toSoapVar(array &$soapObj, $name, $xsdType, array $obj) {

    	if ($this->isEmpty($obj)) {
    		return;
    	}

		if (!array_key_exists($name, $obj)) {
			return;
		}

		$soapObj[] = new SoapVar($obj[$name], $xsdType, null, null, $name);
    }

    /**
     * arrayオブジェクトが空かを判定します。<br>
     *
     * @access public
     * @param $obj array
     * @return 判定結果
     */
    public function isEmpty($obj) {

    	if (is_null($obj)) {
    		return true;
    	}

    	if (count($obj) == 0) {
    		return true;
    	}

    	return false;
    }

}

?>