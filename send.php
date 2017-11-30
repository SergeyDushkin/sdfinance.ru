<?php   
    $name = strip_tags(htmlspecialchars($_POST['name']));
    $date = strip_tags(htmlspecialchars($_POST['date']));
//    $email_address = strip_tags(htmlspecialchars($_POST['email']));
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
		"Год рождения: " . $date . "<br/>" .
		"Сообщение: " . $message_email . "<br/>";

    var_dump(mail($to,$subject,$message,$headers));

	// send message from Telegram bot
	$token = "330835026:AAF_5l8PMe7vxq-H-zn28BFucoLhAyCZklQ";
	$chatid = "312593152";

    	$message = "Имя: " . $name . "\n" .	
		"Телефон: " . $phone . "\n" . 
		"Год рождения: " . $date . "\n" .
		"Сообщение: " . $message_email . "\n";

	$url = "https://api.telegram.org/bot" . $token . "/sendMessage?chat_id=" . $chatid . "&text=" . urlencode($message);
	$ch = curl_init();
	$optArray = array(
		CURLOPT_URL => $url,
		CURLOPT_RETURNTRANSFER => true
    	);
    	curl_setopt_array($ch, $optArray);
    	$result = curl_exec($ch);
    	curl_close($ch);

	return true;
?>