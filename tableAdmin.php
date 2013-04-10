<?php
	//include("xajax/xajax_core/xajaxAIO.inc.php");
	class tableAdmin extends xajaxResponse
	{
		public $link;
		public $table;
		public $fieldArray;
		public $headerArray;
		public $border=1;
		public $headerNormal;
		public $headerDelete;
		public $contentNormal;
		public $contentSpecial;
		public $shadow;
		public $rowLetra;
		
		public function paintTable($query,$id=0)
		{
			if($q=mysql_query($query,$this->link))
			{
				$result= mysql_num_rows($q);
				$fieldArray=str_replace('"',";",json_encode($this->fieldArray));
				$content.='<form name="form_'.$this->table.'" id="form_'.$this->table.'" method="post">
									   
								<input type="hidden" id="counter_'.$this->table.'" name="counter_'.$this->table.'" value="1">
									<table align="center">
										<tr>
											<td >
												<a href="#" class="block" 
															onClick="xajax_rowNew(document.getElementById(\'counter_'.$this->table.'\').value,
																										 \''.$this->table.'\',
																										  \''.$fieldArray.'\',
																										  \''.$id.'\');">
													Nuevo Registro
												</a>
											</td>
											<td id="button_'.$this->table.'" ></td>
											</td>
										</tr>
									</table>
									<table border="'.$this->border.'" class="tableCatalogo">';
				if($id==0)
				{
					$content.='<td class="headerCatalogo" style="width:50px;">Id</td>';
				}
				if($this->headerArray!="")
				{
					foreach($this->headerArray as $field)
					{
						$content.='<td class="headerCatalogo">'.$field.'</td>';
					}
				}
				else
				{
					foreach($this->fieldArray as $field)
					{
						$content.='<td>'.$field.'</td>';
					}
				}
				$content.='<td class="headerCatalogoEliminar">Eliminar</td>';
				
				$content.='</tr>
						   <tbody id="tbody_'.$this->table.'"></tbody>';
				if($result>0)
				{					
												
					$count=count($this->fieldArray);
					
					$c=1;
					while($row=mysql_fetch_array($q))
					{
						$content.='<tr id="'.$this->table.'_'.$row[0].'">';
						if($id==0)
						{
							$content.='<td class="rowCatalogoEspecial">'.$row[0].'</td>';
						}
						foreach($this->fieldArray as $key => $fieldType)
						{
							if(is_numeric($key))
							{
								$field=$fieldType;	
								$fieldValue=$row[$field];	
								$type="";					
							}
							else
							{
								$field=$key; 
								if(count($fieldType)>1)
								{
									$type=$fieldType[0];
									$fieldValue=$row[$field].';'.$fieldType[1].';'.$fieldType[2];
								}
								else 
								{
									$type=$fieldType;
									$fieldValue=$row[$field];
								}
								
							}
							
							$content.='<td id="td_'.$this->table.'_'.$field.'_'.$row[0].'" class="rowCatalogo">
											<a class="rowCatalogoLetra" onClick="xajax_fieldEdit(\''.$row[0].'\',\''.$field.'\',\''.$fieldValue.'\',\''.$type.'\',\''.$this->table.'\');" href="#">
												'.$row[$field].'
											</a>
									   </td>';
						}						
						$content.='	   <td class="headerCatalogoEliminar">
									   		<a href="javascript:xajax_rowDelete(\''.$row[0].'\',\''.$this->table.'\');" 
											   onclick="return confirm(\'Borrar?\');">
												<img src="systemImages/close.gif" width="10px">
			 								</a>
									   </td>';
						$content.='</tr>';
					}
					
				}
				else $content.='<tr><td>No records selected</td></tr>';
				$content.='</table></form>';
			}
			else $content='<br>There was an error with your query '.mysql_error().'<br>';
			
			return $content;
		}
		
		public function newRow($counterValue,$id)
		{
			$fieldArray=str_replace('"',';',json_encode($this->fieldArray));
			$response = new xajaxResponse();
			if($counterValue==1)
			{
				$addInput='<a href="#" class="block" onClick="xajax_formSubmit(\''.$fieldArray.'\',document.getElementById(\'counter_'.$this->table.'\').value, \''.$this->table.'\');" >Submit</a>';				
				$response->assign("button_".$this->table,"innerHTML",$addInput);				
			}
			
			$response->create("tbody_".$this->table,"tr","tr_".$this->table.'_'.$counterValue);
			
			$c=1;
			if($id==0)
			{
				$response->create("tr_".$this->table.'_'.$counterValue,"td","td_".$this->table.'_'.$counterValue.'_'.$c);
				$response->script("$('#td_".$this->table."_".$counterValue.'_'.$c."').addClass('rowCatalogoEspecial');");
				$c++;
			}
			
			foreach ( $this->fieldArray as $key => $fieldType)
			{
				if(!is_numeric($key)) 
				{
					$field=$key;
					if(count($fieldType)>1)
					{
						$switch=$fieldType[0];
					}
					else $switch=$fieldType;
					switch($switch)
					{
						case 'date':
							$input='<input type="text" class="datepicker" name="'.$this->table.'_'.$field.'_'.$counterValue.'" id="'.$this->table.'_'.$field.'_'.$counterValue.'">';
						break;
						case 'index':
												
							$q="SELECT ".$field.", ".$fieldType[1]." FROM  ".$fieldType[2]." ";
							$query=mysql_query($q,$this->link);
							$input='<select name="'.$this->table.'_'.$field.'_'.$counterValue.'" id="'.$this->table.'_'.$field.'_'.$counterValue.'">
									<option value=""></option>';
							while($row=mysql_fetch_array($query))
							{
								$input.='<option value="'.$row[$field].'">'.$row[$fieldType[1]].'</option>';
							}
							$input.='</select>';
						break;
						default:
							$input='<textarea name="'.$this->table.'_'.$field.'_'.$counterValue.'" id="'.$this->table.'_'.$field.'_'.$counterValue.'"></textarea>';
						break;
					
					}
				}
				else
				{
					$field=$fieldType;
					$input='<textarea name="'.$this->table.'_'.$field.'_'.$counterValue.'" id="'.$this->table.'_'.$field.'_'.$counterValue.'"></textarea>';
				}
				
				$response->create("tr_".$this->table.'_'.$counterValue,"td","td_".$this->table.'_'.$counterValue.'_'.$c);
				$response->assign("td_".$this->table.'_'.$counterValue.'_'.$c,"innerHTML",$input);
				$response->assign("td_".$this->table.'_'.$counterValue.'_'.$c,"class",$this->contentNormal);
				$response->script("$('#td_".$this->table."_".$counterValue.'_'.$c."').addClass('rowCatalogo');");
				$c++;
			}		
			$close='<a href="#" onclick="$(\'#tr_'.$this->table.'_'.$counterValue.'\').remove();">
						<img src="systemImages/close.gif" width="10px">
			 		</a>';
			$response->create("tr_".$this->table.'_'.$counterValue,"td","td_".$this->table."_remove_".$counterValue);		
			$response->assign("td_".$this->table."_remove_".$counterValue,"innerHTML",$close);
			$response->script("$('#td_".$this->table."_remove_".$counterValue."').addClass('rowCatalogoEspecial');");
			$newValue=$counterValue+1;
			$response->script("document.getElementById('counter_".$this->table."').value= ".$newValue."");
			$response->script("$(\".datepicker\").datepicker({ dateFormat: \"yy-mm-dd\" });");
			
			return $response;
		}
				
		public function makeFieldEditable($idValue,$fieldName,$fieldValue,$fieldType)
		{
			$response= new xajaxResponse();
			
			switch($fieldType)
			{
				case 'date':
					$newField='<input type="text" class="datepicker" name="'.$this->table.'_'.$fieldName.'_'.$idValue.'_new" id="'.$this->table.'_'.$fieldName.'_'.$idValue.'_new"   onchange="xajax_fieldChange( \''.$this->table.'\',
							   							  \''.$fieldName.'\',
														  document.getElementById(\''.$this->table.'_'.$fieldName.'_'.$idValue.'_new\').value,
														 \''.$idValue.'\',
														 \''.$fieldType.'\');">';
				break;
				case 'index':
					$explode =explode(";",$fieldValue);
					$q="SELECT ".$fieldName.", ".$explode[1]." FROM  ".$explode[2]." ";
					$query=mysql_query($q,$this->link);
					$newField='<select name="'.$this->table.'_'.$fieldName.'_'.$idValue.'_new" id="'.$this->table.'_'.$fieldName.'_'.$idValue.'_new" 					
									   onblur="xajax_fieldChange( \''.$this->table.'\',
							   							  \''.$fieldName.'\',
														  document.getElementById(\''.$this->table.'_'.$fieldName.'_'.$idValue.'_new\').value,
														 \''.$idValue.'\',
														 \''.$fieldType.'\');">';
					while($row=mysql_fetch_array($query))
					{
						$newField.='<option value="'.$row[$fieldName].';'.$row[$explode[1]].';'.$explode[1].';'.$explode[2].'">'.$row[$explode[1]].'</option>';
					}
					$newField.='</select>';
				break;
				default:
				$newField ='<textarea
							   rows="4"
							   cols="25"
							   name="'.$this->table.'_'.$fieldName.'_'.$idValue.'_new" 
							   id="'.$this->table.'_'.$fieldName.'_'.$idValue.'_new" 
							   onblur="xajax_fieldChange( \''.$this->table.'\',
							   							  \''.$fieldName.'\',
														  document.getElementById(\''.$this->table.'_'.$fieldName.'_'.$idValue.'_new\').value,
														 \''.$idValue.'\',
														 \''.$fieldType.'\');" >'.$fieldValue.'</textarea>';
			}
			$response->assign("td_".$this->table."_".$fieldName.'_'.$idValue,"innerHTML",$newField);
			$response->script("$(\".datepicker\").datepicker({ dateFormat: \"yy-mm-dd\" });");
			return $response;
		}
		
		public function changeField($field,$fieldValue,$idValue,$fieldType)
		{
			
			$qidName="SELECT COLUMN_NAME  
				  FROM INFORMATION_SCHEMA.COLUMNS 
				  WHERE table_name = '".$this->table."'
				  LIMIT 1";		
			$idName=mysql_fetch_row(mysql_query($qidName,$this->link));
			$idName=$idName[0];
			
			$q="UPDATE ".$this->table." SET ".$field."='".$fieldValue."' WHERE ".$idName."='".$idValue."'";
			if($query=mysql_query($q,$this->link))
			{
				$uQ="SELECT ".$field." FROM ".$this->table." WHERE ".$idName."='".$idValue."'";
				$uQuery=mysql_query($uQ,$this->link);
				while($row=mysql_fetch_array($uQuery))
				{
					if($row[$field]!="")
					{
						if($fieldType=="index")
						{
							$explode = explode(';',$fieldValue);
							$valueField=$explode[1].';'.$explode[2].';'.$explode[3];				
							$readValue=$explode[1];
						}
						else 
						{
							$valueField=$row[$field];
							$readValue=$fieldValue;
						}
					}
					else $valueField="NULL";
					
					$newValue='<a  class="rowCatalogoLetra"  href="#" onClick="xajax_fieldEdit(\''.$idValue.'\',\''.$field.'\',\''.$valueField.'\',\''.$fieldType.'\',\''.$this->table.'\');">
													'.$readValue.'
							   </a>';
				}
				
			}else $newValue="Update Error";
			
			$response = new xajaxResponse();
			$response->assign('td_'.$this->table.'_'.$field.'_'.$idValue,"innerHTML",$newValue);
			return $response;
		}
		
		public  function deleteRow($idValue,$idName)
		{
			$q="DELETE FROM ".$this->table." WHERE ".$idName."='".$idValue."'";
			$response= new xajaxResponse();
			if($query= mysql_query($q,$this->link))
			{
				$response->remove($this->table.'_'.$idValue);
			}
			else $response->assign($this->table.'_'.$idValue,"innerHTML",'No se puede borrar');
			
			return $response;
			
		}
		public function submitForm($fieldArray,$tableCounter,$query)
		{
			$fieldArray=str_replace(';','"',$fieldArray);
			$response = new xajaxResponse();
			if($query == "")
			{
				$response->script('
									var fieldArray = jQuery.parseJSON(\''.$fieldArray.'\');
									var query="";
									var queryFinal="";
									var nombre="";
									var c;
									for(var i = 1;i<'.$tableCounter.';i++)
									{
										query = "INSERT INTO '.$this->table.' SET ";
										c=1;
										for (var p in fieldArray) 
										{
											if(c!=1)
											{
												query +=", ";
											}
											
											if(isNaN(p))
											{
												nombre=p;
											}
											else
											{
												nombre=fieldArray[p];
											}	
											if($("#'.$this->table.'_"+nombre+"_"+i).val()!= null)
											{
												query  += nombre+"=\'"+$("#'.$this->table.'_"+nombre+"_"+i).val()+"\' ";
											}
											else
											{
												query="";
											}
											c++;									 
										}								
										queryFinal+=query+";";
									}
									xajax_formSubmit(\''.$fieldArray.'\',\''.$tableCounter.'\',\''.$this->table.'\',queryFinal);
									//alert(queryFinal);
								  ');
			}
			else
			{
				$queryArray=explode(';',$query);
				foreach($queryArray as $q)
				{
					mysql_query($q,$this->link);
				}
				$response->assign("button_".$this->table,"innerHTML",'<img src="systemImages/loader.gif">');								
				$response->script("xajax_paint_".$this->table.'();');
			}
			return $response;
		}
		
		
	}
	
	function rowNew($counterValue,$tableName,$fieldArray,$id)
	{
		$table = new tableAdmin();
		$table->table=$tableName;
		$table->link=dbC();		
		$array=json_decode(str_replace(";",'"',$fieldArray));
		
		$table->fieldArray= $array;
		
		$response= $table->newRow($counterValue,$id);
		return $response;
	}		
	
	function fieldEdit($idValue,$fieldName,$fieldValue,$fieldType,$tableName)
	{
		$table = new tableAdmin();
		$table->table=$tableName;
		$table->link=dbC();
		$response= $table->makeFieldEditable($idValue,$fieldName,$fieldValue,$fieldType);
		return $response;
	}
	
	function fieldChange($tableName,$field,$fieldValue,$idValue,$fieldType)
	{		
		$table = new tableAdmin();
		$table->table=$tableName;
		$table->link=dbC();
		$response=$table->changeField($field,$fieldValue,$idValue,$fieldType);
		
		return $response;
	
	}
	function rowDelete($idValue, $tableName)
	{
		$link = dbC();
		$qidName="SELECT COLUMN_NAME  
				  FROM INFORMATION_SCHEMA.COLUMNS 
				  WHERE table_name = '".$tableName."'
				  LIMIT 1";		
		$idName=mysql_fetch_row(mysql_query($qidName,$link));
		$idName=$idName[0];
		$table = new tableAdmin();
		$table->table=$tableName;
		$table->link=$link;
		$response= $table->deleteRow($idValue,$idName);
		return $response;
	}
	function formSubmit($fieldArray,$tableCounter,$tableName,$flag="")
	{
		$table= new tableAdmin();
		$table->link=dbC();
		$table->table=$tableName;		
		$response = $table->submitForm($fieldArray,$tableCounter,$flag);
		
		return $response;
	}
?>
