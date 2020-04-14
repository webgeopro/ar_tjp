<?php
$this->pageTitle='Create your account on TheJigsawPuzzles.com';
$this->breadcrumbs=array(
	'Create your account on TheJigsawPuzzles.com',
);?>

<table cellspacing="0" cellpadding="0" width="100%" class="gcBackground1">
    <tbody><tr valign="top">
        <td width="20%">
            <table cellspacing="0" cellpadding="0">
                <tbody><tr>
                    <td style="padding-bottom:5px" colspan="2">
                        <div class="gsContentDetail">
                            <div class="gbBlock gcBorder1">

                                <div class="block-imageblock-ImageBlock">
                                    <div class="one-image">
                                        <div class="giThumbnailContainer">
                                            <?$this->widget('defaultImage')?>
                                        </div>
                                    </div>
                                </div>

                            </div>
                        </div></td>
                </tr>
                </tbody></table>
        </td>
        <td>
            <table class="gbSignUpIn">
                <tbody><tr>
                    <td width="400">
                        <h1>Create your free account<br>on TheJigsawPuzzles.com</h1>
                        <p>You asked, we did: new cool features available to registered users of TheJigsawPuzzles.com. Well, we're just getting started, but here are what's ready and what to expect soon:</p>
                        <ul>
                            <li>Make puzzles from <b>your own pictures</b></li>
                            <li><b>Share</b> your puzzles with friends</li>
                            <li>Cut puzzles to <b>300</b>, <b>400</b> and <b>500</b> pieces</li>
                            <li><b>Save</b> your progress and continue saved puzzles later</li>
                            <li>Leave <b>comments</b> on puzzles (coming soon)</li>
                            <li><b>Compete</b> for fastest times (coming soon)</li>
                        </ul>
                        So take a second, sign up with the below button (yes, it's free) and start having fun:
                        <?if (Yii::app()->user->isGuest):?>
                        <form action="/user/registration">
                            <input type="submit" class="inputTypeSubmit" value="Sign Up">
                        </form>
                        <?endif?>
                    </td>
                    <td width="400" class="gcBorder1" style="border-left-width:1px;">

                        <h1>Already have an account?</h1>
                        <p>Sign in using the form below. You may also use "Sign In" link in every page's header.</p>
                        <?if (Yii::app()->user->isGuest):?>
                        <div style="width:150px;"><div class="block-core-LoginBlock">
                                <form id="LoginForm" action="/user/login" method="post">
                                    <div>
                                        <input type="text" id="giFormUsername" size="13" name="LoginForm[username]" value="Username"
                                               onfocus="var f=document.getElementById('giFormUsername'); if (f.value == 'Username') { f.value = '' }"
                                               onblur="var f=document.getElementById('giFormUsername'); if (f.value == '') { f.value = 'Username' }"
                                               style="padding-left: 17px;">
                                        <input type="password" id="giFormPassword" size="13" name="LoginForm[password]"
                                               onfocus="var f=document.getElementById('giFormPassword'); f.className='';"
                                               onblur="var f=document.getElementById('giFormPassword'); if (f.value == '') { f.className='giFormPasswordLabeled' }"
                                               class="giFormPasswordLabeled" style="padding-left:17px;background-repeat:no-repeat;">
                                        <SCRIPT type=text/javascript>var f=document.getElementById('giFormPassword'); f.className='giFormPasswordLabeled'</SCRIPT>

                                        <script type="text/javascript">
                                            var f=document.getElementById('giFormPassword');
                                            f.className='giFormPasswordLabeled'
                                        </script>

                                        <input type="submit" class="inputTypeSubmit" name="g2_form[action][login]" value="Login">
                                    </div>
                                </form>
                            </div>
                        </div>
                        <?else:?>
                            <br/>
                            <h1>You are already signed in.</h1>
                        <?endif?>
                    </td>
                </tr>
                </tbody></table>
        </td>
    </tr>
    </tbody></table>