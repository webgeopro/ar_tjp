<?php
$this->breadcrumbs=array(
    empty($this->album->componentUrl)?'Admin Options':$this->album->componentUrl,
    'Edit Album',
);?>
<table style="width:100%;">
    <tr>
        <td style="width:250px;background-color:#ffffee;vertical-align:top;">
            <?$this->widget('menuAdmin')?>
        </td>
        <td style="vertical-align:top;">
            <?$this->widget('menuAdmin', array(
                'currentPage' => $this->action->id,
                'contentMenu' => true,
            ));?>
            <br style="clear:both;" />
            <?if (Yii::app()->user->hasFlash('editTitle')):?>
                <br />
                <h3>Album attribute saved: <?=Yii::app()->user->getFlash('editTitle')?>.</h3>
            <?endif?>
            <?if (Yii::app()->user->hasFlash('sortOrder')):?>
                <br /><h3><?=Yii::app()->user->getFlash('sortOrder')?>.</h3>
            <?endif?>
            <div id="divAdminContent"><?=(empty($content)?:$content)?></div>
        </td>
    </tr>
</table>