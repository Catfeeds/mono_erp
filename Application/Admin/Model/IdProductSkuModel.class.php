<?php
namespace Admin\Model;
use Admin\Model\CommonModel;

class IdProductSkuModel extends CommonModel
{
	protected $_link = array(
		'code' => array(
		    'mapping_type'      	=>  self::MANY_TO_MANY,
		    'class_name'        	=>  'id_product_code',
		    'mapping_name'      	=>  'code_info',
		    'foreign_key'       	=>  'product_sku_id',
		    'relation_foreign_key'  =>  'product_code_id',
		    'relation_table'    	=>  'id_relate_sku' //此处应显式定义中间表名称，且不能使用C函数读取表前缀
	    )
	);
}