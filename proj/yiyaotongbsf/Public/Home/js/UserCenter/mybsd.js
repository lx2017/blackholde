$(function() {

    var opt = {
        preset: 'date', //日期
        theme: 'sense-ui', //皮肤样式
        display: 'modal', //显示方式 
        mode: 'scroller', //日期选择模式
        dateFormat: 'yy-mm-dd', // 日期格式
        setText: '确定', //确认按钮名称
        cancelText: '取消', //取消按钮名籍我
        dateOrder: 'yymmdd', //面板中日期排列格式
        dayText: '日',
        monthText: '月',
        yearText: '年', //面板中年月日文字
        endYear: 2020 //结束年份
    };
    $("#date").mobiscroll(opt).date(opt);
    $("#btn_save_bsd").click(function() {
        var date = $("#date").val();
        if(date == ""){
            $("#dom_error_wrap").html('您的生日不能为空').css("display", 'block')
        }else{
           $("#dom_error_wrap").html('').css("display", 'none');
            $("#form_bsd").submit();
        }
    })



});