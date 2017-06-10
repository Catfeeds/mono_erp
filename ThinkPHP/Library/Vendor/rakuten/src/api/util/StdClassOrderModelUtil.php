<?php

/**
*  <b>受注情報モデルのStdClassオブジェクト変換用クラス。</b><br>
*    このクラスの内容はサンプルとしての処理になります。<br>
*    実際の運用にあわせて処理を実装してください。<br>
*
*  @package api.util
*/
class StdClassOrderModelUtil {

	/**
     * 受注情報モデルのStdclassオブジェクトをarrayオブジェクトに変換する。
     *
     * @access public
	 * @param $obj
     * @return Soapオブジェクト array
     */
	public function toArrayOrderModel($obj)
	{
		if (is_null($obj)) {
    		return null;
    	}

		//受注情報モデルの変換
		$modelList = $this->_stdclass_to_array($obj);

		foreach ($modelList as &$model) {

			//注文者情報モデル
			if (array_key_exists('ordererModel', $model)) {
				$model['ordererModel'] = (array) $model['ordererModel'];
			}

			//通常注文モデル
			if (array_key_exists('normalOrderModel', $model)) {
				$model['normalOrderModel'] = (array) $model['normalOrderModel'];
			}

			//オークション注文モデル
			if (array_key_exists('saOrderModel', $model)) {
				$model['saOrderModel'] = (array) $model['saOrderModel'];
			}

			//共同購入注文モデル
			if (array_key_exists('gbuyOrderModel', $model)) {
				$model['gbuyOrderModel'] = (array) $model['gbuyOrderModel'];
			}

			//支払方法モデル
			if (array_key_exists('settlementModel', $model)) {
				$model['settlementModel'] = $this->_toArraySettlementModel($model['settlementModel']);
			}

			//配送方法モデル
			if (array_key_exists('deliveryModel', $model)) {
				$model['deliveryModel'] = (array) $model['deliveryModel'];
			}

			//ポイントモデル
			if (array_key_exists('pointModel', $model)) {
				$model['pointModel'] = (array) $model['pointModel'];
			}

			//楽天バンクモデル
			if (array_key_exists('rBankModel', $model)) {
				$model['rBankModel'] = (array) $model['rBankModel'];
			}

			//ラッピングモデル1
			if (array_key_exists('wrappingModel1', $model)) {
				$model['wrappingModel1'] = (array) $model['wrappingModel1'];
			}

			//ラッピングモデル2
			if (array_key_exists('wrappingModel2', $model)) {
				$model['wrappingModel2'] = (array) $model['wrappingModel2'];
			}

			//送付先モデルリスト
			if (array_key_exists('packageModel', $model)) {
				$model['packageModel'] = $this->_toArrayPackageModel($model['packageModel']);
			}

			//同梱子注文モデルリスト
			if (array_key_exists('childOrderModel', $model)) {
				$model['childOrderModel'] = $this->_toArrayChildOrderModel($model['childOrderModel']);
			}

			//クーポンモデルリスト
			if (array_key_exists('couponModel', $model)) {
				$model['couponModel'] = $this->_stdclass_to_array($model['couponModel']);
			}
		}

		return $modelList;
	}

	/**
     * 支払方法モデルのStdclassオブジェクトをarrayオブジェクトに変換する。
     *
     * @access public
	 * @param $obj stdClass
     * @return arrayオブジェクト array
     */
	private function _toArraySettlementModel(stdClass $obj)
	{

		if (is_null($obj)) {
    		return null;
    	}

    	//送付先モデルの変換
		$modelList = (array)$obj;

		//カード情報モデル
		if (array_key_exists('cardModel', $modelList)) {
			$modelList['cardModel'] = (array) $modelList['cardModel'];
		}

		return $modelList;
	}


	/**
     * 送付先モデルのStdclassオブジェクトをarrayオブジェクトに変換する。
     *
     * @access public
	 * @param $obj
     * @return arrayオブジェクト array
     */
	private function _toArrayPackageModel($obj)
	{

		if (is_null($obj)) {
    		return null;
    	}

    	//送付先モデルの変換
		$modelList = $this->_stdclass_to_array($obj);

		foreach ($modelList as &$model) {

			//送付者情報モデル
			if (array_key_exists('senderModel', $model)) {
				$model['senderModel'] = (array) $model['senderModel'];
			}

			//コンビニ配送情報
			if (array_key_exists('deliveryCvsModel', $model)) {
				$model['deliveryCvsModel'] = (array) $model['deliveryCvsModel'];
			}

			//商品モデルリスト
			if (array_key_exists('itemModel', $model)) {
				$model['itemModel'] = $this->_toArrayItemModel($model['itemModel']);
			}
		}

    	return $modelList;
	}


	/**
     * 商品モデルのStdclassオブジェクトをarrayオブジェクトに変換する。
     *
     * @access private
	 * @param $obj
     * @return arrayオブジェクト array
     */
	private function _toArrayItemModel($obj)
	{

		if (is_null($obj)) {
    		return null;
    	}

    	//商品モデルの変換
		$modelList = $this->_stdclass_to_array($obj);

		foreach ($modelList as &$model) {

			//通常商品モデル
			if (array_key_exists('normalItemModel', $model)) {
				$model['normalItemModel'] = (array) $model['normalItemModel'];
			}

			//オークション商品モデル
			if (array_key_exists('saItemModel', $model)) {
				$model['saItemModel'] = (array) $model['saItemModel'];
			}

			//共同購入商品モデル
			if (array_key_exists('gbuyItemModel', $model)) {
				$model['gbuyItemModel'] = $this->_toArrayGbuyItemModell($model['gbuyItemModel']);
			}
		}

		return $modelList;
	}

	/**
     * 共同購入商品モデルのStdclassオブジェクトをarrayオブジェクトに変換する。
     *
     * @access private
	 * @param $obj
     * @return arrayオブジェクト array
     */
	private function _toArrayGbuyItemModel($obj)
	{

		if (is_null($obj)) {
    		return null;
    	}

    	//共同購入商品モデルの変換
		$modelList = $this->_stdclass_to_array($obj);

		foreach ($modelList as &$model) {

			//共同購入商品内訳モデルリスト
			if (array_key_exists('gbuyGchoiceModel', $model)) {
				$model['gbuyGchoiceModel'] = $this->_stdclass_to_array($model['gbuyGchoiceModel']);
			}

			//共同購入内訳情報モデルリスト
			if (array_key_exists('gbuyBidInventoryModel', $model)) {
				$model['gbuyBidInventoryModel'] = $this->_stdclass_to_array($model['gbuyBidInventoryModel']);
			}
		}

		return $modelList;
	}


	/**
     * 同梱子注文モデルのStdclassオブジェクトをarrayオブジェクトに変換する。
     *
     * @access private
	 * @param $obj
     * @return arrayオブジェクト array
     */
	public function _toArrayChildOrderModel($obj) {

		if (is_null($obj)) {
    		return null;
    	}

    	$util = new StdClassOrderModelUtil();
 		$modelList = $util->toArrayOrderModel($obj);

 		return $modelList;
    }

   	/**
     * Stdclassオブジェクトをarrayオブジェクトに変換する
     *
     * @access private
	 * @param $obj
     * @return arrayオブジェクト
     */
 	private function _stdclass_to_array($obj) {


 		if ($obj instanceof stdClass) {
			//stdClassの場合
 			$arrayData = (array) $obj;

	 		if ($this->_is_hash($arrayData)) {
	 			//連想配列の場合
	 			return array($arrayData);

	 		} else {
				//配列の場合
	 			for ($i=0;$i<count($arrayData);$i++) {
					$arrayData[$i] = (array) $arrayData[$i];
				}
	 		}
 		} else {
 			//arrayの場合

 			$arrayData = array();
 			for ($i=0;$i<count($obj);$i++) {
 				$arrayData[$i] = (array) $obj[$i];
 			}
 		}

    	return $arrayData;
 	}


	/**
     * 連想配列なのかを判定する。
     *
     * @access private
	 * @param $array
     * @return array
     */
 	private function _is_hash(&$array) {
    	return array_keys($array) !== range(0, count($array) - 1);
 	}


}

?>