<?php 

/**
 * Скрипт для бэкапа сайтов на Яндекс.Диск
 * 
 */
$config = require_once __DIR__.'/config.php';

/**
 * получение общей информации о диске и проверка подключения
 */
function getDiskInfo() {
  global $config;
  $url = 'https://cloud-api.yandex.net/v1/disk/';
  $c = curl_init($url);

  curl_setopt($c, CURLOPT_URL, $url);
  curl_setopt($c, CURLOPT_HTTPHEADER, ['Authorization: OAuth '.$config['token'], 'Accept: application/json']);
  curl_setopt($c, CURLOPT_RETURNTRANSFER, true);
  curl_setopt($c, CURLOPT_VERBOSE, false); // Enable curl verbose output
  $res = json_decode(curl_exec($c), true);
  $code = curl_getinfo($c, CURLINFO_RESPONSE_CODE);

  // Если запрос выполнен неудачно
  if ($code != 200) {
    return false;
  }

  curl_close($c);
  return $res;
}

/**
 * Запрос метаинформации о папке и проверка, что папка существует
 */
function getDirInfo($dir) {
  global $config;
  $url = 'https://cloud-api.yandex.net/v1/disk/resources?';
  $c = curl_init();

  curl_setopt($c, CURLOPT_URL, $url.http_build_query(['path' => 'app:/'.$dir]));
  curl_setopt($c, CURLOPT_HTTPHEADER, ['Authorization: OAuth '.$config['token'], 'Accept: application/json']);
  curl_setopt($c, CURLOPT_RETURNTRANSFER, true);
  curl_setopt($c, CURLOPT_VERBOSE, false); // Enable curl verbose output
  $res = json_decode(curl_exec($c), true);
  $code = curl_getinfo($c, CURLINFO_RESPONSE_CODE);
  curl_close($c);

  // Если не авторизован или папка не найдена
  if ($code != 200) {
    return false;
  }

  // Если папка существует, возвращаем ее имя
  return $res['name'];
}

/**
 * Создание папки сайта
 *
 * Ф-ция проверяет, наличие папки с именем сайта и если папки не существует, то создает ее
 */
function createSiteDir($dir) {
  global $config;
  $url = 'https://cloud-api.yandex.net/v1/disk/resources/?';
  $c = curl_init();

  curl_setopt($c, CURLOPT_URL, $url.http_build_query(['path' => 'app:/'.$dir]));
  curl_setopt($c, CURLOPT_PUT, true);
  curl_setopt($c, CURLOPT_HTTPHEADER, ['Authorization: OAuth '.$config['token'], 'Accept: application/json']);
  curl_setopt($c, CURLOPT_RETURNTRANSFER, true);
  curl_setopt($c, CURLOPT_VERBOSE, false); // Enable curl verbose output
  $res = json_decode(curl_exec($c), true);
  $code = curl_getinfo($c, CURLINFO_RESPONSE_CODE);
  curl_close($c);

  // Если не авторизован или папку не удалось создать
  if ($code != 201) {
    return false;
  }

  // Если папка создана, возвращаем ссылку на нее
  return $res['href'];
}

/**
 * Загрузка файла на Я.Диск
 */
function uploadFile($dir, $file) {
  global $config;
  $url = 'https://cloud-api.yandex.net/v1/disk/resources/upload?';
  $filepath = __DIR__."/{$config['tmp_dir']}/$dir/$file";
  $c = curl_init();

  // Запрос URL для загрузки
  curl_setopt($c, CURLOPT_URL, $url.http_build_query(['path' => "app:/$dir/$file", 'overwrite'=>true]));
  curl_setopt($c, CURLOPT_HTTPHEADER, ['Authorization: OAuth '.$config['token'], 'Accept: application/json']);
  curl_setopt($c, CURLOPT_RETURNTRANSFER, true);
  curl_setopt($c, CURLOPT_VERBOSE, 0); // Enable curl verbose output
  $res = json_decode(curl_exec($c), true);
  $code = curl_getinfo($c, CURLINFO_RESPONSE_CODE);

  // если ссылка не получена
  if ($code != 200 ) {
    print "Ошибка при получении ссылки для загрузки файла: $dir/$file";
    var_dump($res);
    return false;
  }

  // Загружаем файл 
  $url = $res['href'];
  $data = fopen($filepath, 'r');
  curl_setopt($c, CURLOPT_URL, $url);
  curl_setopt($c, CURLOPT_PUT, 1);
  curl_setopt($c, CURLOPT_INFILE, $data); // set file
  curl_setopt($c, CURLOPT_INFILESIZE, filesize($filepath)); // set file size
  curl_setopt($c, CURLOPT_HTTPHEADER, ['Authorization: OAuth '.$config['token'], 'Accept: application/json']);
  curl_setopt($c, CURLOPT_RETURNTRANSFER, true);
  curl_setopt($c, CURLOPT_VERBOSE, 0); // Enable curl verbose output
  $res = json_decode(curl_exec($c), true);
  $code = curl_getinfo($c, CURLINFO_RESPONSE_CODE);
  curl_close($c);
  fclose($data);
  // Если загрузка не удалась
  if ($code != 201) {
    print "Ошибка при загрузке файла на Я.Диск";
    var_dump($res);
    return false;
  }

  print "Файл загружен: $filepath \n";

}

/**
 * Выполнение бэкапа
 */
foreach ($config['sites'] as $key => $site) {

  // Cоздание Zip архива
  $zip = new ZipArchive();
  $date = date('Y-M-d-His').'UTC';
  $archive = "{$key}_{$date}.zip";
  $ret = $zip->open(__DIR__."/{$config['tmp_dir']}/$key/$archive", ZipArchive::CREATE);
  if ($ret !== true) {
    print "Create Zip archive error!";
    var_dump($ret);
  } else {
    $zip->addGlob("{$site['dir']}/*", GLOB_NOSORT, ['add_path' => "$key/", 'remove_path' => $site['dir']]);
    $zip->close();
  }

  // Проверка подключения к Я.Диску и загрузка архива
  if ( getDiskInfo() ) {
    // если папки сайта нет, создаем ее и загружаем архив сайта на Я.Диск
    if (! getDirInfo($key) ) {
      createSiteDir($key);
    } 
    uploadFile($key, $archive);
  } else {
    print "Ошибка или нет подключения к Я.Диску";
  }
}
