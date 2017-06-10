
//根据来源获取套件sku
function get_set_sku(come_from)
{
	$.ajax({
        type : "POST",
        url: _APP_+"/Admin/productstock/product_stock_set_apply_add", //目标地址
        data : {'come_from':come_from,'action':'ajax_get_set_sku'},//数据，这里使用的是Json格式进行传输  
        dataType : 'json',
        success : function(result) {
//        	console.log(result);return;
        	var option = "";
        	$.each(result,function(n,value){
                option += "<option value='"+value['id']+"'>"+value['name']+" ( "+value['sku']+" ) "+"</option>"
        	});
        	$("#sku").html(option);
		},
    });
}