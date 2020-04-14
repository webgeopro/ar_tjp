<?php

class SearchController extends Controller
{
	public $albumUrl = null;  // Со страницы какого альбома пришли
    public $itemUrl  = null;  // Со страницы какого пазла пришли
    public $keywords = array(); // Поиск по ключевым словам
    public $page = 1; // Текущая страница пагинатора

    private $conn = null; // Соединение с базой

    public $inTitle    = true; // Искать среди названий        Default: true
    public $inAuthor   = false;// Искать среди авторов         Default: false
    public $inKeywords = true; // Искать среди ключевых слов   Default: true

    private $num = 5; // Количество объектов в строку
    private $limit = 20; // Количество объектов на странице
    public $albumUser = null;

    public $cutout; // Нарезка

    /**
     * Инициализация
     */
    public function init()
    {
        $this->conn = Yii::app()->db; // Используем соединения для локального сервера.
    }

    /**
     * Действие по умолчанию
     */
    public function actionIndex()
	{
        if (empty($this->keywords)) { // Отобразить пустую страницу поиска
            $objects = array();

        } else { // Обработка введенных слов
            if ($this->inKeywords) // Если указан поиск по ключевым словам
                $where[] = $this->getSearchString('attr.keywords');
            if ($this->inTitle)    // Если указан поиск по названию
                $where[] = $this->getSearchString('item.title');
            if ($this->inAuthor)   // Если указан поиск по автору
                $where[] = $this->getSearchString('attr.author');
            $where = implode(' OR ', $where); // Объединяем в строку
            if (strlen($where))
                $where =
                    ' WHERE /*(item.inSearch = 1)'
                    . ' AND*/ ( (' . $where. ')'
                    . ' AND (item.dateCreated <= "'.date("Y-m-d").'")
                        AND (album.parent_id = 0)
                        AND (album.id <> 7298) ) ';

            $sql = '
                SELECT item.*, attr.author, album.componentUrl albumComponentUrl
                FROM item item
                LEFT JOIN item_attributes attr
                  ON item.id = attr.id
                LEFT OUTER JOIN album_item
                  ON (item.id = album_item.item_id)
                LEFT OUTER JOIN album
                  ON (album.id = album_item.album_id) '
                .$where.' LIMIT :offset, :limit';

            $comm = $this->conn->createCommand($sql); // Подготовим команду выборки
            $page = (0 < $this->page)?($this->page - 1):0;

            $offset = $page * $this->limit;

            //$comm->bindValue(':data', date("Y-m-d"));
            $comm->bindParam(':offset', $offset, PDO::PARAM_INT);
            $comm->bindParam(':limit', $this->limit, PDO::PARAM_INT);

            $objects = $comm->queryAll();
        }

        $this->render('index', array(
            'objects' => $objects,
            'itemWidth' => Yii::app()->params['thumbnailSize'][0],
            'itemHeight'=> Yii::app()->params['thumbnailSize'][1],
            'num' => $this->num,
            'tdWidth' => floor(100 / $this->num),
            'path' => Yii::app()->params['pathThumbnail'],
            'searchAddressString' => implode('%20', $this->keywords),
            'searchString' => stripslashes(implode(' ', $this->keywords)),
            'inTitle'    => $this->inTitle,
            'inAuthor'   => $this->inAuthor,
            'inKeywords' => $this->inKeywords,
            'page' => $page,
        ));//&inpSearchCriteria=street   ---  !!  a    orrr true "Select * from item where 1 fdfd
	}

    /**
     * Формирование поисковой строки для attr, author и keywords
     *
     * @param $attr
     * @return string
     */
    protected function getSearchString($attr)
    {
        return ' ('. $attr . ' LIKE "%'
            . implode('%" OR ' . $attr . ' LIKE "%', $this->keywords)
            .'%") ';
    }

    /**
     * Список накладываемых фильтров
     * @return array
     */
    public function filters()
	{
		return array(
			'parser + index', // Фильтр Parser накладывается на страницу Index
		);
	}

    /**
     * Фильтр-обработчик входных значений
     * @param $chain
     */
    public function filterParser($chain)
	{
		$r = Yii::app()->request; // Объект-запрос

        // Выводим cutot из join-а. Для этого создаем и кешируем отдельный запрос
        $this->cutout = Cutout::getCutout();

        if (!empty($_POST['albumUrl'])) $this->albumUrl = $r->getPost('albumUrl'); // Форма на страницах
        if (!empty($_POST['itemUrl']))  $this->itemUrl  = $r->getPost('itemUrl');  // Форма на страницах

        $keywords = $r->getParam('keySearchCriteria'); // Ссылка на ключевое слово
        if (!empty($keywords)) // Убираем "+" на пробелы
            $keywords = str_replace('+', ' ', $keywords);
        else
            $keywords = $r->getParam('inpSearchCriteria');

        if (!empty($keywords)) {
            // Очистка введенных слов от мусора, инъекций и т.п.
            //$p = new CHtmlPurifier();
            $keywords = preg_replace('/\s+(\S{1}|\d+)\s+/', ' ', $keywords); // Убираем все 1-буквенное
            $keywords = addslashes($keywords);   // Экранируем кавычки и спецсимволы
            //$keywords = $p->purify($keywords);
            $keywords = preg_replace('[\s+]', ' ', $keywords); // Удаляем множественные пробелы

            $this->keywords = explode(' ', $keywords); // Разрываем на слова
        }

        $this->page = $r->getParam('page', $this->page); // Номер страницы пагинатора

        $this->inAuthor   = $r->getParam('inAuthor', $this->inAuthor);      // Искать ли по автору
        $this->inTitle    = $r->getParam('inTitle', $this->inTitle);        // Искать ли названию
        $this->inKeywords = $r->getParam('inKeywords', $this->inKeywords);  // Искать ли по ключевым словам

        $chain->run();
	}

}