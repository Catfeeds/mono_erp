<?php
// Copyright (c) Rakuten, Inc. All Rights Reserved.

/**
 * <b>受注APIの同梱候補一覧実行の実装クラス。</b><br>
 * <pre>
 *   このクラスの内容はサンプルとしての処理になります。<br>
 *   実際の運用にあわせて処理を実装してください。<br>
 * </pre>
 *
 * @package api.impl
 */
class GetEnclosureListPractice extends OrderApiPracticeBase implements OrderApiPractice {

    /**
     * 受注APIの同梱候補一覧実行。
     *
     * @access public
     * @return 同梱候補一覧レスポンス情報
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
        $enclosureListRequestModel = $this->_getEnclosureListRequestModel();

        //接続用オプション
    	$option = array('timeout' => 20);
        //API接続
    	$this->_connect($option);
        //リクエスト引数の設定
		$param = array('arg0' => $userAuthModel, 'arg1' => $enclosureListRequestModel);
		//API実行
		$body = $this->_client->getEnclosureList($param);
		//実行結果判定
		if ($this->_checkRespons($body)) {
			$body = $body->return;
		}

        //API切断
		$this->_disconnect();

		return $body;
    }


    /**
     * _getEnclosureListRequestModel
     *
     * @access private
     * @return リクエストデータ
     */
    private function _getEnclosureListRequestModel()
    {
    	$soapObj = array();
    	$model = $this->_srvReq;

    	//リクエストデータ作成
      	if (array_key_exists('orderNumber', $model)) {
		   	$soapObj[] = $this->_toSoapVar('orderNumber', XSD_STRING, $model['orderNumber']);
    	} else {
    		return $this->_toSoapVar(null, SOAP_ENC_OBJECT, array());
    	}

   		return $this->_toSoapVar(null, SOAP_ENC_OBJECT, $soapObj);
    }
}

?>