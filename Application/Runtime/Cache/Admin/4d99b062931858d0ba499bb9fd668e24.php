<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>Lilysilk - ERP</title>
</head>

<!-- 加载js css -->
<link rel="stylesheet" href="/test/erptest/Application/Admin/View/Default/css/pintuer.css">
<link rel="stylesheet" href="/test/erptest/Application/Admin/View/Default/css/admin.css">
<script src="/test/erptest/Application/Admin/View/Default/js/jquery.min.js"></script>
<script src="/test/erptest/Application/Admin/View/Default/js/pintuer.js"></script>
<script src="/test/erptest/Application/Admin/View/Default/js/respond.js"></script>
<script src="/test/erptest/Application/Admin/View/Default/js/admin.js"></script>
<script src="/test/erptest/Application/Admin/View/Default/js/layer/layer.js"></script>
<script src="/test/erptest/Application/Admin/View/Default/Public/KindEditor/kindeditor-min.js"></script>
<script src="/test/erptest/Application/Admin/View/Default/Public/KindEditor/lang/zh_CN.js"></script>
<script src="/test/erptest/Application/Admin/View/Default/js/function.js?v=<?php echo time();?>"></script><!-- 公共js函数文件 -->
<script>
	KindEditor.ready(function(K) {
		editor = K.create('textarea[name="content"]', {
			allowFileManager: true,
			afterBlur: function(){this.sync();},
		});
	});
</script>
<script language="javascript" type="text/javascript" src="/test/erptest/Application/Admin/View/Default/Public/wdatepicker/WdatePicker.js"></script>
<script>//定义需要的“常量”
var _CONTROLLER_ = '/test/erptest/index.php/Admin/Index';
var _ACTION_  = '/test/erptest/index.php/Admin/Index/js';
var _PUBLIC_ = '/test/erptest/Public';
var _TIMESTAMP_ = <?php echo time();?>;
var _SELF_ = '/test/erptest/index.php/Admin/index/js.html';
var _APP_='/test/erptest/index.php';
var _MODULE_ = '/test/erptest/index.php/Admin';
var _ROOT_ = '/test/erptest';

</script>


<link type="image/x-icon" href="http://www.pintuer.com/favicon.ico" rel="shortcut icon" />
<link href="http://www.pintuer.com/favicon.ico" rel="bookmark icon" />
<body>

<!-- 可以选择不加载菜单 -->
<?php if($no_menu != true): ?><div class="lefter">
    <div class="logo"><img src="/test/erptest/Application/Admin/View/Default/images/logo.png" style="height:40px;" alt="后台管理系统" /><b style="font-size: 12px">ERP</b></div>
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
            <span> <a href="/test/erptest/index.php/Admin/messages/Index.html" class="plcz" style=" margin-right:30px;">私信</a> <?php echo ($username); ?> &nbsp;&nbsp; 工号: <?php echo $monoid>0?$monoid:0;?> &nbsp;&nbsp;</span>
            
            
            
            <ul class="bread">
                <li><a href="index.html" class="icon-home"> 开始</a></li>
                <li>后台首页</li>
            </ul>
        </div>
    </div>
</div>
<span class="icon-dedent" style="position: fixed; top: 84px; left: 180px; z-index: 100;color: #09c;cursor: pointer;font-size: 20px;width: 10px;" onclick='show_hide_left_menu(this);'></span><?php endif; ?>
<div class="admin">

<h1>前进 后退 返回顶部</h1>
<div>
	<button class="button win-back icon-arrow-left">后退</button>
	<button class="button win-forward">
		前进 <span class="icon-arrow-right"></span>
	</button>
	<button class="button win-backtop">
		返回顶部 <span class="icon-arrow-up"></span>
	</button>
</div>


<h1>tab标签</h1>
<div class="tab">
	<div class="tab-head">
		<strong>标题</strong> <span class="tab-more"><a href="#">更多</a></span>
		<ul class="tab-nav">
			<li class="active"><a href="#tab-start">tab1</a> </li>
			<li><a href="#tab-css">tab2</a> </li>
			<li><a href="#tab-units">tab3</a> </li>
		</ul>
	</div>
	<div class="tab-body">
		<div class="tab-panel active" id="tab-start">
			content1</div>
		<div class="tab-panel" id="tab-css">
			con2</div>
		<div class="tab-panel" id="tab-units">
			3</div>
	</div>
</div>

<h1>tab标签 hover触发 右侧 边框</h1>
<div class="tab border-main" data-toggle="hover">
	<div class="tab-head text-right">
		<strong>标题</strong> <span class="tab-more"><a href="#">更多</a></span>
		<ul class="tab-nav">
			<li class="active"><a href="#tab-start2">tab1</a> </li>
			<li><a href="#tab-css2">tab2</a> </li>
			<li><a href="#tab-units2">tab3</a> </li>
		</ul>
	</div>
	<div class="tab-body tab-body-bordered"><!-- 内容边框.tab-body-bordered -->
		<div class="tab-panel active" id="tab-start2">
			content1</div>
		<div class="tab-panel" id="tab-css2">
			con2</div>
		<div class="tab-panel" id="tab-units2">
			3</div>
	</div>
</div>


<h1>对话框</h1>
<!-- 参数： data-toggle=click/hover data-mask=1显示遮掩层  data-width弹出框宽度 -->
<button class="button button-big bg-main dialogs" data-toggle="click" data-target="#mydialog" data-mask="1" data-width="50%">弹出对话框</button>
<div id="mydialog" class="hidden">
	<div class="dialog">
		<div class="dialog-head">
			<span class="close rotate-hover"></span><strong>对话框标题</strong>
		</div>
		<div class="dialog-body">
			对话框内容</div>
		<div class="dialog-foot">
			<button class="button dialog-close">
				取消</button>
			<button class="button bg-green">
				确认</button>
		</div>
	</div>
</div>


<h1>提示</h1>
<button class="button tips" data-toggle="hover" data-place="left" title="提示信息">
	悬浮左方提示</button>
<button class="button tips" data-toggle="click" data-place="top" title="提示信息">
	点击上方提示</button>
<button class="button tips" data-toggle="hover" data-place="right" title="提示信息">
	悬浮右方提示</button>
<button class="button tips" data-toggle="hover" data-place="bottom" data-image="/test/erptest/Application/Admin/View/Default/images/logo.png">
	悬浮下方图片提示</button>
<p>
	<a class="text-red tips" data-toggle="click" data-place="left" data-style="bg-dot border-main" title="xxx" content="中国人自己的css框架" href="javascript:void(0);">拼图 Pintuer</a>：国内优秀的HTML、CSS、JS
	<span class="text-main tips" data-toggle="hover" data-place="top" data-width="200px" title="跨屏响应式" content="自动适应不同设备不同分辨率。">跨屏响应式开源前端框架</span>，使用最新浏览器技术，为快速的前端开发提供一系统的文本、图标、
	<span class="text-dot tips" data-toggle="hover" data-style="bg-dot border-red" title="图片媒体" data-place="bottom" data-image="/test/erptest/Application/Admin/View/Default/images/logo.png">媒体</span>、表格、表单、按钮、菜单、
	<span class="text-sub tips" data-toggle="hover" data-place="right" data-style="bg-yellow border-red" title="包含常规网格及响应式网格">网格系统</span>等样式工具包，占用资源小，使用拼图可以快速构建简洁、优雅而且自动适应手机、平板、桌面电脑等设备的前端界面，让前端开发像玩
	<span class="text-dot tips" title="" data-target="#tips-target">自定义</span>
</p>
<div class="hidden" id="tips-target">
	<div class="bg-dot">
		<span class="text-yellow">hello kitty</span>
		<img src="/test/erptest/Application/Admin/View/Default/images/logo.png"/>
	</div>
</div>


<h1>警告框</h1>
<div class="alert alert-red">
	<span class="close rotate-hover"></span><strong>操作失败</strong>
	<p>
		请重新操作。</p>
	<button class="button bg">
		取消</button>
	<button class="button bg-red">
		确认</button>
</div>
<div class="alert alert-yellow">
	<span class="close rotate-hover"></span><strong>注意：</strong>请按照要求填写内容。</div>
<div class="alert alert-blue">
	<span class="close rotate-hover"></span><strong>提示：</strong>请按照要求填写内容。</div>
<div class="alert alert-green">
	<span class="close rotate-hover"></span><strong>恭喜：</strong>操作成功。</div>


<h1>复选框</h1>
<div class="button-group checkbox">
	<label class="button active">
		<input name="pintuer" value="1" type="checkbox"><span class="icon icon-check"></span> 同意条款
	</label>
</div>
<div class="button-group border-main checkbox">
	<label class="button active">
		<input name="pintuer" value="1" type="checkbox" checked="checked">起步
	</label>
	<label class="button">
		<input name="pintuer" value="2" type="checkbox">CSS
	</label>
	<label class="button">
		<input name="pintuer" value="3" type="checkbox">元件
	</label>
	<label class="button">
		<input name="pintuer" value="4" type="checkbox">JS组件
	</label>
	<label class="button">
		<input name="pintuer" value="5" type="checkbox">模块
	</label>
</div>


<h1>表单验证</h1>
<!-- 验证参数： required	不为空
chinese	纯汉字
number	纯数字
integer	正负整数
plusinteger	正整数
double	正负小数
plusdouble	正小数
english	英文字符
username	英文字母开头的字母、下划线、数字
mobile	手机号码
phone	电话
tel	手机号码或者电话
email	电子邮箱
url	网址
ip	IP地址
currency	货币
zip	邮编
qq	QQ号
radio	单选框是否选择
详见：http://www.pintuer.com/javascript.html 搜索：验证参数 -->
<form method="post" class="form form-block">
	<!-- 不能为空  -->
	<div class="form-group">
		<div class="label"><label for="username">不能为空</label></div>
		<div class="field">
			<input type="text" class="input" id="username" name="username" size="50" data-validate="required:不能为空" placeholder="手机/邮箱/账号" />
		</div>
	</div>
	<!-- 正则验证内容，多项验证用逗号分隔  -->
	<div class="form-group">
		<div class="label"><label for="upfile">正则验证</label>	</div>
		<div class="field">
			<a class="button input-file" href="javascript:void(0);">+ 请选择上传文件
        		<input size="100" data-validate="required:请选择文件,regexp#.+.(jpg|jpeg|png|gif|txt)$:只能上传jpg|gif|png|txt格式文件" type="file"/>
    		</a>
		</div>
	</div>
	<!-- 单选框：必选 -->
	<div class="form-group">
		<div class="label"><strong>拼图</strong></div>
		<div class="field label-block">
			<label><input name="pintuer" type="radio" data-validate="radio:请选择">起步	</label>
			<label><input name="pintuer" type="radio" data-validate="radio:请选择">CSS</label>
			<label><input name="pintuer" type="radio" data-validate="radio:请选择">元件	</label>
			<label><input name="pintuer" type="radio" data-validate="radio:请选择">动画	</label>
			<label><input name="pintuer" type="radio" data-validate="radio:请选择">js组件</label>
			<label><input name="pintuer" type="radio" data-validate="radio:请选择">模块	</label>
		</div>
	</div>
	<!-- 复选框，至少选择几个 -->
	<div class="form-group">
		<div class="label"><strong>拼图</strong></div>
		<div class="field label-block">
			<label><input name="pintuer" type="checkbox" data-validate="required:请选择,length#>=2:至少选择2项">起步</label>
			<label><input name="pintuer" type="checkbox" data-validate="required:请选择,length#>=2:至少选择2项">CSS</label>
			<label><input name="pintuer" type="checkbox" data-validate="required:请选择,length#>=2:至少选择2项">元件</label>
			<label><input name="pintuer" type="checkbox" data-validate="required:请选择,length#>=2:至少选择2项">动画</label>
			<label><input name="pintuer" type="checkbox" data-validate="required:请选择,length#>=2:至少选择2项">js组件</label>
			<label><input name="pintuer" type="checkbox" data-validate="required:请选择,length#>=2:至少选择2项">模块</label>
		</div>
	</div>
	<!-- 选择框，必选 -->
	<div class="form-group">
		<div class="label"><label for="pintuer">拼图</label></div>
		<div class="field">
			<select class="input" name="pintuer" id="pintuer" data-validate="required:请选择拼图内容">
				<option value="">请选择</option>
				<option value="start">起步</option>
				<option value="css">CSS</option>
				<option value="units">元件</option>
			</select>
		</div>
	</div>
	<div class="form-button">
		<button class="button bg-yellow form-reset" type="reset">重设</button>
		<button class="button" type="submit">登录</button>
	</div>
</form>


<!-- <h1>置顶 置底</h1>
<div class="fixed bg-yellow fixed-top" data-style="fixed-top" data-offset-fixed="100px">i wanna be</div>
<div class="fixed bg-yellow fixed-bottom" data-style="fixed-bottom" data-offset-fixed="10">how are you</div>
 -->


<h1>内容折叠</h1>
<div class="doc-demoview doc-viewad0 ">
	<div class="view-body">
		<div class="collapse">
			<div class="panel active">
				<div class="panel-head"><h4>拼图前端框架</h4></div>
				<div class="panel-body" id="first">拼图，是国内一款开源的专业响应式网页前端框架，由HTML、CSS、Javascrip三者组合应用。提供的CSS、元件、动画、组件、模块等样式实现绝大多数的网页特效与响应功能，像玩积木、拼图游戏一样快速简单而富有乐趣的做前端开发。</div>
			</div>
			<div class="panel">
				<div class="panel-head"><h4>拼图前端框架</h4></div>
				<div class="panel-body" id="second">拼图，是国内一款开源的专业响应式网页前端框架，由HTML、CSS、Javascrip三者组合应用。提供的CSS、元件、动画、组件、模块等样式实现绝大多数的网页特效与响应功能，像玩积木、拼图游戏一样快速简单而富有乐趣的做前端开发。</div>
			</div>
			<div class="panel">
				<div class="panel-head"><h4>拼图前端框架</h4></div>
				<div class="panel-body" id="third">拼图，是国内一款开源的专业响应式网页前端框架，由HTML、CSS、Javascrip三者组合应用。提供的CSS、元件、动画、组件、模块等样式实现绝大多数的网页特效与响应功能，像玩积木、拼图游戏一样快速简单而富有乐趣的做前端开发。</div>
			</div>
		</div>
	</div>
</div>


</div>

</body>
</html>