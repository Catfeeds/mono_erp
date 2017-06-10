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
var _CONTROLLER_ = '/test/erptest/index.php/Admin/FactoryManage';
var _ACTION_  = '/test/erptest/index.php/Admin/FactoryManage/factorylist';
var _PUBLIC_ = '/test/erptest/Public';
var _TIMESTAMP_ = <?php echo time();?>;
var _SELF_ = '/test/erptest/index.php/Admin/FactoryManage/factorylist.html';
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
<div class="admin">

<table class="table  table-striped table-hover table-condensed">
		<tr>
			<td colspan="3" class="table_title" style="font-size:14px;padding:10px;">
				<span class="fl icon-bars"> 工厂列表</span>
				<span class="fr" style="margin-right:60px;">
					<a href="<?php echo U('/Admin/FactoryManage/factory_edit');?>"><span class="icon-plus text-green"></span> 添加工厂</a>
				</span>
			</td>
		</tr>
		<tr class="list_head">
			<td width="70" style="text-align:left">序号</td>
			<td width="150" style="text-align:left">工厂名称</td>
			<td width="150" style="text-align:center">操作</td>
<!-- 		<td width="500" style="text-align:center"></td> -->	
		</tr>
		
	    <?php if(is_array($list)): $i = 0; $__LIST__ = $list;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><tr class='<?php if(($mod) == "1"): ?>tr<?php else: ?>ji<?php endif; ?>' style='padding:20px;'>
			<td align='left'><span class="td_number"><?php echo ($i); ?></span></td>
			<td style="color:#03c"><?php echo ($vo["name"]); ?></td>
			<td align='center'>
				<a href="<?php echo U('/Admin/FactoryManage/factory_edit/',array('id'=>$vo['id']));?>" class="icon-pencil" title="修改"> 修改</a>&nbsp;&nbsp;
<!--            <a href="javascript:"  onClick="layer.confirm('你确定删除该用户？', {btn: ['确定','取消']}, function(){location.href='<?php echo U('/Admin/FactoryManage/factory_delete/',array('id'=>$vo['id']));?>'});" class="icon-trash-o" title="删除"> 删除</a></eq> -->
            </td>
<!--        <td></td> -->
		</tr><?php endforeach; endif; else: echo "" ;endif; ?>
</table>

</div>

</body>
</html>