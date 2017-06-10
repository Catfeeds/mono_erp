<?php
// Copyright (c) Rakuten, Inc. All Rights Reserved.

/**
 * <b>受注APIの同梱解除する実行の実装クラス。</b><br>
 * <pre>
 *   このクラスの内容はサンプルとしての処理になります。<br>
 *   実際の運用にあわせて処理を実装してください。<br>
 *   </pre>
 *
 * @package api.impl
 */
class DoUnEnclosurePractice extends OrderApiPracticeBase implements OrderApiPractice {

    /**
     * 受注APIの同梱解除する実行。<br>
     * <pre>
     * １．リクエストID取得を実施します。<br>
     * ２．取得したリクエストIDを設定します。<br>
     * ３．受注APIの同梱解除する処理を呼出します。<br>
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

        //リクエストID取得
        $reqIdBody = $this->_getReqestId($userAuthModel);
        //リクエストID取得結果判定
        if (is_null($reqIdBody) || !array_key_exists('requestId', $reqIdBody)) {
			print("リクエストID取得失敗。\n");
			return null;
		}
        print("リクエストID:". $reqIdBody->requestId ."\n");

		//リクエストデータ作成
        $doUnEnclosureRequestModel = $this->_getUnDoEnclosureRequestModel($reqIdBody->requestId);
        //リクエストデータ作成結果判定
        if (is_null($doUnEnclosureRequestModel)) {
			print("同梱解除リクエスト情報データ不正。\n");
        	return null;
        }

		//接続用オプション
    	$option = array('timeout' => 20);
        //API接続
    	$this->_connect($option);
        //リクエスト引数の設定
		$param = array('arg0' => $userAuthModel, 'arg1' => $doUnEnclosureRequestModel);
		//API実行
		$body = $this->_client->doUnEnclosure($param);
		//実行結果判定
		if ($this->_checkRespons($body)) {
			$body = $body->return;
		}

        //API切断
		$this->_disconnect();

		return $body;
    }


    /**
     * _getUnDoEnclosureRequestModel
     *
     * @access private
     * @param $reqestId
     * @return リクエストデータ
     */
    private function _getUnDoEnclosureRequestModel($reqestId)
    {
    	$soapObj = array();

    	foreach ($this->_srvReq as $model) {

    		//必須項目チェック
    		if (!array_key_exists('orderNumber', $model)) {
  	  			print("項目[orderNumber]が存在しません。\n");
    			return;
    		}

    		//リクエストデータ作成
    		$soapObj[] = $this->_getNominateUnEnclosureModel($model['orderNumber']);
    	}

    	//リクエストID設定
    	$soapObj[] = $this->_toSoapVar('requestId', XSD_INTEGER, $reqestId);

    	return $this->_toSoapVar(null, SOAP_ENC_OBJECT, $soapObj);

    }


    /**
     * _getNominateUnEnclosureModel
     *
     * @access private
     * @param $orderNumber array
     * @return リクエストデータ
     */
    private function _getNominateUnEnclosureModel(array $orderNumber)
    {
    	$soapValue = array();

   		foreach ($orderNumber as $val) {
   			$soapValue[] = $this->_toSoapVar('orderNumber', XSD_STRING, $val);
   		}

    	return $this->_toSoapVar('nominateUnEnclosureModel', SOAP_ENC_OBJECT, $soapValue);

    }
}

?>