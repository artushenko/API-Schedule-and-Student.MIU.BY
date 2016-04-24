<?php
//Список факультетов университета в json


if ($curl = curl_init()) {
    curl_setopt($curl, CURLOPT_URL, 'http://miu.by/rus/schedule/schedule.php');
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($curl, CURLOPT_POST, true);
    curl_setopt($curl, CURLOPT_POSTFIELDS, 'kaf=' . $name_of_the_departament_cp1251);
    $out = curl_exec($curl);
    curl_close($curl);
} else {
    echo "error";
}

$html = new simple_html_dom();
$html = str_get_html($out);
$input_block = $html->find("form");

$nodes = $input_block[2]->find("option");

$facult_array = array();

foreach ($nodes as $node) {
    $val = $node->value;
    if ($val != "") {
      //  echo $val . "<br />";
        $facult_array[] = $val;
    }
}

$facult_all_array_json = array();
foreach ($facult_array as $facult) {
   $facult_all_array_json[] = array("faculty_name" => $facult, "faculty_id" => $facult,
      "date_start" => "01.09.2015", "date_end" => "30.06.2016");
//echo $facult;
}

$html->clear();
unset($html);
$facult_array_json["faculties"]=$facult_all_array_json;
echo json_encode($facult_array_json);


