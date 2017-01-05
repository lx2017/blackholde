$(function () {
	var saleManager = {
		init: function () {
			this.generateUrl();
			this.backList();
			this.backUrl();
			this.addLowerValidate();//添加下级校验
		},
		generateUrl:function(roleId,url){
			switch(parseInt(roleId)){
			case 1:
				url= "../Total/"+url;
				break;	
			case 2:
				url= "../Area/"+url;
				break;
			case 3:
				url= "../Province/"+url;
				break;
			case 4:
				url= "../City/"+url;
				break;
			case 5:
				url= "../County/"+url;
				break;
			default:url= "../County/"+url;
			}
			return url;
		},
		addLowerValidate:function(){
			//添加下级校验
			$('.countyInfo').on('click','.weui-btn-area',function(){
				//校验姓名
				if(($('#real_name').val()).trim().length==0){
					$('.weui-dialog__bd').html('请填写姓名');
    	     			$('#confirm_dialog').css('display','block');
					return false;
				}
				//手机号校验
				if(($('#phone').val()).trim().length==0){
					$('.weui-dialog__bd').html('请填写正确的手机号');
	     			$('#confirm_dialog').css('display','block');
	     			return false;
				}
				//身份证校验
				if(($('#card_number').val()).trim().length==0){
					$('.weui-dialog__bd').html('请填写身份证');
	     			$('#confirm_dialog').css('display','block');
	     			return false;
				}
				//年龄校验
				if(($('#age').val()).trim().length==0){
					$('.weui-dialog__bd').html('请填写年龄');
	     			$('#confirm_dialog').css('display','block');
	     			return false;
				}
				//所属地校验
				if(($('#belong_locations').val()).trim().length==0){
					$('.weui-dialog__bd').html('请填写所属地');
	     			$('#confirm_dialog').css('display','block');
	     			return false;
				}
				
			})
		},
		backUrl:function(roleId,url){
			switch(parseInt(roleId)){
			case 0:
				url= "/Home/SaleManager/Head/"+url; //总部
				break;	
			case 1:
				url= "/Home/SaleManager/Salemanager/"+url; //大区经理
				break;
			case 2:
				url= "/Home/SaleManager/Province/"+url;//省总
				break;
			case 3:
				url= "/Home/SaleManager/City/"+url;//地总
				break;
			case 4:
				url= "/Home/SaleManager/County/"+url;//县总
				break;
			default:url= "/Home/Saleman/Saleman/"+url;//业务员
			}
			return url;
		},
		backList:function(){
			$('.approvalRecord').on('click','.aproving',function(){
				roleId = $(this).attr('data-role-id');
				window.location.href = saleManager.backUrl(roleId,'index');
			})
			
			//返回
        $(".personData").on('click','.infos',function(){
        		roleId = $(this).attr('data-role-id');
	        	if(saleManagerStatus=='success'){	
	        		window.location.href = saleManager.backUrl(roleId,'index');
	        	}else{
	        		window.location.href = saleManager.backUrl(roleId,'index');
	        	}    	
		})
		//目标诊所返回
		$(".targetClinic_w").on('click','.targetBack',function(){
    			roleId = $(this).attr('data-role-id');
        		window.location.href = saleManager.backUrl(roleId,'index');  	
		})
		
		//没有目标诊所返回
		$(".noClinic").on('click','.targetBack',function(){
    			roleId = $(this).attr('data-role-id');
        		window.location.href = saleManager.backUrl(roleId,'index');  	
		})
		//我的诊所返回
		$(".clinic").on('click','.clinicBack',function(){
    			roleId = $(this).attr('data-role-id');
        		window.location.href = saleManager.backUrl(roleId,'index');  	
		})
		$(".clinicOrder").on('click','.orderlistfor',function(){
    			roleId = $(this).attr('data-role-id');
        		window.location.href = saleManager.backUrl(roleId,'index');  	
		})
		//修改密码返回
		$(".order_list_w").on('click','.orderlistfor',function(){
    			roleId = $(this).attr('data-role-id');
        		window.location.href = saleManager.backUrl(roleId,'index');  	
		})
		//审批支持返回
		$(".appliceSupport").on('click','.applicesupport',function(){
    			roleId = $(this).attr('data-role-id');
        		window.location.href = saleManager.backUrl(roleId,'approvalRecord');  	
		})
		}
	}

	saleManager.init();
})