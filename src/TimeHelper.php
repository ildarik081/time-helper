<?php

namespace Ildarik081;

use DateTime;

final class TimeHelper
{
    private DateTime $dateTime;
    private const TODAY_STRING = 'Сегодня';
    private const YESTERDAY_STRING = 'Вчера';
    private const TOMORROW_STRING = 'Завтра';
    private const MONTH = [
        'Январь', 'Февраль', 'Март', 'Апрель', 'Май', 'Июнь', 'Июль', 'Август', 'Сентябрь',
        'Октябрь', 'Ноябрь', 'Декабрь'
    ];
    private const MONTH_PLURAL = [
        'Января', 'Февраля', 'Марта', 'Апреля', 'Мая', 'Июня', 'Июля', 'Августа',
        'Сентября', 'Октября', 'Ноября', 'Декабря'
    ];
    private const SHORT_MONTH = ['Янв', 'Фев', 'Мар', 'Апр', 'Май', 'Июн', 'Июл', 'Авг', 'Сен', 'Окт', 'Ноя', 'Дек'];
    private const DAY = ['Понедельник', 'Вторник', 'Среда', 'Четверг', 'Пятница', 'Суббота', 'Воскресение'];
    private const SHORT_DAY = ['Пн', 'Вт', 'Ср', 'Чт', 'Пт', 'Сб', 'Вс'];

    private const DATETIME = 'Y-m-d H:i:s';
    private const DATE = 'Y-m-d';
    private const EUR_DATETIME = 'd.m.Y H:i:s';
    private const STRING_DATE = 'd month year time';

    private function __construct(?string $date, string $format = self::DATETIME)
    {
        mb_internal_encoding('UTF-8');

        if ($format === self::STRING_DATE) {
            $this->dateTime = $this->parse($date);
        } elseif (is_string($date)) {
            $this->dateTime = new DateTime($date);
        } elseif ($date instanceof DateTime) {
            $this->dateTime = $date;
        } else {
            $this->dateTime = new DateTime();
        }

        return $this;
    }

    public function __toString()
    {
        return $this->datetime();
    }

    public function __clone()
    {
        $this->dateTime = clone $this->dateTime;
    }

    /**
     * Создание TimeHelper с указанием даты и формата
     *
     * @param string|null $date
     * @param string $format
     * @return self
     */
    public static function create(?string $date, string $format = self::DATETIME): self
    {
        return new self($date, $format);
    }

    /**
     * Разбор даты из строки вида '2  Мая 2014 в 12:05'
     *
     * @param string $string
     * @return DateTime
     */
    public function parse(string $string): DateTime
    {
        preg_match(
            '/^(\d+) +([ъхзщшгнекуцйфывапролджэёюбьтимсчя]+) +(\d{4})[A-zА-я ]+(\d{2}:?\d?\d?)?/',
            mb_strtolower(trim($string)),
            $matches
        );

        $result['day'] = (isset($matches[1]) && is_numeric($matches[1]))
            ? str_pad($matches[1], 2, '0', STR_PAD_LEFT)
            : date('d');

        if (isset($matches[2])) {
            $month = array_map('mb_strtolower', self::MONTH);
            $monthPlural = array_map('mb_strtolower', self::MONTH_PLURAL);
            $shortMonth = array_map('mb_strtolower', self::SHORT_MONTH);
            if (array_keys($month, $matches[2])) {
                $monthNum = array_keys($month, $matches[2]);
            } elseif (array_keys($monthPlural, $matches[2])) {
                $monthNum = array_keys($monthPlural, $matches[2]);
            } elseif (array_keys($shortMonth, $matches[2])) {
                $monthNum = array_keys($shortMonth, $matches[2]);
            }
        }

        if ($result) {
            $result['month'] = isset($monthNum[0])
                ? str_pad($monthNum[0] * 1 + 1, 2, '0', STR_PAD_LEFT)
                : date('m');

            $result['year'] = (isset($matches[3]) && strlen($matches[3]) === 4)
                ? $matches[3]
                : date('Y');

            $result['time'] = isset($matches[4])
                ? str_pad($matches[4], 8, ':00')
                : '00:00:00';

            $dateStr = $result['year'] . '-' . $result['month'] . '-' . $result['day'] . ' ' . $result['time'];
        }

        return new DateTime($dateStr);
    }

    /**
     * Изменение даты
     *
     * @param int $day
     * @param string $param
     * @return self
     */
    public function modify(int $day, string $param = 'day'): self
    {
        $this->dateTime->modify((int) $day . ' ' . $param);

        return $this;
    }

    /**
     * Вывод даты и времени с указанием формата
     *
     * @param string $dateFormat
     * @return string
     */
    public function datetime(string $dateFormat = self::DATE): string
    {
        $result = '';

        $time = match ($dateFormat) {
            self::DATETIME,
            self::EUR_DATETIME => false
        };

        if ($this->dateTime) {
            $result .= $this->dateTime->format($dateFormat);

            if ($time) {
                $result .= ' ' . $this->dateTime->format('H:i:s');
            }
        }

        return $result;
    }

    /**
     * Получение номера дня
     *
     * @return string
     */
    public function day(): string
    {
        return $this->dateTime->format('j');
    }

    /**
     * Получения номера дня недели
     *
     * @return int
     */
    public function dayOfWeek(): int
    {
        return (int) $this->dateTime->format('N');
    }

    /**
     * Получение дня недели текстом
     *
     * @param bool $short
     * @return string
     */
    public function dayString(bool $short = false): string
    {
        $formatN = (int) $this->dateTime->format('N') - 1;
        $days = $short ? self::SHORT_DAY : self::DAY;

        return isset($days[$formatN]) ? $days[$formatN] : '';
    }

    /**
     * Получение месяца
     *
     * @param bool $plural
     * @return string
     */
    public function month(bool $plural = true): string
    {
        $result = '';
        $monthNumber = (int) $this->dateTime->format('n') - 1;

        if ($plural) {
            $result .= ' ' . mb_convert_case(self::MONTH_PLURAL[$monthNumber], MB_CASE_TITLE);
        } else {
            $result .= ' ' . mb_convert_case(self::MONTH[$monthNumber], MB_CASE_TITLE);
        }

        return $result;
    }

    /**
     * Разница с текущей датой и временем
     *
     * @param string $format
     * @return string
     */
    public function diff(string $format = '%i'): string
    {
        $date = clone $this->dateTime;

        return (new DateTime())->diff($date)->format($format);
    }

    /**
     * Получение словесного отображения даты
     *
     * Например 'Сегодня', 'Вчера', 'Завтра', '14 сентября 2015 г.'
     *
     * @param bool $year
     * @param bool $time
     * @return string
     */
    public function today(bool $year = true, bool $time = false): string
    {
        $today = (new DateTime())->setTime(0, 0, 0);
        $date = (clone $this->dateTime)->setTime(0, 0, 0);
        $result = $this->longDate($year);

        if ($today->diff($date)->format('%a') === '0') {
            $result = self::TODAY_STRING;
        } elseif ($today->diff($date)->format('%R%a') === '+1') {
            $result = self::TOMORROW_STRING;
        } elseif ($today->diff($date)->format('%R%a') === '-1') {
            $result = self::YESTERDAY_STRING;
        }

        if ($time) {
            $result .= ' ' . $this->shortTime(false);
        }

        return $result;
    }

    /**
     * Получение длинного отображения даты текстом
     *
     * Число, месяц, опц. год, опц. время
     *
     * @param bool $year
     * @param bool $time
     * @return string
     */
    public function longDate(bool $year = false, bool $time = false): string
    {
        $result = $this->day();
        $result .= ' ' . $this->month();

        if ($year) {
            $result .= ' ' . $this->dateTime->format('Y');
        }
        if ($time) {
            $result .= ' ' . $this->shortTime(false);
        }

        return $result;
    }

    /**
     * Получение которого отображения даты
     *
     * День недели, число, 3 буквы месяца
     *
     * @param bool $day
     * @return string
     */
    public function shortDate(bool $day = false): string
    {
        $result = '';

        if ($day) {
            $formatN = $this->dateTime->format('N') * 1 - 1;

            if (isset(self::SHORT_DAY[$formatN])) {
                $result .= self::SHORT_DAY[$formatN] . ', ';
            }
        }

        $result .= $this->dateTime->format('j') * 1;
        $monthNumber = $this->dateTime->format('n') * 1 - 1;
        $result .= ' ' . mb_strtolower(self::SHORT_MONTH[$monthNumber]);

        return $result;
    }

    /**
     * Получение короткой записи времени
     *
     * Опц. день недели, время
     * Например 'Пн, 12:01'
     *
     * @param bool $day
     * @return string
     */
    public function shortTime(bool $day = true): string
    {
        $result = '';

        if ($day) {
            $formatN = $this->dateTime->format('N') * 1 - 1;

            if (isset(self::SHORT_DAY[$formatN])) {
                $result .= mb_convert_case(self::SHORT_DAY[$formatN], MB_CASE_TITLE) . ' ';
            }
        }

        $result .= $this->dateTime->format('H:i');

        return $result;
    }

    /**
     * Получение года или интервала годов
     *
     * @param bool $start год начала интервала, если нужен интервал вида '2014 – 2015'
     * @return string
     */
    public function year(bool $start = false): string
    {
        $result = '';

        if ($start && is_numeric($start) && $start * 1 !== $this->dateTime->format('Y') * 1) {
            $result = $start . ' – ';
        }

        $result .= $this->dateTime->format('Y');

        return $result;
    }

    /**
     * Получение дней недели в массиве
     *
     * @return array
     */
    public function getWeek(): array
    {
        $result = [];

        if ($this->dateTime) {
            $this->dateTime->setTime(0, 0, 0);
            $result['currentDate'] = $this->datetime(false);
            $result['currentDay'] = $day = (int) $this->dateTime->format('N');

            for ($i = 1; $i <= 7; $i++) {
                $date = clone $this;
                $diff = $i - $day;
                $result['list'][$i] = $date->modify($diff . ' day');
            }

            $dateClone = clone $this;
            $result['prev'] = (string) $dateClone->modify(-6 - $this->dateTime->format('N'))->longDate();
            $result['prevDate'] = $dateClone->datetime(false);
            $result['prev'] .= ' – ' . (string) $dateClone->modify(6)->longDate();
            $result['current'] = (string) $dateClone->modify(1)->longDate();
            $result['current'] .= ' – ' . (string) $dateClone->modify(6)->longDate();
            $result['next'] = (string) $dateClone->modify(1)->longDate();
            $result['nextDate'] = $dateClone->datetime(false);
            $result['next'] .= ' – ' . (string) $dateClone->modify(6)->longDate();
        }

        return $result;
    }
}
