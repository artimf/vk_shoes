<?php
include_once 'vk.php';

function set_file($str){
  $output_file=$str;
  file_exists($output_file) ? $is_file= true : $is_file= false; 
  $f = fopen($output_file, "a"); 
  if (!$is_file) fwrite($f, 'my_article;article;text;my_price;price;albom_id;itm_href;img_src;img;img_local'."\r\n");//fclose($f);
  return $f;
}


// Отвечаем только на Ajax
if ($_SERVER['HTTP_X_REQUESTED_WITH'] != 'XMLHttpRequest') {return;}

// Можно передавать в скрипт разный action и в соответствии с ним выполнять разные действия.
$action = $_POST['action'];
if (empty($action)) {return;}

$count = 160;
$step = 20;

// Получаем от клиента номер итерации
$url = $_POST['url']; if (empty($url)) return;
$offset = $_POST['offset'];
$i=0;

//$f=set_file('123.txt');
// Проверяем, все ли строки обработаны
$offset = $offset + $step;
if ($offset >= $count) {
  $sucsess = 1;
} else {
  $i++;

        $token = '09339d0396408814d29a09b99bc1fea086d4d6a6ee416abdbf4336f9e841feca235672404cd2894a1713c';
        $delta = '15'; //вероятность того, что запись опубликуется на стену
        $app_id = '4783911';//ID приложения
        $group_id = '87493519';//ID группы
         
        $vk = new vk( $token, $delta, $app_id, $group_id );
        $cash_file = 'load2vk.csv';

        $j=0;
        if (file_exists($cash_file)){
          $fp = fopen ($cash_file,"r");
          while ($data = fgetcsv ($fp, 10000, ";")) {
              if($j>($offset-20) && $j<=$offset) {
              $my_articl= $data[0];
              $my_text= iconv("WINDOWS-1251","UTF-8", $data[2]);
              $my_price=$data[3];
              $vk_album=$data[5];
              $my_img=$data[9];
                //fwrite($f,'$offset='.$offset.';'.$my_articl."\r\n");
              $vk_photo = $vk->upload_photo($my_img, $vk_album, 'Арт: '.$my_articl.'; Размер '.$my_text.'. Цена '. $my_price);
              $vk_post = $vk->post('Размер '.$my_text.'. Цена '. $my_price, $vk_photo, '_' );
              usleep(400000);//Делаем меньше запросов к vk
            }
            $j++;
          }
          fclose($fp);
        }/**/

  $sucsess = round($offset / $count, 2);
}
//fclose($f);
// И возвращаем клиенту данные (номер итерации и сообщение об окончании работы скрипта)
$output = Array('offset' => $offset, 'sucsess' => $sucsess);
echo json_encode($output);
