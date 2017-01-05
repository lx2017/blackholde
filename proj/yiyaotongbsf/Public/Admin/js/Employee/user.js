/**
 * Created by mafengli on 16/6/12.
 */
/**
 * Created by mafengli on 16/6/2.
 */
$(function () {

    $("#addAdminForm").validate({
        rules: {
            username: {
                remote: {
                    url: $("#login_name").attr('_target'),     //后台处理程序
                    type: "post",               //数据发送方式
                    dataType: "json",           //接受数据格式
                    data: {                     //要传递的数据
                        username: function () {
                            return $("#login_name").val();
                        }
                    }
                }
            },
            email: {
                remote: {
                    url: $("#email").attr('_target'),     //后台处理程序
                    type: "post",               //数据发送方式
                    dataType: "json",           //接受数据格式
                    data: {                     //要传递的数据
                        email: function () {
                            return $("#email").val();
                        }
                    }
                }
            }
        },
        messages: {
            username: {
                remote: "登录名已经存在"
            },
            email: {
                remote: "邮箱已经存在"
            }
        },
        submitHandler: function (form) {
            $.ajax({
                type: 'post',
                data: $("#addAdminForm").serialize(),
                url: $("#addAdminForm").attr('action'),
                dataType: 'json',
                success: function (data) {
                    if (data.status == 1) {
                        alert(data.info);
                        window.location.href = data.url;
                    } else {
                        alert(data.info);
                    }
                }
            })
        }
    });

    $("#editAdminForm").validate({
        submitHandler: function (form) {
            $.ajax({
                type: 'post',
                data: $("#editAdminForm").serialize(),
                url: $("#editAdminForm").attr('action'),
                dataType: 'json',
                success: function (data) {
                    if (data.code == 1002) {
                        alert("修改成功");
                        window.location.href = "index.php/permission/admin/lists/adminlist"
                    } else {
                        alert("修改失败");
                    }
                }
            })

        }
    });
    //$("#checkAdminAll").click(function () {
    //    if ($("#checkAdminAll").is(":checked")) {
    //        $("input[name='adminid']").prop("checked", true);
    //    } else {
    //        $("input[name='adminid']").prop("checked", false);
    //    }
    //})
    $("#enableAdminBtn").click(function () {
        if ($("input[name='id']:checked").length > 0) {

            $.ajax({
                type: 'post',
                url: $(this).attr("_href"),
                data: {'id': getAdminIds()},
                dataType: 'json',
                success: function (data) {
                    if (data.status == 1) {
                        alert(data.info);
                        window.location.href = window.location.href;
                    } else {
                        alert(data.info);
                    }
                }
            })
        } else {
            alert("请选择要操作的人员");
        }
    });
    $("#disableAdminBtn").click(function () {
        if ($("input[name='id']:checked").length > 0) {

            $.ajax({
                type: 'post',
                url: $(this).attr("_href"),
                data: {'id': getAdminIds()},
                dataType: 'json',
                success: function (data) {
                    if (data.status == 1) {
                        alert(data.info);
                        window.location.href = window.location.href;
                    } else {
                        alert(data.info);
                    }
                }
            })
        } else {
            alert("请选择要操作的人员");
        }
    });
    $("#deleteAdminBtn").click(function () {
        if ($("input[name='id']:checked").length > 0) {

            $.ajax({
                type: 'post',
                url: $(this).attr("_href"),
                data: {'id': getAdminIds()},
                dataType: 'json',
                success: function (data) {
                    if (data.status == 1) {
                        alert(data.info);
                        window.location.href = window.location.href;
                    } else {
                        alert(data.info);
                    }
                }
            })
        } else {
            alert("请选择要操作的人员");
        }
    })
});
function getAdminIds() {
    var adminIds = '';
    $("input[name='id']:checked").each(function () {
        if (adminIds == '') {
            adminIds = $(this).val();
        } else {
            adminIds = adminIds + ',' + $(this).val();
        }
    });
    return adminIds;
}
