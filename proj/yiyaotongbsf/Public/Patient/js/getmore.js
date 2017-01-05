/**
 * Created by dower on 2016/11/29 0029.
 */
var page_url = '';
var page_obj = '';
var page_total = 0;
var page_now = 0;
var page_step = 0;
var page_is_load = false;
var page_p = 2;
var myobj = '';
$(function(){
    //设置分页相关信息
    if(myobj){
        page_total = parseInt(myobj.total);//总数据
        page_now = parseInt(myobj.step);//当前数据量
        page_step = parseInt(myobj.step);//分页大小
        page_url = myobj.url;//服务器请求地址
        page_obj = myobj.obj;//数据接收的dom的id
        if(myobj.p){
            page_p = myobj.p;
        }
    }
    //滚动事件
    $(window).scroll(function () {
        if(page_is_load) return false;//加载中,直接返回
        var scrollTop = $(this).scrollTop();
        var scrollHeight = $(document).height();
        var windowHeight = $(this).height();
        if (scrollHeight-scrollTop-windowHeight<100)  {
            loadMore();
        }
    });
    //初始化加载
    function load_init(myobj){
        page_total = parseInt(myobj.total);//总数据
        page_now = parseInt(myobj.step);//当前数据量
        page_step = parseInt(myobj.step);//分页大小
        page_url = myobj.url;//服务器请求地址
        page_obj = myobj.obj;//数据接收的dom的id
        if(myobj.p){
            page_p = myobj.p;
        }
        //清空,加载一次
        $('#'.page_obj).empty();
        loadMore();
    }
    //加载剩余数据
    function loadMore(){
        //设置正在加载数据
        page_is_load = true;
        //数据加载完毕,直接返回
        if(page_now>=page_total) return false;
        //请求数据
        $.get(page_url+'?p='+page_p, '', function(data){
            if(data){
                //追加数据
                $("#"+page_obj).append(data);
                //改变当前数据
                ++page_p;
                page_now += page_step;
                setTimeout(function(){page_is_load = false;},500);
            }else{
                page_is_load = false;
            }
        },'html');
    }
});