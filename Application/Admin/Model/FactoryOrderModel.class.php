<?php 
namespace Admin\Model;
use Admin\Model\CommonModel;
class FactoryOrderModel extends CommonModel{
    protected $_link = array(
	   'name' => array(
			'mapping_type'  => self::HAS_ONE ,                  //关联类型  一对一
			'class_name'    => 'factory_order_detail',            //要关联的模型类名(数据表)
			'foreign_key'   => 'factory_order_id',				 //关联的外键名称
			'mapping_name'  => 'detail',						   //关联的映射名称，用于获取数据用
			// 定义更多的关联属性
			),
		);
}
