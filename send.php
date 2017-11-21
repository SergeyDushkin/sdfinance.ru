<?php   
    $name = strip_tags(htmlspecialchars($_POST['name']));
    $date = strip_tags(htmlspecialchars($_POST['date']));
    $email_address = strip_tags(htmlspecialchars($_POST['email']));
    $phone = strip_tags(htmlspecialchars($_POST['phone']));
    $message_email = strip_tags(htmlspecialchars($_POST['message']));

    // Create the email and send the message  echo "No arguments Provided!";

    $headers = "Content-Type: text/html; charset=UTF-8";
    $to = "hi@sdfinance.ru";

    $tz_object = new DateTimeZone('Europe/Moscow');
    $now = new DateTime();
    $now->setTimezone($tz_object);
    $subject = $now->format('Y-m-d H:i:s');
    $message = "Имя: " . $name . "<br/>" .	
		"Телефон: " . $phone . "<br/>" . 
		"E-mail: " . $email_address . "<br/>" .
		"Дата рождения: " . $date . "<br/>" .
		"Сообщение: " . $message_email . "<br/>";

    var_dump(mail($to,$subject,$message,$headers));

    return true;
?>