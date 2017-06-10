<?php
namespace Admin\Controller;
use Think\Controller;
// 1 审核人通过 -> 交于工厂
// 2 工厂接受成功 
// 3 工厂已发货
// 4 申请人完成收货 
// 5 新品定性  工厂通知状态
// 8 放弃
// 9 审核未通过
class FactoryManageController extends CommonController
{
    public function index()
    {
        $this->display();
    }
    public function factorylist()
    {
    	$factory_model = M('factory');
    	$this->list = $factory_model->select();
        $this->display();
    }
    public function factory_edit()
    {
    	$factory_model = M('factory');
    	
    	if(IS_POST)
    	{
    		if(I('post.id'))//修改
    		{
    			$factory_model->create();
    			$factory_model->save();
    		}
    		else//添加 
    		{
    			$factory_model->create();
    			$factory_model->add();
    		}
    		redirect(U('Admin/FactoryManage/factorylist'));exit;
    	}
    	else 
    	{
    		if( I('get.id') )
    		{
    			$this->tpltitle = '修改';
    			$this->row = $factory_model->where( array('id'=>I('get.id')) )->find();
    		}
    		$this->tpltitle = '添加';
    	}
        $this->display();
    }
	//工厂新品
	public function factory_new(){
		//新品申请列表
		$product_newDB=M('product_new');	
		$userDB = M('user');
		$factory_newDB=M('factory_new');
		$info=$product_newDB->where("`status`= 1 ")->select();
		$username = session('username');    // 用户名
		if($info){
			$this->assign('list',$info);
		}
		
		//已接单列表
		//import('Org.Util.Page');// 导入分页类
		$count =$factory_newDB->where("`operator`="."'".$username."'")->order('begin_time desc')->count();
        $Page       = new \Think\Page1($count);// 实例化分页类 传入总记录数
		$nowPage = isset($_GET['p'])?$_GET['p']:1;
        $show       = $Page->show();
		$info01=$factory_newDB->where("`operator`="."'".$username."'")->order('begin_time desc')->page($nowPage.','.C('LISTROWS'))->select();
		if($info01){
			foreach($info01 as $k=>$v){
				$product_new[$k]=$product_newDB->where('`id`='.$v['productnew_id'])->find();
				$product_new[$k]['begin_time'] = $v['begin_time'];
				$product_new[$k]['end_time'] = $v['end_time'];
				$product_new[$k]['fac_id'] = $v['id'];
				$product_new[$k]['fac_status'] = $v['status'];
				$product_new[$k]['actual_time'] = $v['actual_time'];
			}
			$this->assign('product_new',$product_new);
		}
		$this->assign('page',$show);
        $this->display();
	}
	//接收新品
	public function factory_start(){
		$userDB = M('user');
		$product_newDB=M('product_new');	
		$username = session('username');    // 用户名
		if($_GET['id']){
			$id=$_GET['id'];
			$info=$product_newDB->where('`id`='.$id)->find();
			$this->assign('info',$info);
			}
		if($_POST['dosubmit']){
			//product_new 数据表 显示工厂接收
			$id=$_POST['product_new_id'];
			$product_newDB=M('product_new');
			$date['status']=2;
			$date['examination_time']=time();
			$date['examination'] = $username;
			$pro=$product_newDB->where('`id`='.$id)->save($date);
			
			//factory_newDB 数据表 工厂记录 
			$factory_newDB=M('factory_new');
			$data['productnew_id']=$_POST['product_new_id'];
			$data['operator'] = $username;
			$data['begin_time'] = strtotime($_POST['begin_time']);
			$data['end_time'] = strtotime($_POST['end_time']);
			$data['remark'] = $_POST['remark'];
			$data['status'] = 0;
			$fac=$factory_newDB->add($data);
			if($fac){
				$this->assign("jumpUrl",U('/Admin/FactoryManage/factory_new'));
				$this->success('接收新品成功！');
			}else{
				$this->error('接收新品失败!');	
			}
		}else{
			$this->assign('tpltitle','接收');
			$this->assign('username',$username);			
		$this->display();
		}
		
	}
	//完成新品  工厂表  0 接收未完成   1完成  2等待通知
	public function factory_end(){
		$id=$_GET['id'];
		//工厂数据表修改
		$date['actual_time']=time();
		$date['status']= 1;
		$factory_newDB=M('factory_new');
		$fac=$factory_newDB->where('`id`='.$id)->save($date); 
		  
		//申请数据表修改
		$factory_new_v=$factory_newDB->where('`id`='.$id)->find(); 
		$product_new_id=$factory_new_v['productnew_id'];
		$product_newDB=M('product_new');
		$data['status'] = 3;
		$data['examination_time'] =time();
		$pro=$product_newDB->where('`id`='.$product_new_id)->save($data);   
		if($pro)
		{
			$this->assign("jumpUrl",U('/Admin/FactoryManage/factory_new'));
			$this->success('新品完成操作成功！');
		}else{
			$this->error('新品完成操作失败!');	
		}
	}
	//FBA订单
	public function FbaOrder()
	{
		$fba_orderMOdel = D('FbaOrder');
		$fba_order_detailBD=M('fba_order_detail');	
		
		if(I('get.detail_sta'))
		{
			$fba_detail_cancel=M('fba_order_detail')->field('fba_order_id')->group('fba_order_id')->where('`status` ="cancel"')->select();
			
			foreach($fba_detail_cancel as $detail_k => $detail_v)
			{
				$fba_order_id[$detail_k] = $detail_v['fba_order_id'];
			}
			
			$id=implode(',',$fba_order_id);
			if($id)
			{
				$whereId['id'] =array('in',$id);
			}
			else
			{
				$whereId['id'] =array('in','-1');
			}
			$this->sta =I('get.detail_sta');
		}
		else
		{
			if(I('get.order_num'))
			{
				$order_num_where['order_number'] = trim(I('get.order_num'));
				$order_wed_id = M('order_web')->where($order_num_where)->getField('id');
				if($order_wed_id)
				{
					$whereId['order_id'] = $order_wed_id;
				}
				else
				{
					$order_platform_id = M('order_plat_form')->where($order_num_where)->getField('id');
					$whereId['order_platform_id'] = $order_platform_id;
				}
				$this->order_num = I('get.order_num');			
			}
			else
			{
				if(I('get.data'))
				{
					if(I('get.data') == 'plat')
					{
						$whereId['order_id']  = 0 ;
					}
					elseif(I('get.data') == 'web')
					{
						$whereId['orderplatform_id']  = 0 ;
					}
					$this->data = I('get.data');			
				}
				elseif(I('get.sta'))
				{
					$this-> sta = I('get.sta');
					$whereId['status'] = I('get.sta');
				}
				else
				{
					$this-> sta = 'new';
					$whereId['status'] = 'new';
				}
			}
		}
	//	dump($whereId);exit;
		//import('Org.Util.Page');// 导入分页类
		$count =$fba_orderMOdel->where($whereId)->count();
        $Page       = new \Think\Page1($count);// 实例化分页类 传入总记录数
		$nowPage = isset($_GET['p'])?$_GET['p']:1;
        $show       = $Page->show();
		$list=$fba_orderMOdel->where($whereId)->relation(true)->order('date desc')->page($nowPage.','.C('LISTROWS'))->select();
		foreach($list as $k=>$v)
		{
			
			$list[$k]['come_from']=get_come_from_name($v['come_from_id']);
			$list[$k]['coun']=$fba_order_detailBD->where('`fba_order_id` ='.$v['id'])->count();
			if($list[$k]['orderplatform_id']==0 || $list[$k]['orderplatform_id']=='')
			{
				$list[$k]['order_number']=strtoupper($list[$k]['come_from']).order_number($list[$k]['order_id'],'order_web');
				$list[$k]['uu']=order_number($list[$k]['order_id'],'order_web');
				$list[$k]['url']=__MODULE__.'/OrderManage/order_web/order_status/all/order_number/'.$list[$k]['uu'].'/come_from_id/'.$v['come_from_id'];
				$list[$k]['order'] = '';
		    }
			else
			{
				$list[$k]['order_number']=strtoupper($list[$k]['come_from']).order_number($list[$k]['orderplatform_id'],'order_plat_form');
				$list[$k]['uu']=order_number($list[$k]['orderplatform_id'],'order_plat_form');
				$list[$k]['url']=__MODULE__.'/OrderManage/order_plat/order_status/all/order_number/'.$list[$k]['uu'].'/come_from_id/'.$v['come_from_id'];
				$list[$k]['order'] = 'W';
			}
		}
		$list_new_coun = $fba_orderMOdel->where('`status` = "new"')->count();
		$list_history_coun = $fba_orderMOdel->where('`status` = "history"')->count();
		
		$list_web_coun = $fba_orderMOdel->where('`orderplatform_id` = 0')->count();
		$list_plat_coun = $fba_orderMOdel->where('`order_id` = 0')->count();
		
		if(I('get.detail_sta'))
		{
			$coun_cancel =count($list);
		}
		else
		{
			$fba_detail_cancel=M('fba_order_detail')->field('fba_order_id')->group('fba_order_id')->where('`status` ="cancel"')->select();
			
			foreach($fba_detail_cancel as $detail_k => $detail_v)
			{
				$fba_order_id[$detail_k] = $detail_v['fba_order_id'];
			}
			$id=implode(',',$fba_order_id);
			if($id)
			{
				$map['id'] =array('in',$id);
			}
			else
			{
				$map['id'] =array('in','-1');
			}
			$coun_cancel = M('fba_order')->where($map)->count();
		}
		
		$this->assign('coun_cancel',$coun_cancel);
		
		
		//print_r($list);
		//exit();
		$this->factory_list=M("factory")->where('1=1')->select();
		$this->type="FBA";
		$this->page=$show;
		$this->assign("info",$list);
		$this->assign("list_new_coun",$list_new_coun);
		$this->assign("list_history_coun",$list_history_coun);	
		$this->assign("list_web_coun",$list_web_coun);
		$this->assign("list_plat_coun",$list_plat_coun);		
		$this->display();
	}
	//更改状态
	public function FbaOrder_sta()
	{
		$fba_orderMOdel = D('FbaOrder');
		if(I('get.id'))
		{
			$whereId['id'] = I('get.id');
			$id[0] = I('get.id');
		}
		elseif(I('post.check'))
		{
			$id=I('post.check');
			$aa = implode(',', $id); 
            $whereId = 'id in (' . $aa . ')'; 
		}
		$data['status'] = 'history';
		$result = $fba_orderMOdel->where($whereId)->save($data);
		foreach($id as $v)
		{
			$order_plat_form_statusDB =M('order_plat_form_status');
			$order_web_statusDB = M('order_web_status');
			
			$order = $fba_orderMOdel->where('`id` ='.$v)->field('order_id,orderplatform_id')->find();
			//dump($order);exit;
			if($order['order_id'])
			{
				
				$where['order_web_id'] = $order['order_id'];
				$date['status'] = 'history';
				$date['message'] = "FBA订单转入";
				$date['date_time'] = time();
				$date['operator'] =session('username');
				
				$date_sta = $order_web_statusDB;
				$date_sta ->where($where)->save($date);
				
				$date['order_web_id'] = $order['order_id'];
				$date_sta_history = M('order_web_status_history');
				$date_sta_history ->add($date);
			}
			else
			{
				$date['order_platform_id'] = $order['orderplatform_id'];
				
				$date['status'] = 'history';
				$date['message'] = "FBA订单转入";
				$date['date_time'] = time();
				$date['operator'] =session('username');
				
				$date_sta = $order_web_statusDB;
				$date_sta ->where($where)->save($date);
				
				$date['order_platform_id'] = $order['orderplatform_id'];
				$date_sta_history = M('order_plat_form_status_history');
				$date_sta_history ->add($date);
			}
		
		}
		if($result)
		{
			$this->success('状态改变成功');
		}
		else
		{
			$this->success('状态改变失败');
		}
	}
	//导出FBA execl文档
	public function execl_export_Fba()
	{
		$fba_orderMOdel = D('FbaOrder');
		$fba_order_detailBD=M('fba_order_detail');	
		$factory_orderDB=M('factory_order');
		$factoryDB=M('factory');
		$id_come_fromDB=M('id_come_from');
		$id_product_codeDB = M('id_product_code');
		$id_product_attributeDB =M('id_product_attribute');
		$order_web_addressDB = M('order_web_address');
		$order_plat_form_shippingDB = M('order_plat_form_shipping');
		$fba_orderDB = M('fba_order'); 
		$product_stock_orderDB = M('product_stock_order');
		if(IS_POST)
		{
			if(I('post.check')=="")
			{
			   $this->error('你没有做出选择');	
			}
			$execl_sta=I('post.execl_sta');
			$id=I('post.check');
			$aa = implode(',', $id); 
            $whereId = 'id in (' . $aa . ')'; 
			//分开网站与平台
			$where_plat = $whereId." and `orderplatform_id`!=0";
			$info_plat = $fba_orderMOdel->where($where_plat)->order('date,date_detail')->select();
			$where_web = $whereId." and `order_id`!=0";
			$info_web = $fba_orderMOdel->where($where_web)->order('date,date_detail')->select();
			
			if($info_plat && $info_web)
			{
				$info = array_merge($info_web,$info_plat);
				
			}
			else
			{
				if($info_plat)
				{
					$info = $info_plat;	
				}
				elseif($info_web)
				{
					$info = $info_web;	
				}
			}
			//分开网站与平台  end
			//$info=$fba_orderMOdel->where($whereId)->order('date desc')->select();
			//dump($info);exit;
			$nnn=0;
			foreach($info as $k=>$v)
			{
				if($v['orderplatform_id']!=0)
				{
					$fac_num = associated_fac($v['id'],$v['orderplatform_id'],'fba',1,'rn','plat');
				}
				elseif($v['order_id']!=0)
				{
					$fac_num = associated_fac($v['id'],$v['order_id'],'fba',1,'rn','web');
				}
				
				$list[$nnn]['fac_number'] = $fac_num;
				
				$info[$k]['fba_order_detail']=M('fba_order_detail')->where('`fba_order_id` ='.$v['id']." and `status` !='cancel'")->select();
				
				foreach($info[$k]['fba_order_detail'] as $kk=>$vv)
				{
					if($kk>0)
					{
						$list[$nnn]['fac_number'] ="";
					}
					
					$list[$nnn]['code_name'] = $vv['name'];
					
					//判断 size color
							$code[$nnn] = explode('-',$list[$nnn]['code_name']);
							$code_name_coun[$nnn] =count($code[$nnn]);

							if($code_name_coun[$nnn]>2)         // - 分割
							{
								$list[$nnn]['size'] = $code[$nnn][$code_name_coun[$nnn]-1];
								
								$list[$nnn]['color'] = $code[$nnn][$code_name_coun[$nnn]-2];
							}
							else  // 空格 分割
							{
								$code[$nnn] = explode(' ',$list[$nnn]['code_name']);
							
								$code_name_coun[$nnn] =count($code[$nnn]);
								
								if($code_name_coun[$nnn]>2)         
								{
									$code_dh[$nnn] = explode(',',$list[$nnn]['code_name']);
									
									//棉壳四季被  170x220cm,1.25kg
									//19MM旅行枕芯 旅行枕套套装 杏粉 30x40cm, 300g
							
									$code_dh_name_coun[$nnn] =count($code_dh[$nnn]);
									
									if($code_dh_name_coun[$nnn]>1)
									{
										
										$code[$nnn] = explode(' ',$code_dh[$nnn][0]);
										
										$code_name_coun[$nnn] =count($code[$nnn]);
							
										$list[$nnn]['size'] = $code[$nnn][$code_name_coun[$nnn]-1].' '.$code_dh[$nnn][1];
										
										$list[$nnn]['color'] = $code[$nnn][$code_name_coun[$nnn]-2];
									
									}
									else
									{
										$list[$nnn]['size'] = $code[$nnn][$code_name_coun[$nnn]-1];
										
										$list[$nnn]['color'] = $code[$nnn][$code_name_coun[$nnn]-2];
									}
								}
								else
								{
									$list[$nnn]['size'] = $code[$nnn][$code_name_coun[$nnn]-1];
									
									$list[$nnn]['color'] =" ";
									
								}
								
							}
					/*
					$code_val = $id_product_codeDB->field('size_id,color_id')->where('`code` = "'.$vv['code'].'"')->find();

					$list[$nnn]['size'] = $id_product_attributeDB->where('`id` = '.$code_val['size_id'])->getField('value');
					
					$list[$nnn]['color'] = $id_product_attributeDB->where('`id` = '.$code_val['color_id'])->getField('value');
					*/
					$list[$nnn]['num'] = $vv['number'];
					
					$list[$nnn]['description'] = $vv['description'];
					
					$come_from=get_come_from_name($v['come_from_id']);
					
					if($v['orderplatform_id']!=0)
					{
						
						$list[$nnn]['order_number']=order_number($v['orderplatform_id'],'order_plat_form');	//交易单号
						$order_address =$order_plat_form_shippingDB->where('`order_platform_id`='.$v['orderplatform_id'])->group('name,country')->find();
						
						$list[$nnn]['logo'] = 'Lilysilk';
						$list[$nnn]['come_from'] = $come_from;
						$list[$nnn]['name'] = $order_address['name'];
						
						$list[$nnn]['message_buy'] = M('order_plat_form')->where('`id` = '.$v['orderplatform_id'])->getField('message');  //买家留言
						$list[$nnn]['message_seller']=message_seller($v['orderplatform_id'],'plat');//商家留言
						
						$list[$nnn]['country'] = $order_address['country'];
					}
					elseif($v['order_id']!=0)
					{
						
					//  $list[$nnn]['order_number']=strtoupper($come_from).order_number($v['order_id'],'order_web');	
						$list[$nnn]['order_number'] = order_number_full($v['order_id']);
						$order_address = $order_web_addressDB->where('`order_web_id`='.$v['order_id'])->group('first_name,last_name,country')->find();
						$list[$nnn]['logo'] = 'Lilysilk';
						$list[$nnn]['come_from'] = 'Lilysilk_'.$come_from;
						$list[$nnn]['name'] = $order_address['first_name'].' '.$order_address['last_name'];
						
						$list[$nnn]['message_buy'] = M('order_web')->where('`id` = '.$v['order_id'])->getField('message');  //买家留言
						$list[$nnn]['message_seller']='';
						
						$payment_style = M('order_web')->where('`id` ='.$v['order_id'])->getField('payment_style');
						if($payment_style == 'cash on delivery')
						{
							$list[$nnn]['message_seller'] = '货到付款（cash on delivery）'." \r\n ";
						}
						elseif($payment_style == 'bank transfer' || $payment_style == 'mail remittance')
						{
							$list[$nnn]['message_seller'] = $payment_style." \r\n ";
						}
						$list[$nnn]['message_seller'].=message_seller($v['order_id'],'web'); //商家留言
						
						$list[$nnn]['country'] = $order_address['country'];
					}
					else
					{	
						$list[$nnn]['order_number']='';	
						$list[$nnn]['logo'] = 'Lilysilk';
						$list[$nnn]['come_from'] ='Lilysilk'.$come_from;
						$list[$nnn]['name'] ='';
						$list[$nnn]['message_buy']='';
						$list[$nnn]['message_seller'] ='';
						$list[$nnn]['country'] ='';
						
					}
					$list[$nnn]['code'] = $vv['code'];
					
					if($v['orderplatform_id']!=0)
					{
						$list[$nnn]['SKU'] = M('order_plat_form_product')->where('`order_platform_id` ='.$v['orderplatform_id'])->getField('sku');
					}
					elseif($v['order_id']!=0)
					{
						$list[$nnn]['SKU'] = M('order_web_product_original')->where('`order_web_id` ='.$v['order_id'])->getField('sku');
					}
				
					//附加产品
					if($v['order_id']!=0)
					{
						$list[$nnn]['fac_detail_message'] = product_extra($v['order_id']);
						$list[$nnn]['sample'] = panduan_sample($v['order_id'],$v['come_from_id']);
					}
					else
					{
						$list[$nnn]['fac_detail_message'] ='';
						$list[$nnn]['sample'] ='';
					}
					$nnn++;
					
				}
			}
		//	dump($list);exit; 
			$title=array('工厂单号','品名','规格','颜色','数量','备注','网站单号','商标','网站','客户名称','买家留言','卖家留言','国家','code','sku','特别注意','是否有样布');
			if($execl_sta =='new')
			{
				exportExcel($list,'FBA新订单'."-".date('Y-m-d H:i:s',time()),$title);
			}
			elseif($execl_sta =='history')
			{	
				exportExcel($list,'FBA历史订单'."-".date('Y-m-d H:i:s',time()),$title);
			}
			elseif($execl_sta =='cancel')
			{	
				exportExcel($list,'FBA已取消订单'."-".date('Y-m-d H:i:s',time()),$title);
			}
			elseif($execl_sta =='web')
			{	
				exportExcel($list,'FBA网站订单'."-".date('Y-m-d H:i:s',time()),$title);
			}
			elseif($execl_sta =='plat')
			{	
				exportExcel($list,'FBA平台订单'."-".date('Y-m-d H:i:s',time()),$title);
			}
			
		}	   
	}	
	
	//库存订单
	public function StockOrder()
	{
		$product_stock_orderModel = D('ProductStockOrder');
		$product_stock_orderBD=M('product_stock_order_detail');	
		$factoryDB=M('factory');
		$order_webDB = M('order_web');
		$order_plat_formDB = M('order_plat_form');
		$product_stock_order_detailBD=M('product_stock_order_detail');	
		
		$product_stock_orderDB = M('product_stock_order');
		$fba_orderDB = M('fba_order');
		$factory_orderDB = M('factory_order');
		
		if(I('get.detail_sta'))
		{
			$product_detail_cancel=$product_stock_order_detailBD->field('product_stock_order_id')->group('product_stock_order_id')->where('`status` ="cancel"')->select();
			
			foreach($product_detail_cancel as $detail_k => $detail_v)
			{
				$product_id[$detail_k] = $detail_v['product_stock_order_id'];
			}
			
			$id=implode(',',$product_id);
			if($id)
			{
				$whereId['id'] =array('in',$id);
			}
			else
			{
				$whereId['id'] =array('in','-1');
			}
			$this->detail_sta =I('get.detail_sta');
		}
		else
		{
		
			if(I('get.order_num'))
			{
				$order_num_where['order_number'] = trim(I('get.order_num'));
				$order_wed_id = M('order_web')->where($order_num_where)->getField('id');
				if($order_wed_id)
				{
					$whereId['order_id'] = $order_wed_id;
				}
				else
				{
					$order_platform_id = M('order_plat_form')->where($order_num_where)->getField('id');
					$whereId['order_platform_id'] = $order_platform_id;
				}
				$this->order_num = I('get.order_num');			
			}
			else
			{
				if(I('get.data'))
				{
					if(I('get.data') == 'plat')
					{
						$whereId['order_id']  = 0 ;
					}
					elseif(I('get.data') == 'web')
					{
						$whereId['order_platform_id']  = 0 ;
					}
					$this->data = I('get.data');			
				}
				elseif(I('get.sta'))
				{
					$this-> sta = I('get.sta');
					$whereId['status'] = I('get.sta');
				}
				else
				{
					$this-> sta = 'new';
					$whereId['status'] = 'new';
				}
				
				$screening = I('get.screening');
			}
		}
		//dump($whereId);exit;
		if($screening) //筛选可以发货的数量
		{
			$list=$product_stock_orderModel->where($whereId)->order('id desc')->select();	
			$this->screening = $screening;
			
		}
		else
		{
			//import('Org.Util.Page');// 导入分页类
			$count =$product_stock_orderModel->where($whereId)->count();
			$Page       = new \Think\Page1($count,100);// 实例化分页类 传入总记录数
			$nowPage = isset($_GET['p'])?$_GET['p']:1;
			$show       = $Page->show();
			$list=$product_stock_orderModel->where($whereId)->order('id desc')->page($nowPage,100)->select();	
		}
		//dump($list);exit;
		foreach($list as $k=>$v)
		{
			$info[$k]['order_id']=$v['order_id'];
			$info[$k]['order_platform_id']=$v['order_platform_id'];
			$info[$k]['id']=$v['id'];
			$info[$k]['status']=$v['status'];
			$info[$k]['come_from']=get_come_from_name($v['come_from_id']);
			$info[$k]['number'] =$v['number']; 
			$info[$k]['order_id'] = $v['order_id'];
			$info[$k]['order_platform_id'] = $v['order_platform_id'];
			$info[$k]['coun']=$product_stock_orderBD->where('`product_stock_order_id` ='.$v['id'])->count();
			if($v['order_platform_id']==0 || $v['order_platform_id']=='')
			{
				$info[$k]['order_number']=strtoupper($info[$k]['come_from']).order_number($v['order_id'],'order_web');
				$info[$k]['uu']=order_number($v['order_id'],'order_web');
				$info[$k]['url']=__MODULE__.'/OrderManage/order_web/order_status/all/order_number/'.$info[$k]['uu'].'/come_from_id/'.$v['come_from_id'];
				$order_info = $order_webDB->where('`id` = '.$v['order_id'])->field('message,date_time')->find();
				
				$info[$k]['message_buy'] = $order_info['message'];  //买家留言
				
				$info[$k]['date_time']=$order_info['date_time'];
				$info[$k]['date']=$v['date'];
				$info[$k]['date_detail']=$v['date_detail'];
				$info[$k]['message_seller']=message_seller($v['order_id'],'web','<br>'); //商家留言
			}
			else
			{
				$info[$k]['order_number']=strtoupper($info[$k]['come_from']).order_number($v['order_platform_id'],'order_plat_form');
				$info[$k]['uu']=order_number($v['order_platform_id'],'order_plat_form');
				$info[$k]['url']=__MODULE__.'/OrderManage/order_plat/order_status/all/order_number/'.$info[$k]['uu'].'/come_from_id/'.$v['come_from_id'];
				
				$order_info = $order_plat_formDB->where('`id` = '.$v['order_platform_id'])->field('message,date_time')->find();
				
				$info[$k]['message_buy'] = $order_info['message'];  //买家留言
				
				$info[$k]['date_time']=$order_info['date_time'];
				$info[$k]['date']=$v['date'];
				$info[$k]['date_detail']=$v['date_detail'];
				$info[$k]['message_seller']=message_seller($v['order_platform_id'],'plat','<br>');//商家留言
				
				
			}
			$info[$k]['detail_info'] = $product_stock_order_detailBD->where('`product_stock_order_id` ='.$v['id'])->select();
		}
		//dump($info);exit;
		if($screening)
		{
			$show = count($info)." 条数据";	
		}
		$list_new_coun = $product_stock_orderModel->where('`status` = "new"')->count();
		$list_history_coun = $product_stock_orderModel->where('`status` = "history"')->count();
		$list_web_coun = $product_stock_orderModel->where('`order_platform_id` = 0')->count();
		$list_plat_coun = $product_stock_orderModel->where('`order_id` = 0')->count();
		
		if(I('get.detail_sta'))
		{
			$coun_cancel =count($info);
		}
		else
		{
			$product_detail_cancel=$product_stock_order_detailBD->field('product_stock_order_id')->group('product_stock_order_id')->where('`status` ="cancel"')->select();;
			foreach($product_detail_cancel as $detail_k => $detail_v)
			{
				$product_id[$detail_k] = $detail_v['product_stock_order_id'];
			}
			$id=implode(',',$product_id);
			if($id)
			{
				$map['id'] =array('in',$id);
			}
			else
			{
				$map['id'] =array('in','-1');
			}
			$coun_cancel = $product_stock_orderBD->where($map)->count();
		}
		
		$this->assign('coun_cancel',$coun_cancel);
		
		$factory_list = $factoryDB->where('1=1')->select();
		$this->factory_list=$factory_list;
	//	dump($info);exit;
		$this->type="K";
		$this->page=$show;
		$this->assign("info",$info);
		$this->assign("list_new_coun",$list_new_coun);
		$this->assign("list_history_coun",$list_history_coun);	
		$this->assign("list_web_coun",$list_web_coun);
		$this->assign("list_plat_coun",$list_plat_coun);		
		$this->display();
	}
	//更改状态
	public function StockOrder_sta()
	{
		$product_stock_orderModel = D('ProductStockOrder');
		$product_stock_orderBD=M('product_stock_order_detail');	
		if(I('get.id'))
		{
			$whereId['id'] = I('get.id');
		}
		elseif(I('post.check'))
		{
			$id=I('post.check');
			$aa = implode(',', $id); 
            $whereId = 'id in (' . $aa . ')'; 
		}
		$data['status'] = 'history';
		
		$result = $product_stock_orderModel->where($whereId)->save($data);
		if($result)
		{
			$this->success('状态改变成功');
		}
		else
		{
			$this->success('状态改变失败');
		}
	}
	//导出Stock execl文档
	public function execl_export_Stock()
	{
		$product_stock_orderModel = D('ProductStockOrder');
		$product_stock_orderDB=M('product_stock_order');	
		
		$factory_order_detailDB=M('factory_order_detail');
		$factory_orderDB=M('factory_order');
		$id_product_codeDB = M('id_product_code');
		$id_product_attributeDB =M('id_product_attribute');
		$order_web_addressDB = M('order_web_address');
		$order_plat_form_shippingDB = M('order_plat_form_shipping');
		$fba_orderDB = M('fba_order'); 
		if(IS_POST)
		{
			if(I('post.check')=="")
			{
			   $this->error('你没有做出选择');	
			}
			$execl_sta=I('post.execl_sta');
			$id=I('post.check');
			$aa = implode(',', $id); 
            $whereId = 'id in (' . $aa . ')'; 
			//区分网站平台
			$where_plat = $whereId." and `order_platform_id`!=0";
			$info_plat = $product_stock_orderModel->where($where_plat)->relation('detail_list')->order('date,date_detail')->select();
			$where_web = $whereId." and `order_id`!=0";
			$info_web = $product_stock_orderModel->where($where_web)->relation('detail_list')->order('date,date_detail')->select();
			
			if($info_plat && $info_web)
			{
				$info = array_merge($info_web,$info_plat);				
			}
			else
			{
				if($info_plat)
				{
					$info = $info_plat;	
				}
				elseif($info_web)
				{
					$info = $info_web;	
				}
			}
			//区分网站平台 end
			//$info=$product_stock_orderModel->where($whereId)->relation('detail_list')->order('date desc')->select();
			
			$nnn=0;
			foreach($info as $k=>$v)
			{
				$detail_list = M('product_stock_order_detail')->where('`product_stock_order_id` ='.$v['id']." and `status` !='cancel'")->select();
				
				if($detail_list)
				{
					if($v['order_platform_id']!=0)
					{
						$fac_num = associated_fac($v['id'],$v['order_platform_id'],'bd',1,'rn','plat');
					}
					elseif($v['order_id']!=0)
					{
						$fac_num = associated_fac($v['id'],$v['order_id'],'bd',1,'rn','web');
					}
					$list[$nnn]['fac_number'] = $fac_num;
					
					foreach($detail_list as $kk=>$vv)
					{
						$code_val = $id_product_codeDB->field('name,size_id,color_id')->where('`code` = "'.$vv['code'].'"')->find();
	
						if($kk>0)
						{
							$list[$nnn]['fac_number'] ="";
						}
						$list[$nnn]['code_name'] =$code_val['name'];
						
						//判断 size color
							$code[$nnn] = explode('-',$list[$nnn]['code_name']);
							$code_name_coun[$nnn] =count($code[$nnn]);

							if($code_name_coun[$nnn]>2)         // - 分割
							{
								$list[$nnn]['size'] = $code[$nnn][$code_name_coun[$nnn]-1];
								
								$list[$nnn]['color'] = $code[$nnn][$code_name_coun[$nnn]-2];
							}
							else  // 空格 分割
							{
								$code[$nnn] = explode(' ',$list[$nnn]['code_name']);
							
								$code_name_coun[$nnn] =count($code[$nnn]);
								
								if($code_name_coun[$nnn]>2)         // - 分割
								{
									$code_dh[$nnn] = explode(',',$list[$nnn]['code_name']);
									
									//棉壳四季被  170x220cm,1.25kg
									//19MM旅行枕芯 旅行枕套套装 杏粉 30x40cm, 300g
							
									$code_dh_name_coun[$nnn] =count($code_dh[$nnn]);
									
									if($code_dh_name_coun[$nnn]>1)
									{
										
										$code[$nnn] = explode(' ',$code_dh[$nnn][0]);
										
										$code_name_coun[$nnn] =count($code[$nnn]);
							
										$list[$nnn]['size'] = $code[$nnn][$code_name_coun[$nnn]-1].' '.$code_dh[$nnn][1];
										
										$list[$nnn]['color'] = $code[$nnn][$code_name_coun[$nnn]-2];
									
									}
									else
									{
										$list[$nnn]['size'] = $code[$nnn][$code_name_coun[$nnn]-1];
										
										$list[$nnn]['color'] = $code[$nnn][$code_name_coun[$nnn]-2];
									}
								}
								else
								{
									$list[$nnn]['size'] = $code[$nnn][$code_name_coun[$nnn]-1];
									
									$list[$nnn]['color'] =" ";
									
								}
								
							}
						/*
						$list[$nnn]['size'] = $id_product_attributeDB->where('`id` = '.$code_val['size_id'])->getField('value');
						
						$list[$nnn]['color'] = $id_product_attributeDB->where('`id` = '.$code_val['color_id'])->getField('value');
						*/
						$list[$nnn]['num'] = $vv['number'];
						
						$list[$nnn]['description'] = $vv['description'];
						
						$come_from=get_come_from_name($v['come_from_id']);
						
						if($v['order_platform_id']!=0)
						{
							$list[$nnn]['order_number']=order_number($v['order_platform_id'],'order_plat_form');	//交易单号
							$order_address =$order_plat_form_shippingDB->where('`order_platform_id`='.$v['order_platform_id'])->group('name,country')->find();
							$list[$nnn]['logo'] = 'Lilysilk';
							$list[$nnn]['come_from'] =$come_from;
							$list[$nnn]['name'] = $order_address['name'];
							
							$list[$nnn]['message_buy'] = M('order_plat_form')->where('`id` = '.$v['order_platform_id'])->getField('message');  //买家留言
							$list[$nnn]['message_seller']='';
							$message_seller = M('order_business_message')->where('`order_platform_id` = '.$v['order_platform_id'])->field('message,date_time')->select();
							foreach($message_seller  as $sell_k=>$sell_v)
							{
								$list[$nnn]['message_seller'].=$sell_k+1 .".".$sell_v['message']."-".date('m-d',$sell_v['date_time'])." \r\n ";					 //商家留言
							}
							
							$list[$nnn]['country'] = $order_address['country'];
						}
						elseif($v['order_id']!=0)
						{
							//$list[$nnn]['order_number']=strtoupper($come_from).order_number($v['order_id'],'order_web');	
							$list[$nnn]['order_number'] = order_number_full($v['order_id']);
							$order_address = $order_web_addressDB->where('`order_web_id`='.$v['order_id'])->group('first_name,last_name,country')->find();
							$list[$nnn]['logo'] = 'Lilysilk';
							$list[$nnn]['come_from'] = 'Lilysilk_'.$come_from;
							$list[$nnn]['name'] = $order_address['first_name'].' '.$order_address['last_name'];
							
							$list[$nnn]['message_buy'] = M('order_web')->where('`id` = '.$v['order_id'])->getField('message');  //买家留言
							$list[$nnn]['message_seller']='';
							
							$payment_style = M('order_web')->where('`id` ='.$v['order_id'])->getField('payment_style');
							if($payment_style == 'cash on delivery')
							{
								$list[$nnn]['message_seller'] = '货到付款（cash on delivery）'." \r\n ";
							}
							elseif($payment_style == 'bank transfer' || $payment_style == 'mail remittance')
							{
								$list[$nnn]['message_seller'] = $payment_style." \r\n ";
							}
							
							$message_seller = M('order_business_message')->where('`order_id` = '.$v['order_id'])->field('message,date_time')->select();
							foreach($message_seller  as $sell_k=>$sell_v)
							{
								$list[$nnn]['message_seller'].=$sell_k+1 .".".$sell_v['message']."-".date('m-d',$sell_v['date_time'])." \r\n ";
							}
							
							$list[$nnn]['country'] = $order_address['country'];
						}
						else
						{	
							$list[$nnn]['order_number']='';	
							$list[$nnn]['logo'] = 'Lilysilk';
							$list[$nnn]['come_from'] = 'Lilysilk_'.$come_from;
							$list[$nnn]['name'] ='';
							$list[$nnn]['message_buy']='';
							$list[$nnn]['message_seller'] ='';
							$list[$nnn]['country'] ='';
							
						}
						$list[$nnn]['code'] = $vv['code'];
						if($v['order_platform_id']!=0)
						{
							$list[$nnn]['SKU'] = M('order_plat_form_product')->where('`order_platform_id` ='.$v['order_platform_id'])->getField('sku');
						}
						elseif($v['order_id']!=0)
						{
							$list[$nnn]['SKU'] = M('order_web_product_original')->where('`order_web_id` ='.$v['order_id'])->getField('sku');
						}
						//附加产品
						if($v['order_id']!=0)
						{
							$list[$nnn]['fac_detail_message'] = product_extra($v['order_id']);
							$list[$nnn]['sample'] = panduan_sample($v['order_id'],$v['come_from_id']);
						}
						else
						{
							$list[$nnn]['fac_detail_message'] ='';
							$list[$nnn]['sample'] ='';
						}
						$nnn++;
						
					}
				}
			}
			$title=array('工厂单号','品名','规格','颜色','数量','备注','网站单号','商标','网站','客户名称','买家留言','商家留言','国家','code',"sku",'特别注意','是否有样布');
		    dump($list);exit;
			if($execl_sta =='new')
			{
				exportExcel($list,'库存新订单'."-".date('Y-m-d H:i:s',time()),$title);
			}
			elseif($execl_sta =='history')
			{
				exportExcel($list,'库存历史订单'."-".date('Y-m-d H:i:s',time()),$title);
			}
			elseif($execl_sta =='web')
			{	
				exportExcel($list,'库存网站订单'."-".date('Y-m-d H:i:s',time()),$title);
			}
			elseif($execl_sta =='plat')
			{
				exportExcel($list,'库存平台订单'."-".date('Y-m-d H:i:s',time()),$title);
			}
			
		}	   
	}
	//库存 批量转工厂
	public function transform_form_submit()
	{
		$product_stock_orderModel = D('ProductStockOrder');
		$factory_orderDB = M('factory_order');
		$factory_order_detailDB = M('factory_order_detail');
		$succ_num =0;
		$err_num = 0;
		$err_detail_num = 0; //失败的附表
		if(I('post.check')=="" || I('post.factory_list')=="")
		{
		   $this->error('你没有做出选择');	
		}
		$factory = I('post.factory_list');
		$time= date('Y-m-d',time());
		$factory_num = $factory_orderDB->where('`factory_id` = "'.$factory.'" and date = "'.$time.'"')->order('number desc')->getField('number'); //根据当前时间 查询订单数量
		if(!$factory_num)
		{
			$factory_num =0;
		}
		$id=I('post.check'); 
		foreach($id as $k=>$v)
		{	
			$stock_val = $product_stock_orderModel->relation('detail_info')->where('`id` = '.$v)->find();
			$factory_num ++;
			$date['come_from_id'] = $stock_val['come_from_id'];
			$date['order_platform_id'] = $stock_val['order_platform_id'];
			$date['order_id'] = $stock_val['order_id'];
			$date['factory_id']  = $factory ;
			$date['date'] = $time;
			$date['number'] = $factory_num ;
			$date['status'] ="new";
			$return = $factory_orderDB->add($date);
			if($return)
			{
				foreach($stock_val['detail_info'] as $kk=>$vv)
				{
					$de['factory_order_id'] = $return;
					$de['code'] = $vv['code'];
					$de['number'] = $vv['number'];
					$fac_detail = $factory_order_detailDB ->add($de);
					if($fac_detail)
					{
						$factory_order_detailDB->where('`id` = '.$vv['id'])->delete();
					}
					else
					{
						$err_detail[$err_detail_num] = $vv['id'];
						$err_detail_num++;
					}
				}
				$product_stock_orderModel->where('`id` = '.$v)->delete();
			}
			else
			{
				$err[$err_num] = $v;
				$err_num++;
			}
			
 		}
		if($err_num ==0 && $err_detail_num==0)
		{
			 $this->success('工厂改变成功！！');
		}
		else
		{
			echo "错误工厂订单ID :";
			foreach($err as $kkk=>$vvv)
			{
				echo $vvv."<br>";
			}
			echo "错误工厂副表ID :";
			foreach($err_detail as $kkkk=>$vvvv)
			{
				echo $vvvv."<br>";
			}
		}
	}
	//洗涤剂订单
	public function XdjOrder()
	{
		$FactoryOrderModel=D('FactoryOrder');
		$factory_order_detailDB =M('factory_order_detail');
		$factoryDB=M('factory');
		$id_come_fromDB=M('id_come_from');
		
		$product_stock_orderDB = M('product_stock_order');
		$fba_orderDB = M('fba_order');
		$factory_orderDB = M('factory_order');
		
		$order_webDB = M('order_web');
		$order_plat_formDB = M('order_plat_form');
		if(I('get.detail_sta'))
		{
			$fac_detail_cancel=$factory_order_detailDB->field('factory_order_id')->group('factory_order_id')->where('`status` ="cancel"')->select();
			foreach($fac_detail_cancel as $detail_k => $detail_v)
			{
				$fac_id[$detail_k] = $detail_v['factory_order_id'];
			}
			$id=implode(',',$fac_id);
			if($id)
			{
				$whereId['id'] =array('in',$id);
			}
			else
			{
				$whereId['id'] =array('in','-1');
			}
			$this->detail_sta =I('get.detail_sta');
		}
		else
		{
			if(I('get.order_num'))
			{
				$order_num_where['order_number'] = trim(I('get.order_num'));
				$order_wed_id = M('order_web')->where($order_num_where)->getField('id');
				if($order_wed_id)
				{
					$whereId['order_id'] = $order_wed_id;
				}
				else
				{
					$order_platform_id = M('order_plat_form')->where($order_num_where)->getField('id');
					$whereId['order_platform_id'] = $order_platform_id;
				}
				$this->order_num = I('get.order_num');			
			}
			elseif(I('get.fac_num'))
			{
				$fac_num = trim(I('get.fac_num'));
				if(preg_match('/^([A-Z]*)/i',$fac_num,$arr)){
				   $type = $arr[0];
				}
				$type_num = strlen($type);
				$str = substr($fac_num,$type_num );
				$str_exp = explode('-',$str);
				
				$str_time = $str_exp[0];
				
				$str_num = $str_exp[1];
				$fac_time = explode('.',$str_time);
				
				$whereId['date'] = date("Y",time())."-".$fac_time[0]."-".$fac_time[1];
				if($str_num>9)
				{
					$day_num = $str_num;
				}
				else
				{
					$day_num = substr($str_num,1);   //      number
				}
				$whereId['number']  = $day_num;
				
				$this->fac_num = I('get.fac_num');
				$this->sta = 'get';
			}
			else
			{
				if(I('get.data'))
				{
					if(I('get.data') == 'plat')
					{
						$whereId['order_id']  = 0 ;
					}
					elseif(I('get.data') == 'web')
					{
						$whereId['order_platform_id']  = 0 ;
					}
					$this->data = I('get.data');			
				}
				elseif(I('get.sta'))
				{
					$this-> sta = I('get.sta');
					$whereId['status'] = I('get.sta');
				}
				else
				{
					$this-> sta = 'new';
					$whereId['status'] = 'new';
				}
			}
		}
		$whereId['factory_id'] = 9;
	//	dump($whereId);exit;
		//import('Org.Util.Page');// 导入分页类
		$count =$FactoryOrderModel->where($whereId)->count();
        $Page       = new \Think\Page1($count);// 实例化分页类 传入总记录数
		$nowPage = isset($_GET['p'])?$_GET['p']:1;
        $show       = $Page->show();

		$list=$FactoryOrderModel->where($whereId)->order('date desc')->page($nowPage.','.C('LISTROWS'))->select();
		//dump($list);exit;
		foreach($list as $k=>$v)
		{
			$list[$k]['come_from']=get_come_from_name($v['come_from_id']);
			$list[$k]['coun']=$factory_order_detailDB->where('`factory_order_id` ='.$v['id'])->count();
			$list[$k]['detail']=$factory_order_detailDB->where('`factory_order_id` ='.$v['id'])->select();
			$list[$k]['fac_num'] = get_factory_str('9',$v['date'],$v['number'],'execl',$order);
			if($list[$k]['order_platform_id']==0 || $list[$k]['order_platform_id']=='')
			{
				$list[$k]['order_number']=strtoupper($list[$k]['come_from']).order_number($list[$k]['order_id'],'order_web');
				$list[$k]['uu']=order_number($list[$k]['order_id'],'order_web');
				$list[$k]['url']=__MODULE__.'/OrderManage/order_web/order_status/all/order_number/'.$list[$k]['uu'].'/come_from_id/'.$v['come_from_id'];
				$order_info = $order_webDB->where('`id` = '.$v['order_id'])->field('message,date_time')->find();
				
				$list[$k]['message_buy'] = $order_info['message'];  //买家留言
				
				$list[$k]['date_time']=$order_info['date_time'];
				
				$list[$k]['message_seller']='';
				$message_seller = M('order_business_message')->where('`order_id` = '.$v['order_id'])->field('message,date_time')->select();
				foreach($message_seller  as $sell_k=>$sell_v)
				{
					$list[$k]['message_seller'].=$sell_k+1 .".<span style='color: blue;'>".$sell_v['message']."</span> - ".date('m-d',$sell_v['date_time'])."<br>";					 //商家留言
				}
				
				
		    }
			else
			{
				$list[$k]['order_number']=strtoupper($list[$k]['come_from']).order_number($list[$k]['order_platform_id'],'order_plat_form');
				$list[$k]['uu']=order_number($list[$k]['order_platform_id'],'order_plat_form');
				$list[$k]['url']=__MODULE__.'/OrderManage/order_plat/order_status/all/order_number/'.$list[$k]['uu'].'/come_from_id/'.$v['come_from_id'];
				
				$order_info = $order_plat_formDB->where('`id` = '.$v['order_platform_id'])->field('message,date_time')->find();
				
				$list[$k]['message_buy'] = $order_info['message'];  //买家留言
				
				$list[$k]['date_time']=$order_info['date_time'];
				
				$list[$k]['message_seller']='';
				$message_seller = M('order_business_message')->where('`order_platform_id` = '.$v['order_platform_id'])->field('message,date_time')->select();
				foreach($message_seller  as $sell_k=>$sell_v)
				{
					$list[$k]['message_seller'].=$sell_k+1 .'.<span style="color: blue;">'.$sell_v['message'] ."</span> - ". date('m-d',$sell_v['date_time'])."<br>";					 //商家留言
				}
				
			}
		}
//		dump($list);exit;
		$list_new_coun = $FactoryOrderModel->where('`status` = "new" and `factory_id` = 9')->count();
		$list_history_coun = $FactoryOrderModel->where('`status` = "history" and `factory_id` = 9')->count();
		$list_web_coun = $FactoryOrderModel->where('`order_platform_id` = 0 and `factory_id` = 9')->count();
		$list_plat_coun = $FactoryOrderModel->where('`order_id` = 0 and `factory_id` = 9')->count();
		
		if(! I('get.detail_sta'))
		{
			$fac_detail_cancel=$factory_order_detailDB->field('factory_order_id')->group('factory_order_id')->where('`status` ="cancel"')->select();
			foreach($fac_detail_cancel as $detail_k => $detail_v)
			{
				$fac_id[$detail_k] = $detail_v['factory_order_id'];
			}
			$id=implode(',',$fac_id);
			if($id)
			{
				$map['id'] =array('in',$id);
			}
			else
			{
				$map['id'] =array('in','-1');
			}
			$map['factory_id']='9';
			
			$coun_cancel=$factory_orderDB->where($map)->count();
		}
		else
		{
			$coun_cancel =count($info);
		}
	//	dump($list);exit;
		$this-> delivery_style = delivery_style(); //快递公司
		$this->page=$show;
		$this->assign("info",$list);
		$this->assign("list_new_coun",$list_new_coun);
		$this->assign("list_history_coun",$list_history_coun);	
		$this->assign("list_web_coun",$list_web_coun);
		$this->assign("list_plat_coun",$list_plat_coun);	
		$this->assign('coun_cancel',$coun_cancel);	
		$this->display();
		
	}
	//洗涤剂订单转历史
	public function xdj_factory_sta()
	{
		$username = session('username');    // 用户名
		$factory_orderDB=M('factory_order');
		$order_delivery_detailDB = M('order_delivery_detail');
		if(IS_POST)
		{
			$delivery_num = I('post.delivery_num');
			$delivery = I('post.delivery');
			
			$sta=I('post.sta');
			if(I('post.id'))
			{
				$whereId['id'] = I('post.id'); 
				
			}
			else
			{
				$this->error('不能选择多选！！');
			}
			if(!I('post.order_id'))
			{
				$this->error('订单ID出现错误！！');	
			}
			
			if( $sta == 'history')
			{
				$date['history_time']=time();
				$date['history_operation']=$username;
			}
			
			
			$date['status']=$sta;
			
			$return=$factory_orderDB->where($whereId)->save($date);
			if($return)
			{
				if($delivery_num)
				{
					$de['style']=$delivery;
					$de['delivery_number']=$delivery_num;
				}
				else
				{
					$de['style']='no';
					$de['delivery_number']='no';
				}
				if(I('post.order_id'))
				{
					$v_id = explode('_',I('post.order_id'));
					$de['order_web_id']=$v_id['0'];
					$de['order_platform_id']=$v_id['1'];
				}
				$de['message']='normal';
				$de['status']='normal';
				$de['time']=date('Y-m-d H:i:s',time());
				$de['sign'] = "XDJ";
				//dump($de);exit;
				$delivery_return = M("order_delivery_detail_xdj")->add($de);
			}
			else
			{
				$this->error('状态改变出现错误！！');
			}
				
			if($return)
			{
				if($sta == 'history')
			    {
			        $factory_order_detail=$factory_orderDB->where($whereId)->select();
			        foreach($factory_order_detail as $key=>$value_factory_order_detail)
			        {
			            check_order_status($value_factory_order_detail["order_id"],$value_factory_order_detail["order_platform_id"]);
			        }	        
			    }
			    $this->success('状态改变成功！！');
			}
			else
			{
				$this->success('状态改变失败！！ ');
			}
		}
	}
	//洗涤剂导出
	public function execl_export_xdj()
	{
		$factory_orderDB = M('factory_order');
		$factory_order_detailDB = M('factory_order_detail');
		if(I('post.check'))
		{
			$id=I('post.check');
			$zz = implode(',', $id); 
			$whereId = 'id in (' . $zz . ')';	
			
			
			$list=$factory_orderDB->where($whereId)->order('id')->select();
			$nnn=1;
			foreach($list as $k=>$v)
			{
				$list[$k]['detail'] = $factory_order_detailDB->where('`factory_order_id` ='.$v['id'])->select();
				if($list[$k]['detail'])
				{
					$info[$nnn]['num'] = $k+1;
					foreach($list[$k]['detail'] as $detail_v)
					{
						$info[$nnn]['time'] =date( 'm月d日',strtotime($v['date']));
						$info[$nnn]['fac_num'] = get_factory_str('9',$v['date'],$v['number'],'execl','web');	
						$info[$nnn]['web_num'] = order_number_full($v['order_id']);
						$info[$nnn]['number'] = $detail_v['number'];
						//买家留言
						$info[$nnn]['buy'] = $detail_v['description'];
						
						$info[$nnn]['email'] = M('order_web')->where('`id`='.$v['order_id'])->getField('email');
						
						$address = M('order_web_address')->where('`order_web_id` ='.$v['order_id'])->find();
						$info[$nnn]['address'] = $daaress['first_name']." ".$address['last_name']." \r\n ".$address['address']." \r\n ".$address['city']." \r\n ".$address['province']." \r\n ".$address['country']." \r\n ".$address['telephone'];
						
						//卖家留言
						$message_seller = M('order_business_message')->where('`order_id` = '.$v['order_id'])->field('message,date_time')->select();
						foreach($message_seller  as $sell_k=>$sell_v)
						{
							$info[$nnn]['message_seller'].=$sell_k+1 .".".$sell_v['message']." - ".date('m-d',$sell_v['date_time'])." \r\n ";					 //商家留言
						}
						if($info[$nnn]['message_seller'] =='')
						{
							$info[$nnn]['message_seller']='';	
						}
						
						$info[$nnn]['country'] = '美国';
						$delivery = M('order_delivery_detail_xdj')->where('`order_web_id` ='.$v['order_id'])->find();
						if($delivery)
						{
							$info[$nnn]['delivery_num'] = $delivery['delivery_number']." ";
							$info[$nnn]['delivery_style'] = $delivery['style'];
						}
						else
						{
							$info[$nnn]['delivery_num'] = "";
							$info[$nnn]['delivery_style'] = '';							
						}
						$nnn++;
					}									
				}
			}
		//	dump($info);exit;
			$title=array('编号','日期','工厂单号','网站订单号','数量','备注留言','邮箱','地址','商家备注','始发地','快递单号','快递公司');
			exportExcel($info,'洗涤剂订单'."-".date('Y-m-d H:i:s',time()),$title);
		}
		else
		{
			$this->error('没有选择订单');	
		}
	}
	//FBA小产品提交
	public function fba_to_factory()
	{
		$fba_order_DB=M("fba_order");
		$fba_order_detail_DB=M("fba_order_detail");
		$factory_order_DB=M("factory_order");
		$factory_order_detail_DB=M("factory_order_detail");
		
		$fba_order_detail=$fba_order_detail_DB->where(array("id"=>$_POST[fba_detail_id]))->find();
		$fba_order=$fba_order_DB->where(array("id"=>$fba_order_detail["fba_order_id"]))->find();
		$is_ok=false;
		if($fba_order)
		{
			$factory_order_id="";
			$isset_factory_id=false;
			$factory_order_exist=$factory_order_DB->where(array("order_id"=>$fba_order["order_id"],"order_platform_id"=>$fba_order["orderplatform_id"]))->select();
			if($factory_order_exist)
			{
				foreach($factory_order_exist as $val)
				{
					if($val[factory_id]==$_POST[factory_id])
					{
						$isset_factory_id=true;
						$factory_order_id=$val[id];
						break;
					}
				}
				
				if($isset_factory_id)
				{
					$add_factory_order_detail=$factory_order_detail_DB->add(array("factory_order_id"=>$factory_order_id,"code"=>$fba_order_detail["code"],"number"=>$fba_order_detail["number"]));
				}
				else
				{
					$date=date("Y-m-d",time());
					$time=date("H:i:s",time());
					$old_number=$factory_order_DB->where(array("factory_id"=>$_POST[factory_id],"date"=>$date))->order("number desc")->field("number")->find();
					if($old_number)
					{
						$number=$old_number[number]+1;
					}
					else
					{
						$number=1;
					}
					$add_factory_order=$factory_order_DB->add(array("order_id"=>$fba_order["order_id"],"order_platform_id"=>$fba_order["orderplatform_id"],"factory_id"=>$_POST[factory_id],"number"=>$number,"date"=>$date,"status"=>$fba_order["status"],"come_from_id"=>$fba_order["come_from_id"],"date_detail"=>$time));
					if($add_factory_order)
					{
						$add_factory_order_detail=$factory_order_detail_DB->add(array("factory_order_id"=>$add_factory_order,"code"=>$fba_order_detail["code"],"number"=>$fba_order_detail["number"]));
					}
				}
				if($add_factory_order_detail) $is_ok=true;
			}
			else
			{
				$date=date("Y-m-d",time());
				$time=date("H:i:s",time());
				$old_number=$factory_order_DB->where(array("factory_id"=>$_POST[factory_id],"date"=>$date))->order("number desc")->field("number")->find();
				if($old_number)
				{
					$number=$old_number[number]+1;
				}
				else
				{
					$number=1;
				}
				$add_factory_order=$factory_order_DB->add(array("order_id"=>$fba_order["order_id"],"order_platform_id"=>$fba_order["orderplatform_id"],"factory_id"=>$_POST[factory_id],"number"=>$number,"date"=>$date,"status"=>$fba_order["status"],"come_from_id"=>$fba_order["come_from_id"],"date_detail"=>$time));
				if($add_factory_order)
				{
				$add_factory_order_detail=$factory_order_detail_DB->add(array("factory_order_id"=>$add_factory_order,"code"=>$fba_order_detail["code"],"number"=>$fba_order_detail["number"]));
				if($add_factory_order_detail) $is_ok=true;
				}
			}
		}
		if($is_ok)
		{
			$del_fba_order_detail=$fba_order_detail_DB->delete($_POST[fba_detail_id]);
			if($del_fba_order_detail)
			{
				$count_fba_order_detail=$fba_order_detail_DB->where(array("fba_order_id"=>$fba_order_detail["fba_order_id"]))->getField("id");
				if(!$count_fba_order_detail)
				{
					$del_fba_order=$fba_order_DB->delete($fba_order_detail["fba_order_id"]);
					if($del_fba_order) $this->success("success");
				}
				else
				{
					$this->success("success");
				}
			}
		}
	}
	
	//工厂收货率 
	public function history_percent()
	{
		$factory_order_detailDB=M('factory_order_detail');
		$factory_orderDB=M('factory_order');
		$factoryDB=M('factory');
		$id_come_fromDB=M('id_come_from');
		$btime = I('post.btime');
		$etime = I('post.etime');
		$factory = I('get.factory');
		
		if(!$factory)
		{
			$factory = I('post.factory');
			if(!$factory)
			{
				$factory='hsf';
			}
		}
		$this-> factory = $factory;
		if($btime!='' && $etime!='')
		{
			$etime_date = strtotime($etime)+86400;
			$btime_date =  strtotime($btime);
			$this->etime = $etime;
			$this->btime = $btime;
		}
		else
		{
			$day = date("Y-m-d",time()+86400);
			$etime_date = strtotime($day);
			$btime_date = $etime_date - 86400*15;
			//$map['date'] = array(array('EGT',$btime),array('ELT',date('Y-m-d',$etime_date)));
		}
		$time_k=0;
		for($i=$btime_date;$i<$etime_date;$i=$i+86400)
		{
			$time[$time_k] = date('Y-m-d',$i);
			$time_k++;
		}
		//print_r($time);
		$map_factory['val']=$factory;
		$factory_id = $factoryDB->where($map_factory)->getField('id');
		foreach($time as $k=>$v)
		{
			$map[$k]['date'] = $v;
			$map[$k]['status']= array('in','history,history_ok');
			$map[$k]['factory_id']=$factory_id;
			$list[$k] = $factory_orderDB->where($map[$k])->select();
			$list_coun[$k] = count($list[$k]);
			foreach($list[$k] as $list_k => $list_v)
			{
				$action_time = strtotime($list_v['date'].' '.$list_v['date_detail']);
				$result = $list_v['history_time']-$action_time;
				
				if($result<=60*60*24)
				{
					$one[$k] ++;	
				}
				elseif($result<=60*60*36)
				{
					$two[$k] ++;	
				}
				elseif($result<=60*60*48)
				{
					$three[$k] ++;	
				}
				elseif($result<=60*60*60)
				{
					$fone[$k] ++;	
				}
				elseif($result<=60*60*72)
				{
					$five[$k] ++;	
				}
				else
				{
					$six[$k] ++;	
				}
			}
			if(!$one[$k])
			{
				$one[$k]=0;
			}
			if(!$two[$k])
			{
				$two[$k]=0;
			}
			if(!$three[$k])
			{
				$three[$k]=0;
			}
			if(!$fone[$k])
			{
				$fone[$k]=0;
			}
			if(!$five[$k])
			{
				$five[$k]=0;
			}
			if(!$six[$k])
			{
				$six[$k]=0;
			}
			$info[$k]['time'] = $v;
			$info[$k]['coun'] = $list_coun[$k]  ;
			$info[$k]['one'] = round($one[$k] / $list_coun[$k]  , 4 )*100 . '%';
			$info[$k]['two'] = round($two[$k] / $list_coun[$k]  , 4 )*100 . '%';
			$info[$k]['three'] = round($three[$k] / $list_coun[$k]  , 4 )*100 . '%';
			$info[$k]['fone'] = round($fone[$k] / $list_coun[$k]  , 4 )*100 . '%';
			$info[$k]['five'] = round($five[$k] / $list_coun[$k]  , 4 )*100 . '%';
			$info[$k]['six'] = round($six[$k] / $list_coun[$k]  , 4 )*100 . '%';
			
			if(!I('get.operate'))
			{
				$info[$k]['one_coun'] = $one[$k];
				$info[$k]['two_coun'] = $two[$k] ;
				$info[$k]['three_coun'] = $three[$k];
				$info[$k]['fone_coun'] = $fone[$k];
				$info[$k]['five_coun'] =$five[$k] ;
				$info[$k]['six_coun'] = $six[$k] ;
			}
			//dump($info);exit;
		}
		
		if($btime!='' && $etime!='')
		{
			$where_all['date'] = array(array('EGT',$btime),array('ELT',date('Y-m-d',$etime_date)));
			$where_all['factory_id'] = $factory_id;
			$where_all['status']= array('in','history,history_ok');
			$list_all = $factory_orderDB->where($where_all)->select();	
			//dump($list_all);exit;	
			$one_all = 0;
			$two_all = 0;
			$three_all = 0;
			$fone_all = 0;
			$five_all = 0;
			$six_all  = 0;
			foreach($list_all as $list_all_k => $list_all_v)
			{
				$action_time = strtotime($list_all_v['date'].' '.$list_all_v['date_detail']);
				$result = $list_all_v['history_time'] - $action_time ;
				if($result<=60*60*24)
				{
					$one_all  ++;	
				}
				elseif($result<=60*60*36)
				{
					$two_all  ++;	
				}
				elseif($result<=60*60*48)
				{
					$three_all  ++;	
				}
				elseif($result<=60*60*60)
				{
					$fone_all  ++;	
				}
				elseif($result<=60*60*72)
				{
					$five_all  ++;	
				}
				else
				{
					$six_all  ++;	
				}
			}
			$info_all['time'] = $btime." - ".$etime;
			$info_all['coun'] = count($list_all ) ;
			$info_all['one'] = round($one_all/  count($list_all )  , 4 )*100 . '%';
			$info_all['two'] = round($two_all /  count($list_all )  , 4 )*100 . '%';
			$info_all['three'] = round($three_all /  count($list_all )  , 4 )*100 . '%';
			$info_all['fone'] = round($fone_all /  count($list_all )  , 4 )*100 . '%';
			$info_all['five'] = round($five_all /  count($list_all )  , 4 )*100 . '%';
			$info_all['six'] = round($six_all /  count($list_all )  , 4 )*100 . '%';
			
			
			
			if(!I('get.operate'))
			{
				$info_all['one_coun'] = $one_all ;
				$info_all['two_coun'] = $two_all  ;
				$info_all['three_coun'] = $three_all ;
				$info_all['fone_coun'] = $fone_all ;
				$info_all['five_coun'] =$five_all  ;
				$info_all['six_coun'] = $six_all  ;
			}
		//	dump($info_all);exit;
			$this->info_all =$info_all;
		}
		
		if(I('get.operate'))
		{	
			$info_all_one=array();
			if($info_all)
			{
				$info_all_one['0']= $info_all;
			}
			$info = array_merge($info_all_one,$info);
		//	dump($info);exit;
			$title=array('时间','总数','24小时','36小时','48小时','60小时','72小时','大于72小时');
			
			exportExcel($info,$factory.'工厂收货率'."-".date('Y-m-d H:i:s',time()),$title);
		}
		else
		{
		//	dump($info);exit;
			$this->info =$info;
			$factory_list = $factoryDB ->where('1=1')->select();
			$this->factory_list = $factory_list;
			$this->display();
		}
		
	}
	
}