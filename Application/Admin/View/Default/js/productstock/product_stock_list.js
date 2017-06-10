// JavaScript Document

/* 单品*/
function stock_dp(val)
{
	$.ajax({  
        type : "POST",
        url: _ACTION_, //目标地址 
        data : {'stock_dp':val},//数据，这里使用的是Json格式进行传输  
        dataType : 'json',
		beforeSend: function () {
			    $('#list-more01').text("正在加载....");
			},
        success : function(result) {//返回数据根据结果进行相应的处理 
			if(result=="" || result==null){
				$("#jzgd_dp")[0].innerHTML = "<div class='list-more' id='list-more01' >已加载全部</div>";
				}else{
				$.each(result,function(n,value) {
					$("#stock_dp").append("<tr><td >"+value['name']+"</td> <td style='color: red; font-weight:bold;'>"+value['number']+"</td><td style='color: red; font-weight:bold;'>"+value['sty']+"</td><td >最大库存:<span style='color: red; font-weight:bold;'>"+value['maximum']+"</span>  ；最小库存:<span style='color: red; font-weight:bold;'>"+value['minimum']+"</span></td><td align='center'>"+value['edit']+" | "+value['add']+" | "+value['check']+"</td></tr>")
					
					if(value['start'] < value['record_num'] ){
							$("#jzgd_dp")[0].innerHTML = "<div class='list-more' id='list-more01' onclick=stock_dp('"+value['start']+"')>加载更多 ("+value['record_num']+"条记录)</div>";
						}else{
							$("#jzgd_dp")[0].innerHTML = "<div class='list-more' id='list-more01'>已加载全部 ("+value['record_num']+"条记录)</div>";
							}
				});
				}
		},
    });
 
}

/* 套件 */
function stock_tj(val)
{
	$.ajax({  
        type : "POST",
        url: _ACTION_, //目标地址 
        data : {'stock_tj':val},//数据，这里使用的是Json格式进行传输  
        dataType : 'json',
		beforeSend: function () {
			    $('#list-more02').text("正在加载....");
			},
        success : function(result) {//返回数据根据结果进行相应的处理 
			if(result=="" || result==null){
				$("#jzgd_tj")[0].innerHTML = "<div class='list-more' id='list-more01' >已加载全部</div>";
				}else{
				$.each(result,function(n,value) {
					$("#stock_tj").append("<tr><td >"+value['sku_name']+"</td> <td style='color: red; font-weight:bold;'>"+value['number']+"</td><td style='color: red; font-weight:bold;'>"+value['sty']+"</td><td >最大库存:<span style='color: red; font-weight:bold;'>"+value['maximum']+"</span>  ；最小库存:<span style='color: red; font-weight:bold;'>"+value['minimun']+"</span></td><td align='center'>"+value['edit']+" | "+value['add']+" | "+value['check']+"</td></tr>")
					if(value['start'] < value['record_num'] ){
						$("#jzgd_tj")[0].innerHTML = "<div class='list-more' id='list-more01' onclick=page_tj('"+value['start']+"')>加载更多 ("+value['record_num']+"条记录)</div>";
					}else{
						$("#jzgd_tj")[0].innerHTML = "<div class='list-more' id='list-more01'>已加载全部 ("+value['record_num']+"条记录)</div>";
						}
				});
				}
        },
    });  
}

