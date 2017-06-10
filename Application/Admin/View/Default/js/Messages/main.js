function chat_open(name,page)
{
	var fenge='';
	var aa = '';
	username = $('.username').val();
	if(page !== "Index")
	{
		window.location.href = _CONTROLLER_+"/Index/name/"+name+".html";
	}
	else
	{
		if(username !="")
		{
			fenge=";";	
		}
		username_name = username.split(';');
		for(i=0;i<username_name.length;i++)
		{
			if(username_name[i] == name)
			{
				aa = 1;	
			}
		}
		if(aa == 1)
		{
			val =username;
		}
		else
		{
			val =username+fenge+name;
		}
		$('.username').val(val);
	}
}
//提交私信
function but_sub()
{
	username = $('.username').val();
	if(username =='')
	{
		layer.msg('收件人未填写');
	}
	else
	{
		document.getElementById('myform').action=_CONTROLLER_+"/content_submit";
		$('#myform').submit();
	}
	
	
}