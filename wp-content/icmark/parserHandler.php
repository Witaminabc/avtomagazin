<?php


define( 'WP_DEBUG', false );

require_once ('parser.php');



$parser = new \IcmUtils\Parser1c('/wp-content/icmark/upload_from_1c', false);

if (file_exists($_SERVER['DOCUMENT_ROOT'] . '/wp-load.php')) {

    include_once $_SERVER['DOCUMENT_ROOT'] . '/wp-load.php';

}

// if(is_user_logged_in()) {

//     $current_user = wp_get_current_user();

//     if(!$current_user->allcaps['administrator']) {

//         header('Location: /');

//     }

// } else {

//     header('Location: /');

// }

if ((!isset($_GET['password'])) || ($_GET['password'] != 'Hdyencdk31')) {
    header('Location: /');
}

/**

 * Параметры парсера:

 * iC - импорт клиентов clients.xml

 * iM - импорт менеджеров managers.xml

 * iP - импорт цен prices.xml

 * iA - импорт отгрузок available.xml

 */

if(!empty($_GET['params'])) {

    $arParserParams = explode(',',$_GET['params']);



    function delParserParam(&$params, $key) {

        unset($params[array_search($key,$params)]);

        sleep(1);

    }



    /**

     * Цикличная обработка групп клиентов

     */

    $cycle = false;

    if(isset($_GET['function']) && isset($_GET['gKey'])) {

        $cycle = true;

        $importFunction = $_GET['function'];

        switch ($importFunction) {

            case 'iC':

                echo '<p style="color: green">Импорт клиентов и документов:</p>';

                $return = $parser->importClients($_GET['gKey']);

                if($return) {?>

                    <p>Загружаем группу <?=$return['data']['@attributes']['Ид']?></p>

                    <script>

                        window.location.href = "/wp-content/icmark/parserHandler.php?password=Hdyencdk31&function=iC&gKey=<?=$return['gKey']?>&params=<?=implode(',',$arParserParams)?>";

                    </script>

                    <?

                } else {

                    echo '<p style="color: green">Группы, Клиенты и Документы импортированы. Групп = '.($_GET['gKey']+1).'</p>';

                    delParserParam($arParserParams, 'iC');

                    $cycle = false;

                }

                break;

            case 'iP':

                echo '<p style="color: green">Импорт цен:</p>';

                $return = $parser->importPrices($_GET['gKey']);

                if($return) {?>



                    <p>Загрузили группу <?=$return['data']['@attributes']['Ид']?></p>

                    <script>

                        window.location.href = "/wp-content/icmark/parserHandler.php?password=Hdyencdk31&function=iP&gKey=<?=$return['gKey']?>&params=<?=implode(',',$arParserParams)?>";

                    </script>

                    <?php

                    die();

                } else {

                    echo '<p style="color: green">Цены импортированы.</p>';

                    delParserParam($arParserParams, 'iP');

                    $cycle = false;

                }

                break;

        }

    }

    if(!$cycle) {

        if(in_array('iM',$arParserParams) !== false) {

            $parser->importManagers();

            echo '<p style="color: green">Менеджеры импортированы.</p>';

            delParserParam($arParserParams, 'iM');

        }

        //После загрузки менеджеров продолжаем без перезагрузки

        if(in_array('iC', $arParserParams) !== false) {

            echo '<p style="color: green">Импорт клиентов и документов:</p>';

            $return = $parser->importClients();

            //Загрузка клиентов и документов осуществляется в цикле (через редирект)
            
            if($return) {?>

                <p>Загрузили группу <?=$return['data']['@attributes']['Ид']?></p>
                
                <script>

                    window.location.href = "/wp-content/icmark/parserHandler.php?password=Hdyencdk31&function=iC&gKey=<?=$return['gKey']?>&params=<?=implode(',',$arParserParams)?>";

                </script>

                <?php

                die();

            } else {

                echo '<p style="color: green">Группы, Клиенты и Документы импортированы</p>';

                delParserParam($arParserParams, 'iC');

            }

        }



        if(in_array('iP', $arParserParams) !== false) {

            echo '<p style="color: green">Импорт цен:</p>';

            $return = $parser->importPrices();

            //Загрузка цен осуществляется в цикле (через редирект)

            if($return) {?>

                <p>Загрузили <?=$return['data']['Номенклатура']['Наименование']?></p>

                <script>

                    window.location.href = "/wp-content/icmark/parserHandler.php?password=Hdyencdk31&function=iP&gKey=<?=$return['gKey']?>&params=<?=implode(',',$arParserParams)?>";

                </script>

                <?php

                die();

            } else {

                echo '<p style="color: green">Цены импортированы.</p>';

                delParserParam($arParserParams, 'iP');

            }

        }

        if(in_array('iA', $arParserParams) !== false) {

            echo '<p style="color: green">Импорт отгрузок:</p>';



            $parser->importAvailable();

            echo '<p style="color: green">Отгрузки импортированы.</p>';

            delParserParam($arParserParams, 'iA');

        }

    }

} else {

    echo '<p style="color: red">Не заданы параметры импорта</p>';

}

?>