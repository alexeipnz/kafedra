function EL(id)
{
    return document.getElementById(id);
}

function getPage(url, cb, postdata)
{
    var req = new XMLHttpRequest();
    if (!req)
    {
        alert('Error: unsupported browser');
        return false;
    }

    req.onreadystatechange = function()
    {
        if (req.readyState == 3 || req.readyState == 4)
        {
            if (req.status == 200) // OK
            {
                if (req.readyState == 3) // partially
                {
                    cb(req.responseText, false);
                }
                else if (req.readyState == 4) // completed
                {
                    cb(req.responseText, true);
                }
            }
            else
            {
                alert('Error: ' + req.status + ' ' + req.statusText);
            }
        }
    };

    try
    {
        if (postdata)
        {
            req.open('POST', url);
            req.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
            req.send(postdata);
        }
        else
        {
            req.open('GET', url);
            req.send(null);
        }
    }
    catch (err)
    {
        alert('Error: ' + err.toString());
        return false;
    }
    
    return true;
}



function createPwd()
{
    getPage('createpwd_p.php?email=' + encodeURIComponent(EL('email').value),
        function(resp, completed)
        {
            if (completed)
            {
                alert(resp);
            }
        }
    );
}

function login()
{
    getPage('login_p.php?email=' + encodeURIComponent(EL('email').value) +
            '&pwd=' + encodeURIComponent(EL('pwd').value),
        function(resp, completed)
        {
            if (completed)
            {
                if (resp.substr(0, 8) === 'success|')
                {
                    setCookie('author', resp.substr(8), 30 * 24 * 3600 * 1000);
                    location = 'index.php';
                }
                else
                    alert(resp);
            }
        }
    );
}

function logout()
{
    getPage('logout_p.php',
        function(resp, completed)
        {
            if (completed)
            {
                location.reload();
            }
        }
    );
}

function onPublTypeChange()
{
    var type = EL('publicationstype').value;
    if (type == 1) // article
    {
        EL('journaldata').style.display = 'block';
        EL('bookdata').style.display = 'none';
    }
    else
    {
        EL('bookdata').style.display = 'block';
        EL('journaldata').style.display = 'none';
    }
}


function moveOption(src, dst)
{
    var selSrc = EL(src);
    if (selSrc.selectedIndex < 0)
        return;
    
    var optSrc = selSrc.options[selSrc.selectedIndex];
    var optDst = document.createElement('option');
    optDst.value = optSrc.value;
    optDst.text = optSrc.text;
    
    selSrc.remove(selSrc.selectedIndex);
    EL(dst).add(optDst);
}


function hideBlock(id)
{
    EL(id).style.display = 'none';
}

function showBlock(table, id)
{
    if (id === undefined)
        id = 0;
    
    if (id < 0)
        return;

    var parent = table === 'publishers' ? store.publisherparent : '';

    getPage('showform_p.php?table=' + table + '&id=' + id + '&parent=' + parent,
        function(resp, completed)
        {
            if (completed)
            {
                EL(parent + table).innerHTML = resp + ' | <a href="javascript:hideBlock(\'' + parent + table + '\')">Отменить</a>';
                EL(parent + table).style.display = 'inline-block';
                
                
                if (table === 'publications')
                    onPublTypeChange();
            }
        }
    );
}



function getOptions(selid)
{
    var sel = EL(selid);
    var str = '';
    for (var i = 0; i < sel.options.length; i++)
    {
        if (str.length)
            str += '_';
        str += sel.options[i].value;
    }
    return str;
}


function getSel(id)
{
    var sel = EL(id);
    if (!sel || sel.selectedIndex < 0)
        return -1;
    return sel.options[sel.selectedIndex].value;
}


function delFromSelect(selid, val)
{
    var sel = EL(selid);
    if (!sel)
        return;
    
    for (var i = 0; i < sel.options.length; i++)
    {
        if (sel.options[i].value == val)
        {
            sel.remove(i);
            break;
        }
    }
}

function updateSelect(selid, val, text)
{
    var sel = EL(selid);
    if (!sel)
        return;
    
    for (var i = 0; i < sel.options.length; i++)
    {
        if (sel.options[i].value == val)
        {
            sel.options[i].text = text;
            sel.options[i].title = text;
            break;
        }
    }
}

function addToList(selid, val, text)
{
    var sel = EL(selid);
    if (!sel)
        return;
    
    var opt = document.createElement('option');
    opt.value = val;
    opt.text = text;
    opt.title = text;
    sel.add(opt);
    
    for (var i = 0; i < sel.options.length; i++)
    {
        if (sel.options[i].value == val)
        {
            sel.selectedIndex = i;
            break;
        }
    }
}


function afterDel(table, id)
{
    switch (table)
    {
    case 'participations':
        {
            location.reload();
            break;
        }
    case 'publications':
        {
            location.reload();
            break;
        }
    case 'authors':
        {
            delFromSelect('authall', id);
            delFromSelect('participationsauthorid', id);
            break;
        }
    case 'journals':
        {
            delFromSelect('publicationsjournalid', id);
            break;
        }
    case 'publishers':
        {
            delFromSelect('publicationspublisherid', id);
            delFromSelect('journalspublisherid', id);
            break;
        }
    case 'scevents':
        {
            delFromSelect('journalssceventid', id);
            delFromSelect('participationssceventid', id);
            break;
        }
    }
}

function del(table, id)
{
    if (id <= 0)
        return;
    
    if (!confirm('Удалить запись?'))
        return;
    
    getPage('del_p.php?table=' + table + '&id=' + id,
        function(resp, completed)
        {
            if (completed)
            {
                if (resp === 'success')
                {
                    afterDel(table, id);
                }
                else
                {
                    alert(resp);
                }
            }
        }
    );
}


function store(varName, varValue)
{
    store[varName] = varValue;
}

function afterUpdate(table, id)
{
    switch (table)
    {
    case 'participations':
        {
            location.reload();
            break;
        }
    case 'publications':
        {
            upsert('authorpublications');
            break;
        }
    case 'authors':
        {
            updateSelect(store.authlist, id, EL('authorsname').value);
            updateSelect('participationsauthorid', id, EL('authorsname').value);
            hideBlock('authors');
            break;
        }
    case 'journals':
        {
            updateSelect('publicationsjournalid', id, EL('journalsname').value);
            hideBlock('journals');
            break;
        }
    case 'publishers':
        {
            var parent = store.publisherparent;
            var text = EL(parent + 'publishersname').value;
            updateSelect('publicationspublisherid', id, text);
            updateSelect('journalspublisherid', id, text);
            hideBlock(parent + 'publishers');
            break;
        }
    case 'scevents':
        {
            updateSelect('journalssceventid', id, EL('sceventsname').value);
            updateSelect('participationssceventid', id, EL('sceventsname').value);
            hideBlock('scevents');
            break;
        }
    }
}

function afterAdd(table, id)
{
    switch (table)
    {
    case 'participations':
        {
            location.reload();
            break;
        }
    case 'authorpublications':
        {
            location.reload();
            break;
        }
    case 'publications':
        {
            EL('authorpublicationspublicationid').value = id;
            upsert('authorpublications');
            break;
        }
    case 'journals':
        {
            addToList('publicationsjournalid', id, EL('journalsname').value);
            hideBlock('journals');
            break;
        }
    case 'scevents':
        {
            addToList('journalssceventid', id, EL('sceventsname').value);
            addToList('participationssceventid', id, EL('sceventsname').value);
            hideBlock('scevents');
            break;
        }
    case 'publishers':
        {
            var parent = store.publisherparent;
            var text = EL(parent + 'publishersname').value;
            addToList('publicationspublisherid', id, text);
            addToList('journalspublisherid', id, text);
            hideBlock(parent + 'publishers');
            break;
        }
    case 'authors':
        {
            addToList('authorpublicationsauthorid', id, EL('authorsname').value);
            addToList('participationsauthorid', id, EL('authorsname').value);
            hideBlock('authors');
            break;
        }
    }
}

function upsert(table, parrenttable, id)
{
    if (parrenttable === undefined)
        parrenttable = '';
    
    if (id === undefined)
        id = 0;
    
    var qs = '';
    for (var prop in fieldInfo[table])
    {
        var val;
        switch (fieldInfo[table][prop])
        {
        case 'text': val = encodeURIComponent(EL(parrenttable + table + prop).value); break;
        case 'checkbox': val = EL(parrenttable + table + prop).checked ? 1 : 0; break;
        case 'hidden':
        case 'select': val = EL(parrenttable + table + prop).value; break;
        case 'selectmulti': val = getOptions(parrenttable + table + prop); break;
        }
        qs += '&' + prop + '=' + val;
    }
    
    getPage('upsert_p.php?table=' + table + '&id=' + id + qs,
        function(resp, completed)
        {
            if (completed)
            {
                if (resp.substr(0, 8) === 'success|')
                {
                    var resId = resp.substr(8);
                    if (id)
                        afterUpdate(table, resId);
                    else
                        afterAdd(table, resId);
                }
                else
                {
                    alert(resp);
                }
            }
        }
    );
}

function setCookie(name, value, expire_ms)
{
    var expires;
    if (expire_ms === undefined)
    {
        expires = '';
    }
    else
    {
        var date = new Date();
        date.setTime(date.getTime() + expire_ms);
        expires = '; expires=' + date.toGMTString();
    }
    document.cookie = name + '=' + value + expires + '; path=/';
}

function reloadWithParam(name, value)
{
    setCookie(name, value, 30 * 24 * 3600 * 1000);
    location.reload();
}
