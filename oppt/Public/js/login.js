/**
 * Created by Administrator on 2017/2/16.
 */
//window.$=HTMLElement.prototype.$=function(selector){
//    var elems=(this==window?document:this) .querySelectorAll(selector);
//    return elems==null?null:elems.length==1?elems[0]:elems;
//}
$(document).ready(function(){
    choose();
    validate()
})
function choose(){
    $(".choose a").click(function(){
        $(".choose a.active").attr("class","");
        $("form.show").attr("class","");
        $(this).attr("class","active");
        $("#F"+$(this).attr("id")).attr("class","show");
    })
}
function validate(){
    $("#FL").validate({
        rules: {
            userNameL: {
                required: true,
                minlength: 2
            },
            pwdL: {
                required: true,
                minlength: 5
            },
            confirm_password: {
                required: true,
                minlength: 5,
                equalTo: "#password"
            }
        },
    });

    $("#FR").validate({
        rules : {
            userNameR: {
                required : true
            },
            pwdR : "required",
            RpwdR: {
                required : true,
                equalTo : "#pwdR"
            },
        },
        messages : {
            userNameR : "请输入你的用户名",
            pwdR: "请输入你的密码",
            RpwdR: {
                required : "请输入你的密码",
                equalTo : "两次密码不一致"
            },
        }
    });
}