<?php
/**
 * API (parser) http://miu.by/rus/schedule/schedule.php
 * version 1.802
 * Created by PhpStorm.
 * User: Mihail
 * Date:24.04.2016
 * Time: 12:00
 */

error_reporting(E_ALL ^ E_NOTICE);

function get_data($str_request)
{
    $out = "";
    if ($curl = curl_init()) {
        curl_setopt($curl, CURLOPT_URL, 'http://miu.by/rus/schedule/schedule.php');
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $str_request);
        $out = curl_exec($curl);
        curl_close($curl);
    } else {
        echo "error 1. Connect failed";
    }
    return $out;
}

require_once 'simple_html_dom.php';
/*
echo "<head>
    <meta charset=\"UTF-8\"/>
    <meta name=\"viewport\" content=\"width=device-width, initial-scale=1, maximum-scale=1\"/>
    <title>Главная</title>
</head>";
*/
$name_of_the_departament = $_GET['kafedra'];
$list_name_of_the_departament = $_GET['list_kafedra'];
$name_of_the_teacher = $_GET['teacher'];
$number_of_the_week = $_GET['week'];
$format_of_the_week = $_GET['weekf'];
$name_of_the_specialty = $_GET['specialty'];
$number_of_the_group = $_GET['group'];
$number_of_the_week_now = $_GET['weekn'];
$list_all_teachers = $_GET['allteachers'];
$list_all_groups = $_GET['groups'];
$list_all_groups_and_special = $_GET['groupsspec'];
$list_specialities = $_GET['specialities'];
$json_format = $_GET['json'];
$get_faculties = $_GET['get_faculties'];
$get_spec = $_GET['get_spec'];
$get_groups = $_GET['get_groups'];
$get_schedule = $_GET['get_schedule'];
$group_id = $_GET['group_id'];
$get_spec_group = $_GET['get_spec_group'];
$get_news = $_GET['get_news'];

$name_of_the_departament_cp1251 = iconv("UTF-8", "CP1251", $name_of_the_departament);
$name_of_the_teacher_cp1251 = iconv("UTF-8", "CP1251", $name_of_the_teacher);
$name_of_the_speciality_cp1251 = iconv("UTF-8", "CP1251", $name_of_the_specialty);
$number_of_the_group_cp1251 = iconv("UTF-8", "CP1251", $number_of_the_group);
$group_id_cp1251 = iconv("UTF-8", "CP1251", $group_id);

$out = "";

if ($name_of_the_departament != "" and $name_of_the_teacher == "" and $number_of_the_week == "") {
//Список преподавателей на кафедре
//name_of_the_departament - кафедра
    $out = get_data('kaf=' . $name_of_the_departament_cp1251);
    if ($out == "") {
        echo "error 2. Empty document";
        exit;
    }
    $html = new simple_html_dom();
    $html = str_get_html($out);
    $input_block = $html->find("form");
    $nodes = $input_block[2]->find("option");

    if (count($nodes) <= 1) {
        echo "error 3. Empty document";
        exit;
    }

    foreach ($nodes as $node) {
        $val = $node->value;
        if ($val != "") echo $val . "<br />";
    }
    $html->clear();
    unset($html);
} elseif ($name_of_the_departament != "" and $name_of_the_teacher != "" and $number_of_the_week == "") {
    //Учебные недели преподателя:
    //name_of_the_departament - кафедра,
    //name_of_the_teacher - ФамилияИО
    $out = get_data('kaf=' . $name_of_the_departament_cp1251 . '&fio=' . $name_of_the_teacher_cp1251);
    if ($out == "") {
        echo "error 2. Empty document";
        exit;
    }
    $html = new simple_html_dom();
    $html = str_get_html(iconv("CP1251", "UTF-8", $out));
    $input_block = $html->find("form");

    $nodes = $input_block[3]->find("option");
    foreach ($nodes as $node) {
        $val = $node->plaintext;
        if ($val != "") echo $val . "<br>";
    }
    $html->clear();
    unset($html);
} elseif ($name_of_the_departament != "" and $name_of_the_teacher != "" and $number_of_the_week != "") {
//Расписание преподавателя
//name_of_the_departament - кафедра
//name_of_the_teacher - ФамилияИО
//number_of_the_week - номер учебной недели

    $out = get_data('kaf=' . $name_of_the_departament_cp1251 . '&fio=' . $name_of_the_teacher_cp1251 . '&week=' . $number_of_the_week);
    if ($out == "") {
        echo "error 2. Empty document";
        exit;
    }
    $html = new simple_html_dom();
    $html = str_get_html($out);
   // $table = $html->find('table', 9);
    $table = $html->find('table', 5);

    if ($table->plaintext == " ") {
        echo "error 2. Empty document";
        exit;
    }

    //если параметр weekf=0 или отсутствует, то обычный вывод
    //если параметр weekf=1 упрошеный вывод
    if ($format_of_the_week == 0) {
        echo $table;
        $rowData = array();
    } else {
        foreach ($table->find('tr') as $row) {
            $flight = array();
            foreach ($row->find('td') as $cell) {
                $flight[] = $cell->plaintext;
            }
            $rowData[] = $flight;
        }
        echo '<table border=\"1\">';
        foreach ($rowData as $row => $tr) {
            echo '<tr>';
            foreach ($tr as $td)
                echo '<td>' . $td . '</td>';
            echo '</tr>';
        }
        echo '</table>';
    }
} elseif ($name_of_the_specialty != "" and $number_of_the_group == "" and $get_groups != 1) {
    //Получаем номера групп по данной специальности
    if ($curl = curl_init()) {
        curl_setopt($curl, CURLOPT_URL, 'http://miu.by/rus/schedule/schedule.php');
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_POSTFIELDS, 'spec=' . $name_of_the_speciality_cp1251);
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
        if ($val != "") echo $val . "<br />";
    }

    $html->clear();
    unset($html);

} elseif ($name_of_the_specialty != "" and $number_of_the_group != "" and $number_of_the_week == "") {
    //  echo $number_of_the_group;
//Учебные недели студента : Кафедра-группа
    if ($curl = curl_init()) {
        curl_setopt($curl, CURLOPT_URL, 'http://miu.by/rus/schedule/schedule.php');
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_POSTFIELDS, 'spec=' . $name_of_the_speciality_cp1251 . '&group=' . $number_of_the_group_cp1251);
        $out = curl_exec($curl);
        curl_close($curl);
    } else {
        echo "error";
    }
    //Получаем номера недель для группы
    $out_utf8 = iconv("CP1251", "UTF-8", $out);

    $html = new simple_html_dom();
    $html = str_get_html($out_utf8);
    $input_block = $html->find("form");

    $nodes = $input_block[3]->find("option");
    foreach ($nodes as $node) {
        $val = $node->plaintext;
        if ($val != "") echo $val . "<br>";
    }
    $html->clear();
    unset($html);
} elseif ($number_of_the_group != "" and $name_of_the_specialty == "" and $number_of_the_week == "") {
//Учебные недели студента : группа
    if ($curl = curl_init()) {
        curl_setopt($curl, CURLOPT_URL, 'http://miu.by/rus/schedule/schedule.php');
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_POSTFIELDS, 'spec=1' . '&group=' . $number_of_the_group_cp1251);
        $out = curl_exec($curl);
        curl_close($curl);
    } else {
        echo "error";
    }
    //Получаем номера недель для группы
    $out_utf8 = iconv("CP1251", "UTF-8", $out);

    $html = new simple_html_dom();
    $html = str_get_html($out_utf8);
    $input_block = $html->find("form");

    $nodes = $input_block[3]->find("option");
    $array_all_week = array();
    $i = 0;

    foreach ($nodes as $node) {
        $val = $node->plaintext;
        if ($val != "") {
            if ($json_format != "1") echo $val . "<br>";
            $array_all_week[$i] = $val;
            $i++;
        }
    }
    //полученные данные помещаем в массив $array_all_week_json, вырезая номера недели в каждой строке
    if ($json_format == "1") {
        $array_all_week_num = array();
     //   $a = 0;
        for ($j = 1; $j < $i; $j++) {
            $nom_week_line = explode(" ", $array_all_week[$j]);
            $array_all_week_num[] = $nom_week_line[0];
          //  $a++;
        }
        $array_all_week_json = array();
        $array_all_week_json['weeks'] = $array_all_week_num;
        echo json_encode($array_all_week_json);
    }

    $html->clear();
    unset($html);
} elseif ($name_of_the_specialty != "" and $number_of_the_group != "" and $number_of_the_week != "") {
//Расписание студента (упрощенное): Кафедра-группа-номер_учебной_недели-формат упрощенный(1)
    if ($curl = curl_init()) {
        curl_setopt($curl, CURLOPT_URL, 'http://miu.by/rus/schedule/schedule.php');
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_POSTFIELDS, 'spec=' . $name_of_the_speciality_cp1251 . '&group=' . $number_of_the_group_cp1251 . '&week=' . $number_of_the_week);
        $out = curl_exec($curl);
        curl_close($curl);
    } else {
        echo "error";
    }
    if ($out == "") {
        echo "error";
        exit;
    }

   // echo $out;


    $flag_error = 0;
    $html = new simple_html_dom();
    $html = str_get_html($out);
 //   $table = $html->find('table', 9);
    $table = $html->find('table', 5);



    if ($format_of_the_week == 0) {
        echo $table;
    } else {
        if ($json_format != "1") {
            $rowData = array();
            foreach ($table->find('tr') as $row) {
                $flight = array();
                foreach ($row->find('td') as $cell) {
                    $flight[] = $cell->plaintext;
                }
                $rowData[] = $flight;
            }

            echo '<table border=\"1\">';
            foreach ($rowData as $row => $tr) {
                echo '<tr>';
                foreach ($tr as $td)
                    echo '<td>' . $td . '</td>';
                echo '</tr>';
            }
            echo '</table>';
        } else {


            $rowData = array();
            foreach ($table->find('tr') as $row) {
                $flight = array();
                foreach ($row->find('td') as $cell) {
                    $flight[] = $cell->plaintext;
                }
                $rowData[] = $flight;
            }

            $array_all_lesson = array();
            $array_lesson = array();
            $array_day_lesson = array();

            $lesson_id = 0;
            foreach ($rowData as $row => $tr) {
                unset($array_lesson);

                foreach ($tr as $td) {
                    $array_lesson[] = $td;
                }

                if (count($array_lesson) == 1) {
                    $array_lesson_date = array();
                    $array_lesson_date['date'] = $array_lesson[0];
                    $array_day_lesson[] = $array_lesson_date;
                    $lesson_id = 1;
                } else {
                    $array_lesson_full = array();
                    $array_lesson_full['time'] = $array_lesson[0];
                    $array_lesson_full['subject'] = $array_lesson[1];
                    $array_lesson_full['teacher'] = $array_lesson[2];
                    $array_lesson_full['classroom'] = $array_lesson[3];
                    $array_day_lesson[] = array('lesson_id' => $lesson_id, 'lesson' => $array_lesson_full);
                    $lesson_id++;
                }
            }

            $array_all_days_and_lessons = array();
            $array_temp = array();
            $array_day_lessons = array();

            for ($e = 0; $e < count($array_day_lesson); $e++) {
                $array_temp = $array_day_lesson[$e];
                if ($array_temp["date"] != "") {
                    if (count($array_day_lessons) > 0) {
                        $array_all_days_and_lessons[] = $array_day_lessons;
                        unset($array_day_lessons);
                        $array_day_lessons[] = $array_temp;
                    } else {
                        $array_day_lessons[] = $array_temp;
                    }
                } else {
                    $array_day_lessons[] = $array_temp;
                }
            }
            if (count($array_day_lessons) > 0) $array_all_days_and_lessons[] = $array_day_lessons;

            $array_all_lesson['schedule'] = array('week' => $number_of_the_week, 'lessons' => $array_all_days_and_lessons);

            if (count($array_all_days_and_lessons) == 0) {
                echo "error";
            } else {
                echo json_encode($array_all_lesson);
            }
        }
    }
} elseif ($number_of_the_week_now == 1) {
// Номер максимальной учебной недели с имеющимся расписанием
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


        if (!empty($html)) $input_block = $html->find("form");
		
	        if (!empty($input_block[0])) {
            $nodes = $input_block[0]->find("option");
            $cont1 = 0;
            foreach ($nodes as $node) {
                $cont1++;
                $val = $node->value;
            }
            $html->clear();
            unset($html);
            //echo $cc = count($nodes);
            if (($cc = count($nodes)=="0")) echo "45";
            else echo $cc = count($nodes);
        }
        else {
          //  $html->clear();
            unset($html);
            echo "45";
        }

} elseif ($number_of_the_week_now == 2) {
// Номер текущей учебной недели
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

    if (!empty($html)) 	$current_week_block = $html->find("//*[@id=\"printpage\"]/span[2]",0)->plaintext;
    	if (!empty($current_week_block)){
	        $current_week_array = explode(" ",$current_week_block);
		    echo $current_week_array[3];
		    $html->clear();
            unset($html);
        }
        else {
          //  $html->clear();
            unset($html);
            echo "1";
        }
		} elseif ($number_of_the_week_now == 3) {
// Номер текущей учебной недели и номер максимальной учебной недели с имеющимся расписанием в json
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

    if (!empty($html)) $current_week_block = $html->find("//*[@id=\"printpage\"]/span[2]",0)->plaintext;
	$numWeekData=array();
	$current_week_array = explode(" ",$current_week_block);
	$numWeekData[0]=intval($current_week_array[3]);

    $html = new simple_html_dom();
    $html = str_get_html($out);


        if (!empty($html)) $input_block = $html->find("form");
	        if (!empty($input_block[0])) {
            $nodes = $input_block[0]->find("option");
            $cont1 = 0;
            foreach ($nodes as $node) {
                $cont1++;
                $val = $node->value;
            }
            $html->clear();
            unset($html);
            //echo $cc = count($nodes);
            if (($cc = count($nodes)=="0")) $numWeekData[1]=45;
            else $numWeekData[1] = count($nodes);
        }
        else {
          //  $html->clear();
            unset($html);
            $numWeekData[1]=45;
        }

		
		echo json_encode($numWeekData);
		
} elseif ($list_name_of_the_departament == 1) {
//Список кафедр
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
    if (!empty( $html)) {
        $input_block = $html->find("form");

        $nodes = $input_block[2]->find("option");
        foreach ($nodes as $node) {
            $val = $node->value;
            if ($val != "") echo $val . "<br />";
        }

        $html->clear();
    }
    unset($html);

} elseif ($list_all_teachers != "") {
//Список всех преподавателей университета:

    if ($curl = curl_init()) {
        curl_setopt($curl, CURLOPT_URL, 'http://miu.by/rus/schedule/schedule.php');
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_POST, true);
        $out = curl_exec($curl);
        curl_close($curl);
    } else {
        echo "error";
    }
    $array_kafedra = array();
    $html = new simple_html_dom();
    $html = str_get_html($out);
    $input_block = $html->find("form");

    $nodes = $input_block[2]->find("option");
    $i = 0;
    foreach ($nodes as $node) {
        $val = $node->value;
        if ($val != "") {
            //           echo $val . "<br />";
            $array_kafedra[$i++] = $val;;
        }
    }
    $html->clear();
    unset($html);

    $array_all_teachers = array();
    $i2 = 0;
    foreach ($array_kafedra as $name_of_the_departament) {
        //  echo  $name_of_the_departament_cp1251. "<br />";
        $name_of_the_departament_cp1251 = iconv("UTF-8", "CP1251", $name_of_the_departament);
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
        foreach ($nodes as $node) {
            $val = $node->value;
            if ($val != "") {
                $array_all_teachers[$i2++] = $val;
            }
        }
        $html->clear();
        unset($html);
    }

    sort($array_all_teachers, 2);
    $result_array_all_teachers = array_unique($array_all_teachers);
    if ($list_all_teachers==2){
      //  echo count($result_array_all_teachers) . "<br />";
        foreach ($result_array_all_teachers as $all_teachers) {
            echo  "&#34;".$all_teachers . "&#34;, ";
        }
    }
    else {
      //  $result_array_all_teachers = array_unique($array_all_teachers);
        echo count($result_array_all_teachers) . "<br />";
        foreach ($result_array_all_teachers as $all_teachers) {
            echo $all_teachers . "<br />";
        }
    }
} elseif ($list_specialities == 1) {
//Список всех специальностей университета:

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
        if ($val != "") echo $val . "<br />";
    }

    $html->clear();
    unset($html);

} elseif ($list_all_groups == 1) {
//Список номеров всех групп университета:

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
    if (!empty($html)) $input_block = $html->find("form");
    else exit;

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
        //       echo $name_of_the_specialty." </br> !!!!!!!!!!!!";
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
                if ($list_all_groups_and_special != 1) {
                    $array_groups[] = $val;
                  //  echo $val . "<br />";
                } else {
                    $array_groups[] = $val . ":" . $name_of_the_specialty; //группа:специальность
                }
            }
        }
        $html->clear();
        unset($html);
    }

    if ($list_all_groups_and_special != 1) {
        sort($array_groups, 2);
    }
    $result_array_groups = array_unique($array_groups);
    if ($list_all_groups_and_special != 1) echo count($result_array_groups) . "<br />";// сколько всего групп
    foreach ($result_array_groups as $all_groups) {
        echo $all_groups . "<br />";
    }

    if ($list_all_groups_and_special == 1) {
        $file = fopen("allgroups.html", "w");
        if (!$file) {
            echo("Ошибка открытия файла");
        } else {
            foreach ($result_array_groups as $all_groups) {
                //  echo $all_groups . "<br />";
                fwrite($file, $all_groups . "\n");
            }
        }
        fclose($file);
        //   echo("Файл успешно записан");
    }


} elseif ($get_faculties == 1) {
//Список факультетов университета в json (raspisaniye-vuzov.ru):
    include "json_parser_faculties_raspisaniyevuzovru.php";

} elseif ($get_spec == 1) {
//Список специальностей университета в json (raspisaniye-vuzov.ru):
    include "json_parser_speciality_raspisaniyevuzovru.php";

} elseif ($get_groups == 1) {
//Список всех групп университета в json raspisaniye-vuzov.ru:
    include "json_parser_groups_cache_raspisaniyevuzovru.php";

} elseif ($get_schedule == 1 and $group_id != "") {
//Расписание для групп по специальности университета в json:
    include "json_parser_raspisanie_raspisaniyevuzovru.php";

} elseif ($get_spec_group != "") {
//Номера групп по специальности в json:
    include "json_parser_groups_special_cache_raspisaniyevuzovru.php";
} elseif ($get_news != "") {
    //Получить новости с сайта miu.by:
    if ($curl = curl_init()) {
        curl_setopt($curl, CURLOPT_URL, 'http://miu.by/');
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_POST, true);
    //    curl_setopt($curl, CURLOPT_POSTFIELDS, 'spec=' . $name_of_the_speciality_cp1251);
        $out = curl_exec($curl);
        curl_close($curl);
    } else {
        echo "error";
    }
//echo $out;
    $html = new simple_html_dom();
    $html = str_get_html(iconv("CP1251", "UTF-8", $out));
//    $html = str_get_html($out);

 if (!empty($html)) {
    $table = $html->find('table',8);
   // echo $table;


        $rowData = array();
        foreach ($table->find('tr') as $row) {
            $flight = array();
            foreach ($row->find('td') as $cell) {
             //   $flight[] = $cell->plaintext;
                $flight[] = $cell;
            }
            $rowData[] = $flight;
        }

    $array_news_link=array();
 //   echo "--------------------------------";
    //  echo "<br>3 - ".$rowData[1][0];

    $i=1;
    foreach ($rowData[1][0]->find('a') as $cell)
    {
        if ($i%2==0) {
          //  echo $array_news_link[] = $cell->href;
        $array_news_link[] = $cell->href;
          //  echo "<br>";
        }
        $i++;

    }


$newsData=array();
    $array_el1=0;
    $array_el2=0;
    $arrayNews_count=0;
foreach ($rowData[1][0]->find('p') as $cell)
{
    if ($array_el2>1) {
        $newsData[$array_el1][2]=$array_news_link[$arrayNews_count];
        $arrayNews_count++;
        $array_el1++;
        $array_el2=0;
    }
    $newsData[$array_el1][$array_el2]= $cell->plaintext;
    $array_el2++;

   // echo $cell->plaintext;
  //  echo "<br>";
}
//print_r($newsData);

for ($i=0;$i<5;$i++){
  //  echo $newsData[$i][2]." ".$newsData[$i][0]." ".$newsData[$i][1]."<br>";
  $newsData[$i][2]." ".$newsData[$i][0]." ".$newsData[$i][1]."<br>";
}
    echo json_encode($newsData);


// print_r($rowData[1][0]);
 // print_r($rowData);

//echo "<br>3 - ".$rowData[1][0];

/*
        echo '<table border=\"1\">';
        foreach ($rowData as $row => $tr) {
            echo '<tr>';
            foreach ($tr as $td)
                echo '<td>' . $td . '</td>';
            echo '</tr>';
        }
        echo '</table>';

  */

$html->clear();
 }
    unset($html);

} elseif ($name_of_the_departament == "" and
    $list_name_of_the_departament == "" and
    $name_of_the_teacher == "" and
    $number_of_the_week == "" and
    $format_of_the_week == "" and
    $name_of_the_specialty == "" and
    $number_of_the_group == "" and
    $number_of_the_week_now == "" and
    $list_all_teachers == "" and
    $list_all_groups == "" and
    $list_specialities == "" and
    $json_format == ""
) {
    echo "empty request";
    echo "<br> Click <a href=\"about.html\"> here</a> for information on using the API.</br>";
} else {
    echo "Unknown error";
}
?>