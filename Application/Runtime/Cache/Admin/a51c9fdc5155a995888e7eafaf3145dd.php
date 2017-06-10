<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>拼图后台管理-后台管理</title>
</head>

<!-- 加载js css -->
<link rel="stylesheet" href="/erptest/Application/Admin/View/Default/css/pintuer.css">
<link rel="stylesheet" href="/erptest/Application/Admin/View/Default/css/admin.css">
<script src="/erptest/Application/Admin/View/Default/js/jquery.min.js"></script>
<script src="/erptest/Application/Admin/View/Default/js/pintuer.js"></script>
<script src="/erptest/Application/Admin/View/Default/js/respond.js"></script>
<script src="/erptest/Application/Admin/View/Default/js/admin.js"></script>
<script src="/erptest/Application/Admin/View/Default/js/layer/layer.js"></script>
<script src="/erptest/Application/Admin/View/Default/Public/KindEditor/kindeditor-min.js"></script>
<script src="/erptest/Application/Admin/View/Default/Public/KindEditor/lang/zh_CN.js"></script>
<script src="/erptest/Application/Admin/View/Default/js/function.js?v=<?php echo time();?>"></script><!-- 公共js函数文件 -->
<script>
	KindEditor.ready(function(K) {
		editor = K.create('textarea[name="content"]', {
			allowFileManager: true,
			afterBlur: function(){this.sync();},
		});
	});
</script>
<script language="javascript" type="text/javascript" src="/erptest/Application/Admin/View/Default/Public/wdatepicker/WdatePicker.js"></script>
<script>//定义需要的“常量”
var _CONTROLLER_ = '/erptest/index.php/Admin/OrderManage';
var _ACTION_  = '/erptest/index.php/Admin/OrderManage/order_details';
var _PUBLIC_ = '/erptest/Public';
var _TIMESTAMP_ = <?php echo time();?>;
var _SELF_ = '/erptest/index.php/Admin/OrderManage/order_details/order_id/997/order_status/all.html';
var _APP_='/erptest/index.php';
var _MODULE_ = '/erptest/index.php/Admin';
var _ROOT_ = '/erptest';

</script>


<link type="image/x-icon" href="http://www.pintuer.com/favicon.ico" rel="shortcut icon" />
<link href="http://www.pintuer.com/favicon.ico" rel="bookmark icon" />
<body>

<!-- 可以选择不加载菜单 -->
<?php if($no_menu != true): ?><div class="lefter">
    <div class="logo"><img src="/erptest/Application/Admin/View/Default/images/logo.png" style="height:40px;" alt="后台管理系统" /><b style="font-size: 12px">ERP</b></div>
</div>
<div class="righter nav-navicon" id="admin-nav">
    <div class="mainer">
        <div class="admin-navbar">
            <span class="float-right">
         
                <a class="button button-little bg-yellow" href="<?php echo U('Admin/public/logout');?>">注销登录</a>
            </span>
            <ul class="nav nav-inline admin-nav">
                
                <?php if(is_array($main_menu)): $i = 0; $__LIST__ = $main_menu;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><li <?php echo ($vo[active]?'class="active"':''); ?>><a href="<?php echo ($vo[main_menu_url]); ?>"><?php echo ($vo[title]); ?></a>
                        <ul>
                        <?php echo ($vo[left_menu_html]); ?>
                        </ul>
                    </li><?php endforeach; endif; else: echo "" ;endif; ?>
              </ul>
        </div>
        <div class="admin-bread">
            <span> <a href="/erptest/index.php/Admin/messages/Index.html" class="plcz" style=" margin-right:30px;">私信</a> <?php echo ($username); ?> &nbsp;&nbsp; 工号: <?php echo $monoid>0?$monoid:0;?> &nbsp;&nbsp;</span>
            
            
            
            <ul class="bread">
                <li><a href="index.html" class="icon-home"> 开始</a></li>
                <li>后台首页</li>
            </ul>
        </div>
    </div>
</div>
<span class="icon-dedent" style="position: fixed; top: 84px; left: 180px; z-index: 100;color: #09c;cursor: pointer;font-size: 20px;width: 10px;" onclick='show_hide_left_menu(this);'></span><?php endif; ?>
<script src="/erptest/Application/Admin/View/Default/js/OrderManage/main.js?v=<?php echo time();?>"></script>
<script>
function merchants_message(order_id,style){
	var user_list='';
	var option='';
	$.ajax({
		type: "POST",
		url:  _CONTROLLER_+"/order_message",
		data : {"order_id":order_id},
		dataType: 'json',
		success : function(result){
			$.each(result,function(n,value){
				option+="<option>"+value['username']+"</option>";
			})
			user_list="<select name='accept'>"+option+"</select>";
			layer.open({
				  type: 1,
				  title: "商家留言",
				  area: ['600px', '350px'],
				  closeBtn: 1,
				  shadeClose: false,
				  skin: 'layui-layer-lan',
				  content: "<form action='<?php echo U("/Admin/OrderManage/order_message/deal/add");?>' method='post'><div><ul class='list-group'><li><strong>内容:</strong>&nbsp;&nbsp;&nbsp;<textarea name='plat_message' style='width:300px;height:100px;'></textarea></li><li><strong>指定人:</strong>&nbsp;&nbsp;&nbsp;"+user_list+"</li><li><strong>操作人:</strong>&nbsp;&nbsp;&nbsp;<?php echo ($username); ?></li><li><input type='hidden' value='"+order_id+"' name='order_id'><input type='hidden' value='"+style+"' name='style'><input type='hidden' value='<?php echo ($username); ?>' name='operator'><input type='submit' value='提交' class='button border-blue'></li></ul></div></form>",	 
			});
		}
	})
}

function history_order_information(obj,id){
	var information=document.getElementById("information_"+id);
	if(information.style.display=='none'){
		information.style.display='';
		obj.className="icon-chevron-up";
	}else{
		information.style.display='none';
		obj.className="icon-chevron-down";
	}
}
</script>
<script src="/erptest/Application/Admin/View/Default/js/ServiceManage/logistics_information.js"></script>
<div class="admin">
	<a href="<?php echo U('/Admin/OrderManage/order_plat/order_status/'.$order_status);?>"><button class="button border-black padding-small">返回</button></a>
	<div class="panel" style="width:100%;float:left;margin-top:10px;">
	<div class="panel-head"><strong>顾客邮箱:</strong>&nbsp;&nbsp;&nbsp;<a href="<?php echo U('/Admin/CustomerManage/index/',array('email'=>$order_message_shipping[email]));?>"><?php echo ($order_message_shipping["email"]); ?></a></div>
	</div>
	<div class="panel" style="width:100%;float:left;margin-top:10px;">
		<div class="panel-head"><strong>收件地址:</strong><span style="float:right;"><a class="icon-pencil text-red" title="修改收件地址" href="<?php echo U('/Admin/OrderManage/shipping_edit_plat',array('shipping_id'=>$order_message_shipping[id],'come'=>'plat_details'));?>"></a></span></div>
		<table class="table">
          <tr><td width="110" align="right"><strong>收件人：</strong></td><td><?php echo ($order_message_shipping["shipping_info"]["name"]); ?></td><td width="90" align="right"><strong>国家：</strong></td><td><?php echo ($order_message_shipping["shipping_info"]["country"]); ?></td></tr>
          <tr><td align="right"><strong>州/省：</strong></td><td><?php echo ($order_message_shipping["shipping_info"]["state"]); ?></td><td align="right"><strong>城市：</strong></td><td><?php echo ($order_message_shipping["shipping_info"]["city"]); ?></td></tr>
          <tr><td align="right"><strong>地址1：</strong></td><td><?php echo ($order_message_shipping["shipping_info"]["address1"]); ?></td><td align="right"><strong>地址2：</strong></td><td><?php echo ($order_message_shipping["shipping_info"]["address2"]); ?></td></tr>
          <tr><td align="right"><strong>地址3：</strong></td><td><?php echo ($order_message_shipping["shipping_info"]["address3"]); ?></td><td align="right"><strong>邮编：</strong></td><td><?php echo ($order_message_shipping["shipping_info"]["post"]); ?></td></tr>
          <tr><td align="right"><strong>电话：</strong></td><td><?php echo ($order_message_shipping["shipping_info"]["telephone"]); ?></td><td align="right"><strong>日期：</strong></td><td><?php echo ($order_message_shipping["shipping_info"]["shipping_date"]); ?></td></tr>
      </table>
	</div>
	
	<div class="panel" style="width:100%;float:left;margin-top:10px;">
	<div class="panel-head"><strong>运单信息:</strong><span style="float: right;margin-right: 10px;cursor:pointer;"><?php if(empty($order_message_shipping["detail_info"])): ?><a class="icon-pencil text-red" title="手动添加运单信息" onclick="waybill_information(<?php echo ($_GET['order_id']); ?>,'plat');" style="margin-right:10px"></a><?php endif; ?><a class="icon-truck text-red" title="详情" onclick="btnSnap('<?php echo ($order_message_shipping["detail_info"]["style"]); ?>','<?php echo ($order_message_shipping["detail_info"]["delivery_number"]); ?>')"></a></span></div>
	<table class="table" style="margin-top:5px;">
         	<tr><td>运单号</td><td>运输方式</td><td>时间</td><td>状态</td></tr>
 			<tr><td><?php echo ($order_message_shipping["detail_info"]["delivery_number"]); ?></td><td><?php echo ($order_message_shipping["detail_info"]["style"]); ?></td><td><?php $message=get_last_message($order_message_shipping[detail_info][delivery_number],$order_message_shipping[id],'plat');echo $message[time]; ?></td><td><?php echo ($message[message]); ?></td></tr>
    </table>
	</div>
	
	<div class="panel" style="width:100%;float:left;margin-top:10px;">
		<div class="panel-head"><strong>留言内容:</strong><span style="float:right;"><a class="icon-comment text-green" title="添加商家留言" style="cursor:pointer;" onclick="merchants_message(<?php echo ($order_message_shipping[id]); ?>,'plat')"></a></span></div>
		<ul class="list-group">
		<li>
		<strong>客户留言:</strong>&nbsp;&nbsp;&nbsp;<?php if(!empty($order_message_shipping["0"]["message"])): echo ($order_message_shipping["message"]); else: ?>无<?php endif; ?>
		</li>
         <li>
         <strong>商家留言:</strong> 
         <?php if(!empty($order_message_shipping["message_info"])): ?><table class="table" style="margin-top:5px;">
         	<tr><td>日期</td><td>指定人</td><td>内容</td><td>操作人</td></tr>
         	<?php if(is_array($order_message_shipping["message_info"])): $i = 0; $__LIST__ = $order_message_shipping["message_info"];if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><tr><td><?php echo date("Y-m-d H:i:s",$vo[date_time]);?></td><td class="text-red"><?php echo ($vo["accept"]); ?></td><td class="text-red"><?php echo ($vo["message"]); ?></td><td><?php echo ($vo["operator"]); ?></td></tr><?php endforeach; endif; else: echo "" ;endif; ?>
         </table>
         <?php else: ?>无<?php endif; ?> 
         </li>
         </ul>
	</div>
	
	 <div class="panel" style="width:100%;float:left;margin-top:10px;">
      <div class="panel-head"><strong>订单信息</strong><span style="float:right;"><a href="/erptest/index.php/Admin/ProductReturn/product_return_order/order_number/<?php echo ($order_message_shipping["order_number"]); ?>/come/plat/order_status/<?php echo ($order_status); ?>" class="icon-pencil text-red" title="退货/换货/追加"></a></span></div>
      	 <ul class="list-group">
      	 <li>
      	 	<table class="table">
      	 	<tr class="list_head">
         		<td align="left">订单号</td><td align="left">姓名</td><td align="left">总价</td><td align="left">来源</td><td align="left">下单时间</td><td align="left">状态</td>
         	</tr>
         	<tr>
         		<td><?php echo ($order_message_shipping["order_number"]); ?></td>
         		<td><?php echo ($order_message_shipping["name"]); ?></td>
         		<td><?php echo ($order_message_shipping["price"]); ?></td>
         		<td><?php echo ($order_message_shipping["come_from"]); ?></td>
         		<td><?php echo ($order_message_shipping["date_time"]); ?></td>
         		<td><?php echo ($order_message_shipping["status_info"]["status"]); ?></td>
         	</tr>
      	 	</table>
      	 </li>
    	 <li>
    		<table class="table">
    			<tr><td><strong>订单状态</strong></td></tr>
    			<tr><td>订单号</td><td>时间</td><td>内容</td><td>状态</td></tr>
    			<?php $order_status_array=order_status_list($order_message_shipping[id],'plat') ?>
    			<?php if(is_array($order_status_array)): $i = 0; $__LIST__ = $order_status_array;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$status_array): $mod = ($i % 2 );++$i;?><tr>
    			<td><?php echo ($order_message_shipping["order_number"]); ?></td>
    			<td><?php echo ($status_array["date_time"]); ?></td>
    			<td><?php echo ($status_array["message"]); ?></td>
    			<td><?php echo ($status_array["status"]); ?></td>
    			</tr><?php endforeach; endif; else: echo "" ;endif; ?>
    		</table>
    	 </li>
    	 <li>
	 		<table class="table">
    			<tr><td><strong>订单产品</strong></td></tr>
    			<tr><td>图片</td><td>产品名称</td><td>价格</td><td>数量</td><td>状态</td><td>套件</td></tr>
    			<?php if(is_array($order_message_shipping["product_info"])): $i = 0; $__LIST__ = $order_message_shipping["product_info"];if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><tr>
	    			<td width="100"><img src="#"></td>
	    			<td width="150"><?php echo ($vo["code_info"]["name"]); ?></td>
	    			<td width="60"><?php echo ($vo["price"]); ?></td>
	    			<td width="60"><?php echo ($vo["number"]); ?></td>
	    			<td width="60"><?php echo ($vo["status"]); ?></td>
	    			<td width="60"><?php if(!empty($vo["set_sku"])): echo ($vo["set_sku"]); else: ?>否<?php endif; ?></td>
    			</tr><?php endforeach; endif; else: echo "" ;endif; ?>
    		</table>
    	 </li>
         </ul>
         <div class="panel" style="width:100%;float:left;margin-top:10px;">
   	<div class="panel-head"><strong>历史订单</strong><span style="color: #f60;margin-left:20px;">历史订单金额:&nbsp;&nbsp;<?php echo ($all_price); ?></span><span style="color: #f60;margin-left:20px;">历史订单数:&nbsp;&nbsp;<?php echo ($all_num); ?></span></div>
   		<?php if(!empty($order_history_list)): ?><table class="table">
         <tr class="list_head">
         <td align="left">订单号</td><td align="left">姓名</td><td align="left">总价</td><td align="left">来源</td><td align="left">下单时间</td><td align="left">状态</td><td align="left">运费</td><td align="left">关税</td><td align="left">偏远费</td><td align="center">操作</td>
         </tr>
         <?php if(is_array($order_history_list)): $i = 0; $__LIST__ = $order_history_list;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><tr class="ji">
         <td style="color:#03c"><?php echo ($vo["order_number"]); ?></td>
         <td style="color:#03c"><?php echo ($vo["name"]); ?></td>
         <td style="color:#03c"><?php echo ($vo["price"]); ?></td>
         <td style="color:#03c"><?php echo ($vo["come_from"]); ?></td>
         <td style="color:#03c"><?php echo ($vo["date_time"]); ?></td>
         <td style="color:#03c"><?php echo ($vo["status_info"]["status"]); ?></td>
         <td style="color:#03c"><?php echo get_delivery_price($vo[shipping_info][country],$vo[detail_info][style],$vo[delivery_info][weight]);?></td>
         <td style="color:#03c"><?php echo ($vo["other_price_info"]["tariffs"]); ?></td>
         <td style="color:#03c"><?php echo ($vo["other_price_info"]["remote"]); ?></td>
         <td align="center"><a class="icon-chevron-down" style="cursor:pointer;" onclick="history_order_information(this,<?php echo ($vo["id"]); ?>)"></a></td>
         </tr>
         <tr class="ji" id="information_<?php echo ($vo["id"]); ?>" style="display:none;">
	         <td colspan="11">
	         	<div class="panel" style="width:100%;float:left;margin-top:10px;">
	         		<div class="panel-head"><strong>订单状态</strong></div>
	         		<table class="table">
		    			<tr><td>订单号</td><td>时间</td><td>内容</td><td>状态</td></tr>
		    			<?php $order_status_array=order_status_list($vo[id],'plat') ?>
		    			<?php if(is_array($order_status_array)): $i = 0; $__LIST__ = $order_status_array;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$status_array): $mod = ($i % 2 );++$i;?><tr>
		    			<td><?php echo ($vo["order_number"]); ?></td>
		    			<td><?php echo date("Y-m-d H:i:s",$status_array[date_time]);?></td>
		    			<td><?php echo ($status_array["message"]); ?></td>
		    			<td><?php echo ($status_array["status"]); ?></td>
		    			</tr><?php endforeach; endif; else: echo "" ;endif; ?>
    				</table>
	         	</div>
	         	<div class="panel" style="width:100%;float:left;margin-top:10px;">
					<div class="panel-head"><strong>订单产品</strong></div>
						<table class="table">
					         <tr class="list_head"><td>图片</td><td align="left">产品名称</td><td align="left">价格</td><td align="left">数量</td><td align="left">状态</td><td align="left">套件</td></tr>
					         <?php if(is_array($vo["product_info"])): $i = 0; $__LIST__ = $vo["product_info"];if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo_product): $mod = ($i % 2 );++$i;?><tr class="ji">
					         <td><img src="#"></td>
					         <td>
					         	<?php if(!empty($vo_product["code_id"])): echo ($vo_product["code_info"]["name"]); else: echo get_set_sku_name($vo_product[sku]); endif; ?></td>
					         <td><?php echo ($vo_product["price"]); ?></td>
					         <td><?php echo ($vo_product["number"]); ?></td>
					         <td><?php echo ($vo_product["status"]); ?></td>
					         <td><?php if(empty($vo_product["code_id"])): echo ($vo_product["sku"]); else: ?>无<?php endif; ?></td>
					         </tr><?php endforeach; endif; else: echo "" ;endif; ?>
         				</table>
				</div>
				
				<div class="panel" style="width:100%;float:left;margin-top:10px;">
				<div class="panel-head"><strong>运单信息:</strong><span style="float: right;margin-right: 10px;cursor:pointer;"><a class="icon-truck text-red" title="详情" onclick="btnSnap('<?php echo ($vo["detail_info"]["style"]); ?>','<?php echo ($vo["detail_info"]["delivery_number"]); ?>')"></a></span></div>
				<table class="table" style="margin-top:5px;">
         			<tr><td>运单号</td><td>运输方式</td><td>时间</td><td>状态</td></tr>
 					<tr><td><?php echo ($vo["detail_info"]["delivery_number"]); ?></td><td><?php echo ($vo["detail_info"]["style"]); ?></td><td><?php $message=get_last_message($vo[detail_info][delivery_number],$vo[id],'plat');echo $message[time]; ?></td><td><?php echo ($message[message]); ?></td></tr>
    			</table>
				</div>
	
	         	<div class="panel" style="width:100%;float:left;margin-top:10px;">
				<div class="panel-head"><strong>收件地址:</strong></div>
				<table class="table">
		          <tr><td width="110" align="right"><strong>收件人：</strong></td><td><?php echo ($vo["shipping_info"]["name"]); ?></td><td width="90" align="right"><strong>国家：</strong></td><td><?php echo ($vo["shipping_info"]["country"]); ?></td></tr>
		          <tr><td align="right"><strong>州/省：</strong></td><td><?php echo ($vo["shipping_info"]["state"]); ?></td><td align="right"><strong>城市：</strong></td><td><?php echo ($vo["shipping_info"]["city"]); ?></td></tr>
		          <tr><td align="right"><strong>地址：</strong></td><td><?php echo ($vo["shipping_info"]["address1"]); echo ($vo["shipping_info"]["address2"]); echo ($vo["shipping_info"]["address3"]); ?></td><td align="right"><strong>邮编：</strong></td><td><?php echo ($vo["shipping_info"]["post"]); ?></td></tr>
		          <tr></tr>
		          <tr><td align="right"><strong>电话：</strong></td><td><?php echo ($vo["shipping_info"]["telephone"]); ?></td><td></td><td></td></tr>
      			</table>
				</div>
				<div class="panel" style="width:100%;float:left;margin-top:10px;">
					<div class="panel-head"><strong>留言内容</strong></div>
					<ul class="list-group">
					<li>
						<strong>客户留言:</strong>&nbsp;&nbsp;&nbsp;<?php if(!empty($vo["message"])): echo ($vo["message"]); else: ?>无<?php endif; ?>
					</li>
         			<li>
         				<strong>商家留言:</strong> 
         				<?php if(!empty($vo["message_info"])): ?><table class="table" style="margin-top:5px;">
         				<tr><td>日期</td><td>指定人</td><td>内容</td><td>操作人</td></tr>
         				<?php if(is_array($vo["message_info"])): $i = 0; $__LIST__ = $vo["message_info"];if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$message_info): $mod = ($i % 2 );++$i;?><tr><td><?php echo date("Y-m-d H:i:s",$vo[date_time]);?></td><td class="text-red"><?php echo ($message_info["accept"]); ?></td><td class="text-red"><?php echo ($message_info["message"]); ?></td><td><?php echo ($message_info["operator"]); ?></td></tr><?php endforeach; endif; else: echo "" ;endif; ?>
         				</table>
         				<?php else: ?>无<?php endif; ?> 
         			</li>
         			</ul>
				</div>		
	         </td>
         </tr><?php endforeach; endif; else: echo "" ;endif; ?>
         </table>
         <?php else: ?><div style="font-size: 16px;font-weight: bold;text-align: center;color:red;">暂无历史订单</div><?php endif; ?>
   </div>
   </div>
</div>

</body>
</html>