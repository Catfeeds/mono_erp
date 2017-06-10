<?php
class DatabaseHandle{
	private $mdb="lily3";
	private $mHost="localhost";
	private $mUser="lily3";
	private $mPassWord="mono1234";
	private $mConnect;//链接句柄
    function __construct($db=''){
		if(is_array($db)){
			 $this->mdb=$db[0];
			 $this->mHost=$db[1];
			 $this->mUser=$db[2];
			 $this->mPassWord=$db[3];
			}
		$this->sqlConn();
	   } 
    private function sqlConn(){
		$this->mConnect=mysql_connect($this->mHost,$this->mUser,$this->mPassWord) or die("Could not connect to MySQL server!");
        mysql_select_db($this->mdb,$this->mConnect) or die("Could not select database!");
		mysql_query('set names utf8');
		}
    function updateData($sql){
		return $update_result=mysql_query($sql);
		}

	function selectData($sql){
		$select_result=mysql_query($sql);
		$select_array=array();
		while($mysql_arr=@mysql_fetch_array($select_result)){
			array_push($select_array,$mysql_arr);
			}
		return empty($select_array)?false:$select_array;
		}
    function deleteData($sql){
		return $delete_result=mysql_query($sql);
		}		   
    function insertData($sql){
		if($insert_result=mysql_query($sql)){
			return mysql_insert_id();
			}else{
			return false;	
			}
		}	
     function __destruct(){//当对象被释放时执行
			mysql_close($this->mConnect);
		}	
}



?>