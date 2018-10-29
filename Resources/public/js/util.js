const show500Warning = function () {
    toastr.error("您的修改未保存成功", "消息提示");
};

$(document).ready(function () {

    $('.request-ajax-for-active').off('click').on('click', function () {
        const url = $(this).data('url');
        $.ajax({
            type: 'POST',
            url: url,
            dataType: 'json',
            success: function (response) {
                if (response.status === 'T') {
                    toastr.success('修改成功', '消息提示');
                } else if (response.status === '1') {
                    toastr.success('修改成功', '消息提示');
                } else {
                    show500Warning();
                }
            },
            failure: function (errMsg) {
                show500Warning();
            }
        });
    });

    $(".delete_single_button").off("click").on("click", function () {
        const dialogTitle = $(this).data('title') || '删除提醒';
        const dialogMessage = $(this).data('message') || '您确定要删除吗？所有与之关联的数据都会被删除';
        const deleteUrl = $(this).attr('href');
        $.confirm({
            title: dialogTitle,
            content: dialogMessage,
            buttons: {
                cancel: {
                    text: '取消',
                    action: function () {
                    }
                },
                confirm: {
                    btnClass: 'btn-warning',
                    text: '确认',
                    action: function () {
                        return location.href = deleteUrl;
                    }
                },
            }
        });
        return false;
    });


    $('.submit').click(function () {
        $('form').submit();
    });

    $('.huan_yue_tip').click(function () {
        $message = $(this).data('tip');
        layer.tips($message, $(this));
    });
});
