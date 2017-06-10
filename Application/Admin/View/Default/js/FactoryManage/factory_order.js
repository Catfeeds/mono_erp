// javascript function for Controller CodeManage

/* 单品订单 */
function page_dp(val)
{
	$.ajax({  
        type : "POST",
        url: _ACTION_, //目标地址 
        data : {'ac_dp01':val},//数据，这里使用的是Json格式进行传输  
        dataType : 'json' ,
		beforeSend: function () {
			    $('#list-more01').text("正在加载....");
			},
        success : function(result) {//返回数据根据结果进行相应的处理 
			if(result=="" || result==null){
				$("#jzgd_dp")[0].innerHTML = "<div class='list-more' id='list-more01' >已加载全部</div>";
				}else{
				$.each(result,function(n,value) {
					$("#dpdd").append("<tr><td align='center'>"+value['fac_id']+"</td><td >"+value['name']+"</td><td >"+value['username']+"</td><td >"+value['number']+"</td><td >"+value['sty']+"</td><td >"+value['begin']+"</td><td >"+value['end']+"</td><td >"+value['actual_end']+"</td><td >"+value['factory_sta']+"</td><td align='center'>"+value['over']+"</td></tr>")
					if(value['start'] < value['record_num']){
						$("#jzgd_dp")[0].innerHTML = "<div class='list-more' id='list-more01' onclick=page_dp('"+value['start']+"')>加载更多 ("+value['record_num']+"条记录)</div>";
					}else{
						$("#jzgd_dp")[0].innerHTML = "<div class='list-more' id='list-more01' >已加载全部 ("+value['record_num']+"条记录)</div>";
						}
				});
				}
		},
    });
 
}

/* 套件订单 */
function page_tj(val)
{
	$.ajax({  
        type : "POST",
        url: _ACTION_, //目标地址 
        data : {'ac_tj02':val},//数据，这里使用的是Json格式进行传输  
        dataType : 'json',
		beforeSend: function () {
			    $('#list-more02').text("正在加载....");
			},
        success : function(result) {//返回数据根据结果进行相应的处理 
			if(result=="" || result==null){
				$("#jzgd_tj01")[0].innerHTML = "<div class='list-more' id='list-more01' >已加载全部</div>";
				}else{
				$.each(result,function(n,value) {
					$("#tjdd").append("<tr><td align='center'>"+value['fac_id']+"</td><td >"+value['sku_name']+"</td><td >"+value['username']+"</td><td >"+value['number']+"</td><td >"+value['sty']+"</td><td >"+value['begin']+"</td><td >"+value['end']+"</td><td >"+value['actual_end']+"</td><td >"+value['factory_sta']+"</td><td align='center'>"+value['over']+"</td></tr>")
					if(value['start'] < value['record_num'] ){
						$("#jzgd_tj01")[0].innerHTML = "<div class='list-more' id='list-more01' onclick=page_tj('"+value['start']+"')>加载更多 ("+value['record_num']+"条记录)</div>";
					}else{
						$("#jzgd_tj01")[0].innerHTML = "<div class='list-more' id='list-more01'>已加载全部 ("+value['record_num']+"条记录)</div>";
						}
				});
				}
			
        },
    });  
}

