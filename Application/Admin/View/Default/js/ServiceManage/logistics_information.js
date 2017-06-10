
function btnSnap(style,order_number,update_one,id) //update_one  更新页面的一条记录    number_update页面为 1   id 判断显示class
{
	layer.load(1,{
	  closeBtn: 1, //不显示关闭按钮
	  shadeClose: true, //开启遮罩关闭
	  shade: [0.5,'#000'] //0.1透明度的白色背景
	  
	});
	$.ajax({
			type: "POST",
			url: _APP_+"/Admin/ServiceManage/logistics_information",
			data : {'com':style,'nu':order_number},          //数据，这里使用的是Json格式进行传输  
			dataType : 'json',
			success : function(result){
				if(result=="" || result==null)
				{
					option = '<p style=" text-align: center; color:red;font-weight:bold;">加大马力 再来一次 GO</p>';
				}
				else if(result['status'])
				{
					option = '<p style=" text-align: center; color:red;font-weight:bold;">'+result['message']+'</p>';
					
				}
				else
				{
					option = result;
				}
				layer.open
				({
					type: 1,
					area: ['537px', '530px'],
					title: "快递信息",
					closeBtn: 1,
					shadeClose: true,
					skin: 'layui-layer-lan',
					content: option,
				})
				if(update_one == 1)
				{
					$.ajax
					({
						type: "POST",
						url: _APP_+"/Admin/ServiceManage/delivery_one",
						data : {'com':style,'nu':order_number},          //数据，这里使用的是Json格式进行传输  
						dataType : 'html',
						success : function(res){
							$('.sta_'+id)[0].innerHTML  = res;
						},
						error : function(){
							alert('出现错误！！');
						}
					})
				}
			},
			error : function(){
				alert('查询出现错误！！');
				}
	});
}