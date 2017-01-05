/**
 * Created by mafengli on 16/6/2.
 */
$(function () {
    $("#addRoleForm").validate({
        submitHandler: function (form) {
            $.ajax({
                type: 'post',
                data: $("#addRoleForm").serialize(),
                url: $("#addRoleForm").attr('action'),
                dataType: 'json',
                success: function (data) {
                    if (data.status == 1) {
                        alert(data.info);
                        window.location.href = data.url;
                    } else {
                        alert(data.info);
                    }
                }
            })

        }
    });
    $("#editGroupForm").validate({
        submitHandler: function (form) {
            $.ajax({
                type: 'post',
                data: $("#editGroupForm").serialize(),
                url: $("#editGroupForm").attr('action'),
                dataType: 'json',
                success: function (data) {
                    if (data.status == 1) {
                        alert(data.info);
                        window.location.href = data.url;
                    } else {
                        alert(data.info);
                    }
                }
            })

        }
    });
    $("#addToGroupForm").validate({
        submitHandler: function (form) {
            $.ajax({
                type: 'post',
                data: $("#addToGroupForm").serialize(),
                url: $("#addToGroupForm").attr('action'),
                dataType: 'json',
                success: function (data) {
                    if (data.status == 1) {
                        alert(data.info);
                        window.location.href = data.url;
                    } else {
                        alert(data.info);
                    }
                }
            })

        }
    });

    $("#deleteGroupBtn").click(function () {
        if ($("input[name='id']:checked").length > 0) {
            $.ajax({
                type: 'post',
                url: $(this).attr("_href"),
                data: {'id': getRoleIds()},
                dataType: 'json',
                success: function (data) {
                    if (data.status == 1) {
                        alert(data.info);
                        window.location.href = window.location.href;
                    } else {
                        alert(data.info);
                    }
                }
            })
        } else {
            alert("请选择要操作的组");
        }
    });


    $("#updateRoleSystemBtn").click(function () {
        var nodes = zTreeObj.getCheckedNodes();
        var keys = '';
        for (var i = 0; i < nodes.length; i++) {
            if (keys == '') {
                keys = nodes[i]['key'];
            } else {
                keys = keys + ',' + nodes[i]['key'];
            }
        }
        $.ajax({
            type: 'post',
            url: $(this).attr('_href'),
            data: {'keys': keys},
            dataType: 'json',
            success: function (data) {

            }
        })

    });

    //全选节点
    $('.rules_all').on('change', function () {
        $(this).closest('dl').find('dd').find('input').prop('checked', this.checked);
    });
    $('.rules_row').on('change', function () {
        $(this).closest('.rule_check').find('.child_row').find('input').prop('checked', this.checked);
        if (this.checked) {
            $(this).closest('dl').find('dt').find('input').prop('checked', this.checked);
        } else {
            if (!($(this).closest('dd').find('input:checked').length > 0)) {
                $(this).closest('dl').find('dt').find('input').prop('checked', this.checked);
            }
        }
    });
    $(".rules_op").on('change', function () {
        if (this.checked) {
            $(this).closest('.rule_check').find('.rules_row').prop('checked', this.checked);
            $(this).closest('dl').find('dt').find('.rules_all').prop('checked', this.checked);
        } else {
            var count = 0;
            $(this).closest('.rule_check').find('.rules_op').each(function () {
                if ($(this).is(":checked")) {
                    count++;
                }
            });
            if (count == 0) {
                $(this).closest('.rule_check').find('.rules_row').prop('checked', this.checked);
            }
            if (!($(this).closest('dd').find('input:checked').length > 0)) {
                $(this).closest('dl').find('dt').find('input').prop('checked', this.checked);
            }
        }
    });

    $('.checkbox').each(function () {
        $(this).qtip({
            content: {
                text: $(this).attr('title'),
                title: $(this).text()
            },
            position: {
                my: 'bottom center',
                at: 'top center',
                target: $(this)
            },
            style: {
                classes: 'qtip-dark',
                tip: {
                    corner: true,
                    mimic: false,
                    width: 10,
                    height: 10
                }
            }
        });
    });

    $('select[name=group]').change(function () {
        location.href = this.value;
    });
    $("#editAuthGroupModuleBtn").click(function () {
        $.ajax({
            type: 'post',
            data: $("#authGroupModuleForm").serialize(),
            url: $("#authGroupModuleForm").attr('action'),
            success: function (data) {
                if (data.status == 1) {
                    alert(data.info);
                } else {
                    alert(data.info);
                }
            }
        })
    })
});

function getRoleIds() {
    var roleIds = '';
    $("input[name='id']:checked").each(function () {
        if (roleIds == '') {
            roleIds = $(this).val();
        } else {
            roleIds = roleIds + ',' + $(this).val();
        }
    });
    return roleIds;
}