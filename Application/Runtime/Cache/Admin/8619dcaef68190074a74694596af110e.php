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
var _CONTROLLER_ = '/test/erptest/index.php/Admin/StatisticsManage';
var _ACTION_  = '/test/erptest/index.php/Admin/StatisticsManage/statistics_platform';
var _PUBLIC_ = '/test/erptest/Public';
var _TIMESTAMP_ = <?php echo time();?>;
var _SELF_ = '/test/erptest/index.php/Admin/StatisticsManage/statistics_platform.html';
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
.statistics_table td{border:none;}
</style>
<script src="/test/erptest/Application/Admin/View/Default/js/StatisticsManage/statistics.js"></script>
<div class="admin">
	<form action="<?php echo U('/Admin/StatisticsManage/statistics_chart');?>" method="post" name="form" id="myform">
	<div style="margin-bottom:10px;float:left;">
	<input type="hidden" name='come_from_id' value="<?php echo $_GET['come_from_id'];?>">
	<input type="hidden" name='catalog_id' value="<?php echo $_GET['catalog_id'];?>">
	<input type="hidden" name='product_id' value="<?php echo $_GET['product_id'];?>">
	<input type="hidden" name='begin_time' value="<?php echo $_POST['begin_time'];?>">
	<input type="hidden" name='end_time' value="<?php echo $_POST['end_time'];?>">
	<input type="submit" value="导出统计图表" class="button border-main">
	</div>
	</form>
	<div style="float:left;margin:10px 0px 10px;clear:both;">
	<form style="float:left;" action="" method="post" name="form" id="myform">
	<select name="country" class="admin_input" style="float:left;" onchange='javascript:location.href=_CONTROLLER_+"/statistics_platform/come_from_id/"+this.value+".html"'>
	<option value="0">请选择平台来源</option>
	<?php if(is_array($come_from_id)): $i = 0; $__LIST__ = $come_from_id;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$come_from_vo): $mod = ($i % 2 );++$i;?><option value="<?php echo ($come_from_vo["id"]); ?>" <?php if($_GET['come_from_id'] == $come_from_vo['id']): ?>selected<?php endif; ?>><?php echo ($come_from_vo["name"]); ?></option><?php endforeach; endif; else: echo "" ;endif; ?>
	</select>
	<select name="catalog" onchange='javascript:location.href=_CONTROLLER_+"/statistics_platform/come_from_id/<?php if(isset($_GET[come_from_id])){echo $_GET[come_from_id];}else{echo 0;}?>/catalog_id/"+this.value+".html"' class="admin_input" style="float:left;">
		<option value="0">请选择分类</option>
		<?php if(is_array($catalog_list)): $i = 0; $__LIST__ = $catalog_list;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$catalog_vo): $mod = ($i % 2 );++$i;?><option value="<?php echo ($catalog_vo["id"]); ?>" <?php if($_GET['catalog_id'] == $catalog_vo['id']): ?>selected<?php endif; ?>><?php echo ($catalog_vo["name"]); ?></option><?php endforeach; endif; else: echo "" ;endif; ?>
	</select>
	<select style="float:left;" name="product" class="admin_input" onchange='javascript:location.href=_CONTROLLER_+"/statistics_platform/come_from_id/<?php echo $_GET[come_from_id];?>/catalog_id/<?php echo $_GET[catalog_id];?>/product_id/"+this.value+".html"'>
		<option value="0">请选择产品</option>
		<?php if(is_array($product_list)): $i = 0; $__LIST__ = $product_list;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$product_vo): $mod = ($i % 2 );++$i;?><option value="<?php echo ($product_vo["id"]); ?>" <?php if($_GET['product_id'] == $product_vo['id']): ?>selected<?php endif; ?>><?php echo ($product_vo["name"]); ?></option><?php endforeach; endif; else: echo "" ;endif; ?>
	</select>
	日期：<input type="text" style="cursor:pointer;" name="begin_time" id="btime" class="admin_input" onClick="WdatePicker()" value=""> TO <input type="text" style="cursor:pointer;" name="end_time" id="btime" class="admin_input" onClick="WdatePicker()" value=""> <input type="submit" name="sub" value="查询" class="button border-main">
	</form>
	</div>
	<table class="table  table-striped table-hover table-condensed" style="border:none;">	
		<?php if(is_array($statistics_name)): foreach($statistics_name as $i=>$vo): ?><tr>
			<td style="color:#03c;border:1px solid #ccc;"><?php echo ($i); ?></td>
			<?php if(is_array($vo['name'])): foreach($vo['name'] as $j=>$product_list): if($j != ''): ?><td style="border:1px solid #ccc;">
			<table class="statistics_table">
				<tr>
				<td style="text-align:center"><?php echo ($j); ?></td>
				<td style="text-align:center"><?php echo ($product_list["currency"]); ?> <?php echo ($product_list["price"]); ?></td>
				<td style="text-align:center"><?php echo ($product_list["number"]); ?></td>
				</tr>
			</table>
			</td><?php endif; endforeach; endif; ?>
		</tr><?php endforeach; endif; ?>
	   	
		<tr class="tr">
          <td colspan="9" class="pages" align="center">
            <?php echo ($page); ?>
          </td>
        </tr>
	</table>
</div>

</body>
</html>