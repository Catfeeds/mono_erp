<?php
namespace Admin\Controller;
use Admin\Controller\CommonController;

class OrderDeliveryController extends CommonController
{
    public function index()
    {
 		layout(false);
 		$style=I('get.style');
 		$id=I('get.id');
 		if($style=="flat")
 		{
 		    $order_id=0;
 		    $order_platform_id=$id;
 		}
 		else
 		{
 		    $order_id=$id;
 		    $order_platform_id=0;
 		}
 		
 		$order_detail=delivery($order_id,$order_platform_id);
 		$delivery_parameters=M('order_delivery_parameters');
 		$delivery_parameters_detail=$delivery_parameters->where("order_id=$order_id and order_platform_id=$order_platform_id")->select();
 		if($delivery_parameters_detail[0]["shipping_style"]=="DHL")
 		{
 		    if(!$order_detail)
 		    {
 		        echo "哈哈，出错了！";
 		    }
 		    else
 		    {
 		        $this->assign("order_detail",$order_detail);
 		        $this->display("dhl");
 		    }
 		}
		elseif($delivery_parameters_detail[0]["shipping_style"]=="FEDEX")
		{
		    if(!$order_detail)
		    {
		        echo "哈哈，出错了！";
		    }
		    else 
		    {
		        $this->assign("order_detail",$order_detail);
		        $this->display("fedex");
		    }
		    
		}
		elseif($delivery_parameters_detail[0]["shipping_style"]=="TNT")
		{		    
		    
		    if(!$order_detail)
		    {
		        echo "哈哈，出错了！";
		    }
		    else
		    {
		        $this->assign("order_detail",$order_detail);
		        $this->display("tnt");
		    }
		}
    }
}