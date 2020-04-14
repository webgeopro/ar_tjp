<?php
/**
 * User: Vah
 * Date: 01.07.13
 * Пагинатор под flash-сборкой пазла.
 */

class FlashPager extends CWidget {

    public $item;        // Пазл, отображаемый в данный момент
    public $album;       // Альбом, содержащий пазл
    public $res=array(); // Пазля для пагинатора [first, last, current, prev, next]

    public function run()
    {
        if (null != $this->item) {
            /*$album =  (null == $this->album)
                ? CItemUtils::getAlbum($this->item['id'], 'a.id, a.componentUrl, a.parent_id')
                : array(
                    'id'           => $this->album['id'],
                    'componentUrl' => $this->album['componentUrl'],
                    'parent_id'    => $this->album['parent_id'],
                );*/
            $album = CItemUtils::getAlbum($this->item['id'], 'a.id, a.componentUrl, a.parent_id');

            if (null == $album) return null;

            //$itemID = $this->item['id'];
            $siblings = Yii::app()->db
                ->createCommand('
                    SELECT item.id, item.componentUrl, item.title
                    FROM item
                    LEFT JOIN album_item ai
                      ON ai.item_id = item.id
                    WHERE ai.album_id = :albumID
                    ORDER BY item.id')
                ->bindParam(":albumID", $album['id'], PDO::PARAM_INT)
                ->queryAll(); //OFFSET  AND (item.id >= :itemID OR  item.id <= :itemID)

            if ($cnt = count($siblings)) {
                $this->res = array(
                    'first' => $siblings[0],
                    'last'  => $siblings[--$cnt],
                );
                foreach ($siblings as $key=>$val) {
                    if ($this->item['id'] == $val['id']) { //$itemID
                        $this->res['current'] = $siblings[$key];
                        $this->res['prev'] = isset($siblings[$key-1]) ? $siblings[$key-1] : null;
                        $this->res['next'] = isset($siblings[$key+1]) ? $siblings[$key+1] : null;
                        break;
                    }
                }
            }
            unset($siblings);

            $this->render('flashPager', array(
                'albumName' => Yii::app()->params['userAlbumID'] == $album['parent_id']
                    ? Yii::app()->params['userAlbumName'] .'/'. $album['componentUrl']
                    : $this->album['componentUrl'],
            ));
        }
    }
}