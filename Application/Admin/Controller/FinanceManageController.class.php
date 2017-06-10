<?php 
	namespace Admin\Controller;
	use Think\Controller;
	class FinanceManageController extends CommonController
	{
		function index()
		{
			$this->display();
		}
		//财务添加
		public function finance_add()
		{
			$mono_cost = M("mono_cost");
			if(isset($_POST['dosubmit']))
			{
				$data['name'] = $_POST['name'];
				$data['message'] = $_POST['content'];
				$data['applicant'] = $_POST['applicant'];
				$data['operator'] = $_POST['operator'];
				$data['apply_time'] = $_POST['apply_time'];
				$data['oper_time'] = $_POST['oper_time'];
				$data['status'] = $_POST['status'];
				$insert = $mono_cost->add($data);
				if($insert)
				{
					$this->success('财务花费添加成功！',U('/Admin/FinanceManage/finance'));
				}
				else 
				{
					$this->error("财务花费添加失败！");
				}
			}
			else 
			{
				if(isset($_GET['id']))
				{
					$id = $_GET['id'];
					$info = $mono_cost->where("id=$id")->find();
					$apply_time = strtotime($info[apply_time]);
					$oper_time = strtotime($info[oper_time]);
					$this->assign("info",$info);
					$this->apply_time=$apply_time;
					$this->oper_time=$oper_time;
					$this->assign('tpltitle','修改');
				}
				else
				{
					$this->assign('tpltitle','添加');
				}
			}
			$this->display();
		}
		//财务列表
		public function finance()
		{
			$mono_cost = M("mono_cost");
		//	import('Org.Util.Page');// 导入分页类
			$count = $mono_cost->count();// 查询满足要求的总记录数 $map表示查询条件
			$Page = new \Think\Page1($count);// 实例化分页类 传入总记录数
			// 进行分页数据查询 注意page方法的参数的前面部分是当前的页数使用 $_GET[p]获取
			$nowPage = isset($_GET['p'])?$_GET['p']:1;
			$show = $Page->show();// 分页显示输出
			$mono_cost_list = $mono_cost->order('apply_time desc')->page($nowPage.','.C('LISTROWS'))->select();
			$this->assign("mono_cost_list",$mono_cost_list);
			$this->assign("page",$show);
			$this->display();
		}
		//财务修改
		public function finance_edit()
		{
			$mono_cost = M("mono_cost");
			if(isset($_POST['id']))
			{
				$id = $_POST['id'];
				$data['name'] = $_POST['name'];
				$data['message'] = $_POST['content'];
				$data['applicant'] = $_POST['applicant'];
				$data['operator'] = $_POST['operator'];
				$data['apply_time'] = $_POST['apply_time'];
				$data['oper_time'] = $_POST['oper_time'];
				$data['status'] = $_POST['status'];
				$update_finance = $mono_cost->where("id=$id")->save($data);
				if($update_finance)
				{
					$this->success('财务花费修改成功！',U('/Admin/FinanceManage/finance'));
				}
				else
				{
					$this->error("财务花费修改失败！");
				}
			}
		}
		//财务删除
		public function finance_del() 
		{
			$mono_cost = M("mono_cost");
			if(isset($_GET['id']))
			{
				$id = $_GET['id'];
				$delete_finance = $mono_cost->where("id=$id")->delete();
				if($delete_finance)
				{
					$this->success('财务花费删除成功！',U('/Admin/FinanceManage/finance'));
				}
				else
				{
					$this->error("财务花费删除失败！");
				}
			}
		}
		
		//审核退款申请
		public function refund_audit(){
			$order_return_pricDB=M('order_return_price');
			
		//	import('Org.Util.Page');// 导入分页类
			$count = $order_return_pricDB->where('`status` = 0')->count();// 查询满足要求的总记录数 $map表示查询条件
			$Page = new \Think\Page1($count);// 实例化分页类 传入总记录数
			// 进行分页数据查询 注意page方法的参数的前面部分是当前的页数使用 $_GET[p]获取
			$nowPage = isset($_GET['p'])?$_GET['p']:1;
			$show = $Page->show();// 分页显示输出
			$list=$order_return_pricDB->where('`status` = 0')->order('id desc')->page($nowPage.','.C('LISTROWS'))->select();
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
		//审核通过      1  通过     9  未通过
		public function refund_audit_operate()
		{
			$username = session('username');    // 用户名
			$order_return_pricDB=M('order_return_price');           //退款申请表
			if($_GET['id']){
				$date['status']=$_GET['sta'];
				$date['aduit_time']=time();
				$date['aduit']=$username;
				$result=$order_return_pricDB->where('`id`='.$_GET['id'])->save($date);
				if($result){
					$this->success('审核操作成功！',U('/Admin/FinanceManage/refund_audit'));
					}else{
					$this->error('审核操作失败');
				}
			}
		}
		//退款列表
		public function refund_list()
		{
			$username = session('username');    // 用户名
			$order_return_pricDB=M('order_return_price');           //退款申请表
			if($_GET['id'])
			{
				$date['status']=2;
				$date['operator_time']=time();
				$date['operator']=$username;
				$result=$order_return_pricDB->where('`id`='.$_GET['id'])->save($date);
				if($result)
				{
					$this->success('审核操作成功！',U('/Admin/FinanceManage/refund_list'));
				}else
				{
					$this->error('审核操作失败');
				}
			}
			else
			{
				$list=$order_return_pricDB->where('`status` = 1')->order('id desc')->select();
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
			//	import('Org.Util.Page');// 导入分页类
				$count = $order_return_pricDB->where('`status` = 2')->count();// 查询满足要求的总记录数 $map表示查询条件
				$Page = new \Think\Page1($count);// 实例化分页类 传入总记录数
				$nowPage = isset($_GET['p'])?$_GET['p']:1;
				$show = $Page->show();// 分页显示输出
				$list_over=$order_return_pricDB->where('`status` = 2')->order('id desc')->page($nowPage.','.C('LISTROWS'))->select();
				foreach($list_over as $k=>$v)
				{
					$list_overst[$k]['sta']=refund_sta($v['status']);
					$list_over[$k]['come_from_id'] = get_come_from_id($v['come_from']);
					if($v['order_id'])
					{
						$list_over[$k]['url'] = '<a style="text-decoration: underline; color: #03c;" target="_blank" href="'.__APP__.'/Admin/OrderManage/order_web/order_status/all/come_from_id/'.$list_over[$k]['come_from_id'].'/order_number/'.$v["order_id"].'.html" >'.strtoupper($v['come_from']).$v["order_id"].'</a>';
						$list_over[$k]['order'] = M('order_web')->field('email,payment_style')->where('`order_number` ='.$v['order_id'] .' and `come_from_id`='.$list_over[$k]['come_from_id'])->find();
					}
					elseif($v['transaction_number'])
					{
						$list_over[$k]['url'] = '<a style="text-decoration: underline; color: #03c;" target="_blank" href="'.__APP__.'/Admin/OrderManage/order_plat/order_status/all/come_from_id/'.$list_over[$k]['come_from_id'].'/order_number/'.$v["transaction_number"].'.html" >'.$v["transaction_number"].'</a>';
						$list_over[$k]['order'] = M('order_plat_form')->field('email')->where('`order_number` ="'.$v['transaction_number'] .'" and `come_from_id`='.$list_over[$k]['come_from_id'])->find();
					}
				}	
				$this->assign('list',$list);	
				$this->assign('list_over',$list_over);
				$this->assign('page',$show);
				$this->display();
			
			}
			
		}
	
	
		
		//添加广告费
		public function adwords_fee_add()
		{
			$adwords_fee = M("adwords_fee");
			if(isset($_POST['dosubmit']))
			{
				$data["come_from"] = $_POST["come_from"];
				$data["fee"] = $_POST["fee"];
				$data["operate"] = $_POST["operate"];
				$data["country"] = $_POST["country"];
				$data["fee_date"] = $_POST["fee_date"];
				$insert = $adwords_fee->add($data);
				if($insert)
				{
					$this->success('广告费添加成功！',U('/Admin/FinanceManage/adwords_fee'));
				}
				else
				{
					$this->error("广告费添加失败！");
				}
			}
			else
			{
				if(isset($_GET['id']))
				{
					$id = $_GET['id'];
					$info = $Project->where("id=$id")->find();
					$fee_date = strtotime($info[fee_date]);
					$this->assign("info",$info);
					$this->fee_date=$fee_date;
					$this->assign('tpltitle','修改');
				}
				else
				{
					$this->assign('tpltitle','添加');
				}
			}
			$this->display();
		}
		//广告费列表
		public function adwords_fee1()
		{
			echo "dd";
			die();
			$adwords_fee = M("adwords_fee");
				
		//	import('Org.Util.Page');// 导入分页类
			$count = $adwords_fee->count();// 查询满足要求的总记录数 $map表示查询条件
			$Page = new \Think\Page1($count);// 实例化分页类 传入总记录数
			// 进行分页数据查询 注意page方法的参数的前面部分是当前的页数使用 $_GET[p]获取
			$nowPage = isset($_GET['p'])?$_GET['p']:1;
			$show = $Page->show();// 分页显示输出
			get_to_order(46538);
			if(isset($_POST['sub']))
			{
				$begin_time = $_POST['begin_time'];
				$end_time = $_POST['end_time'];
			}
			else
			{
				$begin_time=date("Y-m-01",time());
				$end_time=date("Y-m-d",(time()+3600*24));
			}
			$map['fee_date'] = array("between","$begin_time,$end_time");
			if(isset($_GET['come_from']))
			{
				$map['come_from'] = $_GET['come_from'];
				$adwords_fee_list = $adwords_fee->where($map)->order('fee_date desc')->page($nowPage.','.C('LISTROWS'))->select();
			}
			else
			{
				$adwords_fee_list = $adwords_fee->where($map)->order('fee_date desc')->page($nowPage.','.C('LISTROWS'))->select();
			}
			$this->assign("adwords_fee_list",$adwords_fee_list);
			$this->assign("page",$show);
			$this->display();
		}
		//广告费删除
		public function adwords_fee_del()
		{
			$adwords_fee = M("adwords_fee");
			if(isset($_GET['id']))
			{
				$id = $_GET['id'];
				$fee_delete = $adwords_fee->where("id=$id")->delete();
				if($fee_delete)
				{
					$this->success('广告费删除成功！',U('/Admin/FinanceManage/adwords_fee',array('come_from'=>$_GET['come_from'])));
				}
				else
				{
					$this->error("广告费删除失败！");
				}
			}
		}
		//APR统计
		public function adwords_apr()
		{
			$this->display();
		}
	
	}
?>