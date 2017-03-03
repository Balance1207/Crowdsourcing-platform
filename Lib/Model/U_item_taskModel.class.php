<?php
/**
 * Powerd by ArPHP.
 *
 * Model.
 *
 * @author ycassnr <ycassnr@gmail.com>
 */

/**
 * Default Model of webapp.
 */
class U_item_taskModel extends ArModel
{

    static public function model($class = __CLASS__)
    {
        return parent::model($class);

    }

    // 表名
    public $tableName = 'u_item_task';

    // 项目审核状态转换
    static $TYPE = array(
        0 => '未审核',
        1 => '已审核',
    );

    // 是否在项目中  1在，0 被踢走了，2 主动退出
    const STATUS_STAY_IN = 1;
    const STATUS_STAY_OUT = 0;
    const STATUS_STAY_LEAVE = 2;

    // 查询当前登录用户的所有项目
    function listItem($uid)
    {
        $items = U_item_taskModel::model()->getDb()
            ->select('i_id')
            ->where(array('u_id' => $uid,'stay' =>'1'))
            ->group('i_id')
            ->queryAll();

        foreach ($items as $key => $value) {
            $iteminfo[$key] = U_itemsModel::model()->getDb()
                ->select('id,i_name,online,img,users,audit')
                ->where(array('id' => $value['i_id'], 'status' => U_itemsModel::STATUS_APPROVED))
                ->queryRow();

            // 参与项目的人数
            if ($iteminfo[$key]['users']) {
                $users                      = explode(',', $iteminfo[$key]['users']);
                $iteminfo[$key]['usersNum'] = count($users);
            } else {
                $iteminfo[$key]['usersNum'] = 0;
            }

            // 二维码图片
            if ($iteminfo[$key]['img']) {
                $iteminfo[$key]['img'] = arCfg('UPLOAD_FILE_SERVER_PATH') . $iteminfo[$key]['img'];
            } else {
                $iteminfo[$key]['img'] = arCfg('DEFAULT_USER_LOG');
            }
            // 项目审核状态
            $iteminfo[$key]['audit'] = self::$TYPE[$iteminfo[$key]['audit']];
        }
        return $iteminfo;

    }

    // 添加项目成员
    function addUser($i_id,$u_id)
    {
        $data = array(
            'i_id' => $i_id,
            'u_id' => $u_id,
        );
        $add = U_item_taskModel::model()->getDb()
            ->insert($data);

        // 查询项目名称
        $itemInfo = U_itemsModel::model()->getInfo($i_id);
        $itemName = $itemInfo['i_name'];

        arModule('Lib.Msg')->sendSystemMsg($post['u_id'], '你已经加入项目'.$itemName);
        return $add;

    }

    // 删除项目成员
    function delUser($i_id,$u_id)
    {
        $data = array(
            'i_id' => $post['i_id'],
            'u_id' => $post['u_id'],
        );

        $del = U_item_taskModel::model()->getDb()
            ->where($data)
            ->update(array('stay' => 0));

        //查询项目名称
        $itemInfo = U_itemsModel::model()->getInfo($post['i_id']);
        $itemName = $itemInfo['i_name'];

        arModule('Lib.Msg')->sendSystemMsg($post['u_id'], '你已经退出项目'.$itemName);

        return $del;
    }

    // 项目成员主动退出项目
    function quit($post)
    {
        $data = array(
            'i_id' => $post['i_id'],
            'u_id' => $post['u_id'],
        );

        $quit = U_item_taskModel::model()->getDb()
            ->where($data)
            ->update(array('stay' => -1));

        //查询项目名称
        $itemInfo = U_itemsModel::model()->getInfo($post['i_id']);
        $itemName = $itemInfo['i_name'];

        arModule('Lib.Msg')->sendSystemMsg($post['u_id'], '你已经退出项目'.$itemName);

        return $quit;

    }

    //判断用户是否是项目成员
    public function judge($post)
    {
        $where = array(
            'i_id' =>$post['i_id'],
            'u_id' =>$post['u_id']
            );

        $judgeResult = U_item_taskModel::model()->getDb()
            ->where($where)
            ->count();

        return $judgeResult;
    }

    // 更新用户所在项目
    public function updateUserTask($iid, $users)
    {
        $allUsers = $this->getAllUsers($iid);
        $allUserIds = array_keys($allUsers);

        foreach ($users as $uid) :
            if (in_array($uid, $allUserIds)) :
                $uidKey = array_search($uid, $allUserIds);
                unset($allUserIds[$uidKey]);
            else :
                $this->addUserToTask($iid, $uid);
            endif;
        endforeach;

        if (!empty($allUserIds)) :
            foreach ($allUserIds as $uid) :
                $this->delUserFromTask($iid, $uid);
            endforeach;
        endif;

    }

    // 添加用户到项目
    public function delUserFromTask($iid, $uid, $stay = U_item_taskModel::STATUS_STAY_OUT, $info = '项目退出通知')
    {
        $condition = array(
            'u_id' => $uid,
            'i_id' => $iid,
        );
        $user = array(
            'u_id' => $uid,
            'i_id' => $iid,
            'update_time' => time(),
            'stay' => $stay,
            'info' => $info,
        );
        // 添加新进入用户
        return U_item_taskModel::model()->getDb()->where($condition)->update($user);

    }

    // 添加用户到项目
    public function addUserToTask($iid, $uid, $info = '项目加入通知')
    {
        $user = array(
            'u_id' => $uid,
            'i_id' => $iid,
            'update_time' => time(),
            'info' => $info,
            'stay' => U_item_taskModel::STATUS_STAY_IN
        );
        // 添加新进入用户
        return U_item_taskModel::model()->getDb()->insert($user);

    }


    // 获取项目里所有的用户 U_item_taskModel::model()->getAllUsers($iid, $stay = U_item_taskModel::STATUS_STAY_IN);
    public function getAllUsers($iid, $stay = U_item_taskModel::STATUS_STAY_IN)
    {
        $condition = array(
            'i_id' => $iid,
            'stay' => $stay,
        );
        $users = U_item_taskModel::model()->getDb()->where($condition)->queryAll('u_id');
        $users = $this->getDetailInfo($users);
        return $users;

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
            $bundle['user'] = U_usersModel::model()->getDb()
                ->select('nickname,id,photo,tel,sex')
                ->where(array('id' => $bundle['u_id']))
                ->queryRow();
            if ($bundle['user']['photo']) :
                $bundle['user']['photo'] = arCfg('UPLOAD_FILE_SERVER_PATH') . $bundle['user']['photo'];
            else :
                $bundle['user']['photo'] = arCfg('DEFAULT_USER_LOG');
            endif;
            /**
             * to do what you want
             * $bundle['????'] = '???';
             */
            return $bundle;
        endif;

        return $bundles;

    }

}
