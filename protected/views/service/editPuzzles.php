<?$this->breadcrumbs=array(
    //$album['componentUrl'] => '/'.$album['componentUrl'].Yii::app()->params['urlSuffix'],
    //$item['componentUrl']=> '/'.$album['componentUrl'].'/'.$item['componentUrl'].Yii::app()->params['urlSuffix'],
    'Make a puzzle' => '/makeapuzzle',
    'Edit puzzles',
);?>

<form action="" method="post" enctype="multipart/form-data" id="itemAdminForm">
    <?=$content?>
    <div class="gbBlock gcBackground1" style="padding-top:20px;">
        <table class="gcBackground1" width="100%" cellspacing="0" cellpadding="0">
            <tr valign="top">
                <td width="20%">&nbsp;</td>
                <td>
                    <input type="submit" class="inputTypeSubmit" name="item[change]" value="Change"/>
                    <!--Note: Any rotations must be undone manually.-->
                </td>
            </tr>
        </table>
    </div>
</form>