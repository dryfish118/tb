function SmartTable() {
    this.fnOrder = null;
    this.fnMod = null;
    this.fnDel = null;
    this.ths = null;
    this.ids = [];
    this.trs = [];
    this.pageTotal = 0;
    this.pageCur = 0;

    this.setOrder = function(fnOrder) {
        this.fnOrder = fnOrder;
    };

    this.setModify = function(fnMod) {
        this.fnMod = fnMod;
    };

    this.setDelete = function(fnDel) {
        this.fnDel = fnDel;
    };

    this.setHeader = function(ths) {
        this.ths = ths;
    };

    this.addRow = function(id, td) {
        this.ids.push(id);
        this.trs.push(td);
    };

    this.setPage = function(pageTotal, pageCur) {
        this.pageTotal = pageTotal;
        this.pageCur = pageCur;
    };

    this.getTable = function() {
        var i = 0,
            j = 0;
        var html = "";

        if (this.pageTotal > 0) {
            html += "<div id='gotoPage'>";
            var pageMax = 10;
            var pageTotal = this.pageTotal;
            if (pageTotal > pageMax) {
                pageTotal = pageMax;
            }
            var from = 1,
                to = this.pageTotal;
            var l = this.pageCur - from,
                r = to - this.pageCur;
            if (l < r) {
                if (l > pageMax / 2) {
                    from = this.pageCur - pageMax / 2;
                    to = this.pageCur + pageMax / 2;
                } else {
                    to = this.pageCur + pageMax - l;
                }
            } else {
                if (r > pageMax / 2) {
                    from = this.pageCur - pageMax / 2;
                    to = this.pageCur + pageMax / 2;
                } else {
                    from = this.pageCur - (pageMax - r);
                }
            }
            for (i = from; i < this.pageCur; i++) {
                html += "<span class='page'>" + i + "</span>&nbsp;";
            }
            html += "<span class='pagecurrent'>" + this.pageCur + "</span>";
            for (i = this.pageCur + 1; i <= to; i++) {
                html += "&nbsp;<span class='page'>" + i + "</span>";
            }
            html += "</div>";
        }

        html += "<table id='edittable'>";
        if (this.ths !== null || this.modify || this.delete) {
            html += "<tr>";
            for (i = 0; i < this.ths.length; i++) {
                html += "<th>" + this.ths[i] + "</th>";
            }
            if (this.fnOrder) {
                html += "<th>" + "排序" + "</th>";
            }
            if (this.fnMod || this.fnDel) {
                html += "<th>" + "操作" + "</th>";
            }
            html += "</tr>";
        }
        if (this.trs.length > 0) {
            for (i = 0; i < this.trs.length; i++) {
                html += "<tr value='" + this.ids[i] + "'>";
                for (j = 0; j < this.trs[i].length; j++) {
                    html += "<td>" + this.trs[i][j] + "</td>";
                }
                if (this.fnOrder) {
                    html += "<td>";
                    html += "<span class='top' " +
                        "onclick='" + this.fnOrder + "(" + i + ", \"top\")'" +
                        ">顶</span>&nbsp;";
                    html += "<span class='bottom' " +
                        "onclick='" + this.fnOrder + "(" + i + ", \"bottom\")'" +
                        ">底</span>&nbsp;";
                    html += "<span class='up' " +
                        "onclick='" + this.fnOrder + "(" + i + ", \"up\")'" +
                        ">上</span>&nbsp;";
                    html += "<span class='down' " +
                        "onclick='" + this.fnOrder + "(" + i + ", \"down\")'" +
                        ">下</span>";
                    html += "</td>";
                }
                if (this.fnMod || this.fnDel) {
                    html += "<td>";
                    if (this.fnMod) {
                        html += "<span class='mod' " +
                            "onclick='" + this.fnMod + "(" + i + ")'" +
                            ">改</span>";
                    }
                    if (this.fnDel) {
                        if (this.fnMod) {
                            html += "&nbsp;";
                        }
                        html += "<span class='del' " +
                            "onclick='" + this.fnDel + "(" + i + ")'" +
                            ">删</span>";
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