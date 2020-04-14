<?if (count($actions)):?>
    <select id="selGetActions" onchange="return getFunc(this)">
        <option value=""> « item actions » </option>

        <?foreach($actions as $key=>$val): @$i++;?>
        <?if (is_array($val)):?>
            <option value="#<?=$val[0]?>" title="<?=$val[1]?>"><?=$key?></option>
        <?else:?>
            <option value="<?=$val?>"><?=$key?></option>
        <?endif?>
        <?endforeach?>

    </select>
<?endif?>
<?/*
 * <option value="<?=$val[0]?>" onclick="return toPage(this, <?=$val[1]?>)"><?=$key?></option>
   <option value="<?=$val?>" onclick="javascript: return doAction(this)"><?=$key?></option>
 * */?>