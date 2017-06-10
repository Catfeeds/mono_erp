<html>
	<!-- API実行時のSOAPメッセージを取得する場合のサンプル --> 

	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
		<title>GetSoapwGetRequestIdTest</title>
	</head>
	<body>
		<div id="mainBody">
			<?php
			
				//認証リクエストモデルのパラメータ設定
				$param = array(
					new SoapVar('XXXXXXXXXXXXXXXX',XSD_STRING, null, null, 'authKey'), //認証キー
					new SoapVar('XXXXXXXXXXXXXXXX',XSD_STRING, null, null, 'shopUrl'), //店舗URL
					new SoapVar('XXXXXXXXXXXXXXXX',XSD_STRING, null, null, 'userName') //ユーザ名
				);
	
				//認証リクエストモデル情報の生成
				$userAuthModel = new SoapVar($param, SOAP_ENC_OBJECT);

				/*SoapClientの生成
				 * SoapClientのオプションに「'trace'=> 1」を設定する
				 * trace オプションはリクエストのトレースを有効にする(デフォルトは FALSE)
				 * */
				$client = new SoapClient(
					'https://orderapi.rms.rakuten.co.jp/rms/mall/order/api/ws?wsdl' ,////WSDLのURL
					array ('timeout' => 20 , 'trace'=> 1 ) //接続用のオプション
				);

				//引数の設定
				$request = array('arg0' => $userAuthModel);

				//API機能（リクエストID取得）の実行
				$body = $client->getRequestId($request);
				
				//結果表示
				print("【実行結果】<br>");
				print("エラーコード：". $body->return->errorCode."<br>");
				print("エラーメッセージ：". $body->return->message."<br>");
				print("リクエストID：". $body->return->requestId."<br>");
				
				 /*SOAP情報表示
				 *  以下のメソッドを用いてSOAP情報を取得する
				 *   SoapClient::__getLastRequestHeaders -直近のSOAPリクエストヘッダを返す 
				 *   SoapClient::__getLastRequest - 直近のSOAPリクエストを返す
				 *   SoapClient::__getLastResponseHeaders - 直近のSOAPレスポンスヘッダを返す
				 *   SoapClient::__getLastResponse - 直近のSOAPレスポンスを返す
				 * */
				print("<br><br>【SOAP情報】<br>");
				print("Request(Header)：<br><textarea>" .$client->__getLastRequestHeaders(). "</textarea><br><br>");
				print("Request(Body)：<br><textarea>" .$client->__getLastRequest(). "</textarea><br><br>");
				print("Response(Header)：<br><textarea>" .$client->__getLastResponseHeaders(). "</textarea><br><br>");
				print("Response(Body)：<br><textarea>" .$client->__getLastResponse(). "</textarea><br><br>");
			
			?>
		</div>
	</body>
</html>
