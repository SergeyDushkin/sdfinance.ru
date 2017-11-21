<?php   
    $name = strip_tags(htmlspecialchars($_POST['name']));
    $date = strip_tags(htmlspecialchars($_POST['date']));
    $email_address = strip_tags(htmlspecialchars($_POST['email']));
    $phone = strip_tags(htmlspecialchars($_POST['phone']));
    $message_email = strip_tags(htmlspecialchars($_POST['message']));

    // Create the email and send the message  echo "No arguments Provided!";

    $to = "hi@sdfinance.ru, leirbythe@gmail.com";
    $subject = "Форма обратной связи от: $email_address $phone";
    $message = "Вы получили новое сообщение через форму обратной связи.\n\n"."Отправитель: $email_address\n\nИмя: $name\n\nДата рождения: $date\n\nСообщение: $message_email";

    var_dump(mail($to,$subject,$message));

    return true;
?>