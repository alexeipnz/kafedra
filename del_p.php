<?php

include 'common.php';


if (!isset($_GET['table']))
{
    echo 'Error: no data (table)';
    exit;
}
$table = $_GET['table'];
if (!array_key_exists($table, $fieldInfo))
{
    echo 'Error: incorrect data (table)';
    exit;
}


if (!isset($_GET['id']))
{
    echo 'Error: no data (id)';
    exit;
}
$id = (int)$_GET['id'];


BeforeDelete($table, $id);

if (!$mysqli->query("DELETE FROM $table WHERE id = $id"))
{
    echo 'Error: could not delete from db';
    exit;
}

AfterDelete($table, $id);


echo 'success';

?>