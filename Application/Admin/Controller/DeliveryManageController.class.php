<?php
namespace Admin\Controller;
use Admin\Controller\CommonController;
class DeliveryManageController extends CommonController
{  
    public function index()
    {
        $this->display();
    }
    
    public function deliverycost_upload()
    {
    	//上传到指定
    	$rootPath = './Public/excel_upload/deivery_weight/';
    	if (!file_exists($rootPath)) mkdir($rootPath);          
    	$config = array(
    		'exts' => array('xls', 'csv', 'txt'),// 设置附件上传类型
    		'rootPath' => $rootPath, // 设置附件上传根目录
    		'saveName' => date("YmdHis"), // 设置附件上传（子）目录
    		'autoSub' => false,
    		'replace' => true,
    	);
    	$upload = new \Think\Upload($config);// 实例化上传类
    	$info = $upload->upload();
    	
    	vendor('PHPExcel.SimplePHPExcel');
    	
    	//用于验证country（B列）的函数。这里用于示范，并无太大实际意义
    	$func_check_country = function($value)
    	{
    		if( strlen($value)!=2 ){ 
    			$msg='格式不符';
    			$state = 'error';
    		}
    		return array('data'=>$value,'msg'=>$msg,'state'=>$state);
    	};
    	$field = array(
    		//'列'=>array('字段','回调函数'),
    		'A'=>array('style', 'simple_check_empty'),
    		'B'=>array('country', $func_check_country),
    		'C'=>array('lower_weight', 'simple_check_float'),
    		'D'=>array('upper_weight', 'simple_check_float'),
    		'E'=>array('price', 'simple_check_float'),
    	);
    	$option=array(
    		'uploadfile'=> $rootPath.$info['file']['savename'], //必须。该文件名不能含中文
    		'table'		=> 'order_delivery_cost', //必须
    		'field'		=> $field, //单表需要(option无配置callback)
    		'mode'		=> 'add', //添加模式
//     		'mode'		=> 'edit','primary'	=> 'A',//修改模式，需要设置主键
    		"start_row"	=> 2,//可选。设置有效内容的 起始行数，默认2
    		"unlink"	=> false,//可选。是否删除文件，默认false
//     		"callback"	=> $callback,//用于插入多表的情况
    	);
    	$log = importExcel($option);
    	
    	//处理显示log
    	$column_list = array('A','B','C','D','E','state');
    	$this->column_list = $column_list;
    	$this->log_list = $log;
    	//通过日志分析
    	$log_html = $this->fetch('Public:log_simple_excel');
    	$this->log_html = $log_html;
    	
    	$this->display();
    }
    
    public function delivery_weight(){
	    $cost=D("order_delivery_cost");
    	if(isset($_GET[act])){
    		$count=$cost->where(array('style'=>$_GET[style],'country'=>$_GET[country]))->count();// 查询满足要求的总记录数
    		$Page=new \Think\Page($count,10);// 实例化分页类 传入总记录数和每页显示的记录数
    		$show=$Page->show_pintuer();// 分页显示输出
    		$list=$cost->where(array('style'=>$_GET[style],'country'=>$_GET[country]))->limit($Page->firstRow.','.$Page->listRows)->select();
    		$this->assign('list',$list);// 赋值数据集
    		$this->assign('page',$show);// 赋值分页输出
    	}else{
    		$count=$cost->where(array('style'=>'DHL','country'=>'HK'))->count();
    		$Page=new \Think\Page($count,15);// 实例化分页类 传入总记录数和每页显示的记录数
    		$show=$Page->show_pintuer();// 分页显示输出
    		$list=$cost->where(array('style'=>'DHL','country'=>'HK'))->select();
    		$list=$cost->where(array('style'=>'DHL','country'=>'HK'))->limit($Page->firstRow.','.$Page->listRows)->select();
    		$this->assign('list',$list);
    		$this->assign('page',$show);// 赋值分页输出
    	}
    	$cost_way=$cost->distinct(true)->field('style')->select();
    	$cost_country=$cost->distinct(true)->field('country')->select();
    	$this->assign("cost_way",$cost_way);
    	$this->assign("cost_country",$cost_country);
    	$this->display();
    }
    
    public function delivery_weight_act(){
    	$cost=D("order_delivery_cost");
    	if(isset($_GET[act])){
    		if($_GET[act]=="delete"){
    			$edit=$cost->where(array('id'=>$_GET[id]))->delete();
    			if($edit){
    				$this->success("删除成功",__URL__/delivery_weight);
    			}else{
    				$this->error("删除失败");
    			}
    		}
    		if($_GET[act]=="add"){
    			if($_POST[price]!=''){
    				$data['style']=$_POST[style];
    				$data['country']=$_POST[country];
    				$data['lower_weight']=$_POST[lower_weight];
    				$data['upper_weight']=$_POST[upper_weight];
    				$data['price']=$_POST[price];
    				$add=$cost->data($data)->add();
    				if($add){
    					$this->success("添加成功",__URL__/delivery_weight);
    				}else{
    					$this->error("添加失败");
    				}
    			}else{
    				$this->error("添加失败");
    			}
    		}
    	}
    }
    
    public function delivery_weigh_add(){
    	$cost=D("order_delivery_cost");
    	$cost_way=$cost->distinct(true)->field('style')->select();
    	$cost_country=$cost->distinct(true)->field('country')->select();
    	$this->assign("cost_way",$cost_way);
    	$this->assign("cost_country",$cost_country);
    	$this->display();
    }
    
    public function delivery_baf(){
    	$baf=D("order_delivery_baf");
    	$baf_list=$baf->order('id desc')->select();
    	$this->assign("baf_list",$baf_list);
    	$this->display();
    }
    
    public function delivery_baf_act(){
    	if($_GET[act]=="add"){
    		if($_POST[percent]!=''){
    			$baf=D("order_delivery_baf");
    			$data['style']=$_POST[style];
    			$data['percent']=$_POST[percent];
    			$data['operator']=$_POST[operator];
    			$data['time']=$_POST[time];
    			$baf_add=$baf->data($data)->add();
    			if($baf_add){
    				$this->success("添加成功",U('DeliveryManage/delivery_baf'));
    			}else{
    				$this->error("添加失败");
    			}
    		}else{
    			$this->error("添加失败");
    		}
    	}else{
    		$cost=D("order_delivery_cost");
    		$cost_way=$cost->distinct(true)->field('style')->select();
    		$this->assign("cost_way",$cost_way);
    		$this->display();
    	} 	
    }
    
    public function delivery_other_price(){
    	$order_web_history=M("order_web_status");
    	$new_web_history_list=array();
    	if(IS_POST){
    		if(!empty($_POST[delivery_number])){
    			$where["delivery_number"]=$_POST[delivery_number];
    		}
    		if(!empty($_POST[start_time])){
    			$where["time"]=array("between",array($_POST[start_time],$_POST[end_time]));
    		}
    		if(!empty($_POST[order_number])){
    			$where["order_number"]=$_POST[order_number];
    		}
    		if(I('post.come_from')=='web'){
    			$order_web_model = D('order_web');
    			$history_list_sql=$order_web_model->status('history',$where)->field("a.id")->select();
    		}elseif(I('post.come_from')=='plat'){
    			$order_plat_model = D('order_plat_form');
    			$history_list_sql=$order_plat_model->status('history',$where)->field("a.id")->select();
    		}
    	}else{
    		$history_list_sql=$order_web_history->alias('ows')->join("left join order_web as ow on ow.id=ows.order_web_id")->where("status='history'")->field("ow.id")->select();
    	}

    	foreach ($history_list_sql as $value){
    		if(I('post.come_from')=='web' || empty($_POST[come_from])){
    			$web_order_information=D("OrderWeb");
    			$order_information_sql=$web_order_information->where(array("id"=>$value[id]))->relation(array("order_web_address","delivery_info","detail_info","other_price_info"))->find();
    			
    			$where_weight["style"]=$order_information_sql[detail_info][style];
    			$where_weight["country"]=$order_information_sql[order_web_address][0][country];
    			$where_weight["lower_weight"]=array("lt",$order_information_sql[delivery_info][weight]);
    			$where_weight["upper_weight"]=array("egt",$order_information_sql[delivery_info][weight]);
    			$weight_price=M("order_delivery_cost")->where($where_weight)->field("price")->find();
    			
    			$delivery_time=M("order_delivery_detail")->where(array("order_web_id"=>$order_information_sql[id]))->field("time")->order("id asc")->find();
    			$delivery_baf_sql=M("order_delivery_baf")->where("time",array("lt",$delivery_time[time]))->field("percent")->order("id desc")->find();
    			
    			$this->assign("come_from","web");
    			
    		}elseif(I('post.come_from')=='plat'){
    			$plat_order_information=D("OrderPlatForm");
    			$order_information_sql=$plat_order_information->where(array("id"=>$value[id]))->relation(array("shipping_info","delivery_info","detail_info","other_price_info"))->find();
    			
    			$where_weight["style"]=$order_information_sql[detail_info][style];
    			$where_weight["country"]=$order_information_sql[shipping_info][country];
    			$where_weight["lower_weight"]=array("lt",$order_information_sql[delivery_info][weight]);
    			$where_weight["upper_weight"]=array("egt",$order_information_sql[delivery_info][weight]);
    			$weight_price=M("order_delivery_cost")->where($where_weight)->field("price")->find();

    			$delivery_time=M("order_delivery_detail")->where(array("order_platform_id"=>$order_information_sql[id]))->field("time")->order("id asc")->find();
    			$delivery_baf_sql=M("order_delivery_baf")->where("time",array("lt",$delivery_time[time]))->field("percent")->order("id desc")->find();
    			
    			$this->assign("come_from","plat");
    			
    		}
				$history_list_one=array("id"=>$order_information_sql[id],"order_number"=>$order_information_sql[order_number],"style"=>$order_information_sql[detail_info][style],"delivery_number"=>$order_information_sql[detail_info][delivery_number],"weight_price"=>$weight_price[price],"paf_price"=>$delivery_baf_sql[percent],"time"=>$order_information_sql[date_time]);
				
    		if($order_information_sql[other_price_info]){
    			$history_list_one+=array("tariffs"=>$order_information_sql[other_price_info][tariffs],"remote"=>$order_information_sql[other_price_info][remote],"operator"=>$order_information_sql[other_price_info][operator]);
    		}
    		$new_history_list[]=$history_list_one;
    	}
    	
	    	$count=count($new_history_list);//得到数组元素个数
	    	$Page= new \Think\Page($count,C('LISTROWS'));// 实例化分页类 传入总记录数和每页显示的记录数
	    	$new_array = array_slice($new_history_list,$Page->firstRow,$Page->listRows);
	    	$show= $Page->show_pintuer();// 分页显示输出﻿
	    	$this->assign("other_price_list",$new_array);
	    	$this->assign('page',$show);// 赋值分页输出
	    	$this->display();
    }
    
    public function delivery_other_price_edit(){
    	$other_price=D("order_delivery_other_price");
    	if($_GET[act]=="edit"){
    		$data['tariffs']=$_POST[tariffs];
    		$data['remote']=$_POST[remote];
    		$data['operator']=$_POST[operator];
    		$other_price_edit=$other_price->data($data)->where(array('id'=>$_GET[id]))->save();
    		if($other_price_edit){
    			$this->success("修改成功",U('DeliveryManage/delivery_other_price'));
    		}else{
    			$this->error("修改失败");
    		}
    	}else{
    		$other_price_one_list=$other_price->where(array('id'=>$_GET[id]))->field('order_web_id,order_platform_id,tariffs,remote')->find();
    		$this->assign("other_price_one_list",$other_price_one_list);
    		$this->display();
    	}	
    }
    
    public function export_excle_delivery(){
    		$export_array=array();
    		$checked=$_POST[checked];
    		for($i=0;$i<count($checked);$i++){
    			if(I('post.come_from')=='web'){
    				$order_web_model = D('order_web');
    				$order_information_sql=$order_web_model->where(array("id"=>$checked[$i]))->relation(array("order_web_address","delivery_info","detail_info","other_price_info"))->find();
    				
    				$where_weight["style"]=$order_information_sql[detail_info][style];
    				$where_weight["country"]=$order_information_sql[order_web_address][0][country];
    				$where_weight["lower_weight"]=array("lt",$order_information_sql[delivery_info][weight]);
    				$where_weight["upper_weight"]=array("egt",$order_information_sql[delivery_info][weight]);
    				$weight_price=M("order_delivery_cost")->where($where_weight)->field("price")->find();
    				 
    				$delivery_time=M("order_delivery_detail")->where(array("order_web_id"=>$order_information_sql[id]))->field("time")->order("id asc")->find();
    				$delivery_baf_sql=M("order_delivery_baf")->where("time",array("lt",$delivery_time[time]))->field("percent")->order("id desc")->find();
    			}elseif(I('post.come_from')=='plat'){
    				$plat_order_information=D("OrderPlatForm");
    				$order_information_sql=$plat_order_information->where(array("id"=>$checked[$i]))->relation(array("shipping_info","delivery_info","detail_info","other_price_info"))->find();
    				
    				$where_weight["style"]=$order_information_sql[detail_info][style];
    				$where_weight["country"]=$order_information_sql[shipping_info][country];
    				$where_weight["lower_weight"]=array("lt",$order_information_sql[delivery_info][weight]);
    				$where_weight["upper_weight"]=array("egt",$order_information_sql[delivery_info][weight]);
    				$weight_price=M("order_delivery_cost")->where($where_weight)->field("price")->find();
    				
    				$delivery_time=M("order_delivery_detail")->where(array("order_platform_id"=>$order_information_sql[id]))->field("time")->order("id asc")->find();
    				$delivery_baf_sql=M("order_delivery_baf")->where("time",array("lt",$delivery_time[time]))->field("percent")->order("id desc")->find();
    			}
 					$history_list_one=array("order_number"=>$order_information_sql[order_number],"style"=>$order_information_sql[detail_info][style],"delivery_number"=>$order_information_sql[detail_info][delivery_number],"weight_price"=>$weight_price[price],"paf_price"=>$delivery_baf_sql[percent],"time"=>$order_information_sql[date_time]);
    			if($order_information_sql[other_price_info]){
    				$history_list_one+=array("tariffs"=>$order_information_sql[other_price_info][tariffs],"remote"=>$order_information_sql[other_price_info][remote],"operator"=>$order_information_sql[other_price_info][operator]);
    			}
    			$export_array[]=$history_list_one;
    		}
//     		print_r($export_array);
//     		exit();
    		$title_array=array("订单号","快递","运单号","基本运费","燃油费(%)","时间","关税","偏远费","操作人");
    		exportExcel($export_array,null,$title_array);	
    }
	//快递优先选择
	public function delivery_priority()
	{
		$order_delivery_priorityDB=M("order_delivery_priority");
		$countriesDB=M('countries');
    	if(isset($_GET[act])){
			if($_GET[style])
			{
			 	$date['style'] = 	$_GET[style];
			}
			if($_GET[country])
			{
			 	$date['country'] = $_GET[country];
			}
			
			
    		$count=$order_delivery_priorityDB->where($date)->count();// 查询满足要求的总记录数
    		
			$Page       = new \Think\Page1($count);// 实例化分页类 传入总记录数
			$nowPage = isset($_GET['p'])?$_GET['p']:1;
			$show       = $Page->show();// 分页显示输出
			
			$list=$order_delivery_priorityDB->where($date)->page($nowPage,C('LISTROWS'))->order('id desc')->select();
			
			$this->style= $_GET[style];
			$this->come_from= $_GET[country];
		//	dump($list);exit;
    	}else{
    		$count=$order_delivery_priorityDB->where('1=1')->count();// 查询满足要求的总记录数
			
    		$Page       = new \Think\Page1($count);// 实例化分页类 传入总记录数
			$nowPage = isset($_GET['p'])?$_GET['p']:1;
			$show       = $Page->show();// 分页显示输出
			
			$list=$order_delivery_priorityDB->page($nowPage,C('LISTROWS'))->order('id desc')->select();
			//dump($list);exit;
			
    	}
    	$style=$order_delivery_priorityDB->distinct(true)->field('style')->select();
    	$country=$countriesDB->select();
		
		$this->assign('list',$list);// 赋值数据集
    	$this->assign('page',$show);// 赋值分页输出
    	$this->assign("style",$style);
    	$this->assign("country",$country);
		
		$this->display();
	}
	//快递优先选择 新增 修改
	 public function delivery_priority_add(){
    	$countriesDB=M('countries');
		$order_delivery_priorityDB = M('order_delivery_priority');
		$username = session('username');
		if(I('get.id')) //修改
		{
			$info = $order_delivery_priorityDB->where('`id` ='.I('get.id'))->find();
			$this->info = $info;
			$this->id = I('get.id');	
			$country=$countriesDB->distinct(true)->select();
			$style= delivery_style();
	
			$this->assign("style",$style);
			$this->assign("country",$country);
			$this->display(); 
		}
		elseif(IS_POST)  //表单体检
		{
			
			$date['style'] = I('post.style');
			$date['country'] = I('post.country');
			$date['lower_weight'] = I('post.lower_weight');
			$date['upper_weight'] = I('post.upper_weight');
			$date['operator'] = $username;
			$date['date_time'] = time();
			if(I('post.id'))
			{
				$where['id'] = I('post.id');
				$return = $order_delivery_priorityDB->where($where)->save($date);
				if($return)
				{
					$this->success('修改成功');
				}
				else
				{
					$this->error('修改失败');
				}
				
			}
			else
			{
				$return = $order_delivery_priorityDB->add($date);
				if($return)
				{
					$this->success('添加成功');
				}
				else
				{
					$this->error('添加失败');
				}
			}
		}
		else  //新增
		{
			$country=$countriesDB->distinct(true)->select();
			$style= delivery_style();
			$this->assign("style",$style);
			$this->assign("country",$country);
			$this->display(); 
		}
	}
	//快递优先选择 删除
	public function delivery_priority_delete()
	{
		$order_delivery_priorityDB = M('order_delivery_priority');
		
		if(I('get.id'))
		{
			$return = 	$order_delivery_priorityDB -> where('`id` ='.I('get.id'))->delete();
			if($return)
			{
				$this->success('删除成功');
			}
			else
			{
				$this->error('删除失败');
			}
		}
		else
		{
			$this->error('参数出现错误');	
		}
	}
}
