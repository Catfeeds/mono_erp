<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>Lilysilk - ERP</title>
</head>

<!-- 加载js css -->
<link rel="stylesheet" href="/test/erptest/Application/Admin/View/Default/css/pintuer.css">
<link rel="stylesheet" href="/test/erptest/Application/Admin/View/Default/css/admin.css">
<script src="/test/erptest/Application/Admin/View/Default/js/jquery.min.js"></script>
<script src="/test/erptest/Application/Admin/View/Default/js/pintuer.js"></script>
<script src="/test/erptest/Application/Admin/View/Default/js/respond.js"></script>
<script src="/test/erptest/Application/Admin/View/Default/js/admin.js"></script>
<script src="/test/erptest/Application/Admin/View/Default/js/layer/layer.js"></script>
<script src="/test/erptest/Application/Admin/View/Default/Public/KindEditor/kindeditor-min.js"></script>
<script src="/test/erptest/Application/Admin/View/Default/Public/KindEditor/lang/zh_CN.js"></script>
<script src="/test/erptest/Application/Admin/View/Default/js/function.js?v=<?php echo time();?>"></script><!-- 公共js函数文件 -->
<script>
	KindEditor.ready(function(K) {
		editor = K.create('textarea[name="content"]', {
			allowFileManager: true,
			afterBlur: function(){this.sync();},
		});
	});
</script>
<script language="javascript" type="text/javascript" src="/test/erptest/Application/Admin/View/Default/Public/wdatepicker/WdatePicker.js"></script>
<script>//定义需要的“常量”
var _CONTROLLER_ = '/test/erptest/index.php/Admin/OrderManage';
var _ACTION_  = '/test/erptest/index.php/Admin/OrderManage/order_plat';
var _PUBLIC_ = '/test/erptest/Public';
var _TIMESTAMP_ = <?php echo time();?>;
var _SELF_ = '/test/erptest/index.php/Admin/OrderManage/order_plat/order_status/all/come_from_id/1.html';
var _APP_='/test/erptest/index.php';
var _MODULE_ = '/test/erptest/index.php/Admin';
var _ROOT_ = '/test/erptest';

</script>


<link type="image/x-icon" href="http://www.pintuer.com/favicon.ico" rel="shortcut icon" />
<link href="http://www.pintuer.com/favicon.ico" rel="bookmark icon" />
<body>

<!-- 可以选择不加载菜单 -->
<?php if($no_menu != true): ?><div class="lefter">
    <div class="logo"><img src="/test/erptest/Application/Admin/View/Default/images/logo.png" style="height:40px;" alt="后台管理系统" /><b style="font-size: 12px">ERP</b></div>
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
            <span> <a href="/test/erptest/index.php/Admin/messages/Index.html" class="plcz" style=" margin-right:30px;">私信</a> <?php echo ($username); ?> &nbsp;&nbsp; 工号: <?php echo $monoid>0?$monoid:0;?> &nbsp;&nbsp;</span>
            
            
            
            <ul class="bread">
                <li><a href="index.html" class="icon-home"> 开始</a></li>
                <li>后台首页</li>
            </ul>
        </div>
    </div>
</div>
<span class="icon-dedent" style="position: fixed; top: 84px; left: 180px; z-index: 100;color: #09c;cursor: pointer;font-size: 20px;width: 10px;" onclick='show_hide_left_menu(this);'></span><?php endif; ?>
<script src="/test/erptest/Application/Admin/View/Default/js/OrderManage/main.js?v=<?php echo time();?>"></script>
<script src="/test/erptest/Application/Admin/View/Default/js/CodeManage/main.js?v=<?php echo time();?>"></script>
<script type="text/javascript" src="/test/erptest/Application/Admin/View/Default/js/ServiceManage/logistics_information.js"></script>
<script>
<?php if(!empty($anchor)): ?>$(window).load(
	function(){
		anchor(<?php echo ($anchor); ?>,'<?php echo ($correct_url); ?>');
	}
);<?php endif; ?>
<?php if(!empty($order_sync_result)): ?>layer.alert("<?php echo ($order_sync_result); ?>",{title:'订单更新结果',shade: 0.5});<?php endif; ?>
<?php if(!empty($order_to_factory_message)): ?>layer.msg("<?php echo ($order_to_factory_message); ?>");<?php endif; ?>
</script>
<style>
.red_warning,.red_warning td,.red_warning td span{color:#e33 !important;}
.yellow_warning,.yellow_warning td,.yellow_warning td span{color:#f60 !important;}
.accept_ul{ margin:0; padding:0;}
.accept_ul li{list-style:none; float: left;}
.product_original_set_tr td{ padding:2px 8px 2px 8px;}
</style>

<div class="admin">
<form action="<?php echo U('order_plat', $param );?>" method="post" id="muti_form">
<input type="hidden" id="action" name="action" value=""/>
<input type="hidden" id="action" name="data" value="plat"/>
	<table class="table table-striped table-hover table-condensed">
   	<tr>
		<td colspan="20" class="table_title" style="font-size:14px;padding:10px;">
			<span class="float-left icon-list"> <?php echo ($title); ?></span>
			<?php if(!in_array(($filter["come_from_id"]), explode(',',"100,101"))): ?><small style="margin-left:10px;">
					（最近更新：<span style="font-size:12px;color:red;"><?php echo ($update_time_previous); ?> — <?php echo ($update_time_current); ?></span>
					<?php if(in_array(($order_status), explode(',',"all,audit"))): ?><a onclick="return muti_form_submit('sync')" style="cursor:pointer;margin-left:8px;text-decoration:underline;color:#0a8;">点击更新数据</a><?php endif; ?>
					）
				</small><?php endif; ?>
			<span class="fr">
				<?php if(!empty($subtitle["total"])): ?>共<span class="text-red"><?php echo ($subtitle["total"]); ?></span>条记录，<?php endif; ?>
				<?php if(!empty($subtitle["overtime"])): ?>其中有<span class="text-red"><?php echo ($subtitle["overtime"]); ?></span>条<span style="font-weight:bold;">超时</span>记录，<?php endif; ?>
				<?php if(!empty($subtitle["lacksku"])): ?>有<span class="text-red"><?php echo ($subtitle["lacksku"]); ?></span>条记录<span style="font-weight:bold;">未关联sku</span>，<?php endif; ?>
			</span>
		</td>
	</tr>
	
	<tr>
		<td colspan="20" class="table_title" style="font-size:14px;padding:10px;">
		<style>
			.button_country{font-size:10px;margin-right:10px;padding:3px;border:1px solid black;}
			.button_country:hover{border-color:#0a8;cursor:pointer;}
			.button_country.active{font-size:16px;border-color:#0a8;color:#FFF;background-color:#0a8}
		</style>
		<?php if(is_array($come_from_list)): $i = 0; $__LIST__ = $come_from_list;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$come_from_vo): $mod = ($i % 2 );++$i;?><a class="button_country <?php if(($filter["come_from_id"]) == $come_from_vo["id"]): ?>active<?php endif; ?>" onclick="return muti_form_submit('come_from_id',<?php echo ($come_from_vo["id"]); ?>)"><?php echo ($come_from_vo["alias"]); ?></a><?php endforeach; endif; else: echo "" ;endif; ?>
		<input type="hidden" name="come_from_id" id="come_from_id" value=""/>
		</td>
	</tr>
	
	<tr>
		<td colspan="20">
<!-- 		<form action="<?php echo U('order_plat', $param );?>" method="post"> -->
			<label for="order_number">订单号:</label>
			<input name="order_number" type="text" value="<?php echo ($filter["order_number"]); ?>" class="margin-right"/>
			<label for="currency">币种:</label>
			<select name="currency" class="margin-right">
				<option value="-1">--请选择--</option>
				<?php if(is_array($currency_list)): $i = 0; $__LIST__ = $currency_list;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$currency_vo): $mod = ($i % 2 );++$i;?><option <?php if(($filter['currency']) == $currency_vo["currency"]): ?>selected="selected"<?php endif; ?> value="<?php echo ($currency_vo["currency"]); ?>"><?php echo ($currency_vo["currency"]); ?></option><?php endforeach; endif; else: echo "" ;endif; ?>
			</select>
<!-- 		<label for="come_from_id">来源:</label>
			<select name="come_from_id" class="margin-right">
				<option value="-1">--请选择--</option>
				<?php if(is_array($come_from_list)): $i = 0; $__LIST__ = $come_from_list;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$come_from_vo): $mod = ($i % 2 );++$i;?><option <?php if(($filter['come_from_id']) == $come_from_vo["id"]): ?>selected="selected"<?php endif; ?> value="<?php echo ($come_from_vo["id"]); ?>"><?php echo ($come_from_vo["name"]); ?></option><?php endforeach; endif; else: echo "" ;endif; ?>
			</select> -->	
			<label for="overtime">超时:</label>
			<input name="overtime" type="checkbox" value="true" <?php if(($filter["overtime"]) == "true"): ?>checked="checked"<?php endif; ?> class="margin-right"/>
			<input type="submit" value="查询" onclick="return muti_form_submit('filter')"/>
			<input type="submit" value="清空查询" onclick="return muti_form_submit('clear')"/>
			
			<span class="float-right"><?php echo ($page_bar); ?></span>
<!--		</form> -->
		</td>
	</tr>
	
	<tr>
		<td colspan="20">
		<span style="font-size:14px;"><a onclick="select_all()" class="icon-arrows-v margin-left" href="#"> 全选 </a></span>
		<span style="font-size:14px;"><a href="javascript:void(0)" onClick="muti_form_submit('export_address')" class="icon-share-square-o margin-left">导出订单地址 </a></span>
		<span style="font-size:14px;"><a href="<?php echo U('order_address_import');?>" class="icon-share-square-o margin-left">导入订单地址 </a></span>
        <?php if(($order_status) == "all"): ?><span style="font-size:14px;"><a  class="icon-share-square-o margin-left" href="/test/erptest/index.php/Admin/OrderManage/export_execl_order/data/plat"> 导出未发货订单 </a></span><?php endif; ?>
		<?php if(($order_status) == "audit"): ?>
			<span style="font-size:14px;"><a href="javascript:void(0)" onClick="layer.confirm('确认全部审核通过？',{title:false,closeBtn:false},
					function(index){muti_form_submit('batch_audit');layer.close(index);})" class="icon-check-square-o margin-left"> 批量审核 </a></span><?php endif; ?>
		<?php if(($order_status) == "shipping"): ?>
			<span style="font-size:14px;"><a href="javascript:void(0)" onClick="layer.confirm('确认全部转入历史？',{title:false,closeBtn:false},
					function(index){muti_form_submit('batch_history');layer.close(index);})" class="icon-check-square-o margin-left"> 批量转入历史</a></span><?php endif; ?>
		<input type="hidden" name="come" value="plat">
        <span style="font-size:14px;"><a onclick="order_print_plat()" class="icon-print margin-left" style='cursor:pointer;'> 打印商品详情单 </a></span>
        <span style="font-size:14px;"><a onclick="return muti_form_submit('export_order_for_finance')" class="icon-share-square-o margin-left" style='cursor:pointer;'> 导出订单（财务用） </a></span>
		</td>
	</tr>
    <?php if(($order_status) == "history"): ?><tr>
        	<td colspan="20">
                <select name="type_waybill" class="margin-right">
                    <option value="0">--全部--</option>
                    <?php
 $aa=delivery_style(); foreach($aa as $k=>$v) { ?>
                    	<option value="<?php echo $v; ?>"><?php echo $v; ?></option>
                     <?php  } ?>
                </select>
                <span>时间区间：</span>
                    <input type="text" style="cursor:pointer;" name="beginTime" id="btime" class="Wdate bd" onClick="WdatePicker()" />
                    <input type="text" style="cursor:pointer;" name="endTime" id="btime" class="Wdate bd" onClick="WdatePicker()" />
                <span style="font-size:14px;"><a onclick="order_waybill_plat(2)" class="icon-share-square-o margin-left" href="#"> 导出筛选运单 </a></span>
                <span style="font-size:14px;"><a onclick="order_waybill_plat(1)" class="icon-share-square-o margin-left" href="#"> 导出选中运单 </a></span>
            </td>
        </tr><?php endif; ?>
   	<tr class="list_head">
   		<td width="30"></td>
   		<td width="45" style="text-align:left"></td>
   		<td width="120" style="text-align:left">
    		<a onclick="free_sort('order_number','<?php echo ($param_json); ?>')" href="#" class="text-green">订单号 
    		<?php if(($sort_field) == "order_number"): if(($sort_style) == "desc"): ?><span class="icon-sort-desc"></span>
	    		<?php else: ?>
	    			<span class="icon-sort-asc"></span><?php endif; endif; ?>
   			</a>
  		</td>
  		<?php if(in_array(($filter["come_from_id"]), explode(',',"18,19"))): ?><td width="100" style="text-align:left">订单ID</td><?php endif; ?>
  		<?php if(($order_status) != "audit"): ?>
        	<td width="110" style="text-align:left">工厂编号</td><?php endif; ?>
   		<td width="100" style="text-align:left">用户名</td>
<!--	<td width="150">邮箱</td>-->
<!--	<td width="100">电话</td>-->
   		<td width="60">币种</td>
   		<td width="60">总价</td>
   		<td style=" max-width:200px;">客户留言</td>
<!-- 	<td width="60">礼品</td> -->
   		<td width="100">来源</td>
   		<td width="100">
    		<a onclick="free_sort('date_time','<?php echo ($param_json); ?>')" href="#" class="text-green">下单时间 
	    		<?php if(($sort_field) == "date_time"): if(($sort_style) == "desc"): ?><span class="icon-sort-desc"></span>
		    		<?php else: ?>
		    			<span class="icon-sort-asc"></span><?php endif; endif; ?>
   			</a>
   		</td>
<!--	<td width="100">货运服务</td>	-->
   		<td style=" max-width:200px;">商家留言</td>
   		<?php if(($order_status) == "shipping"): ?>
   		<td width="120">运单</td><?php endif; ?>
   		<td width="100">状态</td>
   		<td width="100">操作</td>
	</tr>
	<?php if(is_array($order_list)): $i = 0; $__LIST__ = $order_list;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><tr id="list_row_<?php echo ($vo['id']); ?>" class='<?php if(($mod) == "1"): ?>tr<?php else: ?>ji<?php endif; ?>'> 
   		<td><input class="check" name="check[]" id="check_<?php echo ($vo["id"]); ?>" type="checkbox" value="<?php echo ($vo["id"]); ?>" ></td>
       	<td align='left'><span class="td_number"><?php if(empty($_GET['p'])): echo ($i); else: echo ($_GET['p']-1)*$listRows+$i; endif; ?></span></td>
       	<td align='left' style="color:#03c">
       		<?php if(($vo["is_return_exchange"]) == "1"): ?><span style="color:red;">主单号</span><?php endif; ?>
       		<span onclick="see_more(<?php echo ($vo["id"]); ?>)" style="cursor:pointer;"><?php echo ($vo["order_number"]); if(($vo["is_return_exchange"]) == "1"): ?>&nbsp;R&E<?php endif; ?></span>
       		<strong style="display:none;" id="warning_<?php echo ($vo['id']); ?>"></strong>
			<span style="color:red;"><?php echo (address_isset_box($vo['shipping_info']['address1'])); ?></span>
            <?php if($vo['is_sample_record']): ?><div style="padding: 5px; background: red;width: 50px;text-align: center; color: #000;"><?php echo ($vo["is_sample_record"]); ?></div><?php endif; ?>
  		</td>
  		<?php if(in_array(($filter["come_from_id"]), explode(',',"18,19"))): ?><td align='left' style="color:#03c" width="100"><?php echo ($vo["ebay_order_id"]); ?></td><?php endif; ?>
  		<?php if(($order_status) != "audit"): ?><td align="left">
        	<?php if(is_array($vo["factory_order"])): $factory_order_key = 0; $__LIST__ = $vo["factory_order"];if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo2): $mod = ($factory_order_key % 2 );++$factory_order_key;?><p style=" margin:0px;">
                    <?php echo get_factory_str($vo2[factory_id],$vo2["date"],$vo2['number'],'execl','plat');?>
                     <?php if($vo2['factory_id'] == 9): ?><span class="icon-check" style="color:red;"></span>
                     <?php else: ?>
                        <?php if(in_array(($vo2['status']), explode(',',"history,history_ok"))): ?><span class="icon-check" style="color:red;"></span><?php endif; endif; ?>   
                </p><?php endforeach; endif; else: echo "" ;endif; ?>
            <?php if(is_array($vo["fba_order"])): $k = 0; $__LIST__ = $vo["fba_order"];if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo3): $mod = ($k % 2 );++$k;?><p style=" margin:0px;">
                	FBAW<?php echo (date('m.d',strtotime($vo3["date"]))); ?>-<?php if($vo3["number"] < 10): ?>0<?php endif; echo ($vo3["number"]); ?>
                	<?php if($vo3[status] == "history"): ?><span class="icon-check" style="color:red;"></span><?php endif; ?>
                </p><?php endforeach; endif; else: echo "" ;endif; ?>
           <?php if(is_array($vo["product_stock_order"])): $k = 0; $__LIST__ = $vo["product_stock_order"];if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo4): $mod = ($k % 2 );++$k;?><p style=" margin:0px;">
                 	KW<?php echo (date('m.d',strtotime($vo4["date"]))); ?>-<?php if($vo4["number"] < 10): ?>0<?php endif; echo ($vo4["number"]); ?>
                    <?php if($vo4[status] == "history"): ?><span class="icon-check" style="color:red;"></span><?php endif; ?>
                 </p><?php endforeach; endif; else: echo "" ;endif; ?> 
        </td><?php endif; ?>
       	<td align='left'><?php echo ($vo["name"]); ?></td>        
<!--   	<td align='center' style="word-break:break-all;min-width:80px;"><?php echo ($vo["email"]); ?></td>-->
<!--   	<td align='center'><?php echo ($vo["telephone"]); ?></td> -->
       	<td align='center'><?php echo ($vo["currency"]); ?></td>
       	<td align='center' style="max-width:80px;overflow:hidden;"><?php echo ($vo["price"]); ?></td>
       	<td align='center'><?php echo ($vo["message"]); ?></td>
<!-- 	<td align='center'><?php if(($vo["is_gift_package"]) == "1"): ?><font color="red" class="icon-check"></font><?php else: ?><font color="blue" class="icon-times"></font><?php endif; ?></td> -->       
       	<td align='center'><?php echo ($vo["come_from_info"]["name"]); ?></td>
       	<td align='center'><?php echo ($vo["date_time"]); ?></td>
<!--  	<td align='center'><?php echo ($vo["ship_service_level"]); ?></td> -->   
   		<td align='left' style="max-width: 200px;min-width:120px;overflow: auto;">
			<div id="message_first_<?php echo ($vo["id"]); ?>" class="tips" data-place="bottom" data-target="#message_tips_<?php echo ($vo["id"]); ?>" >
		   		<?php if(!empty($vo['message_info'])): ?><strong><?php echo username_name($vo['message_info'][0]['operator']);?></strong> -
				<small><?php echo date('Y-m-d H:i:s',$vo['message_info'][0]['date_time']);?></small> : <br/> 
				<?php echo ($vo['message_info'][0]['message']); ?>
				<?php if(sizeof($vo['message_info']) >= 2): ?><span class="icon-eye text-red"></span><?php endif; endif; ?>
			</div>
	   		<div id='message_tips_<?php echo ($vo["id"]); ?>' class="hidden">
	   			<?php if(sizeof($vo['message_info']) >= 2): if(is_array($vo['message_info'])): $k = 0; $__LIST__ = array_slice($vo['message_info'],1,null,true);if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$message_vo): $mod = ($k % 2 );++$k;?><p style="max-width:300px;">
					<strong><?php echo username_name($message_vo['operator']);?></strong> - 
					<small><?php echo date('Y-m-d H:i:s',$message_vo['date_time']);?></small> : <br/>
					<?php echo ($message_vo["message"]); ?>
				</p><?php endforeach; endif; else: echo "" ;endif; endif; ?>
			</div>
		</td>
		<?php if(($order_status) == "shipping"): ?>
		<td>
		<?php $temp = get_delivery_info($vo['id'],'plat'); ?>
		<?php if(!empty($temp)): ?><span><strong>重量:&nbsp;</strong><?php echo ($temp["weight"]); ?></span><br/>
	  		<span>
	  			<strong>快递:&nbsp;</strong><?php echo ($temp["shipping_style"]); ?>
	  			<a href="#" onclick="delivery_edit(<?php echo ($vo["id"]); ?>,'plat','fetch')" class="text-red icon-pencil margin-small-left" title="编辑运单信息"></a>
	  			<a href="#" class="icon-sign-out text-yellow margin-small-left" title="打印运单"></a>
	  		</span>
		<?php else: ?>
	  		<?php $temp = delivery_recommend($vo['id'],'plat'); ?>
	  		<span><strong>预估重量: </strong><?php echo ($temp['weight']); ?></span><br/>
	  		<span>
	  			<strong>推荐快递: </strong><?php echo ($temp['style']); ?>
	  			<a href="#" onclick="delivery_edit(<?php echo ($vo["id"]); ?>,'plat','fetch')" class="text-red icon-pencil margin-small-left" title="编辑运单信息"></a>
	  			<a href="#" onclick="layer.msg('请先完善运单信息！')" class="icon-sign-out text-gray margin-small-left" title="打印运单"></a>
  			</span><?php endif; ?>
		</td><?php endif; ?>
       	<td align='center' style="color:#f60"><?php if(empty($vo["status_info"]["status"])): ?>待审核<?php else: echo $order_status_list[$vo['status_info']['status']]; endif; ?></td>
       	<td align='center'>
       		<?php if(($order_status) == "audit"): ?>
       		<a href="javascript:void(0)" id="audit_button_<?php echo ($vo["id"]); ?>" onclick="layer.confirm('确定审核通过该订单？', {btn:['确定','取消'], title:false, closeBtn:0}, 
				function(){location.href='<?php echo U('order_plat', array_merge($param,array('order_id'=>$vo['id']) ) );?>'});"
				class="icon-check-square-o text-red margin-right" title="审核通过"></a><?php endif; ?>
			<a class="icon-file-text margin-right" title="详情" href="<?php echo U('/Admin/OrderManage/order_details',array('order_id'=>$vo[id],'order_status'=>$order_status));?>"></a>
            <?php if($vo['fba_order'] ): ?><span class="icon-chevron-down" style="color:#CCC;"></span>
            <?php else: ?>
       			<a id="button_see_more_<?php echo ($vo["id"]); ?>" onclick="see_more(<?php echo ($vo["id"]); ?>)" class="icon-chevron-down" title="展开" href="#"></a><?php endif; ?>
      	</td>
      	
		<?php echo check_limit_time($vo['id'], $order_status, 'audit', $vo['date_time'], get_system_parameter('order_audit_limit_lv1','int'), '超时未审核', get_system_parameter('order_audit_limit_lv1','char'));?>
		<?php echo check_limit_time($vo['id'], $order_status, 'audit', $vo['date_time'], get_system_parameter('order_audit_limit_lv2','int'), '', get_system_parameter('order_audit_limit_lv2','char'));?>
		<?php echo check_limit_time($vo['id'], $order_status, 'audit', $vo['date_time'], get_system_parameter('order_audit_limit_lv3','int'), '', get_system_parameter('order_audit_limit_lv3','char'));?>
		
		<?php if(($vo["total_order_count"] > 1) AND ($vo["is_return_exchange"] != 1)): ?><script>
      			mark_row(<?php echo ($vo['id']); ?>,"该用户还有另<?php echo ($vo[total_order_count]-1); ?>条订单");
   			</script><?php endif; ?>
		
   	</tr>
   	<tr id="see_more_<?php echo ($vo["id"]); ?>" style="display:none;">
   	<td></td>
   	<td></td>
	<td colspan="<?php echo ($colspan); ?>" style="border:1px solid gray;border-right-width:0;">
	<?php if(in_array(($vo['status_info']['status']), explode(',',"shipping,history"))): ?>
	<div class="panel">
		<?php $temp = get_delivery_info($vo['id'],'plat'); ?>
		<div class="panel-head">
			<strong>运单详情：</strong>
			<a href="#" onclick="delivery_edit(<?php echo ($vo["id"]); ?>,'plat','fetch')" class="icon-pencil text-red" title="编辑运单信息"></a>
			<?php if(!empty($temp)): ?><a target="_blank" href="<?php echo U('/Admin/OrderDelivery/index',array('style'=>'flat','id'=>$vo['id']));?>" class="icon-sign-out text-yellow margin-left" style="font-size:14px;" title="打印运单"></a>
			<?php else: ?>
				<a href="#" onclick="layer.msg('请先完善运单信息！')" class="icon-sign-out text-gray margin-left" style="font-size:14px;" title="打印运单"></a><?php endif; ?>
		</div>
		<ul class="my_ul">
		<?php $temp2 = get_delivery_number($vo['id'],'plat'); ?>
		<?php if(!empty($temp2)): ?><li><strong>运单号:</strong><?php echo ($temp2["delivery_number"]); ?></li><?php endif; ?>
		<?php if(!empty($temp)): ?><li><strong>重量:</strong><?php echo ($temp["weight"]); ?></li>
	  		<li><strong>快递:</strong><?php echo ($temp["shipping_style"]); ?></li>
	  		<li><strong>运费:</strong><?php echo ($temp["shipping_price"]); ?></li>
	  		<?php if(!empty($temp['hs'])): ?><li><strong><?php echo ($temp["hs"]); ?>, <?php echo ($temp["name"]); ?>, <?php echo ($temp["price"]); ?></strong></li><?php endif; ?>
            <li><strong>打单时间:</strong> <?php echo ($temp2["time"]); ?> </li>
            <span class="icon-times cur" style="color: red;font-size: 16px;" onclick="delivery_delete(<?php echo ($vo['id']); ?>,'plat')" title="删除"></span>
		<?php else: ?>
	  		<?php $temp = delivery_recommend($vo['id'],'plat'); ?>
	  		<li><strong>预估重量:</strong><?php echo ($temp['weight']); ?></li>
	  		<li><strong>推荐快递:</strong><?php echo ($temp['style']); ?></li>
	  		<li><strong>运费:</strong><?php echo ($temp['price']); ?></li>
	  		<?php if($temp2["delivery_number"] == ''): ?><li><a href="#" onclick="delivery_edit(<?php echo ($vo["id"]); ?>,'plat','fetch')" class="text-yellow">点击编辑运单信息</a></li><?php endif; endif; ?>
		</ul>
        <?php if($temp2["delivery_number"] != ''): ?><br />
            <ul class="my_ul">
            	<?php $delivery_numbe_val= logistics_information($temp2['style'],$temp2['delivery_number']); ?>
                <li style="color:red;"><?php echo ($delivery_numbe_val); ?></li>
                <li class="plcz" onclick="btnSnap('<?php echo ($temp2["style"]); ?>','<?php echo ($temp2["delivery_number"]); ?>')">查看运单详情</li>
            </ul><?php endif; ?>
	</div><?php endif; ?>
	<div class="panel">
		<div class="panel-head">
			<strong>商品详情：</strong>
			<a href="/test/erptest/index.php/Admin/ProductReturn/product_return_order/order_id/<?php echo ($vo["id"]); ?>/come/plat/order_status/<?php echo ($order_status); ?>" class="icon-pencil text-red" title="退货/换货/追加"></a>
			<span style="display:none;margin-left:10px;cursor:pointer;" id="audit_button_<?php echo ($vo["id"]); ?>" onclick="layer.confirm('确定审核通过该订单？', {btn:['确定','取消'], title:false, closeBtn:0}, 
				function(){location.href='<?php echo U('order_plat', array_merge($param,array('order_id'=>$vo['id'],'exchange'=>1,'anchor'=>$vo['id']) ) );?>'});"
				class="icon-check-square-o text-red margin-right" title="追加审核">追加审核
			</span>
		</div>
		<table>
			<tr>
				<!-- 序号	套件	价格	名称	颜色	尺码	工厂	数量	状态  -->
				<th style="text-align:center;" width="45"></th>
				<th style="text-align:center;" width="180">产品名称</th>
				<th style="text-align:center;" width="100">sku</th>
				<th style="text-align:center;" width="80">价格</th>
				<th style="text-align:center;" width="120">code</th>
				<th style="text-align:center;" width="80">颜色</th>
				<th style="text-align:center;" width="60">尺码</th>
				<th style="text-align:center;" width="60">工厂</th>
				<th style="text-align:center;" width="60">数量</th>
				<th style="text-align:center;" width="60">状态</th>
			</tr>
			<?php $flag = 'false'; ?>
			<?php $flag_for_cancel = 'false'; ?>
			<?php if(is_array($vo['product_info'])): $product_group_i = 0; $__LIST__ = $vo['product_info'];if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$product_group_vo): $mod = ($product_group_i % 2 );++$product_group_i; if( ( (sizeof($product_group_vo) > 1) AND ( $key != 0 ) ) OR ( (sizeof($product_group_vo) == 1) AND ($product_group_vo[0]['number_in_set'] > 1) ) ): ?>
					<?php if(is_array($product_group_vo)): $product_i = 0; $__LIST__ = $product_group_vo;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$product_vo): $mod = ($product_i % 2 );++$product_i;?><tr bgcolor=<?php if($product_group_vo[0][status] == 'return'): ?>'#999999'<?php elseif($product_group_vo[0][status] == 'new'): ?>'#7F9DB9'<?php elseif($product_group_vo[0][status] == 'cancel'): ?>'#FF0000'<?php endif; ?>>
							<td><?php if(($product_i) == "1"): echo ($product_group_i); endif; ?></td>
							<td><?php if(($product_i) == "1"): echo ($product_vo["name"]); ?><span style="color:red;margin-left:10px;">[套件]</span><?php endif; ?>
							</td>
							<td align="center" id="td_{product_vo.id}">
								<?php if(($product_i) == "1"): if(($filter["come_from_id"]) == "20"): ?>
										<?php if(($product_i) == "1"): ?><input id="add_sku_{product_vo.id}" type="text" style="display:<?php if(!empty($product_group_vo["0"]["sku"])): ?>none<?php else: ?>block<?php endif; ?>">
											<span><?php echo ($product_group_vo["0"]["sku"]); ?></span>
											<?php if(!empty($product_group_vo["0"]["sku"])): ?><span class="icon-plus-square text-green" title="添加sku" onclick="sku_relate(<?php echo ($product_vo["id"]); ?>,<?php echo ($vo["come_from_id"]); ?>)" style="display:none;"></span>
												<span class="icon-pencil text-green" title="修加sku" onclick="update_sku(<?php echo ($product_vo["id"]); ?>)"></span>
											<?php else: ?>
												<span class="icon-plus-square text-green" title="添加sku" onclick="sku_relate(<?php echo ($product_vo["id"]); ?>,<?php echo ($vo["come_from_id"]); ?>)"></span>
												<span class="icon-pencil text-green" title="修改sku" style="display:none;" onclick="update_sku(<?php echo ($product_vo["id"]); ?>)"></span><?php endif; ?>
											<input type="hidden" id="hidden_<?php echo ($product_vo["id"]); ?>" value=""><?php endif; ?>
									<?php else: ?>	
										<span><?php echo ($product_group_vo["0"]["sku"]); ?></span><?php endif; ?>
									<?php if( ($vo['status_info'] == null) OR ($vo['status_info']['status'] == 'audit')): ?>
										<?php if(!empty($product_vo["code_id"])): ?><a onclick="code_relate(<?php echo ($product_vo["id"]); ?>,<?php echo ($product_vo["sku_id"]); ?>,<?php echo ($vo["id"]); ?>,'edit')" class="icon-pencil text-red" title="修改关联"></a>
										<?php else: ?>
											<a onclick="code_relate(<?php echo ($product_vo["id"]); ?>,<?php echo ($product_vo["sku_id"]); ?>,<?php echo ($vo["id"]); ?>,'add')" class="icon-plus-square text-green" title="添加关联"></a><?php endif; endif; endif; ?>
							</td>
							<td align="center"><?php if(($product_i) == "1"): ?><span><?php echo ($product_vo["price"]); ?></span><?php endif; ?>
							</td>
							<?php if(!empty($product_vo["code_id"])): ?><td align="center">
									<?php echo ($product_vo["code_info"]["name"]); ?>
								</td>
								<td align="center"><?php echo ($product_vo["code_info"]["color_info"]["value"]); ?></td>
								<td align="center"><?php echo ($product_vo["code_info"]["size_info"]["value"]); ?></td>
								<td align="center">
									<?php echo ($product_vo["code_info"]["factory_info"]["name"]); ?>&nbsp;
									<span style="font-weight:bold;font-size:12px;"><?php echo check_local_stock($product_vo['code_id'], $product_vo['number']);?></span>
								</td>
							<?php else: ?>
								<td align="center">
									<span class="text-red">未关联</span>
									<?php if(($flag) == "false"): ?>
										<script>
										$("#check_<?php echo ($vo["id"]); ?>").attr("disabled","disabled");
										$("#audit_button_<?php echo ($vo["id"]); ?>").attr("onclick","layer.msg('产品sku未关联code');");
										$("#audit_button_<?php echo ($vo["id"]); ?>").removeClass("text-red");
										$("#audit_button_<?php echo ($vo["id"]); ?>").addClass("text-gray");
										mark_row(<?php echo ($vo['id']); ?>,"产品sku未关联","gradual_color_lv10");
										</script>
										<?php $flag = 'true'; endif; ?>
								</td>
								<td align="center"><span style="color:red;">—</span></td>
								<td align="center"><span style="color:red;">—</span></td>
								<td align="center"><span style="color:red;">—</span></td><?php endif; ?>
							<td align="center">
								<?php if(empty($product_vo。number_in_set)): echo ($product_vo['number']*$product_vo['number_in_set']); ?>
								<?php else: ?>
									<?php echo ($product_vo['number']); endif; ?>
							</td>
							<td align="center">
								<?php if(!empty($product_vo["status"])): echo ($product_vo["status"]); ?>
								<?php else: ?>
									normal<?php endif; ?>
								<?php if(in_array(($order_status), explode(',',"all,accept,shipping"))): if( ( $product_vo['status'] == 'new' ) AND ( $order_status != 'audit' )): ?><script>
											$("#audit_button_<?php echo ($vo["id"]); ?>").css("display","inline-block");
										</script><?php endif; endif; ?>
								<?php if(($product_vo["status"]) == "cancel"): ?><script>mark_row(<?php echo ($vo["id"]); ?>,'有取消产品')</script>
									<?php $flag_for_cancel = 'true'; endif; ?>
							</td>
						</tr><?php endforeach; endif; else: echo "" ;endif; ?>
				<?php else: ?>
					<tr bgcolor=<?php if($product_group_vo[0][status] == 'return'): ?>'#999999'<?php elseif($product_group_vo[0][status] == 'new'): ?>'#7F9DB9'<?php elseif($product_group_vo[0][status] == 'cancel'): ?>'#FF0000'<?php endif; ?>>
						<td><?php echo ($product_group_i); ?></td>
						<td align="center"><?php echo ($product_group_vo["0"]["name"]); ?>
						<?php if(($filter["come_from_id"]) == "20"): ?></br><span style="color:red;">管理号&nbsp;:&nbsp;<?php echo ($product_group_vo["0"]["rakuten_product_model"]); ?></span>
						</br><span style="color:red;">颜色&nbsp;:&nbsp;<?php echo ($product_group_vo["0"]["rakuten_product_color"]); ?></span>
						</br><span style="color:red;">尺码&nbsp;:&nbsp;<?php echo ($product_group_vo["0"]["rakuten_product_size"]); ?></span>
						</br><span style="color:red;">送手帕&nbsp;:&nbsp;<?php echo ($product_group_vo["0"]["rakuten_product_hanky"]); ?></span>
						</br><span style="color:red;">礼品盒包装&nbsp;:&nbsp;<?php echo ($product_group_vo["0"]["rakuten_product_gift_package"]); ?></span><?php endif; ?>
						</td>
						<td align="center" id="td_<?php echo ($product_group_vo["0"]["id"]); ?>">
							<?php if(($filter["come_from_id"]) == "20"): ?><input id="add_sku_<?php echo ($product_group_vo["0"]["id"]); ?>" type="text" style="display:<?php if(!empty($product_group_vo["0"]["sku"])): ?>none<?php else: ?>block<?php endif; ?>">
								<span><?php echo ($product_group_vo["0"]["sku"]); ?></span>
								<?php if(!empty($product_group_vo["0"]["sku"])): ?><span class="icon-plus-square text-green" title="添加sku" onclick="sku_relate(<?php echo ($product_group_vo["0"]["id"]); ?>,<?php echo ($vo["come_from_id"]); ?>)" style="display:none;"></span>
								<span class="icon-pencil text-green" title="修改sku" onclick="update_sku(<?php echo ($product_group_vo["0"]["id"]); ?>)"></span>
								<?php else: ?>
								<span class="icon-plus-square text-green" title="添加sku" onclick="sku_relate(<?php echo ($product_group_vo["0"]["id"]); ?>,<?php echo ($vo["come_from_id"]); ?>)"></span>
								<span class="icon-pencil text-green" title="修改sku" style="display:none;" onclick="update_sku(<?php echo ($product_group_vo["0"]["id"]); ?>)"></span><?php endif; ?>
								<input type="hidden" id="hidden_<?php echo ($product_group_vo["0"]["id"]); ?>" value="">
							<?php else: ?>
								<span><?php echo ($product_group_vo["0"]["sku"]); ?></span><?php endif; ?>
							<?php if( ($vo['status_info'] == null) OR ($vo['status_info']['status'] == 'audit')): if(!empty($product_group_vo["0"]["code_id"])): ?><a onclick="code_relate(<?php echo ($product_group_vo["0"]["id"]); ?>,<?php echo ($product_group_vo["0"]["sku_id"]); ?>,<?php echo ($vo["id"]); ?>,'edit')" class="icon-pencil text-red" title="修改关联"></a>
								<?php else: ?>
									<a onclick="code_relate(<?php echo ($product_group_vo["0"]["id"]); ?>,<?php echo ($product_group_vo["0"]["sku_id"]); ?>,<?php echo ($vo["id"]); ?>,'add')" class="icon-plus-square text-green" title="添加关联"></a><?php endif; endif; ?>
						</td>
						<td align="center">
							<span><?php echo ($product_group_vo["0"]["price"]); ?></span> 
						</td>
						<?php if(!empty($product_group_vo["0"]["code_id"])): ?><td align="center">
								<?php echo ($product_group_vo["0"]["code_info"]["name"]); ?>
							</td>
							<td align="center"><?php echo ($product_group_vo["0"]["code_info"]["color_info"]["value"]); ?></td>
							<td align="center"><?php echo ($product_group_vo["0"]["code_info"]["size_info"]["value"]); ?></td>
							<td align="center">
								<?php echo ($product_group_vo["0"]["code_info"]["factory_info"]["name"]); ?>&nbsp;
								<span style="font-weight:bold;font-size:12px;"><?php echo check_local_stock($product_group_vo[0]['code_id'], $product_group_vo[0]['number']);?></span>
							</td>
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
							<td align="center"><span style="color:red;">—</span></td>
							<td align="center"><span style="color:red;">—</span></td><?php endif; ?>
						<td align="center"><?php echo ($product_group_vo["0"]["number"]); ?></td>
						<td align="center">
							<?php if(!empty($product_group_vo["0"]["status"])): echo ($product_group_vo["0"]["status"]); ?>
							<?php else: ?>
								normal<?php endif; ?>
							<?php if(in_array(($order_status), explode(',',"all,accept,shipping"))): if( ( $product_group_vo[0]['status'] == 'new' ) AND ( $order_status != 'audit' )): ?><script>
										$("#audit_button_<?php echo ($vo["id"]); ?>").css("display","inline-block");
									</script><?php endif; endif; ?>
							<?php if(($product_group_vo["0"]["status"]) == "cancel"): ?><script>mark_row(<?php echo ($vo["id"]); ?>,'有取消产品')</script>
								<?php $flag_for_cancel = 'true'; endif; ?>
						</td>
					</tr><?php endif; endforeach; endif; else: echo "" ;endif; ?>
		</table>
	</div>
	<div class="panel">
		<div class="panel-head">
			<strong>货运详情：</strong>
			<a href="<?php echo U('shipping_edit_plat?shipping_id='.$vo['shipping_info']['id'].'&anchor='.$vo['id'] , $param );?>" class="icon-pencil text-red" title="修改地址"></a>
		</div>
		<style>.my_ul{list-style:none;} .my_ul li{display:inline-block;margin-left:15px;}</style>
		<ul class="my_ul">
		  	<?php if(!empty($vo['shipping_info']['name'])): ?><li><strong>收件人:</strong><?php echo ($vo['shipping_info']['name']); ?></li><?php endif; ?>
		  	<li>
	  			<strong>地址:</strong>
	  			<span id="address_text_<?php echo ($vo['shipping_info']['id']); ?>"><?php echo ($vo['shipping_info']['address1']); ?>   <?php echo ($vo['shipping_info']['address2']); ?>  <?php echo ($vo['shipping_info']['address3']); ?></span>
	  			<span id="address_input_<?php echo ($vo['shipping_info']['id']); ?>" style="display:none;">
	  			<input id="address1_<?php echo ($vo['shipping_info']['id']); ?>" type="text" value="<?php echo ($vo['shipping_info']['address1']); ?>"/>
	  			<input id="address2_<?php echo ($vo['shipping_info']['id']); ?>" type="text" value="<?php echo ($vo['shipping_info']['address2']); ?>"/>
	  			<input id="address3_<?php echo ($vo['shipping_info']['id']); ?>" type="text" value="<?php echo ($vo['shipping_info']['address3']); ?>"/>
	  			</span>
<!-- 			<a id="edit_address_<?php echo ($vo['shipping_info']['id']); ?>" onclick="edit_address(<?php echo ($vo['shipping_info']['id']); ?>)" class="icon-edit text-blue" href="#"></a>
	  			<a id="submit_address_<?php echo ($vo['shipping_info']['id']); ?>" onclick="submit_address(<?php echo ($vo['shipping_info']['id']); ?>)" class="icon-check text-red" style="display:none;" href="#"></a> -->	  
	  		</li>
		  	<?php if(!empty($vo['shipping_info']['city'])): ?><li><strong>城市:</strong><?php echo ($vo['shipping_info']['city']); ?></li><?php endif; ?>
		  	<?php if(!empty($vo['shipping_info']['state'])): ?><li><strong>州/省:</strong><?php echo ($vo['shipping_info']['state']); ?></li><?php endif; ?>
		  	<?php if(!empty($vo['shipping_info']['post'])): ?><li><strong>邮编:</strong><?php echo ($vo['shipping_info']['post']); ?></li><?php endif; ?>
		  	<?php if(!empty($vo['shipping_info']['country'])): ?><li><strong>国家:</strong><?php echo ($vo['shipping_info']['country']); ?></li><?php endif; ?>
		  	<?php if(!empty($vo['shipping_info']['telephone'])): ?><li><strong>电话:</strong><?php echo ($vo['shipping_info']['telephone']); ?></li><?php endif; ?>
		  	<?php if(!empty($vo['shipping_info']['shipping_style'])): ?><li><strong>方式:</strong><?php echo ($vo['shipping_info']['shipping_style']); ?></li><?php endif; ?>
		  	<?php if(!empty($vo['shipping_info']['shipping_number'])): ?><li><strong>运单号:</strong><?php echo ($vo['shipping_info']['shipping_number']); ?></li><?php endif; ?>
		  	<?php if(!empty($vo['shipping_info']['shipping_weight'])): ?><li><strong>重量:</strong><?php echo ($vo['shipping_info']['shipping_weight']); ?></li><?php endif; ?>
		  	<?php if(!empty($vo['shipping_info']['shipping_price'])): ?><li><strong>运费:</strong><?php echo ($vo['shipping_info']['shipping_price']); ?></li><?php endif; ?>
		  	<?php if(!empty($vo['shipping_info']['shipping_tax'])): ?><li><strong>税费:</strong><?php echo ($vo['shipping_info']['shipping_tax']); ?></li><?php endif; ?>
		  	<?php if(!empty($vo['shipping_info']['shipping_date'])): ?><li><strong>日期:</strong><?php echo ($vo['shipping_info']['shipping_date']); ?></li><?php endif; ?>
		  	<?php if(!empty($vo['shipping_info']['shipping_operator'])): ?><li><strong>操作人:</strong><?php echo ($vo['shipping_info']['shipping_operator']); ?></li><?php endif; ?>
		  	<?php if(!empty($vo['shipping_info']['shipping_hs'])): ?><li><strong>shipping_hs:</strong><?php echo ($vo['shipping_info']['shipping_hs']); ?></li><?php endif; ?>
		  	<?php if(!empty($vo['shipping_info']['shipping_sample'])): ?><li><strong>shipping_sample:</strong><?php echo ($vo['shipping_info']['shipping_sample']); ?></li><?php endif; ?>
		  	<?php if(!empty($vo['shipping_info']['shipping_report_price'])): ?><li><strong>shipping_report_price:</strong><?php echo ($vo['shipping_info']['shipping_report_price']); ?></li><?php endif; ?>
		</ul>
	</div>
	</td>
	<td colspan="2" style="border:1px solid gray;border-left:1px dashed #999;">
		<div class="panel">
			<div class="panel-head">
				<strong>其它操作：</strong>
			</div>
			<style>.list-group li{padding:5px 5px 5px 15px;}</style>
			<ul class="list-group">
               	<li onclick="order_status_update(<?php echo ($vo["id"]); ?>,'plat','fetch')" style="cursor:pointer"><span class="icon-exchange text-yellow"></span>&nbsp;&nbsp;修改订单状态</li>
               	<li><a href="/test/erptest/index.php/Admin/ServiceManage/refund_add/id/<?php echo ($vo["id"]); ?>/data/1.html"><span class="icon-usd text-red"></span>&nbsp;&nbsp;&nbsp;&nbsp;申请退款</a></li>
                <li><a href="<?php echo U('/Admin/OrderManage/cancel_order',array('order_number'=>$vo[order_number],'come'=>'plat'));?>"><span class="icon-usd text-red"></span>&nbsp;&nbsp;&nbsp;&nbsp;取消订单</a></li>
               	<li onclick="remark_add(<?php echo ($vo["id"]); ?>,'plat')" style=" cursor:pointer"><span class="icon-comment text-green" title="添加备注"></span>&nbsp;&nbsp;添加备注</li>
<!--           	<?php if(($order_status) == "audit"): ?>
               	<li>
               		<a href="javascript:void(0)" onclick="layer.confirm('确定审核通过该订单？', {btn:['确定','取消'], title:false, closeBtn:0}, 
               			function(){location.href='<?php echo U('order_plat', array_merge($param,array('order_id'=>$vo['id']) ) );?>'});">
               		<span class="icon-check-square-o text-red"></span>&nbsp;&nbsp;审核通过</a>
               	</li><?php endif; ?> -->
               	<?php if($vo['status_info']['status'] == 'shipping' || $vo['status_info']['status']=='history'): ?>
               	<li><a href="#" onclick="delivery_detail_add(<?php echo ($vo["id"]); ?>,'plat','fetch')" ><span class="icon-plus text-red"></span>&nbsp;&nbsp;手动添加运单</a></li><?php endif; ?>
               	<li><a href="#" onclick="see_more(<?php echo ($vo["id"]); ?>)"><span class="icon-chevron-up text-black"></span>&nbsp;&nbsp;收起</a></li>
               </ul>
		</div>
	</td>
	</tr><?php endforeach; endif; else: echo "" ;endif; ?>
   </table>
</form>
<?php echo ($page_bar); ?>
<input type="hidden" id="current_correct_url" value="<?php echo U('order_plat', $param, null, true);?>"/>

<!--添加备注start-->
<div class="order_box" style="width:100%; background:#333;height:100%; position: fixed; top:0; left:0px; opacity:0.6; display: none;"></div>
<div id="remark_smile" style="display:none; position:fixed; top:20%; left:25%; background:#fff; z-index:100; opacity:1;">
<span class="icon-times" onclick="remark_over()" title="关闭" style=" float: right; font-size:15px; margin:5px 10px; cursor:pointer;"></span>
<form action="#" method="post" name="form" id="myform">
	<input type="hidden" name="id" class="click_bz_id">
	<input type="hidden" name="style" value="" class="click_bz_style">
    <table width="98%" border="0" cellpadding="4" cellspacing="1" class="table" id="table">
    <tr class="tr rt">
        <td width="100">备注内容：</td>
        <td colspan="3" class="lt">
            <textarea name="content" id="remark_message" style="width:320px;height:300px;visibility:hidden;"></textarea>
        </td>
    </tr>
    <tr class="tr rt">
        <td width="100">指定人员：</td>
        <td colspan="2" class="lt" style=" width: 20%;">
            <select name="accept" id="remark_accept" onchange="select_accept(this.options[this.options.selectedIndex].value)">
            	<option value="" >--请选择人员--</option>
            	<?php if(is_array($user_list)): $i = 0; $__LIST__ = $user_list;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><option value="<?php echo ($vo["username"]); ?>" ><?php echo ($vo["name"]); ?></option><?php endforeach; endif; else: echo "" ;endif; ?>
            </select>
        </td>
        <td align="left" style=" width: 67%;">
        	<ul class="accept_ul">
            </ul>    
        </td>
    </tr>
    <tr class="tr rt">
            <td width="100">备注预警：</td>
            <td colspan="3" class="lt">
            <p>
              <label>
                <input type="radio" name="warning" value="3" id="warning" checked=""/>
                正常
              </label>
              
              <label>
                <input type="radio" name="warning" value="2" id="warning" />
                加急
              </label>
              
              <label>
                <input type="radio" name="warning" value="1" id="warning" />
                立刻
              </label>

            </p>
                
            </td>
    </tr>
    <tr class="tr lt">
        <td colspan="4">
            <input class="button border-main" type="submit" onclick="return remark_form_submit()" name="dosubmit" value="添 加">
        </td>
    </tr>
</table>
</form>
</div>
<!--添加备注end-->

</div>

</body>
</html>