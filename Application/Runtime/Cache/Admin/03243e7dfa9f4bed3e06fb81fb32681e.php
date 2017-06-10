<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>Lilysilk - ERP</title>
</head>

<!-- 加载js css -->
<link rel="stylesheet" href="/Application/Admin/View/Default/css/pintuer.css">
<link rel="stylesheet" href="/Application/Admin/View/Default/css/admin.css">
<script src="/Application/Admin/View/Default/js/jquery.min.js"></script>
<script src="/Application/Admin/View/Default/js/pintuer.js"></script>
<script src="/Application/Admin/View/Default/js/respond.js"></script>
<script src="/Application/Admin/View/Default/js/admin.js"></script>
<script src="/Application/Admin/View/Default/js/layer/layer.js"></script>
<script src="/Application/Admin/View/Default/Public/KindEditor/kindeditor-min.js"></script>
<script src="/Application/Admin/View/Default/Public/KindEditor/lang/zh_CN.js"></script>
<script src="/Application/Admin/View/Default/js/function.js?v=<?php echo time();?>"></script><!-- 公共js函数文件 -->
<script>
	KindEditor.ready(function(K) {
		editor = K.create('textarea[name="content"]', {
			allowFileManager: true,
			afterBlur: function(){this.sync();},
		});
	});
</script>
<script language="javascript" type="text/javascript" src="/Application/Admin/View/Default/Public/wdatepicker/WdatePicker.js"></script>
<script>//定义需要的“常量”
var _CONTROLLER_ = '/index.php/Admin/OrderManage';
var _ACTION_  = '/index.php/Admin/OrderManage/index';
var _PUBLIC_ = '/Public';
var _TIMESTAMP_ = <?php echo time();?>;
var _SELF_ = '/index.php/Admin/OrderManage/index.html';
var _APP_='/index.php';
var _MODULE_ = '/index.php/Admin';
var _ROOT_ = '';

</script>


<link type="image/x-icon" href="http://www.pintuer.com/favicon.ico" rel="shortcut icon" />
<link href="http://www.pintuer.com/favicon.ico" rel="bookmark icon" />
<body>

<!-- 可以选择不加载菜单 -->
<?php if($no_menu != true): ?><div class="lefter">
    <div class="logo"><img src="/Application/Admin/View/Default/images/logo.png" style="height:40px;" alt="后台管理系统" /><b style="font-size: 12px">ERP</b></div>
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
            <span> <a href="/index.php/Admin/messages/Index.html" class="plcz" style=" margin-right:30px;">私信</a> <?php echo ($username); ?> &nbsp;&nbsp; 工号: <?php echo $monoid>0?$monoid:0;?> &nbsp;&nbsp;</span>
            
            
            
            <ul class="bread">
                <li><a href="index.html" class="icon-home"> 开始</a></li>
                <li>后台首页</li>
            </ul>
        </div>
    </div>
</div>
<span class="icon-dedent" style="position: fixed; top: 84px; left: 180px; z-index: 100;color: #09c;cursor: pointer;font-size: 20px;width: 10px;" onclick='show_hide_left_menu(this);'></span><?php endif; ?>

<div class="admin">

<!-- 
<script>
function fnc(){
chk.outerHTML=chk.outerHTML.replace(/ii/,'multiple')
}
</script>

<select ii onchange="fnc();" id="chk">
<option>111
<option>222
<option>333
<option>444
</select> -->


<!-- 
<script type="text/javascript"> 
$(function(){ 
    $("#a").click(function(){ 
        alert('aaa'); 
    }); 
    
    
    
}); 
</script> 

<div id="a" style="width:300px; height:200px; background-color:#999;">a</div> 
<div id="b" style="width:300px; height:200px; background-color:#999; margin:30px 30px;">b</div> 

<select name="hhh" id="hhh" >
	<option value="1">111</option>
	<option value="2">222</option>
	<option value="3">333</option>
	<option value="4">444</option>
	<option value="5">555</option>
</select>

<script type="text/javascript">
$(document).ready(function(){
  
	
  var e = jQuery.Event("keydown");
  e.keyCode=40,e.altKey=true;
  
  $("#hhh").focus();
  $(window).trigger(e);
  
 
});
</script> -->

</div>

</body>
</html>