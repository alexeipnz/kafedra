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



if (!isset($_GET['parent']))
{
    echo 'Error: no data (parent)';
    exit;
}
$parent = $_GET['parent'];
if (strlen($parent) && !array_key_exists($parent, $fieldInfo))
{
    echo 'Error: incorrect data (parent)';
    exit;
}




if (!isset($_GET['id']))
{
    echo "Error: no data ($table.id)";
    exit;
}
$id = (int)$_GET['id'];



if ($id)
{
    $obj = Select($table, $id);
    $btnName = 'Сохранить';
}
else
{
    $obj = new stdClass();
    $obj->id = $id;
    foreach ($fieldInfo[$table]['fields'] as $colName => $colInfo)
        $obj->$colName = $colInfo['def'];
    $btnName = 'Добавить';
}


$name = $fieldInfo[$table]['name'];
echo "<b>$name</b><br />";

switch ($table)
{
    case 'authors': ShowAuthorForm($table, $obj); break;
    case 'journals': ShowJournalForm($table, $obj); break;
    case 'publications': ShowPublicationForm($table, $obj); break;
    case 'scevents': ShowScEventForm($table, $obj); break;
    case 'publishers': ShowPublisherForm($table, $obj, $parent); break;
    case 'participations': ShowParticipationForm($table, $obj); break;
}

echo "<a href=\"javascript:upsert('$table','$parent',$id)\">$btnName</a>";


////////////////////////////////////////////////////////////////////////////////


function ShowAuthorForm($table, $obj)
{
    echo "<form class='form-horizontal' role='form'>";

    echo "<div class='form-group'>";
    echo "<label for='authorsname' class='control-label col-sm-4'>Фамилия И. О.:</label>";
    echo GetHtmlElement($table, 'name', '', '', '', $obj);
    echo '<br />';
    echo "</div>";

    echo "<div class='form-group'>";
    echo "<label for='authorsisforeign' class='control-label col-sm-4'>Иностранный:</label>";
//    echo 'Иностранный: ';
    echo GetHtmlElement($table, 'isforeign', '', '', '', $obj);
    echo '<br />';
    echo "</div>";

    echo "<div class='form-group'>";
    echo "<label for='authorsstudentgroup' class='control-label col-sm-4'>Студент группы </label>";
//    echo 'Студент группы ';
    echo GetHtmlElement($table, 'studentgroup', '', '', '', $obj);
    echo '<br />';
    echo "</div>";

    echo "</form>";
}


function ShowJournalForm($table, $obj)
{
    $publishers = GetOptionsString2('publishers', $obj->publisherid);
    $types = GetOptionsString2('journaltypes', $obj->type);
    $scevents = GetOptionsString2('scevents', $obj->sceventid, true);

    echo "<form class='form-horizontal>";

    echo "<div class='form-group'>";
    echo "<label for='journalsname' class='control-label col-sm-4'>Название: </label>";
//    echo 'Название: ';
    echo GetHtmlElement($table, 'name', 'class="publname"', '', '', $obj);
    echo "</div>";
    echo '<br />';

    echo "<div class='form-group'>";
    echo "<label for='journalsname' class='control-label col-sm-4'>Тип: </label>";
//    echo 'Тип: ';
    echo GetHtmlElement($table, 'type', '', $types);
    echo "</div>";
    echo '<br />';

    echo "<div class='form-group'>";
    echo "<label for='journalsname' class='control-label col-sm-4'>Импакт-фактор: </label>";
    echo 'Импакт-фактор: ';
    echo GetHtmlElement($table, 'impfactor', 'class="publyear"', '', '', $obj);
    echo '<div class="space"></div>РИНЦ: ';
    echo GetHtmlElement($table, 'inrinc', '', '', '', $obj);
    echo '<div class="space"></div>Рецензируемый: ';
    echo GetHtmlElement($table, 'reviewed', '', '', '', $obj);
    echo '<div class="space"></div>ВАК: ';
    echo GetHtmlElement($table, 'invak', '', '', '', $obj);
    echo "</div>";
    echo '<br />';

    echo "<div class='form-group'>";
    echo 'Scopus: ';
    echo GetHtmlElement($table, 'inscopus', '', '', '', $obj);
    echo '<div class="space"></div>Web of Science: ';
    echo GetHtmlElement($table, 'inwos', '', '', '', $obj);
    echo '<div class="space"></div>Другие иностранные индексы: ';
    echo GetHtmlElement($table, 'inforeignindex', '', '', '', $obj);
    echo "</div>";
    echo '<br />';

    echo '<div class="group">';
    echo 'Издательство: ';
    echo GetHtmlElement($table, 'publisherid', '', $publishers);
    echo ' <a href="javascript:store(\'publisherparent\',\'journals\');showBlock(\'publishers\')">Новое</a>';
    echo ' | <a href="javascript:store(\'publisherparent\',\'journals\');showBlock(\'publishers\',getSel(\'journalspublisherid\'))">Редактировать</a>';
    echo ' | <a href="javascript:del(\'publishers\',getSel(\'journalspublisherid\'))">Удалить</a><br />';
    echo '<div class="inlinedata" id="journalspublishers"></div>';
    echo '</div><br />';

    echo '<div class="group">';
    echo 'Научное мероприятие: ';
    echo GetHtmlElement($table, 'sceventid', '', $scevents);
    echo ' <a href="javascript:showBlock(\'scevents\')">Новое</a>';
    echo ' | <a href="javascript:showBlock(\'scevents\',getSel(\'journalssceventid\'))">Редактировать</a>';
    echo ' | <a href="javascript:del(\'scevents\',getSel(\'journalssceventid\'))">Удалить</a><br />';
    echo '<div class="inlinedata" id="scevents"></div>';
    echo '</div><br />';

    echo "</form>";
}



function ShowPublicationForm($table, $obj)
{
    global $userid;
    
    if ($obj->id)
    {
        $authorids = SelectObjects("SELECT authorid FROM authorpublications WHERE publicationid = $obj->id");
        $authorsRange = '';
        foreach ($authorids as $authorid)
        {
            if (strlen($authorsRange))
                $authorsRange .= ',';
            $authorsRange .= $authorid->authorid;
        }
    }
    else
    {
        $authorsRange = $userid;
    }


    $langs = GetOptionsString2('langtypes', $obj->lang);
    $types = GetOptionsString2('publicationtypes', $obj->type);
    $journals = GetOptionsString2('journals', $obj->journalid);
    $publishers = GetOptionsString2('publishers', $obj->publisherid);
    $orders = GetOptionsString2('ordertypes', $obj->grif, true);


    $authors_publ = SelectObjects("SELECT * FROM authors WHERE id IN ($authorsRange) ORDER BY name");
    $authors_other = SelectObjects("SELECT * FROM authors WHERE id NOT IN ($authorsRange) ORDER BY name");
    $authors_publ = GetOptionsString3($authors_publ);
    $authors_other = GetOptionsString3($authors_other);





    echo 'Название: ';
    echo GetHtmlElement($table, 'name', 'class="publname"', '', '', $obj);
    echo '<br />';

    echo '<div class="group highlight">';
    echo 'Авторы:<br />';
    $objAuth = new stdClass();
    $objAuth->publicationid = $obj->id;
    echo GetHtmlElement('authorpublications', 'publicationid', '', '', '', $objAuth);
    
    
    echo '<div class="group">';
    echo GetHtmlElement('authorpublications', 'authorid', 'size="5" class="authorlist"', $authors_publ);
    echo '<br /><a href="javascript:store(\'authlist\',\'authorpublicationsauthorid\');showBlock(\'authors\',getSel(store.authlist))">Редактировать</a>';
    echo '<br /><a href="javascript:showBlock(\'authors\')">Новый</a>';
    echo '</div>';
    echo '<div class="group aligntop">';
    echo '<br /><a href="javascript:moveOption(\'authall\',\'authorpublicationsauthorid\')">&lt;&lt;====</a>';
    echo '<br /><a href="javascript:moveOption(\'authorpublicationsauthorid\',\'authall\')">====&gt;&gt;</a>';
    echo '</div>';
    echo '<div class="group">';
    echo "<select size=\"5\" class=\"authorlist\" id=\"authall\">$authors_other</select>";
    echo '<br /><a href="javascript:store(\'authlist\',\'authall\');showBlock(\'authors\',getSel(store.authlist))">Редактировать</a>';
    echo '<br /><a href="javascript:del(\'authors\',getSel(\'authall\'))">Удалить</a><br />';
    echo '</div>';
    echo '<br />';
    
    
    echo '<div class="inlinedata" id="authors"></div>';
    echo '</div>'; // group highlight
    echo '<br />';

    echo 'Год издания: ';
    echo GetHtmlElement($table, 'year', 'class="publyear"', '', '', $obj);
    echo '<div class="space"></div>Язык: ';
    echo GetHtmlElement($table, 'lang', '', $langs);
    echo '<div class="space"></div>URL с выходными данными: ';
    echo GetHtmlElement($table, 'url', '', '', '', $obj);
    echo '<br />';

    echo 'Тип: ';
    echo GetHtmlElement($table, 'type', 'onchange="onPublTypeChange()"', $types);
    echo '<br />';

    echo '<div id="journaldata">';

    echo '<div class="group">';
    echo 'Журнал (сборник): ';
    echo GetHtmlElement($table, 'journalid', '', $journals);
    echo ' <a href="javascript:showBlock(\'journals\')">Новый</a>';
    echo ' | <a href="javascript:showBlock(\'journals\',getSel(\'publicationsjournalid\'))">Редактировать</a>';
    echo ' | <a href="javascript:del(\'journals\',getSel(\'publicationsjournalid\'))">Удалить</a><br />';
    echo '<div class="inlinedata" id="journals"></div>';
    echo '</div>'; // group
    echo '<br />';

    echo 'Номер журнала: ';
    echo GetHtmlElement($table, 'journalnumber', '', '', '', $obj);
    echo ' Начальная страница: ';
    echo GetHtmlElement($table, 'journalpagestart', 'class="publyear"', '', '', $obj);
    echo ' Конечная страница: ';
    echo GetHtmlElement($table, 'journalpageend', 'class="publyear"', '', '', $obj);
    echo '<br />';

    echo '</div>'; // journaldata
    echo '<div id="bookdata">';

    echo '<div class="group">';
    echo 'Издательство: ';
    echo GetHtmlElement($table, 'publisherid', '', $publishers);
    echo ' <a href="javascript:store(\'publisherparent\',\'publications\');showBlock(\'publishers\')">Новое</a>';
    echo ' | <a href="javascript:store(\'publisherparent\',\'publications\');showBlock(\'publishers\',getSel(\'publicationspublisherid\'))">Редактировать</a>';
    echo ' | <a href="javascript:del(\'publishers\',getSel(\'publicationspublisherid\'))">Удалить</a><br />';
    echo '<div class="inlinedata" id="publicationspublishers"></div>';
    echo '</div>'; // group
    echo '<br />';

    echo 'Гриф: ';
    echo GetHtmlElement($table, 'grif', '', $orders);
    echo ' Число страниц: ';
    echo GetHtmlElement($table, 'numpages', 'class="publyear"', '', '', $obj);
    echo ' Тираж: ';
    echo GetHtmlElement($table, 'tirazh', 'class="publyear"', '', '', $obj);
    echo '<br />';
    
    echo '</div>'; // bookdata
}



function ShowScEventForm($table, $obj)
{
    $types = GetOptionsString2('evtypes', $obj->type, true);
    $levels = GetOptionsString2('evlevels', $obj->level, true);
    $statuses = GetOptionsString2('evstatuses', $obj->status, true);


    echo 'Название: ';
    echo GetHtmlElement($table, 'name', 'class="publname"', '', '', $obj);
    echo '<br />';

    echo 'Место: ';
    echo GetHtmlElement($table, 'place', '', '', '', $obj);
    echo '<br />';

    echo 'Дата: ';
    echo GetHtmlElement($table, 'date', '', '', '', $obj);
    echo ' Год: ';
    echo GetHtmlElement($table, 'year', 'class="publyear"', '', '', $obj);
    echo '<br />';

    echo 'Уровень: ';
    echo GetHtmlElement($table, 'level', '', $levels);
    echo '<br />';

    echo 'Тип: ';
    echo GetHtmlElement($table, 'type', '', $types);
    echo '<br />';

    echo 'Статус: ';
    echo GetHtmlElement($table, 'status', '', $statuses);
    echo '<br />';
}




function ShowPublisherForm($table, $obj, $parent)
{
    $types = GetOptionsString2('publishertypes', $obj->type);

    
    echo 'Название: ';
    echo GetHtmlElement($table, 'name', '', '', $parent, $obj);
    echo '<br />';
    
    echo 'Город: ';
    echo GetHtmlElement($table, 'city', '', '', $parent, $obj);
    echo '<br />';
    
    echo 'Тип: ';
    echo GetHtmlElement($table, 'type', '', $types, $parent);
    echo '<br />';
}


function ShowParticipationForm($table, $obj)
{
    $authors = GetOptionsString2('authors', $obj->authorid);
    $scevents = GetOptionsString2('scevents', $obj->sceventid);
    
    
    
    echo '<div class="group">';
    echo 'Участник: ';
    echo GetHtmlElement($table, 'authorid', '', $authors);
    echo ' <a href="javascript:showBlock(\'authors\')">Новый</a>';
    echo ' | <a href="javascript:showBlock(\'authors\',getSel(\'participationsauthorid\'))">Редактировать</a>';
    echo ' | <a href="javascript:del(\'authors\',getSel(\'participationsauthorid\'))">Удалить</a><br />';
    echo '<div class="inlinedata" id="authors"></div>';
    echo '</div><br />';

    
    echo '<div class="group">';
    echo 'Мероприятие: ';
    echo GetHtmlElement($table, 'sceventid', '', $scevents);
    echo ' <a href="javascript:showBlock(\'scevents\')">Новое</a>';
    echo ' | <a href="javascript:showBlock(\'scevents\',getSel(\'participationssceventid\'))">Редактировать</a>';
    echo ' | <a href="javascript:del(\'scevents\',getSel(\'participationssceventid\'))">Удалить</a><br />';
    echo '<div class="inlinedata" id="scevents"></div>';
    echo '</div><br />';
}



?>