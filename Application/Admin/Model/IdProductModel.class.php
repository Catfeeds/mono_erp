<?php
namespace Admin\Model;
use Think\Model\RelationModel;

class IdProductModel extends RelationModel
{
	protected $_link = array(
		'catalog' => array(
			'mapping_type' => self::BELONGS_TO,
			'mapping_name' => 'catalog',//作为字段健值
			'class_name' => 'Idcatalog',
			'foreign_key ' => 'catalog_id',
// 			'mapping_fields ' => '',
		),
	);
	
	function getRelationCatalog($where)
	{
		$catalog_model = D('IdCatalog');
		$data = $this->where($where)->select();
		foreach ($data as $key=>$val)
		{
			$catalog_name = $catalog_model->where(array('id'=>$val['catalog_id']))->getField('name');
			$data[$key]['catalog_name'] = $catalog_name;
		}
		return $data;
	}
	
}



