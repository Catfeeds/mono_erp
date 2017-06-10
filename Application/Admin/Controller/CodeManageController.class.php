<?php
namespace Admin\Controller;
use Admin\Controller\CommonController;

class CodeManageController extends CommonController
{
	public function index()
	{
		$this->show("<div class='admin'>Here is CodeManageController</div>");		
	}
	
	//分类列表
	public function catalog()
	{
		$catalog_model = D('IdCatalog');
		//import('Org.Util.Page');// 导入分页类
		$list_coun=$catalog_model->count();
		$Page = new \Think\Page1($list_coun);// 实例化分页类 传入总记录数
		$nowPage = isset($_GET['p'])?$_GET['p']:1;
        $show = $Page->show();// 分页显示输出	
		
		$list = $catalog_model->order('sort_id asc')->page($nowPage,C('LISTROWS'))->select();
		$this->assign('page',$show);
		$this->assign('list',$list);
		$this->display();
	}
	//分类添加、修改
	public function catalog_edit()
	{
		$catalog_model = D('IdCatalog');
		
		if(IS_POST){
			$data['name'] = I('post.name');
			$data['sort_id'] = I('post.sort_id');
			$data['is_work'] = I('post.is_work');
			
			if(I('get.id'))//修改
			{
				$return = $catalog_model->where(array('id'=>I('get.id')))->save($data);
			}
			else//添加
			{
				//检查catalog的name值是否已存在
				$is_duplicate = $catalog_model->check_duplicate(array('name'=>I('post.name')));
				if($is_duplicate)
				{
					echo "<script>alert('该分类已存在');history.go(-1);</script>";
					exit;
				}
				$catalog_model->add($data);
			}
			redirect(__CONTROLLER__.'/catalog');
			exit;
		}
		
		if(I('get.id')){//修改
			$row = $catalog_model->where(array('id'=>I('get.id')))->find();
			$this->assign('row',$row);
		}
		if(!$row) $this->row = array('is_work'=>1);//默认生效
		$this->display();
	}
	//分类删除
	public function catalog_delete()
	{
		$catalog_model = D('IdCatalog');
		if(I('get.id')){
			$catalog_model->where(array('id'=>I('get.id')))->delete();
		}
		redirect(__CONTROLLER__.'/catalog');
		exit;
	}
	
	//产品列表
	public function product()
	{
		$product_model = D('IdProduct');
		$catalog_model = D('IdCatalog');
		
		$filter = array();
		$where = array();

		if(IS_POST)
		{
			$where['name'] = array('LIKE','%'.I('post.name').'%');
			$filter['name'] = I('post.name');
			unset($_GET['p']);
			unset($_GET['name']);
		}
        if( I('get.name') )
        {
        	$where['name'] = array('LIKE','%'.I('get.name').'%');
        	$filter['name'] = I('get.name');
        }
        
        // 导入分页类
        $list_coun = $product_model->where($where)->count();
        $Page = new \Think\Page1($list_coun,C('LISTROWS'));// 实例化分页类 传入总记录数
        if(IS_POST) $Page->parameter = $filter;
        $nowPage = isset($_GET['p'])?$_GET['p']:1;
        $show = $Page->show();// 分页显示输出
       
		$list = $product_model->where($where)->order('sort_id')->page($nowPage.','.C('LISTROWS'))->select();
		foreach ($list as $key=>$val)
		{
			$list[$key]['catalog_name'] = $catalog_model->where('`id` ='.$val['catalog_id'])->getField('name');
		}
		$this->list = $list;
		$this->page = $show;
		$this->filter = $filter;
		$this->display();
	}
	public function product_edit()
	{
		$catalog_model = D('IdCatalog');
		$product_model = D('IdProduct');
		
		$catalog_list = $catalog_model->order('sort_id asc')->getField('id,name');
		$this->catalog_list = $catalog_list;
		
		if(IS_POST){
			$data['name'] = I('post.name');
			$data['catalog_id'] = I('post.catalog_id');
			$data['sort_id'] = I('post.sort_id');
			$data['is_work'] = I('post.is_work');
			if(I('get.id'))//修改
			{
				$product_model->where(array('id'=>I('get.id')))->save($data);
			}
			else//添加
			{
				//检查product的name值是否已存在
// 				$is_duplicate = $product_model->check_duplicate(array('name'=>I('post.name')));
// 				if($is_duplicate)
// 				{
// 					echo "";         //<script>alert('该分类已存在');history.go(-1);
// 					exit;
// 				}
				$product_model->add($data);
			}
			redirect(__CONTROLLER__.'/product');
			exit;
		}
		
		if(I('get.id')){//修改
			$row = $product_model->where(array('id'=>I('get.id')))->find();
			$this->assign('row',$row);
		}
		if(!$row) $this->row = array('is_work'=>1);//默认生效
		$this->display();
	}
	public function product_delete()
	{
		$product_model = D('IdProduct');
		if(I('get.id')){
			$product_model->where(array('id'=>I('get.id')))->delete();
		}
		redirect(__CONTROLLER__.'/product');
		exit;
	}
	
	//产品属性
	public function attribute()
	{
		$attribute_model = D('IdProductAttribute');
		//import('Org.Util.Page');// 导入分页类
		$list_coun=$attribute_model->count();
		$Page       = new \Think\Page1($list_coun);// 实例化分页类 传入总记录数
		$nowPage = isset($_GET['p'])?$_GET['p']:1;
        $show       = $Page->show();// 分页显示输出	
		$list = $attribute_model->order('name')->page($nowPage.','.C('LISTROWS'))->select();
		$this->list = $list;
		$this->page = $show;
		$this->display();
	}
	public function attribute_edit()
	{
		$attribute_model = D('IdProductAttribute');
		if(IS_POST){
			$data['name'] = I('post.name');
			$data['value'] = I('post.value');
			if(I('get.id'))//修改
			{
				$attribute_model->where(array('id'=>I('get.id')))->save($data);
			}
			else//添加
			{
				$attribute_model->add($data);
			}
			redirect(__CONTROLLER__.'/attribute');
			exit;
		}
		
		if(I('get.id')){//修改
			$row = $attribute_model->where(array('id'=>I('get.id')))->find();
			$this->assign('row',$row);
		}
		
		$this->display();
	}
	public function attribute_delete()
	{
		$attribute_model = D('IdProductAttribute');
		if(I('get.id')){
			$attribute_model->where(array('id'=>I('get.id')))->delete();
		}
		redirect(__CONTROLLER__.'/attribute');
		exit;
	}	
	
	//产品Code
	public function code()
	{
		//table
		$catalog_model = D('IdCatalog');
		$product_model = D('IdProduct');
		$attribute_model = D('IdProductAttribute');
		$code_model = D('IdProductCode');
		//筛选，选择catalog
		$catalog = $catalog_model->getField('id,name');
		$this->catalog = $catalog;
		//import('Org.Util.Page');// 导入分页类
		$list_coun=$code_model->where('`is_work` =1')->count();
		$code_list = $code_model->where('`is_work` =1')->page($nowPage,C('LISTROWS'))->select();
		if(isset($_POST['sub']))
		{
			$code_list = $code_model->where(array('code'=>I('post.code'),'is_work'=>1))->page($nowPage,C('LISTROWS'))->select();
			$list_coun=$code_model->where(array('code'=>I('post.code'),'is_work'=>1))->count();
		}
		$Page       = new \Think\Page1($list_coun);// 实例化分页类 传入总记录数
		$nowPage = isset($_GET['p'])?$_GET['p']:1;
		$show    = $Page->show();// 分页显示输出
		foreach ($code_list as $key=>$val)
		{
			$product_row = $product_model->where('`id` = '.$val['product_id'])->find();
			$code_list[$key]['product_name'] = $product_row['name'];
			$code_list[$key]['catalog_name'] = $catalog_model->where(array('id'=>$product_row['catalog_id']))->getField('name');
			$code_list[$key]['size_name'] = $attribute_model->where('`id` = '.$val['size_id'])->getField('value');
			$code_list[$key]['color_name']  = $attribute_model->where('`id` = '.$val['color_id'])->getField('value');
		}
		if(I('get.sta') ==1)
		{
			$id_productDB=M('id_product');
			$catalog_id=session('catalog_id');
			$this->catalog_id = $catalog_id;
			$product_list = $id_productDB->where(array('catalog_id'=>$catalog_id))->select();
			$this->product_list = $product_list;
			$product_id=session('product_id');
			$this->product_id = $product_id;
		}
		$this->code_list = $code_list;
		$this->page = $show;
		$this->display();
	}
	//导出选中code
	public function code_execl_export()
	{
		$id_product_codeDB=M('id_product_code');       //小
		$id_relate_skuDB=M('id_relate_sku');       //code sku 关联表
		$id_product_skuDB=M('id_product_sku');     //sku 表
		$id_come_fromDB=M('id_come_from');
		if(I('post.check'))
		{
			$id=I('post.check');
				$aa = implode(',', $id); 
				$whereId = 'id in (' . $aa . ')'; 
			$code_id=$id_product_codeDB->where($whereId)->field('id,code,name')->select();
			$come_from = $id_come_fromDB->where('1=1')->select();
			foreach($code_id as $k=>$v)
			{
				$info[$k]['code']=$v['code'];
				$info[$k]['name']=$v['name'];
				$sku_id=$id_relate_skuDB->where('`product_code_id`='.$v['id'])->field('product_sku_id')->select();
				if($sku_id)
				{
					foreach($sku_id as $kk=>$vv)
					{
						$bb[$kk]= $sku_id[$kk]['product_sku_id'];
					}
					$sku_id=implode(',', $bb);
					$info_where['id'] = array('in',$sku_id);
					foreach($come_from as $come_from_v)
					{
						$info_where['come_from_id'] = $come_from_v['id'];
						$info[$k][$come_from_v['name']]=$id_product_skuDB->where($info_where)->getField('sku');
					}	
				}
				else
				{
					foreach($come_from as $come_from_v)
					{
						$info[$k][$come_from_v['name']]='';
					}
				}
			}
			$title=array('code','name');
			foreach($come_from as $k=>$v)
				{
					$title[$k+2]=$v['name'];
				}
			exportExcel($info,'code列表'."-".date('Y-m-d H:i:s',time()),$title);
		}
		else
		{
		$this->error('请选择要导出的code！！');	
		}
	}
	//导出筛选code
	public function code_screening_execl_export()
	{
		$id_product_codeDB=M('id_product_code');       //小产品
		$id_relate_skuDB=M('id_relate_sku');       //code sku 关联表
		$id_product_skuDB=M('id_product_sku');     //sku 表
		$id_come_fromDB=M('id_come_from');
		$id_productDB=M('id_product');
		if(I('post.catalog'))
		{
			$id_product = $id_productDB->where('`catalog_id` = '.I('post.catalog'))->field('id')->select();
			foreach($id_product as $k=>$v)
			{
				$pro_id[$k] = $v['id']; 
			}
			$where['product_id']=array('in',implode(',',$pro_id));	
		}
		if(I('post.product_id')!="-1" && I('post.product_id')!="")
		{
			$where['product_id']=I('post.product_id');	
		}
		$where['is_work'] = 1;
		$code_id=$id_product_codeDB->where($where)->field('id,code,name')->select();
		$come_from = $id_come_fromDB->where('1=1')->select();                     //获得所有地址
		foreach($code_id as $k=>$v)
		{
			$info[$k]['code']=$v['code'];
			$info[$k]['name']=$v['name'];
			$sku_id=$id_relate_skuDB->where('`product_code_id`='.$v['id'])->field('product_sku_id')->select();
			if($sku_id)
			{
				foreach($sku_id as $kk=>$vv)
				{
					$aa[$kk]= $sku_id[$kk]['product_sku_id'];
				}
				$sku_id=implode(',', $aa);
				$info_where['id'] = array('in',$sku_id);
				foreach($come_from as $come_from_v)
				{
					$info_where['come_from_id'] = $come_from_v['id'];
					$info[$k][$come_from_v['name']]=$id_product_skuDB->where($info_where)->getField('sku');
				}	
			}
			else
			{
				foreach($come_from as $come_from_v)
				{
					$info[$k][$come_from_vv['name']]='';
				}
			}
			
		}
		if(count($code_id)){
			$title=array('code','name');
			foreach($come_from as $k=>$come_from_v)
				{
					$title[$k+2]=$come_from_v['name'];
				}
			exportExcel($info,'code单品列表'."-".date('Y-m-d H:i:s',time()),$title);
		}
		else
		{
			$this->error('没有要导出的code！！');	
		}
	}	
	public function code_edit()
	{
		$catalog_model = D('IdCatalog');
		$product_model = D('IdProduct');
		$attribute_model = D('IdProductAttribute');
		$code_model = D('IdProductCode');
		$factory_model = M('factory');
		
		$catalog_list = $catalog_model->order('sort_id')->getField('id,name');
		$this->catalog_list = $catalog_list;
		$size_list = $attribute_model->where(array('name'=>'size'))->getField('id,value');
		$this->size_list = $size_list;
		$color_list = $attribute_model->where(array('name'=>'color'))->getField('id,value');
		$this->color_list = $color_list;
		$this->factory_list = $factory_model->select();
		
		if(IS_POST){
			
			$code_model->create();
			if(I('get.id'))//修改
			{
				$code_model->where(array('id'=>I('get.id')))->save();
			}
			else//添加
			{
				$info = $code_model->where('`code` ="'.I('post.code').'"')->find();
				if($info)
				{
					$this->error('此code已经存在');	
				}
				$code_model->add();
			}
			redirect(__CONTROLLER__.'/code/sta/1');
			exit;
		}
		
		if(I('get.id'))//修改
		{
			$row = $code_model->where(array('id'=>I('get.id')))->find();
			if($row['product_id'])
			{
				$product_row = $product_model->where(array('id'=>$row['product_id']))->find();
				$row['catalog_id'] = $product_row['catalog_id'];
				$product_list = $product_model->where(array('catalog_id'=>$product_row['catalog_id']))->order('sort_id')->getField("id,name");
				$product_option_html = '';
				foreach ($product_list as $key=>$val)
				{
					$selected = ($key==$row['product_id']) ? " selected='selected' " : "";
					$product_option_html .= "<option value='{$key}' $selected >{$val}</option>";
				}
				$this->product_option_html = $product_option_html;
			}
			
			$this->assign('row',$row);
		}
		if(!$row) $this->row = array('is_work'=>1);//默认生效
		$this->display();
	}
	public function code_delete()
	{
		$code_model = D('IdProductCode');
		if(I('get.id')){
			$code_model->where(array('id'=>I('get.id')))->delete();
		}
		redirect(__CONTROLLER__.'/code');
		exit;
	}
	public function code_have_sku()//所有包含某一code的sku列表
	{
		$relate_model = M('id_relate_sku');
		
		$single_where = array('a.product_code_id'=>I('get.id'), 'b.name'=>'');
		$set_where = array('a.product_code_id'=>I('get.id'),'b.name'=>array('neq',''));
		
		$this->single_list = $relate_model->alias('a')->join('INNER JOIN id_product_sku as b ON a.product_sku_id=b.id')->where($single_where)->field('a.id as aid,b.*')->select();
		$this->set_list = $relate_model->alias('a')->join('INNER JOIN id_product_sku as b ON a.product_sku_id=b.id')->where($set_where)->field('b.*')->select();
		
		$this->display();
	}
	
	//选择分类 出现产品 ajax
	public function ajax_get_catalog_product()
	{
		$code_model = D('IdProductCode');
		$product_model = D('IdProduct');
		if(I('post.catalog_id') == "")
		{
		$where['is_work']= 1;
		}
		else
		{
		$where['is_work']= 1;
		$where['catalog_id']= I('post.catalog_id');	
		}
		$product_list = $product_model->where($where)->order('sort_id')->field('id,name')->select();
		//$code_list = $code_model->getRelationInfo($where); 
		$this->ajaxReturn($product_list);
	}
	//选择分类或产品 出现小产品 ajax
	public function ajax_get_product_code()
	{
		$factory_model = M('factory');
	
		$code_model = D('IdProductCode');
		$id_product_codeDB = M('id_product_code');
		$id_productDB=M('id_product');
		if(I('post.catalog_id')){
			$where['is_work']= 1;
			$product_id = $id_productDB->where('`catalog_id` ='.I('post.catalog_id'))->field('id')->select();
			foreach($product_id as $k=>$v)
			{
				$pr_id[$k]=$v['id'];
			}
			$aa=implode(',',$pr_id);
			$where['product_id']  = array('in',$aa);
			session('catalog_id',I('post.catalog_id'));
		}
		else
		{
			if(I('post.product_id') == "")
			{
			$where['is_work']= 1;
			}
			else
			{
			$where['is_work']= 1;
			$where['product_id']= I('post.product_id');	
			session('product_id',I('post.product_id'));
			}
			//$product_list = $id_product_codeDB->where($where)->order('sort_id')->select();
		}
		$product_list = $code_model->getRelationInfo($where);
		$this->ajaxReturn($product_list);
		
	}
	
	//产品属性
	public function sku()
	{
		\Think\Hook::add('relation_edit_sql','Admin\\Behavior\\RelationEditSqlBehavior');//设置在ThinkPHP/Library/Think/Model/RelationModel
		$sku_model = D('IdProductSku');
		$come_from_model = M('id_come_from');
		
		if( I('post.sku') )
		{
			$sku = trim( I('post.sku') );
			$where = array('sku'=>$sku);
		}
		elseif( I('get.type') )
		{
			if(I('get.type')=="dp")
			{
				$where['name'] = '';
			}
			elseif(I('get.type')=="tj")
			{
				$where['name'] = array('neq','');
			}
			$this->type = I('get.type');
		}
		
		//import('Org.Util.Page');// 导入分页类
		$list_coun=$sku_model->where($where)->count();
		$Page = new \Think\Page1($list_coun);// 实例化分页类 传入总记录数
		$nowPage = isset($_GET['p'])?$_GET['p']:1;
        $show = $Page->show();// 分页显示输出	
		$list = $sku_model->relation(true)->where($where)->page($nowPage.','.C('LISTROWS'))->order('id desc')->select();
		$this->list = $list;
// 		dump($list);exit;
		$this->page = $show;
		$this->come_from_list = $come_from_model->select();
// 		print_r($list);exit;
		$this->display();
	}
	public function sku_edit()
	{
		$sku_model = D('IdProductSku');
		$come_from_model = M('id_come_from');
		$this->come_from_list = $come_from_model->select();
		if(IS_POST){
			$data['sku'] = I('post.sku');
			$data['name'] = I('post.name');
			$data['come_from_id'] = I('post.come_from_id');
			if(I('get.id'))//修改
			{
				$sku_model->where(array('id'=>I('get.id')))->save($data);
			}
			else//添加
			{
				$sku_model->add($data);
			}
			redirect(__CONTROLLER__.'/sku');
			exit;
		}
	
		if(I('get.id')){//修改
			$row = $sku_model->where(array('id'=>I('get.id')))->find();
			$this->assign('row',$row);
		}
	
		$this->display();
	}
	public function sku_delete()
	{
		$sku_model = D('IdProductSku');
		if(I('get.id')){
			$sku_model->where(array('id'=>I('get.id')))->delete();
		}
		redirect(__CONTROLLER__.'/sku');
		exit;
	}
	
	//产品code 关联sku
	public function code_sku()
	{
		$sku_model = D('IdProductSku');
		$come_from_model = M('id_come_from');
		$id_product_codeDB = M('id_product_code');
		$id_product_skuDB = M('id_product_sku');
		$id_relate_skuDB = M('id_relate_sku');
		if(I('get.id'))
		{
			$code= $id_product_codeDB->where('`id` = '.I('get.id'))->find();
			$this->code = $code;
		}
		else
		{
			$this->error('参数错误！');	
		}
		if(I('post.sku'))
		{
			$sku = $id_product_skuDB ->where('`sku` = "'.I('post.sku').'" and `come_from_id` = '.I('post.come_from_id'))->find();
			if($sku)
			{
				$date['product_sku_id'] = $sku['id'];
				$date['product_code_id'] = $code['id'];
				$date['number'] = 1;
				$return = $id_relate_skuDB-> add($date);
			}
			else
			{
				$sku['sku'] = I('post.sku');
				$sku['come_from_id'] = I('post.come_from_id');
				$sku_id = $id_product_skuDB->add($sku);
				if($sku_id)
				{
					$date['product_sku_id'] = $sku_id;
					$date['product_code_id'] = $code['id'];
					$date['number'] = 1;
					$return = $id_relate_skuDB-> add($date);
				}
				else
				{
					$this->error("新增sku失败,请重试");	
				}
				
			}	
			if($return )
			{
				$this->success("code关联sku成功");	
			}
			else
			{
				$this->error("code关联sku失败");	
			}	
		}
		else
		{
			$this->come_from_list = $come_from_model->select();
			$this->display();	
		}
	}
	//待关联sku`
	public function sku_lack_relate()
	{
		$product_model = D('order_plat_form_product');
		$code_model = D('id_product_code');
		
	//	import('Org.Util.Page');// 导入分页类
		$list_coun=count(  $product_model->relation(true)->where('`code_id`=0')->group('sku_id')->order('id')->select()  );
		$Page       = new \Think\Page1($list_coun);// 实例化分页类 传入总记录数
		$nowPage = isset($_GET['p'])?$_GET['p']:1;
        $show       = $Page->show();// 分页显示输出		
		$list = $product_model->relation(true)->where('`code_id`=0')->group('sku_id')->page($nowPage,C('LISTROWS'))->order('id')->select();
		$this->list = $list;
		$this->assign('page',$show);
		$this->display();
	}
	//[order_plat] 关联code 列表  sku_lack_relate点击 弹框
	public function code_list()
	{
		$id_relate_skuDB=M('id_relate_sku');
		$order_plat_form_productDB=M('order_plat_form_product');
		$id_catalogDB=M('id_catalog');                  //分类
		$id_product_codeDB=M('id_product_code');       //小产品
		$id_productDB=M('id_product');                //产品
		$product_model = D('order_plat_form_product');
		
		layout(false); // 临时关闭当前模板的布局功能
		$product_model = D('order_plat_form_product');
		$code_model = D('id_product_code');
		
		$catalog_id=$id_productDB->where('`is_work`= 1')->group('catalog_id')->getField('catalog_id',true);
		foreach($catalog_id as $k=>$v)
		{
			$catalog[$k]=$id_catalogDB->where('`id` = '.$v)->find();
		}
		if(IS_GET)
		{
			$id=I('get.id'); 
			$this->order_id = I('get.order_id');
			$this->type = I('get.type');
		}
		//$code_list = $code_model->field('id,code,name')->order('id')->select();
		$product_list =$id_productDB->where("1=1")->order('sort_id')->field('id,name')->select();
		$this->product_list = $product_list;
		//$this->code_list = $code_list;
		
		if(I('get.pro_id'))
		{	
			$product = $product_model->group('name,sku')->where('`id`='.I('get.pro_id'))->find();	
			$this->pro_name =$product['name'];
			$this->pro_sku =$product['sku'];
		}
		$this->assign('catalog',$catalog);   //分类
		$this->assign('id',$id);	
		$this->display();			
	}
	//关联code
	public function code_list_add()
	{
// 		dump(I('post.'));exit;
		$id_relate_skuDB=M('id_relate_sku');
		$order_plat_form_productDB=M('order_plat_form_product');
		$id_product_skuDB = M('id_product_sku');
		$id_catalogDB=M('id_catalog');                  //分类
		$id_product_codeDB=M('id_product_code');       //小产品
		$id_productDB=M('id_product');                //产品
		$product_model = D('order_plat_form_product');
		$code_model = D('id_product_code');
		if(IS_POST)
		{
			//修改关联，先恢复到未关联状态，再添加关联
			if( I('post.type')=='edit' )
			{
				//id_product_relate
				$id_relate_skuDB->where( array('product_sku_id'=>I('post.sku_id')) )->delete();
				
				//order_plat_form_product
				//限定audit订单
				$order_model = D('order_plat_form');
				$sub_sql = $order_model->status('audit')->field('id')->fetchSql()->select();
				//限定sku_id
				$temp_where = array('sku_id'=>I('post.sku_id'),'order_platform_id'=>array('EXP',"IN ($sub_sql)") );
				$order_id_list = $order_plat_form_productDB->where($temp_where)->group('order_platform_id')->select();
				unset($temp_where['id']);
				foreach ($order_id_list as $key=>$val)
				{
					//删除旧的product
					$temp_where['order_platform_id'] = $val['order_platform_id'];
					$order_plat_form_productDB->where($temp_where)->delete();
					//重置
					$val['code_id'] = 0;
					$order_plat_form_productDB->add($val);
				}
			}
			
// 			exit;
			$sku_id=I('post.sku_id');
			$code_num=I('post.code_num');
			$order_id = I('post.order_id');
			$code_list = I('post.code_list');//手动输入code
			$sku_name = I('post.sku_name');
// 			dump(I('post.'));exit;
			if($code_num =="" && $code_list=="")
			{
				$this->error('请选择要关联的code！！');
			}
			if($sku_name)
			{
				$sku_where['name'] = $sku_name;
				$aa=$id_product_skuDB->where('`id` = '.$sku_id)->save($sku_where);
			}
			if($code_list)
			{
				$code_array_err_num = 0;
				$code_array_succ_num = 0;
				
				$code = $id_product_codeDB->field('id')->where('`code`="'.$code_list.'"')->find();
				if($code)
				{
					$code_array_succ[$code_array_succ_num]= $code['id'];
					$code_array_succ_num++;
				}
				else
				{
					$this->error('未查到该code！！');
				}
				
				$date['product_sku_id'] = $sku_id;
				
				//添加 手动输入code
				$date['product_code_id']=$code_array_succ;
				$date['number'] = '1';
				$return_array_succ[]=$id_relate_skuDB->add($date);
				
				$code_list_all = $code_array_succ;
			}
			else
			{
				$date['product_sku_id'] = $sku_id;
				//添加 点击code
				foreach($code_num as $key=>$val)
				{
					if($val =='')
					{
						$val=1;
					}
					$date['product_code_id']=$key;
					$date['number'] = $val;
					
					
					$return[]=$id_relate_skuDB->add($date);
				}
				
				
				//[7077] =>0 code_num  转化为 [k] =>7077
				$code_num_num = 0;
				foreach($code_num as $key=>$val)
				{
					$code_num_zh[$code_num_num] = $key;
					$code_num_num++;
				}
					
				$code_list_all = $code_num_zh;
			}
			
			//限定只修改 未审核订单 中的产品
			$order_model = D('order_plat_form');
			$sub_sql = $order_model->status('audit')->field('id')->fetchSql()->select();
			$temp_where = array('sku_id'=>$sku_id,'order_platform_id'=>array('EXP',"IN ($sub_sql)") );			
			$sku_coun=$order_plat_form_productDB->where( $temp_where )->select();

		//	dump($sku_coun);exit;
// 			dump($order_plat_form_productDB->_sql());exit;
			$order_err_num = 0;
			foreach($sku_coun as $k=>$v)
			{
				$product_id=$sku_coun[$k]['id'];
				$da['order_platform_id']=$sku_coun[$k]['order_platform_id'];
				$da['name']=$sku_coun[$k]['name'];
				$da['sku_id']=$sku_id;
				$da['sku']=$sku_coun[$k]['sku'];
				$da['price']=$sku_coun[$k]['price'];
				$da['number']=$sku_coun[$k]['number'];
				$da['status']=$sku_coun[$k]['status'];
				foreach($code_list_all as $kk=>$vv)
				{
					$da['code_id']=$vv;
					$return_product=$order_plat_form_productDB->add($da);
					if(!$return_product)
					{
						$err[$order_err_num] = $vv;
						$order_err_num ++;
					}
				}
				if($order_err_num==0)
				{
					$return_delete=$order_plat_form_productDB->where('`id` ='.$product_id)->delete();
				}
			}
			if($order_err_num==0)
			{
				$this->success('关联code添加成功!');
				
				//order_plat页面
				if(I('post.order_id'))
				{
					echo "<script>window.parent.order_refresh(".I('post.order_id').");</script>";
					exit;
				}
				echo '
					<script>
						var index = parent.layer.getFrameIndex(window.name); //获取窗口索引
						//关闭iframe
						function zz(val){
							parent.$("#conditions").val("123");
							parent.layer.close(index);
							}
							setTimeout(zz,1000);	
						</script>
				';
			}
			else
			{
				echo '关联code添加失败！<br> 失败列表为：<br>';
				foreach($err as $err_k=>$err_v)
				{
					echo $err_v;	
				}
			}
		}
	}
	
	//
	public function ajax_sku_add_relate()
	{
		$relate_model = D('id_relate_sku');
		$product_model = D('order_plat_form_product');
		
		//添加relate
		$data = array();
		$data['product_code_id'] = I('post.product_code_id');	
		$data['product_sku_id'] = I('post.product_sku_id');
		$data['number'] = I('post.number');
		if(I('post.relate_id'))//修改
		{
			$relate_id = I('post.relate_id');
			$relate_model->where(array('id'=>$relate_id))->save($data);
		}
		else//添加
		{
			$relate_id = $relate_model->add($data);
		}
		
		//order_plat_form_product表中添加code
		$product_model->where(array('id'=>I('post.product_id')))->save(array('code_id'=>I('post.product_code_id')));
		
		echo $relate_id;
	}
	
	//产品sku关联code
	public function relate()
	{
		$relate_model = D('IdRelateSku');
		$count =  $relate_model->count();
		$Page = new \Think\Page($count,C('LISTROWS'));
		$list = $relate_model->relation(true)->order('product_sku_id')->limit($Page->firstRow.','.$Page->listRows)->select();
		$this->list = $list;
		$show = $Page->show_pintuer();//分页显示输出
		$this->show = $show;
		$this->display();
	}
	//关联code 以及修改
	public function relate_edit()
	{
		$relate_model = D('IdRelateSku');
		$sku_model = D('IdProductSku');
		$code_model = D('IdProductCode');
		$id_catalogDB=M('id_catalog');                  //分类
		$id_product_codeDB=M('id_product_code');       //小产品
		$id_productDB=M('id_product');                //产品

// 		$sku_list = $sku_model->getField("id,sku,come_from_id");//由于关联列表取消，不再有直接添加关联功能，不需要选择sku。而且现在sku数量过大，引起页面严重卡顿。
// 		$this->sku_list = $sku_list;
		
		$catalog=$id_catalogDB->where('1=1')->select();
		$code_list = $code_model->field('id,code,name')->order('id')->select();
		$product_list =$id_productDB->where('1=1')->order('sort_id')->field('id,name')->select();
// 		dump($catalog);
// 		dump($code_list);
// 		dump($product_list);exit;
		/*
		$this->product_list = $product_list;    //大产品
		$this->code_list = $code_list; 			//小产品
		$this->assign('catalog',$catalog);   //分类*/
		if(IS_POST){
			$relate_model->create();
// 			dump($relate_model);exit;
			if(I('get.id'))//修改
			{
				$relate_model->where(array('id'=>I('get.id')))->save();
			}
			else if(I('post.relate_id'))//修改
			{
				$relate_model->where(array('id'=>I('post.relate_id')))->save();
			}
			else{//添加
				
				$relate_model->add();
			}
			
			if(I('post.sku_id')||I('post.relate_id'))
			{
				redirect(__CONTROLLER__.'/sku');
			}else{
				redirect(__CONTROLLER__.'/relate');
			}
			
			exit;
		}
		
		if(I('get.')){
// 			if(I('get.id')){//修改
// 				$row = $relate_model->relation(true)->where(array('id'=>I('get.id')))->find();
// 				$this->assign('tpltitle',"修改");
// 				$code_list = $code_model->where('`product_id` = '.$row['code_info']['product_id'])->field('id,code,name')->order('id')->select();
// 				$product_row = $id_productDB->where(array('id'=>$row['code_info']['product_id']))->field('catalog_id')->find();
// 				$row['code_info']['catalog_id'] = $product_row['catalog_id'];
// 				$product_list = $id_productDB->where(array('catalog_id'=>$product_row['catalog_id']))->order('sort_id')->field('id,name')->select();
// 				$this->assign('row',$row);
// 				$this->assign('catalog',$catalog);   //分类
// 				$this->product_list = $product_list;    //大产品
// 				$this->code_list = $code_list; 			//小产品
// 			}
			if(I('get.sku_id')){//操作：添加； 来自：sku页； 要求：sku固定选中，完成后返回sku页
				$row['product_sku_id'] = I('get.sku_id');
				$temp = $sku_model->where( array('id'=>I('get.sku_id')) )->find();
				$this->sku = $temp['sku'];
				$this->tpltitle ="添加";
				$this->row = $row;
				$this->assign('catalog',$catalog);   //分类
			}
			if(I('get.relate_id')){//操作：修改； 来自：sku页； 要求：sku和code固定选中，完成后返回sku页
				$row = $relate_model->relation(true)->where(array('id'=>I('get.relate_id')))->find();
				$temp = $sku_model->where( array('id'=>$row['product_sku_id']) )->find();
				$this->sku = $temp['sku'];
				$temp = $id_productDB->where( array('id'=>$row['code_info']['product_id']) )->find();
				$row['code_info']['catalog_id'] = $temp['catalog_id'];
// 				dump($row);exit;
				$this->assign('tpltitle',"修改");
				$this->assign('row',$row);
				$this->product_list = $product_list;    //大产品
				$this->code_list = $code_list; 			//小产品
				$this->assign('catalog',$catalog);   //分类
				I('get.relate_id');
			}
		}
// 		else
// 		{
// 			$this->product_list = $product_list;    //大产品
// 			$this->code_list = $code_list; 			//小产品
// 			$this->assign('catalog',$catalog);   //分类
// 			$this->assign('tpltitle',"添加");
// 		}
		
		$this->display();
	}
	public function relate_delete()
	{
		$relate_model = D('IdRelateSku');
		if(I('get.id')){
			$relate_model->where(array('id'=>I('get.id')))->delete();
			redirect(__CONTROLLER__.'/relate');
		}
		else if(I('get.relate_id')){//来自sku页的请求
			$relate_model->where(array('id'=>I('get.relate_id')))->delete();
			redirect(__CONTROLLER__.'/sku');
		}
		exit;
	}
	public function ajax_get_relate()//relate_edit.html中用。用于避免添加 sku与code重复的relation
	{
		$relate_model = D('IdRelateSku');
		$row = $relate_model->field('id,number')->where(array('product_code_id'=>I('post.code_id'),'product_sku_id'=>I('post.sku_id')))->find();
// 		trace($row,'ajax');
		$this->ajaxReturn($row);
	}
	
	//导入Excel页面Eccel
	public function import_excel()
	{
		$this->display();
	}
	//style 判断类型
	//style  1 code的表
	//       2 sku单件表
	//       3 sku套件表	
	public function upload_excel() {
		$style=I('post.style');
        header("Content-Type:text/html;charset=utf-8");
        $upload = new \Think\Upload(); // 实例化上传类
        $upload->maxSize = 3145728; // 设置附件上传大小
        $upload->exts = array('xls', 'xlsx'); // 设置附件上传类
		//设置附件上传根目录
		if($style==2)
		{
			$upload->rootPath = './Public/sku_dp_excel/';
		}
		elseif($style==3)
		{
			$upload->rootPath = './Public/sku_tj_excel/';
		}
		elseif ($style==4)
		{
			$upload->rootPath = './Public/product_excel/';
		}
		else
		{
			$upload->rootPath = './Public/product_excel/';
		}
		//设置附件上传根目录 end		
        $upload->savePath = ''; // 设置附件上传目录
        // 上传文件
        $info = $upload->uploadOne($_FILES['file']);
		//判断完整路径
		if($style==2)
		{
			$filename = './Public/sku_dp_excel/' . $info['savepath'] . $info['savename'];
		}
		elseif($style==3)
		{
			$filename = './Public/sku_tj_excel/' . $info['savepath'] . $info['savename'];
		}
		elseif($style==4)
		{
			$filename = './Public/product_excel/' . $info['savepath'] . $info['savename'];
		}
		else
		{
			$filename = './Public/product_excel/' . $info['savepath'] . $info['savename'];
		}
		//判断路径 end			
		$exts = $info['ext'];
        if (!$info) {// 上传错误提示错误信息
            $this->error($upload->getError());
        } else {// 上传成功
        	if($style==4)
        	{
        		$log=$this->product_import($filename);
        		dump($log);
//         		$this->assign("log",$log);
//         		$this->display("product_import");
        	}
        	else
        	{
           		 $this->goods_import($filename, $exts,$style);                     //$style  判断execl类型
        	}
        }
    }
	//导入数据方法
    protected function goods_import($filename, $exts = 'xls', $style) {      //$style  判断execl类型
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
        $this->save_import($data,$style);
    }
	//保存导入数据
	//style  1 code的表
	//       2 sku单件表
	//       3 sku套件表
    public function save_import($data,$style) {                                   //$style  判断execl类型
		$id_product_codeDB=M('id_product_code');    //code表
		$id_product_skuDB=M('id_product_sku');      //sku表
		$id_relate_skuDB=M('id_relate_sku');        //code与sku关联表
		$id_come_fromDB= M('id_come_from');			//来源表
		$order_plat_form_productDB=M('order_plat_form_product');  //订单表
		if($style == 2){
			$num_save=0;   //修改数
			$num_no = 0;   //未变动数
			$num_add =0;   //新增数
			$num_out =0;   //失败数
			$code_err_num=0;        //新增sku失败
			$data_count=count($data[1]);
			
			foreach ($data as $k => $v) {
				if($k != 1)      //忽略第一行
				{
					$code_id=$id_product_codeDB->where('`code` ='."'".$v[A]."'".'and `name`='."'".$v[B]."'")->getField('id'); 
					//判断是否存在code
					if($code_id)
					{
						$sku_id = $id_relate_skuDB->where('`product_code_id` = '.$code_id)->field('product_sku_id')->select();//获得sku
						for($i=2;$i<$data_count;$i++)       //忽略前两列
						{
							$aa=chr(65+$i);                 //转换字母
							$come_from_id=$id_come_fromDB->where('`name` = '."'".$data[1][$aa]."'")->getField('id');
							foreach($sku_id as $kk=>$vv)
							{
								if($come_from_id)
								{
									$info=$id_product_skuDB->where('`come_from_id` ='."'".$come_from_id."'".'and `id`='.$vv['product_sku_id'])->find();								//判断是否存在sku
									if($info)
									{
										$date['sku']=$v[$aa];
										$date['come_from_id'] = $come_from_id;
										$result_save = $id_product_skuDB->where('`come_from_id` ='."'".$come_from_id."'".'and `id`='.$vv['product_sku_id'])->save($date);
										if($result_save == 1)
										{
										   $num_save++;				 //已修改
										}
										else if($result_save == 0)
										{
											$num_no++;	              //未修改
										}
										else
										{
											$num_out++;	
											$error[$num_out] = $aa.$k;
										}
									}
								}
								else
								{
								$this->error($aa.'1 列 <strong>'.$data[1][$aa].'</strong> 地址不存在 ！');	
								}
							}
								if(!$info)
								{
									$date['sku']=$v[$aa];
									$date['come_from_id'] = $come_from_id;
									$sku_add = $id_product_skuDB->add($date);
									$val['product_sku_id'] = $sku_add;
									$val['product_code_id'] =  $code_id;  
									$val['number'] =  1;  
									$relate_add = $id_relate_skuDB->add($val);
									if($relate_add)
									{
									   $num_add++;
									}
									else
									{
										$num_out++;		
									}
								}
						}
					}
					else
					{
						$code_err_num++;
						$code_err[$code_err_num] = $k;
					}
				}
			}
			if($error || $code_err)
			{
				print_r("<h1>code查询失败个数为 ".$code_err_num.'</h1>');
				
				foreach($code_err as $v)
				{
					print_r("<br />失败行数为 ".$v."<br />");
				}
				print_r("<h1>code与sku关联失败个数为 ".$num_out.'</h1>');
				foreach($error as $v)
				{
					print_r("<br />code与sku关联失败行数为 ".$v."<br />");
				}
				
			}
			else
			{
			$this->success('导入成功！！<br /> 修改'.$num_save.'个 <br /> 新增'.$num_add.'个 <br /> 未变动'.$num_no++.'个','',4);
			}
		}
		if($style == 5)
		{	//dump($data);exit;
			$save_num=0;
			$save_error_num = 0;
			$add_num = 0;
			$add_error_num =0;
			$ok_num =0;
			foreach ($data as $k => $v) 
			{
				if($k != 1)      //忽略第一行
				{
					if($v['A'] !="" && $v['B']!="" && $v['C']!="")
					{
						$where['country'] = strtolower($v["A"]);
						$where['city_full'] = strtolower($v["B"]);
						$result = M('country_city')->where($where)->find();
						if($result)
						{
							$date['city'] = strtoupper($v['C']);
							$save = M('country_city')->where('`id` ='.$result['id'])->save($date);
							if($save)
							{
								$save_num++;	
							}
							elseif($save===0)
							{
								$ok_num++;
							}
							else
							{
								$save_error_num++;
							}
						}
						else
						{
							$where['city'] = strtoupper($v['C']);
							$add = M('country_city')->add($where);
							if($add)
							{
								$add_num++;
							}
							else
							{
								$add_error_num++;
							}
							
						}
					}
					
					
				}
			}
			echo '成功修改的个数 ：'.$save_num.'<br>';
			echo '未修改的个数 ：'.$ok_num.'<br>';
			echo '修改失败的个数 ：'.$save_error_num.'<br>';
			echo '添加成功的个数 ：'.$add_num.'<br>';
			echo '添加失败的个数 ：'.$add_error_num.'<br>';
			exit;
			
		}
		if($style == 3){
			$sku_err_num=0;        //新增sku失败
			$sku_succ_num=0;        //新增sku成功
			$data_count=count($data[1]);
			foreach ($data as $k => $v) {
				if($k != 1){
					$date_sku['name'] = $v['A'];
					$date_sku['sku'] = $v['B'];
					$come_from_id=$id_come_fromDB->where('`name` = '."'".$v['C']."'")->getField('id');
					$product_sku_info = $id_product_skuDB->where('`come_from_id` ='.$come_from_id .' and `sku` = '.'"'.$v['B'].'"')->find();
					if($product_sku_info)
					{
						if($come_from_id)
						{
							if($product_sku_info['id'])
							{
								$date['product_sku_id'] = $product_sku_info['id'] ;
								for($i=3;$i<$data_count;$i++)
								{
									$aa=chr(65+$i);                 //转换字母
									if($v[$aa]!="")
									{
										$code_num=explode(',',$v[$aa]);        //[0] => 土豪金枕套 M blue [1] => 2 
										$code_id=$id_product_codeDB->where('`code` = '."'".$code_num[0]."'")->getField('id');
										if($code_id)         //没有此code
										{
											$relate_sku_list = $id_relate_skuDB->where('`product_code_id` = '.$code_id .' and `product_sku_id`='.$product_sku_info['id'])->find();
											if($relate_sku_list)
											{
												$relate_sku_list_date['number'] = $code_num[1];
												$result = $id_relate_skuDB->where('`product_code_id` = '.$code_id .' and `product_sku_id`='.$product_sku_info['id'])->save($relate_sku_list_date);
											}
											else
											{
												$date['product_code_id']=$code_id;
												$date['number'] = $code_num[1];
												$result = $id_relate_skuDB->add($date); 
											}
											if($result)
											{
												$num_add++;	     //新增数
											}
											else
											{
												$num_out++;      //失败数
												$error_relate[$num_out] = $aa.$k;
											}
										 }
										 else
										 {
											 $num_out++;      //失败数
											 $error_relate[$num_out] = $aa.$k;
										  }
									}
								}
							}
							else
							{
								$sku_err_num++;				//sku新增失败数
								$error[$sku_err_num] = $k;
							}
						}
						else
						{
							$this->error($k.'行 来源不存在！！','',3);
						}
					}
					else
					{
						if($come_from_id)
						{	
							$sku_succ_num++;
							$date_sku['come_from_id'] = $come_from_id;
							$product_sku_id = $id_product_skuDB->add($date_sku);         //新增sku
							if($product_sku_id)
							{
								$date['product_sku_id'] = $product_sku_id ;
								for($i=3;$i<$data_count;$i++)
								{
									$aa=chr(65+$i);                 //转换字母
									if($v[$aa]!="")
									{
										$code_num=explode(',',$v[$aa]);        //[0] => 土豪金枕套 M blue [1] => 2 
										$code_id=$id_product_codeDB->where('`code` = '."'".$code_num[0]."'")->getField('id');
										if($code_id)         //没有此code
										{
											$date['product_code_id']=$code_id;
											$date['number'] = $code_num[1];
											$result = $id_relate_skuDB->add($date); 
											if($result)
											{
												$num_add++;	     //新增数
											}
											else
											{
												$num_out++;      //失败数
												$error_relate[$num_out] = $aa.$k;
											}
										 }
										 else
										 {
											 $num_out++;      //失败数
											 $error_relate[$num_out] = $aa.$k;
										  }
									}
								}
							}
							else
							{
								$sku_err_num++;				//sku新增失败数
								$error[$sku_err_num] = $k;
							}
						}
						else
						{
							$this->error($k.'行 来源不存在！！','',3);
						}
					//
					}
				}
			}
			if($error || $error_relate)
			{
				print_r("<h1>sku失败数为 ".$sku_err_num."</h1>");
				foreach($error as $v)
				{
					print_r("<br />失败行数为 ".$v."<br />");
				}
				print_r("<h1>sku与code关联失败数为 ".$num_out."</h1> 请查询有没有这个code<br />");
				foreach($error_relate as $v)
				{
					print_r("<br />失败行数为 ".$v."<br />");
				}
			}
			else
			{
			$this->success('导入成功！！<br /> 新增sku数 ：'.$sku_succ_num.'<br />新增sku与code关联数 ：'.$num_add,'',4);
			}
		}
        
    }
    
    public $platStyle = array();
    protected function product_import($filename)
    {
    	vendor('PHPExcel.SimplePHPExcel');
    	$callback=function($row,$this=null)
    	{
    		
    		$log = array();
    		
    		if($row["A"]=="分类名")  //在第一行获取所有要管理的国家或平台
    		{
    			$come_from_handle = M("id_come_from");
    			$platArr=array();
    			foreach ($row as $key=>$value)
    			{
    				if($key > "K")
    				{
    					$come_from_isset = $come_from_handle->field('id')->where("name='".$value."'")->find();
    					if(!$come_from_isset)
    					{
    						$log['fatal_error']="平台".$value."不存在";//当平台不存在时，致命错误
    					}
    					else
    					{
    						$platArr[$key]=$come_from_isset['id'];
    					}
    				}
    			}
    			$this->platStyle = $platArr;
    		}
    		else
    		{
    			$catalog_handle=M("id_catalog");
    			//获取分类id或新加分类
	    		if($row["A"]!="")
	    		{	    			
	    			$this->activeCatalog=$row["A"];
	    			$catalog_data=$catalog_handle->field('id')->where("name='".$this->activeCatalog."'")->find();
	    			if($catalog_data)
	    			{
	    				$this->activeCatalogId=$catalog_data['id'];
	    			}
	    			else
	    			{
	    				//新加分类
	    				$catalog_add['name']=$this->activeCatalog;
	    				$catalog_add['is_work']=1;
	    				$catalog_add['sort_id']=1;
	    				$this->activeCatalogId=$catalog_handle->add($catalog_add);
	    				$log['A']="新加分类：".$this->activeCatalog;
	    			}
	    		}
	    		//获取产品id或新加产品
	    		if($row["B"]!="")
	    		{
	    			$products_handle=M("id_product");
	    			$this->activeProduct=$row["B"];
	    			$product_data=$products_handle->field('id')->where("name='".$this->activeProduct."'")->find();	    			
	    			if($product_data)
	    			{
	    				$this->activeProductId=$product_data['id'];
	    				//判断产品是否属于该分类
	    				$product_is_this_catalog=$products_handle->where('id='.$product_data['id']." and catalog_id=".$this->activeCatalogId)->find();
	    				
	    			}
	    			else 
	    			{
	    				//新加产品
	    				if(isset($this->activeCatalog))
	    				{
	    					$product_add['name']=$this->activeProduct;
	    					$product_add['catalog_id']=$this->activeCatalogId;
	    					$product_add['is_work']=1;
	    					$product_add['sort_id']=1;
	    					$this->activeProductId=$products_handle->add($product_add);
	    					$log['B']="新加产品：".$this->activeProduct;
	    				}
	    				else
	    				{
	    					$log['fatal_error']="新加产品时必须填写分类名称";//当不存在分类时，返回致命错误	    
	    				}
	    			}
	    			unset($products_handle);
	    		}
	    		
	    		if($row["H"]!="")
	    		{
	    			// 添加或修改产品信息
	    			// 添加关联信息
	    			$product_color=$row["C"];
	    			$product_size=$row["D"];
	    			$product_chines_description=$row["E"];
	    			$product_factory=$row["F"];
	    			$product_price=(float)$row["G"];
	    			$product_code=$row["H"];
	    			$product_number=(int)$row["I"];
	    			$product_weight=(float)$row["J"];
	    			//判断工厂是否存在
	    			$factory_handle=M('factory');
	    			$factory_data=$factory_handle->field('id')->where("name='".$product_factory."'")->find();
	    			if($factory_data)
	    			{	    				
	    				$product_factory_id=$factory_data[id];//工厂id
	    				
		    			if(!isset($this->activeProduct))
		    			{
		    				$log['fatal_error']="产品名不能为空";//当不存在产品时，返回致命错误
		    			}
		    			else
		    			{	 	
		    				
			    			//实例化数据库对象
			    			$product_attribute_handle=M("id_product_attribute");
			    			$product_code_handle=M("id_product_code");
			    			$product_sku_handle=M('id_product_sku');
			    			$relate_sku_handle=M('id_relate_sku');
			    			
			    			//获取颜色id或添加颜色
			    			if($product_color!="")
			    			{		    				
				    			$product_color_data=$product_attribute_handle->field('id')->where("name='color' and value='".$product_color."'")->find();
				    			if(!$product_color_data)
				    			{
				    				$product_color_add['value']=$product_color;
				    				$product_color_add['name']='color';
				    				$product_color_id=$product_attribute_handle->add($product_color_add);
				    				$log['C']="新加颜色：".$product_color;
				    			}
				    			else
				    			{
				    				$product_color_id=$product_color_data[id];
				    			}
			    			}
			    			else
			    			{
			    				$product_color_id=0;
			    			}
			    			//获取尺码id或添加尺码
			    			if($product_size!="")
			    			{
				    			$product_size_data=$product_attribute_handle->field('id')->where("name='size' and value='".$product_size."'")->find();
				    			if(!$product_size_data)
				    			{
				    				$product_size_add['value']=$product_size;
				    				$product_size_add['name']='size';
				    				$product_size_id=$product_attribute_handle->add($product_size_add);
				    				$log['D']="新加尺码：".$product_size;
				    			}
				    			else
				    			{
				    				$product_size_id=$product_size_data[id];
				    			}
			    			}
			    			else
			    			{
			    				$product_size_id=0;
			    			}
			    			//判断产品代号是否存在
			    			$product_code_data=$product_code_handle->field('id')->where("code='".$product_code."'")->find();
			    			$product_code_save['catalog_id']=$this->activeCatalogId;
			    			$product_code_save['product_id']=$this->activeProductId;
			    			$product_code_save['color_id']=$product_color_id;
			    			$product_code_save['size_id']=$product_size_id;
			    			$product_code_save['name']=$product_chines_description;
			    			$product_code_save['weight']=$product_weight;
			    			$product_code_save['price']=$product_price;
			    			$product_code_save['factory_id']=$product_factory_id;			    			
			    			$product_code_save['sort_id']=1;
			    			$product_code_save['is_work']=1;
			    			if(!$product_code_data)
			    			{
			    				//不存在产品代号时，则说明没有这个产品，添加之
			    				$product_code_save['code']=$product_code;
			    				$product_code_id=$product_code_handle->add($product_code_save);
			    			}
			    			else
			    			{
			    				//存在该产品代号时，则修改产品信息
			    				$product_code_handle->where("code='".$product_code."'")->save($product_code_save);
			    				$product_code_id=$product_code_data["id"];
			    			}
			    			
			    			foreach($row as $key=>$value)
			    			{
			    				if($key > "K")
			    				{
			    					if($value!="")
			    					{
			    						$import_sku_list=explode(",",$value);
			    						foreach($import_sku_list as $import_sku_list_value)
			    						{
			    							if(empty($import_sku_list_value))
			    							{
			    								continue;
			    							}
			    							//判断sku是否存在
			    							$product_sku_data = $product_sku_handle->field('id')->where("sku='".$import_sku_list_value."' and come_from_id=".$this->platStyle[$key])->find();
			    							if(!$product_sku_data)
			    							{
			    								$product_sku_add['sku']=$import_sku_list_value;
			    								$product_sku_add['come_from_id']=$this->platStyle[$key];
			    								$product_sku_id=$product_sku_handle->add($product_sku_add);
			    							}
			    							else
			    							{
			    								$product_sku_id=$product_sku_data['id'];
			    							}
			    							//判断是否已经关联
			    							$relate_sku_isset=$relate_sku_handle->where("product_sku_id=".$product_sku_id)->select();
			    							if($relate_sku_isset)
			    							{
			    								foreach($relate_sku_isset as $relate_sku_isset_value)
			    								{
			    									$relate_sku_handle->where('id='.$relate_sku_isset_value[id])->delete();
			    								}
			    							}
			    							$relate_sku_add['product_code_id']=$product_code_id;
			    							$relate_sku_add['product_sku_id']=$product_sku_id;
			    							if(!empty($product_number) && $product_number>1)
			    							{
			    								$relate_sku_add['number']=$product_number;
			    							}
			    							else
			    							{
			    								$relate_sku_add['number']=1;
			    							}
			    							$relate_sku_handle->add($relate_sku_add);
			    						}
			    						
			    					}
			    						
			    				}
			    			}
		    			}
	    			}
	    			else
	    			{
	    				$log['F']="工厂".$product_factory."不存在";//当工厂不存在时，跳过该行，并返回在log中
	    			}
	    		}
	    		else 
	    		{
	    			if($row["A"]=="" && $row["B"]=="")
	    			{
	    				$log['H']="产品代号不可以为空";//当产品代号没填时，跳过次行
	    			}	    			
	    		}
    		}
    		return $log;
    	};
    	$option = array(
    		"table"=>'',//必须
    		"uploadfile"=>$filename,//必须
    		"obj"=>$this,//固定，回调方法所在类
    		"start_row"=>1,//可选，设置有效内容的 起始行数，默认2
    		"unlink"=>false,//是否删除文件，默认false
    		"callback"=>$callback,//可选，用于插入多表的情况。一旦设置callback，下面的配置都不需要。其值是回调函数名，其参数是表格的一行所有内容，形态为一位数组
    	);
    	return importExcel($option);    	
    }
    
    //来源管理
    public function come_from_list()
    {
    	$come_from_model = M('id_come_from');
    	$this->list = $come_from_model->order('style')->select();
        $this->display();
    }
    public function come_from_edit()
    {
    	$come_from_model = M('id_come_from');
    	if(IS_POST)
    	{
    		if(I('post.id'))//修改
    		{
    			$come_from_model->create();
    			$come_from_model->save();
    		}
    		else//添加
    		{
    			$come_from_model->create();
    			$come_from_model->add();
    		}
    		redirect(U('Admin/CodeManage/come_from_list'));exit;
    	}
    	else
    	{
    		if( I('get.id') )
    		{
    			$this->tpltitle = '修改';
    			$this->row = $come_from_model->where( array('id'=>I('get.id')) )->find();
    		}
    		$this->tpltitle = '添加';
    	}
    	$this->display();
    }
	
	
}


