<?php
namespace Admin\Model;
use Admin\Model\CommonModel;

class IdProductCodeModel extends CommonModel
{
	protected $tableName = 'id_product_code';
	protected $_link = array(
		'id_product_sku' => array(
			'mapping_type'			=> self::MANY_TO_MANY,
			'foreign_key'			=> 'product_code_id',
            'relation_foreign_key'	=> 'product_sku_id',
            'relation_table'		=> 'id_relate_sku',
		),
		'color' => array(
			'mapping_type'      	=>  self::BELONGS_TO,
			'class_name'        	=>  'id_product_attribute',
			'mapping_name'      	=>  'color_info',
			'foreign_key'       	=>  'color_id',
		),
		'size' => array(
			'mapping_type'      	=>  self::BELONGS_TO,
			'class_name'        	=>  'id_product_attribute',
			'mapping_name'      	=>  'size_info',
			'foreign_key'       	=>  'size_id',
		),
		'factory' => array(
			'mapping_type'      	=>  self::BELONGS_TO,
			'class_name'        	=>  'factory',
			'mapping_name'      	=>  'factory_info',
			'foreign_key'       	=>  'factory_id',
		),
		'product' => array(
			'mapping_type'      	=>  self::BELONGS_TO,
			'class_name'        	=>  'id_product',
			'mapping_name'      	=>  'product_detail',
			'foreign_key'       	=>  'product_id',
		),
	);
	
	//获取关联的idcatalog,idproduct,idproductattribute信息
	function getRelationInfo($where='')
	{
		$catalog_model = D('IdCatalog');
		$product_model = D('IdProduct');
		$attribute_model = D('IdProductAttribute');
		
		$data = $this->where($where)->select();
		foreach ($data as $key=>$val)
		{
			$product_row = $product_model->where('`id` = '.$val['product_id'])->find();
			$data[$key]['catalog_name'] = $catalog_model->where(array('id'=>$product_row['catalog_id']))->getField('name');

			$product_row = $product_model->where(array('id'=>$val['product_id']))->find();
			$data[$key]['product_name'] = $product_row['name'];
			
			$size_row = $attribute_model->where(array('id'=>$val['size_id']))->find();
			$data[$key]['size_name'] = $size_row['value'];
			
			$color_row = $attribute_model->where(array('id'=>$val['color_id']))->find();
			$data[$key]['color_name'] = $color_row['value'];
			
			$data[$key]['factory_name'] = get_factory_name($val['factory_id']);
		}
		return $data;
	}
	
	//限定product、color、size获取所有 不重复的size或color
	function attribute($attr, $product_id=null, $color_id=null, $size_id=null)
	{
		if($attr=='color') $field = 'color_id';
		elseif($attr=='size') $field = 'size_id';
		else return false;
		
		$where = array();
		if($product_id) $where['product_id'] = $product_id;
		if($color_id) $where['color_id'] = $color_id;
		if($size_id) $where['size_id'] = $size_id;
		if(!$where) return false;
		
		$sub_sql = $this->distinct(true)->field($field)->where($where)->fetchSql(true)->select();
// 		SELECT DISTINCT  `color_id` FROM `id_product_code` WHERE `product_id` = 2
		$sub_sql = "SELECT $field FROM ($sub_sql) AS temp_table";
		return $this->table('id_product_attribute')->where(array('id'=>array('EXP'," IN ($sub_sql)")));
	}
	
}