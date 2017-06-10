<?php
// Copyright (c) Rakuten, Inc. All Rights Reserved.

/**
 * <b>受注APIの同梱親注文変更実行の実装クラス。</b><br>
 * <pre>
 *   このクラスの内容はサンプルとしての処理になります。<br>
 *   実際の運用にあわせて処理を実装してください。<br>
 * </pre>
 * @package api.impl
 */
class ChangeEnclosureParentPractice extends OrderApiPracticeBase implements OrderApiPractice {

    /**
     *  受注APIの同梱親注文変更実行。<br>
     *  <pre>
     *  １．リクエストID取得を実施します。<br>
     *  ２．取得したリクエストIDを設定します。<br>
     *  ３．受注APIの同梱親注文変更処理を呼出します。<br>
     *  </pre>
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
        $changeEnclosureParentRequestModel = $this->_getChangeEnclosureParentRequestModel($reqIdBody->requestId);
        //リクエストデータ作成結果判定
        if (is_null($changeEnclosureParentRequestModel)) {
			print("同梱親注文変更リクエストデータ不正。\n");
        	return null;
        }

		//接続用オプション
    	$option = array('timeout' => 20);
        //API接続
    	$this->_connect($option);
        //リクエスト引数の設定
		$param = array('arg0' => $userAuthModel, 'arg1' => $changeEnclosureParentRequestModel);
		//API実行
		$body = $this->_client->changeEnclosureParent($param);
		//実行結果判定
		if ($this->_checkRespons($body)) {
			$body = $body->return;
		}

        //API切断
		$this->_disconnect();

		return $body;
    }


    /**
     * _getChangeEnclosureParentRequestModel
     *
     * @access private
     * @param $reqestId
     * @return リクエストデータ
     */
    private function _getChangeEnclosureParentRequestModel($reqestId)
    {
    	$soapObj = array();

    	foreach ($this->_srvReq as $model) {

    		//必須項目チェック
    		if (!array_key_exists('newParent', $model)) {
	    		print("項目[newParent]が存在しません。\n");
    			return null;
    		}

    		if (!array_key_exists('nowParent', $model)) {
	    		print("項目[nowParent]が存在しません。\n");
    			return null;
    		}

	   		//リクエストデータ作成
    		$soapObj[] =
    			$this->_getChangeEnclosureParentModel($model['newParent'], $model['nowParent']);
    	}

    	//リクエストID設定
    	$soapObj[] = $this->_toSoapVar('requestId', XSD_INTEGER, $reqestId);

    	return $this->_toSoapVar(null, SOAP_ENC_OBJECT, $soapObj);
    }

    /**
     * _getChangeEnclosureParentModel
     *
     * @access private
	 * @param $newParent
	 * @param $nowParent
     * @return リクエストデータ
     */
    private function _getChangeEnclosureParentModel($newParent, $nowParent)
    {
    	$soapValue = array(
			$this->_toSoapVar('newParent', XSD_STRING, $newParent),
   			$this->_toSoapVar('nowParent', XSD_STRING, $nowParent));

   		return $this->_toSoapVar('changeEnclosureParentModel', SOAP_ENC_OBJECT, $soapValue);
    }
}

?>