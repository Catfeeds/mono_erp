<?php if (!defined('THINK_PATH')) exit();?>
<?php switch($style): case "fetch": ?><div style="padding-left:14px;padding-right:14px;">

	<p>
		<span>SKU:　</span>
		<span style="font-weight:bold;"><?php echo ($sku); ?></span>
		<input type="hidden" id="sku_id" name="sku_id" value="<?php echo ($sku_id); ?>"/>
		<input type="hidden" id="order_id" name="order_id" value="<?php echo ($order_id); ?>"/>
		<input type="hidden" id="order_product_id" name="order_product_id" value="<?php echo ($order_product_id); ?>"/>
		<input type="hidden" id="type" name="type" value="<?php echo ($type); ?>"/>
		<input type="hidden" id="original_set_id" name="original_set_id" value="<?php echo ($original_set_id); ?>"/>
		<span id="sku_name_container" style="margin-left:20px;display:none;">
			<span>套件名称: </span>
			<input id="sku_name" name="sku_name" value="<?php echo ($sku_name); ?>"/>
			<span style="color:red;font-weight:bold;">*</span>
		</span>
	</p>
	<p>
		<span>分类：</span>
		<select onchange="relate_select_change(this.value,'catalog')" name="relate_catalog" id="relate_catalog">
			<option value="0">--请选择--</option>
			<?php if(is_array($catalog_list)): $i = 0; $__LIST__ = $catalog_list;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$catalog_vo): $mod = ($i % 2 );++$i;?><option value="<?php echo ($key); ?>"><?php echo ($catalog_vo); ?></option><?php endforeach; endif; else: echo "" ;endif; ?>
		</select>
	</p>
	<p>
		<span>产品：</span>
		<select onchange="relate_select_change(this.value,'product')" name="relate_product" id="relate_product" style="width:400px;">
			<option value="0">--请选择--</option>
		</select>
	</p>
	<p>
		<span>颜色：</span>
		<select onchange="relate_select_change(this.value,'color')" name="relate_color" id="relate_color">
			<option value="0">--请选择--</option>
		</select>
		
		<span style="margin-left:5px;">尺码：</span>
		<select onchange="relate_select_change(this.value,'size')" name="relate_size" id="relate_size">
			<option value="0">--请选择--</option>
		</select>
	</p>
	
	<p>
		<span>code：</span>
		<span id="relate_code"> - </span>
	</p>
	
	<button onclick="return web_relate_submit();" style="position:fixed;bottom:15px;right:15px;">提交</button>
	
</div><?php break;?>

<?php case "select": ?><option value="0">--请选择--</option>
	<?php if(is_array($list)): $i = 0; $__LIST__ = $list;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><option <?php if(($old_value) == $key): ?>selected="selected"<?php endif; ?> value="<?php echo ($key); ?>"><?php echo ($vo); ?></option><?php endforeach; endif; else: echo "" ;endif; break;?>

<?php case "radio": if(is_array($list)): $i = 0; $__LIST__ = $list;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><span style="display:inline-block;">
			<input type="radio" name="relate_code" value="<?php echo ($key); ?>" code_name="<?php echo ($vo); ?>" style="margin-left:5px;"/><?php echo ($vo); ?>
		</span><?php endforeach; endif; else: echo "" ;endif; break; endswitch;?>