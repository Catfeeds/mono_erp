<?php
// error_reporting(0);
set_time_limit(0);
include_once dirname(__FILE__).'/Classes/PHPExcel.php';
global $TRANS;
$TRANS = array('A','B','C','D','E','F','G','H','I','J','K','L','M','N','O','P','Q','R','S','T','U','V','W','X','Y','Z',
    'AA','AB','AC','AD','AE','AF','AG','AH','AI','AJ','AK','AL','AM','AN','AO','AP','AQ','AR','AS','AT','AU','AV','AW','AX','AY','AZ');				
	
//导出excel
function export_excel($data,$title='export',$muti_sheet=false)
{
    global $TRANS;
    $objPHPExcel = new PHPExcel();
    if($muti_sheet==false) $data = array($data);//对 多工作表支持，此时$data是三维数组
//     dump($data);exit;
    $k=0;
    foreach($data as $sheet_key=>$sheet_val) //sheet
    {
    	$objPHPExcel->createSheet($k);//建sheet
    	$objPHPExcel->setActiveSheetIndex($k);
    	$objActSheet=$objPHPExcel->getActiveSheet();
    	if( !is_numeric($sheet_key) ) //sheet name
    	{
    		$objActSheet->setTitle($sheet_key);
    	}
    	for($i=0;$i<count($sheet_val);$i++) //row
    	{
    		$j=0;
    		foreach ($sheet_val[$i] as $key=>$val)//column 考虑非数字索引，用foreach
    		{
    			if($i==0){$objActSheet->getColumnDimension($TRANS[$j])->setAutoSize(true);}
    			if(!$val) $val = '';
    			$objActSheet->setCellValue($TRANS[$j].($i+1),$val);
    			$j++;
    		}
    	}
    	$k++;
    }
	
    ob_end_clean();
    header('Content-Type:text/html;charset= UTF-8');
    header('Pragma:public');
    header('Content-Type:application/x-msexecl;name="'.$title.'.xls"');
    header('Content-Disposition:inline;filename="'.$title.'.xls"');
    
    $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
    $objWriter->save('php://output');
    unset($objPHPExcel);
}

//导入excel
function importExcel($option)
{
    global $TRANS;
	extract($option);
    $log = array();//记录 每一个单元格 日志信息
    
	$start_row || $start_row = 2;
// 	$start_row = $start_row ? $start_row : 2;
    $model = M($table);//实例化数据库模型
    $objReader = PHPExcel_IOFactory::createReader('Excel5');
    $objPHPExcel = $objReader->load($uploadfile);
    $sheet_index || $sheet_index=0;//工作表（sheet）索引
    $sheet = $objPHPExcel->getSheet($sheet_index);
    $highestRow = $sheet->getHighestRow(); // 取得总行数
// 	$highestRow -= 1; // 我导出excel时总是比总数据多一行。。。导入的时候把空行去掉 。。。。。。。第二次打开文件的时候又正常了。。。
    $highestColumn = $sheet->getHighestColumn(); // 取得总列数
    $highestColumnNum = array_search($highestColumn, $TRANS)+1;

    for($j=$start_row;$j<=$highestRow;$j++) // 数据从表格的第x行开始
    {
        $data = array();//要插入/修改的数据

        $row = array();//一行的所有数据
        $state = 'success';//记录 该行 是否不合法
        //取出一行所有数据
        for ($k=0;$k<$highestColumnNum;$k++)
        {
            $row[$TRANS[$k]] = trim($sheet->getCell($TRANS[$k].$j)->getFormattedValue());
        }
        if($callback)//callback模式
        {
        	$log[$j] = $callback($row,$obj);
        	if($log[$j]['fatal_error'])//致命错误
        	{
        		return $log;
        	}
        }
        else//单表模式
        {
        	foreach($field as $key=>$val)
        	{
        		$value = $row[$key];
        		if($val[1])//单元格回调
        		{
        			$fn_name = $val[1];
//         			$pre = substr($val[1],0,7);
//         			if($pre=='simple_')//内置的常用审核过滤函数，约定simple_开头命名，欢迎大家添加
//         			{
//         				$return = $fn_name($value);
//         			}
//         			else
//         			{
//         				$return = $obj->$fn_name($value);
//         			}
        			$return = $fn_name($value);
        			$value = $return['data'];
        	
        			if($return['msg']) $log[$j][$key] = $return['msg'];
        			if($return['state']=='warning')//处理该行状态
        			{
        				$state=='error' || $state='warning';
        			}
        			elseif($return['state']=='error')
        			{
        				$state = 'error';
        			}
        		}
        		$data[$val[0]] = $value;
        		if($mode=='edit' && $key==$primary){
					$primary_value = $value;//主键对应值
        		}
        	}
        	
        	if($state=='error')//如果数据不合法，直接记录state，不作数据库操作
        	{
        		$log[$j]['state'] = 'invalid';//验证内容时标记 非法
        	}
        	else if($mode=='add'){
        		try{//对数据库操作异常处理
        			$result = $model->add($data);
        			if($result) $log[$j]['state'] = 'success';//成功
        			else $log[$j]['state'] = 'none';//影响0行
        		}catch(Exception $e){
        			$log[$j]['state'] = 'exception';
        		}
        		
        	}
        	else if($mode=='edit'){
        		try{
        			$skip = $model->where($data)->find();//如果能查询到，说明这行没有修改
        			if($skip) $log[$j]['state'] = 'skip';
        		}catch(Exception $e){
        			$log[$j]['state'] = 'exception';
        			echo $e->getMessage();
        		}
        		if(!$skip){
        			try{
        				$result = $model->where(" {$field[$primary][0]}='$primary_value' ")->save($data);
        				if($result) $log[$j]['state'] = 'success';
        				else $log[$j]['state'] = 'none';//影响0行
        			}catch(Exception $e){
        				$log[$j]['state'] = 'exception';
        			}
        		}
        	}
        	$log[$j]['result'] = $state;
        }
    }
    if($unlink) unlink($uploadfile);//删除上传文件
    return $log;//返回日志信息
}

//常用验证方法，约定以simple_开头命名
function simple_check_empty($value)
{
	if(!$value){ $msg = '空'; $state = 'warning'; }
    return array('data'=>$value,'msg'=>$msg,'state'=>$state);
}
function simple_check_num($value)//验证纯数字
{
    if( !preg_match("/^\d+$/",$value) ){ $msg = '只能包含数字'; $state = 'warning'; }
    return array('data'=>$value,'msg'=>$msg,'state'=>$state);
}
function simple_check_float($value)//验证数字（包括小数）
{
    if( !preg_match("/^[0-9]+(\.[0-9]+)?$/",$value) ){ $msg = '需要数字'; $state = 'warning'; }
    return array('data'=>$value,'msg'=>$msg,'state'=>$state);
}
function simple_check_state($value)//验证0,1
{
    if( !preg_match("/0|1/",$value) ){ $msg = '只能为0或1'; $state = 'warning'; }
    return array('data'=>$value,'msg'=>$msg,'state'=>$state);
}

function simple_check_url($value)//验证URL
{
	if( !filter_var($value, FILTER_VALIDATE_URL) ){ $msg = 'URL格式不合法'; $state = 'warning'; }
    return array('data'=>$value,'msg'=>$msg,'state'=>$state);
}
function simple_check_email($value)//验证EMAIL
{
	if( filter_var($value, FILTER_VALIDATE_EMAIL) ){ $msg = 'Email格式不合法'; $state = 'warning'; }
    return array('data'=>$value,'msg'=>$msg,'state'=>$state);
}




