<?php
namespace Admin\Model;
use Admin\Model\CommonModel;

class OrderPlatFormProductModel extends CommonModel
{
	protected $_link = array(
		'order' => array(
			'mapping_type'      	=>  self::BELONGS_TO,
			'class_name'        	=>  'order_plat_form',
			'mapping_name'      	=>  'order_info',
			'foreign_key'       	=>  'order_platform_id',
		),
		'code' => array(
			'mapping_type'      	=>  self::BELONGS_TO,
			'class_name'        	=>  'id_product_code',
			'mapping_name'      	=>  'code_info',
			'foreign_key'       	=>  'code_id',
			'relation_deep'       	=>  array('color_info','size_info','factory_info','product_detail'),//自动加载IdProductCodeModel模型类
		),
		'extra_amazon' => array(
			'mapping_type'      	=>  self::HAS_ONE,
			'class_name'        	=>  'order_plat_form_product_extra_amazon',
			'mapping_name'      	=>  'extra_amazon_info',
			'foreign_key'       	=>  'order_plat_form_product_id',
		),
	);
}