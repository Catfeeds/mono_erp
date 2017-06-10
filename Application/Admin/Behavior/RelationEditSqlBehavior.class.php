<?php
namespace Admin\Behavior;

//功能：		使用多对多关联模型时 获取relate表的 number字段
//Listen:	ThinkPHP/Library/Think/Model/RelationModel
//Add:		Application/Admin/Controller/CodeManageController/sku
class RelationEditSqlBehavior {
     // 行为扩展的执行入口必须是run
     public function run(&$params){
		str_insert($params,10,' ,a.number,a.id as relate_id ');
     }
}