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
var _CONTROLLER_ = '/erptest/index.php/Admin/CodeManage';
var _ACTION_  = '/erptest/index.php/Admin/CodeManage/sku';
var _PUBLIC_ = '/erptest/Public';
var _TIMESTAMP_ = <?php echo time();?>;
var _SELF_ = '/erptest/index.php/Admin/CodeManage/sku.html';
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

    <table class="table  table-striped table-hover table-condensed margin-large-top">
        <tr> 
            <td colspan="6" class="table_title tab_title" style="font-size:14px;padding:10px;">
                <span class="fl icon-align-justify"> 产品sku</span>
				<span class="fr"><a href="/erptest/index.php/Admin/CodeManage/sku_edit" class="icon-plus-circle"> 添加sku</a></span>              
            </td>
        </tr>
        <tr>
        	<td colspan="6">
        		<a href="/erptest/index.php/Admin/CodeManage/sku.html" class="plcz <?php if($type == "" ): ?>button_action<?php endif; ?>" >全部</a>
                <a href="/erptest/index.php/Admin/CodeManage/sku/type/dp.html" class="plcz <?php if($type == "dp" ): ?>button_action<?php endif; ?>" >单品</a>
                <a href="/erptest/index.php/Admin/CodeManage/sku/type/tj.html" class="plcz <?php if($type == "tj" ): ?>button_action<?php endif; ?>" >套件</a>
                <form action="<?php echo U('sku');?>" method="post" style="display:inline-block;"> 
	                <label for="sku" style="margin-left:30px;">sku:　</label>
	                <input type="text" name="sku"/>
	                <input type="submit" value="查询"/>
                </form>
        	</td>
        </tr>
    	<tr class="list_head"><th width="45"></th><th width="100">sku</th><th width="100">名称</th><th width="80">来源</th><th width="150">关联code</th><th width="100" style="text-align:center">操作</th>
        </tr>
    	<?php if(is_array($list)): $i = 0; $__LIST__ = $list;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><tr>
        	<td><span class="td_number"><?php echo ($i); ?></span></td>
        	<td style="color:#03c"><?php echo ($vo["sku"]); ?></td>
        	<td><?php echo ($vo["name"]); ?></td>
        	<td><?php echo get_come_from_name($vo['come_from_id']);?></td>
        	<td>
        	<?php if(sizeof($vo['code_info']) == 0): ?><span class="text-dot">未添加关联</span>
        	<?php elseif(sizeof($vo['code_info']) == 1): ?>
        		<b><?php echo ($vo["code_info"]["0"]["code"]); ?> : <?php echo ($vo["code_info"]["0"]["name"]); ?> [<?php echo ($vo["code_info"]["0"]["number"]); ?>] </b>
        		<a class="icon-edit" href="/erptest/index.php/Admin/CodeManage/relate_edit/relate_id/<?php echo ($vo["code_info"]["0"]["relate_id"]); ?>" ></a>
        		<a class="icon-trash-o" href="/erptest/index.php/Admin/CodeManage/relate_delete/relate_id/<?php echo ($vo["code_info"]["0"]["relate_id"]); ?>" ></a>
        	<?php else: ?>
				<div class="button-group">
					<button type="button" style="padding-left:0;" class="button dropdown-toggle">
						点击查看套件<span class="downward"></span>
					</button>
					<ul class="drop-menu">
						<style>.drop-menu a{display:inline-block;padding:0;} .drop-menu{min-width:220px;}</style>
						<?php if(is_array($vo['code_info'])): $j = 0; $__LIST__ = $vo['code_info'];if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$code_vo): $mod = ($j % 2 );++$j;?><li style="display:block;">
								<span> <?php echo ($code_vo["code"]); ?> : <?php echo ($code_vo["name"]); ?> (<?php echo ($code_vo["number"]); ?>) </span>
								<span class="float-right">
									<a class="icon-edit" href="/erptest/index.php/Admin/CodeManage/relate_edit/relate_id/<?php echo ($code_vo["relate_id"]); ?>"></a>
			            			<a class="icon-trash-o" href="/erptest/index.php/Admin/CodeManage/relate_delete/relate_id/<?php echo ($code_vo["relate_id"]); ?>"></a>
			           			</span>
							</li><?php endforeach; endif; else: echo "" ;endif; ?>
					</ul>
				</div><?php endif; ?>
        	</td>
        	<td style="text-align:center">
        		<a class="icon-link" title="关联code" href="/erptest/index.php/Admin/CodeManage/relate_edit/sku_id/<?php echo ($vo["id"]); ?>"></a> &nbsp;&nbsp;
        		<a class="icon-pencil" title="修改" href="/erptest/index.php/Admin/CodeManage/sku_edit/id/<?php echo ($vo["id"]); ?>"></a> &nbsp;&nbsp;
        		<a class="icon-trash-o" title="删除" href="/erptest/index.php/Admin/CodeManage/sku_delete/id/<?php echo ($vo["id"]); ?>" onclick="{if(confirm('确认删除?')){return true;}return false;}"></a>
			</td>
		</tr><?php endforeach; endif; else: echo "" ;endif; ?>
	</table>
    <div class="list-page"><?php echo ($page); ?></div>


</div>

</body>
</html>