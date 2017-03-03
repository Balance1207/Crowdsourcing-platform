<?php
namespace Lib\Module;
// 用户模块中间件
class TrackModule
{
    // 用户所有任务列表 arModule('Lib.Track')->userList($uid)
    public function userList($uid = '')
    {
        $condition = array(
            'uid' => $uid,
            'stay' => \U_item_track_userModel::STATUS_STAY_IN,
            'touid' => 0,
        );
        $userTracks = \U_item_track_userModel::model()->getDb()
                ->where($condition)
                ->queryAll('tid');
        if ($userTracks) :
            $userTrackIds = array_keys($userTracks);
            $tracks = \U_item_trackModel::model()->getDb()
                ->where(array('tid' => $userTrackIds))
                ->queryAll();
            $tracks = \U_item_trackModel::model()->getDetailInfo($tracks);
            return $tracks;
        else :
            return false;
        endif;

    }

    // 项目的所有任务 arModule('Lib.Track')->itemList($iid)
    public function itemList($iid)
    {
        $condition = array('iid' => $iid);
        $itemTracks = \U_item_trackModel::model()
            ->getDb()
            ->where($condition)
            ->queryAll();
        $tracks = \U_item_trackModel::model()->getDetailInfo($itemTracks);
        return $tracks;

    }

    // 任务详情 arModule('Lib.Track')->detail($tid)
    public function detail($tid)
    {
        $condition = array('tid' => $tid);
        $detail = \U_item_trackModel::model()->getDb()
            ->where($condition)
            ->queryRow();
        if ($detail) :
            $detail['itemName'] = \U_itemsModel::model()->getDb()
                ->where(array('id' => $detail['iid']))
                ->queryColumn('i_name');

            $conditionUser = array(
                'tid' => $tid,
                'stay' => \U_item_track_userModel::STATUS_STAY_IN
            );
            $users = \U_item_track_userModel::model()->getDb()
                ->select('uid')
                ->where($conditionUser)
                ->queryAll();
            $users = \U_item_track_userModel::model()->getDetailInfo($users);
            $detail['users'] = $users;

            $detail['dev_status'] = \U_item_trackModel::$TYPE[$detail['dev_status']];
            $detail['status'] = \U_item_trackModel::$STATUS_MAP[$detail['status']];
            $detail['level'] = \U_item_trackModel::$LEVEL_MAP[$detail['level']];

            if ($detail) :
                return $detail;
            else :
                return false;
            endif;
        else :
            return false;
        endif;

    }

    // 任务日志列表 arModule('Lib.Track')->logs($tid)
    public function logs($tid)
    {
        $condition = array('tid' => $tid);
        $logs = \U_item_track_logModel::model()->getDb()
            ->order('time desc')
            ->where($condition)
            ->queryAll();
        $logs = \U_item_track_logModel::model()->getDetailInfo($logs);
        if ($logs) :
            return $logs;
        else :
            return false;
        endif;

    }

    // 指派任务 arModule('Lib.Track')->assign($tid, $touid, $uid)
    public function assign($tid, $touid, $uid)
    {
        // 修改指派人的数据
        $oldData = array('touid' => $touid);
        $oldCondition = array('tid' => $tid, 'uid' => $uid);
        $result['old'] = \U_item_track_userModel::model()->getDb()
            ->where($oldCondition)
            ->update($oldData);

        // 修改任务接收人的数据
        $newData = array('fromuid' => $uid);
        $newCondtion = array('tid' => $tid, 'uid' => $touid);
        $hasExists = \U_item_track_userModel::model()->getDb()
            ->where($newCondtion)->count();
        if ($hasExists) :
            $result['new'] = \U_item_track_userModel::model()->getDb()
                ->where($newCondtion)
                ->update($newData);
        else :
            $newData['uid'] = $touid;
            $newData['tid'] = $tid;
            $result['new'] = \U_item_track_userModel::model()->getDb()
                ->insert($newData);
        endif;

        if ($result) :
            // 日志内容
            $trackName = \U_item_trackModel::model()->taskInfo($tid);
            $toName = \U_usersModel::model()->getPublisher($touid);
            $opName = \U_usersModel::model()->getPublisher($uid);
            $content = $opName.'将任务：'.$trackName['tname'].'指派给'.$toName;
            $contentSysMsg = $opName.'将任务：'.$trackName['tname'].'指派给您';
            $log = \U_item_track_logModel::model()->joinLog($tid, $content, $uid);

            // 发送消息
            arModule('Lib.Msg')->sendSystemMsg($touid, $contentSysMsg, arU('/main/track/detail', array('tid' => $tid)));
            // 发送微信消息
            arModule('wechat.Send')->TplNewTask($touid, $tid, $uid, $msg = '用户指派');

            if ($log) :
                return true;
            else :
                return false;
            endif;
        else :
            return false;
        endif;

    }

    // 是否是当前任务执行用户 arModule('Lib.Track')->isOpuser($tid, $uid)
    public function isOpuser($tid, $uid)
    {
        $condition = array(
            'uid' => $uid,
            'tid' => $tid,
            'touid' => 0,
        );
        $nums = \U_item_track_userModel::model()->getDb()
            ->where($condition)
            ->count();

        if ($nums > 0) :
            return true;
        else :
            return false;
        endif;

    }

    // 任务说明 arModule('Lib.Track')->trackNote($tid, $content, $uid);
    public function trackNote($tid, $content, $uid)
    {
        return \U_item_track_logModel::model()->joinLog($tid, $content, $uid);

    }

    // 申请加入任务 arModule('Lib.Track')->apply($tid, $apply_msg, $uid);
    public function apply($tid, $apply_msg, $uid)
    {
        $data = array(
            'tid' => $tid,
            'apply_msg' => $apply_msg,
            'uid' => $uid,
            'type' => \U_item_track_applyModel::TYPE_APPLYED,
            'time' => time()
        );

        $count = \U_item_trackModel::model()->getDb()
            ->where(array('tid' => $tid))
            ->count();
        if ($count) :
            $result = \U_item_track_applyModel::model()->getDb()
                ->insert($data);

            if ($result) :
                return true;
            else :
                return false;
            endif;
        else :
            return false;
        endif;

    }

    // 是否有用户 arModule('Lib.Track')->hasUser($tid, $uid)
    public function hasUser($tid, $uid = '')
    {
        if ($uid == '') :
            $uid = arModule('Lib.User')->getUid();
        endif;
        $condition = array(
            'tid' => $tid,
            'uid' => $uid,
            'stay' => \U_item_track_userModel::STATUS_STAY_IN
        );
        $hasnum = \U_item_track_userModel::model()->getDb()->where($condition)->count();
        if ($hasnum > 0) :
            return true;
        else :
            return false;
        endif;

    }

    // 取消任务申请 arModule('Lib.Track')->applyCancel($tid, $uid)
    public function applyCancel($tid, $uid)
    {
        $apply = array(
            'tid' => $tid,
            'uid' => $uid,
            'type' => \U_item_track_applyModel::TYPE_APPLYED
        );

        $aid = \U_item_track_applyModel::model()->getDb()->where($apply)->queryColumn('id');

        $cancelTrue = \U_item_track_applyModel::model()->getDb()
            ->where(array('id' => $aid))
            ->update(array('type' => \U_item_track_applyModel::TYPE_APPLY_CANCEL));

        if ($cancelTrue) :
            $trackName = \U_item_trackModel::model()->getDb()
                ->where(array('tid' => $tid))
                ->queryColumn('tname');
            // var_dump($trackName);
            // exit;
            arModule('Lib.Msg')->sendSystemMsg($uid, '你已经取消申请开发项目' . $trackName);
        endif;
        return $cancelTrue;

    }

    // 是否申请了任务 arModule('Lib.Track')->hasApply($tid, $uid)
    public function hasApply($tid, $uid = '')
    {
        if ($uid == '') :
            $uid = arModule('Lib.User')->getUid();
        endif;

        $condition = array(
            'tid' => $tid,
            'uid' => $uid,
            'type' => \U_item_track_applyModel::TYPE_APPLYED
        );
        $hasnum = \U_item_track_applyModel::model()->getDb()->where($condition)->count();
        if ($hasnum > 0) :
            return true;
        else :
            return false;
        endif;

    }

    // 退出任务 arModule('Lib.Track')->quit($tid, $uid)
    public function quit($tid, $uid)
    {
        $apply = array(
            'tid' => $tid,
            'uid' => $uid,
            'type' => \U_item_task_applyModel::TYPE_APPLY_SUCCESS
        );

        \U_item_track_userModel::model()->delUserFromTrack($tid, $uid);
        $aid = \U_item_track_applyModel::model()->getDb()->where($apply)->queryColumn('id');
        \U_item_track_applyModel::model()->getDb()
            ->where(array('id' => $aid))
            ->update(array('type' => \U_item_track_applyModel::TYPE_USER_EXIT));

        $trackName = \U_item_trackModel::model()->getDb()
            ->where(array('tid' => $tid))
            ->queryColumn('tname');
        arModule('Lib.Msg')->sendSystemMsg($uid, '你已经退出项目' . $trackName);
        return true;

    }

    // 申请任务的用户 arModule('Lib.Track')->userApply($tid);
    public function userApply($tid, $type = \U_item_track_applyModel::TYPE_APPLYED)
    {
        $condition = array(
            'tid' => $tid,
            'type' => $type
        );

        $result = \U_item_track_applyModel::model()->getDb()
            ->select('uid')
            ->where($condition)
            ->queryAll();
        foreach ($result as $key => $value) {
            $result[$key]['nickname'] = \U_usersModel::model()->getPublisher($value['uid']);
        }

        return $result;
    }

    // 审核任务申请 arModule('Lib.Track')->checkApply($tid, $type, $uid, $msg);
    public function checkApply($tid, $type, $uid, $msg, $info = '任务加入通知')
    {
        $condition = array(
            'tid' => $tid,
            'uid' => $uid,
            'type' => \U_item_track_applyModel::TYPE_APPLYED
        );

        // 任务日志信息
        $track = \U_item_trackModel::model()->getTname($tid);
        $username = \U_usersModel::model()->getPublisher($uid);
        $opUid = arModule('Lib.User')->getUid();

        if ($type == \U_item_track_applyModel::TYPE_APPLY_SUCCESS) {
            $data = array(
                'tid' => $tid,
                'uid' => $uid,
                'info' => $info,
                'stay' => \U_item_track_userModel::STATUS_STAY_IN,
                'update_time' => time()
            );
            \U_item_track_userModel::model()->getDb()
                ->insert($data);
            $content = $username . '成功加入任务' . $track['tname'];
        } else {
            $content = $username . '申请加入任务' . $track['tname'] . '失败！';
        }

        // 记录任务变更日志
        \U_item_track_logModel::model()->joinLog($tid, $content, $opUid);
        $checkMsg = array(
            'type' => $type,
            'reply_msg' => $msg
        );
        $result = \U_item_track_applyModel::model()
            ->getDb()
            ->where($condition)
            ->update($checkMsg);
        if ($result) {
            return true;
        } else {
            return false;
        }

    }

    // 根据任务id查询所属项目的id arModule('Lib.Track')->itemId($tid);
    public function itemId($tid)
    {
        $iid = \U_item_trackModel::model()->getDb()
            ->where(array('tid' => $tid))
            ->queryColumn('iid');
        return $iid;

    }

}
