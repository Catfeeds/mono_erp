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
var _ACTION_  = '/test/erptest/index.php/Admin/OrderManage/import';
var _PUBLIC_ = '/test/erptest/Public';
var _TIMESTAMP_ = <?php echo time();?>;
var _SELF_ = '/test/erptest/index.php/Admin/OrderManage/import.html';
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
<script>
function handleFile(){
	var full_name = $("#file").attr("value");
	var arr = full_name.split('\\');
	var file_name = arr[arr.length-1];
	$("#file_name").html(file_name);
}
</script>
<style>
.form-x .form-group .label{width:80px;text-align: left}
.form-x .form-button {margin:0}
</style>
<div class="admin">
<h1>手动导入订单</h1>
<a style="text-decoration: underline" href="http://115.28.241.1/erp/Public/general.xls">通用模板下载</a>
<form method="post" action="/test/erptest/index.php/Admin/OrderManage/import_handle.html" class="form-x margin-large-top" enctype="multipart/form-data">
	<div class="form-group">
		<div class="label">
			<label>上传文件：</label>
		</div>
		<div class="field">
			<a class="button input-file">
				+ 浏览文件
				<input size="100" type="file" name="file" id="file" onchange="handleFile()">
				<span id="file_name" style="margin-left:10px;">&nbsp;&nbsp;</span>
			</a>
		</div>
	</div>
	<div class="form-group">
		<div class="label">
			<label class="label">选择平台：</label>
		</div>
		<div class="field">
			<select class="input" name="flatform">
				<option value="-1">--请选择--</option>
				<?php if(is_array($flatform_list)): $i = 0; $__LIST__ = $flatform_list;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$flatform_vo): $mod = ($i % 2 );++$i;?><option value="<?php echo ($flatform_vo); ?>"><?php echo ($flatform_vo); ?></option><?php endforeach; endif; else: echo "" ;endif; ?>
			</select>
		</div>
	</div>
	<div class="form-button">
		<button class="button border-black padding-small" type="submit">导入</button>
	</div>
</form>

</div>

</body>
</html>