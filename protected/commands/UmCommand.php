<?php
/**
 * User Migrations (um).
 * Консольная команда. Перенос пользователей из движка Gallery2 в новое приложение.
 * Date: 09.07.12
 */
class UmCommand extends CConsoleCommand
{
    public $errors = array();
    public $defaultAction = 'index'; //run

    /**
     * Действие по умолчанию.
     */
    public function actionIndex()
    {
        $cr = chr(10); // Символ перевода строки
        Yii::beginProfile('um');    // Профилирование производительности
        $conn = Yii::app()->db;     // Используем соединения для локального сервера.
        $connTJP = Yii::app()->db2; // Используем соединения для сервера TheJigsawPuzzles.com
        $cnt = 0;

        $sql = 'SELECT
                  usr.g_id id, usr.g_userName username, usr.g_fullName fullname, usr.g_hashedPassword password,
                  usr.g_email email, ent.g_creationTimestamp createtime
                FROM g2_User usr
                LEFT JOIN g2_Entity ent
                  ON usr.g_id = ent.g_id
                ';
        $command = $connTJP->createCommand($sql);  // Подготовка sql
        $g2_users = $command->queryAll();

        // 2.
        // Формируем ассоц. массив с доп. данными пользователя: ( Year of Birth-Month of Birth-Birthday )
        // Gender : substr(Male)
        // Country : преобразуем (United States -> US)
        $sqlFields = '
            SELECT map.g_userId id,
                   CONCAT(map_year.g_value, "-", LPAD(MONTH(STR_TO_DATE(map_month.g_value,"%M")), 2, "0"), "-", LPAD(map_day.g_value, 2, "0")) `birthday`,
                   LEFT(map_gender.g_value, 1) `gender`,
                   map_country.g_value `country`
            FROM g2_UserDataMap map
            LEFT JOIN g2_UserDataMap map_year
              ON map_year.g_field = "Year of Birth" AND map_year.g_userId = :userID
            LEFT JOIN g2_UserDataMap map_month
              ON map_month.g_field = "Month of Birth" AND map_month.g_userId = :userID
            LEFT JOIN g2_UserDataMap map_day
              ON map_day.g_field = "Birthday" AND map_day.g_userId = :userID
            LEFT JOIN g2_UserDataMap map_gender
              ON map_gender.g_field = "Gender" AND map_gender.g_userId = :userID
            LEFT JOIN g2_UserDataMap map_country
              ON map_country.g_field = "Country" AND map_country.g_userId = :userID
            WHERE map.g_userId = :userID
            LIMIT 1
        ';
        $commandFields = $connTJP->createCommand($sqlFields);  // Подготовка sql
        foreach ($g2_users as $g2) {
            $commandFields->bindParam(':userID', $g2['id'], PDO::PARAM_INT);
            $tmp = $commandFields->queryRow();
            if ($tmp['id']) {
                if (array_key_exists($tmp['country'], $this->countries))
                    $tmp['country'] = $this->countries[$tmp['country']];
                else {
                    $tmp['country'] = '';
                    $errors[] = array($tmp['id'], $tmp['country']);
                }
                $g2_users_fields[$tmp['id']] = $tmp;
            }
        }

        $sqlInsert = 'REPLACE INTO user_users (id, username, password, email, status, createtime)
                      VALUES (:userID, :userName, :password, :email, 1, :createtime)';
        $sqlProfile = 'REPLACE INTO user_profiles (user_id, birthday, fullname, gender, country)
                       VALUES (:userID, :birthday, :fullname, :gender, :country)';

        $command = $conn->createCommand($sqlInsert); // Подготовим команду SQL
        $commandProfile = $conn->createCommand($sqlProfile); // Подготовим команду SQL c доп. полями

        echo "Start migration. "; // Сообщение о начале работы скрипта

        foreach ($g2_users as $g2) {
            $transaction=$conn->beginTransaction(); // Открываем транзацию
            try {                                   // Пробуем обработать строки
                // Подстановка данных для основной таблицы пользователя
                $command->bindParam(':userID', $g2['id'], PDO::PARAM_INT);
                $command->bindParam(':userName', $g2['username'], PDO::PARAM_STR);
                $command->bindParam(':password', $g2['password'], PDO::PARAM_STR);
                $command->bindParam(':email', $g2['email'], PDO::PARAM_STR);
                $command->bindParam(':createtime', $g2['createtime'], PDO::PARAM_INT);
                $command->execute();
                // Подстановка данных для таблицы пользователя с дополнительными полями
                $commandProfile->bindParam(':userID', $g2['id'], PDO::PARAM_INT);
                $commandProfile->bindParam(':fullname', $g2['fullname'], PDO::PARAM_STR);
                if (array_key_exists($g2['id'], $g2_users_fields)) { // Дополнительные поля
                    $commandProfile->bindParam(':birthday', $g2_users_fields[$g2['id']]['birthday'], PDO::PARAM_STR);
                    $commandProfile->bindParam(':gender', $g2_users_fields[$g2['id']]['gender'], PDO::PARAM_STR);
                    $commandProfile->bindParam(':country', $g2_users_fields[$g2['id']]['country'], PDO::PARAM_STR);
                } else { // Пустые значения
                    $commandProfile->bindValue(':birthday','');
                    $commandProfile->bindValue(':gender','');
                    $commandProfile->bindValue(':country','');
                }
                // Допонительные параметры
                $commandProfile->execute();

                $transaction->commit(); // Завершаем транзакцию если нет ошибок
                Yii::log('test', 'trace'); // Логируем ошибки
            } catch (Exception $e) {    // Перехватеваем ошибку
                $transaction->rollBack(); // Откатываем назад в случае неудачи
                echo ' ='.$cnt++.'-'.$g2['id'].'= '; // Выводим уведомление на экран
                Yii::log($e, 'trace', 'system.console.CConsoleCommand'); // Логируем ошибки
            }
            if ( !(++$cnt % 100) ) echo "$cr $cnt::{$g2['id']} "; // Выводим сообщение каждые 100 записей
        }
        /*if (count($this->errors)) { // Если существуют ошибки - выводим их на экран
            echo 'There are errors:\n ';
            print_r($this->errors);
        }*/
        Yii::endProfile('um');
        echo "End migration. "; // Сообщение об окончании работы скрипта
    }

    /**
     * Миграция пользователей, ожидающих активации
     */
    public function actionPending()
    {
        $cr = chr(10); // Символ перевода строки
        $conn = Yii::app()->db;     // Используем соединения для локального сервера.
        $connTJP = Yii::app()->db2; // Используем соединения для сервера TheJigsawPuzzles.com

        Yii::beginProfile('um_pending'); // Профилирование производительности
        echo "Start migration. $cr"; // Сообщение о начале работы скрипта

        $sql = 'SELECT usr.g_id id, usr.g_userName username, usr.g_fullName fullname, usr.g_hashedPassword password,
                       usr.g_email email, usr.g_registrationKey activkey, ent.g_creationTimestamp createtime,
                       CONCAT(map_year.g_value, "-", LPAD(MONTH(STR_TO_DATE(map_month.g_value,"%M")), 2, "0"), "-", LPAD(map_day.g_value, 2, "0")) `birthday`,
                       LEFT(map_gender.g_value, 1) `gender`,
                       map_country.g_value `country`
                FROM g2_PendingUser usr
                LEFT JOIN g2_Entity ent
                  ON usr.g_id = ent.g_id
                LEFT JOIN g2_UserDataMap map_year
                  ON map_year.g_field = "Year of Birth" AND map_year.g_userId = usr.g_id
                LEFT JOIN g2_UserDataMap map_month
                  ON map_month.g_field = "Month of Birth" AND map_month.g_userId = usr.g_id
                LEFT JOIN g2_UserDataMap map_day
                  ON map_day.g_field = "Birthday" AND map_day.g_userId = usr.g_id
                LEFT JOIN g2_UserDataMap map_gender
                  ON map_gender.g_field = "Gender" AND map_gender.g_userId = usr.g_id
                LEFT JOIN g2_UserDataMap map_country
                  ON map_country.g_field = "Country" AND map_country.g_userId = usr.g_id
                ';
        $command = $connTJP->createCommand($sql); // Подготовка sql
        $g2_users = $command->queryAll(); // Получение всех пользователей

        $sqlInsert = 'REPLACE INTO user_users (id, username, password, email, activkey, status, createtime)
                      VALUES (:userID, :username, :password, :email, :activkey, 0, :createtime)';
        $sqlProfile = 'REPLACE INTO user_profiles (user_id, birthday, fullname, gender, country)
                       VALUES (:userID, :birthday, :fullname, :gender, :country)';
        $command = $conn->createCommand($sqlInsert); // Подготовим команду SQL
        $commandProfile = $conn->createCommand($sqlProfile); // Подготовим команду SQL c доп. полями

        $cnt = 0; $cntAll = count($g2_users); $step = round($cntAll / 10);

        foreach($g2_users as $g2) {
            try { // Пробуем обработать строки
                /** Подстановка данных для основной таблицы пользователя */
                $command->bindParam(':userID',    $g2['id'], PDO::PARAM_INT);
                $command->bindParam(':username',  $g2['username'],  PDO::PARAM_STR); // Подстановка данных
                $command->bindParam(':password',  $g2['password'],  PDO::PARAM_STR);
                $command->bindParam(':email',     $g2['email'],     PDO::PARAM_STR);
                $command->bindParam(':activkey',  $g2['activkey'],  PDO::PARAM_STR);
                $command->bindParam(':createtime',$g2['createtime'],PDO::PARAM_INT);
                $command->execute();

                /** Подстановка данных для таблицы пользователя с дополнительными полями */
                $commandProfile->bindParam(':userID',   $g2['id'], PDO::PARAM_INT);
                $commandProfile->bindParam(':fullname', $g2['fullname'], PDO::PARAM_STR);
                $commandProfile->bindParam(':birthday', $g2['birthday'], PDO::PARAM_STR);
                $commandProfile->bindParam(':gender',   $g2['gender'], PDO::PARAM_STR);
                if (array_key_exists($g2['country'], $this->countries))
                    $commandProfile->bindParam(':country', $this->countries[$g2['country']], PDO::PARAM_STR);
                else
                    $commandProfile->bindValue(':country', '', PDO::PARAM_STR);
                $commandProfile->execute();

            } catch (Exception $e) {    // Перехватываем ошибку
                //echo "\n =".$cnt++.'-'.$g2['id'].'= '; // Выводим уведомление на экран
                echo "\n".$e->getMessage()." =".$cnt++.'::'.$g2['id'].'= '; // Выводим уведомление на экран
                Yii::log($e, 'trace', 'system.console.CConsoleCommand'); // Логируем ошибки
            }
            if ( !(++$cnt % $step) ) echo " #"; // Выводим сообщение каждые 5%
        }
        Yii::endProfile('um_pending'); // Окончание профилирования производительности
        echo "$cr End migration. "; // Сообщение об окончании работы скрипта
    }

    /**
     * Миграция пользователей, ожидающих активации
     * Стакрая версия переноса без учета fullname, gender, birthday, country
     */
    public function actionPending_old()
    {
        $cr = chr(10); // Символ перевода строки
        $conn = Yii::app()->db;     // Используем соединения для локального сервера.
        $connTJP = Yii::app()->db2; // Используем соединения для сервера TheJigsawPuzzles.com

        Yii::beginProfile('um_pending'); // Профилирование производительности
        echo "Start migration. $cr"; // Сообщение о начале работы скрипта

        $sql = 'SELECT usr.g_id id, usr.g_userName username, usr.g_fullName fullname, usr.g_hashedPassword password,
                       usr.g_email email, usr.g_registrationKey activkey, ent.g_creationTimestamp createtime
                FROM g2_PendingUser usr
                LEFT JOIN g2_Entity ent
                  ON usr.g_id = ent.g_id
                ';
        $command = $connTJP->createCommand($sql); // Подготовка sql
        $g2_users = $command->queryAll(); // Получение всех пользователей

        $sqlInsert = 'INSERT INTO user_users (username, password, email, activkey, status, createtime)
            VALUES (:username, :password, :email, :activkey, 0, :createtime)';
        $command = $conn->createCommand($sqlInsert); // Подготовим команду SQL
        $cnt = 0; $cntAll = count($g2_users); $step = round($cntAll / 10);

        foreach($g2_users as $g2) {
            try { // Пробуем обработать строки
                $command->bindParam(':username',  $g2['username'],  PDO::PARAM_STR); // Подстановка данных
                $command->bindParam(':password',  $g2['password'],  PDO::PARAM_STR);
                $command->bindParam(':email',     $g2['email'],     PDO::PARAM_STR);
                $command->bindParam(':activkey',  $g2['activkey'],  PDO::PARAM_STR);
                $command->bindParam(':createtime',$g2['createtime'],PDO::PARAM_INT);

                $command->execute();
            } catch (Exception $e) {    // Перехватываем ошибку
                //echo "\n =".$cnt++.'-'.$g2['id'].'= '; // Выводим уведомление на экран
                echo "\n".$e->getMessage()." =".$cnt++.'-'.$g2['id'].'= '; // Выводим уведомление на экран
                Yii::log($e, 'trace', 'system.console.CConsoleCommand'); // Логируем ошибки
            }
            if ( !(++$cnt % $step) ) echo " #"; // Выводим сообщение каждые 5%
        }
        Yii::endProfile('um_pending'); // Окончание профилирования производительности
        echo "$cr End migration. "; // Сообщение об окончании работы скрипта
    }

    /**
     * Миграция заблокированных пользователей (-1)
     * НЕ ИСПОЛЬЗУЕТСЯ.
     */
    public function actionLocked() {}

    /**
     * Подсчет затраченного времени. НЕ используется: применяем Yii::beginProfile('um');
     * @return float
     */
    private function getmicrotime()
    {
        list($usec, $sec) = explode(" ", microtime());
        return ((float)$usec + (float)$sec);
    }

    public $countriesFlip = array( //private
        'AF' => "Afghanistan",
        'AL' => "Albania",
        'DZ' => "Algeria",
        'AS' => "American Samoa",
        'AD' => "Andorra",
        'AO' => "Angola",
        'AI' => "Anguilla",
        'AQ' => "Antarctica",
        'AG' => "Antigua and Barbuda",
        'AR' => "Argentina",
        'AM' => "Armenia",
        'AW' => "Aruba",
        'AU' => "Australia",
        'AT' => "Austria",
        'AZ' => "Azerbaijan",
        'BS' => "Bahamas",
        'BH' => "Bahrain",
        'BD' => "Bangladesh",
        'BB' => "Barbados",
        'BY' => "Belarus",
        'BE' => "Belgium",
        'BZ' => "Belize",
        'BJ' => "Benin",
        'BM' => "Bermuda",
        'BT' => "Bhutan",
        'BO' => "Bolivia",
        'BA' => "Bosnia and Herzegowina",
        'BW' => "Botswana",
        'BV' => "Bouvet Island",
        'BR' => "Brazil",
        'IO' => "British Indian Ocean Territory",
        'BN' => "Brunei Darussalam",
        'BG' => "Bulgaria",
        'BF' => "Burkina Faso",
        'BI' => "Burundi",
        'KH' => "Cambodia",
        'CM' => "Cameroon",
        'CA' => "Canada",
        'CV' => "Cape Verde",
        'KY' => "Cayman Islands",
        'CF' => "Central African Republic",
        'TD' => "Chad",
        'CL' => "Chile",
        'CN' => "China",
        'CX' => "Christmas Island",
        'CC' => "Cocos (Keeling) Islands",
        'CO' => "Colombia",
        'KM' => "Comoros",
        'CG' => "Congo",
        'CD' => "Congo, the Democratic Republic of the",
        'CK' => "Cook Islands",
        'CR' => "Costa Rica",
        'CI' => "Cote d'Ivoire",
        'HR' => "Croatia (Hrvatska)",
        'CU' => "Cuba",
        'CY' => "Cyprus",
        'CZ' => "Czech Republic",
        'DK' => "Denmark",
        'DJ' => "Djibouti",
        'DM' => "Dominica",
        'DO' => "Dominican Republic",
        'TP' => "East Timor",
        'EC' => "Ecuador",
        'EG' => "Egypt",
        'SV' => "El Salvador",
        'GQ' => "Equatorial Guinea",
        'ER' => "Eritrea",
        'EE' => "Estonia",
        'ET' => "Ethiopia",
        'FK' => "Falkland Islands (Malvinas)",
        'FO' => "Faroe Islands",
        'FJ' => "Fiji",
        'FI' => "Finland",
        'FR' => "France",
        'FX' => "France, Metropolitan",
        'GF' => "French Guiana",
        'PF' => "French Polynesia",
        'TF' => "French Southern Territories",
        'GA' => "Gabon",
        'GM' => "Gambia",
        'GE' => "Georgia",
        'DE' => "Germany",
        'GH' => "Ghana",
        'GI' => "Gibraltar",
        'GR' => "Greece",
        'GL' => "Greenland",
        'GD' => "Grenada",
        'GP' => "Guadeloupe",
        'GU' => "Guam",
        'GT' => "Guatemala",
        'GN' => "Guinea",
        'GW' => "Guinea-Bissau",
        'GY' => "Guyana",
        'HT' => "Haiti",
        'HM' => "Heard and Mc Donald Islands",
        'VA' => "Holy See (Vatican City State)",
        'HN' => "Honduras",
        'HK' => "Hong Kong",
        'HU' => "Hungary",
        'IS' => "Iceland",
        'IN' => "India",
        'ID' => "Indonesia",
        'IR' => "Iran (Islamic Republic of)",
        'IQ' => "Iraq",
        'IE' => "Ireland",
        'IL' => "Israel",
        'IT' => "Italy",
        'JM' => "Jamaica",
        'JP' => "Japan",
        'JO' => "Jordan",
        'KZ' => "Kazakhstan",
        'KE' => "Kenya",
        'KI' => "Kiribati",
        'KP' => "Korea, Democratic People's Republic of",
        'KR' => "Korea, Republic of",
        'KW' => "Kuwait",
        'KG' => "Kyrgyzstan",
        'LA' => "Lao People's Democratic Republic",
        'LV' => "Latvia",
        'LB' => "Lebanon",
        'LS' => "Lesotho",
        'LR' => "Liberia",
        'LY' => "Libyan Arab Jamahiriya",
        'LI' => "Liechtenstein",
        'LT' => "Lithuania",
        'LU' => "Luxembourg",
        'MO' => "Macau",
        'MK' => "Macedonia, The Former Yugoslav Republic of",
        'MG' => "Madagascar",
        'MW' => "Malawi",
        'MY' => "Malaysia",
        'MV' => "Maldives",
        'ML' => "Mali",
        'MT' => "Malta",
        'MH' => "Marshall Islands",
        'MQ' => "Martinique",
        'MR' => "Mauritania",
        'MU' => "Mauritius",
        'YT' => "Mayotte",
        'MX' => "Mexico",
        'FM' => "Micronesia, Federated States of",
        'MD' => "Moldova, Republic of",
        'MC' => "Monaco",
        'MN' => "Mongolia",
        'MS' => "Montserrat",
        'MA' => "Morocco",
        'MZ' => "Mozambique",
        'MM' => "Myanmar",
        'NA' => "Namibia",
        'NR' => "Nauru",
        'NP' => "Nepal",
        'NL' => "Netherlands",
        'AN' => "Netherlands Antilles",
        'NC' => "New Caledonia",
        'NZ' => "New Zealand",
        'NI' => "Nicaragua",
        'NE' => "Niger",
        'NG' => "Nigeria",
        'NU' => "Niue",
        'NF' => "Norfolk Island",
        'MP' => "Northern Mariana Islands",
        'NO' => "Norway",
        'OM' => "Oman",
        'PK' => "Pakistan",
        'PW' => "Palau",
        'PA' => "Panama",
        'PG' => "Papua New Guinea",
        'PY' => "Paraguay",
        'PE' => "Peru",
        'PH' => "Philippines",
        'PN' => "Pitcairn",
        'PL' => "Poland",
        'PT' => "Portugal",
        'PR' => "Puerto Rico",
        'QA' => "Qatar",
        'RE' => "Reunion",
        'RO' => "Romania",
        'RU' => "Russian Federation",
        'RW' => "Rwanda",
        'KN' => "Saint Kitts and Nevis",
        'LC' => "Saint LUCIA",
        'VC' => "Saint Vincent and the Grenadines",
        'WS' => "Samoa",
        'SM' => "San Marino",
        'ST' => "Sao Tome and Principe",
        'SA' => "Saudi Arabia",
        'SN' => "Senegal",
        'SC' => "Seychelles",
        'SL' => "Sierra Leone",
        'SG' => "Singapore",
        'SK' => "Slovakia (Slovak Republic)",
        'SI' => "Slovenia",
        'SB' => "Solomon Islands",
        'SO' => "Somalia",
        'ZA' => "South Africa",
        'GS' => "South Georgia and the South Sandwich Islands",
        'ES' => "Spain",
        'LK' => "Sri Lanka",
        'SH' => "St. Helena",
        'PM' => "St. Pierre and Miquelon",
        'SD' => "Sudan",
        'SR' => "Suriname",
        'SJ' => "Svalbard and Jan Mayen Islands",
        'SZ' => "Swaziland",
        'SE' => "Sweden",
        'CH' => "Switzerland",
        'SY' => "Syrian Arab Republic",
        'TW' => "Taiwan, Province of China",
        'TJ' => "Tajikistan",
        'TZ' => "Tanzania, United Republic of",
        'TH' => "Thailand",
        'TG' => "Togo",
        'TK' => "Tokelau",
        'TO' => "Tonga",
        'TT' => "Trinidad and Tobago",
        'TN' => "Tunisia",
        'TR' => "Turkey",
        'TM' => "Turkmenistan",
        'TC' => "Turks and Caicos Islands",
        'TV' => "Tuvalu",
        'UG' => "Uganda",
        'UA' => "Ukraine",
        'AE' => "United Arab Emirates",
        'GB' => "United Kingdom",
        'US' => "United States",
        'UM' => "United States Minor Outlying Islands",
        'UY' => "Uruguay",
        'UZ' => "Uzbekistan",
        'VU' => "Vanuatu",
        'VE' => "Venezuela",
        'VN' => "Viet Nam",
        'VG' => "Virgin Islands (British)",
        'VI' => "Virgin Islands (U.S.)",
        'WF' => "Wallis and Futuna Islands",
        'EH' => "Western Sahara",
        'YE' => "Yemen",
        'YU' => "Yugoslavia",
        'ZM' => "Zambia",
        'ZW' => "Zimbabwe",
    );

    public $countries = array(
        'Afghanistan' => 'AF',
        'Albania' => 'AL',
        'Algeria' => 'DZ',
        'American Samoa' => 'AS',
        'Andorra' => 'AD',
        'Angola' => 'AO',
        'Anguilla' => 'AI',
        'Antarctica' => 'AQ',
        'Antigua and Barbuda' => 'AG',
        'Argentina' => 'AR',
        'Armenia' => 'AM',
        'Aruba' => 'AW',
        'Australia' => 'AU',
        'Austria' => 'AT',
        'Azerbaijan' => 'AZ',
        'Bahamas' => 'BS',
        'Bahrain' => 'BH',
        'Bangladesh' => 'BD',
        'Barbados' => 'BB',
        'Belarus' => 'BY',
        'Belgium' => 'BE',
        'Belize' => 'BZ',
        'Benin' => 'BJ',
        'Bermuda' => 'BM',
        'Bhutan' => 'BT',
        'Bolivia' => 'BO',
        'Bosnia and Herzegowina' => 'BA',
        'Botswana' => 'BW',
        'Bouvet Island' => 'BV',
        'Brazil' => 'BR',
        'British Indian Ocean Territory' => 'IO',
        'Brunei Darussalam' => 'BN',
        'Bulgaria' => 'BG',
        'Burkina Faso' => 'BF',
        'Burundi' => 'BI',
        'Cambodia' => 'KH',
        'Cameroon' => 'CM',
        'Canada' => 'CA',
        'Cape Verde' => 'CV',
        'Cayman Islands' => 'KY',
        'Central African Republic' => 'CF',
        'Chad' => 'TD',
        'Chile' => 'CL',
        'China' => 'CN',
        'Christmas Island' => 'CX',
        'Cocos  Islands' => 'CC',//(Keeling)
        'Colombia' => 'CO',
        'Comoros' => 'KM',
        'Congo' => 'CG',
        //'Congo, the Democratic Republic of the' => 'CD',
        'Cook Islands' => 'CK',
        'Costa Rica' => 'CR',
        "Cote d'Ivoire" => 'CI',
        'Croatia' => 'HR',
        'Cuba' => 'CU',
        'Cyprus' => 'CY',
        'Czech Republic' => 'CZ',
        'Denmark' => 'DK',
        'Djibouti' => 'DJ',
        'Dominica' => 'DM',
        'Dominican Republic' => 'DO',
        'East Timor' => 'TP',
        'Ecuador' => 'EC',
        'Egypt' => 'EG',
        'El Salvador' => 'SV',
        'Equatorial Guinea' => 'GQ',
        'Eritrea' => 'ER',
        'Estonia' => 'EE',
        'Ethiopia' => 'ET',
        'Falkland Islands' => 'FK',
        'Faroe Islands' => 'FO',
        'Fiji' => 'FJ',
        'Finland' => 'FI',
        'France' => 'FR',
        'France, Metropolitan' => 'FX',
        'French Guiana' => 'GF',
        'French Polynesia' => 'PF',
        'French Southern Territories' => 'TF',
        'Gabon' => 'GA',
        'Gambia' => 'GM',
        'Georgia' => 'GE',
        'Germany' => 'DE',
        'Ghana' => 'GH',
        'Gibraltar' => 'GI',
        'Greece' => 'GR',
        'Greenland' => 'GL',
        'Grenada' => 'GD',
        'Guadeloupe' => 'GP',
        'Guam' => 'GU',
        'Guatemala' => 'GT',
        'Guinea' => 'GN',
        'Guinea-Bissau' => 'GW',
        'Guyana' => 'GY',
        'Haiti' => 'HT',
        'Heard and Mc Donald Islands' => 'HM',
        'Holy See' => 'VA',
        'Honduras' => 'HN',
        'Hong Kong' => 'HK',
        'Hungary' => 'HU',
        'Iceland' => 'IS',
        'India' => 'IN',
        'Indonesia' => 'ID',
        'Iran' => 'IR',
        'Iraq' => 'IQ',
        'Ireland' => 'IE',
        'Israel' => 'IL',
        'Italy' => 'IT',
        'Jamaica' => 'JM',
        'Japan' => 'JP',
        'Jordan' => 'JO',
        'Kazakhstan' => 'KZ',
        'Kenya' => 'KE',
        'Kiribati' => 'KI',
        "Korea, Democratic People's Republic of" => 'KP',
        'Korea, Republic of' => 'KR',
        'Kuwait' => 'KW',
        'Kyrgyzstan' => 'KG',
        "Lao People's Democratic Republic" => 'LA',
        'Latvia' => 'LV',
        'Lebanon' => 'LB',
        'Lesotho' => 'LS',
        'Liberia' => 'LR',
        'Libyan Arab Jamahiriya' => 'LY',
        'Liechtenstein' => 'LI',
        'Lithuania' => 'LT',
        'Luxembourg' => 'LU',
        'Macau' => 'MO',
        'Macedonia' => 'MK',
        'Madagascar' => 'MG',
        'Malawi' => 'MW',
        'Malaysia' => 'MY',
        'Maldives' => 'MV',
        'Mali' => 'ML',
        'Malta' => 'MT',
        'Marshall Islands' => 'MH',
        'Martinique' => 'MQ',
        'Mauritania' => 'MR',
        'Mauritius' => 'MU',
        'Mayotte' => 'YT',
        'Mexico' => 'MX',
        'Micronesia' => 'FM',
        'Moldova, Republic of' => 'MD',
        'Monaco' => 'MC',
        'Mongolia' => 'MN',
        'Montserrat' => 'MS',
        'Morocco' => 'MA',
        'Mozambique' => 'MZ',
        'Myanmar' => 'MM',
        'Namibia' => 'NA',
        'Nauru' => 'NR',
        'Nepal' => 'NP',
        'Netherlands' => 'NL',
        'Netherlands Antilles' => 'AN',
        'New Caledonia' => 'NC',
        'New Zealand' => 'NZ',
        'Nicaragua' => 'NI',
        'Niger' => 'NE',
        'Nigeria' => 'NG',
        'Niue' => 'NU',
        'Norfolk Island' => 'NF',
        'Northern Mariana Islands' => 'MP',
        'Norway' => 'NO',
        'Oman' => 'OM',
        'Pakistan' => 'PK',
        'Palau' => 'PW',
        'Panama' => 'PA',
        'Papua New Guinea' => 'PG',
        'Paraguay' => 'PY',
        'Peru' => 'PE',
        'Philippines' => 'PH',
        'Pitcairn' => 'PN',
        'Poland' => 'PL',
        'Portugal' => 'PT',
        'Puerto Rico' => 'PR',
        'Qatar' => 'QA',
        'Reunion' => 'RE',
        'Romania' => 'RO',
        'Russian Federation' => 'RU',
        'Rwanda' => 'RW',
        'Saint Kitts and Nevis' => 'KN',
        'Saint LUCIA' => 'LC',
        'Saint Vincent and the Grenadines' => 'VC',
        'Samoa' => 'WS',
        'San Marino' => 'SM',
        'Sao Tome and Principe' => 'ST',
        'Saudi Arabia' => 'SA',
        'Senegal' => 'SN',
        'Seychelles' => 'SC',
        'Sierra Leone' => 'SL',
        'Singapore' => 'SG',
        'Slovakia' => 'SK',
        'Slovenia' => 'SI',
        'Solomon Islands' => 'SB',
        'Somalia' => 'SO',
        'South Africa' => 'ZA',
        'South Georgia and the South Sandwich Islands' => 'GS',
        'Spain' => 'ES',
        'Sri Lanka' => 'LK',
        'St. Helena' => 'SH',
        'St. Pierre and Miquelon' => 'PM',
        'Sudan' => 'SD',
        'Suriname' => 'SR',
        'Svalbard and Jan Mayen Islands' => 'SJ',
        'Swaziland' => 'SZ',
        'Sweden' => 'SE',
        'Switzerland' => 'CH',
        'Syrian Arab Republic' => 'SY',
        'Taiwan, Province of China' => 'TW',
        'Tajikistan' => 'TJ',
        'Tanzania, United Republic of' => 'TZ',
        'Thailand' => 'TH',
        'Togo' => 'TG',
        'Tokelau' => 'TK',
        'Tonga' => 'TO',
        'Trinidad and Tobago' => 'TT',
        'Tunisia' => 'TN',
        'Turkey' => 'TR',
        'Turkmenistan' => 'TM',
        'Turks and Caicos Islands' => 'TC',
        'Tuvalu' => 'TV',
        'Uganda' => 'UG',
        'Ukraine' => 'UA',
        'United Arab Emirates' => 'AE',
        'United Kingdom' => 'GB',
        'United States' => 'US',
        'United States Minor Outlying Islands' => 'UM',
        'Uruguay' => 'UY',
        'Uzbekistan' => 'UZ',
        'Vanuatu' => 'VU',
        'Venezuela' => 'VE',
        'Viet Nam' => 'VN',
        'Virgin Islands' => 'VG',
        //'Virgin Islands (U.S.)' => 'VI',
        'Wallis and Futuna Islands' => 'WF',
        'Western Sahara' => 'EH',
        'Yemen' => 'YE',
        'Yugoslavia' => 'YU',
        'Zambia' => 'ZM',
        'Zimbabwe' => 'ZW',
    );
}