coop.out_loadmore('正在加载任务列表', '#load-more');
// 获取自己的任务
function getItemTrackList(iid)
{
    $.get(JSV.PATH_SERVER + 'api/track/itemList', {iid: iid}, function (res) {
        if (res.data) {
            $('#load-more').parents('.weui-cells').first().remove();
            var showModel = $('#tpl-track-list').html();
            var pushHtmlString = '';
            for (var i in res.data) {
                var listString = showModel;
                var itemName = res.data[i]['item']['i_name'];
                res.data[i]['itemName'] = itemName;

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

}


