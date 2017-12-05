# Скрипт для бэкапа сайтов на Яндекс.Диск

Как работает скрипт:
1.Создает резервные копии сайтов и их баз данных.
2.Запаковывает файлы в архивы .zip или .tgz
3.Заливает архивы на Я.Диск

## Настройка

> Настройки сайтов и путей к ним должны находиться в файле config.php

__1.Переименуйте файл `config.dist.php` в `config.php`__

__2.Настройте параметры сайтов и Яндекс.Диска в `config.php`__

__3.Создайте пользователя для mysqldump с правами только на чтение__

```sql
GRANT USAGE ON *.* TO 'dump'@'%' IDENTIFIED BY ...;
GRANT SELECT, LOCK TABLES ON `mysql`.* TO 'dump'@'%';
GRANT SELECT, LOCK TABLES, SHOW VIEW, EVENT, TRIGGER ON `myschema`.* TO 'dump'@'%';
```

