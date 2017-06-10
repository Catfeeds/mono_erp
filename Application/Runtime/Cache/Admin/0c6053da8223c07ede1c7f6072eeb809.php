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
var _ACTION_  = '/erptest/index.php/Admin/OrderManage/shipping_edit_web';
var _PUBLIC_ = '/erptest/Public';
var _TIMESTAMP_ = <?php echo time();?>;
var _SELF_ = '/erptest/index.php/Admin/OrderManage/shipping_edit_web/shipping_id/2032/anchor/2045/order_status/all/come_from_id/4.html';
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
<style>
table{border-collapse:separate;border-spacing:10px;}
</style>
<div class="admin">

<h1>修改地址</h1>
<form method="post" action="" class="form-x margin-large-top">
	<table>
		<tr>
			<td style="width:120px;">名　字:</td>
			<td style="width:200px;">
				<input name="first_name" value="<?php echo ($row["first_name"]); ?>" style="width:200px;"/>
			</td>
		</tr>
		<tr>
			<td style="width:120px;">姓　氏 :</td>
			<td style="width:200px;">
				<input name="last_name" value="<?php echo ($row["last_name"]); ?>" style="width:200px;"/>
			</td>
		</tr>
		<tr>
			<td style="width:120px;">国　家:</td>
			<td style="width:200px;">
				<input name="country" value="<?php echo ($row["country"]); ?>" style="width:200px;"/>
			</td>
		</tr>
		<tr>
			<td style="width:120px;">省　　:</td>
			<td style="width:200px;">
				<input name="province" value="<?php echo ($row["province"]); ?>" style="width:200px;"/>
			</td>
		</tr>
		<tr>
			<td style="width:120px;">城　市:</td>
			<td style="width:200px;">
				<input name="city" value="<?php echo ($row["city"]); ?>" style="width:200px;"/>
			</td>
		</tr>
		<tr>
			<td style="width:120px;">地　址:</td>
			<td style="width:200px;">
				<input name="address" value="<?php echo ($row["address"]); ?>" style="width:200px;"/>
			</td>
		</tr>
		<tr>
			<td style="width:120px;">邮　编:</td>
			<td style="width:200px;">
				<input name="code" value="<?php echo ($row["code"]); ?>" style="width:200px;"/>
			</td>
		</tr>
		<tr>
			<td style="width:120px;">电　话:</td>
			<td style="width:200px;">
				<input name="telephone" value="<?php echo ($row["telephone"]); ?>" style="width:200px;"/>
			</td>
		</tr>
		<tr>
			<td colspan="2">
				<button onclick="history.go(-1);" style="margin-right:10px;">返回</button>
				<input type="submit" value="提交"/>
			</td>
		</tr>
	</table>
</form>

</div>

</body>
</html>