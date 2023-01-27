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
    private const DAY = ['Понедельник', 'Вторник', 'Среда', 'Четверг', 'Пятница', 'Суббота', 'Воскресенье'];
    private const SHORT_DAY = ['Пн', 'Вт', 'Ср', 'Чт', 'Пт', 'Сб', 'Вс'];

    private const DATETIME = 'Y-m-d H:i:s';
    private const DATE = 'Y-m-d';
    private const EUR_DATETIME = 'd.m.Y H:i:s';

    private function __construct(?string $date)
    {
        mb_internal_encoding('UTF-8');

        if (is_string($date)) {
            $this->dateTime = new DateTime($date);
        } else {
            $this->dateTime = new DateTime();
        }
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
    public static function getInstance(?string $date): self
    {
        return new self($date);
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
        $time = match ($dateFormat) {
            self::DATETIME,
            self::EUR_DATETIME => false,
            default => true
        };

        $result = $this->dateTime->format($dateFormat);

        if ($time === true) {
            $result .= ' ' . $this->dateTime->format('H:i:s');
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
     * Получение месяца
     *
     * @param bool $plural
     * @return string
     */
    public function month(bool $plural = true): string
    {
        $result = '';
        $monthNumber = (int) $this->dateTime->format('n') - 1;

        if ($plural === true) {
            $result .= ' ' . mb_convert_case(self::MONTH_PLURAL[$monthNumber], MB_CASE_TITLE);
        } else {
            $result .= ' ' . mb_convert_case(self::MONTH[$monthNumber], MB_CASE_TITLE);
        }

        return $result;
    }

    /**
     * Получение словесного отображения даты
     *
     * Пример:
     * - Сегодня
     * - Вчера
     * - Завтра
     * - 27 января 2023
     * - 27 января 2023 23:57 (_$time = true_)
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

        if ($time === true) {
            $result .= ' ' . $this->shortTime(false);
        }

        return $result;
    }

    /**
     * Получение длинного отображения даты текстом
     *
     * Пример:
     * - 27 января
     * - 27 января 2023 (_$year = true_)
     * - 27 января 21:59 (_$time = true_)
     * - 27 января 2023 21:59 (_$year = true, $time = true_)
     *
     * @param bool $year
     * @param bool $time
     * @return string
     */
    public function longDate(bool $year = false, bool $time = false): string
    {
        $result = $this->day() . ' ' . $this->month();

        if ($year === true) {
            $result .= ' ' . $this->dateTime->format('Y');
        }

        if ($time === true) {
            $result .= ' ' . $this->shortTime(false);
        }

        return $result;
    }

    /**
     * Получение короткой записи времени
     *
     * Пример:
     * - 22:00
     * - ПТ 22:00 (_$day = true_)
     *
     * @param bool $day
     * @return string
     */
    public function shortTime(bool $day = true): string
    {
        $result = '';

        if ($day === true) {
            $result .= $this->dayWeek(true) . ' ';
        }

        $result .= $this->dateTime->format('H:i');

        return $result;
    }

    /**
     * Получить день недели
     *
     * Пример:
     * - Пятница
     * - ПТ (_$short = true_)
     *
     * @param boolean $short
     * @return string
     */
    public function dayWeek(bool $short = false): string
    {
        $formatN = $this->dateTime->format('N') * 1 - 1;

        if ($short == true) {
            return mb_convert_case(self::SHORT_DAY[$formatN], MB_CASE_TITLE) . ' ';
        } else {
            return mb_convert_case(self::DAY[$formatN], MB_CASE_TITLE);
        }
    }
}
