/*私信信息 Msg-index 开始*/
function Msg_index(){
    // 将头部标题改为“私信”
    $("#topTitle").text("私信");

    // 定义方法：计算每个私信下面的回复信息总量
    function countMsgs(){
         $(".comeMsg").each(function(index,obj){
            var $span = $(this).siblings().find(".num");
            var $ul = $(this).siblings().find(".wReplyMsgBox");
            var msgNum = $ul.children().length;
            $span.text(msgNum);
        })
    };

    // 收集已加载消息id
    var readMsgsIds = [];
    function readMsgsId(obj1,obj2){
        if(!(obj1 && obj2)){
            return false;
        }
        obj1.each(function(){
            readMsgsIds.push($(this).attr("id"));
        });
        obj2.each(function(){
            readMsgsIds.push($(this).attr("id"));
        });
    }

    // 获取未读消息条数
    var unReadMsgNum = null;
    function getUnReadMsgNum(){
        $.ajax({
            url: "unreadMessage",
            type: "GET",
            dataType: "json",
            success: function(data){
                unReadMsgNum = data.data.parentNum + data.data.childrenNum;
                $(".badge").text(unReadMsgNum).fadeIn();
            }
        })
    }
    getUnReadMsgNum();

    //声明一个对象，存放用户信息：userName id headImg
    var userInfo = {};
    $.ajax({
        type: "get",
        url: "getUserInfo",
        dataType: "json",
        async: false,
        success: function(data){
            userInfo["userName"] = data.data.nickname;
            userInfo["id"] = data.data.id;
            userInfo["headImg"] = data.data.photo;
            userInfo["tel"] = data.data.tel
        }
    });

    // 全局变量
    var mainMsgList = [];//主消息
    var sonMsgList = [];//子消息
    var readySonMsgIds = [];//定义 存放已加载子消息id的数组
    var readyMainMsgIds = [];// 定义 存放已加载消息id的数组
    // 定义获取消息的方法
    function getMsgList(){
        $.ajax({
            url: "getMsgList",
            type: "GET",
            dataType: "json",
            // async: false,
            success: function(data){
                if(data.data){
                    mainMsgList = data.data;
                }
            }
        });
    };
    getMsgList();//执行获取消息 拿到发送未获取的消息

    // 定义 加载主消息函数
    var comeSonMsgIds = [];
    var comeMainMsgIDS = [];
    function showMainMsg(array){
        comeSonMsgIds = [];
        comeMainMsgIDS = [];
        var $readySonMsg = null;
        var thisWreplyMsgBox = null;
        var msgNum1 = null;
        if(!array.length){return false;};// 判断：如果主消息为空时，直接退出函数
        $.each(array,function(index,obj){
            readyMainMsgIds = [];
            var nickname = obj.nickname;//获取发来信息的用户名
            var photoSrc = obj.photo;//获取发来信息的用户头像
            var content = obj.content;//获取发来信息的内容
            var sendTime = obj.send_time;//获取发来的时间 暂时未做展示
            var mainMsg_id = obj.msg_id;//获取信息id
            comeMainMsgIDS.push(mainMsg_id);
            var $readyMainMsg = $("#comeMsgBox .comeMsg");//获取已加载消息对象 方便取出已加载消息id
            var msgDom = $("#mainMsgMoal").html();//获取添加主信息模板
            if($readyMainMsg.html()){
                $readyMainMsg.each(function(){//遍历已加载消息对象 获取其id放入readyMainMsgIds数组
                    readyMainMsgIds.push($(this).attr("id"));
                });

            }
            console.log(readyMainMsgIds)
            // 判断主消息是否加载 否：则加载
            // if(($.inArray(mainMsg_id,readyMainMsgIds) == (-1))){
                msgDom = msgDom.replace(/photoSrcModal/,photoSrc);
                msgDom = msgDom.replace(/senderNameModal/,nickname);
                msgDom = msgDom.replace(/comMsgContenMoal/,content);
                msgDom = msgDom.replace(/主要洗id/,mainMsg_id);
                msgDom = msgDom.replace(/sendtime/,sendTime);
                $("#comeMsgBox").prepend(msgDom);
            // }
            // 子消息
            thisWreplyMsgBox = $("#"+mainMsg_id).siblings(".replyMsgBox").find(".wReplyMsgBox");
            msgNum1 = parseInt($("#"+mainMsg_id).siblings(".tips").find(".num").text());
            
            if(obj.children && obj.children.length){
                sonMsgList = obj.children;
                $.each(sonMsgList,function(index,obj1){
                    var msg_id = obj1.msg_id;//获取信息id
                    comeSonMsgIds.push(msg_id);
                    // console.log(msg_id)
                    var nickname = obj1.nickname;//获取发来信息的用户名
                    var photoSrc = obj1.photo;//获取发来信息的用户头像
                    var content = obj1.content;//获取发来信息的内容
                    var sendTime = obj1.send_time;//获取发来的时间 暂时未做展示
                    var sonMsgDom = $("#sonMsgMoal").html();//获取添加子信息模板
                    $readySonMsg = thisWreplyMsgBox.find(".readSonMsg");
                    // console.log($readySonMsg);
                    if(thisWreplyMsgBox.html()){
                        readySonMsgIds = [];
                        $readySonMsg.each(function(){
                            readySonMsgIds.push($(this).attr("id"));
                            // console.log(readySonMsgIds)
                        });
                    }
                    if($.inArray(msg_id,readySonMsgIds)==(-1)){
                        sonMsgDom = sonMsgDom.replace(/photoSrcModal/,photoSrc);
                        sonMsgDom = sonMsgDom.replace(/senderNameModal/,nickname);
                        sonMsgDom = sonMsgDom.replace(/comMsgContenMoal/,content);
                        sonMsgDom = sonMsgDom.replace(/sendTime/,sendTime);
                        sonMsgDom = sonMsgDom.replace(/子消息id/,msg_id);
                        thisWreplyMsgBox.prepend(sonMsgDom);
                    }
                });
            }

        });
        // 判断主消息是否删除，删除了就移除
        if($("#comeMsgBox").html()){
            $.each(readyMainMsgIds,function(index,obj){
                if($.inArray(obj,comeMainMsgIDS) == -1){
                    $("#"+obj).parents(".delMainClass").remove();
                }
            })
        }
        // 判断子消息是否删除，删除了就移除
        if(msgNum1 != 0){
            $readySonMsg.each(function(){
                if($.inArray($(this).attr("id"),comeSonMsgIds) == (-1)){
                    $(this).parents(".delClass").remove();
                };
            })
        };
        countMsgs();//计算每个主信息下面的子信息条数
    }
    // getMsgList()
    showMainMsg(mainMsgList);// 执行 加载主消息函数
    
    // console.log(msgDom);
    var getMsgListTimer = setInterval(function(){
        // getMsgList();
        // countMsgs();
        getUnReadMsgNum();
        // unReadMsgShow();
        // alert(1)
    },5000)

    // 点击未读消息
    $(".unReadMsg").on("click",function(){
        getMsgList();
        showMainMsg(mainMsgList);
        $(this).find(".badge").text("0");
        if(parseInt($(this).find(".badge").text())==0){
            $(this).find(".badge").hide();
        }
        $("#comeMsgBox .comeMsg").each(function(index,obj){
            var thisId = $(this).attr("id");
            console.log(thisId);
            $.ajax({
                url: "changeStatus",
                type: "GET",
                data: {
                    msg_id: thisId
                },
                dataType: "json",
                success: function(data){
                    console.log("sccuess")
                },
                error: function(data){
                    alert("error")
                }
            })
        })
    })

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
                }
            }
        });
        if(action == "退出执行函数"){
            return false;
        }
        
        // 判断发送信息和发送对象不能为空 后执行信息发送
        if($("#sendMsg").val() && $("#addressee").val()){
            // $("#comeMsgBox").prepend(sendMediaDom);
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
            getMsgList();//获取信息
            showMainMsg(mainMsgList);//展示消息
        }
    });

    // 点击回复按钮，跳转到回复私信框
    var html11 = null;//定义存放加载信息的变量
    $("#comeMsgBox").on("click",".reply",function(){
        html11 = $("#comeMsgBox").html();//已加载的主信息
        var html22 = $(this).parent().siblings(".replyMsgBox").find(".wReplyMsgBox").html();
        var thisSenderName = $(this).parents(".media-body").find(".sender").text();
        var mainMsgId1 = $(this).parents(".media-body").find(".comeMsg").attr("id");
        $("#comeMsgBox").empty();//清空存放主信息的盒子
        $(".itemsBox .msgMain").hide();
        $(".itemsBox .msgContentBox").show();
        $(".msgContentBox .thisSenderName").text(thisSenderName).attr("id",mainMsgId1);
        $("#sonMsgBox").html(html22);
    });

    //  复输入框下的发送按钮 发送私信
    $("#replyMsgBtn").on("click",function(){
        var receiverName = userInfo.userName;
        var replyMsgCount = $("#replyMsgContent").val();
        var msgId = $(".msgContentBox .thisSenderName").attr("id");
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
                receiver: userInfo["tel"],
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
                            },400)
                // $(".msgContentBox .media-list").append(replyMsgCountDom);
                // $("#replyMsgContent").val("");
                var sonMsgDom1 = $("#sonMsgMoal").html();
                sonMsgDom1 = sonMsgDom1.replace(/photoSrcModal/,userInfo["headImg"]);
                sonMsgDom1 = sonMsgDom1.replace(/senderNameModal/,userInfo["userName"]);
                sonMsgDom1 = sonMsgDom1.replace(/comMsgContenMoal/,replyMsgCount);
                sonMsgDom1 = sonMsgDom1.replace(/sendTime/,"刚刚");
                $("#sonMsgBox").prepend(sonMsgDom1);
            }
        });
        $("#replyMsgContent").val("");
    });

    // 私信主页面中 收起隐藏回复内容
    $("#comeMsgBox").on("click",".slideToggle",function(){
        var replyMsgBoxHtml = $(this).parent().siblings(".replyMsgBox").find(".wReplyMsgBox").html();
        if(replyMsgBoxHtml){
            $(this).parent().siblings(".replyMsgBox").find(".wReplyMsgBox").slideToggle(200);
        }
    });

    // 返回私信主页面
    $("#backMsgMain").on("click",function(){
        $("#comeMsgBox").html(html11);
        $(".itemsBox .msgMain").show();
        $(".itemsBox .msgContentBox").hide();
        $("#sonMsgBox").html("");
    });
    // 删除回复信息
    function deleteMsg(obj1,obj2,obj3){
        $("#comeMsgBox").on("click",obj1,function(){
            var msgId = $(this).parent().siblings(obj2).attr("id");
            $(this).parents(obj3).remove();
            $.ajax({
                url: "deleteMessage",
                type: "POST",
                data: {
                    msg_id: msgId
                },
                async: false,
                success: function(data){
                    // alert("删除成功")
                }
            })
        });
    }
    deleteMsg(".delBtn",".readSonMsg","li.delClass")
    deleteMsg(".delMainMsgBtn",".comeMsg","li.delMainClass")


// touch时间
    var startX,//触摸时的坐标
        startY,
        x, //滑动的距离
        y,
        aboveY=0; //设一个全局变量记录上一次内部块滑动的位置 
    var $inner=$("#comeMsgBox");

    function touchSatrt(e){//触摸
        e.preventDefault();
        var touch=e.touches[0];
        startY = touch.pageY;   //刚触摸时的坐标 
    }

    function touchMove(e){//滑动          
         e.preventDefault();        
         var  touch = e.touches[0];               
         y = touch.pageY - startY;//滑动的距离
        //inner.style.webkitTransform = 'translate(' + 0+ 'px, ' + y + 'px)';  //也可以用css3的方式     
        $inner.css("top",aboveY+y+"px"); //这一句中的aboveY是inner上次滑动后的位置                   
    }  
    var page = 2;
    function touchEnd(e){//手指离开屏幕
        e.preventDefault();
        if(y>0 && parseInt($inner.css("top"))>=0){
            $inner.css("top",0+"px")
            mainMsgList = [];
            sonMsgList = [];
            // alert(page)
            $.ajax({
                url: "getMsgList",
                type: "GET",
                data: {
                    "page": page
                },
                dataType: "json",
                // async: false,
                success: function(data){
                    if(data.data){
                        mainMsgList = data.data;
                        showMainMsg(mainMsgList);
                    }
                }
            });
            page++;
            $(".unReadMsg .badge").text("0");
            $("#comeMsgBox .comeMsg").each(function(index,obj){
                var thisId = $(this).attr("id");
                console.log(thisId);
                $.ajax({
                    url: "changeStatus",
                    type: "GET",
                    data: {
                        msg_id: thisId
                    },
                    dataType: "json",
                    success: function(data){
                        console.log("sccuess")
                    },
                    error: function(data){
                        alert("error")
                    }
                })
            })
        };
        aboveY=parseInt($inner.css("top"));//touch结束后记录内部滑块滑动的位置 在全局变量中体现 一定要用parseInt()将其转化为整数字;
    }
    document.getElementById("outer").addEventListener('touchstart', touchSatrt,false);  
    document.getElementById("outer").addEventListener('touchmove', touchMove,false);  
    document.getElementById("outer").addEventListener('touchend', touchEnd,false);

    function unReadMsgShow(){
        if($("#unReadMsgShow").text()=="0"){
            $("#unReadMsgShow").hide();
        }
        alert($("#unReadMsgShow").text())
    }
    // unReadMsgShow()
}