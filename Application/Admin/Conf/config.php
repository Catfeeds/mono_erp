<?php
return array(
	
	//'SHOW_PAGE_TRACE' =>true,    //调试模式显示页面Trace信息
	
    /*
	*添加数据库配置信�?
	*/
	'DB_TYPE'=>'mysql',// 数据库类�?
	'DB_HOST'=>'localhost',// 服务器地址
	'DB_NAME'=>'erp',// 数据库名
	'DB_USER'=>'root',// 用户�?
	'DB_PWD'=>'123456',// 密码
	'DB_PORT'=>3306,// 端口
	'DB_PREFIX'=>'',// 数据库表前缀
	'DB_CHARSET'=>'utf8',// 数据库字符集
    /*
	*定义模板信息
	*/
	'lily3'=> 'mysql://root:123456@127.0.0.1:3306/lily3',
	'DEFAULT_THEME' => 'Default',
    'TMPL_PARSE_STRING' => array(
        '__IMG__'    => __ROOT__ . '/Application/Admin/View/Default/images',
        '__CSS__'    => __ROOT__ . '/Application/Admin/View/Default/css',
        '__JS__'     => __ROOT__ . '/Application/Admin/View/Default/js',
        '__Theme__'     => __ROOT__ . '/Application/Admin/View/Default',
    ),
    'LAYOUT_ON' => true,
    'LAYOUT_NAME' => 'layout',
	'logistics_update_num' =>'2',  //1  快递信息更新总条数   2 更新产品状态条数
	
	'inventory_min'=>  '0',                //最小库存数
	'inventory_max'=>'0',			  //最大库存数       //默认都为0表示不设置预警
	
	'AppKey'=>"88a04897d83e37e5",            //快递接口 key             
	/*
	* 分页类配�?
	*/
	
	'ROLLPAGE' => 5 , //定义每批显示的页数
	'LISTROWS' => 20 , //定义每页显示的行数
	/*
	速卖通 key*/
	"Smt_appKey" => '33545780',
	"Smt_appSecret" =>'4uCIpUBJgF1',
	"Smt_refreshToken"=>'02bb94af-a9a3-4856-97cc-016f1e010d92',
	
	/* 
	*权限管理配置
	*/
	"SPECIAL_USER"=>"admin",
	"USER_AUTH_ON" => true,                    //是否开启权限验�?必配)
    "USER_AUTH_TYPE" => 1,                     //验证方式�?、登录验证；2、实时验证）
 
    "USER_AUTH_KEY" => 'uid',                  //用户认证识别�?必配)
    "ADMIN_AUTH_KEY" => 'superadmin',          //超级管理员识别号(必配)
    "USER_AUTH_MODEL" => 'user',               //验证用户表模�?ly_user
    'USER_AUTH_GATEWAY'  =>  '/Admin/Public/login',  //用户认证失败，跳转URL
 
    'AUTH_PWD_ENCODER'=>'md5',                 //默认密码加密方式
 
    "RBAC_SUPERADMIN" => 'admin',              //超级管理员名�?
 
 
    "NOT_AUTH_MODULE" => 'Index,Public',       //无需认证的控制器
    "NOT_AUTH_ACTION" => 'index',              //无需认证的方�?
 
    'REQUIRE_AUTH_MODULE' =>  '',              //默认需要认证的模块
    'REQUIRE_AUTH_ACTION' =>  '',              //默认需要认证的动作
 
    'GUEST_AUTH_ON'   =>  false,               //是否开启游客授权访�?
    'GUEST_AUTH_ID'   =>  0,                   //游客标记
 
    "RBAC_ROLE_TABLE" => 'role',            //角色表名�?必配)
    "RBAC_USER_TABLE" => 'role_user',       //用户角色中间表名�?必配)
    "RBAC_ACCESS_TABLE" => 'access',        //权限表名�?必配)
    "RBAC_NODE_TABLE" => 'node',            //节点表名�?必配)
    'IMAGE_URL' =>'http://lilysilk.s3.amazonaws.com/product_new/',
	'CURRENCY_CODE' => 'S$',
	'NIGHTWEAR_CUSTOMIZATION_PRICE' => 30,
	'DOMAIN_HTTPS' => 'https://www.lilysilk.com',
);