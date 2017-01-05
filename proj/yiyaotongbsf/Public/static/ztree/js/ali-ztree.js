/**
 * Created by ali on 2016/9/19.
 */

//点击x删除图片
$('.del-img').live('click',function(){
    var liObj = $(this).parents('li');
    //找到父级li里面的地址
    var path = liObj.attr('path');
    var thumb = liObj.attr('thumb');
    //发异步到服务器删除
    $.ajax({
        type:"post",
        url:$("#hiddenajax").val(),
        data:{path:path,thumb:thumb},
        success:function(data){
            //移除图片
            liObj.remove();
        }

    });
});

var setting = {
    edit: {
        enable: true,
        showRenameBtn:false,
        showRemoveBtn:true
    },
    data: {
        simpleData: {
            enable: true
        }
    },
    callback: {
        onRemove : zTreeOnRemove ,
        onClick: zTreeOnClick
    }
};

var zNodes ={$data};
var i=1;

function zTreeOnClick(event, treeId, treeNode) {
    $("div#add").remove();
    i++;
    if(i%2==0){
        $("#right").show();
        var html='';
        $.ajax({
            type:"post",
            url: $("#edit").val(),
            dataType:"json",
            data:{column_id:treeNode.id,column_pid:treeNode.pId},

            success:function(pp){
                html='<div id="add">';
                html+='<form  id="editGroupForm" method="post" action="" enctype="multipart/form-data" >';
                html+='<p><label for="auth-title" class="form-label col-3">文章所属栏目</label>';
                html+='<select  id="auth-title" style="width:300px;" type="text" name="column_pid" class="input-text" value="{$auth_group.title}" required minlength="0" maxlength="20">';
                $.each(pp.column,function(k,v){
                    html+='<option value="'+v.column_id+'" <if condition="'+v.column_id+' eq '+pp.column_pid+'" > selected </if> >';
                    html+=v.column_title;
                    html+='</option>';
                });
                html+='</select>';
                html+='<p style="margin-bottom:15px;"><label for="auth-title" class="form-label col-3">栏目名称</label>';
                html+='<input type="text" name="column_title" value="'+pp.column_title+'" /></p>'
                html+='<input type="hidden" name="column_id" value="'+pp.column_id+'" />';
                html+='<p style="margin-bottom:15px;"><label class="form-label col-3">颜色</label>';
                html+='<input type="text" name="app_color_style" value="'+pp.app_color_style+'" /></p>';
                html+='<p style="margin-bottom:15px;"><label class="form-label col-3">排序第几位</label>';
                html+='<input type="text" name="sort" value="'+pp.sort+'" /></p>';
                html+='<p style="margin-bottom:15px;"><label class="form-label col-3">Logo原图</label>';
                html+='<input type="text" name="app_icon_url" value="'+pp.app_icon_url+'" /></p>';
                html+='<img width=130 height=50 src="'+pp.app_icon_url+'" /> </p>';
                html+='<p><label class="form-label col-3">更换Logo</label></p>';
                html+='<input class="btn btn-primary radius" type="submit" value="确定"> ';
                html+='<input type="hidden" name="app_icon_url" value="'+pp.app_icon_url+'"/>';
                html+='</form>';
                html+='</div>';

                $("#right").prepend(html);
            },

            error:function(){
            }
        });
    }else{
        $("#right").hide();
    }
};
function zTreeOnRemove(event, treeId, treeNode) {

    $.ajax({
        type:"get",
        url: $("#del").val(),
        dataType:"json",
        data:{id:treeNode.id},

        success:function(pp){


        },
        error:function(){

        }
    });

}
function zTreeOnRename(event, treeId, treeNode, isCancel) {

    $.ajax({
        type:"post",
        url: $("#edit").val(),
        dataType:"json",
        data:{column_id:treeNode.id,column_title:treeNode.name,column_pid:treeNode.pId},

        success:function(pp){
            //TODO
        },
        error:function(){
            //TODO
        }
    });
}
function setEdit() {
    var zTree = $.fn.zTree.getZTreeObj("treeDemo"),
        remove = $("#remove").attr("checked");
    rename = $("#rename").attr("checked");
    removeTitle = $.trim($("#removeTitle").get(0).value);
    renameTitle = $.trim($("#renameTitle").get(0).value);
    zTree.setting.edit.showRemoveBtn = remove;
    zTree.setting.edit.showRenameBtn = rename;
    zTree.setting.edit.removeTitle = removeTitle;
    zTree.setting.edit.renameTitle = renameTitle;
    showCode(['setting.edit.showRemoveBtn = ' + remove, 'setting.edit.showRenameBtn = ' + rename,
        'setting.edit.removeTitle = "' + removeTitle +'"', 'setting.edit.renameTitle = "' + renameTitle + '"']);
}
function showCode(str) {
    var code = $("#code");
    code.empty();
    for (var i=0, l=str.length; i<l; i++) {
        code.append("<li>"+str[i]+"</li>");
    }
}
//是否显示编辑按钮
function  showRenameBtn(treeId, treeNode){
    //获取节点所配置的noEditBtn属性值
    if(treeNode.noEditBtn != undefined && treeNode.noEditBtn){
        return false;
    }else{
        return true;
    }
}
//是否显示删除按钮
function showRemoveBtn(treeId, treeNode){
    //获取节点所配置的noRemoveBtn属性值
    if(treeNode.noRemoveBtn != undefined && treeNode.noRemoveBtn){
        return false;
    }else{
        return true;
    }
}

$(document).ready(function(){
    $.fn.zTree.init($("#treeDemo"), setting, zNodes);
    setEdit();
    setting.edit.showRemoveBtn = remove;
    setting.edit.showRenameBtn = rename;
    $("#remove").bind("change", setEdit);
    $("#rename").bind("change", setEdit);
    $("#removeTitle").bind("propertychange", setEdit).bind("input", setEdit);
    $("#renameTitle").bind("propertychange", setEdit).bind("input", setEdit);

    $("span#treeDemo_12_edit").show();
});