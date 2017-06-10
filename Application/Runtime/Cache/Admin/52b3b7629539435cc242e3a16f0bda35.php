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
var _ACTION_  = '/erptest/index.php/Admin/CodeManage/code_edit';
var _PUBLIC_ = '/erptest/Public';
var _TIMESTAMP_ = <?php echo time();?>;
var _SELF_ = '/erptest/index.php/Admin/CodeManage/code_edit';
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
<style>
.form-x .form-group .label{width:80px;text-align: left}
.form-x .form-button {margin:0}
</style>
<script src="/erptest/Application/Admin/View/Default/js/CodeManage/main.js"></script>
<div class="admin">

<h1>添加Code</h1>
<form method="post" action="" class="form-x margin-large-top">
	<div class="form-group">
		<div class="label">
			<label class="label">分类：</label>
		</div>
		<div class="field">
			<select class="input" name="catalog_id" onchange="get_product_of_catalog(this.value)">
				<option value="-1">--请选择--</option>
				<?php if(is_array($catalog_list)): $i = 0; $__LIST__ = $catalog_list;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$catalog_vo): $mod = ($i % 2 );++$i;?><option value="<?php echo ($key); ?>" <?php if($key == $row['catalog_id']): ?>selected="selected"<?php endif; ?> ><?php echo ($catalog_vo); ?></option><?php endforeach; endif; else: echo "" ;endif; ?>
			</select>
		</div>
	</div>
	<div class="form-group">
		<div class="label">
			<label class="label">产品：</label>
		</div>
		<div class="field">
			<select class="input" name="product_id" id="product_id">
				<option value="0">--请选择--</option>
				<?php if(!empty($product_option_html)): echo ($product_option_html); endif; ?>
			</select>
		</div>
	</div>
	<div class="form-group">
		<div class="label">
			<label class="label">尺寸：</label>
		</div>
		<div class="field">
			<select class="input" name="size_id">
			<option value="0">--请选择--</option>
			<?php if(is_array($size_list)): $i = 0; $__LIST__ = $size_list;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$size_vo): $mod = ($i % 2 );++$i;?><option value="<?php echo ($key); ?>" <?php if($key == $row['size_id']): ?>selected="selected"<?php endif; ?> ><?php echo ($size_vo); ?></option><?php endforeach; endif; else: echo "" ;endif; ?>
			</select>
		</div>
	</div>
	<div class="form-group">
		<div class="label">
			<label class="label">颜色：</label>
		</div>
		<div class="field">
			<select class="input" name="color_id">
			<option value="0">--请选择--</option>
			<?php if(is_array($color_list)): $i = 0; $__LIST__ = $color_list;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$color_vo): $mod = ($i % 2 );++$i;?><option value="<?php echo ($key); ?>" <?php if($key == $row['color_id']): ?>selected="selected"<?php endif; ?> ><?php echo ($color_vo); ?></option><?php endforeach; endif; else: echo "" ;endif; ?>
			</select>
		</div>
	</div>
	<div class="form-group">
		<div class="label">
			<label for="sort_id">Code：</label>
		</div>
		<div class="field">
			<input type="text" class="input" id="code" name="code" size="30" value="<?php echo ($row['code']); ?>"/>
		</div>
	</div>
	<div class="form-group">
		<div class="label">
			<label for="sort_id">名称：</label>
		</div>
		<div class="field">
			<input type="text" class="input" id="name" name="name" size="30" value="<?php echo ($row['name']); ?>"/>
		</div>
	</div>
	<div class="form-group">
		<div class="label">
			<label for="sort_id">重量：</label>
		</div>
		<div class="field">
			<input type="text" class="input" id="weight" name="weight" size="30" value="<?php echo ($row['weight']); ?>"/>
		</div>
	</div>
	<div class="form-group">
		<div class="label">
			<label for="sort_id">价格：</label>
		</div>
		<div class="field">
			<input type="text" class="input" id"price" name="price" size="30" value="<?php echo ($row['price']); ?>"/>
		</div>
	</div>
	<div class="form-group">
		<div class="label">
			<label for="sort_id">工厂：</label>
		</div>
		<div class="field">
			<select class="input" name="factory_id">
				<?php if(is_array($factory_list)): $i = 0; $__LIST__ = $factory_list;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$factory_vo): $mod = ($i % 2 );++$i;?><option value="<?php echo ($factory_vo["id"]); ?>" <?php if($factory_vo["id"] == $row['factory_id']): ?>selected="selected"<?php endif; ?> ><?php echo ($factory_vo["name"]); ?></option><?php endforeach; endif; else: echo "" ;endif; ?>
			</select>
		</div>
		
		
	</div>
	<div class="form-group">
		<div class="label">
			<label for="sort_id">排序：</label>
		</div>
		<div class="field">
			<input type="text" class="input" id="sort_id" name="sort_id" size="30" value="<?php echo ($row['sort_id']); ?>"/>
		</div>
	</div>
	<div class="form-group">
		<div class="label">
			<label for="is_work">生效：</label>
		</div>
		<div class="field">
			<div class="button-group radio">
				<label class="button <?php if($row['is_work'] == 1): ?>active<?php endif; ?> ">
					<input name="is_work" value="1" <?php if($row['is_work'] == 1): ?>checked="checked"<?php endif; ?> type="radio"><span class="icon icon-check"></span>是
				</label>
				<label class="button <?php if($row['is_work'] == 0): ?>active<?php endif; ?> ">
					<input name="is_work" value="0" <?php if($row['is_work'] == 0): ?>checked="checked"<?php endif; ?> type="radio"><span class="icon icon-times"></span>否
				</label>
			</div>
		</div>
	</div>
	<div class="form-button">
		<button class="button border-black padding-small" type="submit">提交</button>
	</div>
</form>

</div>

</body>
</html>