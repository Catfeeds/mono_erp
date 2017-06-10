<?php
namespace Admin\Model;
use Admin\Model\CommonModel;

class IdRelateSkuModel extends CommonModel
{
	protected $_link = array(
        'sku'=>array(
            'mapping_type'      => self::BELONGS_TO,
            'class_name'        => 'id_product_sku',    
        	'foreign_key'		=> 'product_sku_id',   
        	'mapping_name'		=> 'sku_info',
		),
		'code'=>array(
			'mapping_type'      => self::BELONGS_TO,
			'class_name'        => 'id_product_code',
			'foreign_key'		=> 'product_code_id',
			'mapping_name'		=> 'code_info',
		),
		
	);
}