/**
 * Created by mafengli on 16/5/31.
 */
$(function () {
    $("#form").validate({
        submitHandler: function (form) {
            $.ajax({
                type: 'post',
                data: $('#form').serialize(),
                dataType: 'json',
                url: 'login/login/doLogin',
                success: function (data) {
                    if (data['code'] == 1002) {
                        window.location.href = "permission/menu";
                    } else {
                        alert('用户名或密码不正确');
                    }
                }
            })
        }
    });
    $("#changeImg").click(function () {
        $("#yanzhengImg").attr("src", "../validatecode/gain/adminlogin?v=" + new Date().getTime() + "m=" + Math.random());
    });
});