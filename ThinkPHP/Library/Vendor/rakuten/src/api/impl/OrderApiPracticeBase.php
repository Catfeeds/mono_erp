<?php
// Copyright (c) Rakuten, Inc. All Rights Reserved.

//処理成功時のメッセージID
define("MSGID_SUCCESS", "N00-000");
//処理警告終了時のメッセージID
define("MSGID_WARNING", "W00-000");

/**
 * <b>受注API関連実装の共通処理クラス。</b><br>
 * <pre>
 *   このクラスの内容はサンプルとしての処理になります。<br>
 *   実際の運用にあわせて処理を実装してください。<br>
 * </pre>
 *
 * @package api.impl
 */
class OrderApiPracticeBase {

	var $_client  = null;
	var $_authReq = null;
	var $_srvReq = null;


    /**
     * 認証リクエスト情報の設定。
     *
     * @access public
     * @param  ArrayList $req
     * @return void
     */
    public function setAuthRequest(array $req)
    {
    	if ((is_null($req)) || (count($req) == 0)) {
			print("認証リクエスト情報がありません\n");
    		return;
    	}

    	$this->_authReq = $req;
    }

    /**
     * サービスリクエスト情報の設定。
     *
     * @access public
     * @param  $req
     * @return void
     */
    public function setServiceRequest($req)
    {
    	if (is_null($req)) {
			print("サービスリクエスト情報がありません\n");
			return;
    	}

     	if (count($req) == 0) {
			print("サービスリクエスト情報がありません\n");
			return;
    	}

    	$this->_srvReq = $req;
    }

    /**
     * OrderAPI へ接続を行う
     *
     * @access protected
     * @param $wsdlURL  string
     * @return void
     */
    protected function _connect($option) {

        // Clientオブジェクトの生成
        if (!is_null($option)) {
           $this->_client = new SoapClient(WSDL_URL, $option);
        } else {
	       $this->_client = new SoapClient(WSDL_URL);
        }
 	    return $this->_client;
   }

    /**
     * OrderAPI との接続を切断する。
     *
     * @access public
     * @return void
     */
    protected function _disconnect()
    {
        // SOAPサーバへの接続
        if (!is_null($this->_client)){
	        $this->_client = null;
        }
        return true;
    }


    /**
     * 認証リクエストデータの取得
     *
     * @access protected
     * @return 認証リクエストデータ
     */
    protected function _getAuthRequest()
    {

    	if (is_null($this->_authReq)) {
			print("認証リクエスト情報が存在しません\n");
    		return null;
    	}

    	//必須項目チェック
    	if (!array_key_exists('authKey', $this->_authReq)) {
  	  			print("項目[authKey]が存在しません。\n");
    			return null;
    	}

    	if (!array_key_exists('shopUrl', $this->_authReq)) {
  	  			print("項目[shopUrl]が存在しません。\n");
    			return  null;
    	}

    	if (!array_key_exists('userName', $this->_authReq)) {
  	  			print("項目[userName]が存在しません。\n");
    			return null;
    	}

    	$soapObj = array(
            // 認証キー
    		$this->_toSoapVar('authKey', XSD_STRING, $this->_authReq['authKey']),
	        // 店舗URL
        	$this->_toSoapVar('shopUrl', XSD_STRING, $this->_authReq['shopUrl']),
           	// ユーザ名
        	$this->_toSoapVar('userName', XSD_STRING, $this->_authReq['userName']));

    	return $this->_toSoapVar(null, SOAP_ENC_OBJECT, $soapObj);
    }

	/**
     * レスポンス情報の結果判定。
     *
     * @access protected
     * @param $body stdclass
     * @return チェック結果
     */
    protected function _checkRespons($body)
    {
    	if (is_null($body) || (!($body instanceof stdClass))) {
    		print("APIレスポンスなし\n");
    		return false;
    	}

    	if (!isset($body->return)) {
    		print("APIレスポンス不正\n");
    		return false;
    	}
    	$res = $body->return;

    	if (!array_key_exists('errorCode', $res)) {
    		print("APIレスポンス不正\n");
    		return false;
	    }

	    // エラー種類を判断する
        $errCode = $res->errorCode;
        $message = $res->message;
     	if (MSGID_SUCCESS !== $errCode) {
			if (MSGID_WARNING !== $errCode) {
                //エラー終了の場合
//                 print("APIレスポンス:エラー\n");
//                 print("エラーコード:". $errCode ."\n");
//                 print("エラーメッセージ:" . $message . "\n");
                return false;
			}

			//警告終了の場合
// 			$unitErrList = $res->unitError;   //为了不显示报错
//             foreach ($unitErrList as $unitErr) {
//                 print("APIレスポンス:警告\n");
//                 print("***********個別エラー*********************\n");
//                 print("受注番号:". $unitErr->orderKey ."\n");
//                 print("エラーコード:". $unitErr->errCode ."\n");
//                 print("エラーメッセージ:" + $unitErr->message + "\n");
//                 print("*******************************************\n");

//             }
		}
		return true;
    }

    /**
     * 受注APIのリクエストID取得実行。
     *
     * @access protected
     * @param $userAuthModel
     * @return リクエストIDレスポンス情報
     */
    protected function _getReqestId($userAuthModel)
    {

		//接続用オプション
    	$option = array('timeout' => 20);

        //API接続
    	$this->_connect($option);
        //引数の設定
		$param = array('arg0' => $userAuthModel);
		//API実行
		$body = $this->_client->getRequestId($param);
		//実行結果判定
		if ($this->_checkRespons($body)) {
			$body = $body->return;
		}

        //API切断
		$this->_disconnect();

		return $body;
    }

    /**
     * 受注APIの一括受注情報取得実行
     *
     * @access protected
     * @param $userAuthModel
     * @param $orderRequestModel
     * @return 受注情報取得レスポンス情報
     */
    protected function _getOrder($userAuthModel, $orderRequestModel)
    {

        //接続用オプション
    	$option = array('timeout' => 20);
        //API接続
    	$this->_connect($option);
        //リクエスト引数の設定
		$param = array('arg0' => $userAuthModel, 'arg1' => $orderRequestModel);
		//API実行
		$body = $this->_client->getOrder($param);
		//実行結果判定
		if ($this->_checkRespons($body)) {
			$body = $body->return;
		}

        //API切断
		$this->_disconnect();

		return $body;
    }


    /**
     * SoapVarオブジェクトを作成します。
     *
     * @access protected
     * @param $name
     * @param $xsdType
     * @param $obj
     * @return SoapVarオブジェクト
     */
    protected function _toSoapVar($name, $xsdType, $obj) {

    	if (is_null($name)) {
    		return new SoapVar($obj, $xsdType);
    	}
    	return new SoapVar($obj, $xsdType, null, null, $name);
    }

}
?>