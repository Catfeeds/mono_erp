<?php if (!defined('THINK_PATH')) exit();?>
<div style="padding:25px">
	<p>
		<label style="display:inline-block;width:50px">快递：</label>
		<select id="delivery_edit_style">
		<?php if(is_array($delivery_style_list)): $i = 0; $__LIST__ = $delivery_style_list;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$delivery_style_vo): $mod = ($i % 2 );++$i;?><option value="<?php echo ($delivery_style_vo); ?>" <?php if(($old_style) == $delivery_style_vo): ?>selected="selected"<?php endif; ?> ><?php echo ($delivery_style_vo); ?></option><?php endforeach; endif; else: echo "" ;endif; ?>
		</select>
	</p>
	<p>
		<label style="display:inline-block;width:50px">重量：</label>
		<input type="text" id="delivery_edit_weight" value="<?php if($old_weight ): echo ($old_weight); else: ?>0.5<?php endif; ?>"/>
	</p>
	<p>
		<label style="display:inline-block;width:50px">hs：</label>
		<select id="delivery_edit_hs"><?php echo ($hs); ?></select>
	</p>
	<p>
		<input type="submit" onclick="delivery_edit(<?php echo ($order_id); ?>,'<?php echo ($style); ?>','update')">
	</p>
</div>