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
var _CONTROLLER_ = '/erptest/index.php/Admin/ProductManage';
var _ACTION_  = '/erptest/index.php/Admin/ProductManage/stock_local';
var _PUBLIC_ = '/erptest/Public';
var _TIMESTAMP_ = <?php echo time();?>;
var _SELF_ = '/erptest/index.php/Admin/ProductManage/stock_local.html';
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
<script src="/erptest/Application/Admin/View/Default/js/ProductManage/main.js"></script>
<script>
	<?php if(!empty($stock_sync_result)): ?>layer.alert("<?php echo ($stock_sync_result); ?>",{title:'更新结果',shade:0.5});<?php endif; ?>
	
</script>
<style>
.ul li{ float:left; line-height:24px; margin: 4px 1px;}
.xx{ font-size:20px; margin-left:5px;}
.padd{ padding:0px 3px;}
</style>

<div class="admin">
<input type="hidden" name="flag" id="flag" value="<?php echo ($flag); ?>"  />
<input type="hidden" name="warn"  id='warn' value="<?php echo ($warn); ?>"  />
<input type="hidden" name="catalog_id" id="catalog_id" value="<?php echo ($catalog_id); ?>"  />
<input type="hidden" name="product_id"  id='product_id' value="<?php echo ($product_id); ?>"  />
<input type="hidden" name="style"  id='style' value="<?php if($style == 2): ?>stock_local<?php elseif($style == 1): ?>stock_fba<?php elseif($style == 3): ?>stock_us<?php elseif($style == 0): ?>stock_all<?php endif; ?>"  />
	<!--库存列表-->
	<table class="table table-striped table-hover table-condensed" width="98%" border="0" cellpadding="5" cellspacing="1">
		<tr>
			<td colspan="6" class="table_title tab_title">
				<span class="fl icon-align-justify"> <?php echo ($title); ?>库存管理</span>
				
				<span style="margin-left:10px;"><a <?php if( ($flag == 'single') AND ($warn == 'all') ): ?>style="color:#0a8";<?php endif; ?> href="<?php echo U('',array('flag'=>'single','warn'=>'all'));?>"> 单件</a></span>
				<?php if(in_array(($style), explode(',',"0,2,3"))): ?><span style="margin-left:10px;"><a <?php if( ($flag == 'set') AND ($warn == 'all') ): ?>style="color:#0a8";<?php endif; ?> href="<?php echo U('',array('flag'=>'set','warn'=>'all'));?>"> 套件</a></span><?php endif; ?>
				<span style="margin-left:10px;"><a <?php if( ($flag == 'single') AND ($warn == 'warn') ): ?>style="color:#0a8";<?php endif; ?> href="<?php echo U('',array('flag'=>'single','warn'=>'warn'));?>"> 单件预警 </a></span>
				<?php if(in_array(($style), explode(',',"0,2,3"))): ?><span style="margin-left:10px;"><a <?php if( ($flag == 'set') AND ($warn == 'warn') ): ?>style="color:#0a8";<?php endif; ?> href="<?php echo U('',array('flag'=>'set','warn'=>'warn'));?>"> 套件预警 </a></span><?php endif; ?>
				<?php if(in_array(($style), explode(',',"2,3"))): ?><span style="margin-left:10px;"><a <?php if( $check == 'in' ): ?>style="color:#0a8";<?php endif; ?> href="<?php echo U('Admin/ProductManage/product_stock_check_add',array('style'=>$style));?>"> 盘点录入</a></span>
					<span style="margin-left:10px;"><a <?php if( $check == 'list' ): ?>style="color:#0a8";<?php endif; ?> href="<?php echo U('Admin/ProductManage/product_stock_check_list',array('style'=>$style));?>"> 盘点记录</a></span><?php endif; ?>
				<?php if(($style) == "2"): ?><a class="fr icon-share-square-o" href="<?php echo U('/Admin/ProductManage/shock_check_excel/',array('style'=>2,'catalog_id'=>$catalog_id,'product_id'=>$product_id));?>" > 导出盘点列表</a><?php endif; ?>
<!-- 			<?php if(($style) == "1"): ?><span onclick="return sync_fba_stock() " style="cursor:pointer;margin-left:10px;text-decoration:underline;">点击更新数据</span><?php endif; ?> -->	
			</td>
		</tr>
        
        <tr>
        	<td colspan="6" class="table_title tab_title">
                <label for="code">Code / Sku :</label>
            	<input type="text" name="code" id="code" value="<?php echo ($code); ?>">
                <input type="submit" value="查询" onclick="select_stock_local(<?php if($style == 2): ?>'stock_local'<?php elseif($style == 1): ?>'stock_fba'<?php elseif($style == 3): ?>'stock_us'<?php elseif($style == 0): ?>'stock_all'<?php endif; ?>)">
                <?php if(($flag) == "single"): ?><span class="remark_span">分类：</span>
                        <select name="catalog" class="" onchange="get_product_of_catalog(this.value,<?php if($style == 2): ?>'stock_local'<?php elseif($style == 1): ?>'stock_fba'<?php elseif($style == 3): ?>'stock_us'<?php elseif($style == 0): ?>'stock_all'<?php endif; ?>,this.options[this.selectedIndex].text)">
                          <option value="">全部</option>
                          <?php if(is_array($catalog_list)): $i = 0; $__LIST__ = $catalog_list;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><option value='<?php echo ($vo["id"]); ?>' <?php if(($catalog_id) == $vo["id"]): ?>selected=""<?php endif; ?>><?php echo ($vo["name"]); ?></option><?php endforeach; endif; else: echo "" ;endif; ?>
                        </select>
                     <span  <?php if($select_catalog_coun > '1'): ?>class='dis_no'<?php endif; ?>>   
                     <span class="remark_span">产品：</span>    
                        <select  name="product_id" id="product_id" onchange="get_product_of_code(this.value,<?php if($style == 2): ?>'stock_local'<?php elseif($style == 1): ?>'stock_fba'<?php elseif($style == 3): ?>'stock_us'<?php elseif($style == 0): ?>'stock_all'<?php endif; ?>,this.options[this.selectedIndex].text)" style="max-width: 450px;">
                          <option value="">全部</option>
                          <?php if(is_array($product_list)): $i = 0; $__LIST__ = $product_list;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><option value='<?php echo ($vo["id"]); ?>' <?php if(($product_id) == $vo["id"]): ?>selected=""<?php endif; ?>><?php echo ($vo["name"]); ?></option><?php endforeach; endif; else: echo "" ;endif; ?>
                        </select>
                     </span><?php endif; ?>
                     
            </td>
        </tr>
        
       	<tr <?php if(!$select_catalog ): ?>class="catalog_tr dis_no"<?php endif; ?>>
       		<td colspan="6">
            	<ul class="catalog_ul ul" id='catalog_ul'>
                     <li>分类:</li>
                     <?php if(is_array($select_catalog)): $i = 0; $__LIST__ = $select_catalog;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$catalog_vo): $mod = ($i % 2 );++$i;?><li class="plcz padd"><input type="hidden" name="catalog[]" value="<?php echo ($catalog_vo["id"]); ?>"  id="aa"><?php echo ($catalog_vo["name"]); ?><span class="close rotate-hover add_span xx" onclick="$(this).parent().remove(),get_product_of_catalog()"></span></li><?php endforeach; endif; else: echo "" ;endif; ?>                     
                </ul> 
       		</td>
       	</tr>
       
        <tr <?php if(!$select_product ): ?>class="product_tr dis_no"<?php endif; ?>>
       		<td colspan="6">
            	 <ul class="product_ul ul" id='product_ul'>
                   <li>产品:</li>
                   <?php if(is_array($select_product)): $i = 0; $__LIST__ = $select_product;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$product_vo): $mod = ($i % 2 );++$i;?><li class="plcz padd"><input type="hidden" name="product[]" value="<?php echo ($product_vo["id"]); ?>"  id="aa"><?php echo ($product_vo["name"]); ?><span class="close rotate-hover add_span xx" onclick="$(this).parent().remove(),get_product_of_code()"></span></li><?php endforeach; endif; else: echo "" ;endif; ?> 
                   
                 </ul> 
       		</td>
       	</tr>
		<tr class="list_head ct">
		<?php if(($flag) == "single"): ?><td width="30"></td>
			<td align='left' width="180">产品代号</td>
			<td align='left' width="70">当前库存</td>
			<td align='center' width="100">预警设置</td>
            <?php if(($style) == "1"): ?><td align='center' width="70">仓库</td><?php endif; ?>
			<?php if($style != 0): ?><td align='center' width="150">管理操作</td>
			<?php else: ?>
			<td align='center' width="150">库存类型</td><?php endif; ?>
		<?php else: ?>
			<td width="30"></td>
			<td align='left' width="180">产品sku</td>
			<td align='left' width="50">当前库存</td>
			<td align='center' width="100">预警设置</td>
            <?php if(($style) == "1"): ?><td align='center' width="70">仓库</td><?php endif; ?>
			<td align='center' width="200">库存组成</td>
			<?php if($style != 0): ?><td align='center' width="100">管理操作</td>
			<?php else: ?>
			<td align='center' width="150">库存类型</td><?php endif; endif; ?>
		</tr>
		
		<?php if(is_array($list)): $i = 0; $__LIST__ = $list;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><tr>
			<td><span class="td_number"><?php echo ($i); ?></span></td>
		  <td align="left" class="td_cc">
				<?php if(($flag) == "single"): echo get_product_info($vo['code_id'],$flag);?>
				<?php else: ?>
					<?php echo get_product_info($vo['sku_id'],$flag); endif; ?>
				<?php if(($vo['minimum'] != 0) or ($vo['maximum'] != 0)): if($vo['number'] < $vo['minimum']): ?><span class="tag bg-red">过低</span><?php endif; ?>
					<?php if($vo['number'] > $vo['maximum']): ?><span class="tag bg-red">过高</span><?php endif; endif; ?>
			</td>
			<td align='left' ><span style='color:#0a8'><?php echo ($vo["number"]); ?></span></td>
			<td align="center">
				<?php if(($vo['minimum'] != 0) or ($vo['maximum'] != 0)): ?>最大库存:<span style="color:red;"><?php echo ($vo["maximum"]); ?></span>  ；最小库存:<span style="color:red;"><?php echo ($vo["minimum"]); ?></span>
				<?php else: ?>
				未设置<?php endif; ?>
			</td>
            <?php if(($style) == "1"): ?><td align="center"><?php echo ($vo["place"]); ?></td><?php endif; ?>
		  <?php if(($flag) == "set"): ?><td align="center" style=";">
			  <?php if(is_array($vo["code_list"])): $i = 0; $__LIST__ = $vo["code_list"];if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$code_list): $mod = ($i % 2 );++$i;?><span class="td_cc">[<?php echo ($code_list['code']); ?>]</span>&nbsp;<?php echo ($code_list['code_name']); ?>&nbsp;&nbsp;<span style='color:#0a8'><?php echo ($code_list[number]); ?></span><br><?php endforeach; endif; else: echo "" ;endif; ?>
			  </td><?php endif; ?>
			<?php if($style != 0): ?><td align='center'>
				<a href="<?php echo U('/Admin/ProductManage/product_warn_edit?id='.$vo['id'],$param);?>" class="icon-pencil" title='修改'></a>&nbsp;&nbsp;
				<a href="<?php echo U('/Admin/productstock/product_stock_apply_add/',array('code'=>$vo['code_id']));?>" class="icon-file-o" title="添加库存申请"></a>
				&nbsp;&nbsp;<a href="<?php echo U('/Admin/productstock/product_stock_check/',array('id'=>$vo['id'],'flag'=>$flag,'p'=>I('get.p')));?>" class="icon-file-text-o" title="盘点"></a>
			</td><?php endif; ?>
			<?php if($style == 0): ?><td align='center'>
				<?php echo get_stock_style( $vo['style'] );?>
				</td><?php endif; ?>
		</tr><?php endforeach; endif; else: echo "" ;endif; ?>
		
		<tr>
			<td colspan="6" class="list-page table_titlen tab_title" align="center" style='padding:15px 0;'><?php if(empty($page_bar)): ?>暂时没有数据<?php else: echo ($page_bar); endif; ?></td>
		</tr>
	</table>

</div>

</body>
</html>