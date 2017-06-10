<?php

/*
 * 获取运单信息，order_delivery_parameters
 * 
 */
function get_delivery_info($order_id,$style)
{
	$delivery_model = M('order_delivery_parameters');
	$delivery_cost_model = M('order_delivery_cost');
// 	$delivery_detail_model = M('order_delivery_detail');
	$order_shipping_model = null;
	
	if($style=='web')
	{
		$order_shipping_model = M('order_web_address');
		$delivery_where = array('order_id'=>$order_id);
		$shipping_where = array('order_web_id'=>$order_id);
	}
	elseif($style=='plat')
	{
		$order_shipping_model = M('order_plat_form_shipping');
		$delivery_where = array('order_platform_id'=>$order_id);
		$shipping_where = array('order_platform_id'=>$order_id);
	}
	$delivery_row = $delivery_model->where($delivery_where)->find();
	if(!$delivery_row) return;
// 	$detail_row = $delivery_detail_model->where($shipping_where)->find();
	$shipping_row = $order_shipping_model->where($shipping_where)->find();
// 	$delivery_row['delivery_number'] = $detail_row['delivery_number'];
	$country = $shipping_row['country'];
	$weight = $delivery_row['weight'];
	$shipping_style = $delivery_row['shipping_style'];
	$delivery_cost_row = $delivery_cost_model->where( array('country'=>$country,'lower_weight'=>array('elt',$weight),'upper_weight'=>array('gt',$weight),'style'=>$shipping_style) )->find();
	$delivery_row['shipping_price'] = $delivery_cost_row['price'] ? $delivery_cost_row['price'] : 'no data';
	
	return $delivery_row;
	
}

function get_delivery_number($order_id,$style)
{
	$delivery_detail_model = M('order_delivery_detail');
	if($style=='web')
	{
		$where = array('order_web_id'=>$order_id);
	}
	elseif($style=='plat')
	{
		$where = array('order_platform_id'=>$order_id);
	}
	$detail_row = $delivery_detail_model->where($where)->find();
	//return $detail_row['delivery_number'];
	return $detail_row;
}

//估算 订单的总重量，按最低价格推荐快递;style:网站，平台
function delivery_recommend($order_id,$style)
{
	$delivery_cost_model = M('order_delivery_cost');
	if( $style=='web' )
	{
		$shipping_model = M('order_web_address');
		$product_model = D('order_web_product');
		$where = array( 'order_web_id'=>$order_id );
	}
	elseif( $style=='plat' )
	{
		$shipping_model = M('order_plat_form_shipping');
		$product_model = D('order_plat_form_product');
		$where = array( 'order_platform_id'=>$order_id );
	}

	$shipping_row = $shipping_model->where($where)->find();
	$country = $shipping_row['country'];
	//估重
	$total_weight = 0;
	$product_list = $product_model->where($where)->relation(array('code_info'))->select();
	for($j=0;$j<sizeof($product_list);$j++)
	{
		$total_weight += $product_list[$j]['code_info']['weight'];
	}
	//最低运费快递
	$delivery_row = $delivery_cost_model->where( array('country'=>$country,'lower_weight'=>array('elt',$total_weight),'upper_weight'=>array('gt',$total_weight)) )->order('price asc')->find();
	return array('style'=>$delivery_row['style'],'price'=>$delivery_row['price'],'weight'=>$total_weight);
}

//物流最后一条信息
function logistics_information($com,$nu)
{
	$order_delivery_detailDB=M('order_delivery_detail');
	$typeCom =$com;//快递公司
	$typeNu = $nu;  //快递单号
	//判断状态  
	$logistics_sta=$order_delivery_detailDB->where( array('style'=>$typeCom,'delivery_number'=>$typeNu) )->order('time desc')->getField('status');
	if($logistics_sta=="已签收" || $logistics_sta=="已退签")
	{
		$val =$order_delivery_detailDB->where('`style` = '.'"'.$typeCom.'"'.' and `delivery_number` ="'.$typeNu.'"')->order('time desc')->getField('message');
	}
	else
	{			
		$AppKey=C('AppKey');         //在http://kuaidi100.com/app/reg.html申请到的KEY
		$url ='http://api.kuaidi100.com/api?id='.$AppKey.'&com='.$typeCom.'&nu='.$typeNu.'&show=0&muti=1&order=desc'; 
			/*show  0：返回json字符串，1：返回xml对象，2：返回html对象，3：返回text文本。如果不填，默认返回json字符串。*/			
		
		//请勿删除变量$powered 的信息，否者本站将不再为你提供快递接口服务。
		$powered = '查询数据由：<a href="http://kuaidi100.com" target="_blank">KuaiDi100.Com （快递100）</a> 网站提供 ';
		
		//优先使用curl模式发送数据
		if (function_exists('curl_init') == 1){
		  $curl = curl_init();
		  curl_setopt ($curl, CURLOPT_URL, $url);
		  curl_setopt ($curl, CURLOPT_HEADER,0);
		  curl_setopt ($curl, CURLOPT_RETURNTRANSFER, 1);
		  curl_setopt ($curl, CURLOPT_USERAGENT,$_SERVER['HTTP_USER_AGENT']);
		  curl_setopt ($curl, CURLOPT_TIMEOUT,5);
		  $get_content = curl_exec($curl);
		  curl_close ($curl);
		}else{
		  import('Org.Util.Snoopy');   
		  $snoopy = new \Snoopy();
		  $snoopy->referer = 'http://www.google.com/';//伪装来源
		  $snoopy->fetch($url);
		  $get_content = $snoopy->results;
		}
		$aa=json_decode($get_content,true);           //json转换数组
		$courier_val=$aa['data'];                     //获得快递信息
		$val=$courier_val[0]['context'];
		//判断数据表是否有记录
		$order=$order_delivery_detailDB->where( array('delivery_number'=>$typeNu) )->find();       // 物流信息 获得订单表ID
		if($order){
			foreach($courier_val as $k=>$v)
			{
				$info=$order_delivery_detailDB->where('`message` = '.'"'.$v['context'].'"'.' and `delivery_number` ="'.$typeNu.'" and `time` ='.'"'.$v[time].'"')->find();
				if(!$info)
				{
					$date['order_platform_id'] = $order['order_platform_id'];
					$date['order_web_id'] = $order['order_web_id'];
					$date['style']=$typeCom;
					$date['delivery_number']=$typeNu;
					$date['message'] = $v['context'];
					$date['time'] = $v['time'];
					$order_delivery_detailDB->add($date);
				}
				
			}
			//快递状态
			if(isset($aa['state'])){
					$info=$order_delivery_detailDB->where('`style` = '.'"'.$typeCom.'"'.' and `delivery_number` ="'.$typeNu.'"')->order('time desc')->find();
					$whereID='`id` ='.$info['id'];
					$vall['status'] = logistics_status($aa['state']);
					$order_delivery_detailDB->where($whereID)->save($vall);
			}
		}
	
	}
	return $val;
}

function get_last_message($delivery_number,$order_id,$style){
	if($style=='web'){
		$where["order_web_id"]=$order_id;
	}elseif($style=='plat'){
		$where["order_platform_id"]=$order_id;
	}
	$where["delivery_number"]=$delivery_number;
	$message=M("order_delivery_detail")->where($where)->field("message,time")->order("id desc")->find();
	return $message;
}

function order_status_list($order_id,$style){
	if($style=='web'){
		$order_status_list=M("order_web_status")->where(array("order_web_id"=>$order_id))->select();
	}elseif($style=='plat'){
		$order_status_list=M("order_plat_form_status")->where(array("order_platform_id"=>$order_id))->select();
	}
	return $order_status_list;
}

function get_set_sku_name($sku){
	$sku_name=M("id_product_sku")->where(array("sku"=>$sku))->field("name")->find();
	return $sku_name[name];
}

function get_payment_fail($order_id){
	$payment_fail=M("order_web_supplement");
	$payment_fail_sql=$payment_fail->where(array("order_web_id"=>$order_id))->field("payment_fail,custome_ip,come_from_history")->find();
	return $payment_fail_sql;
}

function get_ip($where,$style){
	switch ($style){
		case 'customer':
		$customer_ip=M("customer_ip",null,"lily3")->where(array("email"=>$where))->order("id desc")->find();
		return $customer_ip;
		break;
		case 'shopcart':
		$shopcart_ip=M("shopcart_ip",null,"lily3")->where(array("order_id"=>$where))->find();
		return $shopcart_ip;
		break;
	}
}

function get_delivery_price($country,$style,$weight){
	$where["country"]=$country;
	$where["style"]=$style;
	$where["lower_weight"]=array("lt",$weight);
	$where["upper_weight"]=array("egt",$weight);
	$weight_price=M("order_delivery_cost")->where($where)->field("price")->find();
	return $weight_price[price];
}

//修改网站订单列表结构
function complete_web_order($order_list,$come_from_id)
{
	$product_original_model = D('order_web_product_original');
	
	foreach($order_list as $key=>$val)
	{
		//order_web_product  将同属套件的产品归并
		$product_list = $val['order_web_product'];
		$group_product_info = group_array_by_field($product_list,'order_product_id');
		$order_list[$key]['order_web_product'] = $group_product_info;
		
		//order_web_product_original 添加sku的关联情况
		$product_original_info = $val['product_original_info'];//商品原始数据
		foreach ($product_original_info as $k=>$v)
		{
			if( !$v['product_set_name'] ) //单件
			{
				$return = check_and_register_sku( $v['sku'], $come_from_id);
				$order_list[$key]['product_original_info'][$k]['sku_id'] = $return['sku_id'];
				$order_list[$key]['product_original_info'][$k]['relate'] = $return['relate'];
			}
			else //套件 
			{
				foreach($v['set_info'] as $kk=>$vv)
				{
					$return = check_and_register_sku( $vv['sku'], $come_from_id);
					$order_list[$key]['product_original_info'][$k]['set_info'][$kk]['sku_id'] = $return['sku_id'];
					$order_list[$key]['product_original_info'][$k]['set_info'][$kk]['relate'] = $return['relate'];
				}
			}
		}
		
		if( I('get.order_status')=='shipping' )
		{
			$delivery_cost_model = M('order_delivery_cost');
			//估重 所有商品总重量
			$total_weight = 0;
			$product_list = $val['order_web_product'];
			foreach($product_list as $k=>$v)
			{
				$total_weight += $v['code_info']['weight'];
			}
			$order_list[$key]['total_weight'] = $total_weight;
			//order_delivery_cost 最低运费快递
			$country = $val['shipping_info']['country'];
			$delivery_row = $delivery_cost_model->where( array('country'=>$country,'lower_weight'=>array('elt',$total_weight),'upper_weight'=>array('gt',$total_weight)) )->order('price asc')->find();
			$order_list[$key]['delivery'] = $delivery_row;
		}
		
		if( I('get.order_status')!='history' )
		{
			$order_model = M('order_web');
			//检查用户当前是否有多个订单
			if( $val['email'] )
			{
				$temp_where = array(
					'email' => $val['email'],
					"b.status<>'history' OR b.status is null",
				);
				$temp_list = $order_model->alias('a')->where($temp_where)->join('LEFT JOIN order_web_status as b ON a.id=b.order_web_id')->select();
				$order_list[$key]['total_order_count'] = count($temp_list);
			}
			else 
			{
				$order_list[$key]['total_order_count'] = 0;
			}
		}
		
	}
	
	//处理不同order_status的页面
	if( I('get.order_status')=='shipping' )
	{
		$delivery_cost_model = M('order_delivery_cost');
		for ($i=0;$i<sizeof($order_list);$i++)
		{
			$order_row = $order_list[$i];
			//估重
			$total_weight = 0;
			$product_list = $order_row['product_info'];
			for($j=0;$j<sizeof($product_list);$j++)
			{
				$total_weight += $product_list[$j]['code_info']['weight'];
			}
			$order_list[$i]['total_weight'] = $total_weight;
			//最低运费快递
			$country = $order_row['shipping_info']['country'];
			$delivery_row = $delivery_cost_model->where( array('country'=>$country,'lower_weight'=>array('elt',$total_weight),'upper_weight'=>array('gt',$total_weight)) )->order('price asc')->find();
			$order_list[$i]['delivery'] = $delivery_row;
		}
	}
	
	return $order_list;
}


//检查sku的关联情况，如果是新sku则添加入id_product_sku
function check_and_register_sku($sku,$come_from_id)
{
	$sku_model = M('id_product_sku');
	$sku_id = 0;
	$relate = false;
	$where = array('sku'=>$sku,'come_from_id'=>$come_from_id);
	$sku_row = $sku_model->where($where)->find();
	if( $sku=='' )//如果sku为空，不添加一个值为空的sku
	{
		$sku_id = 0;
		$relate = false;
	}
	elseif( !$sku_row )//新注册sku
	{
		$sku_id = $sku_model->add($where);
		$relate = false;
	}
	else//sku已存在
	{
		$relate_model = M('id_relate_sku');
			
		$sku_id = $sku_row['id'];
		$relate_info = $relate_model->where(array('product_sku_id'=>$sku_id))->select();
		$relate = $relate_info ? true : false;
	}
	return array(
		'sku_id'	=> $sku_id,
		'relate'	=> $relate,//是否已关联
	);
}

//修改网站订单列表结构
function complete_plat_order($order_list,$come_from_id)
{
	$product_original_model = D('order_web_product_original');
	$relate_model = D('id_relate_sku');
	foreach($order_list as $key=>$val)
	{
		//修改order_web_product数据结构，将同属套件的产品归并
		$product_info = $val['product_info'];
		$group_product_info = group_array_by_field($product_info,'sku_id');
		//日本乐天没有存在没有sku的情况
		if( $come_from_id==20 && $group_product_info[0])
		{
			foreach( $group_product_info[0] as $k=>$v )
			{
				$group_product_info["rakuten_$k"][0] = $v;
			}
			unset($group_product_info[0]);
		}
		//获得单品在套件中的数量
		foreach($group_product_info as $k=>$v)
		{
			foreach($v as $kk=>$vv)
			{
				$relate_row = $relate_model->where( array('product_sku_id'=>$vv['sku_id'],'product_code_id'=>$vv['code_id']) )->field('number')->find();
				$group_product_info[$k][$kk]['number_in_set'] = $relate_row['number'];
			}
		}
		
		if( I('get.order_status')!='history' )
		{
			$order_model = M('order_plat_form');
			//检查用户当前是否有多个订单
			if( $val['email'] )
			{
				$temp_where = array(
					'email' => $val['email'],
					"b.status<>'history' OR b.status is null",
				);
				$temp_list = $order_model->alias('a')->where($temp_where)->join('LEFT JOIN order_plat_form_status as b ON a.id=b.order_platform_id')->select();
				$order_list[$key]['total_order_count'] = count($temp_list);
			}
			else 
			{
				$order_list[$key]['total_order_count'] = 0;
			}
		}
		
		$order_list[$key]['product_info'] = $group_product_info;
	}
	return $order_list;
}

//获得套件商品 的实际数量
function real_num_of_set($sku_id,$code_id,$set_num)
{
	$relate_model = D('id_relate_sku');
	$row = $relate_model->where( array('product_sku_id'=>$sku_id,'product_code_id'=>$code_id) )->field('number')->find();
	if( $row['number'] )
	{
		return $row['number']*$set_num;
	}
	else 
	{
		return 0;
	}
}

//检查  系统设置的  操作超时订单  判断是否超时.
function check_limit_time($order_id,$order_status,$target_status,$start_time,$limit_time,$message,$style=null)
{
	if( $order_status!=$target_status && $target_status!='any' ) return;//target=any表示应用于所有 订单状态页面

	$style || $style='gradual_color_lv9';
	if( !is_int($start_time) ) $start_time = strtotime($start_time);//如果是date_time,转为timestamp
	$current_time = time();
	$limit_time = $limit_time*3600;//单位：小时
	if( ($current_time-$start_time)>$limit_time )
	{
		echo "<script>mark_row($order_id,'$message','$style')</script>";
	}
// 	dump(func_get_args());

}

//网站订单，真实订单号
function web_order_number($order_id,$date_time=null,$customer_id=null,$order_number=null)
{
	if( $order_id )
	{
		$order_model = M('order_web');
		$order_row = $order_model->find($order_id);
		$date_time = $order_row['date_time'];
		$customer_id = $order_row['customer_id'];
		$order_number = $order_row['order_number'];
	}
	
	$temp = substr($date_time, 0, 10);
	$temp = str_replace('-', '0', $temp);
	$return .= $temp;
	$return .= "0$customer_id";
	$return .= "0$order_number";
	
	return $return;
}

//标记 恶意用户
function mark_bad_user($order_id,$email,$first_name,$last_name,$ip=null,$phone=null)
{
	$bad_user_model = D('bad_user');
	//确定
	$where=array();
	if(isset($email))
	{
		$where["email"] = $email;
	}
	if(!empty($phone))
	{
		
		$where["telephone"]=$phone;
	}
	if(isset($ip))
	{
		$ip_array_all=array();
		$ip_array=explode("<br>",$ip);
		for($i=0;$i<count($ip_array);$i++)
		{
			$number_id_array=explode(":",$ip_array[$i]);
			$number_id_array[1]=str_replace(" ","",$number_id_array[1]);
			if(!empty($number_id_array[1]))
			{
				$ip_array_all[]=$number_id_array[1];
			}
		}
		$where["custome_ip"]=array("in",$ip_array_all);
	}
	
	$where["_logic"]="OR";
	$bad_user_row = $bad_user_model->where($where)->select();
	if($bad_user_row)
	{
		$message = "恶意用户。{$bad_user_row['remark']}。";
		echo "<script>mark_row($order_id,'$message','gradual_color_lv10')</script>";
		return;
	}
	//可能
	$where = array('first_name'=>$first_name,'last_name'=>$last_name);
	$bad_user_row = $bad_user_model->where($where)->find();
	if($bad_user_row)
	{
		$message = "可能为恶意用户。{$bad_user_row['remark']}。";
		echo "<script>mark_row($order_id,'$message','gradual_color_lv9')</script>";
		return;
	}
}

//标记 样布订单
function mark_sample_email($order_id, $email, $come_from_id)
{
	$sample_model = D('sample_record');
	$result = $sample_model->where( array('email'=>$email,'come_from_id'=>$come_from_id,'is_send'=>0) )->find();
	if($result)
	{
		$message = "含样布订单";
		echo "<script>mark_row($order_id,'$message')</script>";
		return;
	}
}

//操作 网站未审核订单中 某一sku的产品
function sku_in_audit_order_list($sku, $come_from_id, $action)
{
	//所有未审核订单
	$sql_1 = " SELECT order_web_id FROM order_web_status WHERE status!='audit' ";
	//限定come_from_id
	$sql_2 = " SELECT id FROM order_web WHERE id NOT IN ($sql_1) AND come_from_id='$come_from_id' ";
	//限定 order_web_product_original 中的 sku， 并过滤其中的套件
	$sql_3 = " SELECT count(a.id) AS count FROM order_web_product_original AS a LEFT JOIN order_web AS b ON a.order_web_id=b.id WHERE b.id IN ($sql_2) AND a.sku='{$sku}' AND a.product_set_name='' ";
	//所有order_web_product_original
	$sql_4 = " SELECT id FROM order_web_product_original WHERE order_web_id IN ($sql_2) ";
	//order_web_product_original_set
	$sql_5 = " SELECT count(id) AS count FROM order_web_product_original_set WHERE order_web_product_original_id IN ($sql_4) AND sku='$sku' ";
	//所有 order_web_product_original行（只含单件）
	$sql_6 = " SELECT a.* FROM order_web_product_original AS a LEFT JOIN order_web AS b ON a.order_web_id=b.id WHERE b.id IN ($sql_2) AND a.sku='$sku' AND a.product_set_name='' ";
	//所有 order_web_product_original_set行
	$sql_7 = " SELECT * FROM order_web_product_original_set WHERE order_web_product_original_id IN ($sql_4) AND sku='$sku' ";
	
	if( $action=='count' )
	{
		$temp = M()->query($sql_5);
		$count_set = $temp[0]['count'];//套件 包含数
		$temp = M()->query($sql_3);
		$count_single = $temp[0]['count'];//单件 包含数
		return $count_set+$count_single;
	}
	elseif( $action=='product_original' )
	{
		return M()->query($sql_6);
	}
	elseif( $action=='product_original_set' )
	{
		return M()->query($sql_7);
	}
	
}

//
function check_local_stock($code_id, $number)
{
	if($code_id==0) return;
	
	$stock_model = M('product_stock');
	$where = array(
		'code_id'	=> $code_id,
		'number'	=> array('egt',$number),
		'style'		=> 2,);
	$result = $stock_model->where($where)->find();
	if($result) return '（库存）';
}

function address_isset_box($address)
{
	$po_box_array=array("PO","P.O.","P.O");
	$de_po_box_array=array("Packstation","Postfach ist verboten");
	for($i=0;$i<count($po_box_array);$i++)
	{
		if(stripos($address,$po_box_array[$i])!==false && stripos($address,"BOX")!==false)
		{
			return "地址中存在PO BOX";
		}
	}
	for ($j=0;$j<count($de_po_box_array);$j++)
	{
		if(stripos($address,$de_po_box_array[$j])!==false)
		{
			return "地址中存在PO BOX";
		}
	}
	if(stripos($address,"APO"))
	{
		return "地址中存在APO";
	}
	if(stripos($address,"FPO"))
	{
		return "地址中存在FPO";
	}
}


