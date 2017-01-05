$(function(){
	
	$(".pub_md .data").click(function(){
		$(".pub_md .data").removeClass("cur");
		$(this).addClass("cur");
	})
	
	/*function q1(){
		$(".block__list li").click(function(){
			$(this).css("border","1px dashed #999").css("background","#f4fffe");
			$(this).find(".del_fz").show();
			$(this).find(".del_dd").show();
		})
	}*/
	var i=1000;
	//添加
	$(".k1").click(function(){
		$(".edit_zd a").removeClass("add");
		$(".ss2").addClass("add");
		$(".ed_hk1").hide();
		$(".ed_hk2").show();
		$(".ed_hk3").hide();
		$(".ed_hk4").hide();
		$(".ed_hk5").hide();
		$(".ed_hk6").hide();
		$(".ed_hk7").hide();
		$(".ed_hk8").hide();
		$(".ed_hk9").hide();
		$(".ed_hk10").hide();
		$(".ed_hk11").hide();
		$(".ed_hk12").hide();
		var html='<li style="height:100px;" flag=4 i='+i+' draggable="false" >';
			html+='<span class="ed_name"></span>';
			html+='<h6></h6>';
			html+='<input type="text" name="remark" />';
			html+='<input type="hidden" name="is_repeat" />';
			html+='<input type="hidden" name="is_must" />';
			html+='<input type="hidden" name="defaul" />';
			html+='<input type="hidden" name="is_least_bite" />';
			html+='<input type="hidden" name="is_most_bite" />';
			html+='<input type="hidden" name="least_bite" />';
			html+='<input type="hidden" name="most_bite" />';
			html+='<div class="del_fz con4"><span class="ss3" title="复制"></span></div>';
			html+='<div class="del_dd con4" title="删除"><span></span></div></li>';
			
		$(".block__list_words").append(html);
		i++;
	})
	
	$(".k2").click(function(){
		$(".edit_zd a").removeClass("add");
		$(".ss2").addClass("add");
		$(".ed_hk1").hide();
		$(".ed_hk4").show();
		$(".ed_hk2").hide();
		$(".ed_hk3").hide();
		$(".ed_hk5").hide();
		$(".ed_hk6").hide();
		$(".ed_hk7").hide();
		$(".ed_hk8").hide();
		$(".ed_hk9").hide();
		$(".ed_hk10").hide();
		$(".ed_hk11").hide();
		$(".ed_hk12").hide();
		
		var html='<li style="height:100px;" flag=5 i='+i+' draggable="false" >';
			html+='<span class="ed_name"></span>';
			html+='<h6></h6>';
			html+='<input type="text" name="remark" />';
			html+='<input type="hidden" name="is_must" />';
			html+='<input type="hidden" name="defaul" />';
			html+='<input type="hidden" name="is_least_bite" />';
			html+='<input type="hidden" name="is_most_bite" />';
			html+='<input type="hidden" name="least_bite" />';
			html+='<input type="hidden" name="most_bite" />';
			html+='<div class="del_fz con4"><span class="ss3" title="复制"></span></div>';
			html+='<div class="del_dd con4" title="删除"><span></span></div></li>';
			
		$(".block__list_words").append(html);
		i++;
	})
	$(".k3").click(function(){
		$(".edit_zd a").removeClass("add");
		$(".ss2").addClass("add");
		$(".ed_hk1").hide();
		$(".ed_hk3").show();
		$(".ed_hk2").hide();
		$(".ed_hk4").hide();
		$(".ed_hk5").hide();
		$(".ed_hk6").hide();
		$(".ed_hk7").hide();
		$(".ed_hk8").hide();
		$(".ed_hk9").hide();
		$(".ed_hk10").hide();
		$(".ed_hk11").hide();
		$(".ed_hk12").hide();
		
		var html='<li style="height:150px;" flag=1 i='+i+' draggable="false" >';
			html+='<span class="ed_name"></span>';
			html+='<p x=1000><input type="radio" /><span>选项</span></p><br />';
			html+='<p x=1001><input type="radio" /><span>选项</span></p><br />';
			html+='<p x=1002><input type="radio"  /><span>选项</span></p><br />';
			html+='<input type="text" name="remark" />';
			html+='<input type="hidden" name="is_must" />';
			html+='<div class="del_fz con4"><span class="ss3" title="复制"></span></div>';
			html+='<div class="del_dd con4" title="删除"><span></span></div></li>';
		
		
		$(".block__list_words").append(html);
		i++;
	})
	$(".k4").click(function(){
		$(".edit_zd a").removeClass("add");
		$(".ss2").addClass("add");
		$(".ed_hk1").hide();
		$(".ed_hk5").show();
		$(".ed_hk2").hide();
		$(".ed_hk3").hide();
		$(".ed_hk4").hide();
		$(".ed_hk6").hide();
		$(".ed_hk7").hide();
		$(".ed_hk8").hide();
		$(".ed_hk9").hide();
		$(".ed_hk10").hide();
		$(".ed_hk11").hide();
		$(".ed_hk12").hide();
		
		var html='<li style="height:150px;" flag=2 i='+i+' draggable="false" >';
		
			html+='<span class="ed_name"></span>';
			html+='<p x=1000><input type="checkbox"  /><span>选项</span></p><br />';
			html+='<p x=1001><input type="checkbox" /><span>选项</span></p><br />';
			html+='<p x=1002><input type="checkbox" /><span>选项</span></p><br />';
			html+='<input type="text" name="remark" />';
			html+='<input type="hidden" name="is_must" />';
			html+='<input type="hidden" name="at_least" />';
			html+='<input type="hidden" name="at_most" />';
			html+='<input type="hidden" name="is_at_least" />';
			html+='<input type="hidden" name="is_at_most" />';
			html+='<div class="del_fz con4"><span class="ss3" title="复制"></span></div>';
			html+='<div class="del_dd con4" title="删除"><span></span></div></li>';
			
		$(".block__list_words").append(html);
		i++;
		
	})
	$(".k5").click(function(){
		$(".edit_zd a").removeClass("add");
		$(".ss2").addClass("add");
		$(".ed_hk1").hide();
		$(".ed_hk9").show();
		$(".ed_hk2").hide();
		$(".ed_hk3").hide();
		$(".ed_hk4").hide();
		$(".ed_hk5").hide();
		$(".ed_hk6").hide();
		$(".ed_hk7").hide();
		$(".ed_hk8").hide();
		$(".ed_hk10").hide();
		$(".ed_hk11").hide();
		$(".ed_hk12").hide();
		
		var html='<li style="height:100px;" flag=3 i='+i+' draggable="false" >';
			html+='<span class="ed_name"></span>';
			html+='<h6></h6>';
			html+='<select><option class="away" x=1000>选项一</option><option x=1001>选项二</option><option x=1002>选项三</option></select><br />';
			html+='<input type="hidden" name="is_must" />';
			html+='<input type="text" name="remark" />';
			html+='<div class="del_fz con4"><span class="ss3" title="复制"></span></div>';
			html+='<div class="del_dd con4" title="删除"><span></span></div></li>';
			
		$(".block__list_words").append(html);
		i++;
		
	})
	$(".k6").click(function(){
		$(".edit_zd a").removeClass("add");
		$(".ss2").addClass("add");
		$(".ed_hk1").hide();
		$(".ed_hk6").show();
		$(".ed_hk2").hide();
		$(".ed_hk3").hide();
		$(".ed_hk4").hide();
		$(".ed_hk5").hide();
		$(".ed_hk7").hide();
		$(".ed_hk8").hide();
		$(".ed_hk9").hide();
		$(".ed_hk10").hide();
		$(".ed_hk11").hide();
		$(".ed_hk12").hide();
		
		var html='<li style="height:100px;" flag=6 i='+i+' draggable="false" >';
			html+='<span class="ed_name"></span>';
			html+='<h6></h6>';
			html+='<input type="text" name="remark" />';
			html+='<input type="hidden" name="is_must" />';
			html+='<input type="hidden" name="is_start_date" />';
			html+='<input type="hidden" name="is_end_date" />';
			html+='<input type="hidden" name="start_date" />';
			html+='<input type="hidden" name="end_date" />';
			html+='<div class="del_fz con4"><span class="ss3" title="复制"></span></div>';
			html+='<div class="del_dd con4" title="删除"><span></span></div></li>';
			
		$(".block__list_words").append(html);
		i++;
		
	})
	$(".k7").click(function(){
		$(".edit_zd a").removeClass("add");
		$(".ss2").addClass("add");
		$(".ed_hk1").hide();
		$(".ed_hk8").show();
		$(".ed_hk2").hide();
		$(".ed_hk3").hide();
		$(".ed_hk4").hide();
		$(".ed_hk5").hide();
		$(".ed_hk6").hide();
		$(".ed_hk7").hide();
		$(".ed_hk9").hide();
		$(".ed_hk10").hide();
		$(".ed_hk11").hide();
		$(".ed_hk12").hide();
		
		var html='<li style="height:100px;" flag=7 i='+i+' draggable="false" >';
			html+='<span class="ed_name"></span>';
			html+='<h6></h6>';
			html+='<input type="text" name="remark" />';
			html+='<input type="hidden" name="is_must" />';
			html+='<input type="hidden" name="is_the_least" />';
			html+='<input type="hidden" name="is_the_most" />';
			html+='<input type="hidden" name="the_least" />';
			html+='<input type="hidden" name="the_most" />';
			html+='<div class="del_fz con4"><span class="ss3" title="复制"></span></div>';
			html+='<div class="del_dd con4" title="删除"><span></span></div></li>';
			
		$(".block__list_words").append(html);
		i++;
		
	})
	$(".k8").click(function(){
		$(".edit_zd a").removeClass("add");
		$(".ss2").addClass("add");
		$(".ed_hk1").hide();
		$(".ed_hk12").show();
		$(".ed_hk2").hide();
		$(".ed_hk3").hide();
		$(".ed_hk4").hide();
		$(".ed_hk5").hide();
		$(".ed_hk6").hide();
		$(".ed_hk7").hide();
		$(".ed_hk8").hide();
		$(".ed_hk9").hide();
		$(".ed_hk10").hide();
		$(".ed_hk11").hide();
		
		var html='<li style="height:100px;" flag=8 i='+i+' draggable="false" >';
			html+='<span class="ed_name"></span>';
			html+='<h6></h6>';
			html+='<input type="text" name="file_num" />';
			html+='<input type="hidden" name="is_must" />';
			html+='<div class="del_fz con4"><span class="ss3" title="复制"></span></div>';
			html+='<div class="del_dd con4" title="删除"><span></span></div></li>';
			
		$(".block__list_words").append(html);
		i++;
		
	})
	$(".k9").click(function(){
		$(".edit_zd a").removeClass("add");
		$(".ss2").addClass("add");
		$(".ed_hk1").hide();
		$(".ed_hk10").show();
		$(".ed_hk2").hide();
		$(".ed_hk3").hide();
		$(".ed_hk4").hide();
		$(".ed_hk5").hide();
		$(".ed_hk6").hide();
		$(".ed_hk7").hide();
		$(".ed_hk8").hide();
		$(".ed_hk9").hide();
		$(".ed_hk11").hide();
		$(".ed_hk12").hide();
		
		var html='<li style="height:100px;" flag=9 i='+i+' draggable="false" >';
			html+='<span class="ed_name"></span>';
			html+='<h6></h6>';
			html+='<input type="text" name="remark" />';
			html+='<input type="hidden" name="is_repeat" />';
			html+='<input type="hidden" name="is_must" />';
			html+='<input type="hidden" name="is_least_bite" />';
			html+='<input type="hidden" name="is_most_bite" />';
			html+='<input type="hidden" name="least_bite" />';
			html+='<input type="hidden" name="most_bite" />';
			html+='<div class="del_fz con4"><span class="ss3" title="复制"></span></div>';
			html+='<div class="del_dd con4" title="删除"><span></span></div></li>';
			
		$(".block__list_words").append(html);
		i++;
		
	})
	$(".k10").click(function(){
		$(".edit_zd a").removeClass("add");
		$(".ss2").addClass("add");
		$(".ed_hk1").hide();
		$(".ed_hk7").show();
		$(".ed_hk2").hide();
		$(".ed_hk3").hide();
		$(".ed_hk4").hide();
		$(".ed_hk5").hide();
		$(".ed_hk6").hide();		
		$(".ed_hk8").hide();
		$(".ed_hk9").hide();
		$(".ed_hk10").hide();
		$(".ed_hk11").hide();
		$(".ed_hk12").hide();
		
		var html='<li style="height:100px;" flag=10 i='+i+' draggable="false" >';
			html+='<span class="ed_name"></span>';
			html+='<h6></h6>';
			html+='<input type="text" name="remark" />';
			html+='<input type="hidden" name="is_repeat" />';
			html+='<input type="hidden" name="is_must" />';
			html+='<div class="del_fz con4"><span class="ss3" title="复制"></span></div>';
			html+='<div class="del_dd con4" title="删除"><span></span></div></li>';
			
		$(".block__list_words").append(html);
		i++;
		
	})
	$(".k11").click(function(){
		$(".edit_zd a").removeClass("add");
		$(".ss2").addClass("add");
		$(".ed_hk1").hide();
		$(".ed_hk11").show();
		$(".ed_hk2").hide();
		$(".ed_hk3").hide();
		$(".ed_hk4").hide();
		$(".ed_hk5").hide();
		$(".ed_hk6").hide();
		$(".ed_hk7").hide();
		$(".ed_hk8").hide();
		$(".ed_hk9").hide();
		$(".ed_hk10").hide();
		$(".ed_hk12").hide();
		
		var html='<li style="height:100px;" flag=11 i='+i+' draggable="false" >';
			html+='<span class="ed_name"></span>';
			html+='<h6></h6>';
			html+='<input type="text" name="remark" />';
			html+='<input type="hidden" name="is_repeat" />';
			html+='<input type="hidden" name="is_must" />';
			html+='<div class="del_fz con4"><span class="ss3" title="复制"></span></div>';
			html+='<div class="del_dd con4" title="删除"><span></span></div></li>';
			
		$(".block__list_words").append(html);
		i++;
		
	})
	
	$(".ss1").click(function(){
		$(".edit_zd a").removeClass("add");
		$(this).addClass("add");
		$(".ed_hk1").show();
		$(".ed_hk2").hide();
		$(".ed_hk3").hide();
		$(".ed_hk4").hide();
		$(".ed_hk5").hide();
		$(".ed_hk6").hide();
		$(".ed_hk7").hide();
		$(".ed_hk8").hide();
		$(".ed_hk9").hide();
		$(".ed_hk10").hide();
		$(".ed_hk11").hide();
		$(".ed_hk12").hide();
	})
	
	$(".ss2").click(function(){
		$(".edit_zd a").removeClass("add");
		$(this).addClass("add");
		//$(".ed_hk1").hide();
		//$(".ed_hk2").show();
	})	
	/**
	function m1(){
		$(".ty_add").click(function(){
			$(this).parents(".ty_oo").append('<li class="ed_ty"><input type="radio" class="ty_radio" name="ty_name1"><input type="text" \
			class="ty_xx" value="选项"><p class="ty_add" onclick="m1(this)"></p><p class="ty_ajj" onclick="m2(this)"></p><p class="ty_bs"></p></li>')
		})
	}
	function m2(){
		$(".ty_ajj").click(function(){
			$(this).parents(".ed_ty").remove();
		})
	}**/
	
	
	
	       		
			    
			    
})
	
	