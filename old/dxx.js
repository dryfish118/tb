String.prototype.trim = function()
{
    return this.replace(/(^\s*)|(\s*$)/g,"");
}

function onSort(page, col, dir)
{
    sort.fpage.value = page;
    sort.fcolumn.value = col;
    sort.fdirection.value = dir;
    sort.submit();
}

function jsToday()
{
    var dt = new Date();
    return dt.getFullYear() + "-" + (dt.getMonth() + 1) + "-" + dt.getDate();
}

function updateItemToSelect(objSelect, objItemValue)
{
    for (var i = 0; i < objSelect.options.length; i++)
    {
        if (objSelect.options[i].value == objItemValue)
        {
            objSelect.options[i].selected = true;
            break;
        }
    }
}

function getTextFromSelected(objSelect)
{
    var i = 0;
    for (; i < objSelect.options.length; i++)
    {
        if (objSelect.options[i].selected)
        {
            break;
        }
    }
    return objSelect.options[i].text;
}

function getTextByValue(objSelect, v)
{
    var i = 0;
    for (; i < objSelect.options.length; i++)
    {
        if (objSelect.options[i].value == v)
        {
            break;
        }
    }
    return objSelect.options[i].text;
}

function getCookie(name)
{
    var start = document.cookie.indexOf(name + '=');
    if (start == -1)
    {
        return "";
    }
    start += name.length + 1;
    var end = document.cookie.indexOf(';', start);
    if (end == -1)
    {
        return unescape(document.cookie.substring(start));
    }
    else
    {
        return unescape(document.cookie.substring(start, end));
    }
}
