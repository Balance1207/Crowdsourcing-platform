// 删除任务成员
function deleteUser(tid, uid)
{
    layer.confirm('是否确定删除成员？', {
        btn: ['确定','取消'] //按钮
    }, function(){
        // layer.msg('的确很重要', {icon: 1});
        $.get(JSV.PATH_APP_SERVER + 'Task/deleteUser', {tid: tid, uid: uid}, function(res) {
            if (res.success == '1') {
                location.reload();
            } else {
                layer.alert('成员已删除请刷新页面');
            }
        }, 'json');
    }, function(){
    });

}
