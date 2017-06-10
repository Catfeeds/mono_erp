<?php
// Copyright (c) Rakuten, Inc. All Rights Reserved.

/**
*  <b>受注API機能実行のインターフェース。</b><br>
*    このクラスの内容はサンプルとしての処理になります。<br>
*    実際の運用にあわせて処理を実装してください。<br>
*
*  @package api
*/
Interface OrderApiPractice {

   /**
     * 認証情報の設定
     *
     * @access public
     * @param $req array
     * @return void
     */
    public function setAuthRequest(array $req);

    /**
     * サービスリクエスト情報の設定。
     *
     * @access public
     * @param  $req
     * @return void
     */
    public function setServiceRequest($req);

    /**
     * 受注API機能の実行。
     *
     * @access public
     * @return 実行結果
     */
    public function callOrderApi();

}


?>