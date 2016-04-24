<?php
//Список всех групп  в json
$file = fopen("allgroups.html", "r");
while (!feof($file)) {
    $val = fgets($file);
    list($ngroup, $name_of_the_specialty) = explode(":", $val, 2);
    if ($ngroup != "") $array_groups[] = $ngroup;
}
fclose($file);
sort($array_groups, 2);
$result_array_groups = array_unique($array_groups);
//$groups_all_array_json = array();
foreach ($result_array_groups as $group) {
    $groups_all_array_json[] = array("group_name" => $group, "group_id" => $group,
        "date_start" => "01.09.2015", "date_end" => "30.06.2016");
}
$groups_array_json["groups"] = $groups_all_array_json;
echo json_encode($groups_array_json);