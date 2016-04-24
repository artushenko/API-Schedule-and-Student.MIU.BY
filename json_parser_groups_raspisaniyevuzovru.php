<?php
//Список групп по специальности в json
$groups_array = array();

$array_specialities = array();
// $i1 = 0;
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
    if ($val != "") {
        $array_specialities[] = $val;
    }
}
$html->clear();
unset($html);

$array_groups = array();

foreach ($array_specialities as $name_of_the_specialty) {
    $name_of_the_specialty_cp1251 = iconv("UTF-8", "CP1251", $name_of_the_specialty);
    if ($curl = curl_init()) {
        curl_setopt($curl, CURLOPT_URL, 'http://miu.by/rus/schedule/schedule.php');
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_POSTFIELDS, 'spec=' . $name_of_the_specialty_cp1251);
        $out = curl_exec($curl);
        curl_close($curl);
    } else {
        echo "error";
    }
    $html = new simple_html_dom();
    $html = str_get_html($out);
    $input_block = $html->find("form");

    $nodes = $input_block[2]->find("option");
    foreach ($nodes as $node) {
        $val = $node->value;
        if ($val != "") {
            $array_groups[] = $val;
  //          echo $val . "<br />";
        }
    }
    $html->clear();
    unset($html);
}

sort($array_groups, 2);
$result_array_groups = array_unique($array_groups);

/*
foreach ($result_array_groups as $all_groups) {
    echo $all_groups . "<br />";
}
*/

$groups_all_array_json = array();
foreach ($result_array_groups as $group) {
    $groups_all_array_json[] = array("group_name" => $group, "group_id" => $group,
        "date_start" => "01.09.2015", "date_end" => "30.06.2016");
}

$groups_array_json["groups"] = $groups_all_array_json;
echo json_encode($groups_array_json);


