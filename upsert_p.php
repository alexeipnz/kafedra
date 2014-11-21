<?php

include 'common.php';

if (!isset($_GET['id']))
{
    echo 'Error: no data (id)';
    exit;
}
$id = (int)$_GET['id'];

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

$hasList = GetInputData($table, $fieldValues, $cols, $vars);
CheckInputData($table, $id, $fieldValues);

$resId = $hasList ? UpsertList($table, $fieldValues) : Upsert($table, $id, $cols, $vars);
if (!$resId)
{
    echo "Error: could not insert/update table '$table'";
    exit;
}

echo 'success|' . $resId;

?>