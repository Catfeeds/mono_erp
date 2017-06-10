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
var _CONTROLLER_ = '/index.php/Admin/FactoryOrder';
var _ACTION_  = '/index.php/Admin/FactoryOrder/factory_order_dp';
var _PUBLIC_ = '/Public';
var _TIMESTAMP_ = <?php echo time();?>;
var _SELF_ = '/index.php/Admin/FactoryOrder/factory_order_dp.html';
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
<table class="table  table-striped table-hover table-condensed" width="98%" border="0" cellpadding="5" cellspacing="1" class="table">
    <tr>
        <td colspan="10" class="table_title tab_title">
            <span class="fl icon-align-justify"> 工厂单品订单管理</span>
        </td>
        <tr class="list_head ct">
            <td width="50" align="left">ID</td>
            <td width="100"  align="left">产品名称</td>
            <td width="70"  align="left">申请人</td>
            <td width="70"  align="left">申请数量</td>
            <td width="70"  align="left">库存类型</td>
            <td width="150"  align="center">接收时间</td>
            <td width="150"  align="center">管理操作</td>
        </tr>
    <?php if(is_array($list)): $i = 0; $__LIST__ = $list;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><tr class='<?php if(($mod) == "1"): ?>tr<?php else: ?>ji<?php endif; ?>'>
            <td align='left' class="td_number"><?php echo ($vo["id"]); ?></td>
            <td class="td_cc"><?php echo ($vo["code_name"]); ?></td>
            <td ><?php echo ($vo["apply"]); ?></td>
            <td class="td_ff"><?php echo ($vo["number"]); ?></td>
            <td ><?php echo ($vo["sty"]); ?></td>
            <td align="center"><?php echo (date("Y-m-d H:i:s ",$vo["change_time"])); ?></td>
            <td align="center"><a href="<?php echo U('/Admin/FactoryOrder/factory_order_start',array('id'=>$vo['id']));?>" class="icon-check-square-o" title="确认接单">&nbsp;接单</a>
            </td>
        </tr><?php endforeach; endif; else: echo "" ;endif; ?>
</table>
    <br /><br /><br />
    
<!-- 已接单列表-->
<table class="table  table-striped table-hover table-condensed" width="98%" border="0" cellpadding="5" cellspacing="1" id="dpdd">
    <tr>
        <td colspan="10" class="table_title tab_title">
            <span class="flfl icon-align-justify"> 已接受工厂订单管理</span>
        </td>
    </tr>    
    <tr class="list_head ct">
        <td width="50" align="left">ID</td>
        <td width="100"align="left">产品名称</td>
        <td width="50" align="left">申请人</td>
        <td width="50" align="left">申请数量</td>
        <td width="70" align="left">库存类型</td>
        <td width="80" align="center">开始时间</td>
        <td width="80" align="center">预计结束时间</td>
        <td width="100" align="center">实际结束时间</td>
        <td width="70" align="center">状态</td>
        <td width="70" align="center">管理操作</td>
    </tr>
<?php if(is_array($prostockcome_dp)): $i = 0; $__LIST__ = $prostockcome_dp;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$in): $mod = ($i % 2 );++$i;?><tr>
        <td class="td_number"><?php echo ($in["fac_id"]); ?></td>
        <td class="td_cc"><?php echo ($in["name"]); ?></td>
        <td ><?php echo ($in["apply"]); ?></td>
        <td ><?php echo ($in["number"]); ?></td>
        <td ><?php echo ($in["sty"]); ?></td>
        <td align="center"><?php echo (date("Y-m-d",$in["begin_time"])); ?></td>
        <td align="center"><?php echo (date("Y-m-d",$in["end_time"])); ?></td>
        <td align="center"><?php if($in["status"] > 2): echo (date("Y-m-d H:i:s",$in["actual_end_time"])); endif; ?></td>
        <td align="center" class="td_ff"> <?php if($in["status"] == 2): ?>未完成<?php elseif($in["status"] == 3): ?>已提交<?php elseif($in["status"] == 4): ?>对方已收货<?php endif; ?></td>
        <td align='center'><a <?php if($in["status"] < '3'): ?>href="<?php echo U('/Admin/FactoryOrder/factory_order_end',array('id'=>$in['fac_id']));?>"<?php else: ?>style="color:#ccc"<?php endif; ?>>完成</a>
        </td>
    </tr><?php endforeach; endif; else: echo "" ;endif; ?>
<tr>
    <td colspan="10" class="page"  align="center">
        <?php if($page != ''): echo ($page); else: ?>暂时没有记录<?php endif; ?>
    </td>
 </tr>
</table>


</div>

</body>
</html>