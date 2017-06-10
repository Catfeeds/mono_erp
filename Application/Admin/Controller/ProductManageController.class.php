<?php
namespace Admin\Controller;

// 0 提交审核
// 1 审核人通过 -> 交于工厂
// 2 工厂接受成功 
// 3 工厂已发货
// 4 申请人完成收货 
// 5 新品定性  工厂通知状态
// 8 放弃
// 9 审核未通过
class ProductManageController extends CommonController
{
    public function index()
    {
        $this->display();
    }
    
    public function productstock()
    {
        $this->display();
    }
    public function productcode()
    {
        $this->display();
    }
	//所有产品申请列表
    public function product_new_all()
    {
		$userid = session('userid');    // 用户ID
		$product_newDB=M('product_new');	
		$userDB = M('user');
		
// 		import('Org.Util.Page');// 导入分页类
		$count =$product_newDB->where("1=1")->order('applicant_time desc')->count();
        $Page       = new \Think\Page1($count);// 实例化分页类 传入总记录数
		$nowPage = isset($_GET['p'])?$_GET['p']:1;
        $show       = $Page->show();
		$info=$product_newDB->where("1=1")->order('applicant_time desc')->page($nowPage.','.C('LISTROWS'))->select();
		foreach($info as $k=>$v){
			$info[$k]['sta'] = panduan_status($v['status']);
			}
		$this->assign('page',$show);	
		$this->assign('list',$info);
        $this->display();
    }
	//个人产品申请列表
    public function product_new()
    {
		$username = session('username');    // 用户名
		$product_newDB=M('product_new');	
		$userDB = M('user');
		$info=$product_newDB->where("`applicant`="."'".$username."'")->select();
		foreach($info as $k=>$v){
			$info[$k]['sta']=panduan_status($v['status']);
		}
		$this->assign('list',$info);
        $this->display();
    }
	//产品申请添加
    public function product_new_add()
    {
		$username = session('username');    // 用户名
		$product_newDB=M('product_new');	
		//上传	
		if(isset($_POST['dosubmit'])){
			$date['name']=$_POST['name'];
			$date['message']=$_POST['content'];
			$date['applicant']=$username;
			$date['applicant_time']=time();
			$date['examination_time']=time();
			$pro=$product_newDB->add($date);
			if($pro){
				$this->assign("jumpUrl",U('/Admin/ProductManage/product_new'));
				$this->success('产品申请成功！');
			}else{
				$this->error('产品申请失败!');
			}					
		}else{
			//修改
			if($_GET['id']){
				$id=$_GET['id'];
				$info=$product_newDB->where('`id`='.$id)->find();
				$this->assign('info',$info);
				$this->assign('tpltitle','修改');
			}else{
				$this->assign('tpltitle','添加');
			}
			//公共输出  判断审核逻辑
			$accessDB=M('access');
			$userDB=M('user');
			$RoleUser = M("RoleUser");
			$access=$accessDB->where('`node_id`=54')->select();
			foreach($access as $k=>$v){
				$role_id[$k].=$v['role_id'];
				}
			$roleid01= implode(',', $role_id);
			if($roleid01)	{
			$whereId = '`role_id` in ('.$roleid01.')';
			$role_id=$RoleUser->where($whereId)->select();
			foreach($role_id as $k=>$v){
				$user_id[$k].=$v['user_id'];
				}
			$user_id01= implode(',', $user_id);
			$user = $userDB->where('`id` in ('.$user_id01.')')->select();
			$this->assign('user',$user);}
			$this->display();
			}
    }
	//产品申请修改
	public function product_new_edit()
	{
		$id=$_POST['id'];
		$product_newDB=M('product_new');	
		if(isset($id))
		{
			//根据表单提交的POST数据更新数据对象
			$date['name']=$_POST['name'];
			$date['message']=$_POST['content'];
			$date['applicant_time']=time();
			$pro=$product_newDB->where('`id`='.$id)->save($date);
			if($pro){
				$this->assign("jumpUrl",U('/Admin/ProductManage/product_new'));
				$this->success('产品申请修改成功！');
			}else{
				 $this->error('产品申请修改失败!');
			}
		}
	}
	//未用   产品申请删除      
	public function product_new_del()
	{
		$id=$_GET['id'];
		$product_newDB=M('product_new');
		$info=$product_newDB->where('`id`='.$id)->delete();	
		if($info){
			$this->assign("jumpUrl",U('/Admin/ProductManage/product_new'));
			$this->success('申请删除成功！');
		}else{
			$this->error('申请删除失败!');	
		}
	}
	//产品申请放弃
	public function product_new_out()
	{
		$id=$_GET['id'];
		$status=$_GET['status'];
		$product_newDB=M('product_new');
		$date['status'] = $status;
		$date['examination_time'] =time();
		$info=$product_newDB->where('`id`='.$id)->save($date);	
		if($info){
			$this->assign("jumpUrl",U('/Admin/ProductManage/product_new'));
			$this->success('放弃成功！');
		}else{
			$this->error('放弃失败!');	
		}
	}
	//产品重新审核
	public function product_new_again()
	{
		$id=$_GET['id'];
		$status=$_GET['status'];
		$product_newDB=M('product_new');
		$date['status'] = $status;
		$date['examination_time']=time();
		$info=$product_newDB->where('`id`='.$id)->save($date);	
		if($info){
			$this->assign("jumpUrl",U('/Admin/ProductManage/product_new'));
			$this->success('重新审核成功！');
		}else{
			$this->error('重新审核失败!');	
		}
	}
	//新品审核
	public function product_new_audit()
	{
		$username = session('username');    // 用户名
		if($_GET['status']){
			$status=$_GET['status'];
			$id=$_GET['id'];
			$product_newDB=M('product_new');	
			$date['status']=$status;
			$date['examination_time']=time();
			$date['examination'] = $username;
			$pro=$product_newDB->where('`id`='.$id)->save($date);
			if($pro){
				$this->assign("jumpUrl",U('/Admin/ProductManage/product_new_audit'));
				$this->success('审核成功！');
			}else{
				$this->error('审核失败!');	
			}
		}else{
			$product_newDB=M('product_new');	
			$userDB = M('user');
			$info=$product_newDB->where("`status`=0 ")->select();
			$this->assign('list',$info);
			$this->display();
		}
	}

	//工厂发货已收到按钮
	public function product_new_received()
	{
		if($_GET['id'])
		{
			$id=$_GET['id'];
			$product_newDB=M('product_new');	
			$date['status']=4;
			$date['examination_time']=time();
			$pro=$product_newDB->where('`id`='.$id)->save($date);  //申请数据表状态修改
			
			//factory_newDB 数据表 工厂记录   显示等待通知
			$factory_newDB=M('factory_new');
			$data['status'] = 2;
			$fac=$factory_newDB->where('`product_new_id`='.$id)->order('id desc')->save($data);
			if($pro and $fac)
			{
				$this->assign("jumpUrl",U('/Admin/ProductManage/product_new'));
				$this->success('完成收货操作成功！');
			}
			else
			{
				$this->error('完成收货操作失败!');	
			}
		}
	}
	//FBA产品管理
	public function product_fba_add()
	{
		$product_fba = M("product_fba");
		if(isset($_POST['dosubmit']))
		{
			$data['name'] = $_POST['name'];
			$data['code'] = $_POST['code'];
			$data['total_number'] = $_POST['total_number'];
			$data['sale_number'] = 0;
			$data['now_number'] = $_POST['total_number']-0;
			$map['code'] = $_POST['code'];
			$fba_select = $product_fba->where($map)->select();
			if($fba_select)
			{
				$this->success("产品code已经存在！",U('/Admin/ProductManage/product_fba_add'));
			}
			else 
			{	
				$fba_insert = $product_fba->add($data);
				if($fba_insert)
				{
					$this->success("添加产品FBA成功！",U('/Admin/ProductManage/product_fba'));
				}
				else
				{
					$this->error("添加产品FBA失败！");
				}
			}
			
		}
		else
		{
			//修改
			if(isset($_GET['id']))
			{
				$id = $_GET['id'];
				$info = $product_fba->where("id=$id")->find();
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
	//Fba产品管理列表
	public function product_fba()
	{
		$product_fba = M("product_fba");
		//import('Org.Util.Page');// 导入分页类
		$count = $product_fba->count();// 查询满足要求的总记录数 $map表示查询条件
		$Page = new \Think\Page1($count);// 实例化分页类 传入总记录数
		// 进行分页数据查询 注意page方法的参数的前面部分是当前的页数使用 $_GET[p]获取
		$nowPage = isset($_GET['p'])?$_GET['p']:1;
		$show = $Page->show();// 分页显示输出
		$fbalist = $product_fba->order('id asc')->page($nowPage.','.C('LISTROWS'))->select();
		$this->assign('fbalist',$fbalist);
		$this->assign("page",$show);
		$this->display();
	}
	//Fba产品管理修改
	public function product_fba_edit()
	{
		$product_fba = M("product_fba");
		if(isset($_POST['id']))
		{
			$id = $_POST['id'];
			$data['name'] = $_POST['name'];
			$data['code'] = $_POST['code'];
			$data['total_number'] = $_POST['total_number'];
			$data['sale_number'] = 0;
			$data['now_number'] = $_POST['total_number']-0;
			$fba_update = $product_fba->where("id=$id")->save($data);
			if($fba_update)
			{
				$this->success("修改产品FBA成功！",U('/Admin/ProductManage/product_fba'));
			}
			else
			{
				$this->error("修改产品FBA失败！");
			}
		}
	}
	//Fba产品管理删除
	public function product_fba_del()
	{
		$product_fba = M("product_fba");
		if(isset($_GET['id']))
		{
			$id = $_GET['id'];
			$fba_delete = $product_fba->where("id=$id")->delete();
			if($fba_delete)
			{	
				$this->success("删除产品FBA成功！",U('/Admin/ProductManage/product_fba'));
			}
			else
			{
				$this->error("删除产品FBA失败！");
			}
		}
	}
	//网站产品库存管理
	public function product_stock_add()
	{
		$product_stock_total = M("product_stock_total");
		if(isset($_POST['dosubmit']))
		{
			$data['product_chinese_name'] = $_POST['product_chinese_name'];
			$data['code'] = $_POST['code'];
			$data['total_num'] = $_POST['total_num'];
			$data['sale_num'] = 0;
			$data['num'] = $_POST['total_num']-0;
			$data['date'] = date('Y-m-d');
			$map['code'] = $_POST['code'];
			$product_stock_select = $product_stock_total->where($map)->select();
			if($product_stock_select)
			{
				$this->success("产品code已经存在！",U('/Admin/ProductManage/product_stock_add'));
			}
			else
			{
				$product_stock_insert = $product_stock_total->add($data);
				if($product_stock_insert)
				{
					$this->success("添加产品库存成功！",U('/Admin/ProductManage/product_stock'));
				}
				else
				{
					$this->error("添加产品库存失败！");
				}
			}
				
		}
		else
		{
			//修改
			if(isset($_GET['id']))
			{
				$id = $_GET['id'];
				$info = $product_stock_total->where("id=$id")->find();
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
	//网站产品库存管理列表
	public function product_stock()
	{
		$product_stock_total = M("product_stock_total");
	//	import('Org.Util.Page');// 导入分页类
		$count = $product_stock_total->count();// 查询满足要求的总记录数 $map表示查询条件
		$Page = new \Think\Page1($count);// 实例化分页类 传入总记录数
		// 进行分页数据查询 注意page方法的参数的前面部分是当前的页数使用 $_GET[p]获取
		$nowPage = isset($_GET['p'])?$_GET['p']:1;
		$show = $Page->show();// 分页显示输出
		$product_stock_list = $product_stock_total->order('date desc')->page($nowPage.','.C('LISTROWS'))->select();
		$this->assign('product_stock_list',$product_stock_list);
		$this->assign("page",$show);
		$this->display();
	}
	//网站产品库存管理修改
	public function product_stock_edit()
	{
		$product_stock_total = M("product_stock_total");
		if(isset($_POST['id']))
		{
			$id = $_POST['id'];
			$data['name'] = $_POST['name'];
			$data['code'] = $_POST['code'];
			$data['total_num'] = $_POST['total_num'];
			$data['sale_num'] = 0;
			$data['num'] = $_POST['total_num']-0;
			$product_stock_update = $product_stock_total->where("id=$id")->save($data);
			if($product_stock_update)
			{
				$this->success("修改产品库存成功！",U('/Admin/ProductManage/product_stock'));
			}
			else
			{
				$this->error("修改产品库存失败！");
			}
		}
	}
	//网站产品库存管理删除
	public function product_stock_del()
	{
		$product_stock_total = M("product_stock_total");
		if(isset($_GET['id']))
		{
			$id = $_GET['id'];
			$product_stock_delete = $product_stock_total->where("id=$id")->delete();
			if($product_stock_delete)
			{
				$this->success("删除产品库存成功！",U('/Admin/ProductManage/product_stock'));
			}
			else
			{
				$this->error("删除产品F库存失败！");
			}
		}
	}
	
	//套件库存预警
	public function product_stock_warn_tj()
	{
		$productstocksetDB=M('product_stock_set');      //套件库存对比预警表
		//套件预警列表
		$prolist_tj_warn=$productstocksetDB->where('`number` >= maximum or `number` <= minimum')->select();
		foreach($prolist_tj_warn as $k=>$v){
			$prolist_tj_warn[$k]['sty']=panduan_style($prolist_tj_warn[$k]['style']);
			$prolist_tj_warn[$k]['sku_name']=$this->panduan_sku_name($prolist_tj_warn[$k]['sku_id']);
		}
		//套件正常列表
		if( IS_GET && !I('get.style') ) $_GET['style'] = 2;//默认local
		$where = array(
			'number' => array('EXP','between minimum and maximum'),//minimum 数据库字段 单词错了 囧
			'style' => I('get.style'),
		);
		
	//	import('Org.Util.Page');// 导入分页类
		$prolist_tj_coun=$productstocksetDB->where($where)->count();
		$Page       = new \Think\Page1($prolist_tj_coun);// 实例化分页类 传入总记录数
		$nowPage = isset($_GET['p'])?$_GET['p']:1;
		$show       = $Page->show();// 分页显示输出
		$prolist_tj=$productstocksetDB->where($where)->page($nowPage.','.C('LISTROWS'))->order('id')->select();
		foreach($prolist_tj as $k=>$v){
			$prolist_tj[$k]['sty']=panduan_style($prolist_tj[$k]['style']);
			$prolist_tj[$k]['sku_name']=$this->panduan_sku_name($prolist_tj[$k]['sku_id']);
		}
		$this->assign('prolist_tj_warn',$prolist_tj_warn);
		$this->assign('prolist_tj',$prolist_tj);
		$this->assign('page',$show);
		$this->style = I('get.style');
		$this->display();
	}
	//单品库存预警
	public function product_stock_warn_dp()
	{
		$idproductcodeDB=M('id_product_code');			//单品信息表
		$productstockDB=M('product_stock');				//单品库存对比预警表
		
		//单品预警列表
		$prolist_dp_warn=$productstockDB->where('`number` >= maximum or `number`<= minimum')->select();
		$prolist_dp_warn_coun=$productstockDB->where('`number` >= maximum or `number`<= minimum')->count();
		foreach($prolist_dp_warn as $k=>$v){
			$id_code=$idproductcodeDB->where('`id`='.$prolist_dp_warn[$k]['code_id'])->find(); //根据id code_id关联
			$prolist_dp_warn[$k]['name']=$id_code['name'];
			$prolist_dp_warn[$k]['sty']=panduan_style($prolist_dp_warn[$k]['style']);
		}
		
		//单品正常列表
		if( IS_GET && !I('get.style') ) $_GET['style'] = 2;//默认local
		$where = array(
			'number' => array('EXP','between minimum and maximum'),
			'style' => I('get.style'),
		);
	//	import('Org.Util.Page');// 导入分页类
		$prolist_dp_coun=$productstockDB->where($where)->count();
		$Page       = new \Think\Page1($prolist_dp_coun);// 实例化分页类 传入总记录数
		$nowPage = isset($_GET['p'])?$_GET['p']:1;
		$show       = $Page->show();// 分页显示输出
		$prolist_dp=$productstockDB->where($where)->page($nowPage.','.C('LISTROWS'))->order('id')->select();
		foreach($prolist_dp as $k=>$v){
			$id_code=$idproductcodeDB->where('`id`='.$prolist_dp[$k]['code_id'])->find();
			$prolist_dp[$k]['name']=$id_code['name'];
			$prolist_dp[$k]['sty']=panduan_style($prolist_dp[$k]['style']);
		}
		$this->assign('prolist_dp_warn',$prolist_dp_warn);
		$this->assign('prolist_dp',$prolist_dp);
		$this->assign('page',$show);
		$this->style = I('get.style');
		$this->display();
	}
	
	//库存列表
	public function stock_local()
	{
		$this->stock_list(2);//本地
	}
	public function stock_fba()
	{
	    set_time_limit(0);
	    $update_time_model = M('product_stock_fba_update_time');
	    $product_stock_model = M('product_stock');//单品库存表
	    
	    //上次更新时间
	    $update_time = $update_time_model->order('time desc')->find();
	    $update_time = $update_time ? $update_time['time'] : strtotime('2016-05-01 00:00:00');//如果第一次更新，设置起点为2016-05-01
	    if( (time()-$update_time)<3600 )//一小时内不更新
	    {
	    	$args = array('status' => 'skip',);
	    	$this->stock_list(1,$args);
	    	exit;
	    }
	    
	    //api
	    $param = "?QueryStartDateTime=$update_time";
	    $return_data = json_decode(httpPost("https://www.lilysilk.com/singapore/FBAInventoryServiceMWS/Samples/ListInventorySupplySample.php",$param),true);	    
// 	    dump($return_data);exit;
	    //如果api发生错误
// 	    if( !$return_data || $return_data['status']!='success' ) 
// 	    {
// 	    	$args = array('status'	=> 'error',);
// 	    	$this->stock_list(1,$args);
// 	    	exit;
// 	    }
	    	
	    //处理数据
	    $error_num = 0;
	    $total_num = 0;
	    $stock_list_group = $return_data;
// 	    $lack_relate_sku_list[] = array('SKU','PLACE','CODE');
	    foreach ($stock_list_group as $key=>$val) //$key=[eu|us]
	    {
	    	foreach ($val as $k=>$v)
	    	{
	    		$row = array();
	    		$row['edit_name'] = session('username');//操作人
	    		$row['number'] = $v['number'];
	    		$row['style'] = 1;//FBA
	    		$row['place'] = $key;
				//sku -> code
	    		$fba_sku_relate_model = M('product_stock_fba_sku_relate');
	    		$temp = $fba_sku_relate_model->where( array('sku'=>$v['sku'],'come_from'=>$key) )->find();
	    		if($temp && $temp['code_id'])
	    		{
	    			//product_stock
	    			$row['code_id'] = $temp['code_id'];
					$where = array(
	    				'code_id'	=> $temp['code_id'],
	    				'style'		=> 1,
	    				'place'		=> $key,);
	    			$temp = $product_stock_model->where($where)->find();
	    			if( $temp['code_id'] )
	    			{
	    				$product_stock_model->where($where)->save($row);
	    			}
	    			else
	    			{
	    				$product_stock_model->add($row);
	    			}
	    		}
	    		else
	    		{
// 	    			$temp || $fba_sku_relate_model->add( array('sku'=>$v['sku']) );//添加，待关联
	    			$error_num++;
	    			continue;//不更新product_stock
	    		}
	    	}
	    	$total_num += sizeof($val);
	    }
	    
	    //update_time
	    $update_time_model->add( array('time'=>time()) );
	    
	    //返回同步结果
	    if($error_num)
	    {
	    	$args = array(
	    		'total_num' => $total_num,
	    		'error_num'	=> $error_num,
	    		'status'	=> 'error',
	    	);
	    }
	    else 
	    {
	    	$args = array(
	    		'total_num'	=> $total_num,
	    		'status'	=> 'success',
	    	);
	    }
		$this->stock_list(1,$args);
	}
	public function stock_us()
	{
		$this->stock_list(3);//美国
	}
	public function stock_all()
	{
		$this->stock_list(0);//本地
	}
	protected function stock_list($style,$args=null)
	{
		$id_catalogDB=M('id_catalog');                  //分类
		$id_product_codeDB=M('id_product_code');       //小产品
		$id_productDB=M('id_product');                //产品
		
		if( !I('get.flag') ) $_GET['flag'] = 'single';//single单件，set套件
		if( !I('get.warn') ) $_GET['warn'] = 'all';//all所有，warn警告
		if( $style==1 ) //FBA的额外参数
		{
			$stock_sync_result = '';
			if( $args['status']=='success' )
			{
				$stock_sync_result .= "<span style='color:red;'>同步库存信息成功！</span><br/>";
				$stock_sync_result .= "共更新了<span style='color:red;'>{$args['total_num']}</span>条记录。<br/>";
			}
			else if( $args['status']=='error' )
			{
				$stock_sync_result .= "<span style='color:red;'>同步库存信息成功！</span><br/>";
				$stock_sync_result .= "有<span style='color:red;'>{$args['total_num']}</span>条库存变动记录。<br/>";
				$stock_sync_result .= "其中<span style='color:red;'>{$args['error_num']}</span>条记录因 <span style='color:red;'>sku未关联code</span>而未保存<br/>";
			}
			else if( $args['status']=='skip' )
			{
				//do nothing
			}
			
			$this->stock_sync_result = $stock_sync_result;
		}
		
		/*if($style==2 || $style==1)//本地库存code/sku搜索
		{*/
			if(isset($_GET[code]))
			{
				$code_id=M("id_product_code")->where(array("code"=>$_GET[code]))->getField("id");
				$where["code_id"]=$code_id;
			}
		/*}*/
		
		if( I('get.flag')=='single' )//单件
		{
			$stock_model = M('product_stock');
		}
		elseif( I('get.flag')=='set' )//套件
		{
			$stock_model = M('product_stock_set');
		}
		
		if( I('get.warn')=='all' )//所有
		{
            $style && $where['style'] = $style;
		}
		elseif( I('get.warn')=='warn' )//警告
		{
		    $where = array(
		        'number' => array('EXP','not between minimum and maximum'),
		        'minimum!=0 or maximum!=0',
		    );
		    $style && $where['style'] = $style;
		}
		//判断是否选择筛选
		if(I('get.product_id'))
		{
			$product_id  = explode('-',I('get.product_id'));
			
			//选中
			$where_select_product['id'] = array('in',$product_id );
			$select_product = M('id_product')->where($where_select_product)->select();
			$this->select_product = $select_product;
			$this->select_product_coun = count($select_product) ;
			//选中 end	
			
			$where_product['product_id'] = array('in',$product_id);
			//dump($where_product);exit;
			$code_id = $id_product_codeDB->where($where_product)->field('id')->select();
			foreach($code_id as $k=>$v)
			{
				$code_list_id[$k] = $v['id'];	
			}
				if($code_list_id)
				{
					$where['code_id'] =array('in',$code_list_id);
				}
				else
				{
					$where['code_id'] =array('in','-1');
				}
		}
		elseif(I('get.catalog_id') )
		{
			$catalog_id  = explode('-',I('get.catalog_id'));
			//选中
			$where_select_catalog['id'] = array('in',$catalog_id );
			$select_catalog = M('id_catalog')->where($where_select_catalog)->select();
			$this->select_catalog = $select_catalog;
			$this->select_catalog_coun = count($catalog_id) ;
			//选中 end			
			$where_catalog['catalog_id'] = array('in',$catalog_id );
			$product_all_list=$id_productDB->where($where_catalog)->order('sort_id')->field('id')->select();
			foreach($product_all_list as $product_all_k=>$product_all_v)
			{
				$product_id[$product_all_k]= $product_all_v['id'];	
			}
				if($product_id)
				{
					$where_code['product_id'] =array('in',$product_id);
				}
				else
				{
					$where_code['product_id'] =array('in','-1');
				}
			$code_id = $id_product_codeDB->where($where_code)->field('id')->select();
			
			foreach($code_id as $k=>$v)
			{
				$code_list_id[$k] = $v['id'];	
			}
				if($code_list_id)
				{
					$where['code_id'] =array('in',$code_list_id);
				}
				else
				{
					$where['code_id'] =array('in','-1');
				}
			
		}
		//判断是否选择筛选 end
		
		//页面标题
		$title = get_stock_style($style);
		$style==0 && $title='所有';
		//dump($where);exit;
		//import('Org.Util.Page');// 导入分页类
		$count = $stock_model->where($where)->count();
		$Page = new \Think\Page1($count);// 实例化分页类 传入总记录数
		$nowPage = isset($_GET['p']) ? $_GET['p'] : 1;
		$show = $Page->show();// 分页显示输出

		$list = $stock_model->where($where)->page($nowPage.','.C('LISTROWS'))->order('id')->select();
		if( I('get.flag')=='set' )//套件
		{
			$id_product_code_handle=M('id_product_code');
			$id_relate_sku_handle=M('id_relate_sku');
			$come_from=M('id_come_from');
			foreach($list as $list_key => $list_value)
			{
				$sku_relate_data = $id_relate_sku_handle->where('product_sku_id='.$list_value['sku_id'])->select();
				foreach($sku_relate_data as $sku_relate_data_value)
				{
					$code_name=$id_product_code_handle->field('name,code')->where('id='.$sku_relate_data_value['product_code_id'])->find();
					$list[$list_key]['code_list'][]=array('code'=>$code_name['code'],'code_name'=>$code_name['name'],'number'=>$sku_relate_data_value['number']);
				}
			}
		}
		$this->code = $_GET['code'];
		$this->list = $list;
		$this->page_bar = $show;
		$this->style = $style;
		$this->flag = I('get.flag');
		$this->warn = I('get.warn');
		$this->title = $title;
		$this->param = array('style'=>$style,'flag'=>I('get.flag'),'warn'=>I('get.warn'),'p'=>I('get.p'));
		
    	$this->catalog_product_list(I('get.catalog_id') ,I('get.product_id'));
		$this->display('stock_list');
	}
	//产品 分类 产品 列表
	public function catalog_product_list($catalog_id,$product_id)
	{
		$id_catalogDB=M('id_catalog');                  //分类
		$id_product_codeDB=M('id_product_code');       //小产品
		$id_productDB=M('id_product');                //产品
		if($catalog_id ||  $product_id)
		{	
			if($catalog_id)
			{
				$product_list =$id_productDB->where("`catalog_id` =".$catalog_id)->order('sort_id')->field('id,name')->select();
			}
			else
			{
				$product_list =$id_productDB->where("1=1")->order('sort_id')->field('id,name')->select();
			}
			
			$catalog_all_id=$id_productDB->where('`is_work`= 1')->group('catalog_id')->getField('catalog_id',true);
			foreach($catalog_all_id as $k=>$v)
			{
				$catalog_list[$k]=$id_catalogDB->where('`id` = '.$v)->find();
			}
		}
		else
		{
			$catalog_all_id=$id_productDB->where('`is_work`= 1')->group('catalog_id')->getField('catalog_id',true);
			foreach($catalog_all_id as $k=>$v)
			{
				$catalog_list[$k]=$id_catalogDB->where('`id` = '.$v)->find();
			}
			
			$product_list =$id_productDB->where("1=1")->order('sort_id')->field('id,name')->select();
		}
		$this->catalog_id = $catalog_id;
		$this->product_id = $product_id;
		$this->product_list = $product_list;	
		$this->assign('catalog_list',$catalog_list);   //分类
	}
	//库存盘点录入
	public function product_stock_check_add()
	{
		$product_stock_checkDB=M('product_stock_check');
		$product_stockDB=M('product_stock');
		$product_stock_setDB=M('product_stock_set');
		$username = session('username');
		$this->stock_style=I('get.style');
		if(I('get.style')=="2")
		{
			$this->title="本地";
			$this->return_stock_style="stock_local";
		}
		elseif(I('get.style')=="1")
		{
			$this->title="FBA";
			$this->return_stock_style="stock_fba";
		}
		elseif(I('get.style')=="3")
		{
			$this->title="美国";
			$this->return_stock_style="stock_us";
		}
		if(I("post.code_name") ||I("post.sku"))
		{
			if(I("post.type")=='dp')
			{
				$product_stock_isset=$product_stockDB->where('`code_id` ='.I("post.code_name") .' and `style` = '.I('get.style'))->find();
				if($product_stock_isset)
				{
					$dd['number'] = I('post.number');
					$pr_st=$product_stockDB->where('`code_id` ='.I("post.code_name") .' and `style` = '.I('get.style'))->save($dd);
				}
				else
				{
					$no['code_id'] = I("post.code_name");
					$no['number'] = I("post.number");
					$no['style'] = I('get.style');
					$no['minimum'] = C('inventory_min');
					$no['maximum'] = C('inventory_max');
					$pr_st=$product_stockDB->add($no);	
				}
				$date['code_id'] = I("post.code_name");
			}
			elseif(I("post.type")=='tj')
			{
				$product_stock_isset=$product_stock_setDB->where('`sku_id` ='.I("post.sku") .' and `style` = '.I('get.style'))->find();
				if($product_stock_isset)
				{
					$dd['number'] = I('post.number');
					$pr_st=$product_stock_setDB->where('`sku_id` ='.I("post.sku") .' and `style` = '.I('get.style'))->save($dd);
					
				}
				else
				{
					$no['sku_id'] = I("post.sku");
					$no['number'] = I("post.number");
					$no['style'] = I('get.style');
					$no['minimum'] = C('inventory_min');
					$no['maximum'] = C('inventory_max');
					$pr_st=$product_stock_setDB->add($no);	
					
				}
					$date['sku_id'] = I("post.sku");
				
			}
				$date['number'] =  I('post.number') - $product_stock_isset['number'];
				$date['message'] = I('post.message');
				$date['style'] = I('get.style');
				$date['operator'] = $username;
				$date['date_time'] = time();
				
			$return=$product_stock_checkDB->add($date);
			if($return)
			{
				$this->success('库存盘点录入成功！！');
			}
			else
			{
				$this->error('库存盘点录入失败！！');	
			}
		}
		else
		{
			$this->assign("come_from",come_from_new());
			$this-> style = STYLE();
			$this->catalog = select_all();
			$this->display();
		}
	}
	//导入盘点
	public function upload_excel() {
		$style=I('post.style');
        header("Content-Type:text/html;charset=utf-8");
        $upload = new \Think\Upload(); // 实例化上传类
        $upload->maxSize = 3145728; // 设置附件上传大小
        $upload->exts = array('xls', 'xlsx'); // 设置附件上传类
		//设置附件上传根目录
		
		$upload->rootPath = './Public/check_num_execl/';

		//设置附件上传根目录 end		
        $upload->savePath = ''; // 设置附件上传目录
        // 上传文件
        $info = $upload->uploadOne($_FILES['file']);
		//判断完整路径
		$filename = './Public/check_num_execl/' . $info['savepath'] . $info['savename'];

		//判断路径 end			
		$exts = $info['ext'];
        if (!$info) 
		{// 上传错误提示错误信息
            $this->error($upload->getError());
        }
		else 
		{// 上传成功
        	$this->goods_import($filename,$exts,$style,2);                     //$style  判断execl类型
        }
    }
	//导入数据方法
    protected function goods_import($filename, $exts = 'xls', $style,$sheet=0) {      //$style  判断execl类型
        //导入PHPExcel类库，因为PHPExcel没有用命名空间，只能import导入
        import("Org.Util.PHPExcel");
        //创建PHPExcel对象，注意，不能少了\
        $PHPExcel = new \PHPExcel();
        //如果excel文件后缀名为.xls，导入这个类
		if ($exts == 'xls') {
            import("Org.Util.PHPExcel.Reader.Excel5");
            $PHPReader = new \PHPExcel_Reader_Excel5();
        } else if ($exts == 'xlsx') {
            import("Org.Util.PHPExcel.Reader.Excel2007");
            $PHPReader = new \PHPExcel_Reader_Excel2007();
        }
        //载入文件
        $PHPExcel = $PHPReader->load($filename);
        //获取表中的第一个工作表，如果要获取第二个，把0改为1，依次类推
		if($sheet-1>0)
		{
			for($i=0;$i<$sheet;$i++)
			{
				$currentSheet = $PHPExcel->getSheet($i);
				//获取总列数
				$allColumn = $currentSheet->getHighestColumn();
				//获取总行数
				$allRow = $currentSheet->getHighestRow();
				//循环获取表中的数据，$currentRow表示当前行，从哪行开始读取数据，索引值从0开始
				for ($currentRow = 1; $currentRow <= $allRow; $currentRow++) {
					//从哪列开始，A表示第一列
					for ($currentColumn = 'A'; $currentColumn <= $allColumn; $currentColumn++) {
						//数据坐标
						$address = $currentColumn . $currentRow;
						//读取到的数据，保存到数组$arr中
						$data[$i][$currentRow][$currentColumn] = $currentSheet->getCell($address)->getValue();
					}
				}				
			}
			$this->product_stock_check_import($data,$style);		
		}
		else
		{		
        	$currentSheet = $PHPExcel->getSheet(0);
			//获取总列数
			$allColumn = $currentSheet->getHighestColumn();
			//获取总行数
			$allRow = $currentSheet->getHighestRow();
			//循环获取表中的数据，$currentRow表示当前行，从哪行开始读取数据，索引值从0开始
			for ($currentRow = 1; $currentRow <= $allRow; $currentRow++) {
				//从哪列开始，A表示第一列
				for ($currentColumn = 'A'; $currentColumn <= $allColumn; $currentColumn++) {
					//数据坐标
					$address = $currentColumn . $currentRow;
					//读取到的数据，保存到数组$arr中
					$data[$currentRow][$currentColumn] = $currentSheet->getCell($address)->getValue();
				}
			}
			$this->product_stock_check_import($data,$style);
		}
        
    }
	//导入数据库  
	public function product_stock_check_import($data)
	{
		$product_stock_checkDB=M('product_stock_check');
		$product_stockDB=M('product_stock');
		$product_stock_setDB=M('product_stock_set');
		$id_product_skuDB = M('id_product_sku');
		$id_product_codeDB = M('id_product_code');
		$id_come_fromDB = M('id_come_from');
		$username = session('username');
		$stock_uptate_err_num=0;        //修改库存失败
		$stock_uptate_succ_num=0;        //修改库存成功
		$stock_err_num=0; 				//新增失败
		$stock_succ_num=0;        //新增库存成功

		$stock_uptate_err_num_sku=0;        //sku修改库存失败
		$stock_uptate_succ_num_sku=0;        //sku修改库存成功
		$stock_err_num_sku=0; 				//sku新增失败
		$stock_succ_num_sku=0;        //sku新增库存成功
		
		$code_no_num = 0;     //未找到code
		$sku_no_num = 0;     //未找到sku
		if($data)
		{
			foreach($data as $k => $v)
			{
				foreach($v as $kk=>$vv)
				{
					$data_count=count($v[1]);
					if($k == 1)  //单品库存
					{
						if($kk != 1)
						{
							$code_id=$id_product_codeDB->where('`code` ="'.$vv['A'] .'"')->getField('id');
							if($code_id)
							{
								if($product_stockDB->where('`code_id` ='.$code_id.' and `style`="2"')->find()) //a判断这个表是否有记录
								{
									if($vv['D'] !="")
									{
										$dd['number'] = $vv['D'];
										$dd['edit_name'] =$username ;
										$return = $product_stockDB->where('`code_id` ='.$code_id.' and `style`="2"')->save($dd); //修改内存表中记录
										if($return)
										{
											$new['number'] = $vv['D'] - $vv['C'];
											if($vv['E'])
											{
											  $new['message'] = $vv['E'];
											}
											$new['date_time'] = time();
											$new['style'] = 2;   
											$new['code_id'] = $code_id;
											$new['sku_id'] = 0;
											$new['operator'] =$username ;
											$product_stock_checkDB->add($new);//新增记录表中记录
											$stock_uptate_succ_num++;        //修改成功数量
										}
										elseif($return === false) 
										{
											$err[$stock_uptate_err_num] = $vv['A'];
											$stock_uptate_err_num++;  //修改失败数量
										}
									}
								}
								else
								{
									if($vv['C'] == 0)
									{
										$cc['code_id'] = $code_id;
										$cc['edit_name'] = 	$username;
										$cc['number'] = $vv['D'];
										$cc['style'] = 2;   
										$cc['minimum'] = C('inventory_min');
										$cc['maximum'] = C('inventory_max');
										$return = $product_stockDB->add($cc);
										if($return)
										{
											$new['number'] = $vv['D'] - $vv['C'];
											if($vv['E'])
											{
											  $new['message'] = $vv['E'];
											}
											$new['date_time'] = time();
											$new['style'] = 2;   
											$new['code_id'] = $code_id;
											$new['sku_id'] = 0;
											$new['operator'] =$username ;
											$product_stock_checkDB->add($new);
											$stock_succ_num++;             //新增成功数量
										}
										elseif($return === false)
										{
											$add_err[$stock_err_num] = $vv['A'];
											$stock_err_num++;				//新增失败数量
										}
									}
									else
									{
										$add_err[$stock_err_num] = $vv['A'];
										$stock_err_num++;					 //新增失败数量
									}
									
								}
								
							}
							else
							{
									$code_no[$code_no_num] = $vv['A'];
									$code_no_num++;
							}
						}
					}
					elseif($k == 0)  //套件
					{
						if($kk != 1)
						{
							$come_from_id = $id_come_fromDB ->where('`name` ="'.$vv['C'].'"') ->getField('id');
							$sku_id=$id_product_skuDB->where('`sku` ="'.$vv['A'] .'" and `come_from_id` ='.$come_from_id)->getField('id');
							if($sku_id)
							{
								if($product_stock_setDB->where('`sku_id` ='.$sku_id.' and `style`="2"')->find())
								{
									if($vv['E'] !="")
									{
										$dd['number'] = $vv['F'];
										$dd['edit_name'] =$username;
										$return = $product_stock_setDB->where('`sku_id` ='.$sku_id.' and `style`="2"')->save($dd);
										if($return === 1)
										{
											$new['number'] = $vv['F'] - $vv['E'];
											if($vv['G'])
											{
											  $new['message'] = $vv['G'];
											}
											$new['date_time'] = time();
											$new['style'] = 2;   
											$new['sku_id'] = $sku_id;
											$new['code_id'] = 0;
											$new['operator'] =$username ;
											$product_stock_checkDB->add($new);
											$stock_uptate_succ_num_sku++;
										}
										elseif($return === false)
										{
											$err_sku[$stock_uptate_err_num_sku] = $vv['A'];
											$stock_uptate_err_num_sku++;
										}
									}
								}
								else
								{
									if($vv['E'] == 0)
									{
										$cc['sku_id'] = $sku_id;
										$cc['edit_name'] = 	$username;
										$cc['number'] = $vv['F'];
										$cc['style'] = 2;   
										$cc['minimum'] = C('inventory_min');
										$cc['maximum'] = C('inventory_max');
										$return = $product_stock_setDB->add($cc);
										if($return)
										{
											$new['number'] = $vv['F'] - $vv['E'];
											if($vv['G'])
											{
											  $new['message'] = $vv['G'];
											}
											$new['date_time'] = time();
											$new['style'] = 2;   
											$new['sku_id'] = $sku_id;
											$new['code_id'] = 0;
											$new['operator'] =$username ;
											$product_stock_checkDB->add($new);
											$stock_succ_num_sku++;  //sku新增成功
										}
										elseif($return === false)
										{
											$add_err_sku[$stock_err_num_sku] = $vv['A']." + ".$vv['C'];
											$stock_err_num_sku++;
										}
									}
									else
									{
										$add_err_sku[$stock_err_num_sku] = $vv['A']." + ".$vv['C'];
										$stock_err_num_sku++;
									}
								}
							}
							else
							{
								$sku_no[$sku_no_num] = $vv['A']." + ".$vv['C'];
								$sku_no_num++;
							}
							
						}
					}
				}			
			}
			if($stock_uptate_err_num == 0 && $stock_err_num == 0 && $sku_no_num==0 && $code_no_num==0 && $stock_uptate_err_num_sku == 0 && $stock_err_num_sku == 0 )
			{
				$this->success('导入成功');
			}
			else
			{
				echo  $stock_uptate_succ_num.'条code修改成功'."<br>";
			
				echo  $stock_succ_num.'条code新增成功'."<br>";
				
				echo  $stock_uptate_err_num.'条code修改失败'."<br>";
				foreach($err as $err_k=>$err_v)
				{
					echo 	"<p style='margin-left: 20px;color: red;'>".$err_v."<p>";
				}
				
				echo  $stock_err_num.'条code新增失败'."<br>";
				foreach($add_err as $add_err_k=>$add_err_v)
				{
					echo 	"<p style='margin-left: 20px;color: red;'>".$add_err_v."<p>";
				}
				
				echo  $stock_uptate_succ_num_sku.'条sku修改成功'."<br>";
			
				echo  $stock_succ_num_sku.'条sku新增成功'."<br>";
				
				echo  $stock_uptate_err_num_sku.'条sku修改失败'."<br>";
				foreach($err_sku as $err_sku_k=>$err_sku_v)
				{
					echo 	"<p style='margin-left: 20px;color: red;'>".$err_sku_v."<p>";
				}
				
				echo  $stock_err_num_sku.'条sku新增失败'."<br>";
				foreach($add_err_sku as $add_err_sku_k=>$add_err_sku_v)
				{
					echo 	"<p style='margin-left: 20px;color: red;'>".$add_err_sku_v."<p>";
				}
				
				echo  $sku_no_num.'条SKU未找到'."<br>";
				foreach($sku_no as $sku_no_k=>$sku_no_v)
				{
					echo 	"<p style='margin-left: 20px;color: red;'>".$sku_no_v."<p>";
				}
				
				echo  $code_no_num.'条code未找到'."<br>";
				foreach($code_no as $code_no_k=>$code_no_v)
				{
					echo 	"<p style='margin-left: 20px;color: red;'>".$code_no_v."<p>";
				}
				exit;
			}
			
		}
		else
		{
			$this->error('导入失败');	
		}
	}
	
	//盘点列表
	public function product_stock_check_list()
	{
		$product_stock_checkDB = M('product_stock_check');
		$id_product_codeDB = M('id_product_code');
		$id_product_skuDB= M ('id_product_sku');
		$where['style'] =I('get.style');
		$this->style=I('get.style');
		if(I('get.style')=="2")
		{
			$this->title="本地";
			$this->return_stock_style="stock_local";
		}
		elseif(I('get.style')=="1")
		{
			$this->title="FBA";
			$this->return_stock_style="stock_fba";
		}
		elseif(I('get.style')=="3")
		{
			$this->title="美国";
			$this->return_stock_style="stock_us";
		}
		if(I('get.type'))
		{
			if(I('get.type') =="dp")
			{
				$where['sku_id'] =0;
				$this->tpltitle = '单品';
			}
			elseif(I('get.type') =="tj")
			{
				$where['code_id'] =0;
				$this->tpltitle = '套件';
			}
			$this->type = I('get.type');
		}
		else
		{
			$this->tpltitle = '全部';
		}
		
		//import('Org.Util.Page');// 导入分页类
		$coun=$product_stock_checkDB->where($where)->count();
		$Page       = new \Think\Page1($coun);// 实例化分页类 传入总记录数
		$nowPage = isset($_GET['p'])?$_GET['p']:1;
		$show       = $Page->show();// 分页显示输出
		$info = $product_stock_checkDB->where($where)->page($nowPage,C('LISTROWS'))->order('id desc')->select();
		
		if(I('get.type') =="dp")
		{
			foreach($info as $k=>$v)
			{
				if($v['code_id'] !=0)
				{
					$product_code=$id_product_codeDB->where('`id` ='.$v['code_id'])->field('code,name')->find();
					$info[$k]['name']="[".$product_code[code]."] ".$product_code[name];
				}
			}
		}
		elseif(I('get.type') =="tj")
		{
			foreach($info as $k=>$v)
			{
				if($v['sku_id'] !=0)
				{
					$product_sku=$id_product_skuDB->where('`id` ='.$v['sku_id'])->field('sku,name')->find();
					$info[$k]['name']="[".$product_sku[sku]."] ".$product_sku[name];
				}
			}
		}
		else
		{
			foreach($info as $k=>$v)
			{
				if($v['code_id'] !=0)
				{
					$product_code=$id_product_codeDB->where('`id` ='.$v['code_id'])->field('code,name')->find();
					$info[$k]['name']="[".$product_code[code]."] ".$product_code[name];
				}
				elseif($v['sku_id'] !=0)
				{
					$product_sku=$id_product_skuDB->where('`id` ='.$v['sku_id'])->field('sku,name')->find();
					$info[$k]['name']="[".$product_sku[sku]."] ".$product_sku[name];
				}
				else
				{
					$info[$k]['name']='出现错误';
				}
			}
		}
		$this->info =$info;
		$this->page =$show;
		$this->display();
	}
	//次品录入 
	public function product_stock_inforier_add()
	{
		$product_stock_inferiorDB=M('product_stock_inferior');
		$product_stock_inferior_detailDB=M('product_stock_inferior_detail');
		
		$username = session('username');
		$this->tpltitle = '全部';
		if(I('get.type') =="dp")
		{
			$where['sku_id'] =0;
			$this->tpltitle = '单品';
		}
		elseif(I('get.type') =="tj")
		{
			$where['code_id'] =0;
			$this->tpltitle = '套件';
		}
		if(I("post.code_name") ||I("post.sku"))
		{
			if(I("post.type")=='dp')
			{
				$number=$product_stock_inferiorDB->where('`code_id` ='.I("post.code_name"))->getField('number');
				if($number)
				{
					$dd['number'] = I('post.number');
					$pr_st=$product_stock_inferiorDB->where('`code_id` ='.I("post.code_name") .' and `style` = 1')->save($dd);
				}
				else
				{
					$no['code_id'] = I("post.code_name");
					$no['number'] = I("post.number");
					$pr_st=$product_stock_inferiorDB->add($no);	
				}
				$date['code_id'] = I("post.code_name");
			}
			elseif(I("post.type")=='tj')
			{
				$number=$product_stock_inferiorDB->where('`sku_id` ='.I("post.sku") )->getField('number');
				if($number)
				{
					$dd['number'] = I('post.number');
					$pr_st=$product_stock_inferiorDB->where('`sku_id` ='.I("post.sku"))->save($dd);
					
				}
				else
				{
					$no['sku_id'] = I("post.sku");
					$no['number'] = I("post.number");
					$pr_st=$product_stock_inferiorDB->add($no);	
					
				}
				$date['sku_id'] = I("post.sku");
				
			}
				$date['number'] =  I('post.number');
				$date['message'] = I('post.message');
				$date['operator'] = $username;
				$date['date_time'] = time();
				
			$return=$product_stock_inferior_detailDB->add($date);
			if($return)
			{
				$this->success('次品录入成功！！');
			}
			else
			{
				$this->error('次品录入失败！！');	
			}
		}
		else
		{
			$this->assign("come_from",come_from_new());
			$this-> style = STYLE();
			$this->catalog = select_all();
			$this->display();
		}	
	}
	
	//次品列表 
	public function product_stock_inforier_list()
	{
		$product_stock_checkDB = M('product_stock_check');
		$id_product_codeDB = M('id_product_code'); 
		$id_product_skuDB= M ('id_product_sku');
		$product_stock_inferiorDB=M('product_stock_inferior');
		$product_stock_inferior_detailDB=M('product_stock_inferior_detail');
		if(I('get.type'))
		{
			if(I('get.type') =="dp")
			{
				$where['sku_id'] =0;
				$this->tpltitle = '单品';
			}
			elseif(I('get.type') =="tj")
			{
				$where['code_id'] =0;
				$this->tpltitle = '套件';
			}
			$this->type = I('get.type');
		}
		else
		{
			$where='1=1';	
			$this->tpltitle = '全部';
		}
		
	//	import('Org.Util.Page');// 导入分页类
		$coun=$product_stock_inferior_detailDB->where($where)->count();
		$Page       =new \Think\Page1($coun);// 实例化分页类 传入总记录数
		$nowPage = isset($_GET['p'])?$_GET['p']:1;
		$show       = $Page->show();// 分页显示输出
		$info = $product_stock_inferior_detailDB->where($where)->page($nowPage,C('LISTROWS'))->select();
		
		if(I('get.type') =="dp")
		{
			foreach($info as $k=>$v)
			{
				if($v['code_id'] !=0)
				{
					$info[$k]['name']=$id_product_codeDB->where('`id` ='.$v['code_id'])->getField('code');
				}
			}
		}
		elseif(I('get.type') =="tj")
		{
			foreach($info as $k=>$v)
			{
				if($v['sku_id'] !=0)
				{
					$info[$k]['name']=$id_product_skuDB->where('`id` ='.$v['sku_id'])->getField('sku');
				}
			}
		}
		else
		{
			foreach($info as $k=>$v)
			{
				if($v['code_id'] !=0)
				{
					$info[$k]['name']=$id_product_codeDB->where('`id` ='.$v['code_id'])->getField('code');
				}
				elseif($v['sku_id'] !=0)
				{
					$info[$k]['name']=$id_product_skuDB->where('`id` ='.$v['sku_id'])->getField('sku');
				}
				else
				{
					$info[$k]['name']='出现错误';
				}
			}
		}
		$this -> info = $info;
		$this -> page = $show;
		$this -> display();
	}
	
	//库存预警修改
	public function product_warn_edit()
	{
		$productstocksetDB = M('product_stock_set');      //套件库存对比预警表
		$productstockDB=M('product_stock');      //单品库存对比预警表
		$idproductcodeDB=M('id_product_code');    //产品信息
		$username = session('username');    // 用户名
		if(I('get.style')=="2")
		{
			$return_stock_style="stock_local";
		}
		elseif(I('get.style')=="1")
		{
			$return_stock_style="stock_fba";
		}
		elseif(I('get.style')=="3")
		{
			$return_stock_style="stock_us";
		}
		elseif(I('get.style')=="0")
		{
			$return_stock_style="stock_all";
		}
		if($_POST['id']){
			if($_POST['flag']=='set'){//
				$date['edit_name']=$username;    // 用户ID
				$date['minimum']=$_POST['minimum'];
				$date['maximum']=$_POST['maximum'];
				$return=$productstocksetDB->where('`id`='.$_POST['id'])->save($date);
// 				dump($productstocksetDB->_sql());exit;
				if($return){
					$this->assign("jumpUrl",U('Admin/ProductManage/'.$return_stock_style.'/flag/set/warn/all',array('p'=>I('get.p'))));
					$this->success('库存预警修改成功！');
				}else{
					$this->error('库存预警修改失败!');
				}
			}else{
				$date['edit_name']=$username;    // 用户ID
				$date['minimum']=$_POST['minimum'];
				$date['maximum']=$_POST['maximum'];
				$return=$productstockDB->where('`id`='.$_POST['id'])->save($date);
				if($return){
					$this->assign("jumpUrl",U('Admin/ProductManage/'.$return_stock_style.'/flag/single/warn/all',array('p'=>I('get.p'))));
					$this->success('库存预警修改成功！');
				}else{
					$this->error('库存预警修改失败!');
				}
			}
	
		}else{
			if(!$_GET['id']){
				$this->error('出现错误!');
			}
			if($_GET['flag']=='set'){
				$info = $productstocksetDB->where('`id` = '.$_GET['id'])->find();
				$info['flag']=$_GET['flag'];
				$info['sku_name']=panduan_sku_name($info['sku_id']);
				$this->assign('tpltitle','修改套件');
			}else{
				$info = $productstockDB->where('`id` = '.$_GET['id'])->find();
				$idpr=$idproductcodeDB->where('`id`='.$info['code_id'])->find();
				$info['code_name']=$idpr['name'];
				$this->assign('tpltitle','修改单品');
			}
			$info['sty']=panduan_style($info['style']);//库存类型，中文
			$this->assign('info',$info);
			$this->style = I('get.style');//库存类型，数字id
			$this->display();
		}
	}
	public function shock_check_excel()
	{
		$style=I('get.style');
		//单件产品
		$title=array("产品代号","产品名","库存数量","实际数量","备注");
		$product_stock_handle=M('product_stock');
		$product_code_handle=M('id_product_code');
		$id_product_codeDB = M('id_product_code');
		$id_productDB = M('id_product');
		//判断是否选择筛选
		if(I('get.product_id'))
		{
			$product_id  = explode('-',I('get.product_id'));
			
			$where_product['product_id'] = array('in',$product_id);
			$code_id = $id_product_codeDB->where($where_product)->field('id')->select();
			foreach($code_id as $k=>$v)
			{
				$code_list_id[$k] = $v['id'];	
			}
				if($code_list_id)
				{
					$where['code_id'] =array('in',$code_list_id);
				}
				else
				{
					$where['code_id'] =array('in','-1');
				}
		}
		elseif(I('get.catalog_id') )
		{
			$catalog_id  = explode('-',I('get.catalog_id'));	
			$where_catalog['catalog_id'] = array('in',$catalog_id );
			$product_all_list=$id_productDB->where($where_catalog)->order('sort_id')->field('id')->select();
			foreach($product_all_list as $product_all_k=>$product_all_v)
			{
				$product_id[$product_all_k]= $product_all_v['id'];	
			}
				if($product_id)
				{
					$where_code['product_id'] =array('in',$product_id);
				}
				else
				{
					$where_code['product_id'] =array('in','-1');
				}
			$code_id = $id_product_codeDB->where($where_code)->field('id')->select();
			
			foreach($code_id as $k=>$v)
			{
				$code_list_id[$k] = $v['id'];	
			}
				if($code_list_id)
				{
					$where['code_id'] =array('in',$code_list_id);
				}
				else
				{
					$where['code_id'] =array('in','-1');
				}
			
		}
		//判断是否选择筛选 end
		$where['style'] = $style;
	//	dump($where);exit;
 		//判断是否选择筛选 end
		$product_stock_data=$product_stock_handle->where($where)->select();
		$k=1;
		foreach($product_stock_data as $product_stock_data_value)
		{
			
			$product_code_data=$product_code_handle->field('name,code')->where('id='.$product_stock_data_value['code_id'])->find();
			$info[$k]['code']=$product_code_data['code'];
			$info[$k]['name']=$product_code_data['name'];
			$info[$k]['number']=$product_stock_data_value['number'];			
			$k++;			
		}
		
		//套件产品
		$j=1;
		$product_stock_set_handle=M('product_stock_set');
		$product_stock_set_data=$product_stock_set_handle->where('style='.$style)->select();
		$set_title=array("产品sku","产品名","来源","组成","数量","实际数量","备注");
		$product_sku_handle=M("id_product_sku");
		$come_from_handle=M('id_come_from');
		$sku_code_handle=M('id_relate_sku');
		foreach($product_stock_set_data as $product_stock_set_data_value)
		{
			$product_sku_data=$product_sku_handle->where("id=".$product_stock_set_data_value[sku_id])->find();
			$set_info[$j]['sku']=$product_sku_data['sku'];
			$set_info[$j]['name']=$product_sku_data['name'];
			$come_from_data=$come_from_handle->where('id='.$product_sku_data[come_from_id])->find();
			$set_info[$j]['come_from']=$come_from_data['name'];
			$sku_code_data=$sku_code_handle->where("product_sku_id=".$product_stock_set_data_value[sku_id])->select();
			$code_str="";
			foreach($sku_code_data as $sku_code_data_value)
			{
				$code_data=$product_code_handle->where("id=".$sku_code_data_value['product_code_id'])->find();
				$code_str.=$code_data['code']." ".$code_data["name"]." ".$sku_code_data_value["number"]."\r\n";
			}
			$set_info[$j]['code_list']=$code_str;
			$set_info[$j]['number']=$product_stock_set_data_value['number'];
			$j++;
		}
		$sheet_more=array(array("data"=>$set_info,"file_name"=>"","title"=>$set_title,"sheetname"=>"套件"));
		exportExcel($info,'库存列表'."-".date('Y-m-d H:i:s',time()),$title,'单件',$sheet_more);
	}
	
	public function product_gift_product(){
		if(isset($_POST[catalog])){
			$products=M("id_product");
			$is_sku=false;
			$products_list_new='';
			$products_list=$products->where(array("catalog_id"=>$_POST[catalog]))->field("id,name")->select();
			if(!$products_list){
				$products_list=M("id_product_sku")->where(array("catalog_id"=>$_POST[catalog]))->field("id,name")->select();
				$is_sku=true;
			}
			if($_POST[style]){
				$products_list_new.="<input type='hidden' value='$is_sku' id='gift_is_sku'>";
				$products_list_new.="<select onchange='product_gift_code(this.value,\"$_POST[style]\")' name='gift_product_id'><option value='0'>--请选择产品--</option>";
			}else{
				$products_list_new.="<input type='hidden' value='$is_sku' id='is_sku'>";
				if($is_sku){
					$products_list_new.="<select onchange='product_gift_code(this.value)' name='product_set_id'><option value='0'>--请选择产品--</option>";
				}else{
					$products_list_new.="<select onchange='product_gift_code(this.value)' name='product_id'><option value='0'>--请选择产品--</option>";
				}
				
			}
			
			foreach ($products_list as $value){
				$products_list_new.="<option value='".$value[id]."'>".$value[name]."</option>";
			}
			$products_list_new.="</select>";
			echo $products_list_new;
		}
	}
	
	public function product_gift_code(){
		if(isset($_POST[product])){
			$code=M("id_product_code");
			$code_list_new='';
			$code_list=$code->where(array("product_id"=>$_POST[product]))->field("id,name")->select();
			if($_POST[style]){
				$code_list_new.="<select name='gift_code_id' ><option value='0'>--请选择Code--</option>";
			}else{
				$code_list_new.="<select name='code_id' ><option value='0'>--请选择Code--</option>";
			}
			foreach ($code_list as $value){
				$code_list_new.="<option value='".$value[id]."'>".$value[name]."</option>";
			}
			$code_list_new.="</select>";
			echo $code_list_new;
		}
	}
	public function product_stock_import()
	{
		$rootPath = './Public/product_stock_import/';
		if(!file_exists($rootPath)) mkdir($rootPath);
		$upload = new \Think\Upload();// 实例化上传类
		$upload->maxSize   =     0 ;// 设置附件上传大小
		$upload->exts      =     array('xlsl','xls');// 设置附件上传类型
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
				$sku = explode(',',$row[C]);
				if($sku[0])
				{
					$sku_id = D('id_product_sku')->where(array('sku'=>$sku[0]))->getField('id');
					if(!$sku_id)
					{
						$sku_id = D('id_product_sku')->where(array('sku'=>$sku[1]))->getField('id');
					}
				}
				$data['edit_name'] = "";
				$data['number'] = $row[D];
				$data['style'] = 2;
				$data['place'] = "";
				$data['minimum'] = 0;
				$data['maximum'] = 0;
				if($sku_id)
				{
					$map['product_sku_id'] = array('in',$sku_id);
					$product_code_id = D('id_relate_sku')->where($map)->limit(0,1)->select();
					if($product_code_id)
					{
						$data['code_id'] = $product_code_id[0][product_code_id];
						$product_stock_insert = D('product_stock')->add($data);
					}
				}
				else 
				{
					echo "此sku未关联code".$row[A]."->".$row[B]."->".$row[C]."->".$row[D]."<br>";
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
		}
	}
}
