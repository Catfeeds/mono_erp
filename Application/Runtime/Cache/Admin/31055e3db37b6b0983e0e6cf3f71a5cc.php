<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>Lilysilk - ERP</title>
</head>

<!-- 加载js css -->
<link rel="stylesheet" href="/Application/Admin/View/Default/css/pintuer.css">
<link rel="stylesheet" href="/Application/Admin/View/Default/css/admin.css">
<script src="/Application/Admin/View/Default/js/jquery.min.js"></script>
<script src="/Application/Admin/View/Default/js/pintuer.js"></script>
<script src="/Application/Admin/View/Default/js/respond.js"></script>
<script src="/Application/Admin/View/Default/js/admin.js"></script>
<script src="/Application/Admin/View/Default/js/layer/layer.js"></script>
<script src="/Application/Admin/View/Default/Public/KindEditor/kindeditor-min.js"></script>
<script src="/Application/Admin/View/Default/Public/KindEditor/lang/zh_CN.js"></script>
<script src="/Application/Admin/View/Default/js/function.js?v=<?php echo time();?>"></script><!-- 公共js函数文件 -->
<script>
	KindEditor.ready(function(K) {
		editor = K.create('textarea[name="content"]', {
			allowFileManager: true,
			afterBlur: function(){this.sync();},
		});
	});
</script>
<script language="javascript" type="text/javascript" src="/Application/Admin/View/Default/Public/wdatepicker/WdatePicker.js"></script>
<script>//定义需要的“常量”
var _CONTROLLER_ = '/index.php/Admin/FactoryOrder';
var _ACTION_  = '/index.php/Admin/FactoryOrder/factory';
var _PUBLIC_ = '/Public';
var _TIMESTAMP_ = <?php echo time();?>;
var _SELF_ = '/index.php/Admin/FactoryOrder/factory/type/tb.html';
var _APP_='/index.php';
var _MODULE_ = '/index.php/Admin';
var _ROOT_ = '';

</script>


<link type="image/x-icon" href="http://www.pintuer.com/favicon.ico" rel="shortcut icon" />
<link href="http://www.pintuer.com/favicon.ico" rel="bookmark icon" />
<body>

<!-- 可以选择不加载菜单 -->
<?php if($no_menu != true): ?><div class="lefter">
    <div class="logo"><img src="/Application/Admin/View/Default/images/logo.png" style="height:40px;" alt="后台管理系统" /><b style="font-size: 12px">ERP</b></div>
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
            <span> <a href="/index.php/Admin/messages/Index.html" class="plcz" style=" margin-right:30px;">私信</a> <?php echo ($username); ?> &nbsp;&nbsp; 工号: <?php echo $monoid>0?$monoid:0;?> &nbsp;&nbsp;</span>
            
            
            
            <ul class="bread">
                <li><a href="index.html" class="icon-home"> 开始</a></li>
                <li>后台首页</li>
            </ul>
        </div>
    </div>
</div>
<span class="icon-dedent" style="position: fixed; top: 84px; left: 180px; z-index: 100;color: #09c;cursor: pointer;font-size: 20px;width: 10px;" onclick='show_hide_left_menu(this);'></span><?php endif; ?>
<script src="/Application/Admin/View/Default/js/FactoryOrder/main.js"></script>
<script>
function transform_click(id,pid,code_name)
{
$('#detail_id').val(id+"_"+pid);
//自定页
layer.open({
  type: 1,
  skin: 'layui-layer-demo', //样式类名
  closeBtn: 1, //显示关闭按钮
  shift: 2,
  shadeClose: true, //开启遮罩关闭
  area: ['420px', '240px'], //宽高
  title: code_name,
  content: '<div style=" text-align: center;"> <select class="select" onchange="factory(this.options[this.options.selectedIndex].value)"><option value="">--请选择--</option><?php if(is_array($factory_list)): $i = 0; $__LIST__ = $factory_list;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><option value="<?php echo ($vo["id"]); ?>"><?php echo ($vo["name"]); ?></option><?php endforeach; endif; else: echo "" ;endif; ?></select><span style="font-size:14px;"><input class="plcz" type="button"  onclick="transform_form_submit('+id+')" value="转工厂提交" /> </span></div> '
});	
}
function order_detail_print()
{
	var myform=document.getElementById('myform');
	myform.action="<?php echo U('OrderManage/order_detail_print');?>";
	var input=getByClass(myform,'check');
	for(var i=0;i<input.length;i++)
	{
		input[i].value=input[i].id;
	}
	myform.submit();
}
function add_message(detail_id,name)
{
//自定页
layer.open({
  type: 1,
  skin: 'layui-layer-demo', //样式类名
  closeBtn: 1, //显示关闭按钮
  shift: 2,
  shadeClose: true, //开启遮罩关闭
  area: ['420px', '240px'], //宽高
  title: name,
  content: '<div style=" text-align: center;"><form method="post" name="add_message" id="add_message"> <input type="hidden" value="'+detail_id+'"  name="id"/><textarea name="message" style="width: 400px;height: 150px;"></textarea><br><br><input class="plcz" style="border-radius:0px;" type="button"  onclick="factory_order_detail_message_add()" value="提交" /></form></div> '
});	
}
</script>

<style>
.table td{vertical-align:middle; }
p{ margin:0px;} 
</style>
<div class="admin">
	<div class="remark">
    	
    	<ul class="ul">
        	<li <?php if($sta == 'history_ok'): ?>class="action"<?php endif; ?>>
            	<a href="/index.php/Admin/FactoryOrder/factory/<?php if(isset($type)): ?>type/<?php echo strtolower($type); ?>/<?php endif; ?>sta/history_ok.html"  > 历史订单 <span class="badge bg-dot"><?php echo ($coun_history_ok); ?></span></a>
            </li>
            <li  style="margin-left:30px;">
            
            <input type="text" style="cursor:pointer;" name="beginTime" id="beginTime" class="Wdate bd" onClick="WdatePicker()" value="<?php echo ($btime); ?>"  />
            TO
            <input type="text" style="cursor:pointer;" name="endTime" id="endTime" class="Wdate bd" onClick="WdatePicker()" value="<?php echo ($etime); ?>"  /> 
       	 	<input  type="button" class="but_sub" onclick="factory_order_check_execl()"  style="color:#fff;" value="导出工厂核对单">
            
            </li>
            <div style=" clear:both; height:10px;"></div>
        
        	<li <?php if($sta == 'new'): ?>class="action"<?php endif; ?>>
            	<a href="/index.php/Admin/FactoryOrder/factory/<?php if(isset($type)): ?>type/<?php echo strtolower($type); ?>/<?php endif; ?>sta/new.html"  > 新单生成 <span class="badge bg-dot"><?php echo ($coun_new); ?></span></a>
            </li>
            
            <li <?php if($sta == 'accept'): ?>class="action"<?php endif; ?>>
            	<a href="/index.php/Admin/FactoryOrder/factory/<?php if(isset($type)): ?>type/<?php echo strtolower($type); ?>/<?php endif; ?>sta/accept.html" >已接单 <span class="badge remark_bg"><?php echo ($coun_accept); ?></span></a>
            </li>
            
            <li <?php if($sta == 'shipping'): ?>class="action"<?php endif; ?>>
            	<a href="/index.php/Admin/FactoryOrder/factory/<?php if(isset($type)): ?>type/<?php echo strtolower($type); ?>/<?php endif; ?>sta/shipping.html" >正在派送 <span class="badge remark_bg"><?php echo ($coun_shipping); ?></span></a>
            </li>
            
            <li <?php if($sta == 'history'): ?>class="action"<?php endif; ?>>
            	<a href="/index.php/Admin/FactoryOrder/factory/<?php if(isset($type)): ?>type/<?php echo strtolower($type); ?>/<?php endif; ?>sta/history.html" >已收货 <span class="badge remark_bg"><?php echo ($coun_history); ?></span></a>
            </li>
            <li <?php if($detail_sta == 'cancel'): ?>class="action"<?php endif; ?>>
            	<a href="/index.php/Admin/FactoryOrder/factory/<?php if(isset($type)): ?>type/<?php echo strtolower($type); ?>/<?php endif; ?>detail_sta/cancel.html" >已取消 <span class="badge remark_bg"><?php echo ($coun_cancel); ?></span></a>
            </li>
            <?php if(in_array(($sta), explode(',',"shipping,history,history_ok"))): ?><li>
                    <input type="text" style="width:240px; height:24px;" name="delivery_number" class='bd' id="delivery_number" placeholder='请输入快递单号'  value="<?php echo ($delivery_number); ?>" /><span onclick="delivery_number_sub()"  class="plcz" style="cursor:pointer; position: relative;right:45px;border-radius: 0px 4px 4px 0px;">筛选</span>
                </li><?php endif; ?>
         </ul>
         
        <div style=" clear:both;" ></div>
        
        <br> 
        <p style="margin-left:20px; font-size:12px;">
            <input type="text" style="width:240px; height:24px;    margin-bottom: 10px;" name="order_num" class='bd' id="order_num" placeholder='请输入交易单号'  value="<?php echo ($order_num); ?>" /><span onclick="order_num_sub()"  class="plcz" style="cursor:pointer; position: relative;right:45px;border-radius: 0px 4px 4px 0px;">筛选</span>
            <input type="text" style="width:240px; height:24px;" name="fac_num" class='bd' id="fac_num" placeholder='请输入工厂订单号'  value="<?php echo ($fac_num); ?>" /><span onclick="fac_num_sub()" class="plcz"  style="cursor:pointer; position: relative;right:45px;border-radius: 0px 4px 4px 0px;">筛选</span>
        </p>    
    </div>
	<form  method="post" name="form" id="myform">
    <input type="hidden" value="<?php echo ($input_sta); ?>"  name="sta"/>
    <input type="hidden" value="<?php echo ($sta); ?>"  name="execl_sta" id="execl_sta"/>
    <input type="hidden" value="<?php echo ($type); ?>"  name="factory_type" id="factory_type"/>
    <input type="hidden" name="factory_list" id="factory_list" />
    <input type="hidden" name="delivery" id="delivery" value="顺丰"/>
    <input type="hidden" name="delivery_num" id="delivery_num" />
    <input type="hidden" name="id" id="id" />
    <input type="hidden" name="detail_id"  id="detail_id"/>
    
    <div class="tab-panel" id="tab-not">
         <table class="table  table-striped table-hover table-condensed" width="98%" border="0" cellpadding="5" cellspacing="1" id="come_dp">
            <tr>
            <td colspan="12" class="table_title tab_title">
                <span class="fl icon-align-justify"> 工厂订单管理</span>
                <?php if($sta != 'history_ok'): ?><span style="font-size:14px;"><a onclick="select_all()" class="icon-arrows-v margin-left" href="#"> 全选 </a></span>
                    <?php if($sta != 'get'): ?><span style="font-size:14px;"><input class="plcz" type="button"  onclick="plcz_form_submit('<?php echo ($input_sta); ?>','<?php echo ($type); ?>')" value="批量操作" /> </span><?php endif; ?>
                    <?php if($sta == 'new'): ?><span style="font-size:14px;"><input class="plcz" type="button"  onclick="transform_click()" value="转工厂提交" /> </span><?php endif; ?>
                     <span style="font-size:14px;"><input class="plcz" type="button"  onclick="order_detail_print()" value="打印商品详情单" /> </span><?php endif; ?>
                <span class="fr">
                	<input class="plcz" type="button"  onclick="execl_export()"  value="导出订单" />
                </span>
            </td>
            <tr class="list_head ct">
           	    <?php if($sta != 'history_ok'): ?><td width="2%"></td><?php endif; ?>
                <td align='left'></td>
                <td align='left'>交易单号</td>
                <td align='left' style=" min-width:100px;">工厂单号</td>
                <td align='left'   style="   min-width: 120px;">关联工厂单号</td>
                <td align='left'>来源</td>
                <?php if(in_array(($sta), explode(',',"history,shipping,history_ok,get"))): ?><td align='center' width="6%">快递信息</td><?php endif; ?>
                <td align='center'>提交时间</td>
                	<?php if($sta != 'new'): ?><td align='center' width="13%"><?php if($sta == 'shipping'): ?>发货时间<?php elseif($sta == 'history_ok'): ?>发货时间<?php else: ?>接收时间<?php endif; ?></td><?php endif; ?>
                <td align='left' style="min-width:420px;">备注 </td>
                <td style="min-width: 70px; max-width:70px;">状态</td>
               		 <?php if($sta != 'history_ok'): ?><td align='center' width="6%">管理操作</td><?php endif; ?>
            </tr>
        <?php if(is_array($info)): $a = 0; $__LIST__ = $info;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($a % 2 );++$a;?><tr>
            	<?php if($sta != 'history_ok'): ?><td><input class="check" name="check[]" type="checkbox" value="<?php echo ($vo["id"]); ?>" id="<?php echo ($vo["come_from"]); ?>_<?php if($vo["order_id"] != 0): echo ($vo["order_id"]); else: echo ($vo["order_platform_id"]); endif; ?>"></td><?php endif; ?>
                <td align='left'><?php echo ($a); ?></td>
                <td  class="td_ff">
                	<a href="<?php echo ($vo["url"]); ?>" style=" color:#03F; text-decoration: underline;" target="_blank">
                    <?php echo ($vo["order_number"]); ?>
                    </a>
                    <div style=" margin-top:3px;">
                    	<?php
 if($vo['order_id']) { $product_extra = product_extra($vo['order_id'],"<br>"); echo '<div>'.$product_extra.'</div>'; if(panduan_sample($vo['order_id'],$vo['come_from_id'])) { echo '<div>有样布</div>'; } echo '<div>'.payment_style($vo['order_id']).'</div>'; echo '<div>'.product_customization($vo['order_id']).'</div>'; $num = all_order_number($vo['order_id'],'web'); if($num != 1 && $num != 0) { echo '<div>该用户还有其他'.($num-1).'条订单</div>'; } if(order_cancel($vo['order_id'],'web')) { echo '<div>有取消订单</div>'; } $malicious_user = malicious_user($vo['order_id']); if($malicious_user) { echo '<div>'.$malicious_user.'</div>'; } } if($vo['order_platform_id']) { $num = all_order_number($vo['order_platform_id'],'plat'); if( $num!= 1 && $num != 0) { echo '<div>该用户还有其他'.( $num-1).'条订单</div>'; } if(order_cancel($vo['order_platform_id'],'plat')) { echo '<div>有取消订单</div>'; } } ?>
                    </div>
                </td>
                <td  class="td_ff">
                	<?php echo ($vo["factory_number"]); ?>
                </td>
                <td  class="td_ff">
                    <?php if($vo['order_id'] !='0'): echo associated_fac($vo['id'],$vo['order_id'],'fac',0,'<br>','web','YES');?>
                    <?php elseif($vo['order_platform_id'] !='0'): ?>
                    	<?php echo associated_fac($vo['id'],$vo['order_platform_id'],'fac',0,'<br>','plat','YES'); endif; ?>
                </td>
                <td><?php echo ($vo["come_from"]); ?></td>
                <?php if(in_array(($sta), explode(',',"history,shipping,history_ok,get"))): ?><td><?php echo ($vo["delivery_style"]); ?><br><?php echo ($vo["delivery_number"]); ?></td><?php endif; ?>
                <td align="center"><?php echo ($vo["date"]); ?> <?php echo ($vo["date_detail"]); ?></td>
                	<?php if($sta != 'new'): ?><td align="center">
                            <?php if($sta == 'history'): echo (date('Y-m-d H:i:s',$vo["history_time"])); ?>
                            <?php elseif($sta == 'shipping'): ?>
                                <?php echo (date('Y-m-d H:i:s',$vo["shipping_time"])); ?>
                            <?php elseif($sta == 'accept'): ?>
                                <?php echo (date('Y-m-d H:i:s',$vo["accept_time"])); ?>
                            <?php elseif($sta == 'history_ok'): ?>
                                <?php echo (date('Y-m-d H:i:s',$vo["ok_time"])); endif; ?>
                            
                            <?php if($sta == 'get'): if($vo['status'] == 'history'): echo (date('Y-m-d H:i:s',$vo["history_time"])); ?>
                                <?php elseif($vo['status'] == 'shipping'): ?>
                                    <?php echo (date('Y-m-d H:i:s',$vo["shipping_time"])); ?>
                                <?php elseif($vo['status'] == 'accept'): ?>
                                    <?php echo (date('Y-m-d H:i:s',$vo["accept_time"])); ?>
                                <?php elseif($vo['status'] == 'history_ok'): ?>
                                    <?php echo (date('Y-m-d H:i:s',$vo["ok_time"])); ?>   
                                <?php elseif($vo['status'] == 'new'): ?>
                                    新单<?php endif; endif; endif; ?>
                            
                        </td>
                    </if>
                    
                    
                <td class="td_cc">
                     <?php if(is_array($vo["detail"])): $k = 0; $__LIST__ = $vo["detail"];if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo2): $mod = ($k % 2 );++$k; if(($vo['factory_id'] != '7') and ($vo['factory_id'] != '8')): ?><!--非定制产品-->
                            <?php if($k > 1): if($k <= $vo["coun"] ): ?><hr /><?php endif; endif; ?>
                            <?php echo ($vo2["code"]); ?>--<span style="color:red"><?php echo ($vo2["code_name"]); ?></span> <span class="icon-times"></span> <span style="color:red;font-weight:bold;"><?php echo ($vo2["number"]); ?></span>
                            <?php if($vo2["status"] == 'cancel'): ?><strong style="color:red;margin-left:10px;">已取消</strong><?php endif; ?>
                            <?php if($vo2["description"] != ""): ?><hr /> 
                                 <span style=" color:#000;font-size:10px;">备注：</span>
                                 <span style="color:red;font-size:10px;"><?php echo ($vo2["description"]); ?></span><?php endif; ?>
                         <?php else: ?>  <!--定制产品-->
                          	<?php if($k > 1): if($k <= $vo["coun"] ): ?><hr /><?php endif; endif; ?>
                                数量 ：<span style="color:red; font-weight: bold; font-size:20px;"><?php echo ($vo2["number"]); ?></span>
                                <?php if($vo2["status"] == 'cancel'): ?><strong style="color:red;margin-left:10px;">已取消</strong><?php endif; ?>
                                <br />
                                <span style=" color:#000;font-size:10px;">备注：</span>
                                <br />
                                <span style="color:red;font-size:10px;"><?php echo ($vo2["description"]); ?></span><?php endif; ?>
                         <?php  if($vo2['code_name']) { $name = $vo2['code_name']; } else { $name ="定制产品 ".$vo['order_number'] ; } ?>
                         <?php if($vo['status'] == 'new'): ?><span  onclick="transform_click('<?php echo ($vo2["id"]); ?>','<?php echo ($vo["id"]); ?>','<?php echo ($vo2["code_name"]); ?>')"  class="fr icon-mail-forward cur" style=" margin-left:10px;" title="转工厂"></span><?php endif; ?>
                         <span class="fr icon-plus cur" title="添加留言" onclick="add_message('<?php echo ($vo2["id"]); ?>','<?php echo ($name); ?>')"></span>
                         	<?php if($vo2["message"] ): ?><br />
                                <p style=" font-weight: bold; color:#000;font-size:10px;">特别注意：</p> <!-- 生成工厂订单号可以添加的备注-->
                                <div  style="border:1px solid #999;">
                                    <?php if(is_array($vo2['message'])): $i = 0; $__LIST__ = $vo2['message'];if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$mes_vo): $mod = ($i % 2 );++$i;?><p style="color:#000;font-size:10px; padding-left:5px;"> <?php echo ($mes_vo["message"]); ?> - <?php echo username_name($mes_vo['operator']);?> - <span style="color:#390;"><?php echo (date("Y-m-d H:i:s",$mes_vo["time"])); ?></span></p><?php endforeach; endif; else: echo "" ;endif; ?>
                                </div><?php endif; endforeach; endif; else: echo "" ;endif; ?>
                     <!--订单商家留言-->
                    <?php  if($vo['order_id']) { $message_seller_web = message_seller($vo['order_id'],'web','<br>'); if($message_seller_web) { echo '<hr><p style=" font-weight: bold; color:#000;font-size:10px;margin:0px;">订单商家留言：</p>'; } } if($vo['order_platform_id']) { $message_seller_plat = message_seller($vo['order_platform_id'],'plat','<br>'); if($message_seller_plat) { echo '<hr><p style=" font-weight: bold; color:#000;font-size:10px;margin:0px;">订单商家留言：</p>'; } } if($vo['order_id']) { echo '<p style="color:#000;font-size:10px; padding-left:5px;margin:0px; overflow: auto; max-width: 420px;">'.$message_seller_web.'</p>'; } if($vo['order_platform_id']) { echo '<p style="color:#000;font-size:10px; padding-left:5px;margin:0px;overflow: auto; max-width: 420px;">'.$message_seller_plat.'</p>'; } ?>  
              </td>
              <td align="center" style=" font-weight:bold; color:#F00;"><?php echo factory_status($vo['status']);?></td>
              <?php $is_true=factory_all_cancel($vo["id"],$vo["is_come"]); ?>
              <?php $fac_sta = fac_sta($vo['status']) ;?>
                <?php if($vo["status"] != 'history_ok'): ?><td align="center">
                        <?php if($sta != 'get' && $detail_sta != 'cancel'): if($sta != 'shipping'): ?><a onclick="factory_sta('<?php echo ($vo['id']); ?>','<?php echo ($input_sta); ?>','<?php echo ($type); ?>')"  class="icon-check cur" title="<?php echo ($input_sta); ?>"></a><?php endif; ?>
                            
                            <?php if($sta == 'shipping'): ?><a href="<?php echo U('/Admin/FactoryOrder/factory_sta/',array('id'=>$vo['id'],'sta'=>history));?>" class="icon-check cur" title='history' ></a><?php endif; ?>       
                        <?php else: ?>
                        	<?php if($fac_sta != 'history'): ?><a onclick="factory_sta('<?php echo ($vo['id']); ?>','<?php echo ($fac_sta); ?>','<?php echo ($type); ?>')"  class="icon-check cur" title="<?php echo ($fac_sta); ?>"></a><?php endif; ?>
                            
                            <?php if($fac_sta == 'history'): ?><a href="<?php echo U('/Admin/FactoryOrder/factory_sta/',array('id'=>$vo['id'],'sta'=>$fac_sta));?>" class="icon-check cur" title='history' ></a><?php endif; endif; ?>    
           				<?php if($sta == 'accept'): if($is_true == 'yes'): ?><a onclick="factory_sta('<?php echo ($vo["id"]); ?>','history_ok','<?php echo ($type); ?>')" style="cursor:pointer;">转入历史</a><?php endif; endif; ?>     
                    </td>
                <?php else: ?>
                	<td align="center"></td><?php endif; ?>    
            </tr><?php endforeach; endif; else: echo "" ;endif; ?>
        </table>
     </div>
    
     </form>
     <div class="list-page">
     	<?php if(empty($page)): ?>暂时没有相关数据<?php else: echo ($page); endif; ?>
     </div>

</div>

</body>
</html>