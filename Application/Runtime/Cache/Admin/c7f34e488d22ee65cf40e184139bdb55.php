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
var _CONTROLLER_ = '/erptest/index.php/Admin/CodeManage';
var _ACTION_  = '/erptest/index.php/Admin/CodeManage/code';
var _PUBLIC_ = '/erptest/Public';
var _TIMESTAMP_ = <?php echo time();?>;
var _SELF_ = '/erptest/index.php/Admin/CodeManage/code/sta/1';
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
<script src="/erptest/Application/Admin/View/Default/js/CodeManage/main.js"></script>
<?php if($catalog_id != ""): ?><script>
window.onload=function()
{
    get_code_of_code('',<?php echo ($catalog_id); ?>);
	}
</script>
<if condition='$product_id neq ""'>
<script>
window.onload=function()
{
	get_code_of_code(<?php echo ($product_id); ?>)
}
</script><?php endif; ?>
<div class="admin">
<form  method="post" name="myform_screening" id="myform_screening">
<table class="table table-hover margin-large-top" style="border-bottom:none;">
    <tr>
		<td colspan="9" class="table_title" style="font-size:14px;padding:10px;">
			<span class="fl icon-users"> 产品Code列表</span>
			<from action="" method="post"><span style="margin-left:50px;">code: &nbsp;&nbsp;<input type="text" name="code" style="width:150px;">&nbsp;&nbsp;<input type="submit" name="sub" value="code查询"></from></span>
			<span class="fr" style="margin:5px;font-size:14px;"><a href="/erptest/index.php/Admin/CodeManage/code_edit" class="icon-plus-circle"> 添加Code</a></span>
		</td>
	</tr>
    <tr> <td class="table_title" colspan="13" style=" border-bottom:none;">
    <div class="remark">
        <span class="remark_span">分类：</span>
            <select name="catalog"  onchange="get_product_of_catalog(this.value) , get_code_of_code('',this.value)">
              <option value="">全部</option>
              <?php if(is_array($catalog)): $i = 0; $__LIST__ = $catalog;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><option value='<?php echo ($key); ?>' <?php if(($catalog_id) == $key): ?>selected=""<?php endif; ?>><?php echo ($vo); ?></option><?php endforeach; endif; else: echo "" ;endif; ?>
            </select>
         <span class="remark_span">产品：</span>    
            <select  name="product_id" id="product_id" onchange="get_code_of_code(this.value)">
              <option value="">全部</option>
              <?php if(is_array($product_list)): $i = 0; $__LIST__ = $product_list;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><option value='<?php echo ($vo["id"]); ?>' <?php if(($product_id) == $vo["id"]): ?>selected=""<?php endif; ?>><?php echo ($vo["name"]); ?></option><?php endforeach; endif; else: echo "" ;endif; ?>
            </select>
         <span  style=" margin-left:30px;"> 
             <span style="font-size:14px;"><input class="plcz" type="button"  onclick="code_screening_form_submit()" value="导出筛选code" /> </span>  
             <span style="font-size:14px;"><a onclick="select_all()" class="icon-arrows-v margin-left" href="#"> 全选 </a></span>
             <span style="font-size:14px;"><input class="plcz" type="button"  onclick="code_form_submit()" value="导出选中code" /> </span> 
         </span>
    </div>
    </tr>
</table>
</form>
    
<div class="clear"></div> 
<form  method="post" name="form" id="myform">
<div class="panel admin-panel">
    <table class="table table-hover update">
   	<tr><th width="45"></th>
   		<th width="100">分类</th>
   		<th width="100">产品</th>
   		<th width="100">尺寸</th>
   		<th width="100">颜色</th>
   		<th width="100">Code</th>
   		<th width="100">名称</th>
   		<th width="100">重量</th>
   		<th width="100">价格</th>
   		<th width="100">工厂</th>
   		<th width="100">排序</th>
   		<th width="100">生效</th>
   		<th width="200" style="text-align:center">操作</th></tr>
    	<?php if(is_array($code_list)): $i = 0; $__LIST__ = $code_list;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$code_vo): $mod = ($i % 2 );++$i;?><tr>
        	<td><input class="check" name="check[]" type="checkbox" value="<?php echo ($code_vo["id"]); ?>" ></td>
        	<td style="color: #f60"><?php echo ($code_vo["catalog_name"]); ?></td>
        	<td style="color:#03c"><?php echo ($code_vo["product_name"]); ?></td>
        	<td><?php echo ($code_vo["size_name"]); ?></td>
        	<td><?php echo ($code_vo["color_name"]); ?></td>
        	<td><?php echo ($code_vo["code"]); ?></td>
        	<td style="color:#03c"><?php echo ($code_vo["name"]); ?></td>
        	<td><?php echo ($code_vo["weight"]); ?></td>
        	<td><?php echo ($code_vo["price"]); ?></td>
        	<td><?php echo get_factory_name($code_vo['factory_id']);?></td>
        	<td><?php echo ($code_vo["sort_id"]); ?></td>
        	<td><?php if(($code_vo["is_work"]) == "1"): ?><font color="red" class="icon-check"></font><?php else: ?><font color="blue" class="icon-times"></font><?php endif; ?></td>
        	<td style="text-align:center">
	        	<a class="icon-link" title="查看相关sku" href="/erptest/index.php/Admin/CodeManage/code_have_sku/id/<?php echo ($code_vo["id"]); ?>"></a>&nbsp;&nbsp;
        		<a class="icon-pencil" title="修改" href="/erptest/index.php/Admin/CodeManage/code_edit/id/<?php echo ($code_vo["id"]); ?>"></a>&nbsp;&nbsp;
        		<a class="icon-trash-o" title="删除" href="/erptest/index.php/Admin/CodeManage/code_delete/id/<?php echo ($code_vo["id"]); ?>" onclick="{if(confirm('确认删除?')){return true;}return false;}"></a>&nbsp;&nbsp;
                <a class="icon-plus" title="关联SKU" href="/erptest/index.php/Admin/CodeManage/code_sku/id/<?php echo ($code_vo["id"]); ?>"></a>
        	</td>
       	</tr><?php endforeach; endif; else: echo "" ;endif; ?>
       </table>
       <div class="list-page"><?php echo ($page); ?></div> 
</div>
</form>

</div>

</body>
</html>