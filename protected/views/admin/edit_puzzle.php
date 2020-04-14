<?php
if (null == $this->breadcrumbs)
    $this->breadcrumbs=array(
        'Admin Options',
        'Edit Puzzle',
    );
?>
<table style="width:100%;">
    <tr>
        <td style="width:250px;background-color:#ffffee;vertical-align:top;">
            <?$this->widget('menuAdmin')?>
        </td>
        <td style="vertical-align:top;">
            <?$this->widget('menuAdmin', array(
                'currentPage' => $this->action->id,
                'contentMenu' => true,
                //'currentAction' => 'index',
            ))?>
            <div id="divAdminContent">
                <?if (Yii::app()->user->hasFlash('mainSuccess')):?><h3><?=Yii::app()->user->getFlash('mainSuccess')?></h3><?endif?>
                <?if (Yii::app()->user->hasFlash('attrSuccess')):?><h3><?=Yii::app()->user->getFlash('attrSuccess')?></h3><?endif?>
                <?if (Yii::app()->user->hasFlash('blockSuccess')):?><h3><?=Yii::app()->user->getFlash('blockSuccess')?></h3><?endif?>
                <?=$content?>
            </div>
        </td>
    </tr>
</table>
