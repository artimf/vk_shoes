<?
//v1.
header("Content-Type: text/html; charset=utf-8"); 
session_start();
$_SESSION['PARSE'] = '';
echo '<!DOCTYPE html>';


$albom  = array(
 212624609=>array('txt'=>'Летняя обувь','name'=>'letnya'),
 212624557=>array('txt'=>'Резиновые сапоги','name'=>'Rezinov')
);


?>

<html>
  <head>
    <title>ScriptOffset</title>
    <script type="text/javascript" src="./js/jquery.min.js"></script>
    <script type="text/javascript" src="./js/scriptoffset.js"></script>
    <link rel="stylesheet" type="text/css" href="./js/scriptoffset.css">
  </head>
  <body>
	<div class="form">
	  <input id="url" name="url" value ="http://neposeda-shoes.ru/catalog/letnyaya-obuv/?PAGEN_1=[DD]" style="width: 400px;"> 
      <input id="offset" name="offset" type="hidden">
      <div class="progress" style="display: none;">
		<div class="bar" style="width: 0%;"></div>
      </div>
	  <p>Альбом в ВК
	  <?
        echo '<select id="albom_id" name="albom_id">';
		foreach ($albom as $key => $value){ //echo $key.$value['name'].$value['txt'].' <br>';
			echo "<option value=$key>".$value['txt'].'</option>';
		}
		echo '</select>';
	  ?>
	  <select id="upload_img" name="upload_img"><option value='1'>Загружать картинки</option><option value='0'>Не загружать картинки</option></select>
	  </p>
	  <p> 
		<input id="pages" name="pages" value ="1,2" style="width: 400px;">
	  </p>
	  <p> 
		<input id="out_file" name="out_file" value ="items_file.csv" style="width: 400px;">
	  </p>
      <a href="#" id="runScript"  class="btn" data-action="run">Start</a>
      <a href="#" id="refreshScript" class="btn" style="display: none;">Restart</a> 
    </div>
	<div class="info"></div>
  </body>
</html>