<?php
namespace Admin\Controller;
use Think\Controller;
use Org\Util\Date;

class ProductReturnController extends CommonController
{	
	public function index(){
		$this->display();
	}
	public function product_return_list(){
		$product_return=D("product_return");
		if($_GET[act]=="select"){
			if($_POST[order_id]!=''){
				$product_return_one_list=$product_return->where(array('order_id'=>$_POST[order_id]))->select();
			}
			elseif($_POST[transaction_id]!=''){
				$product_return_one_list=$product_return->where(array('transaction_id'=>$_POST[transaction_id]))->select();
			}
			if($product_return_one_list){
				$this->assign("product_return_list",$product_return_one_list);
			}else{
				$this->error("没有对应的记录",U('ProductReturn/product_return_list'));
			}
		}else{
			$product_return_list=$product_return->select();
			$this->assign("product_return_list",$product_return_list);
		}
		$this->display();
	}
	
	public function product_return_act(){
		$is_ok=false;
		$product_return=D("product_return");
		if($_GET[act]=="edit"){
			$data['status']=$_POST[status];
			$data['place']=$_POST[place];
			$data['is_cancel']=$_POST[is_cancel];
			$data['operator']=$_POST[operator];
			$product_return_one_edit=$product_return->data($data)->where(array('id'=>$_GET[id]))->save();
			if($product_return_one_edit){
				$this->success("修改成功",U('ProductReturn/product_return_list'));
			}else{
				$this->error("修改失败");
			}
		}
		if($_GET[come]=='plat')
		{
			if(I("get.act")=="return")
			{
				$product_id_array=$_POST[product_id];
		  		$order_history["order_platform_id"]=$_POST['order_id'];
		  		$order_history["status"]="return";
		  		$order_history["message"]=$_POST["message"];
		  		$order_history["date_time"]=date("Y-m-d H:i:s",time());
		  		$order_history["operator"]=$_POST['operator'];
		  		$insert_order_status_history=M("order_plat_form_status_history")->add($order_history);
		  		//改变订单产品的状态
		  		$order_product_status=D("order_plat_form_product");
		  		$order_product['status']='return';
				foreach ($product_id_array as $value)
				{
		  			$edit_order_product_status=$order_product_status->where(array('id'=>$value))->save($order_product);
		  			if($edit_order_product_status)
					{
		  				$is_ok=true;
		  			}
					else
					{
		  				$is_ok=false;
		  				$this->error("产品状态修改失败");
		  			}
		  		}
				if($is_ok===true)
				{
					//插入退货表
		  			$data['order_id']=$_POST[order_id];
		  			$data['transaction_id']=11;
		  			$data['status']='';
		  			$data['place']=$_POST[place];
		  			$data['operator']=$_POST[operator];
		  			$data['come_from']=$_POST[come_from];
		  			$data['is_cancel']=0;
		  			$data['time']=date("Y-m-d h:i:s",time());
		  			$add_product_return=$product_return->data($data)->add();
		  			$product_return_is_ok=false;
					if($add_product_return)
					{
						//插入退货产品
		  				$product_return_detail=D("product_return_detail");
						$code_id_array=array();
						foreach ($product_id_array as $value)
						{
		  					$select_order_product=D("order_plat_form_product");
		  					$select_order_product_sql=$select_order_product->where(array('id'=>$value))->select();
		  					$product_detail['product_return_id']=$add_product_return;
		  					$product_detail['code_id']=$select_order_product_sql[0][code_id];
							$code_id_array[]=$select_order_product_sql[0][code_id];
		  					$product_detail['sku_id']=$select_order_product_sql[0][sku_id];
		  					$product_detail['number']=$select_order_product_sql[0][number];
							
		  					$add_product_return_detail=$product_return_detail->data($product_detail)->add();
							
		  					if($add_product_return_detail)
							{
		  						$product_return_is_ok=true;
		  					}
							else
							{
		  						$product_return_is_ok=false;
		  					}
		  				}
					}
					if($product_return_is_ok)
					{
						$product_code_array=array();
						foreach($code_id_array as $val_code)
						{
							$product_code=M("id_product_code")->where(array("id"=>$val_code))->getField("code");
							$product_code_array[]=$product_code;
						}
		  				$this->success("操作成功");
					}
					else
					{
						$this->error("退货产品操作失败");
					}
				}
			}
			if(I("get.act")=="add")
			{
				$products_price=M("id_product_code");
		  		$products_price_sql=$products_price->where(array("id"=>$_POST[code]))->field("price")->find();
		  		$products_name=M("id_product");
		  		$products_name_sql=$products_name->where(array("id"=>$_POST[product_id]))->field("name")->find();	
				$ptice=!empty($_POST[new_order_price])?$_POST[new_order_price]:$products_price_sql[price];
		  		$data['order_platform_id']=$_POST[order_id];
		  		$data['name']=$_POST[product_description];
		  		$data['code_id']=$_POST[code];
		  		$data['sku_id']='';
		  		$data['sku']='';
		  		$data['price']=$ptice;
		  		$data['number']=$_POST[number];
		  		$data['status']='new';
		  		$add_order_plat_form_product=M("order_plat_form_product");
		  		$add_order_plat_form_product_sql=$add_order_plat_form_product->data($data)->add();
				if($add_order_plat_form_product_sql)
				{
		  			$this->success("添加成功");
		  		}
				else
				{
		  			$this->error("添加失败");
		  		}
			}
			if(I("get.act")=="cancel")
			{
				$product_id_array=$_POST[product_id];
				$code_array=array();
				
				foreach($product_id_array as $val)
				{
					$product_array=M("order_plat_form_product")->where(array("id"=>$val))->getField("code_id");
					$code=M("id_product_code")->where(array("id"=>$product_array))->getField("code");
					$code_array[]=$code;
				}
				order_product_return(0,$_POST[order_id],$code_array,$product_id_array,$customization_code,$product_customization_id);
				$this->success("取消成功");
			}
		}
		if($_GET[come]=='web')//网站订单
		{
			if(I("get.act")=="return")
			{
				$product_id_array=$_POST[product_id];
				$order_history["order_web_id"]=$_POST['order_id'];
				$order_history["status"]="return";
				$order_history["message"]=$_POST["message"];
				$order_history["date_time"]=date("Y-m-d H:i:s",time());
				$order_history["operator"]=$_POST['operator'];
				$insert_order_status_history=M("order_web_status_history")->add($order_history);
						
				//改变订单产品的状态
				$order_product_status=D("order_web_product");
				$order_product['status']='return';
				foreach ($product_id_array as $value)
				{
					$edit_order_product_status=$order_product_status->where(array

	('id'=>$value))->save($order_product);				
					if($edit_order_product_status){
						$is_ok=true;
					}
					else
					{
						$is_ok=false;
						$this->error("订单产品状态操作失败");
					}
				}
				if($is_ok===true)
				{
					//插入退货表
					$data['order_id']=$_POST[order_id];
					$data['transaction_id']=11;
					$data['status']='';
					$data['place']=$_POST[place];
					$data['operator']=$_POST[operator];
					$data['come_from']=$_POST[come_from];
					$data['is_cancel']=0;
					$data['time']=date("Y-m-d h:i:s",time());
					$add_product_return=$product_return->data($data)->add();
					$product_return_is_ok=false;
					if($add_product_return)//插入退货产品
					{
						$code_id_array=array();
						$product_return_detail=D("product_return_detail");
						foreach ($product_id_array as $value)
						{
							$select_order_product=D("order_web_product");
							$select_order_product_sql=$select_order_product->where

	(array('id'=>$value))->select();
							$product_detail['product_return_id']=$add_product_return;
							$product_detail['code_id']=$select_order_product_sql[0]

	[code_id];
							$code_id_array[]=$select_order_product_sql[0][code_id];
							$product_detail['sku_id']=0;
							$product_detail['number']=$select_order_product_sql[0]

	[number];
							$add_product_return_detail=$product_return_detail->data

	($product_detail)->add();
							if($add_product_return_detail)
							{
								$product_return_is_ok=true;
							}
							else
							{
								$product_return_is_ok=false;
							}
						}
					}
					if($product_return_is_ok)
					{
						$product_code_array=array();
						foreach($code_id_array as $val_code)
						{
							$product_code=M("id_product_code")->where(array

	("id"=>$val_code))->getField("code");
							$product_code_array[]=$product_code;
						}
						$this->success("操作成功");
					}
					else
					{
						$this->error("退货产品操作失败");
					}
				}
				
			}
			if(!empty($_POST[code]))//新增
			{
				$products_price=M("id_product_code");
				$products_price_sql=$products_price->where(array("id"=>$_POST[code]))->field

	("price")->find();
				$products_name=M("id_product");
				$products_name_sql=$products_name->where(array("id"=>$_POST[products]))->field

	("name")->find();
				$order_product_id=rand(1,1000);
				$price=isset($_POST['new_order_price'])?$_POST['new_order_price']:$products_price_sql[price];
				$data['order_web_id']=$_POST[order_id];
				$data['code_id']=$_POST[code];
				$data['set_sku']='';
				$data['price']=$price;
				$data['discount_price']='';
				$data['number']=$_POST[number];
				$data['status']='new';
				$data['order_product_id']=$order_product_id;
				$add_order_web_form_product=M("order_web_product");
				$add_order_web_form_product_sql=$add_order_web_form_product->data($data)->add();
				
				$original_data['order_web_id']=$_POST[order_id];
				$original_data['order_product_id']=$order_product_id;
				$original_data['product_name']=$_POST['product_description'];
				$original_data['number']=$_POST['number'];
				$original=M('order_web_product_original');
				$original_add=$original->data($original_data)->add();
				
				if($add_order_web_form_product_sql)
				{
					$this->success("添加成功");
				}
				else
				{
					$this->error("添加失败");
				}
			}
			if(I("get.act")=="cancel")
			{
				$product_id_array=$_POST[product_id];
				$product_customization_id=$_POST[product_customization_id];
				$code_array=array();
				$customization_code=array();
				//普通产品
				foreach($product_id_array as $val)
				{
					$product_array=M("order_web_product")->where(array("id"=>$val))->getField("code_id");
					if($product_array)
					{
						$code=M("id_product_code")->where(array("id"=>$product_array))->getField("code");
						$code_array[]=$code;
					}
				}
				//定制产品
				if(!empty($product_customization_id))
				{
					$customization_code[]="customization";
				}
				order_product_return($_POST[order_id],0,$code_array,$product_id_array,$customization_code,$product_customization_id);
				$this->success(取消成功);
			}
		}	
	}
	public function product_return_order(){
		if(I('get.come')=='plat'){
			$return_order=M("order_plat_form");
			$return_order_one_list=$return_order->where(array('id'=>$_GET[order_id]))->find();
			
			$plat_status_DB=M("order_plat_form_status");
			$plat_status=$plat_status_DB->where(array("order_platform_id"=>$return_order_one_list[id]))->getField("status");
			if($return_order_one_list){
				$return_order_product=M("order_plat_form_product");
				$return_order_product_list=$return_order_product->where(array('order_platform_id'=>$return_order_one_list[id]))->select();
				$this->assign("return_order_product_list",$return_order_product_list);
			}
			$this->order_status_DB=$plat_status;
		}
		elseif(I('get.come')=='web')
		{
			$return_order=M("order_web");
			$return_order_one_list=$return_order->where(array('id'=>$_GET[order_id]))->find();
			
			$order_status_DB=M("order_web_status");
			$order_status=$order_status_DB->where(array("order_web_id"=>$return_order_one_list[id]))->getField("status");
			$return_order_product=M("order_web_product");
			$return_order_product_list=$return_order_product->alias('owp')->join("left join id_product_code as ipc on owp.code_id=ipc.id")->where(array("order_web_id"=>$return_order_one_list[id]))->field("owp.*,ipc.name")->select();
			$this->assign("return_order_product_list",$return_order_product_list);
			
			//定制单
			$order_customization=M("order_web_product_customization")->where(array("order_web_id"=>$return_order_one_list[id]))->select();
			
			$this->order_customization=$order_customization;
			$this->order_status_DB=$order_status;
		}
		//分类
		$catalog=M("id_catalog");
		$catalog_list=$catalog->field("id,name")->select();
		$this->assign("catalog_list",$catalog_list);
		$this->assign("order_product",$return_order_one_list);
		$this->assign("status",order_status());
		$this->order_status=$_GET["order_status"];
		$this->come=$_GET[come];
		$this->display();
	}
	
	public function admin_select_catalog(){
		if(isset($_POST[catalog])){
			$products=M("id_product");
			$is_sku=false;
			$products_list_new='';
			$products_list=$products->where(array("catalog_id"=>$_POST[catalog]))->field("id,name")->select();
			if(!$products_list){
				$products_list=M("id_product_sku")->where(array("catalog_id"=>$_POST[catalog]))->field("id,name")->select();
				$is_sku=true;
			}
			$products_list_new.="<input type='hidden' value='$is_sku' id='is_sku'>";
			if($is_sku){
				$products_list_new.="<select onchange='admin_select_products(this.value)' name='product_set_id' class='input'><option value='0'>--请选择产品--</option>";
			}else{
				$products_list_new.="<select onchange='admin_select_products(this.value)' name='product_id' class='input'><option value='0'>--请选择产品--</option>";
			}
			foreach ($products_list as $value){
				$products_list_new.="<option value='".$value[id]."'>".$value[name]."</option>";
			}
			$products_list_new.="</select>";
			echo $products_list_new;
		}
	}
	
	public function admin_select_products(){
		if(isset($_POST[products])){
			$code=M("id_product_code");
			$code_list_new='';
			$code_list=$code->where(array("product_id"=>$_POST[products]))->field("id,name")->select();
			$code_list_new.="<select name='code' class='input'><option value='0'>--请选择Code--</option>";
			foreach ($code_list as $value){
				$code_list_new.="<option value='".$value[id]."'>".$value[name]."</option>";
			}
			$code_list_new.="</select>";
			echo $code_list_new;
		}
	}
	
	public function add_product_new_order()
	{
		if(I('post.style')=="product")
		{
			$id_product=M("id_product");
			$product_list_html="";
			$product_list=$id_product->where(array("catalog_id"=>$_POST[catalog]))->select();
			$product_list_html.="<option value='0'>--请选择产品--</option>";
			foreach ($product_list as $val)
			{
				$product_list_html.="<option value='".$val[id]."'>".$val[name]."</option>";
			}
			
			echo $product_list_html;
		}
		elseif(I('post.style')=="code")
		{
			$product_code=M("id_product_code");
			$code_list_html="";
			$product_code_list=$product_code->where(array("product_id"=>$_POST[product]))->select();
			$code_list_html="<option value='0'>--请选择Code--</option>";
			foreach ($product_code_list as $val)
			{
				$code_list_html.="<option value='$val[id]'>$val[name]</option>";
			}
			echo $code_list_html;
		}
		elseif(I('get.act')=="add_new_order")
		{
			if(I('get.come')=="web")
			{
				$order_web=D("OrderWeb");
				$order_web_DB=M("order_web");
				//$new_order_array=array();
				$old_order_web_information=$order_web->where(array("id"=>$_POST[new_order_order_id]))->relation(array("order_web_address"))->find();
				
				$new_order_web=array(
					"order_number"=>$old_order_web_information[order_number],
					"email"=>$old_order_web_information[email],
					"first_name"=>$old_order_web_information[first_name],
					"last_name"=>$old_order_web_information[last_name],
					"customer_id"=>$old_order_web_information[customer_id],
					"message"=>$old_order_web_information[message],
					//"couponcode"=>$old_order_web_information[couponcode],
					"total_price"=>$_POST[new_order_price],
					"total_price_discount"=>$_POST[new_order_price],
					"device"=>$old_order_web_information[device],
					"payment_style"=>"手动添加订单",
					"come_from_id"=>$old_order_web_information[come_from_id],
					"cookie_from"=>$old_order_web_information[cookie_from],
					"least_from"=>$old_order_web_information[least_from],
					"date_time"=>date("Y-m-d H:i:s",time())
				);
				$add_new_order_web=$order_web_DB->add($new_order_web);
				
				if($add_new_order_web)
				{
					$new_order_address=array(
							"order_web_id" => $add_new_order_web,
							"first_name" => $old_order_web_information[order_web_address][0][first_name],
							"last_name" => $old_order_web_information[order_web_address][0][last_name],
							"country" => $old_order_web_information[order_web_address][0][country],
							"province" => $old_order_web_information[order_web_address][0][province],
							"city" => $old_order_web_information[order_web_address][0][city],
							"address" => $old_order_web_information[order_web_address][0][address],
							"code" => $old_order_web_information[order_web_address][0][code],
							"telephone" => $old_order_web_information[order_web_address][0][telephone]
					);
					$order_product_id=rand(1,1000);
					$add_new_order_web_product=M("order_web_product")->add(array("order_web_id" => $add_new_order_web,"code_id" => $_POST[new_order_code],"order_product_id"=>$order_product_id,"price" => $_POST[new_order_price],"discount_price" => $_POST[new_order_price],"number" => $_POST[new_order_number]));
					
					$add_new_order_web_address=M("order_web_address")->add($new_order_address);
					$original_data['order_web_id']=$add_new_order_web;
					$original_data['order_product_id']=$order_product_id;
					$original_data['product_name']=$_POST['product_description'];
					$original_data['number']=$_POST['new_order_number'];
					$original=M('order_web_product_original');
					$original_add=$original->data($original_data)->add();
					if($add_new_order_web_product && $add_new_order_web_address)
					{
						$this->success("添加成功");
					}
				}
			}
			
			if(I('get.come')=="plat")
			{
				$order_plat=D("OrderPlatForm");
				$order_plat_DB=M("order_plat_form");
				$old_order_plat_information=$order_plat->where(array("id"=>$_POST[new_order_order_id]))->relation(array("shipping_info"))->find();
				
				$new_order_plat=array(
					"order_number"=>$old_order_plat_information[order_number],
					"email"=>$old_order_plat_information[email],
					"name"=>$old_order_plat_information[name],
					"telephone"=>$old_order_plat_information[telephone],
					"currency"=>$old_order_plat_information[currency],
					//"couponcode"=>$old_order_web_information[couponcode],
					"price"=>$_POST[new_order_price],
					"message"=>$old_order_plat_information[message],
					"is_gift_package" => $old_order_plat_information[is_gift_package],
					"come_from_id"=>$old_order_plat_information[come_from_id],
					"operator"=>$_SESSION["username"],
					"date_time"=>date("Y-m-d H:i:s",time()),
					"is_return_exchange"=>1,
					"ship_service_level"=>$old_order_plat_information[ship_service_level]
				);
				
				$add_new_order_plat=$order_plat_DB->add($new_order_plat);
				
				if($add_new_order_plat)
				{
					$new_order_address=array(
						"order_platform_id" => $add_new_order_plat,
						"name" => $old_order_plat_information[shipping_info][name],
						"country" => $old_order_plat_information[shipping_info][country],
						"state" => $old_order_plat_information[shipping_info][state],
						"city" => $old_order_plat_information[shipping_info][city],
						"address1" => $old_order_plat_information[shipping_info][address1],
						"address2" => $old_order_plat_information[shipping_info][address2],
						"address3" => $old_order_plat_information[shipping_info][address3],
						"post" => $old_order_plat_information[shipping_info][post],
						"telephone" => $old_order_plat_information[shipping_info][telephone]
					);
					
					$new_order_code_name=M("id_product_code")->where(array("id"=>$_POST[new_order_code]))->getField("name");
					if(!empty($_POST[product_description]))
					{
						$new_order_code_name=$_POST[product_description];
					}
					$add_new_order_plat_product=M("order_plat_form_product")->add(array("order_platform_id" => $add_new_order_plat,"name"=>$new_order_code_name,"code_id" => $_POST[new_order_code],"price" => $_POST[new_order_price],"number" => $_POST[new_order_number]));
					
					$add_new_order_plat_address=M("order_plat_form_shipping")->add($new_order_address);
					
					if($add_new_order_plat_product && $add_new_order_plat_address)
					{
						$this->success("添加成功");
					}
				}
			}
		}
	}
}