# Скрипт бэкапа сайтов на Яндекс.Диск

Упаковывает файлы каждого сайта в zip архив, создает mysql дамп, добавляет его к архиву и загружает архив с файлами и дампом на Я.Диск

## Получение токена для доступа к API Яндекс.Диска

Зарегистрируйте приложение и получите токен в [oauth.yandex.ru/](https://oauth.yandex.ru/)

## Настройка скрипта

__1.Создайте пользователя в MySQL__

Создайте пользователя с правами только на чтение баз данных, которые вы хотите бэкапить:

```mysql
GRANT USAGE ON *.* TO 'dump_user'@'%' IDENTIFIED BY 'password';
GRANT SELECT, LOCK TABLES ON `mysql`.* TO 'dump_user'@'%';
GRANT SELECT, LOCK TABLES, SHOW VIEW, EVENT, TRIGGER ON `site1_db`.* TO 'dump_user'@'%';
GRANT SELECT, LOCK TABLES, SHOW VIEW, EVENT, TRIGGER ON `site2_db`.* TO 'dump_user'@'%';
```

__2.Настройте скрипт__

Скопируйте файл `config.dist.php` в `config.php` и измените в нем параметры сайтов, введите токен и логин/пароль пользователя MySQL

__3.Создавайте бэкапы__
