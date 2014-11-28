<?php

include 'common.php';


if (!isset($_GET['email']))
{
    echo 'Error: no email';
    exit;
}

$email = trim($_GET['email']);
if (!strlen($email))
{
    echo 'Введите свой email';
    exit;
}

$uid = SelectId('authors', 'email:s', array($email));
if (!$uid)
{
    echo 'Указанный email не найден';
    exit;
}


$pwd = GenerateRandomString(10);

$body = "<html><body>$pwd</body></html>";
$subj = base64_encode('Пароль для кафедрасапр.рф');



////////////////////////////////////////////////////////////////////////////////
require 'PHPMailer/PHPMailerAutoload.php';
$mail = new PHPMailer;
$mail->isSMTP();
$mail->Host = 'smtp.gmail.com';
$mail->Port = 587;
$mail->SMTPSecure = 'tls';
$mail->SMTPAuth = true;
$mail->Username = $smtpEmail;
$mail->Password = $smtpPwd;
$from = base64_encode('Почтовый робот');
$mail->FromName = "=?UTF-8?B?$from?=";
$mail->addAddress($email);
$mail->Subject = "=?UTF-8?B?$subj?=";
$mail->CharSet = 'UTF-8';
$mail->isHTML();
$mail->Body = $body;
$mailIsSent = $mail->send();
////////////////////////////////////////////////////////////////////////////////
if (!$mailIsSent)
{
    echo 'Error: could not send email';
    exit;
}


if (!Upsert('authors', $uid, array('pwd:s'), array(sha1($pwd))))
{
    echo 'Error: could not update db';
    exit;
}

echo 'Пароль выслан на указанный email';

?>