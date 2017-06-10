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
var _CONTROLLER_ = '/index.php/Admin/Index';
var _ACTION_  = '/index.php/Admin/Index/index';
var _PUBLIC_ = '/Public';
var _TIMESTAMP_ = <?php echo time();?>;
var _SELF_ = '/index.php/Admin.html';
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
<script>
var aa = 't';
function dis_no_pl()
{
	if(aa == 't')
	{
		$('.unread_messages').removeClass('dis_no');
		aa = 'f';
	}
	else
	{
		$('.unread_messages').addClass('dis_no');
		aa = 't';
	}
	
}
var pd = 1;
function ss()
{
	if(pd == 1)
	{
		$('.ss').css({'background':'#fff','color':"red"});
		pd=2;
	}
	else
	{
		$('.ss').css({'background':'#333','color':"#fff"});
		pd=1;
	}
	
}
if(<?php echo $unread_messages_coun?> > 0)
{
	setInterval(ss,1000);
}
</script>
<style>
.list{ margin:0; padding:0;}
.list_li{ line-height:25px; margin-left:20px;}
.menu{ margin:0;color:seagreen;}
.red{ color:red;} 
.blue{ color:#000;}
</style>
<div class="admin" >
	<div class="line-big">
    	<div class="xm3">
        	<div class="panel border-back">
            	<!--
                <div class="panel-body text-center">
                	<img src="/Application/Admin/View/Default/images/face.jpg" width="120" class="radius-circle" /><br />
                    admin
                </div>
                -->
                <div class="panel-foot bg-back border-back" style=" line-height:25px;">您好！ <span  style="font-size:19px; font-weight:bold; color:red;"><?php echo ($username); ?></span> ，这是您第<?php echo ($login_coun); ?>次登录，上次登录为 <?php echo ($login_time); ?>。</div>
            </div>
            <br />
        	<div class="panel">
            	<div class="panel-head"><strong>站点统计</strong></div>
                <ul class="list-group">
                	<li><span class="float-right badge bg-red">88</span><span class="icon-user"></span> 会员</li>
                    <li><span class="float-right badge bg-main">828</span><span class="icon-file"></span> 文件</li>
                    <li><span class="float-right badge bg-main">828</span><span class="icon-shopping-cart"></span> 订单</li>
                    <li><span class="float-right badge bg-main">828</span><span class="icon-file-text"></span> 内容</li>
                    <li><span class="float-right badge bg-main">828</span><span class="icon-database"></span> 数据库</li>
                </ul>
            </div>
            <br />
        </div>
        <div class="xm9">
        	<div class="alert alert-yellow"><span class="close"></span><strong>注意：</strong>您有 <span onclick="dis_no_pl()" class="button ss" style="font-size:19px; font-weight:bold; color:red; background:#333; text-decoration:blink;"><?php echo ($unread_messages_coun); ?></span> 条未处理信息，<a onclick="dis_no_pl()" class='cur'>点击查看</a>。</div>
            <div class="alert dis_no unread_messages"><!-- dis_no -->
            	<ul class="list">
            		<?php if($order_business_message_val): ?><p class="menu">留言管理</p>
						<?php if(is_array($order_business_message_val)): $i = 0; $__LIST__ = $order_business_message_val;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$business_vo): $mod = ($i % 2 );++$i;?><a href="/index.php/Admin/ServiceManage/remark_check/sta/0/accept/<?php echo ($username); ?>.html"><li class="list_li"><?php echo ($business_vo["message"]); ?></li></a><?php endforeach; endif; else: echo "" ;endif; endif; ?>
            	</ul>
            </div>
            <!--
            <div class="alert">
                <h4>拼图前端框架介绍</h4>
                <p class="text-gray padding-top">拼图是优秀的响应式前端CSS框架，国内前端框架先驱及领导者，自动适应手机、平板、电脑等设备，让前端开发像游戏般快乐、简单、灵活、便捷。</p>
            	<a target="_blank" class="button bg-dot icon-code" href="pintuer2.zip"> 下载示例代码</a> 
            	<a target="_blank" class="button bg-main icon-download" href="http://www.pintuer.com/pintuer.zip"> 下载拼图框架</a> 
            	<a target="_blank" class="button border-main icon-file" href="http://www.pintuer.com/"> 拼图使用教程</a>
            </div>
            -->
            <div class="panel">
            	<div class="panel-head"><strong>系统信息</strong></div>
                <table class="table">
                	<tr><th colspan="1">服务器信息</th><th colspan="1">系统信息</th></tr>
                    <tr><td width="110" align="right">操作系统：</td><td>Linux</td></tr>
                    <tr><td align="right">Web服务器：</td><td>Apache</td></tr>
                    <tr><td align="right">程序语言：</td><td>PHP</td></tr>
                    <tr><td align="right">数据库：</td><td>MySQL</td></tr>
                </table>
            </div>
            
        </div>
    </div>       
   
</div>



</body>
</html>