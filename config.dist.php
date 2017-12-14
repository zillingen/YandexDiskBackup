<?php

$db_user = 'backup';
$db_pass = 'password';

return array(
    'token' => '', // Yandex Disk Token
    'tmp_dir' => 'tmp', // папка в текущей директории для сохранения архивов и mysql дампов
    'sites' => array(
        'site1.ru' => array(
            'dir'         => '/var/www/html/site1.ru',
            'db_name'     => 'site1',
            'db_username' => $db_user,
            'db_password' => $db_pass,
        ),
        'site2.ru' => array(
            'dir'         => '/var/www/html/site2.ru',
            'db_name'     => 'site2',
            'db_username' => $db_user,
            'db_password' => $db_pass,
        ),
        'simple-site.com' => array(
            'dir'         => '/var/www/html/simple-site.com', // Если базы данных нет, то следует указать только папку сайта
        ),
    ),
);
