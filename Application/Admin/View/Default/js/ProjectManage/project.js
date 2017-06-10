/**
 * 
 */
function add_list_project(project_id)
{
	$.ajax({
		type:"POST",
		url:_CONTROLLER_+"/project_subtask_add",
		data : {"project_id":project_id},
		//dataType: 'json',
		success : function(data){
			//自定页
			layer.open({
			  type: 1,
			  area: ['700px', '600px'],
			  skin: 'layui-layer-lan', //样式类名
			  closeBtn: 1, //不显示关闭按钮
			  shift: 2,
			  shadeClose: false, //开启遮罩关闭
			  content: "<form action='"+_CONTROLLER_+"/project_subtask_add/project_id/"+project_id+"' method='post'><div style='margin-left:20px;'><p>任务描述：<textarea name='content' style='width:650px;height:280px;' id='content'></textarea></p><p>操作人：&nbsp;&nbsp;&nbsp;<input type='text' name='operator' id='name' class='admin_input' value=''></p><p>开始时间：<input type='text' style='cursor:pointer;' name='begin_time' id='btime' class='admin_input' onClick='WdatePicker()' value=''></p><p>截止时间：<input type='text' style='cursor:pointer;' name='end_time' id='btime' class='admin_input' onClick='WdatePicker()' value=''></p><p>任务等级：<input type='text' name='level' id='name' class='admin_input' value=''></p><p><input class='button border-main' type='submit' name='dosubmit' value='添加'></p></div></form>" 
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

function project_finish(project_id,name,finish_name)
{
	$.ajax({
		type:"POST",
		url:_CONTROLLER_+"/project_finish",
		data : {"project_id":project_id},
		//dataType: 'json',
		success : function(data){
			//自定页
			layer.open({
			  type: 1,
			  area: ['400px', '200px'],
			  skin: 'layui-layer-lan', //样式类名
			  closeBtn: 1, //不显示关闭按钮
			  title:name+">"+finish_name,
			  shift: 2,
			  shadeClose: false, //开启遮罩关闭
			  content: "<form action='"+_CONTROLLER_+"/project_finish/project_id/"+project_id+"' method='post'><div style='margin:20px;'><p style='margin-'>完成人：&nbsp;&nbsp;&nbsp;<input type='text' name='executers' id='name' class='admin_input' value=''></p><p>完成时间：<input type='text' style='cursor:pointer;' name='finish_time' id='btime' class='admin_input' onClick='WdatePicker()' value=''></p><p><input class='button border-main' type='submit' name='dosubmit' value='完成'></p></div></form>" 
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
function project_start(project_id,name,start_name)
{
	
	$.ajax({
		type:"POST",
		url:_CONTROLLER_+"/project_start",
		data : {"project_id":project_id},
		//dataType: 'json',
		success : function(data){
			//自定页
			layer.open({
			  type: 1,
			  area: ['400px', '200px'],
			  skin: 'layui-layer-lan', //样式类名
			  closeBtn: 1, //不显示关闭按钮
			  title:name+">"+start_name,
			  shift: 2,
			  shadeClose: false, //开启遮罩关闭
			  content: "<form action='"+_CONTROLLER_+"/project_start/project_id/"+project_id+"' method='post'><div style='margin:20px;'><p>开始时间：<input type='text' style='cursor:pointer;' name='start_time' id='btime' class='admin_input' onClick='WdatePicker()' value=''></p><p><input class='button border-main' type='submit' name='dosubmit' value='完成'></p></div></form>" 
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