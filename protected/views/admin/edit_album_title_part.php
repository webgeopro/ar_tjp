<h1>Edit album</h1>

<div class="form">
    <?$form=$this->beginWidget('CActiveForm', array(
        'id'=>'album-form',
        'enableAjaxValidation'=>true,
        'enableClientValidation'=>true,
    ));
    echo $form->hiddenField($this->album, 'id')?>

    <h2>Component Url</h2>
    <div class="row">
        <?=$this->album->title?><br />
        <?=$form->textField($this->album,'componentUrl', array('class' => 'formInput',)); ?>
        <?=$form->error($this->album,'componentUrl'); ?>
    </div>

    <h2>Title</h2>
    <div class="row">
        <?=$form->textField($this->album,'title', array('class' => 'formInput',)); ?>
        <?=$form->error($this->album,'title'); ?>
    </div>

    <h2>Keywords (<span id="spKeywords"><?=(255-strlen($this->album->keywords))?></span>)</h2>
    <div class="row">
        <?=$form->textArea($this->album,'keywords', array(
            'class' => 'formInput','cols' => 60, 'rows' => 2, 'id' => 'inpKeywords'
        ))?>
        <?=$form->error($this->album,'keywords'); ?>
    </div>

    <div class="gbBlock gcBackground1">
        <input type="submit" value="Save" name="Album[action][edit]" class="inputTypeSubmit">
    </div>

<?$this->endWidget()?>