function order_manual_add(order_manual_id,action)
	{	
		if(action=="fetch")//渲染弹窗
		{
			$.ajax({
				type:"POST",
				url:_CONTROLLER_+"/ajax_order_manual_relate_code",
				data : {"order_manual_id":order_manual_id,"action":action},
				//dataType: 'json',
				success : function(data){
					//打开弹窗
					layer.open({
						zIndex: 1,
						type: 1,
						skin: 'layui-layer-lan', //样式类名
						title: '<h2>添加关联产品</h2>',
						offset: ['100px'],
						area: ['800px', '80%'], //宽高
						content: data,
					});
				}
			})
		}
		 	
	}
	function order_manual_select_change(tag)
	{
		$.ajax({
			type : "POST",
	        url : _CONTROLLER_+"/ajax_order_manual_relate_code",
	        data : {'tag':tag,'action':'select_change',
	        	'catalog':$("#relate_catalog").val(),
	        	'product':$("#relate_product").val(),
	        	'color':$("#relate_color").val(),
	        	'size':$("#relate_size").val()},
	        dataType : 'json',
	        success : function(data) {//返回数据根据结果进行相应的处理  
	        	for(i = 0; i < data.length; i++) 
	        	{
	        		$("#relate_"+data[i]['tag']).html( data[i]['html']);
	        	}
	        },	
	        error : function(data) {
	        	console.log('error');
	        }
		});
	}
	function customization()
	{
		if($('#order_customization').css('display','none'))
		{
			$('#order_customization').css('display','block');
		}
		else
		{
			$('#order_customization').css('display','none');
		}
	}
	function order_manual_submit()
	{
		$.ajax({
			type : "POST",
	        url : _CONTROLLER_+"/ajax_order_manual_relate_code",
	        data : {'action':'add_order_customization',
	        	'order_manual_id':$("#order_manual_id").val(),
	        	'product_id':$("#relate_product").val(),
	        	'color_id':$("#relate_color").val(),
	        	'size_id':$("#relate_size").val(),
	        	'price':$("#price").val(),
	        	'number':$("#number").val(),
	        	'order_customization':$("#order_customization").val()},
	        dataType : 'json',
	        success : function(data) {//返回数据根据结果进行相应的处理  
	        	alert("添加产品成功!");
	        	location.href = _CONTROLLER_+"/order_manual_product/id/"+$("#order_manual_id").val()+".html";
	        },	
	        error : function(data) {
	        	alert("添加产品失败!");
	        }
		});
	}
	function order_manual_update(order_manual_id)
	{
		$.ajax({
			type:"POST",
			url:_CONTROLLER_+"/order_manual_update",
			data : {"order_manual_id":order_manual_id},
			//dataType: 'json',
			success : function(data){
				//自定页
				layer.open({
				  type: 1,
				  area: ['400px', '200px'],
				  skin: 'layui-layer-lan', //样式类名
				  closeBtn: 1, //不显示关闭按钮
				  shift: 2,
				  shadeClose: false, //开启遮罩关闭
				  content: "<form action='"+_CONTROLLER_+"/order_manual_update/order_manual_id/"+order_manual_id+"' method='post'><div style='margin-left:20px;margin-top: 20px;'><p>任务描述：<select name='status'><option value='未审核'>未审核</option><option value='待审核'>待审核</option><option value='待收货'>待收货</option><option value='待发货'>待发货</option><option value='历史'>历史</option><option value='退货'>退货</option><option value='换货'>换货</option></select></p><p style='margin-top: 50px'><input class='button border-main' type='submit' name='dosubmit' value='修改'></p></div></form>" 
				});
				KindEditor.ready(function(K){
					editor = K.create('textarea[id="content"]', {
						allowFileManager: true,
						afterBlur: function(){this.sync();},
					});
				});
			}
		}) 
	}