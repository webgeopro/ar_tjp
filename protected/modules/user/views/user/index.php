<script type="text/javascript">
    $(document).ready(function(){
        $(".aAlbumDelete").live('click', function(){
            if (confirm('Вы действительно хотите удалить альбом пользователя?'))
                $.post(this.href, {}, function(){
                    alert('Альбом удален.');
                });

            return false;
        });
    });
</script>
<?php
    $this->breadcrumbs=array('Users',);
    function getNextType($data) // Получение следующего типа статуса (противоположного)
    {
        #switch ($data->arTypes[$data->status][1])
        #    case
        die($this->arTypes);
    }
?>
<h1>Users</h1>
<table style="width:100%;">
    <tr>
        <td style="width:250px;vertical-align:top;">
            <?$this->widget('menuAdmin', array('currentPage'=>$this->action->id))?>
        </td>
        <td style="0vertical-align:top;">
            <?#die(print_r($arTypes))?>
            <?$this->renderPartial('_search',array('model'=>$model,))?>
            <div id="divAdminContent"><?#='$content'?>
                <?php $this->widget('zii.widgets.grid.CGridView', array(
                    'dataProvider'=>$model->searchAdmin(),
                    #'enablePagination' => true,
                    #'enableHistory'=>true,
                    'ajaxUpdate' => false,
                    'pager' => array(
                        'header' => '', // Подпись к пагинатору
                        'actionPrefix' => 'page',
                        'pageSize' => 5,
                    ),
                    'columns'=>array(
                        array(
                            'header' => 'Username',
                            'name' => 'username',
                            'type'=>'raw',
                            'value' => 'CHtml::link(
                                            CHtml::encode($data->username),
                                            array("profile/admin","id"=>$data->id, "User_page"=>isset($_GET["User_page"])?$_GET["User_page"]:1),
                                            array("class"=>"aProfileEdit","name"=>$data->id)
                                        )',
                            'sortable' => true,
                        ),
                        array(
                            'header' => '&nbsp;',
                            'type'=>'raw', //http://thejigsaw/User-Albums/demo-jigsaw-puzzle
                            'value' => 'CHtml::link(
                                            "<img src=\'/images/stickers.png\'>",
                                            "/User-Albums/"
                                                . CUserAlbums::getUserAlbumNameFromUserId($data->id, $data->username)
                                                . Yii::app()->params["urlSuffix"],
                                            array("target"=>"_blank")
                                        )',
                            'headerHtmlOptions' => array('style'=>'width:22px'),
                        ),
                        /*array( // display a column with "view", "update" and "delete" buttons
                            'class'=>'CButtonColumn',
                            'template'=>'{delete}',
                            'viewButtonLabel'=>'Delete User Album',
                            //'deleteConfirmation' => 'Вы действительно хотите удалить альбом пользователя?',
                            'headerHtmlOptions' => array('style'=>'width:13px;padding:0;margin:0;','class'=>'deleteAlbum'),
                            'htmlOptions' => array('style'=>'width:13px;padding:0;margin:0;'),
                            'deleteButtonUrl' => 'Yii::app()->controller->createUrl("/admin/deletealbum",array("id"=>$data->primaryKey,"userAlbum"=>1))',
                        ),*/
                        array(
                            'name' => 'createtime',
                            'value' => 'date("d.m.Y",$data->createtime)',
                            'filter' => false,
                        ),
                        array(
                            'name' => 'status',
                            'type'=>'raw',
                            //'value' => 'CHtml::dropDownList("status", $data->status, array("0"=>"No active","1"=>"Active","-1"=>"Banned"))',
                            'value' => '$data->arTypes[$data->status][0] . CHtml::link(
                                            CHtml::image("/images/admin/".$data->arTypes[$data->status][1].".png"),
                                            array("profile/admin","id"=>$data->id),
                                            array("class"=>"aStatusChange", "style"=>"float:right;",
                                                "title"=>$data->arTypes[$data->status][1],
                                                "name"=>$data->id."::".$data->arTypes[$data->status][1])
                                        )',
                        ),
                        array(
                            'header' => 'Email',
                            'name' => 'email',
                            'value' => '$data->email',
                            'sortable' => true,
                            'filter' => false,
                        ),
                        array(
                            'name' => 'profile.fullname',
                            'header' => 'Fullname',
                            'value' => '$data->profile->fullname',
                        ),
                        array( // display a column with "view", "update" and "delete" buttons
                            'class'=>'CButtonColumn',
                            'template'=>'{albumDelete} &nbsp; {delete}',
                            'headerHtmlOptions' => array('style'=>'width:42px;padding:0;margin:0;'),
                            'htmlOptions' => array('style'=>'padding:0;margin:0;'),
                            'deleteButtonUrl' => 'Yii::app()->controller->createUrl("/user/admin/delete",array("id"=>$data->primaryKey))',
                            'buttons' => array(
                                'albumDelete' =>array(
                                    'label' => 'Delete User Album',
                                    'imageUrl' => '/images/folder_delete.png',
                                    'url' => 'Yii::app()->controller->createUrl("/admin/deletealbum",array("id"=>$data->primaryKey,"userAlbum"=>1))',
                                    'options' => array('class'=>'aAlbumDelete'),
                                ),
                            ),
                        ),
                        /*array(
                            'class'=>'CLinkColumn',
                            'imageUrl' => '/images/stickers.png',
                            'url' => '/User-Albums/$data->username'.Yii::app()->params["urlSuffix"],
                            'linkHtmlOptions' => array('target'=>'_blank'),
                        ),*/
                    ),
                )); ?>
            </div>
        </td>
    </tr>
</table>