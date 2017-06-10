
function hello()
{
	alert('hello');
}

//刷新页面，保留参数
function order_refresh(anchor)
{
	var url = $('#current_correct_url').val();
	if(anchor!=null)
	{
		url += '/anchor/'+anchor;
	}
	window.location.href = url;
}

function see_more(id)
{
	if( $("#see_more_"+id).css("display")=="none" )
	{
		$("#see_more_"+id).css("display","table-row");
		$("#button_see_more_"+id).removeClass("icon-chevron-down");
		$("#button_see_more_"+id).addClass("icon-chevron-up");
	}
	else
	{
		$("#see_more_"+id).css("display","none");
		$("#button_see_more_"+id).removeClass("icon-chevron-up");
		$("#button_see_more_"+id).addClass("icon-chevron-down");
	}
}

function anchor(id,correct_url)
{
	$("#see_more_"+id).css("display","table-row");
	$("#button_see_more_"+id).removeClass("icon-chevron-down");
	$("#button_see_more_"+id).addClass("icon-chevron-up");
	location.hash = 'see_more_'+id;
//	location.hash = '';//...这样可以去掉url中的#tagid
	history.pushState({},'',correct_url);//改变当前地址栏，但是不刷新
	
}

function edit_address(id)
{
	$("#address_text_"+id).css("display","none");
	$("#address_input_"+id).css("display","inline-block");
	$("#edit_address_"+id).css("display","none");
	$("#submit_address_"+id).css("display","inline-block");
}

//弃用。修改地址
function submit_address(id)
{
	var address1 = $("#address1_"+id).attr("value");
	var address2 = $("#address2_"+id).attr("value");
	var address3 = $("#address3_"+id).attr("value");
	$.ajax({
		type : "POST",
        url : _CONTROLLER_+"/ajax_edit_order_address",//路径  
        data : {'id':id,'address1':address1,'address2':address2,'address3':address3},//数据，这里使用的是Json格式进行传输  
//        dataType : 'json',
        success : function(result) {//返回数据根据结果进行相应的处理  
        	if(result)
        	{
        		$("#address_text_"+id).text(address1+" / "+address2+" / "+address3);
        		$("#address_text_"+id).css("display","inline-block");
        		$("#address_input_"+id).css("display","none");
        		$("#edit_address_"+id).css("display","inline-block");
        		$("#submit_address_"+id).css("display","none");
        	}
        	else
    		{
        		alert("修改失败，请重试！");
    		}
        },	
	});
}

//按字段自由排序。用于order.html
function free_sort(this_field,param)
{
	var param_json = JSON.parse(param);//字符串转json对象
	if(param=="[]"){param_json = {} };
//	console.log(param_json);
	if(param_json['sort_field'])
	{
		if(param_json['sort_style']=='asc')
		{
			param_json['sort_style'] = 'desc';
		}
		else if(param_json['sort_style']=='desc')
		{
			param_json['sort_style'] = 'asc';
		}
	}
	else
	{
		param_json['sort_style'] = 'asc';
	}
	param_json['sort_field'] = this_field;

	var url = _ACTION_;
	$.each(param_json,function(n,value) {
        url += '/'+n+'/'+value;
	});
	window.location.href=url;
}

//弃用。
function edit_address_web(id)
{
	document.getElementById("address_span_"+id).style.display = "none";
	document.getElementById("address_input_"+id).style.display = "block";
	var address_web = document.getElementById("address_input_"+id).value;
	$.ajax({
		type : "POST",//请求方式
        url : _CONTROLLER_+"/ajax_edit_order_web_address",//请求的url地址  
        data : {'id':id,'address_web':address_web},//参数值  
        //dataType : 'json', //返回格式为json
    	success : function(data) //返回数据根据结果进行相应的处理  
    	{
	    	if(data!='')	
	    	{
	    		document.getElementById("address_span_"+id).innerHTML = data;
	    		address_web = data;
	    		document.getElementById("address_span_"+id).style.display = "block";
	    		document.getElementById("address_input_"+id).style.display = "none";
	    		alert("修改成功");
	    	}
    	}
	});
}

//修改订单状态
var layer_index_status_update;
function order_status_update(order_id,style,action){
	
	if(action=='fetch')//渲染弹框
	{
		$.ajax({
			type : "POST",
	        url : _CONTROLLER_+"/ajax_status_update",
	        data : {'order_id':order_id,'style':style,'action':'fetch'},  
//	        dataType : 'json',
	        success : function(data) {//返回数据根据结果进行相应的处理  
	        	//打开弹窗
	        	layer_index_status_update = layer.open({
	        		zIndex: 1,
	        		type: 1,
	        		title: '<h2>修改订单状态</h2>',
	        		offset: ['100px'],
	        		area: ['800px', '60%'], //宽高
	        		content: data,
	        	});
	        	//初始化编辑器
	        	KindEditor.ready(function(K) {
	        		editor = K.create('#status_update_message', {
	        			allowFileManager: true,
	        			afterCreate: function(){this.sync();},
	        			afterBlur: function(){this.sync();},
	        		});
	        	});
	        },	
	        error : function(result){
	        	console.log('error');
	        }
	        
		});
	}
	
	else if(action=='update')//修改状态
	{
		var status = $('#status_update_status').val();
		var message = $('#status_update_message').val();
		$.ajax({
			type : "POST",
	        url : _CONTROLLER_+"/ajax_status_update",
	        data : {'order_id':order_id,'style':style,'action':'update','status':status,'message':message},  
//	      	dataType : 'json',
	        success : function(data) {//返回数据根据结果进行相应的处理  
	        	//关闭弹窗
	        	if(data){
	        		layer.close(layer_index_status_update);
	        	}
	        	else{
	        		layer.msg('修改失败');
	        	}
	        	//刷新页面
	        	var url = $('#current_correct_url').val();
	        	window.location.href = url;
	        },	
	        error : function(data){
	        	console.log('error');
	        }
	        
		});
	}
}



//关闭弹框	
function remark_over(){
	$('.order_box').css('display','none');
	$('#remark_smile').css('display','none');
	$('#order_status').css('display','none');
}
//展开弹框
function remark_add(id,style){
	$('.order_box').css('display','block');
	$('#remark_smile').css('display','block');
	$('.click_bz_id').val(id);
	$('.click_bz_style').val(style);
}

//提交留言
function remark_form_submit()
{
	var id = $('.click_bz_id').val();//订单id
	var style = $('.click_bz_style').val();//网站，平台
	var accept = $('#accept_name').val();//指派给
	var message = $('#remark_message').val();//留言内容
	
	var accept_name = document.getElementsByName("accept_name[]");//指派给
	var accept='';
	for(var i=0;i<accept_name.length;i++){
		if(accept_name[i].value!='')
		{
			accept+=accept_name[i].value+",";//指派给
		}
	}
	var inputs=document.getElementsByName("warning");
  	for(var index=0;index<inputs.length;index++){
		if(inputs[index].checked==true)
		{
			warning=inputs[index].value;
		}
	}
	if(accept=='')
	{
		layer.msg('指定人员为空！如果没有要指定的人员 请选自己');
		return false;
	}
	if(!message)
	{
		layer.msg('留言不能为空！');
		return false;
	}
	$.ajax({
		type : "POST",
        url : _CONTROLLER_+"/ajax_remark_add",
        data : {'id':id,'style':style,'accept':accept,'message':message,'warning':warning},  
        dataType : 'json',
        success : function(result) {//返回数据根据结果进行相应的处理
        	$('#message_first_'+id).html(result[0]);
    		$('#message_tips_'+id).html(result[1]);
        	remark_over();
        },	
        error : function(result){
        	console.log('error');
        }
        
	});
	
	return false;
}
//留言指派人
function select_accept(val)
{
	$(".accept_ul")[0].innerHTML += '<li> <input type="button" class="plcz" id="accept_name"  style="font-size:12px; padding: 5px 25px 5px 5px;margin:0px;" value="'+val+'" /><input type="hidden" name="accept_name[]" value="'+val+'"/><span class="close rotate-hover add_span" style="font-size:12px;position: relative;left: -23px;" onclick="$(this).parent().remove()"></span></li>';
	
	
}

//order.html中提交表单
function muti_form_submit()
{
	$("#action").attr("value",arguments[0]);//标志
	if(arguments[0]=="come_from_id" )
	{
		$("#come_from_id").attr("value",arguments[1]);//第二个参数
	}
	if(arguments[0]=="sync" )
	{
		layer.load(1,{shade: 0.5,});//弹出加载层
	}
	$("#muti_form").submit();
	return false;
}

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

//标记行
function mark_row(order_id,message,style)
{
	$("#warning_"+order_id).css('display','block');
	if( message!="" && message!=null )
	{
		$("#warning_"+order_id).append(message+"<br/>");
	}
	if( style!="" && style!=null )
	{
		$("#list_row_"+order_id).addClass(style);
	}
}

//禁止审核，禁止选中
function disable_audit(order_id)
{
	$("#check_"+order_id).attr("disabled","disabled");
	$("#audit_button_"+order_id).attr("onclick","layer.msg('产品sku未关联code');");
	$("#audit_button_"+order_id).removeClass("text-red");
	$("#audit_button_"+order_id).addClass("text-gray");
}

//修改运单
var layer_index_delivery_edit;
function delivery_edit(order_id,style,action)
{
	console.log(_CONTROLLER_+"/ajax_edit_delivery");
	if(action=='fetch')//渲染弹框
	{
		$.ajax({
			type : "POST",
	        url : _CONTROLLER_+"/ajax_edit_delivery",
	        data : {'order_id':order_id,'style':style,'action':'fetch'},  
//	        dataType : 'json',
	        success : function(data) {//返回数据根据结果进行相应的处理  
	        	//打开弹窗
	        	layer_index_delivery_edit = layer.open({
	        		zIndex: 1,
	        		type: 1,
	        		title: '<h2>编辑运单信息</h2>',
	        		offset: ['200px'],
	        		//area: ['400px', '40%'], //宽高
	        		content: data,
	        	});
	        },	
	        error : function(result){
	        	console.log('error');
	        }
	        
		});
	}
	else if(action=='update')//修改状态
	{
		var shipping_style = $('#delivery_edit_style').val();
		var weight = $('#delivery_edit_weight').val();
		var hs = $('#delivery_edit_hs').val();
		//询问框
		layer.confirm('确认使用'+shipping_style+'！！', {
		  btn: ['是的','我在看看'] //按钮
		}, function(){
				$.ajax({
					type : "POST",
					url : _CONTROLLER_+"/ajax_edit_delivery",
					data : {'order_id':order_id,'style':style,'action':'update','shipping_style':shipping_style,'weight':weight,'hs':hs},  
		//	      	dataType : 'json',
					success : function(data) {//返回数据根据结果进行相应的处理  
						//关闭弹窗
						if(data){
							layer.close(layer_index_delivery_edit);
							if(shipping_style == "FEDEX" || shipping_style == "DHL" || shipping_style == "TNT")
							{
								
								if(style == 'web')
								{
									tempwindow=window.open('_blank');
									tempwindow.location=_APP_+"/Admin/OrderDelivery/index/style/web/id/"+order_id+".html";
								}
								else if(style == 'plat')
								{
									topen=window.open('_blank');
									topen.location=_APP_+"/Admin/OrderDelivery/index/style/flat/id/"+order_id+".html";
								}
								else
								{
									alert(style);
								}
								jjj.click()
							}
						}
						else{
							layer.msg('修改失败');
						}
						
						//刷新页面
						var url = $('#current_correct_url').val();
						window.location.href = url+"/anchor/"+order_id;
					},	
					error : function(data){
						console.log('error');
					}
					
				});
		});
	}
}

//修改运单
var layer_index_delivery_detail_add;
function delivery_detail_add(order_id,style,action)
{
	if(action=='fetch')//渲染弹框
	{
		$.ajax({
			type : "POST",
	        url : _CONTROLLER_+"/ajax_delivery_detail_add",
	        data : {'order_id':order_id,'style':style,'action':'fetch'},  
//	        dataType : 'json',
	        success : function(data) {//返回数据根据结果进行相应的处理  
	        	//打开弹窗
	        	layer_index_delivery_detail_add = layer.open({
	        		zIndex: 1,
	        		type: 1,
	        		title: '<h2>手动添加运单</h2>',
	        		offset: ['200px'],
	        		//area: ['400px', '40%'], //宽高
	        		content: data,
	        	});
	        },	
	        error : function(result){
	        	console.log('error');
	        }
	        
		});
	}
	
	else if(action=='update')//修改状态
	{
		var shipping_style = $('#delivery_edit_style').val();
		var delivery_number = $('#delivery_number').val();
		var weight = $('#delivery_weight').val();
		$.ajax({
			type : "POST",
	        url : _CONTROLLER_+"/ajax_delivery_detail_add",
	        data : {'order_id':order_id,'style':style,'action':'update','shipping_style':shipping_style,'delivery_number':delivery_number,'weight':weight},  
//	      	dataType : 'json',
	        success : function(data) {//返回数据根据结果进行相应的处理  
	        	//关闭弹窗
	        	if(data){
	        		layer.close(layer_index_delivery_detail_add);
	        	}
	        	else{
	        		layer.msg('修改失败');
	        	}
	        	//刷新页面
	        	var url = $('#current_correct_url').val();
	        	window.location.href = url+"/anchor/"+order_id;
	        },	
	        error : function(data){
	        	console.log('error');
	        }
	        
		});
	}
}

function add_extra_costs(id,style,operator){
	layer.open({
		  type: 1,
		  title: "添加额外费用",
		  area: ['420px', '180px'],
		  closeBtn: 1,
		  shadeClose: false,
		  skin: 'layui-layer-lan',
		  content: "<form action='"+_CONTROLLER_+"/order_extra_costs' method='post'><div class='panel' style='width:100%;float:left;'><table class='table'><tr><td>关税</td><td><input type='text' name='tariffs'></td></tr><tr><td>偏远费</td><td><input type='text' name='remote'><input type='hidden' value='"+id+"' name='order_id'><input type='hidden' value='"+style+"' name='style'><input type='hidden' value='"+operator+"' name='operator'></td></tr><tr><td colspan='2'><input class='button border-blue' type='submit' value='提交'></td></tr></table></div></form>",	 
	});
}
//打印订单  网站
function order_print_web()
{
	document.getElementById('muti_form').action = _CONTROLLER_+"/order_detail_print";	 
	document.getElementById('muti_form').submit(); 
}
//打印订单  平台
function order_print_plat()
{
	
	document.getElementById('muti_form').action = _CONTROLLER_+"/order_detail_print";	 
	document.getElementById('muti_form').submit(); 
}

//导出未发货订单
function export_execl_order()
{
	document.getElementById('muti_form').action = _CONTROLLER_+"/export_execl_order";	 
	document.getElementById('muti_form').submit(); 
}
//导出运单
function order_waybill_plat(val)
{
	document.getElementById('muti_form').action = _CONTROLLER_+"/export_order_waybill_plat/style/"+val;	 
	document.getElementById('muti_form').submit(); 
}
//导出运单
function order_waybill_web(val)
{
	document.getElementById('muti_form').action = _CONTROLLER_+"/export_order_waybill_web/style/"+val;	 
	document.getElementById('muti_form').submit(); 
}
//速卖通API
function SmtApi()
{
	document.getElementById('access_token_form').action = _CONTROLLER_+"/SmtApi";	 
	document.getElementById('access_token_form').submit(); 
		
}

//获取ebayAPI信息
function get_ebay(platform)
{
	if(platform=="ebay.us")	
	{
		window.location.href = _CONTROLLER_+"/ebay_api/platform/"+platform;
	}
	else if(platform=="ebay.uk")
	{
		window.location.href = _CONTROLLER_+"/ebay_api/platform/"+platform;
	}
	return false;
}

function sku_relate(product_id,come_from_id){
	var obj=document.getElementById("add_sku_"+product_id);
	var hidden_value=document.getElementById("hidden_"+product_id);
	$.ajax({
		type : "POST",
        url : _CONTROLLER_+"/sku_relate",
        data : {"relate_sku":obj.value,"product_id":product_id,"come_from_id":come_from_id,"hidden_value":hidden_value.value},  
      	dataType : 'html',
        success : function(data) {
        		var td=document.getElementById("td_"+product_id);
        		hidden_value.value=data;
        		td.getElementsByTagName("span")[0].innerHTML=data;
        		obj.style.display="none";
        		td.getElementsByTagName("span")[1].style.display="none";
        		td.getElementsByTagName("span")[2].style.display="block";
        }  
	});
}

function update_sku(product_id){
	var obj=document.getElementById("add_sku_"+product_id);
	var td=document.getElementById("td_"+product_id);
	td.getElementsByTagName("span")[0].innerHTML="";
	td.getElementsByTagName("span")[1].style.display="block";
	td.getElementsByTagName("span")[2].style.display="none";
	obj.style.display="block";
}

function waybill_information(order_id,come_from)
{
	var option='';
	var style_list='';
	var come_page=_CONTROLLER_+"/order_delivery?come="+come_from;
	$.ajax({
		type:"POST",
		url:  _CONTROLLER_+"/order_delivery/come/ajax",
		data : {"order_id":order_id},
		dataType: 'json',
		success: function(result){
			$.each(result,function(n,value){
				option+="<option value='"+value+"'>"+value+"</option>";
			})
			style_list="<select name='delivery_style'>"+option+"</select>";
			layer.open({
			type: 1,
			title: "运单信息",
			closeBtn: 1,
			area: ['500px', '233px'],
			shadeClose: false,
			skin: 'layui-layer-lan',
			content: "<form action='"+come_page+"' method='post' id='myform'><div><ul class='list-group'><li><strong>运单号:</strong>&nbsp;&nbsp;<input type='text' name='delivery_number' msg='请填写运单号' check='empty'></li><li><strong>运输方式:</strong>&nbsp;&nbsp;"+style_list+"<input type='hidden' value='"+order_id+"' name='order_id'></li><li><input type='submit' value='提交' class='button border-blue' onclick='return check_form(\"myform\")'></li></ul></div></form>",
});
			}
		})
}
//删除运单详情
function delivery_delete(order_id,type)
{
	layer.confirm('您确定要删除该条信息，订单状态会改成 待发货 ？', {
	  btn: ['Yes','No'] //按钮
	}, function(){
		window.location.href =_CONTROLLER_+'/delivery_delete/id/'+order_id+'/type/'+type;
	});
	
	
	
}
