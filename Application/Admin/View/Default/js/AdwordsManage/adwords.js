//广告管理公共JS
	//广告费来源筛选
	var  adwords_domain = "http://localhost/lilysilkERP/index.php";
	function adwords_fee(obj)
	{
		if(obj.value=="all")
		{
			window.location.href = adwords_domain+"/Admin/AdwordsManage/adwords_fee";
		}
		else if(obj.value=="google")
		{
			window.location.href = adwords_domain+"/Admin/AdwordsManage/adwords_fee/come_from/"+obj.value;
		}
		else if(obj.value=="bing")
		{
			window.location.href = adwords_domain+"/Admin/AdwordsManage/adwords_fee/come_from/"+obj.value;
		}
		else if(obj.value=="yahoo")
		{
			window.location.href = adwords_domain+"/Admin/AdwordsManage/adwords_fee/come_from/"+obj.value;
		}
	}
	//APR国家筛选
	function adwords_select_country(country)
	{
		switch(country)
		{
			case "ca":
				window.location.href = adwords_domain+"/Admin/AdwordsManage/adwords_apr/country/"+country;
				break;
			case "au":
				window.location.href = adwords_domain+"/Admin/AdwordsManage/adwords_apr/country/"+country;
				break;
			case "uk":
				window.location.href = adwords_domain+"/Admin/AdwordsManage/adwords_apr/country/"+country;
				break;
			case "sg":
				window.location.href = adwords_domain+"/Admin/AdwordsManage/adwords_apr/country/"+country;
				break;
			case "fr":
				window.location.href = adwords_domain+"/Admin/AdwordsManage/adwords_apr/country/"+country;
				break;
			case "de":
				window.location.href = adwords_domain+"/Admin/AdwordsManage/adwords_apr/country/"+country;
				break;
			case "nl":
				window.location.href = adwords_domain+"/Admin/AdwordsManage/adwords_apr/country/"+country;
				break;
			case "it":
				window.location.href = adwords_domain+"/Admin/AdwordsManage/adwords_apr/country/"+country;
				break;
			case "es":
				window.location.href = adwords_domain+"/Admin/AdwordsManage/adwords_apr/country/"+country;
				break;
		}
	}