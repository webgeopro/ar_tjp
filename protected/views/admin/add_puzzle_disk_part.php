<?$this->breadcrumbs=array('New Options', 'Add Puzzles',); // Добавление пазла. Локальный диск ?>
<script language="JavaScript">
    /*$(document).ready(function(){
        $("#btnFileSend").live('click', function(){
            $.post();

            return false;
        });
    });*/
</script>
<h1>Add Puzzles from disk</h1>

<form id="form1" name="form1" action="/admin/makeapuzzle" method="post" enctype="multipart/form-data">
    <?
    echo CHtml::hiddenField(Yii::app()->request->csrfTokenName, Yii::app()->request->csrfToken );
    echo CHtml::hiddenField('adminAction', 'filesend');
    if (isset($album)) {
        if (isset($album['componentUrl']))
            echo CHtml::hiddenField('album', (Yii::app()->params['userAlbumID'] == $album['parent_id'])
                    ? Yii::app()->params['userAlbumName'] .'/'. $album['componentUrl']
                    : $album['componentUrl']
                 );
        elseif (is_string($album))
            echo CHtml::hiddenField('album', $album);
    }
    echo CHtml::fileField('Filedata');
    echo CHtml::submitButton('Отправить', array('id'=>'btnFileSend'));
    ?>
</form>
<?/* Отправка формы после успешной AJAX-загрузки
<form id="formUploadedFile" name="formUploadedFile" method="post" action="/service/edit-puzzles">
    <div id="divUploadedFile">
        <input type="hidden" id="cntUploadedFile" name="cntUploadedFile" value="0">
    </div>
</form>
*/?>