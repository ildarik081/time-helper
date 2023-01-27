[![php](https://img.shields.io/badge/PHP->=8.1-green.svg?style=flat&logo=appveyor "Доступно для PHP 8.1+")](http://php.net/)


## Библиотека для форматирования даты и времени

### Установка

```bash
composer req ildarik081/time-helper
```

### Использование
```php
use Ildarik081\TimeHelper;

// Получить дату в строковом формате: Сегодня, Завтра, Вчера, 27 Января 2023
TimeHelper::getInstance('2023-01-27 20:31:00')->today();

// Модифицировать дату и получить в строковом формате: Завтра
TimeHelper::getInstance('2023-01-27 20:31:00')->modify(1)->today();

// Модифицировать дату и получить в строковом формате: 27 Января 2023
TimeHelper::getInstance('2023-01-27 20:31:00')->longDate();

// Модифицировать дату и получить в строковом формате: ПТ 20:31
TimeHelper::getInstance('2023-01-27 20:31:00')->shortTime();

// Модифицировать дату и получить в строковом формате: Пятница
TimeHelper::getInstance('2023-01-27 20:31:00')->dayWeek();

// Модифицировать дату и получить в строковом формате: Январь
TimeHelper::getInstance('2023-01-27 20:31:00')->month(false);
```
