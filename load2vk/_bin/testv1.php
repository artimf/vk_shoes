<?php
//http://dudev.ru/blog/20_styena-vk.html
class vk {
  private $token;
  private $app_id;
  //ID группы или страницы пользователя
  private $group_id;
  //вероятность публикации поста на стену
  private $delta;
  public function __construct( $token, $delta, $app_id, $group_id ) {
    $this->token = $token;
    $this->delta = $delta;
    $this->app_id = $app_id;
    $this->group_id = $group_id;
  }
  //постинг на стену
  public function post( $desc, $photo, $link ) {
    if( rand( 0, 99 ) < $this->delta ) {
      $data = json_decode(
            $this->execute(
              'wall.post',
              array(
                'owner_id' => -$this->group_id,
                'from_group' => 1,
                'message' => $desc,
                'attachments' => 'photo-' . $this->group_id . '_' . $photo . ',' . $link
              )
            )
          );
      if( isset( $data->error ) ) {
        return $this->error( $data );
      }
      return $data->response->post_id;
    }
    return 0;
  }
  //создание альбома
  public function create_album( $name, $desc ) {
    $data = json_decode(
          $this->execute(
            'photos.createAlbum',
            array(
              'title' => $name,
              'gid' => $this->group_id,
              'description' => $desc,
              'comment_privacy' => 1,
              'privacy' => 1
            )
          )
        );
    if( isset( $data->error ) ) {
      return $this->error( $data );
    }
    return $data->response->aid;
  }
  //получение кол-ва фотографий в альбоме
  public function get_album_size( $id ) {
    $data = json_decode(
          $this->execute(
            'photos.getAlbums',
            array(
              'oid' => -$this->group_id,
              'aids' => $id
            )
          )
        );
    if( isset( $data->error ) ) {
      return $this->error( $data );
    }
    return $data->response['0']->size;
  }
  //загрузка фотографии
  public function upload_photo( $file, $album_id, $desc ) {
    $data = json_decode(
          $this->execute(
            'photos.getUploadServer',
            array(
              'aid' => $album_id,
              'gid' => $this->group_id,
              'save_big' => 1
            )
          )
        );
    if( isset( $data->error ) ) {
      return $this->error( $data );
    }
    $ch = curl_init( $data->response->upload_url );
    curl_setopt ( $ch, CURLOPT_HEADER, false );
    curl_setopt ( $ch, CURLOPT_RETURNTRANSFER, true );
    curl_setopt ( $ch, CURLOPT_SSL_VERIFYPEER, false );
    curl_setopt ( $ch, CURLOPT_POST, true );
    curl_setopt ( $ch, CURLOPT_POSTFIELDS, array( 'file1' => '@' . $file ) );
    $data = curl_exec($ch);
    curl_close($ch);
    $data = json_decode( $data );
    if( isset( $data->error ) ) {
      return $this->error( $data );
    }
    $data = json_decode(
          $this->execute(
            'photos.save',
            array(
              'aid' => $album_id,
              'gid' => $this->group_id,
              'server' => $data->server,
              'photos_list' => $data->photos_list,
              'hash' => $data->hash,
              'caption' => $desc
            )
          )
        );
    if( isset( $data->error ) ) {
      return $this->error( $data );
    }
    return $data->response['0']->pid;
  }
  private function execute( $method, $params ) {
    $ch = curl_init( 'https://api.vk.com/method/' . $method . '?access_token=' . $this->token );
    curl_setopt ( $ch, CURLOPT_HEADER, false );
    curl_setopt ( $ch, CURLOPT_RETURNTRANSFER, true );
    curl_setopt ( $ch, CURLOPT_SSL_VERIFYPEER, false );
    curl_setopt ( $ch, CURLOPT_POST, true );
    curl_setopt ( $ch, CURLOPT_POSTFIELDS, $params );
    $data = curl_exec($ch);
    curl_close($ch);
    return $data;
  }
  private function error( $data ) {
    //обработка ошибок
    return false;
  }
}

//http://theme4u.ru/vk_test/test.php
//https://oauth.vk.com/authorize?client_id=[ID приложения]&scope=[запрашиваемые права]&display=page&response_type=token&redirect_uri=https://oauth.vk.com/blank.html
//id app 4783911
//права offline,group,photos,wall
//token 09339d0396408814d29a09b99bc1fea086d4d6a6ee416abdbf4336f9e841feca235672404cd2894a1713c

$token = '09339d0396408814d29a09b99bc1fea086d4d6a6ee416abdbf4336f9e841feca235672404cd2894a1713c';
$delta = '1'; //вероятность того, что запись опубликуется на стену
$app_id = '4783911';//ID приложения
$group_id = '87493519';//ID группы
$vk = new vk( $token, $delta, $app_id, $group_id );
//Допустим альбом уже создан, проверим не переполнен ли он
$vk_album = '212212406';//ID альбома
if( $vk->get_album_size( $vk_album ) > 400 ) {
  //если переполнен, более 400 фоток
  //то создаём новый
  $vk_album = $vk->create_album( 'Auto_albom', '{{text albom описание}}' );
}

//загружаем фотографию
$vk_photo = $vk->upload_photo('57.jpg', $vk_album, 'Фото загружено с помощью программы' );
  //пишем пост на стену
$vk_post = $vk->post('{{Text post текст поста}}', $vk_photo, '{{href to post ссылка, добавляемая к посту}}' );

//загружаем фотографию
 $vk_photo = $vk->upload_photo('1b.jpg', $vk_album, 'Повторяем' );
//пишем пост на стену
$vk_post = $vk->post('{{Text post текст поста}}', $vk_photo, '{{href to post ссылка, добавляемая к посту}}' );
