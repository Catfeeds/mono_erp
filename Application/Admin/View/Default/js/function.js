//公共js函数
function _addLoadEvent(func,args)
{
    var oldonload=window.onload;
    if(typeof window.onload != 'function')
    {
        window.onload=function()
        {
        	func.apply(null,args);
        }
    }
    else
    {
        window.onload=function()
        {
            oldonload();
            func.apply(null,args);
        }
    }
    
}

function writeObj(obj){ 
    var description = ""; 
    for(var i in obj){   
        var property=obj[i];   
        description+=i+" = "+property+"\n";  
    }   
    alert(description); 
}

function check_form(form)
{
	var list = $("#"+form+" :input[check]" );
	for(i=0;i<list.length;i++)
	{
		var one = $(list[i]);
		var check = one.attr("check");
		var msg = one.attr("msg");
		var value = one.attr("value");
//		console.log(i);
//		console.log(check);
//		console.log(msg);
//		console.log(value);
		
		if( check=="empty" )//空
		{
			var trim = $.trim(value);
			if( trim=="" || trim==null )
			{
				layer.msg(msg);
				return false;
			}
		}
		else if( check=="number")//数字
		{
			if( isNaN(value) || value=="" || value==null )
			{
				layer.msg(msg);
				return false;
			}
		}
		else//不等于
		{	
			if( check==value )
			{
				layer.msg(msg);
				return false;
			}
		}
	}
	return true;
}

/*
 * 获取网站新订单
 * ajax_url：传入获取网站订单的控制器路径，固定使用：
 */
function get_web_order(ajax_url)
{
	var aj = $.ajax({
		  url:ajax_url,// 跳转到 action  
		  type:'post',  
		  cache:false,  
		  dataType:'json',  
		  success:function(data) {  
		         if(data.msg =="true" ){ 
		        	 layer.msg('发现并已经更新新订单', {icon: 16});
		        	 location.reload(); 
		         }else{  
		               
		         }  
		      },  
		 });
}

function U(url,param)
{
	 var args = arguments;
	 console.log(args);
}