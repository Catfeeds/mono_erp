// JavaScript Document

function shipping_execl()
{
	document.getElementById('myform').action = _CONTROLLER_+"/delivered_list_execl";		
	document.getElementById('myform').submit();		
}
function shipping_sub()
{
	document.getElementById('myform').action = _ACTION_;		
	document.getElementById('myform').submit();		
}

function sign_execl()
{
	document.getElementById('myform').action = _CONTROLLER_+"/sign_for_execl";		
	document.getElementById('myform').submit();		
}
function sign_sub()
{
	document.getElementById('myform').action = _ACTION_;		
	document.getElementById('myform').submit();		
}
