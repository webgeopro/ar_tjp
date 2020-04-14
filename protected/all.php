<?php
    set_time_limit(0);
    $cr = chr(10);
    
    echo $cr. 'Start'. $cr . 'UM Start';
    #echo `yiic um > um.log` . $cr. ' UM finished'. $cr . 'UM Pending Start';
    #echo `yiic um pending > um_p.log` . $cr. ' UM Pending finished'. $cr . 'AM Start';
    #echo `yiic am > am.log` . $cr. ' AM finished'. $cr . 'IM SELECT Start';
    
    echo `yiic im select > im_select.log` . 'IM SELECT finished'. $cr . 'IM INSERT Start';
    #echo `yiic im insert > im_insert.log` . 'IM INSERT finished'. $cr . 'IM CORRECT Start';
    #echo `yiic im correct > im_correct.log` . 'IM CORRECT finished'. $cr . 'CI Start';
    //echo `./yiic im > im.log` . $cr. ' IM finished'. $cr . 'CI Start';
    
    #echo `yiic ci > ci.log` . $cr. ' CI finished'. $cr . 'CI Main Start';
    #echo `yiic ci main > ci_main.log` . $cr. ' CI Main finished'. $cr . 'CI User Start';
    #cho `yiic ci user > ci_user.log` . $cr. ' CI User finished'. $cr . 'Correct Main Start';
    #echo `yiic correct main > correct_main.log` . $cr. ' Correct Main finished' . $cr . 'Correct Size Start';
    #echo `yiic correct size > correct_size.log` . $cr. ' Correct Size finished' . $cr . 'Url Start';
    #echo `yiic url > url.log` . $cr. ' URL finished' . $cr . 'Final ExtLinks Start';
    #echo `yiic final extlinks > final_links.log` . $cr. ' Final ExtLinks finished' . $cr . 'Final Thumbs Start';
    #echo `yiic final thumbs > final_thumbs.log` . $cr. ' Final Thumbs finished' . $cr . 'Final DatePublished Start';
    #echo `yiic final datepublished > final_datepublished.log` . $cr. ' Final DatePublished finished' . $cr . 'Final UpdateInSearch Start';
    #echo `yiic final updateinsearch > final_updateinsearch.log` . $cr. ' Final UpdateInSearch finished' . $cr . 'Final SetAdmin Start';
    #echo `yiic final setadmin > final_setadmin.log` . $cr. ' Final SetAdmin finished';

    #echo $cr. ' Chown Start' . `chown -R apache:apache /var/www/test` . $cr . 'Chown finished';

    echo $cr. ' All operations finished.';