// javascript function for Controller CodeManage
function get_product_of_catalog01(val)
{
	var option="" ;
	$.ajax({  
        type : "POST",
        url: _ACTION_, //目标地址
        data : {'code_id':val},//数据，这里使用的是Json格式进行传输  
        dataType : 'json',
		success : function(result) {//返回数据根据结果进行相应的处理 
        	$.each(result,function(n,value) {
				option += "<option value='"+value['style']+"'>"+value['name']+"</option>"
			});
			$("#style")[0].innerHTML = option;
        },
    });  
}

function admin_select_catalog(catalog){
	$.ajax({
		type: "POST",
		url:  _APP_+"/Admin/ProductReturn/admin_select_catalog",
		data: {'catalog': catalog},
		dataType: 'html',
		success : function(result){
			$("#products_td").html(result);
		}
	})
}

function admin_select_products(products){
	var is_sku=document.getElementById("is_sku").value;
	if(!is_sku){
		$.ajax({
			type: "POST",
			url:  _APP_+"/Admin/ProductReturn/admin_select_products",
			data: {'products': products},
			dataType: 'html',
			success : function(result){
				$("#code_tr").show();
				$("#code_td").html(result);
			}
		})	
	}else{
		$("#code_tr").hide();
	}
}