layui.use('layer',function(){
    var layer = layui.layer;
});

// 验证form表单中的输入信息不能为空
function notNull($dom,valStatues){//$dom是一个dom元素  valStatues是验证通过状态
    $dom.on("blur",function(){
        var text = $.trim($(this).val());
        if(!text){
            layer.msg("请填写完整的申请流程",{icon:5,anim:6});
            valStatues = false;
        }else{
            valStatues = true;
        }
    });
}
//-----------------------------------------------------------------

// 项目管理 -> 项目查看
function pmProjectJs(){
    // 用来存放接口中返回的所有项目
    var allPrpject = [];
    // 项目模板
    var projectModal =null;
    var page = 1;//分页传递参数参数
    var totalPage = null;
    // 调用全部项目接口
    function addProject(page){
        $.ajax({
            url: JSV.PATH_SERVER+"home/Item/getItem",
            type: "GET",
            data: {
                "page": page
            },
            dataType: 'json',
            success: function(data){
                // alert(0);
                if (data.ret_code == "1000") {
                    totalPage = data.data.count;//取得项目总页数 方面下面判断请求page页边界
                    allPrpject = data.data.result;
                    $.each(allPrpject,function(){
                        projectModal = $("#prject_modal").html();
                        projectModal = projectModal.replace(/prejectIdM/g,this.id);
                        projectModal = projectModal.replace(/prejectSrcM/,this.img);
                        projectModal = projectModal.replace(/prejectNameM/,this.i_name);
                        projectModal = projectModal.replace(/partersM/,this.usersNum);
                        projectModal = projectModal.replace(/prejectStatusM/,this.audit);
                        projectModal = projectModal.replace(/aa/,this.id);
                        $("#project_wrap").append(projectModal);
                    });
                }
            },
            error: function(){
                alert(error);
            }
        });
    }
    addProject();
    // 获取 #pm_wrap 滚动高度
    $(window).scroll(function(){
        // 文档视口高度
        var cT = document.documentElement.clientHeight || document.body.clientHeight;
        // 文档真实高度
        var oT = document.documentElement.offsetHeight || document.body.offsetHeight;
        // 文档收起的高度
        var sT = $(document).scrollTop();
        var yes = sT + cT;

        if(yes==oT){
            $("#loading_more").show();
        }

        if(page>totalPage){
            // $("#loading_more").addClass("");
            var loadingMoreTimer = setInterval(function(){
                $("#loading_more").html("所以项目已加载完毕！");
                clearInterval(loadingMoreTimer);
            },1000);
        }
    });
    $("#loading_more a").on("click",function(){
        page++;
        if(page>totalPage){
            $("#loading_more").html("所以项目已加载完毕！");
            return false;
        }
        addProject(page);
    });
}
//-------------------------------------------------------------------
// 项目管理 -> 项目详情
function pmProjectInfoJs(){
    // 获取项目详情信息
    $.ajax({
        url: JSV.PATH_SERVER+"home/Item/itemInfo",
        type: "GET",
        data: {
            "id": pmProjectId//通过ArPHP框架获取的 （在html页面后去的）
        },
        dataType: 'json',
        success: function(data){
            var itemInfo = data.data;
            $("input[name='i_name']").val(itemInfo.i_name);
            $("input[name='status']").val(itemInfo.audit);
            $("input[name='money']").val(itemInfo.money);
            $("input[name='pmPublisher']").val(itemInfo.publisher);
            // $(".requirement").text(itemInfo.requirement);
            $("textarea[name='requirement']").val(itemInfo.requirement);
            $("input[name='contractDate']").val(itemInfo.contractDate);
            $("input[name='releaseDate']").val(itemInfo.releaseDate);
            $("input[name='days']").val(itemInfo.days);
            $("#pm_project_coderimg").attr("src",itemInfo.img);
        }
    });
}
//-----------------------------------------------------------------
// 个人管理 -> 绑定设置
function wqUmJs(){
    // 验证邮箱
    function isEmail(str){
        var reg = /^\w+((-\w+)|(\.\w+))*\@[A-Za-z0-9]+((\.|-)[A-Za-z0-9]+)*\.[A-Za-z0-9]+$/;
        return reg.test(str);
    }

    // 验证手机号
    function isPhone(phoneNum){
        var reg = /^1[34578]\d{9}$/g;
        return reg.test(phoneNum);
    }

    $(".bundSet").find(".email").on({
        focus: function(){
            $(".eamilTips").text("请输入常用的邮箱").show();
        },
        keyup: function(){
            var email = isEmail($(this).val());
            if (!email) {
                $(".eamilTips").text("输入中...");
            }else {
                $(".eamilTips").text("邮箱格式正确");
            };
        },
        blur: function(){
            var email = isEmail($(this).val());
            console.log($.trim($(this).val()))
            if (!email) {
                $(this).val("").focus();
                $(".eamilTips").text("请输入正确的邮箱");
            }else {
                $(".eamilTips").text("").hide();
            };
        }
    });

    $(".bundSet").find(".phone").on({
        focus: function(){
            $(".phoneTips").text("请输入常用手机号").show();
        },
        keyup: function(){
            var phone = isPhone($(this).val());
            if (!phone) {
                $(".phoneTips").text("输入中...");
            }else{
                $(".phoneTips").text("这个账号可以用");
            }
        },
        blur: function(){
            var phone = isPhone($(this).val())
            if (!phone) {
                $(".phoneTips").text("请输入正确的手机号");
                $(this).val("").focus();
            }else {
                $(".phoneTips").text("").hide();
            };
        }
    });

    $("#bind_weix").on("click",function(){
        $.alert("你已成功绑定微信账号","恭喜！");
    });
}

// 个人管理 -> 我的项目
function umProjectJs(){
    // 将头部标题改为“我的项目”
    $("#topTitle").text("我的项目");

    // 获取当前账号的项目信息
    var itemsArr = [];
    $.ajax({
        url: JSV.PATH_SERVER+"home/Item/listItem",
        type: "GET",
        // data: {},
        dataType: "json",
        success: function(data){
            if(data.data){
                itemsArr = data.data;
                if (itemsArr.length >= 1) {
                    var itemProjectModal = null;
                    var checkStatus = null;
                    $.each(itemsArr,function(){
                        if (this.i_name) {
                            itemProjectModal = $("#project_modal").html();
                            itemProjectModal = itemProjectModal.replace(/item_num/,this.id);
                            itemProjectModal = itemProjectModal.replace(/coderImg/,this.img);
                            itemProjectModal = itemProjectModal.replace(/projectname/,this.i_name);
                            itemProjectModal = itemProjectModal.replace(/parterNum/,this.usersNum);
                            itemProjectModal = itemProjectModal.replace(/checkStatus/,this.audit);
                            itemProjectModal = itemProjectModal.replace(/aa/,this.id);
                            $("#all_project>.row").append(itemProjectModal);
                            // 渲染正在开发项目
                            if (this.online == "1") {
                                $("#doing_project>.row>p").text("");
                                $("#doing_project>.row").append(itemProjectModal);
                            }else if (this.online == "0") {
                                $("#waste_project>.row>p").text("");
                                $("#waste_project>.row").append(itemProjectModal);
                            }
                        }
                    });
                }
            }else{
                $("#all_project>.row").html("&nbsp;&nbsp;&nbsp;&nbsp;您还没有接受项目！！！");
            }
        },
        error: function(){
            alert("error");
        }
    });
}

// 个人管理 -> 项目详情
function umProjectInfoJs(){
    // 将头部标题改为“我的项目”
    $("#topTitle").text("项目详情");

    // 获取项目详情信息
    $.ajax({
        url: JSV.PATH_SERVER+"home/Item/itemInfo",
        type: "GET",
        data: {
            "id": projectId//通过ARPHP语法，在html页面获取的
        },
        dataType: 'json',
        success: function(data){
            var itemInfo = data.data;
            $("#um_project_info_wrap .item-name").text(itemInfo.i_name);
            $("#um_project_info_wrap .itme-status").text(itemInfo.audit);
            $("#um_project_info_wrap .itme-money").text(itemInfo.money);
            $("#um_project_info_wrap .item-publisher").text(itemInfo.publisher);
            $("#um_project_info_wrap .check-info").text(itemInfo.requirement);
            $("#um_project_info_wrap .contract-date").text(itemInfo.contractDate);
            $("#um_project_info_wrap .publish-date").text(itemInfo.releaseDate);
            $("#um_project_info_wrap .development-time").text(itemInfo.days);
            $("#um_project_info_wrap .coder-img").attr("src",itemInfo.img);
        }
    });

    // 调用仓库接口 根据返回字段判断此项目是否有仓库：操作 -> 显示 or 申请 仓库
    $.ajax({
        url: JSV.PATH_SERVER+"home/Item/checkGit",
        type: "GET",
        data: {
            "i_id": projectId
        },
        dataType: "json",
        success: function(data){
            // alert(0)
            var gitExist = data.data.git;
            if(gitExist == "0"){//根据文档接口信息 0表示没有仓库
                $(".apply-git-btn").show();//显示申请仓库按钮
            }else{
                $("#git_show").show();//显示仓库信息
                //将数据加入页面
            }
        },
        error: function(){
            // alert(1)
        }
    });

    // 申请仓库
    $.ajax({
        url: JSV.PATH_SERVER+"home/Item/applyGit",
        type: "GET",
        data: {
            "i_id": projectId,
            "name": $("input[name='gitName']").val(),
            "description": $("textarea[name='']"),
            "content": ""
        },
        dataType: "json",
        success: function(data){
            alert(0)

        },
        error: function(){
            alert(1)
        }
    });

    // 点击"申请仓库"按钮 出现申请版块，隐藏"申请按钮"
    $(".apply-git-btn").on("click",function(){
        $(this).hide();
        $(".apply-form").show();
    });

    // 点击申请仓库表单中的"取消"按钮 隐藏申请仓库表单，显示"申请仓库"按钮
    $("input[name='cancel']").on("click",function(){
        $(".apply-form").hide();
        $(".apply-git-btn").show();
    });

    // 申请仓库验证状态
    var gitYes = false;

    // 申请仓库名称验证
    $("input[name='gitName']").on({
        focus: function(){
           $(this).attr("placeholder","仓库名称只能是字母")
        },
        blur: function(){
            var text = $(this).val();
            var textBool = /[^A-Za-z]+/g.test(text);
            if(textBool || !$.trim(text)){
                $(this).val("").attr("placeholder","仓库名称只能是字母");
                $(this).closest(".form-group").removeClass("has-success").addClass("has-error");
                $(this).next().removeClass("glyphicon-ok").addClass("glyphicon-remove");
                gitYes = false;
            }else{
                $(this).next().removeClass("glyphicon-remove").addClass("glyphicon-ok");
                $(this).closest(".form-group").removeClass("has-error").addClass("has-success");
                gitYes = true;
            }
        }
    });

    // 申请仓库->申请信息验证不能为空
    notNull($("input[name='requirement']"),gitYes);

    // 申请仓库->仓库描述验证不能为空
    notNull($("textarea[name='decription']"),gitYes);

    // 提交申请
    $("input[name='submit']").on("click",function(e){
        if(gitYes == true){
            // 仓库申请成功后执行的操作
            $("#git_show").show();
            $(".apply-git-wrap").hide();
        }else{
            layer.msg("请填写完整的申请流程",
            {
                icon: 5,
                anim: 6
            });
            e.preventDefault();
        }
    });
}

// 个人管理 -> 我的仓库
function myGitJs(){
    // 将头部标题改为“我的项目”
    $("#topTitle").text("仓库列表");
    alert(0);
}

// 我的任务 -> 任务详情
function taskInfoJs(){
    layui.use("layer",function(){
        var layer = layui.layer;
    });
    var popupContent = $("#popup_content");
    layer.msg("哇哈哈哈哈哈哈")
}