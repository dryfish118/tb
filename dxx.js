String.prototype.trim = function() {
    return this.replace(/(^\s*)|(\s*$)/g, "");
};

function onSort(page, col, dir) {
    sort.fpage.value = page;
    sort.fcolumn.value = col;
    sort.fdirection.value = dir;
    sort.submit();
}

function jsToday() {
    var dt = new Date();
    return dt.getFullYear() + "-" + (dt.getMonth() + 1) + "-" + dt.getDate();
}

function updateItemToSelect(objSelect, objItemValue) {
    for (var i = 0; i < objSelect.options.length; i++) {
        if (objSelect.options[i].value == objItemValue) {
            objSelect.options[i].selected = true;
            break;
        }
    }
}

function getTextFromSelected(objSelect) {
    var i = 0;
    for (; i < objSelect.options.length; i++) {
        if (objSelect.options[i].selected) {
            break;
        }
    }
    return objSelect.options[i].text;
}

function getTextByValue(objSelect, v) {
    var i = 0;
    for (; i < objSelect.options.length; i++) {
        if (objSelect.options[i].value == v) {
            break;
        }
    }
    return objSelect.options[i].text;
}

function getCookie(name) {
    var start = document.cookie.indexOf(name + '=');
    if (start == -1) {
        return "";
    }
    start += name.length + 1;
    var end = document.cookie.indexOf(';', start);
    if (end == -1) {
        return unescape(document.cookie.substring(start));
    } else {
        return unescape(document.cookie.substring(start, end));
    }
}

function SmartTable() {
    this.modify = false;
    this.delete = false;
    this.ths = null;
    this.ids = [];
    this.trs = [];

    this.setModify = function() {
        this.modify = true;
    };

    this.setDelete = function() {
        this.delete = true;
    };

    this.setHeader = function(ths) {
        this.ths = ths;
    };

    this.addRow = function(id, td) {
        this.ids.push(id);
        this.trs.push(td);
    };

    this.getTable = function() {
        var i = 0,
            j = 0;
        var html = "<table>";
        if (this.ths != null || this.modify || this.delete) {
            html += "<tr>";
            for (i = 0; i < this.ths.length; i++) {
                html += "<th>" + this.ths[i] + "</th>";
            }
            html += "<th>" + "操作" + "</th>";
            html += "</tr>";
        }
        if (this.trs.length > 0) {
            for (i = 0; i < this.trs.length; i++) {
                html += "<tr value='" + this.ids[i] + "'>";
                for (j = 0; j < this.trs[i].length; j++) {
                    html += "<td>" + this.trs[i][j] + "</td>";
                }
                if (this.modify || this.delete) {
                    html += "<td>";
                    if (this.modify) {
                        html += "<span class='mod'>改</span>";
                    }
                    if (this.delete) {
                        html += "<span class='del'>删</span>";
                    }
                    html += "</td>";
                }
                html += "</tr>";
            }
        }
        html += "</table>";
        return html;
    };
}

function loadUser() {
    document.title = "人员";
    $.get("user.php", function(rawData, textStatus) {
        var data = $.parseJSON(rawData);
        var user = data.user;

        var st = new SmartTable();
        st.setModify();
        st.setDelete();
        st.setHeader(["人员"]);
        $.each(user, function(i, info) {
            st.addRow(info.id, [info.name]);
        });

        var html = "<div id='useradd'><label>人员：</label>" +
            "<input type='text' name='fname' />" +
            "<input type='submit' value='增加' />" +
            "<input type='reset' />" +
            "</div><div id='userlist'>" + st.getTable() +
            "</div>";
        $('#main').html(html);
    });
}