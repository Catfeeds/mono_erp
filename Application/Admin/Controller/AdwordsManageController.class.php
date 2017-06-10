<?php
	namespace Admin\Controller;
	use Think\Controller;
	class AdwordsManageController extends CommonController
	{
		public function index()
		{
			$this->display();
		} 
		//添加广告费
		public function adwords_fee_add()
		{
			$adwords_fee = M("adwords_fee");
			if(isset($_POST['dosubmit']))
			{
				$data["come_from"] = $_POST["come_from"];
	    		$data["fee"] = $_POST["fee"];
	    		$data["operate"] = $_POST["operate"];
	    		$data["country"] = $_POST["country"];
	    		$data["fee_date"] = $_POST["fee_date"];
	    		$insert = $adwords_fee->add($data);
				if($insert)
	    		{
	    			$this->success('广告费添加成功！',U('/Admin/AdwordsManage/adwords_fee'));
	    		}
	    		else
	    		{
	    			$this->error("广告费添加失败！");
	    		}
			}
			else
			{
				if(isset($_GET['id']))
				{
					$id = $_GET['id'];
					$info = $Project->where("id=$id")->find();
					$fee_date = strtotime($info[fee_date]);
					$this->assign("info",$info);
					$this->fee_date=$fee_date;
					$this->assign('tpltitle','修改');
				}
				else
				{
					$this->assign('tpltitle','添加');
				}
			}
			$this->display();
		}
		//广告费列表
		public function adwords_fee()
		{
			$adwords_fee = M("adwords_fee");
			
			//import('Org.Util.Page');// 导入分页类
			$count = $adwords_fee->count();// 查询满足要求的总记录数 $map表示查询条件
			$Page = new \Think\Page1($count);// 实例化分页类 传入总记录数
			// 进行分页数据查询 注意page方法的参数的前面部分是当前的页数使用 $_GET[p]获取
			$nowPage = isset($_GET['p'])?$_GET['p']:1;
			$show = $Page->show();// 分页显示输出
			get_to_order(46538);
			if(isset($_POST['sub']))
			{
				$begin_time = $_POST['begin_time'];
				$end_time = $_POST['end_time'];
			}
			else 
			{
				$begin_time=date("Y-m-01",time());
				$end_time=date("Y-m-d",(time()+3600*24));
			}
			$map['fee_date'] = array("between","$begin_time,$end_time");
			if(isset($_GET['come_from']))
			{
				$map['come_from'] = $_GET['come_from'];
				$adwords_fee_list = $adwords_fee->where($map)->order('fee_date desc')->page($nowPage.','.C('LISTROWS'))->select();
			}
			else 
			{
				$adwords_fee_list = $adwords_fee->where($map)->order('fee_date desc')->page($nowPage.','.C('LISTROWS'))->select();
			}
			$this->assign("adwords_fee_list",$adwords_fee_list);
			$this->assign("page",$show);
			$this->display();
		}
		//广告费删除
		public function adwords_fee_del()
		{
			$adwords_fee = M("adwords_fee");
			if(isset($_GET['id']))
			{
				$id = $_GET['id'];
				$fee_delete = $adwords_fee->where("id=$id")->delete();
				if($fee_delete)
				{
					$this->success('广告费删除成功！',U('/Admin/AdwordsManage/adwords_fee',array('come_from'=>$_GET['come_from'])));
	    		}
	    		else
	    		{
	    			$this->error("广告费删除失败！");
	    		}
			}
		}
		//APR统计
		public function adwords_apr()
		{
			$this->display();	
		}
	}
?>