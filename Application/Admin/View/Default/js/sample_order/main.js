// JavaScript Document
function order_num_up(k)
{
	for(i=0;i<21;i++)
	{
		if(i!=k)
		{
			$('.div_dis_'+i).addClass('dis_no');
			$('#dis_'+i).removeClass('dis_no');
		}
		else
		{
			$('.div_dis_'+i).removeClass('dis_no');
		}
	}
	$('#dis_'+k).addClass('dis_no');
	$('#number_'+k).addClass('dis_bl');
	$('#style_'+k).addClass('dis_bl');
}
function order_num_add(id,k)
{
	$('#dis_'+k).removeClass('dis_no');
	$('#number_'+k).removeClass('dis_bl');
	$('#style_'+k).removeClass('dis_bl');
}
function order_num_sub(id,k)
{
	number = $('#number_'+k).val();
	style = $('#delivery_style_'+k+' option:selected').val();
	if(!number)
	{
		layer.msg('运单号没有填写');
		return false;
	}
	$.ajax({
		type:"POST",
		url:_CONTROLLER_+"/sample_order_num_update",
		data : {'id':id,'style':style,'number':number},//数据，这里使用的是Json格式进行传输  
		//dataType: 'json',
		success : function(data){
			if(data == 1 || data == 3)
			{
				if(data == 1)
				{
					layer.msg('修改成功');
				}
				else if(data == 3)
				{
					layer.msg('添加成功');
					$('.delivery_img_'+k).html('<img id="334" width="20" src="http://www.lilysilk.com/mono_admin/mono_admin_images/sample_star.png">');
					$('#dis_'+k).html('<span onclick="order_num_up('+k+');" style="background: #999; display:block; padding:5px;">修改单号</span>');
				}
				$('.delivery_val_'+k).text(style+' '+number);
				
				$('#dis_'+k).removeClass('dis_no');
				$('#number_'+k).removeClass('dis_bl');
				$('#style_'+k).removeClass('dis_bl');
			}
			else if(data == 2 || data == 4)
			{
				if(data == 2)
				{
					layer.msg('修改失败，请稍后重试');
				}
				else if(data == 4)
				{
					layer.msg('添加失败，请稍后重试');
				}
			}
			else
			{
				alert('出现错误');	
			}
		},
		error : function()
		{
			alert('出现错误');	
		}
	})
}
//筛选
function screening_sub(type)
{
	var url;
	if(type == 'screening')
	{
		url='/sample_order_explode_execl/type/screening';
	}
	else if(type == 'select')
	{
		url='/sample_order_explode_execl/type/select';
	}
	else if(type == 'word')
	{
		url='/sample_order_word';
	}
	else
	{
		url ="/sample_order";
	}
	document.getElementById('myform').action = _CONTROLLER_+url;	
	$('#myform').submit();
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
