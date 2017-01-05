/**
 * Created by dower on 2016/7/19 0019.
 */
//弹窗提醒
function layer_msg(msg,time){
    if(typeof time == 'undefined') time=2000;
    layer.msg(msg,{
        time:time
    });
}
//loading
function layer_load(){
    layer.load(1, {
        shade: [0.1,'#fff'] //0.1透明度的白色背景
    });
}
//成功失败弹窗
function layer_success(msg,time,func){
    if(typeof time == 'undefined') time=1500;
    if(typeof func != 'function') func=function(){};
    layer.msg(msg,{
        time:time,
        icon:1
    },func())
}
function layer_error(msg,time){
    if(typeof time == 'undefined') time=1500;
    layer.msg(msg,{
        time:time,
        icon:2
    })
}
//关闭loading
function layer_close_load(){
    layer.closeAll('loading');
}
//询问框
function layer_confirm(msg,opt,func){
    if(typeof func != 'function') func=function(){};
    if(typeof opt != 'object') opt={};
    layer.confirm(msg,opt,func);
}
//验证手机-- 默认只验证手机
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
//验证价格
function check_price(price,type){
    return /^\d+$/.test(price);
}
//验证联系人
function check_name(name){
    if(name.length>30){
        return false;
    }
    return /^([\u4E00-\u9FA5]|[A-Za-z])+$/i.test(name);
}
//处理搜索
function handleSearch(obj,url) {
    if(typeof ext =='object') layer_msg('初始化搜索失败');
    var temp = {};//相关信息
    for(var k in obj){
        if(obj[k]){
            temp[k] = $('#'+obj[k]).val();//放到json对象中
        }
    }
    //调用url处理函数
    return handleUrl(url,temp);
}
//处理url
function handleUrl(url,obj,ext){
    if(typeof ext =='undefined') ext = '.html';
    var len = ext.length;
    url = url.slice(0,0-len);
    for(var k in obj){
        if(obj[k]){
            url += ('/'+k+'/'+encodeURIComponent(obj[k]));
        }
    }
    return url+ext;
}
//上传模块
function handleUpload(id,opt,success,error) {
    //检测
    var obj = $('#'+id);
    if(obj.length==0){
        layer_msg('id错误');
    }
    if(typeof success!='function'){
        layer_msg('success回调函数错误');
    }
    if(opt.uploader){
        layer_msg('未传入上传处理地址');
    }
    //一些参数的默认值
    if(typeof opt.width=='undefined'){
        opt.width = 120;
    }
    if(typeof opt.width=='undefined'){
        opt.height = 30;
    }
    if(typeof opt.text=='undefined'){
        opt.text = '上传';
    }
    if(typeof opt.size=='undefined'){
        opt.size = '2000000B';
    }
    if(typeof opt.ext=='undefined'){
        opt.ext = '';
    }
    if(typeof opt.multi=='undefined'){
        opt.multi = 'false';
    }
    if(typeof opt.formData=='undefined'){
        opt.formData = {};
    }
    //注册上传事件
    obj.uploadify({
        height: opt.height,
        width: opt.width,
        'multi'    : opt.multi,
        'buttonText': opt.text,
        'fileSizeLimit': opt.size,
        'swf': '/Public/static/uploadify/uploadify.swf',
        'fileTypeExts': opt.ext,
        'uploader': opt.handle,
        'formData': opt.formData,
        'onUploadSuccess': function (file, data, response) {
            layer_close_load();
            data = eval('(' + data + ')');
            if(data.code==0){
                success(data);//成功处理函数
            }else{
                if(typeof error=='function'){
                    error(data);
                }else{
                    layer.alert(data.msg);
                }
                return false;
            }
        },
        'onUploadError': function (file, errorCode, errorMsg, errorString) {
            layer_close_load();
            layer.alert('"' + file.name + '" 不能上传: ' + errorString);
        },
        'onSelect': function (e, queueId, fileObj) {
            layer_load();
        }
    });
}