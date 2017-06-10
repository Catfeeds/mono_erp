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
var _CONTROLLER_ = '/erptest/index.php/Admin/CodeManage';
var _ACTION_  = '/erptest/index.php/Admin/CodeManage/code_list';
var _PUBLIC_ = '/erptest/Public';
var _TIMESTAMP_ = <?php echo time();?>;
var _SELF_ = '/erptest/index.php/Admin/CodeManage/code_list/pro_id/479/id/81204/type/edit/order_id/391';
var _APP_='/erptest/index.php';
var _MODULE_ = '/erptest/index.php/Admin';
var _ROOT_ = '/erptest';

</script>


<link type="image/x-icon" href="http://www.pintuer.com/favicon.ico" rel="shortcut icon" />
<link href="http://www.pintuer.com/favicon.ico" rel="bookmark icon" />
<body>

<script src="/erptest/Application/Admin/View/Default/js/CodeManage/main.js?v=<?php echo time();?>"></script>
<style>
.code_ul li{ list-style: none; float:left; width:180px; overflow:hidden;line-height:25px; font-size:12px; border-radius: 4px; background: #CCC; margin:2px 4px; padding:0px 4px;}
.code_ul li:hover{ background: #E6E6E6; cursor: pointer;}
#code_id .add{ position:relative;}
#code_id .add .add_span{position: absolute;right: 4px; top: 3px;}
.code_num{ width:20px; height:20px;}
#dis_no{ display: none;}
</style>
<div class="remark">
			<span class="remark_span">分类：</span>
                <select name="catalog" class="" onchange="get_product_of_catalog(this.value)">
                  <option value="">全部</option>
                  <?php if(is_array($catalog)): $i = 0; $__LIST__ = $catalog;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><option value='<?php echo ($vo["id"]); ?>' <?php if(($catalog_id) == $vo["id"]): ?>selected=""<?php endif; ?>><?php echo ($vo["name"]); ?></option><?php endforeach; endif; else: echo "" ;endif; ?>
                </select>
             <span class="remark_span">产品：</span>    
                <select  name="product_id" id="product_id" onchange="get_product_of_code(this.value)" style="max-width: 450px;">
                  <option value="">全部</option>
                  <?php if(is_array($product_list)): $i = 0; $__LIST__ = $product_list;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><option value='<?php echo ($vo["id"]); ?>' <?php if(($product_id) == $vo["id"]): ?>selected=""<?php endif; ?>><?php echo ($vo["name"]); ?></option><?php endforeach; endif; else: echo "" ;endif; ?>
                </select>

</div>
<form method="post" name="form" id="myform">
<input type="hidden" name="sku_id" value="<?php echo ($id); ?>">
<span class="remark_span" style=" margin-top:10px;font-size:14px; line-height:25px;color:#960;">产品名称 ：</span><?php echo ($pro_name); ?>
<br />
<span class="remark_span" style="font-size:12px; line-height:35px; width:20%;">sku ：<?php echo ($pro_sku); ?></span>
<p id="code_list" style=" margin:0px;">
<span class="remark_span" style="font-size:12px; line-height:35px; width:20%;">手动输入code：</span>
<input type="text" name="code_list"  style="border-radius:6px;">
</p>

<p id="dis_no" style=" margin:0px;">
<span class="remark_span" style="font-size:12px; line-height:35px; width:20%;">请输入sku名称：</span>
<input type="text" name="sku_name" id="sku_name" style="border-radius:6px;">
</p>

<div style="font-size:12px; margin-left:20px;line-height: 30px;">表单内不填写数字， 默认为一件</div>
	<ul class="code_ul" id='code_id'></ul> 
        <div style="clear:both;"></div>
        <div style="position:fixed; bottom:10px; right:20px;">
        		<input type="hidden" name="order_id" value="<?php echo ($order_id); ?>">
        		<input type="hidden" name="type" value="<?php echo ($type); ?>">
        		<input class="button border-main" type="button" name="dosubmit" onclick="code_list_sub()" value="提 交">
        </div>
</form>
<hr />
 <ul class="code_ul" id='code_ul'>
         <!--
		 <?php if(is_array($code_list)): $i = 0; $__LIST__ = $code_list;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><li onclick="code_add(this.innerHTML)" ><input type="text" name="code_num[<?php echo ($vo["id"]); ?>]" class="code_num"  id="aa"> <span title="<?php echo ($vo["code"]); ?>"><?php echo ($vo["code"]); ?></span>
		  <br>
		  <span title="<?php echo ($vo["code"]); ?>"><?php echo ($vo["name"]); ?></span>
		  </li><?php endforeach; endif; else: echo "" ;endif; ?>
		-->
    </ul> 
</body>
</html>