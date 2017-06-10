<?php
namespace Admin\Behavior;

//功能：		修改Rbac模块的授权逻辑，以适应项目中左侧菜单的二级目录
//Listen:	ThinkPHP/Library/Org/Util/Rbac.class.php/getAccessList
//Add:		ThinkPHP/Library/Org/Util/Rbac.class.php/getAccessList
class RbacGetAccessListBehavior {
	
	public function run(&$params){
		
		$authId = $params[0];
		$access = &$params[1];//权限列表，初始值array()
		
		$table = array('role'=>C('RBAC_ROLE_TABLE'),'user'=>C('RBAC_USER_TABLE'),'access'=>C('RBAC_ACCESS_TABLE'),'node'=>C('RBAC_NODE_TABLE'));
		//Module level=1
		$sql = "SELECT node.id,node.name FROM ".
				$table['role']." as role,".
				$table['user']." as user,".
				$table['access']." as access ,".
				$table['node']." as node ".
				"where user.user_id='{$authId}' and user.role_id=role.id and ( access.role_id=role.id  or (access.role_id=role.pid and role.pid!=0 ) ) and role.status=1
				and access.node_id=node.id and node.level=1 and node.status=1";
		$modules =   M()->query($sql);//lilyERP项目 只有一个module
// 		dump($modules);exit;
		$access_module = array();
		//Controller level=2
		//相比原来授权规则，去掉了node.pid限制,因为module只有一个，而有些controller的父节点不是admin
		//level=2的节点，可能存在display=1|2的两种的多个同名节点，action层面的授权时要注意不能覆盖前面的
		$sql = "SELECT node.id,node.name FROM ".
				$table['role']." as role,".
				$table['user']." as user,".
				$table['access']." as access ,".
				$table['node']." as node ".
				"where user.user_id='{$authId}' and user.role_id=role.id and ( access.role_id=role.id  or (access.role_id=role.pid and role.pid!=0 ) ) and role.status=1
				and access.node_id=node.id and node.level=2 and node.status=1";
		
		$controllers = M()->query($sql);
// 		dump($controllers);exit;
		foreach($controllers as $key=>$controller)
		{
			$access_controller = array();
			$controller_node_id = $controller['id'];
			//Action level=3 寻找所有子节点
			$sql = "select node.id,node.name from ".
					$table['role']." as role,".
					$table['user']." as user,".
					$table['access']." as access ,".
					$table['node']." as node ".
					"where user.user_id='{$authId}' and user.role_id=role.id and ( access.role_id=role.id  or (access.role_id=role.pid and role.pid!=0 ) ) and role.status=1
					and access.node_id=node.id and node.level=3 and node.pid={$controller_node_id} and node.status=1";//这里要保证pid的正确，action必须对应controller
			$actions = M()->query($sql);
// 			dump($actions);exit;
			foreach($actions as $k=>$v)
			{
				if( strstr($v['name'],'/') )//action节点名中是否包含“/”
				{
					$temp = explode('/', $v['name']);
					$access_controller[strtoupper($temp[0])][strtoupper($temp[2])] = $temp[1];
				}
				else 
				{
					$access_controller[strtoupper($v['name'])] = $v['id'];
				}
			}
				
			$previous_access_controller = $access_module[strtoupper($controller['name'])];//注意合并而非覆盖
			$previous_access_controller && $access_controller=array_merge_recursive($access_controller,$previous_access_controller);
			$access_module[strtoupper($controller['name'])] = $access_controller;
		}
		
		$access[strtoupper($modules[0]['name'])] = $access_module;
// 		dump($access);exit;
	}
	
}