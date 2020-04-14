<?=$this->renderPartial('_form_albums', array(
    'dataProvider'=>$dataProvider,
    'action' => 'editalbum',
    'title' =>  'ред.',
    'href'  =>  true,
));?>