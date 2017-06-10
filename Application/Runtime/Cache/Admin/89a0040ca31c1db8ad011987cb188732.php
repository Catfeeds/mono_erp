<?php if (!defined('THINK_PATH')) exit();?>
<div style="padding:25px">
	<p>
		<label style="display:inline-block;width:60px">快递：</label>
		<select id="delivery_edit_style">
		<?php if(is_array($delivery_style_list)): $i = 0; $__LIST__ = $delivery_style_list;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$delivery_style_vo): $mod = ($i % 2 );++$i;?><option value="<?php echo ($delivery_style_vo); ?>" <?php if(($old_shipping_style) == $delivery_style_vo): ?>selected="selected"<?php endif; ?> ><?php echo ($delivery_style_vo); ?></option><?php endforeach; endif; else: echo "" ;endif; ?>
		</select>
	</p>
	<p>
		<label style="display:inline-block;width:60px">运单号：</label>
		<input type="text" id="delivery_number" value="<?php echo ($old_number); ?>"/>
	</p>
	<p>
		<label style="display:inline-block;width:60px">重量：</label>
		<input type="text" id="delivery_weight" value="<?php echo ($old_weight); ?>"/>
	</p>
	<p>
		<input type="submit" onclick="delivery_detail_add(<?php echo ($order_id); ?>,'<?php echo ($style); ?>','update')">
	</p>
</div>