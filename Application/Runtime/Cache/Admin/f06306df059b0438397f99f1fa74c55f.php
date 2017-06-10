<?php if (!defined('THINK_PATH')) exit();?>
<table width="98%" border="0" cellpadding="4" cellspacing="1" class="table" id="table">
	<tr class="tr rt">
       <td width="100">状态：</td>
       <td class="lt">
       <select name="status" id="status_update_status">
        <?php if(is_array($status_list)): $i = 0; $__LIST__ = $status_list;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$status_vo): $mod = ($i % 2 );++$i;?><option value="<?php echo ($key); ?>" <?php if(($old_status) == $key): ?>selected="selected"<?php endif; ?> ><?php echo ($status_vo); ?></option><?php endforeach; endif; else: echo "" ;endif; ?>
       </select>
       </td>
   </tr>
   <tr class="tr rt">
       <td width="100">备注：</td>
       <td class="lt">
           <textarea name="status_update_message" id="status_update_message" value="<?php echo ($old_message); ?>" style="width:320px;height:300px;visibility:hidden;"></textarea>
       </td>
   </tr>
   <tr class="tr lt">
       <td colspan="4">
           <input onclick="order_status_update(<?php echo ($order_id); ?>,'<?php echo ($style); ?>','update')" class="button" type="submit" value="添 加">
       </td>
   </tr>
</table>