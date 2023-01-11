[![php](https://img.shields.io/badge/PHP->=8.1-green.svg?style=flat&logo=appveyor "Доступно для PHP 8.1+")](http://php.net/)


## Библиотека для форматирования даты и времени

### Установка

```bash
composer req ildarik081/time-helper
```

### Использование
```php
TimeHelper::create('2023-01-09 20:31:00')->today(); // Сегодня, Завтра, Вчера или 9 января 2023 г.
```
