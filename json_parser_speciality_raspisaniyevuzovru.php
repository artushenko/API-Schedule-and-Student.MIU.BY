<?php
//Список специальностей университета в json
$facult_array = array();

if ($curl = curl_init()) {
    curl_setopt($curl, CURLOPT_URL, 'http://miu.by/rus/schedule/schedule.php');
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($curl, CURLOPT_POST, true);
    $out = curl_exec($curl);
    curl_close($curl);
} else {
    echo "error";
}

$html = new simple_html_dom();
$html = str_get_html($out);
$input_block = $html->find("form");

$nodes = $input_block[1]->find("option");
foreach ($nodes as $node) {
    $val = $node->value;
    if ($val != "") $facult_array[] = $val;
}

$facult_all_array_json = array();
foreach ($facult_array as $facult) {
    $facult_all_array_json[] = array("faculty_name" => $facult, "faculty_id" => $facult,
        "date_start" => "01.09.2015", "date_end" => "30.06.2016");
}

$html->clear();
unset($html);
$facult_array_json["faculties"] = $facult_all_array_json;
echo json_encode($facult_array_json);


