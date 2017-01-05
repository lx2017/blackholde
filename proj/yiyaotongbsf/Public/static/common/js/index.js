/**
 * 列表页
 * 备注: 定义--search_obj(搜索字段对应的dom中的id),search_url
 * Created by dower on 2016/7/28 0028.
 */
$(function(){
    //回车搜索
    $(".search-input").keyup(function (e) {
        if (e.keyCode === 13) {
            $("#search").click();
            return false;
        }
    });
    //搜索功能
    $("#search").click(function(){
        //提交请求
        location.href = handleSearch(search_obj,search_url);
    });
    //批量删除事件(伪删除)
    $('#delete').click(function(){
        //检验是否为空
        var ids = $('.ids:checked');
        if(ids.length==0){
            layer_msg('请选择删除的标签');
            return false;
        }
        var _this = this;
        var value = $(this).data('value');
        if(typeof value == 'undefined') value=2;
        //询问框
        layer_confirm('确定删除吗?',{btn: ['确定','取消']},function(){
            //提交请求
            var str = '';
            $.each(ids,function(){
                str += (','+$(this).val());
            });
            str = str.substr(1);
            var url = $(_this).attr('_href');
            $.post(url,{'id':str,'value':value},function(data){
                if(data.code==0){
                    layer_success('删除成功',1500,location.reload());
                }else{
                    layer_error(data.msg);
                }
            },'json');
        });
    });
    //单删除事件(伪删除)
    $('.delete').click(function(){
        var _this = this;
        var value = $(this).data('value');
        if(typeof value == 'undefined') value=2;
        //询问框
        layer_confirm('确定删除吗?',{btn: ['确定','取消']},function(){
            //提交请求
            var url = $(_this).attr('_href');
            $.post(url,{'value':value},function(data){
                if(data.code==0){
                    layer_success('删除成功',1500,location.reload());
                }else{
                    layer_error(data.msg);
                }
            },'json');
        });
    });
});