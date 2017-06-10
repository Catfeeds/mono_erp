//admin.js
function showHideSecondMenu(obj,id)
{
	
	var secondMenu=document.getElementById('second_menu_'+id);
	if(secondMenu.style.display=="none")
	{
		var parentMenu=obj.parentNode.parentNode;
		var secondMenuList=parentMenu.getElementsByTagName("ul");
		for(var i=0;i<secondMenuList.length;i++)
		{
			secondMenuList[i].style.display="none";
		}
		secondMenu.style.display="block";
	}
	else
	{
		secondMenu.style.display="none";
	}
	
}
function show_hide_left_menu(obj)
{
	var oMain=getByClass(document,"admin")[0];
	if(obj.className=="icon-dedent")
	{
		startMove(oMain,{left:0});
		startMove(obj,{left:0});
		obj.className="icon-indent";
	}
	else
	{
		startMove(oMain,{left:180});
		startMove(obj,{left:180});
		obj.className="icon-dedent"
	}
}
function getByClass(oParent,sClass)
{
	var aEle=oParent.getElementsByTagName('*');
	var aResult=[];
	var i=0;
	for(i=0;i<aEle.length;i++)
	{
		if(aEle[i].className==sClass)
		{
			aResult.push(aEle[i]);
		}
	}
	return aResult;
}
/**
*用classNama获取对象:结束
**/

/**
*运动库:开始
**/
function startMove(obj,json,fn)
{
    clearInterval(obj.timer);
    obj.timer=setInterval(function()
	{	
		var bStop=true;
		for(var attr in json){
		var iCur=0;
		if(attr=="opacity")
		{
		  iCur=parseInt(parseFloat(getStyle(obj, attr))*100);    
		}else
		{
		  iCur=parseInt(getStyle(obj,attr));	
		}
		var iSpeed=(json[attr]-iCur)/4;
		iSpeed=iSpeed>0?Math.ceil(iSpeed):Math.floor(iSpeed);	
		if(iCur!=json[attr])
		{
			  bStop=false;
		}
		if(attr=="opacity")
		{
			obj.style.filter="alpha(opacity:"+(iCur+iSpeed)+")";
			obj.style.opacity=(iCur+iSpeed)/100;		
		}else
		{
			 obj.style[attr]=iCur+iSpeed+'px';    	
		}	
    }
	if(bStop)
	{	   
        clearInterval(obj.timer);
		 if(fn){
		    fn();
		 }	   
	 } 
	},30);
}

function getStyle(obj, attr)
{
	if(obj.currentStyle)
	{
	return obj.currentStyle[attr];
	}
	else
	{
		return getComputedStyle(obj, false)[attr];
	}
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