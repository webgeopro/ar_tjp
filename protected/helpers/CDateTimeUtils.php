<?php
/**
 * Вспомогательные функции для работы с датой и временем
 * Date: 25.04.14
 */

class CDateTimeUtils
{

    /**
     * Текущая дата
     *
     * @return DateTime
     */
    public static function now()
    {
        return new DateTime();
    }

    /**
     * Вывод даты в требуемом формате
     *
     * @param DateTime $date
     * @param string $template
     * @return string
     */
    public static function format(DateTime $date, $template='Y-m-d')
    {
        return $date->format($template);
    }

    /**
     * Изменение даты
     *
     * @param DateTime $date
     * @param string $diff
     * @return DateTime
     */
    public static function modify($date=null, $diff='+1 day')
    {
        if (null == $date)
            $date = self::now();
        elseif (!is_object($date)) // Преобразуем в DateTime
            $date = self::toDateTime($date);

        return $date->modify($diff);
    }

    /**
     * Разница дат
     *
     * @param $date1
     * @param null $date2
     * @param bool $absolute
     * @return bool|void
     */
    public static function diff($date1, $date2=null, $absolute=false)
    {
        if (null == $date1 && null == $date2)
            return null;

        if (null == $date2)
            $date2 = self::now();

        if (!is_object($date1)) // Преобразуем в DateTime
            $date1 = self::toDateTime($date1);
        if (!is_object($date2)) // Преобразуем в DateTime
            $date2 = self::toDateTime($date2);

        return date_diff($date1, $date2, $absolute);

    }


    /**
     * Разница дат
     *
     * @param $date1
     * @param null $date2
     * @return bool|void
     */
    public static function diffWithoutSeconds($date1, $date2=null)
    {
        if (null == $date1 && null == $date2)
            return null;

        if (null == $date2)
            $date2 = date('Y-m-d');

        return ($date1 < $date2)
            ? true
            : false;

    }

    /**
     * Сравнение дат. Больше определенного кол-ва дней.
     *
     * @param $date1         Первый параметр date_diff()
     * @param null $date2    Второй параметр date_diff()
     * @param bool $absolute Третий параметр date_diff()
     * @param int $daysCount Разница дней для сравнения
     * @return bool
     */
    public static function diffBool($date1, $date2=null, $absolute=false, $daysCount=1)
    {
        $diff = self::diff($date1, $date2, $absolute);

        return ($daysCount <= $diff->d)
            ? true   // Разница больше $daysCount дней
            : false;
    }

    /**
     * Сравнение дат. Не учитываем H:i:s
     */
    /*public static function diffBoolWithoutSeconds($date1, $date2=null, $absolute=false)
    {//die(print_r($date2));
        $diff = self::diffWithoutSeconds($date1, $date2, $absolute);
        die(print_r($diff));
        return ($daysCount <= $diff->d)
            ? true   // Разница больше $daysCount дней
            : false;
    }*/

    /**
     * Разница дат. Булево сравнение.
     *
     * @param $date1
     * @param null $date2
     * @return bool|void
     */
    public static function diffEqual($date1, $date2=null)
    {
        if (null == $date1 && null == $date2)
            return null;

        if (null == $date2)
            $date2 = self::now();

        if (!is_object($date1)) // Преобразуем в DateTime
            $date1 = self::toDateTime($date1);
        if (!is_object($date2)) // Преобразуем в DateTime
            $date2 = self::toDateTime($date2);

        if ($date1 == $date2)
            return '0';
        elseif ($date1 > $date2)
            return '1';
        elseif ($date1 < $date2)
            return '-1';
    }

    /**
     * Преобразование даты в объект DateTime
     *
     * @param $date
     * @param bool $withoutSeconds
     * @return DateTime
     */
    public static function toDateTime($date, $withoutSeconds=false)
    {
        if (19 == strlen($date))
            if ($withoutSeconds) {
                $format =  'Y-m-d';
                $date = substr($date, 0, 10);
            } else
                $format = 'Y-m-d H:i:s';

        else//if (10 == strlen($date))
            $format = 'Y-m-d';

        return date_create_from_format($format, $date);
    }
} 