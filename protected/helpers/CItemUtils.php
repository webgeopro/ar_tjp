<?php
/**
 * Date: 15.02.13
 * Формирование url, очистка title
 * Применяется при миграции
 */
class CItemUtils
{

    /**
     * Очистка title и формирование componentUrl
     *
     * @param string $title
     * @param bool $getOne
     * @return bool
     */
    public static function clearTitle($title='', $getOne=false)
    {
        $str = strip_tags(trim($title)); // Обрезка пробелов в начале и конце строки
        $normalizeChars = array( // Массив подстановок
            'Á'=>'A', 'À'=>'A', 'Â'=>'A', 'Ã'=>'A', 'Å'=>'A', 'Ä'=>'A', 'Æ'=>'AE', 'Ç'=>'C',
            'É'=>'E', 'È'=>'E', 'Ê'=>'E', 'Ë'=>'E', 'Í'=>'I', 'Ì'=>'I', 'Î'=>'I', 'Ï'=>'I', 'Ð'=>'Eth',
            'Ñ'=>'N', 'Ó'=>'O', 'Ò'=>'O', 'Ô'=>'O', 'Õ'=>'O', 'Ö'=>'O', 'Ø'=>'O',
            'Ú'=>'U', 'Ù'=>'U', 'Û'=>'U', 'Ü'=>'U', 'Ý'=>'Y',

            'á'=>'a', 'à'=>'a', 'â'=>'a', 'ã'=>'a', 'å'=>'a', 'ä'=>'a', 'æ'=>'ae', 'ç'=>'c',
            'é'=>'e', 'è'=>'e', 'ê'=>'e', 'ë'=>'e', 'í'=>'i', 'ì'=>'i', 'î'=>'i', 'ï'=>'i', 'ð'=>'eth',
            'ñ'=>'n', 'ó'=>'o', 'ò'=>'o', 'ô'=>'o', 'õ'=>'o', 'ö'=>'o', 'ø'=>'o',
            'ú'=>'u', 'ù'=>'u', 'û'=>'u', 'ü'=>'u', 'ý'=>'y',

            'ß'=>'sz', 'þ'=>'thorn', 'ÿ'=>'y',
            '&'=>' and '
        );
        $str = strtr($str, $normalizeChars); // Замена некорректных символов из массива подстановок
        $str = preg_replace("/([\s]+)|([\^\-\+\_]+)/", '-', $str); // Все пробелы (один и более) на дефис '-'
        $str = preg_replace('/[^\w\d\-]+/i', '', $str); // Убираем все, кроме букв, цифр, знаков подчеркивания '_' и дефиса '-'
        $str = preg_replace('/[\-]+/', '-', $str); // Убираем все, кроме букв, цифр, знаков подчеркивания '_' и дефиса '-'
        $str = preg_replace('/\-$/', '', $str); // Убираем дефис в конце предложения

        $componentUrl = trim($str); //mysql_real_escape_string()
        $title = trim($title); // Записываем title

        if (empty($componentUrl) OR !preg_match('/[^\-]+/', $componentUrl)) // Пуст по причине некорректных символов или отличных от Latin1
            $componentUrl = md5(date('His')); // Hid

        if ($getOne) return $componentUrl; // Если необходим только url, отдаем его и завершаем скрипт

        // Проверка на уникальность (Incrementator  в случае существования подобного)
        return self::uniqueUrl($componentUrl, $title);
    }

    /**
     * Проверка уникальности componentUrl
     *
     * @param $url
     * @param $title
     * @return array(title, componentUrl)
     */
    public function uniqueUrl($url, $title)
    {
        $albums = array();
        $tmp = array(); // Массив альбомов, содержащих этот пазл
        die('В процессе разработки');
        foreach($albums as $alb) // Список альбомов к которым принадлежит пазл
            $tmp[] = $alb['album_id'];

        $albumStatement = '';
        if (count($tmp)) { // Есть непустой массив альбомов, содержащих этот пазл
            $inAlbums = '('.implode(',', $tmp).')';
            $albumStatement = '( ai.album_id IN '.$inAlbums.' ) AND ';
        }
        //todo Если не Admin, тогда $albumStatement - проверка есть у user такой componentUrl
        $conn = Yii::app()->db; // Соединение с базой для DAO
        $sql = '
            SELECT i.componentUrl, SUBSTRING_INDEX(i.componentUrl, "-", -1) rem
            FROM item i
            LEFT JOIN album_item ai
              ON ai.item_id = i.id
            WHERE '.$albumStatement.' ( i.componentUrl REGEXP :url ) '.($this->isNewRecord?'':'AND i.id <> '.$this->id).'
            ORDER BY (rem+0) DESC
            LIMIT 1
        ';
        $sqlStrict = '
            SELECT COUNT(i.componentUrl) FROM item i
            LEFT JOIN album_item ai
              ON ai.item_id = i.id
            WHERE '.$albumStatement.' ( i.componentUrl = :urlStrict ) '.($this->isNewRecord?'':'AND i.id <> '.$this->id).'
        ';
        $com = $conn->createCommand($sql);            // Подготовка зпроса
        $comStrict = $conn->createCommand($sqlStrict);// Подготовка зпроса
        $com->bindValue(':url', '^'.$url.'-[[:digit:]]+$', PDO::PARAM_STR); // Подстановка шаблона с именем
        $comStrict->bindValue(':urlStrict', $url, PDO::PARAM_STR);          // Строгая подстановка имени

        $itemCnt = $comStrict->queryScalar(); // Число. Строгое соответствие (именно это слово)
        $itemUrl = $com->queryScalar();       // Title (string). REGEXP ('^{{url}}-[[:digit:]]+$')
        //$itemUrl = $this->getLastRecord($itemUrl);
        $urlInc = substr($itemUrl, strlen($url)); //echo $urlInc; // Остаток строки (-128)
        $urlIncDigital = abs((int)$urlInc); //echo ' :: '.$urlIncDigital;// Остаток строки, приведенный к положительному числу
        if ($itemCnt) // Такой точно componentUrl существует
            $this->componentUrl = $url.'-'.++$urlIncDigital; // Инкремент цифрового остатка
        else
            $this->componentUrl = $url; // Уникальное имя

        return array($this->title, $this->componentUrl);
    }


    /**
     * Простое копирование componentUrl.
     * Если пустое значение - получаем уникальное md5
     *
     * @param $url
     * @return mixed
     */
    public static function componentUrl($url)
    {
        $componentUrl = $url;
        if ('' == $componentUrl)
            $componentUrl = md5(date('His'));

        return $componentUrl;
    }

    /**
     * Получение альбома в котором размещен пазл
     * @param $id
     * @param string $fields
     */
    public static function getAlbum($id, $fields='a.*')
    {
        return Yii::app()->db
            ->createCommand('
                SELECT ' . $fields . ' FROM album a
                LEFT JOIN album_item ai
                  ON ai.album_id = a.id
                WHERE ai.item_id = :itemID
                ORDER BY id DESC LIMIT 1
            ')
            ->bindParam(':itemID', $id, PDO::PARAM_INT)
            ->queryRow();
    }

    /**
     * Разбивка ключевых слов и помещение в массив
     * Происход в модели ItemAttributes автоматически после поиска
     */
    public static function afterFindAttr($keywords)
    {
        $arKeywords = array();

        if ($keywords) { // Преобразуем ссылки по ключевым словам
            $kw = explode(',', $keywords);
            if (count($kw))
                foreach ($kw as $k)
                    $arKeywords[] = array(
                        urlencode(str_ireplace(' ', '+', $k)).Yii::app()->params['keySuffix'], $k
                    );
        }
        return $arKeywords;
    }

    /**
     * Проверка является ли пазл только что загруженным (первое ред-е после загрузки файла)
     *
     * @param array $item Пазл
     * @return bool
     */
    public static function isNewPuzzle($item)
    {
        return (isset($item['inSearch']) AND 2 == $item['inSearch']) // Признак нового пазла
            ? true
            : false;
    }
}
