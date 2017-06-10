var domain = "http://www.local.com/erp/index.php";
function cost_list_act(obj,id){
	var style=document.getElementById("style").value;
	var country=document.getElementById("country").value;
	if(obj=="select"){
		window.location.href=_APP_+"/Admin/DeliveryManage/delivery_weight/style/"+style+"/country/"+country+"/act/select";
	}
	if(obj=="delete"){
		window.location.href=_APP_+"/Admin/DeliveryManage/delivery_weight_act/style/"+style+"/country/"+country+"/id/"+id+"/act/delete";
	}
	
}

function delivery_priority_act(obj,id){
	var style=document.getElementById("style").value;
	var country=document.getElementById("country").value;
	
	if(obj=="select"){
		if(style!="")
		{
			if(country!="")
			{
				window.location.href=_APP_+"/Admin/DeliveryManage/delivery_priority/style/"+style+"/country/"+country+"/act/select";
			}
			else
			{
				window.location.href=_APP_+"/Admin/DeliveryManage/delivery_priority/style/"+style+"/act/select";
			}
			
		}
		if(country!="")
		{
			if(style!="")
			{
				window.location.href=_APP_+"/Admin/DeliveryManage/delivery_priority/style/"+style+"/country/"+country+"/act/select";
			}
			else
			{
				window.location.href=_APP_+"/Admin/DeliveryManage/delivery_priority/country/"+country+"/act/select";
			}
			
		}
	}
	
	
}