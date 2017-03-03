$(function () {
    //验证用户名
    $("input[name='username']").focus(function () {
        //提示信息增加样式
        $(".usernametips").removeClass("state2");
        //聚焦时显示提示的内容
        $(".usernametips").text("字母开头的数字、字母、'_'的组合");
        $(".usernameIco").hide();
    }).blur(function () {
        var txt = $("input[name='username']").val();
        //ajax请求

        //声明一个用户名要求的格式
        var registerName = /^[a-zA-Z][a-zA-Z0-9_]*$/;
        if (txt == "") {//当文本为空时
            $(".usernametips").text("用户名不能为空").addClass("state2");
        } else if (txt.length > 12) {//当文本字符大于12时
            $(".usernametips").text("格式不正确，超出12位").addClass("state2");
        } else if (txt.length < 4) {//当文本字符小于4时
            $(".usernametips").text("格式不正确，不足4位").addClass("state2");
        } else if (!(registerName.test(txt))) {//当文本内容没有与用户名要求格式匹配时
            $(".usernametips").text("以首字母开头且不能有特殊字符").addClass("state2");
        } else {
            $(".usernametips").text("");
            $(".usernameIco").show();
        }
    });
    //密码验证
    $("input[name='password']").focus(function () {
        //聚焦时，提示内容颜色格式的增减
        $(".passwordtips").removeClass("state2");
        //聚焦时显示提示的内容
        $(".passwordtips").text("密码为6-16位的字母，数字组合");
        $(".passwordIco").hide();
    }).blur(function () {
        //获取密码提示盒子的值
        var txt = $("input[name='password']").val();
        //声明变量，密码正则格式
        var allnum = /^\d+$/g;//密码为纯数字时
        var allletter = /^[A-Za-z]+$/;//密码为纯字母时
        if (txt == "") {//密码为空时
            $(".passwordtips").text("密码不能为空");
            $(".passwordtips").addClass("state2");
        } else if (txt.length < 6) {
            //当密码长度小于6时
            $(".passwordtips").text("密码不足6位");
            $(".passwordtips").addClass("state2");
        } else if (txt.length > 16) {
            //当密码长度大于16时
            $(".passwordtips").text("密码超出16位");
            $(".passwordtips").addClass("state2");
        } else if (allnum.test(txt)) {//当密码全为数字时
            $(".passwordtips").text("密码不能为纯数字");
            $(".passwordtips").addClass("state2");
        } else if (allletter.test(txt)) {//当密码全为字母时
            $(".passwordtips").text("密码不能为纯字母");
            $(".passwordtips").addClass("state2");
        } else {//匹配密码成功时
            $(".passwordtips").text("");
            $(".passwordIco").show();
        }
    });
    //确认密码验证
    $("input[name='confirmpassword']").focus(function () {
        $(".confirmpasswordtips").removeClass("state2");
        $(".confirmpasswordtips").text("请输入相同密码");
        $(".confirmpasswordIco").hide();
    }).blur(function () {
        var txt = $("input[name='confirmpassword']").val();
        if ((txt == $("input[name='password']").val()) && (txt != "")) {
            $("input[name='confirmpassword']").addClass("icos");
            $(".confirmpasswordtips").text("");
            $(".confirmpasswordIco").show();
        } else {
            $(".confirmpasswordtips").text("输入不正确，请再次输入").addClass("state2");
        }
    });

    // 注册验证
    $("input[name='button']").click(function () {
        var usernameval = $("input[name='username']").val();
        var pwdval = $("input[name='password']").val();
        var cpwdval = $("input[name='confirmpassword']").val();
        if (usernameval == "" ||
            pwdval == "" ||
            cpwdval == "") {
            $(".usernametips").text("用户名不能为空").addClass("state2");
            $(".passwordtips").text("密码不能为空").addClass("state2");
            $(".confirmpasswordtips").text("密码不能为空").addClass("state2");
            return false;
        } else if (!(pwdval == cpwdval)) {
            $(".confirmpasswordtips").text("输入密码与上面不匹配").addClass("state2");
            $(".usernametips").text("");
            $(".passwordtips").text("");
            return false;
        }
    });
});