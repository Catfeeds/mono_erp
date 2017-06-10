// JavaScript Document
//全选
var current = false;//当前未全选
function select_all()
{
	var check = document.getElementsByClassName('check');
	for (i = 0; i < check.length; i++) 
	{
		if ( $(check[i]).attr("disabled")!="disabled" && check[i].name == "check[]")
		{
			check[i].checked = !current;
		}
	}
	current = !current;
}

function time_sub()
{
	btime=$('#beginTime').val();
	etime=$('#endTime').val();
	if(btime=="" && etime=="")
	{
		alert('请选择完整的时间区间！！');
	}
	else
	{
		document.getElementById('myform').action = _CONTROLLER_+"/new_number";		
		document.getElementById('myform').submit();	
	}
}
//execl导出
function execl_sub(style)
{
	
	if(style=='screening')
	{
		document.getElementById('myform').action =  _CONTROLLER_+"/new_number_execl/act/screening";
	}
	if(style=="address")
	{
		document.getElementById('myform').action =  _CONTROLLER_+"/new_number_execl/act/address";
	}	
	document.getElementById('myform').submit();	
}
//更新选中的订单
function select_update()
{
	document.getElementById('sub_form').action = _CONTROLLER_+"/select_update";		
	document.getElementById('sub_form').submit();	
}
//精确查找
function search_submit(aa)
{
	$('#pd').val(aa);
	order_num = $('#order_num').val();
	delivery_number = $('#delivery_number').val();
	if(order_num=="" && delivery_number=="")
	{
		alert('您没有做出填写！！');	
	}
	else
	{
		document.getElementById('myform').action = _CONTROLLER_+"/number_update";		
		document.getElementById('myform').submit();	
	}
}
//
function delivery_style_change(val)
{
	$('#delivery_style').val(val);
}
//number_update   时间区间
function time_search_submit(aa)
{
	btime=$('#beginTime').val();
	etime=$('#endTime').val();
	delivery_style = $('#delivery_style').val();
	
	if((btime=="" || etime=="") && delivery_style=="")
	{
		alert('请做出选择！！');
	}
	else
	{
		if(delivery_style)
		{
			if(btime!="" && etime!="")
			{
				window.location.href=_CONTROLLER_+"/number_update/beginTime/"+btime+"/endTime/"+etime+"/delivery_style/"+delivery_style;
			}
			else
			{
				window.location.href= _CONTROLLER_+"/number_update/delivery_style/"+delivery_style;
			}
		}
		else if(btime!="" && etime!="")
		{
			if(delivery_style)
			{
				window.location.href=_CONTROLLER_+"/number_update/beginTime/"+btime+"/endTime/"+etime+"/delivery_style/"+delivery_style ;
			}
			else
			{
				window.location.href= _CONTROLLER_+"/number_update/beginTime/"+btime+"/endTime/"+etime;
			}
		}
				
	}
}

//排除已签收
function rule_submit()
{ 
	btime=$('#beginTime').val();
	etime=$('#endTime').val();
	delivery_style = $('#delivery_style').val();
	if(delivery_style !="")
	{
		if(btime!="" && etime!="")
		{
			window.location.href=_CONTROLLER_+"/number_update/beginTime/"+btime+"/endTime/"+etime+"/delivery_style/"+delivery_style+"/rule/1.html";
		}
		else
		{
			window.location.href= _CONTROLLER_+"/number_update/delivery_style/"+delivery_style+"/rule/1.html";
		}
	}
	else if(btime!="" && etime!="")
	{
		if(delivery_style!="")
		{
			window.location.href=_CONTROLLER_+"/number_update/beginTime/"+btime+"/endTime/"+etime+"/delivery_style/"+delivery_style+"/rule/1.html";
		}
		else
		{
			window.location.href= _CONTROLLER_+"/number_update/beginTime/"+btime+"/endTime/"+etime+"/rule/1.html";
		}
	}
	else
	{
		window.location.href= _CONTROLLER_+"/number_update/rule/1.html";
	}
}

//异常
function abnormal_submit()
{
	btime=$('#beginTime').val();
	etime=$('#endTime').val();
	delivery_style = $('#delivery_style').val();
	if(delivery_style !="")
	{
		if(btime!="" && etime!="")
		{
			window.location.href=_CONTROLLER_+"/number_update/beginTime/"+btime+"/endTime/"+etime+"/delivery_style/"+delivery_style+"/status/yc.html";
		}
		else
		{
			window.location.href= _CONTROLLER_+"/number_update/delivery_style/"+delivery_style+"/status/yc.html";
		}
	}
	else if(btime!="" && etime!="")
	{
		if(delivery_style!="")
		{
			window.location.href=_CONTROLLER_+"/number_update/beginTime/"+btime+"/endTime/"+etime+"/delivery_style/"+delivery_style+"/status/yc.html";
		}
		else
		{
			window.location.href= _CONTROLLER_+"/number_update/beginTime/"+btime+"/endTime/"+etime+"/status/yc.html";
		}
	}
	else
	{
		window.location.href= _CONTROLLER_+"/number_update/status/yc.html";
	}
}


