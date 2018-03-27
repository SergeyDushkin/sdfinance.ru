<?php   
	require_once 'vendor/autoload.php';

    $name = strip_tags(htmlspecialchars($_POST['name']));
    $date = strip_tags(htmlspecialchars($_POST['date']));
//    $email_address = strip_tags(htmlspecialchars($_POST['email']));
    $phone = strip_tags(htmlspecialchars($_POST['phone']));
    $message_email = strip_tags(htmlspecialchars($_POST['message']));

    $now = new DateTime();
    $now->setTimezone($tz_object);
    
    // Create the email and send the message  echo "No arguments Provided!";
/*
    $headers = "Content-Type: text/html; charset=UTF-8";
    $to = "hi@sdfinance.ru";

    $tz_object = new DateTimeZone('Europe/Moscow');
    $subject = $now->format('Y-m-d H:i:s');
    $message = "Имя: " . $name . "<br/>" .	
		"Телефон: " . $phone . "<br/>" . 
		"Год рождения: " . $date . "<br/>" .
		"Сообщение: " . $message_email . "<br/>";

    var_dump(mail($to,$subject,$message,$headers));
*/
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

	$contacts = rapidweb\googlecontacts\factories\ContactFactory::getAll();

	function findByPhoneOrEmail($arg_1, $arg_2, $arg_3)
	{
		foreach ($arg_1 as $xmlContactsEntry) {
	
			if (property_exists($xmlContactsEntry, 'phoneNumber'))
			{
				$a = $xmlContactsEntry->phoneNumber;
		
				foreach($a as $p) {
					$n = $p['number'];
					$letters = array(' ', '-', '(', ')');
					if (str_replace($letters, '', $n) == str_replace($letters, '', $arg_2)) {
						return $xmlContactsEntry;
					}
				}
			}
		
			if (property_exists($xmlContactsEntry, 'email'))
			{
				$b = (array) $xmlContactsEntry->email;
	
				foreach($b as $p) {
					$n = $p['email'];
					if ($n == $arg_3) {
						return $xmlContactsEntry;
					}
				}
			}
		}
	}
	
	$fdate = $now->format('Y-m-d');
	$email = "";

	$contact = findByPhoneOrEmail($contacts, $phoneNumber, $email);

	if ($contact) {

		$contact->name = $name . " " . $fdate;
		$contact->phoneNumber = $phone;
		//$contact->email = $email_address;
		$contact->content = $message;
		$contactAfterUpdate = rapidweb\googlecontacts\factories\ContactFactory::submitUpdates($contact);
	} else {
		
		$name = $name . " " . $fdate;
		$phoneNumber = $phone;
		$emailAddress = $email;
		$note = $message;
		
		$newContact = rapidweb\googlecontacts\factories\ContactFactory::create($name, $phoneNumber, $emailAddress, $note);
	}
	return true;
?>