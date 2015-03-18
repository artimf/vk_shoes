 <?php 
//session_start();
if ($_SERVER['HTTP_X_REQUESTED_WITH'] != 'XMLHttpRequest') {return;}// Отвечаем только на Ajax
$action = $_POST['action']; if (empty($action)) {return;}      // Можно передавать в скрипт разный action и в соответствии с ним выполнять разные действия.
include_once './simplehtmldom/simple_html_dom.php';

function str_clear($str){
    $str = str_replace(array("\r\n", "\r", "\n", "\t", '  ', '    ', '    ','Артикул:','&nbsp;','Размерный ряд','Внешний материал:','Внутренний материал:'), ' ', $str);
  $str = ltrim($str);
  $str = strip_tags($str); 
  $str = str_replace(array('  ', '    ', '    ', ': '), ' ', $str);
  $str = str_replace(array('   ','  ', '    ', '    ', ': '), ' ', $str);
  return $str;
}
function get_itm_text ($str){ 
  return preg_replace('/^[0-9]*/', '', $str) ;
}
function get_digit($str){
  $str=str_replace(' ','',$str);
  preg_match('/^[0-9]*/', $str, $match);
  return $match[0];
}
function set_file($str){
  $output_file=$str;
  file_exists($output_file) ? $is_file= true : $is_file= false; 
  $f = fopen($output_file, "a"); 
  if (!$is_file) fwrite($f, 'my_article;article;text;my_price;price;albom_id;itm_href;img_src;img;img_local'."\r\n");//fclose($f);
  return $f;
}
function extractFileName($filename){
  $p=strpos($filename,'.');
  if($p>0) return substr($filename,0,$p);
  else return $filename;
}
function randValue($length){
  //return substr(chr( mt_rand( 97 ,122 ) ) .substr( md5( time( ) ) ,1 ),0,15);
  $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $charactersLength = strlen($characters);
    $randomString = '';
    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[rand(0, $charactersLength - 1)];
    }
    return $randomString;
}

$offset = $_POST['offset'];    // Получаем от клиента номер итерации

$step = 1;
$count = 1;

$site_url='http://neposeda-shoes.ru/';
   $outFile=$_POST['out_file']; $outFile = 'out.csv';
$albom_id=$_POST['albom_id'];
$upload_img=$_POST['upload_img'];

$url = $_POST['url']; if (empty($url)) return; 
$f=set_file($outFile);
$forMyArt=0;

if ($offset >= $count) {                           // Проверяем, все ли строки обработаны
  $sucsess = 1; 
} else {
         $HTML = file_get_html($url); 
         foreach($HTML->find('div[id^=bx_]') as $div) 
        { 
          $itm_href = $site_url.$div->find('a',0)->href; 
          $itm_img_src = $site_url.$div->find('img',0)->src;// 'http://localhost/shoes/test/'
          $itm_text_arr = explode("<br />", $div->find('div.col-md-12',0)->outertext);
          $itm_artcl = '_'.str_clear($itm_text_arr[0]); 
          $itm_text=''; 
          $itm_text = str_clear($itm_text.$itm_text_arr[1].', '.$itm_text_arr[3].', '.$itm_text_arr[4]); 
          $itm_price = get_digit($div->find('span',0)->plaintext);

          $img_local = './img/'.basename($itm_img_src);
          
          if($upload_img==1)file_put_contents('./load2vk/img/'.basename($itm_img_src), file_get_contents($itm_img_src));  
          
          $my_artcl= randValue(7);
          
          fwrite($f, iconv( "UTF-8", "WINDOWS-1251", 
            $my_artcl.';'.
            $itm_artcl.';'.
            $itm_text.';'.
            $itm_price.';'.
            $itm_price.';'.
            $albom_id.';'.
            $itm_href.';'.
            $itm_img_src.';'. 
            '.;'.
            $img_local. 
             "\r\n")); /**/
        }

        $sucsess = round($offset / $count, 2);
}
$offset = $offset + $step;
//fclose($f);
$output = Array('offset' => $offset, 'sucsess' => $sucsess); // И возвращаем клиенту данные (номер итерации и сообщение об окончании работы скрипта)
echo json_encode($output);