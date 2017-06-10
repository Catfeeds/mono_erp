<?php 
namespace Admin\Model;
use Admin\Model\CommonModel;

class OrderWebModel extends CommonModel
{
	//关联模型
	protected $_link = array(
		'order_web_address' => array(
			'mapping_type' => self::HAS_MANY,//一对多关联
			'class_name' => 'order_web_address',//要关联的模型类名
			'mapping_name' => 'order_web_address',//关联的映射名称，用于获取数据用
			'foreign_key' => 'order_web_id', //关联的外键名称默认是当前数据的对象名称_id
			'mapping_fields'=>array('id','first_name','last_name','country','province','city','address','code','telephone'),
		),
		'order_web_product' => array(
			'mapping_type' => self::HAS_MANY,//一对多关联
			'class_name' => 'order_web_product',//要关联的模型类名
			'mapping_name' => 'order_web_product',//关联的映射名称，用于获取数据用
			'foreign_key' => 'order_web_id', //关联的外键名称默认是当前数据的对象名称_id
			'relation_deep'=>array('code_info','extra_info','nightwear_customization_info'),
		),
		'order_web_product_original' => array(
			'mapping_type' 			=>	self::HAS_MANY,
			'class_name'			=>	'order_web_product_original',
			'mapping_name'			=>	'product_original_info',
			'foreign_key'			=>	'order_web_id',
			'relation_deep'			=>	array('set_info'),
		),
		'order_web_status' => array(
			'mapping_type' => self::HAS_ONE,//一对多关联
			'class_name' => 'order_web_status',//要关联的模型类名
			'mapping_name' => 'order_web_status',//关联的映射名称，用于获取数据用
			'foreign_key' => 'order_web_id', //关联的外键名称默认是当前数据的对象名称_id
		),
		'order_web_product_customization' => array(
			'mapping_type'      	=>  self::HAS_MANY,
			'class_name'        	=>  'order_web_product_customization',
			'mapping_name'      	=>  'product_customization_info',
			'foreign_key'       	=>  'order_web_id',
		),
		'message' => array(
			'mapping_type'      	=>  self::HAS_MANY,
			'class_name'        	=>  'order_business_message',
			'mapping_name'      	=>  'message_info',
			'foreign_key'       	=>  'order_id',
			'mapping_order'			=>	'warning asc, date_time desc'
		),
		'delivery' => array(
			'mapping_type'      	=>  self::HAS_ONE,
			'class_name'        	=>  'order_delivery_parameters',
			'mapping_name'      	=>  'delivery_info',
			'foreign_key'       	=>  'order_id',
		),
		'factory_order' => array(
			'mapping_type'      	=>  self::HAS_MANY,
			'class_name'        	=>  'factory_order',
			'mapping_name'      	=>  'factory_order',
			'foreign_key'       	=>  'order_id'
		),
		'factory' => array(
			'mapping_type'      	=>  self::MANY_TO_MANY,
			'class_name'        	=>  'factory',
			'mapping_name'      	=>  'factory',
			'foreign_key'       	=>  'order_id',
			'relation_foreign_key'  =>  'factory_id',
			'relation_table'		=>  'factory_order'
		),
		'fba_order' => array(
			'mapping_type'      	=>  self::HAS_MANY,
			'class_name'        	=>  'fba_order',
			'mapping_name'      	=>  'fba_order',
			'foreign_key'       	=>  'order_id',
		),	
		'product_stock_order' => array(
			'mapping_type'      	=>  self::HAS_MANY,
			'class_name'        	=>  'product_stock_order',
			'mapping_name'      	=>  'product_stock_order',
			'foreign_key'       	=>  'order_id',
		),		
		'delivery_detail' => array(
			'mapping_type'      	=>  self::HAS_ONE,
			'class_name'        	=>  'order_delivery_detail',
			'mapping_name'      	=>  'detail_info',
			'foreign_key'       	=>  'order_web_id',
			'mapping_fields'        =>  array('style','delivery_number')
		),
		'other_price' => array(
			'mapping_type'      	=>  self::HAS_ONE,
			'class_name'        	=>  'order_delivery_other_price',
			'mapping_name'      	=>  'other_price_info',
			'foreign_key'       	=>  'order_web_id',
			'mapping_fields'        =>  array('tariffs','remote','operator')
		),
		'come_from' => array(
			'mapping_type'      	=>  self::BELONGS_TO,
			'class_name'        	=>  'id_come_from',
			'mapping_name'      	=>  'come_from_info',
			'foreign_key'       	=>  'come_from_id',
		),
		'supplement' => array(
			'mapping_type'      	=>  self::HAS_ONE,
			'class_name'        	=>  'order_web_supplement',
			'mapping_name'      	=>  'supplement_info',
			'foreign_key'       	=>  'order_web_id',
		),
	);

	public function status($status, $where=array())
	{
		if($status=='all')
		{
			return $this->where($where);
		}
		if($status=='audit')
		{
			$sub_query = "SELECT order_web_id FROM order_web_status WHERE status!='audit' ";
			$where['id'] = array('EXP',"NOT IN ($sub_query)");
			return $this->where($where);
		}
		else //注意，此处用到联表和别名，左表a，右表b，对后面的连贯操作会造成影响
		{
			foreach($where as $key=>$val)
			{
				if( !is_numeric($key) )//考虑某些字段直接使用sql的特殊情况，此时若与order_web_status字段重名会出错
				{
					$where["a.$key"] = $val;
					unset($where[$key]);
				}
			}
			$where['b.status'] = $status;
			return $this->alias('a')->join('LEFT JOIN order_web_status as b ON a.id = b.order_web_id')->where($where)->field('a.*');
		}
	}
	
	public function enhanced_where()
	{
		$relate_tree = array(
			'order_web' => array(
				'order_web_address' => array(),
				'order_web_nightwear_customization' => array(),
				'order_web_product' => array(
					'order_web_product_extra' => array(),
				),
				'order_web_product_customization' => array(),
				'order_web_product_original' => array(
					'order_web_product_original_set' => array(),
				),
				'order_web_status' => array(),
				'order_web_status_history' => array(),
				
			)
		);
		
		$sql = 'SELECT 
					* 
				FROM 
					order_web,@
					order_web_address,@
					order_web_nightwear_customization,@
					order_web_product,@
					order_web_product_customization,@
					order_web_product_extra,@
					order_web_product_original,@
					order_web_product_original_set,@
					order_web_status,
					order_web_status_history
				WHERE
					order_web.id=order_web_address.order_web_id AND
					order_web.id=order_web_product.order_web_id AND
					order_web.id=order_web_product_customization.order_web_id AND
					order_web.id=order_web_product_original.order_web_id AND
					order_web.id=order_web_status.order_web_id AND
					order_web.id=order_web_status_history.order_web_id AND
					order_web_product.id=order_web_nightwear_customization.order_web_product_id AND
					order_web_product.id=order_web_product_extra.order_web_product_id AND
					order_web_product_original.id=order_web_product_original_set.order_web_product_original_id 
				LIMIT
					0,15
				';
		
		return $this->query($sql);
	}
	
}
?>