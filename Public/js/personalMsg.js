/*私信信息 Msg-index 开始*/
function Msg_index(){
    // 将头部标题改为“私信”
    $("#topTitle").text("私信");
    // 计算#outer的高度
    var cH = document.documentElement.clientHeight || document.body.clientHeight;
    var msgHeadHeight = parseInt($("#topNavWrap").css("height"));
    var msgCopyrightHeight = parseInt($(".weui-footer").css("height"));
    var msgTitleHeight = parseInt($("#userWrap .msgTitle").css("height"));
    var $outerHeight = cH-(msgHeadHeight+msgCopyrightHeight+msgTitleHeight);
    // console.log($("#comeMsgBox").outerHeight());
    $("#outer").css("height",$outerHeight+"px")
    // 定义方法：计算每个私信下面的回复信息总量
    function countMsgs(){
        $(".comeMsg").each(function(index,obj){
            var $span = $(this).siblings().find(".num");
            var $ul = $(this).siblings().find(".wReplyMsgBox");
            var msgNum = $ul.children().length;
            $span.text(msgNum);
        });
    }
    // 声明一个数组，存放消息状态改变成功用户id;
    var idOfSuccessChange = [];
    // 定义改变 改变消息状态函数
    function changeStatus(headId){
    	if(idOfSuccessChange.indexOf(headId)!=-1){
    		return;
    	}
        $.ajax({
            url: "changeStatus",
            type: "GET",
            data: {
                msg_id: headId
            },
            dataType: "json",
            success: function(data){
                idOfSuccessChange.push(headId);
            },
            error: function(data){
                alert("error")
            }
        })
    }
    //声明一个对象，存放用户信息：userName id headImg
    var userInfo = {};
    //获取用户信息
    $.ajax({
        type: "get",
        url: "getUserInfo",
        dataType: "json",
        async: false,
        success: function(data){
            userInfo["userName"] = data.data.nickname;
            userInfo["id"] = data.data.user_id;
            userInfo["headImg"] = data.data.photo;
            userInfo["tel"] = data.data.tel
        }
    });

    var unReadMsgNum = null;// 获取未读消息条数
    var unReadSonMsgArr = [];// 未读子消息
    var unReadMainMsgArr = [];// 未读主消息
    // 定义 获取未读消息方法
    function getUnReadMsgNum(){
        $.ajax({
            url: "unreadMessage",
            type: "GET",
            dataType: "json",
            success: function(data){
                unReadMsgNum = data.data.parentNum + data.data.childrenNum;
                unReadMsgNum = parseInt(unReadMsgNum);
                //console.log('asdfa:');
                //console.log(unReadMsgNum);

                unReadSonMsgArr = data.data.childrenMsgs;
                unReadMainMsgArr = data.data.parentMsgs;
                //$(".badge").text(unReadMsgNum).fadeIn();
                if(unReadMsgNum > 0){
                    $(".badge").text(unReadMsgNum).fadeIn();

                    $(".badge").show();
                    $(".msgTips").show()
                }else{
                    $(".badge").hide();
                    $(".msgTips").hide();
                }
            }
        })
    };
    getUnReadMsgNum();
    var unReadMsgModal;//定义未读消息模板变量
    // 渲染未读消息函数
    function addUnReadMsg(arrObj){
        $.each(arrObj,function(){
            var unReadMsgId = 'unReadMsgId_'+this.msg_id;
            if ($("#"+unReadMsgId).length == 0) {
                unReadMsgModal = $("#unReadMsgModal").html();
                unReadMsgModal = unReadMsgModal.replace(/unReadMsg_id_tpl/,unReadMsgId);
                if (arrObj == unReadSonMsgArr){
                    unReadMsgModal = unReadMsgModal.replace(/nameTpl/,this.send_user.nickname+"  （回复）");
                    unReadMsgModal = unReadMsgModal.replace(/mainMsgId_tpl/,this.parent.msg_id);
                    unReadMsgModal = unReadMsgModal.replace(/mainMsgContent_tpl/,this.parent.content);
                } else {
                    unReadMsgModal = unReadMsgModal.replace(/nameTpl/,this.send_user.nickname);
                    unReadMsgModal = unReadMsgModal.replace(/mainMsgId_tpl/,this.msg_id);
                    unReadMsgModal = unReadMsgModal.replace(/mainMsgContent_tpl/,this.content);

                }
                unReadMsgModal = unReadMsgModal.replace(/account_tpl/,this.send_user.user_account);
                unReadMsgModal = unReadMsgModal.replace(/mainMsgSrc_tpl/,this.send_user.photo);
                unReadMsgModal = unReadMsgModal.replace(/toName/,this.send_user.nickname);
                unReadMsgModal = unReadMsgModal.replace(/sendTimeTpl/,this.send_time);
                unReadMsgModal = unReadMsgModal.replace(/contentTpl/,this.content);
                $(".showUnReadMsg").append(unReadMsgModal);
            }
        })
    }

    // 点击未读消息弹出预览消息事件
    $(".unReadMsg").click(function(){
        getUnReadMsgNum();
        addUnReadMsg(unReadMainMsgArr)
        addUnReadMsg(unReadSonMsgArr)
        if (unReadMsgNum > 0){
            $(".showUnReadMsg").stop(true,true).slideToggle();
        };
        if($(".showUnReadMsg").css("display") == "none"){
            $(".showUnReadMsg").html("");
        }
    })

    // 点击未读消息内容，跳转到消息列表
    var unReadSonMsgs;
    $(".showUnReadMsg").on("click",".unReadMsgItem",function(){
        var msgId = $(this).find(".unReadMsg_tpl").attr("id").match(/\d+/g).toString();
        var headName = $(this).find(".unReadMsg_tpl").attr("data-name");
        var headPhotoSrc = $(this).find(".unReadMsg_tpl").attr("data-src");
        var userAccount = $(this).find(".unReadMsg_tpl").attr("data-account");
        var headMsgId = $(this).find(".unReadMsg_tpl").attr("data-msgid");
        var msgContent = $(this).find(".unReadMsg_tpl").attr("data-content");
        if (msgId != headMsgId) {
            $.each(unReadSonMsgArr,function(){
                if (this.msg_id == msgId){
                    unReadSonMsgs = this.parent.children;
                    addSonMsg(unReadSonMsgs,1,2)//参数1,2只是为了借助其判断addsonmsg函数的arguments
                }
            });
        }
        changeStatus(headMsgId);
        $(".msgContentBox .thisSenderName").text(headName).attr({"id":headMsgId,"data-account":userAccount});
        $(".replyHeadMsg .headMsg").attr("src",headPhotoSrc);
        $(".replyHeadMsg .sender").text(headName);
        $(".replyHeadMsg .comeMsg").text(msgContent);
        $(".msgContentBox").show();
        $(".msgMain").hide();
        $(".showUnReadMsg").hide();
    });
    // 全局变量
    var mainMsgList = [];//接受主消息的数组
    var sonMsgBox = [];//接受子消息的数组

    // 定义获取消息的方法
    var page = 1;
    var totalCount = null;
    function getMsgList(changePage){
        $.ajax({
            url: "getMsgList",
            type: "GET",
            data: {
                "page": changePage
            },
            dataType: "json",
            async: false,
            success: function(data){
                if(data.data){
                    mainMsgList = data.data;
                    totalCount = data.totalCount
                }
            }
        });
    };
    getMsgList();//执行获取消息 拿到发送未获取的消息
    // 定义 子消息模板
    var sonMsgModal;
    // 定义 主消息模板
    var mainMsgModal;
    // 定义 添加子消息函数
    function addSonMsg(msgArrayObj,msgId,num){
        $.each(msgArrayObj,function(){
            var sonMsgId = 'sonMsgId_'+this.msg_id;
            if(msgArrayObj == unReadSonMsgs){
                sonMsgModal = $("#sonMsgMoal").html();
                sonMsgModal = sonMsgModal.replace(/sonMsg_id_tpl/,sonMsgId);
                sonMsgModal = sonMsgModal.replace(/photoSrcModal/,this.send_user.photo);
                sonMsgModal = sonMsgModal.replace(/senderNameModal/,this.send_user.nickname);
                sonMsgModal = sonMsgModal.replace(/子消息id/,this.msg_id);
                sonMsgModal = sonMsgModal.replace(/comMsgContenMoal/,this.content);
                sonMsgModal = sonMsgModal.replace(/sendTime/,this.send_time);
                if(addSonMsg.arguments.length==2){
                    $("#"+msgId).prepend(sonMsgModal);
                }else if(addSonMsg.arguments.length==3){
                    $("#sonMsgBox").prepend(sonMsgModal);
                }
            }else{
                if($("#"+sonMsgId).length == 0){
                    sonMsgModal = $("#sonMsgMoal").html();
                    sonMsgModal = sonMsgModal.replace(/sonMsg_id_tpl/,sonMsgId);
                    sonMsgModal = sonMsgModal.replace(/photoSrcModal/,this.send_user.photo);
                    sonMsgModal = sonMsgModal.replace(/senderNameModal/,this.send_user.nickname);
                    sonMsgModal = sonMsgModal.replace(/子消息id/,this.msg_id);
                    sonMsgModal = sonMsgModal.replace(/comMsgContenMoal/,this.content);
                    sonMsgModal = sonMsgModal.replace(/sendTime/,this.send_time);
                    
                    if(addSonMsg.arguments.length==2){
                        $("#"+msgId).prepend(sonMsgModal);
                    }else if(addSonMsg.arguments.length==3){
                        $("#sonMsgBox").prepend(sonMsgModal);
                    }
                }
            }
            countMsgs();//计算
        });
    }

    // 定义 添加主消息函数
    function addMainMsg(msgArrayObj,msgId){
        $.each(msgArrayObj,function(){
            var msg_id = 'msgid_'+this.msg_id;
            var mainMsgId = this.msg_id;
            var senderId = parseInt(this.sender);
            if ($('#'+msg_id).length == 0) {
                mainMsgModal = $("#mainMsgModal").html();
                if(parseInt(userInfo.id) == senderId){
                    mainMsgModal = mainMsgModal.replace(/photoSrcModal/,this.rec_user.photo);
                    mainMsgModal = mainMsgModal.replace(/senderNameModal/,this.rec_user.nickname);
                    mainMsgModal = mainMsgModal.replace(/comMsgContenMoal/,'我：'+this.content);
                }else{
                    mainMsgModal = mainMsgModal.replace(/photoSrcModal/,this.send_user.photo);
                    mainMsgModal = mainMsgModal.replace(/senderNameModal/,this.send_user.nickname);
                    mainMsgModal = mainMsgModal.replace(/comMsgContenMoal/,this.content);
                }
                mainMsgModal = mainMsgModal.replace(/userTel/,this.rec_user.user_account);
                mainMsgModal = mainMsgModal.replace(/主消息id/,this.msg_id);
                mainMsgModal = mainMsgModal.replace(/msg_id_tpl/,msg_id);
                mainMsgModal = mainMsgModal.replace(/sendtime/,this.send_time);
                mainMsgModal = mainMsgModal.replace(/删除/,"");
                // 如果是系统消息就不回复和删除
                if(this.type == "2"){
                    mainMsgModal = mainMsgModal.replace(/回复/,"");
                    mainMsgModal = mainMsgModal.replace(/条信息/,"系统消息");
                }
                $("#comeMsgBox").prepend(mainMsgModal);
                // 如果为系统消息，就去掉前面的消息数量标签
                if($("#comeMsgBox .slideToggle:contains('系统消息')")){
                    $("#comeMsgBox .slideToggle:contains('系统消息')").html("系统消息");
                }
                // 遍历子消息
                if(this.children.length){//如果子消息存在，开始遍历
                    sonMsgBox = this.children;//将拿到的子消息数组 赋值给sonMsgBox
                    // console.log(sonMsgBox)
                    addSonMsg(sonMsgBox,mainMsgId);//渲染子消息
                };
            }
            if(addMainMsg.arguments.length == 2){
                changeStatus(mainMsgId)
            }
        });
    };

    // 定义：展示主消息方法的函数
    function showMainMsg(){
        // 判断主消息是否存在 否：退出函数
        if(!mainMsgList.length){return false;};
        // 遍历主消息
        $.each(mainMsgList,function(){
        	//如果后台json有url，前端文本添加链接
            if(this.url){
                this.content = "<a href=\'../.."+this.url+"\'>"+this.content+"</a>";
            }
            var msg_id = 'msgid_'+this.msg_id;
            var mainMsgId = this.msg_id;
            var senderId = parseInt(this.sender);
            // console.log(userInfo.id+"=="+senderId)
            if ($('#'+msg_id).length == 0) {
                mainMsgModal = $("#mainMsgModal").html();
                if(parseInt(userInfo.id) == senderId){
                    mainMsgModal = mainMsgModal.replace(/userTel/,this.rec_user.user_account);
                    mainMsgModal = mainMsgModal.replace(/photoSrcModal/,this.rec_user.photo);
                    mainMsgModal = mainMsgModal.replace(/senderNameModal/,this.rec_user.nickname);
                    mainMsgModal = mainMsgModal.replace(/comMsgContenMoal/,'我：'+this.content);
                }else{
                    mainMsgModal = mainMsgModal.replace(/userTel/,this.send_user.user_account);
                    mainMsgModal = mainMsgModal.replace(/photoSrcModal/,this.send_user.photo);
                    mainMsgModal = mainMsgModal.replace(/senderNameModal/,this.send_user.nickname);
                    mainMsgModal = mainMsgModal.replace(/comMsgContenMoal/,this.content);
                }
                mainMsgModal = mainMsgModal.replace(/主消息id/,this.msg_id);
                mainMsgModal = mainMsgModal.replace(/msg_id_tpl/,msg_id);
                mainMsgModal = mainMsgModal.replace(/sendtime/,this.send_time);
                mainMsgModal = mainMsgModal.replace(/删除/,"");
                // 如果是系统消息就不回复和删除
                if(this.type == "2"){
                    mainMsgModal = mainMsgModal.replace(/回复/,"");
                    mainMsgModal = mainMsgModal.replace(/条信息/,"系统消息");
                }
                $("#comeMsgBox").append(mainMsgModal);
                // 如果为系统消息，就去掉前面的消息数量标签
                if($("#comeMsgBox .slideToggle:contains('系统消息')")){
                    $("#comeMsgBox .slideToggle:contains('系统消息')").html("系统消息");
                }
                // 遍历子消息
                if(this.children.length){//如果子消息存在，开始遍历
                    sonMsgBox = this.children;//将拿到的子消息数组 赋值给sonMsgBox
                    addSonMsg(sonMsgBox,mainMsgId);//渲染子消息
                };
                changeStatus(mainMsgId);//改变消息状态
            }
            countMsgs();//计算
        });
    };
    showMainMsg() 

    // 点击发送私信按钮 弹出发送私信弹窗
    $("#openMsgModal").on("click",function(){
        $("#addressee").val("");//初始收信人信息为空，避免上次输入的信息出现
        $("#sendMsg").val("");//初始发送信息为空，避免上次输入的信息出现
        $(".sendMsgModal").modal('show');
    });

    // 发送私信事件
    $("#sendMsgBtn").on("click",function(){
        var action = "继续执行函数";
        var sendMediaDom = null;
        var addresseeText = $("#addressee").val();//获取接受者的信息
        var sendMsgText = $("#sendMsg").val();//获取发送的信息
        // 判断发送对象是否为自己
        if(addresseeText == userInfo["tel"]){
            alert("这是发送给好友的信息！！！")
            $("#addressee").val("").focus();
            return false;
        }
        //判断发送对象是否存在
        $.ajax({
            url: "listName",
            type: "get",
            data: {
                "receiver": addresseeText
            },
            async: false,
            dataType: "json",
            success: function(data){
                if(data.ret_code == 1111){
                    action = "退出执行函数";
                    alert(data.error_msg+"====="+addresseeText);
                    $("#addressee").val("").focus();
                }
            }
        });
        if(action == "退出执行函数"){
            return false;
        }
        
        // 判断发送信息和发送对象不能为空 后执行信息发送
        if($("#sendMsg").val() && $("#addressee").val()){
            // 发送私信
            $.ajax({
                url: "sendMessage",
                type: "POST",
                data: {
                    "receiver": addresseeText,
                    "content": sendMsgText
                },
                dataType: "json",
                beforeSend: function(){
                    $("#userWrap .MyShade").addClass("loadingBg");
                },
                success: function(data){
                    var timer = setTimeout(function(){
                        $("#userWrap .MyShade").text("消息发送成功！").fadeOut(function(){
                            $("#userWrap .MyShade").removeClass("loadingBg");
                        });
                    },400)
                },
                error: function(XMLHttpRequest, textStatus, errorThrown){
                    console.log(XMLHttpRequest.readyState)
                    console.log(XMLHttpRequest.status)
                },
            });
            $(".sendMsgModal").modal('hide');
        }
        getMsgList(1);
        addMainMsg(mainMsgList);
    });

    // 获取 被点击回复按钮的主信息下的子信息
    var thisSonMsgs = null;
    // 点击回复按钮，跳转到回复私信框
    $("#comeMsgBox").on("click",".reply",function(){
        $("#comeMsgBox").html("");
        var thisMsgId = $(this).parent().siblings().find(".wReplyMsgBox").attr("id");
        var thisMsgSender = $(this).parent().siblings(".sender").text();
        var dataAccount = $(this).parents("li.media").attr("data-account");
        var headMsgImg = $(this).parents("li.media").find(".headMsg").attr("src");
        var headMsgContent = $(this).parents("li.media").find(".comeMsg").text();
        thisSonMsgs = $(this).parent().siblings().find(".wReplyMsgBox").html();
        $(".msgContentBox .thisSenderName").text(thisMsgSender).attr({"id":thisMsgId,"data-account":dataAccount});
        $(".replyHeadMsg .headMsg").attr("src",headMsgImg);
        $(".replyHeadMsg .sender").text(thisMsgSender);
        $(".replyHeadMsg .comeMsg").text(headMsgContent);
        $("#sonMsgBox").html(thisSonMsgs);
        $(".msgContentBox").show();
        $(".msgMain").hide();
    });

    //  点击输入框下的发送按钮 回复私信
    $("#replyMsgBtn").on("click",function(){
        var replyMsgCount = $("#replyMsgContent").val();
        var msgId = $(".msgContentBox .thisSenderName").attr("id");
        var receiverTel = $(".msgContentBox .thisSenderName").attr("data-account");
        // 定义回复内容对象
        var replyMsgCountDom =  null;
        if(!$.trim(replyMsgCount)){//判断回复信息不能为空 或者空字符串
            $("p.replyMsgContentTips").text("回复消息为空白，请多说点话吧！").fadeIn();
            var timer = setTimeout(function(){
                $("p.replyMsgContentTips").fadeOut(function(){
                    $("p.replyMsgContentTips").text("");
                });
                clearTimeout(timer);
            },2000);
            $("#replyMsgContent").val("").focus();
            return false;
        }
        $.ajax({
            url: "sendMessage",
            type: "POST",
            data: {
                receiver: receiverTel,
                content: replyMsgCount,
                msg_id: msgId
            },
            dataType: "json",
            beforeSend: function(){
                $("#userWrap .MyShade").addClass("loadingBg");
            },
            success: function(data){
                // alert(data.ret_msg);
                var timer = setTimeout(function(){
                    $("#userWrap .MyShade").text("消息发送成功！").fadeOut(function(){
                        $("#userWrap .MyShade").removeClass("loadingBg");
                    });
                },400);
            }
        });
        // 获取回复的信息
        $.ajax({
            url: "getMsgList",
            type: "GET",
            data: {
                "page": 1
            },
            dataType: "json",
            success: function(data){
                mainMsgList = data.data;
                $.each(mainMsgList,function(index3,obj3){
                    if(this.msg_id == msgId){
                        sonMsgBox = this.children;
                        addSonMsg(sonMsgBox,1,2)//参数1、2使为了填充addSonMsg()的arguments
                    };
                });
            }
        });
        $("#replyMsgContent").val("");
    });

    // 定时查收未读消息
    var getMsgListTimer = setInterval(function(){
        getUnReadMsgNum();
    },100000)

    // 私信主页面中 收起隐藏回复内容
    $("#comeMsgBox").on("click",".slideToggle",function(){
        var replyMsgBoxHtml = $(this).parent().siblings(".replyMsgBox").find(".wReplyMsgBox").html();
        if(replyMsgBoxHtml){
            $(this).parent().siblings(".replyMsgBox").find(".wReplyMsgBox").slideToggle(200);
        }
    });

    // 返回私信主页面
    $("#backMsgMain").on("click",function(){
        $("#sonMsgBox").html("");
        $(".itemsBox .msgContentBox").hide();
        $(".itemsBox .msgMain").show();
        var msgId = $(".msgContentBox .thisSenderName").attr("id");
        getMsgList(1);
        showMainMsg()
    });

    // 子消息删除信息
    $("#comeMsgBox").on("click",".delBtn",function(){
        var msgId = $(this).parent().siblings(".readSonMsg").attr("id");
        $(this).parents("li.delClass").remove();
        $.ajax({
            url: "deleteMessage",
            type: "POST",
            data: {
                msg_id: msgId
            }
        });
        countMsgs();//计算值消息条数
    });

    // 删除主消息
    $("#comeMsgBox").on("click",".delMainMsgBtn",function(){
        var deleteMsgStatus = "no1";
        var msgId = $(this).parent().siblings().find(".wReplyMsgBox").attr("id");
        $.ajax({
            url: "deleteMessage",
            type: "POST",
            data: {
                msg_id: msgId
            },
            dataType: "json",
            success: function(data){
                deleteMsgStatus = "yes1";
            }
        });
        $(this).parents("li.delMainClass").remove();
    })

    // touch事件
    var startX;//触摸时的坐标
    var startY;
    var x;//滑动的距离
    var y;
    var aboveY = 0; //设一个全局变量记录上一次内部块滑动的位置 
    var $inner = $("#comeMsgBox");

    function touchSatrt(e){//触摸
        // e.preventDefault();
        var touch = e.touches[0];
        startY = touch.pageY;   //刚触摸时的坐标 
    }

    function touchMove(e){//滑动          
        e.preventDefault();        
        var  touch = e.touches[0];               
        y = touch.pageY - startY;//滑动的距离
        $inner.css("top",aboveY+y+"px"); //aboveY是inner上次滑动后的位置
        if(y>0 && parseInt($inner.css("top"))>50){
            $(".downStatues").addClass("bgImg1").show();
            var timer2 = setTimeout(function(){
                $(".downStatues").removeClass("bgImg1").text("本次消息加载完毕！！！");
                clearTimeout(timer2);
            },800)
            var timer3 = setTimeout(function(){
                $(".downStatues").text("").hide();
                clearTimeout(timer3);
            },1100) 
        }
        var $comeMsgBoxHeight = parseInt($("#comeMsgBox").outerHeight());
        var upHeight = $comeMsgBoxHeight + y
        if(y<0 && parseInt($inner.css("top"))<=($outerHeight-$comeMsgBoxHeight-50)){
            $(".upStatues").addClass("bgImg2").show();
            var timer4 = setTimeout(function(){
                $(".upStatues").removeClass("bgImg2").text("本次消息加载完毕！！！");
                clearTimeout(timer4);
            },800)
            var timer5 = setTimeout(function(){
                $(".upStatues").text("").hide();
                clearTimeout(timer5);
            },1100)
        }
    } 
    var page = 2;
    function touchEnd(e){//手指离开屏幕
        // e.preventDefault();
        if(y>0 && parseInt($inner.css("top"))>=0){$inner.css("top",0+"px");}
        if(y>50 && parseInt($inner.css("top"))>=0){
            $("#comeMsgBox").html("");
            $inner.css("top",0+"px");
            getMsgList(1);
            showMainMsg()
        }
        var $comeMsgBoxHeight = parseInt($("#comeMsgBox").outerHeight());
        if(y<0 && parseInt($inner.css("top"))<=($outerHeight-$comeMsgBoxHeight-50)){
            if($comeMsgBoxHeight<$outerHeight){
                $inner.css("top",0+"px");
            }else{
                $inner.css("top",($outerHeight-$comeMsgBoxHeight)+"px");
                if(page>totalCount){
                    page = totalCount;
                };
                getMsgList(page);
                showMainMsg()
            }
            page++;
        };
        var $comeMsgBoxHeight = parseInt($("#comeMsgBox").outerHeight());
        var upHeight = $comeMsgBoxHeight + y
        aboveY = parseInt($inner.css("top"));//touch结束后记录内部滑块滑动的位置 在全局变量中体现 一定要用parseInt()将其转化为整数字;
        getUnReadMsgNum();
    }
    document.getElementById("outer").addEventListener('touchstart', touchSatrt,false);  
    document.getElementById("outer").addEventListener('touchmove', touchMove,false);  
    document.getElementById("outer").addEventListener('touchend', touchEnd,false);
}
