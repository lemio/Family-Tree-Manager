<link href="tm_files/styles.css" rel="stylesheet" type="text/css">
<script language="Javascript" src="tm_files/datepicker.js"></script>
<script language="Javascript">
<!--

// function: Validate
// purpose: validate form fields (does not validate data types or email syntax)
// author: Quentin O'Sullivan
// usage: onSubmit="var Fields = array('Field1','Field2'); var Descriptions = array('Descrip1','Descrip2'); return Validate(FormName, Fields, Descriptions);"
// usage: The Field and Description values have to be in the same order e.g. the first Description is for the first Field
function Validate(FormName, Fields, Descriptions) {
	// the requiredfields array contains the elements[] index number for each
	// of the required fields in the form
	var errormsg = "The following required fields were left blank:\n\n";
  	var flag = 0;

	for (i = 0; i < Fields.length; i++) {
    	var field = Fields[i];
		if (!document.forms[FormName].elements[field].value) {
			flag++;
			errormsg = errormsg + Descriptions[i] + "\n";
		}
	}
		
	if (flag > 0) {		
		alert(errormsg);
		return false;
	} else {
    	return true;
	}
}

function GoTo() {
	document.form1.keywords.value = '';
	var url = "<?php echo $_SERVER['PHP_SELF']; ?>?"
	window.open(url,'_parent');
}

function GoTo2() {
	var url = "<?php echo $_SERVER['PHP_SELF']; ?>?Search=" + document.form1.keywords.value;
	window.open(url,'_parent');
}

// -->
</script> 

<div id="datepicker" style="position:absolute; width:277px; height:271px; z-index:1; visibility: hidden;" onmouseover="javascript:dpmouseover=true;" onmouseout="javascript:dpmouseover=false;">
  <object id="fdatepicker" classid="clsid:D27CDB6E-AE6D-11cf-96B8-444553540000" codebase="http://download.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=7,0,19,0" width="230" height="230">
    <param name="movie" value="tm_files/datepicker.swf" />
    <param name="wmode" value="transparent" />
    <param name="quality" value="high" />
    <param name="swfversion" value="8.0.35.0" />
    <embed name="fdatepicker" wmode="transparent" src="tm_files/datepicker.swf" quality="high" pluginspage="http://www.macromedia.com/go/getflashplayer" type="application/x-shockwave-flash" width="230" height="230"></embed>
  </object>
</div>

<?php



if($_REQUEST['delete']) { // if the delete record button was pressed

	$_REQUEST[$CONFIG['primarykey']] = mysql_real_escape_string($_REQUEST[$CONFIG['primarykey']]);
	
	// delete any images
	foreach($FIELDS as $field => $details) {
		if($details['type'] == 'directoryimage') {
			$thumbpath = "$details[imagedirectory]/thumb-$_REQUEST[pk].jpg";
			if(file_exists($thumbpath)) unlink($thumbpath);
			foreach($details['imagesizes'] as $size) {
				$path = "$details[imagedirectory]/$size-$_REQUEST[pk].jpg";
				if(file_exists($path)) unlink($path);
			}
		}
	}
	
	// delete any associated records --------
	foreach($FIELDS as $field => $details) {
		if($details['type'] == 'manytomany') {
			if($details['optiontable'] != '') {
				// delete any existing record first
				$sql = "DELETE FROM $details[bridgetable] WHERE $details[bridgekey] = '$_REQUEST[pk]'";
				mysql_query($sql) or displayerror($sql);	
			}
		}
	}
	
	$sql = "DELETE FROM $CONFIG[tablename] WHERE $CONFIG[primarykey] = '$_REQUEST[pk]'";
	mysql_query($sql) or displayerror($sql);
	$message = 'The record has been deleted.';
	$_REQUEST['offset'] = 0;

} elseif($_REQUEST['add']) { // if the save button was pressed to add a new record

	foreach($FIELDS as $field => $details) { // change the format of date fields
		if($details['editable'] == 1 AND $details['editdisplay'] == 1 AND $details['type'] == 'date') {
			$dateparts = explode('/',$_POST[$field]);
			$_POST[$field] = $dateparts[2].'-'.$dateparts[1].'-'.$dateparts[0];
		}
	}
	
	// create the insert statement
	$sql  = "INSERT INTO $CONFIG[tablename] (";
	
	foreach($FIELDS as $field => $details) {
		if($details['editable'] == 1 AND $details['editdisplay'] == 1 AND $details['type'] != 'manytomany' AND $details['type'] != 'directoryimage') {
			//if($field != $CONFIG['primarykey']) {
				$sql .= "$field,";
			//}	
		}
	}
	$sql = substr($sql,0,strlen($sql)-1);
	$sql .= ") VALUES (";
	
	foreach($FIELDS as $field => $details) {
		if($details['editable'] == 1 AND $details['editdisplay'] == 1 AND $details['type'] != 'manytomany' AND $details['type'] != 'directoryimage') {
			$fieldvalue = mysql_real_escape_string($_POST[$field]);
			if($field != $CONFIG['primarykey']) {
				$sql .= "'$fieldvalue',";
			} else {
				if(trim($_POST[$field]) == '') {
					$sql .= "NULL,";
				} else {
					$sql .= "'$fieldvalue',";
				}
			}	
		}
	}
	$sql = substr($sql,0,-1);
	$sql .= ")";
	
	mysql_query($sql) or displayerror($sql);
	$insertid = mysql_insert_id();
	$message = 'The record has been added.';
	$_REQUEST['offset'] = 0;
	
	// insert any many association record necessary ------------------------------
	foreach($FIELDS as $field => $details) {
		if($details['type'] == 'manytomany' AND $details['editable'] == 1 AND $details['editdisplay'] == 1) {
			if($details['optiontable'] != '') {
				if(is_array($_POST[$field])) {
					foreach($_POST[$field] as $bridgekey) {
						$sql = "INSERT INTO $details[bridgetable] ($details[bridgekey],$details[bridgeoptionkey]) VALUES('$insertid','$bridgekey')";
						mysql_query($sql) or displayerror($sql);
					}
				} 
			}
		}
	}
	
	// add any images
	foreach($FIELDS as $field => $details) {
		if($details['type'] == 'directoryimage') {
			if($_FILES[$field]['name']) {// if a file was actually uploaded 
				if($_FILES[$field]['size'] > 0 && $_FILES[$field]['size'] < $details['maxuploadsize']) {
					$pathparts = pathinfo($_FILES[$field]['name']);
					$ext = strtolower($pathparts['extension']);
					if($ext == 'jpeg') { $ext = 'jpg'; }
					if($ext == 'jpg' OR $ext == 'png' OR $ext == 'gif' OR $ext == 'bmp') {
						// create the master file
						$filename = $_FILES[$field]['name'];
						$masterpath = "$details[imagedirectory]/master-$insertid";
						move_uploaded_file($_FILES[$field]['tmp_name'], $masterpath);
						// create the thumbnail	
						exec("convert -size 80x80 $masterpath -resize 80x80 +profile \"*\" $details[imagedirectory]/thumb-$insertid.jpg");
						foreach($details['imagesizes'] as $size) {
							exec("convert -size 80x80 $masterpath -resize 80x80 +profile \"*\" $details[imagedirectory]/$size-$insertid.jpg");
						}	
						unlink($masterpath);
					}
				}
			}
		}
	}
	
} elseif($_REQUEST['save']) { // if the modify button was pressed to save the form

	foreach($FIELDS as $field => $details) { // change the format of date fields
		if($details['editable'] == 1 AND $details['editdisplay'] == 1 AND $details['type'] == 'date') {
			$dateparts = explode('/',$_POST[$field]);
			$_POST[$field] = $dateparts[2].'-'.$dateparts[1].'-'.$dateparts[0];
		}
	}
	
	// create the update statement
	$sql  = "UPDATE $CONFIG[tablename] SET";
	foreach($FIELDS as $field => $details) {
		if($details['editable'] == 1 AND $details['editdisplay'] == 1 AND $details['type'] != 'manytomany' AND $details['type'] != 'directoryimage') {
			$fieldvalue = mysql_real_escape_string($_POST[$field]);
			$sql .= " $field = '$fieldvalue',";	
		}
	}
	$sql = substr($sql, 0 , -1);  // removes the last comma
	$fieldvalue = mysql_real_escape_string($_POST['pk']);
	$sql .= " WHERE $CONFIG[primarykey] = $fieldvalue";
	
	mysql_query($sql) or displayerror($sql);
	$message = 'The record has been updated.';

	// insert any many association record necessary ------------------------------
	
	foreach($FIELDS as $field => $details) {
		if($details['type'] == 'manytomany' AND $details['editable'] == 1 AND $details['editdisplay'] == 1) {
			if($details['optiontable'] != '') {
				// delete any existing record first
				$sql = "DELETE FROM $details[bridgetable] WHERE $details[bridgekey] = '$fieldvalue'";
				mysql_query($sql) or displayerror($sql);
		
				if(is_array($_POST[$field])) {
					foreach($_POST[$field] as $bridgekey) {
						$sql = "INSERT INTO $details[bridgetable] ($details[bridgekey],$details[bridgeoptionkey]) VALUES('$fieldvalue','$bridgekey')";
						mysql_query($sql) or displayerror($sql);
					} 
				}
			}
		}
	}
	
	// add any images
	foreach($FIELDS as $field => $details) {
		if($details['type'] == 'directoryimage') {
		
			// if the delete checkbox was ticked
			if($_POST[$field.'-delete'] == '1') {
				// remove any existing images if they exist
				$thumbpath = "$details[imagedirectory]/thumb-$fieldvalue.jpg";
				if(file_exists($thumbpath)) unlink($thumbpath);
				foreach($details['imagesizes'] as $size) {
					$path = "$details[imagedirectory]/$size-$fieldvalue.jpg";
					if(file_exists($path)) unlink($path);
				}
			}
			
			if($_FILES[$field]['name']) {// if a file was actually uploaded 
				if($_FILES[$field]['size'] > 0 && $_FILES[$field]['size'] < $details['maxuploadsize']) {
				
					// remove any existing images if they exist
					$thumbpath = "$details[imagedirectory]/thumb-$fieldvalue.jpg";
					if(file_exists($thumbpath)) unlink($thumbpath);
					foreach($details['imagesizes'] as $size) {
						$path = "$details[imagedirectory]/$size-$fieldvalue.jpg";
						if(file_exists($path)) unlink($path);
					}
				
					$pathparts = pathinfo($_FILES[$field]['name']);
					$ext = strtolower($pathparts['extension']);
					if($ext == 'jpeg') { $ext = 'jpg'; }
					if($ext == 'jpg' OR $ext == 'png' OR $ext == 'gif' OR $ext == 'bmp') {
						// create the master file
						$filename = $_FILES[$field]['name'];
						$masterpath = "$details[imagedirectory]/master-$fieldvalue";
						move_uploaded_file($_FILES[$field]['tmp_name'], $masterpath);
						// create the thumbnail	
						exec("convert -size 80x80 $masterpath -resize 80x80 +profile \"*\" $details[imagedirectory]/thumb-$fieldvalue.jpg");
						foreach($details['imagesizes'] as $size) {
							exec("convert -size $size $masterpath -resize $size +profile \"*\" $details[imagedirectory]/$size-$fieldvalue.jpg");
						}	
						unlink($masterpath);
					}
				}
			}
		}
	}	
	
}

if($_REQUEST['addpage'] == '' AND $_REQUEST['modifypage'] == '' AND $_REQUEST['query']== '') {
	buildmenu($message);
} elseif($_REQUEST['addpage'] OR $_REQUEST['modifypage']) {
	buildrecordedit();
} elseif($_REQUEST['query']) {
	buildquery();
} else {
	buildmenu($message);
}


// this function builds a select query to execute any select query ------------------------------------------------------------
function buildquery() {

	global $CONFIG;

	$sql = mysql_real_escape_string($_REQUEST['querysql']);
	$data = mysql_query($sql) or displayerror($sql);
	?>	
    	<div><a class="tm" href="<?php echo $_SERVER['PHP_SELF']."?offset=$_REQUEST[offset]&keywords=$_REQUEST[keywords]"; ?>">&lt;&lt;BACK</a></div> 
	<?php
	
	while($myrow = mysql_fetch_row($data)) {
		foreach($myrow as $value) {
			$content .= "$value, ";
		}
		$content .= "<br />";
	}
	echo $content;
}


// builds the main screen ---------------------------------------------------------------------------------------
function buildmenu($message)
{
	global $CONFIG;

	list($listingscount,$recorddisplay,$nextprevious) = searchresults();

?>
	<table>
    	<tr> 
        	<td>
				<form name="form1" method="post" action="<?php echo $_SERVER['PHP_SELF'] ?>">
               		<div class="tmheading"><?php echo $CONFIG['title'] ?></div>
                  	<div class="tmmessage"><?php echo $message ?></div>
                	<input type="text" name="keywords" value="<?php echo $_REQUEST['keywords']; ?>" />
					<input type="submit" name="Submit" value="Search" onClick="GoTo2()" />&nbsp;&nbsp;&nbsp;
					<input type="submit" name="Submit" value="Show All" onClick="GoTo()" />&nbsp;&nbsp;&nbsp;
					<input type="submit" name="addpage" value="Add a New <?php echo $CONFIG['recordname'] ?>" /><br /><br />
					<span class="tmtext"><?php echo $listingscount; ?></span>
                  	<table class="tmlisttable" border="1">
                    	<?php echo $recorddisplay; ?> 
                  	</table>
            	</form>
			</td>
		</tr>
		<tr>
			<td class="tmnav"><?php echo $nextprevious; ?></td>
		</tr>
	</table>
    <?php
}


// builds the javascript validation code --------------------------------------------------------------------------------------------
function buildhandler() {

	global $CONFIG, $FIELDS;
		
	// build a comma seperated field list
	foreach($FIELDS as $field => $details) {
		if($details['required'] AND $details['editdisplay'] == 1 AND $details['editable'] == 1) {
			$fieldlist .= "'$field',";
			$descriplist .= "'$details[description]',";
		}
	}
	$fieldlist = substr($fieldlist, 0 , -1);  // removes the last comma
	$descriplist = substr($descriplist, 0 , -1);  // removes the last comma
	
	// build the handler string
	$handler = "var Fields = new Array(";
	$handler .= $fieldlist;
	$handler .= '); var Descriptions = new Array(';
	$handler .= $descriplist;
	$handler .= '); return Validate(\'form1\', Fields, Descriptions);';
	return $handler;
}


// builds a form allowing someone to add or edit a record --------------------------------------------------------------------------------------------
function buildrecordedit() {
	global $CONFIG, $FIELDS;

	if($_REQUEST['pk']) {
		$sql = "SELECT * FROM $CONFIG[tablename] WHERE $CONFIG[primarykey] = '$_REQUEST[pk]'";
		$data = mysql_query($sql) or displayerror($sql);
		$myrow = mysql_fetch_array($data);
		$pageheading = "Edit a $CONFIG[recordname]";
	} else {
		$pageheading = "Add a new $CONFIG[recordname]";
	}
	?>
 
    	<div class="tmheading"><?php echo $pageheading ?></div> 
        <div class="tmtext"><a class="tm" href="<?php echo $_SERVER['PHP_SELF']."?offset=$_REQUEST[offset]&keywords=$_REQUEST[keywords]"; ?>">&lt;&lt; BACK</a></div>
        <div class="tmtext" style="margin-top: 7px;">Fields marked with a red <span style="color:#FF0000">*</span> are required fields</div>
	
    	<table>
        	<tr> 
          		<td>
					<form name="form1" enctype="multipart/form-data" method="post" action="<?php echo $_SERVER['PHP_SELF'] ?>" onSubmit="<?php echo buildhandler(); ?>">
              		<table class="tmedittable" border="1">
                		<?php
		
						// output the fields
						foreach($FIELDS as $field => $details) {
							$myrow[$field] = htmlspecialchars($myrow[$field]);
							$textview = 0;
							if($details['editdisplay'] == 0) {
								$details['type'] = 'hidden';
							} else if($details['editable'] == 0) {
								$textview = 1;
							}
						
							if($details['required']) {
								$redasterisk = ' <span style="color:#FF0000">*</span>';
							} else {
								$redasterisk = '';
							}

							if($details['type'] != 'hidden') {
							?>
                			<tr> 
                  				<td class="tmeditlabelcol" align="right" valign="top"><?php echo $details['description'] ?>:</td>
                  				<td class="tmeditinputcol">
                    		<?php
							}
			
								if($details['type'] == 'hidden') {
									
									$formelement = '';
								
								} elseif($details['type'] == 'input') { // if the form element type is text input

									if($textview == 1) {
										$formelement = $myrow[$field];
									} else {
										if($details['length'] > 40) {
											$fieldsize = 40;
										} else {
											$fieldsize = $details['length'];
										}
										$formelement = "<input type=\"text\" name=\"$field\" value=\"$myrow[$field]\" size=\"$fieldsize\" maxlength=\"$details[length]\" />$redasterisk $details[explanation]";
									}
								
								} elseif($details['type'] == 'text') { // create a textarea
									
									if($textview == 1) {
										$formelement = $myrow[$field];
									} else {
										$formelement = "<textarea name=\"$field\" cols=\"30\" rows=\"5\">$myrow[$field]</textarea>$redasterisk<br />$details[explanation]";	
									}
									
								} elseif($details['type'] == 'arraylist') { // create a select list 
								
									if($textview == 1) {
										foreach($details['list'] as $value => $option) {
											if($value == $myrow[$field]) $formelement = $option;
										}
									} else {
										$formelement = "<select name=\"$field\">";
										foreach($details['list'] as $value => $option) {
											if($value == $myrow[$field]) $selected = ' selected'; else $selected = '';
											$formelement .= "<option value=\"$value\"$selected>$option</option>\n";
										}
										$formelement .= "</select>$redasterisk $details[explanation]";
									}
							
								} elseif($details['type'] == 'tablelist') { // create a select list 
								
									if($textview == 1) {
										$sql = "SELECT $details[listdescfield] FROM $details[listtable] WHERE $details[listkeyfield] = '$myrow[$field]'";
										$rs = mysql_query($sql) or displayerror($sql);
										list($option) = mysql_fetch_row($rs);
										$formelement = $option;
									} else {
										$formelement = "<select name=\"$field\">";
										
										$sql = "SELECT $details[listkeyfield], $details[listdescfield] FROM $details[listtable] ORDER BY $details[listdescfield]";
										$rs = mysql_query($sql) or displayerror($sql);
										while(list($value,$option) = mysql_fetch_row($rs)) {
											if($value == $myrow[$field]) $selected = ' selected'; else $selected = '';
											$formelement .= "<option value=\"$value\"$selected>$option</option>\n";
										}
										$formelement .= "</select>$redasterisk $details[explanation]";
									}
							
								}elseif($details['type'] == 'tablelist') { // create a select list 
								
									if($textview == 1) {
										$sql = "SELECT $details[listdescfield] FROM $details[listtable] WHERE $details[listkeyfield] = '$myrow[$field]'";
										$rs = mysql_query($sql) or displayerror($sql);
										list($option) = mysql_fetch_row($rs);
										$formelement = $option;
									} else {
										$formelement = "<select name=\"$field\">";
										
										$sql = "SELECT $details[listkeyfield], $details[listdescfield] FROM $details[listtable] ORDER BY $details[listdescfield]";
										$rs = mysql_query($sql) or displayerror($sql);
										while(list($value,$option) = mysql_fetch_row($rs)) {
											if($value == $myrow[$field]) $selected = ' selected'; else $selected = '';
											$formelement .= "<option value=\"$value\"$selected>$option</option>\n";
										}
										$formelement .= "</select>$redasterisk $details[explanation]";
									}
							
								} elseif($details['type'] == 'tree') { // create a select list 
								
									if($textview == 1) {
										$formelement = buildtreepath($myrow[$field], $details);
									} else {
										$formelement = "<select name=\"$field\">";
										$formelement .= buildtree($details['idofroot'],'','',$details,$myrow[$field]);
										$formelement .= "</select>$redasterisk $details[explanation]";
									}
							
								} elseif($details['type'] == 'directoryimage') { // create a select list 
									$formelement = '';
									$thumbpath = "$details[imagedirectory]/thumb-$_REQUEST[pk].jpg";
									if($_REQUEST['pk'] AND file_exists($thumbpath)) {
										$formelement = "<img src=\"$thumbpath\" width=\"80\" height=\"80\" /><br /><br />";
									}
									if($textview == 1) {
										//
									} else {
										$formelement .= "<input type=\"file\" name=\"$field\"><br />";
										if(file_exists($thumbpath)) {
											$formelement .= "<input type=\"checkbox\" name=\"$field-delete\" value=\"1\"> Delete existing image";
										}
									}
							
								} elseif($details['type'] == 'date') { // create a select list 
								
									if($textview == 1) {
										if($myrow[$field] == '0000-00-00 00:00:00' OR $myrow[$field] == '') $myrow[$field] = ''; else $myrow[$field] = date('jS M Y, g:i a',strtotime($myrow[$field]));
										$formelement = "$myrow[$field] $details[explanation]";
									} else {								
										
										if($myrow[$field] == '0000-00-00 00:00:00' OR $myrow[$field] == '') {
											$myrow[$field] = '';
										} else {
											$dateparts = explode(' ',$myrow[$field]);
											$dateparts = explode('-',$dateparts[0]);
											$myrow[$field] = $dateparts[2].'/'.$dateparts[1].'/'.$dateparts[0];
										}
										
										$formelement = "
										<div>
										<input name='$field' type='input' value='$myrow[$field]' readonly='readonly' />
										<a href='#'><img id='dpicon' src='tm_files/datepicker.gif' alt='date picker' width='17' height='20' border='0' /></a>$redasterisk $details[explanation]
										</div>";
										
									}
								
								} elseif($details['type'] == 'manytomany') {
									
									if($details['optiontable'] != '') {
										$sql = "
										SELECT 
											$details[optiontable].$details[optionkeyfield], $details[optiontable].$details[optiondescfield], $details[bridgetable].$details[bridgekey]
										FROM 
											$details[optiontable]
										LEFT JOIN $details[bridgetable] ON $details[bridgetable].$details[bridgeoptionkey] = $details[optiontable].$details[optionkeyfield] AND $details[bridgetable].$details[bridgekey] = '$_REQUEST[pk]'	
										";
										$rs = mysql_query($sql) or displayerror($sql);
										
										$options1 = '';
										$options2 = '';
										$options3 = '';
										$desc = '';
										$count = 1;
										$num = mysql_num_rows($rs);
										$each = intval($num/$details['columns']);
										while($optiondata = mysql_fetch_row($rs)) {

											if($optiondata[2] != '') $checked = ' checked'; else $checked = '';
											$disabled = '';
											if($textview == 1) $disabled = 'disabled';
												
											if($count <= $each) {
												$options1 .= "<input type='checkbox' name='".$field."[]' value='$optiondata[0]'$checked $disabled/> $optiondata[1]<br />";
											} elseif($count <= ($each*2)) {
												$options2 .= "<input type='checkbox' name='".$field."[]' value='$optiondata[0]'$checked $disabled/> $optiondata[1]<br />";
											} elseif($count <= ($each*3)) {
												$options3 .= "<input type='checkbox' name='".$field."[]' value='$optiondata[0]'$checked $disabled/> $optiondata[1]<br />";
											} else {
												$options1 .= "<input type='checkbox' name='".$field."[]' value='$optiondata[0]'$checked $disabled/> $optiondata[1]<br />";
											}
																						
											$count++;
										}
										$formelement = "<table cellpadding=\"0\" cellspacing=\"0\" border=\"0\" width=\"100%\">
											<tr class=\"tmtext\"> 
												<td valign=\"top\" nowrap>$options1</td>
												<td valign=\"top\" nowrap>$options2</td>
												<td valign=\"top\" nowrap>$options3</td>
											</tr>
										</table>";
									}
								}
								echo $formelement;
								if($details['type'] != 'hidden') {
								?>
								</td>
                				</tr>
                				<?php
								}
					}
				?>
			</table>
		
		<br />
		<center>
			<?php
				if($_REQUEST['pk']) {
            ?><input type="submit" name="save" value="Save" />
			<input type="hidden" name="pk" value="<?php echo $_REQUEST['pk'] ?>" />
			<?php
				} else {
				?><input type="submit" name="add" value="Save" /><?php
				}
				?>
			<input name="offset" type="hidden" value="<?php echo $_REQUEST['offset']; ?>" />
			<input name="keywords" type="hidden" value="<?php echo $_REQUEST['keywords']; ?>" />
		</center>
		</form>
		</td>

        </tr>
	</table>
	<?php
}


// does a record search and created next previous steps etc -------------------------------------------------------------------------------------
function searchresults() {
	
	global $CONFIG, $FIELDS;

	$offset = $_REQUEST['offset'];
	$keywords = $_REQUEST['keywords'];
	if(!$offset) { $offset = 0; }

	foreach($FIELDS as $field => $details) {
		if($details['listdisplay'] == 1 AND $details['type'] != 'manytomany') {
			$displaycolumns .= ",$field";
		}
	}
	$displaycolumns = substr($displaycolumns,1);
	if($keywords) {
		$keywords = mysql_real_escape_string($keywords);
		$sql = "SELECT $CONFIG[primarykey], $displaycolumns FROM $CONFIG[tablename] WHERE $CONFIG[searchfield] LIKE '%$keywords%' ORDER BY $displaycolumns";
	} else {
		$sql = "SELECT $CONFIG[primarykey], $displaycolumns FROM $CONFIG[tablename] ORDER BY $displaycolumns";
	}

	$data = mysql_query($sql) or displayerror($sql);

	$num = mysql_num_rows($data);
	if($num != 0) {
		mysql_data_seek ($data, $offset);

		foreach($FIELDS as $field => $details) {
			if($details['listdisplay'] == 1) {
				$text .= "<td class=\"tmlisttablehead\">$details[description]</td>";
			}
		}
		$recorddisplay = "<tr>$text<td class=\"tmlisttablehead\"></td><td class=\"tmlisttablehead\"></td></tr>";
		
		
		$n = 0;
		$myrow = mysql_fetch_array($data);
		while($myrow[0] != '' && $n < $CONFIG['pagesize']) {
			
			$text = '';
			foreach($FIELDS as $field => $details) {
				if($details['listdisplay'] == 1) {
					if($details['type'] == 'date') {
						if(strpos($myrow[$field],'0000-00-00') !== false) {
							$myrow[$field] = '';
						} else {
							$myrow[$field] = date('jS M Y, g:i a',strtotime($myrow[$field]));
						}
					} else if($details['type'] == 'arraylist') {
						$myrow[$field] = $details['list'][$myrow[$field]];
					} else if($details['type'] == 'tablelist') {
						$sql = "SELECT $details[listdescfield] FROM $details[listtable] WHERE $details[listkeyfield] = '$myrow[$field]'";
						$listrs = mysql_query($sql);
						list($listvalue) = mysql_fetch_row($listrs);
						$myrow[$field] = $listvalue;
					} else if($details['type'] == 'tree') {
						$myrow[$field] = buildtreepath($myrow[$field], $details);
					}
					if($details['listdisplaysize'] > 0) {
						$myrow[$field] = substr($myrow[$field],0,$details['listdisplaysize']);
					}
					$myrow[$field]=nl2br($myrow[$field]);
					$text .= "<td class=\"tmlisttablerow\">$myrow[$field]</td>";
				}
			}	
			
			$recorddisplay .= "<tr>$text<td class=\"tmlisttablerow\"><a class=\"tm\" href='".$_SERVER['PHP_SELF']."?modifypage=1&pk=$myrow[0]'>Edit</a></td><td class=\"tmlisttablerow\"><a class=\"tm\" href='".$_SERVER['PHP_SELF']."?delete=1&pk=$myrow[0]' onClick=\"return confirm('Are you sure you would like to delete the selected $CONFIG[recordname] ?');\">Delete</a></td></tr>";
		
			$myrow = mysql_fetch_array($data);	
			$n++;
		}

		if($CONFIG['pagesize'] < $num) {
			
			$start = $offset+1;
			$finish = $start+$CONFIG['pagesize']-1;
			if($finish > $num) { $finish = $num; }
			$listingscount = "Displaying records <b>$start</b> to <b>$finish</b> of $num";

			// create links for stepping through the pages
			if($CONFIG['pagesize'] < $num) { 

				$nextprevious = "<div>";
				$currentoffset = $offset;
				$pages = 20;
				
				if($offset == 0) {
					$startdiget = 1;
				} else {
					$startdiget = floor($offset/($CONFIG['pagesize']*$pages));
					$startdiget = $startdiget*$pages+1;
				}
				 
				$diget = $startdiget;
				$offset = ($startdiget-1)*$CONFIG['pagesize'];
				 // set to the max number of digets to display
				$maxdigits = ceil($num/$CONFIG['pagesize']); // the number of digits to be displayed

				// create the PREVIOUS link
				if($currentoffset == 0) {
					$nextprevious .= "PREVIOUS &lt;&lt; ";
				} else {
					$previousoffset = $currentoffset-$CONFIG['pagesize'];
					$nextprevious .= "<a href=\"$_SERVER[PHP_SELF]?keywords=$keywords&offset=$previousoffset\">PREVIOUS</a> &lt;&lt; ";
				}
				
				// create the digits
				while($num && ($diget < ($startdiget+$pages)) && $diget <= $maxdigits) { // while there are still digits to create
	
					// create a digit				
					if($currentoffset == $offset) {
						$nextprevious .= "$diget ";
					} else {
						$nextprevious .= "<a href=\"$_SERVER[PHP_SELF]?keywords=$keywords&offset=$offset\">$diget</a> ";
					}		
	
					$num = $num - $CONFIG['pagesize'];	
					if($num < 0) { $num = 0; }
					$diget++;
					$offset = $offset + $CONFIG['pagesize'];
				} 

				// add the NEXT link
				$currentoffset += $CONFIG['pagesize'];
				if($currentoffset == ($maxdigits*$CONFIG['pagesize'])) {
					$nextprevious .= "&gt;&gt; NEXT";
				} else {
					$nextprevious .= "&gt;&gt; <a href=\"$_SERVER[PHP_SELF]?keywords=$keywords&offset=$currentoffset\">NEXT</a>";
				}
	
				$nextprevious .= "</div>";
			}			
		}
	} else {
		$recorddisplay = "<div>Sorry, no records were found.</div>";	
	}
	$result = array($listingscount,$recorddisplay,$nextprevious);
	return $result;
}

function displayerror($sql) {
	global $CONFIG;
	if($CONFIG['debug'] == 'on') {
		echo mysql_error().'<br /><br />'.$sql;
		exit;
	}
}


// builds a tree structure in a select field 
function buildtree($id, $string, $content, $details, $selected) {

	// add the page title to the string
	if($id) {
		$data = mysql_query("SELECT $details[namefield], $details[parentidfield] FROM $details[treetable] WHERE $details[idfield] = '$id'");
		list($name, $parent) = mysql_fetch_row($data);
	}
	$string .= "--> $name";
	
	// output the string
	if($id == $details[idofroot]) { $string = substr($string, 4); }// remove the first -->
	if($id == $selected) $isselected = 'selected'; else $isselected = '';
	$content .= "<option value=\"$id\" $isselected>$string</option>";
	
	// get all the child pages
	$data = mysql_query("SELECT $details[idfield] FROM $details[treetable] WHERE $details[parentidfield] = '$id'");
	while($child = mysql_fetch_row($data)) { $children[] = $child[0]; }
	
	// for each child call the function again
	if($children[0]) {
		foreach ($children as $child) { $content = buildtree($child, $string, $content, $details, $selected); }
	}
	
	return $content;
}

// build a tree path
function buildtreepath($id, $details) {
	while(list($name, $parent_id) = gettreedata($id, $details)) {
		$path = "$name--> $path";
		$id = $parent_id;
	}
	$path = substr($path, 0, -4);  // remove the last -->
	return $path;
}

function gettreedata($id, $details) {
	$rs = mysql_query("SELECT $details[namefield], $details[parentidfield] FROM $details[treetable] WHERE $details[idfield] = '$id'");
	return mysql_fetch_row($rs);
}
?>
