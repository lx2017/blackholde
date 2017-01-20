/**
 * Created by mafengli on 16/6/15.
 */
$(function () {
    var tables;
    $("#optimizeBtn").click(function () {
        if ($("input[name='tables[]']:checked").length > 0) {
            $.ajax({
                type: 'post',
                url: $(this).attr("_href"),
                data: $("#exportForm").serialize(),
                dataType: "json",
                success: function (data) {
                    if (data.status == 1) {
                        alert(data.info);
                    } else {
                        alert(data.info);
                    }
                }
            })
        }
    });
    $("#repairBtn").click(function () {
        if ($("input[name='tables[]']:checked").length > 0) {
            $.ajax({
                type: 'post',
                url: $(this).attr("_href"),
                data: $("#exportForm").serialize(),
                dataType: "json",
                success: function (data) {
                    if (data.status == 1) {
                        alert(data.info);
                    } else {
                        alert(data.info);
                    }
                }
            })
        }
    })
    $("#backBtn").click(function () {
        $("#btnDiv").find(".btn").addClass("disabled").prop("disabled", true);
        $("#backBtn").val("正在发送备份请求...");
        $.post(
            $(this).attr("_href"),
            $("#exportForm").serialize(),
            function (data) {
                if (data.status) {
                    tables = data.tables;
                    $("#backBtn").val(data.info + "开始备份，请不要关闭本页面！");
                    backup(data.tab);
                    window.onbeforeunload = function () {
                        return "正在备份数据库，请不要关闭！"
                    }
                } else {
                    //updateAlert(data.info, 'alert-error');
                    $("#btnDiv").find(".btn").removeClass("disabled").prop('disabled', false);
                    $("#backBtn").val("立即备份");
                    //setTimeout(function () {
                    //    $('#top-alert').find('button').click();
                    //    $("#btnDiv").find(".btn").removeClass('disabled').prop('disabled', false);
                    //}, 1500);
                }
            },
            "json"
        );
        return false;
    });

    function backup(tab, status) {
        status && showmsg(tab.id, "开始备份...(0%)");
        $.get($("#backBtn").attr("_href"), tab, function (data) {
            if (data.status) {
                showmsg(tab.id, data.info);
                if (!$.isPlainObject(data.tab)) {
                    $("#btnDiv").find(".btn").removeClass("disabled");
                    $("#backBtn").val("备份完成，点击重新备份");
                    window.onbeforeunload = function () {
                        return null
                    }
                    return;
                }
                backup(data.tab, tab.id != data.tab.id);
            } else {
                // updateAlert(data.info, 'alert-error');
                $("#btnDiv").find(".btn").removeClass("disabled").prop('disabled', false);
                $("#backBtn").val("立即备份");
                //setTimeout(function () {
                //    //$('#top-alert').find('button').click();
                //    $("#btnDiv").find(".btn").removeClass('disabled').prop('disabled', false);
                //}, 1500);
            }
        }, "json");

    }

    function showmsg(id, msg) {
        $("#exportForm").find("input[value=" + tables[id] + "]").closest("tr").find(".info").html(msg);
    }
    $(".doImportBtn").click(function () {
        var  status = ".";
        var self = $(this);
        $.post(self.attr("_href"), success, "json");
        window.onbeforeunload = function () {
            return "正在还原数据库，请不要关闭！"
        }
        return false;

        function success(data) {
            if (data.status) {
                if (data.gz) {
                    data.info += status;
                    if (status.length === 5) {
                        status = ".";
                    } else {
                        status += ".";
                    }
                }
                self.parent().prev().text(data.info);
                if (data.part) {
                    $.post(self.attr("_href"),
                        {"part": data.part, "start": data.start},
                        success,
                        "json"
                    );
                } else {
                    window.onbeforeunload = function () {
                        return null;
                    }
                }
            } else {
//                    updateAlert(data.info, 'alert-error');
//                    alert(data.info);
            }
        }
    });
})


