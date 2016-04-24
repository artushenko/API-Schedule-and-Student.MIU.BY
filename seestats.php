<?php
if (isset($_GET[col])) { $col=$_GET[col]; } else { $col=50; }
$file=file("stat.log"); ?>

<html lang="ru"> <head>
    <meta charset="UTF-8"/>
<style type='text/css'>
 td.zz {PADDING-LEFT: 3px; FONT-SIZE: 9pt; PADDING-TOP: 2px; FONT-FAMILY: Arial; }
</style>
</head>

<body>
<center>
<?php
if ($col>sizeof($file)) { $col=sizeof($file); }
echo "Последние <b>".$col."</b> посещений сайта:"; ?>

<table width="680" cellspacing="1" cellpadding="1" border="0"
    STYLE="table-layout:fixed">
<tr bgcolor="#eeeeee">
 <td class="zz" width="100"><b>Время и дата</b></td>
 <td class="zz" width="200"><b>Данные о посетителе</b></td>
 <td class="zz" width="100"><b>IP/прокси</b></td>
 <td class="zz" width="280"><b>Посещенный URL</b></td>
<td class="zz" width="50"><b>ID</b></td>
</tr>

<?php
   for ($si=sizeof($file)-1; $si+1>sizeof($file)-$col; $si--) {
   $string=explode("|",$file[$si]);
   $q1[$si]=$string[0]; // дата и время
   $q2[$si]=$string[1]; // имя бота
   $q3[$si]=$string[2]; // ip бота
   $q4[$si]=$string[3]; // адрес посещения
   $q5[$si]=$string[4]; // ID
echo '<tr bgcolor="#eeeeee"><td class="zz">'.$q1[$si].'</td>';
echo '<td class="zz">'.$q2[$si].'</td>';
echo '<td class="zz">'.$q3[$si].'</td>';
echo '<td class="zz">'.$q4[$si].'</td>';
echo '<td class="zz">'.$q5[$si].'</td></tr>';
}
echo '</table>';
echo '<br>Просмотреть последние <a href=?col=100>100</a> <a href=?col=500>500</a>';
echo '<a href=?col=1000>1000</a> посещений.';
echo '<br>Просмотреть <a href=?col='.sizeof($file).'>все посещения</a>.</center>';
echo '</body></html>';
?>
