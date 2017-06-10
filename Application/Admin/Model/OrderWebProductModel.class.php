<?php
namespace Admin\Model;
use Admin\Model\CommonModel;

class OrderWebProductModel extends CommonModel
{
	protected $_link = array(
		'order' => array(
			'mapping_type'      	=>  self::BELONGS_TO,
			'class_name'        	=>  'order_web',
			'mapping_name'      	=>  'order_info',
			'foreign_key'       	=>  'order_web_id',
		),
		'code' => array(
			'mapping_type'      	=>  self::BELONGS_TO,
			'class_name'        	=>  'id_product_code',
			'mapping_name'      	=>  'code_info',
			'foreign_key'       	=>  'code_id',
			'relation_deep'       	=>  array('color_info','size_info','factory_info'),
		),
		'extra' => array(
			'mapping_type'      	=>  self::HAS_ONE,
			'class_name'        	=>  'order_web_product_extra',
			'mapping_name'      	=>  'extra_info',
			'foreign_key'       	=>  'order_web_product_id',
		),
		'nightwear_customization' => array(
			'mapping_type'      	=>  self::HAS_ONE,
			'class_name'        	=>  'order_web_nightwear_customization',
			'mapping_name'      	=>  'nightwear_customization_info',
			'foreign_key'       	=>  'order_web_product_id',
		),
		
	);
}