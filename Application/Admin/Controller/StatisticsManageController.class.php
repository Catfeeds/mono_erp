<?php 
	namespace Admin\Controller;
	use Admin\Controller\CommonController;
	Class StatisticsManageController extends CommonController
	{
		public function index()
		{
			$this->display();
		}
		//平台统计
		public function statistics_platform()
		{
			$order_plat_form = D('order_plat_form');
			//import('Org.Util.Page');// 导入分页类
			$id_catalog = D('id_catalog');
			$id_product = D('id_product');
			$id_come_from =D('id_come_from');
			$id_product_code = D('id_product_code');
			$order_plat_form_product = D('order_plat_form_product');
			//展示所有分类
			$catalog_list = $id_catalog->where(array('is_work'=>1))->field('id,name')->select();
			$day_arr=array();
			$this->assign('catalog_list',$catalog_list);
			$come_from_id = $id_come_from->where(array('style'=>'plat'))->select();
			$this->assign('come_from_id',$come_from_id);
			if(I('get.catalog_id'))
			{
				//展示所有产品
				$product_list = $id_product->where(array('catalog_id'=>I('get.catalog_id'),'is_work'=>1))->field('id,name')->select();
				$this->assign('product_list',$product_list);
			}
			//提交按钮处理
			if(isset($_POST['sub']))
			{
				//如果存在来源并且不等于0
				if(I('get.come_from_id'))
				{
					$map['come_from_id'] = I('get.come_from_id');
				}	
				//如果分类id等于0
				//如果产品id不等于0
				if(I('get.catalog_id') && I('get.product_id'))
				{
					$product_code_list = $id_product_code->where(array('product_id'=>I('get.product_id'),'is_work'=>1))->field('id')->select();
					if($product_code_list)
					{
						$order_platform_product_map = array();
						foreach($product_code_list as $value_product_code_list)
						{
						    //echo "select id_product_code.name,order_plat_form_product.order_platform_id,order_plat_form_product.price,order_plat_form_product.number from order_plat_form_product inner join id_product_code on order_plat_form_product.code_id=id_product_code.id where id_product_code.id=$value_product_code_list[id]";
							$order_plat_form_product_list = $order_plat_form_product->where(array('code_id'=>$value_product_code_list['id']))->select();
							if($order_plat_form_product_list)
							{
								foreach($order_plat_form_product_list as $value_order_plat_form_product_list)
								{
									$order_platform_product_map[] = $value_order_plat_form_product_list['code_id'];
								}
							}
						}
					}
				}
				else
				{
					$product_detail = $id_product->where(array('catalog_id'=>I('get.catalog_id'),'is_work'=>1))->field('id')->select();
					$product_array = array();
					foreach($product_detail as $value_product_detail)
					{
						$product_array[] = $value_product_detail['id'];
					}
					if($product_array)
					{
						$product_where['product_id'] = array('in',$product_array);
					}
					
					$product_where['is_work'] = 1;
					$product_code_list = $id_product_code->where($product_where)->field('id')->select();
					if($product_code_list)
					{
						$order_platform_product_map = array();
						foreach($product_code_list as $value_product_code_list)
						{
							$order_plat_form_product_list = $order_plat_form_product->where(array('code_id'=>$value_product_code_list['id']))->select();
							if($order_plat_form_product_list)
							{
								foreach($order_plat_form_product_list as $value_order_plat_form_product_list)
								{
									$order_platform_product_map[] = $value_order_plat_form_product_list['code_id'];
								}
							}
						}
					}
				}
				
				//print_r($order_platform_product_map);				
				if($order_platform_product_map)
				{
					$product_code_where['code_id'] = array('in',$order_platform_product_map);
					if($product_code_where)
					{
						$order_plat_form_product_detail = $order_plat_form_product->where($product_code_where)->field('order_platform_id,name,code_id,price,number')->select();
						if($order_plat_form_product_detail)
						{
							$order_plat_form_where = array();
							foreach($order_plat_form_product_detail as $value_order_plat_form_product_detail)
							{
								$order_plat_form_where[] = $value_order_plat_form_product_detail['order_platform_id'];
							}
							$map['id'] = array('in',$order_plat_form_where);
						}
					}
				}
				if(I('post.begin_time')!='' && I('post.end_time')!='')
				{
					$begin_time = I('post.begin_time')." 00:00:00";
					$end_time = I('post.end_time')." 23:59:59";
				}
				else 
				{
					$begin_time = date("Y-m-01 00:00:00",time());
					$end_time = date("Y-m-d 23:59:59",time());
				}
				$order_begin_time = strtotime($begin_time);
				$order_end_time = strtotime($end_time);
				$day = ceil(($order_end_time-$order_begin_time)/(3600*24));
				
				$statistics_list = array();
				for($i=0;$i<$day;$i++)
				{
					$every_day = date("Y-m-d",($order_begin_time+($i*3600*24)));
					$start_day = date("$every_day 00:00:00");
					$end_day = date("$every_day 23:59:59");
					$map['date_time'] =  array(array('egt',$start_day),array('elt',$end_day));
					$statistics_list[$every_day] = $order_plat_form->where($map)->order("date_time desc")->limit($Page->firstRow.','.$Page->listRows)->relation(true)->select();
				}
			}
			$statistics_name = array();
			foreach($statistics_list as $value_statistics_key=>$value_statistics_list)
			{
				foreach($value_statistics_list as $order_platfrom_value)
				{
					foreach($order_platfrom_value['product_info'] as $product_value)
					{
						$product_name = $id_product_code->where(array('id'=>$product_value['code_id'],'is_work'=>1))->getField('name');
						$statistics_name[$value_statistics_key][name][$product_name]['currency'] = $order_platfrom_value['currency'];
						$statistics_name[$value_statistics_key][name][$product_name]['number'] += $product_value['number'];
						$statistics_name[$value_statistics_key][name][$product_name]['price'] += $product_value['number']*$product_value['price'];
					}
				}
			}
			//print_r($statistics_name);
			/*
			foreach($statistics_name as $statistics_name_value)
			{
			    foreach($statistics_name_value['name'] as $key=>$value)
			    {
			        echo $key."<hr>";
			        echo $value['currency'];
			    }
			}
			/*
			$count = count($day);// 查询满足要求的总记录数 $map表示查询条件
			$Page = new \Think\Page1($count,C('LISTROWS'));// 实例化分页类 传入总记录数
			$show = $Page->show();// 分页显示输出
			*/
			//print_r($statistics_name);
			$this->assign('statistics_name',$statistics_name);
			$this->assign('page',$show);
			$this->display();
				/*
				for($i=0;$i<$day;$i++)
				{
					$every_day=date("Y-m-d",($order_begin_time+($i*3600*24)));
					$start = date("$every_day 00:00:00");
					$end = date("$every_day 23:59:59");
					$map['date_time'] =  array(array('egt',$start),array('elt',$end));
				}
				*/
				
				
				/*
				if($product_code_list)
				{
					foreach($product_code_list as $value)
					{
						$order_plat_form_product = D('order_plat_form_product');
						$order_plat_form_product_list = $order_plat_form_product->where(array('code_id'=>$value['id']))->relation(true)->select();
						$order_platform_product_array = array();
						foreach($order_plat_form_product_list as $order_plat_form_product_list_value)
						{
							$order_platform_product_array[] = $order_plat_form_product_list_value['order_platform_id'];
						}
						$map['id'] = array('in',$order_platform_product_array);
					}
				}
				*/
				/*
				if(I('post.begin_time')!='' && I('post.end_time')!='')
				{
					$begin_time = date("Y-m-d H:i:s",strtotime(I('post.begin_time')));
					$end_time = date("Y-m-d 00:00:00",(strtotime(I('post.end_time'))+3600*24));
				}
				else
				{
					$begin_time=date("Y-m-01 00:00:00",time());
					$end_time=date("Y-m-d 00:00:00",(time()+3600*24));
				}
				$order_begin_time=strtotime($begin_time);
				$order_end_time=strtotime($end_time);
				$day=($order_end_time-$order_begin_time)/(3600*24);//开始时间戳减去结束时间戳在除以一天的时间就得到这个月的天数
				for($i=0;$i<$day;$i++)
				{
					$every_day=date("Y-m-d",($order_begin_time+($i*3600*24)));
					$start = date("$every_day 00:00:00");
					$end = date("$every_day 23:59:59");
					$map['date_time'] = array("between","$start,$end");
					$list = $order_plat_form->where($map)->order("date_time desc")->limit($Page->firstRow.','.$Page->listRows)->relation(true)->select();
					if($product_code_list)
					{
						foreach($product_code_list as $product_code_list_value)
						{
							foreach($list as $list_value)
							{
								foreach($list_value['product_info'] as $product_info_value)
								{
									if($product_code_list_value['id']==$product_info_value['code_id'])
									{
										$product_code = $id_product_code->where(array('id'=>$product_info_value['code_id']))->getField('name');
										
										$day_arr[$every_day][]= array('currency'=>$list_value['currency'],'name'=>$product_code,'price'=>$product_info_value['price'],'number'=>$product_info_value['number']);
									}
								}
							}
						}
					}
					else 
					{
						foreach($list as $list_value)
						{
							foreach($list_value['product_info'] as $product_info_value)
							{
								$product_code = $id_product_code->where(array('id'=>$product_info_value['code_id']))->getField('name');
									
								$day_arr[$every_day][]= array('currency'=>$list_value['currency'],'name'=>$product_code,'price'=>$product_info_value['price'],'number'=>$product_info_value['number']);
						
							}
						}	
					}

				}
				
				$count = count($day_arr);// 查询满足要求的总记录数 $map表示查询条件
				$Page = new \Think\Page1($count,C('LISTROWS'));// 实例化分页类 传入总记录数
				$show = $Page->show();// 分页显示输出
				$names_array = array();
				*/
				//处理同一天中code名称一样的数量
				/*
				foreach($day_arr as $day_arr_key=>$day_arr_value)
				{
					foreach($day_arr_value as $names_value)
					{
						if(@$names_array[$day_arr_key][$names_value['name']])
						{
							$names_array[$day_arr_key][$names_value['name']]['number'] += $names_value['number'];
							$names_array[$day_arr_key][$names_value['name']]['price'] += $names_value['price'];
						}
						else
						{
							$names_array[$day_arr_key][$names_value['name']] = $names_value;
						}
					}
				}
				*/
				/*
				foreach($names_array as $day_arr_value)
				{
					foreach($day_arr_value as $day_arr_value_list)
					{
						switch($day_arr_value_list['currency'])
						{
							case 'EUR' :
								$eur_price = round($day_arr_value_list['price']*$day_arr_value_list['number']);
								$parities = 1.1296;
								$parities_eur_price = round($day_arr_value_list['price']*$parities*$day_arr_value_list['number']);
								$parities_eur+=$parities_eur_price;
								$eur+=$eur_price;
								$this->assign('eur',$eur);
								break;
							case 'GBP' :
								$gbp_price = round($day_arr_value_list['price']*$day_arr_value_list['number']);
								$parities = 1.4334;
								$parities_gbp_price = round($day_arr_value_list['price']*$parities*$day_arr_value_list['number']);
								$parities_gbp+=$parities_gbp_price;
								$gbp+=$gbp_price;
								$this->assign('gbp',$gbp);
								break;
							case 'AUD' :
								$aud_price = round($day_arr_value_list['price']*$day_arr_value_list['number']);
								$parities = 0.7803;
								$parities_aud_price = round($day_arr_value_list['price']*$parities*$day_arr_value_list['number']);
								$parities_aud+=$parities_aud_price;
								$aud+=$aud_price;
								$this->assign('aud',$aud);
								break;
							case 'CAD' :
								$cad_price = round($day_arr_value_list['price']*$day_arr_value_list['number']);
								$parities = 0.7913;
								$parities_cad_price = round($day_arr_value_list['price']*$parities*$day_arr_value_list['number']);
								$parities_cad+=$parities_cad_price;
								$cad+=$cad_price;
								$this->assign('cad',$cad);
								break;
							default:
								$usd_price = round($day_arr_value_list['price']*$day_arr_value_list['number']);
								$parities = 1;
								$parities_usd_price = round($day_arr_value_list['price']*$parities*$day_arr_value_list['number']);
								$parities_usd+=$parities_usd_price;
								$usd+=$usd_price;
								$this->assign('usd',$usd);
						}
						$totle_price =$parities_eur+$parities_gbp+$parities_aud+$parities_cad+$parities_usd;
						$totle_number+=$day_arr_value_list['number'];
						$this->assign('totle_price',$totle_price);
						$this->assign('totle_number',$totle_number);
					}
				
				}
				*/
			
			/*
			else 
			{
				$begin_time=date("Y-m-01 00:00:00",time());
				$end_time=date("Y-m-d 00:00:00",(time()+3600*24));
				$order_begin_time=strtotime($begin_time);
				$order_end_time=strtotime($end_time);
				$day=($order_end_time-$order_begin_time)/(3600*24);
				for($i=0;$i<$day;$i++)
				{
					$every_day=date("Y-m-d",($order_begin_time+($i*3600*24)));
					$start = date("$every_day 00:00:00");
					$end = date("$every_day 23:59:59");
					$map['date_time'] = array("between","$start,$end");
					$list = $order_plat_form->where($map)->order("date_time desc")->limit($Page->firstRow.','.$Page->listRows)->relation(true)->select();
					foreach($list as $list_value)
					{
						foreach($list_value['product_info'] as $product_info_value)
						{
							$product_code = $id_product_code->where(array('id'=>$product_info_value['code_id']))->getField('name');
							$day_arr[$every_day][]= array('currency'=>$list_value['currency'],'name'=>$product_code,'price'=>$product_info_value['price'],'number'=>$product_info_value['number']);
						}
					}
				}
			$count = count($day_arr);// 查询满足要求的总记录数 $map表示查询条件
				$Page = new \Page($count,C('LISTROWS'));// 实例化分页类 传入总记录数
				$show = $Page->show();// 分页显示输出
				$names_array = array();
				//处理同一天中code名称一样的数量
				foreach($day_arr as $day_arr_key=>$day_arr_value)
				{
					foreach($day_arr_value as $names_value)
					{
						if(@$names_array[$day_arr_key][$names_value['name']])
						{
							$names_array[$day_arr_key][$names_value['name']]['number'] += $names_value['number'];
						}
						else
						{
							$names_array[$day_arr_key][$names_value['name']] = $names_value;
						}
					}
				}
				foreach($names_array as $day_arr_value)
				{
					foreach($day_arr_value as $day_arr_value_list)
					{
						switch($day_arr_value_list['currency'])
						{
							case 'EUR' :
								$eur_price = round($day_arr_value_list['price']*$day_arr_value_list['number']);
								$parities = 1.1296;
								$parities_eur_price = round($day_arr_value_list['price']*$parities*$day_arr_value_list['number']);
								$parities_eur+=$parities_eur_price;
								$eur+=$eur_price;
								$this->assign('eur',$eur);
								break;
							case 'GBP' :
								$gbp_price = round($day_arr_value_list['price']*$day_arr_value_list['number']);
								$parities = 1.4334;
								$parities_gbp_price = round($day_arr_value_list['price']*$parities*$day_arr_value_list['number']);
								$parities_gbp+=$parities_gbp_price;
								$gbp+=$gbp_price;
								$this->assign('gbp',$gbp);
								break;
							case 'AUD' :
								$aud_price = round($day_arr_value_list['price']*$day_arr_value_list['number']);
								$parities = 0.7803;
								$parities_aud_price = round($day_arr_value_list['price']*$parities*$day_arr_value_list['number']);
								$parities_aud+=$parities_aud_price;
								$aud+=$aud_price;
								$this->assign('aud',$aud);
								break;
							case 'CAD' :
								$cad_price = round($day_arr_value_list['price']*$day_arr_value_list['number']);
								$parities = 0.7913;
								$parities_cad_price = round($day_arr_value_list['price']*$parities*$day_arr_value_list['number']);
								$parities_cad+=$parities_cad_price;
								$cad+=$cad_price;
								$this->assign('cad',$cad);
								break;
							default:
								$usd_price = round($day_arr_value_list['price']*$day_arr_value_list['number']);
								$parities = 1;
								$parities_usd_price = round($day_arr_value_list['price']*$parities*$day_arr_value_list['number']);
								$parities_usd+=$parities_usd_price;
								$usd+=$usd_price;
								$this->assign('usd',$usd);
						}
						$totle_price =$parities_eur+$parities_gbp+$parities_aud+$parities_cad+$parities_usd;
						$totle_number+=$day_arr_value_list['number'];
						$this->assign('totle_price',$totle_price);
						$this->assign('totle_number',$totle_number);
					}
				
				}
			}
			*/
		}
		
		//网站统计
		public function statistics_web()
		{
			$order_web = D('order_web');
		//	import('Org.Util.Page');// 导入分页类
			$id_catalog = D('id_catalog');
			$id_product = D('id_product');
			$id_product_code = D('id_product_code');
			$id_product_sku = D('id_product_sku');
			$id_come_from = D('id_come_from');
			$order_web = D("order_web");
			//展示所有分类
			$catalog_list = $id_catalog->where(array('is_work'=>1))->field('id,name')->select();
			$day_arr=array();
			$this->assign('catalog_list',$catalog_list);
			$come_from_id = $id_come_from->where(array('style'=>'web'))->select();
			$this->assign('come_from_id',$come_from_id);
			if(I('get.catalog_id'))
			{
				//展示所有产品
				$product_list = $id_product->where(array('catalog_id'=>I('get.catalog_id'),'is_work'=>1))->field('id,name')->select();
				$this->assign('product_list',$product_list);
			}
			if(isset($_POST['sub']))
			{
			    //如果存在来源并且不等于0
			    $order_web_sql = "select id_product.name,id_product_code.name,order_web_product.set_sku,order_web_product.discount_price,order_web_product.number,order_web.date_time from id_product,id_product_code,order_web_product,order_web where id_product.id=id_product_code.product_id and id_product_code.id=order_web_product.code_id and order_web_product.order_web_id=order_web.id and id_product.is_work=1 and id_product_code.is_work=1";
			    if(I('get.come_from_id'))
			    {
			        $order_web_sql.= " and order_web.come_from_id=".I('get.come_from_id');
			    }
			    //如果产品id不等于0
			    if(I('get.catalog_id') && I('get.product_id'))
			    {
                    $order_web_sql.= " and id_product.id=".I('get.product_id');
			    }
			    if(I('post.begin_time')!='' && I('post.end_time')!='')
			    {
			        $begin_time = I('post.begin_time')." 00:00:00";
			        $end_time = I('post.end_time')." 23:59:59";
			    }
			    else
			    {
			        $begin_time = date("Y-m-01 00:00:00",time());
			        $end_time = date("Y-m-d 23:59:59",time());
			    }
			    $order_web_sql.= " and order_web.date_time>='$begin_time' and order_web.date_time<='$end_time' order by date_time asc";
			    //echo $order_web_sql;
			     
			    
			    $order_begin_time = strtotime($begin_time);
			    $order_end_time = strtotime($end_time);
			    $day = ceil(($order_end_time-$order_begin_time)/(3600*24));
			    $statistics_day = array();
			    for($i=0;$i<$day;$i++)
			    {
			        $every_day = date("Y-m-d",($order_begin_time+($i*3600*24)));
			        $statistics_day[] = $every_day;
			    }
			    //print_r($statistics_day);
			    $order_web_day = array();
			    foreach($statistics_day as $statistics_day_value)
			    {
			        foreach($order_web_list as $order_web_list_value)
			        {
			            if($statistics_day_value==substr($order_web_list_value[date_time],0,-9))
			            {
			                $order_web_day[$statistics_day_value][$order_web_list_value[name]]['number'] += $order_web_list_value[number];
			                $order_web_day[$statistics_day_value][set_sku]['discount_price'] += $order_web_list_value[discount_price].$order_web_list_value[number];
			                $order_web_day[$statistics_day_value][set_sku]['number'] += $order_web_list_value[number];
			                array("name"=>$order_web_list_value[name],"set_sku"=>$order_web_list_value[set_sku],"discount_price"=>$order_web_list_value[discount_price],"number"=>$order_web_list_value[number]);
			            }
			        }
			        
			    }
			}
			//print_r($order_web_day);
			$this->order_web_day=$order_web_day;
			
			$this->display();
		}
		public function statistics_chart()
		{
			//引入highcharts支持库
			$order_plat_form = D('order_plat_form');
			$id_product_code = D('id_product_code');
			if(I('post.come_from_id'))
			{
				$map['come_from_id'] = I('post.come_from_id');
			}
			//如果存在分类id
			if(I('post.catalog_id'))
			{
				$code_map['catalog_id'] = I('post.catalog_id');
				//如果存在分类id和产品id
				if(I('post.catalog_id') && I('post.product_id'))
				{
					$code_map['product_id'] = I('post.product_id');
				}
				$code_map['is_work'] = 1;
				$product_code_list = $id_product_code->where($code_map)->select();
				if($product_code_list)
				{
					foreach($product_code_list as $value)
					{
						$order_plat_form_product = D('order_plat_form_product');
						$order_plat_form_product_list = $order_plat_form_product->where(array('code_id'=>$value['id']))->relation(true)->select();
						$order_platform_product_array = array();
						foreach($order_plat_form_product_list as $order_plat_form_product_list_value)
						{
							$order_platform_product_array[] = $order_plat_form_product_list_value['order_platform_id'];
						}
						$map['id'] = array('in',$order_platform_product_array);
					}
				}
			}
			
			if(I('post.begin_time')!='' && I('post.end_time')!='')
			{
				$begin_time = date("Y-m-d H:i:s",strtotime(I('post.begin_time')));
				$end_time = date("Y-m-d 00:00:00",(strtotime(I('post.end_time'))+3600*24));
			}
			else
			{
				$begin_time=date("Y-m-01 00:00:00",time());
				$end_time=date("Y-m-d 00:00:00",(time()+3600*24));
			}
			$day_arr = array();
			$order_begin_time=strtotime($begin_time);
			$order_end_time=strtotime($end_time);
			$day=($order_end_time-$order_begin_time)/(3600*24);//开始时间戳减去结束时间戳在除以一天的时间就得到这个月的天数
			for($i=0;$i<$day;$i++)
			{
				$every_day=date("Y-m-d",($order_begin_time+($i*3600*24)));
				$start = date("$every_day 00:00:00");
				$end = date("$every_day 23:59:59");
				$map['date_time'] = array("between","$start,$end");
				$list = $order_plat_form->where($map)->order("date_time desc")->limit($Page->firstRow.','.$Page->listRows)->relation(true)->select();
				if($product_code_list)
				{
					foreach($product_code_list as $product_code_list_value)
					{
						foreach($list as $list_value)
						{
							foreach($list_value['product_info'] as $product_info_value)
							{
								if($product_code_list_value['id']==$product_info_value['code_id'])
								{
									$product_code = $id_product_code->where(array('id'=>$product_info_value['code_id']))->getField('name');
			
									$day_arr[$every_day][]= array('currency'=>$list_value['currency'],'name'=>$product_code,'price'=>$product_info_value['price'],'number'=>$product_info_value['number']);
								}
							}
						}
					}
				}
				else
				{
					foreach($list as $list_value)
					{
						foreach($list_value['product_info'] as $product_info_value)
						{
							$product_code = $id_product_code->where(array('id'=>$product_info_value['code_id']))->getField('name');
								
							$day_arr[$every_day][]= array('currency'=>$list_value['currency'],'name'=>$product_code,'price'=>$product_info_value['price'],'number'=>$product_info_value['number']);
				
						}
					}
				}
			}
			$names_array = array();
			//处理同一天中code名称一样的数量
			foreach($day_arr as $day_arr_key=>$day_arr_value)
			{
				foreach($day_arr_value as $names_value)
				{
					if(@$names_array[$day_arr_key][$names_value['name']])
					{
						$names_array[$day_arr_key][$names_value['name']]['number'] += $names_value['number'];
						$names_array[$day_arr_key][$names_value['name']]['price'] += $names_value['price'];
					}
					else
					{
						$names_array[$day_arr_key][$names_value['name']] = $names_value;
					}
				}
			}
			$day_array = array();
			$series1 = array();
			$key = 0;
			foreach($names_array as $names_array_key=>$names_array_value)
			{
				$day_array[] = $names_array_key;
				foreach($names_array_value as $value)
				{	
					$series1[$key]['data']= array($value['number']);
					$series1[$key]['name'] = $value['name'];
				}
				$key++;
			}
			//print_r($day_array);
			//print_r($series1);
			vendor('Highcharts.Highcharts');
			//条形图(colume)、折线图(line)、面积图(area) 数据格式
			$option1 = array(
					'container' => 'my_chart',//容器id，不能重复
					'type' => 'column',//图表类型 可选用: 折线line 曲线spline 柱状column 面积图area 曲线面积图areaspline 饼状pie
					'width' => '100%',//图表宽度,默认100%
					'height' => '400px',//图表宽度，默认400px
					'theme' => 'gray',//主题 可选用: dark-blue dark-green dark-unica gray grid-light grid sand-signika skies
					'title' => '产品销量图表',//图表标题
					'xtitle' => 'Date',//x轴名称
					'ytitle' => '产品数量',//y轴名称
					'suffix' => '个',//数值单位
					'category' => $day_array,//固定的 x轴坐标
					'xmin' => 0,//线性的x轴坐标 起始  默认为0  与category之间选用其中一种
					'xstep' => 1,//线性的x轴坐标 步长 默认为1
					'ymin' => 0,//y轴初始值
			);
			$chart1 = getChirt($series1,$option1);
			$this->assign('chart1',$chart1);
			
			$this->display();
		}
		//订单发货率
		
		public function statistics_delivery()
		{
			//订单号
			$order_web=D("OrderWeb");
			$order_web_DB=M("order_web");
			if(!empty($_POST[order_number]))
			{
				
				$sub_sql = "SELECT order_web.id FROM order_web,order_delivery_detail,order_delivery_parameters WHERE order_web.id=order_delivery_detail.order_web_id AND order_web.id=order_delivery_parameters.order_id AND order_web.order_number='{$_POST[order_number]}' AND order_web.come_from_id='{$_POST[come_from]}' AND order_delivery_detail.style='{$_POST[style]}'";
				
			}
			elseif(!empty($_POST[delivery_number]))
			{
				$sub_sql="select order_web.id from order_web,order_delivery_detail,order_delivery_parameters where order_web.id=order_delivery_detail.order_web_id AND order_web.id=order_delivery_parameters.order_id and order_delivery_detail.delivery_number='{$_POST[delivery_number]}' and order_web.come_from_id='{$_POST[come_from]}' and order_delivery_detail.style='{$_POST[style]}'";
				
			}
			elseif(!empty($_POST[email]))
			{
				$sub_sql="select order_web.id from order_web,order_delivery_detail,order_delivery_parameters where order_web.id=order_delivery_detail.order_web_id AND order_web.id=order_delivery_parameters.order_id and order_web.email='{$_POST[email]}' and order_web.come_from_id='{$_POST[come_from]}' and order_delivery_detail.style='{$_POST[style]}'";
			}
			elseif(!empty($_POST[beginTime]) && !empty($_POST[endTime]))
			{
				$beginTime=date("Y-m-d H:i:s",strtotime($_POST[beginTime]));
				$endTime=date("Y-m-d H:i:s",strtotime($_POST[endTime]));
				$sub_sql="select order_web.id from order_web,order_delivery_detail,order_delivery_parameters where order_web.id=order_delivery_detail.order_web_id AND order_web.id=order_delivery_parameters.order_id and order_web.date_time >= '{$beginTime}' and order_web.date_time < '{$endTime}' and order_web.come_from_id='{$_POST[come_from]}' and order_delivery_detail.style='{$_POST[style]}'";
			}
			if(!empty($sub_sql))
			{
				$order_delivery_info=$order_web->relation(array("delivery_info","detail_info"))->where(array('id'=>array('EXP'," IN ($sub_sql) ")))->select();
				
				$total_price="";
				foreach ($order_delivery_info as $k=>$val)
				{
					$country=get_come_from_name($val[come_from_id]);
					$delivery_price=M("order_delivery_cost")->where(array("style"=>$_POST[style],"country"=>$country,"lower_weight"=>array("LT",$val[delivery_info][weight]),"upper_weight"=>array("EGT",$val[delivery_info][weight])))->getField("price");
					
					if($delivery_price)
					{
						$order_delivery_info[$k]["delivery_price"]=$delivery_price;
						$total_price+=$delivery_price;
					}
					else
					{
						$order_delivery_info[$k]["delivery_price"]="没有对应的运费";
					}
				}
				
				$this->total_price=$total_price;
				$this->assign("order_delivery_info",$order_delivery_info);
			}
			
			$id_come_from=M("id_come_from");
			$come_from_list=$id_come_from->where(array("style"=>"web"))->select();
			$this->come_from=$come_from_list;
			$this->style=delivery_style();
			$this->display();
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
