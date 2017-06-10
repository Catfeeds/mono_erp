<?php
function order_to_fba($order_id,$order_platform_id,$come_from_id)
{
	$is_fba=1;
    $fba = M('fba_order');
	$order_web_product_customization=M("order_web_product_customization");
	$order_web_product_customization_check=$order_web_product_customization->where("order_web_id=$order_id and status<>'return' and status<>'cancel'")->select();
	if($order_web_product_customization_check)//如果订单中包含定制产品，不能FBA发货
	{
		$is_fba=0;
	}
	else
	{
	    if($order_id!=0)//网站
	    {
	        $fba_check=$fba->where(array('order_id' =>$order_id,'come_from_id'=>$come_from_id,'status'=>'cancel'))->select();
	    }
	    else//平台
	    {
	        $fba_check=$fba->where(array('orderplatform_id' =>$order_platform_id,'come_from_id'=>$come_from_id,'status'=>'cancel'))->select();
	    }
	    
	    if(!$fba_check)
	    {
	    	//三种fba库存 分别的覆盖范围
	    	$place_us = array('us');
	    	$place_ca = array('ca');
	    	$place_uk = array('uk','fr','de','it','es');//
	    	$place_jp = array('jp');
	    	$place = null;
	    	
			if($order_id!=0)//网站
	        {
	            $order_web_product = M('order_web_product');
	            $order_shipping_model = M('order_web_address');
	            
	            $shipping_row = $order_shipping_model->where( array('order_web_id'=>$order_id) )->find();
	            $shippint_country = strtolower($shipping_row['country']);
	            in_array($shippint_country, $place_us) && $place='us';
	            in_array($shippint_country, $place_ca) && $place='ca';
	            in_array($shippint_country, $place_uk) && $place='uk';
	            in_array($shippint_country, $place_jp) && $place='jp';
	            if( $place==null ) //订单地址不在fba库存 覆盖范围
	            {
	            	$is_fba=0;
	            }
	            else
	            {
	            	$order_product_list=$order_web_product->where("order_web_id=$order_id and status<>'return' and status<>'cancel'")->select();
	            	foreach($order_product_list as $key=>$value_order_product_list)
	            	{
	            		if($value_order_product_list[set_sku]!="")//有套件，不能FBA发货
	            		{
	            			$is_fba=0;
	            		}
	            		else
	            		{
	            			$order_web_nightwear_customization = M('order_web_nightwear_customization');
	            			$order_web_nightwear_customization_check = $order_web_nightwear_customization->WHERE("order_web_product_id=$value_order_product_list[id]")->select();
	            			if($order_web_nightwear_customization_check)//有 定制睡衣
	            			{
	            				$is_fba=0;
	            			}
	            			else
	            			{
	            				$code_id = $value_order_product_list[code_id];
	            				$product_stock = M('product_stock');
	            				
	            				$stock_where = array(
	            					'code_id'	=> $code_id,
	            					'style'		=> 1,
	            					'place'		=> $place,
	            					'number>minimum', //库存量 处于安全值
	            				);
	            				$product_stock_check = $product_stock->WHERE($stock_where)->select();
	            				($product_stock_check[0][number]<$value_order_product_list[number]) && $is_fba=0;
	            			}
	            		}
	            	}
	            }
	        }
	        else//平台
	        {
				$order_plat_form_product = M('order_plat_form_product');
				$order_shipping_model = M('order_plat_form_shipping');
				 
				$shipping_row = $order_shipping_model->where( array('order_platform_id'=>$order_platform_id))->find();
				$shippint_country = strtolower($shipping_row['country']);
				in_array($shippint_country, $place_us) && $place='us';
				in_array($shippint_country, $place_ca) && $place='ca';
				in_array($shippint_country, $place_uk) && $place='uk';
				in_array($shippint_country, $place_jp) && $place='jp';
				if( $place==null ) //订单地址不在fba库存 覆盖范围 
				{
					$is_fba=0;
				}
				else 
				{
					$order_product_list=$order_plat_form_product->where("order_platform_id=$order_platform_id and status<>'return' and status<>'cancel'")->select();
					foreach($order_product_list as $key=>$value_order_product_list)
					{
						$Model = new \Think\Model();
						$id_relate_sku=$Model->query("select count(*) as total from id_relate_sku where product_sku_id=(select id from id_product_sku where sku='$value_order_product_list[sku]' and come_from_id=$come_from_id)");
						if($id_relate_sku[0][total]>1)//套件不发fba，这里有问题，存在复数单产品的套件
						{
							$is_fba=0;
						}
						else
						{
							$code_id=$value_order_product_list[code_id];
							$product_stock = M('product_stock');
							$stock_where = array(
								'code_id'	=> $code_id,
								'style'		=> 1,
								'place'		=> $place,
								'number>minimum', //库存量 处于安全值
							);
							$product_stock_check = $product_stock->WHERE($stock_where)->select();
							$product_stock_check[0][number]<$value_order_product_list[number] && $is_fba=0;
						}
					}
				}
				//所有amazon平台的订单不通过fba发货
				$is_amazon = in_array($come_from_id, array(1,2,3,23,24,25,26,27,28)) ? true : false;
				if($is_amazon && $is_fba) //或  订单来自amazon平台
				{
					$is_fba = 0;
					M('fba_order_forbid_for_amazon')->add(array('come_from_id'=>$come_from_id,'order_platform_id'=>$order_platform_id));//备用
				}
	        }
	    }
	    else
	    {
	        $is_fba=0;
	    }
	}
    if($is_fba==1)
    {
		$data_time = date('Y-m-d');
		$date_time_detail=date("H:i:s");
        $fba_order_id=0;
        $fba_number=1;
        $fba_order=M('fba_order');
        $check_fba_number = $fba_order->where("date='$data_time'")->order("number desc")->limit(0,1)->select();
        if($check_fba_number)
        {
            $fba_number=$check_fba_number[0][number]+1;
        }
        if($order_id!=0)
        {
            if($fba_order_id==0)
            {
                $data['order_id']=$order_id;
                $data['orderplatform_id']=0;
                $data['number']=$fba_number;
                $data['date']=$data_time;
				$data['date_detail']=$date_time_detail;
                $data['come_from_id']=$come_from_id;
                $data['status']="new";
                $fba_order_id=$fba_order->data($data)->add();
            }            
            $order_web_product = M('order_web_product');
            $order_product_list=$order_web_product->where("order_web_id=$order_id and status<>'return' and status<>'cancel'")->select();
            foreach($order_product_list as $key=>$value_order_product_list)
            {
                $Model = M();
                $code_select=$Model->query("select code,name from id_product_code where id=$value_order_product_list[code_id]");
                $code_value=$code_select[0][code];
                $code_name=$code_select[0][name];
                $update_product_fba=$Model->execute("update product_stock set number=number-$value_order_product_list[number] where code_id=$value_order_product_list[code_id]");
                if($update_product_fba)
                {
                    $fba_order_detail=M("fba_order_detail");
                    $data1['fba_order_id']=$fba_order_id;
                    $data1['code']=$code_value;
                    $data1['name']=$code_name;
                    $data1['number']=$value_order_product_list[number];
                    $fba_order_detail->data($data1)->add();
                }
            }
        }
        else
        {
            if($fba_order_id==0)
            {
                $data['order_id']=0;
                $data['orderplatform_id']=$order_platform_id;
                $data['number']=$fba_number;
                $data['date']=$data_time;
				$data['date_detail']=$date_time_detail;
                $data['come_from_id']=$come_from_id;
                $data['status']="new";
                $fba_order_id=$fba_order->data($data)->add();
            }
            $order_plat_form_product = M('order_plat_form_product');
            $order_product_list=$order_plat_form_product->where("order_platform_id=$order_platform_id and status<>'return' and status<>'cancel'")->select();
            foreach($order_product_list as $key=>$value_order_product_list)
            {    
                $Model = new \Think\Model();
                $code_select=$Model->query("select code,name from id_product_code where id=$value_order_product_list[code_id]");
                $code_value=$code_select[0][code];
                $code_name=$code_select[0][name];
                $update_product_fba=$Model->execute("update product_stock set number=number-$value_order_product_list[number] where code_id=$value_order_product_list[code_id] and style=1");
                if($update_product_fba)
                {
                    $fba_order_detail=M("fba_order_detail");
                    $data1['fba_order_id']=$fba_order_id;
                    $data1['code']=$code_value;
                    $data1['name']=$code_name;
                    $data1['number']=$value_order_product_list[number];
                    $fba_order_detail->data($data1)->add();
                }
            }
        }
    }
    if($is_fba==1)
    {
        return 1;
    }
    else
    {
        return 0;
    }
}



function order_to_factory($order_id,$order_platform_id,$come_from_id,$is_exchange=0)
{
	if($is_exchange==1)
	{
		$fba_order_id=M("fba_order")->where(array("order_id"=>$order_id,"orderplatform_id"=>$order_platform_id))->getField("id");
		if($fba_order_id)
		{
		$del_fba_order_detail=M("fba_order_detail")->where(array("fba_order_id"=>$fba_order_id))->delete();
		if($del_fba_order_detail)
		{
			$del_fba_order=M("fba_order")->where(array("id"=>$fba_order_id))->delete();
			M("order_web_product")->where(array("order_web_id"=>$order_id,"status"=>"new"))->save( array('status'=>'audited') );//修改商品状态已审核
			order_to_factory($order_id,$order_platform_id,$come_from_id);
			exit();
		}
		}
	}
	
	$success=1;
	if($is_exchange==0)
	{
		$check_fba=order_to_fba( $order_id, $order_platform_id, $come_from_id);
	}
	else
	{
		$check_fba=0;
	}
	
    if($check_fba==0)
    {
        $date_time = date('Y-m-d');
		$date_time_detail=date("H:i:s");
        if($order_platform_id==0)//网站
        {
			$factory_order_id=0;
            $order_web_product = M("order_web_product");
			
			$search_str ="order_web_id=$order_id and status<>'return' and status<>'cancel'";
			if($is_exchange==1)
			{
				$search_str = "order_web_id=$order_id and status='new'";
			}

            $order_web_product_list = $order_web_product->where($search_str)->select();
// 			dump($order_web_product_list);exit;
			if($is_exchange==0)
			{
            //定制商品
			$order_web_product_customization = M("order_web_product_customization");
			$order_web_product_customization_detail = $order_web_product_customization->where("order_web_id=$order_id and status<>'return' and status<>'cancel'")->select();
// 			dump($order_web_product_customization_detail);exit;
			if($order_web_product_customization_detail)
			{
				$factory_id = 8;
				$factory_order = M("factory_order");
				$check_factory_order = $factory_order->where(array('order_id' =>$order_id,'factory_id'=>$factory_id,'date'=>"$date_time",'status'=>"new"))->select();
				if($check_factory_order) //如果已有尚未接收的工厂订单，则把新商品加入此订单
				{
					$factory_order_id = $check_factory_order[0][id];
				}
				else
				{
					$factory_order_number_now=1;
					$factory_order_number=$factory_order->where("date='$date_time' and factory_id='$factory_id'")->order("number desc")->limit(0,1)->select();
					if($factory_order_number)
					{
						$factory_order_number_now=$factory_order_number[0][number]+1;
					}
					$data['order_id']=$order_id;
					$data['order_platform_id']=$order_platform_id;
					$data['factory_id']=$factory_id;
					$data['number']=$factory_order_number_now;
					$data['date']=$date_time;
					$data['date_detail']=$date_time_detail;
					$data['accept_time']=0;
					$data['end_time']=0;
					$data['status']="new";
					$data['come_from_id']=$come_from_id;
					$data['delivery_style']="";
					$data['delivery_number']="";
					$factory_order_id=$factory_order->data($data)->add();
				}				
				$factory_order_detail=M("factory_order_detail");
				$data1['factory_order_id']=$factory_order_id;
				$data1['code']="customization";
				$data1['number']=1;
				$data1['description']="<p>".$order_web_product_customization_detail[0][name]."</p>".$order_web_product_customization_detail[0][description]."<p>".$order_web_product_customization_detail[0][href]."</p>";
				$factory_order_detail->data($data1)->add();
			}
			}
			
			//一般商品
            foreach($order_web_product_list as $key => $value_product_list)
            {
				usleep(200);

				if($is_exchange==1)
				{
					M("order_web_product")->where(array("order_web_id"=>$value_product_list[id],"status"=>"new"))->save( array('status'=>'audited') );
				}

//             	dump($value_product_list);exit;
				$check_stock=order_to_stock($order_id,$order_platform_id,$value_product_list[code_id],$value_product_list[number],$come_from_id);
// 				dump($check_stock);exit;
                if($check_stock==0)
                {
					$id_product_code=M("id_product_code");
                    $product_code_detail=$id_product_code->where("id=$value_product_list[code_id]")->select();
                    if($product_code_detail)
                    {
                        $factory_id=$product_code_detail[0][factory_id];
						$factory_order=M("factory_order");
                        $check_factory_order=$factory_order->where(array('order_id' =>$order_id,'factory_id'=>$factory_id,'date'=>"$date_time",'status'=>"new"))->select();
                        if($check_factory_order)
                        {
                            $factory_order_id=$check_factory_order[0][id];
                        }
                        else
                        {	
                            $factory_order_number_now=1;
                            $factory_order_number=$factory_order->where("date='$date_time' and factory_id='$factory_id'")->order("number desc")->limit(0,1)->select();
                            if($factory_order_number)
                            {
                                $factory_order_number_now=$factory_order_number[0][number]+1;
                            }
                            $data['order_id']=$order_id;
                            $data['order_platform_id']=$order_platform_id;
                            $data['factory_id']=$factory_id;
                            $data['number']=$factory_order_number_now;
                            $data['date']=$date_time;
							$data['date_detail']=$date_time_detail;
                            $data['accept_time']=0;
                            $data['end_time']=0;
                            $data['status']="new";
                            $data['come_from_id']=$come_from_id;
							$data['delivery_style']="";
							$data['delivery_number']="";
                            $factory_order_id=$factory_order->data($data)->add();
                        }
                        
                        $factory_order_detail=M("factory_order_detail");
                        $data1['factory_order_id']=$factory_order_id;
                        $data1['code']=$product_code_detail[0][code];
                        $data1['number']=$value_product_list[number];
                        $data1['description']="";
                        $factory_order_detail->data($data1)->add();
                    }
					else
					{
						$order_web_nightwear_customization=M("order_web_nightwear_customization");
						$order_web_nightwear_customization_detail=$order_web_nightwear_customization->where("order_web_product_id=$value_product_list[id] and status<>'return' and status<>'cancel'")->select();
						if($order_web_nightwear_customization_detail)
						{
							$factory_id=7;
							$factory_order=M("factory_order");
							$check_factory_order=$factory_order->where(array('order_id' =>$order_id,'factory_id'=>$factory_id,'date'=>"$date_time",'status'=>"new"))->select();
							if($check_factory_order)
							{
								$factory_order_id=$check_factory_order[0][id];
							}
							else
							{	
								$factory_order_number_now=1;
								$factory_order_number=$factory_order->where("date='$date_time' and factory_id='$factory_id'")->order("number desc")->limit(0,1)->select();
								if($factory_order_number)
								{
									$factory_order_number_now=$factory_order_number[0][number]+1;
								}
								$data['order_id']=$order_id;
								$data['order_platform_id']=$order_platform_id;
								$data['factory_id']=$factory_id;
								$data['number']=$factory_order_number_now;
								$data['date']=$date_time;
								$data['date_detail']=$date_time_detail;
								$data['accept_time']=0;
								$data['end_time']=0;
								$data['status']="new";
								$data['come_from_id']=$come_from_id;
								$data['delivery_style']="";
								$data['delivery_number']="";
								$factory_order_id=$factory_order->data($data)->add();
							}
                        
							$order_web_product_original=M("order_web_product_original");
							$order_web_product_original_detail=$order_web_product_original->where("order_product_id=$value_product_list[order_product_id]")->select();
							
							$factory_order_detail=M("factory_order_detail");
							$data1['factory_order_id']=$factory_order_id;
							$data1['code']="ncustomization";
							$data1['number']=$value_product_list[number];
							$data1['description']="<p>".$value_product_list[0]["product_name"]."</p>"."<p>".$value_product_list[0]["sku"]."</p>"."<p>".$value_product_list[0]["color"]."</p>"."<p>".$value_product_list[0]["href"]."</p>".$order_web_nightwear_customization_detail[0][customization];
							$factory_order_detail->data($data1)->add();
						}
					}
                }
            }
            //追加审核
            if($is_exchange==1)
            {
            	$order_web_product->where($search_str)->save( array('status'=>'audited') );//修改商品状态已审核
            	$order_status_data = array(
            		'status'	=>	'accept',
            		'message'	=>	'新增商品，追加审核',
            		'date_time'	=>	time(),
            		'operator'	=>	session('username'),	
            	); 
            	M('order_web_status')->where( array('order_web_id'=>$order_id) )->save($order_status_data);//修改订单状态为待收货
            	$order_status_data['order_web_id'] = $order_id;
            	M('order_web_status_history')->add($order_status_data);//修改订单状态为待收货
            }
        }
        else //平台
        {
            $factory_order_id=0;
            $order_plat_form_product=M("order_plat_form_product");

			$search_str="order_platform_id=$order_platform_id and status<>'return' and status<>'cancel'";
			if($is_exchange==1)
			{
				$search_str="order_platform_id=$order_platform_id and status='new'";
			}

            $order_plat_form_product_list=$order_plat_form_product->where($search_str)->select();
            foreach($order_plat_form_product_list as $key => $value_platform_product_list)
            {
                usleep(200);
				if($is_exchange==1)
				{
					M("order_plat_form_product")->where(array("order_platform_id"=>$value_platform_product_list[id],"status"=>"new"))->save( array('status'=>'audited') );
				}
				$id_relate_sku=M("id_relate_sku");
				$id_relate_sku_number=$id_relate_sku->where("product_code_id=$value_platform_product_list[code_id] and product_sku_id=$value_platform_product_list[sku_id]")->select();
				if($id_relate_sku_number){
				$value_platform_product_list[number]=$id_relate_sku_number[0][number]*$value_platform_product_list[number];
				}
				$check_stock=order_to_stock($order_id,$order_platform_id,$value_platform_product_list[code_id],$value_platform_product_list[number],$come_from_id);
                if($check_stock==0)
                {
                    $id_product_code=M("id_product_code");
                    $product_code_detail=$id_product_code->where("id=$value_platform_product_list[code_id]")->select();
                    
                    if($product_code_detail)
                    {
                        $factory_order=M("factory_order");
                        $check_factory_order=$factory_order->where(array('order_platform_id' =>$order_platform_id,'factory_id'=>$product_code_detail[0][factory_id],'date'=>$date_time,'status'=>"new"))->select();
                        if($check_factory_order)
                        {
                            $factory_order_id=$check_factory_order[0][id];
                        }
                        else
                        {
                            $factory_id=$product_code_detail[0][factory_id];
                            $factory_order_number_now=1;
                            $factory_order_number=$factory_order->where("date='$date_time' and factory_id='$factory_id'")->order("number desc")->limit(0,1)->select();
                            if($factory_order_number)
                            {
                                $factory_order_number_now=$factory_order_number[0][number]+1;
                            }
                            $data['order_id']=$order_id;
                            $data['order_platform_id']=$order_platform_id;
                            $data['factory_id']=$factory_id;
                            $data['number']=$factory_order_number_now;
                            $data['date']=$date_time;
							$data['date_detail']=$date_time_detail;
                            $data['accept_time']=0;
                            $data['end_time']=0;
                            $data['status']="new";
                            $data['come_from_id']=$come_from_id;
							$data['delivery_style']="";
							$data['delivery_number']="";
                            $factory_order_id=$factory_order->data($data)->add();
                        }
                        
                        $factory_order_detail=M("factory_order_detail");
                        $data1['factory_order_id']=$factory_order_id;
                        $data1['code']=$product_code_detail[0][code];
                        $data1['number']=$value_platform_product_list[number];
                        $data1['description']="";
                        $factory_order_detail->data($data1)->add();
                    }
                }
            }
            
            //追加审核后，修改商品状态为audited
            if($is_exchange==1)
            {
            	$search_str="order_platform_id=$order_platform_id and status='new'";
            	$order_plat_form_product->where($search_str)->save( array('status'=>'audited') );
            }
            
        }
    }
    
    //修改订单状态
    $is_ok=check_order_status($order_id,$order_platform_id);//处理订单状态
    if($is_ok==0)
    {
        if($order_id!=0)//网站
        {
            $order_web_status=M("order_web_status");
            $order_web_status_history=M("order_web_status_history");
// 			$order_web_status_check=$order_web_status->where("order_web_id=$order_id and status='accept'")->select();
// 			if(!$order_web_status_check)
// 			{
// 	            $data[order_web_id]=$order_id;
// 	            $data[status]="accept";
// 	            $data[message]="";
// 	            $data[date_time]=time();
// 	            $data[operator]=I('session.username');
// 	            $order_web_status->add($data);
// 			}
			$data[order_web_id]=$order_id;
			$data[status]="accept";
			$data[message]="";
			$data[date_time]=time();
			$data[operator]=I('session.username');
            $order_web_status_check = $order_web_status->where( array('order_web_id'=>$order_id) )->find();
            if($order_web_status_check)
            {
            	$order_web_status->where( array('order_web_id'=>$order_id) )->save($data);
            }
            else 
            {
            	$order_web_status->add($data);
            }
            $order_web_status_history->add($data);
        }
        else//平台
        {
            $order_plat_form_status=M("order_plat_form_status");
            $order_plat_form_status_history=M("order_plat_form_status_history");
// 			$order_plat_form_status_check=$order_plat_form_status->where("order_platform_id=$order_id and status='accept'")->select();
// 			if(!$order_plat_form_status_check)
// 			{
// 	            $data[order_platform_id]=$order_platform_id;
// 	            $data[status]="accept";
// 	            $data[message]="";
// 	            $data[date_time]=time();
// 	            $data[operator]=I('session.username');
// 	            $order_plat_form_status->add($data);
// 			}
			$data[order_platform_id]=$order_platform_id;
			$data[status]="accept";
			$data[message]="";
			$data[date_time]=time();
			$data[operator]=I('session.username');
            $order_plat_form_status_check = $order_plat_form_status->where( array("order_platform_id"=>$order_platform_id) )->select();
            if($order_plat_form_status_check)
            {
            	$order_plat_form_status->where( array("order_platform_id"=>$order_platform_id) )->save($data);
            }
            else
            {
            	$order_plat_form_status->add($data);
            }
            $order_plat_form_status_history->add($data);
        }
    }
}

function order_to_stock($order_id,$order_platform_id,$code_id,$number,$come_from_id)
{
// 	dump($code_id);exit;
	$product_stock_order_id = 0;
	$date_time = date('Y-m-d');
	$date_time_detail=date("H:i:s");
    $product_stock=M('product_stock');
    $product_stock_check=$product_stock->where("code_id=$code_id and style=2 and number>$number")->select();
// 	dump($product_stock_check);exit;
    if($product_stock_check && $code_id!=0)
    {
		$product_stock_order=M('product_stock_order');
        $check_product_stock_order=$product_stock_order->where("order_id=$order_id and order_platform_id=$order_platform_id and date='$date_time' and status='new'")->select();
// 		dump($check_product_stock_order);exit;
        if($check_product_stock_order)
        {
            $product_stock_order_id=$check_product_stock_order[0][id];          
        }
        else
        {
            $stock_number=1;
            $check_stock_number=$product_stock_order->where(array("date"=>$date_time))->order("number desc")->limit(0,1)->select();

            if($check_stock_number)
            {
                $stock_number=$check_stock_number[0][number]+1;
            }
            $data['order_id']=$order_id;
            $data['order_platform_id']=$order_platform_id;
            $data['number']=$stock_number;
            $data['date']=$date_time;
			$data['date_detail']=$date_time_detail;
            $data['come_from_id']=$come_from_id;
            $data['is_work']=1;
            $data['status']="new";
            $product_stock_order_id=$product_stock_order->data($data)->add();
        }
        if($product_stock_order_id!=0)
        {
//         	exit($product_stock_order_id);
            $id_product_code = M('id_product_code');
            $code_name = $id_product_code->WHERE("id=$code_id")->select();
            $product_stock_order_detail = M("product_stock_order_detail");
            $data_detail['product_stock_order_id'] = $product_stock_order_id;
            $data_detail['code'] = $code_name[0][code];
            $data_detail['number'] = $number;
            $data_detail['is_work'] = 1;
            $product_stock_order_detail_add = $product_stock_order_detail->data($data_detail)->add();

            if($product_stock_order_detail_add)
            {
                $Model = new \Think\Model();
                $Model->execute("update `product_stock` set `number`=`number`-$number where `code_id`=$code_id and style=2");
                return 1;
            }
        }
        else
        {
            return 0;
        }
    }
    else
    {
        return 0;
    }
	
}

function check_order_status($order_id,$order_platform_id)
{
	$is_ok=0;
    $factory_order=M("factory_order");
    $factory_order_check=$factory_order->where("order_id=$order_id and order_platform_id=$order_platform_id and factory_id<>9")->select();
// 	dump($factory_order_check);exit;
    if($factory_order_check)//追加审核 操作，很有可能是invalid结果。。从而不能更新订单状态
    {
		$is_shipping=1;
        foreach ($factory_order_check as $key=> $value_factory_order)
        {
            if($value_factory_order[status]!="history" && $value_factory_order[status]!="history_ok" )//???
            {
                $is_shipping=0;
            }
        }
// 		dump($is_shipping);exit;
        if($is_shipping==1)
        {
            if($order_id!=0)//网站
            {
                $order_web_status=M("order_web_status");
                $check_web_status=$order_web_status->where("order_web_id=$order_id")->select();
                if($check_web_status)
                {
                    $id=$check_web_status[0][id];
                    $data[order_web_id]=$order_id;
                    $data[status]="shipping";
                    $data[message]="";
                    $data[date_time]=time();
                    $data[operator]=I('session.username');
                    $order_web_status->where("id=$id")->save($data);
                }
                else
                {
                    $data[order_web_id]=$order_id;
                    $data[status]="shipping";
                    $data[message]="";
                    $data[date_time]=time();
                    $data[operator]=I('session.username');
                    $order_web_status->add($data);
                }
                $order_web_status_history=M("order_web_status_history");
                $data[order_web_id]=$order_id;
                $data[status]="shipping";
                $data[message]="";
                $data[date_time]=time();
                $data[operator]=I('session.username');
                $order_web_status_history->add($data);
                $is_ok=1;
             }
             else
             {
                
                 $order_plat_form_status=M("order_plat_form_status");
                 $check_plat_form_status=$order_plat_form_status->where("order_platform_id=$order_platform_id")->select();
                 if($check_plat_form_status)
                 {
                     $id=$check_plat_form_status[0][id];
                     $data[order_platform_id]=$order_platform_id;
                     $data[status]="shipping";
                     $data[message]="";
                     $data[date_time]=time();
                     $data[operator]=I('session.username');
                     $order_plat_form_status->where("id=$id")->save($data);
                 }
                 else
                 {
                     $data[order_platform_id]=$order_platform_id;
                     $data[status]="shipping";
                     $data[message]="";
                     $data[date_time]=time();
                     $data[operator]=I('session.username');
                     $order_plat_form_status->add($data);
                 }
                 
                $order_plat_form_status=M("order_plat_form_status_history");
                $data[order_platform_id]=$order_platform_id;
                $data[status]="shipping";
                $data[message]="";
                $data[date_time]=time();
                $data[operator]=I('session.username');
                $order_plat_form_status->add($data);
                $is_ok=1;
             }
          }
          else
          {
              return "invalid";
          }
    }
    else
    {
    	//fba订单
        $fba_order=M("fba_order");
        $fba_order_check=$fba_order->where("order_id=$order_id and orderplatform_id=$order_platform_id")->select();
        if($fba_order_check)
        {
            if($order_id!=0)
            {
                $order_web_status=M("order_web_status");
                $check_web_status=$order_web_status->where("order_web_id=$order_id")->select();
                if($check_web_status)
                {
                    $id=$check_web_status[0][id];
                    $data[order_web_id]=$order_id;
                    $data[status]="shipping";
                    $data[message]="";
                    $data[date_time]=time();
                    $data[operator]=I('session.username');
                    $order_web_status->where("id=$id")->save($data);
                }
                else
                {
                    $data[order_web_id]=$order_id;
                    $data[status]="shipping";
                    $data[message]="";
                    $data[date_time]=time();
                    $data[operator]=I('session.username');
                    $order_web_status->add($data);
                }
                
                $order_web_status_history=M("order_web_status_history");
                $data[order_web_id]=$order_id;
                $data[status]="shipping";
                $data[message]="";
                $data[date_time]=time();
                $data[operator]=I('session.username');
                $order_web_status_history->add($data);
                $is_ok=1;
            }
            else
            {
                
                $order_plat_form_status=M("order_plat_form_status");
                $check_plat_form_status=$order_plat_form_status->where("order_platform_id=$order_platform_id")->select();
                if($check_plat_form_status)
                {
                    $id=$check_plat_form_status[0][id];
                    $data[order_platform_id]=$order_platform_id;
                    $data[status]="shipping";
                    $data[message]="";
                    $data[date_time]=time();
                    $data[operator]=I('session.username');
                    $order_plat_form_status->where("id=$id")->save($data);
                }
                else
                {
                    $data[order_platform_id]=$order_platform_id;
                    $data[status]="shipping";
                    $data[message]="";
                    $data[date_time]=time();
                    $data[operator]=I('session.username');
                    $order_plat_form_status->add($data);
                }
                
                $order_plat_form_status_history=M("order_plat_form_status_history");
                $data[order_platform_id]=$order_platform_id;
                $data[status]="shipping";
                $data[message]="";
                $data[date_time]=time();
                $data[operator]=I('session.username');
                $order_plat_form_status_history->add($data);
                $is_ok=1;
            }
        }
        //库存订单
        else 
        {
            $product_stock_order=M("product_stock_order");
            $product_stock_order_check=$product_stock_order->where("order_id=$order_id and order_platform_id=$order_platform_id")->select();
            if($product_stock_order_check)
            {
                if($order_id!=0)
                {
                    
                    $order_web_status=M("order_web_status");
                    $check_web_status=$order_web_status->where("order_web_id=$order_id")->select();
                    if($check_web_status)
                    {
                        $id=$check_web_status[0][id];
                        $data[order_web_id]=$order_id;
                        $data[status]="shipping";
                        $data[message]="";
                        $data[date_time]=time();
                        $data[operator]=I('session.username');
                        $order_web_status->where("id=$id")->save($data);
                    }
                    else
                    {
                        $data[order_web_id]=$order_id;
                        $data[status]="shipping";
                        $data[message]="";
                        $data[date_time]=time();
                        $data[operator]=I('session.username');
                        $order_web_status->add($data);
                    }
                    $order_web_status_history=M("order_web_status_history");
                    $data[order_web_id]=$order_id;
                    $data[status]="shipping";
                    $data[message]="";
                    $data[date_time]=time();
                    $data[operator]=I('session.username');
                    $order_web_status_history->add($data);
                    $is_ok=1;
                }
                else
                {
                    
                    $order_plat_form_status=M("order_plat_form_status");
                    $check_plat_form_status=$order_plat_form_status->where("order_platform_id=$order_platform_id")->select();
                    if($check_plat_form_status)
                    {
                        $id=$check_plat_form_status[0][id];
                        $data[order_platform_id]=$order_platform_id;
                        $data[status]="shipping";
                        $data[message]="";
                        $data[date_time]=time();
                        $data[operator]=I('session.username');
                        $order_plat_form_status->where("id=$id")->save($data);
                    }
                    else
                    {
                        $data[order_platform_id]=$order_platform_id;
                        $data[status]="shipping";
                        $data[message]="";
                        $data[date_time]=time();
                        $data[operator]=I('session.username');
                        $order_plat_form_status->add($data);
                    }
                    
                    $order_plat_form_status_history=M("order_plat_form_status_history");
                    $data[order_platform_id]=$order_platform_id;
                    $data[status]="shipping";
                    $data[message]="";
                    $data[date_time]=time();
                    $data[operator]=I('session.username');
                    $order_plat_form_status_history->add($data);
                    $is_ok=1;
                }
            }
            else
            {
                return "invalid";
            }
        }
    }
    return $is_ok;
}

function order_product_return($order_id,$order_plat_id,$product_code,$product_array,$customization_code,$product_customization_array)
{
	//工厂	
	$factory_order=M("factory_order");
	$check_factory_order=$factory_order->where("order_id=$order_id and order_platform_id=$order_plat_id")->field("id,status")->select();
	
	foreach($check_factory_order as $value)
	{
		$factory_order_id=$value[id];
		$factory_order_detail=M("factory_order_detail");
		if($value["status"]=="new")
		{
			foreach($product_code as $val_code)
			{
				$factory_order_detail->where(array("factory_order_id"=>$factory_order_id,"code"=>$val_code))->delete();
			}
			//定制单
			foreach($customization_code as $cus_code)
			{
				$factory_order_detail->where(array("factory_order_id"=>$factory_order_id,"code"=>$cus_code))->delete();
			}
			
			$factory_order_detail_new=$factory_order_detail->where(array("factory_order_id"=>$value[id]))->select();
			if(!$factory_order_detail_new)
			{
				$delete_factory_order=$factory_order->where(array("id"=>$value[id]))->delete();	
			}
		}
		else
		{
			foreach($product_code as $val_code)
			{
				$factory_order_detail->where(array("factory_order_id"=>$factory_order_id,"code"=>$val_code))->save(array("status"=>"cancel"));
			}
			
			foreach($customization_code as $cus_code)
			{
				$factory_order_detail->where(array("factory_order_id"=>$factory_order_id,"code"=>$cus_code))->save(array("status"=>"cancel"));
			}
		}
	}

	$product_stock_order=M("product_stock_order");//库存
	$check_product_stock_order=$product_stock_order->where("order_id=$order_id and order_platform_id=$order_plat_id")->field("id,status")->select();

	foreach($check_product_stock_order as $value)
	{
		if($value["status"]=="new")
		{
			$product_stock_order_detail=M("product_stock_order_detail");
			foreach($product_code as $value_code)
			{
				$product_stock_order_id=$value[id];
				$code_number=$product_stock_order_detail->where(array("product_stock_order_id"=>$product_stock_order_id,"code"=>$value_code))->getField("number");
				if($code_number)
				{
					$id_product_code=M("id_product_code");
					$code_id=$id_product_code->where(array("code"=>$value_code))->getField("id");
					$code_id_value=$code_id;
					$code_number_value=$code_number;
					$Model = new \Think\Model();
					$update_product_fba=$Model->execute("update product_stock set number=number+$code_number_value where code_id=$code_id_value and style=2");
					if($update_product_fba)
					{
						$is_delete=$product_stock_order_detail->where(array("product_stock_order_id"=>$product_stock_order_id,"code"=>$value_code))->delete();
					}
				}
			}
			$product_stock_order_detail_new=$product_stock_order_detail->where(array("product_stock_order_id"=>$product_stock_order_id))->select();
			if(!$product_stock_order_detail_new)
			{
				$delete_stock_order=$product_stock_order->where(array("id"=>$value[id]))->delete();
			}
		}
		else
		{
			foreach($product_code as $val_code)
			{
				$product_stock_order_id=$value[id];
				$product_stock_order_detail=M("product_stock_order_detail");
				$is_save=$product_stock_order_detail->where(array("product_stock_order_id"=>$product_stock_order_id,"code"=>$val_code))->save(array("status"=>"cancel"));
				if($is_save)
				{
					$code_number=$product_stock_order_detail->where(array("product_stock_order_id"=>$product_stock_order_id,"code"=>$val_code))->getField("number");
					if($code_number)
					{
						$id_product_code=M("id_product_code");
						$code_id=$id_product_code->where(array("code"=>$val_code))->getField("id");
						$code_id_value=$code_id;
						$code_number_value=$code_number;
						$Model = new \Think\Model();
						$update_product_fba=$Model->execute("update product_stock set number=number+$code_number_value where code_id=$code_id_value and style=2");
					}
					
				}
			}
		}
	}
	//FBA
	$fba_order_DB=M("fba_order");
	$fba_order_detail_DB=M("fba_order_detail");
	
	$fba_order=$fba_order_DB->where(array("order_id=$order_id and orderplatform_id=$order_plat_id"))->field("id")->select();
	
	foreach($fba_order as $val)
	{
		$fba_order_id=$val[id];
		foreach($product_code as $val_code)
		{
			$fba_order_detail_DB->where(array("fba_order_id"=>$fba_order_id,"code"=>$val_code))->save(array("status"=>"cancel"));
		}
	}
	
	//更改订单产品的状态
	if($order_id==0)//平台
	{
		foreach($product_array as $val)
		{
			$save_order_product_status=M("order_plat_form_product")->where(array("id"=>$val))->save(array("status"=>"cancel"));
		}
	}
	elseif($order_plat_id==0)//网站
	{
		//定制单
		foreach($product_customization_array as $val)
		{
			$save_order_web_product_customization=M("order_web_product_customization")->where(array("id"=>$val))->save(array("status"=>"cancel"));
		}
		
		foreach($product_array as $val)
		{
			$save_order_product_status=M("order_web_product")->where(array("id"=>$val))->save(array("status"=>"cancel"));
		}
	}
}

function factory_all_cancel($id,$is_come)
{
		$is_all_cancel="yes";
		$factory_order_status=M("factory_order_detail")->where(array("factory_order_id"=>$id))->field("status")->select();
		foreach($factory_order_status as $value_status)
		{
			if($value_status[status]!="cancel")
			{
				$is_all_cancel="no";
			}
		}
	return $is_all_cancel;
}