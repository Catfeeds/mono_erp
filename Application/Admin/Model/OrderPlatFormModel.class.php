<?php
namespace Admin\Model;
use Admin\Model\CommonModel;

class OrderPlatFormModel extends CommonModel
{
	protected $_link = array(
		'product' => array(
			'mapping_type'      	=>  self::HAS_MANY,
			'class_name'        	=>  'order_plat_form_product',
			'mapping_name'      	=>  'product_info',
			'foreign_key'       	=>  'order_platform_id',
			'relation_deep'			=>	array('code_info','extra_amazon_info'),
		),
		'shipping' => array(
			'mapping_type'      	=>  self::HAS_ONE,
			'class_name'        	=>  'order_plat_form_shipping',
			'mapping_name'      	=>  'shipping_info',
			'foreign_key'       	=>  'order_platform_id',
		),
		'status' => array(
			'mapping_type'      	=>  self::HAS_ONE,
			'class_name'        	=>  'order_plat_form_status',
			'mapping_name'      	=>  'status_info',
			'foreign_key'       	=>  'order_platform_id',
		),
		'message' => array(
			'mapping_type'      	=>  self::HAS_MANY,
			'class_name'        	=>  'order_business_message',
			'mapping_name'      	=>  'message_info',
			'foreign_key'       	=>  'order_platform_id',
			'mapping_order'			=>	'warning asc,date_time desc'
		),
		'delivery' => array(
			'mapping_type'      	=>  self::HAS_ONE,
			'class_name'        	=>  'order_delivery_parameters',
			'mapping_name'      	=>  'delivery_info',
			'foreign_key'       	=>  'order_platform_id',
		),
		'factory_order' => array(
			'mapping_type'      	=>  self::HAS_MANY,
			'class_name'        	=>  'factory_order',
			'mapping_name'      	=>  'factory_order',
			'foreign_key'       	=>  'order_platform_id'
		),
		'fba_order' => array(
			'mapping_type'      	=>  self::HAS_MANY,
			'class_name'        	=>  'fba_order',
			'mapping_name'      	=>  'fba_order',
			'foreign_key'       	=>  'orderplatform_id',
		),	
		'product_stock_order' => array(
			'mapping_type'      	=>  self::HAS_MANY,
			'class_name'        	=>  'product_stock_order',
			'mapping_name'      	=>  'product_stock_order',
			'foreign_key'       	=>  'order_platform_id',
		),
		'delivery_detail' => array(
			'mapping_type'      	=>  self::HAS_ONE,
			'class_name'        	=>  'order_delivery_detail',
			'mapping_name'      	=>  'detail_info',
			'foreign_key'       	=>  'order_platform_id',
			'mapping_fields'        =>  array('style','delivery_number')
		),
		'other_price' => array(
			'mapping_type'      	=>  self::HAS_ONE,
			'class_name'        	=>  'order_delivery_other_price',
			'mapping_name'      	=>  'other_price_info',
			'foreign_key'       	=>  'order_platform_id',
			'mapping_fields'        =>  array('tariffs','remote','operator')
		),
		'come_from' => array(
			'mapping_type'      	=>  self::BELONGS_TO,
			'class_name'        	=>  'id_come_from',
			'mapping_name'      	=>  'come_from_info',
			'foreign_key'       	=>  'come_from_id',
		),
	);
	public function group()
	{
		return $this->group('code_id');
	}
	public function status($status, $where=array())
	{
		if($status=='all')
		{
			return $this->where($where);
		}
		elseif($status=='audit')
		{
			$sub_query = "SELECT order_platform_id FROM order_plat_form_status WHERE status!='audit' ";
			$where['id'] = array('EXP',"NOT IN ($sub_query)");
			return $this->where($where);
		}
		else //注意，此处用到联表和别名，左表a，右表b，对后面的连贯操作会造成影响
		{
			$where['status'] = $status;
			return $this->alias('a')->join('LEFT JOIN order_plat_form_status as b ON a.id = b.order_platform_id')->where($where)->field('a.*');
		}
	}
	
	
}