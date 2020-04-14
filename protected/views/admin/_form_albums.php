<table style="margin-top:15px;" id="tabDeleteAlbums">
    <thead><tr>
        <th>&nbsp;</th>
        <th>Title</th>
        <th>Component Url</th>
        <th>Items</th>
        <th>&nbsp;</th>
    </tr></thead>
    <?php $this->widget('zii.widgets.CListView', array(
        'dataProvider'=>$dataProvider,
        'itemView'=>'_view_album',
        'viewData' => array(
            'action' => $action,
            'title'  => $title,
            'href'   => @$href,
        ),
    ));?>
</table>