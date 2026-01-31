<?php
//Читаем текстовые данные POST-запроса
$submit = ( isset($_POST['submit']) ) ? intval($_POST['submit']) : false;
$decode = ( isset($_POST['decode']) ) ? intval($_POST['decode']) : false;
$message = ( isset($_POST['message']) ) ? htmlspecialchars($_POST['message']) : '';

////Проверим user-agent, хотя большого толку от такой проверки нет. См. статью.
//if ( $_SERVER['HTTP_USER_AGENT'] != '1C+Enterprise/8.1' )
//{
//    @header('HTTP/1.0 403 Forbidden');
//    die('Hacking attempt');
//}

if ( $submit )
{
    //Здесь работаем с содержимым переданного файла.
    $uploadFile = $_FILES['datafile'];
    $tmp_name = $uploadFile['tmp_name'];
    $data_filename = $uploadFile['name'];
    if ( !is_uploaded_file($tmp_name) )
    {
        die('Ошибка при загрузке файла ' . $data_filename);
    }
    else
    {
        //Считываем файл в строку
        $data = file_get_contents($tmp_name);

        if ($decode)
        {
            //При необходимости декодируем данные
            $data = base64_decode($data);
        }
        //Теперь нормальный файл можно сохранить на диске
        if ( !empty($data) && ($fp = @fopen('upload_from_1c/'.$data_filename, 'wb')) )
        {
            @fwrite($fp, $data);
            @fclose($fp);
        }
        else
        {
            die('Ошибка при записи файла ' . $data_filename);
        }
        @header('HTTP/1.1 200 Ok');
        @header('Content-type: text/html; charset=windows-1251');
        $answer = "\n" . 'Файл ' . $data_filename . ' успешно загружен. ' . "\n" . 'Переданное сообщение: ' . $message;
        print ($answer);
    }
}
?>