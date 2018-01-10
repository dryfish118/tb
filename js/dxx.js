function callLoad(page) {
    var fun = eval("load" + page.substring(0, 1).toUpperCase() + page.substring(1));
    if (fun) {
        fun();
    }
}

function loadPage(page) {
    $.getScript("./js/" + page + ".js", function(data, textStatus, jqXHR) {
        $.cookie("cookie_" + page, 1);
        callLoad(page);
    });
}

function loadMain() {
    var user = $.cookie("cookie_login");
    if (typeof(user) == "undefined" || user === null || user == "null") {
        document.title = "登录";
        var html = "<form id='loginform'>" +
            "<label>人员：</label><input type='text' id='fname' />" +
            "<input type='submit' value='登录' /></form>";
        $('#main').html(html);

        $("#loginform").submit(function() {
            var name = $("#fname").val();
            if (name === "") {
                return false;
            }
            $.ajax({
                type: "POST",
                url: "./dxx/user.php",
                cache: false,
                data: {
                    "faction": "login",
                    "fname": name
                },
                dataType: "text",
                success: function(data, textStatus) {
                    var id = parseInt(data);
                    if (id != -1) {
                        $.cookie("cookie_login", id);
                        loadMain();
                    }
                }
            });
            return false;
        });
    } else {
        $.getScript("./js/table.js", function(data, textStatus, jqXHR) {
            $.cookie("cookie_table", 1);
            var page = $.cookie("cookie_page");
            if (typeof(page) == "undefined" || page === null || page == "null") {
                page = "user";
            }
            loadPage(page);
        });
    }
}

function onPage(txt, pages) {
    var page = 1;
    if (txt === "<<") {
        page = 1;
    } else if (txt === ">>") {
        page = pages;
    } else {
        page = parseInt(txt);
    }
    return page;
}

function onCustomDel(strUrl, faction, fid, fnLoad) {
    if (!confirm("确定要删除吗？")) {
        return;
    }
    $.ajax({
        type: "POST",
        url: strUrl,
        cache: false,
        data: {
            "flogin": $.cookie("cookie_login"),
            "faction": faction,
            "fid": fid
        },
        dataType: "text",
        success: function(data, textStatus) {
            if (parseInt(data) == 1) {
                fnLoad();
            }
        }
    });
}

function onDel(strUrl, fid, fnLoad) {
    onCustomDel(strUrl, "delete", fid, fnLoad);
}

$("#menu li").click(function() {
    var user = $.cookie("cookie_login");
    if (typeof(user) == "undefined" || user === null || user == "null") {
        return;
    }
    var page = $(this).attr("id");
    if (page !== "") {
        if (page == "login") {
            $.cookie("cookie_login", null);
            $.cookie("cookie_page", null);
        } else {
            $.cookie("cookie_page", page);
        }
        loadMain();
    }
});