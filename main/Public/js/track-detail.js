coop.out_loadmore('正在加载...', '#load-more');
// 获取任务详情
function getTrackDetail(tid)
{
    var loading = coop.open_loading();
    $.get(JSV.PATH_SERVER + 'api/track/detail', {tid: tid}, function (res) {
        coop.close(loading);
        if (res.data) {
            $('#load-more').parents('.weui-cells').first().remove();

            // 处理开发者信息
            var users = res.data['users'];
            if (users.length > 0) {
                var utpl = $('#tpl-deverlopers').html();
                uPushString = '';
                for (var ukey in users) {
                    var listUstring = utpl;
                    var reg = new RegExp("\{photo\}");
                    listUstring = listUstring.replace(reg, users[ukey]['user']['photo']);
                    reg = new RegExp("\{nickname\}");
                    listUstring = listUstring.replace(reg, users[ukey]['user']['nickname']);
                    reg = new RegExp("\{user.id\}");
                    listUstring = listUstring.replace(reg, users[ukey]['user']['id']);
                    uPushString += listUstring;
                }
                $('#tpl-deverlopers').html(uPushString).show();
            }


            var showModel = $('#tpl-trackInfo').html();
            var pushHtmlString = '';
            var listString = showModel;
            for (var key in res.data) {
                var reg = new RegExp("\{" + key + "\}");
                listString = listString.replace(reg, res.data[key]);
            }
            pushHtmlString += listString;
            $('#tpl-trackInfo').html(pushHtmlString);

        } else {
            if (res.ret_msg) {
                coop.out_loadnone(res.ret_msg, '#load-more');
            } else {
                coop.out_loadnone('暂无数据', '#load-more');
            }
        }

    }, 'json');

};


// 任务日志列表
function trackLogs(tid)
{
    $.get(JSV.PATH_SERVER + 'api/track/logList', {tid: tid}, function (res) {
        if (res.data) {
            $('#load-more').parents('.weui-cells').first().remove();
            var showModel = $('#tpl-track-list').html();
            var pushHtmlString = '';
            for (var i in res.data) {
                var listString = showModel;
                var content = res.data[i]['content'];
                var time = res.data[i]['time'];
                var nickname = res.data[i]['opName']['nickname'];
                res.data[i]['nickname'] = nickname;
                var photo = res.data[i]['opName']['photo'];
                res.data[i]['photo'] = photo;

                for (var key in res.data[i]) {
                    var reg = new RegExp("\{" + key + "\}");
                    listString = listString.replace(reg, res.data[i][key]);
                    // listString = listString.replace(/{tname}/, 'aaaa');
                }
                pushHtmlString += listString;
            }
            $('#track-list').html(pushHtmlString);

        } else {
            if (res.ret_msg) {
                coop.out_loadnone(res.ret_msg, '#load-more');
            } else {
                coop.out_loadnone('暂无数据', '#load-more');
            }
        }

    }, 'json');

};


// 指派任务
function assign(iid, tid)
{
    $.get(JSV.PATH_SERVER + 'api/Item/users', {iid: iid}, function (res) {
        var selectList = [];
        for (var i in res.data) {
            if (login_user.uid == res.data[i]['user']['id']) {
                continue;
            }
            selectList.push({label: res.data[i]['user']['nickname'], value: res.data[i]['user']['id']});
        }
        if (selectList.length == 0) {
            coop.open_alert('提示：', '暂时还没有其他用户加入项目');
            return;
        }
        coop.select(selectList, {
            onChange: function (result) {
                // console.log(result);
            },
            onConfirm: function (result) {
                 coop.open_confirm('是否任务提交给下一个人处理？', '提交任务将转移当前执行任务的控制权', function(){
                    console.log(result[0]);
                    $.get(JSV.PATH_SERVER + 'api/Track/assign', {tid: tid, touid: result[0]}, function(res){
                        if (res.success == '1') {
                            coop.open_msg_page('指派提醒', res.ret_msg, res.url);
                        } else {
                            coop.open_alert('指派提醒', res.ret_msg);
                        }
                    }, 'json');
                 });
            }
        });

   }, 'json');

};

// 获取任务详细
getTrackDetail(tid);
//获取任务日志列表
trackLogs(tid);

// 添加任务说明
function trackNote(tid)
{
    coop.open_input('请输入说明信息', function(content){
        var loading = coop.open_loading();
        $.get(JSV.PATH_SERVER + 'api/Track/trackNote', {tid: tid, content: content}, function (res){
            coop.close(loading);
            if (res.success == '1') {
                coop.open_msg_page('提交提醒', res.ret_msg, res.url);
            } else {
                coop.open_alert('消息提醒', res.ret_msg);
            }
        }, 'json');
    });

};

// 申请任务
function apply(tid)
{
    coop.open_input('请输入申请信息', function(content){
        var loading = coop.open_loading();
        $.get(JSV.PATH_SERVER + 'api/Track/apply', {tid: tid, msg: content}, function (res){
            coop.close(loading);
            if (res.success == '1') {
                coop.open_msg_page('申请提醒', res.ret_msg, res.url);
            } else {
                coop.open_alert('消息提醒', res.ret_msg);
            }
        }, 'json');
    });

};

// 退出任务
function quit(tid)
{
    coop.open_confirm('是否退出任务？', '退出任务将视为自动放弃所有任务福利，确认退出', function() {
        var loading = coop.open_loading();
        $.get(JSV.PATH_SERVER + 'api/Track/quit', {tid: tid}, function (res){
            coop.close(loading);
            if (res.success == '1') {
                coop.open_msg_page('退出通知', res.ret_msg, res.url);
            } else {
                // coop.open_msg_page('撤销提醒', res.ret_msg);
                coop.open_alert('警告', res.ret_msg);
            }
        }, 'json');
    });

};

// 撤销任务申请
function applyCancel(tid)
{
    coop.open_confirm('是否撤销任务申请？', '当前任务申请正处审查阶段，建议联系管理员处理审核信息,仍然撤销？', function() {
        var loading = coop.open_loading();
        $.get(JSV.PATH_SERVER + 'api/Track/applyCancel', {tid: tid}, function (res){
            coop.close(loading);
            if (res.success == '1') {
                coop.open_msg_page('撤销提醒', res.ret_msg, res.url);
            } else {
                // coop.open_msg_page('撤销提醒', res.ret_msg);
                coop.open_alert('警告', res.ret_msg);
            }
        }, 'json');
    });

};

// 展示用户信息框
function user_show(content){

        layui.use('layer', function(){
          var layer = layui.layer;
          layer.open({
                      type:1,
                      // anim:1,
                      skin: 'demo-class',
                      title: false,
                      closeBtn: 0,
                      shadeClose: true,
                      skin: 'yourclass',
                      content: content
             });
        });  
};
//调用用户信息
function personalInformation(uid){ 
       
        $.ajax({

            　　type:"get",

                 asynch : "false",//并发访问时异步提交

            　　url:JSV.PATH_SERVER +"api/User/userInfo", 

            　　dataType:"json",//指定返回格式，不指定则返回字符串

            　　data:{"u_id":uid},

            　　success:function(res){
                    var showBoxTpl = $('#userBox').html();
                    var userMsgDiv = showBoxTpl; 
                    
                    var reg = new RegExp("\{nickname\}");
                    userMsgDiv = userMsgDiv.replace(reg, res.data.nickname);
                      var reg = new RegExp("\{photo\}");
                    userMsgDiv = userMsgDiv.replace(reg, res.data.photo);
                      var reg = new RegExp("\{weixin\}");
                    userMsgDiv = userMsgDiv.replace(reg, res.data.weixin);
                      var reg = new RegExp("\{id\}");
                    userMsgDiv = userMsgDiv.replace(reg, res.data.id);
                    user_show(userMsgDiv);
                     }
                });
    }
   
    // $(function(){
    //    $(".code img").click(function(){  
    //     $(this).animate({
    //               width:'300px',
    //               height:'300px',
    //        });
    //     alert("xsjhd")
    //   });
   
    // })
   
  
   

   
