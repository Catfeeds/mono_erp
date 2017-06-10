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
//另全选
var current01 = false;//当前未全选
function select_all01()
{
	var detail = document.getElementsByClassName('detail');
	for (i = 0; i < detail.length; i++) 
	{
		if ( $(detail[i]).attr("disabled")!="disabled" && detail[i].name == "detail[]")
		{
			detail[i].checked = !current01;
		}
	}
	current01 = !current01;
}

//FactoryOrder/factory 表单提交
function plcz_form_submit(sta,type)
{
	if(sta=='shipping'  && type!='XTJ' &&type!="ZC")
	{
		//自定页
		layer.open({
		  type: 1,
		  skin: 'layui-layer-demo', //样式类名
		  closeBtn: 1, //显示关闭按钮
		  shift: 2,
		  shadeClose: true, //开启遮罩关闭
		  area: ['420px', '240px'], //宽高
		  content: '<div style=" text-align: center;"> <p style="margin:5px"><span>快递公司 </spam> <select style="width: 200px; height: 25px;"  onchange="delivery(this.options[this.options.selectedIndex].value)"><option value="顺丰">顺丰</option><option value="圆通">圆通</option><option value="申通">申通</option></select></p><p style="margin:5px"><span>快递单号 </span> <input type="text" style="width: 200px;height: 25px;" onchange="delivery_num($(this).val())" ></p><span style="font-size:14px;"><input class="plcz" type="button" style=" margin-top: 10px;padding: 5px 10px;"  onclick="plcz_form_submit()" value="提交" /> </span></div> '
		});	
		
	}
	else
	{
		layer.confirm('确定要批量提交吗？', {
			btn: ['Yes','No'] //按钮
				}, function(){
				  document.getElementById('myform').action = _CONTROLLER_+"/factory_sta";	 
				  document.getElementById('myform').submit(); 
				});
	}
}
//快递
function factory_sta(id,sta,type)
{
	$('#id').val(id);
	if(sta=='shipping' && type!='XTJ' && type!="ZC")
	{
		//自定页
		layer.open({
		  type: 1,
		  skin: 'layui-layer-demo', //样式类名
		  closeBtn: 1, //显示关闭按钮
		  shift: 2,
		  shadeClose: true, //开启遮罩关闭
		  area: ['420px', '240px'], //宽高
		  content: '<div style=" text-align: center;"> <p style="margin:5px"><span>快递公司 </spam> <select style="width: 200px; height: 25px;"  onchange="delivery(this.options[this.options.selectedIndex].value)"><option value="顺丰">顺丰</option><option value="圆通">圆通</option><option value="申通">申通</option></select></p><p style="margin:5px"><span>快递单号 </span> <input type="text" style="width: 200px;height: 25px;" onchange="delivery_num($(this).val())" ></p><span style="font-size:14px;"><input class="plcz" type="button" style=" margin-top: 10px;padding: 5px 10px;"  onclick="factory_sta_update('+"'"+id+"','"+sta+"'"+')" value="提交" /> </span></div> '
		});	
		
	}
	else
	{
		document.getElementById('myform').action = _CONTROLLER_+"/factory_sta/sta/"+sta;	 
	    document.getElementById('myform').submit(); 
		
	}
}

function factory_sta_update(id,sta)
{
	  document.getElementById('myform').action = _CONTROLLER_+"/factory_sta/sta/"+sta;	 
	  document.getElementById('myform').submit(); 
}

//FactoryOrder/factory 导出数据
function execl_export()
{
	document.getElementById('myform').action = _CONTROLLER_+"/execl_export";	 
	document.getElementById('myform').submit(); 
	
	}
//批量转工厂	
function transform_form_submit(id)
{
	if(!id)
	{ 	
		document.getElementById('myform').action = _CONTROLLER_+"/transform_form_submit";
		document.getElementById('myform').submit(); 
	}
	else
	{
		document.getElementById('myform').action = _CONTROLLER_+"/small_transform_form_submit";	
		document.getElementById('myform').submit(); 
	}
}
function factory(val)
{
	$('#factory_list').val(val);
}
function delivery(val)
{
	$('#delivery').val(val);
}
function delivery_num(val)
{
	$('#delivery_num').val(val);
}
//添加给工厂留言	
function factory_order_detail_message_add()
{
	document.getElementById('add_message').action = _CONTROLLER_+"/factory_order_detail_message_add";	 
	document.getElementById('add_message').submit(); 
}
//计算收货率
function calculate_submit(val)
{
	btime = $('#btime').val();
	etime = $('#etime').val();
	if(btime=="" || etime=="" || val=="")
	{
		alert('时间区间必填');
	}
	else
	{
		$.ajax({
			type: "POST",
			url:  _APP_+"/Admin/FactoryOrder/calculate_submit",
			data: {'type': val,'btime': btime,'etime': etime},
			dataType: 'html',
			success : function(result){
				$(".calculate").html(result);
			}
		})
	}

}
//未收货报表
function not_arrival_submit(val)
{
	btime = $('#btime').val();
	etime = $('#etime').val();
	
	if(btime=="" || etime=="")
	{
		alert('时间区间必填');
	}
	else
	{
		if(val=='')
		{
			window.location.href=_ACTION_+"/btime/"+btime+"/etime/"+etime;
		}
		else
		{
			window.location.href=_ACTION_+"/faction/"+val+"/btime/"+btime+"/etime/"+etime;
		}
	}	
}
//导出筛选
function screening_execl()
{
	document.getElementById('myform').action = _CONTROLLER_+"/screening_execl";	 
	document.getElementById('myform').submit(); 
}


//导出选中订单
function export_order()
{
	document.getElementById('myform').action = _CONTROLLER_+"/fac_export_order";	 
	document.getElementById('myform').submit(); 
}
//工厂筛选快递单
function delivery_number_sub()
{
	delivery_number = $('#delivery_number').val();
	factory_type = $('#factory_type').val();
	execl_sta =$('#execl_sta').val();
	window.location.href= _CONTROLLER_+"/factory/type/"+factory_type+"/sta/"+execl_sta+"/delivery_number/"+delivery_number+".html";	
}
//交易单号
function order_num_sub(val)
{
	order_num = $('#order_num').val();
	if(val =='not')
	{
		factory_type =$('#factory').val();
		if(factory_type=='')
		{
			window.location.href= _CONTROLLER_+"/not_arrival_order/order_num/"+order_num+".html";
		}
		else
		{
			window.location.href= _CONTROLLER_+"/not_arrival_order/factory/"+factory_type+"/order_num/"+order_num+".html";
		}
	}
	else
	{
		factory_type = $('#factory_type').val();
		window.location.href= _CONTROLLER_+"/factory/type/"+factory_type+"/order_num/"+order_num+".html";	
	}
}

//工厂订单号
function fac_num_sub(val)
{
	fac_num = $('#fac_num').val();
	factory_type = $('#factory_type').val();
	execl_sta =$('#execl_sta').val();
	if(val =='not')
	{
		window.location.href= _CONTROLLER_+"/not_arrival_order/fac_num/"+fac_num+".html";	
	}
	else
	{
		window.location.href= _CONTROLLER_+"/factory/type/"+factory_type+"/fac_num/"+fac_num+".html";	
	}
}

//导出核对工厂订单
function factory_order_check_execl()
{
	
	btime = $("#beginTime").val();
	etime = $('#endTime').val();
	
	if(btime=="" || etime=="")
	{
		layer.msg('请输入完整时间');
		return false;
	}
	window.location.href = _CONTROLLER_+"/factory_order_check_execl/btime/"+btime+"/etime/"+etime+".html";
	
}