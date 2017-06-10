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
class ProductstockController extends CommonController
{
	public function index(){
		
		$this->assign("jumpUrl",U('/Admin/ProductManage/index'));
		$this->success('非法访问！');
		
		}
		
	
	//库存盘点
	public function product_stock_check(){
		$productstocksetDB=M('product_stock_set');     		 //套件库存对比预警表
		$productstockDB=M('product_stock');                  //单品库存对比预警表
		$idproductcodeDB=M('id_product_code');     			 //产品信息
		$productstockchangeDB=M('product_stock_check');     //库存盘点记录
		$username = session('username');    // 用户名
		if($_POST['id']){
			if($_POST['flag']=='set'){
				$date['edit_name']=$username;    // 用户ID
				$date['number']=$_POST['check_num'];
				$return=$productstocksetDB->where('`id`='.$_POST['id'])->save($date); 
				$stock_data=$productstocksetDB->where('id='.$_POST['id'])->find();
					$data['number']=$_POST['check_num']-$_POST['number'];
					$data['sku_id'] =$stock_data['sku_id'];
					$data['date_time']=time();
					$data['operator']=$username;    // 用户ID
					$data['message']=$_POST['remark'];
					$data['style']=$stock_data['style'];
					if($stock_data['style']=="2")
					{
						$return_stock_style="stock_local";
					}
					elseif($stock_data['style']=="1")
					{
						$return_stock_style="stock_fba";
					}
					elseif($stock_data['style']=="3")
					{
						$return_stock_style="stock_us";
					}
					if($return){
					$return01=$productstockchangeDB->add($data);
					}
					if($return && $return01){
						$this->assign("jumpUrl",U('/Admin/ProductManage/'.$return_stock_style.'/flag/set/warn/all',array('p'=>I('get.p'))));
						$this->success('库存盘点成功！');
						}else{
						$this->error('库存盘点失败!');
					   } 
				}else{
					//print_r($_POST);
					//die();
					$date['edit_name']=$username;    // 用户ID
					$date['number']=$_POST['check_num'];
					$return=$productstockDB->where('`id`='.$_POST['id'])->save($date);  
					$stock_data=$productstockDB->where('id='.$_POST['id'])->find();
					$data['number']=$_POST['check_num']-$_POST['number'];
						$data['number']=$_POST['check_num']-$_POST['number'];
						$data['code_id'] =$stock_data['code_id'];
						$data['date_time']=time();
						$data['operator']=$username;    // 用户ID
						$data['message']=$_POST['remark'];
						$data['style']=$stock_data['style'];
						if($stock_data['style']=="2")
						{
							$return_stock_style="stock_local";
						}
						elseif($stock_data['style']=="1")
						{
							$return_stock_style="stock_fba";
						}
						elseif($stock_data['style']=="3")
						{
							$return_stock_style="stock_us";
						}
						if($return)
						{
						$return01=$productstockchangeDB->add($data);
						}
						if($return && $return01){				
							$this->assign("jumpUrl",U('/Admin/ProductManage/'.$return_stock_style,array('p'=>I('get.p'))));
							$this->success('库存盘点成功！');
							}else{
							$this->error('库存盘点失败!');
						   }
					}
				
		}else{
			if(!$_GET['id']){
				$this->error('出现错误!');
				}				
				$flag=$_GET['flag'];
			if($flag=='set'){				
					$product_stock_set_handle=M('product_stock_set');
					$info = $product_stock_set_handle->where('`id` = '.$_GET['id'])->find();
					/*
					$id_product_code_handle=M('id_product_code');
					foreach ($info['code_list'] as $code_key => $list_code)
					{
						$code_name=$id_product_code_handle->field('name,code')->where('id='.$list_code['code_id'])->find();
						$info['code_list'][$code_key]['code_name']=$code_name['name'];
						$info['code_list'][$code_key]['code']=$code_name['code'];
					}
					
					$code_name=$id_product_code_handle->field('name')->where('id='.$list_code['code_id'])->find();
					*/
					//echo "<pre>";
					//print_r($info);
					//die();
					$info['sku_name']=panduan_sku_name($info['sku_id']);
					$this->assign('tpltitle','盘点套件');
				}else{
					$info = $productstockDB->where('`id` = '.$_GET['id'])->find();
					$idpr=$idproductcodeDB->where('`id`='.$info['code_id'])->find();
					$info['code_name']=$idpr['name'];
					$this->assign('tpltitle','盘点单品');
					}
			$info['sty']=panduan_style($info['style']);
			$this->assign('flag',$flag);
			$this->assign('info',$info);
			$this->display();
			
		}
	}
	
	//库存单品申请列表 （个人）
	public function product_stock_apply()
	{
		$username = session('username');// 用户名
		$productstockDB=M('product_stock');      //库存对比预警表
		$idproductcodeDB=M('id_product_code');    //产品信息
		$productstockcomeDB=M('product_stock_come');  //库存申请表
		$productstocksetcomeDB=M('product_stock_set_come');  //套件库存申请表
		$productstockcomecheckDB=M('product_stock_come_check');   //库存申请记录表
		
		$where = array('apply'=>$username,'status'=>array('in',array(0,1,2,3)));
		
		$count = $productstockcomeDB->where($where)->count();//分页
		$listrows = 15;
		$Page = new \Think\Page($count,$listrows);
		$nowPage = isset($_GET['p']) ? $_GET['p'] : 1;
        $show = $Page->show_pintuer();
        
		$info = $productstockcomeDB->where($where)->page("$nowPage,$listrows")->order('status desc,begin_time asc')->select();
// 		dump($productstockcomeDB->_sql());exit;
		foreach($info as $k=>$v)
		{
			$ckname = $idproductcodeDB->where('`id`='.$info[$k]['code_id'])->find();//根据id code_id 关联
			$info[$k]['ckname'] = $ckname['name'];
			$info[$k]['pro_change_count'] = $productstockcomecheckDB->where('`product_stock_come_id` ='.$info[$k]['id'])->count();
			$info[$k]['sta'] = panduan_status($info[$k]['status']);
			$info[$k]['sty'] = panduan_style($info[$k]['style']);
		}
		$this->assign('page',$show);// 赋值分页输出			
		$this->assign('list',$info);	
		$this->display();
	}
	
	//库存申请列表  （全部单件）
	public function product_stock_apply_all(){
		$username = session('username');    // 用户名
		$idproductcodeDB=M('id_product_code');    //产品信息
		$productstockcomeDB=M('product_stock_come');  //单品库存申请表
		$productstocksetcomeDB=M('product_stock_set_come');  //套件库存申请表
// 		if(!IS_POST && !I('get.style')) $_GET['style'] = 'dj';//单件
		
		//单品列表
// 		if(I('get.style')=='dj')
// 		{
			if(IS_POST)
			{
				if( I('post.status')!=-1 )
				{
					$where['status'] = I('post.status');
				}
				$old_status = I('post.status');
			}
			else
			{
				if( I('get.status')==null)
				{
					$old_status = -1;
				}
				else
				{
					$where['status'] = I('get.status');
					$old_status = I('get.status');
				}
			}
			$count = $productstockcomeDB->where($where)->count();//分页
			$listrows = 15;
			$Page = new \Think\Page($count,$listrows);
			if(IS_POST) $Page->parameter = $where;
			$nowPage = isset($_GET['p']) ? $_GET['p'] : 1;
			$show = $Page->show_pintuer();
			
			$info_dp=$productstockcomeDB->where($where)->page("$nowPage,$listrows")->order('status asc,id desc')->select();
			foreach($info_dp as $k=>$v)
			{
				$info_dp[$k]['ckname']=panduan_codeName($info_dp[$k]['code_id']);
				$info_dp[$k]['sty']=panduan_style($info_dp[$k]['style']);
				$info_dp[$k]['sta']=panduan_status($info_dp[$k]['status']);
			}
// 		}
		//套件列表
// 		if(I('get.style')=='tj')
// 		{
			if(IS_POST)
			{
				if( I('post.status')!=-1)
				{
					$where['status'] = I('post.status');
				}
				$old_status = I('post.status');
			}
			else
			{
				if( I('get.status')==null)
				{
					$old_status = -1;
				}
				else
				{
					$where['status'] = I('get.status');
					$old_status = I('get.status');
				}
			}
			$count = $productstocksetcomeDB->where($where)->count();//分页
			$listrows = 15;
			$Page = new \Think\Page($count,$listrows);
			if(IS_POST) $Page->parameter = $where;
			$nowPage = isset($_GET['p']) ? $_GET['p'] : 1;
			$show = $Page->show_pintuer();
			
			$info_tj=$productstocksetcomeDB->where($where)->page("$nowPage,$listrows")->order('status asc,id desc')->select();
			foreach($info_tj as $k=>$v){
				$info_tj[$k]['ckname']=panduan_sku_name($info_tj[$k]['sku_id']);
				$info_tj[$k]['sty']=panduan_style($info_tj[$k]['style']);
				$info_tj[$k]['sta']=panduan_status($info_tj[$k]['status']);
			}
// 		}
		$this->assign('style',I('get.style'));
		$this->assign('list_dp',$info_dp);	
		$this->assign('list_tj',$info_tj);
		$this->assign('page_bar',$show);
		$this->assign('status_list',stock_apply_status());
		$this->assign('old_status',$old_status);
		$this->assign('style',I('get.style'));
		$this->display();
	}
	
	//单品库存申请添加 修改      status   0 提交申请  8 取消申请
	public function product_stock_apply_add()
	{
		$username = session('username');    // 用户名
		$productstockDB=M('product_stock');      //库存对比表
		$idproductcodeDB=M('id_product_code');    //产品信息
		$productstockcomeDB=M('product_stock_come');  //库存申请表
		$productstockcomecheckDB=M('product_stock_come_check');   //库存修改记录表
		$id_catalogDB=M('id_catalog');
		$code_name=$idproductcodeDB->field('id,name')->where('`is_work`= 1')->order('id desc')->select();    //产品名称		
		//表单提交
		if($_POST['dosubmit']){
			if(!$_POST['number']){
				$this->error('数量必须填写!');
				}
			$date['code_id']=$_POST['code_name'];
			$date['number']=$_POST['number'];
				if($_POST['style']){
					$date['style']=$_POST['style'];
				}
			$date['status']=$_POST['status'];
			$date['begin_time']=strtotime($_POST['begin_time']);
			$date['end_time']=strtotime($_POST['end_time']);
			$date['apply'] = $username;
			$date['change_time']=time();
			$return=$productstockcomeDB->add($date);       
			if($return){
				$this->assign("jumpUrl",U('/Admin/productstock/product_stock_apply'));
				$this->success('库存申请成功！');
				}else{
				$this->error('库存申请失败!');
			}
		}else{
			//修改
			if($_GET['id']){
				$info=$productstockcomeDB->where('`id`='.$_GET['id'])->find();			
			    $code_row=$idproductcodeDB->where('`id`='.$info['code_id'])->find(); //根据id code_id 关联
				$info['code_name'] = $code_row['name'];
				$info['code_code'] = $code_row['code'];
				$this->assign('info',$info);
				$this->assign('tpltitle','修改单品');
			}else{
				//传code 进行添加
				if($_GET['code']){
					$info['code_id']=$_GET['code'];	
					$this->assign('info',$info);
				}
				$this->assign('tpltitle','添加单品');
			}
		$this->assign('catalog',$id_catalogDB->where('1=1')->select());
		$this->assign('style',STYLE());
		$this->assign('code_name',$code_name);
		$this->display();
		}
	}
	
	//查看库存申请  修改记录   （单品 套件）
	public  function product_stock_apply_record(){
		$productstockcomeDB=M('product_stock_come');  //库存申请表
		$productstockcomecheckDB=M('product_stock_come_check');  //库存修改记录
		$productstocksetcomeDB=M('product_stock_set_come');  //套件库存申请表
		$idproductskuDB=M('id_product_sku');                    //套件信息表
		$idproductcodeDB=M('id_product_code');    //产品信息
		$userDB = M('user');                    //用户表
			//套件
	  	    if($_GET['id'] && $_GET['data']){
				$info = $productstockcomecheckDB->where('`product_set_stock_come_id`='.$_GET['id'])->order('id')->select();
				$pro_set_come =$productstocksetcomeDB ->where('`id`='.$_GET['id'])->find();
					foreach($info as $k=>$v){							
						$info[$k]['sta']=panduan_status($info[$k]['status']);									//状态
						$info[$k]['code_name']=panduan_sku_name($info[$k]['sku_id']);
					 } 
			}else{
				//单品
				$info = $productstockcomecheckDB->where('`product_stock_come_id`='.$_GET['id'])->order('id')->select();
				$pro_come =$productstockcomeDB ->where('`id`='.$_GET['id'])->find();

					foreach($info as $k=>$v){
						if($info[$k]['inspector'] !=''){
						$info[$k]['sta']=panduan_status($info[$k]['status']);									//状态
						$info[$k]['code_name']=panduan_codeName($info[$k]['code_id']);
						}          
					}
			}
		$this->assign('info',$info);	
		$this->display();
	}
	
	//单品库存修改      status   // 0 提交审核   8 取消申请
	public function product_stock_edit(){
		$username = session('username');    // 用户名
		$productstockDB=M('product_stock');      //库存对比表
		$idproductcodeDB=M('id_product_code');    //产品信息
		$productstockcomeDB=M('product_stock_come');  //库存申请表
		$productstockcomecheckDB=M('product_stock_come_check');  //库存修改记录
		
		if($_POST['dosubmit']){
			//判断库存修改记录表中是否有原始数据
			$coun=$productstockcomecheckDB->where('`product_stock_come_id`='.$_POST['id'])->count();
			if($coun==0){
				$info=$productstockcomeDB->where('`id`='.$_POST['id'])->find();
				$aa['product_stock_come_id']=$_POST['id'];
				$aa['number']=$info['number'];
				$aa['code_id']=$info['code_id'];
				$aa['inspector']=$info['apply'];
				$aa['status']=$info['status'];
				$aa['date_time']=$info['change_time'];
				$productstockcomecheckDB->add($aa);                   //写初始记录 
				}
			if(!$_POST['number']){
				$this->error('数量必须填写!');
				}
			$date['code_id']=$_POST['code_name'];
			$date['number']=$_POST['number'];
				if($_POST['style']){
					$date['style']=$_POST['style'];
				}
			$date['begin_time']=strtotime($_POST['begin_time']);
			$date['end_time']=strtotime($_POST['end_time']);
			$date['status']=$_POST['status'];
			$date['change_time']=time();
			
			$return=$productstockcomeDB->where('`id`='.$_POST['id'])->save($date);
			$date['inspector'] = $username;
			$date['product_stock_come_id'] = $_POST['id'];
			$date['date_time']=time();
			$productstockcomecheckDB->add($date);                   //写记录 
			if($return){
				$this->assign("jumpUrl",U('/Admin/productstock/product_stock_apply'));
				$this->success('库存申请修改成功！');
				}else{
				$this->error('库存申请修改失败!');
			}
		}
	}
	//库存审核         （单品 套件）
	public function product_stock_audit(){
		$productstockcomeDB=M('product_stock_come');  //库存申请表
		$productstocksetcomeDB=M('product_stock_set_come');  //套件库存申请表
		$idproductcodeDB=M('id_product_code');    //产品信息
		$productstockcomecheckDB=M('product_stock_come_check');  //库存修改记录
		$username = session('username');    // 用户名
			if($_GET['id']){
				if($_GET['data']){
					//判断库存修改记录表中是否有原始数据
					$coun=$productstockcomecheckDB->where('`product_set_stock_come_id`='.$_GET['id'])->count();
				}else{
					$coun=$productstockcomecheckDB->where('`product_stock_come_id`='.$_GET['id'])->count();
					}
				if($coun==0){
					if($_GET['data']){
						$info=$productstocksetcomeDB->where('`id`='.$_GET['id'])->find();
						$aa['product_set_stock_come_id']=$_GET['id'];
						$aa['sku_id']=$info['sku_id'];
					}else{
						$info=$productstockcomeDB->where('`id`='.$_GET['id'])->find();
						$aa['product_stock_come_id']=$_GET['id'];
						$aa['code_id']=$info['code_id'];
					}
					$aa['inspector'] =$info['apply'];
					$aa['status']=$info['status'];
					$aa['date_time']=$info['change_time'];
					$pro_change=$productstockcomecheckDB->add($aa);
					if(!$pro_change){
						$this->error('出现错误！');	
						}
					}	
				if($_GET['id'] && $_GET['data'])         
				{ 
					//套件
						$status=$_GET['status'];
						$id=$_GET['id'];
						$date['status']=$status;
						$date['change_time']=time();
						$date['examination'] = $username;    //审核人
						$pro=$productstocksetcomeDB->where('`id`='.$id)->save($date);
						//写入记录
						$date['date_time']=time();
						$date['inspector'] = $username;
						$date['product_set_stock_come_id'] = $id;
						$productstockcomecheckDB->add($date);                   //  写记录 
						if($pro){
							$this->assign("jumpUrl",U('/Admin/productstock/product_stock_audit'));
							$this->success('套件审核成功！');
						}else{
							$this->error('套件审核失败!');	
						}
					}elseif($_GET['id']){
						//单品
						$status=$_GET['status'];
						$id=$_GET['id'];
						$date['status']=$status;
						$date['change_time']=time();
						$date['examination'] = $username;    //审核人
						$pro=$productstockcomeDB->where('`id`='.$id)->save($date);
						//写入记录
						$date['inspector'] = $username;
						$date['date_time']=time();
						$date['product_stock_come_id'] = $id;
						$productstockcomecheckDB->add($date);                   //productstockchange  写记录 
						if($pro){
							$this->assign("jumpUrl",U('/Admin/productstock/product_stock_audit'));
							$this->success('单品审核成功！');
						}else{
							$this->error('单品审核失败!');	
						}
					}
			}else{
				//单品
				$info_dp=$productstockcomeDB->where('`status`= 0')->select();
				$coun_dp=$productstockcomeDB->where('`status`= 0')->count();	                                          
				foreach($info_dp as $k=>$v){
					$ckname=$idproductcodeDB->where('`id`='.$info_dp[$k]['code_id'])->find(); //根据id code_id 关联
					$info_dp[$k]['ckname']=$ckname['name'];
					$info_dp[$k]['sty']=panduan_style($info_dp[$k][style]);
					}
			  	//套件
				$info_tj=$productstocksetcomeDB->where('`status`= 0')->select();
				$coun_tj=$productstocksetcomeDB->where('`status`= 0')->count();	 
				                                      
				foreach($info_tj as $k=>$v){
					$info_tj[$k]['ckname']=panduan_sku_name($info_tj[$k]['sku_id']);
					$info_tj[$k]['sty']=panduan_style($info_tj[$k][style]);
					}	
		$this->assign('coun_dp',$coun_dp);	
		$this->assign('coun_tj',$coun_tj);			
		$this->assign('info_dp',$info_dp);
		$this->assign('info_tj',$info_tj);
		$this->display();
		}
	}	
	//库存申请放弃 （单件  套装）  status  8
	public function product_stock_apply_out(){
		$productstockcomeDB=M('product_stock_come');  //库存申请表
		$productstockcomecheckDB=M('product_stock_come_check');  //库存修改记录
		$productstocksetcomeDB=M('product_stock_set_come');  //套件库存申请表
		$username = session('username');    // 用户名
		if($_GET['id'] && $_GET['data']){         //套装
			$id=$_GET['id'];
			$date['status'] = 8;
			$date['change_time']=time();
			$return=$productstocksetcomeDB->where('`id`='.$_GET['id'])->save($date);
			//写入记录
			$date['inspector'] = $username;
			$date['product_set_stock_come_id'] = $id;
			$date['date_time']=time();
			$productstockcomecheckDB->add($date);                   //productstockchange  写记录 
			if($return){
				$this->assign("jumpUrl",U('/Admin/productstock/product_stock_set_apply'));
				$this->success('套件库存申请放弃成功！');
				}else{
				$this->error('套件库存申请放弃失败!');
				}
		}else{					                //单品
			$id=$_GET['id'];	
			$date['status'] = 8;
			$date['change_time']=time();
			$return=$productstockcomeDB->where('`id`='.$_GET['id'])->save($date);
			//写入记录
			$date['inspector'] = $username;
			$date['date_time']=time();
			$date['product_stock_come_id'] = $id;
			$productstockcomecheckDB->add($date);                   //productstockchange  写记录 
			
			if($return){
				$this->assign("jumpUrl",U('/Admin/productstock/product_stock_apply'));
				$this->success('单品库存申请放弃成功！');
				}else{
				$this->error('单品库存申请放弃失败!');
			}
		}
	}
	//库存订单完成收货
	public function product_new_received(){
		$username = session('username');    // 用户名
		$productstockcomeDB=M('product_stock_come');  //库存申请表
		$productstocksetcomeDB=M('product_stock_set_come');  //套件库存申请表
		$productstockcomecheckDB=M('product_stock_come_check');  //库存修改记录
		$productstockDB=M('product_stock');              //单品库存数据表
		$productstocksetDB=M('product_stock_set');		//套件库存数据表
		if($_GET['id']){
			$id=$_GET['id'];	
			$date['status']=4;
			$date['change_time']=time();
			$date['accept']=$username;                      //收货人
			if($_GET['data']){
				$pro=$productstocksetcomeDB->where('`id`='.$id)->save($date);  //套件申请数据表状态修改
				}else{
				$pro=$productstockcomeDB->where('`id`='.$id)->save($date);  //单品申请数据表状态修改
				}
			//写入记录
			$date['inspector'] = $username;
			if($_GET['data']){
				$date['product_set_stock_come_id'] = $id;
				}else{
					$date['product_stock_come_id'] = $id;
				}
			$date['date_time']=time();
			$productstockcomecheckDB->add($date);                   //productstockcomecheck  写记录 
			if($_GET['data']){
				$pro_set_come=$productstocksetcomeDB->where('`id` = '.$id)->find();
				$pro_set_stock=$productstocksetDB->where('`style`='.$pro_set_come['style'].' and `sku_id`='.$pro_set_come['sku_id'])->find();
					if(!$pro_set_stock){
						
						$date['sku_id']=$pro_set_come['sku_id'];
						$date['number']=$pro_set_come['number'];
						$date['style']=$pro_set_come['style'];
						$date['minimun']=C('inventory_min');
						$date['maximum']=C('inventory_max');           //最大库存
						$pro=$productstocksetDB->add($date);
					}else{
						$data['number']=$pro_set_come['number']+$pro_set_stock['number'];
						$pro=$productstocksetDB->where('`sku_id`='.$pro_set_come['sku_id'])->save($data);
					}
					if($pro){
							$this->assign("jumpUrl",U('/Admin/productstock/product_stock_set_apply'));
							$this->success('完成收货操作成功！');
						}else{
							$this->error('完成收货操作失败!');	
						}
				}else{
					$pro_come=$productstockcomeDB->where('`id` = '.$id)->find();
					$pro_stock=$productstockDB->where('`style`='.$pro_come['style'].' and `code_id`='.$pro_come['code_id'])->find();
						if(!$pro_stock){
							$date['code_id']=$pro_come['code_id'];
							$date['number']=$pro_come['number'];
							$date['style']=$pro_come['style'];
							$date['minimum']=C('inventory_min');
							$date['maximum']=C('inventory_max');           //最大库存
							$pro=$productstockDB->add($date);
						}else{
							$data['number']=$pro_come['number']+$pro_stock['number'];
							$pro=$productstockDB->where('`code_id`='.$pro_come['code_id'])->save($data);
						}
						if($pro){
							$this->assign("jumpUrl",U('/Admin/productstock/product_stock_apply'));
							$this->success('完成收货操作成功！');
						}else{
							$this->error('完成收货操作失败!');	
						}
					}
						
					
		}
	}
	//套件库存列表
	public function product_stock_set_apply(){
		$username = session('username');    // 用户名
		$productstockDB=M('product_stock');      //库存对比预警表
		$idproductcodeDB=M('id_product_code');    //产品信息
		$productstocksetcomeDB=M('product_stock_set_come');  //套件库存申请表
		$productstockcomecheckDB=M('product_stock_come_check');   //库存申请记录表
		$id_product_skuDB=M('id_product_sku');                    //套件信息表  
		
        $where = array('apply'=>$username,'status'=>array('in',array(0,1,2,3)));
        
        $count = $productstocksetcomeDB->where($where)->count();//分页
        $listrows = 15;
        $Page = new \Think\Page($count,$listrows);
        $nowPage = isset($_GET['p']) ? $_GET['p'] : 1;
        $show = $Page->show_pintuer();
        
		$info = $productstocksetcomeDB->where($where)->page("$nowPage,$listrows")->order('status desc,begin_time asc')->select();
		$record_num_dp = $productstocksetcomeDB->where('`apply` = '."'".$username."'")->count();
		foreach($info as $k=>$v){
			$info[$k]['pro_change_count']= $productstockcomecheckDB->where('`product_set_stock_come_id` ='.$info[$k]['id'])->count();
			$info[$k]['sta']=panduan_status($info[$k]['status']);
			$info[$k]['sty']=panduan_style($info[$k]['style']);
			$info[$k]['sku_name']=panduan_skuName($info[$k]['sku_id']);
			$info[$k]['sku']=$this->panduan_sku($info[$k]['sku_id']);
		}
				
		$this->assign('page_bar',$show);// 赋值分页输出			
		$this->assign('list',$info);	
		$this->display();
	}	
	//套件库存申请      status   0 提交申请  8 取消申请
	public function product_stock_set_apply_add(){
		$username = session('username');    // 用户名
		$productstockDB=M('product_stock');      //库存对比表
		$productstocksetcomeDB=M('product_stock_set_come');  //套件库存申请表
		$productstockcomecheckDB=M('product_stock_come_check');   //库存修改记录表	
		$idproductskuDB=M('id_product_sku');                    //套件信息表
		
		if( I('post.action')=='ajax_get_set_sku' )//ajax请求，获取所有套件sku
		{
			$sku_list = $idproductskuDB->where(array('come_from_id'=>I('post.come_from'),'name'=>array('NEQ','')))->select();
			echo json_encode($sku_list);exit;
		}
		elseif($_POST['dosubmit'])//表单提交 
		{
			$date['sku_id'] = $_POST['sku'];
			$date['number'] = $_POST['number'];
			if($_POST['style']){
				$date['style']=$_POST['style'];
			}
			$date['status']=$_POST['status'];
			$date['begin_time']=strtotime($_POST['begin_time']);
			$date['end_time']=strtotime($_POST['end_time']);
			$date['apply'] = $username;
			$date['change_time']=time();
			$return=$productstocksetcomeDB->add($date);  
			if($return){
				$this->assign("jumpUrl",U('/Admin/productstock/product_stock_set_apply'));
				$this->success('库存申请成功！');
			}else{
				$this->error('库存申请失败!');
			}
		}
		else
		{
			if($_GET['id'])//修改
			{
				$info = $productstocksetcomeDB->where('`id`='.$_GET['id'])->find();
				$sku_info = $idproductskuDB->where(array('id'=>$info['sku_id']))->find();
// 				$info['sku_name']=panduan_skuName($info['sku_id']);
// 			    $info['sku']=$this->panduan_sku($info['sku_id']);
			    $this->action = 'edit';
				$this->assign('info',$info);
				$this->sku_info = $sku_info;
				$this->assign('tpltitle','修改套件');
			}
			else//传sku_id 进行添加
			{
				if($_GET['sku_id'])
				{
					$val=$idproductskuDB->where('`id` ='.$_GET['sku_id'])->find();
					$info['sku']=$val['sku'];
					$this->assign('info',$info);
					$this->assign('tpltitle','添加套件');
				}
				$this->assign('tpltitle','添加套件');
			}
			$this->come_from_list = come_from_new();
			$this->assign('style',STYLE());	
			$this->display();
		}
	}
	
	//套装库存申请修改      status   // 0 提交审核   8 取消申请
	public function product_stock_set_edit(){
		$username = session('username');    // 用户名
		$productstockDB = M('product_stock');      //库存对比表
		$productstocksetcomeDB = M('product_stock_set_come');  //套装库存申请表
		$productstockcomecheckDB = M('product_stock_come_check');  //库存修改记录
		$idproductskuDB = M('id_product_sku');                    //套件信息表
		if($_POST['dosubmit']){
			
				//判断库存修改记录表中是否有原始数据
				$coun=$productstockcomecheckDB->where('`product_set_stock_come_id`='.$_POST['id'])->count();
				if($coun==0){
					$info=$productstocksetcomeDB->where('`id`='.$_POST['id'])->find();
					$aa['product_set_stock_come_id']=$_POST['id'];
					$aa['number']=$info['number'];
					$aa['sku_id']=$info['sku_id'];
					$aa['inspector']=$info['apply'];
					$aa['status']=$info['status'];
					$aa['date_time']=$info['change_time'];
					$aa['inspector']=$info['apply'];
					$productstockcomecheckDB->add($aa);                   //写初始记录 
				}
				$date['sku_id'] = I('post.sku');
				$date['number'] = $_POST['number'];
				if($_POST['style']){
					$date['style']=$_POST['style'];
				}
				$date['begin_time']=strtotime($_POST['begin_time']);
				$date['end_time']=strtotime($_POST['end_time']);
				$date['status']=$_POST['status'];
				$date['change_time']=time();
				$return=$productstocksetcomeDB->where('`id`='.$_POST['id'])->save($date);
				$date['inspector'] = $username;
				$date['date_time']=time();
				$date['product_set_stock_come_id']=$_POST['id'];
				$productstockcomecheckDB->add($date);                   //写记录 
				if($return){
					$this->assign("jumpUrl",U('/Admin/productstock/product_stock_set_apply'));
					$this->success('库存申请修改成功！');
				}else{
					$this->error('库存申请修改失败!');
				}
		}
	}
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
}
