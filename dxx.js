///////////////////////////////////////
// user
function onModUser(row) {
    var $tr = $("#edittable tr").eq(row + 1);
    var fid = $tr.attr("value");
    var fname = $tr.children("td").eq(0).text();
    $("#faction").attr("value", "update");
    $("#fid").attr("value", fid);
    $("#fname").val(fname);
}

function onDelUser(row) {
    if (!confirm("确定要删除吗？")) {
        return;
    }
    var fuser = $.cookie("cookie_user");
    var $tr = $("#edittable tr").eq(row + 1);
    var fid = $tr.attr("value");
    $.ajax({
        type: "POST",
        url: "./user.php",
        cache: false,
        data: {
            "fuser": fuser,
            "faction": "delete",
            "fid": fid
        },
        dataType: "text",
        success: function(data, textStatus) {
            if (parseInt(data) == 1) {
                loadUser();
            }
        }
    });
}

function onUser() {
    var fuser = $.cookie("cookie_user");
    var faction = $("#faction").attr("value");
    var fid = $("#fid").attr("value");
    var fname = $("#fname").val();
    $("#faction").attr("value", "add");
    $.ajax({
        type: "POST",
        url: "./user.php",
        cache: false,
        data: {
            "fuser": fuser,
            "faction": faction,
            "fid": fid,
            "fname": fname
        },
        dataType: "text",
        success: function(data, textStatus) {
            if (parseInt(data) == 1) {
                loadUser();
                return true;
            }
        }
    });

    return false;
}

function loadUser() {
    document.title = "人员";
    $.post("user.php", {
        "fuser": $.cookie("cookie_user"),
        "faction": "list"
    }, function(rawData, textStatus) {
        var data = $.parseJSON(rawData);
        var user = data.user;

        var st = new SmartTable();
        st.setModify("onModUser");
        st.setDelete("onDelUser");
        st.setHeader(["人员"]);
        $.each(user, function(i, item) {
            st.addRow(item.id, [item.name]);
        });

        var html = "<form id='editform' onsubmit='return onUser();'>" +
            "<input type='hidden' id='faction' value='add' />" +
            "<input type='hidden' id='fid' value='0' />" +
            "<label>人员：</label><input type='text' id='fname' />" +
            "<input type='submit' /><input type='reset' />" +
            "</form>" +
            "<div>" + st.getTable() + "</div>";
        $('#main').html(html);
    });
}

///////////////////////////////////////
// issue
function onModIssue(row) {
    var $tr = $("#edittable tr").eq(row + 1);
    var fid = $tr.attr("value");
    var fname = $tr.children("td").eq(0).text();
    var fout = $tr.children("td").eq(1).text();
    $("#faction").attr("value", "update");
    $("#fid").attr("value", fid);
    $("#fname").val(fname);
    $("#fout").prop("checked", fout == "1" ? true : false);
}

function onDelIssue(row) {
    if (!confirm("确定要删除吗？")) {
        return;
    }
    var fuser = $.cookie("cookie_user");
    var $tr = $("#edittable tr").eq(row + 1);
    var fid = $tr.attr("value");
    $.ajax({
        type: "POST",
        url: "./issue.php",
        cache: false,
        data: {
            "fuser": fuser,
            "faction": "delete",
            "fid": fid
        },
        dataType: "text",
        success: function(data, textStatus) {
            if (parseInt(data) == 1) {
                loadIssue();
            }
        }
    });
}

function onIssue() {
    var fuser = $.cookie("cookie_user");
    var faction = $("#faction").attr("value");
    var fid = $("#fid").attr("value");
    var fname = $("#fname").val();
    var fout = $("#fout").prop("checked") ? 1 : 0;
    $("#faction").attr("value", "add");
    $.ajax({
        type: "POST",
        url: "./issue.php",
        cache: false,
        data: {
            "fuser": fuser,
            "faction": faction,
            "fid": fid,
            "fname": fname,
            "fout": fout
        },
        dataType: "text",
        success: function(data, textStatus) {
            if (parseInt(data) == 1) {
                loadIssue();
                return true;
            }
        }
    });

    return false;
}

function loadIssue() {
    document.title = "条目";
    $.post("issue.php", {
        "fuser": $.cookie("cookie_user"),
        "faction": "list"
    }, function(rawData, textStatus) {
        var data = $.parseJSON(rawData);
        var issue = data.issue;

        var st = new SmartTable();
        st.setModify("onModIssue");
        st.setDelete("onDelIssue");
        st.setHeader(["条目", "收支"]);
        $.each(issue, function(i, item) {
            st.addRow(item.id, [item.name, item.out]);
        });

        var html = "<form id='editform' onsubmit='return onIssue();'>" +
            "<input type='hidden' id='faction' value='add' />" +
            "<input type='hidden' id='fid' value='0' />" +
            "<label>条目：</label><input type='text' id='fname' />" +
            "<label>支出：</label><input type='checkbox' id='fout' CHECKED />" +
            "<input type='submit' /><input type='reset' />" +
            "</form>" +
            "<div>" + st.getTable() + "</div>";
        $('#main').html(html);
    });
}

///////////////////////////////////////
// brand
function onModBrand(row) {
    var $tr = $("#edittable tr").eq(row + 1);
    var fid = $tr.attr("value");
    var fname = $tr.children("td").eq(0).text();
    $("#faction").attr("value", "update");
    $("#fid").attr("value", fid);
    $("#fname").val(fname);
}

function onDelBrand(row) {
    if (!confirm("确定要删除吗？")) {
        return;
    }
    var fuser = $.cookie("cookie_user");
    var $tr = $("#edittable tr").eq(row + 1);
    var fid = $tr.attr("value");
    $.ajax({
        type: "POST",
        url: "./brand.php",
        cache: false,
        data: {
            "fuser": fuser,
            "faction": "delete",
            "fid": fid
        },
        dataType: "text",
        success: function(data, textStatus) {
            if (parseInt(data) == 1) {
                loadBrand();
            }
        }
    });
}

function onBrand() {
    var fuser = $.cookie("cookie_user");
    var faction = $("#faction").attr("value");
    var fid = $("#fid").attr("value");
    var fname = $("#fname").val();
    $("#faction").attr("value", "add");
    $.ajax({
        type: "POST",
        url: "./brand.php",
        cache: false,
        data: {
            "fuser": fuser,
            "faction": faction,
            "fid": fid,
            "fname": fname
        },
        dataType: "text",
        success: function(data, textStatus) {
            if (parseInt(data) == 1) {
                loadBrand();
                return true;
            }
        }
    });

    return false;
}

function loadBrand() {
    document.title = "品牌";
    $.post("brand.php", {
        "fuser": $.cookie("cookie_user"),
        "faction": "list"
    }, function(rawData, textStatus) {
        var data = $.parseJSON(rawData);
        var brand = data.brand;

        var st = new SmartTable();
        st.setModify("onModBrand");
        st.setDelete("onDelBrand");
        st.setHeader(["品牌"]);
        $.each(brand, function(i, item) {
            st.addRow(item.id, [item.name]);
        });

        var html = "<form id='editform' onsubmit='return onBrand();'>" +
            "<input type='hidden' id='faction' value='add' />" +
            "<input type='hidden' id='fid' value='0' />" +
            "<label>品牌：</label><input type='text' id='fname' />" +
            "<input type='submit' /><input type='reset' />" +
            "</form>" +
            "<div>" + st.getTable() + "</div>";
        $('#main').html(html);
    });
}

///////////////////////////////////////
// size
function onOrderSize(row, type) {

}

function onModSize(row) {
    var $tr = $("#edittable tr").eq(row + 1);
    var fid = $tr.attr("value");
    var fname = $tr.children("td").eq(0).text();
    $("#faction").attr("value", "update");
    $("#fid").attr("value", fid);
    $("#fname").val(fname);
}

function onDelSize(row) {
    if (!confirm("确定要删除吗？")) {
        return;
    }
    var fuser = $.cookie("cookie_user");
    var $tr = $("#edittable tr").eq(row + 1);
    var fid = $tr.attr("value");
    $.ajax({
        type: "POST",
        url: "./size.php",
        cache: false,
        data: {
            "fuser": fuser,
            "faction": "delete",
            "fid": fid
        },
        dataType: "text",
        success: function(data, textStatus) {
            if (parseInt(data) == 1) {
                loadSize();
            }
        }
    });
}

function onSize() {
    var fuser = $.cookie("cookie_user");
    var faction = $("#faction").attr("value");
    var fid = $("#fid").attr("value");
    var fname = $("#fname").val();
    $("#faction").attr("value", "add");
    $.ajax({
        type: "POST",
        url: "./size.php",
        cache: false,
        data: {
            "fuser": fuser,
            "faction": faction,
            "fid": fid,
            "fname": fname
        },
        dataType: "text",
        success: function(data, textStatus) {
            if (parseInt(data) == 1) {
                loadSize();
                return true;
            }
        }
    });

    return false;
}

function loadSize() {
    document.title = "尺寸";
    $.post("size.php", {
        "fuser": $.cookie("cookie_user"),
        "faction": "list"
    }, function(rawData, textStatus) {
        var data = $.parseJSON(rawData);
        var size = data.size;

        var st = new SmartTable();
        st.setOrder("onOrderSize");
        st.setModify("onModSize");
        st.setDelete("onDelSize");
        st.setHeader(["尺寸"]);
        $.each(size, function(i, item) {
            st.addRow(item.id, [item.name]);
        });

        var html = "<form id='editform' onsubmit='return onSize();'>" +
            "<input type='hidden' id='faction' value='add' />" +
            "<input type='hidden' id='fid' value='0' />" +
            "<label>尺寸</label><input type='text' id='fname' />" +
            "<input type='submit' /><input type='reset' />" +
            "</form>" +
            "<div>" + st.getTable() + "</div>";
        $('#main').html(html);
    });
}

///////////////////////////////////////
// history
function loadHistory() {
    document.title = "历史";
    $.post("history.php", {
        "fuser": $.cookie("cookie_user"),
        "faction": "list"
    }, function(rawData, textStatus) {
        var data = $.parseJSON(rawData);
        var history = data.history;

        var st = new SmartTable();
        st.setHeader(["人员", "时间", "内容"]);
        $.each(history, function(i, item) {
            st.addRow(item.id, [item.user, item.time, item.contents]);
        });

        var html = "<div id='edittable'>" + st.getTable() + "</div>";
        $('#main').html(html);
    });
}

///////////////////////////////////////
// loadMain
function loadMain(page) {
    switch (page) {
        case "user":
            {
                loadUser();
                break;
            }
        case "issue":
            {
                loadIssue();
                break;
            }
        case "brand":
            {
                loadBrand();
                break;
            }
        case "size":
            {
                loadSize();
                break;
            }
        case "history":
            {
                loadHistory();
                break;
            }
    }
}