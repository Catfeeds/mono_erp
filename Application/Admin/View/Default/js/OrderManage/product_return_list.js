// JavaScript Document
function screening_sub(sta,data)
{
	start_time = $('#start_time').val();
	end_time = $('#end_time').val();
	
	if(data)
	{
		if(start_time && end_time)
		{
			if(sta)
			{
				window.location.href = _ACTION_+"/start_time/"+start_time+"/end_time/"+end_time+"/data/"+data+"/sta/"+sta+".html";
			}
			else
			{
				window.location.href = _ACTION_+"/start_time/"+start_time+"/end_time/"+end_time+"/data/"+data+".html";
			}
		}
		else
		{
			if(sta)
			{
				window.location.href = _ACTION_+"/data/"+data+"/sta/"+sta+".html";
			}
			else
			{
				window.location.href = _ACTION_+"/data/"+data+".html";
			}
			
		}
	}
	else if(sta)
	{
		if(start_time && end_time)
		{
			if(data)
			{
				window.location.href = _ACTION_+"/start_time/"+start_time+"/end_time/"+end_time+"/data/"+data+"/sta/"+sta+".html";
			}
			else
			{
				window.location.href = _ACTION_+"/start_time/"+start_time+"/end_time/"+end_time+"/data/"+data+".html";
			}
			
		}
		else
		{
			if(data)
			{
				window.location.href = _ACTION_+"/data/"+data+"/sta/"+sta+".html";
			}
			else
			{
				window.location.href = _ACTION_+"/sta/"+sta+".html";
			}
		}
	}
	else
	{
		window.location.href = _ACTION_+"/start_time/"+start_time+"/end_time/"+end_time+".html";
	}
}