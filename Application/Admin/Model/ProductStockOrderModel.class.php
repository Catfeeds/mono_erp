<?php 
namespace Admin\Model;
class ProductStockOrderModel extends CommonModel {

	protected $_link = array(
		'detail' => array(
			'mapping_type'      =>  self::HAS_MANY,
			'class_name'        =>  'product_stock_order_detail',
			'mapping_name'      =>  'detail_info',
			'foreign_key'       =>  'product_stock_order_id',
		),
		'plat' => array(
			'mapping_type'      =>  self::BELONGS_TO,
			'class_name'        =>  'order_plat_form',
			'mapping_name'      =>  'plat_info',
			'foreign_key'       =>  'order_platform_id',
		),
		'web' => array(
			'mapping_type'      =>  self::BELONGS_TO,
			'class_name'        =>  'order_web',
			'mapping_name'      =>  'web_info',
			'foreign_key'       =>  'order_id',
		),
		'detail_list' => array(
			'mapping_type'      =>  self::HAS_MANY,
			'class_name'        =>  'product_stock_order_detail',
			'mapping_name'      =>  'detail_list',
			'foreign_key'       =>  'product_stock_order_id',
		//	'condition'     	=>  '`status` != "cancel"', 
		),
	);
	
}