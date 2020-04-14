<?php
$this->breadcrumbs=array(
	'Admin Options',
);?>
<h1>Admin Options</h1>
<table style="width:100%;">
    <tr>
        <td style="width:250px;vertical-align:top;">
            <?$this->widget('menuAdmin', array('currentPage'=>$this->action->id))?>
        </td>
        <td style="vertical-align:top;">
            <?$this->widget('menuAdmin', array('currentPage'=>$this->action->id, 'contentMenu'=>true))?>
            <div id="divAdminContent"><?=(empty($content)?:$content)?></div>
        </td>
    </tr>
</table>