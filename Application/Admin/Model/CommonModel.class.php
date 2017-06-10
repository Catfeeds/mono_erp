<?php
/*
 * 公共模型类
 */
namespace Admin\Model;
use Think\Model\RelationModel;

class CommonModel extends RelationModel
{
	/*
	 * 检查某字段是否已存在某值
	 * @param $where=array($key=>$val,...)
	 */
	public function check_duplicate($where)
	{
		return $this->where($where)->find();
	}
	
}