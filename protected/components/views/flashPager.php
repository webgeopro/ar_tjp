<div class="gbNavigatorBottom">
    <div>
        <table width="100%" cellspacing="0" cellpadding="0"><tbody><tr>
            <td width="20%" align="left">
                <div class="first-and-previous">
                    <?if (!empty($this->res['prev'])):?>
                    <table cellspacing="0" cellpadding="0"><tbody><tr>
                        <td>
                            <div class="buttonAction buttonFirst">
                                <a title="First" href="/<?=$albumName?>/<?=$this->res['first']['componentUrl']?>-jigsaw-puzzle"></a>
                            </div>
                        </td>
                        <td>&nbsp;</td>
                        <td>
                            <div class="buttonAction buttonPrev">
                                <a title="Previous" href="/<?=$albumName?>/<?=$this->res['prev']['componentUrl']?>-jigsaw-puzzle"></a>
                            </div>
                        </td>
                    </tr></tbody></table>
                    <?else:?>
                        &nbsp;
                    <?endif?>
                </div>
            </td>

                <td align="center">
                    <table cellspacing="0" cellpadding="0">
                        <tbody><tr>
                        </tr>
                        </tbody>
                    </table>
                </td>

            <td width="20%" align="right">
                <div class="next-and-last">
                    <?if (!empty($this->res['next'])):?>
                    <table cellspacing="0" cellpadding="0"><tbody><tr>
                        <td>
                            <div class="buttonAction buttonNext">
                                <a title="Next" href="/<?=$albumName?>/<?=$this->res['next']['componentUrl']?>-jigsaw-puzzle"></a>
                            </div>
                        </td>
                        <td>&nbsp;</td>
                        <td>
                            <div class="buttonAction buttonLast">
                                <a title="Last" href="/<?=$albumName?>/<?=$this->res['last']['componentUrl']?>-jigsaw-puzzle"></a>
                            </div>
                        </td>
                    </tr></tbody></table>
                    <?else:?>
                        &nbsp;
                    <?endif?>
                </div>
            </td>

        </tr></tbody></table>
    </div>
</div>