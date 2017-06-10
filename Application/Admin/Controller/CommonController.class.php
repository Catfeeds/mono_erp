<?php
namespace Admin\Controller;
use Think\Controller;
use Org\Util\Rbac;
use Think\Model;
class CommonController extends Controller {
    function _initialize(){
    	
    	//自动加载 控制器对应的 function文件
    	$file_base_name = cc_format(CONTROLLER_NAME);//下划线风格命名的控制器同名文件
    	$file_full_name = APP_PATH.'Admin/Common/function/'.$file_base_name.'.php';
    	if(file_exists($file_full_name))
    	{
    		include($file_full_name);
    	}
    	
    	//登陆
		if(!$_SESSION [C('USER_AUTH_KEY')] && (CONTROLLER_NAME!='Public' || (ACTION_NAME!="login" && ACTION_NAME!='checkLogin')))
		{			
				redirect(PHP_FILE . C('USER_AUTH_GATEWAY'));			
		}
		
        // 后台用户权限检查
        if (C('USER_AUTH_ON') && !in_array(MODULE_NAME, explode(',', C('NOT_AUTH_MODULE')))) {
            if (!Rbac::AccessDecision()) {
                //检查认证识别号
                if (!$_SESSION [C('USER_AUTH_KEY')]) {
                    //跳转到认证网关
                    redirect(PHP_FILE . C('USER_AUTH_GATEWAY'));
                }
                // 没有权限 抛出错误
                if (C('RBAC_ERROR_PAGE')) {
                    // 定义权限错误页面
                    redirect(C('RBAC_ERROR_PAGE'));
                } else {
                    if (C('GUEST_AUTH_ON')) {
                        $this->assign('jumpUrl', PHP_FILE . C('USER_AUTH_GATEWAY'));
                    }					
                    // 提示错误信息
                    $this->error(L('_VALID_ACCESS_'));
                }
            }
			else
			{
				/*
				*获取用户菜单				
				*/
				$username = session('username');    // 用户名
				$roleid   = session('roleid');      // 角色ID
				$monoid   = session('monoid');      // 角色ID
				if($username){
				if($username == C('SPECIAL_USER')){     //如果是无视权限限制的用户，则获取所有主菜单
					$sql = 'SELECT `id`,`title`,level,name FROM `node` WHERE ( `status` =1 AND `display`=1 AND `level`<>1 ) ORDER BY sort DESC';
				}else{  //更具角色权限设置，获取可显示的主菜单
					//$sql = "SELECT `node`.`id` as id,`node`.`title` as title,`node`.`name` as name,`node`.`level` as level FROM `node`,`access` WHERE `node`.id=`access`.node_id AND `access`.role_id=$roleid  AND  `node`.`status` =1 AND `node`.`display`=1 AND (`node`.`level` =0 OR `node`.`level` =2)  ORDER BY `node`.sort DESC";
					$sql = "SELECT `node`.`id` as id,`node`.`title` as title,`node`.`name` as name,`node`.`level` as level FROM `node`,`access` WHERE `node`.id=`access`.node_id AND `access`.role_id=$roleid  AND  `node`.`status` =1 AND `node`.`display`=1   ORDER BY `node`.sort DESC";
				}
				
				$Model = new Model(); // 实例化一个model对象 没有对应任何数据表
				$main_menu = $Model->query($sql);
				
				$active=array();
				foreach($main_menu as $main_menu_key=>$main_menu_value){
										
					$main_menu_url=U($this->get_url($main_menu_value[id]));
					
					
					if(isset($_GET))
					{
						$get_first_value=array();
						foreach($_GET as $key=>$value)
						{
							$get_first_value[$key]=$value;
							break;
						}						
						$this_url=U("MODULE_NAME."/".CONTROLLER_NAME."/".ACTION_NAME",$get_first_value);
					}
					else
					{
						$this_url=U("MODULE_NAME."/".CONTROLLER_NAME."/".ACTION_NAME");
					}
					
				
					//$this_url=$this->get_this_url();
					
					//判断哪个主菜单是选中状态
					if($this_url==$main_menu_url)
					{
						$active[2]=$main_menu_key;
					}
					if($main_menu_value[level]==2 && strtolower($main_menu_value[name])==strtolower(CONTROLLER_NAME))
					{
						$active[1]=$main_menu_key;
					}
					$left_second_menu_active=D('node')->where("name='".CONTROLLER_NAME."' and pid=".$main_menu_value[id])->find();
					if($left_second_menu_active)
					{
						$active[3]=$main_menu_key;
					}
					
					$pid = $main_menu_value[id];    //选择子菜单
					$NodeDB = D('Node');
					
					$datas = $this->left_child_menu($pid);
					$parent_info = $NodeDB->getNode(array('id'=>$pid),'title');
					$sub_menu_html = '<dl>';
					foreach($datas as $key => $_value) {
						$sub_array = $this->left_child_menu($_value['id']);						
						$left_menu_url=U($this->get_url($_value['id']));						
						$left_menu_activ_str="";
						$left_menu_activ_a="";
					
						if(strtolower($left_menu_url)==strtolower($this_url) && empty($sub_array))
						{														
							$left_menu_activ_str="style='background:#09c'";
							$left_menu_activ_a="style='color:#fff;'";
						}
						$sub_menu_html .="<li $left_menu_activ_str>";

						if(!empty($sub_array)){
							
							$sub_menu_html .= "<a target='_self' onclick='javascript:showHideSecondMenu(this,".$_value['id'].");' style='cursor:pointer;'>{$_value[title]}<span style='float: right;line-height: 8px;padding-bottom: 3px;margin-top: 4px;padding-right: 1px' class='icon-plus-square'></span></a>";
												
							if($left_second_menu_active && strtolower(CONTROLLER_NAME)==strtolower($_value[name]))
							{
								$sub_menu_html .= " <ul style='position:relative;margin-top: 0px;display:block;' id='second_menu_".$_value['id']."'>";
							}
							else
							{
								$sub_menu_html .= " <ul style='position:relative;margin-top: 0px;display:none;' id='second_menu_".$_value['id']."'>";
							}
							foreach ($sub_array as $value) {
								$left_second_url=U($this->get_url($value[id]));
								
								$href = $left_second_url;
								$left_menu_activ_str="";
								$left_menu_activ_a="";
								if(strtolower($this_url)==strtolower($href))
								{
									$left_menu_activ_str="background:#09c";
									$left_menu_activ_a="style='color:#fff;'";
								}
								$sub_menu_html .= "<li style='margin-left:0;$left_menu_activ_str'><a $left_menu_activ_a id='a_{$value[id]}' onClick='sub_title({$value[id]})' href='{$href}'>{$value[title]}</a></li>";
							}
							$sub_menu_html .= "</ul>";
						}
						else
						{
							$sub_menu_html .= "<a target='_self' $left_menu_activ_a href='$left_menu_url'>{$_value[title]}</a>";
						}
					  $sub_menu_html .=  '</li>';
					}
					$sub_menu_html .= '</dl>';
			
					//$this->assign('sub_menu_title',$parent_info['title']);
					//$this->assign('sub_menu_html',$sub_menu_html);
					$main_menu[$main_menu_key][main_menu_url]=$main_menu_url;
					$main_menu[$main_menu_key][left_menu_html]=$sub_menu_html;
					
					//判断主菜单是否选中
					
				}
				if(isset($active[2]))
				{
					$main_menu[$active[2]][active]=true;
				}
				else
				{
					if(isset($active[3]))
					{
						$main_menu[$active[3]][active]=true;
					}
					else
					{
						$main_menu[$active[1]][active]=true;
					}
					
				}
				
				$this->assign('main_menu',$main_menu);
				$this->assign('username',$username);
				$this->assign('monoid',$monoid);
				}
			}
        }
	}
	
	private function left_child_menu($pid, $with_self = 0) {
        $pid = intval($pid);

        $username = session('username');    // 用户名
        $roleid   = session('roleid');      // 角色ID
        if($username == C('SPECIAL_USER')){     //如果是无视权限限制的用户，则获取所有主菜单
            $sql = "SELECT `id`,`title`,`name` FROM `node` WHERE ( `status` =1 AND `display`=2 AND `level` <>1 AND `pid`=$pid ) ORDER BY sort DESC";
        }else{
            $sql = "SELECT `node`.`id` as `id` ,`node`.`name` as `name` , `node`.`title` as `title` FROM `node`,`access` WHERE `node`.id = `access`.node_id AND `access`.role_id = $roleid AND `node`.`pid` =$pid AND `node`.`status` =1 AND `node`.`display` =2 AND `node`.`level` <>1 ORDER BY `node`.sort DESC";
        }
        $Model = new Model(); // 实例化一个model对象 没有对应任何数据表
        $result = $Model->query($sql);

        if($with_self) {
            $NodeDB = D('Node');
            $result2[] = $NodeDB->getNode(array('id'=>$pid),`id`,`title`);
            $result = array_merge($result2,$result);
        }
        return $result;
    }
	private function get_url($id)
	{
		$node=M("node")->where("id=".$id)->find();
		if($node[level]==2)
		{
			return "Admin/".$node[name]."/index";
		}
		elseif($node[level]==3)
		{
			$p_node=M("node")->where("id=".$node[pid])->find();
			return "Admin/".$p_node[name]."/".$node[name];
		}	
	}

	public function get_this_url() {
	    $sys_protocal = isset($_SERVER['SERVER_PORT']) && $_SERVER['SERVER_PORT'] == '443' ? 'https://' : 'http://';
	    $php_self = $_SERVER['PHP_SELF'] ? $_SERVER['PHP_SELF'] : $_SERVER['SCRIPT_NAME'];
	    $path_info = isset($_SERVER['PATH_INFO']) ? $_SERVER['PATH_INFO'] : '';
	    $relate_url = isset($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : $php_self.(isset($_SERVER['QUERY_STRING']) ? '?'.$_SERVER['QUERY_STRING'] : $path_info);
	    return $relate_url;
	}
	
	//私信
	public function messages()
	{
		$mono_branchDB = M('mono_branch');
		$userDB = M('user');
		$branch = $mono_branchDB->where('`pid` = 0')->select();
		foreach($branch as $branch_k => $branch_v)
		{
			$branch[$branch_k]['personnel'] = $userDB ->field('id,name')->where('`branch` = "'.$branch_v['branch_name'] .'" and `status` =1')->select();
			$branch[$branch_k]['detail'] = $mono_branchDB->where('`pid` = '.$branch_v['id'])->order('id')->select();
			foreach($branch[$branch_k]['detail'] as $detail_k => $detail_v)
			{
				$branch[$branch_k]['detail'][$detail_k]['personnel'] = $userDB ->field('id,name')->where('`branch` = "'.$detail_v['branch_name'] .'" and `status` =1')->select();
			}
		}
	//	dump($branch);exit;
		$this->branch_list = $branch;
	
	}
	
}