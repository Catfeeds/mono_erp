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
class FactoryOrderController extends CommonController
{
	//单品库存订单
	public function factory_order_dp()
	{
		//库存申请列表	
		$userDB = M('user');
		$username = session('username');    // 用户名
		$productstockDB=M('product_stock');      //库存对比预警表
		$idproductcode=M('id_product_code');    //产品信息
		$productstockcomeDB=M('product_stock_come');  //单品库存申请表
		$productstocksetcomeDB=M('product_stock_set_come');  //套件库存申请表
		$factoryacceptDB=M('factory_accept');        //工厂订单表
		
		//单品未接
		$info=$productstockcomeDB->where("`status`= 1 ")->select();
		if($info)
		{
			foreach($info as $k=>$v)
			{
				$code_id = $idproductcode->where('`id`='.$v['code_id'])->find();
				$info[$k]['code_name']=$code_id['name'];  //申请物品名称
				$info[$k]['sty'] = panduan_style($v['style']);
			}
		}
		//单品库存已接单列表
		//import('Org.Util.Page');// 导入分页类
		$coun=$factoryacceptDB->where("`product_stock_come_id`!= 0 and `accept`="."'".$username."'")->count();
        $Page       = new \Think\Page1($coun);// 实例化分页类 传入总记录数
		$nowPage = isset($_GET['p'])?$_GET['p']:1;
        $show       = $Page->show();// 分页显示输出
		
		$info01=$factoryacceptDB->where("`product_stock_come_id`!= 0 and `accept`="."'".$username."'")->order('begin_time,id,end_time')->page($nowPage ,C('LISTROWS'))->select();//工厂订单表	
		if($info01){
			foreach($info01 as $k=>$v)
			{
				$prostockcome[$k]=$productstockcomeDB->where('`id`='.$v['product_stock_come_id'])->find();//申请表格信息  获得code_id
				$info_come[$k]= $idproductcode->where('`id`='.$prostockcome[$k]['code_id'])->find();
				$prostockcome[$k]['name']= $info_come[$k]['name'];
				$prostockcome[$k]['fac_id']= $info01[$k]['id'];
				$prostockcome[$k]['sty']= panduan_style($prostockcome[$k]['style']);
			}
		}
		$this->assign('page',$show);
		$this->assign('list',$info);
		$this->assign('prostockcome_dp',$prostockcome);
		$this->display();
	}
	//套件库存订单
	public function factory_order_tj()
	{
		//库存申请列表	
		$userDB = M('user');
		$username = session('username');    // 用户名
		$productstockDB=M('product_stock');      //库存对比预警表
		$idproductcode=M('id_product_code');    //产品信息
		$productstockcomeDB=M('product_stock_come');  //单品库存申请表
		$productstocksetcomeDB=M('product_stock_set_come');  //套件库存申请表
		$factoryacceptDB=M('factory_accept');        //工厂订单表
		
		//套件未接
		$info_set=$productstocksetcomeDB->where("`status`= 1 ")->select();
		$info_set_coun=$productstocksetcomeDB->where("`status`= 1 ")->count();
		if($info_set)
		{
			foreach($info_set as $k=>$v)
			{
				$info_set[$k]['sty'] = panduan_style($v['style']);
				$info_set[$k]['sku_name'] = panduan_sku_name($v['sku_id']);
			}
		}
		//import('Org.Util.Page');// 导入分页类
		$coun=$factoryacceptDB->where("`product_stock_set_come_id`!= 0  and `accept`="."'".$username."'")->count();
        $Page       =new \Think\Page1($coun);// 实例化分页类 传入总记录数
		$nowPage = isset($_GET['p'])?$_GET['p']:1;
        $show       = $Page->show();// 分页显示输出s
	
		//套件库存已接单列表
		$info_tj=$factoryacceptDB->where("`product_stock_set_come_id`!= 0  and `accept`="."'".$username."'")->order('begin_time,id,end_time')->page($nowPage,C('LISTROWS'))->select();//工厂订单表	
		if($info_tj)
		{
			foreach($info_tj as $k=>$v)
			{
				$prostockcome_tj[$k]=$productstocksetcomeDB->where('`id`='.$v['product_stock_set_come_id'])->find();//申请表格信息  获得code_id
				$info_come_tj[$k]= $idproductcode->where('`id`='.$prostockcome_tj[$k]['sku_id'])->find();
				$prostockcome_tj[$k]['fac_id']= $info_tj[$k]['id'];
				$prostockcome_tj[$k]['sty']= panduan_style($prostockcome_tj[$k]['style']);
				$prostockcome_tj[$k]['sku_name']= panduan_sku_name($prostockcome_tj[$k]['sku_id']);
			}
			
		}
		$this->assign('page',$show);
		$this->assign('info_set',$info_set);
		$this->assign('prostockcome_tj',$prostockcome_tj);
		$this->display();
	}
	//接收单品订单
	public function factory_order_start()
	{
		$userDB = M('user');
		//库存申请列表	
		$userDB = M('user');
		$username = session('username');    // 用户名
		$productstockDB=M('product_stock');      //库存对比预警表
		$idproductcode=M('id_product_code');    //产品信息
		$productstockcomeDB=M('product_stock_come');  //库存申请表
		$factoryacceptDB=M('factory_accept');        //工厂订单表
		$productstockcomechangeDB=M('product_stock_come_check');  //库存修改记录
		if($_POST['dosubmit'])
		{
			//productstockcome  库存申请数据表 显示工厂接收
			$id=$_POST['product_stock_come_id'];
			$date['status']=2;
			$date['change_time']=time();
			$date['remark'] = $_POST['remark'];
			$pro=$productstockcomeDB->where('`id`='.$id)->save($date);
			
			//写入记录
			$date['inspector']= $username ;
			$date['product_stock_come_id'] = $id;
			$date['code_id']=$_POST['code_id'];
			$date['date_time']=time();
			$productstockcomechangeDB->add($date);                   //productstockchange  写记录 
			 
			//factoryaccept 工厂记录数据表  工厂记录 
			$data['product_stock_come_id']=$_POST['product_stock_come_id'];
			$data['accept'] = $username;
			$data['begin_time'] = $_POST['begin_time'];
			$data['end_time'] = $_POST['end_time'];
			$data['remark'] = $_POST['remark'];
			$fac=$factoryacceptDB->add($data);
			if($fac)
			{
				$this->assign("jumpUrl",U('/Admin/FactoryOrder/factory_order_dp'));
				$this->success('订单接收成功！');
			}
			else
			{
				$this->error('订单接收失败!');	
			}
		}
		else
		{
			if($_GET['id'])
			{
				$id=$_GET['id'];
				$info=$productstockcomeDB->where('`id`='.$id)->find();
				if($info)
				{
						$code_id = $idproductcode->where('`id`='.$info['code_id'])->find();
						$info['code_name']=$code_id['name'];  //申请物品名称
						$info['sty']=panduan_style($info['style']);  //申请物品类型
				}
				$this->assign('info',$info);
			}
			$this->assign('tpltitle','接收');
			$this->assign('username',$username);
			$this->display();
		}
	}
	//接收套件订单
	public function factory_set_order_start()
	{
		$userDB = M('user');
		//库存申请列表	
		$userDB = M('user');
		$username = session('username');    // 用户名
		$productstockDB=M('product_stock');      //库存对比预警表
		$idproductcode=M('id_product_code');    //产品信息
		$productstocksetcomeDB=M('product_stock_set_come');  //套件库存申请表
		$factoryacceptDB=M('factory_accept');        //工厂订单表
		$productstockcomecheckDB=M('product_stock_come_check');  //库存修改记录 
		if($_POST['dosubmit'])
		{
			//productstocksetcome  库存申请数据表 显示工厂接收
			$id=$_POST['product_stock_set_come_id'];
			$date['status']=2;
			$date['change_time']=time();
			$date['remark'] = $_POST['remark'];
			$pro=$productstocksetcomeDB->where('`id`='.$id)->save($date);
			
			//写入记录
			$date['inspector']= $username;
			$date['date_time']=time();
			$date['product_set_stock_come_id'] = $id;
			$date['sku_id']=$_POST['sku_id'];
			$productstockcomecheckDB->add($date);                   //product_stock_come_check  写记录 
			
			//factoryaccept 工厂记录数据表  工厂记录 
			$data['product_stock_set_come_id']=$_POST['product_stock_set_come_id'];
			$data['accept'] = $username;
			$data['begin_time'] = $_POST['begin_time'];
			$data['end_time'] = $_POST['end_time'];
			$data['remark'] = $_POST['remark'];
			$fac=$factoryacceptDB->add($data);
			if($fac)
			{
				$this->assign("jumpUrl",U('/Admin/FactoryOrder/factory_order_tj'));
				$this->success('套件订单接收成功！');
			}
			else
			{
				$this->error('套件订单接收失败!');	
			}
		}
		else
		{
			
			if($_GET['id'])
			{
				$id=$_GET['id'];
				$info=$productstocksetcomeDB->where('`id`='.$id)->find();
				$info['sku_name']=panduan_sku_name($info['sku_id']);
				$info['sku_val']=panduan_sku_val($info['sku_id']);
				$info['sty']=panduan_style($info['style']);  //申请物品类型
				$this->assign('info',$info);
				$this->assign('tpltitle','接收');
			}
			if($_GET['data'])
			{
				$this->assign('tpltitle','查看');
				$this->assign('data','1');
			}	
			$this->assign('username',$username);
			$this->display();
		}
	}	
	//完成订单
	public function factory_order_end()
	{
		$id=$_GET['id'];
		//工厂订单数据表修改
		$username = session('username');    // 用户名
		$idproductcode=M('id_product_code');    //产品信息
		$productstockcomeDB=M('product_stock_come');  //库存申请表
		$productstocksetcomeDB=M('product_stock_set_come');  //套件库存申请表
		$factoryacceptDB=M('factory_accept');        //工厂订单表
		$productstockcomechangeDB=M('product_stock_come_check');  //库存修改记录
		
		//工厂订单表写入记录
		$date['actual_end_time']=time();
		$fac=$factoryacceptDB->where('`id`='.$id)->save($date); 
		  
		//订单申请数据表修改
		$factoryacceptDB_v=$factoryacceptDB->where('`id`='.$id)->find(); 
		
		$data['status'] = 3;
		$data['actual_end_time'] =time();
		$data['change_time'] =time();
		if($_GET['data'])
		{
			$productstocksetcome_id=$factoryacceptDB_v['product_stock_set_come_id'];
			$pro=$productstocksetcomeDB->where('`id`='.$productstocksetcome_id)->save($data);   
		}
		else
		{
			$productstockcome_id=$factoryacceptDB_v['product_stock_come_id'];
			$pro=$productstockcomeDB->where('`id`='.$productstockcome_id)->save($data);   
		}
		
		
		//写入记录
		$date['inspector']= $username;
		if($_GET['data'])
		{
			$date['product_set_stock_come_id'] = $productstocksetcome_id;
		}
		else
		{
			$date['product_stock_come_id'] = $productstockcome_id;
		}
		$date['date_time'] =time();
		$date['status'] = 3;
		$productstockcomechangeDB->add($date);                   //productstockchange  写记录 
			
		if($pro)
		{
			$this->success('订单完成操作成功！');
		}
		else
		{
			$this->error('订单完成操作失败!');	
		}
	}
	
	
		
	//工厂订单
	public function factory()
	{
		$factory_order_detailDB=M('factory_order_detail');
		$factory_order_detail_messageDB = M('factory_order_detail_message');
		$factoryDB=M('factory');
		$id_come_fromDB=M('id_come_from');
		
		$product_stock_orderDB = M('product_stock_order');
		$fba_orderDB = M('fba_order');
		$factory_orderDB = M('factory_order');
		
		//批量操作  判断状态
		
		if(I('get.type'))
		{
			$type=I('get.type');
			$map_factory['val']=$type;
			$factory_id = $factoryDB->where($map_factory)->getField('id');
			$this->assign('type',strtoupper($type));
			$where="`factory_id` ="."'".$factory_id."'"."  and" ;
			$map['factory_id']=$factory_id;
		}
		
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
				$map['id'] =array('in',$id);
			}
			else
			{
				$map['id'] =array('in','-1');
			}
			$this->detail_sta =I('get.detail_sta');
		}
		else
		{
			if(I('get.sta')=='' || !I('get.sta') || I('get.sta')=="new" )
			{
				$input_sta='accept';
				$input_sta_next ='';
			}
			elseif(I('get.sta')=='accept')
			{
				$input_sta='shipping';
			}
			elseif(I('get.sta')=='shipping')
			{
				$input_sta='history';
			}
			elseif(I('get.sta')=='history')
			{
				$input_sta='history_ok';
			}
			$this->assign('input_sta',$input_sta);
			//批量操作 end
			
			if(I('get.order_num'))
			{
				$order_num_where['order_number'] = trim(I('get.order_num'));
				$order_wed_id = M('order_web')->where($order_num_where)->getField('id');
				if($order_wed_id)
				{
					$map['order_id'] = $order_wed_id;
				}
				else
				{
					$order_platform_id = M('order_plat_form')->where($order_num_where)->getField('id');
					$map['order_platform_id'] = $order_platform_id;
				}
				
				$this->order_num = I('get.order_num');
				$this->sta = 'get';			
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
				
				$map['date'] = date("Y",time())."-".$fac_time[0]."-".$fac_time[1];
				if($str_num>9)
				{
					$day_num = $str_num;
				}
				else
				{
					$day_num = substr($str_num,1);   //      number
				}
				$map['number']  = $day_num;
				
				$this->fac_num = I('get.fac_num');
				$this->sta = 'get';
			}
			else
			{
				if(I('get.sta')!='')
				{
					$map['status']=I('get.sta');
					$this->assign('sta',I('get.sta'));
				}
				else
				{
					$map['status']='new';
					$this->assign('sta','new');
				}
			
				if(I('get.delivery_number'))
				{
					$map['delivery_number'] = I('get.delivery_number');
					$this->delivery_number = I('get.delivery_number');
				}
			}
		}
	//	dump($map);exit;
	//	import('Org.Util.Page');// 导入分页类
		$coun=$factory_orderDB->where($map)->count();
		$Page       = new \Think\Page1($coun);// 实例化分页类 传入总记录数
		$nowPage = isset($_GET['p'])?$_GET['p']:1;
		$show       = $Page->show();// 分页显示输出
		$info=$factory_orderDB->where($map)->order('id desc')->page($nowPage.','.C('LISTROWS'))->select();
		//print_r($info);exit;
		foreach($info as $k=>$v)
		{
			$info[$k]['come_from']=$id_come_fromDB->where('`id` ='.$info[$k]['come_from_id'])->getField('name');
			if($info[$k]['order_platform_id']==0 || $info[$k]['order_platform_id']=='')
			{
				$info[$k]['order_number']=strtoupper($info[$k]['come_from']).order_number($info[$k]['order_id'],'order_web');
				$info[$k]['uu']=order_number($info[$k]['order_id'],'order_web');
				$info[$k]['url']=__MODULE__.'/OrderManage/order_web/order_status/all/order_number/'.$info[$k]['uu'].'/come_from_id/'.$info[$k]['come_from_id'];
				$info[$k]["is_come"]="web";
		    }
			else
			{
				$info[$k]['order_number']=strtoupper($info[$k]['come_from']).order_number($info[$k]['order_platform_id'],'order_plat_form');
				$info[$k]['uu']=order_number($info[$k]['order_platform_id'],'order_plat_form');
				$info[$k]['url']=__MODULE__.'/OrderManage/order_plat/order_status/all/order_number/'.$info[$k]['uu'].'/come_from_id/'.$info[$k]['come_from_id'];
				$info[$k]["is_come"]="plat";
			}
			$info[$k]['factory_number']=get_factory_str($v['factory_id'],$v['date'],$v['number'],"execl",$info[$k]["is_come"]);
			
			$info[$k]['detail']=$factory_order_detailDB->where('`factory_order_id` ='.$info[$k]['id'])->select();
			foreach($info[$k]['detail'] as $detail_k => $detail_v){
				$info[$k]['detail'][$detail_k]['message']=$factory_order_detail_messageDB->where('`factory_order_detail_id` ='.$detail_v['id'])->order('time desc')->select();
			}
			$info[$k]['coun']=$factory_order_detailDB->where('`factory_order_id` ='.$info[$k]['id'])->count();
			if($v['factory_id']!="7" && $v['factory_id']!="8")//是判断定制
			{
				foreach($info[$k]['detail'] as $kk=>$vv)
				{
					$info[$k]['detail'][$kk]['code_name'] = code_val($info[$k]['detail'][$kk]['code']);
				}
			}
			$info[$k]['delivery_style'] =$v['delivery_style'];
			$info[$k]['delivery_number'] = $v['delivery_number'];
		}
	//	dump($info);exit;
		$coun_new=$factory_orderDB->where($where.' `status` = "new"')->count();
		$coun_accept=$factory_orderDB->where($where.'  `status` = "accept"')->count();
		$coun_shipping=$factory_orderDB->where($where.'  `status` = "shipping"')->count();
		$coun_history_ok=$factory_orderDB->where($where.'  `status` ="history_ok"')->count();
		
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
			$map['factory_id']=$factory_id;
			$coun_cancel=$factory_orderDB->where($map)->count();
		}
		else
		{
			$coun_cancel =count($info);
		}
		
		
		
		$coun_history=$factory_orderDB->where($where.'  `status` ="history"')->count();
		
		$factory_list = $factoryDB->where('1=1')->select();
		$this->factory_list=$factory_list;
		$this->assign('coun_new',$coun_new);
		$this->assign('coun_accept',$coun_accept);
		$this->assign('coun_shipping',$coun_shipping);
		$this->assign('coun_history',$coun_history);
		$this->assign('coun_history_ok',$coun_history_ok);
		$this->assign('coun_cancel',$coun_cancel);
		$this->assign('page',$show);
		$this->assign('info',$info);
		$this->display();
	}
	//添加给工厂留言
	public function factory_order_detail_message_add()
	{
		$factory_order_detail_messageDB = M('factory_order_detail_message');
		if(IS_POST)
		{
			$id = I('post.id');
			$message = I('post.message');
			$date['factory_order_detail_id'] = $id;
			$date['message'] = $message;
			$date['operator'] =session('username');
			$date['time'] =time();
			$retuen = $factory_order_detail_messageDB->add($date);
			if($retuen)
			{
				$this->success('添加留言成功！');
			}
			else
			{
				$this->error('添加留言失败！');
			}
		}
		else
		{
			$this->error('出现错误');	
		}
	}
	//导出execl文档
	public function execl_export()
	{
		$username = session('username');    // 用户名
		$factory_order_detailDB=M('factory_order_detail');
		$factory_orderDB=M('factory_order');
		$factoryDB=M('factory');
		$id_come_fromDB=M('id_come_from');
		$id_product_codeDB = M('id_product_code');
		$id_product_attributeDB =M('id_product_attribute');
		$order_web_addressDB = M('order_web_address');
		$order_plat_form_shippingDB = M('order_plat_form_shipping');
		$fba_orderDB = M('fba_order'); 
		$product_stock_orderDB = M('product_stock_order');
		$factory_order_detail_messageDB = M('factory_order_detail_message');
		if(IS_POST)
		{
			if(I('post.check')=="")
			{
			   $this->error('你没有做出选择');	
			}
			$execl_sta=I('post.execl_sta');
			$id=I('post.check');
			$zz = implode(',', $id); 
            $whereId = 'id in (' . $zz . ')'; 
			$factory_type=I('post.factory_type');//订单工厂
			$map_factory['val']=$factory_type;
			$factory_id = $factoryDB->where($map_factory)->getField('id');	
			//区分网站平台
			$where_plat = $whereId." and `order_platform_id`!=0";
			$info_plat = $factory_orderDB->where($where_plat)->order('date,date_detail')->select();
			$where_web = $whereId." and `order_id`!=0";
			$info_web = $factory_orderDB->where($where_web)->order('date,date_detail')->select();
			
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
			//区分网站平台  end
			//$info=$factory_orderDB->where($whereId)->order('id desc')->select();
			$nnn=0;
			//dump($info);exit;
			foreach($info as $k=>$v)
			{
				$info[$k]['detail']=$factory_order_detailDB->where('`factory_order_id` ='.$v['id']." and `status` !='cancel'")->select();
				if($info[$k]['detail'])
				{
					$come_from = $id_come_fromDB->where('`id` ='.$info[$k]['come_from_id'])->getField('name');
					//dump($come_from);
					if($v['order_platform_id']!=0)
					{
						$fac_num = associated_fac($v['id'],$v['order_platform_id'],'fac',1,'rn','plat');
					}
					elseif($v['order_id']!=0)
					{
						$fac_num = associated_fac($v['id'],$v['order_id'],'fac',1,'rn','web');
					}
					$list[$nnn]['fac_number'] = $fac_num;
					
					foreach($info[$k]['detail'] as $kk=>$vv)
					{
						//$list[$nnn]['date_time'] = $v['date'];
						if($kk>0)
						{
							$list[$nnn]['fac_number'] ="";
						}
						
						if($info[$k]['detail'][$kk]['code'] != 'customization' and $info[$k]['detail'][$kk]['code'] != 'ncustomization')
						{
							$list[$nnn]['code_name'] = code_val($info[$k]['detail'][$kk]['code']);
							
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
							/*  size color 弃用
							$code_val = $id_product_codeDB->field('size_id,color_id')->where('`code` = "'.$vv['code'].'"')->find();
		
							$list[$nnn]['size'] = $id_product_attributeDB->where('`id` = '.$code_val['size_id'])->getField('value');
							
							$list[$nnn]['color'] = $id_product_attributeDB->where('`id` = '.$code_val['color_id'])->getField('value');
							*/
						}
						else
						{
							$list[$nnn]['code_name'] ='定制';
							$list[$nnn]['size'] ='定制';
							$list[$nnn]['color'] ='定制'; 
						}
						$list[$nnn]['num'] = $vv['number'];
						
						$list[$nnn]['description'] = $vv['description'];
						
						
						if($v['order_platform_id']!=0)
						{
							$list[$nnn]['order_number']=order_number($v['order_platform_id'],'order_plat_form').' ';	//交易单号
							$order_address =$order_plat_form_shippingDB->where('`order_platform_id`='.$v['order_platform_id'])->group('name,country')->find();
							
							$list[$nnn]['logo'] = 'Lilysilk';
							$list[$nnn]['come_from'] =$come_from;
							$list[$nnn]['name'] = $order_address['name'];
							
							//买家留言
							$list[$nnn]['message_buy'] = M('order_plat_form')->where('`id` = '.$v['order_platform_id'])->getField('message');  //买家留言
							$list[$nnn]['message_seller']='';
							
							//卖家留言
							$message_seller = M('order_business_message')->where('`order_platform_id` = '.$v['order_platform_id'])->field('message,date_time')->select();
							foreach($message_seller  as $sell_k=>$sell_v)
							{
								$list[$nnn]['message_seller'].=$sell_k+1 .".".$sell_v['message']."-".date('m-d',$sell_v['date_time'])." , ";
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
							//卖家留言
							$message_seller = M('order_business_message')->where('`order_id` = '.$v['order_id'])->field('message,date_time')->select();
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
							//dump($message_seller);exit;
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
						
						
						
						//给工厂留言
						$fac_detail_message=$factory_order_detail_messageDB->field('message,time')->where('`factory_order_detail_id` ='.$vv['id'])->order('time desc')->select();
						//附加产品
						if($v['order_id']!=0)
						{
							$list[$nnn]['fac_detail_message'] = product_extra($v['order_id']);
							if($list[$nnn]['fac_detail_message'] !='')
							{
								$list[$nnn]['fac_detail_message'].=" \r\n ";
							}
						}
						
						if($fac_detail_message)
						{
							foreach($fac_detail_message as $detail_message_k => $detail_message_v){
								if($detail_message_k !=0)
								{
									$list[$nnn]['fac_detail_message'].=" \r\n ";
								}
								$list[$nnn]['fac_detail_message'].=$detail_message_k+1 .".".$detail_message_v['message']." - ".date('Y-m-d',$detail_message_v['time']);
							}
						}
						if(!$list[$nnn]['fac_detail_message'])
						{
							$list[$nnn]['fac_detail_message']="";
						}
						//是否有样布
						if($v['order_id']!=0)
						{
							$list[$nnn]['sample'] = panduan_sample($v['order_id'],$v['come_from_id']);
						}
						else
						{
							$list[$nnn]['sample'] ='';
						}
						$nnn++;
					}
				}
				
			}
			$title=array('工厂单号','品名','规格','颜色','数量','备注','网站单号','商标','网站','客户名称','买家留言','商家留言','国家','code','SKU',"特别注意",'是否有样布');
			
	//		dump($list);exit;
			if(!$list)
			{
				$this->error('指定的单号没有可以导出的');	
			}
			if($execl_sta =='new')
			{
				exportExcel($list,$factory_type.'工厂新订单'."-".date('Y-m-d H:i:s',time()),$title);
			}
			elseif($execl_sta =='accept')
			{
				exportExcel($list,$factory_type.'工厂已接订单'."-".date('Y-m-d H:i:s',time()),$title);
			}
			elseif($execl_sta =='shipping')
			{
				exportExcel($list,$factory_type.'工厂正在派送订单'."-".date('Y-m-d H:i:s',time()),$title);
			}
			elseif($execl_sta =='ok')
			{
				exportExcel($list,$factory_type.'已收到货订单'."-".date('Y-m-d H:i:s',time()),$title);
			}
			else
			{
				exportExcel($list,$factory_type.'筛选订单'."-".date('Y-m-d H:i:s',time()),$title);
			}
			
		}	   
	}
	
	//工厂订单状态改变     
	public function factory_sta()
	{
		$username = session('username');    // 用户名
		$factory_orderDB=M('factory_order');
		if(IS_POST)
		{
			if(I('get.sta'))
			{
				$sta=I('get.sta');	
			}
			else
			{
				$sta=I('post.sta');	
			}
			
			if(I('post.id'))
			{
				$whereId['id'] = I('post.id'); 
			}
			else
			{
				if(I('post.check')=="")
				{
				   $this->error('你没有做出选择');	
				}
				$id=I('post.check');		
				$aa = implode(',', $id); 
				$whereId = 'id in (' . $aa . ')'; 
			} 
			
			if($sta == 'accept')
			{
				$date['accept_time']=time();
				$date['accept_operation']=$username;
			}
			elseif( $sta == 'shipping')
			{
				$delivery_num = I('post.delivery_num');
				$delivery = I('post.delivery');
				
				$date['shipping_time']=time();
				$date['shipping_operation']=$username;
				if($delivery_num)
				{
					$date['delivery_style']=$delivery;
					$date['delivery_number']=$delivery_num;
				}
				else
				{
					$date['delivery_style']='no';
					$date['delivery_number']='no';
				}
			}
			
			elseif( $sta == 'history')
			{
				$date['history_time']=time();
				$date['history_operation']=$username;
			}
			elseif( $sta == 'history_ok')
			{
				$date['ok_time']=time();
				$date['ok_operation']=$username;
			}
			$date['status']=$sta;
			$return=$factory_orderDB->where($whereId)->save($date);
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
		elseif(IS_GET)
		{
			$sta=I('get.sta');
			if(I('get.id'))
			{
				$whereId['id'] = I('get.id'); 
			}
			if($sta == 'accept')
			{
				$date['accept_time']=time();
				$date['accept_operation']=$username;
			}
			elseif( $sta == 'shipping')
			{
				$date['shipping_time']=time();
				$date['shipping_operation']=$username;
				if($delivery_num)
				{
					$date['delivery_style']=$delivery;
					$date['delivery_number']=$delivery_num;
				}
				else
				{
					$date['delivery_style']='no';
					$date['delivery_number']='no';
				}
			}
			
			elseif( $sta == 'history')
			{
				$date['history_time']=time();
				$date['history_operation']=$username;
				
				
			}
			elseif( $sta == 'history_ok')
			{
				$date['ok_time']=time();
				$date['ok_operation']=$username;
			}
			
			$date['status']=$sta;
			$return=$factory_orderDB->where($whereId)->save($date);
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
	//小产品转工厂
	public function small_transform_form_submit()
	{
		$factory_orderDB=M('factory_order');
		$factory_order_detailDB = M('factory_order_detail');
		$succ_num =0;
		$err_num = 0;
		if(I('post.detail_id')=="" || I('post.factory_list')=="")
		{
		   $this->error('你没有做出选择');	
		}
		$factory = I('post.factory_list');
		$detail_id = I('post.detail_id');
		$detail = explode('_',$detail_id);
	//	dump($detail);exit;
		$time= date('Y-m-d',time());
		
		$factory_val = $factory_orderDB->where('`id` = '.$detail[1])->find(); //查询主表
		
		$whereId['factory_id'] =  $factory;
		$whereId['order_id'] =  $factory_val['order_id'];
		$whereId['order_platform_id'] = $factory_val['order_platform_id'];
		$whereId['status'] = 'new';
		$factory_new = $factory_orderDB->where($whereId)->find(); //判断是否有今日 切状态为new的订单
		
		if($factory_new)
		{
			$date['factory_order_id'] = $factory_new['id'];	
			$return = $factory_order_detailDB->where('`id` = '.$detail[0])->save($date);
			
			$delete_pd = $factory_order_detailDB->where('`factory_order_id` = '.$detail[1])->find();
			//dump($delete_pd);exit;
			if(!$delete_pd)
			{
				$factory_orderDB->where('`id` = '.$detail[1])->delete();
			}
		}
		else
		{
			$factory_num = $factory_orderDB->where('`factory_id` = "'.$factory .'" and date = "'.$time.'"')->order('number desc')->getField('number');
			if(!$factory_num)
			{
				$factory_num =0;
			}
			
			$whereId['number'] = $factory_num + 1;
			$whereId['date'] = $time;
			$whereId['come_from_id'] = $factory_val['come_from_id'];
		//	dump($whereId);exit;
			$fac_order_add = $factory_orderDB->add($whereId);
			
			$date['factory_order_id'] = $fac_order_add;	
			$return = $factory_order_detailDB->where('`id` = '.$detail[0])->save($date);
			
			$delete_pd = $factory_order_detailDB->where('`factory_order_id` ='.$detail[1])->find();
		//	dump($delete_pd);exit;
			if(!$delete_pd)
			{
				$factory_orderDB->where('`id` = '.$detail[1])->delete();
			}
		}
		if($return)
		{
			 $this->success('工厂改变成功！！');
		}
		else
		{
			$this->error('工厂改变失败！！');
		}
	}
	
	//批量转工厂
	public function transform_form_submit()
	{
		$factory_orderDB=M('factory_order');
		$succ_num =0;
		$err_num = 0;
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
			$factory_num ++;
			$date['factory_id']  = $factory ;
			$date['date'] = $time;
			$date['number'] = $factory_num ;
			$whereId['id'] = $v;
			$return = $factory_orderDB->where($whereId)->save($date);
			if($return)
			{
				 $succ_num++;
			}
			else
			{
				$err_num++;
			}
 		}
		if($err_num ==0)
		{
			 $this->success('工厂改变成功！！');
		}
		else
		{
			$this->error('失败'.$err_num."个");
		}
	}
	
	//未到货列表
	public function not_arrival_order()
	{
		$factoryDB = M('factory');
		$factory_orderDB = M('factory_order');
		$factory_order_detailDB = M('factory_order_detail');
		$id_come_fromDB = M('id_come_from');
		$id_product_codeDB = M('id_product_code');
		$id_product_attributeDB = M('id_product_attribute');
		
		//工厂列表
		$factory_list = $factoryDB->where('1=1')->select();
		foreach($factory_list as $k=>$v)
		{
			$factory_list[$k]['num'] = $factory_orderDB ->where('`factory_id` = '.$v['id'] . ' and `status` not in("history","history_ok")')->count();
			$overdue = factory_overdue_time($v['val']);
			$time = time()-$overdue;
			$factory_overdue =  $factory_orderDB ->where('`factory_id` = '.$v['id'] . ' and `status` not in("history","history_ok") and `date` <= "'.date('Y-m-d',$time).'"')->select();
			//dump($factory_overdue);exit;
			foreach($factory_overdue as $overdue_k => $overdue_v)
			{
				if($overdue_k ==0)
				{
					$factory_list[$k]['overdue_num'] =0;
				}
				$factory_time = strtotime($overdue_v['date'].' '.$overdue_v['date_detail']);
				if($factory_time < $time)
				{
					$factory_list[$k]['overdue_num'] ++;	
				}
			}
			if($factory_list[$k]['overdue_num'] =='')
			{
				$factory_list[$k]['overdue_num']=0;
			}
		}
		//dump($factory_list);exit;
		$num_all = $factory_orderDB ->where('`status` not in("history","history_ok")')->count();
		$this->num_all = $num_all;
		//获得过期的总数
		$overdue_v_num =0;
		for($i=0;$i<=count($factory_list);$i++)
		{
			$overdue_v_num = $overdue_v_num + $factory_list [$i]['overdue_num'];	
		}
		$this->overdue_v_num = $overdue_v_num;
		//获得过期的总数 end
		
		//工厂列表 end
		$factory = I('get.factory');
		if($factory)
		{
			$fac = strtolower($factory);
			$factory_id = $factoryDB ->where('`val` ="'.$factory.'"')->getField('id');
			$where['factory_id'] = $factory_id;
			$this->factory = $fac;	
		}
		else
		{
			$factory ='hsf';
			$this->factory = 'hsf';	
			$fac = strtolower($factory);
			$factory_id = $factoryDB ->where('`val` ="'.$factory.'"')->getField('id');
			$where['factory_id'] = $factory_id;
			$this->factory = $fac;	
		}
		$btime = I('get.btime');
		$etime = I('get.etime');
		if($btime && $etime)
		{
			$where['date'] = array(array('egt',$btime ),array('elt',$etime ));
			$this->btime = $btime;
			$this->etime = $etime;
		}
		if(I('get.order_num'))
		{
			$order_num_where['order_number'] = trim(I('get.order_num'));
			$order_wed_id = M('order_web')->where($order_num_where)->getField('id');
			if($order_wed_id)
			{
				$where['order_id'] = $order_wed_id;
			}
			else
			{
				$order_platform_id = M('order_plat_form')->where($order_num_where)->getField('id');
				$where['order_platform_id'] = $order_platform_id;
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
			
			$where['date'] = date("Y",time())."-".$fac_time[0]."-".$fac_time[1];
			if($str_num>9)
			{
				$day_num = $str_num;
			}
			else
			{
				$day_num = substr($str_num,1);   //      number
			}
			$where['number']  = $day_num;
			
			$this->fac_num = I('get.fac_num');
			$this->sta = 'get';
		}	
		//判断 是否到达  过期
		if(I('get.screening') =='yes')
		{
			$this ->screening = I('get.screening');
			$overdue = factory_overdue_time(I('get.factory'));
			$time = time()-$overdue;
			$where['date'] = array('elt',date('Y-m-d',$time));
			//$where_overdue['date_detail'] = array('lt',date('H:i:s',$time));
		}
		//判断 是否到达  过期 end
		$where['status'] = array('not in','history,history_ok');
		$list = $factory_orderDB ->where($where)->order('date desc, date_detail desc')->select();
		//重组数组
		if(I('get.screening') =='yes')
		{
			$list_num = 0;
		//	echo date("Y-m-d H:i:s",$time);
			foreach($list as $list_k=>$list_v)
			{
				$factory_time = strtotime($list_v['date'].' '.$list_v['date_detail']);
				if($factory_time < $time)
				{
					$info_array[$list_num] = $list_v;
					$list_num++;
				}
				
			}
		}
		else
		{
			$info_array = $list ;
		}
		//重组数组  end  
		$coun=count($info_array);
		$Page       = new \Think\Page1($coun);// 实例化分页类 传入总记录数
		$nowPage = isset($_GET['p'])?$_GET['p']:1;
		$show       = $Page->show();// 分页显示输出
		$iii =0;
		for($i=($nowPage-1)*C('LISTROWS');$i<$nowPage*C('LISTROWS');$i++)
		{
			if($info_array[$i])
			{
				$info[$iii]= $info_array[$i];
				$iii++;
			}
			
		}		
		//dump(date('Y-m-d H:i:s',$time));
	//	$info = $factory_orderDB ->where($where)->order('date desc, date_detail desc')->page($nowPage.','.C('LISTROWS'))->select();
	//	dump($info);exit;
		foreach($info as $k=>$v)
		{
			$info[$k]['come_from']=$id_come_fromDB->where('`id` ='.$info[$k]['come_from_id'])->getField('name');
			if($info[$k]['order_platform_id']==0 || $info[$k]['order_platform_id']=='')
			{
				$info[$k]['uu']=order_number($info[$k]['order_id'],'order_web');
				$info[$k]['order_number']=strtoupper($info[$k]['come_from']).$info[$k]['uu'];
				$info[$k]['url']=__MODULE__.'/OrderManage/order_web/order_status/all/order_number/'.$info[$k]['uu'].'/come_from_id/'.$info[$k]['come_from_id'];
				
				$order = 'web';
		    }
			else
			{
				$info[$k]['uu']=order_number($info[$k]['order_platform_id'],'order_plat_form');
				$info[$k]['order_number']=strtoupper($info[$k]['come_from']).$info[$k]['uu'];
				$info[$k]['url']=__MODULE__.'/OrderManage/order_plat/order_status/all/order_number/'.$info[$k]['uu'].'/come_from_id/'.$info[$k]['come_from_id'];
				
				$order = 'plat';
			}
			$info[$k]['type'] =strtoupper($factoryDB ->where('`id` ="'.$v['factory_id'].'"')->getField('val'));
			
			$info[$k]['fac_num'] = get_factory_str($v['factory_id'],$v['date'],$v['number'],'execl',$order);
			$info[$k]['detail']=$factory_order_detailDB->where('`factory_order_id` ='.$v['id'])->select();
			$info[$k]['coun']=$factory_order_detailDB->where('`factory_order_id` ='.$v['id'])->count();
			
			foreach($info[$k]['detail'] as $kk=>$vv)
			{
				$info[$k]['detail'][$kk]['code_name'] = code_val($vv['code']);
				//$code_val = $id_product_codeDB->field('size_id,color_id')->where('`code` = "'.$vv['code'].'"')->find();
				//$info[$k]['detail'][$kk]['size'] = $id_product_attributeDB->where('`id`='.$code_val['size_id'])->getField('value');
				//$info[$k]['detail'][$kk]['color'] = $id_product_attributeDB->where('`id`='.$code_val['color_id'])->getField('value');
			}
		}
		//dump($info);exit;
		$this->page = $show;
		$this->info = $info;
		$this->factory_list = $factory_list;
		$this->display();
	}
	//导出筛选execl
	public function screening_execl()
	{
		$factoryDB = M('factory');
		$factory_orderDB = M('factory_order');
		$factory_order_detailDB = M('factory_order_detail');
		$id_come_fromDB = M('id_come_from');
		$id_product_codeDB = M('id_product_code');
		$id_product_attributeDB = M('id_product_attribute');
		$factory = I('post.factory');
		if($factory)
		{
			$fac = strtolower($factory);
			$factory_id = $factoryDB ->where('`val` ="'.$factory.'"')->getField('id');
			$where['factory_id'] = $factory_id;
		}
		$btime = I('post.beginTime');
		$etime = I('post.endTime');
		if(I('post.screening') !='')
		{
			$overdue = factory_overdue_time(I('post.factory'));
			$time = time()-$overdue;
			$where['date'] = array('elt',date('Y-m-d',$time));
		}
		elseif($btime && $etime)
		{
			$where['date'] = array(array('egt',$btime ),array('elt',$etime ));
		}	
		$where['status'] = array('not in','history,history_ok');
		//区分网站平台
		$whereId_plat['order_platform_id'] = array('neq','0');
		$where_plat = array_merge($where,$whereId_plat);
		$info_plat = $factory_orderDB->where($where_plat)->order('date,date_detail')->select();
		
		$whereId_web['order_id'] =  array('neq','0');
		$where_web = array_merge($where,$whereId_web);
		$info_web = $factory_orderDB->where($where_web)->order('date,date_detail')->select();
		
		if($info_plat && $info_web)
		{
			$list_array = array_merge($info_web,$info_plat);
			
		}
		else
		{
			if($info_plat)
			{
				$list_array = $info_plat;	
			}
			elseif($info_web)
			{
				$list_array = $info_web;	
			}
		}
		//区分网站平台  end
		//重组数组
		if(I('post.screening') =='yes')
		{
			$list_num = 0;
			foreach($list_array as $list_k=>$list_v)
			{
				$factory_time = strtotime($list_v['date'].' '.$list_v['date_detail']);
				if($factory_time < $time)
				{
					$list[$list_num] = $list_v;
					$list_num++;
				}
			}
		}
		else
		{
			$list = $list_array ;
		}
		//重组数组  end  
		
		$nnn=0;
		foreach($list as $k=>$v)
		{
			$type =strtoupper($factoryDB ->where('`id` ="'.$v['factory_id'].'"')->getField('val'));
			
			
			//get_factory_str($fac_v['factory_id'],$fac_v['date'],$fac_v['number'],'execl');
			
			$detail=$factory_order_detailDB->where('`factory_order_id` ='.$v['id']." and `status` !='cancel'")->select();
			//$info[$k]['detail'][$kk]['code'] != 'customization' and $info[$k]['detail'][$kk]['code'] != 'ncustomization'
			if($detail)
			{
				if($v['order_platform_id']==0 || $v['order_platform_id']=='')
				{
					$info[$nnn]['number'] = get_factory_str($v['factory_id'],$v['date'],$v['number'],'execl','web');	
				}
				else
				{
					$info[$nnn]['number'] = get_factory_str($v['factory_id'],$v['date'],$v['number'],'execl','plat');	
				}
				
				foreach($detail as $kk=>$vv)
				{	
					if($kk>0)
					{
						$info[$nnn]['number'] ="";
					}
					
					if($vv['code'] != 'customization' and $vv['code'] != 'ncustomization')
					{
						
						$info[$nnn]['code_name'] = code_val($detail[$kk]['code']);
							
							//判断 size color
							$code[$nnn] = explode('-',$info[$nnn]['code_name']);
							$code_name_coun[$nnn] =count($code[$nnn]);

							if($code_name_coun[$nnn]>2)         // - 分割
							{
								$info[$nnn]['size'] = $code[$nnn][$code_name_coun[$nnn]-1];
								
								$info[$nnn]['color'] = $code[$nnn][$code_name_coun[$nnn]-2];
							}
							else  // 空格 分割
							{
								$code[$nnn] = explode(' ',$info[$nnn]['code_name']);
							
								$code_name_coun[$nnn] =count($code[$nnn]);
								
								if($code_name_coun[$nnn]>2)         
								{
									$code_dh[$nnn] = explode(',',$info[$nnn]['code_name']);
									
									//棉壳四季被  170x220cm,1.25kg
									//19MM旅行枕芯 旅行枕套套装 杏粉 30x40cm, 300g
									
									$code_dh_name_coun[$nnn] =count($code_dh[$nnn]);
									
									if($code_dh_name_coun[$nnn]>1)
									{
										
										$code[$nnn] = explode(' ',$code_dh[$nnn][0]);
										
										$code_name_coun[$nnn] =count($code[$nnn]);
							
										$info[$nnn]['size'] = $code[$nnn][$code_name_coun[$nnn]-1].' '.$code_dh[$nnn][1];
										
										$info[$nnn]['color'] = $code[$nnn][$code_name_coun[$nnn]-2];
									
									}
									else
									{
										$info[$nnn]['size'] = $code[$nnn][$code_name_coun[$nnn]-1];
										
										$info[$nnn]['color'] = $code[$nnn][$code_name_coun[$nnn]-2];
									}
								}
								else
								{
									$info[$nnn]['size'] = $code[$nnn][$code_name_coun[$nnn]-1];
									
									$info[$nnn]['color'] =" ";
									
								}
								
							}
						
						/*
						$info[$nnn]['code_name'] = code_val($detail[$kk]['code']);
				
						$code_val = $id_product_codeDB->field('size_id,color_id')->where('`code` = "'.$detail[$kk]['code'].'"')->find();
						
						$info[$nnn]['size'] = $id_product_attributeDB->where('`id` = '.$code_val['size_id'])->getField('value');
						
						$info[$nnn]['color'] = $id_product_attributeDB->where('`id` = '.$code_val['color_id'])->getField('value');
						*/
					}
					else
					{
						$info[$nnn]['code_name'] ='定制';
						$info[$nnn]['size'] ='定制';
						$info[$nnn]['color'] ='定制'; 
					}
					
					$info[$nnn]['de_num'] = $detail[$kk]['number'];
					
					$info[$nnn]['description'] = $detail[$kk]['description'];
					
					$info[$nnn]['come_from']=$id_come_fromDB->where('`id` ='.$v['come_from_id'])->getField('name');
					
					if($v['order_platform_id']==0 || $v['order_platform_id']=='')
					{
						$info[$nnn]['order_number']=order_number_full($v['order_id']);
					}
					else
					{
						$info[$nnn]['order_number']=order_number($v['order_platform_id'],'order_plat_form').' ';
					}
					
					$nnn++;
				}
			}
		}
	//	dump($info);exit;
		$title=array('工厂单号','产品名称','尺寸','颜色','数量','备注','来源','订单号',);

		if(I('post.factory'))
		{
			$fac_name = I('post.factory');	
		}
		else
		{
			$fac_name ='全部';	
		}
		exportExcel($info,$fac_name.'-未收货报表'."-".date('Y-m-d H:i:s',time()),$title);
	}
	
	//导出选中订单
	public function fac_export_order()
	{
		$factoryDB = M('factory');
		$factory_orderDB = M('factory_order');
		$factory_order_detailDB = M('factory_order_detail');
		$id_come_fromDB = M('id_come_from');
		$id_product_codeDB = M('id_product_code');
		$id_product_attributeDB = M('id_product_attribute');
		$factory = I('post.factory');
		
		$id = I('post.check');
		if(!$id)
		{
			$this->error('你不选择，我怎么执行程序');	
		}
		$id_list = implode(',',$id);
		
		$where['id'] = array('in',$id_list);
		
		
		//区分网站平台
		$whereId_plat['order_platform_id'] = array('neq','0');
		$where_plat = array_merge($where,$whereId_plat);
		$info_plat = $factory_orderDB->where($where_plat)->order('date,date_detail')->select();
		
		$whereId_web['order_id'] =  array('neq','0');
		$where_web = array_merge($where,$whereId_web);
		$info_web = $factory_orderDB->where($where_web)->order('date,date_detail')->select();
		
	//	dump($info_plat);exit;
		if($info_plat && $info_web)
		{
			$list = array_merge($info_web,$info_plat);
			
		}
		else
		{
			if($info_plat)
			{
				$list = $info_plat;	
			}
			elseif($info_web)
			{
				$list = $info_web;	
			}
		}
		//区分网站平台  end
		
		//$list = $factory_orderDB ->where($where)->order('id desc')->select();
		$nnn=0;
		//dump($list);exit;
		foreach($list as $k=>$v)
		{
			$type =strtoupper($factoryDB ->where('`id` ="'.$v['factory_id'].'"')->getField('val'));
			
			$detail=$factory_order_detailDB->where('`factory_order_id` ='.$v['id']." and `status` !='cancel'")->select();
			if($detail)
			{
				$come_from_factory=$id_come_fromDB->where('`id` ='.$v['come_from_id'])->getField('style');
				
				$info[$nnn]['number'] = get_factory_str($v['factory_id'],$v['date'],$v['number'],'execl',$come_from_factory);
				
				foreach($detail as $kk=>$vv)
				{	
					if($kk>0)
					{
						$info[$nnn]['number'] ="";
					}
					
					if($v['factory_id'] !='7' and $v['factory_id'] !='8' and $vv['code']!='customization')
					{
						
						$info[$nnn]['code_name'] = code_val($detail[$kk]['code']);
							
							//判断 size color
							$code[$nnn] = explode('-',$info[$nnn]['code_name']);
							$code_name_coun[$nnn] =count($code[$nnn]);
							if($code_name_coun[$nnn]>2)         // - 分割
							{
								$info[$nnn]['size'] = $code[$nnn][$code_name_coun[$nnn]-1];
								
								$info[$nnn]['color'] = $code[$nnn][$code_name_coun[$nnn]-2];
							}
							else  // 空格 分割
							{
								$code[$nnn] = explode(' ',$info[$nnn]['code_name']);
							
								$code_name_coun[$nnn] =count($code[$nnn]);
								
								if($code_name_coun[$nnn]>2)         
								{
									$code_dh[$nnn] = explode(',',$info[$nnn]['code_name']);
									
									//棉壳四季被  170x220cm,1.25kg
									//19MM旅行枕芯 旅行枕套套装 杏粉 30x40cm, 300g
									
									$code_dh_name_coun[$nnn] =count($code_dh[$nnn]);
									
									if($code_dh_name_coun[$nnn]>1)
									{
										
										$code[$nnn] = explode(' ',$code_dh[$nnn][0]);
										
										$code_name_coun[$nnn] =count($code[$nnn]);
							
										$info[$nnn]['size'] = $code[$nnn][$code_name_coun[$nnn]-1].' '.$code_dh[$nnn][1];
										
										$info[$nnn]['color'] = $code[$nnn][$code_name_coun[$nnn]-2];
									
									}
									else
									{
										$info[$nnn]['size'] = $code[$nnn][$code_name_coun[$nnn]-1];
										
										$info[$nnn]['color'] = $code[$nnn][$code_name_coun[$nnn]-2];
									}
								}
								else
								{
									$info[$nnn]['size'] = $code[$nnn][$code_name_coun[$nnn]-1];
									
									$info[$nnn]['color'] =" ";
									
								}
								
							}
						
						/*$info[$nnn]['code_name'] = code_val($vv['code']);
						$code_val = $id_product_codeDB->field('size_id,color_id')->where('`code` = "'.$vv['code'].'"')->find();
	
						$info[$nnn]['size'] = $id_product_attributeDB->where('`id` = '.$code_val['size_id'])->getField('value');
	
						$info[$nnn]['color'] = $id_product_attributeDB->where('`id` = '.$code_val['color_id'])->getField('value');*/
					}
					else
					{
						$info[$nnn]['code_name'] ='定制';
						$info[$nnn]['size'] ='定制';
						$info[$nnn]['color'] ='定制'; 
					}
					
					$info[$nnn]['de_num'] = $vv['number'];
					
					$info[$nnn]['description'] = $vv['description'];
					
					$info[$nnn]['come_from']=$id_come_fromDB->where('`id` ='.$v['come_from_id'])->getField('name');
					
					if($v['order_platform_id']==0 || $v['order_platform_id']=='')
					{
						$info[$nnn]['order_number']=order_number_full($v['order_id']);
					}
					else
					{
						$info[$nnn]['order_number']=order_number($v['order_platform_id'],'order_plat_form');
					}
					
					$nnn++;
				}
			}
		}
	//	dump($info);exit;
		$this->info = $info;
		$title=array('工厂单号','产品名称','尺寸','颜色','数量','备注','来源','订单号',);
		if(I('post.factory'))
		{
			$fac_name = I('post.factory');	
		}
		else
		{
			$fac_name ='全部';	
		}
		exportExcel($info,$fac_name.'-未收货报表'."-".date('Y-m-d H:i:s',time()),$title);
	}
	
	//导出工厂核对单
	function factory_order_check_execl()
	{
		$factory_orderDB = M('factory_order');
		$factory_order_detailDB = M('factory_order_detail');
		$factoryDB = M('factory');
		if(IS_GET)
		{
			$btime = I('get.btime');
			$etime = I('get.etime');
			$factory_list = $factoryDB->where('1=1')->select();	
			$nnn =0 ;	
			foreach($factory_list  as $fac_list_k => $fac_list_v)
			{
				$fac_order = $factory_orderDB->where('`factory_id` ='.$fac_list_v['id'].' and `date`>="'.$btime.'" and `date`<="'.$etime.'"')->order('id desc')->select();
				//dump($fac_order);exit;
				foreach($fac_order as $fac_order_k => $fac_order_v)
				{
					if($fac_order_v['order_id'])
					{
						$fac_num = associated_fac($fac_order_v['id'],$fac_order_v['order_id'],'fac',1,'rn','web');
					}
					elseif($fac_order_v['order_platform_id'])
					{
						$fac_num = associated_fac($fac_order_v['id'],$fac_order_v['order_platform_id'],'fac',1,'rn','plat');
					}
					$list[$nnn]['fac_num'] = $fac_num;
					$fac_detail = $factory_order_detailDB->where('`factory_order_id` ='.$fac_order_v['id'])->order('id desc')->select();
					foreach($fac_detail as $fac_detail_k => $fac_detail_v)
					{
						if(!$list[$nnn]['fac_num'])
						{
							$list[$nnn]['fac_num'] ='';   //工厂单号
						}
						
						//姓名
						if($fac_order_v['order_id'])
						{
							$name = M('order_web') ->where('`id` ='.$fac_order_v['order_id'])->field('first_name,last_name,come_from_id')->find();
							$list[$nnn]['name'] = $name['first_name']." ".$name['last_name'];
							
							$come_from = "lily_".get_come_from_name($name['come_from_id']);
						}
						elseif($fac_order_v['order_platform_id'])
						{
							$name =  M('order_plat_form')->where('`id` ='.$fac_order_v['order_platform_id'])->field('name,come_from_id')->find();
							$list[$nnn]['name'] =$name['name'];
							
							$come_from = get_come_from_name($name['come_from_id']);
						}
						else
						{
							$list[$nnn]['name'] ='错误';
							
							$come_from = '错误';
						}
						//品名
						$list[$nnn]['code_name'] = M('id_product_code')->where('`code` ="'.$fac_detail_v['code'].'"')->getField('name');
						
						$list[$nnn]['number'] = $fac_detail_v['number'];
						
						$list[$nnn]['description'] = $fac_detail_v['description'];
						
						if($fac_order_v['order_id'])
						{
							$list[$nnn]['oredr_number'] = order_number_full($fac_order_v['order_id']);
						}
						elseif($fac_order_v['order_platform_id'])
						{
							$list[$nnn]['oredr_number'] =order_number($fac_order_v['order_platform_id'],'order_plat_form')." ";
						}
						
						$list[$nnn]['come_from'] = $come_from;
						
						if($fac_order_v['order_id'])
						{
							$list[$nnn]['country'] = M('order_web_address')->where('`order_web_id`='.$fac_order_v['order_id'])->getField('country');
						}
						elseif($fac_order_v['order_platform_id'])
						{
							$list[$nnn]['country'] =M('order_plat_form_shipping')->where('`order_platform_id` ='.$fac_order_v['order_platform_id'])->getField('country');
						}
						
						if($fac_detail_v['status'] =='cancel')
						{
							$list[$nnn]['status'] = "已取消";
						}
						else
						{
							$list[$nnn]['status'] ='';
						}
												
						$nnn++;
					}
				
				}
			
			}
			$title =array('单号','客户姓名','品名','数量','备注','网站单号','网站','国家',"是否取消");
			exportExcel($list,"工厂订单核对".date('Y-m-d H:i:s',time()),$title);
		}
	}
	
	
}