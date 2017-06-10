<?php
namespace Admin\Controller;
class ProductoutController extends CommonController
{  
    public function index()
    {
        $this->display();
    }
	//人员出库
	public function product_out_contrived()
	{
		$idproductcodeDB=M('id_product_code');                    //产品信息
		$productstockoutDB=M('product_stock_out');               //出库记录
		$productstockoutdetailDB=M('product_stock_out_detail');  //出库记录附表
		
		
		//import('Org.Util.Page');// 导入分页类
		$coun=$productstockoutDB->where('`operator`!="-1"')->count();
        $Page       =new \Think\Page1($coun);// 实例化分页类 传入总记录数
		$nowPage = isset($_GET['p'])?$_GET['p']:1;
        $show       = $Page->show();// 分页显示输出s
		
		$pro_out=$productstockoutDB->where('`operator`!="-1"')->order('id,date_time desc')->page($nowPage ,C('LISTROWS'))->select();
		$pro_out01=array();
		$pro_out02=array();
		foreach($pro_out as $k=>$v){
			 $pro_out_detail=$productstockoutdetailDB->where('`product_stock_out_id` ='.$pro_out[$k][id])->find();
			 $name=$idproductcodeDB->where('`id`='.$pro_out_detail['code_id'])->find();
			 if($name){
			 	$pro_out01[$k]['code_name']=$name['name']."&nbsp;&nbsp;".$name[code];
			 	$pro_out01[$k]['number']=$pro_out_detail['number'];
			 	$pro_out01[$k]['sty']=panduan_style($pro_out_detail['style']);
			 	$pro_out01[$k]['operator']=$pro_out_detail['operator'];
			 	$pro_out01[$k]['date_time']=$v['date_time'];
			 	$pro_out01[$k]['reason']=$v['reason'];
			 }else{
			 	$name=M("id_product_sku")->where('`id`='.$pro_out_detail['sku_id'])->find();
			 	$pro_out02[$k]['code_name']=$name['name']."&nbsp;&nbsp;".$name[sku];
			 	$pro_out02[$k]['number']=$pro_out_detail['number'];
			 	$pro_out02[$k]['sty']=panduan_style($pro_out_detail['style']);
			 	$pro_out02[$k]['operator']=$pro_out_detail['operator'];
			 	$pro_out02[$k]['date_time']=$v['date_time'];
			 	$pro_out02[$k]['reason']=$v['reason'];
			 }
		}
		$this->assign('page',$show);	
		$this->assign('pro_out01',$pro_out01);
		$this->assign('pro_out02',$pro_out02);
		$this->display();
	}
	//系统出库
	public function product_out_system()
	{
		$idproductcodeDB=M('id_product_code');                    //产品信息
		$productstockoutDB=M('product_stock_out');               //出库记录
		$productstockoutdetailDB=M('product_stock_out_detail');  //出库记录附表
		
		$record_num02=$productstockoutDB->where('`operator`="-1"')->count();
		//import('Org.Util.Page');// 导入分页类
		$coun=$productstockoutDB->where('`operator`="-1"')->count();
        $Page       = new \Think\Page1($coun);// 实例化分页类 传入总记录数
		$nowPage = isset($_GET['p'])?$_GET['p']:1;
        $show       = $Page->show();// 分页显示输出
		
		$pro_out02=$productstockoutDB->where('`operator`="-1"')->order('id,date_time desc')->page($nowPage,C('LISTROWS'))->select();
		foreach($pro_out02 as $k=>$v){
			 $pro_out_detail02=$productstockoutdetailDB->where('`id` ='.$pro_out02[$k][id])->find();
			 $code_name02=$idproductcodeDB->where('`id`='.$pro_out_detail02['code_id'])->find();
			 $pro_out02[$k]['number']=$pro_out_detail02['number'];
			 $pro_out02[$k]['sty']=panduan_style($pro_out_detail['style']);
			 $pro_out02[$k]['code_name']=$code_name02['name'];
			 $pro_out02[$k]['username']=$pro_out_detail02['operator'];
			}
		
		$this->assign('pro_out02',$pro_out02);
		$this->assign('page',$show);	
		$this->display();
	}
	
	//已弃用
	//出库列表     
	public function product_out_list()
    {
		$idproductcodeDB=M('id_product_code');                    //产品信息
		$productstockoutDB=M('product_stock_out');               //出库记录
		$productstockoutdetailDB=M('product_stock_out_detail');  //出库记录附表
		$userDB=M('user');
		$record_num01=$productstockoutDB->where('`operator`!="-1"')->order('id,date_time desc')->count();
			//人员输入
			    //ajax
			   if($_POST['ac_page01']){                                   
					$pro_out01=$productstockoutDB->where('`operator`!="-1"')->order('id,date_time desc')->limit($_POST['ac_page01'],C('LISTROWS'))->select();
					$record_num=$productstockoutDB->where('`operator`!="-1"')->order('id,date_time desc')->count();
					foreach($pro_out01 as $k=>$v){
						 $pro_out_detail=$productstockoutdetailDB->where('`id` ='.$pro_out01[$k][id])->find();
						 $code_name=$idproductcodeDB->where('`id`='.$pro_out_detail['code_id'])->find();
						 $pro_out01[$k]['number']=$pro_out_detail['number'];
						 $pro_out01[$k]['sty']=panduan_style($pro_out_detail['style']);
						 $pro_out01[$k]['date_time']=date('Y-m-d H:i:s',$pro_out01[$k]['date_time']);
						 $pro_out01[$k]['code_name']=$code_name['name'];
						 $pro_out01[$k]['username']=userName($pro_out01[$k]['operator']);
						 $pro_out01[$k]['start']=$_POST['ac_page01']+C('LISTROWS');                  //开始输出的条数
						 $pro_out01[$k]['record_num']=$record_num;
						}
					echo json_encode($pro_out01);
					exit;
				}else{
					$pro_out01=$productstockoutDB->where('`operator`!="-1"')->order('id,date_time desc')->limit(0,C('LISTROWS'))->select();
					$record_num01=$productstockoutDB->where('`operator`!="-1"')->order('id,date_time desc')->count();
					foreach($pro_out01 as $k=>$v){
						 $pro_out_detail=$productstockoutdetailDB->where('`id` ='.$pro_out01[$k][id])->find();
						 $code_name=$idproductcodeDB->where('`id`='.$pro_out_detail['code_id'])->find();
						 $user_name=$userDB->where('`id`='.$pro_out01[$k]['operator'])->find();
						 $pro_out01[$k]['number']=$pro_out_detail['number'];
						 $pro_out01[$k]['sty']=panduan_style($pro_out_detail['style']);
						 $pro_out01[$k]['code_name']=$code_name['name'];
						 $pro_out01[$k]['username']=userName($pro_out01[$k]['operator']);
						}
				}
				//系统输入	
			   if($_POST['ac_page02']){   //ajax
					$pro_out02=$productstockoutDB->where('`operator`="-1"')->order('id,date_time desc')->limit($_POST['ac_page02'],C('LISTROWS'))->select();
					$record_num=$productstockoutDB->where('`operator`="-1"')->order('id,date_time desc')->count();
					foreach($pro_out02 as $k=>$v){
						 $pro_out_detail=$productstockoutdetailDB->where('`id` ='.$pro_out02[$k][id])->find();
						 $code_name=$idproductcodeDB->where('`id`='.$pro_out_detail['code_id'])->find();
						 $pro_out02[$k]['number']=$pro_out_detail['number'];
						 $pro_out02[$k]['sty']=panduan_style($pro_out_detail['style']);
						 $pro_out02[$k]['date_time']=date('Y-m-d H:i:s',$pro_out02[$k]['date_time']);	 
						 $pro_out02[$k]['code_name']=$code_name['name'];
						 $pro_out02[$k]['username']=userName($pro_out02[$k]['operator']);
						 $pro_out02[$k]['start']=$_POST['ac_page02']+C('LISTROWS');    //开始输出的条数
						 $pro_out02[$k]['record_num']=$record_num;
						}
					echo json_encode($pro_out02);
					exit;
				}else{
					$pro_out02=$productstockoutDB->where('`operator`="-1"')->order('id,date_time desc')->limit(0,C('LISTROWS'))->select();
					$record_num02=$productstockoutDB->where('`operator`="-1"')->order('id,date_time desc')->count();
					foreach($pro_out02 as $k=>$v){
						 $pro_out_detail02=$productstockoutdetailDB->where('`id` ='.$pro_out02[$k][id])->find();
						 $code_name02=$idproductcodeDB->where('`id`='.$pro_out_detail02['code_id'])->find();
						 $pro_out02[$k]['number']=$pro_out_detail02['number'];
						 $pro_out02[$k]['sty']=panduan_style($pro_out_detail['style']);
						 $pro_out02[$k]['code_name']=$code_name02['name'];
						 $pro_out02[$k]['username']=userName($pro_out02[$k]['operator']);
						}
				}
		$this->assign('record_num01',$record_num01);
		$this->assign('record_num02',$record_num02);
		$this->assign('pro_out01',$pro_out01);
		$this->assign('pro_out02',$pro_out02);
        $this->display();
    }
	//出库记录输入
	public function product_out_add()
    {
		$idproductcodeDB=M('id_product_code');                    //产品信息
		$productstockoutDB=M('product_stock_out');               //出库记录主表  
  		$productstockoutdetailDB=M('product_stock_out_detail');  //出库记录附表
		$product_stockDB=M('product_stock');                     //库存信息
		$username = session('username');    // 用户名
		if($_POST['dosubmit']){
			$date['number'] = $_POST['number'];
			$date['reason'] = $_POST['reason'];   
			$date['operator'] = $username;
			$date['date_time'] = time(); 
			$date['style'] = $_POST['style'];
			if(I('get.act')=='item'){    //单品
				$date['code_id'] =$_POST['code_name'];
				$coun=$product_stockDB->where('`style`='.$_POST['style']. ' and `code_id` = '.$_POST['code_name'])->find();
				if($coun['number'] > $_POST['number']){
					$num['number']=$coun['number']-$_POST['number'];
					$product_stock=$product_stockDB->where('`style`='.$_POST['style']. ' and `code_id` = '.$_POST['code_name'])->save($num);//库存表记录
					$pro_out_id=$productstockoutDB->add($date);
					$date['product_stock_out_id']=$pro_out_id;
					$detail=$productstockoutdetailDB->add($date);
					if($pro_out_id and $detail){
						$this->success('出库记录添加成功！');
					}else{
						$this->error('出库记录添加失败!');
					}
				}else{
				$this->success('失败！！<br/> 库存数量不足！！');
				}
			}elseif(I('get.act')=='set'){
				$date['sku_id'] =$_POST['sku'];
				$coun=M("product_stock_set")->where('`style`='.$_POST['style']. ' and `sku_id` = '.$_POST['sku'])->find();
				if($coun['number'] > $_POST['number']){
					$num['number']=$coun['number']-$_POST['number'];
					$product_stock=M("product_stock_set")->where('`style`='.$_POST['style']. ' and `sku_id` = '.$_POST['sku'])->save($num);//库存表记录
					$pro_out_id=$productstockoutDB->add($date);
					$date['product_stock_out_id']=$pro_out_id;
					$detail=$productstockoutdetailDB->add($date);
					if($pro_out_id and $detail){
						$this->success('出库记录添加成功！');
					}else{
						$this->error('出库记录添加失败!');
					}
				}else{
				$this->success('失败！！<br/> 库存数量不足！！');
				}	
			}
		}else{
			$code_id=$_POST['code_id'];
			
			if(isset($code_id)){        //判断ajax
				$style=$product_stockDB->where('`code_id` = '.$code_id)->select();
				foreach($style as $k=>$v){
					$style[$k]['name']=panduan_style($style[$k]['style']);
				}
				echo json_encode($style);
				exit;
			}
		$this->assign("reason",product_out_note());
		$this->assign("catalog",select_all("catalog"));	
		$this->assign('code_name',$code_name);
		$this->assign("come_from",come_from_new());
		$this->assign('tpltitle','添加');
        $this->display();
		}
	}
	
	//库存订单列表
	public function stock_order_list()
	{
		$order_model = D('product_stock_order');
		$factory_model = M('factory_order');
		
		$list = $order_model->relation(true)->select();
		foreach($list as $key => $val)
		{
			$order_info = $val['web_info'] ? $val['web_info'] : $val['plat_info'];
			
			if( $val['order_id'] )
			{
				$list[$key]['order'] = $val['web_info'];
				$customer_info = get_customer_info( $order_info['customer_id'] );
				$list[$key]['customer_name'] = $customer_info['first_name'].' '.$customer_info['last_name'];
				$factory_info = $factory_model->where( array('order_id'=>$val['order_id']) )->select();
			}
			elseif( $val['order_platform_id'] )
			{
				$list[$key]['order'] = $val['plat_info'];
				$list[$key]['customer_name'] = $order_info['name'];
				$factory_info = $factory_model->where( array('order_id'=>$val['order_id']) )->select();
			}
			$list[$key]['danhao'] = $order_info['order_number'];//单号
			$list[$key]['stock_number'] = "K".str_replace("-",".",substr($val['date'],5))."-".$val['number'];//库存号
			$list[$key]['factory_info'] = $factory_info;//工厂信息
			
			
		}
// 		dump($list);

		
		$this->list = $list;
		$this->display();
	}

}
