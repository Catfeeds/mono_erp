<?php
namespace Admin\Controller;
use Think\Controller;

class DeliveryNumberController extends CommonController
{  
	public function new_number()
	{
		$order_delivery_detailDB = M('order_delivery_detail');
		$order_webDB = M('order_web');
		$order_web_addressDB = M('order_web_address');
		$order_plat_formDB = M('order_plat_form');
		$order_delivery_parametersDB = M('order_delivery_parameters');
		$factory_orderDB = M('factory_order');
		$fba_orderDB = M('fba_order'); 
		$product_stock_orderDB = M('product_stock_order');
		
		$delivery_style = delivery_style();
		
		if(I('post.beginTime'))
		{
			$beginTime =I('post.beginTime');
			$endTime = I('post.endTime');
			$this->btime = $beginTime;
			$this->etime = $endTime;
			$endT = $endTime." 23:59:59";
			$where['time'] = array(array('egt',$beginTime),array('elt',$endT));
			
			foreach($delivery_style as $k=>$v)
			{
				if($v=='UPS货代')
				{
					$v_url[$k]='UPS_HD'	;
				}
				elseif($v == '顺丰')
				{
					$v_url[$k]='SF';	
				}
				elseif($v == 'Amazon Logistics')
				{
					$v_url[$k]='Amazon_Logistics';	
				}
				else
				{
					$v_url[$k] = $v;	
				}
				$style_url[$k]['url'] = '<a  href="'.__ACTION__.'/beginTime/'.$beginTime.'/endTime/'.$endTime.'/style/'.$v_url[$k].'".html> '. $v .' </a>';
				$style_url[$k]['val'] = $v_url[$k];
			}
			//dump($style);exit;
			
			$one_url = __ACTION__."/beginTime/".$beginTime."/endTime/".$endTime.".html";
			
			$web_url = __ACTION__."/beginTime/".$beginTime."/endTime/".$endTime."/data/web.html";
			$platform_url = __ACTION__."/beginTime/".$beginTime."/endTime/".$endTime."/data/plat.html";
			
			$data_url =__ACTION__."/beginTime/".$beginTime."/endTime/".$endTime.".html";
			
			$this-> web_url = $web_url;
			$this-> platform_url = $platform_url;
			$this->data_url = $data_url;
			
			$this-> one_url =$one_url;
		}
		elseif(I('get.beginTime'))
		{
			
			$beginTime =I('get.beginTime');
			$endTime = I('get.endTime');
			$this->btime = $beginTime;
			$this->etime = $endTime;
			$endT = $endTime." 23:59:59";
			$where['time'] = array(array('egt',$beginTime),array('elt',$endT));
			if(I('get.data'))//是否有网站
			{
				
				foreach($delivery_style as $k=>$v)
				{
					if($v=='UPS货代')
					{
						$v_url[$k]='UPS_HD'	;
					}
					elseif($v == '顺丰')
					{
						$v_url[$k]='SF';	
					}
					elseif($v == 'Amazon Logistics')
					{
						$v_url[$k]='Amazon_Logistics';	
					}
					else
					{
						$v_url[$k] = $v;	
					}
					$style_url[$k]['url'] = '<a  href="'.__ACTION__.'/beginTime/'.$beginTime.'/endTime/'.$endTime.'/style/'.$v_url[$k].'/data/'.I("get.data").'".html> '. $v .' </a>';
					$style_url[$k]['val'] = $v_url[$k];
				}
				
				$one_url = __ACTION__."/beginTime/".$beginTime."/endTime/".$endTime."/data/".I('get.data').".html";
				
			}
			else
			{
				foreach($delivery_style as $k=>$v)
				{
					if($v=='UPS货代')
					{
						$v_url[$k]='UPS_HD'	;
					}
					elseif($v == '顺丰')
					{
						$v_url[$k]='SF';	
					}
					elseif($v == 'Amazon Logistics')
					{
						$v_url[$k]='Amazon_Logistics';	
					}
					else
					{
						$v_url[$k] = $v;	
					}
					$style_url[$k]['url'] = '<a  href="'.__ACTION__.'/beginTime/'.$beginTime.'/endTime/'.$endTime.'/style/'.$v_url[$k].'".html> '. $v .' </a>';
					$style_url[$k]['val'] = $v_url[$k];
				}
				$one_url = __ACTION__."/beginTime/".$beginTime."/endTime/".$endTime.".html";
				
			}
			//是否有style
			if(I('get.style'))
			{
				$web_url = __ACTION__."/beginTime/".$beginTime."/endTime/".$endTime."/style/".I('get.style')."/data/web.html";
				$platform_url = __ACTION__."/beginTime/".$beginTime."/endTime/".$endTime."/style/".I('get.style')."/data/plat.html";
				$data_url =__ACTION__."/beginTime/".$beginTime."/endTime/".$endTime."/style/".I('get.style').".html";
			}
			else
			{
				$web_url = __ACTION__."/beginTime/".$beginTime."/endTime/".$endTime."/data/web.html";
				$platform_url = __ACTION__."/beginTime/".$beginTime."/endTime/".$endTime."/data/plat.html";	
				$data_url =__ACTION__."/beginTime/".$beginTime."/endTime/".$endTime.".html";
			}
			
			
			if(I('get.style'))
			{
				if(I('get.style') == 'UPS_HD')
				{
					$where['style'] = "UPS货代";
				}
				elseif(I('get.style') == 'SF')
				{
					$where['style'] = "顺丰";
				}
				elseif(I('get.style') == 'Amazon_Logistics')
				{
					$where['style'] = "Amazon Logistics";
				}
				else
				{
					$where['style'] = I('get.style');
				}
				$this->style = I('get.style');
			}
			if(I('get.data')=="web")
			{
				$where['order_web_id'] = array('neq',0);
			}
			else if(I('get.data')=="plat")
			{
				$where['order_platform_id'] = array('neq',0);
			}
				
				$this->data = I('get.data');
			
			$this-> one_url =$one_url;
			
			$this-> web_url = $web_url;
			$this-> platform_url = $platform_url;
			$this-> data_url = $data_url;
		}
		else
		{
			$this_time=date('Y-m-d H:i:s',strtotime(date('Y-m-d',time())));
			$where['time'] = array('EGT',$this_time);
			if(I('get.data'))
			{
				foreach($delivery_style as $k=>$v)
				{
					if($v=='UPS货代')
					{
						$v_url[$k]='UPS_HD'	;
					}
					elseif($v == '顺丰')
					{
						$v_url[$k]='SF';	
					}
					elseif($v == 'Amazon Logistics')
					{
						$v_url[$k]='Amazon_Logistics';	
					}
					else
					{
						$v_url[$k] = $v;	
					}
					
					$style_url[$k]['url'] = '<a  href="'.__ACTION__.'/style/'.$v_url[$k].'/data/'.I('get.data').'".html> '. $v .' </a>';
					$style_url[$k]['val'] = $v_url[$k];
				}
				
				$one_url = __ACTION__."/data/".I('get.data').".html";
			}
			else
			{
				foreach($delivery_style as $k=>$v)
				{
					if($v=='UPS货代')
					{
						$v_url[$k]='UPS_HD'	;
					}
					elseif($v == '顺丰')
					{
						$v_url[$k]='SF';	
					}
					elseif($v == 'Amazon Logistics')
					{
						$v_url[$k]='Amazon_Logistics';	
					}
					else
					{
						$v_url[$k] = $v;	
					}
					$style_url[$k]['url'] = '<a  href="'.__ACTION__.'/style/'.$v_url[$k].'".html> '. $v .' </a>';
					$style_url[$k]['val'] = $v_url[$k];
					
				}
				$one_url = __ACTION__.".html";
			}
			if(I('get.style'))
			{
				$web_url = __ACTION__."/style/".I('get.style')."/data/web.html";
				$platform_url = __ACTION__."/style/".I('get.style')."/data/plat.html";
				$data_url =__ACTION__."/style/".I('get.style').".html";
			}
			else
			{
				$web_url = __ACTION__."/data/web.html";
				$platform_url = __ACTION__."/data/plat.html";	
				$data_url =__ACTION__.".html";
			}
			
			
			if(I('get.style'))
			{
				if(I('get.style') == 'UPS_HD')
				{
					$where['style'] = "UPS货代";
				}
				elseif(I('get.style') == 'SF')
				{
					$where['style'] = "顺丰";
				}
				elseif(I('get.style') == 'Amazon_Logistics')
				{
					$where['style'] = "Amazon Logistics";
				}
				else
				{
					$where['style'] = I('get.style');
				}
				$this->style = I('get.style');
			}
			
			if(I('get.data')=="web")
			{
				$where['order_web_id'] = array('neq',0);
			}
			else if(I('get.data')=="plat")
			{
				$where['order_platform_id'] = array('neq',0);
			}
				
				$this->data = I('get.data');
			
			$this-> web_url = $web_url;
			$this-> platform_url = $platform_url;
			$this-> data_url = $data_url;
			
			$this-> one_url =$one_url;
		}
		//dump($style_url);exit;
		$this -> style_url = $style_url;
		
		$where['message'] = 'normal';
		
		$list_one = $order_delivery_detailDB->where($where)->group('order_platform_id,order_web_id')->select();
		
		$list_xdj = M('order_delivery_detail_xdj')->where($where)->group('order_platform_id,order_web_id')->select();
		
		$list_array = my_sort(array_merge($list_xdj,$list_one),'time','time'); 
		
		$list_coun = count($list_array);	
		$Page       = new \Think\Page1($list_coun);// 实例化分页类 传入总记录数
		$nowPage = isset($_GET['p'])?$_GET['p']:1;
        $show       = $Page->show();// 分页显示输出
		$iii =0;
		for($i=($nowPage-1)*C('LISTROWS');$i<$nowPage*C('LISTROWS');$i++)
		{
			if($list_array[$i])
			{
				$list[$iii]= $list_array[$i];
				$iii++;
			}
			
		}
		//重组数组 end
		
		foreach($list as $k=>$v)
		{
			if($v['order_platform_id']!=0)
			{
				$order_type = 'plat';
				$order_type_bz = 'W';
				$order =$order_plat_formDB->where('`id`='.$v['order_platform_id'])->group('order_number,email,name,telephone,date_time,come_from_id')->find();
				$come_from_name = D('id_come_from')->where(array('id'=>$order['come_from_id']))->getField('name');
				
				$info[$k]['order_number'] = $order['order_number'];
				$info[$k]['url']=__MODULE__.'/OrderManage/order_plat/order_status/all/order_number/'.$order['order_number'].'/come_from_id/'.$order['come_from_id'];
				$info[$k]['date_time'] = $order['date_time'];
				$info[$k]['name'] = $order['name'];
				$info[$k]['email'] = $order['email'];
				$info[$k]['phone'] = $order['telephone'];
				$info[$k]['come_from_name'] =  strtoupper($come_from_name);
				$price = $order_delivery_parametersDB->where('`order_platform_id` ='.$v['order_platform_id'])->find();
				if($price)
				{
					$info[$k]['weight'] = $price['weight'];
				}
				$info[$k]['order_platform_id'] = $v['order_platform_id'];
				$info[$k]['order_id'] = $v['order_web_id'];
			}
			elseif($v['order_web_id']!=0)
			{
				$order_type = 'web';
				$order_type_bz = '';
				$order =$order_webDB->where('`id`='.$v['order_web_id'])->group('order_number,email,first_name,last_name,date_time,come_from_id')->find();
				$come_from_name = D('id_come_from')->where(array('id'=>$order['come_from_id']))->getField('name');
				$info[$k]['order_number'] = $order['order_number'];
				$info[$k]['url']=__MODULE__.'/OrderManage/order_web/order_status/all/order_number/'.$order['order_number'].'/come_from_id/'.$order['come_from_id'];
				$info[$k]['date_time'] = $order['date_time'];
				$info[$k]['name'] = $order['first_name'].' '.$order['last_name'];
				$info[$k]['email'] = $order['email'];
				$info[$k]['come_from_name'] =  strtoupper($come_from_name);
				$phone = $order_web_addressDB->where('`order_web_id`='.$v['order_web_id'])->getField('telephone');
				$info[$k]['phone'] = $phone;
				$price = $order_delivery_parametersDB->where('`order_id` ='.$v['order_web_id'])->find();
				if($price)
				{
					$info[$k]['weight'] = $price['weight'];
				}
				$info[$k]['order_platform_id'] = $v['order_platform_id'];
				$info[$k]['order_id'] = $v['order_web_id'];
				
			}
			$info[$k]['time'] = $v['time'];
			
			if($bb!=''){$bbb = " , ";}else{$bbb='';}
			if($cc != '' &&  $aa.$bbb.$bb!=""){$ccc = " , ";}else{$ccc='';}
			
			$info[$k]['factory_num'] = $aa.$bbb.$bb.$ccc.$cc;
			$info[$k]['delivery_number'] = $v['delivery_number'];
			$info[$k]['style'] = $v['style'];
			$info[$k]['sign'] = $v['sign'];
		}
		$this->list_coun = $list_coun;
		$this->page = $show;
		$this-> assign ('info',$info);
		$this->tpltitle = "发货量管理";
		$this->display();	
	}
	//导出execl
	public function new_number_execl()
	{
		$order_delivery_detailDB = M('order_delivery_detail');
		$order_webDB = M('order_web');
		$order_web_addressDB = M('order_web_address');
		$order_plat_formDB = M('order_plat_form');
		$order_delivery_parametersDB = M('order_delivery_parameters');
		$factory_orderDB = M('factory_order');
		$fba_orderDB = M('fba_order'); 
		$product_stock_orderDB = M('product_stock_order');
		if(I('post.beginTime'))
		{
			$beginTime =I('post.beginTime');
			$endTime= I('post.endTime');
			$endT = $endTime." 23:59:59";
			$where['time'] = array(array('egt',$beginTime),array('elt',$endT));
		}
		else
		{
			$this_time=date('Y-m-d H:i:s',strtotime(date('Y-m-d',time())));
			$where['time'] = array('EGT',$this_time);
		}
		
		if(I('post.style'))
		{
			if(I('post.style'))
			{
				if(I('post.style') == 'UPS_HD')
				{
					$where['style'] = "UPS货代";
				}
				elseif(I('post.style') == 'SF')
				{
					$where['style'] = "顺丰";
				}
				elseif(I('post.style') == 'Amazon_Logistics')
				{
					$where['style'] = "Amazon Logistics";
				}
				else
				{
					$where['style'] = I('post.style');
				}
			}
		}
		if(I('post.data'))
		{
			if(I('post.data')=="web")
			{
				$where['order_web_id'] = array('neq',0);
			}
			else if(I('post.data')=="plat")
			{
				$where['order_platform_id'] = array('neq',0);
			}
		}
		
		$where['message'] = 'normal';
	//	dump($where);exit;
		//重组数组
		$list_one = $order_delivery_detailDB->where($where)->group('order_platform_id,order_web_id')->select();
		$list_xdj = M('order_delivery_detail_xdj')->where($where)->group('order_platform_id,order_web_id')->select();
		$list = my_sort(array_merge($list_xdj,$list_one),'time','time'); 
		//重组数组 end、
	//	dump($list);exit;
		if(I("get.act")=="screening")
		{
			foreach ($list as $k=>$v)
			{
				if($v['order_platform_id']!=0)
				{
					$order_type = 'plat';
					$order_type_bz = 'W';
					$order =$order_plat_formDB->where('`id`='.$v['order_platform_id'])->group('order_number,email,name,telephone,date_time,come_from_id')->find();
					$come_from_name = D('id_come_from')->where(array('id'=>$order['come_from_id']))->getField('name');
					$info[$k]['order_web_number'] = 0;
					$info[$k]['order_platfrom_number'] = $order['order_number'];
					$info[$k]['date_time'] = $order['date_time'];
					$info[$k]['name'] = $order['name'];
					$info[$k]['email'] = $order['email'];
					$info[$k]['phone'] = $order['telephone'];
					$address[$k] = M('order_plat_form_shipping') ->where('`order_platform_id` ='.$v['order_platform_id'])->find();
					$price = $order_delivery_parametersDB->where('`order_platform_id` ='.$v['order_platform_id'])->find();
					if($price)
					{
						$info[$k]['weight'] = $price['weight'];
					}
					else
					{
						$info[$k]['weight'] = '';
					}
					//工厂编码
					$fac_num = associated_fac('',$v['order_platform_id'],'',1,',','plat');
				}
				elseif($v['order_web_id']!=0)
				{
					$order_type = 'web';
					$order_type_bz = '';
					$order =$order_webDB->where('`id`='.$v['order_web_id'])->group('order_number,email,first_name,last_name,date_time,come_from_id')->find();
					$come_from_name = D('id_come_from')->where(array('id'=>$order['come_from_id']))->getField('name');
					$info[$k]['order_web_number'] =$order['order_number'];
					if($v['sign'])
					{
						$info[$k]['order_web_number'] .='-'.$v['sign'];
					}
					$info[$k]['order_platfrom_number'] = 0;
					$info[$k]['date_time'] = $order['date_time'];
					$info[$k]['name'] = $order['first_name'].' '.$order['last_name'];
					$info[$k]['email'] = $order['email'];
					$address[$k] = $order_web_addressDB->where('`order_web_id`='.$v['order_web_id'])->find();
					if($address[$k])
					{
						$info[$k]['phone'] = $address[$k]['telephone'];
					}
					else
					{
						$info[$k]['phone'] ='';
					}
					$price = $order_delivery_parametersDB->where('`order_id` ='.$v['order_web_id'])->find();
					if($price)
					{
						$info[$k]['weight'] = $price['weight'];
					}
					//工厂编码
					$fac_num = associated_fac('',$v['order_web_id'],'',1,',','web');
				}
				$info[$k]['time'] = $v['time'];
				
				$info[$k]['factory_num'] = $fac_num;
					
				$info[$k]['delivery_number'] = $v['delivery_number'].' ';
				$info[$k]['style'] = $v['style'];
				$info[$k]['country'] = $address[$k]['country'];
			}
			$info_web =0;
			$info_plat =0;
			foreach($info as $info_k=>$info_v)
			{
				if($info_v['order_web_number'] !='0')
				{
					$info_web_array[$info_web] = $info_v;
					$info_web++;
				}
				else
				{
					$info_plat_array[$info_plat] = $info_v;
					$info_plat++;
				}
			}
			$info_web_array_sort = my_sort($info_web_array,'order_web_number');
			$info_plat_array_sort =  my_sort($info_plat_array,'order_platfrom_number');
			
			$info_all = array_merge($info_web_array_sort,$info_plat_array_sort);
			//dump($info_all);exit;
			$title=array('网站订单号','平台订单号','订单日期','收件人','email','电话','重量','发货时间','工厂订单编号','跟单号','快递公司','目的地国家');
			exportExcel($info_all,'发货报表',$title);
		}
		elseif(I("get.act")=="address")
		{
			foreach ($list as $k=>$v)
			{
				if($v['order_platform_id']!=0)
				{
					$info[$k]['factory_num'] =  associated_fac('',$v['order_platform_id'],'',1,',','plat');;
					$order_plat_shipping=M("order_plat_form_shipping")->where('`order_platform_id`='.$v['order_platform_id'])->find();
					$order =$order_plat_formDB->where('`id`='.$v['order_platform_id'])->getField("order_number");
					$info[$k]['order_number'] = $order;
					$info[$k]['name'] = $order_plat_shipping["name"];
					$info[$k]['company'] = $order_plat_shipping["name"];
					$info[$k]['address'] = $order_plat_shipping["address1"].$order_plat_shipping["address2"].$order_plat_shipping["address3"];
					$info[$k]['city'] = $order_plat_shipping["city"];
					$info[$k]['state'] = $order_plat_shipping["state"];
					$info[$k]['post'] = $order_plat_shipping["post"];
					$info[$k]['second_word'] = "";
					$info[$k]['telephone'] = $order_plat_shipping["telephone"];
					$info[$k]['detailed_address'] = $order_plat_shipping["address1"].$order_plat_shipping["address2"].$order_plat_shipping["address3"]."\n".$order_plat_shipping["city"]."\n".$order_plat_shipping["state"]."\n".$order_plat_shipping["post"]."\n".$order_plat_shipping["country"]."\n".$order_plat_shipping["telephone"];
				}
				elseif($v['order_web_id']!=0)
				{
					$info[$k]['factory_num'] = associated_fac('',$v['order_web_id'],'',1,',','web');;
					
					$order =$order_webDB->where('`id`='.$v['order_web_id'])->group('order_number')->find();
					$info[$k]['order_number'] = $order['order_number'];
					if($v['sign'])
					{
						$info[$k]['order_number'] .=' -'.$v['sign'];
					}
					$order_web_address = $order_web_addressDB->where('`order_web_id`='.$v['order_web_id'])->find();
						
					$info[$k]['name'] = $order_web_address["first_name"].$order_web_address["last_name"];
					$info[$k]['company'] = $order_web_address["first_name"].$order_web_address["last_name"];
					$info[$k]['address'] = $order_web_address["address"];
					$info[$k]['city'] = $order_web_address["city"];
					$info[$k]['state'] = $order_web_address["province"];
					$info[$k]['post'] = $order_web_address["code"];
					$info[$k]['second_word'] = "";
					$info[$k]['telephone'] = $order_web_address["telephone"];
					$info[$k]['detailed_address'] = $order_web_address["address"]."\n".$order_web_address["city"]."\n".$order_web_address["province"]."\n".$order_web_address["code"]."\n".$order_web_address["country"]."\n".$order_web_address["telephone"];	
				}
			}
			$title=array('工厂单号','订单号','收件人','收件公司','地址','收件城市','收件人州省','收件人邮编','二字码','收件电话','全部地址');
			exportExcel($info,'发货订单地址'.$execl_time,$title);
		}
	}
	//单号跟踪
	public function number_update()
	{
		$order_delivery_detailDB = M('order_delivery_detail');
		$order_webDB = M('order_web');
		$order_web_addressDB = M('order_web_address');
		$order_plat_formDB = M('order_plat_form');
		$order_delivery_parametersDB = M('order_delivery_parameters');
		$factory_orderDB = M('factory_order');
		$fba_orderDB = M('fba_order'); 
		$product_stock_orderDB = M('product_stock_order');
		
		if(I('post.delivery_number') || I('post.order_num'))  //精确查找
		{
			$delivery_number = I('post.delivery_number');
			$order_num = I('post.order_num');
			if($order_num)
			{
				$order_web_id = $order_webDB->where('`order_number` ="'.$order_num.'"')->getField('id');
				if($order_web_id)
				{
					$where['order_web_id'] = $order_web_id;
				}
				else
				{
					$order_platform_id = $order_plat_formDB ->where('`order_number` ="'.$order_num.'"')->getField('id');
					$where['order_platform_id'] = $order_platform_id;
				}
				$this->order_num = $order_num;
			}
			else
			{
				$where['delivery_number'] =$delivery_number;
				$this->delivery_number = $delivery_number;
			}
		}
		elseif(I('get.beginTime') || I('get.delivery_style'))     //时间区间筛选
		{	
			if(I('get.beginTime') && I('get.endTime'))
			{
				$beginTime =I('get.beginTime');
				$endTime = I('get.endTime');
				$eTime =$endTime." 23:59:59"; 
				$this->btime = $beginTime;
				$this->etime = $endTime;
				$where['time'] = array(array('egt',$beginTime),array('elt',$eTime));
			}
			if(I('get.delivery_style'))
			{
				if(I('get.delivery_style') == 'UPS_HD')
				{
					$where['style'] = 'UPS货代';
					$this->delivery_style = 'UPS货代';
				}
				elseif(I('get.delivery_style') == 'Amazon%20Logistics')
				{
					$where['style'] = 'Amazon Logistics';
					$this->delivery_style = 'Amazon Logistics';
				}
				else
				{
					$where['style'] = I('get.delivery_style');
					$this->delivery_style = I('get.delivery_style');
				}
				
			}
			elseif(I('post.delivery_style'))
			{
				
				if(I('post.delivery_style') == 'UPS_HD')
				{
					$where['style'] = 'UPS货代';
					$this->delivery_style = 'UPS货代';
				}
				elseif(I('post.delivery_style') == 'Amazon%20Logistics')
				{
					$where['style'] = 'Amazon Logistics';
					$this->delivery_style = 'Amazon Logistics';
				}
				else
				{
					$where['style'] = I('post.delivery_style');
					$this->delivery_style = I('post.delivery_style');
				}
				
			}
			
		}
		else
		{
			$this_time=date('Y-m-d H:i:s',strtotime(date('Y-m-d',time())));
			$where['time'] = array('EGT',$this_time);
		}
		$where['message'] = 'normal';
		//dump($where);exit;
		$list_one = $order_delivery_detailDB->where($where)->field('delivery_number,style')->group('order_platform_id,order_web_id')->order('time desc')->select();
		//dump($list_one);exit;
		$nnn= 0 ;
		foreach($list_one as $one_k=>$one_v)
		{
			$one_where['style'] = $one_v['style'];
			$one_where['delivery_number'] = $one_v['delivery_number'];
			//$one_where['message'] = 'normal';
			$list_two[$one_k] = $order_delivery_detailDB ->where($one_where)->order('time desc')->find();	
			
			if(I('get.rule') == 1)
			{
				if($list_two[$one_k]['status'] !="已签收")
				{
					$list[$nnn] = $list_two[$one_k];
					$nnn++;
				}			
			}
			elseif(I('get.status') == "yc")
			{
				if($list_two[$one_k]['status'] =="寄送出现问题" || $list_two[$one_k]['status'] =="退回" || $list_two[$one_k]['status'] =="已退签")
				{
					$list[$nnn] = $list_two[$one_k];
					$nnn++;
				}
			}
			else
			{
				$list[$nnn] = $list_two[$one_k];
				$nnn++;
			}
			
					
		}	
		//dump($list);exit;
		foreach($list as $k=>$v)
		{
			$info[$k]['id']=$v['id'];
			if($v['order_platform_id']!=0)
			{
				$order =$order_plat_formDB->where('`id`='.$v['order_platform_id'])->group('order_number,email,name,telephone,date_time,come_from_id')->find();
				$info[$k]['order_number'] = $order['order_number'];
				$come_from_name = D('id_come_from')->where(array('id'=>$order['come_from_id']))->getField('name');
				$info[$k]['come_from_name'] = strtoupper($come_from_name);
				
				$info[$k]['url']=__MODULE__.'/OrderManage/order_plat/order_status/all/order_number/'.$order['order_number'].'/come_from_id/'.$order['come_from_id'];
				
				$info[$k]['date_time'] = $order['date_time'];
				$info[$k]['currency'] = $order['currency'];
			}
			elseif($v['order_web_id']!=0)
			{
				$order =$order_webDB->where('`id`='.$v['order_web_id'])->group('order_number,email,first_name,last_name,date_time,come_from_id')->find();
				$info[$k]['order_number'] = $order['order_number'];
				$come_from_name = D('id_come_from')->where(array('id'=>$order['come_from_id']))->getField('name');
				$info[$k]['come_from_name'] = strtoupper($come_from_name);
				
				$info[$k]['url']=__MODULE__.'/OrderManage/order_web/order_status/all/order_number/'.$order['order_number'].'/come_from_id/'.$order['come_from_id'];
				
				$info[$k]['date_time'] = $order['date_time'];
				$country = $order_web_addressDB->where('`order_web_id`='.$v['order_web_id'])->getField('country');
				$info[$k]['country'] = $country;
				
			}
			$where[$k]['message'] = 'normal';
			$where[$k]['delivery_number'] = $v['delivery_number'];
			$where[$k]['style'] = $v['style'];
			$info[$k]['time'] = $order_delivery_detailDB->where($where[$k])->getField('time');
			$info[$k]['delivery_number'] = $v['delivery_number'];
			$info[$k]['style'] = $v['style'];
			if($v['status'] =="寄送出现问题" || $v['status'] =="已退签" || $v['status'] =="退回")
			{
				$info[$k]['sta'] = "<span style=' color:red; font-weight: bold;'>".$v['status'] .'('.$v['message'].' '.$v['time'].')</span>';
			}
			else
			{
				$info[$k]['sta'] = $v['status'] .'('.$v['message'].' <span style="color:#000;">'.$v['time'].'</span>)';
			}
		}
	//	dump($info);exit;
		$style = $order_delivery_detailDB->where('`style` !=""')->field('style')->group('style')->select();
		$this->style = $style;	
		$this->list_coun = $nnn;
		$this->page = $show;
		$this-> assign ('info',$info);		
		$this->tpltitle = "跟单号管理";
		$this->display();
	}
	
	//更新选中物流信息
	public function select_update()
	{
		layout(false); // 临时关闭当前模板的布局功能
		$order_plat_formDB = M('order_plat_form');
		$order_webDB = M('order_web');
		$order_delivery_detailDB=M('order_delivery_detail');
		
		$error_num=0;		//更新错误量
		$return_sta_num=0;  //更新数据状态量
		$return_num=0;      //更新数据量
		$over_num = 0; 		//快递已完成量
		
		$AppKey=C('AppKey');         //在http://kuaidi100.com/app/reg.html申请到的KEY
		
		$id = I('post.check');
		if($id)
		{
			$where['id'] = array('in',implode(',',$id));
		}
		else
		{
			$this->error('没有选择');	
		}
		$delivery_number=$order_delivery_detailDB->field('delivery_number,style,order_platform_id,time')->where($where)->select();
		
		
		foreach($delivery_number as $k=>$v){
			//判断状态 
			$logistics_sta=$order_delivery_detailDB->where('`style` = '.'"'.$v['style'].'"'.' and `delivery_number` ='.'"'.$v['delivery_number'].'"')->order('time desc')->getField('status');
			if($logistics_sta =="已签收" && $logistics_sta =="已退签")
			{	
				$over_num++;				
			}
			else
			{
				$url ='http://api.kuaidi100.com/api?id='.$AppKey.'&com='.$v['style'].'&nu='.$v['delivery_number'].'&show=0&muti=1&order=asc'; /*show  0：返回json字符串，1：返回xml对象，2：返回html对象，3：返回text文本。如果不填，默认返回json字符串。*/			
				//请勿删除变量$powered 的信息，否者本站将不再为你提供快递接口服务。
				$powered = '查询数据由：<a href="http://kuaidi100.com" target="_blank">KuaiDi100.Com （快递100）</a> 网站提供 ';
				//优先使用curl模式发送数据
			   $curl = curl_init();
			   curl_setopt ($curl, CURLOPT_URL, $url);
			   curl_setopt ($curl, CURLOPT_HEADER,0);
			   curl_setopt ($curl, CURLOPT_RETURNTRANSFER, 1);
			   curl_setopt ($curl, CURLOPT_USERAGENT,$_SERVER['HTTP_USER_AGENT']);
			   curl_setopt ($curl, CURLOPT_TIMEOUT,5);
			   $get_content = curl_exec($curl);
			   curl_close ($curl);
				
				$aa=json_decode($get_content,true);           //json转换数组
				
				$courier_val=$aa['data'];                     //获得快递信息
				if($courier_val){
					$order=$order_delivery_detailDB->where('`delivery_number` ="'.$v['delivery_number'].'"')->find();       // 物流信息 获得订单表ID
					if($order){
						foreach($courier_val as $key=>$val){
							$info[$key]=$order_delivery_detailDB->where('`message` = '.'"'.$val['context'].'"'.'and `delivery_number` ="'.$v['delivery_number'].'" and `time` ='.'"'.$val[time].'"')->find();
							if(!$info[$key])
							{
								$date['order_platform_id'] = $order['order_platform_id'];
								$date['order_web_id'] = $order['order_web_id'];
								$date['style']=$v['style'];
								$date['delivery_number']=$v['delivery_number'];
								$date['message'] = $val['context'];
								$date['time'] = $val['time'];
								$return=$order_delivery_detailDB->add($date);
								if($return)
								{
									$return_num++;
								}
							}
						}
						
						//快递状态
						if(isset($aa['state'])){
							$info=$order_delivery_detailDB->where('`delivery_number` ="'.$v['delivery_number'].'"')->order('time desc')->find();
							if(logistics_status($aa['state']) != $info['status'])
							{
								$whereID='`id` ='.$info['id'];
								$val['status'] = logistics_status($aa['state']);
								$return_sta=$order_delivery_detailDB->where($whereID)->save($val);
								if($return_sta)
								{
									$return_sta_num++;
								}
							}
						}
					}
				}
				else
				{
					$error[$error_num]['message']=$aa['message'];
					
					if($v['order_platform_id']!=0)
					{
						$order =$order_plat_formDB->where('`id`='.$v['order_platform_id'])->field('order_number')->find();
						$error[$error_num]['order_number'] = $order['order_number'];
					}
					elseif($v['order_web_id']!=0)
					{
						$order =$order_webDB->where('`id`='.$v['order_web_id'])->field('order_number')->find();
						$error[$error_num]['order_number'] = $order['order_number'];
					}
					$error_num++;
				}
			}
		}
		if($_POST['check'])
		{
			echo '	已经更新  <span style="color:red; fone-size:16px;font-weight:bold;">' . $return_num . '</span>  条数据！<br/> 
				更新  <span style="color:red;fone-size:16px;font-weight:bold;">' . $return_sta_num . '</span>  条物流状态<br>
				<span style="color:red;fone-size:16px;font-weight:bold;">' . $over_num . '</span>  条物流已结束<br>
				<span style="color:red;fone-size:16px;font-weight:bold;">' . $error_num . '</span>  条物流信息获取错误 ：
				<br>';
				foreach($error as $kk=>$vv)
				{ 
				echo '<span style="color:red;fone-size:16px;font-weight:bold;">'.$vv['order_id']."</span> : ".$vv['message']."<br>";
				}
				echo '(此处为错误订单ID)';
				echo '<span class="plcz"><a href="javascript:history.go(-1)" class="href_button">返回</a></span>';
		}
		$this->display();		
	}
	public function import_order()
	{
		$rootPath = './Public/import_order/';
		if(!file_exists($rootPath)) mkdir($rootPath);
		$upload = new \Think\Upload();// 实例化上传类
		$upload->maxSize   =     0 ;// 设置附件上传大小
		$upload->exts      =     array('xlsx','xls');// 设置附件上传类型
		$upload->rootPath  =     $rootPath; // 设置附件上传根目录
		$upload->savePath  =     ''; // 设置附件上传（子）目录
		$upload->autoSub   =     false;//设置文件子目录名
		// 上传文件
		$info = $upload->upload();
		if(!$info) 
		{
			// 上传错误提示错误信息
			$this->error($upload->getError());
		}
		else
		{
			// 上传成功
			vendor('PHPExcel.SimplePHPExcel');
			$callback = function ($row,$obj)
			{
				if($row[A]!=0)
				{
					$order_web_id = D('order_web')->where(array('order_number'=>$row[A]))->getField("id");
					$order_platform_id = 0;
				}
				else if($row[B]!=0)
				{
					$order_web_id = 0;
					$order_platform_id = D('order_plat_form')->where(array('order_number'=>$row[B]))->getField("id");
				}
				/*
				if($row[A]!=0 || $row[B]!=0)
				{
					$data['order_web_id'] = $order_web_id;
					$data['order_platform_id'] = $order_platform_id;
					$parameters_data['order_id'] = $order_web_id;
					$parameters_data['order_platform_id'] = $order_platform_id;
					$data['style'] = $row[K];
					$data['delivery_number'] = $row[J];
					$data['message'] = 'normal';
					$data['status'] = 'normal';
					$parameters_data['shipping_style'] = $row[K];
					$parameters_data['weight'] = $row[G];
					$parameters_data['price'] = 0;
					$parameters_data['hs'] = '';
					$parameters_data['name'] = '';
					$parameters_data['operator'] = $_SESSION["username"];
					if($row[H]!='')
					{
						$data['time'] = $row[H];
						$parameters_data['date_time'] = strtotime($row[H]);
					}
					else
					{
						$data['time'] = date('y-m-d h:i:s',time());
						$parameters_data['date_time'] = time();
					}
					
					$insert = D('order_delivery_detail')->add($data);
					
					$parameters_insert = D('order_delivery_parameters')->add($parameters_data);
				}
				*/
				if($row[A]!=0 || $row[B]!=0)
				{
					if($order_web_id!=0)
					{
						$order_delivery_detail_list = D('order_delivery_detail')->where(array('order_web_id'=>$order_web_id))->select();
						$order_delivery_parameters_list = D('order_delivery_parameters')->where(array('order_id'=>$order_web_id))->select();
						if($order_delivery_detail_list)
						{
							$data['order_platform_id'] = 0;
							$data['style'] = $row[K];
							$data['delivery_number'] = $row[J];
							$data['message'] = 'normal';
							$data['status'] = 'normal';
							if($row[H]!='')
							{
								$data['time'] = $row[H];
							}
							else
							{
								$data['time'] = date('y-m-d h:i:s',time());
							}
							D('order_delivery_detail')->where(array('order_web_id'=>$order_web_id))->save($data);
						}
						else 
						{
							$data['order_web_id'] = $order_web_id;
							$data['order_platform_id'] = 0;
							$data['style'] = $row[K];
							$data['delivery_number'] = $row[J];
							$data['message'] = 'normal';
							$data['status'] = 'normal';
							if($row[H]!='')
							{
								$data['time'] = $row[H];
							}
							else
							{
								$data['time'] = date('y-m-d h:i:s',time());
							}
							D('order_delivery_detail')->add($data);
						}
						if($order_delivery_parameters_list)
						{
							$parameters_data['order_platform_id'] = $order_platform_id;
							$parameters_data['shipping_style'] = $row[K];
							$parameters_data['weight'] = $row[G];
							$parameters_data['price'] = 0;
							$parameters_data['hs'] = '';
							$parameters_data['name'] = '';
							$parameters_data['operator'] = $_SESSION["username"];
							if($row[H]!='')
							{
								$parameters_data['date_time'] = strtotime($row[H]);
							}
							else
							{
								$parameters_data['date_time'] = time();
							}
							D('order_delivery_parameters')->where(array('order_id'=>$order_web_id))->save($parameters_data);
						}
						else 
						{
							$parameters_data['order_id'] = $order_web_id;
							$parameters_data['order_platform_id'] = $order_platform_id;
							$parameters_data['shipping_style'] = $row[K];
							$parameters_data['weight'] = $row[G];
							$parameters_data['price'] = 0;
							$parameters_data['hs'] = '';
							$parameters_data['name'] = '';
							$parameters_data['operator'] = $_SESSION["username"];
							if($row[H]!='')
							{
								$parameters_data['date_time'] = strtotime($row[H]);
							}
							else
							{
								$parameters_data['date_time'] = time();
							}
							D('order_delivery_parameters')->add($parameters_data);
						}
					}
					else if($order_platform_id!=0)
					{
						$order_delivery_detail_list = D('order_delivery_detail')->where(array('order_platform_id'=>$order_platform_id))->select();
						$order_delivery_parameters_list = D('order_delivery_parameters')->where(array('order_platform_id'=>$order_platform_id))->select();
						if($order_delivery_detail_list)
						{
							$data['order_web_id'] = 0;
							$data['style'] = $row[K];
							$data['delivery_number'] = $row[J];
							$data['message'] = 'normal';
							$data['status'] = 'normal';
							if($row[H]!='')
							{
								$data['time'] = $row[H];
							}
							else
							{
								$data['time'] = date('y-m-d h:i:s',time());
							}
							D('order_delivery_detail')->where(array('order_platform_id'=>$order_platform_id))->save($data);
						}
						else
						{
							$data['order_web_id'] = 0;
							$data['order_platform_id'] = $order_platform_id;
							$data['style'] = $row[K];
							$data['delivery_number'] = $row[J];
							$data['message'] = 'normal';
							$data['status'] = 'normal';
							if($row[H]!='')
							{
								$data['time'] = $row[H];
							}
							else
							{
								$data['time'] = date('y-m-d h:i:s',time());
							}
							D('order_delivery_detail')->add($data);
						}
						if($order_delivery_parameters_list)
						{
							$parameters_data['order_web_id'] = 0;
							$parameters_data['shipping_style'] = $row[K];
							$parameters_data['weight'] = $row[G];
							$parameters_data['price'] = 0;
							$parameters_data['hs'] = '';
							$parameters_data['name'] = '';
							$parameters_data['operator'] = $_SESSION["username"];
							if($row[H]!='')
							{
								$parameters_data['date_time'] = strtotime($row[H]);
							}
							else
							{
								$parameters_data['date_time'] = time();
							}
							D('order_delivery_parameters')->where(array('order_platform_id'=>$order_platform_id))->save($parameters_data);
						}
						else
						{
							$parameters_data['order_id'] = 0;
							$parameters_data['order_platform_id'] = $order_platform_id;
							$parameters_data['shipping_style'] = $row[K];
							$parameters_data['weight'] = $row[G];
							$parameters_data['price'] = 0;
							$parameters_data['hs'] = '';
							$parameters_data['name'] = '';
							$parameters_data['operator'] = $_SESSION["username"];
							if($row[H]!='')
							{
								$parameters_data['date_time'] = strtotime($row[H]);
							}
							else
							{
								$parameters_data['date_time'] = time();
							}
							D('order_delivery_parameters')->add($parameters_data);
						}
					}
				}
				
			};
			
			$option=array(
				'uploadfile'=> $rootPath.$info['file']['savename'], //必须。该文件名不能含中文
				// 							'table'		=> 'order_plat_form', //必须
				// 							'field'		=> $field, //单表需要(option无配置callback)
				// 							'mode'		=> 'add', //添加模式
				// 							'mode'		=> 'edit',
				// 							'primary'	=> 'A',//修改模式，需要设置主键
				'obj' 		=> $this,
				"start_row"	=> 2,//可选。设置有效内容的 起始行数，默认2
				"unlink"	=> false,//可选。是否删除文件，默认false
				"callback"	=> $callback,//用于插入多表的情况
			);
			$log = importExcel($option);
			if($log)
			{
				$this->success('批量导入成功！',U('/Admin/DeliveryNumber/new_number'));
			}
			else
			{
				$this->error("批量导入失败！");
			}
		}
	}
}