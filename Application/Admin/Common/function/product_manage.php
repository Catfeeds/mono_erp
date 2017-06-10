<?php

function get_product_info($id,$flag)
{
	if( $flag=='set' )
	{
		$model = M('id_product_sku');
		$row = $model->where(array('id'=>$id))->find();
		return "{$row['name']} [ {$row['sku']} ]";
	}
	elseif( $flag=='single' )
	{
		$model = M('id_product_code');
		$row = $model->where(array('id'=>$id))->find();
		return "{$row['name']} [ {$row['code']} ]";
	}
}


