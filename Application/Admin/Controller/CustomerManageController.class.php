<?php
namespace Admin\Controller;
use Admin\Controller\CommonController;
use Org\Util\Date;

class CustomerManageController extends CommonController
{
	public function index(){
			//基本信息
			if(I("post.email")){
				$email=$_POST[email];
			}
			elseif(I("get.email"))
			{
				$email=$_GET[email];
			}
			$customer=M('customers',null,'lily3');
			$customer_one=$customer->alias('c')->join("left join customer_detailed_information as cdi on cdi.customer_id=c.id")->where(array("email"=>$email))->find();
			$this->assign("customer_one",$customer_one);
			
			//客户积分
			$integral=M("customer_integral",null,'lily3');
			$customer_integral=$integral->where(array('email'=>$email))->field("now_integral")->find();
			$this->assign("customer_integral",$customer_integral);
			
			$code=M("order_code",null,'lily3');
			$return=M("product_return");
			$customer_order_web=M("order_web");
			$customer_order_plat=M("order_plat_form");
			$customer_order_list=array();
			$code_list_new=array();//优惠码
			$num=0;
			
			//客户所有订单
			$customer_order_web_list=$customer_order_web->where(array('email'=>$email))->field("id,order_number")->select();
			if($customer_order_web_list){
				foreach ($customer_order_web_list as $value){
					$web_one=array('id'=>$value[id],'order_number'=>$value[order_number]);
					$customer_order_list[]=$web_one;
				}
			}
			$customer_order_plat_list=$customer_order_plat->where(array('email'=>$email))->field("id,order_number")->select();
			if($customer_order_plat_list){
				foreach ($customer_order_plat_list as $value){
					$plat_one=array('id'=>$value[id],'order_number'=>$value[order_number]);
					$customer_order_list[]=$plat_one;
				}
			}
			if(empty($customer_order_list)){
				$this->assign("code_list_new",'');
				$this->assign("num",'');
			}else{
				foreach ($customer_order_list as $value){
					//优惠码
					$code_list=$code->where(array('order_id'=>$value[id]))->field('code_name')->find();
					$code_list_new[]=array('code_name'=>$code_list[code_name]);
					
					//退货
					$return_list=$return->where(array('order_id'=>$value[id]))->field('id')->find();
					if($return_list){
						$num++;
					}
				}
				$this->assign("num",$num);
				$this->assign("code_list_new",$code_list_new);
			}
			//shipping_address
			$shipping_address=M("customer_address",null,'lily3');
			$customer_address=$shipping_address->where(array('customer_id'=>$customer_one[id]))->find();
			if($customer_address){
				$this->assign("customer_address",$customer_address);
			}else{
				$this->assign("customer_address",'');
			}
				
			//登录时间
			$time=M("customer_login_time");
			$login_time=$time->where(array('customer_id'=>$customer_one[id]))->order('id desc')->limit(1)->field('login_time')->select();
			if($login_time){
				$this->assign("login_time",$login_time);
			}else{
				$this->assign("login_time",'');
			}
			
			//订阅信息
			$mail_subscription=M("mail_subscription",null,"lily3");
			$sqp_mail_subscription=$mail_subscription->where(array("email"=>$email))->find();
			$this->assign("sqp_mail_subscription",$sqp_mail_subscription);
			$this->display();
	}
	
	public function order_list_details(){
		layout(false);
		$customer_order_web=M("order_web");
		$customer_order_plat=M("order_plat_form");
		$order_web_status=M("order_web_status");
		$order_plat_status=M("order_plat_form_status");
		$order_detail=M("order_delivery_detail");
		$customer_order_list=array();
		$customer_order_web_list=$customer_order_web->where(array('email'=>$_POST[email]))->field('id,order_number,total_price_discount,date_time')->select();
		if($customer_order_web_list){
			foreach ($customer_order_web_list as $value){
				$delivery_number=$order_detail->where(array('order_web_id'=>$value[id]))->field("delivery_number")->find();
				$web_status=$order_web_status->where(array('order_web_id'=>$value[id]))->field('status')->find();
				$payment_information=M("order_payment_information");
				$sql_payment_information=$payment_information->where(array("order_platform_id"=>$value[id]))->field("payment_id,payment_email,is_paypal")->find();
				if($web_status && $delivery_number){
					$web_one=array('order_number'=>$value[order_number],'price'=>$value[total_price_discount],'date_time'=>$value[date_time],'delivery_number'=>$delivery_number[delivery_number],'status'=>$web_status[status],"payment_id"=>$sql_payment_information[payment_id],"payment_email"=>$sql_payment_information[payment_email],"is_paypal"=>$sql_payment_information[is_paypal]);
					$customer_order_list[]=$web_one;
				}
			}
		}
		
		$customer_order_plat_list=$customer_order_plat->where(array('email'=>$_POST[email]))->field('id,order_number,price,date_time')->select();
		if($customer_order_plat_list){
			foreach ($customer_order_plat_list as $value){
				$delivery_number=$order_detail->where(array('order_platform_id'=>$value[id]))->field("delivery_number")->find();
				$plat_status=$order_plat_status->where(array('order_platform_id'=>$value[id]))->field('status')->find();
				$payment_information=M("order_payment_information");
				$sql_payment_information=$payment_information->where(array("order_platform_id"=>$value[id]))->field("payment_id,payment_email,is_paypal")->find();
				if($plat_status && $delivery_number){
					$plat_one=array('order_number'=>$value[order_number],'price'=>$value[price],'date_time'=>$value[date_time],'delivery_number'=>$delivery_number[delivery_number],'status'=>$plat_status[status],"payment_id"=>$sql_payment_information[payment_id],"payment_email"=>$sql_payment_information[payment_email],"is_paypal"=>$sql_payment_information[is_paypal]);
					$customer_order_list[]=$plat_one;
				}
			}
		}
		//计算总金额，订单数量
		if(!empty($customer_order_list)){
			$num=0;
			$total_price=0;
			$first_time='';
			$last_time='';
			foreach ($customer_order_list as $value){
				$num++;
				$total_price+=$value[price];
				if($first_time==''){
					$first_time=$value[date_time];
				}elseif($first_time>$value[date_time]){
					$first_time=$value[date_time];
				}
					
				if($last_time==''){
					$last_time=$value[date_time];
				}elseif($last_time<$value[date_time]){
					$last_time=$value[date_time];
				}
			}
			$first_time=strtotime($first_time);
			$last_time=strtotime($last_time);
			$day=intval(($last_time-$first_time)/3600/24);
			$this->assign("day",$day);
			$this->assign("num",$num);
			$this->assign("total_price",$total_price);
			$this->assign("customer_order_list",$customer_order_list);
			$html=$this->fetch("CustomerManage/order");
			$this->ajaxReturn($html);
		}else{
			$this->assign("customer_order_list","No");
			$html=$this->fetch("CustomerManage/order");
			$this->ajaxReturn($html);
		}
		
	}
	
	public function customer_details(){
		layout(false);
		//wishlist
		if($_POST[style]=="wishlist"){
			$wishlist_product=M("wishlist_product",null,'lily3');
			$wish_list_products=$wishlist_product->where(array('customer_email'=>$_POST[email]))->select();
			if($wish_list_products){
				$wishlist_product_array=array();
				$customization=array();
				$product_array=array();
				$all_array=array();
				foreach ($wish_list_products as $value){
					if($value[product_id]!=0){
						$products=M("products",null,'lily3');
						//单品信息
						$wishlist_products=$products->where(array('id'=>$value[product_id]))->field("name,image_url,price,image")->find();
						$name=$wishlist_products[name];
						$image_url=$wishlist_products[image_url];
						$image=$wishlist_products[image];
						$price=$wishlist_products[price];
						
						//颜色图片,颜色名
						if($value[color_id]!=0){
							$attribute_color=M("product_attribute_color",null,'lily3');
							$product_attribute_color=$attribute_color->where(array('product_id'=>$value[product_id],'option_value_id'=>$value[color_id]))->field('id')->find();
							$product_image=M("product_color_image",null,"lily3");
							$wishlist_products_image=$product_image->where(array('product_attribute_color_id'=>$product_attribute_color[id],'is_work'=>1))->field('image')->order('order_id asc')->limit(1)->find();
							if($wishlist_products_image){
								$image=$wishlist_products_image[image];
							}
							
							$attribute_value_color=M("product_option_value",null,"lily3");
							$wishlist_products_color=$attribute_value_color->where(array('id'=>$value[color_id]))->field("value")->find();
							$color=$wishlist_products_color[value];
						}
						//尺码描述和价格
						if($value[size_id]!=0){
							$attribute_size=M("product_attribute_size",null,'lily3');
							$product_attribute_size=$attribute_color->where(array('product_id'=>$value[product_id],'option_value_id'=>$value[size_id]))->field('price,size_description')->find();
							
							$size_description=$product_attribute_size[size_description];
							$price+=$product_attribute_size[price];
							
							$attribute_value_size=M("product_option_value",null,"lily3");
							$wishlist_products_size=$attribute_value_size->where(array('id'=>$value[size_id]))->field('value')->find();
							$size=$wishlist_products_size[value];
						}
						
						//定制
						$product_customization=M("wishlist_product_customization",null,"lily3");
						$sql_check_customization=$product_customization->where(array("wishlist_product_id"=>$value[id]))->find();
						if($sql_check_customization){
							$customization=array("height"=>$sql_check_customization[height],"bust"=>$sql_check_customization[bust],"waist"=>$sql_check_customization[waist],"hips"=>$sql_check_customization[hips],"sleeve"=>$sql_check_customization[sleeve],"length"=>$sql_check_customization[length],"weight"=>$sql_check_customization[weight],"description"=>$sql_check_customization[description]);
						}
						
						//儿童
						$sql_check_children=$products->where(array("id"=>$value[product_id]))->field("is_children")->find();
						if($sql_check_children[is_children]==0){
							$price+=(int)NIGHTWEAR_CUSTOMIZATION_PRICE;
						}else{
							$customization_height=explode("foot", $sql_check_customization[height]);
							if($customization_height[0]>=4)
							{
								$price+=30;
							}
						}
						//价格(原价)
						$price=$price*$value[product_number];
						
						$wishlist_one=array("name"=>$name,'image_url'=>$image_url,'color'=>$color,'size'=>$size,'image'=>$image,'price'=>$price,"num"=>$value[product_number],"customization"=>$customization);
						$wishlist_product_array[]=$wishlist_one;
						unset($customization);
					}
					else
					{
						//套件
						$products_set=M("products_set",null,'lily3');
						$wishlist_products_set_name=$products_set->where(array('id'=>$value[product_set_id]))->field("name,image_url,image")->find();
						$name=$wishlist_products_set_name[name];
						$image_url=$wishlist_products_set_name[image_url];
						$image=$wishlist_products_set_name[image];
						
						//颜色图片,颜色名(套件)
						if($value[color_id]!=0){
							$attribute_color_set=M("product_attribute_color",null,"lily3");
							$wishlist_products_set_color=$attribute_color_set->where(array("product_set_id"=>$value[product_set_id],"option_value_id"=>$value[color_id]))->field("id")->find();
							$product_set_image=M("product_color_image",null,"lily3");
							$wishlist_products_set_image=$product_set_image->where(array("product_attribute_color_id"=>$wishlist_products_set_color[id],"is_work"=>1))->field("image")->order("order_id asc")->limit(1)->find();
							if($wishlist_products_set_image){
								$image=$wishlist_products_set_image[image];
							}
							$attribute_value_color_set=M("product_option_value",null,"lily3");
							$products_set_color=$attribute_value_color_set->where(array("id"=>$value[color_id]))->field("value")->find();
							$color=$products_set_color[value];
						}
						if($value[size_id]!=0){
							$attribute_size_set=M("product_option_value",null,"lily3");
							$wishlist_products_set_size=$attribute_size_set->where(array('id'=>$value[size_id]))->field('value')->find();
							$size=$wishlist_products_set_size[value];
						}
						//套件价格和尺码
						$product_set_price=0;
						$wishlist_set=M("wishlist_set_product",null,"lily3");
						$wishlist_set_product=$wishlist_set->where(array("wishlist_product_id"=>$value[id]))->select();
											
						foreach ($wishlist_set_product as $value_set){
							$products=M("products",null,'lily3');
							$products_price=$products->where(array("id"=>$value_set[product_id]))->field("price")->find();
							$product_set_detail=M("product_set_detail",null,"lily3");
							$product_one_name=$product_set_detail->where(array("products_set_id"=>$value[product_set_id],"products_id"=>$value_set[product_id]))->field("rewrite_name")->find();
							$sql_product_one_size="";
							if($value_set[pillowcase_size]==0){
								$product_one_size=M("product_attribute_size",null,"lily3");
								$sql_product_one_size=$product_one_size->where(array("product_id"=>$value_set[product_id],"option_value_id"=>$value[size_id]))->field("price,size_description")->find();
							}else{
								$product_one_size=M("product_attribute_size",null,"lily3");
								$sql_product_one_size=$product_one_size->where(array("product_id"=>$value_set[product_id],"option_value_id"=>$value_set[pillowcase_size]))->field("price,size_description")->find();
							}
							$one_set_price=$products_price[price]+$sql_product_one_size[price];
							$one_set_price=$one_set_price*$value_set[num];
							$product_set_price+=$one_set_price;
							$product_array_one=array("product_one_name"=>$product_one_name[rewrite_name],"product_one_size"=>$sql_product_one_size[size_description],"product_one_num"=>$value_set[num],"product_one_price"=>$one_set_price);
							$product_array[]=$product_array_one;
						}
						$wishlist_one=array("name"=>$name,"image_url"=>$image_url,"image"=>$image,"color"=>$color,"size"=>$size,"price"=>$product_set_price,"product_array"=>$product_array);
						$wishlist_product_array[]=$wishlist_one;
						unset($product_array);
					}
				}
				$this->assign("wishlist_array",$wishlist_product_array);
				$html=$this->fetch("CustomerManage/wishlist");
				$this->ajaxReturn($html);
			}else{
				$this->assign("wishlist_array","No");
				$html=$this->fetch("CustomerManage/wishlist");
				$this->ajaxReturn($html);
			}
			
		}
		//定制信息
		elseif(I('post.style')=="custom")
		{
			$order_web=M("order_web");
			$order_plat=M("order_plat_form");
			$order_array=array();
			$gift_box_array=array();
			$gramming_array=array();
			$customization_array=array();
			$order_web_list=$order_web->where(array("email"=>$_POST[email]))->field("id,date_time,order_number")->select();
			foreach ($order_web_list as $value_web){
				$order_web_list_one=array("order_id"=>$value_web[id],"time"=>$value_web['date_time'],"order_number"=>$value_web[order_number]);
				$order_array[]=$order_web_list_one;
			}
			
			$order_plat_list=$order_plat->where(array("email"=>$_POST[email]))->field("id,date_time,order_number")->select();
			foreach ($order_plat_list as $value_plat){
				$order_plat_list_one=array("order_id"=>$value_plat[id],"time"=>$value_plat[date_time],"order_number"=>$value_plat[order_number]);
				$order_array[]=$order_plat_list_one;
			}
			
			if(!empty($order_array)){
				foreach ($order_array as $value){
				$order_product=M("order_product",null,"lily3");
				$order_product_list=$order_product->where(array("order_id"=>$value[order_id],"is_work"=>1))->field("id,product_name,color_name,size_name")->select();
					foreach ($order_product_list as $value_order_product){
						//gift_box
						$gift_box_order=M("order_product_gift_box",null,"lily3");
						$is_gift_box=$gift_box_order->where(array("order_product_id"=>$value_order_product[id]))->field("message,gift_box_id")->find();
						if($is_gift_box){
							$gift_box=M("gift_box",null,"lily3");
							$sql_gift_box=$gift_box->where(array("id"=>$is_gift_box[gift_box_id]))->field("gift_image,gift_price")->find();
							$gift_box_array_one=array("order_number"=>$value[order_number],"product_name"=>$value_order_product[product_name],"color_name"=>$value_order_product[color_name],"size_name"=>$value_order_product[size_name],"message"=>$is_gift_box[message],"gift_image"=>$sql_gift_box[gift_image],"gift_price"=>$sql_gift_box[gift_price]);
							$gift_box_array[]=$gift_box_array_one;
						}
						
						//绣字
						$order_gramming=M("order_product_gramming",null,"lily3");
						$is_gramming=$order_gramming->where(array("order_product_id"=>$value_order_product[id]))->find();
						if($is_gramming){
							$gramming_array_one=array("order_number"=>$value[order_number],"product_name"=>$value_order_product[product_name],"color_name"=>$value_order_product[color_name],"size_name"=>$value_order_product[size_name],"font_name"=>$is_gramming[font_name],"font_style"=>$is_gramming[font_style],"font_color"=>$is_gramming[font_color]);
							$gramming_array[]=$gramming_array_one;
						}
						
						//睡衣定制
						$order_customization=M("order_product_customization",null,"lily3");
						$is_customization=$order_customization->where(array("order_product_id"=>$value_order_product[id]))->field("customization,description")->find();
						if($is_customization){
							$customization_array_one=array("order_number"=>$value[order_number],"product_name"=>$value_order_product[product_name],"color_name"=>$value_order_product[color_name],"size_name"=>$value_order_product[size_name],"customization"=>$is_customization[customization],"description"=>$is_customization[description]);
							$customization_array[]=$customization_array_one;
						}
					}
			    }
			    $this->assign("gift_box_array",$gift_box_array);
			    $this->assign("gramming_array",$gramming_array);
			    $this->assign("customization_array",$customization_array);
			    $html=$this->fetch("CustomerManage/custom");
			    $this->ajaxReturn($html);
			}else{
				$this->assign("custom","No");
				$html=$this->fetch("CustomerManage/custom");
				$this->ajaxReturn($html);
			}
		}elseif(I('post.style')=='sns'){
			vendor('SNS.src.index');
			$sns=getSns(I('post.email'));
			$count_sns_id=count($sns[sns_id]);
			$count_sns_url=count($sns[url]);
			if($count_sns_id > $count_sns_url){
				$this->assign("sns_num",$count_sns_id);
			}else{
				$this->assign("sns_num",$count_sns_url);
			}
			$this->assign("sns",$sns);
			$html=$this->fetch("CustomerManage/sns");
			$this->ajaxReturn($html);
		}
	}
	
	public function show_integral() {
		if(isset($_POST[email])){
			$customer_integral=M("customer_integral",null,"lily3");
			$customer_integral_list=$customer_integral->alias('ci')->join('left join customer_integral_used as ciu on ci.id=ciu.customer_integral_id')->where(array("email"=>$_POST[email]))->field("ciu.*")->select();
			echo json_encode($customer_integral_list);
		}
	}
	public function add_integral(){
		if(!empty($_POST[total_integral]) && !empty($_POST[get_integral])){
			$customer_integral=M("customer_integral",null,"lily3");
			$sql_customer_integral=$customer_integral->where(array("email"=>$_POST[email]))->field("id,now_integral")->find();
			if($sql_customer_integral){
				$now_integral=$_POST[total_integral]+$sql_customer_integral[now_integral];
				$data_customer_integral["now_integral"]=$now_integral;
				$save_customer_integral=$customer_integral->where(array("email"=>$_POST[email]))->save($data_customer_integral);
				if($save_customer_integral){
					$customer_integral_add=M("customer_integral_add",null,"lily3");
					$data_customer_integral_add["total_integral"]=$_POST[total_integral];
					$data_customer_integral_add["get_integral"]=$_POST[get_integral];
					$data_customer_integral_add["customer_integral_id"]=$sql_customer_integral[id];
					$insert_customer_integral_add=$customer_integral_add->add($data_customer_integral_add);
					if($insert_customer_integral_add){
						$this->redirect('CustomerManage/index',array("email"=>$_POST[email]));
					}
				}
			}else{
				$data["total_integral"]=$_POST[total_integral];
				$data["now_integral"]=$_POST[total_integral];
				$data["email"]=$_POST[email];
				$add_customer_integral=$customer_integral->add($data);
				if($add_customer_integral){
					$this->redirect('CustomerManage/index',array("email"=>$_POST[email]));
				}
			}
		}else{
			$this->redirect('CustomerManage/index',array("email"=>$_POST[email]));
		}
	}
	
	public function customer_bad_user()
	{
		
		$bad_use=M("bad_user");
		if(I('get.page')=="edit")
		{
			$user=$bad_use->where(array("id"=>$_GET[id]))->find();
			$this->assign("user",$user);
 			$this->display("bad_user_edit");
		}
		elseif(I('get.page')=="add")
		{
			$this->display("add_bad_user");
		}
		elseif(I('get.act')=="edit")
		{
			$time=strtotime($_POST[time]);
			$edit_bad_user=$bad_use->where(array("id"=>$_GET[id]))->save(array("telephone"=>$_POST[telephone],"email"=>$_POST[email],"first_name"=>$_POST[first_name],"last_name"=>$_POST['last_name'],"remark"=>$_POST['remark'],"custome_ip"=>$_POST[custome_ip],"is_work"=>$_POST[is_work],"datetime"=>$time));
			if($edit_bad_user)
			{
				$this->success("修改成功",U(CustomerManage/customer_bad_user));
			}
		}
		elseif(I('get.act')=="add")
		{
			$time=strtotime($_POST[time]);
			$add_bad_user=$bad_use->add(array("telephone"=>$_POST[telephone],"email"=>$_POST[email],"first_name"=>$_POST[first_name],"last_name"=>$_POST['last_name'],"remark"=>$_POST['remark'],"custome_ip"=>$_POST[custome_ip],"is_work"=>$_POST[is_work],"datetime"=>$time));
			if($add_bad_user)
			{
				$this->success("添加成功",U(CustomerManage/customer_bad_user));
			}
		}
		else
		{
			$count=$bad_use->where(array("is_work"=>1))->count();// 查询满足要求的总记录数
			$Page=new \Think\Page($count,10);// 实例化分页类 传入总记录数和每页显示的记录数
			$show=$Page->show_pintuer();// 分页显示输出
			$list=$bad_use->where(array("is_work"=>1))->limit($Page->firstRow.','.$Page->listRows)->select();
			$this->assign('bad_user_list',$list);// 赋值数据集
			$this->assign('page',$show);// 赋值分页输出
			$this->display();
		}
	}
	
}