<?php
// Copyright (c) Rakuten, Inc. All Rights Reserved.

/**
 * <b>受注APIの一括受注ステータス変更実行の実装クラス。</b><br>
 * <pre>
 *   このクラスの内容はサンプルとしての処理になります。<br>
 *   実際の運用にあわせて処理を実装してください。<br>
 * </pre>
 *
 * @package api.impl
 */
class ChangeStatusPractice extends OrderApiPracticeBase implements OrderApiPractice {

    /**
     * 受注APIの一括受注ステータス変更実行。<br>
     * <pre>
     * １．リクエストID取得を実施します。<br>
     * ２．取得したリクエストIDを設定します。<br>
     * ３．受注APIの一括受注ステータス変更処理を呼出します。<br>
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
        $changeStatusRequestModel = $this->_getChangeStatusRequestModel($reqIdBody->requestId);
        //リクエストデータ作成結果判定
        if (is_null($changeStatusRequestModel)) {
			print("受注ステータス変更リクエストデータ不正。\n");
        	return null;
        }

        //接続用オプション
    	$option = array('timeout' => 20);
        //API接続
    	$this->_connect($option);
        //リクエスト引数の設定
		$param = array('arg0' => $userAuthModel, 'arg1' => $changeStatusRequestModel);
		//API実行
		$body = $this->_client->changeStatus($param);
		//実行結果判定
		if ($this->_checkRespons($body)) {
			$body = $body->return;
		}

        //API切断
		$this->_disconnect();

		return $body;
    }


    /**
     * _getchangeStatusRequestModel
     *
     * @access private
     * @param $reqestId
     * @return リクエストデータ
     */
    private function _getChangeStatusRequestModel($reqestId)
    {
    	$soapObj = array();

    	foreach ($this->_srvReq as $model) {

    		//必須項目チェック
     		if (!array_key_exists('orderNumber', $model)) {
	    		print("項目[orderNumber]が存在しません。\n");
    			return null;
    		}

     		if (!array_key_exists('statusName', $model)) {
	    		print("項目[statusName]が存在しません。\n");
    			return null;
    		}

    		//リクエストデータ作成
    		$soapObj[] = $this->_getStatusModel($model['orderNumber'], $model['statusName']);
    	}

    	//リクエストID設定
   		$soapObj[] = $this->_toSoapVar('requestId', XSD_INTEGER, $reqestId);

    	return $this->_toSoapVar(null, SOAP_ENC_OBJECT, $soapObj);
    }


    /**
     * _getStatusModel
     *
     * @access private
     * @param $orderNumber array
     * @param $statusName
     * @return リクエストデータ
     */
    private function _getStatusModel(array $orderNumber, $statusName)
    {
    	$soapValue = array();

   		foreach ($orderNumber as $val) {
   			$soapValue[] = $this->_toSoapVar('orderNumber', XSD_STRING, $val);
   		}

   		$soapValue[] = $this->_toSoapVar('statusName', XSD_STRING, $statusName);

   		return $this->_toSoapVar('orderStatusModel', SOAP_ENC_OBJECT, $soapValue);
    }
}

?>