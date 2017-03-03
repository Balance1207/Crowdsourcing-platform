<?php
namespace Lib\Module;
// 用户模块中间件
class ItemModule
{
    // 是否有用户 arModule('Lib.Item')->hasUser($iid, $uid)
    public function hasUser($iid, $uid = '')
    {
        if ($uid == '') :
            $uid = arModule('Lib.User')->getUid();
        endif;
        $condition = array(
            'i_id' => $iid,
            'u_id' => $uid,
            'stay' => \U_item_taskModel::STATUS_STAY_IN
        );
        $hasnum = \U_item_taskModel::model()->getDb()->where($condition)->count();
        if ($hasnum > 0) :
            return true;
        else :
            return false;
        endif;

    }

    // 是否申请了项目 arModule('Lib.Item')->hasApply($iid, $uid)
    public function hasApply($iid, $uid = '')
    {
        if ($uid == '') :
            $uid = arModule('Lib.User')->getUid();
        endif;

        $condition = array(
            'i_id' => $iid,
            'u_id' => $uid,
            'type' => \U_item_task_applyModel::TYPE_APPLYED
        );
        $hasnum = \U_item_task_applyModel::model()->getDb()->where($condition)->count();
        if ($hasnum > 0) :
            return true;
        else :
            return false;
        endif;

    }

    // 添加申请信息 arModule('Lib.Item')->addApply($iid, $uid, $msg)
    public function addApply($iid, $uid, $msg)
    {
        $apply = array(
            'i_id' => $iid,
            'u_id' => $uid,
            'apply_msg' => $msg,
            'atime' => time(),
            'type' => \U_item_task_applyModel::TYPE_APPLYED
        );

        $addTrue = \U_item_task_applyModel::model()->getDb()->insert($apply);

        if ($addTrue) :
            $itemName = \U_itemsModel::model()->getDb()
                ->where(array('id' => $iid))
                ->queryColumn('i_name');
            arModule('Lib.Msg')->sendSystemMsg($uid, '你已经提交申请' . $itemName);
        endif;
        return $addTrue;

    }

    // 添加用户 arModule('Lib.Item')->addUser($iid, $uid, $msg = '')
    public function addUser($iid, $uid, $msg = '')
    {
        $apply = array(
            'i_id' => $iid,
            'u_id' => $uid,
            'type' => \U_item_task_applyModel::TYPE_APPLYED
        );

        $aid = \U_item_task_applyModel::model()->getDb()->where($apply)->queryColumn('id');

        \U_item_task_applyModel::model()->getDb()
            ->where(array('id' => $aid))
            ->update(array('type' => \U_item_task_applyModel::TYPE_APPLY_SUCCESS));

        \U_item_taskModel::model()->addUserToTask($iid, $uid, $msg);

        $itemName = \U_itemsModel::model()->getDb()
            ->where(array('id' => $iid))
            ->queryColumn('i_name');

        arModule('Lib.Msg')->sendSystemMsg($uid, '你已经加入项目' . $itemName);

        return true;

    }

    // 取消申请 arModule('Lib.Item')->applyCancel($iid, $uid)
    public function applyCancel($iid, $uid)
    {
        $apply = array(
            'i_id' => $iid,
            'u_id' => $uid,
            'type' => \U_item_task_applyModel::TYPE_APPLYED
        );

        $aid = \U_item_task_applyModel::model()->getDb()->where($apply)->queryColumn('id');

        $cancelTrue = \U_item_task_applyModel::model()->getDb()
            ->where(array('id' => $aid))
            ->update(array('type' => \U_item_task_applyModel::TYPE_APPLY_CANCEL));

        if ($cancelTrue) :
            $itemName = \U_itemsModel::model()->getDb()
                ->where(array('id' => $iid))
                ->queryColumn('i_name');
            arModule('Lib.Msg')->sendSystemMsg($uid, '你已经取消申请开发项目' . $itemName);
        endif;
        return $cancelTrue;

    }

    // 退出项目 arModule('Lib.Item')->quit($iid, $uid)
    public function quit($iid, $uid)
    {
        $apply = array(
            'i_id' => $iid,
            'u_id' => $uid,
            'type' => \U_item_task_applyModel::TYPE_APPLY_SUCCESS
        );

        \U_item_taskModel::model()->delUserFromTask($iid, $uid);
        $aid = \U_item_task_applyModel::model()->getDb()->where($apply)->queryColumn('id');
        \U_item_task_applyModel::model()->getDb()
            ->where(array('id' => $aid))
            ->update(array('type' => \U_item_task_applyModel::TYPE_USER_EXIT));

        $itemName = \U_itemsModel::model()->getDb()
            ->where(array('id' => $iid))
            ->queryColumn('i_name');
        arModule('Lib.Msg')->sendSystemMsg($uid, '你已经退出项目' . $itemName);
        return true;

    }

    // 项目群组成员 arModule('Lib.Item')->group($iid)
    public function group($iid)
    {
        $group = array(
            'i_id' => $iid,
            'stay' => \U_item_taskModel::STATUS_STAY_IN
        );
        // 项目的任务
        $tracks = \U_item_trackModel::model()->getDb()
            ->where(array('iid' => $iid))
            ->queryAll();
        if ($tracks) :
            $groups['tracks'] = \U_item_trackModel::model()->getDetailInfo($tracks);
        endif;
        // 项目的成员
        $uids = \U_item_taskModel::model()->getDb()
            ->where($group)
            ->group('u_id')
            ->queryAll();
        if ($uids) :
            $groups['users'] = \U_item_taskModel::model()->getDetailInfo($uids);
        endif;

        if ($groups) :
            return $groups;
        else :
            return false;
        endif;

    }

    // 项目详情 arModule('Lib.Item')->info($iid);
    public function info($iid)
    {
        $result = \U_itemsModel::model()->getDb()
            ->select('id,i_name,img,audit,publisher,money,contractDate,money,releaseDate,requirement,days')
            ->where(array('id' => $iid))
            ->queryRow();

        // 项目成员
        $condition = array(
            'i_id' => $iid,
            'stay' => \U_item_taskModel::STATUS_STAY_IN
        );

        $itemUsers = \U_item_taskModel::model()->getDb()
            ->select('u_id')
            ->where($condition)
            ->group('u_id')
            ->queryAll('u_id');
        $users = \U_usersModel::model()->getDb()
            ->where(array('id' => array_keys($itemUsers)))
            ->queryAll();
        // var_dump($itemUsers);
        // exit;
        $result['itemUsers'] = \U_usersModel::model()->getDetailInfo($users);
        // var_dump($itemUsers);
        // exit;
        // 查询项目发布人
        $result['publisher'] = \U_usersModel::model()->getPublisher($result['publisher']);

        //项目二维码图片
        if ($result['img']) {
            $result['img'] = arCfg('UPLOAD_FILE_SERVER_PATH') . $result['img'];
        } else {
            $result['img'] = arCfg('DEFAULT_USER_LOG');
        }

        // 项目审核状态
        $result['audit'] = \U_itemsModel::$TYPE[$result['audit']];

        return $result;

    }

    // 用户参与的项目 arModule('Lib.Item')->myItems($uid);
    public function myItems($uid)
    {
        $condition = array(
            'u_id' => $uid,
            'stay' => \U_item_taskModel::STATUS_STAY_IN
        );
        $items = \U_item_taskModel::model()->getDb()
            ->select('i_id as id')
            ->where($condition)
            ->queryAll();

        $items = \U_itemsModel::model()->getDetailInfo($items);
        // $items['count'] = 1;
        return $items;
    }

}
