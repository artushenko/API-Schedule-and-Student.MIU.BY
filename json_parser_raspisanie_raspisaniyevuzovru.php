<?php
//Список групп по специальности в json
function get_number_of_week($str_week)
{
    switch ($str_week) {
        case "Понедельник":
            $nweek = 1;
            break;
        case "Вторник":
            $nweek = 2;
            break;
        case "Среда":
            $nweek = 3;
            break;
        case "Четверг":
            $nweek = 4;
            break;
        case "Пятница":
            $nweek = 5;
            break;
        case "Суббота":
            $nweek = 6;
            break;
        case "Восресенье":
            $nweek = 7;
            break;
        default:
            $nweek = 0;
            break;
    }
    return $nweek;
    return $nweek;
}

function get_normal_date($bad_date)
{
    //   trim($bad_date);
    // echo $bad_date . "-baddate---<br>";
    list($empty, $day, $month, $year) = explode(" ", $bad_date, 4);
    //  echo "day=".$day1."<br>month=". $month."<br>year=". $year. "!<br>";
    // echo "DAY1=".$day1;
    //  $one_day=$day1;
    switch (trim($month)) {
        case"Января":
            $dig_month = "01";
            break;
        case"Февраля":
            $dig_month = "02";
            break;
        case"Марта":
            $dig_month = "03";
            break;
        case"Апреля":
            $dig_month = "04";
            break;
        case"Мая":
            $dig_month = "05";
            break;
        case"Июня":
            $dig_month = "06";
            break;
        case"Июля":
            $dig_month = "07";
            break;
        case"Августа":
            $dig_month = "08";
            break;
        case"Сентября":
            $dig_month = "09";
            break;
        case"Октября":
            $dig_month = "10";
            break;
        case"Ноября":
            $dig_month = "11";
            break;
        case"Декабря":
            $dig_month = "12";
            break;
        default :
            $dig_month = 0;
            break;
    }
    //  echo $normalize_date = $day . "." . $dig_month . "." . $year;
    //  echo $normalize_date = $day . "." . $dig_month . "." . $year;
    //echo "DAY2=".$one_day;
    // echo $normalize_date = $day;
    //echo $dig_month . " . " . $year;
    //return $normalize_date;
    return $day . "." . $dig_month . "." . $year;

}

function get_type_lesson($name_lesson)
{
    $array_str = str_split($name_lesson);
    $count_str = 0;
    for ($i = count($array_str); $i >= 0; $i--) {
        if ($array_str[$i] == " ") {
            $count_str = $i;
            break;
        }
    }
    $type_lesson = substr($name_lesson, $count_str, strlen($name_lesson));
    $str1 = explode("(", $type_lesson);
    return array(substr($name_lesson, 0, $count_str), trim($str1[0]), substr($str1[1], 0, strlen($str1[1]) - 1));
}

function get_audience($num_classroom)
{
    $array_str = explode(" - ", $num_classroom, 2);
    /* 1 корпус ул. Лазо, 12
     * 2 корпус ул. Лазо, 14
     * 3 корпус ул. Лазо, 16
     * 4 корпус ул. Лазо, 3
     * 6 корпус ул. Котовского, 9
     * 8 корпус ул. Котовского, 11
     * 9 корпус ул. Одесская, 14*/
    switch ($array_str[0]) {
        case "1":
            $adress_audience = "г. Минск, ул. Лазо, 12";
            break;
        case "2":
            $adress_audience = "г. Минск, ул. Лазо, 14";
            break;
        case "3":
            $adress_audience = "г. Минск, ул. Лазо, 16";
            break;
        case "4":
            $adress_audience = "г. Минск, ул. Лазо, 3";
            break;
        case "6":
            $adress_audience = "г. Минск, ул. Котовского, 9";
            break;
        case "8":
            $adress_audience = "г. Минск, ул. Котовского, 11";
            break;
        case "9":
            $adress_audience = "г . Минск, ул. Одесская, 14";
            break;
        default:
            $adress_audience = "г. Минск, ул. Лазо, 12";
            break;
    }
    //return: номер корпуса, номер аудитории, адрес аудитории.
    return array($array_str[0], $array_str[1], $adress_audience);
}

$groups_array = array();
$name_of_the_specialty = "";

//Читаем из файла номера групп и наименование факультетов.
if ($raspisanievuzov == 100) {
   // echo $group_id;
$file = fopen("..\\allgroups.html", "r");}
else{
$file = fopen("allgroups.html", "r");}
while (!feof($file)) {
    $val = fgets($file);
    list($ngroup, $name_of_the_specialty) = explode(":", $val, 2);
    if ($ngroup == $group_id) break;
}
fclose($file);

$group_id_cp1251 = iconv("UTF-8", "CP1251", $group_id);

//получаем все доступные недели
if ($curl = curl_init()) {
    curl_setopt($curl, CURLOPT_URL, 'http://miu.by/rus/schedule/schedule.php');
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($curl, CURLOPT_POST, true);
    curl_setopt($curl, CURLOPT_POSTFIELDS, 'spec=1' . '&group=' . $group_id_cp1251);
    $out = curl_exec($curl);
    curl_close($curl);
} else {
    echo "error";
}
//Получаем номера недель для группы
$out_utf8 = iconv("CP1251", "UTF-8", $out);

//echo $out_utf8;


$html = new simple_html_dom();
$html = str_get_html($out_utf8);
$input_block = $html->find("form");

$nodes = $input_block[3]->find("option");
$array_all_week = array();

foreach ($nodes as $node) {
    $val = $node->plaintext;
    if ($val != "") {
        $nom_week_line = explode(" ", $val);
        if (strlen($nom_week_line[0]) > 2) continue;
        $array_all_week_num[] = $nom_week_line[0];
    }
}
$html->clear();
unset($html);

$number_of_the_week = $array_all_week_num[count($array_all_week_num) - 1];
//echo $number_of_the_week . "<br>";
$name_of_the_speciality_cp1251 = iconv("UTF-8", "CP1251", trim($name_of_the_specialty));

if ($curl = curl_init()) {
    curl_setopt($curl, CURLOPT_URL, 'http://miu.by/rus/schedule/schedule.php');
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($curl, CURLOPT_POST, true);
    curl_setopt($curl, CURLOPT_POSTFIELDS, 'spec=' . $name_of_the_speciality_cp1251 . '&group=' . $group_id_cp1251 . '&week=' . $number_of_the_week);
    $out = curl_exec($curl);
    curl_close($curl);
} else {
    echo "error 1. Connect failed";
}
if ($out == "") {
    echo "error 2. Empty document";
    exit;
}
$html = new simple_html_dom();
$html = str_get_html($out);
//$table = $html->find('table', 9);
$table = $html->find('table', 5);

$rowData = array();
foreach ($table->find('tr') as $row) {
    $flight = array();
    foreach ($row->find('td') as $cell) {
        $flight[] = $cell->plaintext;
    }
    $rowData[] = $flight;
}

$group_name_tag = array("group_name" => $ngroup);
$lesson_array = array();
foreach ($rowData as $row => $tr) {
    if (count($tr) == 1) {
        if (count($lesson_array) == 0) {
            list ($name_day_week, $date_day) = explode(",", $tr[0]);
            $date_day = get_normal_date($date_day);
        } else {
            $day_array[] = array("weekday" => get_number_of_week($name_day_week), "lessons" => $lesson_array);
            unset($lesson_array);
            list ($name_day_week, $date_day) = explode(",", $tr[0]);
            $date_day = get_normal_date($date_day);
        }
    } else {
        list ($lesson_time_begin, $lesson_time_end) = explode(" - ", $tr[0]);
        list ($name_lesson, $type_lesson, $type_lesson, $num_subgroup) = get_type_lesson($tr[1]);
        $teacher_name = $tr[2];
        list ($num_housing, $num_audience, $adress_audience) = get_audience($tr[3]);
        $auditories = $tr[3];
        $lesson_array[] = array("subject" => $name_lesson, "type" => 1, "time_start" => $lesson_time_begin,
            "time_end" => $lesson_time_end, "parity" => null, "date_start" => null, "date_end" => null,
            "dates" => array($date_day), "teachers" => array(array("teacher_name" => $teacher_name)),
            "auditories" => array(array("auditory_name" => $auditories, "auditory_address" => $adress_audience)));
    }
}
$array_all_lessons = array("group_name" => $ngroup, "days" => $day_array);
echo json_encode($array_all_lessons);














/*

$groups_all_array_json = array();
foreach ($groups_array as $group) {
    $groups_all_array_json[] = array("group_name" => $group, "group_id" => $group,
        "date_start" => "01.09.2015", "date_end" => "30.06.2016");
}

$html->clear();
unset($html);
$groups_array_json["groups"] = $groups_all_array_json;
echo json_encode($groups_array_json);

*/
