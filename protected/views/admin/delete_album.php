<script type="text/javascript">
    $(document).ready(function(){
        $("#tabDeleteAlbums a").click(function(){
            if (confirm('Вы действительно хотите удалить альбом?'))
                if (confirm('При удалении альбома также удаляются пазлы входящие в его состав!'))
                    return true;

            return false;
        });
    });
</script>
<?php
$this->breadcrumbs=array(
    'Admin Options' => '/admin',
    'Delete Album',
);?>
<table style="width:100%;">
    <tr>
        <td style="width:250px;background-color:#ffffee;vertical-align:top;">
            <?$this->widget('menuAdmin')?>
        </td>
        <td style="vertical-align:top;">
            <?=$this->renderPartial('_form_albums', array(
                'dataProvider'=>$dataProvider,
                'action' => 'deletealbum',
                'title' =>  'уд.',
            ));?>
        </td>
    </tr>
</table>