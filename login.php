<?php

include 'common.php';

ShowHeader('Кафедра САПР');
?>
<form role="form">
<div class="form-group">
<?php
echo 'Email: <input type="text" id="email" class="form-control"/>';
echo '<br />';
echo 'Пароль: <input type="password" id="pwd" class="form-control"/>';
echo ' <a href="javascript:createPwd()" class="btn">Сгенерировать</a>';
echo '<br />';
echo '<a href="javascript:login()" class="btn">Войти</a>';
?>
</div>
</form>
<?php
ShowFooter();

?>