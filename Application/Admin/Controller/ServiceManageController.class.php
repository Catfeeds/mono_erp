<?php 
	namespace Admin\Controller;
	use Think\Controller;
	class ServiceManageController extends CommonController
	{
		public function index()
		{
			$this->display();
		}
		//常见问题添加
		public function service_add()
		{
			$service_question = M("service_question");
			if(isset($_POST['dosubmit']))
			{
				$data['question'] = $_POST['question'];
				$data['content'] = $_POST['content'];
				$insert = $service_question->add($data);
				if($insert)
				{
					$this->success('常见问题添加成功！',U('/Admin/ServiceManage/service'));
				}
				else 
				{
					$this->error("常见问题添加失败！");
				}
			}
			else 
			{
				if(isset($_GET['id']))
				{
					$id = $_GET['id'];
					$info = $service_question->where("id=$id")->find();
					$this->assign("info",$info);
					$this->assign('tpltitle','修改');
				}
				else
				{
					$this->assign('tpltitle','添加');
				}
			}
			$this->display();
		}
		//常见问题列表
		public function service()
		{ 
			$service_question = M("service_question");
			//import('Org.Util.Page');// 导入分页类
			$count = $service_question->count();// 查询满足要求的总记录数 $map表示查询条件
			$Page = new \Think\Page1($count);// 实例化分页类 传入总记录数
			// 进行分页数据查询 注意page方法的参数的前面部分是当前的页数使用 $_GET[p]获取
			$nowPage = isset($_GET['p'])?$_GET['p']:1;
			$show = $Page->show();// 分页显示输出
			$service_list = $service_question->order('id asc')->page($nowPage.','.C('LISTROWS'))->select();
			$this->assign("service_list",$service_list);
			//get_to_order(47279);
			$this->assign("page",$show);
			$this->display();
		}
		//常见问题修改
		public function service_edit()
		{
			$service_question = M("service_question");
			if(isset($_POST['id']))
			{
				$id = $_POST['id'];
				$data['question'] = $_POST['question'];
				$data['content'] = $_POST['content'];
				$update_service = $service_question->where("id=$id")->save($data);
				if($update_service)
				{
					$this->success('常见问题修改成功！',U('/Admin/ServiceManage/service'));
				}
				else 
				{
					$this->error("常见问题修改失败！");
				}
			}
		}
		//常见问题删除
		public function service_del()
		{
			$service_question = M("service_question");
			if(isset($_GET['id']))
			{
				$id = $_GET['id'];
				$delete_service = $service_question->where("id=$id")->delete();
				if($delete_service)
				{
					$this->success('常见问题删除成功！',U('/Admin/ServiceManage/service'));
				}
				else 
				{
					$this->error("常见问题删除失败！");
				}
			}
		}
		//退款申请   $_GET['data']  1 :平台订单   2：网站订单
		public function refund_add(){ 
			$username = session('username');    // 用户名
			$order_plat_formDB=M('order_plat_form');                  //平台订单表
			$order_webDB=M('order_web');								//网站订单表
			$order_return_priceDB=M('order_return_price');
			if($_GET['id'] && $_GET['data'])
			{
				if($_GET['data'] == 1)
				{
					$data=M('order_plat_form');             //平台
				}
				elseif($_GET['data'] ==2)
				{
					$data=M('order_web');                   //网站
				}
				$info=$data->where('`id` ='.$_GET['id'])->find();
				$this->assign('data',$_GET['data']);
				$this->assign('info',$info);
				$this->display();
			}
			else
			{
				if($_POST['id'] && $_POST['data'])
				{
					if(!$_POST['price']){
						$this->error('价格必须填写');	
						}
					
					if($_POST['data'] == 1)
					{
						$data=M('order_plat_form');             //平台
						$info=$data->where('`id` ='.$_POST['id'])->find();
						$date['transaction_number']=$info['order_number'];
						$date['come_from']=get_come_from_name($info['come_from_id']);
						$date['currency'] = $info['currency'];
					}
					elseif($_POST['data'] ==2)
					{
						$data=M('order_web');                   //网站
						$info=$data->where('`id` ='.$_POST['id'])->find();
						$date['order_id']=$info['order_number'];
						$date['come_from']=get_come_from_name($info['come_from_id']);
						$date['currency'] = currency(get_come_from_name($info['come_from_id']));
					}
						$date['price'] = $_POST['price'];
						$date['applicant']=$username;
						$date['applicant_time']=time();
						$date['message']=$_POST['message'];
						$status=0;
						$result=$order_return_priceDB->add($date);
					    if($result)
						{
							$this->success('退款申请成功',U('/Admin/ServiceManage/refund_apply'));
						}else{
							$this->error('申请失败',U('Admin/ServiceManage/refund_add/id/'.$_POST['id'].'/data/'.$_POST['data'].''));
						}
				}
				elseif($_POST['order_number'])
				{
						if(!$_POST['price']){
							$this->error('价格必须填写');	
						}
						$order_number="'".$_POST['order_number']."'";
						if($order_plat_formDB->where('`order_number`='.$order_number)->find())
						{
							$info=$order_plat_formDB->where('`order_number`='.$order_number)->find();
							$date['transaction_number']=$info['order_number'];
							$date['currency'] = $info['currency'];
							$date['come_from']=$info['come_from'];
							
						}
						elseif($order_webDB->where('`order_number`='.$order_number)->find())
						{
							$info=$order_webDB->where('`order_number`='.$order_number)->find();
							$date['order_id']=$info['order_number'];
							$date['currency'] = currency($info['come_from']);
							$date['come_from']=$info['come_from'];
						}
						else
						{
							$this->error('单号错误');	
						}
							$date['price'] = $_POST['price'];
							$date['applicant']=$username;
							$date['applicant_time']=time();
							$date['message']=$_POST['message'];
							$status=0;
							
							$result=$order_return_priceDB->add($date);
							if($result)
							{
								$this->success('退款申请成功',U('/Admin/ServiceManage/refund_apply'));
							}else{
								$this->error('申请失败');
							}
				}else{
					$this->display();
					}
			}
		} 
		//退款申请列表
		public function refund_apply(){
			$order_return_pricDB=M('order_return_price');
			
			//import('Org.Util.Page');                                   // 导入分页类
			$count = $order_return_pricDB->count();                   // 查询满足要求的总记录数 $map表示查询条件
			$Page = new \Think\Page1($count);                               // 实例化分页类 传入总记录数
			$nowPage = isset($_GET['p'])?$_GET['p']:1;
			$show = $Page->show();                                  // 分页显示输出
			$list=$order_return_pricDB->order('id desc')->page($nowPage.','.C('LISTROWS'))->select();
			foreach($list as $k=>$v)
			{
				$list[$k]['sta']=refund_sta($list[$k]['status']);
				$list[$k]['come_from_id'] = get_come_from_id($v['come_from']);
				if($v['order_id'])
				{
					$list[$k]['url'] = '<a style="text-decoration: underline; color: #03c;" target="_blank" href="'.__APP__.'/Admin/OrderManage/order_web/order_status/all/come_from_id/'.$list[$k]['come_from_id'].'/order_number/'.$v["order_id"].'.html" >'.strtoupper($v['come_from']).$v["order_id"].'</a>';
					$list[$k]['order'] = M('order_web')->field('email,payment_style')->where('`order_number` ='.$v['order_id'] .' and `come_from_id`='.$list[$k]['come_from_id'])->find();
				}
				elseif($v['transaction_number'])
				{
					$list[$k]['url'] = '<a style="text-decoration: underline; color: #03c;" target="_blank" href="'.__APP__.'/Admin/OrderManage/order_plat/order_status/all/come_from_id/'.$list[$k]['come_from_id'].'/order_number/'.$v["transaction_number"].'.html" >'.$v["transaction_number"].'</a>';
					$list[$k]['order'] = M('order_plat_form')->field('email')->where('`order_number` ="'.$v['transaction_number'] .'" and `come_from_id`='.$list[$k]['come_from_id'])->find();
				}
			}	
			$this->assign('list',$list);
			$this->assign('page',$show);
			$this->display();
			} 
		//物流信息
		/*
		state 	快递单当前的状态 ：　
				0：在途，即货物处于运输过程中；
				1：揽件，货物已由快递公司揽收并且产生了第一条跟踪信息；
				2：疑难，货物寄送过程出了问题；
				3：已签收，收件人已签收；
				4：已退签，即货物由于用户拒签、超区等原因退回，而且发件人已经签收；
				5：派件，即快递正在进行同城派件；
				6：退回，货物正处于退回发件人的途中；
		*/
		public function logistics_information()
		{
			$order_delivery_detailDB=M('order_delivery_detail');
			if($_POST['com'])
			{
				layout(false); // 临时关闭当前模板的布局功能
				$typeCom = $_POST["com"];//快递公司
				$typeNu = $_POST["nu"];  //快递单号
				
				//判断状态  
				$logistics_sta=$order_delivery_detailDB->where('`style` = "'.$typeCom.'" and `delivery_number` ="'.$typeNu.'"')->order('time desc')->getField('status');
				if($logistics_sta=="已签收" || $logistics_sta=="已退签")
				{
					$courier_val=$order_delivery_detailDB->where('`style` = "'.$typeCom.'" and `delivery_number` ="'.$typeNu.'"')->order('time')->select();
				}
				else
				{			
					$AppKey=C('AppKey');         //在http://kuaidi100.com/app/reg.html申请到的KEY
					$url ='http://api.kuaidi100.com/api?id='.$AppKey.'&com='.$typeCom.'&nu='.$typeNu.'&show=0&muti=1&order=asc'; 
						/*show  0：返回json字符串，1：返回xml对象，2：返回html对象，3：返回text文本。如果不填，默认返回json字符串。*/			
					
					//请勿删除变量$powered 的信息，否者本站将不再为你提供快递接口服务。
					$powered = '查询数据由：<a href="http://kuaidi100.com" target="_blank">KuaiDi100.Com （快递100）</a> 网站提供 ';
					//echo $url;exit;
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
					if($aa['status']!=1)
					{
						echo json_encode($aa);        //json输出
						exit;
					}
					$courier_val=$aa['data'];                     //获得快递信息
					
					//判断数据表是否有记录
					$order=$order_delivery_detailDB->where('`delivery_number` ="'.$typeNu.'"')->find();       // 物流信息 获得订单表ID
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
								$val['status'] = logistics_status($aa['state']);
								$order_delivery_detailDB->where($whereID)->save($val);
						}
					}
				
				}
				$this->assign('courier_val',$courier_val);
				$html=$this->fetch("ServiceManage/Courier");  //渲染模板
				$this->ajaxReturn($html);					//html输出
				/*
				echo json_encode($get_content);          //json输出
				exit;
				*/
			}
			else
			{
			$this->display();
			}
		}
		//更新全部物流信息
		public function logistics_update()
		{
			$num=0;
			layout(false); // 临时关闭当前模板的布局功能
			
			$order_delivery_detailDB=M('order_delivery_detail');
			$delivery_number=$order_delivery_detailDB->field('delivery_number,style,order_platform_id,time')->group('delivery_number')->select();
			
			$AppKey=C('AppKey');         //在http://kuaidi100.com/app/reg.html申请到的KEY
			
			foreach($delivery_number as $k=>$v){
				//判断状态 
				$logistics_sta=$order_delivery_detailDB->where('`style` = '.'"'.$v['style'].'"'.' and `delivery_number` ='.'"'.$v['delivery_number'].'"')->order('time desc')->getField('status');
				if($logistics_sta!="已签收" && $logistics_sta!="已退签")
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
						$order=$order_delivery_detailDB->where('`delivery_number` ='.$v['delivery_number'])->find();       // 物流信息 获得订单表ID
						if($order){
							foreach($courier_val as $key=>$val){
								$info[$key]=$order_delivery_detailDB->where('`message` = '.'"'.$val['context'].'"'.'and `delivery_number` ='.$v['delivery_number'].' and `time` ='.'"'.$val[time].'"')->find();
								if(!$info[$key])
								{
									$date['order_platform_id'] = $order['order_platform_id'];
									$date['order_web_id'] = $order['order_web_id'];
									$date['style']=$v['style'];
									$date['delivery_number']=$v['delivery_number'];
									$date['message'] = $val['context'];
									$date['time'] = $val['time'];
									$return=$order_delivery_detailDB->add($date);
									if(C('logistics_update_num') ==1)
									{
										if($return){
											$num++;
											}
									}
								}
							}
							
							//快递状态
							if(isset($aa['state'])){
								$info=$order_delivery_detailDB->where('`delivery_number` ='.$v['delivery_number'])->order('time desc')->find();
								if(logistics_status($aa['state']) != $info['status'])
								{
									$whereID='`id` ='.$info['id'];
									$val['status'] = logistics_status($aa['state']);
									$return_sta=$order_delivery_detailDB->where($whereID)->save($val);
									if(C('logistics_update_num') ==2)
									{
										if($return_sta)
										{
											$num++;
										}
									}
								}
							}
						}
					}
				}
			}
			if($return && $return_sta){
				$this->success('已经更新<span style="color:red">' . $num . '</span>条数据！');	
			}else{
				$this->error('没有要更新的数据！');		
			}		
		}
		
		
		
		
		//返回最近一条物流信息
		public function delivery_one()
		{
			$order_delivery_detailDB=M('order_delivery_detail');
			if($_POST["nu"])
			{
				$typeCom = $_POST["com"];//快递公司
				$typeNu = $_POST["nu"];  //快递单号
				//判断状态  
				$logistics_sta=$order_delivery_detailDB->where('`style` = '.'"'.$typeCom.'"'.' and `delivery_number` ="'.$typeNu.'"')->order('time desc')->find();
				if( $logistics_sta['status'] =="寄送出现问题" || $logistics_sta['status'] =="已退签" || $logistics_sta['status'] =="退回")
				{
					echo "<span style=' color:red;'>".$logistics_sta['status'].'('.$logistics_sta['message'].')'."</span>";
				}
				else
				{
					echo $logistics_sta['status'].'('.$logistics_sta['message'].')';
				}
				exit;			
			}
			else
			{
				echo '单号出现错误！';
				exit;	
				
			}
			
		}
		

		//查看全部备注  data 1:平台  2：网站
	public function remark_check()
	{
		$userid=session('userid');
		$username = session('username');    // 用户名
		$order_business_messageDB=M('order_business_message');
		$order_plat_formDB=M('order_plat_form');
		$order_webDB=M('order_web');
		$userDB=M('user');
		/*
		if($username =="admin")
		{
			$where ="name!=''";
		}
		else
		{
			$where['username']=$username;
		}
		*/
		$user_list=$userDB->where("name!=''")->field('id,username,name')->order('id desc')->select();
		
		$product_stock_orderDB = M('product_stock_order');
		$fba_orderDB = M('fba_order');
		$factory_orderDB = M('factory_order');
		
		if(IS_POST)
		{
			if($_POST['sta']!="")
			{                 //状态
				$map['status']=$_POST['sta'];
				$this->assign('sta',$_POST['sta']);
			}
			if($_POST['data']==1)
			{                //平台
				$map['order_id'] =0;
				$this->assign('data',$_POST['data']);
			}
			if( $_POST['data']==2)
			{                //网站
				$map['order_platform_id'] =0;
				$this->assign('data',$_POST['data']);
			}
			if($_POST['operator']!="")
			{		//发起者
				$map['operator'] =$_POST['operator'];
				$this->assign('operator',$_POST['operator']);
			}	
			if($_POST['accept']!="")
			{				//指定人员
				$map['accept'] =array('LIKE','%'.$_POST['accept'].'%');
				$this->assign('accept',$_POST['accept']);
			}
			if($_POST['order_number']!="")
			{				//订单号
				$order_number_id=$order_plat_formDB->where('`order_number` ='."'".$_POST['order_number']."'")->getfield('id');
				if($order_number_id)
				{
					$map['order_platform_id'] =$order_number_id;
				}
				else
				{
					$order_number_id=$order_webDB->where('`order_number` ='."'".$_POST['order_number']."'")->getfield('id');
					$map['order_id'] =$order_number_id;
				}
				$this->assign('order_number',$_POST['order_number']);
			}			
			if($_POST['yes'] ==1)
			{						//时间区间
				if($_POST['beginTime'])
				{
					if($_POST['endTime'])
					{
						$map['date_time']=array('between',array(strtotime($_POST['beginTime']),strtotime($_POST['endTime']." 23:59:59")));
						$this->assign('beginTime',$_POST['beginTime']);
						$this->assign('endTime',$_POST['endTime']);
						$this->assign('yes',$_POST['yes']);
						/*if($_POST['operator']=="" && $_POST['accept']=="")
						{
							$map['accept'] =array('LIKE','%'.$username.'%');
							$this->assign('accept',$username);	
						}*/
					}
				}
			}
		}
		elseif(IS_GET)
		{
			if($_GET['sta']!="")
			{                 //状态
				$map['status']=$_GET['sta'];
				$this->assign('sta',$_GET['sta']);
			}
			if($_GET['data']==1)
			{                //平台
				$map['order_id'] =0;
				$this->assign('data',$_GET['data']);
			}
			if( $_GET['data']==2)
			{                //网站
				$map['order_platform_id'] =0;
				$this->assign('data',$_GET['data']);
			}
			if($_GET['operator']!="")
			{		//发起者
				$map['operator'] =$_GET['operator'];
				$this->assign('operator',$_GET['operator']);
			}	
			if($_GET['accept']!="")
			{				//指定人员
				$map['accept'] =array('LIKE','%'.$_GET['accept'].'%');
				$this->assign('accept',$_GET['accept']);
			}
			if($_POST['order_number']!="")
			{				//订单号
				$order_number_id=$order_plat_formDB->where('`order_number` ='."'".$_GET['order_number']."'")->getfield('id');
				if($order_number_id)
				{
					$map['order_platform_id'] =$order_number_id;
				}
				else
				{
					$order_number_id=$order_webDB->where('`order_number` ='."'".$_GET['order_number']."'")->getfield('id');
					$map['order_id'] =$order_number_id;
				}
				$this->assign('order_number',$_GET['order_number']);
			}			
			if($_GET['yes'] ==1)
			{						//时间区间
				if($_GET['beginTime'])
				{
					if($_GET['endTime'])
					{
						$map['date_time']=array('between',array(strtotime($_GET['beginTime']),strtotime($_GET['endTime']." 23:59:59")));
						$this->assign('beginTime',$_GET['beginTime']);
						$this->assign('endTime',$_GET['endTime']);
						$this->assign('yes',$_GET['yes']);
						/*if($_GET['operator']=="" && $_GET['accept']=="")
						{
							$map['accept'] =array('LIKE','%'.$username.'%');
							$this->assign('accept',$username);	
						}*/
					}
				}
			}
			if(!$map)
			{
				$map['accept'] =array('LIKE','%'.$username.'%');
				$this->assign('accept',$username);	
				$map['status']=0;
				$this->assign('sta',0);
			}
		}
		else
		{
			$map['accept'] =array('LIKE','%'.$username.'%');
			$this->assign('accept',$username);	
			$map['status']=0;
			$this->assign('sta',0);
		}
		/*
		if($username !='admin')
		{   
			if($_POST['operator']=="" && $_POST['accept']=="")
			{
				$map['accept'] =array('LIKE','%'.$username.'%');
				$this->assign('accept',$username);	
			}
		}*/
		//dump($map);exit;
		$remark_coun=$order_business_messageDB->where($map)->count();
        $Page       = new \Think\Page1($remark_coun);// 实例化分页类 传入总记录数
		$nowPage = isset($_GET['p'])?$_GET['p']:1;
        $show       = $Page->show();// 分页显示输出
		$info=$order_business_messageDB->where($map)->page($nowPage,C('LISTROWS'))->order('warning')->select();
		foreach($info as $k=>$v)
		{
			$info[$k]['accept_s'] = explode(',',$v['accept']);
			$info[$k]['sta']=remark_sta($info[$k]['status']);
			if($info[$k]['order_id']==0)
			{
				$order = 'plat';
				$order_bz = 'W';
				$data_val=$order_plat_formDB->field('come_from_id,order_number')->where('`id` = '.$info[$k]['order_platform_id'])->find();
				$info[$k]['order_number']=strtoupper(get_come_from_name($data_val['come_from_id'])).$data_val['order_number'];
				$info[$k]['uu']=$data_val['order_number'];
				$info[$k]['url']=__MODULE__.'/OrderManage/order_plat/order_status/all/order_number/'.$info[$k]['uu'].'/come_from_id/'.$data_val['come_from_id'];
				
				//工厂编码
				$fac = $factory_orderDB->field('factory_id,date,number,status')->where('`order_platform_id`='.$v['order_platform_id'] ) ->select();
				foreach($fac as $fac_k=>$fac_v)
				{
					
					if($fac_v['factory_id']== 9 )
					{
						$fac_val['fac_num_'.$k][$fac_k] ='<a target="_blank" href="'.__APP__.'/Admin/FactoryManage/XdjOrder/order_num/'.$info[$k]['uu'].'.html" class="cur">'.get_factory_str($fac_v['factory_id'],$fac_v['date'],$fac_v['number'],'execl',$order).'</a>';
					}
					else
					{
						$fac_val['fac_num_'.$k][$fac_k] ='<a target="_blank" href="'.__APP__.'/Admin/FactoryOrder/factory/type/'.get_factory_val($fac_v['factory_id']).'/order_num/'.$info[$k]['uu'].'.html" class="cur">'.get_factory_str($fac_v['factory_id'],$fac_v['date'],$fac_v['number'],'execl',$order).'</a>';
					}
					
					if($fac_v['status'] =='history' || $fac_v['factory_id']=='9' || $fac_v['status'] =='history_ok')
					{
						$fba_val['fac_num_'.$k][$fba_k].=' <span class="icon-check" style="color:red;"></span>';
					}
				}
				$aa=implode(" <br> ",$fac_val['fac_num_'.$k]);
				//fba编码
				$fba = $fba_orderDB ->field('date,number,status')->where('`orderplatform_id`='.$v['order_platform_id']) ->select();
			//	dump($fba);
				foreach($fba as $fba_k=>$fba_v)
				{
					if($fba_v['number'] < 10)
					{
						$num = '0' . $fba_v['number'];
					}
					else
					{
						$num = $fba_v['number'];
					}
					$fba_val['fba_num_'.$k][$fba_k]= '<a target="_blank" href="'.__APP__.'/Admin/FactoryManage/FbaOrder/order_num/'.$info[$k]['uu'].'.html" class="cur">'."FBA".$order_bz.date('m.d',strtotime($fba_v["date"])).'-'.$num.'</a>';
					if($fba_v['status'] =='history')
					{
						$fba_val['fba_num_'.$k][$fba_k].=' <span class="icon-check" style="color:red;"></span>';
					}
				}
				$bb=implode(" <br> ",$fba_val['fba_num_'.$k]);
				//库存编码
				$bd = $product_stock_orderDB ->field('date,number,status')->where('`order_platform_id`='.$v['order_platform_id']) ->select();
				foreach($bd as $bd_k=>$bd_v)
				{
					if($bd_v['number'] < 10)
					{
						$num = '0' . $bd['number'];
					}
					else
					{
						$num = $bd['number'];
					}
					$bd_val['bd_num_'.$k][$bd_k] = '<a target="_blank" href="'.__APP__.'/Admin/FactoryManage/StockOrder/order_num/'.$info[$k]['uu'].'.html" class="cur">'."K".$order_bz.date('m.d',strtotime($bd_v["date"])).'-'.$num."</a>";
					if($bd_v['status'] =='history')
					{
						$bd_val['bd_num_'.$k][$bd_k].= ' <span class="icon-check" style="color:red;"></span>';
					}
				}
				$cc=implode(" <br> ",$bd_val['bd_num_'.$k]);
			
			}
			if($info[$k]['order_platform_id']==0)
			{
				$order = 'web';
				$order_bz = '';
				$data_val=$order_webDB->field('come_from_id,order_number')->where('`id` = '.$info[$k]['order_id'])->find();
				$info[$k]['order_number']=strtoupper(get_come_from_name($data_val['come_from_id'])).$data_val['order_number'];
				$info[$k]['uu']=$data_val['order_number'];
				$info[$k]['url']=__MODULE__.'/OrderManage/order_web/order_status/all/order_number/'.$info[$k]['uu'].'/come_from_id/'.$data_val['come_from_id'];
				//工厂编码
				$fac = $factory_orderDB->field('factory_id,date,number,status')->where('`order_id`='.$v['order_id']) ->select();
				foreach($fac as $fac_k=>$fac_v)
				{
					if($fac_v['factory_id']== 9 )
					{
						$fac_val['fac_web_num_'.$k][$fac_k] ='<a target="_blank" href="'.__APP__.'/Admin/FactoryManage/XdjOrder/order_num/'.$info[$k]['uu'].'.html" class="cur">'.get_factory_str($fac_v['factory_id'],$fac_v['date'],$fac_v['number'],'execl',$order).'</a>';
					}
					else
					{
						$fac_val['fac_web_num_'.$k][$fac_k] ='<a target="_blank" href="'.__APP__.'/Admin/FactoryOrder/factory/type/'.get_factory_val($fac_v['factory_id']).'/order_num/'.$info[$k]['uu'].'.html" class="cur">'.get_factory_str($fac_v['factory_id'],$fac_v['date'],$fac_v['number'],'execl',$order).'</a>';
					}
					if($fac_v['status'] =='history' || $fac_v['factory_id']=='9' || $fac_v['status'] =='history_ok')
					{
						$fac_val['fac_web_num_'.$k][$fac_k].= ' <span class="icon-check" style="color:red;"></span>';
					}
					
				}
				$aa=implode(" <br> ",$fac_val['fac_web_num_'.$k]);
				//fba
				$fba = $fba_orderDB ->field('date,number,status')->where('`order_id`='.$v['order_id']) ->select();
				
				foreach($fba as $fba_k=>$fba_v)
				{
					if($fba_v['number'] < 10)
					{
						$num = '0' . $fba_v['number'];
					}
					else
					{
						$num = $fba_v['number'];
					}
					$fba_val['fba_web_num_'.$k][$fba_k] = '<a target="_blank" href="'.__APP__.'/Admin/FactoryManage/FbaOrder/order_num/'.$info[$k]['uu'].'.html" class="cur">'.$order_bz."FBA".date('m.d',strtotime($fba_v["date"])).'-'.$num.'</a>';
					
					if($fba_v['status'] =='history')
					{
						$fba_val['fba_web_num_'.$k][$fba_k].= ' <span class="icon-check" style="color:red;"></span>';
					}
				}
				$bb=implode(" <br> ",$fba_val['fba_web_num_'.$k]);
				
				$bd = $product_stock_orderDB ->field('date,number,status')->where('`order_id`='.$v['order_id']) ->select();
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
					$bd_val['bd_web_num_'.$k][$bd_k] = '<a target="_blank" href="'.__APP__.'/Admin/FactoryManage/StockOrder/order_num/'.$info[$k]['uu'].'.html" class="cur">'.$order_bz."K".date('m.d',strtotime($bd_v["date"])).'-'.$num.'</a>';
					
					if($bd_v['status'] =='history')
					{
						$bd_val['bd_web_num_'.$k][$bd_k].= ' <span class="icon-check" style="color:red;"></span>';
					}
					
				}
				$cc=implode(" <br> ",$bd_val['bd_web_num_'.$k]);
				
			}
			if($bb!=''){$bbb = " <br> ";}else{$bbb='';}
			if($cc!=''){$ccc = " <br> ";}else{$ccc='';}
			
			$info[$k]['fac_number'] =$aaa.$aa.$bbb.$bb.$ccc.$cc; //工厂单号	
		}
		$this->username = $username;
		$this->assign('user_list',$user_list);
		$this->assign('username',$username);
		$this->assign('page',$show);
		$this->assign('info',$info);
		$this->display();
	}
	
	// 备注状态改变
	public function remark_status()
	{	
		$id=$_GET['id'];
		$status=$_GET['sta'];
		$order_business_messageDB=M('order_business_message');	
		if(!$id){
			$this->error('非法操作！！');
			}
			
		$date['status']=$status;
		$date['end_time']=time();	
		$return=$order_business_messageDB->where('`id` ='.$id)->save($date);	
		if($return){
			$this->success("状态改变成功");
		}else{
			$this->error("状态改变失败");
		}
	}
	//订单留言管理（ 客户 ）
	public function order_message()
	{
		if($_POST['beginTime']!='' && $_POST['endTime']!='')
		{
			$beginTime = date("Y-m-d H:i:s",strtotime(I('post.beginTime')));
			$endTime = date("Y-m-d 00:00:00",(strtotime(I('post.endTime'))+3600*24));
			$map['date_time'] = array("between","$beginTime,$endTime");
		}
		else 
		{
			$begin_time=date("Y-m-d h:i:s",time()-604800);
			$map['date_time'] = array('egt',$begin_time);
		}	
		$map["message"]=array(array('neq',""),array('neq'," "));
		$order_dispose_status = D('order_dispose_status');
		if(isset($_GET["style"]))
		{
			if(I('get.style')=='plat')
			{
				$order_plat_form=M("order_plat_form");
				$order_plat_form_list=$order_plat_form->where($map)->order('date_time desc')->select();
				foreach($order_plat_form_list as $key=>$value)
				{
					$order_dispose_status_list = $order_dispose_status->where(array('order_platform_id'=>$value['id']))->select();
					foreach($order_dispose_status_list as $order_dispose_status_list_value)
					{
						$order_plat_form_list[$key]['accept'] = $order_dispose_status_list_value['accept'];
					}
				}
				$this->assign('order_list',$order_plat_form_list);
				$this->assign('style',"plat");
			}
			else if(I('get.style')=='web')
			{
				$order_web=M("order_web");
				$order_web_list=$order_web->where($map)->order('date_time desc')->select();
				foreach($order_web_list as $key=>$value)
				{
					$order_dispose_status_list = $order_dispose_status->where(array('order_web_id'=>$value['id']))->select();
					foreach($order_dispose_status_list as $order_dispose_status_list_value)
					{
						$order_web_list[$key]['accept'] = $order_dispose_status_list_value['accept'];
					}
				}
				$this->assign('order_list',$order_web_list);
				$this->assign('style',"web");
			}	
		}
		else 
		{
			$order_web=M("order_web");
			$order_web_list=$order_web->where($map)->order('date_time desc')->select();
			foreach($order_web_list as $key=>$value)
			{
				$order_dispose_status_list = $order_dispose_status->where(array('order_web_id'=>$value['id']))->select();
				foreach($order_dispose_status_list as $order_dispose_status_list_value)
				{
					$order_web_list[$key]['accept'] = $order_dispose_status_list_value['accept'];
				}
			}
			$this->assign('order_list',$order_web_list);
			$this->assign('style',"web");
		}
		
		$this->display();
	}	
	//接收客户留言
	public function order_dispose_status_add()
	{
		$order_dispose_status =D('order_dispose_status');
		if(isset($_GET['style']))
		{
			if(I('get.style')=='web')
			{
				$data['order_web_id'] = I('get.id');
				$data['order_platform_id'] = 0;
			}
			else if(I('get.style')=='plat')
			{
				$data['order_web_id'] = 0;
				$data['order_platform_id'] = I('get.id');
			}
			$data['accept'] = session('username');
			$data['date_time'] = date('y-m-d h:i:s',time());
			$insert = $order_dispose_status->add($data);
			if($insert)
			{
    			$this->success('接受成功！',U('/Admin/ServiceManage/order_message/',array('style'=>I('get.style'))));
    		}
    		else
    		{
    			$this->error("接受失败！");
    		}
		}
	}
}
?>