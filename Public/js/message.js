
$(function(){
    $("#send").click(function () {
        $.ajax({
            type: "post",
            url: "",
            data: {
                "receiver": $("#receiver").val(),
                "content": $("#content").val()
            },
            //dataType: "json",
            success: function (result) {
                //layer.close(index);
                //console.debug(result.username);
                //$('.oklio').text(result.username);
                alert('发送成功');
            },
            error: function () {
                //layer.close(index);
                alert('用户名或密码错误！');

            }
        });
    });
});


