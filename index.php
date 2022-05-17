<?php

$configFile = '../sites/all/modules/civicrm/civicrm.config.php';
require_once $configFile;
require_once 'CRM/Core/Config.php';
$config = CRM_Core_Config::singleton();

function main() {
  echo '<link type="text/css" rel="stylesheet" href="style.css">';
  echo '<meta http-equiv="refresh" content="300" >';
  showTodaysDate();
  showEvents();
  showEventsTeGast();

}

function showEvents() {
	$dao = getEvents();
	//Remove special chars from Zaal
	while ($dao->fetch()) {
		echo '<p><span style="font-size: 40px;">' . $dao->title . '</span><br/><span style="font-size: 32px;">';
		$today = date('Y-m-d');
		$einddatum = $dao->Einddatum;
		if($einddatum !== $today){
			echo 'DOORLOPEND';
		}else{
			echo $dao->Startuur . ' -  ' . $dao->Einduur;
		}
		echo ' / ' . strtoupper(preg_replace("/\x01/",", ",substr($dao->Zaal,1,-1))) . '</span></p>';
		echo '<hr >';
  }
}

function showEventsTeGast() {
	$dao = getEventsTeGast();
	$daoCheck = getEventsTeGast();
        //Remove special chars from Zaal
	if($daoCheck->fetch()){
		echo '<p  style="font-size: 60px;" class="tegast">TE GAST</p>';
	}
	while ($dao->fetch()) {
                echo '<p><span style="font-size: 40px;">' . $dao->title . '</span><br/><span style="font-size: 32px;">';
                $today = date('Y-m-d');
                echo $dao->Startuur . ' -  ' . $dao->Einduur;
                echo ' / ' . strtoupper(preg_replace("/\x01/",", ",substr($dao->Zaal,1,-1))) . '</span></p>';
                echo '<hr >';
  }
}


function showTodaysDate(){

  //Dutch Translation months & days
  $months = [
    'January' => 'JANUARI',
    'February' => 'FEBRUARI',
    'March' => 'MAART',
    'April' => 'APRIL',
    'May' => 'MEI',
    'June' => 'JUNI',
    'July' => 'JULI',
    'August' => 'AUGUSTUS',
    'September' => 'SEPTEMBER',
    'October' => 'OKTOBER',
    'November' => 'NOVEMBER',
    'December' => 'DECEMBER'
  ];
  $days = [
    'Monday' => 'MAANDAG',
    'Tuesday' => 'DINSDAG',
    'Wednesday' => 'WOENSDAG',
    'Thursday' => 'DONDERDAG',
    'Friday' => 'VRIJDAG',
    'Saturday' => 'ZATERDAG',
    'Sunday' => 'ZONDAG'
  ];
  //Get date of today and replace english with dutch
  $today = date('l j F');
  $today = str_replace(array_keys($months), array_values($months), $today);
  $today = str_replace(array_keys($days), array_values($days), $today);
  echo '<p style="font-size: 54px;" class="datumvandaag">' . $today . '</p>';

}

function getEvents() {
  $sql = "
	SELECT title,
   	start_date,
   	end_date,
   	DATE_FORMAT(start_date,'%H:%i') AS Startuur,
   	DATE_FORMAT(end_date,'%H:%i') AS Einduur,
   	DATE_FORMAT(start_date,'%Y-%m-%d') AS Startdatum,
   	DATE_FORMAT(end_date,'%Y-%m-%d') AS Einddatum,
   	d.muntpunt_zalen_555 As Zaal,
   	d.activiteit_status_415,
   	event_type_id,
   	c.label
	FROM civicrm_event a
	LEFT JOIN civicrm_value_evenet_doelpgroep_109 d ON a.id = d.entity_id
	LEFT JOIN civicrm_option_value c ON event_type_id = c.value
	WHERE (DATE_FORMAT(now(),'%d %M %Y') = DATE_FORMAT (start_date, '%d %M %Y')
	OR (DATE_FORMAT(start_date,'%Y %m %d')  <= DATE_FORMAT(now(),'%Y %m %d')
	AND DATE_FORMAT(end_date,'%Y %m %d') >= DATE_FORMAT(now(),'%Y %m %d')))
	AND d.muntpunt_zalen_555 NOT LIKE ''
	AND d.activiteit_status_415 IN (2,5)
	AND c.option_group_id = 14
	AND event_type_id IN (11,24,25,30,26,27,28,31,29,36,44,33,32,9,35,6,20,49,50)
	ORDER BY start_date , Startuur
	LIMIT 0,10
";
  $dao = CRM_Core_DAO::executeQuery($sql);
  return $dao;
}

function getEventsTeGast() {
  $sql = "
SELECT title,
   start_date,
   end_date,
   DATE_FORMAT(start_date,'%H:%i') AS Startuur,
   DATE_FORMAT(end_date,'%H:%i') AS Einduur,
   d.muntpunt_zalen_555 As Zaal,
   d.activiteit_status_415,
   event_type_id,
   c.label
FROM civicrm_event a
LEFT JOIN civicrm_value_evenet_doelpgroep_109 d ON a.id = d.entity_id
LEFT JOIN civicrm_option_value c ON event_type_id = c.value
WHERE (DATE_FORMAT(now(),'%d %M %Y') = DATE_FORMAT (start_date, '%d %M %Y')
OR (DATE_FORMAT(start_date,'%Y %m %d')  <= DATE_FORMAT(now(),'%Y %m %d')
AND DATE_FORMAT(end_date,'%Y %m %d') >= DATE_FORMAT(now(),'%Y %m %d')))
AND d.muntpunt_zalen_555 NOT LIKE ''
AND d.activiteit_status_415 IN (2,5)
AND c.option_group_id = 14
AND event_type_id IN (39,48)
ORDER BY start_date
LIMIT 0,10
";
  $dao = CRM_Core_DAO::executeQuery($sql);
  return $dao;
}


main();


