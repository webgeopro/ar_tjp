<?php
/*$this->breadcrumbs=array(
    empty($this->album->componentUrl)
        ? (empty($this->album['componentUrl'])
            ? 'Admin Options'
            : $this->album['componentUrl'])
        : ($this->album->title ? $this->album->title : $this->album->componentUrl),
    'Add Puzzles',
);*/
$this->breadcrumbs=array(
    CHtml::decode($this->album['title']) => CHtml::decode("/{$this->album['componentUrl']}".Yii::app()->params['urlSuffix']),
    'Add Puzzles',
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
            ));?>
            <div id="divAdminContent"><?=(empty($content)?:$content)?></div>
        </td>
    </tr>
</table>