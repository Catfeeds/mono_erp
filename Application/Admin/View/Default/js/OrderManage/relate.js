
//修改网站产品sku
var layer_index_edit_sku;
function edit_sku(action, product_original_id, product_original_set_id, sku, order_product_id, order_id)
{
	if(action=='fetch')//渲染弹框
	{
		$.ajax({
			type : "POST",
			url : _CONTROLLER_+"/ajax_edit_sku",
			data : {'action':'fetch','product_original_id':product_original_id,'product_original_set_id':product_original_set_id,'sku':sku,'order_product_id':order_product_id,'order_id':order_id},  
//	        dataType : 'json',
			success : function(data) {//返回数据根据结果进行相应的处理  
				//打开弹窗
				layer_index_edit_sku = layer.open({
					zIndex: 1,
					type: 1,
					title: '<h2>修改sku</h2>',
					offset: ['150px'],
					area: ['280px', '160px'], //宽高
					content: data,
				});
			},	
		});
	}
	else if(action=='cancel')
	{
		layer.close(layer_index_edit_sku);
	}
	else if(action=='submit')
	{
		var new_sku = $("#sku").val();
		$.ajax({
			type : "POST",
			url : _CONTROLLER_+"/ajax_edit_sku",
			data : {'action':'submit','old_sku':sku,'new_sku':new_sku,'product_original_id':product_original_id,'product_original_set_id':product_original_set_id,'order_product_id':order_product_id,'order_id':order_id},  
//	        dataType : 'json',
			success : function(data) {//返回数据根据结果进行相应的处理  
//				console.log(data);return;
				if( data=='new' )
	    		{
	        		layer.msg('修改成功！ <br/> 新sku未关联！');
	    		}
				else if( data=='exist' )
				{
					layer.msg('修改成功！ <br/> 订单产品已修改！');
				}
				else if( data=='set' )
				{
					layer.msg('修改失败！ <br/> 新sku是套件！');
				}
				else if( data=='unknown product' )
				{
					layer.msg('修改失败！ <br/> 原始数据错误！');
				}
	        	else
	        	{
	        		layer.msg( data );
	        	}
        		layer.close(layer_index_edit_sku);
        		setTimeout("order_refresh("+order_id+")",200);
			},	
		});
	}
}

//网站订单页面 sku关联code 弹窗 ; type=[add|edit]; 
var layer_index_web_sku_relate_code;
function web_sku_relate_code( sku_id, order_product_id, action, type, order_id, original_set_id)
{
	if(action=='fetch')//渲染弹框
	{
		$.ajax({
			type : "POST",
			url : _CONTROLLER_+"/ajax_web_sku_relate_code",
			data : {'sku_id':sku_id,'order_product_id':order_product_id,'action':'fetch','type':type,'order_id':order_id,'original_set_id':original_set_id},  
//	        dataType : 'json',
			success : function(data) {//返回数据根据结果进行相应的处理  
				//打开弹窗
				layer_index_web_sku_relate_code = layer.open({
					zIndex: 1,
					type: 1,
					title: '<h2>添加关联</h2>',
					offset: ['100px'],
					area: ['800px', '60%'], //宽高
					content: data,
				});
			},	
		});
	}
}

function web_relate_submit()
{
	var sku_id = $("#sku_id").val();
	var code_id = $('input[name="relate_code"]:checked').val();
	var order_id = $("#order_id").val();
	var order_product_id = $("#order_product_id").val();
	var type = $("#type").val();
	var original_set_id = $("#original_set_id").val();
	
	if( code_id==null || code_id=='' )
	{
		layer.msg('必须选择一个code！');
		return false;
	}
	
	$.ajax({
		type : "POST",
        url : _CONTROLLER_+"/ajax_web_sku_relate_code",
        data : {'action':'update','code_id':code_id,'sku_id':sku_id,'order_id':order_id,'order_product_id':order_product_id,'type':type,'original_set_id':original_set_id},
        dataType : 'json',
        success : function(data) {//返回数据根据结果进行相应的处理  
//        	console.log(data);return;
        	if( data['status']=='success' )
    		{
        		layer.msg('success! <br/> ' + data['msg'] + ' records are affected');
        		layer.close(layer_index_web_sku_relate_code);
        		setTimeout("order_refresh("+order_id+")",400);
    		}
        	else if( data['status']=='error' )
        	{
        		layer.msg( data['msg'] );
        	}
        },
	});
	
	return false;
}


/*
 * 已弃用。该方法着重于 关联套件sku，而网站sku关联code只针对单件情况。可以用于平台。
 * 舍不得删，留着看看。。。
 * sku添加关联 弹出窗
 */
var layer_index_sku_relate_code;
function order_sku_relate_code(order_id,sku_id,style,action)
{
	if(action=='fetch')//渲染弹框
	{
		$.ajax({
			type : "POST",
			url : _CONTROLLER_+"/ajax_sku_relate_code",
			data : {'order_id':order_id,'sku_id':sku_id,'style':style,'action':'fetch'},  
//	        dataType : 'json',
			success : function(data) {//返回数据根据结果进行相应的处理  
				//打开弹窗
				layer_index_sku_relate_code = layer.open({
					zIndex: 1,
					type: 1,
					title: '<h2>添加关联</h2>',
					offset: ['100px'],
					area: ['800px', '60%'], //宽高
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
		var status = $('#status_update_status').val();
		var message = $('#status_update_message').val();
		$.ajax({
			type : "POST",
			url : _CONTROLLER_+"/ajax_status_update",
			data : {'order_id':order_id,'style':style,'action':'update','status':status,'message':message},  
//	      	dataType : 'json',
			success : function(data) {//返回数据根据结果进行相应的处理  
//				console.log(data);return;
				//关闭弹窗
				if(data){
					layer.close(layer_index_sku_relate_code);
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

//添加一条关联，注意：并非提交
function relate_add_one()
{
	var code_id = $('input[name="relate_code"]:checked').val();
	var code_name = $('input[name="relate_code"]:checked').attr('code_name');
	var num = $("#relate_num").val();
	if( code_id==null )
	{
		layer.msg('必须选择一个code！');
		return false;
	}
	var append_html = 
	"<div id='relate_added_"+code_id+"' class='relate_added'>"+
		"<span>"+code_name+"</span>"+
		"<span style='font-weight:bold;margin-left:5px;'> * "+num+"</span>"+
		"<span class='text-red' style='margin-left:15px;font-size:10px;cursor:pointer;' onclick='relate_delete_one("+code_id+")'>删除</span>"+
		"<input type='hidden' name='added_code_id' value='"+code_id+"'/>"+
		"<input type='hidden' name='added_num' value='"+num+"'/>"+
	"</div>";
	$("#relate_added_container").append(append_html);
	
	//检查当前添加条数
	var relate_added_total_number = $("#relate_added_container").children().length;
	if( relate_added_total_number > 1 )
	{
		$("#sku_name_container").css("display","inline-block");
	}
}

//删除一条关联
function relate_delete_one(code_id)
{
	$("#relate_added_"+code_id).remove();
	
	//检查当前添加条数
	var relate_added_total_number = $("#relate_added_container").children().length;
	if( relate_added_total_number < 2 )
	{
		$("#sku_name_container").css("display","none");
	}
}

//
function relate_submit()
{
	var data = $(".relate_added");
	if( data.length == 0 )
	{
		layer.msg("必须选择至少一个code！");
		return false;
	}
	var sku_name = "";
	if( data.length >= 2 )
	{
		var sku_name = $("#sku_name").val();
		if( sku_name=="" || sku_name==null )
		{
			layer.msg("必须为套件sku添加名称！");
			return false;
		}
	}
	
	var sku_id = $("#sku_id").val();
	var data_json = [];
	for(var i=0;i<data.length;i++)
	{
		var temp = $(data[i]).children("input[name=added_code_id]")[0];
		var temp_code_id = $(temp).val();
		var temp = $(data[i]).children("input[name=added_num]")[0];
		var temp_num = $(temp).val();
		data_json.push({code:temp_code_id,num:temp_num});
	}
	
	$.ajax({
		type : "POST",
        url : _CONTROLLER_+"/ajax_sku_relate_code",
        data : {'action':'update','data':data_json,'sku_id':sku_id,'sku_name':sku_name},
        dataType : 'json',
        success : function(success) {//返回数据根据结果进行相应的处理  
//        	console.log(data);return false;
        	if(success)
    		{
        		var order_id = $("#order_id").val();
        		layer.msg('success');
        		layer.close(layer_index_sku_relate_code);
        		order_refresh(order_id);
    		}
        	else
        	{
        		layer.msg('failed');
        	}
        },
	});
	
	return false;
}

//处理 各选择框的onchange 事件
function relate_select_change(value,tag)
{
	var relate_catalog = $("#relate_catalog");
	var relate_product = $("#relate_product");
	var relate_color = $("#relate_color");
	var relate_size = $("#relate_size");
	var relate_code = $("#relate_code");
	var relate_num = $("#relate_num");

	$.ajax({
		type : "POST",
        url : _CONTROLLER_+"/ajax_web_sku_relate_code",
        data : {'tag':tag,'action':'select_change',
        	'catalog':$("#relate_catalog").val(),
        	'product':$("#relate_product").val(),
        	'color':$("#relate_color").val(),
        	'size':$("#relate_size").val(),
        	'code':$("#relate_code").val()},
        dataType : 'json',
        success : function(data) {//返回数据根据结果进行相应的处理  
//        	console.log(data);
        	for (i = 0; i < data.length; i++) 
        	{
        		$( "#relate_"+data[i]['tag'] ).html( data[i]['html'] );
        	}
        },
        error : function(data) {
        	console.log('error');
        },
        
	});
	
}
