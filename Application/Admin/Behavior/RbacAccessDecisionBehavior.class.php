<?php
namespace Admin\Behavior;

//功能：		修改Rbac模块的验证权限逻辑，以适应项目中左侧菜单的二级目录
//Listen:	ThinkPHP/Library/Org/Util/Rbac.class.php/AccessDecision
//Add:		ThinkPHP/Library/Org/Util/Rbac.class.php/AccessDecision
class RbacAccessDecisionBehavior {
	
	public function run(&$params){
		$allow_access = &$params[0];
		$accessList = $params[1];
		
// 		dump(MODULE_NAME);
// 		dump(CONTROLLER_NAME);
// 		dump(ACTION_NAME);
// 		dump(I('get.'));
		
		if( isset($accessList[strtoupper(MODULE_NAME)][strtoupper(CONTROLLER_NAME)][strtoupper(ACTION_NAME)]) ) //验证到action
		{
			$action_access = $accessList[strtoupper(MODULE_NAME)][strtoupper(CONTROLLER_NAME)][strtoupper(ACTION_NAME)];
			if(is_array($action_access)) //验证action之后的参数
			{
				$param_access_key = reset($action_access);//数组第一个元素的值，这里是目标参数名
				$param_access = I("get.$param_access_key");//目标参数值
				if($param_access)
				{
					if( $action_access[strtoupper($param_access)] )//验证lv4:参数
					{
						$allow_access = true;
					}
					else 
					{
						$allow_access = false;
					}
				}
				else //如果没有该参数值。。。就算做有权限吧 
				{
					$allow_access = true;
				}
			}
			else //action之后无参数
			{
				$allow_access = true;
			}
		}
		else 
		{
			$allow_access = false;
		}
	}
	
}