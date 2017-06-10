<?php
namespace Admin\Controller;
use Think\Controller;

class StatementManageController extends CommonController
{  
	//已发货报表
	public function delivered_list()
	{
		$order_delivery_detailDB=M('order_delivery_detail');
		$order_plat_form_statusDB = M('order_plat_form_status');
		$order_web_statusDB = M('order_web_status');
		$OrderPlatFormModel = D('OrderPlatForm');
		$OrderWebModel = D('OrderWeb');
		
		$data = I('get.data');
		$data01 = I('post.data');
		
		if( $data =='plat' || $data01 =="plat")
		{
			//运单信息表里的 订单ID
			$order_delivery_detail_order = $order_delivery_detailDB->field('order_platform_id')->where('`order_platform_id` != 0 and `order_platform_id`!=""')->group('order_platform_id')->select();
			
			foreach($order_delivery_detail_order as $k=>$v)
			{
				$delivery_order_id[$k] = $v['order_platform_id'];
			}
			$order_delivery_id_list = implode(',',$delivery_order_id);  //获得运单信息表里面的 order_id	
			//运单信息表里的 订单ID          end
			//状态表里的 订单ID   筛选判断
				$map['order_platform_id'] = array('in',$order_delivery_id_list);
				$map['status'] = 'history';
			if(I('post.beginTime') && I('post.endTime'))
			{
				$begintime = strtotime(I('post.beginTime'));
				$endtime = strtotime(I('post.endTime')." 23:59:59");
				
 				$this->beginTime =I('post.beginTime');
				$this->endTime =I('post.endTime');
				
				$map['date_time'] = array(array('gt',$begintime),array('lt',$endtime));
			}
			
			$order_status = $order_plat_form_statusDB->field('order_platform_id')->where($map)->group('order_platform_id')->select();
			//dump($order_status);exit;
			//状态表里的 订单ID + 时间       end
			if($order_status)
			{
				foreach($order_status as $k=>$v)
				{
					$order_id[$k] = $v['order_platform_id'];
				}
				$order_id_list = implode(',',$order_id);
				$whereId['id'] = array('in',$order_id_list);  
				
				//import('Org.Util.Page');// 导入分页类
				$coun=$OrderPlatFormModel->where($whereId)->count();
				$Page       = new \Think\Page1($coun);// 实例化分页类 传入总记录数
				$nowPage = isset($_GET['p'])?$_GET['p']:1;
				$show       = $Page->show();// 分页显示输出
				
				$list = $OrderPlatFormModel->relation(true)->where($whereId)->page($nowPage , C('LISTROWS'))->select();
				foreach($list as $k=>$v)
				{
					$info[$k]['order_number'] = $v['order_number'];
					$info[$k]['name'] = $v['name'];
					$info[$k]['price'] =  $v['price'];
					$info[$k]['come_from'] =  get_come_from_name($v['come_from_id']);
					$info[$k]['delivery_number'] =  $v['detail_info']['delivery_number'];
					$info[$k]['style'] =  $v['detail_info']['style'];
					$info[$k]['weight'] =  $v['delivery_info']['weight'];
					$info[$k]['courier_price'] =  $v['delivery_info']['price'];
					$info[$k]['date_time'] =  date("Y-m-d H:i:s",$v['status_info']['date_time']);
				}
				
			}
			$this->info =$info;
			$this->page = $show;
			if($data)
			{
				$this->data = $data;	
			}
			else
			{
				$this->data = $data01;
			}
			
		}
		else
		{
			//运单信息表里的 订单ID
			$order_delivery_detail_order = $order_delivery_detailDB->field('order_web_id')->where('`order_web_id` != 0 and `order_web_id`!=""')->group('order_web_id')->select();
			$this->data = 'web';
			foreach($order_delivery_detail_order as $k=>$v)
			{
				$delivery_order_id[$k] = $v['order_web_id'];
			}
			$order_delivery_id_list = implode(',',$delivery_order_id);  //获得运单信息表里面的 order_id	
			//运单信息表里的 订单ID          end
			//状态表里的 订单ID   筛选判断
		//	dump($order_delivery_id_list);exit;
			
				$map['order_web_id'] = array('in',$order_delivery_id_list);
				$map['status'] = 'history';
			if(I('post.beginTime') && I('post.endTime'))
			{
				$begintime = strtotime(I('post.beginTime'));
				$endtime = strtotime(I('post.endTime')." 23:59:59");
				
				$this->beginTime =I('post.beginTime');
				$this->endTime =I('post.endTime');
				
				$map['date_time'] = array(array('egt',$begintime),array('elt',$endtime));
			}
			
			$order_status = $order_web_statusDB->field('order_web_id')->where($map)->group('order_web_id')->select();
			
			//dump($order_status);exit;
			//状态表里的 订单ID + 时间       end
			if($order_status)
			{
				foreach($order_status as $k=>$v)
				{
					$order_id[$k] = $v['order_web_id'];
				}
				$order_id_list = implode(',',$order_id);
				$where['id'] = array('in',$order_id_list);  
				//import('Org.Util.Page');// 导入分页类
				$coun=$OrderWebModel->where($where)->count();
				$Page       = new \Think\Page1($coun);// 实例化分页类 传入总记录数
				$nowPage = isset($_GET['p'])?$_GET['p']:1;
				$show       = $Page->show();// 分页显示输出
				$list = $OrderWebModel->relation(true)->where($where)->page($nowPage , C('LISTROWS'))->select();
				foreach($list as $k=>$v)
				{
					$info[$k]['order_number'] = $v['order_number'];
					$info[$k]['name'] = $v['order_web_address'][0]['first_name']." ".$v['order_web_address'][0]['last_name'];
					foreach($v['order_web_product'] as $vv)
					{
						$price +=$vv['price'];
					}
					$info[$k]['price'] = $price;
					$info[$k]['come_from'] =  get_come_from_name($v['come_from_id']);
					$info[$k]['delivery_number'] =  $v['detail_info']['delivery_number'];
					$info[$k]['style'] =  $v['detail_info']['style'];
					$info[$k]['weight'] =  $v['delivery_info']['weight'];
					$info[$k]['courier_price'] =  $v['delivery_info']['price'];
					$info[$k]['date_time'] =  date("Y-m-d H:i:s",$v['order_web_status']['date_time']);
				}
				
			}
			$this->info =$info;
			$this->page = $show;
			$this->data = 'web';
		}
		$this->tpltitle='已发货报表管理';
		$this->display();	
	}
	
	//导出已发货报表 
	public function delivered_list_execl()
	{
		$order_delivery_detailDB=M('order_delivery_detail');
		$order_plat_form_statusDB = M('order_plat_form_status');
		$order_web_statusDB = M('order_web_status');
		$OrderPlatFormModel = D('OrderPlatForm');
		$OrderWebModel = D('OrderWeb');

		$data = I('get.data');
		$data01 = I('post.data');
		if( $data =='plat' || $data01 =="plat")
		{
			//运单信息表里的 订单ID
			$order_delivery_detail_order = $order_delivery_detailDB->field('order_platform_id')->where('`order_platform_id` != 0 and `order_platform_id`!=""')->group('order_platform_id')->select();
			
			foreach($order_delivery_detail_order as $k=>$v)
			{
				$delivery_order_id[$k] = $v['order_platform_id'];
			}
			$order_delivery_id_list = implode(',',$delivery_order_id);  //获得运单信息表里面的 order_id	
			//运单信息表里的 订单ID          end
			//状态表里的 订单ID   筛选判断
				$map['order_platform_id'] = array('in',$order_delivery_id_list);
				$map['status'] = 'history';
			if(I('post.beginTime') && I('post.endTime'))
			{
				$begintime = strtotime(I('post.beginTime'));
				$endtime = strtotime(I('post.endTime'));
				
				$this->beginTime =I('post.beginTime');
				$this->endTime =I('post.endTime');
				
				$map['date_time'] = array(array('gt',$begintime),array('lt',$endtime));
				
			}
			
			$order_status = $order_plat_form_statusDB->field('order_platform_id')->where($map)->group('order_platform_id')->select();
			//状态表里的 订单ID + 时间       end
			if($order_status)
			{
				foreach($order_status as $k=>$v)
				{
					$order_id[$k] = $v['order_platform_id'];
				}
				$order_id_list = implode(',',$order_id);
				$whereId['id'] = array('in',$order_id_list);  
				$list = $OrderPlatFormModel->relation(true)->where($whereId)->select();
				foreach($list as $k=>$v)
				{
					$info[$k]['order_number'] = $v['order_number'];
					$info[$k]['name'] = $v['name'];
					$info[$k]['price'] =  $v['price'];
					$info[$k]['come_from'] =  get_come_from_name($v['come_from_id']);
					$info[$k]['delivery_number'] =  $v['detail_info']['delivery_number'];
					$info[$k]['style'] =  $v['detail_info']['style'];
					$info[$k]['weight'] =  $v['delivery_info']['weight'];
					$info[$k]['courier_price'] =  $v['delivery_info']['price'];
					$info[$k]['date_time'] =  date("Y-m-d H:i:s",$v['status_info']['date_time']);
				}
				$this->info =$info;
				$this->page = $show;
				if($data)
				{
					$this->data = $data;	
				}
				else
				{
					$this->data = $data01;
				}
			}
			
		}
		else
		{
			//运单信息表里的 订单ID
			$order_delivery_detail_order = $order_delivery_detailDB->field('order_web_id')->where('`order_web_id` != 0 and `order_web_id`!=""')->group('order_web_id')->select();
			$this->data = 'web';
			foreach($order_delivery_detail_order as $k=>$v)
			{
				$delivery_order_id[$k] = $v['order_web_id'];
			}
			$order_delivery_id_list = implode(',',$delivery_order_id);  //获得运单信息表里面的 order_id	
			
			//运单信息表里的 订单ID          end
			//状态表里的 订单ID   筛选判断
				$map['order_web_id'] = array('in',$order_delivery_id_list);
				$map['status'] = 'history';
			if(I('post.beginTime') && I('post.endTime'))
			{
				$begintime = strtotime(I('post.beginTime'));
				$endtime = strtotime(I('post.endTime'));
				
				$this->beginTime =I('post.beginTime');
				$this->endTime =I('post.endTime');
				
				$map['date_time'] = array(array('gt',$begintime),array('lt',$endtime));
				$order_status = $order_web_statusDB->field('order_web_id')->where($map)->group('order_web_id')->select();
			}
			
			$order_status = $order_web_statusDB->field('order_web_id')->where($map)->group('order_web_id')->select();

			//状态表里的 订单ID + 时间       end
		//	dump($order_status);exit;
			if($order_status)
			{
				foreach($order_status as $k=>$v)
				{
					$order_id[$k] = $v['order_web_id'];
				}
			//	dump($order_id);exit;
				$order_id_list = implode(',',$order_id);
				$where['id'] = array('in',$order_id_list);  
				$list = $OrderWebModel->relation(true)->where($where)->select();
				foreach($list as $k=>$v)
				{
					$info[$k]['order_number'] = $v['order_number'];
					$info[$k]['name'] = $v['order_web_address'][0]['first_name']." ".$v['order_web_address'][0]['last_name'];
					foreach($v['order_web_product'] as $vv)
					{
						$price +=$vv['price'];
					}
					$info[$k]['price'] = $price;
					$info[$k]['come_from'] =  get_come_from_name($v['come_from_id']);
					$info[$k]['delivery_number'] =  $v['detail_info']['delivery_number'];
					$info[$k]['style'] =  $v['detail_info']['style'];
					$info[$k]['weight'] =  $v['delivery_info']['weight'];
					$info[$k]['courier_price'] =  $v['delivery_info']['price'];
					$info[$k]['date_time'] =  date("Y-m-d H:i:s",$v['order_web_status']['date_time']);
				}
			}
		}
		
		if(count($info)==0)
		{
			$this->error('请确定筛选是否有值！！');
		}
		if( $data =='plat' || $data01 =="plat")
		{
			$title=array('订单号','收件人','价格','来源','运单号','运单公司','重量','快递价格','接收时间');
			exportExcel($info,'平台—已发货报表'."-".date('Y-m-d H:i:s',time()),$title);
		}
		else
		{
			$title=array('订单号','收件人','价格','来源','运单号','运单公司','重量','快递价格','接收时间');
			exportExcel($info,'网站—已发货报表'."-".date('Y-m-d H:i:s',time()),$title);
		}
	}
	
	
	
	//签收时效报表
	public function sign_for()
	{
		$order_delivery_detailDB = M('order_delivery_detail'); //运单信息
		$order_delivery_other_priceDB = M('order_delivery_other_price');  //运单详情
		$order_plat_form_shippingDB = M('order_plat_form_shipping');
		$order_plat_form_shippingDB = M('order_plat_form_shipping');
		$order_plat_formDB = M('order_plat_form');
		$order_webDB = M('order_web');
		$order_web_addressDB = M('order_web_address');
		$OrderPlatFormModel = D('OrderPlatForm');
		$OrderWebModel = D('OrderWeb');
		$data = I('get.data');
		$data01 = I('post.data');
		$delivery_sta = array('已签收','已退签');
		if( $data =='plat' || $data01=="plat")
		{
			if(I('post.data'))
			{
				//下单时间  
				if(I('post.beginTime') && I('post.endTime'))
				{
					$this->beginTime = I('post.beginTime');
					$this->endTime = I('post.endTime');
					$whereId01['date_time'] = array(array('gt',I('post.beginTime')),array('lt',I('post.endTime')." 23:59:59"));
				}
				$oder_beginTime = $order_plat_formDB->field('id')->where($whereId01)->group('id')->select();
				foreach($oder_beginTime as $k=>$v)
				{
					$order_id01[$k] = $v['id'];
				}
				//发货时间  2
				if(I('post.beginTime_delivery') && I('post.endTime_delivery'))
				{
					$this->beginTime_delivery = I('post.beginTime_delivery');
					$this->endTime_delivery = I('post.endTime_delivery');
					$whereId02['time'] = array(array('gt',I('post.beginTime_delivery')),array('lt', I('post.endTime_delivery')." 23:59:59"));
					$whereId02['order_platform_id'] = array('neq','0');
				}
				$order_id_list02=$order_delivery_other_priceDB->where($whereId02)->field('order_platform_id')->group('order_platform_id')->select();

				foreach($order_id_list02 as $k2=>$v2)
				{
					$order_id02[$k2] = $v2['order_platform_id'];
				}
				
				//签收时间  3
				if(I('post.beginTime_over') && I('post.endTime_delivery'))
				{
					$this->beginTime_over = I('post.beginTime_over');
					$this->endTime_over = I('post.endTime_over');
					$whereId03['time'] = array(array('gt',I('post.beginTime_over')),array('lt',I('post.endTime_over')." 23:59:59"));
					$whereId03['status'] = array('in',$delivery_sta);
				}
				$order_id_list03=$order_delivery_detailDB->where($whereId03)->field('order_platform_id')->group('order_platform_id')->select();
				foreach($order_id_list03 as $k3=>$v3)
				{
					$order_id03[$k3] = $v3['order_platform_id'];
				}
				//目的国家  4
				if(I('post.order_country'))
				{
					$this->order_country = I('post.order_country');
					$whereId04['country'] = I('post.order_country');
				}
				$order_id_list04=$order_plat_form_shippingDB->where($whereId04)->field('order_platform_id')->group('order_platform_id')->select();
				foreach($order_id_list04 as $k4=>$v4)
				{
					$order_id04[$k4] = $v4['order_platform_id'];
				}
				
				//运输方式  5
				if(I('post.delivery_style'))
				{
					$this->delivery_style = I('post.delivery_style');
					$whereId05['style'] = I('post.delivery_style');
					$whereId05['order_platform_id'] = array('neq','0');
				}
				$order_id_list05=$order_delivery_other_priceDB->where($whereId05)->field('order_platform_id')->group('order_platform_id')->select();
				
				foreach($order_id_list05 as $k5=>$v5)
				{
					$order_id05[$k5] = $v5['order_platform_id'];
				}
				//快递状态  7
				if(I('post.delivery_status'))
				{
					$this->delivery_status = I('post.delivery_status');
					$whereId07['status'] = I('post.delivery_status');
					$whereId05['order_platform_id'] = array('neq','0');
				}
				$order_id_list07=$order_delivery_detailDB->where($whereId07)->field('order_platform_id')->group('order_platform_id')->select();
				foreach($order_id_list07 as $k7=>$v7)
				{
					$order_id07[$k7] = $v7['order_platform_id'];
				}
				//判断是否是签收 6
 				$whereId06['status'] = array('in',$delivery_sta);
				$whereId06['order_platform_id'] = array('neq','0');
				$order_id_list06=$order_delivery_detailDB->where($whereId06)->field('order_platform_id')->group('order_platform_id')->select();
				foreach($order_id_list06 as $k6=>$v6)
				{
					$order_id06[$k6] = $v6['order_platform_id'];
				}
				$order_id_all_01 = array_intersect($order_id01,$order_id02);
				$order_id_all_02 = array_intersect($order_id_all_01,$order_id03);
				$order_id_all_03 = array_intersect($order_id_all_02,$order_id04);
				$order_id_all_04 = array_intersect($order_id_all_03,$order_id05);
				$order_id_all_05 = array_intersect($order_id_all_04,$order_id07);
				$result_id_all = array_intersect($order_id_all_05,$order_id06);
				if(count($result_id_all) == '0')
				{
					$result_id_all ="";
				}
				$where_box['id'] = array('in',$result_id_all);
			}
			else
			{
				$whereID['status'] = array('in',$delivery_sta);
				$whereId06['order_platform_id'] = array('neq','0');
				$order_id=$order_delivery_detailDB->field('order_platform_id')->where($whereID)->group('order_platform_id')->select();
				foreach($order_id as $k2=>$v2)
				{
					$result_id_all[$k2] = $v2['order_platform_id'];
				}
				if(count($result_id_all) == '0')
				{
					$result_id_all ="";
				}
				$where_box['id'] = array('in',$result_id_all);
			}
			//import('Org.Util.Page');// 导入分页类
			$coun=$OrderPlatFormModel->where($where_box)->count();
			$Page       = new \Think\Page1($coun);// 实例化分页类 传入总记录数
			$nowPage = isset($_GET['p'])?$_GET['p']:1;
			$show       = $Page->show();// 分页显示输出
			
			$list = $OrderPlatFormModel->relation(true)->where($where_box)->page($nowPage , C('LISTROWS'))->select();
			foreach($list as $k=>$v)
			{
				$info[$k]['order_number'] = $v['order_number'];
				$info[$k]['name'] = $v['name'];
				$info[$k]['time'] = $v['date_time'];
				$info[$k]['come_from'] =  get_come_from_name($v['come_from_id']);
				$info[$k]['delivery_number'] =  $v['detail_info']['delivery_number'];
				$info[$k]['style'] =  $v['detail_info']['style'];
				$info[$k]['date_time'] =  date("Y-m-d H:i:s",$v['status_info']['date_time']);
				
				$delivery_where['style'] = $v['detail_info']['style'];
				$delivery_where['delivery_number'] = $v['detail_info']['delivery_number'];
				$delivery = $order_delivery_detailDB->field('status,time')->where($delivery_where)->order('time desc')->find();
				$info[$k]['status'] = $delivery['status'];
				$info[$k]['qs_time'] = $delivery['time'];
			}
			if($data)
			{
				$this->data = $data;	
			}
			else
			{
				$this->data = $data01;
			}
		}
		else
		{//网站
			if(I('post.data'))
			{
				//下单时间  1
				if(I('post.beginTime') && I('post.endTime'))
				{
					$this->beginTime = I('post.beginTime');
					$this->endTime = I('post.endTime');
					$whereId01['date_time'] = array(array('gt',I('post.beginTime')),array('lt',I('post.endTime')." 23:59:59"));
				}
				$oder_beginTime = $order_webDB->field('id')->where($whereId01)->group('id')->select();
				foreach($oder_beginTime as $k=>$v)
				{
					$order_id01[$k] = $v['id'];
				}
				//发货时间  2
				if(I('post.beginTime_delivery') && I('post.endTime_delivery'))
				{
					$this->beginTime_delivery = I('post.beginTime_delivery');
					$this->endTime_delivery = I('post.endTime_delivery');
					$whereId02['time'] = array(array('gt', I('post.beginTime_delivery')),array('lt',I('post.endTime_delivery')." 23:59:59"));
					$whereId02['order_web_id'] = array('neq','0');
				}
				$order_id_list02=$order_delivery_other_priceDB->where($whereId02)->field('order_web_id')->group('order_web_id')->select();
				foreach($order_id_list02 as $k2=>$v2)
				{
					$order_id02[$k2] = $v2['order_web_id'];
				}
				//签收时间  3
				if(I('post.beginTime_over') && I('post.endTime_delivery'))
				{
					$this->beginTime_over = I('post.beginTime_over');
					$this->endTime_over = I('post.endTime_over');
					$whereId03['time'] = array(array('gt',I('post.beginTime_over')),array('lt',I('post.endTime_over')." 23:59:59"));
					$whereId03['status'] = array('in',$delivery_sta);
					$whereId03['order_web_id'] = array('neq','0');
				}
				$order_id_list03=$order_delivery_detailDB->where($whereId03)->field('order_web_id')->group('order_web_id')->select();
				foreach($order_id_list03 as $k3=>$v3)
				{
					$order_id03[$k3] = $v3['order_web_id'];
				}
				//目的国家  4
				if(I('post.order_country'))
				{
					$this->order_country = I('post.order_country');
					$whereId04['country'] = I('post.order_country');
				}
				$order_id_list04=$order_web_addressDB->where($whereId04)->field('order_web_id')->group('order_web_id')->select();
				foreach($order_id_list04 as $k4=>$v4)
				{
					$order_id04[$k4] = $v4['order_web_id'];
				}
				//运输方式  5
				if(I('post.delivery_style'))
				{
					$this->delivery_style = I('post.delivery_style');
					$whereId05['style'] = I('post.delivery_style');
					$whereId05['order_web_id'] = array('neq','0');
				}
				$order_id_list05=$order_delivery_other_priceDB->where($whereId05)->field('order_web_id')->group('order_web_id')->select();
				foreach($order_id_list05 as $k5=>$v5)
				{
					$order_id05[$k5] = $v5['order_web_id'];
				}
				//快递状态  7
				if(I('post.delivery_status'))
				{
					$this->delivery_status = I('post.delivery_status');
					$whereId07['status'] = I('post.delivery_status');
					$whereId07['order_web_id'] = array('neq','0');
				}
				$order_id_list07=$order_delivery_detailDB->where($whereId07)->field('order_web_id')->group('order_web_id')->select();
				foreach($order_id_list07 as $k7=>$v7)
				{
					$order_id07[$k7] = $v7['order_web_id'];
				}
				
				//判断是否是签收 6
 				$whereId06['status'] = array('in',$delivery_sta);
				$whereId06['order_web_id'] = array('neq','0');
				$order_id_list06=$order_delivery_detailDB->where($whereId06)->field('order_web_id')->group('order_web_id')->select();
				foreach($order_id_list06 as $k6=>$v6)
				{
					$order_id06[$k6] = $v6['order_web_id'];
				}
				
				$order_id_all_01 = array_intersect($order_id01,$order_id02);
				$order_id_all_02 = array_intersect($order_id_all_01,$order_id03);
				$order_id_all_03 = array_intersect($order_id_all_02,$order_id04);
				$order_id_all_04 = array_intersect($order_id_all_03,$order_id05);
				$order_id_all_05 = array_intersect($order_id_all_04,$order_id07);
				$result_id_all = array_intersect($order_id_all_05,$order_id06);
				if(count($result_id_all) == '0')
				{
					$result_id_all ="";
				}
				$where_box['id'] = array('in',$result_id_all);
			}
			else
			{
				$whereID['status'] = array('in',$delivery_sta);
				$whereID['order_web_id'] = array('neq','0');
				$order_id=$order_delivery_detailDB->field('order_web_id')->where($whereID)->group('order_web_id')->select();
				foreach($order_id as $k2=>$v2)
				{
					$result_id_all[$k2] = $v2['order_web_id'];
				}
				if(count($result_id_all) == 0)
				{
					$result_id_all ="";
				}
				$where_box['id'] = array('in',$result_id_all);
			}
			//import('Org.Util.Page');// 导入分页类
			$coun=$OrderWebModel->where($where_box)->count();
			$Page       = new \Think\Page1($coun);// 实例化分页类 传入总记录数
			$nowPage = isset($_GET['p'])?$_GET['p']:1;
			$show       = $Page->show();// 分页显示输出
			
			$list = $OrderWebModel->relation(true)->where($where_box)->page($nowPage , C('LISTROWS'))->select();
			foreach($list as $k=>$v)
			{
				$info[$k]['order_number'] = $v['order_number'];
				$info[$k]['name'] = $v['order_web_address'][0]['first_name']." ".$v['order_web_address'][0]['last_name'];
				$info[$k]['time'] = $v['date_time'];
				foreach($v['order_web_product'] as $vv)
				{
					$price +=$vv['price'];
				}
				$info[$k]['price'] = $price;
				$info[$k]['come_from'] =  get_come_from_name($v['come_from_id']);
				$info[$k]['delivery_number'] =  $v['detail_info']['delivery_number'];
				$info[$k]['style'] =  $v['detail_info']['style'];
				$info[$k]['weight'] =  $v['delivery_info']['weight'];
				$info[$k]['courier_price'] =  $v['delivery_info']['price'];
				$info[$k]['date_time'] = date("Y-m-d H:i:s",$v['order_web_status']['date_time']);
			}
			
			$this->data = $data;	
		}
		$ID= implode(',',$result_id_all);
		$this->whereId = $ID;
		$this->info =$info;
		$this->page = $show;
		
		//获得运单公司
		$style = $order_delivery_other_priceDB->field('style')->where('`style`!=""')->group('style')->select();
		$this->style=$style;
		//目的国家
		$country = $order_plat_form_shippingDB->field('country')->where('`country`!=""')->group('country')->select();
		$this->country=$country;
		$this->tpltitle='签收时效管理';
		$this->display();	
	}
	
	//导出签收时效报表
	public function sign_for_execl()
	{
		$order_delivery_detailDB = M('order_delivery_detail'); //运单信息
		$OrderPlatFormModel = D('OrderPlatForm');
		$OrderWebModel = D('OrderWeb');
		$data = I('get.data');
		$data01 = I('post.data');
		$delivery_sta = array('已签收','已退签');
		$result_id_all = I('post.whereId');
	//	dump($result_id_all);exit;
		if( $data =='plat' || $data01=="plat")
		{
			$where_box['id'] = array('in',$result_id_all);
			$list = $OrderPlatFormModel->relation(true)->where($where_box)->select();
			foreach($list as $k=>$v)
			{
				$info[$k]['order_number'] = $v['order_number'];
				$info[$k]['name'] = $v['name'];
				$info[$k]['time'] = $v['date_time'];
				$info[$k]['come_from'] =  get_come_from_name($v['come_from_id']);
				$info[$k]['delivery_number'] =  $v['detail_info']['delivery_number'];
				$info[$k]['style'] =  $v['detail_info']['style'];
				$info[$k]['date_time'] =  date("Y-m-d H:i:s",$v['status_info']['date_time']);
				
				$delivery_where['style'] = $v['detail_info']['style'];
				$delivery_where['delivery_number'] = $v['detail_info']['delivery_number'];
				$delivery = $order_delivery_detailDB->field('status,time')->where($delivery_where)->order('time desc')->find();
				$info[$k]['qs_time'] = $delivery['time'];
				$info[$k]['status'] = $delivery['status'];
			}
		}
		else
		{//网站
			$where_box['id'] = array('in',$result_id_all);
			$list = $OrderWebModel->relation(true)->where($where_box)->select();
			foreach($list as $k=>$v)
			{
				$info[$k]['order_number'] = $v['order_number'];
				$info[$k]['name'] = $v['order_web_address'][0]['first_name']." ".$v['order_web_address'][0]['last_name'];
				$info[$k]['time'] = $v['date_time'];
				foreach($v['order_web_product'] as $vv)
				{
					$price +=$vv['price'];
				}
				$info[$k]['price'] = $price;
				$info[$k]['come_from'] =  get_come_from_name($v['come_from_id']);
				$info[$k]['delivery_number'] =  $v['detail_info']['delivery_number'];
				$info[$k]['style'] =  $v['detail_info']['style'];
				$info[$k]['weight'] =  $v['delivery_info']['weight'];
				$info[$k]['courier_price'] =  $v['delivery_info']['price'];
				$info[$k]['date_time'] = date("Y-m-d H:i:s",$v['order_web_status']['date_time']);
				
				$delivery_where['style'] = $v['detail_info']['style'];
				$delivery_where['delivery_number'] = $v['detail_info']['delivery_number'];
				$delivery = $order_delivery_detailDB->field('status,time')->where($delivery_where)->order('time desc')->find();
				$info[$k]['qs_time'] = $delivery['time'];
				$info[$k]['status'] = $delivery['status'];	
				
			}
		}
		if($result_id_all == '')
		{
			$this->error('请确定筛选是否有值！！');
		}
		if( $data =='plat' || $data01 =="plat")
		{
			$title=array('订单号','收件人','下单时间','来源','运单号','运单公司','发货时间','签收日期','运输状态');
			exportExcel($info,'平台—签收时效'."-".date('Y-m-d H:i:s',time()),$title);
		}
		else
		{
			$title=array('订单号','收件人','下单时间','来源','运单号','运单公司','发货时间','签收日期','运输状态');
			exportExcel($info,'网站—签收时效'."-".date('Y-m-d H:i:s',time()),$title);
		}
	}
	
	//发货效率报表
	public function  delivery_efficiency()
	{
		$order_delivery_detailDB = M('order_delivery_detail'); //运单信息
		$OrderPlatFormModel = D('OrderPlatForm');
		$OrderWebModel = D('OrderWeb');
		$data = I('get.data');
		$data01 = I('post.data');
		if( $data =='plat' || $data01=="plat")
		{
			$coun=$OrderPlatFormModel->where('1=1')->count();
			$Page       = new \Think\Page1($coun);// 实例化分页类 传入总记录数
			$nowPage = isset($_GET['p'])?$_GET['p']:1;
			$show       = $Page->show();// 分页显示输出			
			
			$aa[] = 'detail_info';
			$aa[] = 'delivery_info';
			$list = $OrderPlatFormModel->relation($aa)->order('date_time desc')->page($nowPage , C('LISTROWS'))->select();
			foreach($list as $k=>$v)
			{
				$info[$k]['order_number'] = $v['order_number'];
				$info[$k]['name'] = $v['name'];
				$info[$k]['email'] = $v['email'];
				$info[$k]['come_from'] =  get_come_from_name($v['come_from_id']);
				$info[$k]['delivery_number'] =  $v['detail_info']['delivery_number'];
				$info[$k]['style'] =  $v['detail_info']['style'];
				
				if($v['status_info']['date_time'])
				{
					$info[$k]['date_time'] =date("Y-m-d H:i:s",$v['status_info']['date_time']);
				}
				else
				{
					$info[$k]['date_time'] = '未发货';
				}
			}
		}
		else
		{//网站
			$coun=$OrderWebModel->where('1=1')->count();
			$Page       = new \Think\Page1($coun);// 实例化分页类 传入总记录数
			$nowPage = isset($_GET['p'])?$_GET['p']:1;
			$show       = $Page->show();// 分页显示输出
			
			$aa[] = 'detail_info';
			$aa[] = 'order_web_address';
			$aa[] = 'delivery_info';
			$list = $OrderWebModel->relation($aa)->order('date_time desc')->page($nowPage , C('LISTROWS'))->select();
			foreach($list as $k=>$v)
			{
				$info[$k]['order_number'] = $v['order_number'];
				$info[$k]['name'] = $v['order_web_address'][0]['first_name']." ".$v['order_web_address'][0]['last_name'];
				$info[$k]['email'] = $v['email'];
				$info[$k]['come_from'] =  get_come_from_name($v['come_from_id']);
				$info[$k]['delivery_number'] =  $v['detail_info']['delivery_number'];
				$info[$k]['style'] =  $v['detail_info']['style'];
				
				if($v['delivery_info']['date_time'])
				{
					$info[$k]['date_time'] =date("Y-m-d H:i:s",$v['delivery_info']['date_time']);
				}
				else
				{
					$info[$k]['date_time'] = '未发货';
				}
			}
		}
	//	dump($list);exit;
		$this->info = $info;
		if($data)
		{
			$this->data = $data;	
		}
		else
		{
			$this->data = $data01;
		}
		
		$this->page =$show;
		$this->tpltitle='发货效率报表';
		$this->display();
	}
	
	
	
	
}