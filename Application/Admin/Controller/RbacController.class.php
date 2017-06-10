<?php
/**
 * Description: 基于ThinkPHP Rbac实现的权限管理系统
 */
namespace Admin\Controller;

class RbacController extends CommonController {

	public function index()
	{
		$this->display();
	}
    //用户列表
    public function userList() 
	{
		//import('Org.Util.Page');// 导入分页类
        $map = array();
		//$map['_query']= 'limit '.I('p').','.C('LISTROWS');
        $UserDB = D('User');
        $count = $UserDB->where($map)->count();
        $Page       = new \Think\Page1($count);// 实例化分页类 传入总记录数
        // 进行分页数据查询 注意page方法的参数的前面部分是当前的页数使用 $_GET[p]获取
        $nowPage = isset($_GET['p'])?$_GET['p']:1;
        $show       = $Page->show();// 分页显示输出
        $list = $UserDB->where($map)->relation(true)->order('id ASC')->page($nowPage.','.C('LISTROWS'))->select();
        $this->assign('list',$list);
        $this->assign('page',$show);// 赋值分页输出
        $this->display();
	}
	
	// 添加用户
    public function userAdd(){
        $UserDB = D("User");
		$mono_branchDB = M('mono_branch');
		
		$branch = $mono_branchDB->where('`is_work` = 1 and `pid` = 0')->select();
		foreach($branch as $branch_k => $branch_v)
			{
				$branch[$branch_k]['detail'] = $mono_branchDB->where('`is_work` = 1 and `pid` = '.$branch_v['id'])->order('id')->select();
			}
		$this->branch_list = $branch;
		
        if(isset($_POST['dosubmit'])) {
            $password = $_POST['password'];
            $repassword = $_POST['repassword'];
            if(empty($password) || empty($repassword)){
                $this->error('密码必须！');
            }
            if($password != $repassword){
                $this->error('两次输入密码不一致！');
            }

            //根据表单提交的POST数据创建数据对象
            if($UserDB->create()){			
                $user_id = $UserDB->add();
                if($user_id){
                    $data['user_id'] = $user_id;
                    $data['role_id'] = $_POST['role'];
                    if (M("RoleUser")->data($data)->add()){
                        $this->assign("jumpUrl",U('/Admin/Rbac/userList'));
                        $this->success('添加成功！');
                    }else{
                        $this->error('用户添加成功,但角色对应关系添加失败!');
                    }
                }else{
                     $this->error('添加失败!');
                }
            }else{
                $this->error($UserDB->getError());
            }
        }else{
           //用户信息
			$use_id=isset($_GET[id])?intval($_GET[id]):0;
			if($use_id){
			$user =$UserDB ->where('`id` = '.$use_id)->find();	
			$user_role = M("RoleUser")->where('`user_id` = '.$use_id)->find();
			$user['role'] = $user_role['role_id'];
			    $this->assign('tpltitle','修改');
			}else{
				$this->assign('tpltitle','添加');	
			}
			//用户角色
            $role = D('Role')->getAllRole(array('status'=>1),'sort DESC');
			
            $this->assign('role',$role);
            $this->assign('info',$user);
            $this->display();
        }
    }
	//修改用户
	public function userEdit()
	{
		if(isset($_POST[id]))
		{
			$UserDB = M("User");
			$RoleUser = M("RoleUser");
			//根据表单提交的POST数据更新数据对象		
			$password = $_POST['password'];
            $repassword = $_POST['repassword'];
            if($password != $repassword){
                $this->error('两次输入密码不一致！');
            }
			//修改用户信息
			$date['id'] = $_POST['id'];
			$date['username']=$_POST['username'];
				if($_POST['password']!= ''){
			$date['password'] = $_POST['password'];
				}
			$date['name'] = I('post.name');
			$date['branch'] = I('post.branch');
			$date['logintime'] = date('Y-m-d H:i:s',time());	
			$date['create_time'] = $_POST['create_time'];		
			$date['status'] = $_POST['status'];
			$date['remark'] = $_POST['remark'];
			$user=$UserDB->where('`id`='.$_POST['id'])->save($date);
				if($user!==false){
					//修改用户角色
					$data['role_id'] = $_POST['role'];
					$role=$RoleUser->where('`user_id`='.$_POST['id'])->save($data);
					if ($role!==false){
						$this->assign("jumpUrl",U('/Admin/Rbac/userList'));
						$this->success('用户角色修改成功！');
					}else{
						$this->error('用户角色修改失败!');
					}
				}else{
					 $this->error('用户信息修改失败!');
				}
		}
	}
	//删除用户
	public function userDel(){
		$UserDB = M("User");
		$RoleUser = M("RoleUser");
		$id=$_GET['id'];
		$user=$UserDB->where('`id`='.$id)->delete();
		if($user){
		    $roleUser=$RoleUser->where('`user_id`='.$id)->delete();
			if($roleUser){
					$this->assign("jumpUrl",U('/Admin/Rbac/userList'));
					$this->success('用户信息删除成功！');
				}else{
					$this->error('用户角色删除失败!');	
					}
		}else{
			$this->error('用户信息删除失败!');
			}
		}
	
	
	//角色列表
	public function roleList()
	{
		$RoleDb = D('Role');
		$list = $RoleDb -> select();
		$this->assign('list',$list);
		$this->display();
	}
	//角色排序
	public function roleSort()
	{ 
	    if($_POST['dosubmit']){
			$RoleDb = D('Role');
			$sort=$_POST['sort'];
			$a=0;
			foreach($sort as  $k=>$v){
				$date['sort']=$v;
				$role[$a]=$RoleDb->where('`id` ='. $k)->save($date);  //查找节点
				$a++;
				}
			if ($role){
				$this->success('排序权重修改成功');
				exit;
			}else{
				$this->error('不好意思,排序权重修改失败');
				exit;
			}
		}
	}
	//角色添加
	public function roleAdd()
	{
		$RoleDb = D('Role');
		if(isset($_POST['name']))
		{
			if($RoleDb->create())
			{				
				$role_id = $RoleDb->add();				
				if(!$role_id)
				{
					$this->error("添加失败");
				}
				else
				{					
					 $this->assign("jumpUrl",U('/Admin/Rbac/roleList'));
					 $this->success('添加成功！');
				}
			}
			else
			{
				$this->error($RoleDb->getError());
			}
		}
		else
		{
			if($_GET['id']){	
				$id=$_GET['id'];
				$info = $RoleDb->where('`id` ='.$id)->find();
				$this->assign('tpltitle','修改');
				$this->assign('info',$info);
			}
			$this->assign('tpltitle','添加');
			$this->display();
		}
	}
	//角色修改
	public function roleEdit()
	{
		if(isset($_POST[id]))
		{
			$RoleDb = D('Role');
			//根据表单提交的POST数据更新数据对象
			if($RoleDb->create()){
				if($RoleDb->save()){
					$this->assign("jumpUrl",U('/Admin/Rbac/roleList'));
    				$this->success('角色修改成功！');
				}else{
					 $this->error('角色修改失败!');
				}
			}else{
				$this->error($RoleDb->getError());
			}
		}
		
	}
	//角色删除
	public function roleDel()
	{
		$RoleDb = D('Role');
		$id=$_GET['id'];
		$role = $RoleDb->where('`id`='.$id)->delete();
		if($role){
		    $this->assign("jumpUrl",U('/Admin/Rbac/roleList'));
				$this->success('角色删除成功！');
			}else{
				 $this->error('角色删除失败!');
			}
	}
	
	//节点列表
	public function nodeList()
	{
		$Node = D('Node')->getAllNode();
		$array = array();
		// 构建生成树中所需的数据
		foreach($Node as $k => $r) {
			$r['id']      = $r['id'];
			//判断是否有子节点
			$judge_son =D('Node')->where('`pid` ='. $r['id'])->count();  //判断子节点
			if($judge_son)
			{
				$str = '该模块下包含'.$judge_son.'个节点，你确定要删除该模块以及该模块下的节点吗？';
			}
			else
			{
				$str='你确定删除该模块吗？';	
			}			
			$r['title']   = $r['title'];
			$r['name']    = $r['name'];
			$r['status']  = $r['status']==1 ? '<font color="red" class="icon-check"></font>' :'<font color="blue" class="icon-times"></font>';
			$r['submenu'] = $r['level']==3 ? '<font color="#cccccc" class="icon-th-large" title="添加子菜单"></font>' : "<a href='".U('/Admin/Rbac/nodeAdd/id/'.$r['id'])."' class='icon-th-large' title='添加子菜单'></a>";
			$r['edit']    = $r['level']==1 ? '<font color="#cccccc" class="icon-pencil" title="修改"></font>' : "<a href='".U('/Admin/Rbac/nodeAdd/id/'.$r['id'].'/pid/'.$r['pid'])."' class='icon-pencil'  title='修改'></a>";
			$r['del']     = $r['level']==1 ? '<font color="#cccccc" class="icon-trash-o" title="删除"></font>' : "<a href='javascript:'  onClick=\"layer.confirm('你确定删除该用户？', {btn: ['确定','取消']}, function(){location.href='".U('/Admin/Rbac/nodeDel/id/'.$r['id'])."'});\" href='javascript:void(0)' class='icon-trash-o' title='删除'></a>";
			switch ($r['display']) {
				case 0:
					$r['display'] = '不显示';
					break;
				case 1:
					$r['display'] = '主菜单';
					break;
				case 2:
					$r['display'] = '子菜单';
					break;
			}
			switch ($r['level']) {
				case 0:
					$r['level'] = '非节点';
					break;
				case 1:
					$r['level'] = '应用';
					break;
				case 2:
					$r['level'] = '模块';
					break;
				case 3:
					$r['level'] = '方法';
					break;
			}
			$array[]      = $r;
		}
		$str  = "<tr class='tr'>
				    <td align='center'><input type='text' value='\$sort' size='3' name='sort[\$id]' style='color:#58f;'></td>
				    <td align='center' style='color:#58f;'>\$id</td> 
				    <td style='color:#03c;'>\$spacer \$title (\$name)</td> 
				    <td align='center'>\$level</td> 
				    <td align='center'>\$status</td> 
				    <td align='center'>\$display</td> 
					<td align='center'>
						\$submenu &nbsp;&nbsp; \$edit &nbsp;&nbsp; \$del
					</td>
				  </tr>";
		import('Org.Util.Tree');// 导入分页类
  		$Tree = new \Tree();
		$Tree->icon = array('&nbsp;&nbsp;&nbsp;│ ','&nbsp;&nbsp;&nbsp;├─ ','&nbsp;&nbsp;&nbsp;└─ ');
		$Tree->nbsp = '&nbsp;&nbsp;&nbsp;';
		$Tree->init($array);
		$html_tree = $Tree->get_tree(0, $str);
		$this->assign('html_tree',$html_tree);
		$this->display();
	}
	
	//节点添加
	public function nodeAdd()
	{
	    //表单提交
		if(isset($_POST[name])){
		  //是否子菜单
			if($_POST['id']){
				$date['pid']=$_POST['id'];
				$date['name']= $_POST['name'];
				$date['title']= $_POST['title'];
				$date['status']= $_POST['status'];
				$date['display']= $_POST['display'];
				$date['level']=$_POST['level'];
				$NodeDB = M("Node");
					if($NodeDB->add($date)){
							$this->assign("jumpUrl",U('/Admin/Rbac/nodeList'));
							$this->success('子菜单添加成功！');
						}else{
							 $this->error('子菜单添加失败!');
						}		
				}else{
					$NodeDB = M("Node");
					//根据表单提交的POST数据创建数据对象
					if($NodeDB->create()){
						if($NodeDB->add()){
							$this->assign("jumpUrl",U('/Admin/Rbac/nodeList'));
							$this->success('添加成功！');
						}else{
							 $this->error('添加失败!');
						}
					}
			 }
		}
		else
		{
			if($_GET['pid']){
			//节点信息
			$info_id=isset($_GET[id])?intval($_GET[id]):0;
			$info = D('Node')->where('`id` = '.$info_id)->find();
			$pid=isset($_GET[pid])?intval($_GET[pid]):0;
			}else{
				$info_id=isset($_GET[id])?intval($_GET[id]):0;
				$superior = D('Node')->where('`id` = '.$info_id)->find();
				$this->assign('superior',$superior);
			}
			$Node = D('Node')->getAllNode();	
			$array = array();
			foreach($Node as $k => $r) {
				$r['id']         = $r['id'];
				$r['title']      = $r['title'];
				$r['name']       = $r['name'];
				$r['disabled']   = $r['level']==3 ? 'disabled' : '';
				$array[$r['id']] = $r;
			}
			$str  = "<option value='\$id'  \$selected \$disabled >\$spacer \$title</option>";
			import('Org.Util.Tree');// 导入通用树型类
			$Tree = new \Tree();
			$Tree->init($array);
			$select_categorys = $Tree->get_tree(0, $str,$_GET['pid']);
			$this->assign('select_categorys',$select_categorys);

			$this->assign('info',$info);
			$this->assign('tpltitle','添加');
			$this->assign('pid',$pid);
			$this->display();
		}
	}
	//部门列表
	public function branch_list()
	{
		$mono_branchDB = M('mono_branch');
		$branch = $mono_branchDB->where('`pid` = 0')->select();
		foreach($branch as $branch_k => $branch_v)
		{
			$branch[$branch_k]['detail'] = $mono_branchDB->where('`pid` = '.$branch_v['id'])->order('id')->select();
		}
		//dump($branch);exit;
		$this->list = $branch;
		$this->display();
	}
	
	//部门新增 修改
	public function branchAdd()
	{
		$mono_branchDB = M('mono_branch');
		$branch = $mono_branchDB->where('`is_work` = 1 and `pid` = 0')->select();
		//提交修改
		if(IS_POST)
		{
			if(I('post.type'))
			{
				if(I('post.type')=="add")//添加
				{
					$date['pid']=I('post.pid');
					$date['branch_name']=I('post.branch_name');
					$date['is_work']= I('post.status');  //1 开启  0 关闭
					$return = $mono_branchDB  -> add($date);
					if($return)
					{
						$this->success('新增成功！！',U('/Admin/Rbac/branch_list'));
					}
					else
					{
						$this->error('新增失败！！');	
					}
				}
				elseif(I('post.type')=="update" && I('post.id'))//修改
				{
					$date['pid']=I('post.pid');
					$date['branch_name']=I('post.branch_name');
					$date['is_work']= I('post.status');  //1 开启  0 关闭
					$return = $mono_branchDB  -> where('`id` ='.I('post.id'))->save($date);
					if($return)
					{
						$this->success('修改成功！！',U('/Admin/Rbac/branch_list'));
					}
					else
					{
						$this->error('修改失败！！');	
					}
				}
				else
				{
					$this->error('出现错误！！');	
				}
			}
			else
			{
				$this->error('出现错误！！');	
			}
		}
		elseif(IS_GET)
		{
			foreach($branch as $branch_k => $branch_v)
			{
				$branch[$branch_k]['detail'] = $mono_branchDB->where('`is_work` = 1 and `pid` = '.$branch_v['id'])->order('id')->select();
			}
			if(I('get.pid'))
			{
				$up = $mono_branchDB->where('`id` ='.I('get.pid'))->getField('branch_name');
				$this-> select_branch = $up;
			}
			if(I('get.type') == 'add')
			{
				$this->type =I('get.type');
				$this -> tpltitle = "添 加";	
			}
			else
			{
				$branch_val = $mono_branchDB->where('`id` = '.I('get.id'))->find();
				$this-> info = $branch_val;
				$this-> type =I('get.type');
				$this -> tpltitle = "修 改";
			}
			
			$this->list = $branch;
			$this->display();
		}
	}
	
	//修改节点
	public function nodeEdit()
	{
		if(isset($_POST[id]))
		{
			$NodeDB = M("Node");
			//根据表单提交的POST数据更新数据对象
			if($NodeDB->create()){
				if($NodeDB->save()){
					$this->assign("jumpUrl",U('/Admin/Rbac/nodeList'));
    				$this->success('修改成功！');
				}else{
					 $this->error('修改失败!');
				}
			}else{
				$this->error($NodeDB->getError());
			}
		}
	}
	//删除节点
	public function nodeDel()
	{
	    $info_id=isset($_GET[id])?intval($_GET[id]):0;
		$NodeDB = M("Node");
		$judge_son =$NodeDB->where('`pid` ='. $info_id)->select();  //判断子节点
		if($judge_son){
			foreach($judge_son as  $k=>$v){
				$id.=$v[id].',';
				}
			$whereId = '`id` in (' . $id . $info_id . ')'; 
			$result =$NodeDB->where($whereId)->delete();  //删除			
			}else{	
			$result =$NodeDB->where('`id` ='. $info_id)->delete();  //删除	
			}
        if ($result) {
            $this->success('删除成功');
            exit;
        } else {
            $this->error('不好意思,删除失败');
            exit;
        }
	}	
	//节点排序
	public function nodeSort()
	{
		$NodeDB = M("Node");
		$sort = $_POST['sort'];
		$v=array();
		$a=0;
		foreach($sort as  $k=>$v){
			$date['sort']=$v;
			$node[$a]=$NodeDB->where('`id` ='. $k)->save($date);  //查找节点
			$a++;
			}
        if ($node) {
            $this->success('排序权重修改成功');
            exit;
        } else {
            $this->error('不好意思,排序权重修改失败');
            exit;
        }
	}	
	//系统参数列表
	public function system_parameters()
	{
		$system_parametersDB=M('system_parameters');
		$info=$system_parametersDB->where('1=1')->select();
		$this->assign('info',$info);
		$this->display();
	}
	//系统参数 添加 修改
	public function system_parameters_add()
	{
		$system_parametersDB=M('system_parameters');
		if($_POST['name']){
			if($_POST['id']){
				$date['name']=$_POST['name'];
				$date['value_int']=$_POST['value_int'];
				$date['value_var']=$_POST['value_var'];
				$date['message']=$_POST['message'];
					if($system_parametersDB->where('`id` ='.$_POST['id'])->save($date)){
						$this->assign("jumpUrl",U('/Admin/Rbac/system_parameters'));
						$this->success('修改成功！');
					}else{
						 $this->error('修改失败!');
					}
			}
			else
			{
				$date['name']=$_POST['name'];
				$date['value_int']=$_POST['value_int'];
				$date['value_var']=$_POST['value_var'];
				$date['message']=$_POST['message'];
				$date['add_time']=time();
					if($system_parametersDB->add($date)){
						$this->assign("jumpUrl",U('/Admin/Rbac/system_parameters'));
						$this->success('添加成功！');
					}else{
						 $this->error('添加失败!');
					}
			}
		}
		else{
			if($_GET['id'])
			{ 
				$info=$system_parametersDB->where('`id` ='.$_GET['id'])->find();
				$this->assign('info',$info);
			    $this->assign('tpltitle',"修改");		
			}
			else
			{
			  $this->assign('tpltitle',"添加");	
			}
			$this->display();
		}
	}
	//系统参数删除
	public function system_parameters_delete()
	{
		$system_parametersDB=M('system_parameters');
		if($_GET['id'])
		{
			$info=$system_parametersDB->where('`id` ='.$_GET['id'])->delete();
			if($info){
				$this->success('删除成功！',U('/Admin/Rbac/system_parameters'));
			}else{
				$this->error('删除错误!');
			}
		}
		else
		{
			$this->error('出现错误!');
		}
	}
		
	//权限管理
	public function access()
	{
		$roleid=isset($_GET[roleid])?intval($_GET[roleid]):0;
        if(!$roleid) $this->error('参数错误!');
        import('Org.Util.Tree');//导入通用树型类

        $Tree = new \Tree();
        $Tree->icon = array('&nbsp;&nbsp;&nbsp;│ ','&nbsp;&nbsp;&nbsp;├─ ','&nbsp;&nbsp;&nbsp;└─ ');
        $Tree->nbsp = '&nbsp;&nbsp;&nbsp;';

        $NodeDB = D('Node');
        $node = $NodeDB->getAllNode();

        $AccessDB = D('Access');
        $access = $AccessDB->getAllAccess('','role_id,node_id,level');
       

        foreach ($node as $n=>$t) {
            $node[$n]['checked'] = ($AccessDB->is_checked($t,$roleid,$access))? ' checked' : '';
            $node[$n]['depth'] = $AccessDB->get_level($t['id'],$node);
            $node[$n]['pid_node'] = ($t['pid'])? ' class="tr lt child-of-node-'.$t['pid'].'"' : '';
        }
        $str  = "<tr id='node-\$id' \$pid_node>
                    <td style='padding-left:30px;'>\$spacer<input type='checkbox' name='nodeid[]' value='\$id' class='radio' level='\$depth' \$checked onclick='javascript:checknode(this);' > \$title (\$name)</td>
                </tr>";

        $Tree->init($node);
        $html_tree = $Tree->get_tree(0, $str);
        $this->assign('html_tree',$html_tree);

        $this->display();
    }
	
	public function accessEdit()
	{
		$roleid=isset($_POST[roleid])?intval($_POST[roleid]):0;
        $nodeid = $_POST['nodeid'];
        if(!$roleid) $this->error('参数错误!');
        $AccessDB = D('Access');

        if (is_array($nodeid) && count($nodeid) > 0) {  //提交得有数据，则修改原权限配置
            $AccessDB -> delAccess(array('role_id'=>$roleid));  //先删除原用户组的权限配置

            $NodeDB = D('Node');
            $node = $NodeDB->getAllNode();
			foreach ($node as $_v) $node[$_v[id]] = $_v;
            foreach($nodeid as $k => $node_id){
                $data[$k] = $AccessDB -> get_nodeinfo($node_id,$node);
                $data[$k]['role_id'] = $roleid;
            }
            $AccessDB->addAll($data);   // 重新创建角色的权限配置
        } else {    //提交的数据为空，则删除权限配置
            $AccessDB -> delAccess(array('role_id'=>$roleid));
        }
        $this->assign("jumpUrl",U('/Admin/Rbac/access',array('roleid'=>$roleid)));
        $this->success('设置成功！');
	}
	public function test()
	{
		$this->display();
	}
}
