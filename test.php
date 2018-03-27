<?php 
require_once 'vendor/autoload.php';

$contacts = rapidweb\googlecontacts\factories\ContactFactory::getAll();

$phoneNumber = "+7(925)290-96-201";
$email = ""; // = "e_ka@inbox.ru";

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

$contact = findByPhoneOrEmail($contacts, $phoneNumber, $email);
var_dump($contact);

/*
if ($contact) {

    $contact->name = 'Test';
    $contact->phoneNumber = '07812363789';
    $contact->email = 'test@example.com';
    $contact->content = 'Note for example';
    $contactAfterUpdate = rapidweb\googlecontacts\factories\ContactFactory::submitUpdates($contact);

    var_dump($contact); 
} else {
    
    $name = "Frodo Baggins";
    $phoneNumber = "06439111222";
    $emailAddress = "frodo@example.com";
    $note = "Note for example";
    
    $newContact = rapidweb\googlecontacts\factories\ContactFactory::create($name, $phoneNumber, $emailAddress, $note);
}
*/

//$contact = rapidweb\googlecontacts\factories\ContactFactory::getBySelfURL($selfURL);
//var_dump($contacts);
?>