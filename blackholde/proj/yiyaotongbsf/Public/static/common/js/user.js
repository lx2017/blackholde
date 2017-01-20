/**
 * 验证账户
 * Created by dower on 2016/12/8 0008.
 */

/**
 * 验证手机号码
 * @param mobile
 * @param type 默认验证手机, 为1时验证手机和电话
 * @returns {*|boolean}
 */
function check_mobile(mobile,type){
    if(typeof type == 'undefined') type=0;
    var reg = '';
    if(type){
        reg = /^(0?1\d{10})|\d{3,4}-?\d{7,8}$/;//电话和手机
    }else{
        reg = /^0?1\d{10}$/;
    }
    return reg.test(mobile);
}

/**
 * 验证密码(或确认密码)
 * @param password 密码
 * @param repassword 确认密码
 * @returns {*} code=0成功, msg提示错误
 */
function check_password(password,repassword){
    if(typeof repassword == 'undefined') repassword=null;
    var reg = /^[a-z0-9A-Z]{6,12}$/i;//只能包含字母和数字的6-12位字符串
    //验证长度
    if(!password){
        return {code:1,msg:'请输入密码'};
    }
    if(!reg.test(password)){
        return {code:2,msg:'密码为6-12位数字或字母'};
    }
    if(repassword){//验证密码与确认密码
        if(repassword!=password){
            return {code:3,msg:'输入密码不一致'};
        }
    }
    return {code:0,msg:'验证成功'};
}

/**
 * 查看密码或隐藏密码
 * @param obj
 */
function password_view(obj){
    var type = obj.attr('type');
    if(type=='password'){
        obj.prop('type','text');
    }else{
        obj.prop('type','password');
    }
}