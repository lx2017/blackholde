$(function(){
//$(".headimg").click(function(){
//	$(".zhezhao").show();
//	$(".alert").show();
//
//})
$(".alert-lastbtn").click(function(){
	$(".zhezhao").hide();
	$(".alert").hide();
})
$(".index-mingfang-list").click(function(){
	$(this).addClass("index-mingfang-click");
	$(this).children('span').addClass("index-mingfang-left");
	$(this).children("img").show();
	$(this).children(".index-mingfang-select").show();
	$(this).siblings().removeClass("index-mingfang-click");
	$(this).siblings().children('span').removeClass("index-mingfang-left");
	$(this).siblings().children("img").hide();
	$(this).siblings().children(".index-mingfang-select").hide();
})
$(".rember").click(function(){
    var insertText = '<input class="aui-radio" class="myradio" type="radio" name="remember" checked><span>记住密码</span>';
    document.getElementById("insert").innerHTML = insertText;
    if($(this).is(":hidden")){
        $(this).show();
		$(this).next().hide();
	}else{
		$(this).hide();
		$(this).next().show();
	}
});
	$('#insert').click(function(){
		document.getElementById("insert").innerHTML = '';
		$('.rember').show();
	});
$(".index-desde-up").toggle(function(){
	$(this).children("img").attr("src","/Public/Patient/img/down.png");
	$(this).next().hide();
},function(){
	$(this).children("img").attr("src","/Public/Patient/img/up.png");
	$(this).next().show();
})
$(".index-disea-li").toggle(function(){
	$(this).children("img").attr("src","/Public/Patient/img/down.png");
	$(this).children("p").hide();
},function(){
	$(this).children("img").attr("src","/Public/Patient/img/up.png");
	$(this).children("p").show();
})
$(".cd-gray-wrap span").toggle(function(){
$(this).addClass("cd-bord");
$(this).removeClass("cd-gray");
},function(){
$(this).removeClass("cd-bord");
$(this).addClass("cd-gray");
})
$(".clincimg").click(function(){
	$(".zigeimg").show();
})
$(".cancelclinic").click(function(){
	$(".zigeimg").hide();
})
$(".shoucangle").toggle(function(){
	$(this).children().attr("src","../img/sca.png");
},function(){
	$(this).children().attr("src","../img/sc.png");
})
})