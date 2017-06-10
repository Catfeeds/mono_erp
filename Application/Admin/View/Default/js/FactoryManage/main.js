// JavaScript Document
//FactoryManage/FbaOrder 表单提交
function plcz_form_submit()
{
	layer.confirm('确定要批量转历史吗？', {
		btn: ['Yes','No'] //按钮
			}, function(){
			  document.getElementById('myform').action = _CONTROLLER_+"/FbaOrder_sta";	 
			  document.getElementById('myform').submit(); 
			});
}
//FactoryManage/FbaOrder 导出数据
function execl_export_Fba()
{
	document.getElementById('myform').action = _CONTROLLER_+"/execl_export_Fba";	 
	document.getElementById('myform').submit(); 
}
//FactoryManage/StockOrder_sta 表单提交
function Stock_rder_form_submit()
{
	layer.confirm('确定要批量转历史吗？', {
		btn: ['Yes','No'] //按钮
			}, function(){
			  document.getElementById('myform').action = _CONTROLLER_+"/StockOrder_sta";	 
			  document.getElementById('myform').submit(); 
			});
}
//FactoryManage/StockOrder_sta 导出数据
function execl_export_Stock()
{
	document.getElementById('myform').action = _CONTROLLER_+"/execl_export_Stock";	 
	document.getElementById('myform').submit(); 
}
//打印商品详情
function order_detail_print()
{
	var myform=document.getElementById('myform');
	myform.action=_MODULE_+"/OrderManage/order_detail_print";
	var input=getByClass(myform,'check');
	for(var i=0;i<input.length;i++)
	{
		input[i].value=input[i].id;
	}
	myform.submit();
}

//批量转工厂	
function transform_form_submit()
{
	document.getElementById('myform').action = _CONTROLLER_+"/transform_form_submit";	 
	document.getElementById('myform').submit(); 
}
function factory(val)
{
	$('#factory_list').val(val);
}

function execl_export_xdj()
{
	document.getElementById('myform').action = _CONTROLLER_+"/execl_export_xdj";	 
	document.getElementById('myform').submit(); 
}
function order_num_sub(url)
{
	order_num = $('#order_num').val();
	window.location.href= _CONTROLLER_+"/"+url+"/order_num/"+order_num+".html";	
}
function fac_num_sub(url)
{
	fac_num = $('#fac_num').val();
	window.location.href= _CONTROLLER_+"/"+url+"/fac_num/"+fac_num+".html";	
}


//计算工厂收货路
function screening_sub(val)
{
	if(val)
	{
		url = '/operate/'+val;
	}
	else
	{
		url='';
	}
	document.getElementById('myform').action = _CONTROLLER_+"/history_percent"+url;	 
	document.getElementById('myform').submit(); 
}