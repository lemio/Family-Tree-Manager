<style>
*{
font-family: "Century Gothic", sans-serif;
}
text{
font-family: "Century Gothic", sans-serif;
}
</style>
<div>
<a href="tm_customer.php">Change the database</a> (This image works only under Firefox, Internet Explorer 9, Google Chrome, and Safari)
</div>
<?php
//header("Content-type: text/plain;");
$begin_time = 1133308800; //(Jear 2006)
$begin_year = 2006;
$seconds_year = 31556926; //according to Google
$end_time = time();
$style["text_color"] = "#31b1ff";
$style["node_fill_color"] = "#31b1ff";
$style["node_stroke_color"] = "#31b1ff";
$style["node_stroke_width"] = 2;
$style["node_size"] = 40;
$style["line_style"] = "semi-linear";//block,linear or semi-linear
$style["line_css"] = "stroke:#31b100;stroke-width:5";
$style["year_line_css"] = "stroke:rgb(230,230,230);stroke-width:2";
//playground_width
$width = 1000;
//image_width
$image_width = $width + 100;

$grid_size = 40;
include("connect.php");
$query = mysql_query("SELECT *, UNIX_TIMESTAMP(reprap_family.rel_date) as release_timestamp FROM reprap_family ");
$i = 0;
while($result = mysql_fetch_array($query)){
$node[$result["id"]] = $result;
$node[$result["id"]]['x'] = ($result["release_timestamp"]-$begin_time)*($width/($end_time-$begin_time));
$node_id[$i]=$result["id"];
$i ++;
}
$height = $grid_size*$i + 100;
$results = $i;

//giving Y coordinates 

for($i=0;$i<($results)+1;$i+=2){
$node[$node_id[$i]]['y'] = $height/2-$i*($grid_size/2);
}
for($i=1;$i<($results)+1;$i+=2){
$node[$node_id[$i]]['y'] = $height/2+($i+1)*($grid_size/2);
}

for($i=0;isset($node_id[$i]);$i++){
	if ($node[$node_id[$i]]["parent_id"] != 0){
		$old_y = $node[$node_id[$i]]['y'];
		$node[$node_id[$i]]['y'] = $node[$node[$node_id[$i]]["parent_id"]]['y']+1*$grid_size;
		
	}
}
/*
for($i=0;$i<($results+1);$i++){
	$node[$node_id[$i]]['y'] = $i*($grid_size);
}*/

echo "<svg xmlns=\"http://www.w3.org/2000/svg\" version=\"1.1\" style=\"height:$height; width:$image_width\">\n";

//Printing everything

//printing year lines
echo "<g>";
for($i=0;$i<=(date("Y")-$begin_year);$i++){
	//One year is 31556926 seconds
	$x =  ($i*$seconds_year)*($width/($end_time-$begin_time));
	echo "<line stroke-linecap=\"round\" x1=\"".$x."\" y1=\"".(0)."\" x2=\"".$x."\" y2=\"".$height."\" class=\"year_line\" style=\"".$style["year_line_css"]."\"/>\n";
	echo "<text x=\"".($x)."\" y=\"".($height)."\" >".($begin_year+$i)."</text>\n";
}
echo "</g>";

//Printing lines
echo "<g>";
for($i=0;isset($node_id[$i]);$i++){
	if ($node[$node_id[$i]]["parent_id"] != 0){
		print_line($node[$node[$node_id[$i]]["parent_id"]],$node[$node_id[$i]],$style);
	}
}
echo "</g>";

//Printing nodes
echo "<g>";
for($i=0;isset($node_id[$i]);$i++){
	print_node($node[$node_id[$i]],$style);

}
echo "</g>";


	



function print_node($current_node,$style){
	echo "<circle cx=\"".round($current_node['x'])."\" cy=\"".round($current_node['y'])."\" r=\"5\" stroke=\"".$style["node_stroke_color"]."\" stroke-width=\"".$style["node_stroke_width"]."\" fill=\"".$style["node_fill_color"]."\" class=\"node\"/>\n";
	echo "<a xlink:href=\"".$current_node['url']."\" target=\"_blank\"><text x=\"".round($current_node['x']+15)."\" y=\"".round($current_node['y']+15)."\" fill=\"".$style["text_color"]."\">".$current_node['name']."</text></a>\n";
}


function print_line($parent,$child,$style){
	switch ($child['parent_type']){
			case 0:
			//Derrivated from
				$extra_style = "stroke-linecap=\"round\"";
			break;
			case 1:
			//inspired by
				$extra_style = "stroke-dasharray=\"10,10\"";
			break;
			case 2:
			break;		
		}
	switch ($style["line_style"]){
		case "linear":
			echo "<line stroke-linecap=\"round\" x1=\"".$parent["x"]."\" y1=\"".$parent["y"]."\" x2=\"".$child["x"]."\" y2=\"".$child["y"]."\" style=\"".$style["line_css"]."\" />\n";
			break;
		case "block":
			echo "<line stroke-linecap=\"round\" x1=\"".$parent["x"]."\" y1=\"".$parent["y"]."\" x2=\"".$parent["x"]."\" y2=\"".$child["y"]."\" style=\"".$style["line_css"]."\" />\n";
			echo "<line stroke-linecap=\"round\" x1=\"".$parent["x"]."\" y1=\"".$child["y"]."\" x2=\"".$child["x"]."\" y2=\"".$child["y"]."\" style=\"".$style["line_css"]."\" />\n";
			break;
		case "semi-linear":
			if ($parent['y']>$child['y']){
				echo "<line stroke-dasharray=\"10,10\" stroke-linecap=\"round\" x1=\"".$parent["x"]."\" y1=\"".$parent["y"]."\" x2=\"".($parent["x"] + ($parent['y']-$child['y']))."\" y2=\"".$child["y"]."\" style=\"".$style["line_css"]."\" />\n";
				echo "<line stroke-dasharray=\"10,10\" stroke-linecap=\"round\" x1=\"".($parent["x"] + ($parent['y']-$child['y']))."\" y1=\"".$child["y"]."\" x2=\"".$child['x']."\" y2=\"".$child["y"]."\" style=\"".$style["line_css"]."\" />\n";
			
			}else{
				echo "<line $extra_style  x1=\"".$parent["x"]."\" y1=\"".$parent["y"]."\" x2=\"".($parent["x"] + ($child['y']-$parent['y']))."\" y2=\"".$child["y"]."\" style=\"".$style["line_css"]."\" />\n";
				echo "<line $extra_style  x1=\"".($parent["x"] + ($child['y']-$parent['y']))."\" y1=\"".$child["y"]."\" x2=\"".$child['x']."\" y2=\"".$child["y"]."\" style=\"".$style["line_css"]."\" />\n";
			}
			break;
}
}


/*
for(isset($node[$i]

function print_line($id_parent,$id)
function print_node()
/*
*/
?>
</svg>
(CC-BY-SA Geert Roumen (Lemio) <a href="https://github.com/lemio/Family-Tree-Manager">Github</a> inspired by Emmanuel's <a href="http://reprap.org/wiki/RepRap_Family_Tree">Family Tree</a>)