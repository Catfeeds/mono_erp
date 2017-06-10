<?php

function get_factory_name($factory_id)
{
	$factory_model = M('factory');
	$row = $factory_model->find($factory_id);
	if($row)
	{
		return $row['name'];
	}
	else
	{
	    return '出错';	
	}

}

