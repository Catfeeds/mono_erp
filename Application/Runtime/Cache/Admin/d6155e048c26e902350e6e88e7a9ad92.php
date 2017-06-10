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
var _CONTROLLER_ = '/test/erptest/index.php/Admin/Productstock';
var _ACTION_  = '/test/erptest/index.php/Admin/Productstock/product_stock_apply_all';
var _PUBLIC_ = '/test/erptest/Public';
var _TIMESTAMP_ = <?php echo time();?>;
var _SELF_ = '/test/erptest/index.php/Admin/Productstock/product_stock_apply_all.html';
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
	<div class="tab">
		<div class="tab-head">
		<strong>库存申请</strong>
		<ul class="tab-nav">
          <li class="active"><a href="#tab-set">单品库存申请</a></li>
          <li><a href="#tab-email">套件库存申请 </a></li>
        </ul>
		</div>
		<div class="tab-body">
			<div class="tab-panel active" id="tab-set">
			<form action="<?php echo U('Admin/Productstock/product_stock_apply_all?style='.$style);?>" id="status_form" method="post">
			<table class="table  table-striped table-hover table-condensed" width="98%" border="0" cellpadding="5" cellspacing="1" id="come_dp">
				<tr>
	                <td colspan="7" class="table_title tab_title">
	                    <span class="fl icon-align-justify"> 库存申请管理</span>
	                            
	                </td>
	             </tr>
             	<tr>
             		<td colspan="7"><label for="status">状态:　</label>
             		<select onchange="status_form.submit()" name="status" class="margin-right">
					<option value="-1">全部</option>
					<?php if(is_array($status_list)): $i = 0; $__LIST__ = $status_list;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$status_vo): $mod = ($i % 2 );++$i;?><option <?php if(($old_status) == $key): ?>selected="selected"<?php endif; ?> value="<?php echo ($key); ?>"><?php echo ($status_vo); ?></option><?php endforeach; endif; else: echo "" ;endif; ?>
					</select>
					</td>
             	</tr>
                <tr class="list_head ct">
                    <td align='left' width="200">产品名称</td>
                    <td align='left' width="70">数量</td>
                    <td align='left' width="70">库存类型</td>
                    <td align='left' width="70">申请人</td>
                    <td align='center' width="60">开始时间</td>
                    <td align='center' width="60">结束时间</td>
                    <td align='center' width="60">状态</td>
                    <!--<td align='center' width="100">管理操作</td>-->
                </tr>
            <?php if(is_array($list_dp)): $i = 0; $__LIST__ = $list_dp;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><tr>
                    <td class="td_cc"><?php echo ($vo["ckname"]); ?></td>
                    <td style="color: red; font-weight:bold;"><?php echo ($vo["number"]); ?></td>
                    <td ><?php echo ($vo["sty"]); ?></td>
                    <td ><?php echo username_name($vo['apply']);?></td>
                    <td align='center'><?php echo (date("Y-m-d",$vo["begin_time"])); ?></td>
                    <td align='center'><?php echo (date("Y-m-d",$vo["end_time"])); ?></td>
                    <td align='left'><?php echo ($vo["sta"]); ?> (<?php echo (date("Y-m-d H:i:s",$vo["change_time"])); ?>)
                    </td>
                </tr><?php endforeach; endif; else: echo "" ;endif; ?>
			</table>
			</form>
			</div>
			
			<div class="tab-panel" id="tab-email">
				<table class="table  table-striped table-hover table-condensed" width="98%" border="0" cellpadding="5" cellspacing="1" id="come_tj">
                <tr>
                <td colspan="7" class="table_title tab_title">
                    <span class="fl icon-align-justify"> 库存申请管理</span>
                </td>
                </tr>
                <tr class="list_head ct">
                    <td align='left' width="200">产品名称</td>
                    <td align='left' width="70">数量</td>
                    <td align='left' width="70">库存类型</td>
                    <td align='left' width="70">申请人</td>
                    <td align='center' width="60">开始时间</td>
                    <td align='center' width="60">结束时间</td>
                    <td align='center' width="60">状态</td>
                    <!--<td align='center' width="100">管理操作</td>-->
                </tr>
            	<?php if(is_array($list_tj)): $i = 0; $__LIST__ = $list_tj;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><tr>
                    <td class="td_cc"><?php echo ($vo["ckname"]); ?></td>
                    <td style="color: red; font-weight:bold;"><?php echo ($vo["number"]); ?></td>
                    <td ><?php echo ($vo["sty"]); ?></td>
                    <td ><?php echo username_name($vo['apply']);?></td>
                    <td align='center'><?php echo (date("Y-m-d",$vo["begin_time"])); ?></td>
                    <td align='center'><?php echo (date("Y-m-d",$vo["end_time"])); ?></td>
                    <td align='center'><?php echo ($vo["sta"]); ?> (<?php echo (date("Y-m-d H:i:s",$vo["change_time"])); ?>)
                    </td>
                </tr><?php endforeach; endif; else: echo "" ;endif; ?>
            </table>
		</div>
		</div>
	</div>
</div>

</body>
</html>