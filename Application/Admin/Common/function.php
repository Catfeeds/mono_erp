<?php
//驼峰命名 转 下划线命名
function cc_format($name)
{
	$temp_array = array();
	for($i=0;$i<strlen($name);$i++)
	{
		$ascii_code = ord($name[$i]);
		if($ascii_code >= 65 && $ascii_code <= 90)
		{
			if($i == 0){
				$temp_array[] = chr($ascii_code + 32);
			}else{
				$temp_array[] = '_'.chr($ascii_code + 32);
			}
		}else{
			$temp_array[] = $name[$i];
		}
	}
	return implode('',$temp_array);
}

//获取时间颜色
function get_color_date($type='Y-m-d H:i:s',$time,$color='red'){
	if($time > xtime(1)){
		return '<font color="'.$color.'">'.date($type,$time).'</font>';
	}else{
	    return date($type,$time);
	}
}
//获得某天前的最后一秒时间戳
function xtime($day){
	$day = intval($day);
	return mktime(23,59,59,date("m"),date("d")-$day,date("y"));
}

/**
 * 指定位置插入字符串
 * @param $str  原字符串
 * @param $i    插入位置
 * @param $substr 插入字符串
 * @return string 处理后的字符串
 */
function str_insert(&$str, $i, $substr){
	//指定插入位置前的字符串
	$startstr="";
	for($j=0; $j<$i; $j++){
		$startstr .= $str[$j];
	}
	//指定插入位置后的字符串
	$laststr="";
	for ($j=$i; $j<strlen($str); $j++){
		$laststr .= $str[$j];
	}
	//将插入位置前，要插入的，插入位置后三个字符串拼接起来
	$str = $startstr . $substr . $laststr;
	//返回结果
	return $str;
}

//多维数组 按某一字段 排序
function sort_array_by_field($array,$field,$desc=false)
{
	$fieldArr = array();
	foreach ($array as $k => $v) {
		$fieldArr[$k] = $v[$field];
	}
	$sort = $desc == false ? SORT_ASC : SORT_DESC;
	array_multisort($fieldArr, $sort, $array);
	return $array;
}
//多维数组 按某一字段 分组，支持排序
function group_array_by_field($array,$field,$sort=false)
{
	$group = array();
	foreach($array as $key=>$val)
	{
		$group[$val[$field]][] = $val;
	}
	
	if( $sort=='asc' )
	{
		ksort($group);
	}
	elseif( $sort=='desc' )
	{
		krsort($group);
	}
	
	return $group;
}

//获取订单信息
function get_to_order($order_id)
{
	//切换数据库 
	$order = M('order',null,'mysql://root:123456@127.0.0.1/lily3#utf8');
	$order_list = $order->where("id=$order_id")->find();
	$market = M('market',null,'mysql://root:123456@127.0.0.1/lily3#utf8');
	$couponcodes_id = $market->where('id='.$order_list['market_id'])->getField("couponcode_id");
	$couponcodes = M('couponcode',null,'mysql://root:123456@127.0.0.1/lily3#utf8');
	if($couponcodes_id!=0)
	{
		$couponcodes_name = $couponcodes->where('id='.$couponcodes_id)->getField("name");
	}
	else 
	{
		$couponcodes_name = "";
	}
	$order_web_data['order_number'] = $order_list['id'];
	$order_web_data['email'] = $order_list['customer_email'];
	$order_web_data['customer_id'] = $order_list['customer_id'];
	$order_web_data['message'] = $order_list['message'];
	$order_web_data['couponcode'] = $couponcodes_name;
	$order_web_data['total_price'] = $order_list['total_price'];
	$order_web_data['total_price_discount'] = $order_list['total_price_discount'];
	$order_web_data['device'] = $order_list['come_from'];
	$order_web_data['payment_style'] = $order_list['check_out_style'];
	$order_web_data['cookie_from'] = $order_list['cookie_from'];
	$order_web_data['least_from'] = $order_list['least_from'];
	$order_web_data['come_from'] = $order_list['come_from'];
	$order_web_data['date_time'] = $order_list['time'];
	$order_web = M('order_web',null,'mysql://root:123456@127.0.0.1/erp#utf8');
	$order_web_add = $order_web->add($order_web_data);
	$order_address = M('order_address',null,'mysql://root:123456@127.0.0.1/lily3#utf8');
	$order_address_list = $order_address->where('order_id='.$order_list['id'])->find();
	$data['order_web_id'] = $order_web_add;
	$data['first_name'] = $order_address_list['first_name'];
	$data['last_name'] = $order_address_list['last_name'];
	$data['country'] = $order_address_list['country'];
	$data['province'] = $order_address_list['province'];
	$data['city'] = $order_address_list['city'];
	$data['address'] = $order_address_list['address'];
	$data['code'] = $order_address_list['code'];
	$data['telephone'] = $order_address_list['telephone'];
	$order_web_address = M('order_web_address',null,'mysql://root:123456@127.0.0.1/erp#utf8');
	$order_web_address->add($data);
	$order_product = M('order_product',null,'mysql://root:123456@127.0.0.1/lily3#utf8');
	$order_product_list = $order_product->where('order_id='.$order_list['id'])->select();
	foreach($order_product_list as $order_product_list_value)
	{
		//同步单品
		if($order_product_list_value['product_id']!=0)
		{
			$order_web_product_data['order_web_id'] = $order_web_add;
			$order_web_product_data['set_sku'] = '';
			$order_web_product_data['price'] = $order_product_list_value['total_price'];
			$order_web_product_data['discount_price'] = $order_product_list_value['total_price_discount'];
			$order_web_product_data['number'] = $order_product_list_value['product_number'];
			$order_web_product_data['status'] = "正常";
			$product_model_stock = M('product_model_stock',null,'mysql://root:123456@127.0.0.1/lily3#utf8');
			$map['product_id'] = $order_product_list_value['product_id'];
			$map['color_id'] = $order_product_list_value['color_id'];
			$map['size_id'] = $order_product_list_value['size_id'];
			$model = $product_model_stock->where($map)->getField("model");
			if($model)
			{
				$id_product_sku = M('id_product_sku',null,'mysql://root:123456@127.0.0.1/erp#utf8');
				$product_sku_id = $id_product_sku->where("sku='$model'")->getField("id");
				if($product_sku_id)
				{
					$id_relate_sku =  M('id_relate_sku',null,'mysql://root:123456@127.0.0.1/erp#utf8');
					$product_code_id = $id_relate_sku->where('product_sku_id='.$product_sku_id)->getField("product_code_id");
					$order_web_product_data['code_id'] = $product_code_id;
				}
			}
			$order_web_product = M('order_web_product',null,'mysql://root:123456@127.0.0.1/erp#utf8');
			$order_web_product_add = $order_web_product->add($order_web_product_data);
		}
		//同步套件
		else if($order_product_list_value['product_set_id']!=0)
		{
			$order_data['order_web_id'] = $order_web_add;
			$products_set = M('products_set',null,'mysql://root:123456@127.0.0.1/lily3#utf8');
			$product_set_model = $products_set->where('id='.$order_product_list_value['product_set_id'])->getField("model");
			//获取套件set_sku值
			$order_data['set_sku'] = $product_set_model;
			$order_set_product = M('order_set_product',null,'mysql://root:123456@127.0.0.1/lily3#utf8');
			$order_set_product_list = $order_set_product->where('order_product_id='.$order_product_list_value['id'])->select();
			foreach($order_set_product_list as $set_value)
			{
				if($order_product_list_value['total_price']!=$order_product_list_value['total_price_discount'])
				{
					$discount = $order_product_list_value['total_price_discount']/$order_product_list_value['total_price'];
				}
				else
				{
					$discount =1;
				}
				$order_data['price'] = $set_value['total_price'];
				$order_data['discount_price'] = $set_value['total_price']*$discount;
				$order_data['number'] = $set_value['number'];
				$order_data['status'] = "正常";
				$product_set_model_stock = M('product_model_stock',null,'mysql://root:123456@127.0.0.1/lily3#utf8');
				$map['product_id'] = $set_value['product_id'];
				$map['color_id'] = $order_product_list_value['color_id'];
				//如果pillowcase_size不等于0
				if($set_value['pillowcase_size']!=0)
				{
					$map['size_id'] = $set_value['pillowcase_size'];
				}
				else 
				{
					$map['size_id'] = $order_product_list_value['size_id'];
				}
				//获取套件里单品model
				$products_set_model = $product_set_model_stock->where($map)->getField("model");
				if($products_set_model)
				{
					$products_set_sku = M('id_product_sku',null,'mysql://root:123456@127.0.0.1/erp#utf8');
					$products_set_sku_id = $products_set_sku->where("sku='$products_set_model'")->getField("id");
					if($products_set_sku_id)
					{
						$products_set_relate_sku = M('id_relate_sku',null,'mysql://root:123456@127.0.0.1/erp#utf8');
						$products_set_code_id = $products_set_relate_sku->where('product_sku_id='.$products_set_sku_id)->getField("product_code_id");
						$order_data['code_id'] = $products_set_code_id;
					}
				}		
				$order_web_product = M('order_web_product',null,'mysql://root:123456@127.0.0.1/erp#utf8');
				$order_web_product_add = $order_web_product->add($order_data);
			}
		}
		if($order_product_list_value['id'])
		{
			$order_product_gift = M('order_product_gift',null,'mysql://root:123456@127.0.0.1/lily3#utf8');
			$order_product_gift_message = $order_product_gift->where('order_product_id='.$order_product_list_value['id'])->getField("message");
			if($order_product_gift_message)
			{
				$order_web_product_data['gift_message'] = $order_product_gift_message;
			}
			$order_product_gift_box = M('order_product_gift_box',null,'mysql://root:123456@127.0.0.1/lily3#utf8');
			$gift_box = $order_product_gift_box->where('order_product_id='.$order_product_list_value['id'])->getField("message");
			if($gift_box)
			{
				$order_web_product_data['gift_box'] = $gift_box;
			}
			$order_product_gramming = M('order_product_gramming',null,'mysql://root:123456@127.0.0.1/lily3#utf8');
			$order_product_gramming_list = $order_product_gramming->where('order_product_id='.$order_product_list_value['id'])->select();
			if($order_product_gramming_list)
			{
				foreach($order_product_gramming_list as $order_product_gramming_list_value)
				{
					$order_web_product_data['gramming_name'] = $order_product_gramming_list_value['font_name'];
					$order_web_product_data['gramming_style'] = $order_product_gramming_list_value['font_style'];
					$order_web_product_data['gramming_color'] = $order_product_gramming_list_value['font_color'];
				}
			}
			if($order_product_gift_message || $gift_box || $order_product_gramming_list)
			{
				$order_web_product_data['order_web_product_id'] = $order_web_product_add;
				$order_web_product_extra = M('order_web_product_extra',null,'mysql://root:123456@127.0.0.1/erp#utf8');
				$order_web_product_extra->add($order_web_product_data);
			}
		}
	}
}

//平台列表
function come_from($style='')
{
	$web_list = array('us','au','ca','sg','uk','fr','de','it','nl','es','jp','ru','ar');
	$country_list = array('us'=>'美国','au'=>'澳大利亚','ca'=>'加拿大','sg'=>'新加坡','uk'=>'英国','fr'=>'法国','de'=>'德国','it'=>'意大利','nl'=>'荷兰','es'=>'西班牙','jp'=>'日本','ru'=>'俄罗斯','ar'=>'阿拉伯');
	$plat_list = array('Amazon.co.uk', 'Amazon.de',	'Amazon.fr', 'Amazon.es', 'Amazon.it');
	
	switch($style)
	{
		case 'web'://网站来源
			return $web_list;
			break;
		case 'plat'://平台来源
			return $plat_list;
			break;
		case 'country':
			return $country_list;
			break;
		case 'all':
		default:
			return array_merge($web_list,$plat_list);
	}
}

function come_from_new(){
	$come_from=M("id_come_from");
	$come_from_sql=$come_from->order("id asc")->select();
	$come_from_array='';
	foreach ($come_from_sql as $value){
		$come_from_array.="<option value='$value[id]'>$value[name]</option>";
	}
	return $come_from_array;
}

//订单状态
function order_status($style)
{
	switch($style)
	{
		case 'simple':
			return array('audit','accept','shipping','shipped','history','return','change','cancel');
			break;
		case 'full':
		default:
			return array(
				'audit'		=> '待审核',
				'accept'	=> '待收货',
				'shipping'	=> '待发货',
				'history'	=> '历史',
				'return'	=> '退货',
				'change'	=> '换货',
				'cancel'	=>	'已取消',
			);
	}
}

function order_status_list_hk($style)
{
	switch($style)
	{
		case "audit":
			$style='待审核'; break;
		case "accept":
			$style='待收货'; break;
		case "shipping":
			$style='待发货'; break;
		case "history":
			$style='历史'; break;
		case "return":
			$style='退货'; break;
		case "change":
			$style='换货'; break;	
		default:
			$style='未知'; break;
	}
	return $style;
	
}

//工厂订单状态
function factory_status($sta,$type)
{
	if($type == 1)
	{
		switch($sta)
		{
			case "new":
				$style='新单'; break;
			case "history":
				$style='历史'; break;
			default:
				$style=$sta; break;
		}
	}
	else
	{
		switch($sta)
		{
			case "new":
				$style='新单'; break;
			case "accept":
				$style='已接单'; break;
			case "shipping":
				$style='派送中'; break;
			case "history":
				$style='已收货'; break;
			case "history_ok":
				$style='历史'; break;			
			default:
				$style='未知'; break;
		}
	}
	return $style;
	
}
//根据用户id获取用户名
function get_user_name($user_id)
{
	$user_model = M('user');
	$row = $user_model->field('username')->find($user_id);
	return $row['username'];
}

//获取系统配置参数
function get_system_parameter($name,$option='auto')
{
	$parameter_model = M('system_parameters');
	$row = $parameter_model->where(array('name'=>$name))->find();
	switch($option)
	{
		case 'auto':
			return $row['value_int'] ? $row['value_int'] : $row['value_var'];
			break;
		case 'int':
			return $row['value_int'];
			break;
		case 'char':
			return $row['value_var'];
			break;
		case 'both':
			return array($row['value_int'],$row['value_var']);
			break;
	}
}

//判断是否超时. $start_time开始时刻（可以是date_time或者timestamp）； $limit_time限定时间（单位：天）
function is_out_time($start_time,$limit_time)
{
	if( !is_int($start_time) ) $start_time = strtotime($start_time);
	$current_time = time();
	return ($current_time-$start_time) > $limit_time*24*3600 ;
}

//获得hs信息
function hs($selected='')
{
	if($selected){
		switch($selected)
		{
			case 'hs:6302219010':	$select_1="selected='selected'";break;
			case 'hs:6302319990':	$select_2="selected='selected'";break;
			case 'hs:6304112910':	$select_3="selected='selected'";break;
			case 'hs:6302319100':	$select_4="selected='selected'";break;
			case 'hs:6302101000':	$select_5="selected='selected'";break;
			case 'hs:5407720000':	$select_6="selected='selected'";break;
			case 'hs:6301300000':	$select_7="selected='selected'";break;
			case 'hs:9404903000':	$select_8="selected='selected'";break;
			case 'hs:9404904000':	$select_9="selected='selected'";break;
			case 'hs:5407720000':	$select_10="selected='selected'";break;
			case 'hs:6302319100':	$select_11="selected='selected'";break;
			case 'hs:6505002000':	$select_12="selected='selected'";break;
			
			case 'hs:6302219010':	$select_13="selected='selected'";break;
			case 'hs:6302319990':	$select_14="selected='selected'";break;
			case 'hs:6304112910':	$select_15="selected='selected'";break;
			case 'hs:6302319100':	$select_16="selected='selected'";break;
			case 'hs:6302101000':	$select_17="selected='selected'";break;
			case 'hs:5407720000':	$select_18="selected='selected'";break;
			case 'hs:6301300000':	$select_19="selected='selected'";break;
			case 'hs:9404903000':	$select_20="selected='selected'";break;
			case 'hs:9404904000':	$select_21="selected='selected'";break;
			case 'hs:5407720000':	$select_22="selected='selected'";break;
			case 'hs:3402209000':	$select_23="selected='selected'";break;
			case 'hs:5209410090':	$select_24="selected='selected'";break;
			case 'hs:6505002000':	$select_25="selected='selected'";break;
			
			case 'hs:6302219010':	$select_26="selected='selected'";break;
			case 'hs:6302319990':	$select_27="selected='selected'";break;
			case 'hs:6304112910':	$select_28="selected='selected'";break;
			case 'hs:6302319100':	$select_29="selected='selected'";break;
			case 'hs:6302101000':	$select_30="selected='selected'";break;
			case 'hs:5407720000':	$select_31="selected='selected'";break;
			case 'hs:6301300000':	$select_32="selected='selected'";break;
			case 'hs:9404903000':	$select_33="selected='selected'";break;
			case 'hs:5407720000':	$select_34="selected='selected'";break;
			case 'hs:3402209000':	$select_35="selected='selected'";break;
			case 'hs:5209410090':	$select_36="selected='selected'";break;
			case 'hs:6505002000':	$select_37="selected='selected'";break;
		}
	}
	return "
	<option value='hs:6302219010,satin pillowcase sample,10.00' $select_1 >Pillowcase</option>
	<option value='hs:6302319990,satin duvet cover sample,15.00' $select_2 >Duvet cover</option>
	<option value='hs:6304112910,satin fitted sheet sample,15.00' $select_3 >Fitted sheet</option>
	<option value='hs:6302319100,satin flat sheet sample,15.00' $select_4 >Flat sheet</option>
	<option value='hs:6302101000,satin bedding set sample,20.00' $select_5 >bedding set</option>
	<option value='hs:5407720000,satin fabric sample,15.00' $select_6 >pajamas</option>
	<option value='hs:6301300000,cotton blanket sample,20.00' $select_7 >Blanket</option>
	<option value='hs:9404903000,cotton pillow sample,20.00' $select_8 >Pillow</option>
	<option value='hs:9404904000,cotton duvet sample,20.00' $select_9 >Duvet</option>
	<option value='hs:5407720000,satin swatch sample,5.00' $select_10 >swatch</option>
	<option value='hs:3402209000,Tenestar washing detergent,10.00' $select_11 >Tenestar washing detergent</option>
	<option value='hs:6505002000,Satin Cap,15.00' $select_12 >Satin Cap</option>
		
	<option value='hs:6302219010,satin pillowcase sample,10.00' $select_13 >TNT satin pillowcase sample</option>
	<option value='hs:6302319990,satin duvet cover sample,15.00' $select_14 >TNT satin duvet cover sample</option>
	<option value='hs:6304112910,satin fitted sheet sample,20.00' $select_15 >TNT satin fitted sheet sample</option>
	<option value='hs:6302319100,satin flat sheet sample,20.00' $select_16 >TNT satin flat sheet sample</option>
	<option value='hs:6302101000,satin bedding set sample,20.00' $select_17 >TNT satin bedding set sample</option>
	<option value='hs:5407720000,satin fabric sample,15.00' $select_18 >TNT satin fabric sample</option>
	<option value='hs:6301300000,cotton blanket sample,20.00' $select_19 >TNT cotton blanket sample</option>
	<option value='hs:9404903000,cotton pillow sample,20.00' $select_20 >TNT cotton pillow sample</option>
	<option value='hs:9404904000,cotton duvet sample,20.00' $select_21 >TNT cotton duvet sample</option>
	<option value='hs:5407720000,satin swatch sample,20.00' $select_22 >TNT satin swatch sample</option>
	<option value='hs:3402209000,Tenestar washing detergent,10.00' $select_23 >TNT Tenestar washing detergent</option>
	<option value='hs:5209410090,fabric sample,10.00' $select_24 >TNT fabric sample</option>
	<option value='hs:6505002000,Satin Cap,15.00' $select_25 >TNT Satin Cap</option>
		
	<option value='hs:6302219010,satin pillowcase sample,15.00' $select_26 >Fedex satin pillowcase sample</option>
	<option value='hs:6302319990,satin duvet cover sample,20.00' $select_27 >Fedex satin duvet cover sample</option>
	<option value='hs:6304112910,satin fitted sheet sample,20.00' $select_28 >Fedex satin fitted sheet sample</option>
	<option value='hs:6302319100,satin flat sheet sample,20.00' $select_29 >Fedex satin flat sheet sample</option>
	<option value='hs:6302101000,satin bedding set sample,60.00' $select_30 >Fedex satin bedding set sample</option>
	<option value='hs:5407720000,satin fabric sample,45.00' $select_31 >Fedex satin fabric sample</option>
	<option value='hs:6301300000,cotton blanket sample,40.00' $select_32 >Fedex cotton blanket sample</option>
	<option value='hs:9404903000,cotton pillow sample,40.00' $select_33 >Fedex cotton pillow sample</option>
	<option value='hs:9404904000,cotton duvet sample,40.00' $select_33 >Fedex cotton duvet sample</option>
	<option value='hs:5407720000,satin swatch sample,5.00' $select_34 >Fedex satin swatch sample</option>
	<option value='hs:3402209000,Tenestar washing detergent,20.00' $select_35 >Fedex Tenestar washing detergent</option>
	<option value='hs:5209410090,fabric sample,10.00' $select_36 >Fedex fabric sample</option>
	<option value='hs:6505002000,Satin Cap,15.00' $select_37 >Fedex Satin Cap</option>";
}

//导出execl
function exportExcel($data, $savefile = null, $title = null, $sheetname = 'sheet1' ,$sheet_more=array()) {
    import("Org.Util.PHPExcel");
	
    $first_array=array(
    		"data"=>$data,
    		"file_name"=>$savefile,
    		"title"=>$title,
    		"sheetname"=>$sheetname
    		);
    array_push($sheet_more,$first_array);
    $sheet_i=0;
    $objPHPExcel = new \PHPExcel();
    foreach($sheet_more as $sheet_more_value)
    {
		
    	$data=$sheet_more_value["data"];
    	$savefile=$sheet_more_value["file_name"];
    	$title=$sheet_more_value["title"];
    	$sheetname=$sheet_more_value["sheetname"];
	    //若没有指定文件名则为当前时间戳
	    if (is_null($savefile)) {
	        $savefile = time();
	    }
		
	    //若指字了excel表头，则把表单追加到正文内容前面去
	    if (is_array($title)) {
	        array_unshift($data, $title);
	    }
	   
	    //Excel内容
	    $head_num = count($data);
	    if($sheet_i>0){
	    $msgWorkSheet = new PHPExcel_Worksheet($objPHPExcel, 'card_message'); //创建一个工作表
	    $objPHPExcel->addSheet($msgWorkSheet); //插入工作表
	    }
	    $obj = $objPHPExcel->setActiveSheetIndex($sheet_i);
		
	    foreach ($data as $k => $v) {        
	        $row = $k + 1; //行
	        $nn = 0;
	        foreach ($v as $vv) {
				if ($nn < 26) {				//列
					$col = chr(65 + $nn);
				} elseif ($nn < 702) {
					$col = chr(64 + ($nn / 26)) . chr(65 + $nn % 26);
				} else {
					$col = chr(64 + (($nn - 26) / 676)). chr(65 + ((($nn - 26) % 676) / 26)) . chr(65 + $nn % 26);
				}				
				$obj->setCellValue($col . $row, $vv); //列,行,值
	            $nn++;
	        }
		}
		
		//设置列头标题
		/*
		$title_num = count($title); //表头长度
	    for ($i = 0; $i < $title_num; $i++) {
			if ($i < 26) {
	            $alpha = chr(65 + $i);
	        } elseif ($i < 702) {
	            $alpha = chr(64 + ($i / 26)) . chr(65 + $i % 26);
	        } else {
	            $alpha = chr(64 + (($i - 26) / 676)). chr(65 + ((($i - 26) % 676) / 26)) . chr(65 + $i % 26);
	        }
	        $objPHPExcel->getActiveSheet()->getColumnDimension($alpha)->setAutoSize(true); //单元宽度自适应 
	        $objPHPExcel->getActiveSheet()->getStyle($alpha . '1')->getFont()->setName("Candara");  //设置字体
	        $objPHPExcel->getActiveSheet()->getStyle($alpha . '1')->getFont()->setSize(12);  //设置大小
	        $objPHPExcel->getActiveSheet()->getStyle($alpha . '1')->getFont()->getColor()->setARGB(PHPExcel_Style_Color::COLOR_BLACK); //设置颜色
	        $objPHPExcel->getActiveSheet()->getStyle($alpha . '1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER); //水平居中
	        $objPHPExcel->getActiveSheet()->getStyle($alpha . '1')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER); //垂直居中
	        $objPHPExcel->getActiveSheet()->getStyle($alpha . '1')->getFont()->setBold(true); //加粗
			
			for ($z = 2; $z < $head_num+1; $z++) {
				$objPHPExcel->getActiveSheet()->getStyle($alpha . $z)->getFont()->setSize(11);  //设置大小
				$objPHPExcel->getActiveSheet()->getStyle($alpha . $z)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER); //垂直居中
				$objPHPExcel->getActiveSheet()->getStyle($alpha . $z)->getAlignment()->setWrapText(true); //自动换行
			}
	    }
		*/
		/*
		for ($i = 2; $i < $head_num+1; $i++) {
		//$objPHPExcel->getActiveSheet()->getStyle('D'.$i)->getFont()->getColor()->setARGB(PHPExcel_Style_Color::COLOR_RED); //设置颜色
		//$objPHPExcel->getActiveSheet()->getStyle('D'.$i)->getAlignment()->setWrapText(true); //自动换行
		}
		*/
	    $objPHPExcel->getActiveSheet()->setTitle($sheetname); //题目
	    //$objPHPExcel->setActiveSheetIndex($sheet_i); //设置当前的sheet  
	    $sheet_i++;
    
    }
	ob_end_clean();//清除缓冲区,避免乱码
    header('Content-Type: application/vnd.ms-excel');
    header('Content-Disposition: attachment;filename="' . $savefile . '.xls"'); //文件名称
    header('Cache-Control: max-age=0');
    $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5'); //Excel5 Excel2007
    $objWriter->save('php://output');
}


function delivery_style()
{
	return array('FEDEX','DHL','TNT','UPS','USPS','UPS货代',"顺丰","Amazon Logistics","ONTRAC",'EMS');
}


//特殊字符转换
function str_rewrite($str)
{   
	$str_now=str_replace(array("ó","Á","á","í","É","È","À","Â","Ê","Û","Î","Ô","Ù","Ë","é","è","ê","ë","à","â","û","ù","î","ô","ç","œ","ö","ä","ß","ü"),array("o","A","a","i","e","e","a","a","e","u","i","o","u","e","e","e","e","e","a","a","u","u","i","o","c","oe","o","a","b","u"),$str);
	return $str_now;
}

function httpPost($url, $parms="") {
	if ( !is_array($parms) ) {
		$url = $url . $parms;
	}
	if (($ch = curl_init($url)) == false) {
		throw new Exception(sprintf("curl_init error for url %s.", $url));
	}
//	dump($url);dump($ch);exit;
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);//不验证ssl
	curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_HEADER, 0);
	curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 600);
	if (is_array($parms)) {
		curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: multipart/form-data;'));
	}
	$postResult = @curl_exec($ch);
//	dump($postResult);exit;
	$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
	if ($postResult === false || $http_code != 200 || curl_errno($ch)) {
		$error = curl_error($ch);
		curl_close($ch);
		throw new Exception("HTTP POST FAILED:$error");
	} else {
		switch (curl_getinfo($ch, CURLINFO_CONTENT_TYPE)) {
			case 'application/json':
				$postResult = json_decode($postResult);
				break;
		}
		curl_close($ch);
		return $postResult;
	}
}

function select_all(){
	$catalog=M("id_catalog");
	$catalog_sql=$catalog->where(array("is_work"=>1))->field("name,id")->order("sort_id asc")->select();
	return $catalog_sql;
}

//注意：另在CommonController有相同方法
function get_stock_style($val){
	switch($val)
	{
		case 1:
			$style='FBA'; break;
		case 2:
			$style='本地'; break;
		case 3:
			$style='美国'; break;
		default:
			$style='未知'; break;
	}
	return $style;
}

function get_come_from_name($come_from_id)
{
	$come_from_model = M('id_come_from');
	return $come_from_model->where(array('id'=>$come_from_id))->getField('name');
}
function get_come_from_id($come_from_name)
{
	$come_from_model = M('id_come_from');
	return $come_from_model->where(array('name'=>$come_from_name))->getField('id');
}

function get_customer_info($customer_id)
{
	$model = M();
	return $model->db('lily3')->table('customers')->where( array('id'=>$customer_id) )->find();
}

function get_factory_str($factory_id,$date,$number,$type,$come_from)
{
	switch($factory_id)
	{
		case 1:
			$factory="H";
			break;
		case 2:
			$factory="R";
			break;
		case 3:
			$factory="L";
			break;
		case 4:
			$factory="S";
			break;	
		case 5:
			$factory="Z";
			break;
		case 6:
			$factory="T";
			break;
		case 7:
			$factory="NDZ";
			break;
		case 8:
			$factory="DZ";
			break;
		case 9:
			$factory="XDJ";			
			break;
		case 10:
			$factory="B";			
			break;
		
	}
	if($number>9)
	{
		$num = $number;	
	}
	else
	{
		$num = '0'.$number;	
	}
	if($type=='execl')
	{
		$br='';
	}
	else
	{
		$br='<br>';
	}
	
	if($come_from == 'plat')
	{
		$ac = "W";
	}
	
	return $factory.$ac.str_replace("-",".",substr($date,5))."-".$num.$br;
}



function get_factory_val($factory_id)
{
	$factoryDB = M('factory');
	$factory_name = $factoryDB->where('`id` = '.$factory_id)->getField("val");
	$fa_na = strtolower($factory_name );
	return $fa_na;
}

/* $data = array(
     0 => array(
  		'order' => array(
  			'order_number'		=>			//important
  			'email'				=>
  			'name'				=>
	  		'telephone'			=>
  			'currency'			=>	
  			'price'				=>0,
  			'message'			=>	
  			'is_gift_package'	=>	
  			'come_from_id'		=>			//important
  			'operator'			=>			//?
  			'date_time'			=>			//要求格式：Y-m-d H:i:s
  			'ship_service_level'=>	
  		)
  		'product' => array(
  			0 => array(
  				'name'			=>
  				'sku'			=>			//important
  				'price'			=>
  				'number'		=>	
	  			'gx1'			=>
	  			'gx2'			=>
	  			'amazon_extra'	=> array(
					ASIN		
					OrderItemId	
					QuantityShipped	
					ItemPrice	
					CurrencyCode	
					ShippingPrice	
					GiftWrapPrice	
					ItemTax	
					ShippingTax	
					GiftWrapTax	
					ShippingDiscount	
					PromotionDiscount
					ConditionId	
	  			),
  			),
  			1 => ...
  		)
  		'shipping' => array(
  			'name'					=>
  			'country'				=>
  			'state'					=>
  			'city'					=>
  			'address1'				=>
  			'address2'				=>
  			'address3'				=>
  			'post'					=>
  			'telephone'				=>
  			'shipping_style'		=>
  			'shipping_number'		=>
  			'shipping_weight'		=>
  			'shipping_price'		=>
  			'shipping_tax'			=>
  			'shipping_date'			=>
  			'shipping_operator'		=>
  			'shipping_hs'			=>
  			'shipping_sample'		=>
  			'shipping_report_price'	=>
  		)
  		'status' => 'unshipped'				//important
	  1 => ...
	)
 */
function make_platform_order($data)
{
	$order_model = D('order_plat_form');
	$product_model = D('order_plat_form_product');
	$shipping_model = D('order_plat_form_shipping');
	$status_model = D('order_plat_form_status');
	$status_history_model = D('order_plat_form_status_history');
	$sku_model = D('id_product_sku');
	$relate_model = D('id_relate_sku');
	$code_model = D('id_product_code');
	
	$order_data = array();
	$product_data = array();
	$shipping_data = array();
	$error_log = array();//错误日志
	foreach($data as $key=>$row)
	{
		//判断该订单是否已存在
		$exist = $order_model->where( array('order_number'=>$row['order']['order_number'],'come_from_id'=>$row['order']['come_from_id'] ) )->field('id')->find();
		if($exist)
		{
			$error_log[$row['order']['order_number']] = '订单已存在';
			continue;
		}
		//order
		$order_id = $order_model->add($row['order']);
		if( !$order_id )
		{
			$error_log[$row['order']['order_number']] = 'order表错误';
			continue;
		}
		//shipping
		$row['shipping']['order_platform_id'] = $order_id;
		$shipping_id = $shipping_model->add($row['shipping']);
		if( !$shipping_id )
		{
			$error_log[$row['order']['order_number']] = 'shipping表错误';
			continue;
		}
		//product
		foreach($row['product'] as $k=>$v)
		{
			$v['order_platform_id'] = $order_id;
			$sku_info = $sku_model->where( array('sku'=>$v['sku'],'come_from_id'=>$row['order']['come_from_id']) )->field('id')->find();
			if($sku_info)
			{
				$v['sku_id'] = $sku_info['id'];
				$relate_list = $relate_model->where( array('product_sku_id'=>$sku_info['id']) )->select();
				if($relate_list)//如果已关联
				{
// 					$old_num = $v['number'];
					foreach ($relate_list as $kk=>$vv)
					{
						$v['code_id'] = $vv['product_code_id'];
// 						$v['number'] = $old_num*$vv['number'];
						$product_id = $product_model->add($v);
					}
				}
				else //如果未关联,直接插入
				{
					$product_id = $product_model->add($v);
				}
			}
			elseif(!$sku_info)
			{
				if($v['sku'])
				{
					$sku_id = $sku_model->add( array('sku'=>$v['sku'],'come_from_id'=>$row['order']['come_from_id']) );
				}
				else
				{
					$sku_id = 0;
				}
				$v['sku_id'] = $sku_id;
				$product_id = $product_model->add($v);
			}

			if( !$product_id )
			{
				$error_log[$row['order']['order_number']] = 'product表错误';
				continue;
			}
			else 
			{
				//amazon_extra
				$amazon_extra_model = M('order_plat_form_product_extra_amazon');
				if( $v['amazon_extra'] )
				{
					$v['amazon_extra']['order_plat_form_product_id'] = $product_id;
					$product_amazon_extra_id = $amazon_extra_model->add($v['amazon_extra']);
					if( !$product_amazon_extra_id )
					{
						$error_log[$row['order']['order_number']] = 'amazon_extra表错误';
						continue;
					}
				}
			}
			
		}
		//status_history
		$status_data = array();
		if($row['status']) $status_data['status'] = $row['status'];
		if($order_id) $status_data['order_platform_id'] = $order_id;
		if($row['order']['operator']) $status_data['operator'] = $row['order']['operator'];
		if($row['order']['date_time']) $status_data['date_time'] = strtotime($row['order']['date_time']);
		$status_history_id = $status_history_model->add($status_data);
		if( !$status_history_id )
		{
			$error_log[$row['order']['order_number']] = 'status_history表错误';
			continue;
		}
		//status	此处将所有平台所有状态 分为 audit和history两种状态
		//amazon:Unshipped; ebay:Completed; lakuten:発送待ち,3月発送; 速卖通:Unshipped;
		if( !in_array($row['status'],array("Unshipped","Completed","新規受付","発送前入金待ち","発送待ち","発送後入金待ち","コンビニPC","銀行PC","当日発送")) )
		{
			$status_data['status'] = 'history';
			$status_id = $status_model->add($status_data);
			if( !$status_id )
			{
				$error_log[$row['order']['order_number']] = 'status_history表错误';
				continue;
			}
		}
	}
	return $error_log;//一维数组
}

//获取excel网站订单导入
function get_website_order($data)
{
	$order_web = D('order_web');
	$order_web_address = D('order_web_address');
	$order_web_product = D('order_web_product');
	$order_web_status = D('order_web_status');
	$error_status = array();
	//print_r($data);
	foreach($data as $data_value)
	{
		//判断订单是否存在
		$order_web_list = $order_web->where(array('order_number'=>$data_value['order']['order_number'],'come_from_id'=>$data_value['order']['come_from_id']))->field('id')->find();
		if($order_web_list)
		{
			$error_status[$data_value['order']['order_number']] = '订单已经存在';
			continue;
		}
		else
		{
			$order_web_id = $order_web->add($data_value['order']);
			if($order_web_id)
			{
				//order_web_address
				$data_value['address']['order_web_id'] = $order_web_id;
				$order_web_address_id = $order_web_address->add($data_value['address']);
				if(!$order_web_address_id)
				{
					$error_status[$data_value['order']['order_number']] = '获取订单地址表数据源格式出错';
					continue;
				}
				//order_web_product
				foreach($data_value['product'] as $product_value)
				{
					$product_value['order_web_id'] = $order_web_id;
					$prdocut_id = $order_web_product->add($product_value);
					if(!$prdocut_id)
					{
						$error_status[$data_value['order']['order_number']] = '获取订单产品表数据源格式出错';
						continue;
					}
				}
				//order_web_status
				$data_value['status']['order_web_id'] = $order_web_id;
				if($data_value['order']['date_time'])
				{
					$data_value['status']['date_time'] = strtotime($data_value['order']['date_time']);
				}
				$data_value['status']['message'] = '';
				$order_web_status_id = $order_web_status->add($data_value['status']);
				if(!$order_web_status_id)
				{
					$error_status[$data_value['order']['order_number']] = '获取订单状态表数据源格式出错';
					continue;
				}
			}
			else
			{
				$error_status[$data_value['order']['order_number']] = '获取订单表数据源格式出错';
				continue;
			}
		}
	}
	return $error_status;
}


function get_email_array($name,$order_number,$courier,$delivery_number,$courier_href_1,$courier_href_2,$country)
{
	$country=strtoupper($country);//转化大写
	$courier=strtoupper($courier);
	if($country=="US")
	{
		return EMAIL_HEAD_US.EMAIL_CENTER_US_NAME.$name.EMAIL_CENTER_US_ORDER_NUMBER.$order_number.EMAIL_CENTER_US_COURIER.$courier.EMAIL_CENTER_US_DELIVERY_NUMBER.$courier_href_1.EMAIL_CENTER_US_COURIER_URL.$courier_href_2.EMAIL_CENTER_US_COURIER_TWO. $courier.EMAIL_FOOT_US;
	}
	elseif($country=="AU")
	{
		return EMAIL_HEAD_AU.EMAIL_CENTER_AU_NAME.$name.EMAIL_CENTER_AU_ORDER_NUMBER.$order_number.EMAIL_CENTER_AU_COURIER.$courier.EMAIL_CENTER_AU_DELIVERY_NUMBER.$courier_href_1.EMAIL_CENTER_AU_COURIER_URL.$courier_href_2.EMAIL_CENTER_AU_COURIER_TWO.$courier.EMAIL_FOOT_AU;
	}
	elseif($country=="CA")
	{
		return EMAIL_HEAD_CA.EMAIL_CENTER_CA_NAME.$name.EMAIL_CENTER_CA_ORDER_NUMBER.$order_number.EMAIL_CENTER_CA_COURIER.$courier.EMAIL_CENTER_CA_DELIVERY_NUMBER.$courier_href_1.EMAIL_CENTER_CA_COURIER_URL.$courier_href_2.EMAIL_CENTER_CA_COURIER_TWO.$courier.EMAIL_FOOT_CA;
	}
	elseif($country=="UK")
	{
		return EMAIL_HEAD_UK.EMAIL_CENTER_UK_NAME.$name.EMAIL_CENTER_UK_ORDER_NUMBER.$order_number.EMAIL_CENTER_UK_COURIER.$courier.EMAIL_CENTER_UK_DELIVERY_NUMBER.$courier_href_1.EMAIL_CENTER_UK_COURIER_URL.$courier_href_2.EMAIL_CENTER_UK_COURIER_TWO.$courier.EMAIL_FOOT_UK;
	}
	elseif($country=="DE")
	{
		return EMAIL_HEAD_DE.EMAIL_CENTER_DE_NAME.$name.EMAIL_CENTER_DE_ORDER_NUMBER.$order_number.EMAIL_CENTER_DE_COURIER.$courier.EMAIL_CENTER_DE_DELIVERY_NUMBER.$courier_href_1.EMAIL_CENTER_DE_COURIER_URL.$courier_href_2.EMAIL_FOOT_DE;
	}
	elseif($country=="FR")
	{
		return EMAIL_HEAD_FR.EMAIL_CENTER_FR_NAME.$name.EMAIL_CENTER_FR_ORDER_NUMBER.$order_number.EMAIL_CENTER_FR_COURIER_URL.$courier_href_2.EMAIL_CENTER_FR_DELIVERY_NUMBER.$courier_href_1.EMAIL_FOOT_FR;
	}
	elseif($country=="NL")
	{
		return EMAIL_HEAD_NL.EMAIL_CENTER_NL_NAME.$name.EMAIL_CENTER_NL_ORDER_NUMBER.$order_number.EMAIL_CENTER_NL_COURIER.$courier.EMAIL_CENTER_NL_DELIVERY_NUMBER.$courier_href_1.EMAIL_CENTER_NL_COURIER_URL.$courier_href_2.EMAIL_CENTER_NL_COURIER_TWO.$courier.EMAIL_FOOT_NL;
	}
	elseif($country=="IT")
	{
		return EMAIL_HEAD_IT.EMAIL_CENTER_IT_NAME.$name.EMAIL_CENTER_IT_ORDER_NUMBER.$order_number.EMAIL_CENTER_IT_COURIER.$courier.EMAIL_CENTER_IT_DELIVERY_NUMBER.$courier_href_1.EMAIL_CENTER_IT_COURIER_URL.$courier_href_2.EMAIL_CENTER_IT_COURIER_TWO.$courier.EMAIL_FOOT_IT;
	}
	elseif($country=="ES")
	{
		return EMAIL_HEAD_ES.EMAIL_CENTER_ES_NAME.$name.EMAIL_CENTER_ES_ORDER_NUMBER.$order_number.EMAIL_CENTER_ES_DELIVERY_NUMBER.$courier_href_1.EMAIL_CENTER_ES_COURIER_URL.$courier_href_2.EMAIL_FOOT_ES;
	}
	elseif($country=="JP")
	{
		return EMAIL_HEAD_JP.EMAIL_CENTER_JP_NAME.$name.email_center_jp_courier.$courier.EMAIL_CENTER_JP_DELIVERY_NUMBER.$courier_href_1.EMAIL_CENTER_JP_COURIER_URL.$courier_href_2.EMAIL_FOOT_JP;
	}
}

function get_courier_href($courier,$delivery_number)
{
	$courier=strtoupper($courier);
	$courier_array=array();
	switch($courier)
	{
		case "DHL" :
		$courier_array[]="<a href='http://www.dhl.com'>$delivery_number</a>";
		$courier_array[]="<a href='http://www.dhl.com'>[ http://www.dhl.com ]</a>";
		//$courier_array[]="service@lilysilk.com";
		return $courier_array;
		break;
		
		case "FEDEX" :
		$courier_array[]="<a href='http://www.fedex.com'>$delivery_number</a>";
		$courier_array[]="<a href='http://www.fedex.com'>[ http://www.fedex.com ]</a>";
		//$courier_array[]="service@lilysilk.com";
		return $courier_array;
		break;
		
		case "TNT" :
		$courier_array[]="<a href='http://www.tnt.com'>$delivery_number</a>";
		$courier_array[]="<a href='http://www.tnt.com'>[ http://www.tnt.com ]</a>";
		//$courier_array[]="service@lilysilk.com";
		return $courier_array;
		break;
			
		case "UPS" :
		$courier_array[]="<a href='http://www.ups.com'>$delivery_number</a>";
		$courier_array[]="<a href='http://www.ups.com'>[ http://www.ups.com ]</a>";
		//$courier_array[]="service@lilysilk.com";
		return $courier_array;
		break;
	}
}

function send_email_function($email,$name,$order_number,$courier,$delivery_number,$country)
{
	vendor('Email.sendemail');
	$courier_href=get_courier_href($courier,$delivery_number);
	$email_html=get_email_array($name,$order_number,$courier,$delivery_number,$courier_href[0],$courier_href[1],$country);
	sendmail($email,$name,$email_html);
}


include("factory.php");
include("function_hk.php");