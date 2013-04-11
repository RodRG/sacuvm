<?php
	
	include("xajax/xajax_core/xajaxAIO.inc.php");
	
	include("tableAdmin.php");
	
	$xajax = new xajax();
	
	
	function dbC() 
	{
		if($link= mysql_connect("localhost","root",""))
		{
			if(mysql_select_db('bd_sacuvm',$link))
			{
				return $link;
			}
			else return "The Database don't exist";
		}
		else return "The connection was unsuccesful";
	}
	
	
	function paint_tblareas()
	{		
			
		
		$table= new tableAdmin(); 
		$table->link=dbC(); 
		$table->table='tblareas';
	
		$table->fieldArray=array("nombre","idCampus"=>array("index","nombre","tblcampus")); 
		
		$table->headerArray=array("Nombre","Campus");
		
		$q="SELECT a.idArea, a.nombre, c.nombre AS idCampus FROM tblareas AS a, tblcampus AS c WHERE a.idCampus=c.idCampus";
		
		$contenido=$table->paintTable($q);
		
		
		$respuesta = new xajaxResponse();
		$respuesta->assign("contenido","innerHTML",$contenido);
		return $respuesta;
		
	}
	
	
	$xajax->registerFunction("paint_tblareas");
	
	$xajax->registerFunction("rowNew");	
	$xajax->registerFunction("formSubmit");
	$xajax->registerFunction("rowDelete");	
	$xajax->registerFunction("fieldEdit");
	$xajax->registerFunction("fieldChange");
	

	$xajax->processRequest();
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<?php $xajax->printJavascript("xajax/"); ?>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>SAC :: UVM</title>

<script src="jquery/js/jquery-1.9.1.js"></script>
<script src="jquery/js/jquery-ui-1.10.2.custom.min.js"></script>
<link rel="stylesheet" type="text/css" href=css/style.css"jquery/css/ui-lightness/jquery-ui-1.10.2.custom.min.css" />
<style> @import 'css/styles.css'; </style>
</head>

<body onload="xajax_paint_tblareas();">
	<div class="container" >
    	<center><div class="topHeader">HOME</div></center>
        <div class="separator"></div>
        <center><div id="contenido" class="tableHeight"  style="position:relative;"></div></center>
    </div>
</html>