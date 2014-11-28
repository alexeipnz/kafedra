<?php
include 'common.php';


$selectedAuthor = isset($_COOKIE['author']) ? (int)$_COOKIE['author'] : 0;


$table = isset($_COOKIE['table']) ? $_COOKIE['table'] : '';
if (!($table === 'publications' || $table === 'participations'))
    $table = 'publications';
?>
    <div class="container">
    <?php ShowHeader('Кафедра САПР'); ?>
    <ul class="nav nav-pills">
        <?php
        if ($table === 'publications') {
            ?>
            <li role="presentation" class="active"><a href="javascript:reloadWithParam('table','publications')">Публикации</a>
            </li>
            <li role="presentation"><a href="javascript:reloadWithParam('table','participations')">Научные
                    мероприятия</a></li>
        <?php
        } else {
            ?>
            <li role="presentation"><a href="javascript:reloadWithParam('table','publications')">Публикации</a></li>
            <li role="presentation" class="active"><a href="javascript:reloadWithParam('table','participations')">Научные
                    мероприятия</a></li>
        <?php
        }
        ?>
    </ul>


    <br/>

    <div role="tabpanel">
        <ul class="nav nav-tabs" role="tablist">

            <li role='presentation' class='active'><a href="javascript:reloadWithParam('author', <?= $userid; ?>)"
                                                      aria-controls='my' role='tab' data-toggle='tab'>Мои</a></li>

            <li role='presentation' class='active'><a href="javascript:reloadWithParam('author',0)"
                                                      aria-controls='all' role='tab' data-toggle='tab'>Все</a></li>

            <li role='presentation' class='active'><a href="javascript:showBlock('<?= $table; ?>')" aria-controls='add'
                                                      role='tab' data-toggle='tab'>Добавить</a></li>
        </ul>
    </div>

    <div class="tab-content">

    <div class="inlinedata" id="<?= $table ?>"></div>
    <?php
    if ($selectedAuthor) {
        $author = Select('authors', $selectedAuthor);
        ?>

        <br/>
        <b>
            <?=
            htmlspecialchars($author->name); ?>
        </b>
        <br/>
        <br/>
    <?php
    }
    if ($table === 'publications') {
        ?>
        <table class="table table-striped">
            <tr>
                <th>№</th>
                <th>Название</th>
                <?= $selectedAuthor ? '<th>Соавторы</th>' : '<th>Авторы</th>'; ?>
                <th>Журнал</th>
                <th>Издательство</th>
                <th>Год</th>
                <th>Страницы</th>
                <th>Удалить</th>
            </tr>
            <?php
            $cntr = 1;

            if ($selectedAuthor)
                $query = "SELECT publications.* FROM publications
        LEFT OUTER JOIN authorpublications ON publications.id = authorpublications.publicationid
        WHERE authorpublications.authorid = $selectedAuthor
        ORDER BY publications.year DESC, publications.name";
            else
                $query = 'SELECT * FROM publications ORDER BY year DESC, name';
            $publications = SelectObjects($query);
            foreach ($publications as $publication) {
                ?>
                <tr>
                    <td>
                        <?= $cntr++; ?>
                    </td>

                    <td>
                        <a href="javascript:showBlock('publications',<?= $publication->id; ?>)">
                            <?= htmlspecialchars($publication->name); ?>
                        </a>
                    </td>
                    <td>
                        <?php
                        if ($selectedAuthor)
                            $query = "SELECT authors.* FROM authorpublications
                LEFT OUTER JOIN authors ON authors.id = authorpublications.authorid
                WHERE authorpublications.publicationid = $publication->id AND
                authors.id <> $selectedAuthor
                ORDER BY authors.name";
                        else
                            $query = "SELECT authors.* FROM authorpublications
                LEFT OUTER JOIN authors ON authors.id = authorpublications.authorid
                WHERE authorpublications.publicationid = $publication->id
                ORDER BY authors.name";
                        $coauthors = SelectObjects($query);
                        $coauthorsStr = '';
                        foreach ($coauthors as $coauthor) {
                            if (strlen($coauthorsStr))
                                $coauthorsStr .= ', ';
                            $coauthorsStr .= "<a href=\"javascript:reloadWithParam('author',$coauthor->id)\">" .
                                htmlspecialchars($coauthor->name) . '</a>';
                        }
                        echo $coauthorsStr;
                        ?>
                    </td>
                    <td>
                        <?php
                        if ($publication->type == 1) {
                            $journal = Select('journals', $publication->journalid);
                            echo htmlspecialchars($journal->name);
                            if (strlen($publication->journalnumber))
                                echo htmlspecialchars(". – $publication->journalnumber");
                        }
                        ?>
                    </td>
                    <td>
                        <?php
                        $publisher = Select('publishers', $publication->type == 1 ? $journal->publisherid :
                            $publication->publisherid);
                        if (strlen($publisher->city))
                            echo htmlspecialchars("$publisher->city: ");
                        echo htmlspecialchars($publisher->name);
                        ?>
                    </td>

                    <td><?= $publication->year; ?></td>

                    <td>
                        <?php
                        echo $publication->type == 1 ? "$publication->journalpagestart-$publication->journalpageend" :
                            $publication->numpages;
                        ?>
                    </td>

                    <td>
                        <a href="javascript:del('publications',<?= $publication->id; ?>)"><span
                                class="glyphicon glyphicon-remove"></span></a>
                    </td>

                </tr>

            <?php
            }
            ?>

        </table>
    <?php
    } else {
        ?>

        <table class="table table-striped">
            <tr>
                <th>№</th>
                <th>Название</th>
                <?= $selectedAuthor ? '' : '<th>Участник</th>'; ?>
                <th>Место</th>
                <th>Дата</th>
                <th>Год</th>
                <th>Удалить</th>
            </tr>
            <?php
            $cntr = 1;

            if ($selectedAuthor)
                $query = "SELECT participations.id,
        scevents.name, scevents.date, scevents.place, scevents.year
        FROM participations
        LEFT OUTER JOIN scevents ON scevents.id = participations.sceventid
        WHERE participations.authorid = $selectedAuthor
        ORDER BY scevents.year DESC, scevents.name";
            else
                $query = 'SELECT participations.id, participations.authorid, authors.name AS authorname,
        scevents.name, scevents.date, scevents.place, scevents.year
        FROM participations
        LEFT OUTER JOIN scevents ON scevents.id = participations.sceventid
        LEFT OUTER JOIN authors ON authors.id = participations.authorid
        ORDER BY scevents.year DESC, scevents.name';
            $participations = SelectObjects($query);
            foreach ($participations as $participation) {
                ?>

                <tr>
                    <td>
                        <?= $cntr++; ?>
                    </td>
                    <td>
                        <a href="javascript:showBlock('participations',<?= $participation->id; ?>)">
                            <?= htmlspecialchars($participation->name); ?>
                        </a>
                    </td>
                    <?php
                    if (!$selectedAuthor) {
                        ?>

                        <td>
                            <a href="javascript:reloadWithParam('author',<?= $participation->authorid; ?>)">
                                <?= htmlspecialchars($participation->authorname); ?>
                            </a>
                        </td>
                    <?php
                    }
                    ?>
                    <td>
                        <?= htmlspecialchars($participation->place); ?>
                    </td>

                    <td>
                        <?= htmlspecialchars($participation->date); ?>
                    </td>

                    <td>
                        <?= $participation->year; ?>
                    </td>

                    <td>
                        <a href="javascript:del('participations',<?= $participation->id; ?>)"><span
                                class="glyphicon glyphicon-remove"></span></a>
                    </td>

                </tr>

            <?php
            }
            ?>

        </table>
    <?php
    }
    ?>
    </div>
    </div>

<?php
ShowFooter();
?>