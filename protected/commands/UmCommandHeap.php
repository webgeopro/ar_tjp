<?php
/**
 * User Migrations (um).
 * Консольная команда. Пренос пользователей из движка Gallery2 в новое приложение.
 * Date: 09.07.12
 */
class UmCommand extends CConsoleCommand
{
    public $errors = array();
    public $defaultAction = 'index'; //run
    /**
     * Действие по умолчанию.
     */
    public function actionIndex() //actionIndex()$args=array()
    {
        $g2_users = G2User::model()->findAll();         // Выбираем всех пользователей из старой БД
        foreach ($g2_users as $g2) {
            $model = User::model();
            if ($model->exists('id=:ID', array(':ID'=>$g2['g_id']))) {
                $user = $model->findByPk($g2['g_id']); // Ищем пользователя по ID если нет, создается новый
            } else {
                $user = new User;
            }
            if (Profile::model()->exists('user_id=:ID', array(':ID'=>$g2['g_id']))) {
                $profile = Profile::model()->findByPk($g2['g_id']); // Открывает таблицу доп. полей
            } else {
                $profile = new Profile;
            }
            $user->username = $g2['g_userName'];          // Логин
            $user->password = $g2['g_hashedPassword'];    // Пароль (md5)
            $user->email    = $g2['g_email'];             // E-mail пользователя
            if ($user->validate()) { // Если поля заполнены корректно

                // Транзакция. Сохраняем последовательно user и profile.
                if ($user->save()) {
                    //$transaction=$model->dbConnection->beginTransaction();
                    //try {
                    $profile->fullname = $g2['g_fullName'];
                    if ($profile->save()) {
                        //$transaction->commit();
                    } else {
                        //$transaction->rollBack();
                        $errors[] = $g2;
                    }

                    //} catch(Exception $e) {

                    //}
                }

            }
        }
        if (count($errors)) { // Если существуют ошибки - выводим их на экран
            echo 'There are errors:\n ';
            print_r($errors);
            $par =
                $k->build_number_coefficient / $sumOfWeights
                    * $this->distanceQual($a1->r_house, $a2->r_house)

                + $k->cost_coefficient / $sumOfWeights
                    * sqrt( 2.0 * $this->distanceQual( ($a1->price, $a2->price) + 0.00000001)
                            / ($a1->price + $a2->price + 1.0)
                      )

                + $k->total_apartment_area_coefficient / $sumOfWeights
                    * pow( $this->distanceQual($a1->general_space,$a2->general_space)
                            / ($a1->general_space+$a2->general_space + 1.0)
                      , 2)

                + $k->living_apartment_area_coefficient / $sumOfWeights
                    * pow( $this->distanceQual($a1->living_space, $a2->living_space)
                           / ($a1->living_space+$a2->living_space+1.0)
                      , 2)

                + $k->kitchen_area_coefficient / $sumOfWeights
                    * pow( $this->distanceQual($a1->kitchen_space, $a2->kitchen_space)
                          / ($a1->kitchen_space+$a2->kitchen_space+1.0)
                     , 2)

                + $k->floors_coefficient/$sumOfWeights
                    *($this->distanceQual($a1->floor_count, $a2->floor_count)<=1 ? 0.0 : 1.0);


            sqrt($par);


        }
    }
}