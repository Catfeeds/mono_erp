<?php
// Copyright (c) Rakuten, Inc. All Rights Reserved.

/**
 * <b>受注APIのリクエストID取得実行クラス。</b><br>
 * <pre>
 *   このクラスの内容はサンプルとしての処理になります。<br>
 *   実際の運用にあわせて処理を実装してください。<br>
 * </pre>
 *
 * @package api.impl
 */
class GetRequestIdPractice extends OrderApiPracticeBase implements OrderApiPractice {

    /**
     * 受注APIのリクエストID取得実行。
     *
     * @access public
     * @return リクエストIDレスポンス情報
     */
    public function callOrderApi()
    {

    	//認証リクエストデータ取得
     	$userAuthModel = $this->_getAuthRequest();

     	//認証リクエストデータ取得結果判定
     	if (is_null($userAuthModel)) {
        	return null;
        }

        //API実行
		$body = $this->_getReqestId($userAuthModel);
		//実行結果判定
        if (is_null($body) || !array_key_exists('requestId', $body)) {
			print("リクエストID取得失敗。\n");
			return null;
		}

		print("リクエストID:". $body->requestId ."\n");
		return $body;

    }
}

?>