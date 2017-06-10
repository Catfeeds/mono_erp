// JavaScript Document

/* 单品*/
function come_dp(val)
{
	$.ajax({  
        type : "POST",
        url: _ACTION_, //目标地址 
        data : {'come_dp':val},//数据，这里使用的是Json格式进行传输  
        dataType : 'json',
		beforeSend: function () {
			    $('#list-more01').text("正在加载....");
			},
        success : function(result) {//返回数据根据结果进行相应的处理 
			if(result=="" || result==null){
				$("#come_dp01")[0].innerHTML = "<div class='list-more' id='list-more01' >已加载全部</div>";
				}else{
				$.each(result,function(n,value) {
					$("#come_dp").append("<tr><td class='td_cc'>"+value['ckname']+"</td><td style='color: red; font-weight:bold;'>"+value['number']+"</td><td >"+value['sty']+"</td><td>"+value['apply']+"</td><td align='center'>"+value['begin']+"</td><td align='center'>"+value['end']+"</td><td >"+value['sta']+"  ("+value['change']+")</td></tr>")
					
					if(value['start'] < value['record_num'] ){
							$("#come_dp01")[0].innerHTML = "<div class='list-more' id='list-more01' onclick=come_dp('"+value['start']+"')>加载更多 ("+value['record_num']+"条记录)</div>";
						}else{
							$("#come_dp01")[0].innerHTML = "<div class='list-more' id='list-more01'>已加载全部 ("+value['record_num']+"条记录)</div>";
							}
				});
				}
		},
    });
 
}

/* 套件 */
function come_tj(val)
{
	$.ajax({  
        type : "POST",
        url: _ACTION_, //目标地址 
        data : {'come_tj':val},//数据，这里使用的是Json格式进行传输  
        dataType : 'json',
		beforeSend: function () {
			    $('#list-more02').text("正在加载....");
			},
        success : function(result) {//返回数据根据结果进行相应的处理 
			if(result=="" || result==null){
				$("#come_tj01")[0].innerHTML = "<div class='list-more' id='list-more01' >已加载全部</div>";
				}else{
				$.each(result,function(n,value) {
					$("#come_tj").append("<tr><td class='td_cc'>"+value['ckname']+"</td><td style='color: red; font-weight:bold;'>"+value['number']+"</td><td >"+value['sty']+"</td><td >"+value['apply']+"</td><td align='center'>"+value['begin']+"</td><td align='center'>"+value['end']+"</td><td >"+value['sta']+"  ("+value['change']+")</td></tr>")
					
					if(value['start'] < value['record_num'] ){
						$("#come_tj01")[0].innerHTML = "<div class='list-more' id='list-more01' onclick=come_tj('"+value['start']+"')>加载更多 ("+value['record_num']+"条记录)</div>";
					}else{
						$("#come_tj01")[0].innerHTML = "<div class='list-more' id='list-more01'>已加载全部 ("+value['record_num']+"条记录)</div>";
						}
				});
				}
        },
    });  
}


