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
var _CONTROLLER_ = '/erptest/index.php/Admin/Rbac';
var _ACTION_  = '/erptest/index.php/Admin/Rbac/userList';
var _PUBLIC_ = '/erptest/Public';
var _TIMESTAMP_ = <?php echo time();?>;
var _SELF_ = '/erptest/index.php/Admin/Rbac/userList.html';
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
<div class="admin">
<table class="table  table-striped table-hover table-condensed">
		<tr>
			<td colspan="11" class="table_title" style="font-size:14px;padding:10px;">
				<span class="fl icon-users"> 用户管理</span>
				<span class="fr">
					<a href="<?php echo U('/Admin/Rbac/userAdd');?>" class="icon-user"> 添加用户</a>
				</span>
			</td>
			</tr>
			<tr class="list_head">
				<td width="70" style="text-align:left">ID</td>
				<td width="100" style="text-align:left">用户名称</td>
                <td width="100" style="text-align:left">真实名称</td>
				<td width="150" style="text-align:left">角色名称</td>
				<td style="text-align:left">用户描述</td>
                <td >所属部门</td>
				<td width="100">最后登录IP</td>
				<td width="150">最后登录时间</td>
                <td width="150">创建时间</td>
				<td width="70">状态</td>
				<td width="100">管理操作</td>
			</tr>
	    <?php if(is_array($list)): $i = 0; $__LIST__ = $list;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><tr class='<?php if(($mod) == "1"): ?>tr<?php else: ?>ji<?php endif; ?>' style='padding:20px;'>
				<td align='left'><span class="td_number"><?php echo ($vo["id"]); ?></span></td>
				<td style="color:#03c"><?php echo ($vo["username"]); ?></td>
                <td><?php echo ($vo["name"]); ?></td>
				<td >
                
                	<?php if(is_array($vo[role])): $i = 0; $__LIST__ = $vo[role];if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$role): $mod = ($i % 2 );++$i; echo ($role[name]); ?>&nbsp;&nbsp;<?php endforeach; endif; else: echo "" ;endif; ?>
                </td>
				<td ><?php echo ($vo["remark"]); ?></td>
                <td align='center'><?php echo ($vo['branch']); ?></td>
				<td align='center' style="color: #f60"><?php echo ($vo["loginip"]); ?></td>	
                <td align='center'><?php echo ($vo['logintime']); ?></td>
                <td align='center'><?php echo ($vo['create_time']); ?></td>
				<td align='center'><?php if(($vo["status"]) == "1"): ?><font color="red" class="icon-check"></font><?php else: ?><font color="blue" class="icon-times"></font><?php endif; ?> 
				</td>
				<td align='center'>
					<a href="<?php echo U('/Admin/Rbac/userAdd/',array('id'=>$vo['id']));?>" class="icon-pencil" title="修改"></a>
					 &nbsp;&nbsp; <?php if(($vo["username"]) == C("SPECIAL_USER")): ?><font  class="icon-trash-o"></font><?php else: ?>
                    <a href="javascript:"  onClick="layer.confirm('你确定删除该用户？', {btn: ['确定','取消']}, function(){location.href='<?php echo U('/Admin/Rbac/userDel/',array('id'=>$vo['id']));?>'});" class="icon-trash-o" title="删除"></a><?php endif; ?>
                </td>
			</tr><?php endforeach; endif; else: echo "" ;endif; ?>
		<tr class="tr">
          <td colspan="11" class="pages" align="center">
            <?php echo ($page); ?>
          </td>
        </tr>
		</table>
</div>


</body>
</html>