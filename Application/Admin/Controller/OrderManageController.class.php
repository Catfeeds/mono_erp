<?php
namespace Admin\Controller;
use Admin\Controller\CommonController;

class OrderManageController extends CommonController
{
	public function __construct()
	{
		\Think\Hook::add('correct_url_encode','Admin\\Behavior\\CorrectUrlEncodeBehavior');
		parent::__construct();
	}
	
	//有关FBA库存的一些脚本
	public $fba_sku_relate_come_from_id = '';
	public function fba_sku_relate()
	{
		vendor('PHPExcel.SimplePHPExcel');
		$callback = function($row,$obj)
		{
			$fba_sku = $row['B'];//FBA SKU
			$web_sku = $row['C'];//网站SKU
			$come_from_id = $obj->fba_sku_relate_come_from_id;
			$come_from = get_come_from_name($come_from_id);
			
			$fba_sku_relate_model = M('product_stock_fba_sku_relate');
			$sku_model = M('id_product_sku');
			$relate_model = M('id_relate_sku');
			
			$sku_row = $sku_model->where(array('sku'=>$web_sku,'come_from_id'=>$come_from_id))->find();
			if($sku_row)
			{
				$sku_id = $sku_row['id'];
				$relate_row = $relate_model->where(array('product_sku_id'=>$sku_id))->find();
				if($relate_row)
				{
					$code_id = $relate_row['product_code_id'];
				}
			}
			$code_id || $code_id=0;
			
			$fba_row = $fba_sku_relate_model->where(array('sku'=>$fba_sku,'come_from'=>$come_from))->find();
			if($fba_row)
			{
				$fba_sku_relate_model->where( array('sku'=>$fba_sku,'come_from'=>$come_from) )->save( array('code_id'=>$code_id) );
			}
			else
			{
				$fba_sku_relate_model->add( array('sku'=>$fba_sku,'code_id'=>$code_id,'come_from'=>$come_from) );
			}
			
		};
		
		$this->fba_sku_relate_come_from_id = 14;
		$option=array(
			'uploadfile'=> './Public/excel/jp.xls', //必须。该文件名不能含中文
			'obj' 		=> $this,
			"start_row"	=> 2,//可选。设置有效内容的 起始行数，默认2
			"sheet_index"=> 0,
			"unlink"	=> false,//可选。是否删除文件，默认false
			"callback"	=> $callback,//用于插入多表的情况
		);
		importExcel($option);
	}
	public function fba_sku_relate_set_limit_number()
	{
		vendor('PHPExcel.SimplePHPExcel');
		$callback = function($row,$obj)
		{
			$fba_sku = $row['B'];//FBA SKU
			$min = $row['J'];//网站SKU
// 			$max = $row['K'];//网站SKU
			$max = 500;//for jp
			$come_from_id = $obj->fba_sku_relate_come_from_id;
			$come_from = get_come_from_name($come_from_id);
	
			$fba_sku_relate_model = M('product_stock_fba_sku_relate');
			$stock_model = M('product_stock');
			
			$relate_row = $fba_sku_relate_model->where(array('sku'=>$fba_sku,'come_from'=>$come_from))->find();
			if( $relate_row && $relate_row['code_id'] )
			{
				$where = array(
					'code_id'=>$relate_row['code_id'],
					'style'=>'1',
					'place'=>$come_from,
				);
				$stock_model->where($where)->save( array('minimum'=>$min,'maximum'=>$max) );
			}
		};
	
		$this->fba_sku_relate_come_from_id = 14;//us 4 d.xls; ca 6 e.xls; uk 8 f.xls; jp 14 jp.xls
		$option=array(
			'uploadfile'=> './Public/excel/jp.xls', //必须。该文件名不能含中文
			'obj' 		=> $this,
			"start_row"	=> 2,//可选。设置有效内容的 起始行数，默认2
			"sheet_index"=> 0,
			"unlink"	=> false,//可选。是否删除文件，默认false
			"callback"	=> $callback,//用于插入多表的情况
		);
		importExcel($option);
	}
	
    public function index()
    {
		$this->display();
    }
    
    public function test()
    {
    	$enhanced_where = array(
    		'order_web' => array(
    			'come_from_id' => 4,
    		),
			'order_web_address' => array(
    			'order_web' => 3174319098,
    		),
    	);

    	$start = microtime();
    	$hello = D('order_web')->enhanced_where('audit',$enhanced_where);
    	$end = microtime();
		dump(($end-$start));    	
//     	dump($list);
    	
    	
    	exit;
    }
    
    public function import()
    {
    	$this->flatform_list = array('web_order_general','platfrom_order_general','amazon','lotte','ebay');
    	$this->display();
    }
    public $collection_array=array();
	public function import_handle()
    {
		if(IS_POST){
			$rootPath = './Public/order_excle/';
			if(!file_exists($rootPath)) mkdir($rootPath);
			$upload = new \Think\Upload();// 实例化上传类
			$upload->maxSize   =     0 ;// 设置附件上传大小
			$upload->exts      =     array('jpg', 'gif', 'png', 'jpeg','xls');// 设置附件上传类型
			$upload->rootPath  =     $rootPath; // 设置附件上传根目录
			$upload->savePath  =     ''; // 设置附件上传（子）目录
			$upload->autoSub   =     false;//设置文件子目录名
			// 上传文件
			$info = $upload->upload();
			if(!$info) {// 上传错误提示错误信息
				$this->error($upload->getError());
			}
			else
			{// 上传成功
				vendor('PHPExcel.SimplePHPExcel');
				$func_check_country = function($value)
				{
					if( strlen($value)!=2 )
					{
						$msg='格式不符';
						$state = 'error';
					}
					return array('data'=>$value,'msg'=>$msg,'state'=>$state);
				};
				if(I('post.flatform')=='amazon')
				{
					$callback = function ($row,$obj)
					{
						$order=array();
						$shipping=array();
						$product=array();
						$data=array();
						$come_form_id=M("id_come_from")->where(array("name"=>$row[AG]))->getField("id");
						$order=array(
								'order_number'		=> $row[A],	//important
								'email'				=> $row[D],
								'name'				=> $row[E],
								'telephone'			=> $row[F],
								'currency'			=> $row[J],
								'price'				=> $row[K],
								'message'			=> '',
								'is_gift_package'	=> '',
								'come_from_id'		=> $come_form_id,	//important
								'operator'			=> $_SESSION["username"],			//?
								'date_time'			=> $row[B],		//要求格式：Y-m-d H:i:s
								'ship_service_level'=> '',
						);
						$shipping=array(
								'name'					=> $row[P],
								'country'				=> $row[W],
								'state'					=> $row[U],
								'city'					=> $row[T],
								'address1'				=> $row[Q],
								'address2'				=> $row[R],
								'address3'				=> $row[S],
								'post'					=> $row[V],
								'telephone'				=> $row[X],
								'shipping_style'		=> '',
								'shipping_number'		=> '',
								'shipping_weight'		=> '',
								'shipping_price'		=> '',
								'shipping_tax'			=> '',
								'shipping_date'			=> '',
								'shipping_operator'		=> '',
								'shipping_hs'			=> '',
								'shipping_sample'		=> '',
								'shipping_report_price'	=> '',
						);
						
						//判断是否是新订单
						$is_new_order = true;
						$old_order_key = '';
						foreach ($obj->collection_array as $key=>$val)
						{
							if($val["order"]["order_number"]==$row[A])
							{
								$is_new_order = false;
								$old_order_key = $key;
								break;
							}
						}
						
						if($is_new_order)//新订单
						{
							$product[] = array(
								'name'		=> $row[H],
								'sku'		=> $row[G],			//important
								'price'		=> $row[K],
								'number'	=> $row[I],
							);
							$data["order"]=$order;
							$data["shipping"]=$shipping;
							$data["product"]=$product;
							$data["status"]='Unshipped';
							$obj->collection_array[]=$data;
						}
						else //重复订单（多条商品）
						{
							$product_one=array(
								'name'		=> $row[H],
								'sku'		=> $row[G],			//important
								'price'		=> $row[K],
								'number'	=> $row[I],
							);
							$obj->collection_array[$old_order_key]['product'][] = $product_one;
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
					$amazon_platform_order = make_platform_order($this->collection_array);
					if($amazon_platform_order)
		    		{
		    			$this->success('amazon平台订单导入成功！',U('/Admin/OrderManage/import'));
		    		}
		    		else
		    		{
		    			$this->error("amazon平台订单导入失败！");
		    		}
				}
				//ebay订单导入
				if(I('post.flatform')=='ebay')
				{
					$callback = function ($row,$obj)
					{
						$order=array();
						$shipping=array();
						$product=array();
						$data=array();
						$come_form_id=M("id_come_from")->where(array("name"=>$row[AG]))->getField("id");
						$order=array(
								'order_number'		=> $row[A],	//important
								'email'				=> $row[E],
								'name'				=> $row[C],
								'telephone'			=> $row[D],
								'currency'			=> '',
								'price'				=> $row[U],
								'message'			=> $row[AC],
								'is_gift_package'	=> '',
								'come_from_id'		=> $come_form_id,	//important
								'operator'			=> $_SESSION["username"],			//?
								'date_time'			=> $row[Y],		//要求格式：Y-m-d H:i:s
								'ship_service_level'=> '',
						);
						$shipping=array(
								'name'					=> $row[C],
								'country'				=> $row[AQ],
								'state'					=> $row[AO],
								'city'					=> $row[AN],
								'address1'				=> $row[AL],
								'address2'				=> $row[AM],
								'address3'				=> '',
								'post'					=> $row[AP],
								'telephone'				=> $row[D],
								'shipping_style'		=> '',
								'shipping_number'		=> '',
								'shipping_weight'		=> '',
								'shipping_price'		=> '',
								'shipping_tax'			=> '',
								'shipping_date'			=> '',
								'shipping_operator'		=> '',
								'shipping_hs'			=> '',
								'shipping_sample'		=> '',
								'shipping_report_price'	=> '',
						);
				
						//判断是否是新订单
						$is_new_order = true;
						$old_order_key = '';
						foreach ($obj->collection_array as $key=>$val)
						{
							if($val["order"]["order_number"]==$row[A])
							{
								$is_new_order = false;
								$old_order_key = $key;
								break;
							}
						}
				
						if($is_new_order)//新订单
						{
							$product[] = array(
									'name'		=> $row[M],
									'sku'		=> $row[N],			//important
									'price'		=> $row[U],
									'number'	=> $row[O],
							);
							$data["order"]=$order;
							$data["shipping"]=$shipping;
							$data["product"]=$product;
							$data["status"]='Unshipped';
							$obj->collection_array[]=$data;
						}
						else //重复订单（多条商品）
						{
							$product_one=array(
									'name'		=> $row[M],
									'sku'		=> $row[N],			//important
									'price'		=> $row[U],
									'number'	=> $row[O],
							);
							$obj->collection_array[$old_order_key]['product'][] = $product_one;
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
					$ebay_platform_order = make_platform_order($this->collection_array);
					if($ebay_platform_order)
					{
						$this->success('ebay平台订单导入成功！',U('/Admin/OrderManage/import'));
					}
					else
					{
						$this->error("ebay平台订单导入失败！");
					}
				}
				//平台通用订单导入
				if(I('post.flatform')=='platfrom_order_general')
				{
					$callback = function ($row,$obj)
					{
						$order=array();
						$shipping=array();
						$product=array();
						$data=array();
						$come_form_id=M("id_come_from")->where(array("name"=>$row[S]))->getField("id");
						$code_id = D("id_product_code")->where(array("code"=>$row[L]))->getField("id");
						$order=array(
								'order_number'		=> $row[A],	//important
								'email'				=> $row[D],
								'name'				=> $row[B],
								'telephone'			=> $row[C],
								'currency'			=> $row[M],
								'price'				=> $row[P],
								'message'			=> $row[R],
								'is_gift_package'	=> '',
								'come_from_id'		=> $come_form_id,	//important
								'operator'			=> $_SESSION["username"],			//?
								'date_time'			=> substr($row[Q],5),//要求格式：Y-m-d H:i:s
								'ship_service_level'=> '',
						);
						$shipping=array(
								'name'					=> $row[B],
								'country'				=> $row[J],
								'state'					=> $row[H],
								'city'					=> $row[G],
								'address1'				=> $row[E],
								'address2'				=> $row[F],
								'address3'				=> '',
								'post'					=> $row[I],
								'telephone'				=> $row[C],
								'shipping_style'		=> '',
								'shipping_number'		=> '',
								'shipping_weight'		=> '',
								'shipping_price'		=> '',
								'shipping_tax'			=> '',
								'shipping_date'			=> '',
								'shipping_operator'		=> '',
								'shipping_hs'			=> '',
								'shipping_sample'		=> '',
								'shipping_report_price'	=> '',
						);
				
						//判断是否是新订单
						$is_new_order = true;
						$old_order_key = '';
						foreach ($obj->collection_array as $key=>$val)
						{
							if($val["order"]["order_number"]==$row[A])
							{
								$is_new_order = false;
								$old_order_key = $key;
								break;
							}
						}
				
						if($is_new_order)//新订单
						{
							$product[] = array(
									'name'		=> $row[K],
									'code_id'		=> $code_id,			//important
									'price'		=> $row[N],
									'number'	=> $row[O],
							);
							$data["order"]=$order;
							$data["shipping"]=$shipping;
							$data["product"]=$product;
							$data["status"]='Unshipped';
							$obj->collection_array[]=$data;
						}
						else //重复订单（多条商品）
						{
							$product_one=array(
									'name'		=> $row[K],
									'code_id'		=> $code_id,			//important
									'price'		=> $row[N],
									'number'	=> $row[O],
							);
							$obj->collection_array[$old_order_key]['product'][] = $product_one;
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
					$general_platform_order = make_platform_order($this->collection_array);
					if($general_platform_order)
					{
						$this->success('通用平台订单导入成功！',U('/Admin/OrderManage/import'));
					}
					else
					{
						$this->error("通用平台订单导入失败！");
					}
				}
				
	    		if(I('post.flatform')=='web_order_general')
	    		{
	    			$callback = function ($row,$obj)
	    			{
	    				$order=array();
	    				$address=array();
	    				$product=array();
	    				$data=array();
	    				$come_form_id=M("id_come_from")->where(array("name"=>$row[S]))->getField("id");
	    				$code_id = D("id_product_code")->where(array("code"=>$row[L]))->getField("id");
	    				if($row[Q]!=='')
	    				{
	    					$date_time = substr($row[Q],5);
	    				}
	    				else 
	    				{
	    					$date_time = date('Y-m-d H:i:s',time());
	    				}
	    				$name = explode(' ',$row[B]); 
	    				$order=array(
	    						'order_number'		=> $row[A],	//important
	    						'email'				=> $row[D],
	    						'first_name'		=> $name[0],
	    						'last_name'			=> $name[1],
	    						'customer_id'		=> 0,
	    						'message'			=> $row[R],
	    						'couponcode'		=> '',
	    						'total_price'		=> $row[P],
	    						'total_price_discount'	=> $row[P],
	    						'device'			=> '',
	    						'payment_style'		=> '',
	    						'come_from_id'		=> $come_form_id,	//important
	    						'cookie_from'			=> '',			//?
	    						'least_from'			=> '',
	    						'date_time'=> $date_time,//要求格式：Y-m-d H:i:s
	    				);
	    				$address=array(
	    						'first_name'			=> $name[0],
	    						'last_name'				=> $name[1],
	    						'country'				=> $row[J],
	    						'province'				=> $row[H],
	    						'city'					=> $row[G],
	    						'address'				=> $row[E],
	    						'code'					=> $row[I],
	    						'telephone'				=> $row[C],
	    				);
	    		
	    				//判断是否是新订单
	    				$is_new_order = true;
	    				$old_order_key = '';
	    				foreach ($obj->collection_array as $key=>$val)
	    				{
	    					if($val["order"]["order_number"]==$row[A])
	    					{
	    						$is_new_order = false;
	    						$old_order_key = $key;
	    						break;
	    					}
	    				}
	    		
	    				if($is_new_order)//新订单
	    				{
	    					$product[] = array(
	    							'code_id'	=> $code_id,			//important
	    							'set_sku'	=> '',			//important
	    							'order_product_id'		=> 0,
	    							'price'	=> $row[N],
	    							'discount_price'	=> $row[N],
	    							'number'	=> $row[O],
	    							'status'	=> '正常',
	    					);
	    					$data["order"]=$order;
	    					$data["address"]=$address;
	    					$data["product"]=$product;
	    					$data["status"]["status"]='audit';
	    					$data["status"]["operator"]=$_SESSION["username"];
	    					$obj->collection_array[]=$data;
	    				}
	    				else //重复订单（多条商品）
	    				{
	    					$product_one=array(
	    							'code_id'	=> $code_id,			//important
	    							'set_sku'	=> '',			//important
	    							'order_product_id'		=> 0,
	    							'price'	=> $row[N],
	    							'discount_price'	=> $row[N],
	    							'number'	=> $row[O],
	    							'status'	=> '正常',
	    					);
	    					$obj->collection_array[$old_order_key]['product'][] = $product_one;
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
	    			$website_order = get_website_order($this->collection_array);
	    			if($website_order)
	    			{
	    				$this->success('通用网站订单导入成功！',U('/Admin/OrderManage/import'));
	    			}
	    			else
	    			{
	    				$this->error("通用网站订单导入失败！");
	    			}
	    		}
	    		
	    		/*
	    		echo "<pre>";
	    		print_r($this->collection_array);
	    		*/
	    		
	    		
	    		
	    			
			}
		}
		else
		{
			$this->display();
		}	
    }
    
    //网站订单
    public function order_web()
    {
    	$order_model = D('order_web');
    	$status_model = D('order_web_status');
    	$param = array();//用于构造url, 注意：根据情况要消除不需要的部分，例如 操作标志参数
    	$where = array();//sql where，用于filter, 注意：不包含order_status
    	$filter = array();//记录筛选条件
    	$sort = '';//sql order sort
    	$limit = '';//sql limit page
    	$listRows = 15;//每页
    	$order_status = I('get.order_status');//由于 all状态和audit状态 的特殊性，order_status需要联合where特殊处理
    	$title = '';//页面标题
    	$subtitle = array();//副标题
    	$anchor = '';//锚点，其值为order_id;要实现1.展开栏2.向下箭头3.锚点
    	$relation = array('order_web_address','order_web_product','product_original_info','order_web_status','message_info','come_from_info','product_customization_info','factory_order','supplement_info');
    	
    	if(I('get.anchor'))
    	{
			$this->anchor = I('get.anchor');
    		unset($_GET['anchor']);//构造的url虽然对了，但是当前url依然带有 anchor参数和anchor锚点，使用后退功能时有影响
    		$this->correct_url = U('order_plat',$_GET);//解决上一行问题
    	}
    	
    	//公共请求方法，包括筛选，清除筛选
    	function common_request(&$where,&$filter,&$param,&$obj)
    	{
    		//选择国家
    		if( I('post.action')=='come_from_id' )
    		{
    			$where['come_from_id'] = I('post.come_from_id');
    			$_SESSION['order_web_come_from_id'] = I('post.come_from_id');//用于切换订单状态时保留come_from_id参数
    			//去除url中可能带有的旧filter参数
    			clear_get_params();
    			$param = array_merge( I('get.'), $where);
    			$filter = $where;
    		}
    		//筛选
    		elseif( I('post.action')=='filter' )
    		{
    			$where['come_from_id'] = I('get.come_from_id');//come_from_id在url中，永不丢失
	    		if( I('post.order_number') ) $where['order_number']=trim( I('post.order_number') );//订单号
	    		if( I('post.email') ) $where['email']=trim( I('post.email') );
	    		if( I('post.payment_style')!=-1 ) $where['payment_style']=I('post.payment_style');
				$filter = $where;
	    		//date_time是真正的搜索条件，start_time和end_time是筛选选项
	    		$start_time = date('Y-m-d H:i:s', strtotime(I('post.start_time')) );
	    		$end_time = date('Y-m-d H:i:s', strtotime(I('post.end_time'))+24*3600 );
	    		if( I('post.start_time') && !I('post.end_time') )
	    		{
	    			$filter['start_time'] = I('post.start_time');
	    			$where['date_time']=array('egt',$start_time );//时间
	    		}
	    		if( I('post.end_time') && !I('post.start_time') )
	    		{
	    			$filter['end_time'] = I('post.end_time');
	    			$where['date_time']=array('elt',$end_time );
	    		}
	    		if( I('post.end_time') && I('post.start_time') )
	    		{
	    			$filter['start_time'] = I('post.start_time');
	    			$filter['end_time'] = I('post.end_time');
	    			$where['date_time'] = array('between',array($start_time,$end_time));
	    		}
	    		//检索客户姓名，分析$_POST['customer_name']，查询first_name,last_name字段
	    		if( I('post.customer_name') )
	    		{
	    			$customer_name =  urldecode( trim( I('post.customer_name') ) );
	    			$filter['customer_name'] =  $customer_name;
	    			$temp = explode(' ', $customer_name);
	    			if( count($temp)==1 )
	    			{
	    				$where[] = " ( first_name LIKE '%{$temp[0]}%' OR last_name LIKE '%{$temp[0]}%' ) ";
	    			}
	    			elseif(  count($temp)>=2 )
	    			{
	    				$temp_first_name = $temp[0];
	    				$temp_last_name = end($temp);
	    				$where[] = " ( first_name LIKE '%{$temp_first_name}%' AND last_name LIKE '%{$temp_last_name}%' ) ";
	    			}
	    		}
	    		
	    		clear_get_params();
	    		$param = array_merge( I('get.'),$filter );
// 	    		dump($where);exit;
    		}
    		//清除筛选
    		elseif( I('post.action')=='clear' )
    		{
    			$where = array();
    			$where['come_from_id'] = I('get.come_from_id');
				$filter = $where;
	    		clear_get_params();
	    		$param = I('get.');
    		}
    		//导出订单地址
    		elseif (I('post.action')=='export_address') 
    		{
    			if( I('post.check') )
    			{
    				$order_id_list = I('post.check');
    			}
    			else //如果没有选择，导出当前筛选的所有订单
    			{
    				normal($where,$filter,$param,$obj);//构造$where
    				$order_list = D('order_web')->status( I('get.order_status'),$where )->select();
    				foreach($order_list as $key=>$val)
    				{
    					$order_id_list[] = $val['id'];
    				}
    			}
    			
    			$order_array=$order_id_list;
    			$order_address_array=array();
    			for($i=0;$i<count($order_array);$i++){
    					$order_address=M("order_web_address")->where(array("order_web_id"=>$order_array[$i]))->find();
    					$order_number=M("order_web")->where(array("id"=>$order_array[$i]))->getField("order_number");
 						$order_address_array_one=array("id"=>$order_address[id],"order_number"=>$order_number,"first_name"=>$order_address[first_name],"last_name"=>$order_address[last_name],"country"=>$order_address[country],"province"=>$order_address[province],"city"=>$order_address[city],"address"=>$order_address[address],"code"=>$order_address[code],"telephone"=>$order_address[telephone]);
 						$order_address_array[]=$order_address_array_one;	
    			}
    			$excle_title=array("ID","订单号","first_name","last_name","国家","州/省","城市","地址","邮编","电话");
    			exportExcel($order_address_array,null,$excle_title);
    		}
    		//同步网站订单
    		elseif( I('post.action')=='sync' )
    		{
    			$come_from_code = get_come_from_name(I('get.come_from_id'));				
    			$return_data = $obj->get_web_order( array($come_from_code) );
// 				dump($return_data);exit;
    			$order_sync_result = '';
    			if($return_data['status']) $order_sync_result.="<span style='color:red;'>{$return_data['status']}</span><br/>";
    			if($return_data['message']) $order_sync_result.="信　　息：　<span style='color:red;'>{$return_data['message']}</span><br/>";
//     			if($return_data['update_time']) $order_sync_result.="更新至：　<span style='color:red;'>{$return_data['update_time']}</span><br/>";
    			$order_sync_result.="本次更新：　<span style='color:red;'>{$return_data['all_new_order_number']}条</span><br/>";
    			$obj->order_sync_result = $order_sync_result;
    			 
    			clear_get_params();
    			normal($where,$filter,$param,$obj);
    		}
    		//追加审核
    		elseif( I('get.exchange')==1 )
    		{
    			$order_web_id = I('get.order_id');
    			$order_web = M("order_web");
    			$order_web_come_from = $order_web->field("come_from_id")->where("id=$order_web_id")->select();//这里也要改
    			$come_from_id = $order_web_come_from[0][come_from_id];
    			order_to_factory($order_web_id,0,$come_from_id,1);
    			
    			unset($_GET['order_id']);
    			unset($_GET['exchange']);
    			normal($where,$filter,$param,$obj);
    		}
    		elseif( I('post.action')=='export_order_for_finance' )
    		{
    			normal($where,$filter,$param,$obj);
    			$obj->export_order_for_finance('web',$where);
    		}
    		//普通请求
    		else
    		{
    			normal($where,$filter,$param,$obj);
    		}
    	}
    	//普通请求
    	function normal(&$where,&$filter,&$param,&$obj)
    	{
    		if( !I('get.come_from_id') )
    		{
    			if( I('session.order_web_come_from_id') )
    			{
    				$_GET['come_from_id'] = I('session.order_web_come_from_id');
    			}
    			else 
    			{
    				$_GET['come_from_id'] = 4; //写死，网站默认美国
    			}
    		}
    		
    		$where['come_from_id'] = I('get.come_from_id');
    		if( I('get.order_number') ) $where['order_number'] = trim( I('get.order_number') );//订单号
    		if( I('get.email') ) $where['email']= trim( I('get.email') );
    		if( I('get.payment_style') ) $where['payment_style']= urldecode( I('get.payment_style') );
    		$filter = $where;
    		$start_time = date('Y-m-d H:i:s', strtotime(I('get.start_time')) );
    		$end_time = date('Y-m-d H:i:s', strtotime(I('get.end_time'))+24*3600 );
    		if( I('get.start_time') && !I('get.end_time') )
    		{
    			$filter['start_time'] = I('get.start_time');
    			$where['date_time'] = array('egt',$start_time);//时间
    		}
    		if( I('get.end_time') && !I('get.start_time') )
    		{
    			$filter['end_time'] = I('get.end_time');
    			$where['date_time'] = array('elt',$end_time);
    		}
    		if( I('get.end_time') && I('get.start_time') )
    		{
    			$filter['start_time'] = I('get.start_time');
    			$filter['end_time'] = I('get.end_time');
    			$where['date_time'] = array('between',array($start_time,$end_time));
    		}
    		//检索客户姓名，分析$_POST['customer_name']，查询first_name,last_name字段
    		if( I('get.customer_name') )
    		{
    			$customer_name =  urldecode( trim( I('get.customer_name') ) );
    			$filter['customer_name'] =  $customer_name;
    			$temp = explode(' ', $customer_name);
    			if( count($temp)==1 )
    			{
    				$where[] = " ( first_name LIKE '%{$temp[0]}%' OR last_name LIKE '%{$temp[0]}%' ) ";
    			}
    			elseif(  count($temp)>=2 )
    			{
    				$temp_first_name = $temp[0];
    				$temp_last_name = end($temp);
    				$where[] = " ( first_name LIKE '%{$temp_first_name}%' AND last_name LIKE '%{$temp_last_name}%' ) ";
    			}
    		}
    		
    		$param = I('get.');
    	}
    	//清除$_GET参数
    	function clear_get_params()
    	{
    		unset($_GET['order_number']);//去除url中可能带有的旧filter参数
    		unset($_GET['email']);
    		unset($_GET['start_time']);
    		unset($_GET['end_time']);
    		unset($_GET['payment_style']);
    		unset($_GET['p']);//筛选之后 去除 分页参数
    		unset($_GET['sort_style']);//筛选之后 去除 排序参数
    		unset($_GET['sort_field']);
    		unset($_GET['customer_name']);
    	}
    	
    	if( I('get.order_status')=='all' )//所有订单
    	{
    		$this->title = '网站所有订单';
    		$this->colspan = 10;
    		$relation[]="other_price_info";
    		$relation[] = 'fba_order';
    		$relation[] = 'product_stock_order';
    		if(false)
    		{
				//...
    		}
    		else //公共的几种请求方法
    		{
    			common_request($where,$filter,$param,$this);
    		}
    	}
    	elseif( I('get.order_status')=='audit' )
    	{
    		$this->title = '网站待审核订单';
    		$this->colspan = 9;
    		$listRows = 100;
    		if( !I('get.sort_field') )//排序。默认按 折后价格 降序
    		{
    			$_GET['sort_field'] = 'total_price_discount';
    			$_GET['sort_style'] = 'desc';
    		}
    		$order_audit_limit = get_system_parameter('order_audit_limit_lv1');//overtime 超时未审核 时限
    		$deadline = date('Y-m-d H:i:s',time()-$order_audit_limit*3600);//次日期之前的订单都审核超时
			if(I('get.order_id')||(I('post.action')=='batch_audit'))//处理审核和批量审核
			{
    			if( I('post.action')=='batch_audit' )//批量审核
    			{
					I('post.check') && $order_id_list=I('post.check');
					for($i=0;$i<sizeof($order_id_list);$i++)
					{
						$order_web_id=$order_id_list[$i];
						$order_web=M("order_web");
						$order_web_come_from=$order_web->field("come_from_id")->where("id=$order_web_id")->select();//这里也要改
						$come_from_id=$order_web_come_from[0][come_from_id];
						$order_web_status=M("order_web_status");
						$order_web_status_check=$order_web_status->where("order_web_id=$order_web_id")->select();
						if(!$order_web_status_check||$order_web_status_check[0][status]=="audit")
						{
							order_to_factory($order_web_id,0,$come_from_id);
						}
					}
					//where 和 param
					normal($where,$filter,$param,$this);
    			}
    			elseif( I('get.order_id') )//单个审核
    			{
					//$data['order_id'] = I('get.order_id');
					//$status_model = D('order_plat_form_status');
    				//$status_model->add($data);
    				$order_web_id=I('get.order_id');
					$order_web=M("order_web");
					$order_web_come_from=$order_web->field("come_from_id")->where("id=$order_web_id")->select();//这里也要改
					$come_from_id=$order_web_come_from[0][come_from_id];
					$order_web_status=M("order_web_status");
					$order_web_status_check=$order_web_status->where("order_web_id=$order_web_id")->select();
					if(!$order_web_status_check||$order_web_status_check[0][status]=="audit")
					{
						order_to_factory($order_web_id,0,$come_from_id);//tagtag
					}
    				//where 和 param
    				unset($_GET['order_id']);//order_id参数是 审核操作的 标志参数，不希望带入分页链接和其它链接
    				normal( $where,$filter,$param,$this);
    			}
			}
			else 
			{
				common_request($where,$filter,$param,$this);
				//for overtime normal
				if( !IS_POST && I('get.overtime') )
				{
					$where['date_time'] = array('lt',$deadline);
					$param['overtime'] = I('get.overtime');
					$filter['overtime'] = I('get.overtime');
				}
			}
			if( I('post.action')=='filter' && I('post.overtime') )//overtime超时 筛选项不是 多页面的公共功能，在此额外操作
			{
				$where['date_time'] = array('lt',$deadline);
				$param['overtime'] = I('post.overtime');
				$filter['overtime'] = I('post.overtime');
				//如果在此前存在针对date_time的筛选，则将其将覆盖
				if($param['start_time']) unset($param['start_time']);
				if($param['end_time']) unset($param['end_time']);
				if($filter['start_time']) unset($filter['start_time']);
				if($filter['end_time']) unset($filter['end_time']);
			}
			//计算超时未审核订单数量
			$overtime_num = $order_model->status('audit',$where)->where(" date_time < '$deadline' ")->count();
			//计算sku缺少关联订单数量
			$temp_where = array();
			foreach ($where as $key=>$val)
			{
				if( !is_numeric($key) )
				{
					$temp_where["a.$key"] = $val;
				}
			}
			$sub_query = "SELECT order_web_id FROM order_web_status WHERE status!='audit' ";//订单状态条件
			$temp_where['a.id'] = array('EXP',"NOT IN ($sub_query)");
			$temp_list = $order_model->alias('a')->distinct(true)->field('a.id')->join('LEFT JOIN order_web_product AS b ON a.id=b.order_web_id')->where($temp_where)->where(array('b.code_id'=>array('eq',0)))->select();
			$lacksku_num = sizeof($temp_list);
    	}
    	elseif( I('get.order_status')=='accept' )//tagtag
    	{
    		$relation[] = 'fba_order';
    		$relation[] = 'product_stock_order';
    		$this->title = '网站待收货订单';
    		$this->colspan = 10;
    		$listRows = 100;
    		
			if( I('get.exchange')==1 )
			{
				$order_web_id=I('get.order_id');
				$order_web=M("order_web");
				$order_web_come_from=$order_web->field("come_from_id")->where("id=$order_web_id")->select();//这里也要改
				$come_from_id=$order_web_come_from[0][come_from_id];				
				order_to_factory($order_web_id,0,$come_from_id,1);
				
				unset($_GET['order_id']);
				unset($_GET['exchange']);
				normal($where,$filter,$param,$this);
			}
			else 
			{
				common_request($where,$filter,$param,$this);
			}
			
    	}
    	elseif( I('get.order_status')=='shipping' )
    	{
    		$this->title = '网站待发货订单';
    		$this->colspan = 11;
    		$listRows = 100;
    		$relation[] = "other_price_info";
    		$relation[] = 'fba_order';
    		$relation[] = 'product_stock_order';
    		
    		if( I('post.action')=='batch_history' )
    		{
    			$data = array();
    			$data['operator'] = get_user_name( I('session.userid') );
    			$data['date_time'] = date('Y-m-d H:i:s');
    			$data['status'] = 'history';//shipping->history
    			I('post.check') && $order_id_list=I('post.check');
    			for($i=0;$i<sizeof($order_id_list);$i++)
    			{
    				$status_where['order_web_id'] = $order_id_list[$i];
    				$status_model->where($status_where)->save($data);
    			}
    			//where 和 param
    			normal($where,$param);
    		}
			else
			{
				common_request($where,$filter,$param,$this);
			}
    	}
    	elseif( I('get.order_status')=='history' )
    	{
    		$this->title = '网站历史订单';
    		$this->colspan = 10;
    		$relation[]="other_price_info";
    		$relation[] = 'fba_order';
    		$relation[] = 'product_stock_order';
    		
			if( false )//处理审核和批量审核
			{
				//...
			}
			else 
			{
				common_request($where,$filter,$param,$this);
			}
    		
    	}
    	else
    	{
    		//error
    	}
    	
    	//不同状态的订单 列表组成有些许不同
    	$this->order_status = I('get.order_status');//标志变量，
    	//filter 筛选栏中保留筛选条件
    	$this->filter = $filter;
    	$this->come_from_list = M('id_come_from')->where(array('style'=>'web'))->select();
    	$this->payment_style_list = $order_model->distinct(true)->field('payment_style')->where( array('payment_style'=>array('neq','')) )->select();
//     	$this->payment_style_list = array('check out','paypal express checkout','paypal checkout','	mail remittance','bank transfer','cash on delivery');//静态化
    	//sort 可排序按钮 显示当前 排序字段 和 排序方式;
    	if( !I('get.sort_field') )//默认按时间降序排序
    	{
    		$this->sort_field = 'date_time';
    		$this->sort_style = 'desc';
    		$sort = ' date_time desc, total_price_discount desc ';
    	}
    	else
    	{
    		$this->sort_field = I('get.sort_field');
    		$this->sort_style = I('get.sort_style');
    		$sort = I('get.sort_field').' '.I('get.sort_style');
    	}
    	//page 分页
    	$count = $order_model->status( I('get.order_status'),$where )->count();
    	$listRows || $listRows=15;//每页显示条数
    	$Page = new \Think\Page($count,$listRows);
//     	dump($param);exit;
    	if(IS_POST) $Page->parameter = $param;//build url
    	$this->listRows = $listRows;
    	$this->page_bar = $Page->show_pintuer();
    	$limit = $Page->firstRow.','.$Page->listRows;
    	//param 构造url
    	$this->param_json = htmlspecialchars(json_encode($param));//用于js,目前用于sort
    	$this->param = $param;
    	//数据最近同步时间
    	$temp = $order_model->where( array('come_from_id'=>$where['come_from_id']) )->order('date_time desc')->field('date_time')->find();
    	if($temp)
    		$this->update_time = $temp['date_time'];
    	else
    		$this->update_time = 'null';
    	//其它
    	$this->order_status_list = order_status();//订单状态数组
    	$user_model = D('user');//用户列表
    	$this->user_list = $user_model->getAllUser('`name` !="" and `username` !="admin"');
    	$subtitle['total'] = $count;
    	$subtitle['overtime'] = $overtime_num;
    	$subtitle['lacksku'] = $lacksku_num;
    	$this->subtitle = $subtitle;
    	
    	//订单列表
    	$order_list = $order_model->status( I('get.order_status'),$where )->order($sort)->limit($limit)->relation($relation)->select();
//     	echo $order_model->_sql();exit;
    	//处理不同order_status的页面
    	$order_list = complete_web_order($order_list, $where['come_from_id']);
		//判断是否有样布	
		foreach($order_list as $list_k=>$list_v)
		{
			if($list_v['email'])
			{
				$sample_record = M('sample_record')->where('`email` ="'.$list_v['email'].'" and `is_send`=0')->select();	
				if($sample_record)
				{
					$order_list[$list_k]['is_sample_record']  = '有样布';
				}
				else
				{
					$order_list[$list_k]['is_sample_record']  = '';
				}
			}
			
		}
    	$this->order_list = $order_list;
// 		dump($order_list);exit;
    	$this->display('order_web');
    }

    //平台订单列表
    public function order_plat()
    {
    	$order_model = D('order_plat_form');
    	$status_model = D('order_plat_form_status');
    	
    	$param = array();//用于构造url, 注意：根据情况要消除不需要的部分，例如 操作标志参数
    	$where = array();//sql where  注意：不包含order_status
    	$filter = array();//在筛选栏 保存 筛选信息
    	$sort = '';//sql order sort
    	$limit = '';//sql limit page
		$listRows = 15;//分页
    	$order_status = I('get.order_status');//由于 all状态和audit状态 的特殊性，order_status需要联合where特殊处理
		$title = '';//页面标题
		$anchor = '';//锚点，其值为order_id;要实现1.展开栏2.向下箭头3.锚点
		$relation = array('product_info','shipping_info','status_info','message_info','come_from_info','factory_order');//关联模型
		
		//先处理锚点，因为param中不能带有
		if(I('get.anchor'))
		{
			$this->anchor = I('get.anchor');
			unset($_GET['anchor']);//构造的url虽然对了，但是当前url依然带有 anchor参数和anchor锚点，使用后退功能时有影响
			$this->correct_url = U('order_plat',$_GET);
		}
    		
    	//公共请求方法，包括选择国家、筛选、清除筛选等
    	function common_request(&$where,&$filter,&$param,&$obj)
    	{
    		//选择国家
    		if( I('post.action')=='come_from_id' )
    		{
	    		$where['come_from_id'] = I('post.come_from_id');
	    		$_SESSION['order_plat_come_from_id'] = I('post.come_from_id');//用于切换订单状态时保留come_from_id参数
	    		//去除url中可能带有的旧filter参数
	    		unset($_GET['come_from_id']);
	    		unset($_GET['order_number']);
	    		unset($_GET['currency']);
	    		unset($_GET['p']);//筛选之后 去除 分页参数
	    		unset($_GET['sort_style']);//筛选之后 去除 排序参数
	    		unset($_GET['sort_field']);
	    		$param = array_merge( I('get.'), $where);
	    		$filter = $where;
    		}
    		//筛选
    		elseif( I('post.action')=='filter' )
    		{
//     			dump(I('get.'));dump(I('post.'));exit;
    			$where['come_from_id'] = I('get.come_from_id');//一定有come_from_id参数
    			if( I('post.order_number') ) $where['order_number']= trim(I('post.order_number'));
	    		if( I('post.currency') && I('post.currency')!=-1 ) $where['currency']= I('post.currency');
// 	    		if( I('post.come_from_id') && I('post.come_from_id')!=-1 ) $where['come_from_id']=I('post.come_from_id');
	    		//去除url中可能带有的旧filter参数
	    		unset($_GET['order_number']);
	    		unset($_GET['currency']);
// 	    		unset($_GET['come_from_id']);
	    		unset($_GET['p']);//筛选之后 去除 分页参数
	    		unset($_GET['sort_style']);//筛选之后 去除 排序参数
	    		unset($_GET['sort_field']);
	    		$param = array_merge( I('get.'),$where );
	    		$filter = $where;
    		}
    		//清除筛选
    		else if( I('post.action')=='clear' )
    		{
    			$where = array();
    			$where['come_from_id'] = I('get.come_from_id');
	    		$filter = $where;
	    		unset($_GET['order_number']);
	    		unset($_GET['currency']);
// 	    		unset($_GET['come_from_id']);
	    		unset($_GET['p']);//筛选之后 去除 分页参数
	    		unset($_GET['sort_style']);//筛选之后 去除 排序参数
	    		unset($_GET['sort_field']);
	    		$param = I('get.');
    		}
    		elseif (I('post.action')=='export_address')
    		{
    			if( I('post.check') )
    			{
    				$order_id_list = I('post.check');
    			}
    			else //如果没有选择，导出当前筛选的所有订单
    			{
    				normal($where,$filter,$param,$obj);//构造$where
    				$order_list = D('order_plat_form')->status( I('get.order_status'),$where )->select();
    				foreach($order_list as $key=>$val)
    				{
    					$order_id_list[] = $val['id'];
    				}
    			}
    			
    			$order_array = $order_id_list;
    			$order_address_array=array();
    			for($i=0;$i<count($order_array);$i++){
    				$order_address=M("order_plat_form_shipping")->where(array("order_platform_id"=>$order_array[$i]))->find();
 					$order_address_array_one=array("id"=>$order_address[id],"name"=>$order_address[name],"country"=>$order_address[country],"state"=>$order_address[state],"city"=>$order_address[city],"address1"=>$order_address[address1],"address2"=>$order_address[address2],"address3"=>$order_address[address3],"post"=>$order_address[post],"telephone"=>$order_address[telephone]);
    				$order_address_array[]=$order_address_array_one;
    			}
    			$excle_title=array("ID","姓名","国家","州/省","城市","地址1","地址2","地址3","邮编","电话");
    			exportExcel($order_address_array,null,$excle_title);
    			normal($where,$filter,$param,$this);
    		}
    		//同步平台订单数据
    		elseif( I('post.action')=='sync' )
    		{
    			$return_data = array();
				//Amazon.com Amazon.ca Amazon.com.mx IBA
    			if( in_array( I('get.come_from_id'), array(1,2,3,23,24,25,26,27,28) ) )
    			{
    				$return_data = $obj->amazon_api( I('get.come_from_id') );
    			}
    			//乐天
    			elseif(I('get.come_from_id')==20)
    			{
    				$return_data = $obj->rakuten_api();
    			}
    			//Ebay.us和Ebay.uk
    			elseif(I('get.come_from_id')==18 || I('get.come_from_id')==19)
    			{
    				$return_data = $obj->ebay_api( I('get.come_from_id') );
    			}
				//速卖通
    			elseif(I('get.come_from_id')==17)
    			{
    				$return_data = $obj->smt_api(17);
    			}
				//速卖通
    			elseif(I('get.come_from_id')==103)
    			{
    				$return_data = $obj->smt_api(103);
    			}
    			else 
    			{
    				$return_data = array('msg'=>'该平台尚未添加订单同步功能！');
    			}
    			
    			$order_sync_result = '';
    			if($return_data['msg']) $order_sync_result.="<span style='color:red;'>{$return_data['msg']}</span><br/>";
    			if($return_data['update_time']) $order_sync_result.="更新至：　<span style='color:red;'>{$return_data['update_time']}</span><br/>";
    			$return_data['success_num'] || $return_data['success_num']=0;
				$order_sync_result.="成　功：　<span style='color:red;'>{$return_data['success_num']}条</span><br/>";
				$return_data['fail_num'] || $return_data['success_num']=0;
				$order_sync_result.="失　败：　<span style='color:red;'>{$return_data['fail_num']}条</span><br/>";
    			$obj->order_sync_result = $order_sync_result;
    			
    			unset($_GET['order_number']);//去除filter参数
    			unset($_GET['currency']);
    			unset($_GET['p']);//去除 page参数
    			unset($_GET['sort_style']);//去除 sort参数
    			unset($_GET['sort_field']);
    			normal($where,$filter,$param,$this);
    		}
    		elseif( I('get.exchange')==1 )
    		{
    			$order_platfrom_id = I('get.order_id');
    			$order_plat_form = M("order_plat_form");
    			$order_plat_come_from = $order_plat_form->field("come_from_id")->where("id=$order_platfrom_id")->select();
    			$come_from_id = $order_plat_come_from[0][come_from_id];
    			order_to_factory(0, $order_platfrom_id, $come_from_id, 1);
    		}
    		elseif( I('post.action')=='export_order_for_finance' )
    		{
    			normal($where,$filter,$param,$obj);
    			$obj->export_order_for_finance('plat',$where);
    		}
    		//普通请求
    		else
    		{
	    		normal($where,$filter,$param,$obj);
    		}
    	}
    	//普通请求
    	function normal(&$where,&$filter,&$param,&$obj)
    	{
    		if( !I('get.come_from_id') )
    		{
    			if( I('session.order_plat_come_from_id') )
    			{
    				$_GET['come_from_id'] = I('session.order_plat_come_from_id');
    			}
    			else
    			{
    				$_GET['come_from_id'] = 1; //写死，默认Amazon.com
    			}
    		}
    		$where['come_from_id'] = I('get.come_from_id');
    		if( I('get.order_number') ) $where['order_number']=I('get.order_number');
    		if( I('get.currency') ) $where['currency']=I('get.currency');
    		
    		$param = I('get.');
	    	$filter = $where;
    	}
    	
    	//处理 各页面 各种 操作  在这里分情况处理where和param
    	if( I('get.order_status')=='all' )//所有订单
    	{
    		$this->title = '平台所有订单';
    		$this->colspan = 9;
    		$relation[] = 'fba_order';
    		$relation[] = 'product_stock_order';
    		if(false)
    		{
				//...
    		}
    		else
    		{
    			common_request($where,$filter,$param,$this);
    		}
    	}
    	elseif( I('get.order_status')=='audit' )
    	{
    		$this->title = '平台待审核订单';
    		$this->colspan = 8;
    		$listRows = 100;
    		$order_audit_limit = get_system_parameter('order_audit_limit_lv1');//超时 时限
    		$deadline = date('Y-m-d H:i:s',time()-$order_audit_limit*3600);
    		// 单个审核 和 批量审核
    		if( I('get.order_id')||(I('post.action')=='batch_audit') )
    		{
    			if( I('post.action')=='batch_audit' )//批量审核
    			{
					I('post.check') && $order_id_list=I('post.check');
					for($i=0;$i<sizeof($order_id_list);$i++)
					{
    					$order_platfrom_id=$order_id_list[$i];
						$order_plat_form=M("order_plat_form");
						$order_plat_come_from=$order_plat_form->field("come_from_id")->where("id=$order_platfrom_id")->select();//这里也要改
						$come_from_id=$order_plat_come_from[0][come_from_id];	
						$order_plat_form_status=M("order_plat_form_status");
						$order_plat_form_status_check=$order_plat_form_status->where("order_platform_id=$order_platfrom_id")->select();
						if(!$order_plat_form_status_check||$order_plat_form_status_check[0][status]=="audit")
						{
							order_to_factory(0,$order_platfrom_id,$come_from_id);
						}
					}
					//where 和 param
					normal($where,$filter,$param,$this);
    			}
    			elseif( I('get.order_id') )//单个审核
    			{
					$data['order_platform_id'] = I('get.order_id');
					//$status_model = D('order_plat_form_status');
    				//$status_model->add($data);
    				$order_platfrom_id=I('get.order_id');
					$order_plat_form=M("order_plat_form");
					$order_plat_come_from=$order_plat_form->field("come_from_id")->where("id=$order_platfrom_id")->select();//这里也要改
					$come_from_id=$order_plat_come_from[0][come_from_id];
					$order_plat_form_status=M("order_plat_form_status");
					$order_plat_form_status_check=$order_plat_form_status->where("order_platform_id=$order_platfrom_id")->select();
					if(!$order_plat_form_status_check||$order_plat_form_status_check[0][status]=="audit")
					{
						order_to_factory(0,$order_platfrom_id,$come_from_id);
					}
    				//where 和 param
    				unset($_GET['order_id']);//order_id参数是 审核操作的 标志参数，不希望带入分页链接和其它链接
    				normal($where,$filter,$param,$this);
    			}
    		}
    		else
    		{
    			common_request($where,$filter,$param,$this);
    			//for overtime normal
    			if( !IS_POST && I('get.overtime') )
    			{
    				$where['date_time'] = array('lt',$deadline);
    				$param['overtime'] = I('get.overtime');
    				$filter['overtime'] = I('get.overtime');
    			}
    		}
    		if( I('post.action')=='filter' && I('post.overtime') )//overtime超时 筛选项不是 多页面的公共功能，在此额外操作
    		{
    			$where['date_time'] = array('lt',$deadline);
    			$param['overtime'] = I('post.overtime');
    			$filter['overtime'] = I('post.overtime');
    			//如果在此前存在针对date_time的筛选，则将其将覆盖
    			if($param['start_time']) unset($param['start_time']);
    			if($param['end_time']) unset($param['end_time']);
    			if($filter['start_time']) unset($filter['start_time']);
    			if($filter['end_time']) unset($filter['end_time']);
    		}
    		
    		//计算超时未审核订单数量
    		$overtime_where = array('date_time'=>array('lt',$deadline));//如果在此前存在针对date_time的筛选，则将其将覆盖
    		$overtime_num = $order_model->status('audit',$where)->where($overtime_where)->count();
    		//计算sku缺少关联订单数量 囧囧囧囧囧囧囧囧
    		$temp_where = array();
    		foreach ($where as $key=>$val)
    		{
    			$temp_where["a.$key"] = $val;
    		}
    		$sub_query = "SELECT order_platform_id FROM order_plat_form_status WHERE status!='audit' ";//订单状态条件
    		$temp_where['a.id'] = array('EXP',"NOT IN ($sub_query)");
    		$temp_list = $order_model->alias('a')->distinct(true)->field('a.id')->join('LEFT JOIN order_plat_form_product AS b ON a.id=b.order_platform_id')->where($temp_where)->where(array('b.code_id'=>array('eq',0)))->select();
			//SELECT DISTINCT a.id FROM order_plat_form a LEFT JOIN order_plat_form_product AS b ON a.id=b.order_platform_id  WHERE a.id NOT IN (SELECT order_platform_id FROM order_plat_form_status WHERE status!='audit' ) AND b.code_id = 0
			$lacksku_num = sizeof($temp_list);
    	}
    	elseif( I('get.order_status')=='accept' )
    	{
			$this->title = '平台待收货订单';
    		$this->colspan = 9;
    		$listRows = 100;
    		$relation[] = 'fba_order';
    		$relation[] = 'product_stock_order';
    		if(false)
    		{
    			
    		}
    		else
    		{
    			common_request($where,$filter,$param,$this);
    		}
    	}
    	elseif( I('get.order_status')=='shipping' )
    	{
    		$this->title = '平台待发货订单';
    		$this->colspan = 10;
    		$listRows = 100;
    		$relation[] = 'other_price_info';
    		$relation[] = 'fba_order';
    		$relation[] = 'product_stock_order';
    		if( I('post.action')=='batch_history' )
    		{
    			$data = array();
    			$data['operator'] = get_user_name( I('session.userid') );
    			$data['date_time'] = date('Y-m-d H:i:s');
    			$data['status'] = 'history';//shipping->history
    			I('post.check') && $order_id_list=I('post.check');
    			for($i=0;$i<sizeof($order_id_list);$i++)
    			{
    				$status_where['order_platform_id'] = $order_id_list[$i];
    				$status_model->where($status_where)->save($data);
    			}
    			//where 和 param
    			normal($where,$filter,$param,$this);
    		}
    		else
    		{
    			common_request($where,$filter,$param,$this);
    		}
    	}
    	elseif( I('get.order_status')=='history' )
    	{
    		$this->title = '平台历史订单';
    		$this->colspan = 9;
    		$relation[] = 'other_price_info';
    		$relation[] = 'fba_order';
    		$relation[] = 'product_stock_order';
    		if(false)
    		{
    			
    		}
    		else
    		{
    			common_request($where,$filter,$param,$this);
    		}
    	}
    	else
    	{
    		//未知状态，do something
    	}
    	
    	//处理come_from_id的不同情况
    	if( in_array($where['come_from_id'], array(18,19)) )//ebay
    	{
    		$this->colspan = $this->colspan + 1;
    	}
    	elseif( false )
    	{
    		
    	}
    	
    	//不同状态的订单 列表组成有些许不同
    	$this->order_status = I('get.order_status');//标志变量，
    	//filter 筛选栏中保留筛选条件
    	$this->filter = $filter;
    	$this->currency_list = $order_model->distinct(true)->field('currency')->select();
    	$this->come_from_list = M('id_come_from')->where( array('style'=>'plat') )->select();
    	//sort 可排序按钮 显示当前 排序字段 和 排序方式;
    	if( !I('get.sort_field') )//默认按时间降序排序
    	{
    		$this->sort_field = 'date_time';
    		$this->sort_style = 'desc';
    		$sort = 'date_time desc';
    	}
    	else 
    	{
    		$this->sort_field = I('get.sort_field');
    		$this->sort_style = I('get.sort_style');
    		$sort = I('get.sort_field').' '.I('get.sort_style');
    	}
    	//page 分页
    	$count = $order_model->status( I('get.order_status'),$where )->count();
    	$listRows || $listRows = 15;//每页显示条数
    	$Page = new \Think\Page($count,$listRows);
    	if(IS_POST) $Page->parameter = $param;//build url
    	$this->listRows = $listRows;
    	$this->page_bar = $Page->show_pintuer();
    	$limit = $Page->firstRow.','.$Page->listRows;
    	//param 构造url
    	$this->param_json = htmlspecialchars(json_encode($param));//用于js,目前用于sort
    	$this->param = $param;
    	//数据最近同步时间
    	$update_time_model = M('order_plat_form_update_time');
    	$temp = $update_time_model->where( array('come_from_id'=>$where['come_from_id']) )->order('time desc')->find();
    	if($temp)
    		$this->update_time_current = date('Y-m-d H:i:s',$temp['time']);
    	else 
    		$this->update_time_current = 'null';
    	$temp = $update_time_model->where( array('come_from_id'=>$where['come_from_id']) )->order('time desc')->limit("1,1")->select();
    	if($temp)
    		$this->update_time_previous = date('Y-m-d H:i:s',$temp[0]['time']);
    	else
    		$this->update_time_previous = 'null';
    	//其它
    	$this->order_status_list = order_status();//订单状态数组
    	$user_model = D('user');
    	$this->user_list = $user_model->getAllUser('`name` !="" and `username` !="admin"');
    	$subtitle['total'] = $count;//计数
    	$subtitle['overtime'] = $overtime_num;
    	$subtitle['lacksku'] = $lacksku_num;
    	$this->subtitle = $subtitle;
    	//订单列表
    	$order_list = $order_model->status( I('get.order_status'),$where )->order($sort)->limit($limit)->relation($relation)->select();
    	$order_list = complete_plat_order($order_list,$where['come_from_id']);
	//	dump($order_list);exit;
		//判断是否有样布	
		foreach($order_list as $list_k=>$list_v)
		{
			if($list_v['email'])
			{
				$sample_record = M('sample_record')->where('`email` ="'.$list_v['email'].'" and `is_send`=0')->select();	
				if($sample_record)
				{
					$order_list[$list_k]['is_sample_record']  = '有样布';
				}
				else
				{
					$order_list[$list_k]['is_sample_record']  = '';
				}
			}
			
		}
		
		
		
    	$this->order_list = $order_list;
//     	print_r($order_list);
//     	exit();
//     	dump($order_list);exit;
    	
    	$this->display('order_plat');
    }
    
    //[通用] 添加留言    style plat:平台   web：网站
 	public function ajax_remark_add()
	{
		$message_model = M('order_business_message');
		if( I('post.style')=='web' )
		{
			$data['order_id'] = I('post.id');
			$where['order_id'] = I('post.id');
		}
		elseif( I('post.style')=='plat' )
		{
			$data['order_platform_id'] = I('post.id');
			$where['order_platform_id'] = I('post.id');
		}
		$data['message'] = stripslashes( html_entity_decode( I('post.message') ) );

		$data['accept'] =I('post.accept');
		$data['date_time'] = time();
		$data['operator'] = get_user_name( session('userid') );
		$data['status'] = 0;//新留言，未处理
		$data['warning'] =I('post.warning');
		$message_model->add($data);//添加
		$message_list = $message_model->where($where)->order('warning asc, date_time desc')->select();
		if(sizeof($message_list)>=2)
		{
			$see_more = "<span class='icon-eye text-red'></span>";
			$html_many = "";
			for($i=1;$i<sizeof($message_list);$i++)
			{
				$user_name = username_name($message_list[$i]['operator']);//转化为name中文名
				$date_time = date('Y-m-d H:i:s',$message_list[$i]['date_time']);
				$html_many .= "
					<p style='max-width:300px;'>
					<strong>$user_name</strong> -
					<small>$date_time</small> : <br/>
					{$message_list[$i]['message']}	
					</p>
				";
			}
		}
		$user_name = username_name($message_list[0]['operator']);//转化为name中文名
		$date_time = date('Y-m-d H:i:s',$message_list[0]['date_time']);
		$html_one = "
			<strong>$user_name</strong> -
			<small>$date_time</small> : <br/>
			{$message_list[0]['message']}
			$see_more";
		echo  json_encode(array($html_one,$html_many));
	}
	
	//[通用] 修改订单
	public function ajax_status_update()
	{
		$user_model = D('user');
		if( I('post.style')=='web' )
		{
			$status_model = M('order_web_status');
			$status_history_model = M('order_web_status_history');
			$where = array('order_web_id'=>I('post.order_id'));
		}
		elseif( I('post.style')=='plat' )
		{
			$status_model = M('order_plat_form_status');
			$status_history_model = M('order_plat_form_status_history');
			$where = array('order_platform_id'=>I('post.order_id'));
		}
		$status_row = $status_model->where($where)->find();
		
		if( I('post.action')=='fetch' )
		{
			$this->status_list = order_status();
			if( $status_row )
			{
				$old_status = $status_row['status'];
			}
			else 
			{
				$old_status = 'audit';
			}
			$this->old_status = $old_status;
			$this->user_list = $user_model->getAllUser();
			$this->order_id = I('post.order_id');
			$this->style = I('post.style');
			echo $this->fetch('order_status_update');
		}
		elseif( I('post.action')=='update' )
		{
			$data['message'] = stripslashes( html_entity_decode( I('post.message') ) );
			$data['status'] = I('post.status');
			$data['operator'] = get_user_name( session('userid') );
			$data['date_time'] = time();
			
			//新加 或 修改 status表
			if($status_row)
			{
				$result = $status_model->where($where)->save($data);
			}
			else 
			{
				$data2 = array_merge($data, $where);
				$result = $status_model->add($data2);
			}
			//新加 status_history表
			$data3 = array_merge($data, $where);
			$status_history_model->add($data3);
			echo $result;
		}
	}
	
	//[网站] 订单 修改产品sku
	public function ajax_edit_sku()
	{
		if( I('post.action')=='fetch' )
		{
			$this->product_original_id = I('post.product_original_id');
			$this->product_original_set_id = I('post.product_original_set_id');
			$this->order_product_id = I('post.order_product_id');
			$this->order_id = I('post.order_id');
			$this->sku = I('post.sku');
			
			echo $this->fetch('ajax_edit_sku');
		}
		elseif( I('post.action')=='submit' )
		{
// 			I('post.old_sku');
// 			I('post.new_sku');
// 			I('post.product_original_id');
// 			I('post.product_original_set_id');
// 			I('post.order_product_id');
// 			I('post.order_id');
// 			exit(json_encode(I('post.')));
			
			$product_original_model = M('order_web_product_original');
			$product_original_set_model = M('order_web_product_original_set');
			$product_model = M('order_web_product');
			$sku_model = M('id_product_sku');
			$code_model = M('id_product_code');
			$relate_model = M('id_relate_sku');
			$order_model = M('order_web');
			
			//come_from_id
			$order_row = $order_model->find( I('post.order_id') ); 
			
			//修改原数据sku
			if( I('post.product_original_id') )
			{
				$product_original_model->where( array('id'=>I('post.product_original_id')) )->save( array('sku'=>I('post.new_sku')) );
			}
			elseif( I('post.product_original_set_id') )
			{
				$product_original_set_model->where( array('id'=>I('post.product_original_set_id')) )->save( array('sku'=>I('post.new_sku')) );
			}
			
			//旧code
			$old_sku_where = array(
				'sku'			=> I('post.old_sku'),
				'come_from_id'	=> $order_row['come_from_id'],);
			$old_sku_row = $sku_model->where($old_sku_where)->find();
			$old_relate_where = array('product_sku_id'=>$old_sku_row['id']);
			$old_relate_row = $relate_model->where($old_relate_where)->find();
			if($old_relate_row) $old_code_id=$old_relate_row['product_code_id'];
			else $old_code_id=0;
			$old_code_id = $old_relate_row ? $old_relate_row['product_code_id'] : 0;
			
			//新code
			$product_where = array(
				'order_web_id'		=> I('post.order_id'),
				'order_product_id'	=> I('post.order_product_id'),
				'code_id'			=> $old_code_id, );
			$new_sku_where = array(
				'sku'			=> I('post.new_sku'),
				'come_from_id'	=> $order_row['come_from_id'],);
			$new_sku_row = $sku_model->where($new_sku_where)->find();
			if(!$new_sku_row) //new_sku不存在
			{
				$sku_model->add($new_sku_where);//添加id_product_sku
				$temp = $product_model->where($product_where)->field('id')->find();//只取一条
				$temp || exit('unknown product');//如果temp不存在，说明当前数据错误，product表与original表不对应
				$result = $product_model->where( array('id'=>$temp['id']) )->save( array('code_id'=>0) );//order_web_product
				$result ? exit('new') : exit('error1');
			}
			else //new_sku已存在	
			{
				$new_relate_where = array('product_sku_id'=>$new_sku_row['id']);
				$new_relate_list = $relate_model->where($new_relate_where)->select();
				if( sizeof($new_relate_list) >= 2 ) exit('set');//套件sku 非法
				$new_relate_row = $new_relate_list[0];
				$new_code_id = $new_relate_row ? $new_relate_row['product_code_id'] : 0; //new_sku是否已关联
				$temp = $product_model->where($product_where)->field('id')->find();//只取一条
				$temp || exit('unknown product');
				$result = $product_model->where( array('id'=>$temp['id']) )->save( array('code_id'=>$new_code_id) ); //order_web_product
				$result ? exit('exist') : exit('error2');
			}
			//返回值：	exist:sku已存在,合法 	new:sku不存在,合法	 error:出错,	set:sku是套件，非法, 
		}
	}
	
	//[网站] sku关联code
	public function ajax_web_sku_relate_code()
	{		
		$order_model = D('order_web');
		$sku_model = M('id_product_sku');
		$catalog_model = M('id_catalog');
		$code_model = D('id_product_code');
		$id_product_model = M('id_product');
		$relate_model = M('id_relate_sku');
		$product_model = M('order_web_product');
		$product_original_model = M('order_web_product_original');
		
		if( I('post.action')=='fetch' )
		{
			$sku_row = $sku_model->find( I('post.sku_id') ) ;
			$this->sku_id = I('post.sku_id');
			$this->sku = $sku_row['sku'];
// 			$original_row = $product_original_model->find( I('post.product_original_id') );
// 			$this->order_id = $original_row['order_web_id'];
			$this->order_id = I('post.order_id');
			$this->order_product_id = I('post.order_product_id');
			$this->catalog_list = $catalog_model->where( array('is_work'=>1) )->order('sort_id')->getField('id,name');
			$this->style = 'fetch';//模板文件的使用方式
			$this->type = I('post.type');
			$this->original_set_id = I('post.original_set_id');
			//计算该sku包含于当前哪些 待审核 订单
			$this->affected_count = sku_in_audit_order_list($sku_row['sku'], $sku_row['come_from_id'], 'count');
			echo $this->fetch('ajax_web_sku_relate_code');
		}
		elseif( I('post.action')=='select_change' )
		{
			if( I('post.tag')=='catalog' )
			{
				//product
				$this->list = $id_product_model->where( array('catalog_id'=>I('post.catalog')) )->order('sort_id')->getField('id,name');
				$this->value_0 = true;
				$this->style = 'select';
				$html = $this->fetch('ajax_web_sku_relate_code');
				$data[] = array('tag'=>'product','html'=>$html);
				//其它清空
				$data[] = array('tag'=>'color','html'=>'<option value="0">--请选择--</option>');
				$data[] = array('tag'=>'size','html'=>'<option value="0">--请选择--</option>');
				$data[] = array('tag'=>'code','html'=>'');
				exit(json_encode($data));
			}
			elseif( I('post.tag')=='product' )
			{
				//build color
				$this->list = $code_model->attribute('color', I('post.product'), null, null)->getField('id,value');
				$this->style = 'select';
				$html = $this->fetch('ajax_web_sku_relate_code');
				$data[] = array('tag'=>'color','html'=>$html);
				//build size
				$this->list = $code_model->attribute('size', I('post.product'), null, null)->getField('id,value');
				$html = $this->fetch('ajax_web_sku_relate_code');
				$data[] = array('tag'=>'size','html'=>$html);
				//build code
				$this->list = $code_model->where( array('product_id'=>I('post.product')) )->getField('id,name');
// 				exit(json_encode($this->list));
				$this->style = 'radio';
				$html = $this->fetch('ajax_web_sku_relate_code');
				$data[] = array('tag'=>'code','html'=>$html);
				exit(json_encode($data));
			}
			elseif( I('post.tag')=='color' || I('post.tag')=='size' )
			{
				$where['product_id'] = I('post.product');
				I('post.color') && $where['color_id'] = I('post.color');
				I('post.size') && $where['size_id'] = I('post.size');
		
				//select color,build size
				if( I('post.tag')=='color' )
				{
					$this->list = $code_model->attribute('size', I('post.product'), I('post.color'), null)->getField('id,value');
					$this->style = 'select';
					$this->old_value = I('post.size');
					$html = $this->fetch('ajax_web_sku_relate_code');
					$data[] = array('tag'=>'size','html'=>$html);
				}
				//select size,build color
				elseif( I('post.tag')=='size' )
				{
					$this->list = $code_model->attribute('color', I('post.product'), null, I('post.size'))->getField('id,value');
					$this->style = 'select';
					$this->old_value = I('post.color');
					$html = $this->fetch('ajax_web_sku_relate_code');
					$data[] = array('tag'=>'color','html'=>$html);
				}
				//build code
				$this->list = $code_model->where($where)->getField('id,name');
				$this->style = 'radio';
				$html = $this->fetch('ajax_web_sku_relate_code');
				$data[] = array('tag'=>'code','html'=>$html);
				exit(json_encode($data));
			}
		}
		elseif( I('post.action')=='update' )
		{
			//结果标志
			$error = false;
			//sku info
			$sku_row = $sku_model->find( I('post.sku_id') ) ;
			
			//id_relate_sku
			if( I('post.type')=='add' )//添加关联
			{
				$relate_row['product_sku_id'] = I('post.sku_id');
				$relate_row['product_code_id'] = I('post.code_id');
				$relate_row['number'] = 1;
				$result = $relate_model->add($relate_row);
				$old_code_id = 0;
				$result || $error='添加sku关联关系时出错！';
			}
			else //修改关联
			{
				$relate_row = $relate_model->where( array('product_sku_id'=>I('post.sku_id')) )->find();
				$old_code_id = $relate_row['product_code_id'];
				$where = array('id'=>$relate_row['id']);
				$result = $relate_model->where($where)->save(array('product_code_id'=>I('post.code_id')));
				$result || $error='修改sku关联关系时出错！';
			}
			
			//order_web_product <= order_web_product_original 单件
			$product_original_list = sku_in_audit_order_list( $sku_row['sku'], $sku_row['come_from_id'], 'product_original');
			foreach($product_original_list as $key=>$val)
			{
				//逐行修改 对应的order_web_product
				$where = array(
					'order_web_id'		=> $val['order_web_id'],
					'order_product_id'	=> $val['order_product_id'],
					'code_id'			=> $old_code_id,);
				$temp = $product_model->where($where)->find();//只修改未关联的第一行
				$result = $product_model->where( array('id'=>$temp['id']) )->save(array('code_id'=>I('post.code_id')));
// 				$result || $error='找不到该条原始记录的对应产品！';// $temp 为空时
			}
			
			//order_web_product <= order_web_product_original_set 套件
			$product_original_set_list = sku_in_audit_order_list($sku_row['sku'], $sku_row['come_from_id'], 'product_original_set');
			foreach($product_original_set_list as $key=>$val)
			{
				//判断是套件中的第几条
				$original_set_row = M('order_web_product_original_set')->where( array('id'=>I('post.original_set_id')) )->find();
				$original_set_list = M('order_web_product_original_set')->
					where( array('order_web_product_original_id'=>$original_set_row['order_web_product_original_id']) )->order('id ASC')->select();
				$offset = 0;
// 				exit(json_encode($original_set_list));
				foreach($original_set_list as $key=>$val)
				{
					if( $val['id']==I('post.original_set_id') )
					{
						$offset = $key;
						break;
					}
				}
				
				$original_row = $product_original_model->find( $val['order_web_product_original_id'] );
				//逐行修改 对应的order_web_product
				$where = array(
					'order_web_id'		=> $original_row['order_web_id'],
					'order_product_id'	=> $original_row['order_product_id'],
// 					'code_id'			=> $old_code_id,
				);
				$product_list = $product_model->where($where)->select();
				$result = $product_model->where( array('id'=>$product_list[$offset]['id']) )->save( array('code_id'=>I('post.code_id')) );
// 				$result || $error='找不到该条原始记录的对应产品！';

			}
			
			$error && $success=sizeof($product_original_list)+sizeof($product_original_set_list);
			if($error)
			{
				exit(json_encode(array('status'=>'error','msg'=>$error)));
			}
			else 
			{
				$success = sizeof($product_original_list)+sizeof($product_original_set_list);
				exit(json_encode(array('status'=>'success','msg'=>$success)));
			}
			
		}
	}
	
	//已弃用。网站 sku关联code
	public function ajax_sku_relate_code()
	{
		$order_model = M('order_web');
		$sku_model = M('id_product_sku');
		$catalog_model = M('id_catalog');
		
		$where = array('order_web_id'=>I('post.order_id'));
		
		if( I('post.action')=='fetch' )
		{
			$sku_row = $sku_model->find( I('post.sku_id') ) ;
			$this->sku = $sku_row['sku'];
			$this->sku_id = I('post.sku_id');
			$this->order_id = I('post.order_id');
			$this->catalog_list = $catalog_model->where( array('is_work'=>1) )->order('sort_id')->getField('id,name');
			$this->style = 'fetch';//模板文件的使用方式
			echo $this->fetch('ajax_sku_relate_code');
		}
		elseif( I('post.action')=='select_change' )
		{
			I('post.tag');
			I('post.value');
			
			if( I('post.tag')=='catalog' )
			{
				$product_model = M('id_product');
				//product
				$this->list = $product_model->where( array('catalog_id'=>I('post.catalog')) )->order('sort_id')->getField('id,name');
				$this->value_0 = true;
				$this->style = 'select';
				$html = $this->fetch('ajax_sku_relate_code');
				$data[] = array('tag'=>'product','html'=>$html);
				//其它清空
				$data[] = array('tag'=>'color','html'=>'<option>--请选择--</option>');
				$data[] = array('tag'=>'size','html'=>'<option>--请选择--</option>');
				$data[] = array('tag'=>'code','html'=>'');
				exit(json_encode($data));
			}
			elseif( I('post.tag')=='product' )
			{
				$product_model = D('id_product');
				$code_model = M('id_product_code');
				//color
				$this->list = $product_model->attribute( I('post.product'), 'color')->getField('id,value');
				$this->style = 'select';
				$html = $this->fetch('ajax_sku_relate_code');
				$data[] = array('tag'=>'color','html'=>$html);
				//size
				$this->list = $product_model->attribute( I('post.product'), 'size')->getField('id,value');
				$html = $this->fetch('ajax_sku_relate_code');
				$data[] = array('tag'=>'size','html'=>$html);
				//code
				$this->list = $code_model->where( array('product_id'=>I('post.product')) )->getField('id,name');
				$this->style = 'radio';
				$html = $this->fetch('ajax_sku_relate_code');
				$data[] = array('tag'=>'code','html'=>$html);
				exit(json_encode($data));
			}
			elseif( I('post.tag')=='color' || I('post.tag')=='size' )
			{
				$code_model = M('id_product_code');
				$attribute_model = M('id_product_attribute');
				
				$where['product_id'] = I('post.product');
				I('post.color') && $where['color_id'] = I('post.color');
				I('post.size') && $where['size_id'] = I('post.size');
				
				//size
				if( I('post.tag')=='color' )
				{
					$sub_sql = $code_model->distinct(true)->field('size_id')->where($where)->fetchSql(true)->select();
					$this->list = $attribute_model->where(array('id'=>array('EXP'," IN ($sub_sql)")))->getField('id,value');
					$this->style = 'select';
					$this->old_value = I('post.size');
					$html = $this->fetch('ajax_sku_relate_code');
					$data[] = array('tag'=>'size','html'=>$html);
				}
				//color
				elseif( I('post.tag')=='size' )
				{
					$sub_sql = $code_model->distinct(true)->field('color_id')->where($where)->fetchSql(true)->select();
					$this->list = $attribute_model->where(array('id'=>array('EXP'," IN ($sub_sql)")))->getField('id,value');
					$this->style = 'select';
					$this->old_value = I('post.color');
					$html = $this->fetch('ajax_sku_relate_code');
					$data[] = array('tag'=>'color','html'=>$html);
				}
				//code
				$this->list = $code_model->where($where)->getField('id,name');
				$this->style = 'radio';
				$html = $this->fetch('ajax_sku_relate_code');
				$data[] = array('tag'=>'code','html'=>$html);
				exit(json_encode($data));
			}
			
		}
		elseif( I('post.action')=='update' )
		{
			$relate_model = M('id_relate_sku');
			$product_model = M('order_web_product');
			$row = array();
			$success = true;
			//order_web_product
// 			$product_model-> ;
			//id_relate_sku
			$row['product_sku_id'] = I('post.sku_id');
			$data = I('post.data');
			for($i=0;$i<count($data);$i++)
			{
				$row['product_code_id'] = $data[$i]['code'];
				$row['number'] = $data[$i]['num'];
				$result = $relate_model->add($row);
				$result || $success=false;
			}
			//id_product_sku
			if( count($data) >= 2 )
			{
				$sku_model = M('id_product_sku');
				$result = $sku_model->where( array('id'=>I('post.sku_id')) )->save(array('name'=>I('post.sku_name')));
				$result || $success=false;
			}
			exit(json_encode($success));
		}
	}
	
	//[通用] 编辑运单详情
	public function ajax_edit_delivery()
	{
		//dump(order_weight(I('post.order_id'),I('post.style')));exit;
		$delivery_model = M('order_delivery_parameters');
		if( I('post.style')=='web' )
		{
			$where = array('order_id'=>I('post.order_id'));
		}
		elseif( I('post.style')=='plat' )
		{
			$where = array('order_platform_id'=>I('post.order_id'));
		}
		$delivery_row = $delivery_model->where($where)->find();
		
		if( I('post.action')=='fetch' )
		{
			if($delivery_row)
			{
				$this->old_style = $delivery_row['shipping_style'];
				$this->old_weight = $delivery_row['weight'];
			}
			else //采用预估值
			{
				$priority = order_weight(I('post.order_id'),I('post.style'));
				if($priority)
				{
					$this->old_style = $priority['style'];
					$this->old_weight = $priority['weight'];
				}
				else
				{
					$recommend = delivery_recommend( I('post.order_id'),I('post.style') );
					$this->old_style = $recommend['style'];
					$this->old_weight = $recommend['weight'];
				}
			}
			$this->delivery_style_list = delivery_style();
			$this->hs = hs( $delivery_row['hs'] );
			$this->order_id = I('post.order_id');
			$this->style = I('post.style');//网站，平台
			
			echo $this->fetch('delivery_edit');
		}
		elseif( I('post.action')=='update' )
		{
			$hs = explode(',',I('post.hs'));
			$data['hs'] = $hs[0];
			$data['name'] = $hs[1];
			$data['price'] = $hs[2];
			$data['weight'] = I('post.weight');
			$data['shipping_style'] = I('post.shipping_style');
			$data['operator'] = get_user_name( session('userid') );
			$data['date_time'] = time();
			
			if($delivery_row)
			{
				$result = $delivery_model->where($where)->save($data);
			}
			else
			{
				$data = array_merge($data, $where);
				$result = $delivery_model->add($data);
			}
			//货代直接转历史 action
			$shipping_style =I('post.shipping_style');
			if($shipping_style == 'UPS货代' || $shipping_style == 'EMS')
			{
				$sta['status'] = 'history';
				$sta['message'] = $shipping_style;
				$sta['date_time'] = time();
				$sta['operator'] = session('username');
				
				if( I('post.style')=='web' )
				{
					$where_sta = array('order_web_id'=>I('post.order_id'));
					
					M('order_web_status')->where($where_sta)->save($sta);
					
					$sta['order_web_id'] = I('post.order_id');
					M('order_web_status_history')->add($sta);
				}
				elseif( I('post.style')=='plat' )
				{
					$where_sta = array('order_platform_id'=>I('post.order_id'));
					
					M('order_plat_form_status')->where($where_sta)->save($sta);
					
					$sta['order_platform_id'] = I('post.order_id');
					M('order_plat_form_status_history')->add($sta);
				}
				$delivery_detail['style'] = $shipping_style;
				$delivery_detail['message'] = "normal";
				$delivery_detail['status'] = "normal";
				$delivery_detail['time'] = date('Y-m-d H:i:s',time());
				$delivery = array_merge($where_sta, $delivery_detail);
				M('order_delivery_detail')->add($delivery);          //插入运单表
			}
			//货代直接转历史 end
			echo $result;
		}
	}
	
	//[通用] 手动添加 运单
	public function ajax_delivery_detail_add()
	{
		$delivery_parameters_model = M('order_delivery_parameters');
		$delivery_detail_model = M('order_delivery_detail');
		if( I('post.style')=='web' )
		{
			$parameters_where = array('order_id'=>I('post.order_id'));
			$detail_where = array('order_web_id'=>I('post.order_id'));
		}
		elseif( I('post.style')=='plat' )
		{
			$parameters_where = array('order_platform_id'=>I('post.order_id'));
			$detail_where = array('order_platform_id'=>I('post.order_id'));
		}
		$parameters_row = $delivery_parameters_model->where($parameters_where)->find();
		$detail_row = $delivery_detail_model->where($detail_where)->find();
		
		if( I('post.action')=='fetch' )
		{
			if($detail_row)
			{
				$this->old_shipping_style = $detail_row['style'];
				$this->old_number = $detail_row['delivery_number'];
			}
			if($parameters_row) 
			{
				$this->old_weight = $parameters_row['weight'];
			}
			$this->delivery_style_list = delivery_style();
			$this->order_id = I('post.order_id');
			$this->style = I('post.style');//网站，平台
				
			echo $this->fetch('delivery_detail_add');
		}
		elseif( I('post.action')=='update' )
		{
			$time = time();
			$detail_data['style'] = I('post.shipping_style');
			$detail_data['delivery_number'] = I('post.delivery_number');
			$detail_data['time'] = date('Y-m-d H:i:s',$time);
			$detail_data['message'] = 'normal';
			$detail_data['status'] ='normal';
			
			$paramters_data['weight'] = I('post.weight');
			$paramters_data['shipping_style'] = I('post.shipping_style');
			$paramters_data['date_time'] = $time;
			$paramters_data['operator'] = get_user_name( session('userid') );
				
			if($detail_row)
			{
				$result = $delivery_detail_model->where($detail_where)->save($detail_data);
			}
			else
			{
				$detail_data = array_merge($detail_data,$detail_where);
				$result = $delivery_detail_model->add($detail_data);
			}
			
			if($parameters_row)
			{
				$result = $delivery_parameters_model->where($parameters_where)->save($paramters_data);
			}
			else
			{
				$paramters_data = array_merge($paramters_data,$parameters_where);
				$result = $delivery_parameters_model->add($paramters_data);
			}
			
			echo $result;
		}
	}
	
	//[通用] 查看全部备注  data 1:平台  2：网站
	public function remark_check()
	{
		$userid=session('userid');
		$username = session('username');    // 用户名
		$order_business_messageDB=M('order_business_message');
		$order_plat_formDB=M('order_plat_form');
		$order_webDB=M('order_web');
		$userDB=M('user');
		$user_list=$userDB->where('1=1')->field('id,username')->order('id desc')->select();
		
		if(IS_POST){
			if($_POST['sta']!=""){                 //状态
				$map['status']=$_POST['sta'];
				$this->assign('sta',$_POST['sta']);
				}
			if($_POST['data']==1){                //平台
				$map['order_id'] =0;
				$this->assign('data',$_POST['data']);
				}
			if( $_POST['data']==2){                //网站
				$map['order_platform_id'] =0;
				$this->assign('data',$_POST['data']);
				}
			if($_POST['operator']!=""){		//发起者
				$map['operator'] =$_POST['operator'];
				$this->assign('operator',$_POST['operator']);
				}	
			if($_POST['accept']!=""){				//指定人员
				$map['accept'] =$_POST['accept'];
				$this->assign('accept',$_POST['accept']);
				}
			if($_POST['order_number']!=""){				//订单号
				$order_number_id=$order_plat_formDB->where('`order_number` ='."'".$_POST['order_number']."'")->getfield('id');
				if($order_number_id){
					$map['order_platform_id'] =$order_number_id;
					}else{
						$order_number_id=$order_webDB->where('`order_number` ='."'".$_POST['order_number']."'")->getfield('id');
						$map['order_id'] =$order_number_id;
						}
				$this->assign('order_number',$_POST['order_number']);
				}			
			if($_POST['yes'] ==1){						//时间区间
				if($_POST['beginTime'])	{
					if($_POST['endTime']){
						$map['date_time']=array('between',array(strtotime($_POST['beginTime']),strtotime($_POST['endTime'])));
						$this->assign('beginTime',$_POST['beginTime']);
						$this->assign('endTime',$_POST['endTime']);
						$this->assign('yes',$_POST['yes']);
						}
					}
			}
		}else if(IS_GET){
			if($_GET['sta']!=""){                    //状态
				$map['status']=$_GET['sta'];
				$this->assign('sta',$_GET['sta']);
				}
			if($_GET['data']==1){                    //平台
				$map['order_id'] =0;
				$this->assign('data',$_GET['data']);
				}
			if($_GET['data']==2){                    //网站
				$map['order_platform_id'] =0;
				$this->assign('data',$_GET['data']);
				}
			if($_GET['operator']!=""){          //发起者
				$map['operator'] =$_GET['operator'];
				$this->assign('operator',$_GET['operator']);
				}	
			if($_GET['accept']!=""){            //指定人员
				$map['accept'] =$_GET['accept'];
				$this->assign('accept',$_GET['accept']);
				}
			if($_GET['order_number']!=""){           //订单号
				$order_number_id=$order_plat_formDB->where('`order_number` ='."'".$_GET['order_number']."'")->getfield('id');
				if($order_number_id){
					$map['order_platform_id'] =$order_number_id;
					}else{
						$order_number_id=$order_webDB->where('`order_number` ='."'".$_GET['order_number']."'")->getfield('id');
						$map['order_id'] =$order_number_id;
						}
				$this->assign('order_number',$_GET['order_number']);
				}			
			if($_GET['yes']){	                     //时间区间
				if($_GET['beginTime'])	{
					if($_GET['endTime']){
						$map['date_time']=array('between',array(strtotime($_GET['beginTime']),strtotime($_GET['endTime'])));
						$this->assign('beginTime',$_GET['beginTime']);
						$this->assign('endTime',$_GET['endTime']);
						$this->assign('yes',$_GET['yes']);
						}
					}
				}
			}else{
				$map="1=1";
				}
		import('Org.Util.Page');// 导入分页类
		$remark_coun=$order_business_messageDB->where($map)->count();
		
        $Page       = new \Page($remark_coun);// 实例化分页类 传入总记录数
		$nowPage = isset($_GET['p'])?$_GET['p']:1;
        $show       = $Page->show();// 分页显示输出
		$info=$order_business_messageDB->where($map)->page($nowPage.','.C('LISTROWS'))->order('id desc')->select();
		foreach($info as $k=>$v){
			$info[$k]['sta']=remark_sta($info[$k]['status']);
			if($info[$k]['order_id']==0){
				$info[$k]['order_number']=$order_plat_formDB->where('`id` = '.$info[$k]['order_platform_id'])->getField('order_number');
				}
			if($info[$k]['order_platform_id']==0){
				$info[$k]['order_number']=$order_webDB->where('`id` = '.$info[$k]['order_id'])->getField('order_number');
				}	
			}
			$info_all_coun=$order_business_messageDB->where('1=1')->count();
			$info_ok_coun=$order_business_messageDB->where('`status` =1')->count();
			$info_not_coun=$order_business_messageDB->where('`status` =0')->count();
			$info_failure_coun=$order_business_messageDB->where('`status` =2')->count();	
			$info_platform_coun=$order_business_messageDB->where('`order_platform_id` != 0')->count();	
			$info_web_coun=$order_business_messageDB->where('`order_id` !=0')->count();	

		$this->assign('info_all_coun',$info_all_coun);
		$this->assign('info_ok_coun',$info_ok_coun);
		$this->assign('info_not_coun',$info_not_coun);
		$this->assign('info_failure_coun',$info_failure_coun);
		$this->assign('info_platform_coun',$info_platform_coun);
		$this->assign('info_web_coun',$info_web_coun);
		
		
		$this->assign('user_list',$user_list);
		$this->assign('username',$username);
		$this->assign('page',$show);
		$this->assign('info',$info);
		$this->display();
	}
	//备注状态改变
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
	
	//弃用？ 修改订单状态
	public function order_status_update()
	{
		layout(false);
		$id = I('get.id');//order_id
		$data = I('get.data');//web网站，plat平台
		
		if(I('get.style') == 'web')
			$status_model = M('order_web_status');
		else if(I('get.style') == 'plat')
			$status_model = M('order_plat_form_status');
		
		if(IS_POST)
		{
			$date['status'] = I('post.status');
			$date['message'] = I('post.content');
			$date['operator'] = session('userid');
			$date['date_time'] = time();
			
			if(I('get.style') == 'plat'){
				$date['order_platform_id']=I('get.id');
				$map['order_platform_id']=I('get.id');
			}
			elseif(I('get.style') == 'web')
			{
				$date['order_web_id']=I('get.id');
				$map['order_web_id']=I('get.id');
			}		
			$info = $data->where($map)->find();
			if($info)
			{
				$return=$data->where($map)->save($date);
			}
			else
			{
				$return=$data->add($date);
			}
			if($return)
			{
				$this->display();
				echo '<script type="text/javascript">document.getElementById("remark_smile").innerHTML="<span>:)</span> </br> 状态修改成功！！</div>";
					function aa(){
						document.get
						window.parent.document.getElementById("layui-layer1").style.display="none";
						window.parent.document.getElementById("layui-layer-shade1").style.display="none";
						}
						setTimeout(aa,1000);
					</script>';
			}
			else
			{
				$this->error('状态修改失败！！');
			}
		}
		else
		{
			$status_row = $status_model->field('status')->find($id);
			if($status_row) $this->old_status = $status_row['status'];
			else $this->old_status = 'audit';
			$this->status_list = order_status();//状态列表
			$this->display();
		}
	}
	
	//弃用。平台修改地址address[1-3]
	function ajax_edit_order_address()
    {
    	$shipping_model = D('order_plat_form_shipping');
    	$data['address1'] = I('post.address1');
    	$data['address2'] = I('post.address2');
    	$data['address3'] = I('post.address3');
    	$result = $shipping_model->where(array('id'=>I('post.id')))->save($data);
    	echo $result;
    }
    
    //弃用。网站修改地址
	function ajax_edit_order_web_address()
	{
		$order_web_address = D("order_web_address");
		$data['address'] = I('post.address_web');
		$update_address = $order_web_address->where(array('id' =>I('post.id')))->save($data);
		if($update_address)
		{
			$select_address = $order_web_address->where(array('id' =>I('post.id')))->getField('address');
			echo $select_address;
		}
	}
	
	//弃用。提交订单
 	public function order_submit()
	{
 		if(isset($_GET['data']))
 		{
 		    $id=$_GET['id'];            //get传值
 		    $data=$_GET['data'];       //1 平台 2 网站
 		    if($data==1)
 		    {
 		        $order_web=M("order_web");
 		        $order_web_come_from=$order_web->field("come_from")->where("id=$id")->select();
 		        $come_from=$order_web_come_from[0][come_from];
 		        order_to_factory(0,$id,"$come_from");
 		    }
 		    else
 		    {
 		        $order_plat_form=M("order_plat_form");
 		        $order_plat_come_from=$order_plat_form->field("come_from")->where("id=$id")->select();
 		        $come_from=$order_plat_come_from[0][come_from];
 		        order_to_factory($id,0,"$come_from");
 		    }
 		}
 		else
 		{
	       if(isset($_POST['plar']))
	       {
	           $plar_id = $_POST['plar'];   //平台
	           $order_web=M("order_web");
	           foreach($plar_id as $value)
	           {    
	               $order_web_come_from=$order_web->field("come_from")->where("id=$value")->select();
	               $come_from=$order_web_come_from[0][come_from];
	               order_to_factory(0,$id,"$come_from");
	           }
	       }
 		   else
 		   {
 		       $order_plat_form=M("order_plat_form");
 		       $web_id = $_POST['web'];     //网站
 		       foreach($web_id as $value)
 		       {  
 		           $order_plat_come_from=$order_plat_form->field("come_from")->where("id=$value")->select();
 		           $come_from=$order_plat_come_from[0][come_from];
 		           order_to_factory($id,0,"$come_from");
 		       }
 		   }
 		}	
	}
	
	//[平台] 修改地址信息
	public function shipping_edit_plat()
	{
		$shipping_model = D('order_plat_form_shipping');
		if(IS_POST)
		{
			$shipping_model->create();
			$shipping_model->where(array('id'=>I('get.shipping_id')))->save();
			if($_GET[come]=="plat_details")
			{
				redirect(U('Admin/OrderManage/order_details',array("order_id"=>$_GET[shipping_id])));	
			}
			unset($_GET['shipping_id']);//去除 操作标志参数
			$url =  U('Admin/OrderManage/order_plat',I('get.'),null,true);
			redirect( $url );
		}
		$this->row = $shipping_model->find(I('get.shipping_id'));
		$this->display();
		
	}
	//[网站] 修改地址信息
	public function shipping_edit_web()
	{
// 		dump(I('get.'));exit;
		$shipping_model = D('order_web_address');
		if(IS_POST)
		{
			$shipping_model->create();
			$shipping_model->where(array('id'=>I('get.shipping_id')))->save();
			if($_GET[come]=="plat_details")
			{
				redirect(U('Admin/OrderManage/order_details',array("order_id"=>$_GET[shipping_id])));
			}
			unset($_GET['shipping_id']);//去除 操作标志参数
			$url =  U('Admin/OrderManage/order_web',I('get.'),null,true);
			redirect( $url );
		}
		$this->row = $shipping_model->find(I('get.shipping_id'));
		$this->display();
	}
	
	//[平台] 订单详情页
	public function order_details(){
		//运单信息及订单信息
		$message_shipping=D("OrderPlatForm");
		$order_message_shipping_sql=$message_shipping->where(array("id"=>$_GET[order_id]))->relation(true)->find();
		$this->assign("order_message_shipping",$order_message_shipping_sql);

		//平台历史订单
		$order_history_list=$message_shipping->where("email='$order_message_shipping_sql[email]' and id!=$_GET[order_id]")->relation(true)->select();
		$all_price=0;
		$all_num=0;
		foreach ($order_history_list as $value){
			$all_price+=$value[price];
			$all_num+=1;
		}
// 				print_r($order_history_list);
// 				exit();
		$this->all_price=$all_price;
		$this->all_num=$all_num;
		$this->order_status=$_GET["order_status"];
		$this->assign("order_history_list",$order_history_list);
		$this->display();
	}
	//[网站] 订单详情页
	public function order_details_web(){
		$web_message_shipping=D("OrderWeb");
		$web_message_shipping_sql=$web_message_shipping->where(array("id"=>$_GET[order_id]))->relation(true)->find();
		$this->assign("web_message_shipping",$web_message_shipping_sql);

		$order_history_list=$web_message_shipping->where("email='$web_message_shipping_sql[email]' and id!=$_GET[order_id]")->relation(true)->select();
		//print_r($web_message_shipping_sql);
 		//exit();
		$all_price=0;
		$all_num=0;
		foreach ($order_history_list as $value){
			$all_price+=$value[total_price];
			$all_num+=1;
		}
		$this->all_price=$all_price;
		$this->all_num=$all_num;
		$this->order_status=$_GET['order_status'];
		$this->assign("order_history_list",$order_history_list);
// 		dump($order_history_list);exit();
		
		$order_model = D('order_web');
		$order_list = $order_model->relation(true)->select(I('get.order_id'));
		$order_list = complete_web_order($order_list,I('get.come_from_id'));
		$this->order_info = $order_list[0];
// 		print_r($order_list);exit;
		
		$this->display();
	}
	
	//弃用。不明。
	public function order_message(){
		if(I('get.deal')=='add'){
			if(I('post.style')=='plat'){
				$data["order_platform_id"]=$_POST[order_id];
			}
			if(I('post.style')=='web'){
				$data["order_id"]=$_POST[order_id];
			}
			$message = M('order_business_message');
			$data["message"]=$_POST[plat_message];
			$data["date_time"]=time();
			$data["operator"]=$_POST[operator];
			$data["accept"]=$_POST[accept];
			$data["status"]=0;
			$message->add($data);
			if(I('post.style')=='plat'){
				redirect(U('/Admin/OrderManage/order_details',array("order_id"=>$_POST[order_id])));
			}
			if(I('post.style')=='web'){
				$this->redirect('OrderManage/order_details_web',array('order_id'=>$_POST[order_id]));
			}
		}
		$user=D("user");
		$user_list=$user->getAllUser();
		echo json_encode($user_list);
	}
	
	//[通用] 添加/修改运单信息；在详情页中
	public function order_delivery()
	{
		if(I("get.come")=="ajax")
		{
			$style_list=delivery_style();
			echo json_encode($style_list);
			exit();
		}
		if(I('get.come')=='web')
		{
			$data["order_web_id"]=$_POST[order_id];
			$data["order_platform_id"]=0;
		}
		if(I('get.come')=='plat')
		{
			$data["order_platform_id"]=$_POST[order_id];
			$data["order_web_id"]=0;
		}
		$data["style"]=$_POST["delivery_style"];
		$data["delivery_number"]=$_POST["delivery_number"];
		$data["message"]="normal";
		$data["status"]="normal";
		$data["time"]=date("Y-m-d,H:i:s",time());
		$order_delivery_detail=M("order_delivery_detail")->add($data);
		if($order_delivery_detail)
		{
			$this->success("添加成功");
		}else
		{
			$this->error("添加失败");
		}
	}
	//[通用] 修改收件地址；在详情页中
	public function address_edit(){
		$address=M("order_web_address");
		if(IS_POST){
			$address->create();
			$insert_address=$address->where(array("id"=>$_GET[address_id]))->save();
			if($insert_address){
				$this->redirect('OrderManage/order_details_web',array('order_id'=>$_GET[address_id]));
			}
		}
		$address_sql=$address->where(array("order_web_id"=>$_GET[address_id]))->find();
		$this->assign("address",$address_sql);
		$this->display('address_edit_web');
	}
	//[网站] 添加额外费用
	public function order_extra_costs(){
		$delivery_style=M("order_delivery_detail");
		$order_other_price=M("order_delivery_other_price");
		if(I('post.style')=='web'){
			$data["order_web_id"]=$_POST[order_id];
			$delivery_style_sql=$delivery_style->where(array("order_web_id"=>$_POST[order_id]))->field("style,delivery_number")->find();
		}
		if(I('post.style')=='plat'){
			$data["order_platform_id"]=$_POST[order_id];
			$delivery_style_sql=$delivery_style->where(array("order_platform_id"=>$_POST[order_id]))->field("style,delivery_number")->find();
		
		}
		$data["style"]=$delivery_style_sql[style];
		$data["delivery_number"]=$delivery_style_sql[delivery_number];
		$data["tariffs"]=$_POST[tariffs];
		$data["remote"]=$_POST[remote];
		$data["operator"]=$_POST[operator];
		$data["time"]=date("Y-m-d H:i:s",time());
		$add_order_other_price=$order_other_price->add($data);
		if($add_order_other_price){
			$this->redirect("OrderManage/order_web",array("order_status"=>"all"));
		}
	}
	
	//[网站] 更新订单
	public function get_web_order($country_list)
	{
		set_time_limit(0);
		
		$new_order_return=array("status"=>"null","message"=>'','all_new_order_number'=>0,"country_status"=>array());
		$order_href="?";
		$order=M("order_web");
		$order_web_address=M("order_web_address");
		$orde_web_product_original=M("order_web_product_original");
		$orde_web_product=M("order_web_product");
		$relate_sku=M("id_relate_sku");
		$id_product_sku=M("id_product_sku");
		$product_code=M("id_product_code");
		$order_web_product_customization=M('order_web_product_customization');
		$order_web_product_original_set=M('order_web_product_original_set');
		$order_web_nightwear_customization=M('order_web_nightwear_customization');
		$order_web_product_extra=M('order_web_product_extra');
		$order_web_supplement=M('order_web_supplement');
		$come_from_handle=M("id_come_from");
		$web_sample_email=M("web_sample_email");
		
		//循环获取最近一个订单
		foreach($country_list as $country)
		{
			$country_come_from_id=$come_from_handle->where("name='$country'")->find();
			$order_last_id=$order->where(array('come_from_id'=>$country_come_from_id['id']))->field('order_number')->order("order_number desc")->limit("0,1")->select();
			$order_href.=$country."=".$order_last_id[0]["order_number"]."&";
		}
		$new_order = json_decode(httpPost("http://www.lilysilk.com/mono_admin/mono_admin_template/erpGetNewOrderApi.php",$order_href),true);
		
		/*
		echo "<pre>";
		print_r($new_order);
		die();
		*/
		
		if(is_array($new_order)){
		//处理返回值
		foreach($new_order as $country=>$order_list)
		{
			if(!empty($order_list)){
				$new_order_return['country_status'][$country]=array('new_order_number'=>0);
				$come_from=$come_from_handle->where("name='".$country."'")->find();
				foreach($order_list as $order_id=>$order_detail)
				{
					if($order_id!='sample_no_send_eamil')
					{					
					$order_isset=$order->where("order_number=$order_id and come_from_id=".$come_from[id])->select();
					if($order_isset)
					{
						continue;
					}
					//同步订单信息
					$order_web_detail=array();
					$order_web_detail['order_number']=$order_id;
					$order_web_detail['email']=$order_detail[customer_email];
					$order_web_detail['customer_id']=$order_detail[customer_id];
					$order_web_detail['message']=$order_detail[message];
					$order_web_detail['first_name']=$order_detail['first_name'];
					$order_web_detail['last_name']=$order_detail['last_name'];
					if(isset($order_detail[couponcode]))
					{
						$order_web_detail['couponcode']=$order_detail[couponcode];
					}
					$order_web_detail['total_price']=$order_detail[total_price];
					$order_web_detail['total_price_discount']=$order_detail[total_price_discount];
					$order_web_detail['device']=$order_detail[come_from];
					$order_web_detail['payment_style']=$order_detail[check_out_style];
					$order_web_detail['come_from_id']=$come_from[id];
					$order_web_detail['cookie_from']=$order_detail[cookie_from];
					$order_web_detail['least_from']=$order_detail[least_from];
					$order_web_detail['date_time']=$order_detail[time];
					$order_web_id=$order->add($order_web_detail);
					
					
					if($order_web_id)
					{
					$new_order_return['country_status'][$country]['new_order_number']++;
					$new_order_return['all_new_order_number']++;
					
					if($order_detail[isset_address]=="yes"){
						//同步订单地址
						$order_web_address_array=array();
						$order_web_address_array['order_web_id']=$order_web_id;
						$order_web_address_array['first_name']=$order_detail[first_name];
						$order_web_address_array['last_name']=$order_detail[last_name];
						$order_web_address_array['country']=$order_detail[country];
						$order_web_address_array['province']=provinces_pithy($country,$order_detail[province]); //省份转换简写
						$order_web_address_array['city']=$order_detail[city];
						
						$order_web_address_array['address']=preg_replace('/<["br"]+>/',' ',$order_detail[address]); //查找<br> 换成空格
						$order_web_address_array['code']=$order_detail[code];
						$order_web_address_array['telephone']=$order_detail[telephone];
						if(!$order_web_address->add($order_web_address_array))
						{
							$new_order_return['message'].=$country."站，订单".$order_id."未获取到订单地址；<br>";
						}
					}
					else
					{
						$new_order_return['message'].=$country."站，订单".$order_id."未获取到订单地址；<br>";
					}
					//获取付款失败记录、用户ip、用户来源
					$order_web_supplement_data['order_web_id']=$order_web_id;
					if(!empty($order_detail[payment_fail]) || !empty($order_detail[custome_ip]) || !empty($order_detail[come_from_history])){
						$order_web_supplement_data['payment_fail']=$order_detail[payment_fail];
						$order_web_supplement_data['custome_ip']=$order_detail[custome_ip];
						$order_web_supplement_data['come_from_history']=$order_detail[come_from_history];
						$order_web_supplement->add($order_web_supplement_data);
					}
					
					//同步订单产品列表
					foreach($order_detail['product_list'] as $order_product_id=>$product_value)
					{
						//保存原数据
						$orde_web_product_original_data['order_product_id']=$order_product_id;
						$orde_web_product_original_data['order_web_id']=$order_web_id;
						$orde_web_product_original_data['product_set_name']=$product_value['product_set_name'];
						$orde_web_product_original_data['product_name']=$product_value['product_name'];
						$orde_web_product_original_data['color']=$product_value['product_color_name'];
						$orde_web_product_original_data['size']=$product_value['product_size_name'];
						$orde_web_product_original_data['sku']=$product_value['sku'];
						$orde_web_product_original_data['img']=$product_value['img'];
						$orde_web_product_original_data['href']=$product_value['href'];
						$orde_web_product_original_data['number']=$product_value['product_number'];
						$new_order_web_product_original=$orde_web_product_original->add($orde_web_product_original_data);
						if(!$new_order_web_product_original)
						{
							$new_order_return['message'].=$country."站，订单".$order_id."，订单产品号".$order_product_id."原数据同步失败；<br>";
						}
						
						
						//保存数据
						$order_web_product_data['order_web_id']=$order_web_id;
						$order_web_product_data['order_product_id']=$order_product_id;
						$order_web_product_data['status']="正常";
						$order_web_product_data['price']=$product_value['product_total_price'];
						$order_web_product_data['discount_price']=$product_value['product_total_price_discount'];
						$id_product_sku_id=0;
						if(!isset($product_value['set_list'])){
							//单件产品
							if($product_value['sku']!=''){
								$id_product_sku_id=$id_product_sku->field('id')->where("sku='".$product_value['sku']."' and come_from_id={$come_from[id]}")->find();
							}
							$code_id=0;
							if($id_product_sku_id){
								$relate_sku_code=$relate_sku->field('product_code_id')->where('product_sku_id='.$id_product_sku_id[id])->find();
								if($relate_sku_code)
								{
									$code_id=$relate_sku_code['product_code_id'];
								}
							}
							$order_web_product_data['set_sku']="";
							$order_web_product_data['code_id']=$code_id;
							$order_web_product_data['number']=$product_value['product_number'];
							$orde_web_product_id=$orde_web_product->add($order_web_product_data);
							if(!$orde_web_product_id)
							{
								$new_order_return['message'].=$country."站，订单".$order_id."，订单产品号".$order_product_id."erp数据同步失败；<br>";
							}
							else
							{
								//睡衣定制
								if(isset($product_value['nightwear_customization']))
								{
									$order_web_nightwear_customization_data['order_web_product_id']=$orde_web_product_id;
									$order_web_nightwear_customization_data['customization']=$product_value['nightwear_customization'];
									$order_web_nightwear_customization->add($order_web_nightwear_customization_data);
								}
								$product_extra=false;
								$order_web_product_extra_data[gift_product_name]="";
								$order_web_product_extra_data[gift_product_img]="";
								$order_web_product_extra_data[gift_name]="";
								$order_web_product_extra_data[gift_box]="";
								$order_web_product_extra_data[gift_box_message]="";
								$order_web_product_extra_data[gift_message]="";
								$order_web_product_extra_data[gramming_name]="";
								$order_web_product_extra_data[gramming_style]="";
								$order_web_product_extra_data[gramming_color]="";
								$order_web_product_extra_data[gramming_img]="";
								//附送礼品
								if(isset($product_value[product_gift_id]))
								{
									$order_web_product_extra_data[gift_product_name]=$product_value[product_gift_product_name];
									$order_web_product_extra_data[gift_product_img]=$product_value[product_gift_product_img];
									$order_web_product_extra_data[gift_name]=$product_value[product_gift_name];
									$order_web_product_extra_data[gift_message]=$product_value[product_gift_message];
									$product_extra=true;
								}
								//礼品盒
								if(isset($product_value['product_box_message']))
								{
									$order_web_product_extra_data[gift_box]=$product_value[product_box_img];
									$order_web_product_extra_data[gift_box_message]=$product_value[product_box_message];
									$product_extra=true;
								}
								//绣字
								if(isset($product_value['product_gramming_name']))
								{
									$order_web_product_extra_data[gramming_name]=$product_value[product_gramming_name];
									$order_web_product_extra_data[gramming_style]=$product_value[product_gramming_style];
									$order_web_product_extra_data[gramming_color]=$product_value[product_gramming_color];
									$order_web_product_extra_data[gramming_img]=$product_value[product_gramming_img];
									$product_extra=true;
								}
								if($product_extra)
								{
									$order_web_product_extra_data[order_web_product_id]=$orde_web_product_id;
									$order_web_product_extra->add($order_web_product_extra_data);
								}
							}
						}
						
						
						if(!empty($product_value['set_list']))
						{
							//套件产品同步
							foreach($product_value['set_list'] as $set_id=>$set_list)
							{
								$order_web_product_original_set_data['order_web_product_original_id']=$new_order_web_product_original;
								$order_web_product_original_set_data['product_name']=$set_list['product_name'];
								$order_web_product_original_set_data['color']=$product_value['product_color_name'];
								$set_size="";
								if($set_list['pillowcase_size']!=0)
								{
									$set_size.=$set_list['pillowcase_size_name'];
								}
								else
								{
									$set_size.=$product_value['product_size_name'];
								}
								$set_size.=$set_list['size_descriotion'];
								$order_web_product_original_set_data['size']=$set_size;
								$order_web_product_original_set_data['number']=$set_list['number'];
								$order_web_product_original_set_data['sku']=$set_list['sku'];
								if(!$order_web_product_original_set->add($order_web_product_original_set_data))
								{
									$new_order_return['message'].=$country."站，订单".$order_id."，订单产品号".$order_product_id."，套件号".$set_list."原数据同步失败；<br>";
								}
								
								$id_product_sku_id=0;
								//套件单品
								if($set_list['sku']!=""){
								$id_product_sku_id=$id_product_sku->field('id')->where("sku='".$set_list['sku']."' and come_from_id={$come_from[id]}")->find();
								}
								$code_id=0;
								if($id_product_sku_id){
									$relate_sku_code=$relate_sku->field('product_code_id')->where('product_sku_id='.$id_product_sku_id[id])->find();
									if($relate_sku_code)
									{
										$code_id=$relate_sku_code['product_code_id'];
									}
								}
								$order_web_product_data['code_id']=$code_id;
								$order_web_product_data['set_sku']=$product_value['sku'];
								$order_web_product_data['number']=$set_list['number'];
									
								if(!$orde_web_product->add($order_web_product_data))
								{
									$new_order_return['message'].=$country."站，订单".$order_id."，订单产品号".$order_product_id."，套件号".$set_list."erp数据同步失败；<br>";
								}
								
							}
						}
						
					}
					//同步定制产品
					if(isset($order_detail['customezation']))
					{
						foreach($order_detail['customezation'] as $customezation_list)
						{
							$customezation_data['order_web_id']=$order_web_id;
							$customezation_data['name']=$customezation_list['customization_name'];
							$customezation_data['description']=$customezation_list['customization_description'];
							$customezation_data['price']=$customezation_list['customization_price'];
							$customezation_data['href']=$customezation_list['customization_href'];
							$order_web_product_customization->add($customezation_data);
						}
					}
				}
				else
				{
					$new_order_return['message'].=$country."站，订单".$order_id."无法同步；<br>";
				}
				}
				else 
				{
					$come_from_id=$come_from[id];
					$web_sample_old_email=$web_sample_email->where("come_from_id=$come_from_id")->select();
					if($web_sample_old_email)
					{
						$web_sample_email->where("come_from_id=$come_from_id")->delete();
					}
					
					$web_sample_email_data['come_from_id']=$come_from_id;
					foreach($order_detail as $sample_email_list)
					{
						if(!empty($sample_email_list))
						{
							$web_sample_email_data['email']=$sample_email_list;
							$web_sample_email->add($web_sample_email_data);
						}
					}
					
				}
				}
			}
		}
		}
		else
		{
			$new_order_return=array("status"=>"error",'all_new_order_number'=>0,"message"=>"no_return","country_status"=>array());
		}
		if($new_order_return['message']!="")
		{
			$new_order_return["status"]="error";
		}
		else
		{
			if($new_order_return['all_new_order_number']>0)
			{
				$new_order_return["status"]="successful";
			}
			else
			{
				$new_order_return["status"]="error";
				$new_order_return["message"]="no_return";
			}
		}
		return $new_order_return;
	}
	

	//弃用。网站打印订单
// 	public function order_print_web()
// 	{
// 		$order_web_model = D('order_web');          //网站
// 		layout(false);
// 		if(I('post.check'))
// 		{
// 			$id=I('post.check');
// 			$data=$order_web_model;
// 			foreach($id as $k=>$v)
// 			{
// 				$info[$k]=$data->relation(true)->where('`id` = '.$v)->find();
// 			}
// 		}
// 		else
// 		{
// 			$this->error('没有选择要打印的订单');	
// 		}
// 		//dump($info);exit;
// 		$this->assign('info',$info);
// 		$this->display();	
// 	}
	//弃用。平台，打印订单      
// 	public function order_print_plat()
// 	{
// 		$order_plat_form_model = D('order_plat_form');  //平台
// 		layout(false);
// 		if(I('post.check'))
// 		{
// 			$id=I('post.check');
// 			$data=$order_plat_form_model;
// 			foreach($id as $k=>$v)
// 			{
// 				$info[$k]=$data->relation(true)->where('`id` = '.$v)->find();
// 			}
// 		}
// 		else
// 		{
// 			$this->error('没有选择要打印的订单');	
// 		}
// 		//dump($info);exit;
// 		$this->assign('info',$info);
// 		$this->display();	
// 	}
	//弃用。平台，打印订单     
// 	public function order_print_amazon()
// 	{
// 		$order_plat_form_model = D('order_plat_form');  //平台
// 		layout(false);
// 		if(I('post.check'))
// 		{
// 			$id=I('post.check');
// 			$data=$order_plat_form_model;
// 			foreach($id as $k=>$v)
// 			{
// 				$info[$k]=$data->relation(true)->where('`id` = '.$v)->find();
// 			}
// 		}
// 		else
// 		{
// 			$this->error('没有选择要打印的订单');
// 		}
// 		//dump($info);exit;
// 		$this->assign('info',$info);
// 		$this->display();
// 	}
	//[通用] 打印订单详情
	public function order_detail_print()
	{
		layout(false);
		if(I('post.check'))
		{
			$id=I('post.check');
			$order_web_model = D('order_web');
			$order_plat_form_model = D('order_plat_form');
			$id_come_from=M('id_come_from');
			$print_recordDB = M('print_record');
			$nnn= 0 ;
			foreach($id as $k=>$v)
			{
				
				if(I('post.come')=="web")
				{
					$data=$order_web_model;
					$info[$k]=$data->relation(true)->where('`id` = '.$v)->find();
					
				}
				elseif(I('post.come')=="plat")
				{
					$data=$order_plat_form_model;
					$info[$k]=$data->relation(true)->where('`id` = '.$v)->find();
				}
				elseif(strstr($v,"_"))
				{
					$v_a=explode("_",$v);
					$come=$v_a[0];
					$order_id=$v_a[1];
					$come_detail=$id_come_from->where("name='$come'")->find();
					if($come_detail[style]=="plat")
					{
						$record = $print_recordDB->where('`order_platfrom_id` = '.$order_id)->find();
						if(!$record)
						{
							$data=$order_plat_form_model;
							$info[$nnn]=$data->relation(true)->where('`id` = '.$order_id)->find();
							$date['order_platfrom_id'] = $order_id;
							$nnn++;
						}
					}
					else
					{
						$record = $print_recordDB->where('`order_web_id` = '.$order_id)->find();
						if(!$record)
						{
							$data=$order_web_model;
							$info[$k]=$data->relation(true)->where('`id` = '.$order_id)->find();
							$date['order_web_id'] = $order_id;
							$nnn++;
						}
					}
					$date['time'] = date('Y-m-d',time());
					$print_recordDB->add($date);
				}
				//
			}
		}
		else
		{
			$this->error('没有选择要打印的订单');
		}
		if(count($info) == 0)
		{
			$this->error('选择的订单都已打印,如想打印请到 <span style=" color:red;font-weight: bold;"> 尼克 </span> 办公室');	
		}
		//dump($info);exit;
		$info_num= count($info);
		$this->assign('info',$info);
		$this->assign('info_num',$info_num);
		$this->display();
	}
	
	//[通用] 导出订单
	public function export_execl_order()
	{
		$order_plat_form_model = D('order_plat_form');  //平台
		$order_web_model = D('order_web');          //网站
		$order_web_addressDB= M('order_web_address');
		$order_plat_form_statusDB=M('order_plat_form_status');
		$order_web_statusDB= M('order_web_status');
		if(I('get.data')=="web")
		{
			$data=$order_web_model;
			$sta['status'] = array('in',"audit,accept,shipping");
			$order_web_id=$order_web_statusDB->field('order_web_id')->where($sta)->group('order_web_id')->select();
			foreach($order_web_id as $k=>$v)
			{
				$id[$k]=$v['order_web_id'];
			}
			$where['id']=array('in',$id);
			$info = $data->where($where)->select();
			foreach($info as $k=>$v)
			{
				$list[$k]['order_number']=$v['order_number'];
				$list[$k]['date_time']=$v['date_time'];
				$list[$k]['email']=$v['email'];
				$name[$k]=$order_web_addressDB->field('first_name,last_name,country')->where('`order_web_id` = '.$v['id'])->find();
				$list{$k}['name'] =$name[$k]['first_name']." ".$name[$k]['last_name'];
				$list[$k]['country']=$name[$k]['country'];
				$list[$k]['plat']=get_come_from_name($v['come_from_id']);
			}
			$title=array('订单号','下单时间','邮箱','姓名','国家','平台');	
			exportExcel($list,'网站未发货订单'."-".date('Y-m-d H:i:s',time()),$title);
		}
		elseif(I('get.data')=="plat")
		{
			$data=$order_plat_form_model;
			$sta['status'] = array('in',"audit,accept,shipping");
			$order_platform_id=$order_plat_form_statusDB->field('order_platform_id')->where($sta)->group('order_platform_id')->select();
			foreach($order_platform_id as $k=>$v)
			{
				$id[$k]=$v['order_platform_id'];
			}
			$where['id']=array('in',$id);
			$info = $data->where($where)->select();
			foreach($info as $k=>$v)
			{
				$list[$k]['order_number']=$v['order_number'];
				$list[$k]['date_time']=$v['date_time'];
				$list[$k]['email']=$v['email'];
				$list[$k]['name']=$v['name'];
				$list[$k]['country']=M('order_plat_form_shipping')->where('`order_platform_id` ='.$v['id'])->getField('country');
				$list[$k]['plat']=get_come_from_name($v['come_from_id']);
			}
			$title=array('订单号','下单时间','邮箱','姓名','国家','平台');	
			exportExcel($list,'平台未发货订单'."-".date('Y-m-d H:i:s',time()),$title);
		}
		//dump($info);exit;
	}
	
	//[通用] 导出订单（财务用）
	public function export_order_for_finance($style,$where)
	{
		vendor('PHPExcel.SimplePHPExcel');
		
		if( I('post.check') )
		{
			$order_id_list = I('post.check');
		}
		else //如果没有选择，导出当前筛选的所有订单
		{
			$style=='web' && $order_list = D('order_web')->status( I('get.order_status'),$where )->select();
			$style=='plat' && $order_list = D('order_plat_form')->status( I('get.order_status'),$where )->select();
			foreach($order_list as $key=>$val)
			{
				$order_id_list[] = $val['id'];
			}
		}
		
		$data = array(array('A'=>'单号','B'=>'折前','C'=>'折后','D'=>'交易渠道','E'=>'留言','F'=>'时间','G'=>'运单号','H'=>'姓名','I'=>'邮箱','J'=>'订单状态','K'=>'打单时间','L'=>'地址','M'=>'城市','N'=>'省','O'=>'国家','P'=>'邮编','Q'=>'电话'));
		if( $style=='web' )
		{
			$order_model = D('order_web');
			foreach($order_id_list as $key=>$val)
			{
				$row = array();
				$relation = array('order_web_address','detail_info','order_web_status');
				$order_row = $order_model->relation($relation)->where( array('id'=>$val) )->find();
// 				dump($order_row);exit;
				//order
				$row['A'] = $order_row['order_number'];
				$row['B'] = $order_row['total_price'];
				$row['C'] = $order_row['total_price_discount'];
				$row['D'] = $order_row['payment_style'];
				$row['E'] = $order_row['message'];
				$row['F'] = $order_row['date_time'];
				$row['H'] = $order_row['first_name'].' '.$order_row['last_name'];
				$row['I'] = $order_row['email'];
				//status
				$row['J'] = $order_row['order_web_status'] ? $order_row['order_web_status']['status'] : 'audit';
				//delivery
				$row['G'] = $order_row['detail_info'] ? $order_row['detail_info']['delivery_number'] : '';
				$temp = get_delivery_number($val,'web');
				$row['K'] = $temp['time'];
				//address
				$row['L'] = $order_row['order_web_address'][0]['address'];
				$row['M'] = $order_row['order_web_address'][0]['city'];
				$row['N'] = $order_row['order_web_address'][0]['province'];
				$row['O'] = $order_row['order_web_address'][0]['country'];
				$row['P'] = $order_row['order_web_address'][0]['code'];
				$row['Q'] = $order_row['order_web_address'][0]['telephone'];
				ksort($row);
				$data[] = $row;
			}
		}
		elseif( $style=='plat' )
		{
			$order_model = D('order_plat_form');
			foreach($order_id_list as $key=>$val)
			{
				$row = array();
				$relation = array('shipping_info','detail_info','status_info');
				$order_row = $order_model->relation($relation)->where( array('id'=>$val) )->find();
// 				dump($order_row);exit;
				//order
				$row['A'] = $order_row['order_number'];
				$row['B'] = $order_row['price'];
				$row['C'] = $order_row['price'];
				$row['D'] = '';
				$row['E'] = $order_row['message'];
				$row['F'] = $order_row['date_time'];
				$row['H'] = $order_row['name'];
				$row['I'] = $order_row['email'];
				//status
				$row['J'] = $order_row['status_info'] ? $order_row['status_info']['status'] : 'audit';
				//delivery
				$row['G'] = $order_row['detail_info'] ? $order_row['detail_info']['delivery_number'] : '';
				$temp = get_delivery_number($val,'plat');
				$row['K'] = $temp['time'];
				//address
				$row['L'] = $order_row['shipping_info']['address1'].' '.$order_row['shipping_info']['address2'].' '.$order_row['shipping_info']['address3'];
				$row['M'] = $order_row['shipping_info']['city'];
				$row['N'] = $order_row['shipping_info']['state'];
				$row['O'] = $order_row['shipping_info']['country'];
				$row['P'] = $order_row['shipping_info']['post'];
				$row['Q'] = $order_row['shipping_info']['telephone'];
				ksort($row);
				$data[] = $row;
			}
		}
// 		dump($data);exit;
		export_excel($data,$title='订单');
	}

	
	//[平台] 导出运单
	public function export_order_waybill_plat()
	{
		$order_plat_form_model = D('order_plat_form');  //平台
		$order_web_model = D('order_web');          //网站
		$order_web_addressDB= M('order_web_address');
		$order_delivery_detailDB=M('order_delivery_detail');
		$order_plat_form_statusDB=M('order_plat_form_status');
		$order_delivery_parametersDB = M('order_delivery_parameters');
		if(I('get.style'))          //1 选中  2筛选
		{
			$style=I('get.style');
			if($style == 1)
			{
				$check=I('post.check');
				if($check)
				{
					$id=implode(',',$check);
					$where_box['id']=array('in',$id);
				}
				else
				{
					$this->error('没有获得筛选值');
				}
			}
			elseif($style== 2)
			{
				$type_waybill=I('post.type_waybill');	
				$beginTime = I('post.beginTime');
				$endTime = I('post.endTime');
				$order_id=$order_plat_form_statusDB->field('order_platform_id')->group('order_platform_id')->where('`status` = "history"')->select();
				foreach($order_id as $k=>$v)
				{
					$order_platform_id[$k]=$v['order_platform_id'];	
				}
				if(!$order_platform_id)
				{
					$this->error('没有筛选值');
				}
				$id=implode(',',$order_platform_id);             //所有历史订单ID
				$where['order_platform_id']=array('in',$id);
				if($type_waybill!="0")
				{
					$where['shipping_style']	= $type_waybill;
				}
				if($beginTime !="" && $endTime!="") 
				{
					$where['date_time'] =array(array('gt',strtotime($beginTime)),array('lt',strtotime($endTime)),'and'); ;
				}
				$order_platform_id = $order_delivery_parametersDB->field('order_platform_id')->group('order_platform_id')->where($where)->select();
				if($order_platform_id)
				{	
					foreach($order_platform_id as $k=>$v)
					{
						$box_id[$k]=$v['order_platform_id'];	
					}
					$where_box['id']=array('in',$box_id);
				}
				else
				{
					$this->error('没有筛选值');
				}			
			}
			$info = $order_plat_form_model->relation(true)->where($where_box)->select();
			foreach($info as $k=>$v)
			{
				$list[$k]['order_number'] = $v['order_number'];
				$list[$k]['delivery_number'] = " ".$v['delivery_detail']['delivery_number'];	
				$list[$k]['name'] = $v['name'];	
				$list[$k]['countries'] = $v['shipping_info']['country'];	
				$list[$k]['web'] = $v['come_from'];	
				$list[$k]['weight'] = $v['delivery_info']['weight'];	
				$list[$k]['date_time'] = $v['date_time'];
				$list[$k]['price'] = $v['delivery_info']['price'];
				foreach($v['factory_order'] as $factory_order_k=>$factory_order_v)
				{
					if($factory_order_v["factory_code"] == "rb")
					{
						$F_N="R";
					}
					elseif($factory_order_v["factory_code"]=='hsf')
					{
						$F_N="H";
					}
					elseif($factory_order_v["factory_code"]=="zc")
					{
						$F_N="Z";
					}
					elseif($factory_order_v["factory_code"]==5)
					{
						$F_N="S";
					}
					elseif($factory_order_v["factory_code"]==6)
					{
						$F_N="L";
					}
					elseif($factory_order_v["factory_code"]=='xl')
					{
						$F_N="X";
					}
					elseif($factory_order_v["factory_code"]=="tb")
					{
						$F_N="T";
					}
					
					if($factory_order_v['number'] < 10)
					{
						$num = "0".	$factory_order_v['number'];
					}
					else
					{
						$num = $factory_order_v['number'];
					}
					$date=date('m.d',strtotime($factory_order_v["date"]));
					$list[$k]['factory_order'] .= $F_N.$date."-".$num." ";	
				}
				
				$list[$k]['delivery_time'] = date('Y-m-d H:i:s',$v['delivery_info']['date_time']);	
				$list[$k]['email'] = $v['email'];	
				$list[$k]['telephone'] = $v['telephone'];	
			}
			$title=array('订单号','运单号','姓名','国家','网站','重量','订单日期','价格','工厂单号','打单日期','用户邮箱
','用户电话');	
			exportExcel($list,'平台订单'."-".date('Y-m-d H:i:s',time()),$title);
		}
		else
		{
			$this->error('参数出现错误！！');
		}
	}
	//[网站] 导出运单
	public function export_order_waybill_web()
	{
		$order_web_model = D('order_web');          //网站
		$order_web_addressDB= M('order_web_address');
		$order_delivery_detailDB=M('order_delivery_detail');
		$order_web_statusDB=M('order_web_status');
		$order_delivery_parametersDB = M('order_delivery_parameters');
		if(I('get.style'))          //1 选中  2筛选
		{
			$style=I('get.style');
			if($style == 1)
			{
				$check=I('post.check');
				if($check){
					$id=implode(',',$check);
					$where_box['id']=array('in',$id);
				}
				else
				{
					$this->error('没有获得筛选值');
				}
			}
			elseif($style== 2)
			{
				$type_waybill=I('post.type_waybill');	
				$beginTime = I('post.beginTime');
				$endTime = I('post.endTime');
				$order_id=$order_web_statusDB->field('order_web_id')->where('`status` = "history"')->group('order_web_id')->select();
				foreach($order_id as $k=>$v)
				{
					$order_web_id[$k]=$v['order_web_id'];	
				}
				if(!$order_web_id)
				{
					$this->error('没有筛选值');
				}
				$id=implode(',',$order_web_id);             //所有历史订单ID
				$where['order_id']=array('in',$id);
				if($type_waybill!="0")
				{
					$where['shipping_style']	= $type_waybill;
				}
				if($beginTime !="" && $endTime!="") 
				{
					$where['date_time'] =array(array('gt',strtotime($beginTime)),array('lt',strtotime($endTime)),'and'); 
				}
				$order_id = $order_delivery_parametersDB->field('order_id')->where($where)->group('order_id')->select();
				if($order_id){	
					foreach($order_id as $k=>$v)
					{
						$box_id[$k]=$v['order_id'];	
					}
					$where_box['id']=array('in',$box_id);
				}
				else
				{
					$this->error('没有筛选值');
				}
			}
			$info = $order_web_model->relation(true)->where($where_box)->select();

			foreach($info as $k=>$v)
			{
				$list[$k]['order_number'] = $v['order_number'];
				$list[$k]['delivery_number'] = " ".$v['detail_info']['delivery_number'];	
				$list[$k]['name'] = $v['order_web_address'][0]['first_name']." ".$v['order_web_address'][0]['last_name'];	
				$list[$k]['countries'] = $v['order_web_address'][0]['country'];	
				$list[$k]['web'] = $v['come_from'];	
				$list[$k]['weight'] = $v['delivery_info']['weight'];	
				$list[$k]['date_time'] = $v['date_time'];
				$list[$k]['price'] = $v['delivery_info']['price'];
				foreach($v['factory_order'] as $factory_order_k=>$factory_order_v)
				{
					if($factory_order_v["factory_code"] == "rb")
					{
						$F_N="R";
					}
					elseif($factory_order_v["factory_code"]=='hsf')
					{
						$F_N="H";
					}
					elseif($factory_order_v["factory_code"]=="zc")
					{
						$F_N="Z";
					}
					elseif($factory_order_v["factory_code"]==5)
					{
						$F_N="S";
					}
					elseif($factory_order_v["factory_code"]==6)
					{
						$F_N="L";
					}
					elseif($factory_order_v["factory_code"]=='xl')
					{
						$F_N="X";
					}
					elseif($factory_order_v["factory_code"]=="tb")
					{
						$F_N="T";
					}
					
					if($factory_order_v['number'] < 10)
					{
						$num = "0".	$factory_order_v['number'];
					}
					else
					{
						$num = $factory_order_v['number'];
					}
					$date=date('m.d',strtotime($factory_order_v["date"]));
					$list[$k]['factory_order'] .= $F_N.$date."-".$num." ";	
				}
				
				$list[$k]['delivery_time'] = date('Y-m-d H:i:s',$v['delivery_info']['date_time']);	
				$list[$k]['email'] = $v['email'];	
				$list[$k]['telephone'] = $v['order_web_address'][0]['telephone'];	
			}
			$title=array('订单号','运单号','姓名','国家','网站','重量','订单日期','价格','工厂单号','打单日期','用户邮箱
','用户电话');	
			exportExcel($list,'网站订单'."-".date('Y-m-d H:i:s',time()),$title);
		}
		else
		{
			$this->error('参数出现错误！！');
		}
	}
	
	//[平台] 导入订单地址
	public function order_address_import() {
		if(IS_POST){
			$rootPath = './Public/excel_upload/order_address/';
			if(!file_exists($rootPath)) mkdir($rootPath);
			$upload = new \Think\Upload();// 实例化上传类
			$upload->maxSize   =     0 ;// 设置附件上传大小
			$upload->exts      =     array('jpg', 'gif', 'png', 'jpeg','xls');// 设置附件上传类型
			$upload->rootPath  =     $rootPath; // 设置附件上传根目录
			$upload->savePath  =     ''; // 设置附件上传（子）目录
			$upload->autoSub   =     false;//设置文件子目录名
			// 上传文件
			$info   =   $upload->upload();
			if(!$info) {// 上传错误提示错误信息
				$this->error($upload->getError());
			}
			else
			{// 上传成功
				vendor('PHPExcel.SimplePHPExcel');
				$func_check_country = function($value)
				{
					if( strlen($value)!=2 ){
						$msg='格式不符';
						$state = 'error';
					}
					return array('data'=>$value,'msg'=>$msg,'state'=>$state);
				};
				if(I('post.style')=='web'){
					$field = array(
							//'列'=>array('字段','回调函数'),
							'A'=>array('id'),
							//'B'=>array('order_number'),
							'C'=>array('first_name', 'simple_check_empty'),
							'D'=>array('last_name', 'simple_check_empty'),
							'E'=>array('country', 'simple_check_empty'),
							'F'=>array('province', 'simple_check_empty'),
							'G'=>array('city', 'simple_check_empty'),
							'H'=>array('address', 'simple_check_empty'),
							'I'=>array('code', 'simple_check_empty'),
							'J'=>array('telephone', 'simple_check_num'),
					);
					$option=array(
							'uploadfile'=> $rootPath.$info['file']['savename'], //必须。该文件名不能含中文
							'table'		=> 'order_web_address', //必须
							'field'		=> $field, //单表需要(option无配置callback)
// 							'mode'		=> 'add', //添加模式
							'mode'		=> 'edit',
							'primary'	=> 'A',//修改模式，需要设置主键
							"start_row"	=> 2,//可选。设置有效内容的 起始行数，默认2
							"unlink"	=> false,//可选。是否删除文件，默认false
							//     		"callback"	=> $callback,//用于插入多表的情况
					);
				}
				elseif(I("post.style")=='plat')
				{
					$field = array(
							//'列'=>array('字段','回调函数'),
							'A'=>array('id'),
							'B'=>array('name', 'simple_check_empty'),
							'C'=>array('country', 'simple_check_empty'),
							'D'=>array('state', 'simple_check_empty'),
							'E'=>array('city', 'simple_check_empty'),
							'F'=>array('address1'),
							'G'=>array('address2'),
							'H'=>array('address3'),
							'I'=>array('post', 'simple_check_empty'),
							'J'=>array('telephone', 'simple_check_num'),
					);
					$option=array(
							'uploadfile'=> $rootPath.$info['file']['savename'], //必须。该文件名不能含中文
							'table'		=> 'order_plat_form_shipping', //必须
							'field'		=> $field, //单表需要(option无配置callback)
							// 							'mode'		=> 'add', //添加模式
							'mode'		=> 'edit',
							'primary'	=> 'A',//修改模式，需要设置主键
							"start_row"	=> 2,//可选。设置有效内容的 起始行数，默认2
							"unlink"	=> false,//可选。是否删除文件，默认false
							//     		"callback"	=> $callback,//用于插入多表的情况
					);
				}
			
				$log = importExcel($option);
				if($log){
					$this->success("更新成功!");
				}else{
					//处理显示log
					$column_list = array('A','B','C','D','E','F','G','H','I','J','state');
					$this->column_list = $column_list;
					$this->log_list = $log;
					//通过日志分析
					$log_html = $this->fetch('Public:log_simple_excel');
					$this->log_html = $log_html;
					$this->display();
				}	
			}	
		}else{
			$this->display();
		}
		
	}
	
	//自动同步平台订单
	public function auto_sync_platform_order()
	{
		//定时执行
		
		//亚马逊
		//ebay
		//乐天
		//速卖通
	}
	//Amazon API
	public function amazon_api($come_from_id)
	{
		set_time_limit(0);
// 		$come_from = 'Amazon.com';
// 		$update_time = strtotime('2016-05-10 00:00:00');
		$come_from = get_come_from_name($come_from_id);
		$update_time_model = M('order_plat_form_update_time');
		$update_time = $update_time_model->where( array('come_from_id'=>$come_from_id) )->order('time desc')->find();
		if($update_time)
		{
			$update_time = $update_time['time'];//格式为时间戳
		}
		else 
		{
			$update_time = strtotime('2016-05-01 00:00:00');//如果第一次更新，设置起点为2016-05-01
		}
		$translate = array(
			//north america
			'Amazon.ca'=>'A2EUQ1WTGCTBG2',
			'Amazon.com'=>'ATVPDKIKX0DER',
			'Amazon.com.mx'=>'A1AM78C64UM0Y8',
			//europe
			'Amazon.co.uk'=>'A1F83G8C2ARO7P',
			'Amazon.de'=>'A1PA6795UKMFR9',
			'Amazon.es'=>'A1RKKUPIHCS9HS',
			'Amazon.fr'=>'A13V1IB3VIYZZH',
			'Amazon.it'=>'APJ6JRA9NG5V4',
			//japan
			'Amazon.co.jp'=>'A1VC38T7YXB528',
		);
		$operator = session('username');
		
		$param = "?MarketplaceId={$translate[$come_from]}&CreatedAfter=$update_time";
		$return_data = json_decode(httpPost("https://www.lilysilk.com/singapore/mono_admin/MarketplaceWebServiceOrders/Samples/ListOrdersSample.php",$param),true);
// 		dump($return_data);exit;//tag
		$order_list = $return_data['data'];//订单数据
		foreach ($order_list as $key=>$val)
		{
			//操作人
			$order_list[$key]['order']['operator'] = $operator;
			//转换时间格式
			$temp = $val['order']['date_time'];
			$temp = strtotime($temp);
			$temp = date('Y-m-d H:i:s',$temp);
			$order_list[$key]['order']['date_time'] = $temp;
			//获取come_from_id
			$temp = $val['order']['come_from'];
			$actual_come_from_id = get_come_from_id($temp);
			if( !$actual_come_from_id )
			{
				return array('msg'=>'平台不存在，订单未同步！');//致命错误，取消同步行为
			}
			$order_list[$key]['order']['come_from_id'] = $come_from_id;
			unset($order_list[$key]['order']['come_from']);
		}
		//记录更新时间	注意：暂时取更新的最后一条订单的下单时间
// 		if( in_array($return_data['status'], array('limit','error')) )//这两种返回状态下，当前时间之前 仍有订单未更新
// 		{
			$update_time = $order_list[sizeof($order_list)-1]['order']['date_time'];
			$update_time_timestamp = strtotime($update_time);//已考虑时区因素
// 		}
// 		else 
// 		{
// 			$update_time_timestamp = time();
// 			$update_time = Date('Y-m-d H:i:s',$update_time_timestamp);
// 		}
		$update_time_model = M('order_plat_form_update_time');
		$update_time_data = array('come_from_id'=>$come_from_id,'time'=>$update_time_timestamp);
		$update_time_model->add($update_time_data);
		//插入数据表，返回错误日志
		$log = make_platform_order($order_list);
		$fail_num = count($log);
		if($log)
		{
			$error_log_model = M('order_plat_form_update_error_log');
			$error_log_data = array(
				'come_from_id'	=> $actual_come_from_id,
				'time'			=> $update_time_timestamp,
				'status'		=> '未处理',
			);
			foreach ($log as $key=>$val)
			{
				$error_log_data['order_number'] = $key;
				$error_log_data['msg'] = $val;
				$error_log_model->add($error_log_data);
				if( $val=='订单已存在' )
				{
					$fail_num--;
				}
			}
		}
		//return
		return array(
			'msg' => $return_data['msg'],
			'update_time' => $update_time,
			'success_num' => count($order_list)-count($log),
			'fail_num' => $fail_num,
		);
	}
	//速卖通 API
	public function smt_api($id)
	{
		$order_plat_formDB=M('order_plat_form');
		$order_plat_form_productDB=M('order_plat_form_product');
		$order_plat_form_shippingDB=M('order_plat_form_shipping');
		$order_plat_form_statusDB=M('order_plat_form_status');
		$id_product_skuDB = M('id_product_sku');
		$id_relate_skuDB = M('id_relate_sku');
		$id_product_codeDB = M('id_product_code');
		$order_plat_form_update_timeDB = M('order_plat_form_update_time');
		$num_yes=0;  //添加成功
		$num_no = 0;//添加失败
		if($id == '17')	
		{
			$update_time = $order_plat_form_update_timeDB->where('`come_from_id` = 17')->order('time desc')->getField('time');	
			$appKey = C('Smt_appKey');
			$appSecret =C('Smt_appSecret');
			$appRefreshToken = C('Smt_refreshToken');
			
			$acceaa_url = "https://gw.api.alibaba.com/openapi/param2/1/system.oauth2/getToken/". $appKey;
			$acceaa_url_get = array(
				'grant_type'=>'refresh_token',
				'client_id'=> $appKey ,
				'client_secret'=> $appSecret,
				'refresh_token'=> $appRefreshToken
			);
		}
		elseif($id == '103')	
		{
			$update_time = $order_plat_form_update_timeDB->where('`come_from_id` = 103')->order('time desc')->getField('time');	
			$appKey = C('Smt_pajamas_appKey');
			$appSecret =C('Smt_pajamas_appSecret');
			$appRefreshToken = C('Smt_pajamas_refreshToken');
			
			$acceaa_url = "https://gw.api.alibaba.com/openapi/param2/1/system.oauth2/getToken/". $appKey;
			$acceaa_url_get = array(
				'grant_type'=>'refresh_token',
				'client_id'=> $appKey ,
				'client_secret'=> $appSecret,
				'refresh_token'=> $appRefreshToken
			);
		}
		$acceaa_ch = curl_init();
		curl_setopt($acceaa_ch, CURLOPT_SSL_VERIFYPEER, 0);
		curl_setopt($acceaa_ch, CURLOPT_URL,$acceaa_url);
		curl_setopt($acceaa_ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($acceaa_ch, CURLOPT_TIMEOUT, 60);
		curl_setopt($acceaa_ch, CURLOPT_FOLLOWLOCATION, 1);
		curl_setopt($acceaa_ch, CURLOPT_SSL_VERIFYHOST, false);
		curl_setopt($acceaa_ch, CURLOPT_HTTPHEADER, array('Expect:')); 
		curl_setopt($acceaa_ch, CURLOPT_POSTFIELDS, $acceaa_url_get); 							//GET要传的值   
		$acceaa_output = curl_exec($acceaa_ch);
		$acceaa_list=json_decode($acceaa_output,true);           //json转换数组
		$access_token = $acceaa_list['access_token'];				//获得签名
		if(!$access_token)
		{
			echo 'access_token 没有获得到';exit;
		}
		if($update_time)
		{
			$update_time = $update_time - 60*60*24*3;	
		}
		else
		{
			$update_time = '1468166400';	
		}
		$Start_time = date('m/d/Y H:i:s',$update_time);
		$time=time();
		
		$End_time = date('m/d/Y H:i:s',time());
		$end_time =time();
		
		$url = 'http://gw.api.alibaba.com/openapi';//1688开放平台使用gw.open.1688.com域名
		
		//计算签名
		$apiInfo = 'param2/1/aliexpress.open/api.findOrderListQuery/' . $appKey;//此处请用具体api进行替换
		//配置参数，请用apiInfo对应的api参数进行替换
		$code_arr = array(
			'page'=>'1',
			'pageSize'=>'50',
			'access_token'=>$access_token,
			'orderStatus'=>'WAIT_SELLER_SEND_GOODS'
		);
		$aliParams = array();
		foreach ($code_arr as $key => $val) {
			$aliParams[] = $key . $val;
		}
		sort($aliParams);
		$sign_str = join('', $aliParams);
		$sign_str = $apiInfo . $sign_str;
		$code_sign = strtoupper(bin2hex(hash_hmac("sha1", $sign_str, $appSecret, true)));
		//计算签名end
		$url_list ="http://gw.api.alibaba.com/openapi/param2/1/aliexpress.open/api.findOrderListQuery/".$appKey;
		$code_arr01 = array(
			'page'=>'1',
			'pageSize'=>'50',
			'access_token'=>$access_token,
			'_aop_signature'=>$code_sign,
			'orderStatus'=>'WAIT_SELLER_SEND_GOODS'
		);
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
		curl_setopt($ch, CURLOPT_URL,$url_list);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_TIMEOUT, 60);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
		curl_setopt($ch, CURLOPT_HTTPHEADER, array('Expect:')); 
		curl_setopt($ch, CURLOPT_POSTFIELDS, $code_arr01); 							//GET要传的值   
		$output = curl_exec($ch);
		$list=json_decode($output,true);           //json转换数组
		$num = $list['totalItem'];
		$order_list = $list['orderList'];
	//	dump($order_list);exit;
		if($num =='' || $num == 0)
		{
			$date['come_from_id'] =$id;
			$date['time'] = $end_time;
			$update_time = $order_plat_form_update_timeDB->add($date);           //插入脚印
			return array('msg'=>'没有要更新的数据');		
		}
		else
		{
			if($num/50 >1)
			{
				$all[0]=$this->smt_orderList($order_list,$update_time,$end_time);	
				for($i=1;$i<floor($num/50);$i++)
				{
					$all[$i]=$this->product_add_smt($i+1,50,$access_token,$update_time,$end_time,$id);		
				}
				$num_size=$num/50 - floor($num/50);
				$all[$i+1]=$this->product_add_smt($i+2,50,$access_token,$update_time,$end_time,$id);	
				$date['come_from_id'] =$id;
				$date['time'] = $end_time;
				$update_time = $order_plat_form_update_timeDB->add($date);           //插入脚印
				$all['update_time'] = date('Y-m-d H:i:s',$end_time);
				$all['msg'] = '更新成功';
				return $all;
			}
			else
			{
				$all=$this->smt_orderList($order_list,$access_token,$update_time,$end_time,$id);
				$date['come_from_id'] =$id;
				$date['time'] = $end_time;
				$update_time = $order_plat_form_update_timeDB->add($date);           //插入脚印
				$all['update_time'] = date('Y-m-d H:i:s',$end_time);
				$all['msg'] = '更新成功';
				return $all;				
			}
		}
		
	}
	//上传第一页
	public function smt_orderList($val,$access_token,$Start_time,$End_time,$id)
	{
		$num=0;
		
		if($id == '17')	
		{	
			$appKey = C('Smt_appKey');
			$appSecret =C('Smt_appSecret');
		}
		elseif($id == '103')	
		{
			$appKey = C('Smt_pajamas_appKey');
			$appSecret =C('Smt_pajamas_appSecret');
		}
		foreach($val as $k=>$v)
		{//date("Y-m-d H:i:s",strtotime(substr(strstr($v['gmtPayTime'], '-', TRUE),0,-3)))
			$gmtPayTime = strtotime(substr(strstr($v['gmtPayTime'], '-', TRUE),0,-3));
			if($gmtPayTime > $Start_time && $gmtPayTime < $End_time)
			{
			//获得订单详情	
				//计算签名
					$apiInfo_order = 'param2/1/aliexpress.open/api.findOrderById/' . $appKey;//此处请用具体api进行替换
					//配置参数，请用apiInfo对应的api参数进行替换
					$code_arr_order = array(
						'access_token'=>$access_token,
						'orderId'=>$v['orderId']
					);
					$aliParams_order = array();
					foreach ($code_arr_order as $key => $val) {
						$aliParams_order[] = $key . $val;
					}
					sort($aliParams_order);
					$sign_str_order = join('', $aliParams_order);
					$sign_str_order = $apiInfo_order . $sign_str_order;
					$code_sign_order = strtoupper(bin2hex(hash_hmac("sha1", $sign_str_order, $appSecret, true)));
				//计算签名end
				$url_order = "http://gw.api.alibaba.com/openapi/param2/1/aliexpress.open/api.findOrderById/".$appKey."?orderId=".$v['orderId']."&access_token=".$access_token."&_aop_signature=".$code_sign_order;			
				
				$val_order = curl_init();
				curl_setopt($val_order, CURLOPT_SSL_VERIFYPEER, 0);
				curl_setopt($val_order, CURLOPT_URL, $url_order);
				curl_setopt($val_order, CURLOPT_RETURNTRANSFER, 1);
				curl_setopt($val_order, CURLOPT_TIMEOUT, 60);
				//curl_setopt($ch, CURLOPT_POSTFIELDS, $XML);
				curl_setopt($val_order, CURLOPT_HTTPHEADER, array('Content-Type:application/xml;charset=UTF-8'));
				curl_setopt($val_order, CURLOPT_FOLLOWLOCATION, 1);
				curl_setopt($val_order, CURLOPT_SSL_VERIFYHOST, false);
				$val_order_output= curl_exec($val_order);
				$list_order=json_decode($val_order_output,true);           //json转换数组
			//获得订单详情	end		
				//买家信息
				if($list_order['receiptAddress']['mobileNo']!="")
				{
					$phone=$list_order['receiptAddress']['phoneArea']."-".$list_order['receiptAddress']['mobileNo'] .", ";
				}
				$date[$num]['order']['telephone']=$phone.$list_order['receiptAddress']['phoneArea']."-".$list_order['receiptAddress']['phoneNumber'];																		//电话
				$date[$num]['order']['name'] =PD_null($list_order['receiptAddress']['contactPerson']);//收件人
				
				$date[$num]['order']['price'] = $v['payAmount']['amount'];					//订单金额
				$date[$num]['order']['currency'] = $v['payAmount']['currencyCode'];			//币种
				$date[$num]['order']['come_from_id'] = $id;
	
				$date[$num]['order']['date_time'] = date("Y-m-d H:i:s",strtotime(substr(strstr($v['gmtPayTime'], '-', TRUE),0,-3)));						//付款时间  date转换 datetime
				$date[$num]['order']['order_number'] = $v['orderId'];						//订单号
				$massage ='';
				foreach($list_order['orderMsgList'] as $orderMsgList_k=>$orderMsgList_v)
				{
					$massage .="(".$orderMsgList_k.") : ".$orderMsgList_v['content']." - ".$orderMsgList_v['gmtCreate'];	
				}
				$date[$num]['order']['message'] =$massage;			//用户留言			
				foreach($v['productList'] as $k2=>$v2)
				{
					$date[$num]['product'][$k2]['sku'] = $v2['skuCode'];						//商品sku
					$date[$num]['product'][$k2]['number'] = $v2['productCount'];
					$date[$num]['product'][$k2]['name'] = $v2['productName'];					//商品名称
					$date[$num]['product'][$k2]['price'] = $v2['productUnitPrice']['amount'];	//商品金额
				}
				
				$date[$num]['shipping']['name']					=PD_null($list_order['receiptAddress']['contactPerson']);//收件人
				$date[$num]['shipping']['country'	]			=PD_null($list_order['receiptAddress']['country']);//国家
				$date[$num]['shipping']['state']				='';
				$date[$num]['shipping']['city']					=PD_null($list_order['receiptAddress']['city']);		//城市
				$date[$num]['shipping']['address1']				=PD_null($list_order['receiptAddress']['detailAddress']);		//详细第地址
				$date[$num]['shipping']['address2']				=PD_null($list_order['receiptAddress']['address2']);     //
				$date[$num]['shipping']['address3']				='';
				$date[$num]['shipping']['post']					=PD_null($list_order['receiptAddress']['zip']);          //邮编
				$date[$num]['shipping']['telephone']			=PD_null($date[$k]['order']['telephone']);               //电话
				$date[$num]['shipping']['shipping_style']		=PD_null($v['logisticsType']);//
				$date[$num]['shipping']['shipping_number']		='';//
				$date[$num]['shipping']['shipping_weight']		='';//
				$date[$num]['shipping']['shipping_price']		='';//
				$date[$num]['shipping']['shipping_tax']			='';//
				$date[$num]['shipping']['shipping_date']		='';//
				$date[$num]['shipping']['shipping_operator']	='';//
				$date[$num]['shipping']['shipping_hs']			='';//
				$date[$num]['shipping']['shipping_sample']		='';//
				$date[$num]['shipping']['shipping_report_price']='';//
				$date[$num]['status']='Unshipped';      //订单状态
				$num++;
			}
		}
		//$result=make_platform_order($date);
		$result=make_platform_order($date);
		//dump($result);exit;
		//插入数据表，返回错误日志
		$repeat_num ==0;  //重复个数
		if($result)
		{
			$error_log_model = M('order_plat_form_update_error_log');
			$error_log_data = array(
				'come_from_id'	=> $id,
				'time'			=> $End_time,
				'status'		=> '未处理',
			);
			foreach ($result as $key=>$val)
			{
				$error_log_data['order_number'] = $key;
				$error_log_data['msg'] = $val;
				$error_log_model->add($error_log_data);
				if($val =='订单已存在')
				{
					$repeat_num ++;	
				}
			}
		 }
		//插入数据表，返回错误日志 end
		$return['fail_num']= count($result) - $repeat_num;
		$return['success_num']= count($date)-count($result);
		
		return $return;
	}
	//上传第一页以后
	public function product_add_smt($page,$pageSize,$access_token,$Start_time,$End_time,$id)
	{
		$url = 'http://gw.api.alibaba.com/openapi';//1688开放平台使用gw.open.1688.com域名
		if($id == '17')	
		{	
			$appKey = C('Smt_appKey');
			$appSecret =C('Smt_appSecret');
		}
		elseif($id == '103')	
		{
			$appKey = C('Smt_pajamas_appKey');
			$appSecret =C('Smt_pajamas_appSecret');
		}
		//计算签名
		$apiInfo = 'param2/1/aliexpress.open/api.findOrderListQuery/' . $appKey;//此处请用具体api进行替换
		//配置参数，请用apiInfo对应的api参数进行替换
		$code_arr = array(
			'page'=>$page,
			'pageSize'=>$pageSize,
			'access_token'=>$access_token,
			'orderStatus'=>'WAIT_SELLER_SEND_GOODS'
		);
		$aliParams = array();
		foreach ($code_arr as $key => $val) {
			$aliParams[] = $key . $val;
		}
		sort($aliParams);
		$sign_str = join('', $aliParams);
		$sign_str = $apiInfo . $sign_str;
		$code_sign = strtoupper(bin2hex(hash_hmac("sha1", $sign_str, $appSecret, true)));
		//计算签名end
		$url_list ="http://gw.api.alibaba.com/openapi/param2/1/aliexpress.open/api.findOrderListQuery/".$appKey;
		
		$code_arr01 = array(
			'page'=>$page,
			'pageSize'=>$pageSize,
			'access_token'=>$access_token,
			'_aop_signature'=>$code_sign,
			'orderStatus'=>'WAIT_SELLER_SEND_GOODS'
		);
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
		curl_setopt($ch, CURLOPT_URL,$url_list);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_TIMEOUT, 60);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
		curl_setopt($ch, CURLOPT_HTTPHEADER, array('Expect:')); 
		curl_setopt($ch, CURLOPT_POSTFIELDS, $code_arr01); 							//GET要传的值   
		$output = curl_exec($ch);
		$list=json_decode($output,true);           //json转换数组
		if($list['error_code'])
		{
			$this->error('信息已过期！！');
			
		}
		else
		{	$num=0;
			foreach($val as $k=>$v)
			{//date("Y-m-d H:i:s",strtotime(substr(strstr($v['gmtPayTime'], '-', TRUE),0,-3)))
				$gmtPayTime = strtotime(substr(strstr($v['gmtPayTime'], '-', TRUE),0,-3));
				if($gmtPayTime > $Start_time && $gmtPayTime < $End_time)
				{
				//获得订单详情	
					//计算签名
						$apiInfo_order = 'param2/1/aliexpress.open/api.findOrderById/' . $appKey;//此处请用具体api进行替换
						//配置参数，请用apiInfo对应的api参数进行替换
						$code_arr_order = array(
							'access_token'=>$access_token,
							'orderId'=>$v['orderId']
						);
						$aliParams_order = array();
						foreach ($code_arr_order as $key => $val) {
							$aliParams_order[] = $key . $val;
						}
						sort($aliParams_order);
						$sign_str_order = join('', $aliParams_order);
						$sign_str_order = $apiInfo_order . $sign_str_order;
						$code_sign_order = strtoupper(bin2hex(hash_hmac("sha1", $sign_str_order, $appSecret, true)));
					//计算签名end
					$url_order = "http://gw.api.alibaba.com/openapi/param2/1/aliexpress.open/api.findOrderById/".$appKey."?orderId=".$v['orderId']."&access_token=".$access_token."&_aop_signature=".$code_sign_order;			
					
					$val_order = curl_init();
					curl_setopt($val_order, CURLOPT_SSL_VERIFYPEER, 0);
					curl_setopt($val_order, CURLOPT_URL, $url_order);
					curl_setopt($val_order, CURLOPT_RETURNTRANSFER, 1);
					curl_setopt($val_order, CURLOPT_TIMEOUT, 60);
					curl_setopt($val_order, CURLOPT_HTTPHEADER, array('Content-Type:application/xml;charset=UTF-8'));
					curl_setopt($val_order, CURLOPT_FOLLOWLOCATION, 1);
					curl_setopt($val_order, CURLOPT_SSL_VERIFYHOST, false);
					$val_order_output= curl_exec($val_order);
					$list_order=json_decode($val_order_output,true);           //json转换数组
				//获得订单详情	end		
					
					//买家信息
					if($list_order['receiptAddress']['mobileNo']!=""){
						$phone=$list_order['receiptAddress']['phoneArea']."-".$list_order['receiptAddress']['mobileNo'] .", ";
					}
					$date[$num]['order']['telephone']=$phone.$list_order['receiptAddress']['phoneArea']."-".$list_order['receiptAddress']['phoneNumber'];																		//电话
					$date[$num]['order']['name'] =$list_order['receiptAddress']['contactPerson'];//收件人
					
					$date[$num]['order']['price'] = $v['payAmount']['amount'];					//订单金额
					$date[$num]['order']['currency'] = $v['payAmount']['currencyCode'];			//币种
					$date[$num]['order']['come_from_id'] = $id;
		
					$date[$num]['order']['date_time'] = date("Y-m-d H:i:s",strtotime(substr(strstr($v['gmtPayTime'], '-', TRUE),0,-3)));						//付款时间  date转换 datetime
					$date[$num]['order']['order_number'] = $v['orderId'];						//订单号
					$massage ='';
					foreach($list_order['orderMsgList'] as $orderMsgList_k=>$orderMsgList_v)
					{
						$massage .="(".$orderMsgList_k.") : ".$orderMsgList_v['content']." - ".$orderMsgList_v['gmtCreate'];	
					}
					$date[$num]['order']['message'] =$massage;			//用户留言			
					foreach($v['productList'] as $k2=>$v2)
					{
						$date[$num]['productList'][$k2]['sku'] = $v2['skuCode'];						//商品sku
						$date[$num]['productList'][$k2]['number'] = $v2['productCount'];
						$date[$num]['productList'][$k2]['name'] = $v2['productName'];					//商品名称
						$date[$num]['productList'][$k2]['price'] = $v2['productUnitPrice']['amount'];	//商品金额
					}
					
					$date[$num]['shipping']['name']					=$list_order['receiptAddress']['contactPerson'];//收件人
					$date[$num]['shipping']['country'	]			=$list_order['receiptAddress']['country'];//国家
					$date[$num]['shipping']['state']				='';
					$date[$num]['shipping']['city']					=PD_null($list_order['receiptAddress']['detailAddress']);//详细地址
					$date[$num]['shipping']['address1']				=PD_null($list_order['receiptAddress']['address']);		//
					$date[$num]['shipping']['address2']				=PD_null($list_order['receiptAddress']['address2']);     //
					$date[$num]['shipping']['address3']				='';
					$date[$num]['shipping']['post']					=PD_null($list_order['receiptAddress']['zip']);          //邮编
					$date[$num]['shipping']['telephone']			=PD_null($date[$k]['order']['telephone']);               //电话
					$date[$num]['shipping']['shipping_style']		=PD_null($v['logisticsType']);//
					$date[$num]['shipping']['shipping_number']		='';//
					$date[$num]['shipping']['shipping_weight']		='';//
					$date[$num]['shipping']['shipping_price']		='';//
					$date[$num]['shipping']['shipping_tax']			='';//
					$date[$num]['shipping']['shipping_date']		='';//
					$date[$num]['shipping']['shipping_operator']	='';//
					$date[$num]['shipping']['shipping_hs']			='';//
					$date[$num]['shipping']['shipping_sample']		='';//
					$date[$num]['shipping']['shipping_report_price']='';//
					$num++;
				}
			}
		}
		$result=make_platform_order($date);
		$repeat_num =0;
		//插入数据表，返回错误日志
		if($result)
		{
			$error_log_model = M('order_plat_form_update_error_log');
			$error_log_data = array(
				'come_from_id'	=> $id,
				'time'			=> $End_time,
				'status'		=> '未处理',
			);
			foreach ($result as $key=>$val)
			{
				$error_log_data['order_number'] = $key;
				$error_log_data['msg'] = $val;
				$error_log_model->add($error_log_data);
				if($val =='订单已存在')
				{
					$repeat_num ++;	
				}
			}
		 }
		//插入数据表，返回错误日志 end
		$return['fail_num']= count($result) - $repeat_num;
		$return['success_num']= count($date)-count($result);
		return $return;
	}	
	//ebay API
	public function ebay_api($come_from_id)
	{
		$id_come_from = D("id_come_from");
		$order_plat_form_update_time = D("order_plat_form_update_time");
	
		if($come_from_id==19)
		{
			$order_href = "?platform=ebay.us";
			// 				$platform = "ebay.us";
			// 				$come_from_id = $id_come_from->where(array('name'=>$platform))->getField('id');
			$order_plat_form_time = $order_plat_form_update_time->where(array('come_from_id'=>$come_from_id))->order('time desc')->limit('time 0,1')->getField('time');
			$now_time = time();
			$CreateTimeFrom = gmdate("Y-m-d\TH:i:s",$order_plat_form_time).".000Z"; //获取最近的时间
	
			$CreateTimeTo = gmdate("Y-m-d\TH:i:s",$now_time).".000Z";
			$data['come_from_id'] = $come_from_id;
			$data['time'] = $now_time;
			$order_plat_form_update_time_insert = $order_plat_form_update_time->add($data);
				
		}
		else if($come_from_id==18)
		{
			$order_href = "?platform=ebay.uk";
			//	$come_from_id = $id_come_from->where(array('name'=>$_GET['platform']))->getField('id');
			$order_plat_form_time = $order_plat_form_update_time->where(array('come_from_id'=>$come_from_id))->order('time desc')->limit('time 0,1')->getField('time');
			$now_time = time();
			$CreateTimeFrom = gmdate("Y-m-d\TH:i:s",$order_plat_form_time).".000Z"; //获取最近的时间
			$CreateTimeTo = gmdate("Y-m-d\TH:i:s",$now_time).".000Z";
			$data['come_from_id'] = $come_from_id;
			$data['time'] = $now_time;
			$order_plat_form_update_time_insert = $order_plat_form_update_time->add($data);
		}
		$order_href.= "&time_from=".$CreateTimeFrom;
		$order_href.= "&time_to=".$CreateTimeTo;
		$order_list = json_decode(httpPost("http://www.lilysilk.com/mono_admin/ebay_api/GetOrders.php",$order_href),true);
			
		//print_r($order_list);
		//print_r($response);
		//Xml string is parsed and creates a DOM Document object
		$responseDoc = new \DomDocument();
		//$responseDoc = new DomDocument();
		$responseDoc->loadXML($order_list);
		//echo ($responseXml);
		//get any error nodes
		$response = simplexml_import_dom($responseDoc);
		$entries = $response->PaginationResult->TotalNumberOfEntries;
		if($entries == 0) {
			return array("msg"=>"这段时间没有订单！");
		}
		else
		{
			header("Content-Type: text/plain; charset=UTF-8");
			$orders = $response->OrderArray->Order;
			//print_r($orders);
			$data = array();
			$key = 0;
			if($orders != null)
			{
				foreach ($orders as $order)
				{
					//订单详情
					$data[$key]['order']['order_number'] = $order->OrderID . "\n";
					$data[$key]['order']['is_gift_package'] = '';
					$data[$key]['order']['come_from_id'] = $come_from_id;
					$data[$key]['order']['operator'] = session('username');
						
					$date_time = $order->PaidTime . "\n";
					$date_time = str_replace("T"," ",$date_time);
					$date_time = substr($date_time,0,-6);
					$data[$key]['order']['date_time'] = $date_time;
					$orderStatus = $order->OrderStatus;
					$data[$key]['order']['ship_service_level'] = '';
					if($orderStatus && $orderStatus=='Completed')
					{
							
						$data[$key]['status'] = 'Completed';
						$ShippingDetails = $order->ShippingDetails;
						$data[$key]['order']['ebay_order_id'] = $ShippingDetails->SellingManagerSalesRecordNumber . "\n";
						// get the amount paid
						$AmountPaid = $order->AmountPaid;
						$AmountPaidAttr = $AmountPaid->attributes();
						$data[$key]['order']['currency'] = $AmountPaidAttr["currencyID"]. "\n";
	
						// get the payment method
						if($order->PaymentMethod)
							$data[$key]['shipping']['shipping_style'] = $order->PaymentMethod . "\n";
							
							
						// get the checkout message left by the buyer, if any
						if($order->BuyerCheckoutMessage)
						{
							$data[$key]['order']['message'] = $order->BuyerCheckoutMessage . "\n";
						}
							
						// get the sales tax, if any
						$SalesTaxAmount = $order->ShippingDetails->SalesTax->SalesTaxAmount;
						$SalesTaxAmountAttr = $SalesTaxAmount->attributes();
							
						// get the buyer's shipping address
						$shippingAddress = $order->ShippingAddress;
							
						$data[$key]['shipping']['name'] = $shippingAddress->Name . "\n";
						if ($shippingAddress->Street1 != null)
						{
							$data[$key]['shipping']['address1'] = $shippingAddress->Street1 . "\n";
						}
						if ($shippingAddress->Street2 != null) {
							$data[$key]['shipping']['address2'] = $shippingAddress->Street2 . "\n";
						}
						$data[$key]['shipping']['address3'] = '';
						if ($shippingAddress->CityName != null) {
							$data[$key]['shipping']['city'] = $shippingAddress->CityName . "\n";
						}
						if ($shippingAddress->StateOrProvince != null) {
							$data[$key]['shipping']['state'] = $shippingAddress->StateOrProvince . "\n";
						}
						if ($shippingAddress->PostalCode != null) {
							$data[$key]['shipping']['post'] = $shippingAddress->PostalCode . "\n";
						}
						if ($shippingAddress->CountryName != null) {
							$data[$key]['shipping']['country'] = $shippingAddress->CountryName . "\n";
						}
						if ($shippingAddress->Phone != null) {
							$data[$key]['order']['telephone'] = $shippingAddress->Phone . "\n";
							$data[$key]['shipping']['telephone'] = $shippingAddress->Phone . "\n";
								
						}
						$data[$key]['shipping']['shipping_style'] = '';
						$data[$key]['shipping']['shipping_number'] = '';
						$data[$key]['shipping']['shipping_weight'] = '';
						$data[$key]['shipping']['shipping_price'] = '';
						$data[$key]['shipping']['shipping_tax'] = '';
						$data[$key]['shipping']['shipping_date'] = '';
						$data[$key]['shipping']['shipping_operator'] = '';
						$data[$key]['shipping']['shipping_hs'] = '';
						$data[$key]['shipping']['shipping_sample'] = '';
						$data[$key]['shipping']['shipping_report_price'] = '';
						$transactions = $order->TransactionArray;
						if($transactions)
						{
							$transaction_key = 0;
							foreach($transactions->Transaction as $transaction)
							{
								// get the OrderLineItemID, Quantity, buyer's email and SKU
								$data[$key]['order']['email'] = $transaction->Buyer->Email . "\n";
								$data[$key]['order']['name'] = $transaction->Buyer->UserFirstName.$transaction->Buyer->UserLastName . "\n";
								$data[$key]['product'][$transaction_key]['sku'] = $transaction->Item->SKU ."\n";
								$data[$key]['product'][$transaction_key]['name'] = $transaction->Item->Title ."\n";
								$data[$key]['product'][$transaction_key]['price'] = $transaction->TransactionPrice ."\n";
								$data[$key]['order']['price'] = $transaction->TransactionPrice ."\n";
								$data[$key]['product'][$transaction_key]['number'] = $transaction->QuantityPurchased . "\n";
								$transaction_key++;
							}
						}
					}
					$key++;
				}
				$ebay_api_data = make_platform_order($data);
					
				//插入数据表，返回错误日志
				if($ebay_api_data)
				{
					$error_log_model = M('order_plat_form_update_error_log');
					$error_log_data = array(
						'come_from_id'	=> $come_from_id,
						'time'			=> $now_time,
						'status'		=> '未处理',
					);
					foreach ($ebay_api_data as $key=>$val)
					{
						$error_log_data['order_number'] = $key;
						$error_log_data['msg'] = $val;
						$error_log_model->add($error_log_data);
					}
				}
					
				$ebay_return = array();
				if(!empty($ebay_api_data)){
					$success_num=count($data)-count($ebay_api_data);
					$ebay_return['msg'] = "请求失败！";
				}else{
					$success_num=count($data);
					$ebay_return['msg'] = "请求成功！";
				}
				$ebay_return['update_time'] = date('Y-m-d H:i:s',strtotime($now_time));
				$ebay_return['success_num'] = $success_num;
				$ebay_return['fail_num'] = count($ebay_api_data);
				return $ebay_return;
			}
			else
			{
				echo "No Order Found";
			}
		}
	}
	//乐天 API
	public function rakuten_api(){
		include LIB_PATH."Vendor/rakuten/src/test/GetOrderTest.php";
		$order_array=$res->orderModel;
		$error_array=$res->return->errorCode;
		if($error_array=='E10-001'){
			return array("msg"=>"这段时间没有订单！");
			exit();
		}
		$data=array();
		$all=array();
		$order=array();
		$product=array();
		$shipping=array();
		if(!is_array($order_array)){
			$order_array=array($order_array);
		}
		
		foreach ($order_array as $order_array_value)
		{
			$packagemodel=$order_array_value->packageModel;
			if(!is_array($packagemodel)){
				$packagemodel=array($packagemodel);
			}
			foreach ($packagemodel as $key=>$order_product_value)
			{
				$color='';
				$size='';
				$rakuten_product_gift_package='';
				$rakuten_product_hanky='';
				$rakuten_sku='';
				$rakuten_model='';
				$is_relate_sku=true;
				if($key==0)
				{
					$order=array("order_number"=>$order_array_value->orderNumber,"email"=>$order_array_value->ordererModel->emailAddress,"name"=>$order_array_value->ordererModel->familyName.$order_array_value->ordererModel->firstName,"telephone"=>$order_array_value->ordererModel->phoneNumber1.$order_array_value->ordererModel->phoneNumber2.$order_array_value->ordererModel->phoneNumber3,"currency"=>"jpy","price"=>$order_product_value->goodsPrice,"message"=>'',"is_gift_package"=>0,"come_from_id"=>20,"operator"=>$_SESSION[username],"date_time"=>$order_array_value->orderDate,"ship_service_level"=>'');
				}
				else
				{
					$order=array("order_number"=>$order_array_value->orderNumber."-".$key,"email"=>$order_array_value->ordererModel->emailAddress,"name"=>$order_array_value->ordererModel->familyName.$order_array_value->ordererModel->firstName,"telephone"=>$order_array_value->ordererModel->phoneNumber1.$order_array_value->ordererModel->phoneNumber2.$order_array_value->ordererModel->phoneNumber3,"currency"=>"jpy","price"=>$order_product_value->goodsPrice,"message"=>'',"is_gift_package"=>0,"come_from_id"=>20,"operator"=>$_SESSION[username],"date_time"=>$order_array_value->orderDate,"ship_service_level"=>'');
	
				}
				if(!is_array($order_product_value->itemModel))
				{
					$itemModel=array($order_product_value->itemModel);
				}
				else
				{
					$itemModel=$order_product_value->itemModel;
				}
				foreach ($itemModel as $value_itemModel)
				{
					$page_value = explode("/",substr($value_itemModel->pageURL,10));
					$rakuten_model= $page_value[0];
					$order_selected_choice=$value_itemModel->selectedChoice;
					$order_selected_choice_array=explode("\n",$order_selected_choice);
					for($i=0;$i<count($order_selected_choice_array);$i++){
						$condition["rakuten_model"]=$rakuten_model;
						$order_selected_choice_array_new=explode(":",$order_selected_choice_array[$i]);
						if($order_selected_choice_array_new[0]=='カラー')
						{//判断是颜色;
							if(!empty($order_selected_choice_array_new[1]))
							{
								$condition["color"]=$order_selected_choice_array_new[1];
							}
							
							$color=$order_selected_choice_array_new[1];
						}
						elseif($order_selected_choice_array_new[0]=='サイズ')
						{//判断是尺码
							if(!empty($order_selected_choice_array_new[1]))
							{
								$condition["size"]=$order_selected_choice_array_new[1];
							}
							
							$size=$order_selected_choice_array_new[1];
						}
						elseif
						($order_selected_choice_array_new[0]=='ギフト包装')
						{
							$rakuten_product_gift_package=$order_selected_choice_array_new[1];
						}
						elseif($order_selected_choice_array_new[0]=='レビュー特典')
						{
							$rakuten_product_hanky=$order_selected_choice_array_new[1];
						}
					}
					if(empty($color) && empty($size))
					{
						$rakuten_sku=$page_value[0];
						$is_relate_sku=false;
					}
					else
					{
						$sku_excle=M("jp_sku_excle")->where($condition)->field("amazon_sku,rakuten_sku,id")->find();
						if(!empty($sku_excle[rakuten_sku]))
						{
							$rakuten_sku=$sku_excle[rakuten_sku];
						}
						else
						{
							$amazon_sku_id=M("id_product_sku")->where(array("come_from_id"=>28,"sku"=>$sku_excle[amazon_sku]))->getField("id");
							if($amazon_sku_id)
							{
								$relate_code_array=M("id_relate_sku")->where(array("product_sku_id"=>$amazon_sku_id))->select();
								$rakuten_sku=$sku_excle[amazon_sku];
								$is_relate_sku=false;
							}
						}
					}
					if(!$is_relate_sku){
						$rakuten_product_sku=M("id_product_sku")->add(array("sku"=>$rakuten_sku,"come_from_id"=>20));
						foreach ($relate_code_array as $value){
							$relate_rakuten_code=M("id_relate_sku")->add(array("product_code_id"=>$value[product_code_id],"number"=>$value[number],"product_sku_id"=>$rakuten_product_sku));
						}
					}
					$product_one=array("name"=>$value_itemModel->itemName,"sku"=>$rakuten_sku,"price"=>$value_itemModel->price,"number"=>$value_itemModel->units,'rakuten_product_color'=>$color,'rakuten_product_size'=>$size,'rakuten_product_gift_package'=>$rakuten_product_gift_package,'rakuten_product_hanky'=>$rakuten_product_hanky,'rakuten_product_model'=>$rakuten_model);
					$product[]=$product_one;
				}
	
				$shipping=array("name"=>$order_product_value->senderModel->familyName.$order_product_value->senderModel->firstName,"country"=>"JP","state"=>$order_product_value->senderModel->prefecture,"city"=>$order_product_value->senderModel->city,"address1"=>$order_product_value->senderModel->subAddress,"address2"=>'',"address3"=>'',"post"=>$order_product_value->senderModel->zipCode1.$order_product_value->senderModel->zipCode2,"telephone"=>$order_product_value->senderModel->phoneNumber1.$order_product_value->senderModel->phoneNumber2.$order_product_value->senderModel->phoneNumber3,"shipping_style"=>'',"shipping_number"=>'',"shipping_weight"=>'',"shipping_price"=>'',"shipping_tax"=>'',"shipping_date"=>'',"shipping_operator"=>'',"shipping_hs"=>'',"shipping_sample"=>'',"shipping_report_price"=>'');
					
				$all["order"]=$order;
				$all["product"]=$product;
				$all["shipping"]=$shipping;
				$all["status"]=$order_array_value->status;
				$data[]=$all;
				unset($order);
				unset($product);
				unset($shipping);
			}
		}
// 		print_r($data);
// 		exit();
		$return=make_platform_order($data);
		if($return)
		{
			$error_log_model = M('order_plat_form_update_error_log');
			$error_log_data = array(
				'come_from_id'	=> 20,
				'time'			=> $end_time,
				'status'		=> '未处理',
			);
			foreach ($return as $key=>$val)
			{
				$error_log_data['order_number'] = $key;
				$error_log_data['msg'] = $val;
				$error_log_model->add($error_log_data);
			}
		}
		$date['come_from_id'] =20;
		$date['time'] = strtotime($end_time);
		$update_time = M("order_plat_form_update_time")->add($date);
		$success_num=count($data)-count($return);
			
		return array('update_time' => $end_time,'success_num' =>$success_num,'fail_num' => count($return));
	}
	
	//弃用？ 关联礼品
	public function attached_gift(){
		if(IS_POST){
			if(!empty($_POST[product_id])) $data["product_id"]=$_POST[product_id];
			if(!empty($_POST[product_set_id])) $data["product_set_id"]=$_POST[product_set_id];
			if(!empty($_POST[code_id])) $data["code_id"]=$_POST[code_id];
			$data["gift_code_id"]=$_POST[gift_code_id];
			$data["min_num"]=$_POST[min_num];
			$data["start_time"]=$_POST[start_time];
			$data["end_time"]=$_POST[end_time];
			$data["work_place"]=$_POST[work_place];
			$data["operator"]=$_POST[operator];
			$data["add_time"]=date("Y-m-d H:i:s",time());
			$data["is_work"]=$_POST[is_work];
			$add_product_gift=M("product_gift")->add($data);
			if($add_product_gift){
				$this->success("关联成功");
			}else{
				$this->success("关联失败");
			}
		}else{
			$this->display();
		}
	}
	
	//[平台.乐天] 添加/修改sku功能
	public function sku_relate(){
		$product_sku=M("id_product_sku")->where(array("sku"=>$_POST['relate_sku'],"come_from_id"=>20))->getField("id");
		if($product_sku)
		{
			echo "sku已存在";
		}
		else
		{
			if(!empty($_POST[hidden_value]))
			{
				$product_info=M("order_plat_form_product")->where(array("id"=>$_POST['product_id']))->find();
				if($product_info)
				{
					$jp_sku_excle=M("jp_sku_excle")->where(array("rakuten_model"=>$product_info[rakuten_product_model],"color"=>$product_info[rakuten_product_color],"size"=>$product_info[rakuten_product_size]))->save(array("rakuten_sku"=>$_POST[relate_sku]));
				}
				$product_sku=M("id_product_sku")->where(array("sku"=>$_POST[hidden_value]))->save(array("sku"=>$_POST[relate_sku]));
					
				$plat_order_product_sku=M("order_plat_form_product")->where(array("id"=>$_POST['product_id']))->save(array("sku"=>$_POST[relate_sku]));
			}
			else
			{
				$product_info=M("order_plat_form_product")->where(array("id"=>$_POST['product_id']))->find();
				if($product_info)
				{
					$jp_sku_excle=M("jp_sku_excle")->add(array("rakuten_sku"=>$_POST[relate_sku],"rakuten_model"=>$product_info[rakuten_product_model],"color"=>$product_info[rakuten_product_color],"size"=>$product_info[rakuten_product_size]));
// 					echo M("jp_sku_excle")->getlastsql();
// 					exit();
				}
				
				$product_sku=M("id_product_sku")->add(array("sku"=>$_POST[relate_sku],"come_from_id"=>$_POST[come_from_id]));
				
				$plat_order_product_sku=M("order_plat_form_product")->where(array("id"=>$_POST['product_id']))->save(array("sku"=>$_POST[relate_sku]));
			}
			if($jp_sku_excle && $plat_order_product_sku && $product_sku)
			{
				echo $_POST[relate_sku];
			}
		}
	}
	
	//手动订单
	public function order_manual()
	{
		$order_manual = D("order_manual");
    	import('Org.Util.Page');// 导入分页类
    	$count = $order_manual->count();// 查询满足要求的总记录数 $map表示查询条件
    	$Page = new \Page($count,C('LISTROWS'));// 实例化分页类 传入总记录数
    	// 进行分页数据查询 注意page方法的参数的前面部分是当前的页数使用 $_GET[p]获取
    	$show = $Page->show();// 分页显示输出
    	$List = $order_manual->order('id asc')->limit($Page->firstRow.','.$Page->listRows)->select();
    	$this->assign("list",$List);
    	$this->assign("page",$show);

    	$this->display();
	}
	public function order_manual_add()
	{
		$order_manual = D("order_manual");
		if(isset($_POST['dosubmit']))
		{
			$data['email'] = I('post.email');
			$data['order_number'] = I('post.order_number');
			$data['country'] = I('post.country');
			$data['state'] = I('post.state');
			$data['city'] = I('post.city');
			$data['address'] = I('post.address');
			$data['telephone'] = I('post.telephone');
			$data['price'] = I('post.price');
			$data['status'] = '未审核';
			$data['operator'] = session('username'); 
			$data['date_time'] = date("Y-m-d h:i:s",time());
			$insert = $order_manual->add($data);
			if($insert)
    		{
    			$this->success('手动订单添加成功！',U('/Admin/OrderManage/order_manual'));
    		}
    		else
    		{
    			$this->error("手动订单添加失败！");
    		}
		}
		else
		{
			if(isset($_GET['id']))
    		{
    			$id = $_GET['id'];
    			$info = $order_manual->where("id=$id")->find();
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
	public function order_manual_edit()
	{
		$order_manual = D("order_manual");
    	if(isset($_POST['id']))
    	{
    		$id = $_POST['id'];
    		$data['email'] = I('post.email');
			$data['order_number'] = I('post.order_number');
			$data['country'] = I('post.country');
			$data['state'] = I('post.state');
			$data['city'] = I('post.city');
			$data['address'] = I('post.address');
			$data['telephone'] = I('post.telephone');
			$data['price'] = I('post.price');
			$data['status'] = I('post.status');
			$data['operator'] = session('username'); 
			$data['date_time'] = date("Y-m-d h:i:s",time());
    		$pro = $order_manual->where("id=$id")->save($data);
    		
    		if($pro)
    		{
    			$this->success("手动订单修改成功",U('/Admin/OrderManage/order_manual'));
    		}
    		else
    		{
    			$this->error("手动订单修改失败");
    		}
    	}
	}
	public function order_manual_del()
	{
		$order_manual = D("order_manual");
    	if(isset($_GET['id']))
    	{
    		$id = $_GET['id'];
    		$Del = $order_manual->where("id=$id")->delete();
    		if($Del)
    		{
    			$this->success("手动订单删除成功",U('/Admin/OrderManage/order_manual'));
    		}
    		else 
    		{
    			$this->error("手动订单删除失败");
    		}
    	}	
	}
	//添加手动订单关联产品
	public function ajax_order_manual_relate_code()
	{
		$id_catalog = M("id_catalog");
		if(I('post.action')=='fetch')
		{
			$this->order_manual_id = I('post.order_manual_id');
			$this->catalog_list = $id_catalog->where(array('is_work'=>1))->order('sort_id')->getField('id,name');
			$this->style = 'fetch';//模板文件的使用方式
			//渲染模板 
			echo $this->fetch();
		}
		elseif(I('post.action')=='select_change')
		{
			if(I('post.tag')=='catalog')
			{
				$id_product = M('id_product');
				//product
				$this->list = $id_product->where(array('catalog_id'=>I('post.catalog'),'is_work'=>1))->order('sort_id')->getField('id,name');
				$this->style = 'select';
				$html = $this->fetch();
				$data[] = array('tag'=>'product','html'=>$html);
				//其它清空
				$data[] = array('tag'=>'color','html'=>'<option>--请选择--</option>');
				$data[] = array('tag'=>'size','html'=>'<option>--请选择--</option>');
				$data[] = array('tag'=>'code','html'=>'<option>--请选择--</option>');
				$this->ajaxReturn($data,'JSON');
			}
			elseif(I('post.tag')=='product')
			{
				$code_model = D('id_product_code');
				//build color
				$this->list = $code_model->attribute('color', I('post.product'), null, null)->getField('id,value');
				$this->style = 'select';
				$html = $this->fetch();
				$data[] = array('tag'=>'color','html'=>$html);
				//build size
				$this->list = $code_model->attribute('size', I('post.product'), null, null)->getField('id,value');
				$html = $this->fetch();
				$data[] = array('tag'=>'size','html'=>$html);
				//build code
				$this->list = $code_model->where( array('product_id'=>I('post.product')) )->getField('id,name');
				$this->style = 'select';
				$html = $this->fetch();
				$data[] = array('tag'=>'code','html'=>$html);
				$this->ajaxReturn($data,'JSON');
			}
			elseif(I('post.tag')=='color' || I('post.tag')=='size')
			{
				$code_model = M('id_product_code');
// 				$attribute_model = M('id_product_attribute');
		
				$where['product_id'] = I('post.product');
				I('post.color') && $where['color_id'] = I('post.color');
				I('post.size') && $where['size_id'] = I('post.size');
		
				//select color,build size
				if(I('post.tag')=='color')
				{
					$this->list = $code_model->attribute('size', I('post.product'), I('post.color'), null)->getField('id,value');
					$this->style = 'select';
					$this->old_value = I('post.size');
					$html = $this->fetch();
					$data[] = array('tag'=>'size','html'=>$html);
				}
				//select size,build color
				elseif(I('post.tag')=='size')
				{
					$this->list = $code_model->attribute('color', I('post.product'), null, I('post.size'))->getField('id,value');
					$this->style = 'select';
					$this->old_value = I('post.color');
					$html = $this->fetch();
					$data[] = array('tag'=>'color','html'=>$html);
				}
				$this->ajaxReturn($data,'JSON');
			}
		}
		elseif(I('post.action'=='add_order_customization'))
		{
			$order_manual_product = D('order_manual_product');
			$data['order_manual_id'] = I('post.order_manual_id');
			$data['price'] = I('post.price');
			$data['color_id'] = I('post.color_id');
			if(I('post.size_id')!=0)
			{
				$id_product_code = D('id_product_code');
				$code_id = $id_product_code->where(array('product_id'=>I('post.product_id'),'color_id'=>I('post.color_id'),'size_id'=>I('post.size_id'),'is_work'=>1))->getField('id');
				$data['size_id'] = I('post.size_id');
				$data['code_id'] = $code_id;
				$data['customization'] = '';
			}
			else
			{
				$data['customization'] = I('post.order_customization');
				$data['size_id'] = 0;
				$data['code_id'] = 0;
			}
			$data['number'] = I('post.number');
			$order_manual_product_add = $order_manual_product->add($data);
			if($order_manual_product_add)
			{
    			$this->success('产品添加成功！',U('/Admin/OrderManage/order_manual_product'));
    		}
    		else
    		{
    			$this->error("产品添加失败！");
    		}
    		$this->ajaxReturn($data,'JSON');
		}
	}
	public function order_manual_product()
	{	
		$order_manual_product = D("order_manual_product");
		
		import('Org.Util.Page');// 导入分页类
    	// 进行分页数据查询 注意page方法的参数的前面部分是当前的页数使用 $_GET[p]获取
    	if(isset($_GET['id']))
    	{
    		$this->assign('order_manual_id',$_GET['id']);
    		$List = $order_manual_product->where(array('order_manual_id'=>$_GET['id']))->order('id asc')->limit($Page->firstRow.','.$Page->listRows)->select();
    		foreach($List as $List_value_key=>$List_value)
    		{
    			$id_product_code = D('id_product_code');
				if($List_value['code_id']!=0)
				{
					$code_list = $id_product_code->where(array('id'=>$List_value['code_id'],'is_work'=>1))->order('sort_id asc')->getField('name,code');
					foreach($code_list as $key=>$code_list_value)
					{
						$List[$List_value_key]['order_manual_product'][] = array('name'=>$key,'code'=>$code_list_value);
					}
				}
    			
    		}
    		$this->assign("list",$List);
    	}
    	$count = count($List);// 查询满足要求的总记录数 $map表示查询条件
    	$Page = new \Page($count,C('LISTROWS'));// 实例化分页类 传入总记录数
    	$show = $Page->show();// 分页显示输出
    	$this->assign("page",$show);
    	$this->display();
	}
	public function order_manual_product_del()
	{
		$order_manual_product = D('order_manual_product');
		if(isset($_GET['id']))
		{
			$id = $_GET['id'];
			$product_delete = $order_manual_product->where(array('id'=>$id))->delete();
			if($product_delete)
			{
    			$this->success("手动订单关联产品删除成功",U('/Admin/OrderManage/order_manual_product/',array('id'=>$_GET['order_manual_id'])));
    		}
    		else 
    		{
    			$this->error("手动订单关联产品删除失败");
    		}
		}
	}
	public function order_manual_update()
	{
		$order_manual = D('order_manual');	
	
		if(isset($_POST['dosubmit']))
		{
			$data['status'] = I('post.status');
			$order_manual_update = $order_manual->where(array('id'=>I('get.order_manual_id')))->save($data);
			if($order_manual_update)
			{
				$this->success("修改订单状态成功",U('/Admin/OrderManage/order_manual'));
			}
			else
			{
				$this->error("修改订单状态失败");
			}
		}
		
		
	}
	
	//弃用？功能不明。
	public function to_heavy()
	{
		if(I('get.act')=='start')
		{
			$success=false;
			$first_code_id='';
			$count="select `name`,count(*) as count from id_product_code group by name having count>1";
			$array=M()->query($count);
			
			foreach ($array as $value)
			{
				$code_array=M("id_product_code")->where(array("name"=>$value[name]))->field("id")->select();

				foreach ($code_array as $key=>$code)
 				{
					if($key=="0")
					{
						$relate_sku=M("id_relate_sku")->where(array("product_code_id"=>$code[id]))->getfield("product_code_id");
 						$first_code_id=$relate_sku;
 					}
 					else
 					{
 						$relate_sku_save=M("id_relate_sku")->where(array("product_code_id"=>$code[id]))->save(array("product_code_id"=>$first_code_id));
						if($relate_sku_save)
						{
							$product_code_delete=M("id_product_code")->where(array("id"=>$code[id]))->delete();
							$success=true;
						}
					}
				}
			}
			if($success)
			{
				$this->success("去重成功");
			}
			else
			{
				echo $first_code_id."失败";
			}
		}
		else
		{
			$this->display();
		}	
	}
	
	//[网站] 退货列表管理
	public function product_return_list_web()
	{
		$order_plat_form_status_historyDB = M('order_plat_form_status_history');
		$order_web_status_historyDB = M('order_web_status_history');
		$order_web_model = D('order_web');
		$order_plat_form_model = D('order_plat_form');
		
		$database = $order_web_status_historyDB ;
		
		if(IS_GET)
		{
			$sta = I('get.sta');
			$start_time = I('get.start_time');
			$end_time = I('get.end_time');
			if($sta)
			{
				$whereId['status'] = $sta;	
				$this-> sta = $sta;	
			}
			else
			{
				$whereId['status'] = array('in',array('change','return'));
			}
				
			if($start_time && $end_time)
			{
				$whereId['date_time'] = array(array("egt",$start_time),array('elt',$end_time));
				$this->start_time = $start_time;
				$this->end_time = $end_time;
			}
		}
		elseif(IS_POST)
		{
			$order_num = I('post.order_number');
			$start_time = I('post.start_time');
			$end_time = I('post.end_time');
			if($order_num)
			{
				$order_num_id = $order_web_model->where('`order_number` = "'.$order_num.'"')->getField('id');
				$whereId['order_web_id'] = $order_num_id;
				$this-> order_number = $order_num;
			}
			elseif($start_time  && $end_time)
			{
				$whereId['date_time'] = array(array("egt",strtotime($start_time)),array('elt',strtotime($end_time)));
				$this->start_time = $start_time;
				$this->end_time = $end_time;
					
			}
			$whereId['status'] = array('in',array('change','return'));
		}
		
		$list_id = $database->field('order_web_id')->group('order_web_id')->where($whereId)->order('date_time desc')->select();
		if($list_id)
		{
			foreach($list_id as $list_id_k=>$list_id_v)
			{
				$info_id[$list_id_k] =$list_id_v['order_web_id'];
			}
			$id = implode(',',$info_id);
			$where_mo['id'] = array('in',$id);
		//	dump($where_mo);exit;
			$list = $order_web_model->where($where_mo)->relation(true)->select();	
			foreach($list as $k=>$v)
			{
				$zz[0]=$v;
				$order_list[$k] =complete_web_order($zz, $v['come_from_id']);
			}
			foreach($order_list as $k=>$v)
			{
				$zz[$k]= $v[0];
				$status['status'] = array('in',array('change','return'));
				$status['order_web_id'] = $zz[$k]['id'];
				$zz[$k]['order_web_status_history'] = $database->group('order_web_id')->where($status)->order('date_time desc')->find();
			}
		}
		else
		{
			$this-> page_bar = "没有记录";
			
		}
	//	dump($zz);exit;
		$user_model = D('user');//用户列表
		$this->user_list = $user_model->getAllUser();
		$this ->order_list = $zz;
		$this-> tpltitle = "退换货列表管理";
		$this->display();
	}
	//[平台] 退货列表管理 
	public function product_return_list_plat()
	{
		$order_plat_form_status_historyDB = M('order_plat_form_status_history');
		$order_web_model = D('order_web');
		$order_plat_form_model = D('order_plat_form');
		
		$database = $order_plat_form_status_historyDB ;
		if(IS_GET)
		{
			$sta = I('get.sta');
			$start_time = I('get.start_time');
			$end_time = I('get.end_time');
			if($sta)
			{
				$whereId['status'] = $sta;	
				$this-> sta = $sta;	
			}
			else
			{
				$whereId['status'] = array('in',array('change','return'));
			}
			
			if($start_time && $end_time)
			{
				$whereId['date_time'] = array(array("egt",$start_time),array('elt',$end_time));
				$this->start_time = $start_time;
				$this->end_time = $end_time;
			}
		}
		elseif(IS_POST)
		{
			$order_num = I('post.order_number');
			$start_time = I('post.start_time');
			$end_time = I('post.end_time');
			if($order_num)
			{
				$order_num_id = $order_plat_form_model->where('`order_number` = '.$order_num)->getField('id');
				$whereId['order_platform_id'] = $order_num_id;
			}
			elseif($start_time  && $end_time)
			{
				$whereId['date_time'] = array(array("egt",$start_time),array('elt',$end_time));
				$this->start_time = $start_time;
				$this->end_time = $end_time;
			}
			$whereId['status'] = array('in',array('change','return'));
		}
		
		$list_id = $database->field('order_platform_id') ->where($whereId)->order('date_time desc')->select();
		if($list_id)
		{
			foreach($list_id as $list_id_k=>$list_id_v)
			{
				$info_id[$list_id_k] =$list_id_v['order_platform_id'];
			}	
			$id = implode(',',$info_id);
			if($id=="")
			{
				$id='0';
			}
			$where_mo['id'] = array('in',$id);
			$list = $order_plat_form_model->where($where_mo)->relation(true)->select();	
			//dump($list);exit;
			
			foreach($list as $k=>$v)
			{
				$zz[0]=$v;
				$order_list[$k] =complete_plat_order($zz, $v['come_from_id']);
			}
			foreach($order_list as $k=>$v)
			{
				$zz[$k]= $v[0];
				$status['status'] = array('in',array('change','return'));
				$status['order_platform_id'] = $zz[$k]['id'];
				$zz[$k]['order_plat_form_status_history'] = $database->group('order_platform_id')->where($status)->order('date_time desc')->find();
			}
		}
		else
		{
			$this-> page= "没有记录";
			
		}
		$user_model = D('user');//用户列表
		$this->user_list = $user_model->getAllUser();
		$this -> order_list = $zz;
		$this-> tpltitle = "退换货列表管理";
		$this->display();
	}
	
	//[通用] 取消订单
	public function cancel_order()
	{
		if(I("get.come")=="web")
		{
			$code_array=array();
			$customization_code=array();
			$product_id_array=array();
			$product_customization_id=array();
			$order_id=M("order_web")->where(array("order_number"=>$_GET["order_number"]))->getField("id");
			$product_array=M("order_web_product")->where(array("order_web_id"=>$order_id))->field("code_id,id")->select();
			$product_customization=M("order_web_product_customization")->where(array("order_web_id"=>$order_id))->select();
			if($product_array)
			{
				foreach($product_array as $value)
				{
					$code=M("id_product_code")->where(array("id"=>$value[code_id]))->getField("code");
					//判断是否是睡衣定制
					$nightwear_customization=M("order_web_nightwear_customization")->where(array("order_web_product_id"=>$value[id]))->getField("id");
					if($nightwear_customization)
					{
						$customization_code[]="ncustomization";
						$product_customization_id[]=$value[id];
					}
					else
					{
						$code_array[]=$code;
						$product_id_array[]=$value[id];
					}
				}
			}
			elseif($product_customization)//定制
			{
				$customization_code[]="customization";
				foreach($product_customization as $val)
				{
					$product_customization_id[]=$val[id];
				}
			}
			order_product_return($order_id,0,$code_array,$product_id_array,$customization_code,$product_customization_id);
		}
		elseif(I("get.come")=="plat")
		{
			$order_id=M("order_plat_form")->where(array("order_number"=>$_GET["order_number"]))->getField("id");
			$product_array=M("order_plat_form_product")->where(array("order_platform_id"=>$order_id))->field("code_id,id")->select();
			$code_array=array();
			$product_id_array=array();
			foreach($product_array as $value)
			{
				$code=M("id_product_code")->where(array("id"=>$value[code_id]))->getField("code");
				$code_array[]=$code;
				$product_id_array[]=$value[id];
			}
			order_product_return(0,$order_id,$code_array,$product_id_array);
		}
		$this->success("取消成功");
	}
	
	//[通用] 删除运单详情
	public function delivery_delete()
	{
		$order_delivery_detailDB = M('order_delivery_detail');
		$order_delivery_parametersDB = M('order_delivery_parameters');
		$order_plat_form_statusDB　= M('order_plat_form_status');
		$order_plat_form_status_historyDB = M('order_plat_form_status_history');
		$order_web_statusDB = M('order_web_status');
		$order_web_status_historyDB = M('order_web_status_history');
		$order_id = I('get.id');
		$type = I('get.type');
		$date['status'] = 'shipping';
		$date['date_time'] = time();
		$date['message'] = '删除运单详情';
		$date['operator'] = session('username');
		if($type=="web")
		{
			$return = $order_delivery_parametersDB->where('`order_id` ='.$order_id)->delete();
			
			$return01 = $order_delivery_detailDB->where('`order_web_id` ='.$order_id)->delete();

			$whereId['order_web_id'] = $order_id ;
			$return02 =M('order_web_status')->where($whereId)->save($date);
			
			$date['order_web_id'] = $order_id ;
			$return03 = M('order_web_status_history')->add($date);
			
		}
		elseif($type=="plat")
		{
			$return = $order_delivery_parametersDB->where('`order_platform_id` ='.$order_id)->delete();
			
			$return01 = $order_delivery_detailDB->where('`order_platform_id` ='.$order_id)->delete();
			
			$whereId['order_platform_id'] = $order_id ;
			$return02 =M('order_plat_form_status')->where($whereId)->save($date);
			
			$date['order_platform_id'] = $order_id ;
			$return03 = M('order_plat_form_status_history')->add($date);
		}
		
		if($return && $return01 && $return02 && $return03)
		{
			$this->success('删除成功');
		}
		else
		{
			$this->error('删除失败');
		}
		
	}
	
	//样布订单
	public function sample_order()
	{
		$sample_recordDB = M('sample_record');
		$sample_record_addressDB = M('sample_record_address');
		$sample_record_deliveryDB = M('sample_record_delivery');
		$id_come_fromDB = M('id_come_from');
		$come_from = $id_come_fromDB ->where('`style` ="web"')->select();
		if(I('get.come_from'))
		{
			$this->come_from_name = I('get.come_from');	
			$come_from_id = $id_come_fromDB ->where('`name` ="'.I('get.come_from').'"') ->getField('id');
			$where['come_from_id'] = $come_from_id;
			$where_check['come_from_id'] = $come_from_id;
			if(I('get.is_send'))
			{
				$where['is_send'] = '0';
				$this->is_send = "no";
			}
		}
		elseif(IS_POST)
		{
			if(I('post.order_number'))
			{
				$where['lily_sample_record_id'] = I('post.order_number');
				$this->order_number = I('post.order_number');
			}
			if(I('post.start_time') && I('post.end_time'))
			{
				$where['date'] =array('between',array(I('post.start_time'),I('post.end_time')." 23:59:59"));
				$this->start_time = I('post.start_time');
				$this->end_time = I('post.end_time');
			}
			if(I('post.email'))
			{
				$where['email'] = I('post.email');
				$this->email = I('post.email');
			}
			$this->come_from_name = I('post.come_from');
			$come_from_id = $id_come_fromDB ->where('`name` ="'.I('post.come_from').'"') ->getField('id');
			$where['come_from_id'] = $come_from_id;	
		}
		else
		{
			$this->come_from_name = 'us';
			$where['come_from_id'] = 4;	
			$where_check['come_from_id'] = 4;	
		}
	//	dump($where);exit;
		$coun = $list = $sample_recordDB ->where($where)->count();
		$Page       = new \Think\Page1($coun);// 实例化分页类 传入总记录数
		$nowPage = isset($_GET['p'])?$_GET['p']:1;
		$show       = $Page->show();// 分页显示输出
		
		$list = $sample_recordDB ->where($where)->page($nowPage.','.C('LISTROWS'))->order('id desc')->select();
		foreach($list as $k=>$v)
		{
			$list[$k]['sample_detail'] = explode(',',$v['sample_detail_id_str']);
			$list[$k]['come_from'] = get_come_from_name($v['come_from_id']);
			$list[$k]['address'] = 	$sample_record_addressDB->where('`sample_record_id` = '.$v['id'])->find();
			$list[$k]['blag_num'] =  $sample_recordDB ->where('`email` = "'.$v['email'].'"')->count();
			$list[$k]['delivery'] = $sample_record_deliveryDB->where('`sample_record_id` ='.$v['id'])->find();
			$order_wed = M('order_web')->where('`email` ="'.$v['email'].'"')->field('id,order_number')->select();
			$order_plat = M('order_plat_form')->where('`email` ="'.$v['email'].'"')->field('id,order_number')->select();
			if($order_wed)
			{
				foreach($order_wed as $order_wed_k=>$order_wed_v)
				{
					$order_status = M('order_web_status')->where('`order_web_id` ='.$order_wed_v['id'])->getField('status');
				//	dump($delivery);
					if($order_status !='history')
					{
						$list[$k]['is_order'] = '有订单';	
					}
				}
			}
			if($order_plat)
			{
				foreach($order_plat as $order_plat_k=>$order_plat_v)
				{
					$order_status = M('order_plat_form_status')->where('`order_platform_id` ='.$order_plat_v['id'])->getField('status');
					if($order_status !='history')
					{
						$list[$k]['is_order'] = '有订单';	
					}
				}
			}
			if(!$list[$k]['is_order'])
			{
				$list[$k]['is_order'] ='';
			}
		}
	//	dump($list);exit;
		$sample_update_cheak =M('sample_record_check')->where($where_check)->order('time desc')->find();
		$this->sample_update_cheak = $sample_update_cheak;
		$this->coun = $coun;
		$this->assign('list',$list);
		$this->page = $show;
		$this->come_from = $come_from;
		$this->nowPage =$nowPage;
		
		$this ->delivery_style = delivery_style();
		$this->display();			
	}
	//更新样布订单
	public function sample_order_update()
	{
		set_time_limit(0);
		$id_come_fromDB = M('id_come_from');
		$sample_recordDB = M('sample_record');
		$sample_record_addressDB = M('sample_record_address');
		$sample_record_checkDB = M('sample_record_check');
		
		$country = I('get.country');
		$come_from_id = $id_come_fromDB->where('`name` ="'.$country.'"')->getField('id');  //国家id
		
		$sample_update_cheak = $sample_record_checkDB->where('`come_from_id` ='.$come_from_id)->order('time desc')->find(); //记录表信息
		
		if($sample_update_cheak['update_end_id'])
		{
			$start_id = $sample_update_cheak['update_end_id'];
		}
		else
		{
			$start_id = 0;
		}
		
		$href="?";
		$order_href=$href.'country='.$country;	
		$order_href.="&start_id=".$start_id;	
		$new_order = json_decode(httpPost("http://www.lilysilk.com/mono_admin/mono_admin_template/erpGetSampleOrderApi.php",$order_href),true);
		
		$err_num = 0;
		$suss_num = 0;
	//	dump($new_order);exit;
		if($new_order)
		{
			if(is_array($new_order))
			{
				foreach($new_order as $k=>$v)
				{	
					if($k != '-1')
					{
						$date['lily_sample_record_id'] = $v['id'];
						$date['email'] = $v['email'];
						$date['sample_detail_id_str'] = $v['sample_detail_id_str'];
						$date['date'] = $v['date'];
						$date['payment_style'] = $v['payment_style'];
						$date['sample_customer_id'] = $v['sample_customer_id'];
						$date['message'] = $v['message'];
						$date['message_ch'] = $v['message_ch'];
						$date['is_send'] = $v['is_send'];
					//	$date['order_num'] = $v['order_num'];
						$date['come_from_id'] = $come_from_id;
						$order[$k] = $sample_recordDB->add($date);
						$suss_num ++ ;
						if($order[$k])
						{
							$detail_date['sample_record_id'] = $order[$k];
							$detail_date['first_name'] = $v['address']['first_name'];
							$detail_date['last_name'] = $v['address']['last_name'];
							$detail_date['address'] = $v['address']['address'];
							$detail_date['city'] = $v['address']['city'];
							$detail_date['province'] = $v['address']['province'];
							$detail_date['code'] = $v['address']['code'];
							$detail_date['telephone'] = $v['address']['telephone'];
							$detail_date['country'] = $v['address']['country'];
							
							$detail= $sample_record_addressDB->add($detail_date);
							if(!$detail)
							{
								$error_detail[$err_num] = $v['id'];
								$err_num++;
							}
							
							if($v['order_num']!='')
							{
								$delivery['date_time'] = $v['date'];
								$delivery['number'] = $v['order_num'];
								$delivery['sample_record_id'] = $order[$k];
								$delivery['status'] = 'normal';
								$delivery['message'] = 'normal';
								M('sample_record_delivery') ->add($delivery);
							}
						}
						else
						{
							$error[$err_num] = $v['id'];
							$err_num++;
						}
					}			
				}
				$end_array =end($new_order);
				$cheak['come_from_id'] = $come_from_id;
				$cheak['update_end_id'] = $end_array['id'];
				$cheak['time'] = date('Y-m-d H:i:s',time());
				$cheak['operator'] = session('username');
				$sample_record_checkDB ->add($cheak);
			}
			if($err_num!=0)
			{
				echo "更新失败的数据ID ：";
				foreach($error as $k=>$v)
				{
					echo $v;				
				}
				echo "detail 更新失败的数据主ID ：";
				foreach($error_detail as $error_detail_k=>$error_detail_v)
				{
					echo $error_detail_v;				
				}
			}
			else
			{
				$this->success('更新成功的数量 ：'.$suss_num);
			}
		}
		else
		{
			$cheak['come_from_id'] = $come_from_id;
			$cheak['update_end_id'] = $start_id ;
			$cheak['time'] = date('Y-m-d H:i:s',time());
			$cheak['operator'] = session('username');
			$sample_record_checkDB ->add($cheak);
			
			$this->success('没有要更新的数据');	
		}
	}
	
	//更新运单号
	public function sample_order_num_update()
	{
		$sample_record_deliveryDB = M('sample_record_delivery');
		$sample_recordDB = M('sample_record');
		$username = session('username');
		if(IS_POST)
		{
			$where['sample_record_id'] = I('post.id');
			$delivery_result = $sample_record_deliveryDB->where($where)->find();
			if($delivery_result)
			{
				$date['number'] = I('post.number');
				$date['style'] = I('post.style');
				$date['message'] = 'normal';
				$date['status'] = 'normal';	
				$date['date_time'] =date("Y-m-d H:i:s",time());
				$result = $sample_record_deliveryDB->where($where)->save($date);
				if($result)
				{
					
					echo '1';	exit;
				}
				else
				{
					echo '2';	exit;
				}
			}
			else
			{
				$date['number'] = I('post.number');
				$date['style'] = I('post.style');
				$date['sample_record_id'] = I('post.id');
				$date['message'] = 'normal';
				$date['status'] = 'normal';
				$date['date_time'] =date("Y-m-d H:i:s",time());
				$result = $sample_record_deliveryDB->add($date);
				
				if($result)
				{
					$sample_recordDB->where(array('id' =>I('post.id')))->save(array('is_send' =>1));
					echo '3';	exit;
				}
				else
				{
					echo '4';	exit;
				}
			}
			
		}
		else
		{
			echo '5';exit;	
		}
		
	}
	//样布订单删除
	public function  sample_order_detele()
	{
		$sample_record_deliveryDB = M('sample_record_delivery');
		$sample_record_addressDB = M('sample_record_address');
		$sample_recordDB = M('sample_record');
		if(IS_GET)
		{
			$id = I('get.id');
			$where['id'] = $id;
			$result = $sample_recordDB->where($where)->delete();
			$whereId['sample_record_id'] = $id;
			$result_address = $sample_record_addressDB->where($whereId)->delete();
			$result_delivery = $sample_record_deliveryDB->where($whereId)->delete();
			
			if($result)
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
			$this->error('参数错误');	
		}
		
	}
	//样布导出
	public function sample_order_explode_execl()
	{
		$sample_recordDB = M('sample_record');
		$sample_record_addressDB =M('sample_record_address');
		$sample_record_deliveryDB = M('sample_record_delivery');
		$id_come_fromDB = M('id_come_from');
		if(I('get.type') =='screening')
		{
			if(I('post.order_number'))
			{
				$whereId['lily_sample_record_id'] = I('post.order_number');
				$this->order_number = I('post.order_number');
			}
			if(I('post.start_time') && I('post.end_time'))
			{
				$whereId['date'] =array('between',array(I('post.start_time'),I('post.end_time')." 23:59:59"));
				$this->start_time = I('post.start_time');
				$this->end_time = I('post.end_time');
			}
			if(I('post.email'))
			{
				$whereId['email'] = I('post.email');
				$this->email = I('post.email');
			}
			if(I('post.send'))
			{
				$whereId['is_send'] = 0;
			}
			$this->come_from_name = I('post.come_from');
			$come_from_id = $id_come_fromDB ->where('`name` ="'.I('post.come_from').'"') ->getField('id');
			$whereId['come_from_id'] = $come_from_id;	
		}
		elseif(I('get.type') =='select')
		{
			$id = I('post.check');
			if(!$id)
			{
				$this->error('没有做出选择');	
			}
			$whereId['id'] = array('in',$id);
		}
		$list = $sample_recordDB ->where($whereId)->select();
		if(!$list)
		{
			$this->error('没有做出选择');	
		}
		foreach($list as $k=>$v)
		{
			$list[$k]['delivery'] = $sample_record_deliveryDB->where('`sample_record_id` = '.$v['id'])->field('number,style')->find();
			$list[$k]['address'] = $sample_record_addressDB -> where('`sample_record_id` = '.$v['id']) ->find();
			
			$info[$k]['order_number']= I('post.come_from').'-'.$v['lily_sample_record_id'];
			$info[$k]['email'] = $v['email'];
			$info[$k]['yb'] = $v['sample_detail_id_str'];
			$info[$k]['time'] = $v['date'];
			$info[$k]['country'] = $list[$k]['address']['country'];
			$info[$k]['name'] = $list[$k]['address']['first_name'].' '.$list[$k]['address']['last_name'];
			$info[$k]['telephone'] = $list[$k]['address']['telephone'];
			$info[$k]['style'] = $list[$k]['delivery']['style'];
			$info[$k]['number'] = $list[$k]['delivery']['number'];
		}
		$title=array('编号','email','样布','时间','国家','姓名','电话','快递公司','快递单号');
		
		exportExcel($info,'样布订单'."-".date('Y-m-d H:i:s',time()),$title);
		
	}
	//修改样布订单的地址
	public function sample_order_address_update()
	{
		$sample_record_addressDB = M('sample_record_address');
		$countriesDB = M('countries');
		if(IS_GET)
		{
			$id = I('get.id');
			$come_from = I('get.come_from');
			$nowPage = I('get.p');
			$this->come_from =$come_from;
			$this->nowPage =$nowPage;
			$this->id = $id;
			$info = $sample_record_addressDB->where('`sample_record_id` = '.$id)->find();
			$this->info =$info;
			$country =  M('countries')->select();
			$this ->country = $country;
			$this->display();
		}
		elseif(IS_POST)
		{
			$where['sample_record_id'] = I('post.id');
			$date['first_name'] = I('post.Fname');
			$date['last_name'] = I('post.Lname');
			$date['address'] = I('post.address');
			$date['city'] = I('post.city');
			$date['province'] = I('post.province');
			$date['code'] = I('post.code');
			$date['telephone'] = I('post.telephone');
			$date['country'] = I('post.country');
			$result = $sample_record_addressDB ->where($where) ->save($date);	
			if($result)
			{
				$this->success('修改成功');
			}
			else
			{
				$this->error('修改失败');
			}
		}
	}
	//添加备注
	public	function sample_order_message_add()
	{
		$sample_recordDB = M('sample_record');
		$countriesDB = M('countries');
		if(IS_GET)
		{

			$id = I('get.id');
			$come_from = I('get.come_from');
			$nowPage = I('get.p');
			$this->come_from =$come_from;
			$this->nowPage =$nowPage;
			$this->id = $id;
			
			$info = $sample_recordDB->where('`id` = '.$id)->find();
			$this->info =$info;
			$this->display();
		}
		elseif(IS_POST)
		{
			$where['id'] = I('post.id');
			$date['message_ch'] = I('post.message_ch');
			$result = $sample_recordDB ->where($where) ->save($date);	
			if($result)
			{
				$this->success('修改成功');
			}
			else
			{
				$this->error('修改失败');
			}
			
		}
		
	}
	//导出word
	public function  sample_order_word()
	{
		$sample_recordDB = M('sample_record');
		$sample_record_addressDB =M('sample_record_address');
		$sample_record_deliveryDB = M('sample_record_delivery');
		$id_come_fromDB = M('id_come_from');
		$id = I('post.check');
		layout(false); // 临时关闭当前模板的布局功能
		if(!$id)
		{
			$this->error('没有做出选择');	
		}
		$whereId['id'] = array('in',$id);
		$list = $sample_recordDB ->where($whereId)->select();
		if(!$list)
		{
			$this->error('没有做出选择');	
		}
		foreach($list as $k=>$v)
		{
			$list[$k]['delivery'] = $sample_record_deliveryDB->where('`sample_record_id` = '.$v['id'])->field('number,style')->find();
			$list[$k]['address'] = $sample_record_addressDB -> where('`sample_record_id` = '.$v['id']) ->find();
			
			$info[$k]['order_number']= I('post.come_from').'-'.$v['lily_sample_record_id'];
			$info[$k]['email'] = $v['email'];
			$info[$k]['yb'] = $v['sample_detail_id_str'];
			$info[$k]['time'] = $v['date'];
			$info[$k]['country'] = $list[$k]['address']['country'];
			$info[$k]['name'] = $list[$k]['address']['first_name'].' '.$list[$k]['address']['last_name'];
			$info[$k]['telephone'] = $list[$k]['address']['telephone'];
			$info[$k]['style'] = $list[$k]['delivery']['style'];
			$info[$k]['number'] = $list[$k]['delivery']['number'];
		}
		
		$content = $this->fetch('sample_order_message_add');//获取模板内容信息word是模板的名称
		$fileContent = WordMake($content);
		dump($fileContent);exit;
		$fp = fopen("test.doc", 'w');
		fwrite($fp, $fileContent);
		fclose($fp);	
	}
	
	//批量转历史
	public function order_to_history()
	{
		$order_web=M("order_web");
		$order_web_id=$order_web->where("date_time<'2016-07-06 00:00:00' and come_from_id IN(7,8,9,10,11,12,13,14,15,16)")->select();
		$web_bstatus=M('order_web_status');
		foreach($order_web_id as $key =>$value_order_web_id)
		{
			$data['order_web_id']=$value_order_web_id[id];
			$data['status']='history';
			$data['date_time']=time();
			$data['operator']='zhuxunming';
			$web_bstatus->add($data);
		}

		$order_plat_form=M("order_plat_form");
		$order_plat_form_id=$order_plat_form->where("date_time<'2016-07-06 00:00:00' and come_from_id IN(17,18,19,20)")->select();
		$plat_status=M('order_plat_form_status');

		foreach($order_plat_form_id as $key =>$value_order_plat_id)
		{
			$data['order_platform_id']=$value_order_plat_id[id];
			$data['status']='history';
			$data['date_time']=time();
			$data['operator']='zhuxunming';
			$plat_status->add($data);
		}
	}
//订单发货率
		public function order_delivery_ratio()
		{
		    $OrderDeliveryDetail = D("order_delivery_detail");
		    $OrderPlatForm = D("order_plat_form");
		    $OrderPlatFormStatus = D("order_plat_form_status");
		    $OrderWeb = D("order_web");
		    $OrderWebStatus = D("order_web_status");
		    $IdComeFrom = D("id_come_from");
		    if(I('get.style'))
		    {
		        $style = I('get.style');
		    }
		    else
		    {
		        $style = "web";
		    }
		    $this->style=$style;
		    $come_from_list = $IdComeFrom->where(array('style'=>$style))->field('id,alias')->select();
		    $this->come_from_list=$come_from_list;
		    if(I('get.come_from_id'))
		    {
		        $come_from_id = I('get.come_from_id');
		    }
		    else
		    {
		        if($style=="web")
		        {
		            $come_from_id = 4;
		        }
		        else
		        {
		            $come_from_id = 1;
		        }
		    }
		    $this->come_from_id=$come_from_id;
		
		    if(I('post.begin_time')!='' && I('post.end_time')!='')
		    {
		        $begin = I('post.begin_time');
		        $end = I('post.end_time');
		        $begin_time = I('post.begin_time')." 00:00:00";
		        $end_time = I('post.end_time')." 23:59:59";
		    }
		    //默认显示当月
		    else
		    {
		        $begin = date("Y-m-01",time());
		        $end = date("Y-m-d",time());
		        $begin_time = date("Y-m-01 00:00:00",time());
		        $end_time = date("Y-m-d 23:59:59",time());
		    }
		    $this->begin_time=$begin_time;
		    $this->end_time=$end_time;
		    $order_begin_time = strtotime($begin_time);
		    $order_end_time = strtotime($end_time);
		
		    $day = ceil(($order_end_time-$order_begin_time)/(3600*24));
		    $statistics_day = array();
		    for($i=0;$i<$day;$i++)
		    {
		        $every_day = date("Y-m-d",($order_begin_time+($i*3600*24)));
		        $statistics_day[] = $every_day;
		    }
		    //获取指定国家的订单发货率
		    $order_delivery_ratio_all = $this->get_order_delivery_ratio($style,$come_from_id,$begin_time,$end_time);
		
		    
		    //获取指定国家每一天的订单发货率开始
		    foreach($statistics_day as $statistics_day_key=>$statistics_day_value)
		    {
		        $start_day = $statistics_day_value." 00:00:00";
		        $end_day = $statistics_day_value." 23:59:59";
		        $order_detail = array();
		        if($style=="web")
		        {
		            $order_web_detail = $OrderWeb->query("SELECT id,date_time FROM `order_web` WHERE `come_from_id`=$come_from_id and `date_time`>='$start_day' and `date_time`<='$end_day' and `id` not in(select order_id from fba_order) and 'id' not in(select order_web_id from order_web_status where status<>'cancel')");
		            //"select order_web.date_time,order_web.id,order_delivery_detail.time from order_web left outer JOIN order_delivery_detail on (order_web.id=order_delivery_detail.order_web_id) where order_web.come_from_id=4 and order_web.date_time>='$start_day' and order_web.date_time<='$end_day' and order_web.id not in(select order_id from fba_order) ORDER BY `order_web`.`id` DESC";
		            $order_list_count[$statistics_day_key] = count($order_web_detail);
		            foreach($order_web_detail as $order_web_detail_value)
		            {
		                $order_delivery_detail_list = $OrderDeliveryDetail->where(array('order_web_id'=>$order_web_detail_value['id'],'message'=>'normal','status'=>'normal'))->field('time')->group('order_web_id')->select();
		
		                foreach($order_delivery_detail_list as $order_delivery_detail_list_value)
		                {
		                    $order_detail[$statistics_day_key][] = array('date_time'=>$order_web_detail_value[date_time],'time'=>$order_delivery_detail_list_value[time]);
		                }
		            }
		
		        }
		        else
		        {
		            $order_web_detail = $OrderPlatForm->query("SELECT id,date_time FROM `order_plat_form` WHERE `come_from_id`=$come_from_id and `date_time`>='$start_day' and `date_time`<='$end_day' and `id` not in(select orderplatform_id from fba_order) and 'id' not in(select order_platform_id from order_plat_form_status where status<>'cancel')");
		            $order_list_count[$statistics_day_key] = count($order_web_detail);
		            foreach($order_web_detail as $order_web_detail_value)
		            {
		                $order_delivery_detail_list = $OrderDeliveryDetail->where(array('order_platform_id'=>$order_web_detail_value['id'],'message'=>'normal','status'=>'normal'))->field('time')->group('order_platform_id')->select();
		
		                foreach($order_delivery_detail_list as $order_delivery_detail_list_value)
		                {
		                    $order_detail[$statistics_day_key][] = array('date_time'=>$order_web_detail_value[date_time],'time'=>$order_delivery_detail_list_value[time]);
		                }
		            }
		        }
		        //print_r($order_detail)
		
		
		        $one[$statistics_day_key]= 0;
		        $two[$statistics_day_key]= 0;
		        $three[$statistics_day_key]= 0;
		        $fone[$statistics_day_key]= 0;
		        $five[$statistics_day_key]= 0;
		        $six[$statistics_day_key]= 0;
		
		        foreach($order_detail[$statistics_day_key] as $key=>$value)
		        {
		            $time_now = strtotime($value['time'])-strtotime($value['date_time']);
		            if($time_now>60 && $time_now<=24*60*60)
		            {
		                $one[$statistics_day_key]++;
		            }
		            else if($time_now>24*60*60 && $time_now<=36*60*60)
		            {
		                $two[$statistics_day_key]++;
		            }
		            else if($time_now>36*60*60 && $time_now<=48*60*60)
		            {
		                $three[$statistics_day_key]++;
		            }
		            else if($time_now>48*60*60 && $time_now<=60*60*60)
		            {
		                $fone[$statistics_day_key]++;
		            }
		            else if($time_now>60*60*60 && $time_now<=72*60*60)
		            {
		                $five[$statistics_day_key]++;
		            }
		            else if($time_now>72*60*60)
		            {
		                $six[$statistics_day_key]++;
		            }
		             
		        }
		
		        $seven[$statistics_day_key] = $order_list_count[$statistics_day_key]-$one[$statistics_day_key]-$two[$statistics_day_key]-$three[$statistics_day_key]-$fone[$statistics_day_key]-$five[$statistics_day_key]-$six[$statistics_day_key];
		        if($seven[$statistics_day_key]<=0)
		        {
		            $seven[$statistics_day_key] = 0;
		        }
		
		        $order_delivery_ratio[$statistics_day_key]['date'] = $statistics_day_value;
		        $order_delivery_ratio[$statistics_day_key]['number'] = $order_list_count[$statistics_day_key];
		        $order_delivery_ratio[$statistics_day_key]['one_num'] = $one[$statistics_day_key];
		        $order_delivery_ratio[$statistics_day_key]['one'] = round($one[$statistics_day_key]/$order_list_count[$statistics_day_key],4)*100 ."%";
		        $order_delivery_ratio[$statistics_day_key]['two_num'] = $two[$statistics_day_key];
		        $order_delivery_ratio[$statistics_day_key]['two'] = round($two[$statistics_day_key]/$order_list_count[$statistics_day_key],4)*100 ."%";
		        $order_delivery_ratio[$statistics_day_key]['three_num'] = $three[$statistics_day_key];
		        $order_delivery_ratio[$statistics_day_key]['three'] = round($three[$statistics_day_key]/$order_list_count[$statistics_day_key],4)*100 ."%";
		        $order_delivery_ratio[$statistics_day_key]['fone_num'] = $fone[$statistics_day_key];
		        $order_delivery_ratio[$statistics_day_key]['fone'] = round($fone[$statistics_day_key]/$order_list_count[$statistics_day_key],4)*100 ."%";
		        $order_delivery_ratio[$statistics_day_key]['five_num'] = $five[$statistics_day_key];
		        $order_delivery_ratio[$statistics_day_key]['five'] = round($five[$statistics_day_key]/$order_list_count[$statistics_day_key],4)*100 ."%";
		        $order_delivery_ratio[$statistics_day_key]['six_num'] = $six[$statistics_day_key];
		        $order_delivery_ratio[$statistics_day_key]['six'] = round($six[$statistics_day_key]/$order_list_count[$statistics_day_key],4)*100 ."%";
		        $order_delivery_ratio[$statistics_day_key]['seven_num'] = $seven[$statistics_day_key];
		        $order_delivery_ratio[$statistics_day_key]['seven'] = round($seven[$statistics_day_key]/$order_list_count[$statistics_day_key],4)*100 ."%";
		
		    }
		    //获取指定国家每一天的订单发货率结束
		    
		    //获取所有国家和平台的订单发货率
		    if(isset($_POST['order_sub']))
		    {
		        if(I('post.begin_time')!='' && I('post.end_time')!='')
		        {
		            $begin_time = I('post.begin_time')." 00:00:00";
		            $end_time = I('post.end_time')." 23:59:59";
		        }
		        //默认显示当月
		        else
		        {
		            $begin_time = date("Y-m-01 00:00:00",time());
		            $end_time = date("Y-m-d 23:59:59",time());
		        }
		        $order_delivery_ratio_list = $this->get_order_delivery_ratio($style,0,$begin_time,$end_time);
		        $this->order_delivery_ratio_list = $order_delivery_ratio_list;
		        //print_r($order_delivery_ratio_list);
		    }
		    $this->order_delivery_ratio_all=$order_delivery_ratio_all;
	        $this->order_delivery_ratio = $order_delivery_ratio;
	        $this->display();
		    
		}
		
	public function get_order_delivery_ratio($style,$come_from_id,$begin_time,$end_time)
	{
	    $OrderDeliveryDetail = D("order_delivery_detail");
	    $OrderPlatForm = D("order_plat_form");
	    $OrderPlatFormStatus = D("order_plat_form_status");
	    $OrderWeb = D("order_web");
	    $OrderWebStatus = D("order_web_status");
	    $IdComeFrom = D("id_come_from");
	    //显示当月总发货率
	    $twenty_four= 0;
	    $thirty_six= 0;
	    $forty_eight= 0;
	    $sixty= 0;
	    $seventy_two= 0;
	    $seventy_two_be= 0;
	    $order_all_detail = array();
	
	    if($style=="web")
	    {
	        if($come_from_id!=0)
	        {
	            $order_web_all_detail = $OrderWeb->query("SELECT id,date_time FROM `order_web` WHERE `come_from_id`=$come_from_id and `date_time`>='$begin_time' and `date_time`<='$end_time' and `id` not in(select order_id from fba_order) and 'id' not in(select order_web_id from order_web_status where status<>'cancel')");
	        }    
	        else
	        {
	            $order_web_all_detail = $OrderWeb->query("SELECT id,date_time FROM `order_web` WHERE `date_time`>='$begin_time' and `date_time`<='$end_time' and `id` not in(select order_id from fba_order) and 'id' not in(select order_web_id from order_web_status where status<>'cancel')");
	        }
	        foreach($order_web_all_detail as $order_web_all_detail_value)
	        {
	            $order_delivery_detail = $OrderDeliveryDetail->where(array('order_web_id'=>$order_web_all_detail_value['id'],'message'=>'normal','status'=>'normal'))->field('time')->group('order_web_id')->select();
	
	            foreach($order_delivery_detail as $order_delivery_detail_value)
	            {
	                $order_all_detail[] = array('date_time'=>$order_web_all_detail_value [date_time],'time'=>$order_delivery_detail_value[time]);
	            }
	        }
	    }
	    else
	    {
	        if($come_from_id!=0)
	        {
	            $order_web_all_detail = $OrderPlatForm->query("SELECT id,date_time FROM `order_plat_form` WHERE `come_from_id`=$come_from_id and `date_time`>='$begin_time' and `date_time`<='$end_time' and `id` not in(select orderplatform_id from fba_order) and 'id' not in(select order_platform_id from order_plat_form_status where status<>'cancel')");
	        }
	        else 
	        {
	            $order_web_all_detail = $OrderPlatForm->query("SELECT id,date_time FROM `order_plat_form` WHERE `date_time`>='$begin_time' and `date_time`<='$end_time' and `id` not in(select orderplatform_id from fba_order) and 'id' not in(select order_platform_id from order_plat_form_status where status<>'cancel')");
	        }
	        foreach($order_web_all_detail as $order_web_all_detail_value)
	        {
	            $order_delivery_detail = $OrderDeliveryDetail->where(array('order_platform_id'=>$order_web_all_detail_value['id'],'message'=>'normal','status'=>'normal'))->field('time')->group('order_platform_id')->select();
	
	            foreach($order_delivery_detail as $order_delivery_detail_value)
	            {
	                $order_all_detail[] = array('date_time'=>$order_web_all_detail_value[date_time],'time'=>$order_delivery_detail_value[time]);
	            }
	        }
	    }
	    //print_r($order_all_detail);
	    foreach($order_all_detail as $order_all_detail_value)
	    {
	        $time_now = strtotime($order_all_detail_value['time'])-strtotime($order_all_detail_value['date_time']);
	        if($time_now<=24*60*60)
	        {
	            $twenty_four++;
	        }
	        else if($time_now>24*60*60 && $time_now<=36*60*60)
	        {
	            $thirty_six++;
	        }
	        else if($time_now>36*60*60 && $time_now<=48*60*60)
	        {
	            $forty_eight++;
	        }
	        else if($time_now>48*60*60 && $time_now<=60*60*60)
	        {
	            $sixty++;
	        }
	        else if($time_now>60*60*60 && $time_now<=72*60*60)
	        {
	            $seventy_two++;
	        }
	        else if($time_now>72*60*60)
	        {
	            $seventy_two_be++;
	        }
	    }
	    $unfilled = count($order_web_all_detail)-$twenty_four-$thirty_six-$forty_eight-$sixty-$seventy_two-$seventy_two_be;
	    if($unfilled<=0)
	    {
	        $unfilled = 0;
	    }
	    $order_delivery_ratio_all['date'] = substr($begin_time,0,-9)."--".substr($end_time,0,-9);
	    $order_delivery_ratio_all['number'] = count($order_web_all_detail);
	    $order_delivery_ratio_all['twenty_four_num'] = $twenty_four;
	    $order_delivery_ratio_all['twenty_four'] = round($twenty_four/count($order_web_all_detail),4)*100 ."%";
	    $order_delivery_ratio_all['thirty_six_num'] = $thirty_six;
	    $order_delivery_ratio_all['thirty_six'] = round($thirty_six/count($order_web_all_detail),4)*100 ."%";
	    $order_delivery_ratio_all['forty_eight_num'] = $forty_eight;
	    $order_delivery_ratio_all['forty_eight'] = round($forty_eight/count($order_web_all_detail),4)*100 ."%";
	    $order_delivery_ratio_all['sixty_num'] = $sixty;
	    $order_delivery_ratio_all['sixty'] = round($sixty/count($order_web_all_detail),4)*100 ."%";
	    $order_delivery_ratio_all['seventy_two_num'] = $seventy_two;
	    $order_delivery_ratio_all['seventy_two'] = round($seventy_two/count($order_web_all_detail),4)*100 ."%";
	    $order_delivery_ratio_all['seventy_two_be_num'] = $seventy_two_be;
	    $order_delivery_ratio_all['seventy_two_be'] = round($seventy_two_be/count($order_web_all_detail),4)*100 ."%";
	    $order_delivery_ratio_all['unfilled_num'] = $unfilled;
	    $order_delivery_ratio_all['unfilled'] = round($unfilled/count($order_web_all_detail),4)*100 ."%"; 
	    return $order_delivery_ratio_all;
	}
}
?>
