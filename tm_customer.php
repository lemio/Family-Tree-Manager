<?php

include("connect.php");

$CONFIG['debug'] = 'on'; // on or off, if it is on all mysql errors will be reported



// the name of the mysql database table

$CONFIG['tablename'] = 'reprap_family';



// a descriptive name given to a single record table, e.g Product, Customer, News Article etc

$CONFIG['recordname'] = 'RepRap 3D printer';



// the field name of the primary key in the database table

$CONFIG['primarykey'] = 'id';



// the field name of the field which will be used to search for records

$CONFIG['searchfield'] = 'name';



$CONFIG['title'] = 'RepRap Family editor';



$CONFIG['pagesize'] = 100; // how many records per page?





$FIELDS['id']['description'] = 'id';

$FIELDS['id']['listdisplay'] = 1;

$FIELDS['id']['editdisplay'] = 0; 

$FIELDS['id']['editable'] = 0; 

$FIELDS['id']['type'] = 'input';

$FIELDS['id']['length'] = 10;

$FIELDS['id']['required'] = 0;

$FIELDS['id']['explanation'] = '';



$FIELDS['name']['description'] = 'name';

$FIELDS['name']['listdisplay'] = 1;

$FIELDS['name']['editdisplay'] = 1;

$FIELDS['name']['editable'] = 1; 

$FIELDS['name']['type'] = 'input';

$FIELDS['name']['length'] = 60;

$FIELDS['name']['required'] = 1;

$FIELDS['name']['explanation'] = '';



$FIELDS['parent_id']['description'] = 'parent';

$FIELDS['parent_id']['listdisplay'] = 1;

$FIELDS['parent_id']['editdisplay'] = 1;

$FIELDS['parent_id']['editable'] = 1; 

$FIELDS['parent_id']['type'] = 'text';

$FIELDS['parent_id']['length'] = 200;

$FIELDS['parent_id']['required'] = 0;

$FIELDS['parent_id']['explanation'] = '';

$FIELDS['parent_id']['listdisplaysize'] = '20';


$FIELDS['parent_type']['description'] = 'parent type';

$FIELDS['parent_type']['listdisplay'] = 1;

$FIELDS['parent_type']['editdisplay'] = 1;

$FIELDS['parent_type']['editable'] = 1; 

$FIELDS['parent_type']['type'] = 'arraylist';

$FIELDS['parent_type']['length'] = 200;

$FIELDS['parent_type']['required'] = 0;

$FIELDS['parent_type']['explanation'] = '';

$FIELDS['parent_type']['listdisplaysize'] = '20';
$FIELDS['parent_type']['list'] = array('2' => 'copied of it', '0' => 'derived from it', '1' => 'inspired by it');


$FIELDS['url']['description'] = 'url';

$FIELDS['url']['listdisplay'] = 1;

$FIELDS['url']['editdisplay'] = 1;

$FIELDS['url']['editable'] = 1; 

$FIELDS['url']['type'] = 'input';

$FIELDS['url']['length'] = 100;

$FIELDS['url']['required'] = 0;

$FIELDS['url']['explanation'] = '';



$FIELDS['rel_date']['description'] = 'release';

$FIELDS['rel_date']['listdisplay'] = 1;

$FIELDS['rel_date']['editdisplay'] = 1;

$FIELDS['rel_date']['editable'] = 1; 

$FIELDS['rel_date']['type'] = 'date';

$FIELDS['rel_date']['length'] = 200;

$FIELDS['rel_date']['required'] = 0;

$FIELDS['rel_date']['explanation'] = '';



$FIELDS['wip']['description'] = 'Work in progress?';

$FIELDS['wip']['listdisplay'] = 1;

$FIELDS['wip']['editdisplay'] = 1;

$FIELDS['wip']['editable'] = 1; 

$FIELDS['wip']['type'] = 'arraylist';

$FIELDS['wip']['length'] = 100;

$FIELDS['wip']['required'] = 0;

$FIELDS['wip']['explanation'] = '';

$FIELDS['wip']['list'] = array('1' => 'Yes', '0' => 'No');



$FIELDS['reprap']['description'] = 'Is RepRap';

$FIELDS['reprap']['listdisplay'] = 1;

$FIELDS['reprap']['editdisplay'] = 1;

$FIELDS['reprap']['editable'] = 1; 

$FIELDS['reprap']['type'] = 'arraylist';

$FIELDS['reprap']['length'] = 100;

$FIELDS['reprap']['required'] = 0;

$FIELDS['reprap']['explanation'] = 'A reprap is a 3D printer who can print it\'s own parts';

$FIELDS['reprap']['list'] = array('1' => 'Yes', '0' => 'No');



$FIELDS['author']['description'] = 'author';

$FIELDS['author']['listdisplay'] = 1;

$FIELDS['author']['editdisplay'] = 1;

$FIELDS['author']['editable'] = 1; 

$FIELDS['author']['type'] = 'input';

$FIELDS['author']['length'] = 200;

$FIELDS['author']['required'] = 0;

$FIELDS['author']['explanation'] = '';

$FIELDS['author']['listdisplaysize'] = '20';



$FIELDS['licence']['description'] = 'licence';

$FIELDS['licence']['listdisplay'] = 1;

$FIELDS['licence']['editdisplay'] = 1;

$FIELDS['licence']['editable'] = 1; 

$FIELDS['licence']['type'] = 'arraylist';

$FIELDS['licence']['length'] = 100;

$FIELDS['licence']['required'] = 0;

$FIELDS['licence']['explanation'] = '';

$FIELDS['licence']['list'] = array('GNU-GPL' => 'GNU-GPL', 'CC-BY-SA' => 'CC-BY-SA', 'CC-ND' => 'CC-ND' , 'CC-NC' => 'CC-NC', 'Commercial' => 'Commercial', 'other' => 'other', 'unknown' => 'unknown');



?>



<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">

<html xmlns="http://www.w3.org/1999/xhtml">

<head>

<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />

<title><?php echo $CONFIG['title']; ?></title>

</head>



<body>



<?php include('tm_files/tablemanager.php'); ?>



</body>

</html><?php
include("connect.php");
$CONFIG['debug'] = 'on'; // on or off, if it is on all mysql errors will be reported

// the name of the mysql database table
$CONFIG['tablename'] = 'reprap_family';

// a descriptive name given to a single record table, e.g Product, Customer, News Article etc
$CONFIG['recordname'] = 'RepRap 3D printer';

// the field name of the primary key in the database table
$CONFIG['primarykey'] = 'id';

// the field name of the field which will be used to search for records
$CONFIG['searchfield'] = 'name';

$CONFIG['title'] = 'RepRap Family editor';

$CONFIG['pagesize'] = 100; // how many records per page?


$FIELDS['id']['description'] = 'id';
$FIELDS['id']['listdisplay'] = 1;
$FIELDS['id']['editdisplay'] = 0; 
$FIELDS['id']['editable'] = 0; 
$FIELDS['id']['type'] = 'input';
$FIELDS['id']['length'] = 10;
$FIELDS['id']['required'] = 0;
$FIELDS['id']['explanation'] = '';

$FIELDS['name']['description'] = 'name';
$FIELDS['name']['listdisplay'] = 1;
$FIELDS['name']['editdisplay'] = 1;
$FIELDS['name']['editable'] = 1; 
$FIELDS['name']['type'] = 'input';
$FIELDS['name']['length'] = 60;
$FIELDS['name']['required'] = 1;
$FIELDS['name']['explanation'] = '';

$FIELDS['parent_id']['description'] = 'parent';
$FIELDS['parent_id']['listdisplay'] = 1;
$FIELDS['parent_id']['editdisplay'] = 1;
$FIELDS['parent_id']['editable'] = 1; 
$FIELDS['parent_id']['type'] = 'text';
$FIELDS['parent_id']['length'] = 200;
$FIELDS['parent_id']['required'] = 0;
$FIELDS['parent_id']['explanation'] = '';
$FIELDS['parent_id']['listdisplaysize'] = '20';

$FIELDS['url']['description'] = 'url';
$FIELDS['url']['listdisplay'] = 1;
$FIELDS['url']['editdisplay'] = 1;
$FIELDS['url']['editable'] = 1; 
$FIELDS['url']['type'] = 'input';
$FIELDS['url']['length'] = 100;
$FIELDS['url']['required'] = 0;
$FIELDS['url']['explanation'] = '';

$FIELDS['rel_date']['description'] = 'release';
$FIELDS['rel_date']['listdisplay'] = 1;
$FIELDS['rel_date']['editdisplay'] = 1;
$FIELDS['rel_date']['editable'] = 1; 
$FIELDS['rel_date']['type'] = 'date';
$FIELDS['rel_date']['length'] = 200;
$FIELDS['rel_date']['required'] = 0;
$FIELDS['rel_date']['explanation'] = '';

$FIELDS['wip']['description'] = 'Work in progress?';
$FIELDS['wip']['listdisplay'] = 1;
$FIELDS['wip']['editdisplay'] = 1;
$FIELDS['wip']['editable'] = 1; 
$FIELDS['wip']['type'] = 'arraylist';
$FIELDS['wip']['length'] = 100;
$FIELDS['wip']['required'] = 0;
$FIELDS['wip']['explanation'] = '';
$FIELDS['wip']['list'] = array('1' => 'Yes', '0' => 'No');

$FIELDS['reprap']['description'] = 'Is RepRap';
$FIELDS['reprap']['listdisplay'] = 1;
$FIELDS['reprap']['editdisplay'] = 1;
$FIELDS['reprap']['editable'] = 1; 
$FIELDS['reprap']['type'] = 'arraylist';
$FIELDS['reprap']['length'] = 100;
$FIELDS['reprap']['required'] = 0;
$FIELDS['reprap']['explanation'] = 'A reprap is a 3D printer who can print it\'s own parts';
$FIELDS['reprap']['list'] = array('1' => 'Yes', '0' => 'No');

$FIELDS['author']['description'] = 'author';
$FIELDS['author']['listdisplay'] = 1;
$FIELDS['author']['editdisplay'] = 1;
$FIELDS['author']['editable'] = 1; 
$FIELDS['author']['type'] = 'input';
$FIELDS['author']['length'] = 200;
$FIELDS['author']['required'] = 0;
$FIELDS['author']['explanation'] = '';
$FIELDS['author']['listdisplaysize'] = '20';

$FIELDS['licence']['description'] = 'licence';
$FIELDS['licence']['listdisplay'] = 1;
$FIELDS['licence']['editdisplay'] = 1;
$FIELDS['licence']['editable'] = 1; 
$FIELDS['licence']['type'] = 'arraylist';
$FIELDS['licence']['length'] = 100;
$FIELDS['licence']['required'] = 0;
$FIELDS['licence']['explanation'] = '';
$FIELDS['licence']['list'] = array('GNU-GPL' => 'GNU-GPL', 'CC-BY-SA' => 'CC-BY-SA', 'CC-ND' => 'CC-ND' , 'CC-NC' => 'CC-NC', 'Commercial' => 'Commercial', 'other' => 'other', 'unknown' => 'unknown');

?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title><?php echo $CONFIG['title']; ?></title>
</head>

<body>

<?php include('tm_files/tablemanager.php'); ?>

</body>
</html>
