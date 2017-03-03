<?php

/**
 * Created by PhpStorm.
 * User: HP
 * Date: 2016/11/29
 * Time: 15:03
 */
class U_messageModel extends ArModel
{
    // 固定写法
    static public function model($class = __CLASS__)
    {
        return parent::model($class);

    }

    // 表名
    public $tableName = 'u_message';

    // 已读
    const STATUS_READED = 1;
    // 未读信息
    const STATUS_NOTREADED = 0;

    // 普通信息
    const TYPE_NORMAL = 1;
    // 系统信息
    const TYPE_SYSTEM = 2;

    // 获取消息列表
    public function getMsgList($status = 0)
    {
        // 通过当前登录的id匹配收件人和发件人
        $uid = arComp('list.session')->get('u_id');

        // 获取未读消息
        if (1 == $status) {
            $where = "p_id = 0 and (status = 0 or status = 1) and (sender = '$uid' or receiver = '$uid') ";
        } else {
            $where = "p_id = 0 and (sender = '$uid' or receiver = '$uid') ";
        }

        // 对数据进行分页
        $totalCount = U_messageModel::model()->getDb()
            ->where($where)
            ->count();
        $page = new Page($totalCount, 10);

        $msgLists = U_messageModel::model()->getDb()
            ->where($where)
            //->select('msg_id,send_time,sender,receiver,content,status,type')
            ->limit($page->limit())
            ->order('msg_id desc')
            ->queryAll();

        $msgLists = $this->getMsgDetailsInfo($msgLists);

        // 主消息中加入子消息
        foreach ($msgLists as $index => $value) {
            // 获取未读消息
            if (1 == $status) {
                $child = array('p_id' => $value['msg_id'], 'status' => 1);
            } else {
                $child = array('p_id' => $value['msg_id']);
            }

            $msgLists[$index]['children'] = U_messageModel::model()->getDb()
                ->where($child)
                //->select('msg_id,send_time,sender,receiver,content,status')
                ->order('msg_id asc')
                ->queryAll();

            // 对子消息进行users表连接
            $children = $msgLists[$index]['children'];
            foreach ($children as $index2 => $value2) {
                $rows = U_usersModel::model()->getDb()
                    ->select('nickname,photo')
                    ->where(array('id' => $value2['sender']))
                    ->queryRow();
                $children[$index2]['nickname'] = $rows['nickname'];
                $children[$index2]['photo'] = $rows['photo'];
            }
            $children = $this->getMsgDetailsInfo($children);
            $msgLists[$index]['children'] = $children;
        }

        $count['totalCount'] = ceil($totalCount / 10);
        $data = array('msgLists' => $msgLists, 'count' => $count);

        return $data;

    }


    // 获取用户信息
    public function getUserInfo()
    {
        $nowUserId = arComp('list.session')->get('u_id');
        $nowUser = U_usersModel::model()->getDb()
            ->select('id as user_id, nickname, tel,photo')
            ->where(array('id' => $nowUserId))
            ->queryRow();
        if ($nowUser) {
            if ($nowUser['photo']) {
                $nowUser['photo'] = arCfg('UPLOAD_FILE_SERVER_PATH') . $nowUser['photo'];
            } else {
                $nowUser['photo'] = arCfg('DEFAULT_USER_LOG');
            }
        }

        return $nowUser;

    }


    // 发送消息
    public function sendMessage($post)
    {
        $pId = 0;
        // 通过 msg_id 判断是否为主消息
        if (!empty($post['msg_id'])) {
            $pId = $post['msg_id'];
        }
        if ($pId) {
            $senderUid = arComp('list.session')->get('u_id');
            //  $receiverId = U_messageModel::model()->getDb()->where(array('msg_id' => $pId))->queryColumn('sender');
            $currentMsgMainInfo = U_messageModel::model()->getDb()
                ->where(array('msg_id' => $pId))
                ->queryRow();
            $receiverId = $currentMsgMainInfo['sender'];
            if ($receiverId == $senderUid) {
                $receiverId = $currentMsgMainInfo['receiver'];
            }
            $row = U_messageModel::model()->getDb()->insert(array(
                'sender' => $senderUid,
                'content' => $post['content'],
                'receiver' => $receiverId,
                'send_time' => time(),
                'p_id' => $pId,
            ));
        } else {
            $receiverName = $post['receiver'];
            $receiverId = U_usersModel::model()->getDb()->where(array('tel' => $receiverName))->queryColumn('id');
            $row = U_messageModel::model()->getDb()->insert(array(
                'sender' => arComp('list.session')->get('u_id'),
                'content' => $post['content'],
                'receiver' => $receiverId,
                'send_time' => time(),
            ));
        }

        return $row;

    }


    // 改变消息状态
    public function changeStatus($id)
    {
        $row = 1;
        $rows = U_messageModel::model()->getDb()
            //->select('status,p_id')
            ->where(array('msg_id' => $id))
            ->queryRow();

        if (0 == $rows['p_id']) {
            $list = U_messageModel::model()->getDb()
                ->select('msg_id')
                ->where(array('sender != ' => arComp('list.session')->get('u_id'), 'p_id' => $id, 'status' => 1))
                ->queryAll();
            foreach ($list as $index => $value) {
                $row = U_messageModel::model()->getDb()
                    ->where(array('msg_id' => $value['msg_id']))
                    ->update(array('status' => 0));
            }

}
        if (arComp('list.session')->get('u_id') != $rows['sender']) {

            if (1 == $rows['status']) {
                $row = U_messageModel::model()->getDb()
                    ->where(array('msg_id' => $id))
                    ->update(array('status' => 0));
            }
        }

        return $row;

    }


    // 未读信息条数
    public function unreadMessage()
    {
        // 通过当前登录的id匹配收件人和发件人
        $uid = arComp('list.session')->get('u_id');
        $where = "p_id = 0 and (receiver = '$uid') ";
        $whereC = "p_id != 0 and (receiver = '$uid') ";

        // 未读的主消息条数
        $parentMsgs = U_messageModel::model()->getDb()
            ->where(array($where, 'status' => 1))
            ->order('msg_id desc')
            ->queryAll();

        // 未读的主消息条数
        $childrenMsgs = U_messageModel::model()->getDb()
            ->where(array($whereC, 'status' => 1))
            ->order('msg_id desc')
            ->queryAll();
            //var_dump($childrenMsgs);

        foreach ($parentMsgs as &$parent) {
            $parent['children'] = U_messageModel::model()
                ->getDb()
                ->where(array('p_id' => $parent['msg_id']))
                ->order('msg_id asc')
                ->queryRow();
        }

        foreach ($childrenMsgs as &$children) {

            $childrenParentMsg = U_messageModel::model()
                ->getDb()
                ->where(array('msg_id' => $children['p_id']))
                ->queryRow();
            // 一维数组
            $childrenParentMsg = $this->getMsgDetailsInfo($childrenParentMsg);
            $pcMsgs = U_messageModel::model()
                ->getDb()
                ->where(array('p_id' => $childrenParentMsg['msg_id']))
                ->order('msg_id asc')
                ->queryAll();
            $pcMsgs = $this->getMsgDetailsInfo($pcMsgs);
            $childrenParentMsg['children'] = $pcMsgs;
            $children['parent'] = $childrenParentMsg;

        }

        $parentMsgs = $this->getMsgDetailsInfo($parentMsgs);
        $childrenMsgs = $this->getMsgDetailsInfo($childrenMsgs);

        $msgInfo = array('parentMsgs' => $parentMsgs, 'childrenMsgs' => $childrenMsgs, 'parentNum' => count($parentMsgs), 'childrenNum' => count($childrenMsgs));

        return $msgInfo;

    }


    // 删除消息
    public function deleteMessage($id)
    {
        $rows = U_messageModel::model()->getDb()
            ->select('p_id')
            ->where(array('msg_id' => $id))
            ->queryRow();

        // 如果删除主消息，同时删除子消息
        if (0 == $rows['p_id']) {
            $list = U_messageModel::model()->getDb()
                ->select('msg_id')
                ->where(array('p_id' => $id))
                ->queryAll();
            foreach ($list as $index => $value) {
                U_messageModel::model()->getDb()
                    ->where(array('msg_id' => $value['msg_id']))
                    ->delete();
            }
        }
        $row = U_messageModel::model()->getDb()
            ->where(array('msg_id' => $id))
            ->delete();

        if ($row) {
            return true;
        }

    }


    // 列出模糊查询用户名
    public function listName($receiver)
    {
        $condition = array('tel like ' => '%' . $receiver . '%'); // 注意%两边不能有空格

        // 第二种写法
        // $condition ='tel like "%' . $receiver . '%"';

        $totalCount = U_usersModel::model()->getDb()
            ->where($condition)
            ->count();

        $page = new Page($totalCount, 5);

        $rows = U_usersModel::model()->getDb()
            ->select('id,nickname,tel')
            ->where($condition)
            ->limit($page->limit())
            ->queryAll();
        //$page->show();

        return $rows;

    }

    // 获取信息详细信息 传一维数组或二维数组
    public function getMsgDetailsInfo(array $msgs)
    {
        // 判断是否为二位数组
        if (!arComp('validator.validator')->checkMutiArray($msgs)) {
             $children = array(0 => $msgs);
        } else {
             $children = $msgs;
        }

        foreach ($children as $index2 => $value2) {
            $viewTime = time() - $value2['send_time'];
            $children[$index2]['send_time'] = arModule('Func')->viewTime($viewTime);

            if ($value2['sender']) :

                if ($value2['type'] == U_messageModel::TYPE_SYSTEM) :
                    $systemUser = S_admin_usersModel::model()->getDb()
                        ->select('username')
                        ->where(array('id' => $value2['sender']))
                        ->queryRow();
                    $sendUser = array('nickname' => '平台管理:' . $systemUser['username']);
                else :
                    $sendUser = U_usersModel::model()->getDb()
                        ->select('nickname,photo,tel as user_account')
                        ->where(array('id' => $value2['sender']))
                        ->queryRow();
                endif;
            else :
                $sendUser = array('nickname' => '系统消息');
            endif;

            $recUser = U_usersModel::model()->getDb()
                ->select('nickname,photo, tel as user_account')
                ->where(array('id' => $value2['receiver']))
                ->queryRow();

            if (isset($sendUser['photo']) && $sendUser['photo']) {
                $sendUser['photo'] = arCfg('UPLOAD_FILE_SERVER_PATH') . $sendUser['photo'];
            } else {
                $sendUser['photo'] = arCfg('DEFAULT_ADMIN_LOG');
            }

            if ($recUser['photo']) {
                $recUser['photo'] = arCfg('UPLOAD_FILE_SERVER_PATH') . $recUser['photo'];
            } else {
                $recUser['photo'] = arCfg('DEFAULT_USER_LOG');
            }

            $children[$index2]['send_user'] = $sendUser;
            $children[$index2]['rec_user'] = $recUser;

        }

        if (!arComp('validator.validator')->checkMutiArray($msgs)) {
            return $children[0];
        } else {
            return $children;
        }

    }

    //用户间发送消息
    public function sendMsgByUid($post)
    {

        $data = array(
            'sender' => $post['u_id'],
            'receiver' => $post['rec_id'],
            'content' => $post['content'],
            'send_time' => time(),
            'type' => 1
            );

        $Msg = U_messageModel::model()->getDb()
            ->insert($data);

        return $Msg;
    }

}
