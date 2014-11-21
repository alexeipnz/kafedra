<?php

include 'common.php';

if (!(isset($_GET['email']) && isset($_GET['pwd'])))
{
    echo 'Error: no email or pwd';
    exit;
}

$email = trim($_GET['email']);
$pwd = $_GET['pwd'];

$uid = Auth($email, $pwd);
if (!$uid)
{
    echo 'Неверный email или пароль';
    exit;
}

setcookie('useremail', $email, time() + 60 * 60 * 24 * 30, '/');
setcookie('userpwd', $pwd, time() + 60 * 60 * 24 * 30, '/');

echo "success|$uid";


?>