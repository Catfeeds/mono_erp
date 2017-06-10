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
var _CONTROLLER_ = '/erptest/index.php/Admin/FactoryManage';
var _ACTION_  = '/erptest/index.php/Admin/FactoryManage/factory_new';
var _PUBLIC_ = '/erptest/Public';
var _TIMESTAMP_ = <?php echo time();?>;
var _SELF_ = '/erptest/index.php/Admin/FactoryManage/factory_new.html';
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
<div class="admin">
	<table class="table  table-striped table-hover table-condensed" width="98%" border="0" cellpadding="5" cellspacing="1" class="table">
		<tr>
			<td colspan="7" class="table_title tab_title">
				<span class="fl icon-align-justify"> 产品申请管理</span>
			</td>
			<tr class="list_head ct">
				<td width="50" align="left">ID</td>
				<td width="100" align="left">产品名称</td>
				<td align="left">产品描述</td>
                <td width="70" align="left">申请人</td>
                <td width="150" align="center">接收时间</td>
				<td width="150" align="center">管理操作</td>
			</tr>
	    <?php if(is_array($list)): $i = 0; $__LIST__ = $list;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><tr class='<?php if(($mod) == "1"): ?>tr<?php else: ?>ji<?php endif; ?>'>
				<td class="td_number" ><?php echo ($vo["id"]); ?></td>
				<td  class="td_cc"><?php echo ($vo["name"]); ?></td>
				<td  class="td_ff"><?php echo ($vo["message"]); ?></td>
                <td ><?php echo ($vo["applicant"]); ?></td>
                <td  align="center"><?php echo (date("Y-m-d H:i:s ",$vo["examination_time"])); ?></td>
				<td align='center'><a href="<?php echo U('/Admin/FactoryManage/factory_start',array('id'=>$vo['id']));?>">查看</a>
				</td>
			</tr><?php endforeach; endif; else: echo "" ;endif; ?>
		</table>
        <br /><br /><br />
       <!-- 已接单列表-->
        <table class="table  table-striped table-hover table-condensed" width="98%" border="0" cellpadding="5" cellspacing="1" class="table">
		<tr>
			<td colspan="9" class="table_title tab_title">
				<span class="fl icon-align-justify"> 已接受产品申请</span>
			</td>
			<tr class="list_head ct">
				<td width="50" align="left">ID</td>
				<td width="250" align="left">产品名称</td>
				<td align="left">产品描述</td>
                <td width="70" align="left">申请人</td>
                <td width="110">开始时间</td>
                <td width="110">预计结束时间</td>
                <td width="150">实际结束时间</td>
                <td width="70">状态</td>
				<td width="100">管理操作</td>
			</tr>
	    <?php if(is_array($product_new)): $i = 0; $__LIST__ = $product_new;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$in): $mod = ($i % 2 );++$i;?><tr class='<?php if(($mod) == "1"): ?>tr<?php else: ?>ji<?php endif; ?>'>
				<td class="td_number"><?php echo ($in["fac_id"]); ?></td>
				<td class="td_cc"><?php echo ($in["name"]); ?></td>
				<td class="td_ff"><?php echo ($in["message"]); ?></td>
                <td ><?php echo ($in["applicant"]); ?></td>
                <td  align="center"><?php echo (date("Y-m-d",$in["begin_time"])); ?></td>
                <td  align="center"><?php echo (date("Y-m-d",$in["end_time"])); ?></td>
                <td  align="center"><?php if($in["fac_status"] != 0): echo (date("Y-m-d H:i:s",$in["actual_time"])); endif; ?></td>
                <td  align="center" class="td_ff"> <?php if($in["fac_status"] == 0): ?>未完成<?php elseif($in["fac_status"] == 1): ?>已提交<?php else: ?>对方已查看<?php endif; ?></td>
				<td align='center'><a <?php if(($in["fac_status"]) == "0"): ?>href="<?php echo U('/Admin/FactoryManage/factory_end',array('id'=>$in['fac_id']));?>"<?php else: ?>style="color:#ccc"<?php endif; ?>>完成</a>
				</td>
			</tr><?php endforeach; endif; else: echo "" ;endif; ?>
		</table>
        <div class="list-page"> <?php echo ($page); ?></div>
</div>

</body>
</html>