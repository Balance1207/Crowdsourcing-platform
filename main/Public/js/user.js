coop.out_loadmore('正在加载...', '#load-more');
// 登录用户信息
function userInfo()
{
    // $("#topTitle").text("个人主页");
	$.get(JSV.PATH_SERVER + 'api/User/userInfo', function (res) {
		if (res.data) {
			var showModel = $('#userInfo').html();
			var pushHtmlString = '';
            var listString = showModel;
            for (var key in res.data) {
                var reg = new RegExp("\{" + key + "\}");
                listString = listString.replace(reg, res.data[key]);
            }
            pushHtmlString += listString;
			$('#userInfo').html(pushHtmlString);
		}
	}, 'json');
}

// 用户参与的项目
function myItemList()
{
    $.get(JSV.PATH_SERVER + 'api/Item/myItems', function (res) {

        if (res.data) {
            $('#load-more').parents('.weui-cells2').first().remove();

            var showModel = $('#list_tpl2').html();
            var pushHtmlString = '';
            for (var i in res.data) {
                var listString = showModel;
                for (var key in res.data[i]) {
                    var reg = new RegExp("\{" + key + "\}");
                    listString = listString.replace(reg, res.data[i][key]);
                    // listString = listString.replace(/{tname}/, 'aaaa');
                }
                pushHtmlString += listString;
            }
            $('#show-list2').html(pushHtmlString);

        } else {
            coop.out_loadnone('暂无数据, 请先去多加入些项目吧', '#load-more');
        }

    }, 'json');

}

// 用户参与的项目
function myTrackList()
{
    $.get(JSV.PATH_SERVER + 'api/Track/myTracks', function (res) {

        if (res.data) {
            $('#load-more').parents('.weui-cells1').first().remove();

            var showModel = $('#list_tpl1').html();
            var pushHtmlString = '';
            for (var i in res.data) {
                var listString = showModel;
                for (var key in res.data[i]) {
                    var reg = new RegExp("\{" + key + "\}");
                    listString = listString.replace(reg, res.data[i][key]);
                    // listString = listString.replace(/{tname}/, 'aaaa');
                }
                pushHtmlString += listString;
            }
            $('#show-list1').html(pushHtmlString);

        } else {
            coop.out_loadnone('暂无数据, 请先去多加入些项目吧', '#load-more');
        }

    }, 'json');

}
