<?php
// Copyright (c) Rakuten, Inc. All Rights Reserved.

/**
 * <b>受注APIの一括受注情報取得実行の実装クラス。</b><br>
 * <pre>
 *   このクラスの内容はサンプルとしての処理になります。<br>
 *   実際の運用にあわせて処理を実装してください。<br>
 * </pre>
 *
 * @package api.impl
 */
class GetOrderPractice extends OrderApiPracticeBase implements OrderApiPractice {


    /**
     * 受注APIの一括受注情報取得実行。
     *
     * @access public
     * @return 受注情報取得レスポンス情報
     */
    public function callOrderApi()
    {

    	//認証リクエストデータ取得
     	$userAuthModel = $this->_getAuthRequest();

     	//認証リクエストデータ取得結果判定
     	if (is_null($userAuthModel) || is_null($this->_srvReq)) {
        	return null;
        }

        //リクエストデータ作成
        $orderRequestModel = $this->_getOrderRequestModel();
        //リクエストデータ作成結果判定
        if (is_null($orderRequestModel)) {
			print("受注情報取得リクエスト情報データ不正。\n");
        	return null;
        }

        //API実行
        $body = $this->_getOrder($userAuthModel, $orderRequestModel);

		return $body;
    }


   /**
     * _getOrderRequestModel
     *
     * @access private
     * @return リクエストデータ
     */
    private function _getOrderRequestModel()
    {
    	$soapObj = array();
    	$model = $this->_srvReq;

  		//リクエストデータ作成
    	if (array_key_exists('orderNumber', $model)) {
      		foreach ($model['orderNumber'] as $orderNumber) {
	    		$soapObj[] = $this->_toSoapVar('orderNumber', XSD_STRING, $orderNumber);
	    	}
    	}

    	if (array_key_exists('isOrderNumberOnlyFlg', $model)) {
	    	$soapObj[] = $this->_toSoapVar('isOrderNumberOnlyFlg', XSD_BOOLEAN, $model['isOrderNumberOnlyFlg']);
    	}

    	if (array_key_exists('orderSearchModel', $model)) {

    		//必須項目チェック
			if (!array_key_exists('dateType', $model['orderSearchModel'])) {
		  		print("項目[dateType]が存在しません。\n");
	    		return null;
			}

		    if (!array_key_exists('startDate', $model['orderSearchModel'])) {
	  			print("項目[startDate]が存在しません。\n");
    			return null;
		    }

		    if (!array_key_exists('endDate', $model['orderSearchModel'])) {
		  		print("項目[endDate]が存在しません。\n");
	    		return null;
			}

    		$soapObj[] = $this->_getOrderSearchModel($model['orderSearchModel']);
    	}

    	return $this->_toSoapVar(null, SOAP_ENC_OBJECT, $soapObj);
    }

	/**
     * _getOrderSearchModel
     *
     * @access private
	 * @param $model
     * @return リクエストデータ
     */
    private function _getOrderSearchModel($model)
    {

    	//必須項目
    	$soapValue = array(
			$this->_toSoapVar('dateType', XSD_INTEGER, $model['dateType']),
			$this->_toSoapVar('startDate', XSD_DATETIME, $model['startDate']),
			$this->_toSoapVar('endDate', XSD_DATETIME, $model['endDate']));


		//任意項目
	    if (array_key_exists('asuraku', $model)) {
	    	$soapValue[] = $this->_toSoapVar('asuraku', XSD_BOOLEAN, $model['asuraku']);
		}

	    if (array_key_exists('coupon', $model)) {
	    	$soapValue[] = $this->_toSoapVar('coupon', XSD_BOOLEAN, $model['coupon']);
		}

	    if (array_key_exists('modify', $model)) {
	    	$soapValue[] = $this->_toSoapVar('modify', XSD_BOOLEAN, $model['modify']);
		}

	    if (array_key_exists('pointUsed', $model)) {
	    	$soapValue[] = $this->_toSoapVar('pointUsed', XSD_BOOLEAN, $model['pointUsed']);
		}

	    if (array_key_exists('comment', $model)) {
	    	$soapValue[] = $this->_toSoapVar('comment', XSD_STRING, $model['comment']);
		}

	    if (array_key_exists('delivery', $model)) {
	    	$soapValue[] = $this->_toSoapVar('delivery', XSD_STRING, $model['delivery']);
		}

	    if (array_key_exists('itemName', $model)) {
	    	$soapValue[] = $this->_toSoapVar('itemName', XSD_STRING, $model['itemName']);
		}

	    if (array_key_exists('itemNumber', $model)) {
	    	$soapValue[] = $this->_toSoapVar('itemNumber', XSD_STRING, $model['itemNumber']);
		}

	    if (array_key_exists('ordererKana', $model)) {
	    	$soapValue[] = $this->_toSoapVar('ordererKana', XSD_STRING, $model['ordererKana']);
		}

	    if (array_key_exists('ordererMailAddress', $model)) {
	    	$soapValue[] = $this->_toSoapVar('ordererMailAddress', XSD_STRING, $model['ordererMailAddress']);
		}

	    if (array_key_exists('ordererName', $model)) {
	    	$soapValue[] = $this->_toSoapVar('ordererName', XSD_STRING, $model['ordererName']);
		}

	    if (array_key_exists('ordererPhoneNumber', $model)) {
	    	$soapValue[] = $this->_toSoapVar('ordererPhoneNumber', XSD_STRING, $model['ordererPhoneNumber']);
		}

	    if (array_key_exists('reserveNumber', $model)) {
	    	$soapValue[] = $this->_toSoapVar('reserveNumber', XSD_STRING, $model['reserveNumber']);
		}

	    if (array_key_exists('senderName', $model)) {
	    	$soapValue[] = $this->_toSoapVar('senderName', XSD_STRING, $model['senderName']);
		}

	    if (array_key_exists('senderPhoneNumber', $model)) {
	    	$soapValue[] = $this->_toSoapVar('senderPhoneNumber', XSD_STRING, $model['senderPhoneNumber']);
		}

	    if (array_key_exists('settlement', $model)) {
	    	$soapValue[] = $this->_toSoapVar('settlement', XSD_STRING, $model['settlement']);
		}

	    if (array_key_exists('enclosureStatus', $model)) {
	    	foreach ($model['enclosureStatus'] as $val) {
	    		$soapValue[] = $this->_toSoapVar('enclosureStatus', XSD_INTEGER, $val);
	    	}
		}

		if (array_key_exists('mailAddressType', $model)) {
	    	foreach ($model['mailAddressType'] as $val) {
	    		$soapValue[] = $this->_toSoapVar('mailAddressType', XSD_INTEGER, $val);
	    	}
		}

		if (array_key_exists('orderSite', $model)) {
	    	foreach ($model['orderSite'] as $val) {
	    		$soapValue[] = $this->_toSoapVar('orderSite', XSD_INTEGER, $val);
	    	}
		}

		if (array_key_exists('orderType', $model)) {
	    	foreach ($model['orderType'] as $val) {
	    		$soapValue[] = $this->_toSoapVar('orderType', XSD_INTEGER, $val);
	    	}
		}

		if (array_key_exists('pointStatus', $model)) {
	    	foreach ($model['pointStatus'] as $val) {
	    		$soapValue[] = $this->_toSoapVar('pointStatus', XSD_INTEGER, $val);
	    	}
		}

		if (array_key_exists('rbankStatus', $model)) {
	    	foreach ($model['rbankStatus'] as $val) {
	    		$soapValue[] = $this->_toSoapVar('rbankStatus', XSD_INTEGER, $val);
	    	}
		}

		if (array_key_exists('status', $model)) {
	    	foreach ($model['status'] as $val) {
	    		$soapValue[] = $this->_toSoapVar('status', XSD_STRING, $val);
	    	}
		}

		if (array_key_exists('cardSearchModel', $model)) {
			$soapValue[] = $this->_getCardSearchModel($model['cardSearchModel']);
		}

	   	return $this->_toSoapVar('orderSearchModel', SOAP_ENC_OBJECT, $soapValue);
    }


	/**
     * _getCardSearchModel
     *
     * @access private
	 * @param $model
     * @return リクエストデータ
     */
    private function _getCardSearchModel($model)
    {
		$soapValue = array();

	    if (array_key_exists('cardName', $model)) {
			$soapValue[] = $this->_toSoapVar('cardName', XSD_STRING, $model['cardName']);
		}

	    if (array_key_exists('cardOwner', $model)) {
			$soapValue[] = $this->_toSoapVar('cardOwner', XSD_STRING, $model['cardOwner']);
		}

		if (array_key_exists('cardStatus', $model)) {
	    	foreach ($model['cardStatus'] as $val) {
				$soapValue[] = $this->_toSoapVar('cardStatus', XSD_INTEGER, $val);
	    	}
		}

		if (array_key_exists('payType', $model)) {
	    	foreach ($model['payType'] as $val) {
				$soapValue[] = $this->_toSoapVar('payType', XSD_INTEGER, $val);
	    	}
		}

	   	return $this->_toSoapVar('cardSearchModel', SOAP_ENC_OBJECT, $soapValue);
    }
}

?>