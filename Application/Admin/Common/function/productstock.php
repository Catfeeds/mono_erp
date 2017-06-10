<?php

function stock_apply_status()
{
	return array(
		0=>'正在审核',
		1=>'通过->工厂',
		2=>'工厂接收成功',
		3=>'工厂已发货',
		4=>'已收到货',
		8=>'放弃申请',
		9=>'未通过审核',
	);
}

