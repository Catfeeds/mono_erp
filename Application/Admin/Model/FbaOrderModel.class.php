<?php 
namespace Admin\Model;
use Admin\Model\CommonModel;
class FbaOrderModel extends CommonModel{
    protected $_link = array(
	   'name' => array(
			'mapping_type'  => self::HAS_MANY,                  //关联类型   一对多关联 一对一
			'class_name'    => 'fba_order_detail',                           //要关联的模型类名(数据表)
			'foreign_key'   => 'fba_order_id',					    //关联的外键名称 Product_new表里面的关联非主键
			'mapping_name'  => 'fba_order_detail',						   //关联的映射名称，用于获取数据用
		//	'condition'     => 'status != "cancel"'
			// 定义更多的关联属性
			),
		);
}
