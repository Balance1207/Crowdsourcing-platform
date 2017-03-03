/*验证函数*/
// window.ontouchstart = function(e) { e.preventDefault(); };
function isQq(str) {
    str = $.trim(str);
    var validate = /^[1-9][0-9]{4,9}$/.test(str);
    if (validate) {
        return true;
    }
    return false;
}

function isTeleNum(str) {
    str = $.trim(str);
    var validate = /^(\+86)?(\s)?(13|14|15|17|18)\d{9}$/.test(str);
    if (validate) {
        return true;
    }
    return false;
}

function isEmail(str) {
    str = $.trim(str);
    var validate = /^\w+((-\w+)|(\.\w+))*\@[A-Za-z0-9]+((\.|-)[A-Za-z0-9]+)*\.[A-Za-z0-9]+$/.test(str);
    if (validate) {
        return true;
    }
    return false;
}
/*验证函数 END*/

/*表单验证结果处理函数*/
function errorTip(element) {
    $(element).siblings('.form-control-feedback').attr("class", "glyphicon glyphicon-remove form-control-feedback").closest('.has-feedback').addClass('has-error');
    $(element).data("errored", true);
    $(element).focus();
}

function defaultTip(element) {
    return $(element).siblings('.form-control-feedback').attr("class", 'glyphicon form-control-feedback').closest('.has-feedback').removeClass('has-error has-warning');
}

function warningTip(element) {
    $(element).siblings('.form-control-feedback').attr("class", "glyphicon glyphicon-warning-sign form-control-feedback").closest('.has-feedback').removeClass('has-error has-success').addClass('has-warning');
    $(element).focus();
}
/*表单验证结果处理函数*/

//密码强度函数
function pwdStrength(pwd) {
    var regExp = /^([a-z]+|[A-Z]+|\d+|[^0-9a-zA-Z]+)$/;
    if (regExp.test(pwd)) {
        return 1; //只包含一类字符
    } else {
        regExp = /^[a-z0-9]+$/;
        if (regExp.test(pwd)) {
            return 2; //包含2类字符
        } else {
            return 3; //不止2类字符
        }
    }
}

/*禁止modal遮罩层滚动*/
if($("#myModal").length){
    $("#myModal").on('touchmove', function(e) {
        e.preventDefault();
    });
}
/*禁止modal遮罩层滚动 END*/

/*序列化表单元素转化为json格式*/
function requestData(formJq, extraObj) {
    var arr = formJq.serializeArray();
    var data = {};
    for (var i in arr) {
        var name = arr[i].name;
        var value = arr[i].value;
        data[name] = value;
    }
    for (var j in extraObj) {
        data[j] = extraObj[j];
    }
    return data;
}
/*序列化表单元素转化为json格式*/

/*下拉菜单局部滑动 scrollable类 ul标签最大高度180+2px (border)*/
$(document).on("touchstart", ".scrollable", function(e) {
    this.posY = e.originalEvent.touches[0].clientY; /*e.originalEvent为原生event对象*/
    this.scrY = $(this).scrollTop();
});
$(document).on("touchmove", ".scrollable", function(e) {
    e.preventDefault(); /*防止全屏滚动*/
    e.stopPropagation();
    var dis = e.originalEvent.touches[0].clientY - this.posY;
    $(this).scrollTop(this.scrY - dis);
});
/*下拉菜单局部滑动 END*/





/*登录页面index*/
function indexJs() {
    $("#topNavWrap").remove();

    var loginItems = $("#login").find("[name]");
    var signItems = $("#signup").find("[name]");


    $(".mzm-btn-toggle").on("click", function(e) {
        var $this = $(this);
        var hrefId = $this.attr("href");
        var hrefElement = $(hrefId);
        // setTimeout(function() {
        //     hrefElement.find("[name]").eq(0).focus();
        // });
        var siblingPanelId = $this.parent().siblings('.panel-heading').find(".btn").attr("href");
        var siblingPanel = $(siblingPanelId);
        if(!$("#loginWrap").hasClass('unfold')){
            $("#loginWrap").addClass('unfold');
        }
        siblingPanel.find("[name]").each(function(index, el) {
            defaultTip(el);
            $(el).val("");
        });
        $this.removeClass('btn-default').addClass('btn-primary');
        $(".mzm-btn-toggle").not($this).removeClass('btn-primary').addClass('btn-default').attr("data-toggle", "collapse").parent().insertAfter($this.parent());
        var clickForId = $this.attr("id").split("Btn").join("");
        if ($("#" + clickForId).hasClass('in')) {
            // e.stopPropagation();
            $this.removeAttr('data-toggle');
            hrefElement.find("[name]").each(function(index, el) {
                $(el).triggerHandler('blur');
            });
            /*表单验证通过*/
            if (hrefElement.find(".glyphicon-remove,.glyphicon-warning-sign").length === 0) {
                if ($(this).attr("id") === "loginBtn") {
                    $.ajax({
                            type: 'POST',
                            data: requestData($("#login"), { sign: "login" })
                        })
                        .done(function(data) {
                            data = JSON.parse(data);
                            code = data.ret_code;
                            switch (code) {
                                case 1000:
                                    console.log("登录成功");
                                    location.href = JSV.PATH_SERVER+'Index/user';
                                    break;
                                case 1001:
                                    $("#myModal").find(".modal-body>.modal-main").html("验证码错误");
                                    $("#myModal").modal("show");
                                    break;
                                case 1002:
                                    $("#myModal").find(".modal-body>.modal-main").html("用户名与密码不匹配");
                                    $("#myModal").modal("show");
                                    break;
                            }
                            $('#myModal').on('hidden.bs.modal', function() {
                                if (code === 1001) {
                                    errorTip(loginItems.filter("[name=verifyCode]")[0]);
                                } else if (code === 1002) {
                                    errorTip(loginItems.filter("[name=tel]")[0]);
                                    errorTip(loginItems.filter("[name=pwd]")[0]);
                                }
                            });

                        })
                        .fail(function() {
                            console.log("error");
                            $("#myModal").modal("show");
                        });


                } else {
                    $.ajax({
                            type: 'POST',
                            data: requestData($("#signup"), { sign: "register" })
                        })
                        .done(function(data) {
                            data = JSON.parse(data);
                            code = data.ret_code;
                            switch (code) {
                                case 1000:
                                    console.log("注册成功");
                                    location.href = JSV.PATH_SERVER+'Index/user';
                                    break;
                                case 1001:
                                    $("#myModal").find(".modal-body>.modal-main").html("验证码错误");
                                    $("#myModal").modal("show");
                                    break;
                                case 1002:
                                    $("#myModal").modal("show");
                                    break;
                                case 1003:
                                    $("#myModal").find(".modal-body>.modal-main").html("用户名已注册");
                                    $("#myModal").modal("show");
                                    break;
                                case 1004:
                                    $("#myModal").find(".modal-body>.modal-main").html("密码格式有误");
                                    $("#myModal").modal("show");
                                    break;
                            }
                            $('#myModal').on('hide.bs.modal', function() {
                                if (code === 1001) {
                                    errorTip(signItems.filter("[name=verifyCode]")[0]);
                                } else if (code === 1002) {
                                    errorTip(signItems.filter("[name=tel]")[0]);
                                } else if (code === 1003) {
                                    errorTip(signItems.filter("[name=tel]")[0]);
                                } else if (code === 1004) {
                                    errorTip(signItems.filter("[name=pwd]")[0]);
                                    errorTip(signItems.filter("[name=pwd2]")[0]);
                                }
                            });
                        })
                        .fail(function() {
                            console.log("error");
                            $("#myModal").modal("show");
                        });
                }
            }
        }
    });
    $(".form-control-feedback").on("click", function() {
        var $this = $(this);
        if ($this.is('.glyphicon-remove,.iconfont,.glyphicon-warning-sign')) {
            if ($this.siblings("[name]").is(signItems.filter("[name=pwd]"))) {
                var reSignPsd = $this.parent().nextAll(".input-group").find("[name=pwd2]");
                defaultTip(reSignPsd);
                reSignPsd.val("");
                reSignPsd.off("blur");
            }
            $this.siblings("[name]").val("").focus();
            reSignPsd && reSignPsd.on("blur", blurValidate);
        }
    });


    signItems.add(loginItems).each(function(index, el) {
        var $this = $(el);
        $this.on("blur", blurValidate);
        $this.on("keyup", keyupValidate);
    });

    function blurValidate() {
        var $this = $(this);
        if ($this.parents(".panel-collapse").hasClass('in')) {
            var val = $.trim($this.val());
            var valLength = val.length;
            if (!valLength || $this.siblings().hasClass('glyphicon-remove')) {
                errorTip(this);
                // $this.focus();
            } else if ($this.siblings().hasClass('glyphicon-warning-sign')) {
                warningTip(this);
            } else {
                if ($this.is(signItems.filter("[name=pwd2]"))) {
                    if( $this.val() === signItems.filter("[name=pwd]").val() ){
                        errorTip(this);
                    }
                }
            }
            var tName=$this.attr("name");
            if (tName === "tel") {
                isTeleNum(val) || errorTip(this);
            } else if (tName === "pwd") {
                if (!/^\w{6,15}$/.test(val)) {
                    errorTip(this);
                }
            } else if(tName === "pwd2"){
                if(val !== signItems.filter("[name=pwd]").val()){
                    errorTip(this);
                }
            }

        }
    }

    function keyupValidate() {
        var $this = $(this);
        var val = $.trim($this.val());
        var valLength = val.length;
        var tName = $this.attr("name");
        /*用户名有效性验证*/
        if (tName === "tel") {
            if (isTeleNum(val)) {
                if($this.siblings().hasClass('glyphicon-remove') ){
                    defaultTip(this);
                }
            } else if ($this.data("errored")) {
                errorTip(this);
            }
        }
        /*用户名有效性验证 END*/

        /*登录密码验证*/
        else if (tName === "pwd" || tName === "pwd2") {
            if (/^\w{6,15}$/.test(val)) {
                if($this.siblings().hasClass('glyphicon-remove') ){
                    defaultTip(this);
                }
                if ($this.is(signItems.filter("[name=pwd]"))) {
                    $this.parent().nextAll(".input-group").find("[name=pwd2]").attr("disabled", false);
                    if (pwdStrength(val) === 1) {
                        $this.siblings('.form-control-feedback').attr("class", "form-control-feedback iconfont icon-shibaibiaoqing");
                    } else if (pwdStrength(val) === 2) {
                        $this.siblings('.form-control-feedback').attr("class", "form-control-feedback iconfont icon-emoji02");
                    } else {
                        $this.siblings('.form-control-feedback').attr("class", "form-control-feedback iconfont icon-emojiicon");
                    }
                }

            } else if ($this.data("errored")) {
                errorTip(this);
            }
        }
        /*登录密码验证 END*/
        else if (tName === "verifyCode") {
            if(valLength){
                defaultTip(this);
            }
        }

    }
    
}
/*登录页面index END*/

/*项目管理pm*/
function pmJs() {
    $("#topTitle").text("项目管理");

    /*初始化模态框*/
    $('#myModal').modal({
        backdrop: "static",
        show: false
    });
    /*初始化模态框 END*/

    /*全局变量*/
    var modalBtn=$("#modalBtn");
    /*全局变量END*/
    /*发布项目*/
    var inputs = $("[name]", "#collapseOne");
    inputs.each(function() {
        var $this = $(this);
        $this.on("blur", blurValidate);
        $this.on("keyup", keyupValidate);
    });

    function blurValidate() {
        var $this = $(this);
        var val = $.trim($this.val());
        var valLength = val.length;
        if (!valLength || $this.siblings().hasClass('glyphicon-remove')) {
            if ($this.hasClass('datePicker') || $this.attr("name") === "requirement") {
                return;
            } else {
                errorTip(this);
            }
        } else if ($this.siblings().hasClass('glyphicon-warning-sign')) {
            warningTip(this);
        } else {
            defaultTip(this);
        }
    }

    function keyupValidate() {
        var $this = $(this);
        var val = $.trim($this.val());
        // var valLength = val.length;
        switch ($this.attr("name")) {
            case "money":
                if (!/^[0-9]+(\.)?[0-9]*$/.test(val)) {
                    errorTip(this);
                } else {
                    defaultTip(this);
                };
                break;
            case "requirement":
                if (!/^[\u4e00-\u9fa5a-zA-Z][\u4e00-\u9fa5\w]*$/.test(val)) {
                    errorTip(this);
                } else {
                    defaultTip(this);
                };
                break;
            default:
                if (val) {
                    defaultTip(this);
                }
        }
    }


    //日期选择
    var currYear = (new Date()).getFullYear();
    var opt = {};
    opt.date = { preset: 'date' };
    opt.datetime = { preset: 'datetime' };
    opt.time = { preset: 'time' };
    opt.default = {
        theme: 'android-ics light', //皮肤样式
        display: 'modal', //显示方式 
        mode: 'scroller', //日期选择模式
        dateFormat: 'yyyy-mm-dd',
        lang: 'zh',
        showNow: true,
        nowText: "今天",
        startYear: currYear - 10, //开始年份
        endYear: currYear + 10 ,//结束年份
        defaultValue:new Date("2016-01-01")
    };

    $("input[name=contractDate]", "#collapseOne").mobiscroll($.extend(opt['date'], opt['default'])).on("change", function() {
        if ($(this).val()) {
            defaultTip(this);
        }
    });


    //日期选择 END

    /*下拉列表模仿select*/
    $(".pickMenu").find("a").on("click", function() {
        $(this).closest('.pickMenu').prevAll("button").find(".picked").text($(this).text());
    });
    /*下拉列表模仿select END*/
    /*提交 取消按钮*/
    $("#pmSubmitBtn").on("click", function() {
        inputs.each(function(index, el) {
            $(el).triggerHandler('blur');
            if ($(el).hasClass('datePicker') || $(el).attr("name") === "requirement") {
                var val = $(el).val();
                val = $.trim(val);
                if (!val) {
                    errorTip(el);
                }
            }

        });
        if ($("#collapseOne").find(".glyphicon-remove,.glyphicon-warning-sign").length === 0) {
            var dateUnit = $("#dateUnit").text();
            var statusTxt = $("#status").text();
            $.ajax({
                    type: 'POST',
                    data: requestData($("#publishForm"), { sign: "item", status: statusTxt, days: inputs.filter("[name=days]").val() + dateUnit })
                })
                .done(function(data) {
                    data = JSON.parse(data);
                    if(data===0){
                         $(".modal-body>.modal-main").html("发布失败");
                         $("#myModal").modal("show");  
                    }else{
                         location.href = JSV.PATH_SERVER+'Index/pd_item/id/'+data.id;
                    }
                })
                .fail(function() {
                    $(".modal-body>.modal-main").html("连接失败");
                    $("#myModal").modal("show");
                });


        }
    });
    $("#pmCancelBtn").on("click", function() {
        var inputs = $("#collapseOne").find("[name]");
        inputs.each(function(index, el) {
            defaultTip(el);
            $(el).off("blur");
            $(el).blur();
            $(el).on("blur", blurValidate);
        });
    });
    /*提交 取消按钮 END*/
    $(".form-control-feedback").on("click", function() {
        var $this = $(this);
        if ($this.is('.glyphicon-remove,.iconfont,.glyphicon-warning-sign')) {
            $this.siblings("[name]").val("").focus();
        }
    });
    /*发布项目 END*/

    $(".txtPopup").on("click", function() {
        defaultTip(this);
        var inputTitle=$(this).prevAll(".input-group-addon").text();
        var textarea = $('<textarea class="scrollable" placeholder="请输入你的需求"></textarea>');
        textarea.val( $(this).val() ).data("inputObj", $(this));
        $(".modal-body>.modal-main").append(textarea);
        $("#myModal").find(".modal_title").text(inputTitle).end().modal("show");
        $("body").css("overflow", "hidden");
    });
    /*禁止遮罩层滚动*/
    $("#myModal").on('touchmove', function(e) {
        if ($(this).outerWidth() <= $(window).width() && $(this).outerHeight() <= $(window).height()) {
            e.preventDefault();
        }
    });
    /*禁止遮罩层滚动 END*/
    $("#modalBtn").on("click", function() {
        var textarea = $(".modal-body>.modal-main").find("textarea");
        if (textarea.length) {
            var val = textarea.val();
            textarea.data("inputObj").val(val);
        }
    });

    $('#myModal').on('hide.bs.modal', function() {
        //清除modal标题和modal-body内容
        var myModal=$("#myModal");
        myModal.find(".modal_title").text("").end().find(".modal-main").html("");
        myModal.find(".close").show();
        if( $("#modalCancel").length ){
            $("#modalCancel").remove();
        }
        modalBtn.removeClass('hasNewHandler');
    });

    //二维码缩略图点击显示大图(事件委派)
    $(document).on("click",".thumbnail img",function(){
        var $this=$(this);
        var bigImageWrap=$this.parent(".thumbnail").next(".bigImageWrap");
        bigImageWrap.show();
        bigImageWrap.one("click",function(){
            $(this).hide();
        });
    });
    



    var myModal=$("#myModal");
    //编辑项目
    //判断有无manage_wrap（间接判断权限）
    if( $(".manage_wrap").length ){
    //查看项目
        var manage_wrap2=$("#collapseTwo .manage_wrap");
        var cancelBtn2=manage_wrap2.find(".glyphicon-remove");
        var manageBtn2=manage_wrap2.find(".glyphicon-cog");
        var nextRow2=manage_wrap2.next(".row");
        manageBtn2.on("click",function(){
            var $this=$(this);
            if($this.hasClass('glyphicon-cog')){
                $this.removeClass('glyphicon-cog').addClass('glyphicon-ok');
                cancelBtn2.show();
                //编辑状态，点击不会跳转页面
                nextRow2.addClass("managing");
                //备份项目清单，以便取消操作恢复清单
                nextRow2.data("lists",nextRow2.html());
            }else{
                // if( nextRow2.data("delLists") && nextRow2.data("delLists").length>0 ){
                    $(".modal-body>.modal-main").html("确定删除并提交");
                    //为模态框增加取消按钮
                    var modalCancelBtn=$('<button type="button" id="modalCancel" class="btn btn-default" data-dismiss="modal">取消</button>');
                    modalCancelBtn.one("click",function(){
                        myModal.find(".close").triggerHandler('click');
                        cancelBtn2.triggerHandler('click');
                    });
                    myModal.find(".modal-footer").prepend(modalCancelBtn);
                    myModal.find(".close").hide();
                    myModal.modal("show"); 
                    modalBtn.addClass('hasNewHandler').one("click",function(){
                        var $this=$(this);
                        if($this.hasClass('hasNewHandler')){
                            $.ajax({
                                url: '',
                                type: 'POST',
                                data: {sign: 'delLists',data:nextRow2.data("delLists")}
                            })
                            .done(function(data) {
                                data=JSON.parse(data);
                                if(data===1){
                                    //模态框提示
                                    $(".modal-body>.modal-main").html("发布成功");
                                    myModal.modal("show"); 
                                    location.reload(true);
                                    // //删除保存的被删除项目数组
                                    // nextRow2.removeData("delLists");
                                    // //恢复初始状态
                                    // $this.removeClass('glyphicon-ok').addClass('glyphicon-cog');
                                    // cancelBtn.hide();
                                    // nextRow2.removeClass("managing");
                                }else{
                                    //模态框提示
                                    $(".modal-body>.modal-main").html("发布失败");
                                    myModal.modal("show"); 
                                }

                            })
                            .fail(function() {
                                //模态框提示
                                $(".modal-body>.modal-main").html("连接失败");
                                myModal.modal("show"); 
                            });
                        }
                    });
                    
                // }
            }
            
        });
        //取消删除项目按钮事件
        cancelBtn2.on("click",function(){
            var $this=$(this);
            if(manage_wrap2.hasClass('hasDel')){
                nextRow2.html(nextRow2.data("lists"));
            }
            nextRow2.removeData('lists');
            nextRow2.removeClass("managing");
            manage_wrap2.removeClass('hasDel');
            //恢复初始显示状态
            cancelBtn2.hide();
            manageBtn2.removeClass('glyphicon-ok').addClass('glyphicon-cog');
        });

        //删除项目按钮事件(事件委派)
        $(document).on("click",".removeBtn",function(){
            var $this=$(this);
            var pItemWrap=$this.closest('.pItemWrap');
            var projectName=$this.siblings('.projectName');
            //保存删除项目的id
            var id=projectName.data("id");
            if( !nextRow2.data("delLists") ){
                nextRow2.data("delLists",[]);
            }
            nextRow2.data("delLists").push(id);
            pItemWrap.remove();
            manage_wrap2.addClass('hasDel');
        });

    //项目回收
        var manage_wrap3=$("#collapseThree .manage_wrap");
        var cancelBtn3=manage_wrap3.find(".glyphicon-remove");
        var commitBtn3=manage_wrap3.find(".glyphicon-ok");
        var nextRow3=manage_wrap3.next(".row");
        var captions3=nextRow3.find(".caption");
        //点击项目下方显示选中图标
        captions3.on("click",function(){
            var $this=$(this);
            var projectName=$this.find('.projectName');
            var id=projectName.data("id");
            if($this.hasClass('chose')){
                //取消选中
                $this.removeClass('chose');
                //删除保存数组中的id
                var revertLists=nextRow3.data("revertLists");
                var index=revertLists.indexOf(id);
                revertLists.splice(index,1);
                //若数组长度为0，则移动data和hasDel类
                if(revertLists.length===0){
                    nextRow3.removeData("revertLists");
                    manage_wrap3.removeClass('hasDel');
                }
            }else{
                $this.addClass('chose');
                //保存要恢复的项目id
                if( !nextRow3.data("revertLists") ){
                    nextRow3.data("revertLists",[]);
                }
                nextRow3.data("revertLists").push(id);
                manage_wrap3.addClass('hasDel');
            }
        });
        //取消按钮
        cancelBtn3.on("click",function(){
            nextRow3.find(".chose").removeClass('chose');
            nextRow3.removeData("revertLists");
            manage_wrap3.removeClass('hasDel');
        });

        //提交按钮
        commitBtn3.on("click",function(){
            var myModal=$('#myModal');
            $(".modal-body>.modal-main").html("确定恢复并提交");
            //为模态框增加取消按钮
            var modalCancelBtn=$('<button type="button" id="modalCancel" class="btn btn-default" data-dismiss="modal">取消</button>');
            modalCancelBtn.one("click",function(){
                myModal.find(".close").triggerHandler('click');
                // cancelBtn3.triggerHandler('click');
            });
            myModal.find(".modal-footer").prepend(modalCancelBtn);
            myModal.find(".close").hide();
            myModal.modal("show"); 
            $("#modalBtn").one("click",function(){
                $.ajax({
                    url: '',
                    type: 'POST',
                    data: {sign: 'recovery',data:nextRow3.data("revertLists")}
                })
                .done(function(data) {
                    data=JSON.parse(data);
                    //模态框提示
                    if(data===1){
                        $(".modal-body>.modal-main").html("发布成功");
                        myModal.modal("show"); 
                        location.reload(true);
                    }else{
                        $(".modal-body>.modal-main").html("发布失败");
                        myModal.modal("show"); 
                    }
                })
                .fail(function() {
                    $(".modal-body>.modal-main").html("连接失败");
                    myModal.modal("show"); 
                });
                
            });
        });
        //面板收起时，移除编辑状态
        $('#collapseTwo,#collapseThree').on('hidden.bs.collapse', function () {
            $(this).find(".manage_wrap>.glyphicon-remove").triggerHandler('click');
        });


    }

}
/*项目管理pm END*/

/*项目详情页面*/
function pd_itemJs() {
    $("#topTitle").text("项目详情");
    /*加载后台数据*/
    $.ajax({
        url: '',
        type: 'POST',
        data: {sign: 'loadData'}
    })
    .done(function(data) {
        data=JSON.parse(data);
        var addStr = '<tr><th rowspan="2"><span class="glyphicon glyphicon-remove"></span><div class="btn-group dropup"><button type="button" class="btn btn-default"><span class="picked">职位</span><span class="caret"></span></button><ul class="dropdown-menu scrollable" style="min-width:60px"><li class="departmentItems"><ul></ul></li><li class="jobItems"><ul></ul></li></ul></div></th><th>人员<br>任务</th><td><input type="text" class="txtPopup" readonly></td></tr><tr><th>提成</th><td><input type="text" name="percent" class="moneyRatio" readonly></td></tr>';
        var addDom,manInput;
        var table=$("#collapseTwo").find("table");
        for(var i in data){
            addDom=$(addStr);
            manInput=addDom.find(".txtPopup");
            addDom.find(".picked").text(data[i].j_name).data("id",data[i].id);
            addDom.find("input[name=percent]")[0].defaultValue=data[i].percent;    
            manInput.data("menDetails",data[i].man);//将人员数据保存在input元素上
            data[i].man.forEach(function(item,index,arr){
                if(manInput[0].defaultValue){
                    manInput[0].defaultValue+="&"+item.nickname;
                }else{
                    manInput[0].defaultValue=item.nickname;
                }
                // manInput[0].defaultValue ? manInput[0].defaultValue+="&"+item.nickname : manInput[0].defaultValue=item.nickname;
            });
            table.append(addDom);
        }
    })
    .fail(function() {
        console.log("error");
    });
    /*加载后台数据END*/

    //保存删除的行信息（职位id）
    var removeTrArr=[];
    $("#collapseTwo").on("click","th>.glyphicon-remove",function(){
        var $this=$(this);
        var parentTr=$this.closest('tr');
        var nextTr=parentTr.next();
        var obj={};
        if( !parentTr.hasClass('newTr') ){
            obj.id=parentTr.find(".picked").data("id");
            obj.sign="delJob";
            removeTrArr.push(obj);
            !$("#collapseTwo").data("removeTrs") && $("#collapseTwo").data("removeTrs",removeTrArr);
        }
        parentTr.add(nextTr).remove();
    });

    /*初始化模态框*/
    $('#myModal').modal({
        backdrop: "static",
        show: false
    });
    /*初始化模态框 END*/

    //日期选择
    var currYear = (new Date()).getFullYear();
    var opt = {};
    opt.date = {
        preset: 'date'
    };
    opt.datetime = {
        preset: 'datetime'
    };
    opt.time = {
        preset: 'time'
    };
    opt.default = {
        theme: 'android-ics light', //皮肤样式
        display: 'modal', //显示方式 
        mode: 'scroller', //日期选择模式
        dateFormat: 'yyyy-mm-dd',
        lang: 'zh',
        showNow: true,
        nowText: "今天",
        startYear: currYear - 10, //开始年份
        endYear: currYear + 10, //结束年份
    };

    $("input[name=contractDate]", "#collapseOne").mobiscroll($.extend(opt['date'], opt['default']));
    //日期选择 END

    /*下拉列表(一级选择)模仿select 单选*/
    $(".pickMenu").find("li").on("click", function() {
        var $this = $(this);
        $this.closest('.pickMenu').prevAll("button").find(".picked").text($this.text());
        var forInput = $this.parent().parent().prev();
        if (forInput.attr("name") === "status") {
            forInput.removeAttr('readonly').val($this.text()).attr("readonly", true);
        }

    });
    /*下拉列表(一级选择)模仿select END*/

    /*管理员编辑按钮*/
    /*验证权限*/
    // $.ajax({
    //         url: '',
    //         type: 'POST',
    //         data: requestData(form,{sign:"updateItem",days:inputs.filter("[name=days]").val()+$("#dateUnit").text()})
    //     })
    //     .done(function(data) {
    //         data = JSON.parse(data);
    //         if (data === 0) {
    //             $(".modal-body>.modal-main").html("发布失败");
    //         } else if (data > 0) {
    //             $this.removeClass('glyphicon-ok').addClass('glyphicon-cog');
    //             $(".modal-body>.modal-main").html("发布成功");
    //             parentPanel.css("border-color", "#ddd");
    //         }
    //         $("#myModal").modal("show");
    //     })
    //     .fail(function() {
    //         console.log("error");
    //         $(".modal-body>.modal-main").html("连接失败");
    //         $("#myModal").modal("show");
    //     });
    var authority = true;
    if (authority === true) {
        var panel_title = $("#collapseOne .panel-title,#collapseTwo .panel-title");
        var manageBtn = $('<span class="mzm-manage-btn pull-right glyphicon glyphicon-cog"></span>');
        var cancelBtn = $('<span class="mzm-manage-btn pull-right glyphicon glyphicon-remove"></span>');
        var addBtn = $('<span class="mzm-manage-btn pull-right glyphicon glyphicon-plus"></span>');
        //为按钮绑定事件
        manageBtn.on("click", function() {
            var $this = $(this);
            var parentPanel = $this.closest('.panel');
            var form = parentPanel.find("form");
            var siblingPanel = parentPanel.siblings('.panel');
            var inputs = parentPanel.find("input").not("[disabled]");
            if ($this.hasClass('glyphicon-cog')) {
                //只能同时激活一个编辑模块
                if (siblingPanel.hasClass('activeInput')) {
                    return;
                }
                if(parentPanel.attr("id")==="collapseTwo"){
                    parentPanel.data("trsClone",form.find("table").find("tr").clone(true));
                }else{
                    //修复日期选择器bug
                    var datePicker=parentPanel.find(".datePicker");
                    datePicker.attr("placeholder",datePicker.val()).val("");
                }
                //通过增加class标示为激活状态
                parentPanel.addClass("activeInput");
                $this.removeClass('glyphicon-cog').addClass('glyphicon-ok');
                inputs.each(function(index, el) {
                    var $this = $(el);
                    var val = $this.val();
                    //编辑状态文本输入框可输入
                    if ($this.hasClass('txtPopup') || $this.attr("name") === "status" || $this.attr("name") === "contractDate") {
                        $this.data("editable", true);
                    } else {
                        $this.removeAttr('readonly');
                    }
                    //编辑状态文本输入框可输入 END
                    if ($this.parent().hasClass('pd_hasDropDown')) {
                        $this.parent().addClass('in');
                        if ($this.attr("name") === "days") {
                            var lastStr = val.charAt(val.length - 1);
                            $this.val(parseInt(val));
                            $("#dateUnit").text(lastStr);
                        } else if ($this.attr("name") === "status") {
                            $("#status").text(val);
                        }
                    }
                    //  else if ($this.is("[disabled]")) {
                    //     $this.parent().prev().css("color", "#888");
                    // }
                });
                

            } else if ( $this.hasClass('glyphicon-ok') ) {
                var validate = true;
                if (parentPanel.attr("id") === "collapseOne") {
                    inputs.each(function(index, el) {
                        var $this = $(el);
                        var val = $.trim($this.val());
                        var name = $this.attr("name");
                        /*验证*/
                        if (name === "i_id" || name === "status" || name === "requirement") {
                            if (!/^[\u4e00-\u9fa5A-z0-9]+$/.test(val)) {
                                validate = false;
                                $this.data("verify", false);
                            }
                        } else if (name === "money") {
                            if (!/^\d+$/.test(val)) {
                                validate = false;
                                $this.data("verify", false);
                            }
                        } else if (name === "contractDate") {
                            //修正日期选择器bug
                            if(!el.value){
                                el.value=el.defaultValue;
                            }
                            // if (!/\d{4}-[01]\d-\d{2}/.test(val)) {
                            //     validate = false;
                            //     $this.data("verify", false);
                            // }
                        } else if (name === "days") {
                            if (!/^\d{1,2}$/.test(val)) {
                                validate = false;
                                $this.data("verify", false);
                            }
                        }
                        if ($this.data("verify") === false) {
                            $this.parent().prev().addClass('text-danger');
                            parentPanel.addClass("hasError");
                            $this.on("focus",function(){
                                $this.parent().prev().removeClass('text-danger');
                                parentPanel.removeClass("hasError");
                                $this.off("focus");
                            });
                            $this.removeData("verify");
                        } else {
                            $this.parent().prev().removeClass('text-danger');
                        }

                    });
                    if (validate === true) {
                        var hasChangeItem;
                        inputs.each(function(index,el){
                            var $this=$(el);
                            if($this.attr("name")==="days"){
                                if(el.value+$this.next().find(".picked").text()!==el.defaultValue){
                                    hasChangeItem=true;
                                }
                            }else{
                                if(el.value!==el.defaultValue){
                                    hasChangeItem=true;
                                }
                            }
                        });
                        if(hasChangeItem){
                            $.ajax({
                                    url: '',
                                    type: 'POST',
                                    data: requestData(form, { sign: "updateItem", days: inputs.filter("[name=days]").val() + $("#dateUnit").text() })
                                })
                                .done(function(data) {
                                    data = JSON.parse(data);
                                    // var id=data.id;
                                    if (data === 0) {
                                        $(".modal-body>.modal-main").html("发布失败");
                                    } else if (data === 1) {
                                        $(".modal-body>.modal-main").html("发布成功");
                                        $this.removeClass('glyphicon-ok').addClass('glyphicon-cog');
                                        parentPanel.removeClass('activeInput');
                                        inputs.each(function(index, el) {
                                            var $this = $(el);
                                            $this.attr("readonly", "true").parent().removeClass('in');
                                        });
                                        //提交成功后更新表单默认值    
                                        parentPanel.find("input:not(:disabled)").each(function(index, el) {
                                            el.defaultValue=el.value;
                                        });
                                    }
                                    $("#myModal").modal("show");
                                })
                                .fail(function() {
                                    console.log("error");
                                    $(".modal-body>.modal-main").html("连接失败");
                                    $("#myModal").modal("show");
                                });
                        }
                    }
                } else if (parentPanel.attr("id") === "collapseTwo") {
                    //验证有无未填或提成输入框是否为数字
                    var invalidInputs=parentPanel.find("input").filter(function(){
                        var $this=$(this);
                        if( $this.hasClass('moneyRatio') && !/^\d{1,2}(\.\d+)?$/.test($this.val()) ){
                            return true;
                        }
                        return !$this.val();
                    });
                    if(invalidInputs.length){
                        invalidInputs.each(function(index, el) {
                            var _$this=$(this);
                            _$this.parent().prev().addClass('text-danger');
                            parentPanel.addClass('hasError');
                            _$this.on("focus",function(){
                                _$this.parent().prev().removeClass('text-danger');
                                parentPanel.removeClass('hasError');
                                _$this.off("focus");
                            });
                        });
                        return;
                    }

                    //只提交修改过的职位行
                    var popups=parentPanel.find(".txtPopup").filter(function(){
                        return !! $(this).data("menDetails2");
                    });
                    var dataArr=[];
                    var parentTrs=popups.closest('tr');
                    parentTrs.each(function(index, el) {
                        var $this=$(el);
                        var obj={ id:$this.find(".picked").data("id"),
                                  percent:$this.next().find(".moneyRatio").val(),
                                  man:$this.find(".txtPopup").data("menDetails2")
                        };
                        dataArr.push(obj);
                    });

                        //只修改提成,则只提交 提成 和 职位id
                    var ratioFixs=parentPanel.find(".moneyRatio").filter(function(){
                       return this.value!==this.defaultValue && !$(this).closest('tr').prev().find(".txtPopup").data("menDetails2"); 
                    });
                    ratioFixs.closest("tr").each(function(index, el) {
                        var $this=$(el);
                        var obj={id:$this.prev().find(".picked").data("id"),
                                 percent:$this.find(".moneyRatio").val()
                        };
                        dataArr.push(obj);
                    });

                    //删除的行
                    var removeTrs=$("#collapseTwo").data("removeTrs");
                    if( removeTrs && removeTrs.length ){
                        removeTrs.forEach(function(item,index,array){
                            dataArr.push(item);
                        });
                    }
                    // console.log(dataArr);
                    if(dataArr.length){
                        $.ajax({
                            url: '',
                            type: 'POST',
                            data: {sign:"update",data: dataArr}
                        })
                        .done(function(data) {
                            data = JSON.parse(data);
                            if (data === 0) {
                                $(".modal-body>.modal-main").html("发布失败");
                                $("#myModal").modal("show");
                            } else if (data === 1) {
                                $(".modal-body>.modal-main").html("发布成功");
                                $("#myModal").modal("show");
                                //更新成功后更新表单默认值
                                parentPanel.find(".txtPopup").filter(function(){
                                    return !!$(this).data("menDetails2");
                                }).each(function(index, el) {
                                    var $this=$(el);
                                    $this.data( "menDetails" , $this.data("menDetails2") );
                                    $this.removeData('menDetails2');
                                    el.defaultValue=el.value;
                                });
                                parentPanel.find(".moneyRatio").each(function(index, el) {
                                    el.defaultValue=el.value;
                                    $(el).attr("readonly",true);
                                });
                                parentPanel.find(".newTr").removeClass('newTr');
                                //重置编辑按钮
                                $this.removeClass('glyphicon-ok').addClass('glyphicon-cog');
                                parentPanel.removeClass('activeInput');
                                parentPanel.removeData();
                                removeTrArr=[];
                            }
                        })
                        .fail(function() {
                            console.log("error");
                            $(".modal-body>.modal-main").html("连接失败");
                            $("#myModal").modal("show");
                        });
                    }
                }
            }
        });

        cancelBtn.on("click", function() {
            var $this = $(this);
            var parentPanel = $this.closest('.panel');
            var form = parentPanel.find("form");
            var inputs = parentPanel.find("input");
            $this.siblings('.glyphicon-ok').removeClass('glyphicon-ok').addClass('glyphicon-cog');
            parentPanel.removeClass('activeInput hasError');
            if(parentPanel.attr("id")==="collapseTwo"){
                //点击取消，恢复原状
                form.find("table").empty().append( parentPanel.data("trsClone") );
                parentPanel.removeData();
                return;
            }
            inputs.each(function(index, el) {
                var $this = $(el);
                $this.attr("readonly", "true").parent().removeClass('in');
                $this.removeData('editable');
            });
            form[0].reset();
        });

        addBtn.on("click", function() {
            var $this = $(this);
            var parentPanel = $this.closest('.panel');
            var addAble=true;
            //当新增列表未填时，不能再新增
            parentPanel.find(".txtPopup").each(function(index, el) {
                if( !$(el).val() ){
                    addAble=false;
                }
            });
            if(addAble){
                var addStr = '<tr class="newTr"><th rowspan="2"><span class="glyphicon glyphicon-remove"></span><div class="btn-group dropup"><button type="button" class="btn btn-default noJob"><span class="picked">职位</span><span class="caret"></span></button><ul class="dropdown-menu scrollable" style="min-width:60px"><li class="departmentItems"><ul></ul></li><li class="jobItems"><ul></ul></li></ul></div></th><th>人员<br>任务</th><td><input type="text" class="txtPopup" readonly></td></tr><tr><th>提成</th><td><input type="text" name="percent" class="moneyRatio"></td></tr>';
                parentPanel.find("table").append($(addStr));
                //新增行滚动到可视区域
                parentPanel.find(".newTr")[0].scrollIntoView();
            }
        });

        panel_title.append(manageBtn);
        panel_title.append(cancelBtn);
        $("#collapseTwo .panel-title").append(addBtn);

    }
    /*验证权限 END*/

    /*有弹窗单元格*/
    $(document).on("click", ".txtPopup", function() {
        var $this = $(this);
        var jobChosen=$this.closest('tr').find("th:first-child .picked");
        var inputVal = $this.val();
        var inputData=$this.data("menDetails2")?$this.data("menDetails2"):$this.data("menDetails");
        var parentPanel = $this.closest('.panel');
        var modalBody = $(".modal-body>.modal-main");
        $(".modal_title").text($this.parent().prev("th").text());
        $("#myModal").data({ "inputObj": $this, "targetPanel": parentPanel });
        /*基本信息类*/
        if (parentPanel.attr("id") === "collapseOne") {
            var textarea = $('<textarea class="scrollable"></textarea>');
            textarea.val($this.val());
            if (!$this.data("editable")) {
                textarea.attr("readonly", "true");
            }else{
                $("#modalBtn").one("click",function(){
                    $this.val( modalBody.find("textarea").val() );
                });
            }
            modalBody.empty().append(textarea);

        }
        /*基本信息类 END*/

        /*人员任务分配类*/
        else if (parentPanel.attr("id") === "collapseTwo") {
            $(".modal-dialog").addClass('fullScreen-modal-dialog');
            modalBody.empty();
            /*任务分配详情*/
            if (false) {
            }
            /*任务分配详情 END*/

            /*人员列表*/
            else {
                var changeView = $('<span class="mzm-changeView-btn pull-right glyphicon glyphicon-th-list"></span>');
                var manWrap = $('<span class="mzm-man-wrap"></span>');
                var manIcon = $('<span class="mzm-man-icon glyphicon glyphicon-remove"></span>');
                changeView.on("click", function(e) {
                    var $this = $(this);
                    if ($this.hasClass('glyphicon-th-list')) {
                        $this.removeClass('glyphicon-th-list').addClass('glyphicon-user');
                        var panel = $('<form class="panel panel-default" ><div class="panel-heading clearfix"><input type="text" class="man-title pull-left form-control" name="nickname"><input type="hidden" class="man-title pull-left form-control" name="id"><div class="input-group pull-right"><span class="input-group-addon">提成</span><input type="text" class="form-control" name="percent"></div></div><textarea class="scrollable" name="duty"></textarea></form>');
                        var man_wraps = modalBody.find(".mzm-man-wrap");
                        man_wraps=man_wraps.clone(true);//防止modalBody清空后遗失数据
                        modalBody.empty();
                        man_wraps.each(function(index, el) {
                            var $this=$(el);
                            var manDetails=$this.data("manDetails");
                            var panelClone=panel.clone(true);
                            panelClone.find("input[name=nickname]").val(manDetails.nickname);
                            panelClone.find("input[name=id]").val(manDetails.id);
                            panelClone.find("input[name=percent]").val(manDetails.percent);
                            panelClone.find("textarea[name=duty]").val(manDetails.duty);
                            if( parentPanel.hasClass("activeInput") ){
                                panelClone.find("input:not([name=percent])").attr("readonly",true);
                            }else{
                                panelClone.find("input,textarea").attr("readonly",true);
                            }
                            panelClone.appendTo(modalBody);
                        });
                        $("#myModal").data("listView","details");
                    } else {
                        $this.addClass('glyphicon-th-list').removeClass('glyphicon-user');
                        var forms=modalBody.find("form");
                        var arr=[];
                        forms.each(function(index,el){
                            arr.push(requestData($(el)));
                        });
                        toManItems(arr);
                        $("#myModal").removeData("listView");
                    }
                });
                changeView.insertAfter(".modal-header>button");
                // $(".modal-header").append(changeView);
                // var addBtnStr='<div class="input-group-btn dropup modalAddBtn" style="width:70px;float:left"><button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" style="min-width: 66px;"><span class="btnTitle"></span><span class="caret"></span></button>'+'<ul class="dropdown-menu pull-right scrollable" style="min-width:60px"><li><a href="javascript:;">未审核</a></li><li><a href="javascript:;">审核</a></li><li><a href="javascript:;">分析</a></li><li><a href="javascript:;">组队</a></li><li><a href="javascript:;">开发</a></li><li><a href="javascript:;">完成</a></li>';

                var toManItems = function(manArr) {
                    modalBody.empty();
                    var nth = 1;
                    manArr.forEach(function(item, index, array) {
                        manWrap.clone(true).data("manDetails",item).text(item.nickname).append(manIcon.clone(true)).appendTo(modalBody);
                        if (nth % 3 === 0) {
                            modalBody.append($("<br/>"));
                        }
                        nth++;
                    });
                };

                if (inputData && inputData.length) {
                    toManItems(inputData);
                }


                //判断是否处于编辑状态
                if (parentPanel.hasClass("activeInput")) {
                    var editBtn = $('<button type="button" class="btn btn-default modalAddBtn pull-left" id="modalEditable">编辑</button>');
                    var addBtnStr = '<div class="btn-group dropup modalAddBtn" id="modalAddMore" style="width:70px;float:left"><button type="button" class="btn btn-default dropdown-toggle" style="min-width: 66px;z-index:999"><span class="btnTitle">添加</span><span class="caret"></span></button>' + '<ul class="dropdown-menu pull-right scrollable" style="min-width:60px;min-height:36px"></ul></div>';
                    // var addBtnStr = '<div class="btn-group dropup modalAddBtn" id="modalAddMore" style="width:70px;float:left"><button type="button" class="btn btn-default dropdown-toggle" style="min-width: 66px;"><span class="btnTitle">添加</span><span class="caret"></span></button>' + '<ul class="dropdown-menu pull-right scrollable" style="min-width:60px"><li class="department"></li><li class="job"></li><li class="staff"></li></ul></div>';

                    var addBtn = $(addBtnStr);
                    addBtn.on("click", "li", function(e) {
                        e.stopPropagation();
                        var $this = $(this);
                        // var manIds=[];
                        // var modalBody=$(".modal-body>.modal-main");
                        if (!$this.hasClass('selSort')) {
                            // if( $("#myModal").data("listView") ){
                            //     modalBody.find("input[name=id]").each(function(index, el) {
                            //         manIds.push($(el).val());
                            //     });
                            // }else{
                            //     modalBody.find(".mzm-man-wrap").each(function(index, el) {
                            //         manIds.push( $(el).data("manDetails").id );
                            //     });
                            // }
                            // if( manIds.indexOf( $this.data("manDetails").id ) ===-1 ){
                            //     $this.addClass('man-chose');
                            //     $("#modalAddMore>button").data("isChose", true);
                            // }

                            $this.addClass('man-chose');
                            $("#modalAddMore>button").data("isChose", true);
                        }


                    });

                    /*添加按钮*/
                    addBtn.on("click", "button", function() {
                        var $this = $(this);
                        var modalBody=$(".modal-body>.modal-main");
                        //点击添加按钮，移除编辑痕迹
                        if( !$("#myModal").data("listView") ){
                            modalBody.find(".man-editable").removeClass('man-editable').end().find(".emptyCaret").remove();
                        }
                        /*加载部门*/
                        if (!$this.data("isClicked")) {
                            var dropdown_mask = $('<div class="mzm-dropdown-mask" style="background-color:red"></div>');
                            dropdown_mask.on("click",function(){
                                $(this).prevAll("button").removeData("isClicked");
                                $(this).nextAll("ul").hide().empty().end().remove();
                            });
                            dropdown_mask.insertAfter($this);
                            $this.nextAll(".dropdown-menu").show();

                            //确认选择了职位才能请求员工数据
                            if( jobChosen.data("id") ){
                                var domStr = "";
                                $.ajax({
                                        url: '',
                                        type: 'POST',
                                        data: { id:jobChosen.data("id"),sign: "users" }
                                    })
                                    .done(function(data) {
                                        data = JSON.parse(data);
                                        var length = data.length;
                                        var domStr,newDom;

                                        var manIds=[];
                                        
                                        if( $("#myModal").data("listView") ){
                                            modalBody.find("input[name=id]").each(function(index, el) {
                                                manIds.push($(el).val());
                                            });
                                        }else{
                                            modalBody.find(".mzm-man-wrap").each(function(index, el) {
                                                manIds.push( $(el).data("manDetails").id );
                                            });
                                        }
                                        // if( manIds.indexOf( $this.data("manDetails").id ) ===-1 ){
                                        //     $this.addClass('man-chose');
                                        //     $("#modalAddMore>button").data("isChose", true);
                                        // }


                                        for (var i = 0; i < length; i++) {
                                            if( manIds.indexOf(data[i].id) !==-1 ){
                                                domStr = '<li class="existingId">' + data[i].nickname + '</li>';
                                            }else{
                                                domStr = '<li>' + data[i].nickname + '</li>';
                                            }
                                            if(newDom){
                                                newDom=newDom.add( $(domStr).data("manDetails",data[i]) );
                                            }else{
                                                newDom=$(domStr).data("manDetails",data[i]);
                                            }
                                        }
                                        console.log(newDom);
                                        console.log("success");
                                        $this.nextAll("ul").empty().append(newDom);
                                    })
                                    .fail(function() {
                                        console.log("error");
                                    });
                            }
                            $this.data("isClicked", true);
                        }
                        /*加载部门 END*/
                        /*添加人员*/
                        if ($this.data("isChose") === true) {
                            var modalBody = $(".modal-body>.modal-main");
                            var choseMen = $("#modalAddMore").find(".man-chose");
                            var menNum = choseMen.length;
                            if( $("#myModal").data("listView") ){
                                var panel = $('<form class="panel panel-default" ><div class="panel-heading clearfix"><input type="text" class="man-title pull-left form-control" name="nickname"><input type="hidden" class="man-title pull-left form-control" name="id"><div class="input-group pull-right"><span class="input-group-addon">提成</span><input type="text" class="form-control" name="percent"></div></div><textarea class="scrollable" name="duty"></textarea></form>');
                                choseMen.each(function(index, el) {
                                    var data=choseMen.eq(index).data("manDetails");
                                    panel.clone(true).find("input[name=nickname]").val( data.nickname ).end().find("input[name=id]").val( data.id ).end().appendTo(modalBody);
                                });
                                panel=null;
                            }else{
                                for (var j = 0; j < menNum; j++) {
                                    if (!modalBody.data("brIndex")) {
                                        modalBody.data("brIndex", $("modal-body").find("br").last().nextAll(".mzm-man-wrap").length + 1);
                                    }
                                    var addManStr = '<span class="mzm-man-wrap">' + choseMen.eq(j).text() + '<span class="mzm-man-icon glyphicon glyphicon-remove"></span></span>';
                                    if (modalBody.data("brIndex") % 3 === 0) {
                                        addManStr += "<br>";
                                    }
                                    $(addManStr).data("manDetails",choseMen.eq(j).data("manDetails")).appendTo('.modal-body>.modal-main');
                                    if (modalBody.data("brIndex")) {
                                        modalBody.data("brIndex", modalBody.data("brIndex") + 1);
                                    }
                                }
                                modalBody.removeData('brIndex');
                            }

                            $this.siblings(".dropdown-menu").hide().empty();
                            $this.siblings('.mzm-dropdown-mask').remove();
                            $this.removeData();
                        }

                    }).prependTo('.modal-footer');



                    editBtn.on("click", function() {
                        modalBody.find(".mzm-man-wrap").addClass('man-editable');
                    }).prependTo('.modal-footer');

                }
            }
            /*人员列表 END*/
        }
        /*人员任务分配类 END*/
        $("#myModal").modal("show");
    });
    /*有弹窗单元格 END*/

    //选择职位，先选部门
    $("#collapseTwo").on("click", "button", function() {
        var $this = $(this);
        var dropdown_mask = $('<div class="mzm-dropdown-mask"></div>');

        dropdown_mask.on("click", function() {
            $(this).nextAll("ul").hide().end().remove();
            //下拉选择失去“焦点”时，清空下拉列表
            $this.nextAll("ul").find(".departmentItems,.jobItems").empty();
        });
        dropdown_mask.insertAfter($this);
        $this.nextAll("ul").show().children('li').empty();
        //请求部门数据
        var ul=$("<ul></ul>");
        $.ajax({
                url: '',
                type: 'POST',
                data: { sign: "department" }
            })
            .done(function(data) {
                data = JSON.parse(data);
                var length = data.length;
                var selSort=$('<li class="selSort">部门</li>');
                var domStr="";
                for (var i = 0; i < length; i++) {
                    domStr += '<li data-id="' + data[i].id + '">' + data[i].d_name + '</li>';
                }
                var newDom=$(domStr);
                newDom.filter("li").on("click",function(){
                    var $this=$(this);
                    $.ajax({
                            url: '',
                            type: 'POST',
                            data: { sign: "jobs", id: $this.data("id") }
                        })
                        .done(function(data) {
                            data = JSON.parse(data);
                            var length = data.length;
                            var ul=$("<ul></ul>");
                            var selSort=$('<li class="selSort backward">职位</li>');
                            selSort.on("click",function(){
                                $(this).closest('.jobItems').hide().siblings(".departmentItems").show();
                            });
                            var domStr="";
                            //不能选择已选部门
                            var existingIds=[]; 
                            $("#collapseTwo").find(".picked").filter(function(){
                                return !$(this).closest('tr').hasClass('newTr');
                            }).each(function(index,el){
                                existingIds.push($(el).data("id")+"");
                            });
                            for (var i = 0; i < length; i++) {
                                if(existingIds.indexOf(data[i].id)!=-1){
                                    domStr += '<li class="existingId" data-id="' + data[i].id + '">' + data[i].j_name + '</li>';
                                }else{
                                    domStr += '<li data-id="' + data[i].id + '">' + data[i].j_name + '</li>';
                                }
                            }
                            var newDom=$(domStr);
                            newDom.filter("li:not(.existingId)").on("click",function(){
                                var $this=$(this);
                                var id=$this.data("id");
                                // var hasSame;
                                // $("#collapseTwo").find(".picked").each(function(index,el){
                                //     if( $(el).data("id") ==id ){
                                //         hasSame=true;
                                //     }
                                // });
                                // if(!hasSame){
                                    var dropdownMenu=$this.closest('.dropdown-menu');
                                    dropdownMenu.hide().prevAll(".mzm-dropdown-mask").remove();
                                    dropdownMenu.prevAll("button").removeClass('noJob').find(".picked").text($this.text()).data("id",$this.data("id"));
                                // }
                            });
                            ul.append(selSort);
                            ul.append(newDom);
                            $this.closest('.departmentItems').hide().siblings(".jobItems").empty().append(ul).show();
                        })
                        .fail(function() {
                            console.log("error");
                        });
                });
                ul.append(selSort);
                ul.append(newDom);
                $this.nextAll("ul").find(".departmentItems").empty().append(ul).show();
            })
            .fail(function() {
                console.log("error");
            });
        //请求

    });

    

    /*模态框确认按钮*/
    $("#modalBtn").on("click", function() {
        var myModal = $("#myModal");
        //排除提交状态提示模态框
        if( !myModal.data("targetPanel") || !myModal.data("targetPanel").hasClass('activeInput') ){
            return;
        }
        var modalBody = $(".modal-body>.modal-main");
        var result = "";
        if (myModal.data("targetPanel").attr("id") === "collapseOne") {
            var textarea = modalBody.find("textarea");
            if (textarea.length) {
                result = textarea.val();
            }
        } else if (myModal.data("targetPanel").attr("id") === "collapseTwo") {
            var arr=[],nicknameArr=[],man_wraps;
            if(myModal.data("listView")==="details"){
                man_wraps=modalBody.find("form");
                man_wraps.each(function(index,el){
                    arr.push(requestData($(el)));
                });
            }else{
                man_wraps=modalBody.find(".mzm-man-wrap");
                man_wraps.each(function(index,el){
                    arr.push( $(el).data("manDetails") );
                });
            }
            arr.forEach(function(item,index,array){
                nicknameArr.push(item.nickname);
            });
            //menDetails2用于ajax，menDetails用于取消重置
            myModal.data('inputObj').data("menDetails2",arr).val(nicknameArr.join("&"));
        }
        // myModal.data("inputObj").val(result);
        // myModal.data("inputObj").data("menDetails",);
        myModal.removeData('inputObj').removeData('targetPanel');
    });
    //关闭模态框后重置模态框
    $('#myModal').on('hide.bs.modal', function() {
        $(".modalAddBtn") && $(".modalAddBtn").remove();
        $(".mzm-changeView-btn") && $(".mzm-changeView-btn").remove();
        $(".modal-dialog").removeClass('fullScreen-modal-dialog');
        /*重置view mode*/
        var listOrUser=$(".modal-header").find(".glyphicon-th-list");
        listOrUser && listOrUser.removeClass('glyphicon-th-list').addClass('glyphicon-user');
        $("#myModal").removeData("listView").find(".modal_title").text("").end().find(".modal-main").html("");
        //清除modal标题和modal-body内容


    });
    /*模态框确认按钮END*/

    


    /*人员删除*/
    $(document).on("click", ".mzm-man-icon", function() {
        var $this = $(this);
        var parent = $this.parent();
        if (parent.hasClass('man-editable')) {
            var siblingsArr = parent.prevUntil("br").add(parent.nextUntil("br")).add(parent);
            var afterBr = siblingsArr.last().next();
            siblingsArr2 = siblingsArr.add(afterBr);
            if (siblingsArr2.filter(".emptyCaret").length + 1 === siblingsArr2.filter("span").length) {
                siblingsArr2.remove();
                return;
            }
            var w = parent.outerWidth(true);
            var h = parent.outerHeight(true);
            parent.attr("class", "emptyCaret").css({ width: w, height: h }).empty();
        }
    });
    /*人员删除END*/


}
/*项目详情页面 END*/

/*部门管理dm*/
function dmJs() {
    $("#topTitle").text("部门管理");

//页面全局变量
    var myModal=$("#myModal");
    var modalTitle=$(".modal_title",myModal);
    var modalBody=$(".modal-body>.modal-main",myModal);
    var modalBtn=$("#modalBtn");
    var collapseOne=$("#collapseOne");
    var collapseTwo=$("#collapseTwo");
    var collapseFive=$("#collapseFive");
    var depWrap=$("#depWrap");
//页面全局变量 END

/*初始化模态框*/
    myModal.modal({
        backdrop: "static",
        show: false
    });
/*初始化模态框 END*/

/*模态框隐藏后移除添加的控件*/
    myModal.on('hide.bs.modal', function () {
        //移除新增的控件
        myModal.find(".newWidget").remove();
        //移除模态框保存的数据
        myModal.removeData();
        //清空主体内容
        modalBody.empty();
        //移除标题
        myModal.find(".modal_title").empty();
        //清除模态框标题
        modalTitle.text("");
        //移除全屏类fullScreen-modal-dialog
        myModal.children('.modal-dialog').removeClass('fullScreen-modal-dialog');
        //移除模态框确认按钮添加的点击处理函数
        modalBtn.off("click",modalBtnNewHandler1);
        modalBtnNewHandler1=null;
        modalBtn.off("click",modalBtnNewHandler2);
        modalBtnNewHandler2=null;
    });
/*模态框隐藏后移除添加的控件END*/

/*部门添加*/
    var inputs1 = $("#collapseOne").find("[name]");
    inputs1.on("blur", blurValidate1);
    inputs1.on("keyup",keyupValidate1);

    function blurValidate1() {
        var $this = $(this);
        var val = $.trim($this.val());
        var valLength = val.length;
        if($this.attr("name")==="dmJob" && $this.data("jobAndStaff") ){
                defaultTip(this);
        }else{
            if (!valLength || $this.siblings().hasClass('glyphicon-remove')) {
                errorTip(this);
            }
        }
    }
    function keyupValidate1() {
        var $this = $(this);
        var val = $.trim($this.val());
        /^[\u4e00-\u9fa5\w]+$/.test(val) ? defaultTip(this) : errorTip(this);
        //不能添加已有相同名的职位
        if($this.attr("name")==="dmJob"){
            if( $this.data("jobAndStaff") ){
                var hasSame;
                $this.data("jobAndStaff").forEach(function(item,index,array){
                    if(item.j_name===$this.val()){
                        hasSame=true;
                    }
                });
                if(hasSame){
                    errorTip($this[0]);
                }else{
                    defaultTip($this[0]);
                }
            }
        }
    }

    //添加成员按钮
    //模态框确认按钮添加点击处理函数
    var modalBtnNewHandler1;
    $("#dmforStaffBtn").on("click", function() {
        inputs1.each(function(index,el){
            $(el).triggerHandler('blur');
        });
        if (!inputs1.siblings().hasClass('glyphicon-remove')) {
            //添加取消按钮
            var cancelBtn=$('<button type="button" class="btn btn-default newWidget" data-dismiss="modal">取消</button>');
            myModal.find(".modal-footer").prepend(cancelBtn);
            //更换模态框标题
            modalTitle.text("添加成员");
            $.ajax({
                url: '',
                type: 'POST',
                data: {sign: 'addUsers'}
            })
            .done(function(data) { 
                data=JSON.parse(data);
                var manWrapIndex=0;
                data.forEach(function(item,index,array){
                    var manWrap=$('<span class="mzm-man-wrap">'+item.nickname+'<span class="mzm-man-icon glyphicon glyphicon-ok"></span></span>');
                    manWrap.on("click",function(){
                        if($(this).hasClass('chose')){
                            $(this).removeClass("chose").find(".glyphicon").css("visibility","hidden");
                        }else{
                            $(this).addClass("chose").find(".glyphicon").css("visibility","visible");
                        }
                    });
                    manWrap.data("manDetails",item);
                    modalBody.append(manWrap);
                    if( (manWrapIndex+1)%3===0 ){
                        modalBody.append($("<br/>"));
                    }
                    manWrapIndex++;
                });
                modalBtnNewHandler1=function(){
                    console.log("modalBtnNewHandler1");
                    var choses=modalBody.find(".chose");
                    if(choses.length>0){
                        var chosesArr=[],chosesArr2=[];
                        choses.each(function(index, el) {
                            chosesArr.push( $(el).data("manDetails").id );
                            chosesArr2.push( $(el).data("manDetails").nickname );
                        });
                        //保存职位和对应成员数据到职位输入框元素上
                        var job=inputs1.filter("[name=dmJob]");
                        if( job.data("jobAndStaff") ){
                            job.data("jobAndStaff").push({j_name:job.val(),users:chosesArr});
                        }else{
                            job.data("jobAndStaff",[{j_name:job.val(),users:chosesArr}]);
                        }
                        //页面显示已添加的职位和成员
                        $("#dmAddedTable>table").append($('<tr><th>'+job.val()+'</th><td><input type="text" style="border:0;width:100%" disabled value="'+chosesArr2.join(" ")+'"></td></tr>')).parent().show();
                        //清空部门输入框
                        job.val("");
                    }
                };
                modalBtn.one("click",modalBtnNewHandler1);
            })
            .fail(function() {
                modalBody.html("连接失败");
            });
            myModal.modal("show");
        }
    });

    //部门添加提交按钮
    $("#dmAddBtn").on("click", function() {
        var $this = $(this);
        inputs1.each(function(index, el) {
            $(el).triggerHandler('blur');
        });
        var collapseOne=$("#collapseOne");
        if (collapseOne.find(".glyphicon-remove").length === 0) {
            var jobAndStaff=inputs1.filter("[name=dmJob]").data("jobAndStaff") || [{j_name:inputs1.filter("[name=dmJob]").val()}];
            $.ajax({
                    url: '',
                    type: 'POST',
                    data: { sign:"addDepartment", 
                            d_name:inputs1.filter("[name=dmAddName]").val(),
                            staff:jobAndStaff
                        }
                })
                .done(function(data) {
                    data=JSON.parse(data);
                    if(data==1){
                        modalBody.html("部门添加成功");
                        location.reload(true);
                    }else if(data==2){
                        modalBody.html("该部门已存在");
                    }else{
                        modalBody.html("部门添加失败");
                    }
                    myModal.modal("show");
                })
                .fail(function() {
                    modalBody.html("连接失败");
                    myModal.modal("show");
                });


        }
    });
    //部门添加取消按钮
    $("#dmAddCancel").on("click",function(){
        //表单输入框恢复初始状态
        inputs1.each(function(index,el){
            $(el).off("blur",blurValidate1).val("").blur();
            defaultTip(el);
            $(el).on("blur",blurValidate1);
        });
        //清空添加的职位成员显示表格
        $("#dmAddedTable").hide().children('table').empty();
        //清除职位表单元素保存的职位和成员数据
        inputs1.filter("[name=dmJob]").removeData('jobAndStaff');    
    });

/*部门添加 END*/

/*部门编辑*/
    //控件与查看项目类似
    //编辑按钮
    var manage_wrap2=collapseTwo.find(".manage_wrap");
    var editBtn2=manage_wrap2.find(".glyphicon-cog");
    var cancelBtn2=manage_wrap2.find(".glyphicon-remove");
    editBtn2.on("click",function(){
        var $this=$(this);
        if($this.hasClass('glyphicon-cog')){
            //保存部门数据
            collapseTwo.data("backup",depWrap.html());
            //添加标示可编辑状态的类
            collapseTwo.addClass('activeInput');
            //职位名称变为可编辑
            depWrap.find(".job_name").each(function(index,el){
                el.contentEditable=true;
            });
            //更换小图标为提交图标
            $this.removeClass('glyphicon-cog').addClass('glyphicon-ok');
            //显示取消图标
            $this.siblings().show();

        }else{
            hide_bs_tab.call( $("#myTab").find("li.active").children('a')[0] );
            console.log(collapseTwo.data("depsFixed"));
        }
    });
    cancelBtn2.on("click",function(){
        var $this=$(this);
        //移除标示可编辑状态的类
        collapseTwo.removeClass('activeInput');
        //隐藏取消图标
        $this.hide();
        //更换编辑&提交图标为编辑图标
        $this.siblings().removeClass('glyphicon-ok').addClass('glyphicon-cog');
        //恢复部门数据
        depWrap.html(collapseTwo.data("backup"));
        collapseTwo.removeData();
    });
    //删除部门
    //模态框确认按钮添加点击处理函数
    var modalBtnNewHandler2;
    var delDepHandler=function(){
        var $this=$(this);
        var depsTab=$("#myTab");
        var dep=depsTab.children('.active');
        var dep_id=dep.children("a").data("id");
        //模态框提示用户是否删除部门（添加取消按钮）
        //添加取消按钮
        var cancelBtn=$('<button type="button" class="btn btn-default newWidget" data-dismiss="modal">取消</button>');
        myModal.find(".modal-footer").prepend(cancelBtn);
        // console.log(dep.text());
        modalBody.html("确定删除部门："+dep.text());
        myModal.modal("show");

        //确认删除后移除相关标签
        modalBtnNewHandler2=function(){
            console.log("modalBtnNewHandler2");
            var depsFixed=collapseTwo.data("depsFixed");
            //判断有无depsFixed数据，有则遍历有无相同部门；无则增加depsFixed数据
            if(depsFixed){
                var hasSameDep=false;
                depsFixed.data.forEach(function(item,index,array){
                    if(item.d_id===dep_id){
                        item.sign="del";
                        item.jobs="";
                        hasSameDep=true;
                    }
                });
            }else{
                collapseTwo.data("depsFixed",{sign: "update",data:[ {d_id:dep_id,sign:"del"} ] });
            }
            $this.closest('.tab-pane').add(dep).remove();

        };
        modalBtn.one("click",modalBtnNewHandler2);
    };
    $(document).on("click",".depDelBtnWrap>.glyphicon",delDepHandler);
    //添加职位
    $(document).on("click",".depAddBtnWrap>.glyphicon",function(){
        var $this=$(this);
        if(!$this.parent().siblings('.input-group').hasClass('newGroup')){
            var addStr='<div class="input-group newGroup"><span class="input-group-addon job_name" contenteditable="true"></span><input type="text" class="form-control txtPopup" readonly><span class="input-group-addon addAddon"><span class="glyphicon glyphicon-plus"></span></span></div><br>';
            $(addStr).insertBefore($this.parent());
        }
    });
    //职位添加确认按钮
    $(document).on("click",".addAddon",function(){
        var $this=$(this);
        //判断职位名称是否重名
        var job=$this.siblings('.job_name');
        var j_name=job.text();
        if(!j_name){
            modalBody.html("职位名称未输入");
            myModal.modal("show");
        }
        var tab_pane=$this.closest('.tab-pane');
        var hasSameJob=false;
        tab_pane.find(".job_name").not(job).each(function(index, el) {
            if($(el).text()===j_name){
                hasSameJob=true;
                modalBody.html("已存在同名职位");
                myModal.modal("show");
            }
        });
        //添加成功（职位名已填写且未重名）后（+变为x ，类由newGroup改为addedJob）
        if(j_name && !hasSameJob){
            var inputGroup=$this.closest('.input-group');
            inputGroup.removeClass('newGroup').addClass('addedJob');
            $this.find(".glyphicon").removeClass('glyphicon-plus').addClass('glyphicon-remove');
        }
    });
    //职位名字修改失去焦点事件
    $(document).on("blur",".job_name",function(){
        var $this=$(this);
        var inputGroup=$this.parent(".input-group");
        if($this.text()!==$this.data("name")){
            inputGroup.addClass('fixedJob1');
        }else{
            inputGroup.removeClass('fixedJob1');
        }
    });
    //职位删除按钮
    $(document).on("click",".removeAddon",function(){
        var $this=$(this);
        var inputGroup=$this.closest('.input-group');
        if(!inputGroup.hasClass('addedJob')){
            var depsTab=$("#myTab");
            var dep=depsTab.children('.active');
            var dep_id=dep.children("a").data("id");
            var job=$this.siblings('.job_name');
            var job_name=job.text();
            var job_id=job.data("id");
            var depsFixed=collapseTwo.data("depsFixed");
            //判断有无depsFixed数据，有则遍历有无相同部门；无则增加depsFixed数据
            if(depsFixed){
                var hasSameDep=false;
                depsFixed.data.forEach(function(item,index,array){
                    if(item.d_id===dep_id){
                        item.jobs.push({j_name:job_name,j_id:job_id,sign:"del"});
                        hasSameDep=true;
                    }
                });
                if(!hasSameDep){
                    depsFixed.data.push( {d_id:dep_id,jobs:[{j_name:job_name,j_id:job_id,sign:"del"}]} );
                }
                
            }else{
                collapseTwo.data("depsFixed",{sign: "update",data:[ {d_id:dep_id,jobs:[{j_name:job_name,j_id:job_id,sign:"del"}]} ] });
            }
        }
        //若删除的职位是该部门的最后一个，则提示是否删除整个部门
        // if(inputGroup.siblings('.input-group').length===0){
        //     delDepHandler.call(inputGroup.siblings('.depDelBtnWrap').find(".glyphicon")[0]);
        // }else{
        //     inputGroup.add(inputGroup.next("br")).remove();
        // }
        inputGroup.add(inputGroup.next("br")).remove();
    });
    

    //部门内容隐藏后保存数据(修改的fixedJob 、 新增的addedJob)
    var hide_bs_tab=function(e){
        // var prevActive;
        // if(e){
        //     prevActive=e.target;
        // }else{
        //     prevActive=this;
        // }
        var prevActive=this;
        var $_prevActive=$(prevActive);
        if(!$_prevActive.is("[data-toggle=tab]")){
            return;
        }
        // console.log(this);
        var prevPane=$("#"+prevActive.href.split("#")[1]);
        // var willActive=e.relatedTarget;
        // var willPane=$("#"+willActive.href.split("#")[1]);
        var prevGroups=prevPane.find(".fixedJob1,.fixedJob2,.addedJob");
        if(prevGroups.length===0){
            return;
        }
        var depsFixed=collapseTwo.data("depsFixed");
        var dep_id=$_prevActive.data("id");
        var jobsArr=[];
        prevGroups.each(function(index, el) {
            var _$this=$(el);
            var job_name=_$this.find(".job_name");
            var users=_$this.find(".txtPopup");
            if(depsFixed){
                //判断是否已存有相同部门的数据
                var hasSameDep,sameIndex;
                depsFixed.data.forEach(function(item,index,array){
                    if(item.d_id===dep_id){
                        hasSameDep=true;
                        sameIndex=index;
                    }
                });
                //已存有相同部门，则在该项（jobs项）增添数据；不然则新添data项
                if(hasSameDep){
                    var jobs=depsFixed.data[sameIndex].jobs;
                    if(jobs instanceof Array){
                        jobs.push({j_name:job_name.text(),j_id:job_name.data("id"),users:users.data("details")});
                    }else{
                        jobs=[];
                    }
                }else{
                    if(index===0){
                        depsFixed.data.push( {d_id:dep_id,jobs:jobsArr});
                    }
                    jobsArr.push({j_name:job_name.text(),j_id:job_name.data("id"),users:users.data("details")});
                }
            }else{
                collapseTwo.data("depsFixed", { sign:"updata",
                    data:[ { d_id:dep_id,jobs:[{j_name:job_name.text(),j_id:job_name.data("id"),users:users.data("details")}] }] 
                 });
            }
            job_name.data("name",job_name.text());
        });
        prevGroups.removeClass('fixedJob1 fixedJob2 addedJob');
    };
    $(document).on('hide.bs.tab','a[data-toggle="tab"]',hide_bs_tab);
    //展开部门编辑，隐藏其他panel
    collapseTwo.on("show.bs.collapse",function(){
        collapseOne.closest('.panel').hide(300);
        collapseFive.closest('.panel').hide(300);
    });
    collapseTwo.on("hide.bs.collapse",function(){
        //panel折叠后，恢复初始状态，即触发取消按钮
        cancelBtn2.triggerHandler('click');
        collapseOne.closest('.panel').show(300);
        collapseFive.closest('.panel').show(300);
    });
    //点击成员输入框弹出模态框编辑
    $(document).on("click", ".txtPopup", function() {
        var depsTab=$("#myTab");
        var activeTab=depsTab.children('.active');
        var $this=$(this);
        //显示标题：部门 / 职位
        myModal.data("hrefInput",$this).find(".modal_title").html(activeTab.text()+" / "+$this.prev().text());
        //模态框全屏显示
        myModal.children('.modal-dialog').addClass('fullScreen-modal-dialog');
        myModal.modal("show");
        //编辑状态下，增加 “编辑” “添加” 按钮 （类似项目详情页面）
        if(collapseTwo.hasClass('activeInput')){
            
        }else{
            
        }
        var manWrap = $('<span class="mzm-man-wrap"></span>');
        var manIcon = $('<span class="mzm-man-icon glyphicon glyphicon-remove"></span>');
        var inputData=$this.data("details");
        var toManItems = function(manArr) {
            modalBody.empty();
            var nth = 1;
            manArr.forEach(function(item, index, array) {
                manWrap.clone(true).data("manDetails", item).text(item.nickname).append(manIcon.clone(true)).appendTo(modalBody);
                if (nth % 3 === 0) {
                    modalBody.append($("<br/>"));
                }
                nth++;
            });
        };

        if (inputData && inputData.length) {
            toManItems(inputData);
        }
    });

/*部门编辑 END*/

}
/*部门管理dm END*/

/*个人管理um*/
function umJs() {
    //更改头部导航栏标题
    $("#topTitle").text("个人管理");

    //该页面全局变量
    var myModal=$("#myModal");

    //错误提示图标点击清空输入框
    $(".form-control-feedback").on("click", function() {
        var $this=$(this);
        if($this.hasClass('glyphicon-remove')){
            $this.siblings('[name]').val("");
        }else if($this.hasClass('iconfont') &&  $this.siblings('[name=newPwd]').length){
            var newPwd2=$this.parent().next().next().find("[name=newPwd2]").val("").attr("disabled",true);
            defaultTip(newPwd2[0]);
            newPwd2.off("blur",pwdFormBlurValidate);
            $this.siblings('[name]').val("").focus();
            newPwd2.on("blur",pwdFormBlurValidate);
        }
    });

/*资料编辑*/
    //获取预览的初始地址
    var avatarPreview=$("#avatarPreview");
    var avatarSrc=avatarPreview.attr("src");

    /*更换头像*/
    $("#uploadFileBtn").change(function() {
        var $file = $(this);
        var fileObj = $file[0];
        var windowURL = window.URL || window.webkitURL;
        var dataURL;
        var $img = $("#avatarPreview");
        if (fileObj && fileObj.files && fileObj.files[0]) {
            dataURL = windowURL.createObjectURL(fileObj.files[0]);
            $img.attr('src', dataURL);
        }
        //  else {
        //     dataURL = $file.val();
        //     var imgObj = $img[0];
        //     imgObj.style.filter = "progid:DXImageTransform.Microsoft.AlphaImageLoader(sizingMethod=scale)";
        //     imgObj.filters.item("DXImageTransform.Microsoft.AlphaImageLoader").src = dataURL;
        // }
    });
    /*更换头像 END*/

    var editInputs = $("#collapseOne").find("[name][type!=file]");
    editInputs.on("blur", editBlurValidate);
    editInputs.on("keyup", editKeyupValidate);

    function editBlurValidate() {
        var $this = $(this);
        var val = $.trim($this.val());
        var valLength = val.length;
        var tName = $this.attr("name");
        if (!valLength || $this.siblings().hasClass('glyphicon-remove')) {
            errorTip(this);
        }
        if (tName === "nickname") {
            /^[\u4e00-\u9fa5\w]+$/.test(val) || errorTip(this);
        } else if (tName === "qq") {
            isQq(val) || errorTip(this);
        } else if (tName === "email"){
            isEmail(val) || errorTip(this);
        }else if(tName === "weixin" ){
            /^[\w]+$/.test(val) || errorTip(this);
        }
    }

    function editKeyupValidate() {
        var $this = $(this);
        var val = $.trim($this.val());
        var tName = $this.attr("name");
        if (tName === "nickname") {
            /^[\u4e00-\u9fa5\w]+$/.test(val) && defaultTip(this);
        } else if (tName === "qq") {
            isQq(val) && defaultTip(this);
        } else if (tName === "email"){
            isEmail(val) && defaultTip(this);
        }else if(tName === "weixin" ){
            /^[\w]+$/.test(val) && defaultTip(this);
        }
    }
    $("#umEditSubmitBtn").on("click", function() {
        editInputs.each(function(index, el) {
            $(el).triggerHandler('blur');
        });
        var formObj=$(this).closest('form');
        var hasChange;
        formObj.find("input[type!=file]").each(function(index,el){
            if(el.value!==el.defaultValue){
                hasChange=true;
            }
        });
        if(avatarPreview.attr('src')!==avatarSrc){
            hasChange=true;
        }

        if ($("#collapseOne").find(".glyphicon-remove,.glyphicon-warning-sign").length === 0 && hasChange) {
        //原生ajax
            var xhr=new XMLHttpRequest();
            //发送完成并成功事件
            xhr.onload=function(){
                if(xhr.responseText==1){
                    //模态框提示
                    $(".modal-body>.modal-main").html("发布成功");
                    myModal.modal("show"); 
                    location.reload(true);
                }else{
                    //模态框提示
                    $(".modal-body>.modal-main").html("发布失败");
                    myModal.modal("show"); 
                }
            };
            //提交失败事件
            xhr.onerror=function(){
                //模态框提示
                $(".modal-body>.modal-main").html("发布失败");
                myModal.modal("show");
            };
            //post方式提交，异步
            xhr.open('post',  JSV.PATH_SERVER+'Index/post_file', true);
            //设置头部信息（post方式必须）
            xhr.setRequestHeader('X-Request-With', 'XMLHttpRequest');
            //获取files对象
            var uploadFileBtn=$("#uploadFileBtn");
            var filesObj=uploadFileBtn[0].files;
            // //通过FormData来构建提交文件数据
            var formData = new FormData();
            //调用 append(name，value) 方法并传入相应的 File 对象作为参数(假设只有一个上传文件)
            formData.append("photo",filesObj[0]);
            var formTextData=requestData(formObj);
            for(var key in formTextData){
                    formData.append(key,formTextData[key]);
            }
            //将formData作为参数调用send()方法
            xhr.send(formData);
            // $.ajax({
            //   url: '',
            //   type: 'POST',
            //   data: formData
            // })
            // .done(function() {
            //   console.log("success");
            // })
            // .fail(function() {
            //   console.log("error");
            // });

        }
    });
    //资料编辑取消按钮
    $("#umEditCancelBtn").on("click",function(){
        editInputs.off("blur", editBlurValidate);
        editInputs.each(function(index,el){
            defaultTip(el);
            $(el).blur();
        });
        $("#collapseOne>.panel-body>form")[0].reset();
        editInputs.on("blur", editBlurValidate);
        avatarPreview.attr("src",avatarSrc);
    });
/*资料编辑 END*/

/*密码服务*/
  //密码修改
    //获取表单
    var pwdForm=$("#collapseSecOne>.panel-body>form");
    var pwdFormInputs=pwdForm.find("[name]");
    //表单验证
    function pwdFormBlurValidate(){
        var $this = $(this);
        var val = $.trim($this.val());
        var tName = $this.attr("name");
        if( !/^\w{6,15}$/.test(val) ){
            errorTip(this);
        }
        if(tName==="newPwd2"){
            if( $this.val() !== pwdFormInputs.filter("[name=newPwd]").val() ){
                errorTip(this);
            }
        }
    }
    function pwdFormKeyupValidate(){
        var $this = $(this);
        var val = $.trim($this.val());
        var tName = $this.attr("name");
        if( /^\w{6,15}$/.test(val) ){
            defaultTip(this);
        }
        if(tName==="newPwd" ){
            if( /^\w{6,15}$/.test(val) ){
                var strength=pwdStrength(val);
                if (strength === 1) {
                    $this.siblings('.form-control-feedback').attr("class", "form-control-feedback iconfont icon-shibaibiaoqing");
                } else if (strength === 2) {
                    $this.siblings('.form-control-feedback').attr("class", "form-control-feedback iconfont icon-emoji02");
                } else if(strength === 3){
                    $this.siblings('.form-control-feedback').attr("class", "form-control-feedback iconfont icon-emojiicon");
                } else{
                    $this.siblings('.form-control-feedback').attr("class", "glyphicon form-control-feedback");
                }
                $this.parent().next().next().find("[name]").removeAttr('disabled');
            }else{
                $this.parent().next().next().find("[name]").attr('disabled',true);
            }
        }
    }
    pwdForm.find("[name]").each(function(index,el){
        $(el).on("blur",pwdFormBlurValidate);
        $(el).on("keyup",pwdFormKeyupValidate);
    });

    var pwdFixCommitBtn=$("#pwdFixCommitBtn");
    var pwdFixCancelBtn=$("#pwdFixCancelBtn");
    //密码修改提交按钮
    pwdFixCommitBtn.on("click",function(){
        pwdFormInputs.each(function(index,el){
            $(el).triggerHandler('blur');
        });
        if(pwdForm.find(".glyphicon-remove").length===0){
            if(pwdFormInputs.filter("[name=oldPwd]").val()===pwdFormInputs.filter("[name=newPwd]").val()){
                $(".modal-body>.modal-main").html("新旧密码相同，请重新输入");
                myModal.modal("show");
            }else{
                $.ajax({
                    url: '',
                    type: 'POST',
                    data: requestData(pwdForm,{sign:"editPassword"})
                })
                .done(function(data) {
                    data=JSON.parse(data);
                    if(data==0){
                        $(".modal-body>.modal-main").html("修改失败");
                    }else if( data==1 ){
                        $(".modal-body>.modal-main").html("修改成功");
                        setTimeout(function(){
                            location.reload(true);
                        },200);
                    }else if( data==2 ){
                        $(".modal-body>.modal-main").html("密码格式不正确");
                    }else if( data==3 ){
                        $(".modal-body>.modal-main").html("旧密码错误");
                    }else if( data==4 ){
                        $(".modal-body>.modal-main").html("两次密码不一致");
                    }else if( data==5 ){
                        $(".modal-body>.modal-main").html("新旧密码相同，请重新输入");
                    }
                    myModal.modal("show");
                })
                .fail(function() {
                    $(".modal-body>.modal-main").html("连接失败");
                    myModal.modal("show");
                });
            }
            
        }
    });
    //密码修改取消按钮
    pwdFixCancelBtn.on("click",function(){
         pwdFormInputs.each(function(index,el){
            $(el).val("").off("blur",pwdFormBlurValidate).blur();
            defaultTip(el);
            $(el).on("blur",pwdFormBlurValidate);
         });
    });
/*密码服务 END*/




}
/*个人管理um END*/


/*个人主页*/
function userJs(){
    //更改头部导航栏标题
    $("#topTitle").text("个人主页");

    //项目名称点击跳转，阻止冒泡(事件委派)
    // $(document).on("click",".projectTitle",function(e){
    //     e.stopPropagation();
    // });

    //展开任务详情，内容滚动到当前视口
    var collapseThree=$("#collapseThree");
    var collapseSecs=collapseThree.find("[id^=collapse]");
    function scrollToYou(){
        collapseThree[0].scrollIntoView();
    }
    collapseSecs.on('shown.bs.collapse',scrollToYou);
    $("#unfoldAll").on("click",scrollToYou);
    window.onunload=function(){
        $("#unfoldAll").off("click",scrollToYou);
    };



}
/*个人主页END*/
