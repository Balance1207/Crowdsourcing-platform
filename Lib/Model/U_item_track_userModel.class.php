<?php
/**
 * Powerd by ArPHP.
 *
 * Model.
 *
 * @author LW
 */

/**
 * Default Model of webapp.
 */
class U_item_track_userModel extends ArModel
{

    static public function model($class = __CLASS__)
    {
        return parent::model($class);

    }

    // 表名
    public $tableName = 'u_item_track_user';

    // 任务状态
    static $TYPE = array(
        0 => '未分配',
        1 => '指派中',
        2 => '已分配，等待开发',
        3 => '开发中',
        4 => '开发完待测试',
        5 => '测试中',
        6 => '测试未通过已返回',
        7 => '测试通过待审核',
        8 => '审核未通过已返回',
        9 => '审核通过开发完成 待发布',
        10 => '已发布完成'
    );

    // 是否在任务中  1在，0 被踢走了，2 主动退出
    const STATUS_STAY_IN = 1;
    const STATUS_STAY_OUT = 0;
    const STATUS_STAY_LEAVE = 2;

    // 分配任务
    public function assignTask($uid,$tid,$touid,$uid)
    {
    	$data = array(
    		'uid' => $uid,
    		'tid' => $tid,
    		'touid' => $touid,
    		'fromuid' => $uid
    		);

    	$result = U_item_track_userModel::model()->getDb()
    		->insert($data);

    	return $result;
    }

    // 根据任务id查看任务当前执行人
    public function getUser($tid)
    {
        $result = U_item_track_userModel::model()->getDb()
            ->select('uid')
            ->where(array('tid' => $tid,'touid' => 0))
            ->queryRow();

        $result['name'] = U_usersModel::model()->getPublisher($result);

        return $result;
    }

    // 用户的任务列表
    public function myTask($uid)
    {
        $totalCount = U_item_track_userModel::model()->getDb()
            ->where(array('uid' => $uid))
            ->count();
        $page = new Page($totalCount, 4);
        $result = U_item_track_userModel::model()->getDb()
            ->limit($page->limit())
            ->where(array('uid' => $uid))
            ->queryAll();

        foreach ($result as $key => $value) {
            $result[$key]['tid'] = $value['tid'];
            $taskInfo[$key] = U_item_trackModel::model()->taskmy($result[$key]['tid']);
        }
        return $taskInfo;
    }

    // 更新任务的当前处理人
    public function updateUser($tid,$assignuser)
    {
        if (!isset($assignuser)) :
            return false;
        endif;

        $assignUserIds = $assignuser;
        $assignInfo = array();
        foreach ($assignUserIds as $uid) {
            $assignUser = array(
                'tid' => $tid,
                'uid' => $uid,
                'stay' => self::STATUS_STAY_IN
            );
            if (U_item_track_userModel::model()->getDb()->where($assignUser)->count() == 0) :
                $assignInfo[] = $assignUser;
            endif;
        }
        if (!empty($assignInfo)) :
            $result = U_item_track_userModel::model()->getDb()->batchInsert($assignInfo);
            $track = U_item_trackModel::model()->getDb()
                ->where(array('tid' => $tid))
                ->queryRow();

            // 发送系统分配微信消息及系统消息
            foreach ($assignInfo as $trackUser) :
                $contentSysMsg = '新任务：' . $track['tname'];
                arModule('Lib.Msg')->sendSystemMsg($trackUser['uid'], $contentSysMsg, arU('/main/track/detail', array('tid' => $tid)));
                arModule('wechat.Send')->TplNewTask($trackUser['uid'], $trackUser['tid'], '', '系统分配');
            endforeach;

            return $result;
        else :
            return false;
        endif;

    }

    // 获取当前处理用户
    public function getCurrentOperator($tid)
    {
        $condition = array(
            'tid' => $tid,
            'touid' => 0,
        );
        $users = U_item_track_userModel::model()->getDb()->where($condition)->queryAll('uid');
        $users = $this->getDetailInfo($users);
        return $users;

    }

    // 主动退出任务
    public function delUserFromTrack($tid, $uid, $stay = U_item_taskModel::STATUS_STAY_LEAVE, $info = '任务退出通知')
    {
        $condition = array(
            'uid' => $uid,
            'tid' => $tid,
        );
        $user = array(
            'uid' => $uid,
            'tid' => $tid,
            'update_time' => time(),
            'stay' => $stay,
            'info' => $info,
        );

        return U_item_track_userModel::model()->getDb()->where($condition)->update($user);

    }

    // 获取bundle详细信息 万能方法
    public function getDetailInfo(array $bundles)
    {
        // 递归遍历所有信息
        if (arComp('validator.validator')->checkMutiArray($bundles)) :
            foreach ($bundles as &$bundle) :
                $bundle = $this->getDetailInfo($bundle);
            endforeach;
        else :
            $bundle = $bundles;
            $user = U_usersModel::model()->getDb()
                ->select('nickname,id,photo')
                ->where(array('id' => $bundle['uid']))
                ->queryRow();
            if (!$user['photo']) :
                $user['photo'] = arCfg('DEFAULT_USER_LOG');
            else :
                if (strpos($user['photo'], 'http:') === false) :
                    $user['photo'] = arCfg('UPLOAD_FILE_SERVER_PATH') . $user['photo'];
                endif;
            endif;
            $bundle['user'] = $user;
            /**
             * to do what you want
             * $bundle['????'] = '???';
             */
            return $bundle;
        endif;

        return $bundles;

    }

}