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
var _CONTROLLER_ = '/test/erptest/index.php/Admin/CustomerManage';
var _ACTION_  = '/test/erptest/index.php/Admin/CustomerManage/index';
var _PUBLIC_ = '/test/erptest/Public';
var _TIMESTAMP_ = <?php echo time();?>;
var _SELF_ = '/test/erptest/index.php/Admin/CustomerManage/index.html';
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
<style>
.customer_top{
	width:100%;
	float:left;
}
.navigation{
	width:100%;
	float:left;
	border-bottom: solid 5px #09c;
	border: solid 1px #ddd;
    border-radius: 4px;
}
.navigation li{
	float:left;
	list-style:none;
}
.navigation_select{
	float:left;
	width:100%;
	background-color: #f5f5f5;
    border-radius: 4px 4px 0 0;
    border-bottom: solid 1px #ddd;
}
.navigation_select ul li a{
	padding: 10px 20px;
    line-height: 20px;
    display: block;
}
.customer_details{
	width:100%;
	float:left;
	margin-top:10px;
}
.details{
	width:100%;
	float:left;
	border-left: solid 1px #ddd;
	border-right: solid 1px #ddd;
    border-radius: 4px;
	margin-top:10px;	
}
.order_list{
	width:100%;
	float:left;
}
.navigation_select a{
	cursor:pointer;
}
.prompt{
	text-align:center;
	font-size:16px;
	font-weight:bold;
	color:red;
}
</style>
<script>
function order_details(obj){
	$.ajax({
		type: "POST",
		url:  _CONTROLLER_+"/order_list_details",
		data: {'email':obj},
		dataType: 'json',
		success : function(result){
			$('#details').html(result);
		}
	})
}

function customer_details(style,obj){
	$.ajax({
		type: "POST",
		url:  _CONTROLLER_+"/customer_details",
		data: {'style':style,'email':obj},
		dataType: 'json',
		success : function(result){
			$('#details').html(result);
		}
	})
}

function add_integral(email){
	layer.open({
		  type: 1,//框类型
		  area: ['300px', '180px'],
		  title: "积分修改",//标题
		  closeBtn: 1,//关闭按钮
		  shadeClose: true,
		  skin: 'layui-layer-lan',
		  content: "<form action='<?php echo U('/Admin/CustomerManage/add_integral');?>' method='post'><div class='panel'><ul class='list-group'><li><span>积分数：</span><input type='text' name='total_integral' class='float-right'></li><li><span>积分来源：</span><input type='text' name='get_integral' class='float-right'><input type='hidden' name='email' value="+email+"></li><li><input type='submit' value='提交' class='button'></li></ul></div></form>",
		});
}

function show_integral(email){
	var used_list='';
	$.ajax({
		type: "POST",
		url:  _CONTROLLER_+"/show_integral",
		data: {'email':email},
		dataType: 'json',
		success : function(result){
			$.each(result,function(n,value){
				used_list="<table class='table  table-striped table-hover table-condensed'><tr class='list_head'><td width='20' style='text-align:left'>订单号</td><td width='20' style='text-align:left'>总价</td><td width='20' style='text-align:left'>总积分</td></tr><tr><td>"+value['order_id']+"</td><td>"+value['total_price']+"</td><td>"+value['total_integral']+"</td></tr></table>";
			})
			layer.open({
			type: 1,
			area: ['700px', '530px'],
			title: "使用列表",
			closeBtn: 1,
			shadeClose: true,
			skin: 'layui-layer-lan',
			content: used_list,
		})
		}
	})
}
</script>
<div class="admin">
<form action="<?php echo U('/Admin/CustomerManage/index');?>" method="post">
<input type="text" name="email">
<input type="submit" value="查 询">
</form>
<div class="customer_top">
	<div class="panel" style="width:22%;float:left;">
   	<div class="panel-head"><strong>基本资料</strong></div>
      <ul class="list-group">
         <li><span>Email:</span>&nbsp;&nbsp;&nbsp;<span><?php echo ($customer_one["email"]); ?></span></li>
         <li><span>First Name:</span>&nbsp;&nbsp;&nbsp;<span><?php echo ($customer_one["first_name"]); ?></span></li>
         <li><span>Last Name:</span>&nbsp;&nbsp;&nbsp;<span><?php echo ($customer_one["last_name"]); ?></span></li>
         <li><span>Title:</span>&nbsp;&nbsp;&nbsp;<span><?php echo ($customer_one["gender"]); ?></span></li>
     </ul>
  	</div>
  	
  	<div class="panel" style="width:75%;float:left;margin-left:3%;">
  	<div class="panel-head"><strong>扩展资料</strong></div>
  	  <table class="table">
          <tr><td width="110" align="right"><strong>生日：</strong></td><td><?php echo ($customer_one["birthday"]); ?></td><td width="90" align="right"><strong>纪念日：</strong></td><td><?php echo ($customer_one["memorial"]); ?></td></tr>
          <tr><td align="right"><strong>年龄：</strong></td><td><?php echo ($customer_one["age"]); ?></td><td align="right"><strong>性别：</strong></td><td><?php echo ($customer_one["gender"]); ?></td></tr>
          <tr><td align="right"><strong>衣服尺码：</strong></td><td><?php echo ($customer_one["dress_size"]); ?></td><td align="right"><strong>床品尺码：</strong></td><td><?php echo ($customer_one["bed_size"]); ?></td></tr>
          <tr><td align="right"><strong>颜色喜好：</strong></td><td><?php echo ($customer_one["like_color"]); ?></td><td align="right"><strong>兴趣爱好：</strong></td><td><?php echo ($customer_one["like_interests"]); ?></td></tr>
      </table>
  	</div>
  	
  	<div class="panel" style="width:100%;float:left;margin-top:10px;">
      <div class="panel-head"><strong>客户网站信息</strong></div>
      	 <ul class="list-group">
         <li><strong>积分:</strong>&nbsp;&nbsp;&nbsp;<?php if($customer_integral["now_integral"] == ''): ?>没有积分<?php else: echo ($customer_integral["now_integral"]); endif; ?><span style="width:40px;float:right;"><a class="icon-file-text" title="使用详情" onclick="show_integral('<?php echo ($customer_one["email"]); ?>')" style="float:right;cursor: pointer;"></a> <a class="icon-pencil" title="修改" onclick="add_integral('<?php echo ($customer_one["email"]); ?>')" style="float:left;cursor: pointer;"></a></span></li>
         <li><strong>优惠码:</strong>&nbsp;&nbsp;&nbsp;<?php if($code_list_new == ''): ?>无使用记录<?php else: if(is_array($code_list_new)): $i = 0; $__LIST__ = $code_list_new;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i; echo ($vo["code_name"]); ?>&nbsp;&nbsp;<?php endforeach; endif; else: echo "" ;endif; endif; ?></li>
         <li><strong>退货:</strong>&nbsp;&nbsp;&nbsp;<?php if($num == ''): ?>无退货记录;<?php else: echo ($num); ?> 次<?php endif; ?></br>
         <strong>换货:</strong>&nbsp;&nbsp;&nbsp;
         </li>
         <li><strong>配送地址:</strong>&nbsp;&nbsp;&nbsp;<?php if($customer_address["id"] == ''): ?>无记录 <?php else: echo ($customer_address["country"]); ?>--<?php echo ($customer_address["province"]); ?>--<?php echo ($customer_address["city"]); ?>--<?php echo ($customer_address["address"]); endif; ?></li>
         <li><strong>上次登录:</strong>&nbsp;&nbsp;&nbsp;<?php if($login_time == ''): ?>无记录 <?php else: echo ($login_time["0"]["login_time"]); endif; ?></li>
         <li><strong>订阅信息:</strong>&nbsp;&nbsp;&nbsp;
         <?php if(empty($sqp_mail_subscription)): ?>无记录 
         <?php else: ?>
         <?php if($sqp_mail_subscription["state"] == 1): ?>已订阅&nbsp;&nbsp;&nbsp;<strong>订阅时间:</strong><?php echo ($sqp_mail_subscription["date_time"]); ?>
         	<?php elseif($sqp_mail_subscription["state"] == 0): ?>
         	已取消订阅&nbsp;&nbsp;&nbsp;<strong>取消时间:</strong><?php echo ($sqp_mail_subscription["date_time"]); endif; endif; ?>
         </li>
         </ul>
    </div>
</div>

<div class="customer_details">
	<div class="navigation">
   	<div class="navigation_select">
       <ul>
		<li onclick="order_details('<?php echo ($customer_one[email]); ?>')"><a>订单信息</a></li>
		<li onclick="customer_details('sns','<?php echo ($customer_one[email]); ?>')"><a>SNS信息</a></li>
		<li onclick="customer_details('wishlist','<?php echo ($customer_one[email]); ?>')"><a>wishlist信息</a></li>
		<li onclick="customer_details('custom','<?php echo ($customer_one[email]); ?>')"><a>定制信息</a></li>
       </ul>
    </div>
	</div>
	
	<div class="details" id="details"></div>
</div>
</div>

</body>
</html>