<?php

namespace Admin\Controller;
use Org\Util\Rbac;

class PublicController extends CommonController {
	
	public function index()
	{
	}
	public function login()
	{
		layout(false);
		$this->display();
	}
	
	// 登录检测
    public function checkLogin() {
        $username = I('post.username');
        $password =  I('post.password');
		$user_login_recordDB = M('user_login_record');
        //$verify   = I('post.verify');

        //生成认证条件
        $map            =   array();
        // 支持使用绑定帐号登录
        $map['username'] = $username;
        $map['status']        = 1;
		/*
        if(session('verify') != md5($verify)) {
            $this->error('验证码错误！');
        }
		*/
		
        $authInfo = Rbac::authenticate($map);
		
        //使用用户名、密码和状态的方式进行认证
        if(false == $authInfo) {
            $this->error('帐号不存在或已禁用！');
        }else {
            if($authInfo['password'] != $password ) {
                $this->error('密码错误！');
            }
			$role_id=M('role_user')->where("user_id=".$authInfo['id'])->find();
			session(C('USER_AUTH_KEY'), $authInfo['id']);
            session('userid',$authInfo['id']);  //用户ID
			session('username',$authInfo['username']);   //用户名
            session('roleid',$role_id['role_id']);    //角色ID
			session('monoid',$authInfo['mono_id']);    //工号
            if($authInfo['username']==C('SPECIAL_USER')) {
                session(C('ADMIN_AUTH_KEY'), true);
            }
            
            //保存登录信息
            $User	=	M(C('USER_AUTH_MODEL'));
            $ip		=	get_client_ip();
            $data = array();
           
            $data['id']	=	$authInfo['id'];
            $data['logintime']	=	date('Y-m-d H:i:s',time());
            $data['loginip']	=	get_client_ip();
			
            $User->save($data);
			$dd['username'] = $username;
			$dd['login_time'] = date('Y-m-d H:i:s',time());
 			$dd['login_ip'] = get_client_ip();
			$user_login_recordDB->add($dd);
            // 缓存访问权限
            Rbac::saveAccessList();
            redirect(U('/Admin'));
        }
    }
	
    // 用户登出
    public function logout() {
        if(session('?'.C('USER_AUTH_KEY'))) {
            session(C('USER_AUTH_KEY'),null);
            session(null);
            redirect(U('/Admin/Public/login'));
        }else {
            $this->error('已经登出！');
        }
    }	
}

?>