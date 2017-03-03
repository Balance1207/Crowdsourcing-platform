coop.out_loadmore('正在加载...', '#load-more');
// 获取项目详情
function getItemDetail(id)
{
    var loading = coop.open_loading();
    $.get(JSV.PATH_SERVER + 'api/Item/itemInfo', {id: id}, function (res) {
        coop.close(loading);
        if (res.data) {
            $('#load-more').parents('.weui-cells').first().remove();

            // 处理开发者信息
            var users = res.data['itemUsers'];
            if (users.length > 0) {
                var utpl = $('#tpl-deverlopers').html();
                uPushString = '';
                for (var ukey in users) {
                    var listUstring = utpl;
                    var reg = new RegExp("\{photo\}");
                    listUstring = listUstring.replace(reg, users[ukey]['photo']);
                    reg = new RegExp("\{nickname\}");
                    listUstring = listUstring.replace(reg, users[ukey]['nickname']);
                    uPushString += listUstring;
                }
                $('#tpl-deverlopers').html(uPushString).show();
            }


            var showModel = $('#tpl-itemInfo').html();
            var pushHtmlString = '';
            var listString = showModel;
            for (var key in res.data) {
                var reg = new RegExp("\{" + key + "\}");
                listString = listString.replace(reg, res.data[key]);
            }
            pushHtmlString += listString;
            $('#tpl-itemInfo').html(pushHtmlString);

        } else {
            if (res.ret_msg) {
                coop.out_loadnone(res.ret_msg, '#load-more');
            } else {
                coop.out_loadnone('暂无数据', '#load-more');
            }
        }

    }, 'json');

}

// 申请项目
function apply(iid)
{
    coop.open_input('请输入申请信息', function(content){
        var loading = coop.open_loading();
        $.get(JSV.PATH_SERVER + 'api/Item/apply', {iid: iid, msg: content}, function (res){
            coop.close(loading);
            if (res.success == '1') {
                coop.open_msg_page('申请提醒', res.ret_msg, res.url);
            } else {
                coop.open_alert('消息提醒', res.ret_msg);
            }
        }, 'json');
    });

}

// 撤销项目
function applyCancel(iid)
{
    coop.open_confirm('是否撤销项目申请？', '当前项目申请正处审查阶段，建议联系管理员处理审核信息,仍然撤销？', function() {
        var loading = coop.open_loading();
        $.get(JSV.PATH_SERVER + 'api/Item/applyCancel', {iid: iid}, function (res){
            coop.close(loading);
            if (res.success == '1') {
                coop.open_msg_page('撤销提醒', res.ret_msg, res.url);
            } else {
                // coop.open_msg_page('撤销提醒', res.ret_msg);
                coop.open_alert('警告', res.ret_msg);
            }
        }, 'json');
    });

}

// 撤销项目
function quit(iid)
{
    coop.open_confirm('是否退出项目？', '退出项目将视为自动放弃所有项目福利，确认退出', function() {
        var loading = coop.open_loading();
        $.get(JSV.PATH_SERVER + 'api/Item/quit', {iid: iid}, function (res){
            coop.close(loading);
            if (res.success == '1') {
                coop.open_msg_page('退出通知', res.ret_msg, res.url);
            } else {
                // coop.open_msg_page('撤销提醒', res.ret_msg);
                coop.open_alert('警告', res.ret_msg);
            }
        }, 'json');
    });

}
