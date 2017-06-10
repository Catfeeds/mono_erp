<?php
namespace Admin\Model;
use Admin\Model\CommonModel;

class OrderWebProductOriginalModel extends CommonModel
{
	protected $_link = array(
		'set' => array(
			'mapping_type'      	=>  self::HAS_MANY,
			'class_name'        	=>  'order_web_product_original_set',
			'mapping_name'      	=>  'set_info',
			'foreign_key'       	=>  'order_web_product_original_id',
		),
	);
	
	//order_web_product与order_web_product_original通过order_web_id与order_product_id存在关联，
	//实际上是  order_web_product_original --has-many--> order_web_product
	public function get_relate_product_info($id)
	{
		$product_original_row = $this->find($id);
		$where = array(
			'order_product_id'	=> $product_original_row['order_product_id'],
			'order_web_id'		=> $product_original_row['order_web_id'],
		);
		return $this->table('order_web_product')->where($where)->select();
	}
	
}

