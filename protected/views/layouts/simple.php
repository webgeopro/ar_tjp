<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html lang="en-US" xmlns="http://www.w3.org/1999/xhtml">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<meta name="language" content="en" />

    <link rel="stylesheet" type="text/css" href="/css/theme.css"/>
    <link rel="stylesheet" type="text/css" href="/css/icons.css"/>
    <link rel="stylesheet" type="text/css" href="/css/admin.css"/>

    <script type="text/javascript" src="/js/jquery.js"></script>

    <title>TheJigsawPuzzles.com :: Admin Options</title>
</head>

<body class="gallery">

    <div id="gallery">
        <div id="gsHeader" >
            <table width="100%" cellspacing="0" cellpadding="0">
                <tr>
                    <td align="left" valign="middle">
                        <div class="gsLogo"><a href="/" onClick="return true;"></a></div>
                    </td>
                    <td width="100%" align="right" valign="top" ></td>
                </tr>
            </table>
        </div>
        <table width="100%" cellspacing="0" cellpadding="0">
            <tr>
                <td>
                    <div id="gsNavBar" class="gcBorder1">
                        <div class="gbBreadCrumb">
                            <?if(isset($this->breadcrumbs)):?>
                                <?$this->widget('zii.widgets.CBreadcrumbs', array(
                                    'homeLink' => CHtml::link('Puzzle Gallery', Yii::app()->homeUrl),
                                    'links'=>$this->breadcrumbs,
                                    'htmlOptions'=>array('class'=>'block-core-BreadCrumb'),
                                ));?>
                            <?endif?>
                        </div>
                    </div>
                </td>
            </tr>
            <tr>
                <td class="iceBackground iceBackgroundImage">
                    <?=$content?>
                    <div id="gsFooter" class="gcBorder1">
                        <table width="100%" cellspacing="0" cellpadding="0">
                            <tr>
                                <td align="left" width="50%">
                                    <?$this->widget('systemLinks')?>
                                </td>
                                <td align="right">
                                    &nbsp;
                                </td>
                            </tr>
                        </table>
                    </div>
                </td>
            </tr>
        </table>
    </div>


</div><!-- page -->

</body>
</html>
