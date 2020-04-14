<div class="gbBlockTop">
    <table width="100%" height="100%">
        <tr>
            <td align="center" valign="center">
                <?if (!Yii::app()->user->isGuest):?>
                <div class="gbUserGreeting">
                    <?if (!empty($this->userProfile->fullname))
                        echo 'Hello '.$this->userProfile->fullname. '!';
                    else
                        echo 'Hello '.Yii::app()->user->name.'!';?>
                </div>
                <?endif?>
                <div class="gbUserActions">
                <?if (Yii::app()->user->isGuest):?>
                    <a href="javascript:;" class="giTitle" onClick="document.getElementById('loginBox').style.display='inline';return false;">
                        Sign In
                    </a>
                    <div id="loginBox">:&nbsp;
                        <DIV class=block-core-LoginBlock>
                            <FORM id="LoginForm" method="post" action="/user/login" name="LoginForm">
                            <DIV>
                                <input type="text" id="giFormUsername" size="13" name="LoginForm[username]" value="Username"
                                       onfocus="var f=document.getElementById('giFormUsername'); if (f.value == 'Username') { f.value = '' }"
                                       onblur="var f=document.getElementById('giFormUsername'); if (f.value == '') { f.value = 'Username' }"
                                       >
                                <input type="password" id="giFormPassword" size="13" name="LoginForm[password]"
                                       onfocus="var f=document.getElementById('giFormPassword'); f.className='';"
                                       onblur="var f=document.getElementById('giFormPassword'); if (f.value == '') { f.className='giFormPasswordLabeled' }"
                                       class="giFormPasswordLabeled">
                                <SCRIPT type=text/javascript>var f=document.getElementById('giFormPassword'); f.className='giFormPasswordLabeled'</SCRIPT>
                                <input type="submit" class="inputTypeSubmit" name="g2_form[action][login]" value="Login">
                            </DIV>
                            </FORM>
                        </DIV>
                    </div>
                    <script type="text/javascript">document.getElementById('loginBox').style.display='none';</script>
                    &nbsp;&nbsp;<a href="/user/registration" class="gbAdminLink gbLink-register_UserSelfRegistration giTitle">Sign Up</a>
                <?else:?>
                    &nbsp;<a class="gbAdminLink gbLink-puzzle_MyPuzzles giTitle" href="/User-Albums/<?=Yii::app()->user->name.Yii::app()->params['urlSuffix']?>">My Puzzles</a>
                    &nbsp;<a class="gbAdminLink gbLink-core_UserPreferences giTitle" href="/user/profile">My Profile</a>
                    &nbsp;<a class="gbAdminLink gbLink-core_UserLogin giTitle" href="/logout">Sign Out</a>
                <?endif?>
                </div>
                <?if ('album' == $this->action->id && !empty($this->albumUser) AND !Yii::app()->user->isGuest):?>
                    <?if (0 <= Item::model()->count('owner_id=:ownerID', array(':ownerID'=>Yii::app()->user->id))):?>
                    <a href="/makeapuzzle">
                        <img src="/images/make_a_puzzle_s.png" alt="Make a puzzle!" title="Make a puzzle!" width="160" height="29"></a>
                    <?endif?>
                <?endif?>
            </td>
        </tr>
    </table>
</div>