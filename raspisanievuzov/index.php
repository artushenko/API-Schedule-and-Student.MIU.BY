<?php

require_once '..\\simple_html_dom.php';
/*
echo "Welcome to my raspisanievuzov API!</br>";
print_r($_GET);
echo "</br>";
echo $_GET['_url'];
echo "<br>";
echo "group_id=".$_GET['group_id'];
echo "<br>";
echo "faculty_id=".$_GET['faculty_id'];
*/

if ($_GET['group_id']!=""){
    $group_id=$_GET['group_id'];
    $raspisanievuzov=100;
    include "..\\json_parser_raspisanie_raspisaniyevuzovru.php";
}elseif ($_GET['faculty_id']){
    $get_spec_group=$_GET['faculty_id'];
    $raspisanievuzov=100;
    include "..\\json_parser_groups_special_cache_raspisaniyevuzovru.php";
}elseif ($_GET['_url']=="/get_faculties"){
    include "..\\json_parser_speciality_raspisaniyevuzovru.php";
}else{
    echo "Error! Empty request.";
}

?>
