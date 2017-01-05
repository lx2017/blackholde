/**
 * 编辑页
 * 备注: 定义--validate_rules,validate_messages
 * Created by dower on 2016/7/19 0019.
 */
$(function(){
    //表单验证模块
    var submit_obj = $("#submitForm");
    var issend = false;
    submit_obj.validate({
        rules: validate_rules,
        messages: validate_messages,
        submitHandler: function (form) {
            if(issend) return false;
            issend = true;
            $.ajax({
                type: 'post',
                data: submit_obj.serialize(),
                url: location.href,
                dataType: 'json',
                success: function (data) {
                    if (data.code == 0) {
                        layer_success('操作成功',1500,location.assign(data.url));
                    } else {
                        layer_error(data.msg);
                    }
                    issend = false;
                },
                error: function (XMLHttpRequest, textStatus, errorThrown) {
                    layer_error('网络繁忙, 请刷新页面');
                    issend = false;
                }
            });
            return false;
        }
    });
});