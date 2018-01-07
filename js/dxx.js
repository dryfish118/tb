function callLoad(page) {
    var fun = eval("load" + page.substring(0, 1).toUpperCase() + page.substring(1));
    if (fun) {
        fun();
    }
}

function loadPage(page) {
    /*
    var c = $.cookie("cookie_" + page);
    if (typeof(c) == "undefined" || c == null || c == "null") {
        $.getScript("./js/" + page + ".js", function(data, textStatus, jqXHR) {
            $.cookie("cookie_" + page, 1);
            callLoad(page);
        });
    } else {
        callLoad(page);
    }
    */
    $.getScript("./js/" + page + ".js", function(data, textStatus, jqXHR) {
        $.cookie("cookie_" + page, 1);
        callLoad(page);
    });
}

function loadMain() {
    // var table = $.cookie("cookie_table");
    // if (typeof(table) == "undefined" || table == null || table == "null") {
    //     $.getScript("./js/table.js", function(data, textStatus, jqXHR) {
    //         $.cookie("cookie_table", 1);
    //         loadPage(page);
    //     });
    // } else {
    //     loadPage(page);
    // }
    var user = $.cookie("cookie_login");
    if (typeof(user) == "undefined" || user === null || user == "null") {
        document.title = "登录";
        var html = "<form onsubmit='return onLogin();'>" +
            "<label>人员：</label><input type='text' id='fname' />" +
            "<input type='submit' value='登录' /></form>";
        $('#main').html(html);
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

function onLogin() {
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
                return true;
            }
        }
    });
    return false;
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