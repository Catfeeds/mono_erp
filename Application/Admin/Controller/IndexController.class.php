<?php
namespace Admin\Controller;
use Think\Controller;
class IndexController extends CommonController {
    public function index(){
		$username = session('username');    // 用户名
		$user_login_recordDB = M('user_login_record');
		$order_business_messageDB =M('order_business_message');
		$login_coun = $user_login_recordDB->where('`username` = "'.$username.'"')->count();
		$login_time = $user_login_recordDB->where('`username` = "'.$username.'"')->order('login_time desc')->getField('login_time');
		//留言
		$order_business_message_val = $order_business_messageDB->where('`status` = 0 and `accept` like "%'.$username.'%"')->select();
		$order_business_message_coun = count($order_business_message_val);//留言个数
		
		
		
		$this->order_business_message_val = $order_business_message_val;
		//现在只做了留言  unread_messages_coun 为消息总数
		$unread_messages_coun = $order_business_message_coun ;
		$this->unread_messages_coun = $unread_messages_coun;
		
		
		$this->login_time = $login_time;
		$this->login_coun = $login_coun;
		$this-> username = $username;
        $this->display();
    }
    public function content(){
        $this->display();
    }
    public function login(){
        $this->assign("no_menu",true);
        $this->display();
    }
    public function system(){
        $this->display();
    }
    public function demo(){
        $this->display();
    }
	public function js(){
        $this->display();
    }
    public function chart(){
        //引入highcharts支持库
        vendor('Highcharts.Highcharts');
        
        //条形图(colume)、折线图(line)、面积图(area) 数据格式
        $series1 = array(
             array(
        		'data'  =>  array(113, 114, 113, 115, 114, 120, 122),
        		'name'  =>  'John',
		    ),
		    array(
		        'data'  =>  array(111, 113, 114, 113, 113, 115, 114),
		        'name'  =>  'Jane',
		    ),
		    array(
		        'data'  =>  array(112, 114, 119, 123, 120, 116),
		        'name'  =>  'Lee',
    		),
        );
        //print_r($series1);
        $day = array('Monday','Tuesday','Wednesday','Thursday',
                'Friday','Saturday','Sunday');
        $option1 = array(
            'container' => 'my_chart',//容器id，不能重复
            'type' => 'column',//图表类型 可选用: 折线line 曲线spline 柱状column 面积图area 曲线面积图areaspline 饼状pie
        
            'width' => '100%',//图表宽度,默认100%
            'height' => '400px',//图表宽度，默认400px
            'theme' => 'gray',//主题 可选用: dark-blue dark-green dark-unica gray grid-light grid sand-signika skies
        
            'title' => 'Myfarm',//图表标题
            'subtitle' => 'lolipop',//图表副标题
            'xtitle' => 'Date',//x轴名称
            'ytitle' => 'Fruit',//y轴名称
            'suffix' => ' kg',//数值单位
        
            'category' => $day,//固定的 x轴坐标
            'xmin' => 0,//线性的x轴坐标 起始  默认为0  与category之间选用其中一种
            'xstep' => 1,//线性的x轴坐标 步长 默认为1
        
            'ymin' => 110,//y轴初始值
        );
        $chart1 = getChirt($series1,$option1);
        $this->assign('chart1',$chart1);
        
        //饼状图 数据格式
        $series2 = array(
            'name' => '数量',//数据名称
            'data' => array(
                array(
                    'name' => 'Microsoft Internet Explorer',
                    'y' => 3,
                ),
                array(
                    'name' => 'Chrome',
                    'y' => 65,
                ),
                array(
                    'name' => 'Firefox',
                    'y' => 45,
                ),
                array(
                    'name' => 'Safari',
                    'y' => 47,
                ),
                array(
                    'name' => 'Opera',
                    'y' => 91,
                ),
                array(
                    'name' => 'Proprietary or Undetectable',
                    'y' => 102,
                ),
            ),
        );
        $option2 = array(
            'container' => 'mypie',
            'type' => 'pie',//图表类型 可选用: 折线line 曲线spline 柱状column 面积图area 曲线面积图areaspline 饼状pie
        
            'width' => '100%',//图表宽度,默认100%
            'height' => '400px',//图表宽度，默认400px
            
            'theme' => 'gray',//主题 可选用: dark-blue dark-green dark-unica gray grid-light grid sand-signika skies
            'export' => true,
        
//             'title' => 'Myfarm',//图表标题
//             'subtitle' => 'lolipop',//图表副标题
//             'xtitle' => 'Date',//x轴名称
//             'ytitle' => 'Fruit',//y轴名称
//             'suffix' => ' kg',//数值单位
        
//             'category' => array('Monday','Tuesday','Wednesday','Thursday',
//                 'Friday','Saturday','Sunday'),//固定的 x轴坐标
//             'xstep' => 1,//线性的x轴坐标 步长 默认为1
//             'xmin' => 0,//线性的x轴坐标 起始  默认为0  与category之间选用其中一种
        
//             'ymin' => 110,//y轴初始值
        );
        
        $chart2 = getChirt($series2,$option2);
        $this->assign('chart2',$chart2);

        $this->display();
    }
	//私信
	public function messages()
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
		$this->display();
	
	}
	//私信 即时消息
	public function messages_instant()
	{
		//layout(false); // 临时关闭当前模板的布局功能
		
		$this->display();
	}
	//聊天界面
	public function messages_val()
	{
		layout(false); // 临时关闭当前模板的布局功能
		$id=I('get.id');
		$userDB = M('user');
		$user_info = $userDB ->where('`id` = '.$id)->find();
		
		$this->user_info = $user_info;
		$this->display();
	}
}