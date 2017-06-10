<?php
namespace Admin\Behavior;

//功能：		修改 U函数的 urlencode 为rawurlencode 方法
//Listen:	ThinkPHP/Common/functions
//Add:		Application/Admin/Controller/CodeManageController/sku
class CorrectUrlEncodeBehavior {
	// 行为扩展的执行入口必须是run
	public function run(&$url){
		$temp = explode( C('URL_PATHINFO_DEPR'), $url);
		$temp[sizeof($temp)-1] = rawurlencode(urldecode($temp[sizeof($temp)-1]));
		$url = implode( C('URL_PATHINFO_DEPR'), $temp);
	}
}