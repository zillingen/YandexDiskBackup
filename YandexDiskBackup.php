<?php

$config = require_once __DIR__."/config.php";
$date = date('Ymd_His').'UTC'; // Дата создания бэкапа

/**
 * Проверка подключения к диску и вывод базовой информации 
 */
function getDiskInfo() {
  global $config;

  $ch = curl_init();

  $headers = array(
    'Authorization: OAuth '.$config['token'],
    'Accept: application/json',
  );

  $options = array(
      CURLOPT_URL             => 'https://cloud-api.yandex.net/v1/disk/',
      CURLOPT_RETURNTRANSFER  => TRUE,
      CURLOPT_VERBOSE         => FALSE,
      CURLOPT_HTTPHEADER      => $headers,
    );
  curl_setopt_array($ch, $options);

  $body = curl_exec($ch);
  $res = curl_getinfo($ch); 
  curl_close($ch);

  if ($res['http_code'] === 200) {
    return TRUE;
  } else {
    return FALSE;
  }
}

/**
 * Получение информации о файле или папке
 *
 * @param string $path Путь к файлу/папке в папке приложения
 */
function getDiskFileInfo($path) {
	global $config;
	$baseUrl = 'https://cloud-api.yandex.net/v1/disk/resources?';
	$url = $baseUrl . http_build_query(array(
		'path'	=> 	'app:/'.$path
	));

  $ch = curl_init();

  $headers = array(
    'Authorization: OAuth '.$config['token'],
    'Accept: application/json',
  );

  $options = array(
      CURLOPT_URL             => $url,
      CURLOPT_RETURNTRANSFER  => TRUE,
      CURLOPT_VERBOSE         => TRUE,
      CURLOPT_HTTPHEADER      => $headers,
    );
  curl_setopt_array($ch, $options);

  $body = curl_exec($ch);
  $res = curl_getinfo($ch);
  curl_close($ch);

  if ($res['http_code'] === 200) {
    return TRUE;
  } else {
    return FALSE;
  }
}

/**
 * Создает директорию в папке приложения на Я.Диске
 *
 * @param string $name Имя директории
 */
function createDiskDir($name) {
  global $config;
	$baseUrl = 'https://cloud-api.yandex.net/v1/disk/resources/?';
	$url = $baseUrl . http_build_query(array(
		'path'	=>	'app:/'.$name,
	));

  $ch = curl_init();

  $headers = array(
    'Authorization: OAuth '.$config['token'],
    'Accept: application/json',
  );

	$options = array(
      CURLOPT_URL             => $url,
      CURLOPT_RETURNTRANSFER  => TRUE,
      CURLOPT_VERBOSE         => FALSE,
      CURLOPT_HTTPHEADER      => $headers,
			CURLOPT_CUSTOMREQUEST   => 'PUT',
    );
  curl_setopt_array($ch, $options);

	$body = curl_exec($ch);
  $res = curl_getinfo($ch);
  curl_close($ch);

  // 201 - Created; 409 - Conflict (папка уже существует)
	if ($res['http_code'] === 201 || $res['http_code'] === 409) {
    return TRUE;
  } else {
    return FALSE;
  }
}

/**
 * Загружает файл на диск
 *
 * @param string $filename Имя файла, который надо загрузить
 */
function uploadDiskFile($filename) {

}

/**
 * Создание Zip архива из директории
 *
 * @param string $sourceDir Директория, которую надо запаковать
 * @param string $zipArchive Имя файла zip архива
 */
function createZipArchiveFromDir($sourceDir, $zipArchive) {

}

/**
 * Добавление файла к zip архиву
 *
 * @param string $fileName Имя добавляемого файла
 * @param string $zipArchive Имя архива
 */
function appendFileToZipArchive($fileName, $zipArchive) {

}

/**
 * Создание Mysql дампа
 *
 * @param string $db Имя базы данных
 * @param string $username Имя пользователя Mysql
 * @param string $password Пароль пользователя Mysql
 * @param string $dumpFile Имя файла дампа
 */
function createMysqlDump($db, $username, $password, $dumpFile) {

}
