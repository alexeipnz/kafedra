<?php

include 'common.php';

ShowHeader('Кафедра САПР');

echo 'Email: <input type="text" id="email" />';
echo '<br />';
echo 'Пароль: <input type="password" id="pwd" />';
echo ' <a href="javascript:createPwd()">Сгенерировать</a>';
echo '<br />';
echo '<a href="javascript:login()">Войти</a>';

ShowFooter();

?>