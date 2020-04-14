<?if($this->contentMenu): // Отображаем верхнее контекстное меню ?>

    <script type="text/javascript">
        var get=location.search,param=[];if(""!=get){tmp=get.substr(1).split("&");for(var i=0;i<tmp.length;i++)tmp2=tmp[i].split("="),param[tmp2[0]]=tmp2[1]};
        $(document).ready(function(){
            $("ul.admin-menu li a[name]").click(function(){$("#divAdminContent").load(this.href,{pageLabel:$(this).attr("name"),item:param.item,album:param.album});return!1});
        });
    </script>

    <ul class="admin-menu">
    <?foreach($list as $key=>$menu):?>
        <li><?#=print_r($list)?><a href="<?=$address?>" name="<?=$key?>"><?=$menu?></a></li>
    <?endforeach?>
    </ul>

<?else: // Отображаем левое меню ?>

    <ul class="admin-menu-left">
    <?foreach($this->menuList as $key=>$menu):?>
        <?if ($menu[3]):?>
        <li><a href="<?=$menu[1]?><?=$key?>"><?=$menu[0]?></a></li>
        <?endif?>
    <?endforeach?>
    </ul>

<?endif?>