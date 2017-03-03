coop.out_loadmore('正在加载任务列表', '#load-more');
// 获取自己的任务
function getMyList()
{
    $.get(JSV.PATH_SERVER + 'api/track/myList', function (res) {

        if (res.data) {
            $('#load-more').parents('.weui-cells').first().remove();

            var showModel = $('#list_tpl').html();
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
            $('#show-list').html(pushHtmlString);

        } else {
            coop.out_loadnone('暂无数据, 请先去多加入些项目吧', '#load-more');
        }

    }, 'json');

}

getMyList();

