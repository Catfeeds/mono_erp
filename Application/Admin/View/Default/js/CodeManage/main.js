// javascript function for Controller CodeManage

function get_product_of_catalog(val)
{
	$.ajax({  
        type : "POST",
        url : _MODULE_+"/CodeManage/ajax_get_catalog_product",//路径  
        data : {'catalog_id':val},//数据，这里使用的是Json格式进行传输  
        dataType : 'json',
        success : function(result) {//返回数据根据结果进行相应的处理  
		
        	var option = "<option value='-1'>--请选择--</option>";
        	$.each(result,function(n,value) {
                 option += "<option  value='"+value['id']+"'>"+value['name']+"</option>"
        	});
        	$("#product_id")[0].innerHTML = option;
			$('.list-page').remove();
        },
    });  
}

function get_relation_by_sku_code()
{
	var sku_id = $("#product_sku_id").attr("value");
	var code_id = $("#product_code_id").attr("value");
	console.log('sku_id:'+sku_id+',code_id:'+code_id);
	$.ajax({
        type : "POST",
        url : _CONTROLLER_+"/ajax_get_relate",//路径  
        data : {'sku_id':sku_id,'code_id':code_id},//数据，这里使用的是Json格式进行传输  
        dataType : 'json',
        success : function(result) {//返回数据根据结果进行相应的处理  
        	if(result)
    		{
        		$("#number").attr("value",result['number']);
        		$("#relate_id").attr("value",result['id']);
    		}
        },
    });  
}

function enable_before_submit()
{
	$("#product_sku_id").attr("disabled",false);
	$("#product_code_id").attr("disabled",false);
}


function add_relate(product_id)
{
	$("#text_relate_"+product_id).css("display","none");
	$("#select_relate_"+product_id).css("display","inline-block");
	
	$("#add_relate_"+product_id).css("display","none");
	$("#submit_relate_"+product_id).css("display","inline-block");
}

function submit_relate(product_id,sku_id,number)
{
	var code_id = $("#select_relate_"+product_id).attr("value");
	var code_name = $("#select_relate_"+product_id).find("option:selected").text();
	if(code_id!=-1)
	{
		var relate_id = $("#id_relate_"+product_id).attr("value");
		$.ajax({
	        type : "POST",
	        url : _CONTROLLER_+"/ajax_sku_add_relate",//路径  
	        data : {'product_code_id':code_id,'product_sku_id':sku_id,'number':number,'relate_id':relate_id,'product_id':product_id},  
	        dataType : 'json',
	        success : function(relate_id) {//返回数据根据结果进行相应的处理  
	        	$("#id_relate_"+product_id).attr("value",relate_id);//写入relate_id,下次修改不再添加
	        	
	        	$("#text_relate_"+product_id).html(code_name);
	        	$("#text_relate_"+product_id).css("display","inline-block");
	        	$("#select_relate_"+product_id).css("display","none");
	        	
	        	$("#add_relate_"+product_id).css("display","inline-block");
	    		$("#submit_relate_"+product_id).css("display","none");
	        },
	    });  
	}
	else
	{
		alert(code_id);
	}
}
//关联code; id:sku_id; order_id:rt,optional,used in order_plat_list
function code_relate(pro_id, sku_id, order_id, type){
	//iframe层
	iframe_url =  _MODULE_+'/CodeManage/code_list/pro_id/'+pro_id+'/id/'+sku_id+'/type/'+type;
	if(order_id!=null)
	{
		iframe_url += '/order_id/'+order_id;
	}
	layer.open({
	  type: 2,
	  title: '<h2>关联Code</h2>',
	  shadeClose: true,
	  shade: 0.5,
	  maxmin: true, //开启最大化最小化按钮
	  area: ['800px', '60%'],
	  content: iframe_url, //iframe的url
//	  yes: function(){
//		  alert('hhh');
//		  alert(order_id);
//	  },
	});
	
}
// ajax  产品select点击    li展现 code_list页面
function get_product_of_code(val){
	var option="";
	$.ajax({  
        type : "POST",
        url : _MODULE_+"/CodeManage/ajax_get_product_code",//路径  
        data : {'product_id':val},//数据，这里使用的是Json格式进行传输  
        dataType : 'json',
        success : function(result) {//返回数据根据结果进行相应的处理  
        	if(result=="" || result==null){
				$("#code_ul")[0].innerHTML = "<div class='list-page'>暂时没有相关数据</div>";
				}else{
					$.each(result,function(n,value) {
						 option += "<li onclick='code_add(this.innerHTML)' ><input type='text' name='code_num["+value['id']+"]' class='code_num' > <span title="+value['code']+">"+value['code']+"</span><br><span title="+value['name']+">"+value['name']+"</span></li>";
					});
					$("#code_ul")[0].innerHTML = option;
					
				}
				
		}
    });  
	}
// ajax  产品select点击    tr 展现 code页面
function get_code_of_code(product,catalog){
	var option="";
	$.ajax({  
        type : "POST",
        url : _CONTROLLER_+"/ajax_get_product_code",//路径  
        data : {'product_id':product,'catalog_id':catalog},//数据，这里使用的是Json格式进行传输  
        dataType : 'json',
        success : function(result) {//返回数据根据结果进行相应的处理  
        	if(result=="" || result==null){
				$(".update")[0].innerHTML = "<div class='list-page'>暂时没有相关数据</div>";
				}else{
					$(".update").children().remove();
					option +='<tr><th width="45"></th><th width="100">分类</th><th width="100">产品</th><th width="100">尺寸</th><th width="100">颜色</th><th width="100">Code</th><th width="100">名称</th><th width="100">重量</th><th width="100">价格</th><th width="100">工厂</th><th width="100">排序</th><th width="100">生效</th><th width="200" style="text-align:center">操作</th></tr>';
					$.each(result,function(n,value) {
						 option += '<tr><td><input class="check" name="check[]" type="checkbox" value='+value["id"]+'></td><td style="color: #f60">'+value["catalog_name"]+'</td><td style="color:#03c">'+value["product_name"]+'</td><td>'+value["size_name"]+'</td><td>'+value["color_name"]+'</td><td>'+value["code"]+'</td><td style="color:#03c">'+value["name"]+'</td><td>'+value["weight"]+'</td><td>'+value["price"]+'</td><td>'+value["factory_name"]+'</td><td>'+value["sort_id"]+'</td>';
						 if(value["is_work"]==1)
						 {
							 option += '<td><font color="red" class="icon-check"></font></td>';
						 }
						 else
						{
							 option += '<font color="blue" class="icon-times"></font>';
						}
						 
						 option += '<td style="text-align:center"><a class="icon-link" title="查看相关sku" href='+_CONTROLLER_+'/code_have_sku/id/'+value["id"]+'></a>&nbsp;&nbsp;<a class="icon-pencil" title="修改" href='+_CONTROLLER_+'/code_edit/id/'+value["id"]+'></a>&nbsp;&nbsp;<a class="icon-trash-o" title="删除" href='+_CONTROLLER_+'/code_delete/id/'+value["id"]+' onclick="{if(confirm("确认删除?")){return true;}return false;}"></a>&nbsp;&nbsp;<a class="icon-plus" title="关联SKU" href='+_CONTROLLER_+'/code_sku/id/'+value["id"]+'></a></td></tr>';
					});
					$(".update")[0].innerHTML = option;
				}
				
		}
    });  
}	
function code_add(val)
{
	$("#code_id")[0].innerHTML += '<li class="add abc" >'+val+'<span class="close rotate-hover add_span" onclick="$(this).parent().remove()"></span></li>';
	$('#code_list').css('display','none');
	aa=document.getElementsByClassName('abc').length;
	if(aa>1)
	{
		$('#dis_no').css('display','block');
	}
}		
// ajax  产品select点击   option展现
function get_product_of_code_option(val){
	var option="";
	$.ajax({  
        type : "POST",
        url : _MODULE_+"/CodeManage/ajax_get_product_code",//路径  
        data : {'product_id':val},//数据，这里使用的是Json格式进行传输  
        dataType : 'json',
        success : function(result) {//返回数据根据结果进行相应的处理  
        	if(result=="" || result==null){
				var option = "<option value='-1'>--请选择--</option>";
				}else{
					var option = "<option value='-1'>--请选择--</option>";
					$.each(result,function(n,value) {
						 option += "<option value='"+value['id']+"'>"+value['name']+"</option>"
					});
				}
				$("#product_code_id")[0].innerHTML = option;
		}
    });  
	}	
//刷新待关联sku列表	
function refresh(){
	window.location.href=_ACTION_;
	}
//刷新待关联sku列表	 end		

function code_list_sub()
{
	aa=document.getElementsByClassName('abc').length;
	sku_name = document.getElementById('sku_name').value;
	if(aa>1 && sku_name=='')
	{
		layer.msg('请添加sku_name');
	}
	else
	{
		document.getElementById('myform').action = _CONTROLLER_+"/code_list_add";	 
		document.getElementById('myform').submit(); 
	}
}



//导出code  选中 表单提交
function code_form_submit()
{
	  document.getElementById('myform').action = _CONTROLLER_+"/code_execl_export";	 
	  document.getElementById('myform').submit(); 

}
//导出code  筛选 表单提交
function code_screening_form_submit()
{
	  document.getElementById('myform_screening').action = _CONTROLLER_+"/code_screening_execl_export";	 
	  document.getElementById('myform_screening').submit(); 

}
