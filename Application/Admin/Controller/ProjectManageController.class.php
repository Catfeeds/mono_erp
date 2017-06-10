<?php
namespace Admin\Controller;
use Think\Controller;
class ProjectManageController extends CommonController
{
    public function index()
    {
        $this->display();
    }
    
    public function project_add()
    {
    	$Project = M("project");
    	//建任务
    	if(isset($_POST["dosubmit"]))
    	{	
    		$data["name"] = $_POST["name"];
    		$data["message"] = $_POST["content"];
    		$data["proposer"] = $_POST["proposer"];
    		$data["begin_time"] = $_POST["begin_time"];
    		$data["end_time"] = $_POST["end_time"];
    		$data["start_time"] = '';
    		$data["finish_time"] = '';
    		$data['executers'] = '';
    		$data["status"] = $_POST["status"];
    		$insert = $Project->add($data);
    		if($insert)
    		{
    			$this->success('任务添加成功！',U('/Admin/ProjectManage/project_list'));
    		}
    		else
    		{
    			$this->error("任务添加失败！");
    		}
    	}
    	else
    	{
    		if(isset($_GET['id']))
    		{
    			$id = $_GET['id'];
    			$info = $Project->where("id=$id")->find();
    			$begin_time = strtotime($info[begin_time]);
    			$end_time = strtotime($info[end_time]);
    			$this->assign("info",$info);
    			$this->begin_time=$begin_time;
    			$this->end_time=$end_time;
    			$this->assign('tpltitle','修改');	
    		}
    		else 
    		{
    			$this->assign('tpltitle','添加');	
    		}
    	}
    	$this->display();
    }
    //任务开始
    public function project_start()
    {
    	$Project = M("project");
    	if(isset($_POST['dosubmit']))
    	{
    		$id = $_GET['project_id'];
    		$data['start_time'] = $_POST['start_time'];
    		$Project_start = $Project->where(array('id'=>$id))->save($data);
    		if($Project_start)
    		{
    			$this->success("任务开始成功",U('/Admin/ProjectManage/project_list'));
    		}
    		else
    		{
    			$this->error("任务开始失败");
    		}
    	}
    }
    //任务申请修改
    public function project_edit()
    {
    	$Project = M("project");
    	if(isset($_POST['id']))
    	{
    		$id = $_POST['id'];
    		$data["name"] = $_POST["name"];
    		$data["message"] = $_POST["content"];
    		$data["proposer"] = $_POST["proposer"];
    		$data["begin_time"] = $_POST["begin_time"];
    		$data["end_time"] = $_POST["end_time"];
    		$data["status"] = $_POST["status"];
    		$pro = $Project->where("id=$id")->save($data);
    		
    		if($pro)
    		{
    			$this->success("任务申请修改成功",U('/Admin/ProjectManage/project_list'));
    		}
    		else
    		{
    			$this->error("任务申请修改失败");
    		}
    	}
    }
    //任务申请列表
    public function project_list()
    {
    	$Project = M("project");
    	//import('Org.Util.Page');// 导入分页类
    	$count = $Project->count();// 查询满足要求的总记录数 $map表示查询条件
    	$Page = new \Think\Page1($count,C('LISTROWS'));// 实例化分页类 传入总记录数
    	// 进行分页数据查询 注意page方法的参数的前面部分是当前的页数使用 $_GET[p]获取
    	$show = $Page->show();// 分页显示输出
    	$List = $Project->order('id asc')->limit($Page->firstRow.','.$Page->listRows)->select();
    	$this->assign("list",$List);
    	$this->assign("page",$show);
    	$this->display();
    }
    //任务申请删除
    public function project_del()
    {
    	$Project = M("project");
    	if(isset($_GET['id']))
    	{
    		$id = $_GET['id'];
    		$Del = $Project->where("id=$id")->delete();
    		if($Del)
    		{
    			$this->success("任务申请删除成功",U('/Admin/ProjectManage/project_list'));
    		}
    		else 
    		{
    			$this->error("任务申请删除失败");
    		}
    	}
    }
    public function projectStart()
    {
    	$Project = M("project");
    	if(isset($_GET['id']))
    	{
    		$id = $_GET['id'];
    		//查询某个字段的值 
    		$Start = $Project->where("id=$id")->getField("status",1);
    		if($Start)
    		{
    			$this->success("任务开启成功",U('/Admin/ProjectManage/project_list'));
    		}
    		else 
    		{
    			$this->error("任务开启失败");
    		}
    	}
    }
    
    public function project_finish()
    {
    	$Project = M("project");
    	if(isset($_POST['dosubmit']))
    	{
    		$id = $_GET['project_id'];
    		$data['status'] = 2;
    		$data['executers'] = $_POST['executers'];
    		$data['finish_time'] = $_POST['finish_time'];
    		$Finish = $Project->where("id=$id")->save($data);
    		if($Finish)
    		{
    			$this->success("任务完成成功",U('/Admin/ProjectManage/project_list'));
    		}
    		else 
    		{
    			$this->error("任务完成失败");
    		}
    	}
    }
    //添加子任务
    public function project_subtask_add()
    {
    	$project_process = M("project_process");
    	if(isset($_POST['dosubmit']))
    	{	
    		$data['project_id'] = $_GET['project_id'];
    		$data['operator'] = $_POST['operator'];
    		$data['message'] = $_POST['content'];
    		$data['begin_time'] = $_POST['begin_time'];
    		$data['end_time'] = $_POST['end_time'];
    		$data['level'] = $_POST['level'];
    		$insert = $project_process->add($data);
    		if($insert)
    		{
    			$this->success("子任务添加成功",U('/Admin/ProjectManage/project_subtask',array('id'=>$_GET['project_id'])));
    		}
    		else 
    		{
    			$this->error("子任务添加失败");
    		}
    	}

    	else 
    	{
    		if(isset($_GET['projectprocess_id']))
    		{
    			$id = $_GET['projectprocess_id'];
    			$info = $project_process->where("id=$id")->find();
    			$this->assign("info",$info);
    			$begin_time = strtotime($info[begin_time]);
    			$end_time = strtotime($info[end_time]);
    			$this->begin_time=$begin_time;
    			$this->end_time=$end_time;
    			$this->assign('tpltitle','修改');
    			$this->display();
    		}
    	}
    	
    }
    //子任务列表
    public function project_subtask()
    {
    	$project_process = M("project_process");
    	//import('Org.Util.Page');// 导入分页类
    	if(isset($_GET['id']))
    	{
    		$project_id = $_GET['id'];
    		$count = $project_process->where("project_id=$project_id")->count();
    		$Page = new \Think\Page1($count);// 实例化分页类 传入总记录数
    		// 进行分页数据查询 注意page方法的参数的前面部分是当前的页数使用 $_GET[p]获取
    		$nowPage = isset($_GET['p'])?$_GET['p']:1;
    		$show = $Page->show();// 分页显示输出
    		$List = $project_process->where("project_id=$project_id")->order('level asc')->page($nowPage.','.C('LISTROWS'))->select();
    		$this->assign("list",$List);
    		$this->assign("page",$show);
    	}
    	$this->display();
    }
    //修改子任务
    public function project_subtask_edit()
    {
    	$project_process = M("project_process");
    	if(isset($_POST['id']))
    	{
    		$id = $_POST['id'];
    		$data['project_id'] = $_GET['id'];
    		$data['operator'] = $_POST['operator'];
    		$data['message'] = $_POST['content'];
    		$data['begin_time'] = $_POST['begin_time'];
    		$data['end_time'] = $_POST['end_time'];
    		$data['level'] = $_POST['level'];
    		$pro = $project_process->where("id=$id")->save($data);
    		if($pro)
    		{
    			$this->success("子任务修改成功",U('/Admin/ProjectManage/project_subtask',array('id'=>$_GET['id'])));
    		}
    		else
    		{
    			$this->error("子任务修改失败");
    		}
    	}	
    }
    //删除子任务
    public function project_subtask_del()
    {
    	$project_process = M("project_process");
    	if(isset($_GET['projectprocess_id']))
    	{
    		$id = $_GET['projectprocess_id'];
    		$Del = $project_process->where("id=$id")->delete();
    		if($Del)
    		{
    			$this->success("子任务删除成功",U('/Admin/ProjectManage/project_subtask',array('id'=>$_GET['id'])));
    		}
    		else
    		{
    			$this->error("子任务删除失败");
    		}
    	}	
    }
    //任务审查
    public function project_subtask_check()
    {
    	$project_check = M("project_check");
		if(isset($_POST['dosubmit']))
		{
			//判断是否审核过
			$map['project_id'] = $_GET['id'];
			$map['project_process_id'] = $_GET['projectprocess_id'];
			$projectcheckList = $project_check->where($map)->select();
			if($projectcheckList)
			{
				$this->success("子任务已经审查成功",U('/Admin/ProjectManage/project_subtask',array('id'=>$_GET['id'])));
			}
			else 
			{
				$data['project_id'] = $_GET['id'];
				$data['project_process_id'] = $_GET['projectprocess_id'];
				$data['operator'] = $_POST['operator'];
				$data['status'] = $_POST['status'];
				$data['date_time'] = $_POST['operator_date'];
				$projectcheck_insert = $project_check->add($data);
				if($projectcheck_insert)
				{
					$this->success("子任务审查成功",U('/Admin/ProjectManage/project_subtask',array('id'=>$_GET['id'])));
				}
				else
				{
					$this->error("子任务审查失败");
				}
			}	
			
		}
		$this->display();
    }
    //任务完成
    public function project_subtask_finish()
    {
    	if(isset($_POST['dosubmit']))
    	{
    		$project_check = M("project_check");
    		$map['project_id'] = $_GET['id'];
    		$map['project_process_id'] = $_GET['projectprocess_id'];
    		//$projectcheckList=获取id值
    		$projectcheckList = $project_check->where($map)->getField("id");
    		if($projectcheckList)
    		{
    			$project_operator = M("project_operator");
    			$data['project_id'] = $_GET['id'];
    			$data['project_process_id'] = $_GET['projectprocess_id'];
    			$data['project_check_id'] = $projectcheckList;
    			$data['operator'] = $_POST['operator'];
    			$projectoperatorList = $project_operator->add($data);
    			if($projectoperatorList)
    			{
    				$this->success("子任务审查完成成功",U('/Admin/ProjectManage/project_subtask',array('id'=>$_GET['id'])));
    			}
    			else 
    			{
    				$this->error("子任务审查完成失败");
    			}
    		}
    		else 
    		{
    			$this->success("请先审查",U('/Admin/ProjectManage/project_subtask',array('id'=>$_GET['id'])));
    		}
    	}
    	$this->display();
    }
}
