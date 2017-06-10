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
var _CONTROLLER_ = '/erptest/index.php/Admin/ProductReturn';
var _ACTION_  = '/erptest/index.php/Admin/ProductReturn/product_return_order';
var _PUBLIC_ = '/erptest/Public';
var _TIMESTAMP_ = <?php echo time();?>;
var _SELF_ = '/erptest/index.php/Admin/ProductReturn/product_return_order/order_id/2250/come/web/order_status/all';
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
<script>
function unfold_return(){
	var return_obj=document.getElementById("return_operator");
	var icon_obj=document.getElementById("icon_return");
	if(return_obj.style.display=='none'){
		return_obj.style.display='';
		icon_obj.className='icon-minus';
	}else if(return_obj.style.display==''){
		return_obj.style.display='none';
		icon_obj.className='icon-plus';
	}
}

function unfold_add(){
	var return_obj=document.getElementById("add_operator");
	var icon_obj=document.getElementById("icon_add");
	if(return_obj.style.display=='none'){
		return_obj.style.display='';
		icon_obj.className='icon-minus';
	}else if(return_obj.style.display==''){
		return_obj.style.display='none';
		icon_obj.className='icon-plus';
	}
}
function admin_select_catalog(catalog){
	$.ajax({
		type: "POST",
		url:  _APP_+"/Admin/ProductReturn/admin_select_catalog",
		data: {'catalog': catalog},
		dataType: 'html',
		success : function(result){
			$("#products_td").html(result);
		}
	})
}

function admin_select_products(products){
	var is_sku=document.getElementById("is_sku").value;
	if(!is_sku){
		$.ajax({
			type: "POST",
			url:  _APP_+"/Admin/ProductReturn/admin_select_products",
			data: {'products': products},
			dataType: 'html',
			success : function(result){
				$("#code_tr").show();
				$("#code_td").html(result);
			}
		})
	}else{
		$("#code_tr").hide();
	}
}

function add_new_order_product(catalog)
{
	$.ajax({
		type: "POST",
		url:  _APP_+"/Admin/ProductReturn/add_product_new_order",
		data: {'catalog': catalog,'style': 'product'},
		dataType: 'html',
		success : function(result){
			document.getElementById("new_order_products").innerHTML=result;
		}
	})
}

function add_new_order_code(product)
{
	$.ajax({
		type: "POST",
		url:  _APP_+"/Admin/ProductReturn/add_product_new_order",
		data: {'product': product,'style': 'code'},
		dataType: 'html',
		success : function(result){
			document.getElementById("new_order_code").innerHTML=result;
		}
	})
}
</script>

<style>
.one_line {
    float: left;
    font-size: 12px;
    margin: 10px 0 10px 10px;
    width: 100%;
}
.lable_text {
    color: #666666;
    float: left;
    font-size: 18px;
    padding: 0 20px 10px 5px;
    width: 100px;
}
</style>
<div class="admin">
<?php if($come == 'plat'): ?><a href="<?php echo U('/Admin/OrderManage/order_details',array('order_id'=>$order_product[id],'order_status'=>$order_status));?>"><button class="button border-black padding-small">返回</button></a>
<?php elseif($come == 'web'): ?>
<a href="<?php echo U('/Admin/OrderManage/order_details_web',array('order_id'=>$order_product[id],'order_status'=>$order_status));?>"><button class="button border-black padding-small">返回</button></a><?php endif; ?>
<table class="table table-bordered table-striped table-hover table-condensed" width="98%" border="0" cellpadding="5" cellspacing="1" class="table" style="margin-top:10px;">
    <?php if($_GET['come']== 'plat'): ?><tr>
    	<td>订单号</td><td>姓名</td><td>邮箱</td><td>价格</td><td>币种</td><td>来源</td><td>时间</td>
    </tr>
    <tr>
 	<td><?php echo ($order_product["order_number"]); ?></td><td><?php echo ($order_product["name"]); ?></td><td><?php echo ($order_product["email"]); ?></td><td><?php echo ($order_product["price"]); ?></td><td><?php echo ($order_product["currency"]); ?></td><td><?php echo (get_come_from_name($order_product["come_from_id"])); ?></td><td><?php echo ($order_product["date_time"]); ?></td>
    </tr>
    <?php elseif($_GET['come']== 'web'): ?>
    <tr>
    	<td>订单号</td><td>姓名</td><td>邮箱</td><td>价格(折后)</td><td>来源</td><td>时间</td>
    </tr>
    <tr>
 	<td><?php echo ($order_product["order_number"]); ?></td><td><?php echo ($order_product["first_name"]); echo ($order_product["last_name"]); ?></td><td><?php echo ($order_product["email"]); ?></td><td><?php echo ($order_product["total_price_discount"]); ?></td><td><?php echo (get_come_from_name($order_product["come_from_id"])); ?></td><td><?php echo ($order_product["date_time"]); ?></td>
    </tr><?php endif; ?>
</table>


<table class="table table-bordered table-striped table-hover table-condensed" width="98%" border="0" cellpadding="5" cellspacing="1" class="table" style="margin-top:10px;">
<tr class="tr rt">
<td colspan="5">
<div class="tab">
<div class="tab-head">
    <ul class="tab-nav">
        <li class="active"><a href="#tab-set">退货</a></li>
		<?php if($order_status_DB != 'history'): ?><li><a href="#tab-cancel">取消</a></li><?php endif; ?>
		<li><a href="#tab-email">新增</a></li>
		<li><a href="#tab-email-order">新增为新订单<span style="color:red;font-size:10px;">(在订单已发出时使用)</span></a></li>
    </ul>
</div>
<div class="tab-body">

<form action="<?php echo U('/Admin/ProductReturn/product_return_act',array('come'=>$_GET[come],'act'=>'return'));?>" method="post" id="myform">
<div class="tab-panel active" id="tab-set">
	<table class="table table-bordered table-striped table-hover table-condensed" width="98%" border="0" cellpadding="5" cellspacing="1" style="margin-top:10px;">
		<?php if(!empty($return_order_product_list)): ?><tr>
			<td colspan="5"><strong>具体产品</strong></td>
		</tr>
		<tr>
			<td>勾选</td><td>产品名</td><td>价格</td><td>数量</td><td>状态</td>
		</tr>
		<?php if(is_array($return_order_product_list)): $i = 0; $__LIST__ = $return_order_product_list;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><tr>
			<td>
			<input type="checkbox" name="product_id[]" value="<?php echo ($vo["id"]); ?>"></td>
			<td><?php echo ($vo["name"]); ?></td><td><?php echo ($vo["price"]); ?></td><td><?php echo ($vo["number"]); ?></td><td><?php echo $status[$vo[status]];?></td>
		</tr><?php endforeach; endif; else: echo "" ;endif; endif; ?>
		<!--<?php if(!empty($order_customization)): ?><tr>
			<td colspan="5"><strong>定制产品</strong></td>
		</tr>
		<tr>
			<td>勾选</td><td>定制名称</td><td>定制描述</td><td>定制价格</td><td>定制链接</td><td>状态</td>
		</tr>
		<?php if(is_array($order_customization)): $i = 0; $__LIST__ = $order_customization;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><tr>
			<td>
			<input type="checkbox" name="product_id[]" value="<?php echo ($vo["id"]); ?>"></td>
			<td><?php echo ($vo["name"]); ?></td><td><?php echo ($vo["description"]); ?></td><td><?php echo ($vo["price"]); ?></td><td><?php echo ($vo["href"]); ?></td><td><?php echo $status[$vo[status]];?></td>
		</tr><?php endforeach; endif; else: echo "" ;endif; endif; ?>
		 -->
		<tr>
			<td colspan="5">
			<div class="label"><label>退货地:</label></div>
			<div class="field">
			<select name="place" >
			<option value="空">空</option>
			<option value="南京公司">南京公司</option>
			<option value="美国仓库">美国仓库</option>
			</select>
			</div>
			</td>
		</tr>
		<tr>
			<td colspan="5">
			<div class="label">
			<label>退货原因:</label>
			</div>
			<div class="field">
			<textarea name="message" check='empty' msg="请填写退货原因!">
			</textarea>
			</div>
			</td>
		</tr>
		<tr>
			<td colspan="5">
			<div class="label">
			<label>操作人:</label>
			</div>
			<div class="field">
			<input type="text" name="operator" readonly="readonly" value="<?php echo ($username); ?>" style="background:white;">
			</div>
			</td>
		</tr>
		<tr>
			<td colspan="5">
			<input type="hidden" value="<?php echo ($order_product["id"]); ?>" name="order_id">
			<input type="hidden" value="<?php echo ($order_product["come_from"]); ?>" name="come_from">
			<button class="button border-black padding-small" type="submit" onclick="return check_form('myform');">退货</button>
			</td>
		</tr>
	</table>
</div>
</form>

<form action="<?php echo U('/Admin/ProductReturn/product_return_act',array('come'=>$_GET[come],'act'=>'add'));?>" method="post">
<div class="tab-panel" id="tab-email">
<table class="table table-bordered table-striped table-hover table-condensed" width="98%" border="0" cellpadding="5" cellspacing="1" style="margin-top:10px;">
	<tr>
		<td>
		<div class="label">
		<label>分类:</label>
		</div>
		<div class="field">
		<select name="catalog" id="catalog" onchange="admin_select_catalog(this.value)" class="input">
		<option>请选择分类</option>
		<?php if(is_array($catalog_list)): $i = 0; $__LIST__ = $catalog_list;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><option value="<?php echo ($vo["id"]); ?>"><?php echo ($vo["name"]); ?></option><?php endforeach; endif; else: echo "" ;endif; ?>
		</select>
		</div>
		</td>
	</tr>
	<tr>
		<td>
		<div class="label">
		<label>产品:</label>
		</div>
		<div class="field" id="products_td">
		<select name="product_id" id="products" onchange="admin_select_products(this.value)" class="input"></select>
		</div>
		</td>
	</tr>
	<tr>
		<td>
		<div class="label">
		<label>CODE:</label>
		</div>
		<div class="field" id="code_td">
		<select name="code" id="code" class="input"></select>
		</div>
		</td>
	</tr>
	<tr>
		<td>
		<div class="label">
		<label>数量:</label>
		</div>
		<div class="field">
		<input type="text" name="number" class="input">
		</div>
		</td>
	</tr>
    <tr>
		<td>
		<div class="label">
		<label>价格:</label>
		</div>
		<div class="field">
		<input type="text" name="new_order_price" class="input" value="">
		</div>
		</td>
	</tr>
    <tr>
		<td>
		<div class="label">
		<label>产品描述（名称，颜色，尺码）<span style="color:red;font-size:12px;">[需写对应国家语种文字，打印商品详情单后发给顾客]</span>:</label>
		</div>
		<div class="field">
		<input type="text" name="product_description" class="input" value="">
		</div>
		</td>
	</tr>
	<tr>
		<td>
		<input type="hidden" value="<?php echo ($order_product["id"]); ?>" name="order_id">
		<button class="button border-black padding-small" type="submit">新增</button>
		</td>
	</tr>
</table>
</div>
</form>

<form action="<?php echo U('/Admin/ProductReturn/product_return_act',array('come'=>$_GET[come],'act'=>'cancel'));?>" method="post" id="myform">
<div class="tab-panel" id="tab-cancel">
<table class="table table-bordered table-striped table-hover table-condensed" width="98%" border="0" cellpadding="5" cellspacing="1" style="margin-top:10px;">
	<?php if(!empty($return_order_product_list)): ?><tr>
		<td colspan="5"><strong>具体产品</strong></td>
	</tr>
	<tr>
		<td>勾选</td><td>产品名</td><td>价格</td><td>数量</td><td>状态</td>
	</tr>
	<?php if(is_array($return_order_product_list)): $i = 0; $__LIST__ = $return_order_product_list;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><tr>
		<td>
		<input type="checkbox" name="product_id[]" value="<?php echo ($vo["id"]); ?>">
		</td>
		<td><?php echo ($vo["name"]); ?></td><td><?php echo ($vo["price"]); ?></td><td><?php echo ($vo["number"]); ?></td><td><?php echo $status[$vo[status]];?></td>
	</tr><?php endforeach; endif; else: echo "" ;endif; endif; ?>
	<?php if(!empty($order_customization)): ?><tr>
		<td colspan="5"><strong>定制产品</strong></td>
	</tr>
	<tr>
		<td>勾选</td><td>定制名称</td><td>定制描述</td><td>定制价格</td><td>定制链接</td><td>状态</td>
	</tr>
	<?php if(is_array($order_customization)): $i = 0; $__LIST__ = $order_customization;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><tr>
		<td>
		<input type="checkbox" name="product_customization_id[]" value="<?php echo ($vo["id"]); ?>">
		</td>
		<td><?php echo ($vo["name"]); ?></td><td><?php echo ($vo["description"]); ?></td><td><?php echo ($vo["price"]); ?></td><td><?php echo ($vo["href"]); ?></td><td><?php echo $status[$vo[status]];?></td>
	</tr><?php endforeach; endif; else: echo "" ;endif; endif; ?>
	<tr>
	<td colspan="5">
	<input type="hidden" value="<?php echo ($order_product["id"]); ?>" name="order_id">
	<button class="button border-black padding-small" type="submit">取消</button>
	</td>
	</tr>
</table>
</div>
</form>

<form action="<?php echo U('/Admin/ProductReturn/product_return_act',array('come'=>$_GET[come],'act'=>'cancel'));?>" method="post" id="myform">
<div class="tab-panel" id="tab-cancel">
<table class="table table-bordered table-striped table-hover table-condensed" width="98%" border="0" cellpadding="5" cellspacing="1" style="margin-top:10px;">
	<?php if(!empty($return_order_product_list)): ?><tr>
		<td colspan="5"><strong>具体产品</strong></td>
	</tr>
	<tr>
		<td>勾选</td><td>产品名</td><td>价格</td><td>数量</td><td>状态</td>
	</tr>
	<?php if(is_array($return_order_product_list)): $i = 0; $__LIST__ = $return_order_product_list;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><tr>
		<td>
		<input type="checkbox" name="product_id[]" value="<?php echo ($vo["id"]); ?>">
		</td>
		<td><?php echo ($vo["name"]); ?></td><td><?php echo ($vo["price"]); ?></td><td><?php echo ($vo["number"]); ?></td><td><?php echo $status[$vo[status]];?></td>
	</tr><?php endforeach; endif; else: echo "" ;endif; endif; ?>
	<?php if(!empty($order_customization)): ?><tr>
		<td colspan="5"><strong>定制产品</strong></td>
	</tr>
	<tr>
		<td>勾选</td><td>定制名称</td><td>定制描述</td><td>定制价格</td><td>定制链接</td><td>状态</td>
	</tr>
	<?php if(is_array($order_customization)): $i = 0; $__LIST__ = $order_customization;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><tr>
		<td>
		<input type="checkbox" name="product_customization_id[]" value="<?php echo ($vo["id"]); ?>">
		</td>
		<td><?php echo ($vo["name"]); ?></td><td><?php echo ($vo["description"]); ?></td><td><?php echo ($vo["price"]); ?></td><td><?php echo ($vo["href"]); ?></td><td><?php echo $status[$vo[status]];?></td>
	</tr><?php endforeach; endif; else: echo "" ;endif; endif; ?>
	<tr>
	<td colspan="5">
	<input type="hidden" value="<?php echo ($order_product["id"]); ?>" name="order_id">
	<button class="button border-black padding-small" type="submit">取消</button>
	</td>
	</tr>
</table>
</div>
</form>

<form action="<?php echo U('/Admin/ProductReturn/add_product_new_order',array('come'=>$_GET[come],'act'=>'add_new_order'));?>" method="post">
<div class="tab-panel" id="tab-email-order">
<table class="table table-bordered table-striped table-hover table-condensed" width="98%" border="0" cellpadding="5" cellspacing="1" style="margin-top:10px;">
	<tr>
		<td>
		<div class="label">
		<label>分类:</label>
		</div>
		<div class="field">
		<select name="new_order_catalog" id="catalog" onchange="add_new_order_product(this.value)" class="input">
		<option>请选择分类</option>
		<?php if(is_array($catalog_list)): $i = 0; $__LIST__ = $catalog_list;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><option value="<?php echo ($vo["id"]); ?>"><?php echo ($vo["name"]); ?></option><?php endforeach; endif; else: echo "" ;endif; ?>
		</select>
		</div>
		</td>
	</tr>
	<tr>
		<td>
		<div class="label">
		<label>产品:</label>
		</div>
		<div class="field">
		<select name="new_order_product" id="new_order_products" onchange="add_new_order_code(this.value)" class="input"></select>
		</div>
		</td>
	</tr>
	<tr>
		<td>
		<div class="label">
		<label>CODE:</label>
		</div>
		<div class="field">
		<select name="new_order_code" id="new_order_code" class="input"></select>
		</div>
		</td>
	</tr>
	<tr>
		<td>
		<div class="label">
		<label>数量:</label>
		</div>
		<div class="field">
		<input type="text" name="new_order_number" class="input">
		</div>
		</td>
	</tr>
	<tr>
		<td>
		<div class="label">
		<label>价格:</label>
		</div>
		<div class="field">
		<input type="text" name="new_order_price" class="input" value="">
		</div>
		</td>
	</tr>
    <tr>
		<td>
		<div class="label">
		<label>产品描述（名称，颜色，尺码）<span style="color:red;font-size:12px;">[需写对应国家语种文字，打印商品详情单后发给顾客]</span>:</label>
		</div>
		<div class="field">
		<input type="text" name="product_description" class="input" value="">
		</div>
		</td>
	</tr>
	<tr>
		<td>
		<input type="hidden" value="<?php echo ($order_product["id"]); ?>" name="new_order_order_id">
		<button class="button border-black padding-small" type="submit">新增</button>
		</td>
	</tr>
</table>
</div>
</form>

</div>
</div>
</td>
</tr>
</table>
</div>

</body>
</html>