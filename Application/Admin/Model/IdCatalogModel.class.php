<?php
namespace Admin\Model;
use Admin\Model\CommonModel;

class IdCatalogModel extends CommonModel
{
	//关联模型
	protected $_link = array(
		'id_product' => array(
				'mapping_type' => self::HAS_MANY,//一对多关联
				'class_name' => 'id_product',//要关联的模型类名
				'mapping_name' => 'id_product',//关联的映射名称，用于获取数据用
				'foreign_key' => 'catalog_id', //关联的外键名称默认是当前数据的对象名称_id
		)
	);
	function test()
	{
		echo 'testtests';exit;
	}
	
}
