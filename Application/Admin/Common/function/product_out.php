<?php
function product_out_note(){
	$note_array=array("库存转FBA","盘点出错","其他");
	$note='';
	for($i=0;$i<count($note_array);$i++){
		$note.="<option value='$note_array[$i]'>$note_array[$i]</option>";
	}
	return $note;
}