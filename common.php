<?php

include 'secret.php'; // contains $dbUser, $dbPwd, $smtpPwd, $smtpEmail


$mysqli = new mysqli('localhost', $dbUser, $dbPwd, 'kafedra-git');
$mysqli->query('SET NAMES utf8');


$scriptName = substr(strrchr($_SERVER['SCRIPT_FILENAME'], '/'), 1, -4);
if (!($scriptName === 'login' || $scriptName === 'login_p' || $scriptName === 'createpwd_p'))
{
    if (!(isset($_COOKIE['useremail']) && isset($_COOKIE['userpwd'])))
    {
        header('Location: login.php');
        exit;
    }
    $userid = Auth($_COOKIE['useremail'], $_COOKIE['userpwd']);
    if (!$userid)
    {
        header('Location: login.php');
        exit;
    }
}




$fieldInfo = array();

$fieldInfo['journals'] = array(
    'name'          => 'Журнал',
    'name_dat_lc'   => 'журнал',
    'fields'        => array(   'name'          => array('type' => 's', 'control' => 'text', 'def' => ''),
                                'impfactor'     => array('type' => 'd', 'control' => 'text', 'def' => ''),
                                'publisherid'   => array('type' => 'i', 'control' => 'select', 'def' => 0),
                                'sceventid'     => array('type' => 'i', 'control' => 'select', 'def' => 0),
                                'type'          => array('type' => 'i', 'control' => 'select', 'def' => 4),
                                'reviewed'      => array('type' => 'i', 'control' => 'checkbox', 'def' => 0),
                                'inscopus'      => array('type' => 'i', 'control' => 'checkbox', 'def' => 0),
                                'invak'         => array('type' => 'i', 'control' => 'checkbox', 'def' => 0),
                                'inrinc'        => array('type' => 'i', 'control' => 'checkbox', 'def' => 0),
                                'inwos'         => array('type' => 'i', 'control' => 'checkbox', 'def' => 0),
                                'inforeignindex'=> array('type' => 'i', 'control' => 'checkbox', 'def' => 0)));

$fieldInfo['authors'] = array(
    'name'          => 'Автор',
    'name_dat_lc'   => 'автора',
    'fields'        => array(   'name'          => array('type' => 's', 'control' => 'text', 'def' => ''),
                                'isforeign'     => array('type' => 'i', 'control' => 'checkbox', 'def' => 0),
                                'studentgroup'  => array('type' => 's', 'control' => 'text', 'def' => '')));

$fieldInfo['publishers'] = array(
    'name'          => 'Издательство',
    'name_dat_lc'   => 'издательство',
    'fields'        => array(   'name' => array('type' => 's', 'control' => 'text', 'def' => ''),
                                'type' => array('type' => 'i', 'control' => 'select', 'def' => 0),
                                'city' => array('type' => 's', 'control' => 'text', 'def' => '')));

$fieldInfo['scevents'] = array(
    'name'          => 'Научное мероприятие',
    'name_dat_lc'   => 'научное мероприятие',
    'fields'        => array(   'name'      => array('type' => 's', 'control' => 'text', 'def' => ''),
                                'type'      => array('type' => 'i', 'control' => 'select', 'def' => 0),
                                'level'     => array('type' => 'i', 'control' => 'select', 'def' => 0),
                                'status'    => array('type' => 'i', 'control' => 'select', 'def' => 0),
                                'year'      => array('type' => 'i', 'control' => 'text', 'def' => (int)date('Y')),
                                'place'     => array('type' => 's', 'control' => 'text', 'def' => ''),
                                'date'      => array('type' => 's', 'control' => 'text', 'def' => '')));

$fieldInfo['publications'] = array(
    'name'          => 'Публикация',
    'name_dat_lc'   => 'публикацию',
    'fields'        => array(   'name'              => array('type' => 's', 'control' => 'text', 'def' => ''),
                                'journalid'         => array('type' => 'i', 'control' => 'select', 'def' => 0),
                                'publisherid'       => array('type' => 'i', 'control' => 'select', 'def' => 0),
                                'year'              => array('type' => 'i', 'control' => 'text', 'def' => (int)date('Y')),
                                'journalnumber'     => array('type' => 's', 'control' => 'text', 'def' => ''),
                                'journalpagestart'  => array('type' => 'i', 'control' => 'text', 'def' => ''),
                                'journalpageend'    => array('type' => 'i', 'control' => 'text', 'def' => ''),
                                'numpages'          => array('type' => 'i', 'control' => 'text', 'def' => ''),
                                'type'              => array('type' => 'i', 'control' => 'select', 'def' => 1),//journal
                                'grif'              => array('type' => 'i', 'control' => 'select', 'def' => 0),
                                'tirazh'            => array('type' => 'i', 'control' => 'text', 'def' => ''),
                                'lang'              => array('type' => 'i', 'control' => 'select', 'def' => 1),//ru
                                'url'               => array('type' => 's', 'control' => 'text', 'def' => '')));

$fieldInfo['authorpublications'] = array(
    'name'          => null,
    'name_dat_lc'   => null,
    'fields'        => array(   'authorid'          => array('type' => 'l', 'control' => 'selectmulti', 'def' => null),
                                'publicationid'     => array('type' => 'i', 'control' => 'hidden', 'def' => 0)));

$fieldInfo['participations'] = array(
    'name'          => 'Участие в научном мероприятии',
    'name_dat_lc'   => null,
    'fields'        => array(   'authorid'          => array('type' => 'i', 'control' => 'select', 'def' => $userid),
                                'sceventid'         => array('type' => 'i', 'control' => 'select', 'def' => 0)));





//////////////////////// FUNCTIONS /////////////////////////////////////////////

function Auth($email, $pwd)
{
    return SelectId('authors', 'email:s AND pwd:s', array($email, sha1($pwd)));
}

function ShowHeader($title)
{
    global $scriptName, $userid, $fieldInfo;
    
    $title = htmlspecialchars($title);
    if ($scriptName === 'login')
    {
        $welcome = '';
        $data = '';
    }
    else
    {
        $user = Select('authors', $userid);
        $welcome = "Добро пожаловать, $user->welcomename |
                    <a href=\"javascript:logout()\">Выйти</a><br /><br />";
        
        $jsData = '<script type="text/javascript">var fieldInfo=[];';
        foreach ($fieldInfo as $table => $fields)
        {
            $str = '';
            foreach ($fields['fields'] as $colname => $colinfo)
            {
                $control = $colinfo['control'];
                if (strlen($str))
                    $str .= ',';
                $str .= "$colname:'$control'";
            }
            $jsData .= "fieldInfo['$table']={{$str}};";
        }
        $jsData .= '</script>';
    }
    
    echo "<!DOCTYPE html>
<html>
    <head>
        <meta http-equiv=\"Content-Type\" content=\"text/html; charset=UTF-8\" />
        <link rel=\"stylesheet\" type=\"text/css\" href=\"common2.css\" />
        <script type=\"text/javascript\" src=\"common2.js\"></script>$jsData
        <title>$title</title>
    </head>
    <body>$welcome";
}

function ShowFooter()
{
    echo '</body></html>';
}





function Query($query, $params)
{
    global $mysqli;
    
    $letters = '';
    $pos = 0;
    while (($pos = strpos($query, ':', $pos)) !== false)
    {
        if ($query[$pos + 1] === 's' || $query[$pos + 1] === 'k')
            $letters .= 's';
        elseif ($query[$pos + 1] === 'i')
            $letters .= 'i';
        else
            return false;

        $pos++;
    }
    
    if (!strlen($letters))
        return false;
    
    if (count($params) != strlen($letters))
        return false;
    
    
    $query = str_replace(':i', ' = ?', $query, $cnt1);
    $query = str_replace(':s', ' = ?', $query, $cnt2);
    $query = str_replace(':k', ' LIKE ?', $query, $cnt3);

    if ($cnt1 + $cnt2 + $cnt3 != strlen($letters))
        return false;
    
    
    
    $paramsRef = array();
    foreach($params as $key => $val)
        $paramsRef[$key] = &$params[$key];
    
    
    $arrLen = array_unshift($paramsRef, $letters);
    if ($arrLen != strlen($letters) + 1)
        return false;
    
    
    $stmt = $mysqli->prepare($query);
    $bSuc = call_user_func_array(array($stmt, 'bind_param'), $paramsRef);
    $bSuc = $stmt->execute();
    return $stmt;
}


function SelectId($table, $cond, $params)
{
    $stmt = Query("SELECT id FROM $table WHERE $cond", $params);
    $bSuc = $stmt->bind_result($id);
    $bSuc = $stmt->fetch();
    $bSuc = $stmt->close();
    return (int)$id;
}

function Select($table, $id)
{
    global $mysqli;
    $id = (int)$id;
    return $mysqli->query("SELECT * FROM $table WHERE id = $id")->fetch_object();
}


function MysqliExec($table, $cols, $vars, $op)
{
    global $mysqli;

    $numFields = count($cols);

    if ($op === 'insert')
        $minNumFields = 1;
    elseif ($op === 'update')
        $minNumFields = 2;
    
    if ($numFields < $minNumFields)
        return false;
    
    
    $fields = '';
    $letters = '';
    $asks = '';
    for ($i = 0; $i < $numFields; $i++)
    {
        $arr = explode(':', $cols[$i]);

        
        if ($arr[1] === 'i' || $arr[1] === 's' || $arr[1] === 'd')
        {
            $ask = '?';
            $letters .= $arr[1];
        }
        elseif ($arr[1] === 'null')
        {
            $ask = 'NULL';
        }
        else
            return false;
        
        
        if ($op === 'insert')
        {
            if ($i >= 1) // 2nd ... last
            {
                $fields .= ',';
                $asks .= ',';
            }
            $fields .= $arr[0];
            $asks .= $ask;
        }
        elseif ($op === 'update')
        {
            if ($i >= 1 && $i < $numFields - 1) // 2nd ... last-1
                $fields .= ',';
            elseif ($i == $numFields - 1) // last
                $fields .= ' WHERE ';
            $fields .= $arr[0] . '=' . $ask;
        }
    }
    

    if ($op === 'insert')
        $query = "INSERT INTO $table ($fields) VALUES ($asks)";
    elseif ($op === 'update')
        $query = "UPDATE $table SET $fields";
    
    
    $res = $mysqli->prepare($query);

    
    $paramsRef = array();
    foreach($vars as $key => $val)
        $paramsRef[$key] = &$vars[$key];
    
    $arrLen = array_unshift($paramsRef, $letters);
    if ($arrLen != strlen($letters) + 1)
        return false;

    $bSuc = call_user_func_array(array($res, 'bind_param'), $paramsRef);
    
    
    

    $bSuc = $res->execute();
    
    return $bSuc;
}


function Upsert($table, $id, $cols, $vars)
{
    global $mysqli;
    
    if ($id)
    {
        $op = 'update';
        $cols[] = 'id:i';
        $vars[] = $id;
    }
    else
    {
        $op = 'insert';
    }

    $bSuc = MysqliExec($table, $cols, $vars, $op);
    
    return $bSuc ? ($id ? $id : $mysqli->insert_id) : 0;
}




function SelectObjects($query)
{
    global $mysqli;
    $arr = array();
    $res = $mysqli->query($query);
    if ($res)
        while ($obj = $res->fetch_object())
            $arr[] = $obj;
    return $arr;
}



function GenerateRandomString($length)
{
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $randomString = '';
    for ($i = 0; $i < $length; $i++)
        $randomString .= $characters[rand(0, strlen($characters) - 1)];
    return $randomString;
}


function GetOptionsString3($objects, $selid = 0, $addNone = false)
{
    if ($addNone)
    {
        $obj = new stdClass();
        $obj->id = 0;
        $obj->name = '—';
        array_unshift($objects, $obj);
    }
    $res = '';
    foreach ($objects as $obj)
    {
        $selStr = $selid == $obj->id ? ' selected="selected"' : '';
        $nameSafe = htmlspecialchars($obj->name);
	$res .= "<option$selStr value=\"$obj->id\" title=\"$nameSafe\">$nameSafe</option>";
    }
    return $res;
}

function GetOptionsString2($table, $selid = 0, $addNone = false)
{
    $objects = SelectObjects("SELECT * FROM $table ORDER BY name");
    return GetOptionsString3($objects, $selid, $addNone);
}


function GetInputData($table, &$fieldValues, &$cols, &$vars)
{
    global $fieldInfo;
    
    $fieldValues = array();
    $cols = array();
    $vars = array();
    
    $hasList = false;
    
    foreach ($fieldInfo[$table]['fields'] as $fieldName => $colInfo)
    {
        if (!isset($_GET[$fieldName]))
        {
            echo "Error: no data ($fieldName)";
            exit;
        }
        $val = trim($_GET[$fieldName]);
        $type = $colInfo['type'];
        switch ($type)
        {
        case 'i': $val = (int)$val; break;
        case 'd': $val = (float)$val; break;
        case 'l':
            {
                $val = strlen($val) ? explode('_', $val) : array();
                foreach ($val as &$el)
                    $el = (int)$el;
                $hasList = true;
                break;
            }
        }
        $fieldValues[$fieldName] = $val;
        $cols[] = "$fieldName:$type";
        $vars[] = $val;
    }
    
    return $hasList;
}




function UpsertList($table, $fieldValues)
{
    global $mysqli, $fieldInfo;

    if (count($fieldInfo[$table]['fields']) != 2)
    {
        echo "Error: incorrect table structure ($table): must be two fields";
        exit;
    }

    
    $list = 0;
    $fk = 0;
    foreach ($fieldValues as $column => $value)
    {
        $fieldType = $fieldInfo[$table]['fields'][$column]['type'];
        if ($fieldType === 'l')
        {
            $list = $value;
            $listName = $column;
        }
        elseif ($fieldType === 'i')
        {
            $fk = $value;
            $fkName = $column;
        }
    }    

    if (!($list && $fk))
    {
        echo "Error: incorrect table structure ($table): must contain integer and list fields";
        exit;
    }
    
    
    
    $values = '';
    foreach ($list as $elem)
    {
        if (strlen($values))
            $values .= ',';
        $values .= "($fk,$elem)";
    }

    if (!$mysqli->query("DELETE FROM $table WHERE $fkName = $fk"))
    {
        echo "Error: delete from '$table'";
        exit;
    }
    
    return $mysqli->query("INSERT INTO $table ($fkName,$listName) VALUES $values");
}



function CheckInputData($table, $id, $fieldValues)
{
    global $userid, $mysqli;
    
    switch ($table)
    {
    case 'journals':
        {
            if (!strlen($fieldValues['name']))
            {
                echo 'Введите название журнала';
                exit;
            }
            if (!$fieldValues['publisherid'])
            {
                echo 'Выберите издательство';
                exit;
            }
            break;
        }
    case 'authors':
        {
            $res = $mysqli->query("SELECT COUNT(*) AS isteacher FROM authors WHERE id = $id AND email <> ''");
            if ($res->fetch_object()->isteacher)
            {
                echo 'Операция запрещена';
                exit;
            }
            if (!strlen($fieldValues['name']))
            {
                echo 'Введите фамилию и инициалы автора';
                exit;
            }
            break;
        }
    case 'publishers':
        {
            if (!strlen($fieldValues['name']))
            {
                echo 'Введите название издательства';
                exit;
            }
            break;
        }
    case 'scevents':
        {
            if (!strlen($fieldValues['name']))
            {
                echo 'Введите название научного мероприятия';
                exit;
            }
            if (!strlen($fieldValues['place']))
            {
                echo 'Укажите место проведения научного мероприятия';
                exit;
            }
            if (!strlen($fieldValues['date']))
            {
                echo 'Укажите дату проведения научного мероприятия';
                exit;
            }
            $curYear = (int)date('Y');
            if (!($fieldValues['year'] > 1900 && $fieldValues['year'] <= $curYear))
            {
                echo 'Укажите, в каком году проходило научное мероприятие';
                exit;
            }
            break;
        }
    case 'publications':
        {
            CheckPublicationRights($id);
            if (!strlen($fieldValues['name']))
            {
                echo 'Введите название публикации';
                exit;
            }
            $curYear = (int)date('Y');
            if (!($fieldValues['year'] > 1900 && $fieldValues['year'] <= $curYear))
            {
                echo 'Укажите год издания';
                exit;
            }
            if ($fieldValues['type'] == 1) // article
            {
                if (!$fieldValues['journalid'])
                {
                    echo 'Выберите журнал';
                    exit;
                }
                if (!($fieldValues['journalpagestart'] && $fieldValues['journalpageend'] &&
                        $fieldValues['journalpagestart'] <= $fieldValues['journalpageend']))
                {
                    echo 'Укажите номера страниц';
                    exit;
                }
            }
            else // book
            {
                if (!$fieldValues['publisherid'])
                {
                    echo 'Выберите издательство';
                    exit;
                }
                if (!$fieldValues['numpages'])
                {
                    echo 'Укажите число страниц';
                    exit;
                }
                if (!$fieldValues['tirazh'])
                {
                    echo 'Укажите тираж';
                    exit;
                }
            }
            break;
        }
    case 'authorpublications':
        {
            if (!$fieldValues['publicationid'])
            {
                echo 'Не указана публикация';
                exit;
            }
            if (!count($fieldValues['authorid']))
            {
                echo 'Выберите авторов';
                exit;
            }
            if (!in_array($userid, $fieldValues['authorid']))
            {
                $range = '';
                foreach ($fieldValues['authorid'] as $authorid)
                {
                    if (strlen($range))
                        $range .= ',';
                    $range .= $authorid;
                }
                $res = $mysqli->query("SELECT COUNT(*) AS cnt_teachers FROM authors WHERE email <> '' AND id IN ($range)");
                if ($res->fetch_object()->cnt_teachers)
                {
                    echo 'Неверно выбраны авторы';
                    exit;
                }
            }
            break;
        }
    case 'participations':
        {
            CheckParticipationRights($id);
            $authorid = $fieldValues['authorid'];
            $res = $mysqli->query("SELECT COUNT(*) AS is_another_teacher FROM authors
                                    WHERE email <> '' AND id = $authorid AND id <> $userid");
            if ($res->fetch_object()->is_another_teacher)
            {
                echo 'Неверно выбран участник';
                exit;
            }
            break;
        }
    }
}


function CheckPublicationRights($id)
{
    global $userid, $mysqli;
    $res = $mysqli->query("SELECT COUNT(*) AS hasteachers FROM authorpublications
                            LEFT OUTER JOIN authors ON authors.id = authorpublications.authorid
                            WHERE authors.email <> '' AND authorpublications.publicationid = $id");
    if ($res->fetch_object()->hasteachers)
    {
        $res = $mysqli->query("SELECT COUNT(*) AS isuserauthor FROM authorpublications
                                WHERE authorid = $userid AND publicationid = $id");
        if (!$res->fetch_object()->isuserauthor)
        {
            echo 'Операция запрещена';
            exit;
        }
    }
}


function CheckParticipationRights($id)
{
    global $userid, $mysqli;
    $res = $mysqli->query("SELECT COUNT(*) AS is_another_teacher FROM participations
                            LEFT OUTER JOIN authors ON authors.id = participations.authorid
                            WHERE authors.email <> '' AND authors.id <> $userid AND participations.id = $id");
    if ($res->fetch_object()->is_another_teacher)
    {
        echo 'Операция запрещена';
        exit;
    }
}



function BeforeDelete($table, $id)
{
    global $mysqli;
    
    switch ($table)
    {
    case 'publications':
        {
            CheckPublicationRights($id);
            break;
        }
    case 'participations':
        {
            CheckParticipationRights($id);
            break;
        }
    case 'authors':
        {
            $res = $mysqli->query("SELECT COUNT(*) AS isteacher FROM authors WHERE id = $id AND email <> ''");
            if ($res->fetch_object()->isteacher)
            {
                echo 'Операция запрещена';
                exit;
            }
            $res = $mysqli->query("SELECT COUNT(*) AS cnt_publ FROM authorpublications WHERE authorid = $id");
            if ($res->fetch_object()->cnt_publ > 0)
            {
                echo 'Нельзя удалить автора, имеющего публикации';
                exit;
            }
            $res = $mysqli->query("SELECT COUNT(*) AS cnt_ev FROM participations WHERE authorid = $id");
            if ($res->fetch_object()->cnt_ev > 0)
            {
                echo 'Нельзя удалить автора, указанного в научном мероприятии';
                exit;
            }
            break;
        }
    case 'journals':
        {
            $res = $mysqli->query("SELECT COUNT(*) AS cnt_publ FROM publications WHERE journalid = $id");
            if ($res->fetch_object()->cnt_publ > 0)
            {
                echo 'Нельзя удалить журнал, который указан в публикациях';
                exit;
            }
            break;
        }
    case 'publishers':
        {
            $res = $mysqli->query("SELECT COUNT(*) AS cnt_journals FROM journals WHERE publisherid = $id");
            if ($res->fetch_object()->cnt_journals > 0)
            {
                echo 'Нельзя удалить издательство, которое указано в журналах';
                exit;
            }
            $res = $mysqli->query("SELECT COUNT(*) AS cnt_publ FROM publications WHERE type <> 1 AND publisherid = $id");
            if ($res->fetch_object()->cnt_publ > 0)
            {
                echo 'Нельзя удалить издательство, которое указано в публикациях';
                exit;
            }
            break;
        }
    case 'scevents':
        {
            $res = $mysqli->query("SELECT COUNT(*) AS cnt_journals FROM journals WHERE sceventid = $id");
            if ($res->fetch_object()->cnt_journals > 0)
            {
                echo 'Нельзя удалить научное мероприятие, которое указано в журналах';
                exit;
            }
            $res = $mysqli->query("SELECT COUNT(*) AS cnt_ev FROM participations WHERE sceventid = $id");
            if ($res->fetch_object()->cnt_ev > 0)
            {
                echo 'Нельзя удалить научное мероприятие, в котором имеются участники';
                exit;
            }
            break;
        }
    }
}

function AfterDelete($table, $id)
{
    global $mysqli;
    
    switch ($table)
    {
    case 'publications':
        {
            $mysqli->query("DELETE FROM authorpublications WHERE publicationid = $id");
            break;
        }
    }
}


function GetHtmlElement($table, $field, $attribs = '', $inner = '', $parent = '', $obj = 0)
{
    global $fieldInfo;

    if (strlen($attribs))
        $attribs .= ' ';
    $attribs .= "id=\"{$parent}{$table}{$field}\" ";
    
    switch ($fieldInfo[$table]['fields'][$field]['control'])
    {
    case 'text':
        {
            $attribs .= 'type="text" ';
            if ($obj)
            {
                $text = htmlspecialchars($obj->$field);
                $attribs .= "value=\"$text\" ";
            }
            return "<input $attribs/>";
        }
    case 'checkbox':
        {
            if ($obj && $obj->$field)
                $attribs .= 'checked="checked" ';
            $attribs .= 'type="checkbox" ';
            return "<input $attribs/>";
        }
    case 'hidden':
        {
            $attribs .= 'type="hidden" ';
            if ($obj)
                $attribs .= "value=\"{$obj->$field}\" ";
            return "<input $attribs/>";
        }
    case 'selectmulti':
    case 'select':
        {
            return "<select $attribs>$inner</select>";
        }
    }
}

?>