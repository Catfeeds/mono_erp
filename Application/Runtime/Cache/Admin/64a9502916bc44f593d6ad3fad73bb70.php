<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>Lilysilk - ERP</title>
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
var _ACTION_  = '/erptest/index.php/Admin/OrderManage/order_details_web';
var _PUBLIC_ = '/erptest/Public';
var _TIMESTAMP_ = <?php echo time();?>;
var _SELF_ = '/erptest/index.php/Admin/OrderManage/order_details_web/order_id/2039/order_status/all.html';
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
<style>
img{vertical-align: middle;}
</style>
<script>
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
				  area: ['700px', '433px'],
				  closeBtn: 1,
				  shadeClose: false,
				  skin: 'layui-layer-lan',
				  content: "<form action='<?php echo U("/Admin/OrderManage/order_message/deal/add");?>' method='post'><div><ul class='list-group'><li><strong>内容:</strong>&nbsp;&nbsp;&nbsp;<textarea name='plat_message' style='width:300px;height:100px;' id='content'></textarea></li><li><strong>指定人:</strong>&nbsp;&nbsp;&nbsp;"+user_list+"</li><li><strong>操作人:</strong>&nbsp;&nbsp;&nbsp;<?php echo ($username); ?></li><li><input type='hidden' value='"+order_id+"' name='order_id'><input type='hidden' value='"+style+"' name='style'><input type='hidden' value='<?php echo ($username); ?>' name='operator'><input type='submit' value='提交' class='button border-blue'></li></ul></div></form>",	 
			});
			KindEditor.ready(function(K) {
				editor = K.create('textarea[id="content"]', {
					allowFileManager: true,
					afterBlur: function(){this.sync();},
				});
			});
		}
	})
}
</script>
<script src="/erptest/Application/Admin/View/Default/js/ServiceManage/logistics_information.js"></script>
<div class="admin">
<a href="<?php echo U('/Admin/OrderManage/order_web/order_status/'.$order_status);?>"><button class="button border-black padding-small" >返回</button></a>
	<div class="panel" style="width:100%;float:left;margin-top:10px;">
	<div class="panel-head"><strong>顾客邮箱:</strong>&nbsp;&nbsp;&nbsp;<a href="<?php echo U('/Admin/CustomerManage/index/',array('email'=>$web_message_shipping[email]));?>"><?php echo ($web_message_shipping["email"]); ?></a>
	<strong id="warning_<?php echo ($web_message_shipping['id']); ?>" style="display: block;color:red;">
	<?php echo mark_bad_user($web_message_shipping['id'], $web_message_shipping['email'], $web_message_shipping['first_name'], $web_message_shipping['last_name'],$web_message_shipping[supplement_info][custome_ip],$web_message_shipping[order_web_address][0][telephone]);?></strong>
	</div>
	</div>
	
	<div class="panel" style="width:100%;float:left;margin-top:10px;">
		<div class="panel-head"><strong>收件地址:</strong><span style="float:right;"><a class="icon-pencil text-red" title="修改收件地址" href="<?php echo U('/Admin/OrderManage/address_edit',array('address_id'=>$web_message_shipping[id]));?>"></a></span></div>
		<table class="table">
          <tr><td width="110" align="right"><strong>收件人：</strong></td><td><?php echo ($web_message_shipping["order_web_address"]["0"]["first_name"]); echo ($web_message_shipping["0"]["order_web_address"]["last_name"]); ?></td><td width="90" align="right"><strong>国家：</strong></td><td><?php echo ($web_message_shipping["order_web_address"]["0"]["country"]); ?></td></tr>
          <tr><td align="right"><strong>州/省：</strong></td><td><?php echo ($web_message_shipping["order_web_address"]["0"]["province"]); ?></td><td align="right"><strong>城市：</strong></td><td><?php echo ($web_message_shipping["order_web_address"]["0"]["city"]); ?></td></tr>
          <tr><td align="right"><strong>地址1：</strong></td><td><?php echo ($web_message_shipping["order_web_address"]["0"]["address"]); ?></td><td align="right"><strong>邮编：</strong></td><td><?php echo ($web_message_shipping["order_web_address"]["0"]["code"]); ?></td></tr>
          <tr></tr>
          <tr><td align="right"><strong>电话：</strong></td><td><?php echo ($web_message_shipping["order_web_address"]["0"]["telephone"]); ?></td><td></td><td></td></tr>
      </table>
	</div>
	
	<div class="panel" style="width:100%;float:left;margin-top:10px;">
	<div class="panel-head"><strong>运单信息:</strong><span style="float: right;margin-right: 10px;cursor:pointer;"><?php if(empty($web_message_shipping["detail_info"])): ?><a class="icon-pencil text-red" title="手动添加运单信息" onclick="waybill_information(<?php echo ($_GET['order_id']); ?>,'web');" style="margin-right:10px"></a><?php endif; ?><a class="icon-truck text-red" title="详情" onclick="btnSnap('<?php echo ($web_message_shipping["detail_info"]["style"]); ?>','<?php echo ($web_message_shipping["detail_info"]["delivery_number"]); ?>')"></a></span></div>
	<table class="table" style="margin-top:5px;">
         	<tr><td>运单号</td><td>运输方式</td><td>时间</td><td>状态</td></tr>
 			<tr><td><?php echo ($web_message_shipping["detail_info"]["delivery_number"]); ?></td><td><?php echo ($web_message_shipping["detail_info"]["style"]); ?></td><td><?php $message=get_last_message($web_message_shipping[detail_info][delivery_number],$web_message_shipping[id],'web');echo $message[time]; ?></td><td><?php echo ($message[message]); ?></td></tr>
    </table>
	</div>
	
	<div class="panel" style="width:100%;float:left;margin-top:10px;">
		<div class="panel-head"><strong>留言内容</strong><span style="float:right;"><a class="icon-comment text-green" title="添加商家留言" style="cursor:pointer;" onclick="merchants_message(<?php echo ($web_message_shipping[id]); ?>,'web')"></a></span></div>
		<ul class="list-group">
		<li>
		<strong>客户留言:</strong>&nbsp;&nbsp;&nbsp;<?php if(!empty($web_message_shipping["0"]["message"])): echo ($web_message_shipping["message"]); else: ?>无<?php endif; ?>
		</li>
         <li>
         <strong>商家留言:</strong> 
         <?php if(!empty($web_message_shipping["message_info"])): ?><table class="table" style="margin-top:5px;">
         	<tr><td>日期</td><td>指定人</td><td>内容</td><td>操作人</td></tr>
         	<?php if(is_array($web_message_shipping["message_info"])): $i = 0; $__LIST__ = $web_message_shipping["message_info"];if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><tr><td><?php echo date("Y-m-d H:i:s",$vo[date_time]);?></td><td class="text-red"><?php echo ($vo["accept"]); ?></td><td class="text-red"><?php echo ($vo["message"]); ?></td><td><?php echo ($vo["operator"]); ?></td></tr><?php endforeach; endif; else: echo "" ;endif; ?>
         </table>
         <?php else: ?>无<?php endif; ?> 
         </li>
         </ul>
	</div>
	
	 <div class="panel" style="width:100%;float:left;margin-top:10px;">
      <div class="panel-head"><strong>订单信息</strong><span style="float:right;"><a href="/erptest/index.php/Admin/ProductReturn/product_return_order/order_number/<?php echo ($web_message_shipping["order_number"]); ?>/come/web/order_status/<?php echo ($order_status); ?>/order_id/<?php echo ($_GET['order_id']); ?>" class="icon-pencil text-red" title="退货/换货/追加"></a></span></div>
      	 <ul class="list-group">
      	 <li>
    		<table class="table">
    			<tr><td><strong>订单状态</strong></td></tr>
    			<tr><td>订单号</td><td>时间</td><td>内容</td><td>状态</td></tr>
    			<?php $order_status_array=order_status_list($web_message_shipping[id],'web') ?>
    			<?php if(is_array($order_status_array)): $i = 0; $__LIST__ = $order_status_array;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$status_array): $mod = ($i % 2 );++$i;?><tr>
    			<td><?php echo ($web_message_shipping["order_number"]); ?></td>
    			<td><?php echo (date('Y-m-d H:i:s',$status_array["date_time"])); ?></td>
    			<td><?php echo ($status_array["message"]); ?></td>
    			<td><?php echo ($status_array["status"]); ?></td>
    			</tr><?php endforeach; endif; else: echo "" ;endif; ?>
    		</table>
    	 </li>
    	 <li>
    		<table class="table">
    			<tr><td><strong>优惠信息</strong></td></tr>
    			<tr><td><?php echo ($web_message_shipping["couponcode"]); ?></td></tr>
    			</volist>
    		</table>
    	 </li>
    	 <li>
<!-- 	 	<table class="table">
      	 	<tr class="list_head">
         		<td align="left">订单号</td><td align="left">姓名</td><td align="left">支付方式</td><td align="left">总价</td><td align="left">来源</td><td align="left">下单时间</td><td align="left">来源(least_from)</td><td align="left">状态</td>
         	</tr>
         	<tr>
         		<td><?php echo ($web_message_shipping["order_number"]); ?></td>
         		<td><?php echo ($web_message_shipping["customer_info"]["first_name"]); echo ($web_message_shipping["customer_info"]["last_name"]); ?></td>
         		<td><?php echo ($web_message_shipping["payment_style"]); ?></td>
         		<td><?php echo ($web_message_shipping["total_price_discount"]); ?></td>
         		<td><?php echo ($web_message_shipping["come_from"]); ?></td>
         		<td><?php echo ($web_message_shipping["date_time"]); ?></td>
         		<td><?php echo ($web_message_shipping["least_from"]); ?></td>
         		<td><?php echo ($web_message_shipping["order_web_status"]["status"]); ?></td>
         	</tr>
      	 	</table> --> 
			
			<table class="table">
				<tr><td colspan="9"><strong>订单产品</strong></td></tr>
				<tr>
					
					<td style="text-align:center;" width="45">序号</td>
					<td style="text-align:center;" width="100">套件</td>
					<td style="text-align:center;" width="100">价格</td>
					<td style="text-align:center;" width="150">名称</td>
					<td style="text-align:center;" width="150">Code</td>
					<td style="text-align:center;" width="150">颜色</td>
					<td style="text-align:center;" width="150">尺码</td>
					<td style="text-align:center;" width="150">工厂</td>
					<td style="text-align:center;" width="100">数量</td>
					<td style="text-align:center;" width="100">状态</td>
				</tr>
				<?php $flag = 'false'; ?>
				<?php if(is_array($order_info["order_web_product"])): $product_group_i = 0; $__LIST__ = $order_info["order_web_product"];if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$product_group_vo): $mod = ($product_group_i % 2 );++$product_group_i; if(empty($product_group_vo["0"]["set_sku"])): ?>
						<tr>
							<td><?php echo ($product_group_i); ?></td>
							<td align="center"> — </td>
							<td align="center">
								<span style="text-decoration:line-through;"><?php echo ($product_group_vo["0"]["price"]); ?></span> 
								<span style="color:red;"><?php echo ($product_group_vo["0"]["discount_price"]); ?></span>
							</td>
							<?php if(!empty($product_group_vo["0"]["code_id"])): ?><td align="center"><?php echo ($product_group_vo["0"]["code_info"]["name"]); ?></td>
								<td align="center"><?php echo ($product_group_vo["0"]["code_info"]["code"]); ?></td>
								<td align="center"><?php echo ($product_group_vo["0"]["code_info"]["color_info"]["value"]); ?></td>
								<td align="center">
									<?php if(empty($product_group_vo["0"]["nightwear_customization_info"])): ?><span><?php echo ($product_group_vo["0"]["code_info"]["size_info"]["value"]); ?></span>
									<?php else: ?>
										<span style="margin-right:5px;float:left;font-weight:bold;">[定制]</span>
										<span><?php echo ($product_group_vo["0"]["nightwear_customization_info"]["customization"]); ?></span><?php endif; ?>
								</td>
								<td align="center"><?php echo ($product_group_vo["0"]["code_info"]["factory_info"]["name"]); ?></td>
							<?php else: ?>
								<?php if(($flag) == "false"): ?><script>
										$("#check_<?php echo ($vo["id"]); ?>").attr("disabled","disabled");
										$("#audit_button_<?php echo ($vo["id"]); ?>").attr("onclick","layer.msg('产品sku未关联code');");
										$("#audit_button_<?php echo ($vo["id"]); ?>").removeClass("text-red");
										$("#audit_button_<?php echo ($vo["id"]); ?>").addClass("text-gray");
										mark_row(<?php echo ($vo['id']); ?>,"产品sku未关联","red_warning");
									</script>
									<?php $flag = 'true'; endif; ?>
								<td align="center">
									<span style="color:red;">未关联</span> 
								</td>
								<td align="center"><span style="color:red;">—</span></td>
								<td align="center">
									<?php if(empty($product_group_vo["0"]["nightwear_customization_info"])): ?><span style="color:red;">—</span>
									<?php else: ?>
										<span style="margin-right:5px;float:left;font-weight:bold;">[定制]</span>
										<span><?php echo ($product_group_vo["0"]["nightwear_customization_info"]["customization"]); ?></span><?php endif; ?>
								</td>
								<td align="center"><span style="color:red;">—</span></td><?php endif; ?>
							<td align="center"><?php echo ($product_group_vo["0"]["number"]); ?></td>
							<td align="center">
								<?php if(!empty($product_group_vo["0"]["status"])): echo ($product_group_vo["0"]["status"]); ?>
								<?php else: ?>
									normal<?php endif; ?>
							</td>
						</tr>
						<?php if(!empty($product_group_vo["0"]["extra_info"]["gift_box"])): ?><tr>
								<td></td>
								<td colspan="3">
									<span style="font-weight:bold;margin-right:5px;">[礼品盒]</span>
									<span><?php echo ($product_group_vo["0"]["extra_info"]["gift_box_message"]); ?></span>
								</td>
								<td>
									<img style="height:60px;width:60px;" src="<?php echo ($product_group_vo["0"]["extra_info"]["gift_box"]); ?>">
								</td>
							</tr><?php endif; ?>
						<?php if(!empty($product_group_vo["0"]["extra_info"]["gramming_name"])): ?><tr>
								<td></td>
								<td>
									<span style="font-weight:bold;margin-right:5px;">[绣字]</span>
									<span><?php echo ($product_group_vo["0"]["extra_info"]["gramming_name"]); ?></span>
								</td>
								<td align="center">
									<span><?php echo ($product_group_vo["0"]["extra_info"]["gramming_style"]); ?></span>
								</td>
								<td align="center">
									<span><?php echo ($product_group_vo["0"]["extra_info"]["gramming_color"]); ?></span>
								</td>
							</tr><?php endif; ?>
						<?php if(!empty($product_group_vo["0"]["extra_info"]["gift_product_name"])): ?><tr>
								<td></td>
								<td>
									<span style="font-weight:bold;margin-right:5px;">[赠品]</span>
									<span><?php echo ($product_group_vo["0"]["extra_info"]["gift_product_name"]); ?></span>
								</td>
								<td align="center">
									<img style="width:60px;height:60px;" src="<?php echo ($product_group_vo["0"]["extra_info"]["gift_product_img"]); ?>">
								</td>
								<td colspan="6">
									<span><?php echo ($product_group_vo["0"]["extra_info"]["gift_message"]); ?></span>
								</td>
							</tr><?php endif; ?>
							
					<?php else: ?>
						<?php if(is_array($product_group_vo)): $product_i = 0; $__LIST__ = $product_group_vo;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$product_vo): $mod = ($product_i % 2 );++$product_i;?><tr>
								<td><?php if(($product_i) == "1"): echo ($product_group_i); endif; ?></td>
								<td align="center"><?php if(($product_i) == "1"): ?><span style="color:red">套件sku： <?php echo ($product_vo["set_sku"]); ?></span><?php endif; ?></td>
								<td align="center">
									<?php if(($product_i) == "1"): ?><span style="text-decoration:line-through;"><?php echo ($product_vo["price"]); ?></span> 
									<span style="color:red;"><?php echo ($product_vo["discount_price"]); ?></span><?php endif; ?>
								</td>
								<?php if(!empty($product_vo["code_id"])): ?><td align="center"><?php echo ($product_vo["code_info"]["name"]); ?></td>
									<td align="center"><?php echo ($product_vo["code_info"]["color_info"]["value"]); ?></td>
									<td align="center">
										<?php if(empty($product_group_vo["0"]["nightwear_customization_info"])): ?><span><?php echo ($product_vo["code_info"]["size_info"]["value"]); ?></span>
										<?php else: ?>
											<span style="margin-right:5px;float:left;font-weight:bold;">[定制]</span>
											<span><?php echo ($product_vo["nightwear_customization_info"]["customization"]); ?></span><?php endif; ?>
									</td>
									<td align="center"><?php echo ($product_vo["code_info"]["factory_info"]["name"]); ?></td>
								<?php else: ?>
									<td align="center">
										<span style="color:red;">未关联</span> 
									</td>
									<td align="center"><span style="color:red;">—</span></td>
									<td align="center">
										<?php if(empty($product_group_vo["0"]["nightwear_customization_info"])): ?><span style="color:red;">—</span>
										<?php else: ?>
											<span style="margin-right:5px;float:left;font-weight:bold;">[定制]</span>
											<span><?php echo ($product_vo["nightwear_customization_info"]["customization"]); ?></span><?php endif; ?>
									</td>
									<td align="center"><span style="color:red;">—</span></td><?php endif; ?>
								<td align="center"><?php echo ($product_vo["number"]); ?></td>
								<td align="center">
									<?php if(!empty($product_group_vo["0"]["status"])): echo ($product_group_vo["0"]["status"]); ?>
									<?php else: ?>
										normal<?php endif; ?>
								</td>
							</tr>
							<?php if(!empty($product_group_vo["0"]["extra_info"]["gift_box"])): ?><tr>
									<td></td><td></td><td></td>
									<td colspan="3">
										<span style="font-weight:bold;margin-right:5px;">[礼品盒]</span>
										<span><?php echo ($product_group_vo["0"]["extra_info"]["gift_box_message"]); ?></span>
									</td>
									<td>
										<img style="height:60px;width:60px;" src="<?php echo ($product_group_vo["0"]["extra_info"]["gift_box"]); ?>">
									</td>
								</tr><?php endif; ?>
							<?php if(!empty($product_group_vo["0"]["extra_info"]["gramming_name"])): ?><tr>
									<td></td><td></td><td></td>
									<td>
										<span style="font-weight:bold;margin-right:5px;">[绣字]</span>
										<span><?php echo ($product_group_vo["0"]["extra_info"]["gramming_name"]); ?></span>
									</td>
									<td align="center">
										<span><?php echo ($product_group_vo["0"]["extra_info"]["gramming_style"]); ?></span>
									</td>
									<td align="center">
										<span><?php echo ($product_group_vo["0"]["extra_info"]["gramming_color"]); ?></span>
									</td>
								</tr><?php endif; ?>
							<?php if(!empty($product_group_vo["0"]["extra_info"]["gift_product_name"])): ?><tr>
									<td></td><td></td><td></td>
									<td>
										<span style="font-weight:bold;margin-right:5px;">[赠品]</span>
										<span><?php echo ($product_group_vo["0"]["extra_info"]["gift_product_name"]); ?></span>
									</td>
									<td align="center">
										<img style="width:60px;height:60px;" src="<?php echo ($product_group_vo["0"]["extra_info"]["gift_product_img"]); ?>">
									</td>
									<td colspan="4">
										<span><?php echo ($product_group_vo["0"]["extra_info"]["gift_message"]); ?></span>
									</td>
								</tr><?php endif; endforeach; endif; else: echo "" ;endif; endif; endforeach; endif; else: echo "" ;endif; ?>
				
				
				<?php if(is_array($vo["product_customization_info"])): $product_customization_i = 0; $__LIST__ = $vo["product_customization_info"];if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$product_customization_vo): $mod = ($product_customization_i % 2 );++$product_customization_i; if(($product_customization_i) == "1"): ?><script>mark_row(<?php echo ($vo["id"]); ?>,'含定制商品');</script><?php endif; ?>
					<tr>
						<td align="left"><?php echo ($product_group_i+$product_customization_i); ?></td>
						<td align="center" style="color:red;">定制</td>
						<td align="center"><?php echo ($product_customization_vo["price"]); ?></td>
						<td align="center" colspan="2">
							<span style="font-weight:bold;margin-right:5px;">[名称]</span>
							<a href="http://<?php echo ($product_customization_vo["href"]); ?>" target="_blank"><?php echo ($product_customization_vo["name"]); ?></a>
						</td>
						<td colspan="20">
							<span style="font-weight:bold;margin-right:5px;float:left;">[描述]</span>
							<?php echo ($product_customization_vo["description"]); ?>
						</td>
					</tr><?php endforeach; endif; else: echo "" ;endif; ?>
			</table>	
      	 </li>
      	 <li>
      	 <table class="table">
      	 	 <tr><td colspan="9"><strong>商品原始数据</strong></td></tr>
				<tr>
					<td>序号</td>
					<td style="text-align:center">名称</td>
					<td style="text-align:center">sku</td>
					<td style="text-align:center">颜色</td>
					<td style="text-align:center">尺码</td>
				</tr>
				<?php if(is_array($order_info["product_original_info"])): $product_original_i = 0; $__LIST__ = $order_info["product_original_info"];if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$product_original_vo): $mod = ($product_original_i % 2 );++$product_original_i; if(!empty($product_original_vo["product_set_name"])): ?><tr>
							<td><?php echo ($product_original_i); ?></td>
							<td align="center">
								<a target="_blank" href="http://<?php echo ($product_original_vo["href"]); ?>"><?php echo ($product_original_vo["product_set_name"]); ?></a>
								<span style="color:red;margin-left:5px;">套件</span>
							</td>
							<td align="center"><?php echo ($product_original_vo["sku"]); ?></td>
							<td align="center"><?php echo ($product_original_vo["color"]); ?></td>
							<td align="center"><?php echo ($product_original_vo["size"]); ?></td>
						</tr>
						<tr>
							<td colspan="20">
							<table class="original_table">
								<style>.original_table td{padding:2px 8px 2px 8px;}</style>
								<?php if(is_array($product_original_vo["set_info"])): $ii = 0; $__LIST__ = $product_original_vo["set_info"];if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$set_vo): $mod = ($ii % 2 );++$ii;?><tr>
										<td><?php echo ($set_vo["product_name"]); ?></td>
										<td><?php echo ($set_vo["sku"]); ?></td>
										<td><?php echo ($set_vo["color"]); ?></td>
										<td><?php echo ($set_vo["size"]); ?></td>
										<td>数量：<?php echo ($set_vo["number"]); ?></td>
									</tr><?php endforeach; endif; else: echo "" ;endif; ?>
							</table>
							</td>
						</tr>
					<?php else: ?>
						<tr>
							<td><?php echo ($product_original_i); ?></td>
							<td align="center"><a target="_blank" href="http://<?php echo ($product_original_vo["href"]); ?>"><?php echo ($product_original_vo["product_name"]); ?></a></td>
							<td align="center"><?php echo ($product_original_vo["sku"]); ?></td>
							<td align="center"><?php echo ($product_original_vo["color"]); ?></td>
							<td align="center"><?php echo ($product_original_vo["size"]); ?></td>
						</tr><?php endif; endforeach; endif; else: echo "" ;endif; ?>
      	 </table>
      	 </li>
    	 <!--<li>
    		<table class="table">
    			<tr><td><strong>订单产品</strong></td></tr>
    			<tr><td>图片</td><td>产品名称</td><td>价格</td><td>数量</td><td>状态</td><td>套件</td></tr>
    			<?php if(is_array($web_message_shipping["order_web_product"])): $i = 0; $__LIST__ = $web_message_shipping["order_web_product"];if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><tr>
    			<td width="100"><img src="#"></td>
    			<td width="150"><?php if(!empty($vo["code_id"])): echo ($vo["code_info"]["name"]); else: echo get_set_sku_name($vo[set_sku]); endif; ?></td>
    			<td width="60"><?php echo ($vo["price"]); ?></td>
    			<td width="60"><?php echo ($vo["number"]); ?></td>
    			<td width="60"><?php echo ($vo["status"]); ?></td>
    			<td width="60"><?php if(!empty($vo["set_sku"])): echo ($vo["set_sku"]); else: ?>否<?php endif; ?></td>
    			</tr><?php endforeach; endif; else: echo "" ;endif; ?>
    		</table>
    	 </li>
    	 -->
       </ul>
   </div>
   
   <div class="panel" style="width:100%;float:left;margin-top:10px;">
   	<div class="panel-head"><strong>历史订单</strong><span style="color: #f60;margin-left:20px;">历史订单金额:&nbsp;&nbsp;<?php echo ($all_price); ?></span><span style="color: #f60;margin-left:20px;">历史订单数:&nbsp;&nbsp;<?php echo ($all_num); ?></span></div>
   		<?php if(!empty($order_history_list)): ?><table class="table">
         <tr class="list_head">
         <td align="left">订单号</td><td align="left">姓名</td><td align="left">支付方式</td><td align="left">金额</td><td align="left">来源</td><td align="left">下单时间</td><td align="left">来源(least_from)</td><td align="left">状态</td><td align="left">运费</td><td align="left">关税</td><td align="left">偏远费</td><td align="center">操作</td>
         </tr>
         <?php if(is_array($order_history_list)): $i = 0; $__LIST__ = $order_history_list;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><tr class="ji">
         <td style="color:#03c"><?php echo ($vo["order_number"]); ?></td>
         <td style="color:#03c"><?php echo ($vo["customer_info"]["first_name"]); echo ($vo["customer_info"]["last_name"]); ?></td>
         <td style="color:#03c"><?php echo ($vo["payment_style"]); ?></td>
         <td style="color:#03c"><?php echo ($vo["total_price"]); ?></td>
         <td style="color:#03c"><?php echo ($vo["come_from"]); ?></td>
         <td style="color:#03c"><?php echo ($vo["date_time"]); ?></td>
         <td style="color:#03c"><?php if(!empty($vo["least_from"])): echo ($vo["least_from"]); else: ?>无<?php endif; ?></td>
         <td style="color:#03c"><?php echo ($vo["order_web_status"]["status"]); ?></td>
         <td style="color:#03c"><?php echo get_delivery_price($vo[order_web_address][0][country],$vo[detail_info][style],$vo[delivery_info][weight]);?></td>
         <td style="color:#03c"><?php echo ($vo["other_price_info"]["tariffs"]); ?></td>
         <td style="color:#03c"><?php echo ($vo["other_price_info"]["remote"]); ?></td>
         <td align="center"><a class="icon-chevron-down" style="cursor:pointer;" onclick="history_order_information(this,<?php echo ($vo["id"]); ?>)"></a></td>
         </tr>
         <tr class="ji" id="information_<?php echo ($vo["id"]); ?>" style="display:none;">
	         <td colspan="13">
	         	<div class="panel" style="width:100%;float:left;margin-top:10px;">
	         		<div class="panel-head"><strong>订单状态</strong></div>
	         		<table class="table">
		    			<tr><td>订单号</td><td>时间</td><td>内容</td><td>状态</td></tr>
		    			<?php $order_status_array=order_status_list($vo[id],'web') ?>
		    			<?php if(is_array($order_status_array)): $i = 0; $__LIST__ = $order_status_array;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$status_array): $mod = ($i % 2 );++$i;?><tr>
		    			<td><?php echo ($vo["order_number"]); ?></td>
		    			<td><?php echo ($status_array["date_time"]); ?></td>
		    			<td><?php echo ($status_array["message"]); ?></td>
		    			<td><?php echo ($status_array["status"]); ?></td>
		    			</tr><?php endforeach; endif; else: echo "" ;endif; ?>
    				</table>
	         	</div>
	         	<div class="panel" style="width:100%;float:left;margin-top:10px;">
					<div class="panel-head"><strong>订单产品</strong></div>
						<table class="table">
					         <tr class="list_head"><td>图片</td><td align="left">产品名称</td><td align="left">价格</td><td align="left">数量</td><td align="left">状态</td><td align="left">套件</td></tr>
					         <?php if(is_array($vo["order_web_product"])): $i = 0; $__LIST__ = $vo["order_web_product"];if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo_product): $mod = ($i % 2 );++$i;?><tr class="ji">
					         <td><img src="#"></td>
					         <td>
					         	<?php if(!empty($vo_product["code_id"])): echo ($vo_product["code_info"]["name"]); else: echo get_set_sku_name($vo_product[set_sku]); endif; ?></td>
					         <td><?php echo ($vo_product["price"]); ?></td>
					         <td><?php echo ($vo_product["number"]); ?></td>
					         <td><?php echo ($vo_product["status"]); ?></td>
					         <td><?php if(!empty($vo_product["set_sku"])): echo ($vo_product["set_sku"]); else: ?>无<?php endif; ?></td>
					         </tr><?php endforeach; endif; else: echo "" ;endif; ?>
         				</table>
				</div>
				
				<div class="panel" style="width:100%;float:left;margin-top:10px;">
				<div class="panel-head"><strong>运单信息:</strong><span style="float: right;margin-right: 10px;cursor:pointer;"><a class="icon-truck text-red" title="详情" onclick="btnSnap('<?php echo ($vo["detail_info"]["style"]); ?>','<?php echo ($vo["detail_info"]["delivery_number"]); ?>')"></a></span></div>
				<table class="table" style="margin-top:5px;">
         			<tr><td>运单号</td><td>运输方式</td><td>时间</td><td>状态</td></tr>
 					<tr><td><?php echo ($vo["detail_info"]["delivery_number"]); ?></td><td><?php echo ($vo["detail_info"]["style"]); ?></td><td><?php $message=get_last_message($vo[detail_info][delivery_number],$vo[id],'web');echo $message[time]; ?></td><td><?php echo ($message[message]); ?></td></tr>
    			</table>
				</div>
	
	         	<div class="panel" style="width:100%;float:left;margin-top:10px;">
				<div class="panel-head"><strong>收件地址:</strong></div>
				<table class="table">
		          <tr><td width="110" align="right"><strong>收件人：</strong></td><td><?php echo ($vo["order_web_address"]["0"]["first_name"]); echo ($vo["order_web_address"]["last_name"]); ?></td><td width="90" align="right"><strong>国家：</strong></td><td><?php echo ($vo["order_web_address"]["0"]["country"]); ?></td></tr>
		          <tr><td align="right"><strong>州/省：</strong></td><td><?php echo ($vo["order_web_address"]["0"]["province"]); ?></td><td align="right"><strong>城市：</strong></td><td><?php echo ($vo["order_web_address"]["0"]["city"]); ?></td></tr>
		          <tr><td align="right"><strong>地址1：</strong></td><td><?php echo ($vo["order_web_address"]["0"]["address"]); ?></td><td align="right"><strong>邮编：</strong></td><td><?php echo ($vo["order_web_address"]["0"]["code"]); ?></td></tr>
		          <tr></tr>
		          <tr><td align="right"><strong>电话：</strong></td><td><?php echo ($vo["order_web_address"]["0"]["telephone"]); ?></td><td></td><td></td></tr>
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
				
				<!-- <div class="panel" style="width:100%;float:left;margin-top:10px;">
	   				<div class="panel-head"><strong>付款记录</strong>&nbsp;&nbsp;&nbsp;共付款&nbsp;&nbsp;<span style="color: #f60"><?php $payment_array_history=get_payment_fail($vo[market_id]);echo get_payment_fail($vo[market_id],1)+1; ?></span>&nbsp;&nbsp;次</div>
	   				<?php if(!empty($payment_array_history)): ?><table class="table">
			   		 	<tr>
			   		 	<td><strong>付款方式</strong></td><td><strong>付款时间</strong><td><strong>失败记录</strong></td>
			   		 	</tr>
			   		 	<?php if(is_array($payment_array_history)): $i = 0; $__LIST__ = $payment_array_history;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo_array): $mod = ($i % 2 );++$i;?><tr><td><?php echo ($vo_array["payment_style"]); ?></td><td><?php echo ($vo_array["pay_time"]); ?></td><td><?php echo ($vo_array["return_result"]); ?></td></tr><?php endforeach; endif; else: echo "" ;endif; ?>
	   		 		</table>
	   		 		<?php else: ?>
	   		 		<div style="font-size: 16px;font-weight: bold;text-align: center;color:red;">暂无记录</div><?php endif; ?>
	   			</div>
	   			 -->
	   			<div class="panel" style="width:100%;float:left;margin-top:10px;">
      				<div class="panel-head"><strong>其他信息</strong></div>
      				<?php $extra_information=get_payment_fail($web_message_shipping[id]); ?>
					<ul class="list-group">
					<li><strong>付款记录:</strong></br><span><?php echo ($extra_information["payment_fail"]); ?></span></li>
					<li><strong>IP信息:</strong></br><span><?php echo ($extra_information["custome_ip"]); ?></span></li>
					<li><strong>历史来源:</strong></br><span><?php echo ($extra_information["come_from_history"]); ?></span></li>
					</ul>
   				</div>
	         </td>
         </tr><?php endforeach; endif; else: echo "" ;endif; ?>
         </table>
         <?php else: ?><div style="font-size: 16px;font-weight: bold;text-align: center;color:red;">暂无历史订单</div><?php endif; ?>
   </div>
   
  <!-- <div class="panel" style="width:100%;float:left;margin-top:10px;">
	   	<div class="panel-head"><strong>付款记录</strong><?php $payment_array=get_payment_fail($web_message_shipping[id]);echo $web_message_shipping[id]; ?></div>
	   		<?php if(!empty($payment_array)): ?><span></span>
	   		 	<?php else: ?>
	   		 	<div style="font-size: 16px;font-weight: bold;text-align: center;color:red;">暂无记录</div><?php endif; ?>
	</div>
     --> 
	<div class="panel" style="width:100%;float:left;margin-top:10px;">
      <div class="panel-head"><strong>其他信息</strong></div>
      <?php $extra_information=get_payment_fail($web_message_shipping[id]); ?>
		<ul class="list-group">
			<li><strong>付款记录:</strong></br><span><?php echo ($extra_information["payment_fail"]); ?></span></li>
			<li><strong>IP信息:</strong></br><span><?php echo ($extra_information["custome_ip"]); ?></span></li>
			<li><strong>历史来源:</strong></br><span><?php echo ($extra_information["come_from_history"]); ?></span></li>
		</ul>
   	</div>
</div>

</body>
</html>