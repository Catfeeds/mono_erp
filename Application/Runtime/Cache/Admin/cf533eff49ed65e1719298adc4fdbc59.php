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
var _CONTROLLER_ = '/erptest/index.php/Admin/FactoryManage';
var _ACTION_  = '/erptest/index.php/Admin/FactoryManage/StockOrder';
var _PUBLIC_ = '/erptest/Public';
var _TIMESTAMP_ = <?php echo time();?>;
var _SELF_ = '/erptest/index.php/Admin/FactoryManage/StockOrder.html';
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
<script src="/erptest/Application/Admin/View/Default/js/FactoryManage/main.js"></script>
<style>
.table td{vertical-align:middle; } 
</style>
<script>
function transform_click()
{
//自定页
layer.open({
  type: 1,
  skin: 'layui-layer-demo', //样式类名
  closeBtn: 1, //显示关闭按钮
  shift: 2,
  shadeClose: true, //开启遮罩关闭
  area: ['420px', '240px'], //宽高
  content: '<div style=" text-align: center;"> <select class="select" onchange="factory(this.options[this.options.selectedIndex].value)"><option value="">--请选择--</option><?php if(is_array($factory_list)): $i = 0; $__LIST__ = $factory_list;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><option value="<?php echo ($vo["id"]); ?>"><?php echo ($vo["name"]); ?></option><?php endforeach; endif; else: echo "" ;endif; ?></select><span style="font-size:14px;"><input class="plcz" type="button"  onclick="transform_form_submit()" value="转工厂提交" /> </span></div> '
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
</script>
<div class="admin">
	<div class="remark">
    	<ul class="ul">
        	<li <?php if($sta == 'new'): ?>class="action"<?php endif; ?>>
            	<a href="/erptest/index.php/Admin/FactoryManage/StockOrder/sta/new.html" > 新单 <span class="badge bg-dot"><?php echo ($list_new_coun); ?></span></a>
            </li>
          	<li <?php if($sta == 'history'): ?>class="action"<?php endif; ?>>
            	<a href="/erptest/index.php/Admin/FactoryManage/StockOrder/sta/history.html" > 历史订单 <span class="badge remark_bg"><?php echo ($list_history_coun); ?></span></a>
            </li>
            <li <?php if($detail_sta == 'cancel'): ?>class="action"<?php endif; ?>>
            	<a href="/erptest/index.php/Admin/FactoryManage/StockOrder/detail_sta/cancel.html" >已取消 <span class="badge remark_bg"><?php echo ($coun_cancel); ?></span></a>
            </li>    
            <li <?php if($data == 'web'): ?>class="action"<?php endif; ?>>
            	<a href="/erptest/index.php/Admin/FactoryManage/StockOrder/data/web.html" > 网站 <span class="badge bg-dot"><?php echo ($list_web_coun); ?></span></a>
            </li>
          	<li <?php if($data == 'plat'): ?>class="action"<?php endif; ?>>
            	<a href="/erptest/index.php/Admin/FactoryManage/StockOrder/data/plat.html" > 平台 <span class="badge remark_bg"><?php echo ($list_plat_coun); ?></span></a>
            </li>
        </ul>
        <div style=" clear:both;" ></div>
        <br> 
        <p style="margin-left:20px; font-size:12px;">
        	<input type="text" style="width:240px; height:24px;" name="order_num" class='bd' id="order_num" placeholder='请输入交易单号'  value="<?php echo ($order_num); ?>" /><span onclick="order_num_sub('StockOrder')"  class="plcz" style="cursor:pointer; position: relative;right:45px;border-radius: 0px 4px 4px 0px;">筛选</span>
            
        </p>      
    </div>
	<form  method="post" name="form" id="myform">
    <input type="hidden" value="<?php echo ($input_sta); ?>"  name="sta"/>
    <input type="hidden" value="<?php echo ($sta); ?>"  name="execl_sta"/>
    <input type="hidden" value="<?php echo ($type); ?>"  name="factory_type"/>
    <input type="hidden" name="factory_list" id="factory_list" />
    <div class="tab-panel" id="tab-not">
         <table class="table  table-striped table-hover table-condensed" width="98%" border="0" cellpadding="5" cellspacing="1" id="come_dp">
            <tr>
            <td colspan="13" class="table_title tab_title">
                <span class="fl icon-align-justify"> 库存订单管理</span>
                <span style="font-size:14px;"><a onclick="select_all()" class="icon-arrows-v margin-left" href="#"> 全选 </a></span>
                <?php if($sta == 'new'): ?><span style="font-size:14px;"><input class="plcz" type="button"  onclick="transform_click()" value="转工厂提交" /> </span><?php endif; ?>
                <?php if($sta != 'history'): ?><span style="font-size:14px;"><input class="plcz" type="button"  onclick="Stock_rder_form_submit()" value="批量转历史" /> </span><?php endif; ?>
                <span style="font-size:14px;"><input class="plcz" type="button"  onclick="order_detail_print()" value="打印商品详情单" /> </span>
                
                <span style="font-size:14px;"><a href="/erptest/index.php/Admin/FactoryManage/StockOrder/<?php if(!$screening): ?>screening/1<?php endif; ?>.html" <?php if($screening): ?>style="    background: goldenrod;"<?php endif; ?> class="plcz"> 筛选可以发货订单</a>
                
                <span class="fr">
                	<input class="plcz" type="button"  onclick="execl_export_Stock()"  value="导出订单" />
                </span>
            </td>
            <tr class="list_head ct">
            	<td width="2%"></td>
                <td width="2%"></td>
                <td align='left'>交易单号</td>
                <td align='left' style="min-width: 100px;">工厂单号</td>
                <td align='left' style="min-width: 120px;">关联工厂单号</td>
                <td align='left' >来源</td>
                <td align='center'>订单时间</td>
                <td align='center'>提交时间</td>
                <td align='left' >备注</td>
                <td align='left' style=" width:5%;" >客户留言</td>
                <td align='left' style=" width:5%;" >商家留言</td>
                <td  style="min-width: 70px; max-width:70px;">状态</td>
                <td align='center' >管理操作</td>
            </tr>   
        <?php if(is_array($info)): $a = 0; $__LIST__ = $info;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($a % 2 );++$a;?><tr>
                <td>
                <?php if($vo['status'] != 'history'): ?><input class="check" name="check[]" type="checkbox" value="<?php echo ($vo["id"]); ?>" id="<?php echo ($vo["come_from"]); ?>_<?php if($vo["order_id"] != 0): echo ($vo["order_id"]); else: echo ($vo["order_platform_id"]); endif; ?>"><?php endif; ?>
                </td>
                <td width="2%"><?php echo ($a); ?></td>
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
                 <?php echo ($type); echo ($vo["order"]); echo (date('m.d',strtotime($vo["date"]))); ?>-<?php if($vo["number"] < 10): ?>0<?php endif; echo ($vo["number"]); ?>
                </td>
                 <td  class="td_ff">
                    <?php if($vo['order_id'] !='0'): echo associated_fac($vo['id'],$vo['order_id'],'bd',0,'<br>','web','YES');?>
                    <?php elseif($vo['order_platform_id'] !='0'): ?>
                    	<?php echo associated_fac($vo['id'],$vo['order_platform_id'],'bd',0,'<br>','plat','YES'); endif; ?>
                </td>
                <td><?php echo ($vo["come_from"]); ?></td>
                <td align="center"><?php echo ($vo["date_time"]); ?></td>
                <td align="center"><?php echo ($vo["date"]); ?> <?php echo ($vo["date_detail"]); ?></td>
                <td class="td_cc">
                     <?php if(is_array($vo["detail_info"])): $k = 0; $__LIST__ = $vo["detail_info"];if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo2): $mod = ($k % 2 );++$k; if($k > 1): if($k <= $vo["coun"] ): ?><hr /><?php endif; endif; ?>
                        <?php echo ($vo2["code"]); ?>--<span style="color:red"><?php echo code_val($vo2['code']);?></span> <span class="icon-times"></span> <span style="color:red;font-weight:bold;"><?php echo ($vo2["number"]); ?></span>
                         <?php if($vo2["status"] == 'cancel'): ?><strong style="color:red;margin-left:10px;">已取消</strong><?php endif; ?>
                       <?php if($vo2["description"] != ""): ?><hr /> <span style=" color:red; margin-left:17px; font-size:10px;">备注：<?php echo ($vo2["description"]); ?></span><?php endif; endforeach; endif; else: echo "" ;endif; ?>
                 </td>
                 <td align='left' ><?php echo ($vo["message_buy"]); ?></td>
                 <td align='left' ><?php echo ($vo["message_seller"]); ?></td>
                 <td align="center" style=" font-weight:bold; color:#F00;"><?php echo factory_status($vo['status'],1);?></td>
                 <td align="center">
                    <?php if($vo['status'] != 'history'): ?><a href="<?php echo U('/Admin/FactoryManage/StockOrder_sta/',array('id'=>$vo['id'] ,'sta'=>'history'));?>" class="icon-long-arrow-right" title="转为历史"></a><?php endif; ?>
                 </td>
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