0. В файле csv_img.php по необходимости добавить альбомы
   $albom  = array(
 	 		212624609=>array('txt'=>'Летняя обувь','name'=>'letnya'),
   	 		212624557=>array('txt'=>'Резиновые сапоги','name'=>'Rezinov')
   );

1. Загрузить прайс
	http://theme4u.ru/vk_test/csv_img.php

2. Файлы попадают в папку
	out.csv -файл с ценами
	./load2vk/img -картинки
	
3. Правим цены в файле out.csv

4. Копируем файл out.csv в \load2vk\load2vk.csv

5. Запускаем загрузку в vk.com
	http://theme4u.ru/vk_test/load2vk/