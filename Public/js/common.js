// 下面代码在页面加载完毕时执行
$(function () {

    // 完成通用ajax请求
    $('.ajax-get').click(function () {

        // 一般是在删除前的确认操作
        if ($(this).hasClass('confirm')) {
            if (!confirm('确认要执行该操作吗?')) {
                return false;
            }
        }
        // 从当前标签上得到请求地址
        var url = $(this).attr('href');
        // 回调函数中的data表示响应数据
        $.get(url, showAjaxLayer,"json");
        // 取消默认操作
        return false;
    });


    // 完成通用post请求,利用jquery.form.js插件实现
    $('.ajax-post').click(function () {
        // 一般是在删除前的确认操作
        if ($(this).hasClass('confirm')) {
            if (!confirm('确认要执行该操作吗?')) {
                return false;
            }
        }

        // 通过向上按钮找到form
        var form = $(this).closest('form');
        if (form.length != 0) {
            // 使用jquery.form.js插件实现的
            form.ajaxSubmit({dataType: "json", success: showAjaxLayer});
        } else {
            // 得到删除按钮上的url
            var url = $(this).attr('url');
            // 不仅可以获得整个表单的值，也可以单独获取每个表单的值
            var params = $('.ids:checked').serialize();
            $.post(url, params, showAjaxLayer,"json");
        }
        // 取消默认操作
        return false;
    })


    // 显示一个提示框,在使用时加载layer.js(拷贝整个layer文件夹到Public下)
    function showAjaxLayer(data) {

        var icon;
        if (data.ret_code == '1000') {
            icon = 1; // 成功符号
        } else {
            icon = 2; // 错误符号
        }
        layer.msg(data.ret_msg, {
            time: 1000, // 等待时间
            offset: 0, // 设置位置
            shift: 5,// 支持动画的场景
            icon: icon
        }, function () {
            if (data.url) { // 如果有地址才转向
                location.href = data.url;
            }
        });
    }


    // 实现全选影响下面复选框的全部选中效果
    $('.selectAll').click(function () {
        $('.ids').prop('checked', $(this).prop('checked'));
    });


    // 实现单个选择影响上方复选框的选中效果
    $('.ids').click(function () {
        $('.selectAll').prop('checked', $('.ids:not(:checked)').length == 0);
    });

  
});


