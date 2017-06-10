<?php
// Copyright (c) Rakuten, Inc. All Rights Reserved.

/**
 * <b>受注APIの非同期処理結果取得実行の実装クラス。</b><br>
 * <pre>
 *   このクラスの内容はサンプルとしての処理になります。<br>
 *   実際の運用にあわせて処理を実装してください。<br>
 * </pre>
 *
 *  @package api.impl
 */
class GetResultPractice extends OrderApiPracticeBase implements OrderApiPractice {

    /**
     * 受注APIの非同期処理結果取得実行。
     *
     * @access public
     * @return 非同期処理結果取得レスポンス情報
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
        $resultRequestModel = $this->_getResultRequestModel();
        //リクエストデータ作成結果判定
        if (is_null($resultRequestModel)) {
			print("非同期処理結果取得リクエスト情報データ不正。\n");
        	return null;
        }

        //接続用オプション
    	$option = array('timeout' => 20);
        //API接続
    	$this->_connect($option);
        //リクエスト引数の設定
		$param = array('arg0' => $userAuthModel, 'arg1' => $resultRequestModel);
		//API実行
		$body = $this->_client->getResult($param);
		//実行結果判定
		if ($this->_checkRespons($body)) {
			$body = $body->return;
		}

        //API切断
		$this->_disconnect();

		return $body;
    }


    /**
     * _getResultRequestModel
     *
     * @access private
     * @return リクエストデータ
     */
    private function _getResultRequestModel()
    {
    	$soapObj = array();
    	$model = $this->_srvReq;

    	//必須項目チェック
		if (!array_key_exists('requestId', $model)) {
	  		print("項目[requestId]が存在しません。\n");
    		return null;
		}

		//リクエストデータ作成
      	foreach ($model['requestId'] as $reqestId) {
    		$soapObj[] = $this->_toSoapVar('requestId', XSD_INTEGER, $reqestId);
    	}

    	return $this->_toSoapVar(null, SOAP_ENC_OBJECT, $soapObj);
    }
}

?>