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
var _CONTROLLER_ = '/erptest/index.php/Admin/FactoryOrder';
var _ACTION_  = '/erptest/index.php/Admin/FactoryOrder/not_arrival_order';
var _PUBLIC_ = '/erptest/Public';
var _TIMESTAMP_ = <?php echo time();?>;
var _SELF_ = '/erptest/index.php/Admin/FactoryOrder/not_arrival_order/p/2.html';
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
<script src="/erptest/Application/Admin/View/Default/js/FactoryOrder/main.js"></script>
<style>
.table td{vertical-align:middle; } 
.tab td{ border:1px solid #ccc;}
</style>
<div class="admin">
	<div class="remark">
    	<ul class="ul">
         	<li <?php if($factory == ''): ?>class="action"<?php endif; ?>>
                    <a href="/erptest/index.php/Admin/FactoryOrder/not_arrival_order.html" >全部  <span class="badge bg-dot"><?php echo ($num_all); ?></span></a>
            </li>
        	<?php if(is_array($factory_list)): $i = 0; $__LIST__ = $factory_list;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><li <?php if($factory == $vo['val'] ): ?>class="action"<?php endif; ?>>
                    <a href="/erptest/index.php/Admin/FactoryOrder/not_arrival_order/factory/<?php echo ($vo["val"]); ?>.html" ><?php echo strtoupper($vo['val']); ?>  <span class="badge bg-dot"><?php echo ($vo["num"]); ?></span></a>
                </li><?php endforeach; endif; else: echo "" ;endif; ?>
            <li>
            <span>查询区间</span>
                <input type="text" style="cursor:pointer;"  id="btime" class="Wdate bd" onClick="WdatePicker()" value="<?php echo ($btime); ?>"  />
                <input type="text" style="cursor:pointer;"  id="etime" class="Wdate bd" onClick="WdatePicker()" value="<?php echo ($etime); ?>"  />
            <span onclick="not_arrival_submit('<?php echo strtoupper($factory);?>')" class="plcz">筛选</span>
            <span class="calculate" style="color:red; font-size:20px; font-weight:bold;"></span>   
            </li>    
        </ul>
        <div style=" clear:both;" ></div>
        <br> 
        <p style="margin-left:20px; font-size:12px;">
            <input type="text" style="width:240px; height:24px;" name="order_num" class='bd' id="order_num" placeholder='请输入交易单号'  value="<?php echo ($order_num); ?>" /><span onclick="order_num_sub('not')"  class="plcz" style="cursor:pointer; position: relative;right:45px;border-radius: 0px 4px 4px 0px;">筛选</span>
            <input type="text" style="width:240px; height:24px;" name="fac_num" class='bd' id="fac_num" placeholder='请输入工厂订单号'  value="<?php echo ($fac_num); ?>" /><span onclick="fac_num_sub('not')" class="plcz"  style="cursor:pointer; position: relative;right:45px;border-radius: 0px 4px 4px 0px;">筛选</span>
        </p>      
    </div>
	<form  method="post" name="form" id="myform">
    <input type="hidden" value="<?php echo ($factory); ?>"  name="factory" id="factory"/>
    <input type="hidden" value="<?php echo ($btime); ?>"   name="beginTime"/>
    <input type="hidden" value="<?php echo ($etime); ?>"   name="endTime"/>
    <div class="tab-panel" id="tab-not">
         <table class="table  table-striped table-hover table-condensed" width="98%" border="0" cellpadding="5" cellspacing="1" id="come_dp">
            <tr>
            <td colspan="8" class="table_title tab_title">
                <span class="fl icon-align-justify"> 工厂订单管理</span>
                <span style="font-size:14px;"><a onclick="select_all()" class="icon-arrows-v margin-left" href="#"> 全选 </a></span>
                <span style="font-size:14px;"><input class="plcz" type="button"  onclick="export_order()" value="导出选中订单" //span>
                <span class="fr" style="font-size:14px;"><input class="plcz" type="button"  onclick="screening_execl()" value="导出筛选订单" /> </span>
			</td>
            <tr class="list_head ct">
            	<td width="2%"></td>
                <td align='left'>交易单号</td>
                <td align='left'>工厂单号</td>
                <td align='left'>来源</td>
                <!--<td align='left'>产品名称</td>-->
                <td align='center'>提交时间</td>
                <td align='left'>产品详情</td>
                <td align='left'  style="width: 40%">备注</td>
            </tr>
        <?php if(is_array($info)): $k = 0; $__LIST__ = $info;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($k % 2 );++$k;?><tr>
                <td><input class="check" name="check[]" type="checkbox" value="<?php echo ($vo["id"]); ?>" ></td>
                 <td  class="td_ff">
                	<a href="<?php echo ($vo["url"]); ?>" style=" color:#03F; text-decoration: underline;" target="_blank">
                    <?php echo ($vo["order_number"]); ?>
                    </a>
                </td>
                <td  class="td_ff">
                	<?php echo ($vo["fac_num"]); ?>
                </td>
                <td><?php echo ($vo["come_from"]); ?></td>
                <td align="center"><?php echo ($vo["date"]); ?></td>
                
                <td class="td_cc">
                	<table>
                    <?php if(($vo['factory_id'] != '7') and ($vo['factory_id'] != '8')): if(is_array($vo["detail"])): $k = 0; $__LIST__ = $vo["detail"];if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo2): $mod = ($k % 2 );++$k; if($k > 1): if($k <= $vo["coun"] ): ?><hr /><?php endif; endif; ?>
                            <P style="margin:0;"><?php echo ($vo2["code"]); ?>--<span style=" color:#000;"><?php echo ($vo2["code_name"]); ?></span> <span class="icon-times"></span> <span style="color:red;font-weight:bold;"><?php echo ($vo2["number"]); ?></span>
                            <?php if($vo2["status"] == 'cancel'): ?><strong style="color:red;margin-left:10px;">已取消</strong><?php endif; ?>
                            </P><?php endforeach; endif; else: echo "" ;endif; ?>
                    <?php else: ?>
                    	定制产品  <?php if($vo2["status"] == 'cancel'): ?><strong style="color:red;margin-left:10px;">已取消</strong><?php endif; endif; ?>
                    </table> 
             	</td>
                <!--<td class="td_cc">
                	<table style="border:1px solid #eee;" class="tab">
                     <?php if(is_array($vo["detail"])): $k = 0; $__LIST__ = $vo["detail"];if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo2): $mod = ($k % 2 );++$k;?><tr >
                        	<td>
                            	<span style=" color:red;"><?php echo ($vo2["code_name"]); ?></span> 
                            </td>
                            <td>
                            	<?php echo ($vo2["size"]); ?>
                            </td>
                            <td>
                            	<?php echo ($vo2["color"]); ?>
                            </td>
                             <td>
                            	<?php echo ($vo2["number"]); ?>
                            </td>
                        	
                        </tr><?php endforeach; endif; else: echo "" ;endif; ?>
                    </table> 
             	</td>-->
                
                <td align="left">
                	<?php if(is_array($vo["detail"])): $k = 0; $__LIST__ = $vo["detail"];if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo2): $mod = ($k % 2 );++$k; if($k > 1): if($k <= $vo["coun"] ): ?><hr /><?php endif; endif; ?>
                        <p style=" color:red; font-size:10px; margin:0;min-height: 25px; line-height:25px;"><?php echo ($vo2["description"]); ?></p><?php endforeach; endif; else: echo "" ;endif; ?>
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