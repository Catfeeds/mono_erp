<?php
namespace Admin\Controller;
use Admin\Controller\CommonController;
use Think\Controller;

class MessagesController extends CommonController
{
	public function Index()
	{
		//layout(false); // 临时关闭当前模板的布局功能
		$mono_branchDB = M('mono_branch');
		$userDB = M('user');
		$branch = $mono_branchDB->where('`pid` = 0')->select();
		foreach($branch as $branch_k => $branch_v)
		{
			$branch[$branch_k]['personnel'] = $userDB ->field('id,name')->where('`branch` = "'.$branch_v['branch_name'] .'" and `status` =1')->select();
			$branch[$branch_k]['coun'] = count($branch[$branch_k]['personnel']); 
			$branch[$branch_k]['detail'] = $mono_branchDB->where('`pid` = '.$branch_v['id'])->order('id')->select();
			foreach($branch[$branch_k]['detail'] as $detail_k => $detail_v)
			{
				$branch[$branch_k]['detail'][$detail_k]['personnel'] = $userDB ->field('id,name')->where('`branch` = "'.$detail_v['branch_name'] .'" and `status` =1')->select();
				$branch[$branch_k]['detail'][$detail_k]['coun'] = count($branch[$branch_k]['detail'][$detail_k]['personnel'] ); //二级采单下面成员数量
				$branch[$branch_k]['coun'] +=$branch[$branch_k]['detail'][$detail_k]['coun'];//顶级采单下面成员数量
			}
		}
	//	dump($branch);exit;
		$this->branch_list = $branch;
		if(I('get.name'))
		{
		    $this->name =I('get.name');	
		}
		
		$this->display();
	}
	//提交私信内容
	public function content_submit()
	{
		$messges_recordDB = M('messges_record');
		$messges_record_detailDB = M('messges_record_detail');
		
		if(IS_POST)
		{
			$theme = I('post.theme');
			$content = I('post.content');
			$name=explode(';',I('post.name'));
			
			$date['operator'] =session('username');
			$date['content'] = $content;
			$date['date_time'] = date("Y-m-d H:i:s",time());
			$messges_record_detail_id = $messges_recordDB->add($date);
			if($messges_record_detail_id)
			{
				foreach($name as $k=>$v)
				{
					$detail['messges_record_id'] = $messges_record_detail_id;
					$detail['theme'] =ltrim($theme);
					$detail['accept'] = $v;		
					$return = $messges_record_detailDB->add($detail);
				}
				if($return)
				{
					$this->success('发送成功！');	
				}
				else
				{
					$this->error('发送失败！');	
				}
			}
			else
			{
				$this->error('发送失败！');	
			}
		}
		else
		{
		 $this->error('参数错误');	
			
		}
	}
	
	//收件列表
	public function receive_list()
	{
		$messges_recordDB = M('messges_record');
		$messges_record_detailDB = M('messges_record_detail');
		$userDB =M('user');
		$username = session('username');
		
		$name= $userDB ->where('`username` ="'.$username.'"')->getField('name');
		
		$receive_count = count($messges_record_detailDB->where('`accept` ="'.$name.'"')->group('messges_record_id')->select());
		$Page       = new \Think\Page1($receive_count);// 实例化分页类 传入总记录数
		$nowPage = isset($_GET['p'])?$_GET['p']:1;
		$show       = $Page->show();// 分页显示输出
		
		$receive_list = $messges_record_detailDB->where('`accept` ="'.$name.'"')->group('messges_record_id')->page($nowPage,8)->order('id desc')->select();
		foreach($receive_list as $k=>$v)
		{
			$receive_list[$k]['main'] = $messges_recordDB->where('`id` = '.$v['messges_record_id'])->find();
		}
		
		
		$this->receive_list = $receive_list;
		$this->page = $show;

		$this->Index();
	}
	
	//内容查看
	public function receive_info()
	{
		
		$messges_recordDB = M('messges_record');
		$messges_record_detailDB = M('messges_record_detail');
		$username = session('username');
		$name = M('user')->where('`username` ="'.$username.'"')->getField('name');
		if(I('get.id'))
		{
			$info = $messges_recordDB->where('`id` ='.I('get.id'))->find();
			
			$info['content'] = htmlspecialchars_decode($info['content']);
			$info['detaik'] = $messges_record_detailDB->where('`messges_record_id` ='.I('get.id').' and `accept` ="'.$name.'"')->find();
			
			if($info['operator'] != $username && $info['detaik']['accept'] != $name)
			{
				$this->error('参数错误');				
			}
			else
			{
				$this->info = $info;
			}
			
			
			//dump($info);exit;
		}
		else
		{
		
			$this->error('参数错误');
		
		}
		$this->Index();
		
	}
	
	
	public function send_list()
	{
		$messges_recordDB = M('messges_record');
		$messges_record_detailDB = M('messges_record_detail');
		$username = session('username');
		
		$coun = $messges_recordDB->where('`operator` ="'.$username.'"')->count();
		$Page       = new \Think\Page1($coun);// 实例化分页类 传入总记录数
		$nowPage = isset($_GET['p'])?$_GET['p']:1;
		$show       = $Page->show();// 分页显示输出
		$send_list = $messges_recordDB->where('`operator` ="'.$username.'"')->page($nowPage,8)->order('id desc')->select();
		foreach($send_list as $k=>$v)
		{
			$send_list[$k]['detail'] = $messges_record_detailDB ->where('`messges_record_id` ='.$v['id']) ->select();
		}
		$this->page = $show;
		$this->send_list = $send_list;
		$this->Index();
	}
}