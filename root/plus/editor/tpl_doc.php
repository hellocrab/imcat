<?php 
require('tpl_cfg.php'); 
require(DIR_STATIC.'/ilibs/tpl_doc.imp_htm'); 
?>

<script>
function tClick(id){
	str = document.getElementById(id).innerHTML;
	window.parent.edt_Insert('<?php echo $fid; ?>', str);
	//window.parent.apiInsertEdtID(str);
	alert('['+id+']已经插入!');
	//document.getElementById("SymMessage").innerHTML = '['+spChar+']已经插入! 可关闭窗口或继续插入...';
	return;
}
</script> 
</body>
</html>