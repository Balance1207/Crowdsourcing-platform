<?php
namespace system\Module;

// 处理任务追踪信息中间件
class TrackModule
{
    // 删除任务成员 arModule('system.Track')->deleteUser(arRequest('tid'), arRequest('uid'));
    public function deleteUser($tid, $uid)
    {
        $condition = array(
            'tid' => $tid,
            'uid' => $uid,
        );
        return \U_item_track_userModel::model()
            ->getDb()
            ->where($condition)
            ->delete();

    }

    // 审核任务申请
    // public function checkApply($tid, $type, $uid, $info = '任务加入通知')
    // {
    //     $condition = array(
    //         'tid' => $tid,
    //         'uid' => $uid
    //     );
    //     // 任务日志信息
    //     $tname = \U_item_trackModel::model()->getTname($tid);
    //     $username = \U_usersModel::model()->getPublisher($uid);
    //     $opUid = arModule('Lib.User')->getUid();

    //     if ($type == \U_item_track_applyModel::TYPE_APPLY_SUCCESS) {
    //         $data = array(
    //             'tid' => $tid,
    //             'uid' => $uid,
    //             'info' => $info,
    //             'stay' => \U_item_track_userModel::STATUS_STAY_IN,
    //             'update_time' => time()
    //         );
    //         \U_item_track_userModel::model()->getDb()
    //             ->insert($data);
    //         $content = $username .'成功加入任务'. $tname;  
    //     } else {
    //         $content = $username .'加入任务'. $tname .'失败！';
    //     }
        
    //     // 记录任务变更日志
    //     \U_item_track_log::model()->joinLog($tid, $content, $opUid);

    //     $return U_item_track_applyModel::model()
    //         ->getDb()
    //         ->where($condition)
    //         ->update(array('type' => $type));
        
    // }

}
