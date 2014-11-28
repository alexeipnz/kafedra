<?php

include 'common.php';


if (!isset($_GET['table'])) {
    echo 'Error: no data (table)';
    exit;
}
$table = $_GET['table'];
if (!array_key_exists($table, $fieldInfo)) {
    echo 'Error: incorrect data (table)';
    exit;
}


if (!isset($_GET['parent'])) {
    echo 'Error: no data (parent)';
    exit;
}
$parent = $_GET['parent'];
if (strlen($parent) && !array_key_exists($parent, $fieldInfo)) {
    echo 'Error: incorrect data (parent)';
    exit;
}


if (!isset($_GET['id'])) {
    echo "Error: no data ($table.id)";
    exit;
}
$id = (int)$_GET['id'];


if ($id) {
    $obj = Select($table, $id);
    $btnName = 'Сохранить';
} else {
    $obj = new stdClass();
    $obj->id = $id;
    foreach ($fieldInfo[$table]['fields'] as $colName => $colInfo)
        $obj->$colName = $colInfo['def'];
    $btnName = 'Добавить';
}


$name = $fieldInfo[$table]['name'];
echo "<b>$name</b><br />";

switch ($table) {
    case 'authors':
        ShowAuthorForm($table, $obj);
        break;
    case 'journals':
        ShowJournalForm($table, $obj);
        break;
    case 'publications':
        ShowPublicationForm($table, $obj);
        break;
    case 'scevents':
        ShowScEventForm($table, $obj);
        break;
    case 'publishers':
        ShowPublisherForm($table, $obj, $parent);
        break;
    case 'participations':
        ShowParticipationForm($table, $obj);
        break;
}

echo "<div style='clear: both;'>&nbsp;</div><div class='col-md-12 col-sm-12'><a class='btn btn-default' href=\"javascript:upsert('$table','$parent',$id)\">$btnName</a>";


////////////////////////////////////////////////////////////////////////////////


function ShowAuthorForm($table, $obj)
{
    ?>
    <form class='form-horizontal'>
        <div class="group col-sm-12">
            <div class='form-group'>
                <label for='authorsname' class='control-label col-sm-4'>Фамилия И. О.:</label>

                <div class="col-sm-8">
                    <?= GetHtmlElement($table, 'name', '', '', '', $obj); ?>
                </div>
            </div>

            <div class='form-group'>
                <div class="checkbox">
                    <label for='authorsisforeign' class='col-sm-4 control-label'>Иностранный:

                        <?= GetHtmlElement($table, 'isforeign', '', '', '', $obj); ?>
                    </label>
                </div>
            </div>

            <div class='form-group'>
                <label for='authorsstudentgroup' class='control-label col-sm-4'>Студент группы </label>

                <div class="col-sm-8">
                    <?= GetHtmlElement($table, 'studentgroup', '', '', '', $obj); ?>
                </div>
            </div>
        </div>
    </form>
<?php
}


function ShowJournalForm($table, $obj)
{
    $publishers = GetOptionsString2('publishers', $obj->publisherid);
    $types = GetOptionsString2('journaltypes', $obj->type);
    $scevents = GetOptionsString2('scevents', $obj->sceventid, true);
    ?>
    <form class='form-horizontal'>

        <div class='form-group'>
            <label for='journalsname' class='control-label col-sm-3'>Название: </label>

            <div class="col-sm-9">
                <?= GetHtmlElement($table, 'name', 'class="publname"', '', '', $obj); ?>
            </div>
        </div>

        <div class='form-group'>
            <label for='journalstype' class='control-label col-sm-3'>Тип: </label>

            <div class="col-sm-9 col-xs-12">
                <?= GetHtmlElement($table, 'type', '', $types); ?>
            </div>
        </div>

        <div class='form-group'>

            <div class='checkbox'>
                <label for='journalsimpfactor' class='control-label col-sm-3'>Импакт-фактор: </label>

                <div class="col-sm-9">
                    <?= GetHtmlElement($table, 'impfactor', 'class="publyear"', '', '', $obj); ?>
                </div>
            </div>

            <div class='checkbox'>
                <div class="space"></div>
                <label for="journalsinrinc" class="col-sm-3 control-label">РИНЦ:
                    <?= GetHtmlElement($table, 'inrinc', '', '', '', $obj); ?>
                </label>
            </div>

            <div class="checkbox">
                <div class="space"></div>
                <label for="journalsreviewed" class="col-sm-3 control-label">Рецензируемый:
                    <?= GetHtmlElement($table, 'reviewed', '', '', '', $obj); ?>
                </label>
            </div>

            <div class="checkbox">
                <div class="space"></div>
                <label for="journalsinvak" class="col-sm-3 control-label">ВАК:
                    <?= GetHtmlElement($table, 'invak', '', '', '', $obj); ?>
                </label>
            </div>
        </div>

        <div class='form-group'>

            <div class="form-group">
                <div class="checkbox">
                    <label for="journalsinscopus" class="col-sm-3 control-label">Scopus:
                        <?= GetHtmlElement($table, 'inscopus', '', '', '', $obj); ?>
                    </label>
                </div>

                <div class="checkbox">
                    <div class="space"></div>
                    <label for="journalsinwos" class="col-sm-3 control-label"> Web of Science:
                        <?= GetHtmlElement($table, 'inwos', '', '', '', $obj); ?>
                    </label>
                </div>

                <div class="checkbox">
                    <div class="space"></div>
                    <label for="journalsinforeignindex" class="col-sm-3 control-label">Другие иностранные индексы:
                        <?= GetHtmlElement($table, 'inforeignindex', '', '', '', $obj); ?>
                    </label>
                </div>
            </div>

            <div class="group col-sm-9">


                <div class='form-group'>
                    <label for='journalspublisherid' class='control-label col-sm-3'>Издательство:</label>

                    <div class="col-sm-9">
                        <?= GetHtmlElement($table, 'publisherid', '', $publishers); ?>
                    </div>
                </div>

                <div class="btn-group">
                    <a class="btn btn-default"
                       href="javascript:store('publisherparent','journals');showBlock('publishers')">Новое</a>
                    <a class="btn btn-default"
                       href="javascript:store('publisherparent','journals');showBlock('publishers',getSel('journalspublisherid'))">Редактировать</a>
                    <a class="btn btn-default"
                       href="javascript:del('publishers',getSel('journalspublisherid'))">Удалить</a>
                </div>

                <div class="form-group">
                    <div class="inlinedata" id="journalspublishers"></div>
                </div>
            </div>

            <div class="group col-sm-9">


                <div class='form-group'>
                    <label for='journalssceventid' class='control-label col-sm-3'>Научное мероприятие:</label>

                    <div class="col-sm-9">
                        <?= GetHtmlElement($table, 'sceventid', '', $scevents); ?>
                    </div>
                </div>

                <div class="btn-group">
                    <a class="btn btn-default" href="javascript:showBlock('scevents')">Новое</a>
                    <a class="btn btn-default" href="javascript:showBlock('scevents',getSel('journalssceventid'))">Редактировать</a>
                    <a class="btn btn-default"
                       href="javascript:del('scevents',getSel('journalssceventid'))">Удалить</a>
                </div>

                <div style='clear: both;'>&nbsp;</div>

                <div class='btn-group'>
                    <div class="inlinedata" id="scevents"></div>
                </div>

            </div>


    </form>
<?php
}


function ShowPublicationForm($table, $obj)
{
    global $userid;

    if ($obj->id) {
        $authorids = SelectObjects("SELECT authorid FROM authorpublications WHERE publicationid = $obj->id");
        $authorsRange = '';
        foreach ($authorids as $authorid) {
            if (strlen($authorsRange))
                $authorsRange .= ',';
            $authorsRange .= $authorid->authorid;
        }
    } else {
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
    ?>
    <form class="form-horizontal">

    <div class="form-group">
        <label for="publicationsname" class="control-label col-sm-4">Название:</label>

        <div class="col-sm-8">
            <?= GetHtmlElement($table, 'name', 'class="publname"', '', '', $obj); ?>
        </div>
    </div>


    <div class="group highlight col-sm-10">

        <div class="form-group">
            <label class="control-label col-sm-4">Авторы: </label>

            <div class="col-sm-8">
                <?php
                $objAuth = new stdClass();
                $objAuth->publicationid = $obj->id;
                echo GetHtmlElement('authorpublications', 'publicationid', '', '', '', $objAuth);
                ?>
            </div>
        </div>


        <div class="group col-sm-4">
            <div class="form-group">
                <?= GetHtmlElement('authorpublications', 'authorid', 'size="5" class="authorlist"', $authors_publ); ?>
            </div>

            <div class="btn-group-vertical">
                <a class="btn btn-default"
                   href="javascript:store('authlist','authorpublicationsauthorid');showBlock('authors',getSel(store.authlist))">Редактировать</a>
                <a class="btn btn-default" href="javascript:showBlock('authors')">Новый</a>
            </div>
        </div>

        <div class="group aligntop col-sm-2">
            <div class="btn-group-vertical">
                <a class="btn btn-default" href="javascript:moveOption('authall','authorpublicationsauthorid')"><i
                        class="glyphicon glyphicon-chevron-left"></i></a>
                <a class="btn btn-default" href="javascript:moveOption('authorpublicationsauthorid','authall')"><i
                        class="glyphicon glyphicon-chevron-right"></i></a>
            </div>
        </div>

        <div class="group col-sm-4">
            <div class="form-group">
                <select class="form-control" size="5" class="authorlist" id="authall"><?= $authors_other ?></select>
            </div>
            <div class="btn-group-vertical">
                <a class="btn btn-default"
                   href="javascript:store('authlist','authall');showBlock('authors',getSel(store.authlist))">Редактировать</a>
                <a class="btn btn-default" href="javascript:del('authors',getSel('authall'))">Удалить</a>
            </div>
        </div>


        <div class="form-group">
            <div class="inlinedata" id="authors"></div>
        </div>
    </div>
    <!--         group highlight-->

    <div style='clear: both;'>&nbsp;</div>
    <div class="form-group">

        <label for="publicationsyear" class="control-label col-sm-4">Год издания:</label>

        <div class="col-sm-8">
            <?= GetHtmlElement($table, 'year', 'class="publyear"', '', '', $obj); ?>
        </div>
    </div>

    <div class="form-group">

        <div class="space"></div>
        <label for="publicationslang" class="control-label col-sm-4">Язык:</label>

        <div class="col-sm-7">
            <?= GetHtmlElement($table, 'lang', '', $langs); ?>
        </div>
    </div>

    <div class="form-group">
        <div class="space"></div>
        <label for="publicationsurl" class="control-label col-sm-4">URL с выходными данными:</label>

        <div class="col-sm-8">
            <?= GetHtmlElement($table, 'url', '', '', '', $obj); ?>
        </div>
    </div>


    <div class="form-group">
        <label for="publicationstype" class="control-label col-sm-4">Тип:</label>

        <div class="col-sm-7">
            <?= GetHtmlElement($table, 'type', 'onchange="onPublTypeChange()"', $types); ?>
        </div>
    </div>


    <div id="journaldata">

        <div class="group">


            <div class="form-group">
                <label for="publicationsjournalid" class="control-label col-sm-4">Журнал (сборник):</label>

                <div class="col-sm-7">
                    <?= GetHtmlElement($table, 'journalid', '', $journals); ?>
                </div>
            </div>

            <div class="btn-group">
                <a class="btn btn-default" href="javascript:showBlock('journals')">Новый</a>
                <a class="btn btn-default" href="javascript:showBlock('journals',getSel('publicationsjournalid'))">Редактировать</a>
                <a class="btn btn-default"
                   href="javascript:del('journals',getSel('publicationsjournalid'))">Удалить</a>


            </div>
            <div class="form-group">
                <div class="inlinedata" id="journals"></div>
            </div>

        </div>
        <!--         group-->


        <div class="form-group">
            <label for="publicationsjournalnumber" class="control-label col-sm-4">Номер журнала:</label>

            <div class="col-sm-8">
                <?= GetHtmlElement($table, 'journalnumber', '', '', '', $obj); ?>
            </div>
        </div>


        <div class="form-group">
            <label for="publicationsjournalpagestart" class="control-label col-sm-4">Начальная страница:</label>

            <div class="col-sm-8">
                <?= GetHtmlElement($table, 'journalpagestart', 'class="publyear"', '', '', $obj) ?>
            </div>
        </div>

        <div class="form-group">
            <label for="publicationsjournalpageend" class="control-label col-sm-4">Конечная страница:</label>

            <div class="col-sm-8">
                <?= GetHtmlElement($table, 'journalpageend', 'class="publyear"', '', '', $obj) ?>
            </div>
        </div>


    </div>
    <!--        journaldata-->

    <div id="bookdata">

        <div class="group">


            <div class="form-group">
                <label for="journalspublisherid" class="control-label ">Издательство:</label>
                <div class="col-sm-7">


                    <?= GetHtmlElement($table, 'publisherid', '', $publishers) ?>
                </div>
            </div>

            <div class="btn-group">
                <a class="btn btn-default"
                   href="javascript:store('publisherparent','publications');showBlock('publishers')">Новое</a>
                <a class="btn btn-default"
                   href="javascript:store('publisherparent','publications');showBlock('publishers',getSel('publicationspublisherid'))">Редактировать</a>
                <a class="btn btn-default"
                   href="javascript:del('publishers',getSel('publicationspublisherid'))">Удалить</a>
            </div>

            <div class="form-group">
                <div class="inlinedata" id="publicationspublishers"></div>
            </div>

        </div>
        <!--                 group-->


        <div class="form-group">
            Гриф:
            <?= GetHtmlElement($table, 'grif', '', $orders) ?>
        </div>
        <div class="form-group">
            Число страниц:
            <?= GetHtmlElement($table, 'numpages', 'class="publyear"', '', '', $obj) ?>
        </div>
        <div class="form-group">
            Тираж:
            <?= GetHtmlElement($table, 'tirazh', 'class="publyear"', '', '', $obj) ?>
        </div>


    </div>
    <!--        // bookdata-->

    </form>
<?php
}


function ShowScEventForm($table, $obj)
{
    $types = GetOptionsString2('evtypes', $obj->type, true);
    $levels = GetOptionsString2('evlevels', $obj->level, true);
    $statuses = GetOptionsString2('evstatuses', $obj->status, true);
    ?>
    <div class="group">
        <div class="form-group">
            <label for="sceventsname" class="control-label col-sm-3">Название: </label>

            <div class="col-sm-7">
                <?= GetHtmlElement($table, 'name', 'class="publname"', '', '', $obj); ?>
            </div>
        </div>

        <div class="form-group">
            <label for="sceventsplace" class="control-label col-sm-3">Место: </label>

            <div class="col-sm-7">
                <?= GetHtmlElement($table, 'place', '', '', '', $obj); ?>
            </div>
        </div>

        <div class="form-group">
            <label for="sceventsdate" class="control-label col-sm-3">Дата: </label>

            <div class="col-sm-7">
                <?= GetHtmlElement($table, 'date', '', '', '', $obj); ?>
            </div>
        </div>

        <div class="form-group">
            <label for="sceventsyear" class="control-label col-sm-3">Год: </label>

            <div class="col-sm-7">
                <?= GetHtmlElement($table, 'year', 'class="publyear"', '', '', $obj); ?>
            </div>
        </div>

        <div class="form-group">
            <label for="sceventslevel" class="control-label col-sm-3">Уровень: </label>

            <div class="col-sm-7">
                <?= GetHtmlElement($table, 'level', '', $levels); ?>
            </div>
        </div>

        <div class="form-group">
            <label for="sceventstype" class="control-label col-sm-3">Тип: </label>

            <div class="col-sm-7">
                <?= GetHtmlElement($table, 'type', '', $types); ?>
            </div>
        </div>

        <div class="form-group">
            <label for="sceventsstatus" class="control-label col-sm-3">Статус: </label>

            <div class="col-sm-7">
                <?= GetHtmlElement($table, 'status', '', $statuses); ?>
            </div>
        </div>
    </div>
<?php
}


function ShowPublisherForm($table, $obj, $parent)
{
    $types = GetOptionsString2('publishertypes', $obj->type);

    ?>
    <div class="group">
        <div class="form-group">
            <label for="journalspublishersname" class="control-label col-sm-3">Название: </label>

            <div class="col-sm-8">
                <?= GetHtmlElement($table, 'name', '', '', $parent, $obj); ?>
            </div>
        </div>

        <div class="form-group">
            <label for="journalspublisherscity" class="control-label col-sm-3">Город: </label>

            <div class="col-sm-8">
                <?= GetHtmlElement($table, 'city', '', '', $parent, $obj); ?>
            </div>
        </div>

        <div class="form-group">
            <label for="journalspublishersname" class="control-label col-sm-3">Тип: </label>

            <div class="col-sm-8">
                <?= GetHtmlElement($table, 'type', '', $types, $parent); ?>
            </div>
        </div>
    </div>
<?php
}


function ShowParticipationForm($table, $obj)
{
    $authors = GetOptionsString2('authors', $obj->authorid);
    $scevents = GetOptionsString2('scevents', $obj->sceventid);
    ?>

    <div class="group">

    <div class="form-group">
        <label class="control-label col-sm-4">Участник:</label>

        <div class="col-sm-8">
            <?= GetHtmlElement($table, 'authorid', '', $authors); ?>
        </div>
    </div>

    <div class="btn-group">
        <a class="btn btn-default" href="javascript:showBlock('authors')">Новый</a>';
        <a class="btn btn-default"
           href="javascript:showBlock('authors',getSel('participationsauthorid'))">Редактировать</a>
        <a class="btn btn-default" href="javascript:del('authors',getSel('participationsauthorid'))">Удалить</a>
    </div>

    <div class="form-group">
        <div class="inlinedata" id="authors"></div>
    </div>

    <div class="group">
        <div class="form-group">
            <label class="control-label col-sm-4">Мероприятие: </label>

            <div class="col-sm-8">
                <?= GetHtmlElement($table, 'sceventid', '', $scevents); ?>
            </div>
        </div>

        <div class="btn-group">
            <a class="btn btn-default" href="javascript:showBlock('scevents')">Новое</a>
            <a class="btn btn-default" href="javascript:showBlock('scevents',getSel('participationssceventid'))">Редактировать</a>
            <a class="btn btn-default" href="javascript:del('scevents',getSel('participationssceventid'))">Удалить</a>
        </div>

        <div class="form-group">

            <div class="inlinedata" id="scevents"></div>
        </div>
    </div>
<?php
}


?>