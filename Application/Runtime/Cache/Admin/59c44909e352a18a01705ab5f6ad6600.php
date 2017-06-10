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
var _ACTION_  = '/erptest/index.php/Admin/OrderManage/order_manual';
var _PUBLIC_ = '/erptest/Public';
var _TIMESTAMP_ = <?php echo time();?>;
var _SELF_ = '/erptest/index.php/Admin/OrderManage/order_manual.html';
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
<script src="/erptest/Application/Admin/View/Default/js/OrderManage/order_manual.js"></script>
<div class="admin">
	<table class="table  table-striped table-hover table-condensed">
		<tr>
			<td colspan="13" class="table_title">
				<span class="fl" class="icon-users">手动订单管理</span>
				<span class="fr">
					<a href="<?php echo U('/Admin/OrderManage/order_manual_add');?>" class="icon-user">添加手动订单</a>
				</span>
			</td>
		</tr>
		<tr class="">
			<th>序号</th>
			<th>手动订单号</th>
			<th>客户邮箱</th>
			<th>客户电话</th>
			<th>国家</th>
			<th>州</th>
			<th>城市</th>
			<th>地址</th>
			<th>金额</th>
			<th>操作人</th>
			<th>订单生成时间</th>
            <th>订单状态</th>
			<th>管理操作</th>
		</tr>
	<?php if(is_array($list)): $k = 0; $__LIST__ = $list;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($k % 2 );++$k;?><tr>
			<td><?php echo ($k); ?></td>
			<td><?php echo ($vo["order_number"]); ?></td>
			<td><?php echo ($vo["email"]); ?></td>
			<td><?php echo ($vo["telephone"]); ?></td>
			<td><?php echo ($vo["country"]); ?></td>
			<td><?php echo ($vo["state"]); ?></td>
			<td><?php echo ($vo["city"]); ?></td>
			<td><?php echo ($vo["address"]); ?></td>
			<td><?php echo ($vo["price"]); ?></td>
			<td><?php echo ($vo["operator"]); ?></td>
			<td><span><?php echo ($vo["date_time"]); ?></span></td>
			<td><span style="color:#f60;"><?php echo ($vo["status"]); ?></span></td>
			<td>
				<a onclick="order_manual_add(<?php echo ($vo["id"]); ?>,'fetch');" href="javascript:" class="icon-plus-circle" title="添加手动订单关联产品"></a>&nbsp;&nbsp;
				<a href="<?php echo U('/Admin/OrderManage/order_manual_product/',array('id'=>$vo['id']));?>" class="icon-file-text" title="查看关联产品"></a>&nbsp;&nbsp;
				<a href="javascript:" class="icon-exchange text-yellow" title="修改订单状态" onclick="order_manual_update(<?php echo ($vo["id"]); ?>)"></a>&nbsp;&nbsp;
				<a class="icon-pencil" title="修改" href="<?php echo U('/Admin/OrderManage/order_manual_add/',array('id'=>$vo['id'] ));?>"></a>&nbsp;&nbsp; 
				<a href="javascript:" class="icon-trash-o" title="删除" onClick="layer.confirm('你确定删除该订单？', {btn: ['确定','取消']}, function(){location.href='<?php echo U('/Admin/OrderManage/order_manual_del/',array('id'=>$vo['id']));?>'});"></a> 
			</td>	
		</tr><?php endforeach; endif; else: echo "" ;endif; ?>
		<tr class="tr">
	          <td colspan="13" class="pages" align="center">
	            <?php echo ($page); ?>
	          </td>
	    </tr>
	</table>
</div>

</body>
</html>