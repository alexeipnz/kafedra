<?php

include 'common.php';

ShowHeader('Кафедра САПР');



$selectedAuthor = isset($_COOKIE['author']) ? (int)$_COOKIE['author'] : 0;


$table = isset($_COOKIE['table']) ? $_COOKIE['table'] : '';
if (!($table === 'publications' || $table === 'participations'))
    $table = 'publications';



echo $table === 'publications' ? 'Публикации' : '<a href="javascript:reloadWithParam(\'table\',\'publications\')">Публикации</a>';
echo ' | ';
echo $table === 'participations' ? 'Научные мероприятия' : '<a href="javascript:reloadWithParam(\'table\',\'participations\')">Научные мероприятия</a>';
echo '<br />';

echo $selectedAuthor == $userid ? 'Мои' : "<a href=\"javascript:reloadWithParam('author',$userid)\">Мои</a>";
echo ' | ';
echo $selectedAuthor == 0 ? 'Все' : '<a href="javascript:reloadWithParam(\'author\',0)">Все</a>';
echo ' | ';
echo "<a href=\"javascript:showBlock('$table')\">Добавить</a>";
echo '<br />';

echo "<div class=\"inlinedata\" id=\"$table\"></div>";
echo '<br />';




if ($selectedAuthor)
{
    $author = Select('authors', $selectedAuthor);
    echo '<b>';
    echo htmlspecialchars($author->name);
    echo '</b><br /><br />';
}







if ($table === 'publications')
{
    echo '<table><tr><th>№</th><th>Название</th>';
    echo $selectedAuthor ? '<th>Соавторы</th>' : '<th>Авторы</th>';
    echo '<th>Журнал</th><th>Издательство</th><th>Год</th><th>Страницы</th></tr>';
    $cntr = 1;


    if ($selectedAuthor)
        $query = "SELECT publications.* FROM publications
                    LEFT OUTER JOIN authorpublications ON publications.id = authorpublications.publicationid
                    WHERE authorpublications.authorid = $selectedAuthor
                    ORDER BY publications.year DESC, publications.name";
    else
        $query = 'SELECT * FROM publications ORDER BY year DESC, name';
    $publications = SelectObjects($query);
    foreach ($publications as $publication)
    {
        echo '<tr>';


        echo '<td>';
        echo $cntr++;
        echo '</td>';



        echo '<td>';
        echo "<a href=\"javascript:showBlock('publications',$publication->id)\">";
        echo htmlspecialchars($publication->name);
        echo '</a>';
        echo '</td>';


        echo '<td>';
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
        foreach ($coauthors as $coauthor)
        {
            if (strlen($coauthorsStr))
                $coauthorsStr .= ', ';
            $coauthorsStr .= "<a href=\"javascript:reloadWithParam('author',$coauthor->id)\">" . htmlspecialchars($coauthor->name) . '</a>';
        }
        echo $coauthorsStr;
        echo '</td>';


        echo '<td>';
        if ($publication->type == 1)
        {
            $journal = Select('journals', $publication->journalid);
            echo htmlspecialchars($journal->name);
            if (strlen($publication->journalnumber))
                echo htmlspecialchars(". – $publication->journalnumber");
        }
        echo '</td>';


        echo '<td>';
        $publisher = Select('publishers', $publication->type == 1 ? $journal->publisherid : $publication->publisherid);
        if (strlen($publisher->city))
            echo htmlspecialchars("$publisher->city: ");
        echo htmlspecialchars($publisher->name);
        echo '</td>';


        echo "<td>$publication->year</td>";


        echo '<td>';
        echo $publication->type == 1 ? "$publication->journalpagestart-$publication->journalpageend" : $publication->numpages;
        echo '</td>';


        echo '<td>';
        echo "<a href=\"javascript:del('publications',$publication->id)\">X</a>";
        echo '</td>';


        echo '</tr>';
    }
    echo '</table>';
}
else
{
    echo '<table><tr><th>№</th><th>Название</th>';
    if (!$selectedAuthor)
        echo '<th>Участник</th>';
    echo '<th>Место</th><th>Дата</th><th>Год</th></tr>';
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
    foreach ($participations as $participation)
    {
        echo '<tr>';

        echo '<td>';
        echo $cntr++;
        echo '</td>';

        echo '<td>';
        echo "<a href=\"javascript:showBlock('participations',$participation->id)\">";
        echo htmlspecialchars($participation->name);
        echo '</a>';
        echo '</td>';

        if (!$selectedAuthor)
        {
            echo '<td>';
            echo "<a href=\"javascript:reloadWithParam('author',$participation->authorid)\">";
            echo htmlspecialchars($participation->authorname);
            echo '</a>';
            echo '</td>';
        }

        echo '<td>';
        echo htmlspecialchars($participation->place);
        echo '</td>';
        
        echo '<td>';
        echo htmlspecialchars($participation->date);
        echo '</td>';
        
        echo "<td>$participation->year</td>";
        echo "<td><a href=\"javascript:del('participations',$participation->id)\">X</a></td>";
        echo '</tr>';
    }
    echo '</table>';
}




ShowFooter();

?>