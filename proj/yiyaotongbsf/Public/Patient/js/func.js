/**
 * Created by dower on 2016/7/19 0019.
 */
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
        return false;
    }
    if(typeof success!='function'){
        return false;
    }
    if(opt.handle){
        return false;
    }
    //一些参数的默认值
    if(typeof opt.width=='undefined'){
        opt.width = 20;
    }
    if(typeof opt.width=='undefined'){
        opt.height = 10;
    }
    if(typeof opt.text=='undefined'){
        opt.text = '上传';
    }
    if(typeof opt.size=='undefined'){
        opt.size = '10000000B';
    }
    if(typeof opt.multi=='undefined'){
        opt.multi = 'false';
    }
    if(typeof opt.formData=='undefined'){
        opt.formData = {};
    }
    //注册上传事件
    obj.uploadifive({
        'auto' : true,
        'multi'    : opt.multi,
        'buttonText': opt.text,
        'formData' : opt.formData,
        'fileSizeLimit': opt.size,
        'queueID'          : 'queue',
        'uploadScript'     : opt.handle,
        'fileType' : 'image/*',//仅能上传图片
        'onUploadComplete' : function(file, data) {
            console.log(1234);
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
        'onSelect': function () {
            console.log(123);
        },
        'onError': function () {

        }
    });
}