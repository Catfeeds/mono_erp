<?php
//快递状态
/*0：在途，即货物处于运输过程中；
1：揽件，货物已由快递公司揽收并且产生了第一条跟踪信息；
2：疑难，货物寄送过程出了问题；
3：签收，收件人已签收；
4：退签，即货物由于用户拒签、超区等原因退回，而且发件人已经签收；
5：派件，即快递正在进行同城派件；
6：退回，货物正处于退回发件人的途中；*/
function logistics_status($val)
{
	switch($val)
	{
	   case "0":
		 $sta= '在途中'; break;	  
	   case "1":
		 $sta= '已揽件'; break; 
	   case "2":
		 $sta= '寄送出现问题'; break;
	   case "3":
		 $sta= '已签收'; break;
	   case "4":
		 $sta= '已退签'; break;
	   case "5":
		 $sta= '同城派件'; break;
	   case "6":
		 $sta= '退回状态'; break;		 	 
	default :
		 $sta='出现错误';   break;
	}
	return $sta;
}
//判断新品审核状态
function panduan_status($val){
	switch($val)
	{
	   case 0:
		 $status="正在审核"; break; 
	   case 1:
		 $status="通过->工厂"; break; 
	   case 2:
		 $status= '工厂接收成功'; break; 
	   case 3:
		 $status='工厂已发货'; break; 
	   case 4:
		 $status='已收到货'; break; 
	   case 8:
		 $status='放弃申请'; break; 
	   case 9:
		 $status='未通过审核'; break; 
		 
	   default :
		 $status='未选择';   break;
	}
	return $status;
}
//库存类型输出
function STYLE(){
	$STYLE[1]='FBA';
	$STYLE[2]='本地';
	$STYLE[3]='美国';
	return $STYLE;
	}
//判断库存类型
function panduan_style($val){
	switch($val)
	{
	   case 1:
		 $style='FBA'; break;  
	   case 2:
		 $style='本地'; break;
	   case 3:
		 $style='美国'; break;
	   default:
		 $style='未选择'; break;
	}
	return $style;
}
//年月日
function panduan_YMD($val){
	$time=date('Y-m-d',$val);
	return $time;
	}
//时间	
function panduan_time($val){
	$time=date('Y-m-d H:i:s',$val);
	return $time;
	}
//操作人员名称
function userName($val){
	$userDB = M('user');                    //用户表
	$user=$userDB->where('`id` ='.$val)->find();
	return $user['username'];
	}
//工厂状态
function factory_sta($val){
	switch($val)
	{
	   case 2:
		 $status= '未完成'; break; 
	   case 3:
		 $status= '已提交'; break;
	   case 4:
		 $status= '对方已收货'; break;	  	 
	  default :
		 $status='出现错误';   break;
	}
	return $status;
	}	
//套件名称判断
function panduan_skuName($val){
	$idproductskuDB = M('id_product_sku');                    //产品信息  
	$sku_name=$idproductskuDB->where('`id` ='.$val)->getField('name');
	return $sku_name;
	}
			
//套件sku判断
function panduan_sku($val){
	$idproductskuDB = M('id_product_sku');                    //产品信息  
	$sku=$idproductskuDB->where('`id` ='.$val)->getField('sku');
	return $sku;
	}
	



//套件sku 名称 判断
function panduan_sku_name($val){
	$idproductskuDB = M('id_product_sku');                    //产品信息  
	$sku=$idproductskuDB->where('`id` ='.$val)->getField('sku');
	$sku_name=$idproductskuDB->where('`id` ='.$val)->getField('name');
	if($val==0){
		$skuName="";
		}else{
			$skuName=$sku_name.' ('.$sku.')';
		}
	return $skuName;
	}	
//单品名称判断
function panduan_codeName($val){
	$idproductcodeDB=M('id_product_code');    //产品信息           
	$code_name=$idproductcodeDB->where('`id` ='.$val)->getField('name');
	return $code_name;
	}
//套件产品内容
function panduan_sku_val($val){
	$idproductcodeDB=M('id_product_code');    //产品信息 
	$idrelateskuDB=M('id_relate_sku');         //套件与产品链接表       
	$sku=$idrelateskuDB->where('`product_sku_id` ='.$val)->select();
	  foreach($sku as $k=>$v){			  
		  $code[$k]=$idproductcodeDB->where('`id` ='.$sku[$k]['product_code_id'])->find();
		  $code_value[$k]=$sku[$k]['number']."件".$code[$k]['name'].';    ';
		  }
	return $code_value;
	}
	
//退款状态   （客服）
function refund_sta($val){
	switch($val)
	{
	   case 0:
		 $status= '审核中...'; break;	  
	   case 1:
		 $status= '打款中...'; break; 
	   case 2:
		 $status= '打款完成'; break;
	   case 9:
		 $status= '未通过审核'; break;
	default :
		 $status='出现错误';   break;
	}
	return $status;
	}	
//币种判断
function currency($val){
	switch($val)
	{
	    case "Amazon.co.uk":
		 $currency= 'GBP'; break;	  
	    case "Amazon.de":
		 $currency= 'EUR'; break; 
	    case "Amazon.fr":
		 $currency= 'EUR'; break;
	    case "Amazon.it":
		 $currency= 'EUR'; break;
	    case "Amazon.es":
		 $currency= 'EUR'; break;
	   	case "us":
		 $currency= 'USD'; break;
	 	case "au":
		 $currency= 'AUD'; break;
		case "ca":
		 $currency= 'CAD'; break;
		case "sg":
		 $currency= 'SGD'; break;			
		case "uk":
		 $currency= 'GBP'; break;
		case "de":
		 $currency= 'EUR'; break; 
	    case "fr":
		 $currency= 'EUR'; break;
	    case "it":
		 $currency= 'EUR'; break;
	    case "es":
		 $currency= 'EUR'; break;
		case "nl":
		 $currency= 'EUR'; break;
		case "jp":
		 $currency= 'JPY'; break;
		case "ru":
		 $currency= 'USD'; break;
		case "ar":
		 $currency= 'USD'; break;     	
		default :
		 $currency='出现错误'; break;
	}
	return $currency;
	}					

function remark_sta($val){
	switch($val)
	{
	   case "0":
		 $sta= '未处理'; break;	  
	   case "1":
		 $sta= '已处理'; break; 
	   case "2":
		 $sta= '已失效'; break;		 	 
	default :
		 $sta='出现错误';   break;
	}
	return $sta;
}
//根据code查询code name
function code_val($val){
	$id_product_codeDB=M('id_product_code');
	$info=$id_product_codeDB->where('`code` ="'.$val.'"')->getField('name');
	return $info;
}
//根据id 查询 订单号
function order_number($order_id,$data){
	$info=M($data)->where('`id` ='.$order_id)->getField('order_number');
	return $info;
}	
//查询一条    $data 数据库     $conditions 查询条件
function FindData($data,$conditions)
{
	$info=M($data)->where($conditions)->find();
	return $info;
}
//判断NULL
function PD_null($val)
{
	if($val == NULL)
	{
		$ret = "";	
	}
	else
	{
		$ret = $val;
	}
	return $ret;
}
function remark_warning($val)
{
	switch($val)
	{
		case '1':
			$sta = '<td align="center" ><span style="color: #fff;font-weight: bold;display: block;width: 50px;height: 25px; background: #FF0047;
line-height: 25px;">立即</span></td>'; break;
		case '2':
			$sta = '<td align="center" ><span style="color: #fff;font-weight: bold;display: block;width: 50px;height: 25px; background: #F60;
line-height: 25px;">加急</span></td>'; break;
		case '3':
			$sta = '<td align="center" ><span style="color: #fff;font-weight: bold;display: block;width: 50px;height: 25px; background: #FFC800;
line-height: 25px;">正常</span></td>'; break;
		default :
		 	$sta = '<td align="center" ><span style="color: #fff;font-weight: bold;display: block;width: 50px;height: 25px; background: #1BFF00;
line-height: 25px;">出现错误</span></td>'; break;
	}
	return $sta;
	
}
//工厂改变后的状态
function fac_sta($sta)
{
	switch($sta)
	{
		case "new":
			$input_sta='accept'; break;
		case "accept":
			$input_sta='shipping'; break;	
		case "shipping":
			$input_sta='history'; break;
		case "history":
			$input_sta='history_ok'; break;	
	}
	return $input_sta;
}

//计算订单的重量
function order_weight($order_id,$type)
{
	
	if($type == 'web')
	{
		//$dataDB = M('order_web');
		//$order_num = $dataDB -> where('`id` ='.$order_id)->getField('order_number');
		$country = M('order_web_address') ->where('`order_web_id` ='.$order_id) ->getField('country');
		$code_list = M('order_web_product')->where('`order_web_id` ='.$order_id)->field('code_id,number')->select();
	}
	elseif($type =='plat')
	{
		//$dataDB = M('order_web');
		//$order_num = $dataDB -> where('`id` ='.$order_id)->getField('order_number');
		$country = M('order_plat_form_shipping') ->where('`order_platform_id` ='.$order_id) ->getField('country');
		$code_list = M('order_plat_form_product')->where('`order_platform_id` ='.$order_id)->field('code_id,number')->select();
	}
	
	$weight_one = 0;
	foreach($code_list as $k=>$v)
	{
		$weight = M('id_product_code')->where('`id` ='.$v['code_id'])->getField('weight');
		$weight_one += $weight * $v['number'];
	}
	//return $country;
	return delivery_priority($weight_one,strtoupper($country));


}
//判断是否有快递优选
function delivery_priority($weight_one,$country)
{
	$order_delivery_priorityDB = M('order_delivery_priority');
	
	$date['country'] = $country;
	$date['lower_weight'] = array('elt',$weight_one);
	$date['upper_weight'] = array('egt',$weight_one);
	//dump($date);exit;
	$style = $order_delivery_priorityDB->where($date)->getField('style');
	
	return array('style'=>$style,'weight'=>$weight_one);
	
}
//是否有礼品  礼品盒  绣字
/*
$br   分段的方式
*/
function product_extra($id,$br)
{
	
	$info ='';
	if($br)
	{
		$br = $br;	
	}
	else
	{
		$br =" \r\n ";
	}
	$product_id = M('order_web_product')->where('`order_web_id` ='.$id)->getField('id');
	if($product_id)
	{
		$extra = M('order_web_product_extra') ->where('`order_web_product_id` ='.$product_id)->find();
		if($extra['gift_box'] !='')
		{
			$info.='含礼品盒';
		}
		if($extra['gramming_name'] !='')
		{
			if($info != "")
			{
				$aa= $br;
			}
			else
			{
				$aa='';
			}
			$info.=$aa.'含绣字';
		}
		if($extra['gift_product_name'] !='')
		{
			if($info !="")
			{
				$aa= $br;
			}
			else
			{
				$aa='';
			}
			$info.=$aa.'含赠品';
		}
	}
	return  $info;
}

//判断是否有样布
function panduan_sample($id,$come_from_id)
{
	$email = M('order_web')->where('`id` = '.$id)->getField('email');
	
	if($email)
	{
		$results = M('sample_record')->where('`email` ="'.$email.'" and `come_from_id`="'.$come_from_id.'" and `is_send`=0')->find();
		if($results)
		{
			$return = "有";	
		}
		else
		{
			$return = "";	
		}
	}
	else
	{
		$return = "";	
	}
	return  $results;
}
//判断付款方式
function payment_style($id)
{
	if($id)
	{
		$payment_style = M('order_web')->where('`id` ='.$id)->getField('payment_style');	
		switch ($payment_style)
		{
			case 'western union':
				$style = '西联付款';
				break;
			case 'bank transfer':
				$style = '银行转账';
				break;
			case 'cash on delivery':
				$style = '采单发送到日本乐天收货人处';
				break;			
		}
		return $style;
	}
	else
	{
		return '出现错误';
	}
}
//定制产品
/*
$description   Yes   输出description
*/
function product_customization($id,$description)
{
	if($id)
	{
		$customization = M('order_web_product_customization')->where('`order_web_id` ='.$id)->find();
		if($customization)
		{
			if($description =="Yes")
			{
				return $customization;				
			}
			else
			{
				return "含定制产品";	
			}
		}
	}
	else
	{
		return '出现错误';
	}
}
//同一人所买订单数量
function all_order_number($id,$style)
{
	if($style=='web')
	{
		$email = M('order_web')->where('`id`='.$id)->getField('email');
	}
	elseif($style=='plat')
	{
		$email = M('order_plat_form')->where('`id`='.$id)->getField('email');
	}
	if($email)
	{	
		$web_coun =0;
		$plat_coun =0;
		$web_list = M('order_web')->where('`email`="'.$email.'"')->select();
		foreach($web_list as $web_k=>$web_v)
		{
			$sta = M('order_web_status')->where('`order_web_id` = '.$web_v['id'])->getField('status');
			if($sta !='history')
			{
				$web_coun++;
			}
		}
		$plat_list = M('order_plat_form')->where('`email`="'.$email.'"')->select();
		foreach($plat_list as $plat_k=>$plat_v)
		{
			$sta = M('order_plat_form_status')->where('`order_platform_id` = '.$plat_v['id'])->getField('status');
			if($sta !='history')
			{
				$plat_coun++;
			}
			
		}
		$coun = $plat_coun + $web_coun ;
	}
	else
	{
		$coun = 1;
	}
	return $coun;
}
//是否又取消产品
function order_cancel($id,$style)
{
	if($style=='web')
	{
		$where['order_web_id'] = $id;
		$data = M('order_web_product');
		
	}
	elseif($style=='plat')
	{
		$where['order_platform_id'] = $id;
		$data = M('order_plat_form_product');
	}
	$where['status'] = 'cancel';
	$cancel =$data->where($where)->find();
	if($cancel)
	{	
		return '有取消订单';
	}
	else
	{
		return '';
	}
	/*if($style=='web')
	{
		$where['order_id'] = $id;
		$where_fac['order_id'] = $id;
	}
	elseif($style=='plat')
	{
		$where['order_platform_id'] = $id;
		$where_fac['order_platform_id'] = $id;
	}
	$fac_coun = 0;
	$fab_coun = 0;
	$bd_coun = 0;
	if($where)
	{
		$fac_order = M('factory_order')->where($where)->select();
		foreach($fac_order as $fac_k=>$fac_v)
		{
			$fac_cancel = M('factory_order_detail')->where('`factory_order_id` ='.$fac_v['id']." and `status` ='cancel'")->select();
			$fac_coun = $fac_coun + count($fac_cancel);
		}
		$fba_order = M('fba_order')->where($where_fac)->select();
		foreach($fba_order as $fba_k=>$fba_v)
		{
			$fba_cancel = M('fba_order_detail')->where('`fba_order_id` ='.$fba_v['id']." and `status` ='cancel'")->select();
			$fba_coun = $fba_coun + count($fba_cancel);
		}
		$bd_order = M('product_stock_order')->where($where)->select();
		foreach($bd_order as $bd_k=>$bd_v)
		{
			$bd_cancel = M('product_stock_order_detail')->where('`product_stock_order_id` ='.$bd_v['id']." and `status` ='cancel'")->select();
			$bd_coun = $bd_coun + count($bd_cancel);
		}
	}
	return $fac_coun+$fab_coun+$bd_coun;*/
}
//标记 恶意用户
//($order_id,$email,$first_name,$last_name,$ip=null,$phone=null)
function malicious_user($id)
{
	$bad_user_model = D('bad_user');
	//确定
	
	$web_info = M('order_web')->where('`id` ='.$id)->find();
	$web_address_info = M('order_web_address')->where('`order_web_id`='.$web_info['id'])->find();
	$order_web_supplement_info = M('order_web_supplement')->where('`order_web_id`='.$web_info['id'])->find();
	if($web_info)
	{
		$email = $web_info['email'];	
		$first_name = $web_info['first_name'];
		$last_name = $web_info['last_name'];
		if($web_address_info)
		{
			$phone = $web_address_info['telephone'];
		}
		if($order_web_supplement_info)
		{
			$ip = $order_web_supplement_info['custome_ip'];
		}
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
			$message = "恶意用户".$bad_user_row['remark'];
			return $message;
		}
		//可能
		$where = array('first_name'=>$first_name,'last_name'=>$last_name);
		$bad_user_row = $bad_user_model->where($where)->find();
		if($bad_user_row)
		{
			$message = "可能为恶意用户".$bad_user_row['remark'];
			return $message;
		}
		
	}
	else
	{
		return '恶意用户查询出现错误';	
	}
}


// username 转换name
function username_name($username)
{
	if($username)
	{
		$user_ex = explode(',',$username);
		foreach($user_ex as $k=>$v)
		{
			if($v)
			{
				$name[$k] = M('user')->where('`username` ="'.$v.'"')->getField('name');
				
				if($name[$k] == '')
				{
					$name[$k] = $v;
				}
			}
			else
			{
				$name[$k] ="";	
			}
		}
		$er_name = implode(',',$name);
	}
	else
	{
		$er_name='';
	}
	return $er_name;
}

//省州转换简写
function provinces_pithy($country,$city)
{
	$country_lower = strtolower($country);
	$city_lower = strtolower($city);
	$city_pithy = M('country_city') ->where('`country` = "'.$country_lower .'" and `city_full` ="'.$city_lower.'"')->getField('city');
	if(!$city_pithy)
	{
		$city_pithy = $city;
	}
	return $city_pithy;
}
//订单号 网站 全称
function order_number_full($id)
{
	$order = M('order_web')->field('order_number,customer_id,come_from_id,date_time')->where('`id` ='.$id)->find();
	
	//$come_from = strtoupper(get_come_from_name($order['come_from_id']));
	$time = date('Y0m0d',strtotime($order['date_time']));
	
	return $time."0".$order['customer_id']."0".$order['order_number'].' ';
}

/**
* 根据HTML代码获取word文档内容
* 创建一个本质为mht的文档，该函数会分析文件内容并从远程下载页面中的图片资源
* 该函数依赖于类WordMake
* 该函数会分析img标签，提取src的属性值。但是，src的属性值必须被引号包围，否则不能提取
*
* @param string $content HTML内容
* @param string $absolutePath 网页的绝对路径。如果HTML内容里的图片路径为相对路径，那么就需要填写这个参数，来让该函数自动填补成绝对路径。这个参数最后需要以/结束
* @param bool $isEraseLink 是否去掉HTML内容中的链接
*/
function WordMake($content , $absolutePath = "" , $isEraseLink = true )
{
	import("Org.Util.Wordmaker");
	$mht = new Wordmaker();
    if ($isEraseLink)
        $content = preg_replace('/<a\s*.*?\s*>(\s*.*?\s*)<\/a>/i' , '$1' , $content);   //去掉链接
    $images = array();
    $files = array();
    $matches = array();
    //这个算法要求src后的属性值必须使用引号括起来
    if ( preg_match_all('/<img[.\n]*?src\s*?=\s*?[\"\'](.*?)[\"\'](.*?)\/>/i',$content ,$matches ) )
    {
        $arrPath = $matches[1];
        for ( $i=0;$i<count($arrPath);$i++)
        {
            $path = $arrPath[$i];
            $imgPath = trim( $path );
            if ( $imgPath != "" )
            {
                $files[] = $imgPath;
                if( substr($imgPath,0,7) == 'http://')
                {
                    //绝对链接，不加前缀
                }
                else
                {
                    $imgPath = $absolutePath.$imgPath;
                }
                $images[] = $imgPath;
            }
        }
    }
    $mht->AddContents("tmp.html",$mht->GetMimeType("tmp.html"),$content);

    for ( $i=0;$i<count($images);$i++)
    {
        $image = $images[$i];
        if ( @fopen($image , 'r') )
        {
            $imgcontent = @file_get_contents( $image );
            if ( $content )
                $mht->AddContents($files[$i],$mht->GetMimeType($image),$imgcontent);
        }
        else
        {
            echo "file:".$image." not exist!<br />";
        }
    }

    return $mht->GetFile();
}
//关联工厂
/*
id       工厂单号 ID
data     工厂：fac FBA：fab 本地：bd   
oneself  是否把自己输出 1 输出  0不出
type     分割关联单符合   <br>  ,  \r\n  默认为 \r\n
web
sta     是否显示状态
*/
function associated_fac($id , $order_id , $data , $oneself , $type , $web , $sta)
{
	$factory_orderDB = M('factory_order');
	
	$fba_orderDB = M('fba_order');
	
	$product_stock_orderDB = M('product_stock_order');

	if($type)
	{
		$im = $type;
		if($type == 'rn')
		{
			$txt = " \r\n "." 关联工厂订单 "." \r\n ";
			$im = " \r\n ";
		}
		else
		{
			$txt = '';	
		}
	}
	else
	{
		$im = " \r\n ";
		$txt = " \r\n "." 关联工厂订单 "." \r\n ";
	}
	
	if($web =='web')
	{
		$mark = "";
		$whereID['order_id'] = $order_id;
	}
	elseif($web =='plat')
	{
		$mark = "W";
		$whereID['order_platform_id'] = $order_id;

	}
	$fac = $factory_orderDB  -> where($whereID)->select();
	
	foreach($fac  as $fac_k=>$fac_v)
	{
		
		switch($fac_v['factory_id'])
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
		}	
		
		if($fac_v['number'] < 10)
		{
			$num = '0' . $fac_v['number'];
		}
		else
		{
			$num = $fac_v['number'];
		}
		
		if($data ='fac')
		{
			if($fac_v['id'] == $id)
			{
				$me_order_num = $factory.$mark.date('m.d',strtotime($fac_v["date"])).'-'.$num;
				continue;	
			}
		}
		$fac_val[$fac_k] = $factory.$mark.date('m.d',strtotime($fac_v["date"])).'-'.$num;
		if($sta =='YES')
		{
			$fac_detail_sta = M('factory_order_detail')->where('`factory_order_id` ='.$fac_v['id'].' and `status` ="cancel"' )->find();
			if($fac_detail_sta)
			{
				$fac_val[$fac_k] .= '<span>(有取消)</span>';
			}
			if($fac_v['status'] =='history' || $fac_v['status'] =='history_ok' || $fac_v['factory_id']=='9')
			{
				$fac_val[$fac_k] .= ' <span class="icon-check" style="color:red;"></span>';
			}
			
		}
	}
	$aa = implode( $im ,$fac_val);
	
	//return $order_id;
	if($web =='web')
	{
		$fba = $fba_orderDB ->where($whereID)->select();
	}
	elseif($web =='plat')
	{
		$fba = $fba_orderDB ->where('`orderplatform_id` ='.$order_id)->select();
	}
	
	foreach($fba as $faa_k=>$fba_v)
	{
		
		if($fba_v['number'] < 10)
		{
			$num = '0' . $fba_v['number'];
		}
		else
		{
			$num = $fba_v['number'];
		}
		
		if($data ='fba')
		{
			if($fba_v['id'] == $id)
			{
				$me_order_num = "FBA".$mark.date('m.d',strtotime($fba_v["date"])).'-'.$num;
				continue;	
			}
		}
		
		$fba_val[$fba_k] = "FBA".$mark.date('m.d',strtotime($fba_v["date"])).'-'.$num;
		if($sta =='YES')
		{
			$fba_detail_sta = M('fba_order_detail')->where('`fba_order_id` ='.$fba_v['id'].' and `status` ="cancel"' )->find();
			if($fba_detail_sta)
			{
				$fba_val[$fba_k] .= '<span>(有取消)</span>';
			}
			
			if($fba_v['status'] =='history')
			{
				$fba_val[$fba_k].= ' <span class="icon-check" style="color:red;"></span>';
			}
		}
	}
	$bb=implode($im,$fba_val);
						
	
	$bd = $product_stock_orderDB  ->where($whereID)->select();
	foreach($bd as $bd_k=>$bd_v)
	{
		
		if($bd_v['number'] < 10)
		{
			$num = '0' . $bd_v['number'];
		}
		else
		{
			$num = $bd_v['number'];
		}
		
		if($data ='bd')
		{
			if($bd_v['id'] == $id)
			{
				$me_order_num = "K".$mark.date('m.d',strtotime($bd_v["date"])).'-'.$num;
				continue;	
			}
		}
		$bd_val[$bd_k] = "K".$mark.date('m.d',strtotime($bd_v["date"])).'-'.$num;
		if($sta =='YES')
		{
			$stock_detail_sta = M('product_stock_order_detail')->where('`product_stock_order_id` ='.$bd_v['id'].' and `status` ="cancel"' )->find();
			if($stock_detail_sta)
			{
				$bd_val[$bd_k].= '<span>(有取消)</span>';
			}
			
			if($bd_v['status'] =='history')
			{
				$bd_val[$bd_k].= ' <span class="icon-check" style="color:red;"></span>';
			}
		}
	}
	$cc=implode($im ,$bd_val);
	
	if($bb!='' && $aa!=''){$bbb = $im;}else{$bbb='';}
	if($cc!='' && $aa!=''){$bbb = $im;}else{$bbb='';}
	if($cc!='' && $bb!='' ){$ccc = $im;}else{$ccc='';}
	if($aa.$bbb.$bb.$ccc.$cc !="")
	{
		$newline = $txt;	
	}
	else
	{
		$newline ='';
	}
	if($oneself == 1)
	{
		$me_order_num = $me_order_num;
	}
	elseif($oneself == 0)
	{
		$me_order_num ='';
	}
	
	return $me_order_num.$newline.$aa.$bbb.$bb.$ccc.$cc;
	//return $id;
}

//数组排序
/*
$array    数组
$sort_key  排序的键值
$type      类型   如果是time类型  请写 time 如果不是请忽略


*/
function my_sort($arrays,$sort_key,$type,$sort_order=SORT_ASC,$sort_type=SORT_NUMERIC )
{   
	if(is_array($arrays))
	{   
		foreach ($arrays as $array)
		{   
			if(is_array($array))
			{ 
				if($type =='time')
				{
					$key_arrays[] = strtotime($array[$sort_key]);	
				} 
				else
				{ 
					$key_arrays[] = $array[$sort_key];
				}
			}
			else
			{   
				return false;   
			}   
		}   
	}
	else
	{   
		return false;   
	}  
	array_multisort($key_arrays,$sort_order,$sort_type,$arrays);   
	return $arrays;   
}  

//判断工厂过期时长
function factory_overdue_time($factory)
{
	switch($factory)
	{
		case 'hsf':
			$time = 60*60*36;
			break;
		case 'rb':
			$time = 60*60*36;
			break;
		case 'xlc':
			$time = 60*60*36;
			break;
		case 'zc':
			$time = 60*60*36;
			break;
		case 'xl':
			$time = 60*60*36;
			break;
		case 'tb':
			$time = 60*60*36;
			break;
		case 'ndz':
			$time = 60*60*36;
			break;
		case 'dz':
			$time = 60*60*36;
			break;
		case 'xdj':
			$time = 60*60*36;
			break;
		case 'B':
			$time = 60*60*36;
			break;
		default :
		 	$time = 60*60*36;
		    break;											
	}
	return $time;
}
//订单商家留言
//  style   web   plat
function message_seller($id,$style,$br)
{
	if($br)
	{
		$br =$br;	
	}
	else
	{
		$br =" \r\n ";	
	}
	if($style =='web')
	{
		$where['order_id'] = $id;
	}
	elseif($style =='plat')
	{
		$where['order_platform_id'] = $id;		
	}
	else
	{
		return '参数错误';
		break;	
	}
	$list = M('order_business_message')->where($where)->select();
	//dump($list);
	foreach($list as $mes_k=>$mes_v)
	{
		$info[$mes_k] = $mes_k+1 .".".$mes_v['message']." - ".date('m-d',$mes_v['date_time']).' - '.username_name($mes_v['operator']); //商家留言
	}
	$info_br = implode($br,$info);
	return $info_br;
}
//工厂名称转换
function fac_name($val)
{
	switch($val)
	{
		case 'hsf':
			$name ='杭丝坊/111/H';
			break;
		case 'rb':
			$name = '瑞帛/555/R';
			break;
		case 'xlc':
			$name = '兴隆工厂/999/L';
			break;
		case 'zc':
			$name = '自产/007/S ';
			break;
		case 'xl':
			$name = '芯类/888/Z ';
			break;
		case 'tb':
			$name = '淘宝工厂';
			break;
		case 'ndz':
			$name = '睡衣定制 ';
			break;
		case 'dz':
			$name = '所有定制 ';
			break;
		case 'B':
			$name = '222/B ';
			break;
		default :
		 	$name = '参数错误';
		    break;											
	}
	return $name;	
}