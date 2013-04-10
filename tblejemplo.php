<?php
	/*
		Revision Final:	29-03-2013
		
		Documentación:
		
		Este es el "template" de como utilizar la clase tableAdmin contenida en el archivo tableAdmin.php.
		
		1. REQUISITOS PARA CLASE
		2. REQUISITOS PARA FUNCIONES
		3. EJMPLO Y PRUEBAS
		
		
		1. REQUISITOS PARA CLASE
			
			-XAJAX
			-jQuery
			-jQuery UI (Para datepickers)
			- funcion dbC() -> CONEXION BASE DE DATOS (NO CAMBIAR NOMBRE DE FUNCION)
			- tener la carpeta "systemImages" en raiz 
			-Registrar las siguientes funciones
			
				$xajax->registerFunction("rowNew");	-> Para insertar nuevas lineas
				$xajax->registerFunction("formSubmit"); -> Para dar de alta los nuevos registros
				$xajax->registerFunction("rowDelete");	-> Para borrar un record
				$xajax->registerFunction("fieldEdit"); -> Para editar un campo
				$xajax->registerFunction("fieldChange"); -> Para cambiar un registro
		
		2. REQUISITOS PARA FUNCIONES
		
			- El nombre de la función para pintar la tabla que gustas debe de ser.
			
			paint_NOMBRETABLA
		
			La razon es que la clase vuelve a pintar la tabla despues de insertar registros y la sintaxis es asi.
			
		
	*/
	
	//3. EJEMPLOS
	//Primero incluimos nuestra clase xajax (Es importante primero incluir xajax ya que table admin hereda funciones de esta)
	include("xajax/xajax_core/xajaxAIO.inc.php");
	//Despues incluimos nuestra clase tableAdmin
	include("tableAdmin.php");
	//Inicializamos nuestra clase xajax
	$xajax = new xajax();
	
	//Funcion para conectarse a nuestra base de datos CAMBIEN LOS PARAMETROS, porfavor NO cambien el nombre de la funcion
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
	
	//EJEMPLO Funcion para pintar tabla "tblejemplo"
	function paint_tblareas()
	{		
			/*
			fieldArray es el arreglo que contiene los campos que vamos a pintar de nuestra tabla,
			(POR OVIAS RAZONES debe de ir de acuerdo al query  que hagamos).
			
			Cada campo que sacamos de nuestra tabla puede tener 3 tipos para desplegar.
			1. text -> DEFAULT
			2. date  -> Da un datepicker al insertar o modificar el campo
			3. index -> Da las opciones de nuestro index al insertar o modificar un campo.
			
			sintaxis fieldArray SIN TIPOS (Por default tipo text)
			
			array("campo1","campo2","campo3");
			
			sintaxis fieldArray con TIPOS
			
			array("campo1"=>"text","campo2"=>"date","campo3"=>array("index","realName","indextable"))
			
			NOTA: Para un tipo index debemos tener en cuenta varias cosas
			1. Nuestro query debe de estar ligado es decir
				- Si se fijan el campo de nombre que traigo de la otra tabla "campus" LO LLAMO "idCampus" que
				  es el nombre real del campo en la tabla areas.
				  
				 SELECT a.idArea, a.nombre, c.nombre AS idCampus FROM tblareas AS a, tblcampus AS c WHERE a.idCampus=c.idCampus
				 
				En este caso si quisieramos llamar este campo pondriamos
				
				array("idCampus"=>array("index","nombre","tblcampus"))
		*/
		
		$table= new tableAdmin(); //Inicializamos clase tableAdmin
		$table->link=dbC(); //Pasamos nuestra conexion a la clase
		$table->table='tblareas';//Pasamos el nombre de la tabla que tratamos de pintar
		$table->border=0;
	
		$table->fieldArray=array("nombre","idCampus"=>array("index","nombre","tblcampus")); 
		//header array solo es para indicar como nos gustaria q se llamaran nuestras columnas de la tabla
		$table->headerArray=array("Nombre","Campus");
		//Hacemos nuestro query
		$q="SELECT a.idArea, a.nombre, c.nombre AS idCampus FROM tblareas AS a, tblcampus AS c WHERE a.idCampus=c.idCampus";
		/*
			Mandamos pintar nuestra tabla con la funcion paintTable de nuestra clase los parametros son solo el query, por default 
			la tabla se pinta con la columna id, si quieren eliminar la columna id solo deben de poner la bandera 1 despues del query.
			
			EJEMPLO $contenido=$table->paintTable($q,1);
		  */
		$contenido=$table->paintTable($q);
		
		//Por ultimo mandamos pintar nuestra tabla en el div que queramos	
		$respuesta = new xajaxResponse();
		$respuesta->assign("contenido","innerHTML",$contenido);
		return $respuesta;
		
	}
	
	//registramos nuestra funcion
	$xajax->registerFunction("paint_tblareas");
	//registramos las funciones OBLIGATORIAS
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
<!-- INCLUIMOS NUESTRAS LIBRERIAS JQUERY Y JQUERY UI OBLIGATORIAS ASI COMO EL CSS PARA EL JQUERY UI-->
<script src="jquery/js/jquery-1.9.1.js"></script>
<script src="jquery/js/jquery-ui-1.10.2.custom.min.js"></script>
<link rel="stylesheet" type="text/css" href="jquery/css/ui-lightness/jquery-ui-1.10.2.custom.min.css" />
</head>

<body onload="xajax_paint_tblareas();">
<!-- EN ESTE EJEMPLO MANDAMOS LLAMAR LA FUNCION CON UN BOTON -->

<div id="contenido"></div>
</body>
</html>