<?php 
namespace Admin\Model;
use Admin\Model\CommonModel;
class ProductStockSetModel extends CommonModel
{
    protected $_link = array(
	   'product_stock_set_code' => array(
			'mapping_type'  => self::MANY_TO_MANY,                  //关联类型   一对多关联 
			'class_name'    => 'id_product_code',                           //要关联的模型类名(数据表)
			'foreign_key'   => 'product_stock_set_id',					    //关联的外键名称 Product_new表里面的关联非主键
			'mapping_name'  => 'code_list',						   //关联的映射名称，用于获取数据用
	   		'relation_table'    =>  'id_relate_sku'
			// 定义更多的关联属性
			),
		);
}
