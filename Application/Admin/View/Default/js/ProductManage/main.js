function select_stock_local(style)
{
	var code=document.getElementById("code").value;
	window.location.href=_CONTROLLER_+"/"+style+"/code/"+code;
}
//分类
function get_product_of_catalog(id,style,text)
{
	flag = $('#flag').val();
	warn = $('#warn').val();
	if(id)
	{
		value = '<li  class="plcz padd"><input type="hidden" name="catalog[]" value="'+id+'"  id="aa">'+text+'<span class="close rotate-hover add_span xx" onclick="$(this).parent().remove(),get_product_of_catalog()"></span></li>';
		$('#catalog_ul')[0].innerHTML += value;
	}
	if(!style)
	{
		style = $('#style').val();
	}
	
	
	var ls =document.getElementsByName('catalog[]');
	var catalog='';
    for( i = 0; i < ls.length; i++)
	{	
		catalog +=ls[i].value;
		if(i <ls.length-1)
		{
			catalog +='-';
		}
 
    }
	if(ls.length >1)
	{
		$('.product').addClass('dis_no');
	}
	else
	{
		$('.product').removeClass('dis_no');
	}
	//分类 tr
	if(ls.length >=1)
	{
		$('.catalog_tr').removeClass('dis_no');
	}
	else
	{
		$('.product').addClass('dis_no');
	}
	//分类 tr end
	
	//alert(catalog);
	// catalog 得到的catalog_id
	
	if(catalog!='')
	{
		window.location.href=_CONTROLLER_+"/"+style+"/flag/"+flag+"/warn/"+warn+"/catalog_id/"+catalog+".html";
	
	}
	else
	{
		window.location.href=_CONTROLLER_+"/"+style+"/flag/"+flag+"/warn/"+warn+".html";
	}
}
//大产品
function get_product_of_code(id,style,text)
{
	flag = $('#flag').val();
	warn = $('#warn').val();
	if(id)
	{
		value = '<li class="plcz padd"><input type="hidden" name="product[]" value="'+id+'"  id="aa">'+text+'<span class="close rotate-hover add_span xx" onclick="$(this).parent().remove(),get_product_of_code()"></span></li>';
		$('#product_ul')[0].innerHTML += value;
	}
	if(!style)
	{
		style = $('#style').val();
	}
	
	
	
	var ls =document.getElementsByName('product[]');
	var product='';
    for( i = 0; i < ls.length; i++)
	{	
		product +=ls[i].value;
		if(i <ls.length-1)
		{
			product +='-';
		}
 
    }
	//产品 tr
	if(ls.length >=1)
	{
		$('.product_tr').removeClass('dis_no');
	}
	else
	{
		$('.product_tr').addClass('dis_no');
	}
	//产品 tr end
	
	catalog_id = $('#catalog_id').val();
	
	if(product)
	{
		if(catalog_id)
		{
			window.location.href=_CONTROLLER_+"/"+style+"/flag/"+flag+"/warn/"+warn+"/catalog_id/"+catalog_id+"/product_id/"+product+".html";
		}
		else
		{
			window.location.href=_CONTROLLER_+"/"+style+"/flag/"+flag+"/warn/"+warn+"/product_id/"+product+".html";
		}
	}
	else
	{
		if(catalog_id)
		{
			window.location.href=_CONTROLLER_+"/"+style+"/flag/"+flag+"/warn/"+warn+"/catalog_id/"+catalog_id+".html";
		}
		else
		{
			window.location.href=_CONTROLLER_+"/"+style+"/flag/"+flag+"/warn/"+warn+".html";
		}
	}
}