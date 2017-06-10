<?php 
namespace Admin\Model;
use Admin\Model\CommonModel;
class ProductNewModel extends CommonModel{
    protected $_link = array(
	   'name' => array(
			'mapping_type'  => self::BELONGS_TO,                  //关联类型   一对多关联 一对一
			'class_name'    => 'user',                           //要关联的模型类名(数据表)
			'foreign_key'   => 'applicant',					    //关联的外键名称 Product_new表里面的关联非主键
			'mapping_name'  => 'name',						   //关联的映射名称，用于获取数据用
			// 定义更多的关联属性
			),
		);
}
