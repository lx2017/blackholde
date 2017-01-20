/**
 * Created by dower on 2016/11/29 0029.
 */
var time_url = '';
$(function(){
    var opt = {
        theme: 'default',
        display: 'bottom',
        mode: 'scroller',
        animate: 'pop',
        dateFormat: 'yy-mm',
        dateOrder: 'yymm',
        onSelect:function(valueText){
            //获取当前时间的数量
            $.getJSON(time_url,{date:valueText},function(data){
                if(data.code==0){
                    $('#mydate').val(valueText);
                    $('#now_num').text(data.data);
                    $('#now_date').text(valueText);
                }else{//评价
                    $('#mydate').val(valueText);
                    $('#now_date').text(valueText);
                    $('#count0').text(data.data[0]);
                    $('#count1').text(data.data[1]);
                    $('#count2').text(data.data[2]);
                    $('#count3').text(data.data[3]);
                    $('#count4').text(data.data[4]);
                    $('#count5').text(data.data[5]);
                }
            });
        }
    };
    $('.mydate').scroller().date(opt);
});