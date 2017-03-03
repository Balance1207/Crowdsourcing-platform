// 获取项目群组成员信息
function itemMemberInfo(){
	$.ajax({
		url: JSV.PATH_SERVER+"api/Item/group",
		type: "get",
		data: {
			"iid": prejectId
		},
		dataType: "json",
		async: false,
		success: function(res){
			if (res.data) {
				var itemMemberInfo ;
				var users = res['data']['users'];
				var tracks = res['data']['tracks'];
				
				// First Thinking
				for (var key in tracks) {
					var itemName = tracks[key]['item'];
					// console.log(itemName);
					$(".i_name").text(itemName['i_name']);
					$(".items-url").attr("href",itemName['items_url']);
					$(".QR-bar").attr("src",itemName['url']);
				}
				for (var i in users) {
					var userInfo = users[i]['user'];
					// console.log(userInfo);
					itemMemberInfo = $("#memberList").html();
					itemMemberInfo = itemMemberInfo.replace(/{idCard}/,userInfo['id']);
					itemMemberInfo = itemMemberInfo.replace(/{srcPhotos}/,userInfo['photo']);
					itemMemberInfo = itemMemberInfo.replace(/{userName}/,userInfo['nickname']);
					itemMemberInfo = itemMemberInfo.replace(/{userTel}/,userInfo['tel']);
					$(".member-list").prepend(itemMemberInfo);
					// for (var userKey in userInfo) {
					// 	// console.log(userInfo[userKey]);
					// 	var userInfoAll = userInfo[userKey];
					// console.log(itemMemberInfo);
				// }
				}

				// Second Thinking
				// console.log(res.data.users);
				// var users = res.data.users;
				// for (var i in users) {
				// 	// console.log(users[i]);
				// 	var user = users[i]['user'];
				// 	var userInfo = user;
				// 	// console.log(userInfo);
				// 	$.each(userInfo,function(){
				// 		// console.log(users);
				// 		// var user = users.user;
				// 		// console.log(user);
				// 		itemMemberInfo = $("#memberList").html();
				// 		itemMemberInfo = itemMemberInfo.replace(/{idCard}/,userInfo['id']);
				// 		itemMemberInfo = itemMemberInfo.replace(/{srcPhotos}/,userInfo['photo']);
				// 		itemMemberInfo = itemMemberInfo.replace(/{userName}/,userInfo['nickname']);
				// 		itemMemberInfo = itemMemberInfo.replace(/{userTel}/,userInfo['tel']);
				// 		itemMemberInfo = itemMemberInfo.replace(/{weiXin}/,userInfo['weixin']);
				// 		$(".member-list").prepend(itemMemberInfo);
				// 	});
				// }
			}
		}
	});
};
// 用户资料框和消息发送框的弹出与关闭
function user_box(){
	// 弹出个人资料框
	$(".person").on("click",function(){
		$(".userImg").attr("src",$(this).attr("src"));
		$(".nick_name").text($(this).parent().attr("data-name"));
		$(".tel").text($(this).parent().attr("data-tel"));
		$(".details-wrap").attr("pro-id",$(this).parent().attr("data-id"));
		$(".message-box").attr("pro-id",$(this).parent().attr("data-id"));
		$(".details-wrap").show();
	});

	// 关闭个人资料框
	$(".close-message-box").on("click",function(){
		$(".details-wrap").hide();
	});

	// 弹出消息发送框
	$(".send-message-box").on("click",function(){
		$(".message-box").show();
		$(".details-wrap").hide();
		$(".content").val('');
	});

	// 关闭消息发送框
	$(".close-send-message").on("click",function(){
		$(".message-box").hide();
	});

	// 弹出群组二维码
	$(".group-qr-code").on("click",function(){
		$(".QR-bar-box img").attr("src",$(".QR-bar").attr("src"));
		$(".QR-bar-box").show();
	});

	// 关闭群组二维码
	$(".close-bar-box").on("click",function(){
		$(".QR-bar-box").hide();
	});

	// 显示与隐藏任务个人任务列表
	$(".perTaskList").on("click",function(){
		$(".task-list").toggle(500);
	});
};
// 获取当前登录用户ID
var loginUserId;
$.ajax({
	url: JSV.PATH_SERVER+"api/User/userInfo",
	type: "get",
	data:{
		"u_id": loginUserId
	},
	dataType: "json",
	success: function(res){
		loginUserId = res['data']['id'];
		// console.log(indexUserId);
	}
})

// 发送消息
function sendMessage(){
	//点击发送消息按钮
	$(".send-message").on("click",function(){
		// 获取消息接受人ID
		var acceptPerId = $(".message-box").attr("pro-id");
		// console.log(acceptPerId);

		// 获取发送消息内容
		var sendMsgContent = $(".content").val();
		// console.log(sendMsgContent);

	//	用户发送消息
		$.ajax({
			url: JSV.PATH_SERVER+"api/Msg/sendMsg",
			type: "get",
			data: {
				"uid": acceptPerId,
				"content": sendMsgContent
			},
			dataType: "json",
			success: function(){
				if (loginUserId == acceptPerId) {
					coop.open_alert("","不能给自己发送消息！");
					setTimeout(function(){$(".message-box").hide()},2000);
					return false;
				}
				else if (sendMsgContent == ''){
					coop.open_alert("","发送消息不能为空！");
				}
				else{
					coop.open_toast('已发送',2);
					setTimeout(function(){$(".message-box").hide()},2000);
				}
			},
			error: function(){
				coop.open_toast('发送失败',2);
				setTimeout($(".message-box").hide(),2000);
			}
		});
	});
}	
// 弹出删除图标
// $(".del").on("click",function(){
// 	$(".closeLayer").toggle();
// });


// 退出群组
function exit_group(){
	$(".drop-out").on("click",function(){
	    coop.open_confirm('确认退出群组？', '退出后不会通知群组中其他成员，且不会再接收此群组消息', function() {
	        var loading = coop.open_loading();
	        $.get(JSV.PATH_SERVER + 'api/Item/quit', {iid: itemId}, function (res){
	            coop.close(loading);
	            if (res.success == '1') {
	                coop.open_msg_page('退出通知', res.ret_msg, res.url);
	            } else {
	                // coop.open_msg_page('撤销提醒', res.ret_msg);
	                coop.open_alert('警告', res.ret_msg);
	            }
	        }, 'json');
	    });
	});
}

// 设定群组二维码
// $(".group-qr-code").on("click",function(){
// 	layer.open({
// 		type: 1,
// 		content: '<img src="/quickoa/main/Public/images/QR-bar.jpg" style="padding:20px 20px;">'
// 	});
// });

// 获取当前登录用户任务列表
function userItemsList(){
	$.ajax({
		url: JSV.PATH_SERVER+"api/Track/myList",
		type: "get",
		dataType: "json",
		success: function(res){
			if(res.data){
				var taskInfo;
				for (var i in res['data']) {
					var taskName = res['data'][i];
					// console.log(taskName);
					taskInfo = $(".model").html();
					taskInfo = taskInfo.replace(/{tName}/g,taskName['tname']);
					taskInfo = taskInfo.replace(/{track_url}/g,taskName['track_url']);
					$(".task-list").prepend(taskInfo);
				}
			};
		}
	});
}





