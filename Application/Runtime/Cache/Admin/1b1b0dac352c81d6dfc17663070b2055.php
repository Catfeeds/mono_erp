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
var _CONTROLLER_ = '/erptest/index.php/Admin/DeliveryNumber';
var _ACTION_  = '/erptest/index.php/Admin/DeliveryNumber/new_number';
var _PUBLIC_ = '/erptest/Public';
var _TIMESTAMP_ = <?php echo time();?>;
var _SELF_ = '/erptest/index.php/Admin/DeliveryNumber/new_number';
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
<script src="/erptest/Application/Admin/View/Default/js/DeliveryNumber/main.js"></script>
<script>
function handleFile(){
	var full_name = $("#file").attr("value");
	var arr = full_name.split('\\');
	var file_name = arr[arr.length-1];
	$("#file_name").html(file_name);
}
</script>
<style>
.a{ text-decoration: underline;color: blueviolet;}

</style>
<div class="admin">
    
    <div class="clear" ></div>
 	<form method="post" name="form" id="myform">
    		<input type="hidden" name="data"   value="<?php echo ($data); ?>"/>
            <input type="hidden" name="style" value="<?php echo ($style); ?>" />
         <span>发货时间区间：
            <input type="text" style="cursor:pointer;" name="beginTime" id="beginTime" class="Wdate bd" onClick="WdatePicker()" value="<?php echo ($btime); ?>"  />
            <input type="text" style="cursor:pointer;" name="endTime" id="endTime" class="Wdate bd" onClick="WdatePicker()" value="<?php echo ($etime); ?>"  /> 
       	 	<input  type="button" class="but_sub" onclick="time_sub()" value="提交">
            <span style=" color:red; font-weight:bold; font-size:20px;"><?php echo ($list_coun); ?>条数据</span>
        </span>
    </form>       
	<div style="width:100%; height:20px;"></div>

    <div class="tab-panel" id="tab-not">
         <table class="table  table-striped table-hover table-condensed" width="98%" border="0" cellpadding="5" cellspacing="1" id="come_dp">
            <tr>
            <td colspan="11" class="table_title tab_title">
                <span class="fl icon-align-justify"> <?php echo ($tpltitle); ?></span>
                <span style=" margin-left:20px;"></span>
                <a href="<?php echo ($one_url); ?>" <?php if($style == ''): ?>style="color: #0a8;"<?php endif; ?> class="plcz"> 全部</a>
                <a href="<?php echo ($DHL_url); ?>" <?php if($style == 'DHL'): ?>style="color: #0a8;"<?php endif; ?> class="plcz"> DHL</a>
                <a href="<?php echo ($Fedex_url); ?>" <?php if($style == 'Fedex'): ?>style="color: #0a8;"<?php endif; ?> class="plcz"> Fedex</a>
                <a href="<?php echo ($UPS_url); ?>" <?php if($style == 'UPS'): ?>style="color: #0a8;"<?php endif; ?> class="plcz"> UPS</a>
                <a href="<?php echo ($TNT_url); ?>" <?php if($style == 'TNT'): ?>style="color: #0a8;"<?php endif; ?> class="plcz"> TNT</a>
                
                <a href="<?php echo ($UPS_HD_url); ?>" <?php if($style == 'UPS_HD'): ?>style="color: #0a8;"<?php endif; ?> class="plcz"> UPS货代 </a>
                <a href="<?php echo ($SF_url); ?>" <?php if($style == 'SF'): ?>style="color: #0a8;"<?php endif; ?> class="plcz"> 顺丰 </a>
                <span class="fl icon-align-justify" style="margin-left:20px;"> 订单管理</span>
                
                <a href="<?php echo ($data_url); ?>" <?php if($data == ''): ?>style="color: #0a8;"<?php endif; ?> class="plcz"> 全部</a>
                <a href="<?php echo ($web_url); ?>" <?php if($data == 'web'): ?>style="color: #0a8;"<?php endif; ?> class="plcz"> 网站订单</a>
                <a href="<?php echo ($platform_url); ?>" <?php if($data == 'plat' ): ?>style="color: #0a8;"<?php endif; ?> class="plcz"> 平台订单 </a>
               
                <span class="fr plcz" onclick="execl_sub()"> 导出筛选 </span><br />
                <form  enctype="multipart/form-data" class="form-x margin-large-top " action="/erptest/index.php/Admin/DeliveryNumber/import_order.html" method="post">
					<div class="form-group">
						<div class="label" style=" width:85px;">
							<label>上传文件：</label>
						</div>
						<div class="field">
							<a class="button input-file" style="float:left;">
								+ 浏览文件
								<input type="file" onchange="handleFile()" id="file" name="file" size="100">
								<span style="margin-left:10px;" id="file_name">&nbsp;&nbsp;</span>
							</a>
                            <button  style="float:left; margin-left:20px;" type="submit" class="button border-black padding-small">批量导入</button>
						</div>
					</div>
				</form>
            </td>
            <tr class="list_head ct">
            	<td align="left"></td>
                <td align="left" >订单号</td>
                <td align="left" >订单日期</td>
                <td >跟单号</td>
                <td >快递方式</td>
                <td >发货日期</td>
                <td >重量(kg)</td>
                <td >收件人</td>
                <td >邮箱</td>
                <td >电话</td>
                <td width="15%">工厂订单编号</td>
            </tr>
        <?php if(is_array($info)): $k = 0; $__LIST__ = $info;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($k % 2 );++$k;?><tr>
            	<td class="td_number"><!--<input class="check" name="check[]" type="checkbox" value="<?php echo ($vo["id"]); ?>" > --><?php echo ($k); ?> </td>
                <td class="td_number">
                	<a href="<?php echo ($vo["url"]); ?>" target="_blank" class="a" >
                    	<?php echo ($vo['come_from_name']); echo ($vo["order_number"]); ?>
                    </a>
                </td>
                <td><?php echo ($vo["date_time"]); ?></td>
                <td align="center" class="td_ff"><?php echo ($vo["delivery_number"]); ?></td>
                <td align="center" class="td_ff"><?php echo ($vo["style"]); ?></td>
                <td align="center"><?php echo ($vo["time"]); ?></td>
                <td class="td_cc"><?php echo ($vo["weight"]); ?></td>
                <td><?php echo ($vo["name"]); ?></td>
                <td><?php echo ($vo["email"]); ?></td>
                <td align="center"><?php echo ($vo["phone"]); ?></td>
                <td><?php echo ($vo["factory_num"]); ?></td>
            </tr><?php endforeach; endif; else: echo "" ;endif; ?>
        </table>
     </div>
     <div class="list-page">
     	<?php if(empty($page)): ?>暂时没有相关数据<?php else: echo ($page); endif; ?>
     </div>
</div>

</body>
</html>