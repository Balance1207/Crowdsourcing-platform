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
class U_item_track_logModel extends ArModel
{

    static public function model($class = __CLASS__)
    {
        return parent::model($class);

    }

    // 表名
    public $tableName = 'u_item_track_log';

    // 查看任务的变更日志
    public function getLog($tid)
    {
        $result = U_item_track_logModel::model()->getDb()
            ->where(array('tid' => $tid))
            ->order('time desc')
            ->queryAll();
        foreach ($result as $key => $value) {
            $result[$key]['opuser'] = S_admin_usersModel::model()->getName($result[$key]['opuid']);
            $track = U_item_trackModel::model()->getTname($result[$key]['tid']);
            $result[$key]['tname'] = $track['tname'];
            $result[$key]['time'] = date('Y-m-d H:i:s', $result[$key]['time']);
        }
        return $result;

    }

    // 记录任务变更日志
    public function joinLog($tid, $content, $uid)
    {
        $data = array(
            'tid' => $tid,
            'content' => $content,
            'time' => time(),
            'opuid' => $uid,
            );
        U_item_track_logModel::model()->getDb()
            ->insert($data);
        return true;

    }

    // 获取bundle详细信息 万能方法
    public function getDetailInfo(array $bundles)
    {
        // 递归遍历所有产品信息
        if (arComp('validator.validator')->checkMutiArray($bundles)) :
            foreach ($bundles as &$bundle) :
                $bundle = $this->getDetailInfo($bundle);
            endforeach;
        else :
            $bundle = $bundles;

            $bundle['time'] = date('Y-m-d H:i:s',$bundle['time']);
            $bundle['timeview'] = $this->viewTime($bundle['time']);
            $bundle['opName'] = S_admin_usersModel::model()->getDb()
                ->select('username as nickname')
                ->where(array('id' => $bundle['opuid']))
                ->queryRow();
            if (!$bundle['opName']) :
                $bundle['opName'] = U_usersModel::model()->getDb()
                    ->select('nickname,photo')
                    ->where(array('id' => $bundle['opuid']))
                    ->queryRow();
                if ($bundle['opName']) :
                    if (strpos($bundle['opName']['photo'], 'http://') === false) :
                        $bundle['opName']['photo'] = arCfg('UPLOAD_FILE_SERVER_PATH') . $bundle['opName']['photo'];
                    endif;
                else :
                    $bundle['opName']['photo'] = arCfg('DEFAULT_USER_LOG');
                endif;
            else :
                $bundle['opName']['photo'] = arCfg('DEFAULT_ADMIN_LOG');
            endif;
            return $bundle;
        endif;

        return $bundles;

    }

    // 对时间处理
    public function viewTime($the_time)
    {
        $now_time = date("Y-m-d H:i:s",time());
        $now_time = strtotime($now_time);
        $show_time = strtotime($the_time);
        $dur = $now_time - $show_time;

        if ($dur < 0) :
            return $the_time;
        else :
            if ($dur < 60) :
                return $dur.'秒前';
            else :
                if ($dur < 3600) :
                    return floor($dur/60).'分钟前';
                else :
                    if ($dur < 86400) :
                        return floor($dur/3600).'小时前';
                    else :
                        if ($dur < 259200) :
                            return floor($dur/86400).'天前';
                        else :
                            return $the_time;
                        endif;
                    endif;
                endif;
            endif;
        endif;

    }

}
