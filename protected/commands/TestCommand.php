<?php
/**
 * Tests.
 * Консольная команда.
 * Различные тесты: целостность, пустые изображения и т.д.
 * Date: 15.02.13
 */
class TestCommand extends CConsoleCommand
{
    public $errors = array(); // НЕ используется
    public $defaultAction = 'index'; // Действие по умолчанию.
    public $userAlbumNumber = 7298; // Фиксированный ID корня для пользовательских альбомов
    public $save = false;

    /**
     * Дефолтное действие
     */
    public function actionIndex()
    {
        // Прогон всех тестов
        echo "Choose actions: \n
            mp: Main albums puzzles  \n
            up: Users albums puzzles \n
        ";
    }

    /**
     * Работа с основными пазлами (добавленными администраторами)
     */
    public function actionMain()
    {
        echo chr(10).'Start Main Test.'.chr(10).'Step 1. Select all puzzles';
        $items = $this->getPuzzles(); // Получение списка основных пазлов

        echo chr(10).'Step 2. Processing';
        foreach($items as $item)
            $this->issetImage($item);

        echo chr(10).'Complete. '.count($items).' items.';
    }

    /**
     * Проверка корректности componentUrl
     */
    public function actionUrl()
    {
        echo chr(10).'Start Url Test.'.chr(10).'Step 1. Select all puzzles';
        $items = $this->getPuzzles('user'); // Получение списка всех пазлов

        echo chr(10).'Step 2. Processing';
        foreach($items as $item) {
            $componentUrl = $item['componentUrl'];
            if (empty($componentUrl) OR !preg_match('/[^\-, ]+/', $componentUrl))
                $this->errors[$item['id']] = array('urlError', $componentUrl);
        }
        echo chr(10).'Complete. '.count($items).' items.';
    }


    /**
     * Пост обработка
     */
    public  function afterAction($action,$params)
    {
        if (count($this->errors)) // Если сформирован массив ошибок
            if ($this->save) { // Сохранить на диск

            } else {
                echo chr(10).'Count Errors: '.count($this->errors).chr(10);
                foreach ($this->errors as $id=>$err) {
                    list($path, $fullPath) = $err;
                    echo chr(10).$id."::".$path."::".$fullPath;
                }
            }
        else echo chr(10)."No errors";
    }


    /**
     * Существуют ли изображения на диске.
     *
     * @param $item
     * @return bool
     */
    private function issetImage($item)
    {
        list($imgFullName, $imgUrl) = CImageSize::getPath($item['id']);
        $prefix = Yii::app()->params['pathOS'];
        $suffix = '/'.$imgUrl.'/'.$imgFullName.'.jpg';
        $paths = array('pathOriginal', 'pathWorked', 'pathThumbnail'); //'pathSource', + исходник

        foreach($paths as $path) { // Проходим по всем ресайзам
            $source = $prefix.Yii::app()->params[$path].$suffix;
            if (!file_exists($source)) {
                $this->errors[$item['id']] = array($path, $source);
            }
        }
        return true;
    }

    /**
     * Получение списка пазлов
     *
     * @param bool $user
     * @return mixed
     */
    private function getPuzzles($user=false)
    {
        $conn = Yii::app()->db; // Используем соединения для локального сервера

        switch ($user) {
            case 'user': // Only Users puzzles
                $where = 'WHERE (album.parent_id = '.$this->userAlbumNumber.')';
                break;
            case 'all': // Main + Users puzzles
                $where = '';
                break;
            default: // Only Main albums puzzles
                $where = 'WHERE (album.parent_id = 0 AND album.id <> '.$this->userAlbumNumber.')';
        }
        $sql = '
            SELECT item.*, item_attributes.author, album.componentUrl albumComponentUrl
            FROM album
            LEFT OUTER JOIN album_item
                ON (album.id = album_item.album_id)
            LEFT OUTER JOIN item
                ON (item.id = album_item.item_id)
            LEFT OUTER JOIN item_attributes
                ON (item.id = item_attributes.id)
            '.$where.'
            GROUP BY item.id
            ORDER BY item.dateCreated DESC, item.id DESC
        ';
        $command=$conn->createCommand($sql);
        $items = $command->queryAll();

        return $items;
    }
}