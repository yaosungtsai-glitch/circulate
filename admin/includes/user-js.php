<?php
echo "<script type='text/javascript'> 
function FUNdownloadData(form){
	form.op2.value='downloaddata';
	form.submit();
}
function FUNsearchData(form){	
	if(form.op2.value=='downloaddata'){
		form.op2.value='userlist';
	}
	form.submit();
}
function input_text1(id) {	// 查詢會員姓名、暱稱、email;游標移到欄位時;清空欄位等待輸入
	obj = document.getElementById(id);
	
	if(obj.value == '"._USERADMINQUERYNAME."' || obj.value == '"._USERADMINQUERYNICKNAME."' || obj.value == '"._USERADMINQUERYEMAIL."' ) {
		obj.value = '';
	}
	
}
</script> ";
?>