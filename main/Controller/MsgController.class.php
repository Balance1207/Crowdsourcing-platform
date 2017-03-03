<?php

/**
 * Created by PhpStorm.
 * User: HP
 * Date: 2016/11/28
 * Time: 15:50
 */
class MsgController extends BaseController
{

    // 初始化方法
    public function init()
    {
        parent::init();
    }

    // 消息
    public function msgAction()
    {
        $this->assign(array('page_title' => '消息'));
        $this->display('index');

    }


    // 获取消息列表
    public function getMsgListAction()
    {
        // 如果有传参数，表示获取未读消息列表
        $status = arRequest('status');
        if (!empty($status)) {
            $status = 1;
        }

        $data = U_messageModel::model()->getMsgList($status);

        if (!$data) {
            $this->showJsonError('数据库没有保存消息！', 1100);
        } else {
            $msgLists = $data['msgLists'];
            $count = $data['count'];
            $this->showJson($msgLists, $count);
        }

    }


    // 获取用户信息
    public function getUserInfoAction()
    {
        $nowUser = U_messageModel::model()->getUserInfo();

        if (!$nowUser) {
            $this->showJsonError('用户在数据库中不存在！', 1101);
        } else {
            $this->showJson($nowUser);
        }

    }


    // 发送消息事件
    public function sendMessageAction()
    {
        $post = arRequest();

        if (!isset($post['receiver'])) {
            $this->showJsonError('没有传入参数receiver！', 1102);
            return;
        } elseif (!isset($post['content'])) {
            $this->showJsonError('没有传入参数content！', 1103);
            return;
        } else {
            $msgId = U_messageModel::model()->sendMessage($post);
        }

        if (!$msgId) {
            $this->showJsonError('发送消息失败！', 1104);
        } else {
            $this->showJsonSuccess('发送消息成功！');
        }

    }


    // 改变消息状态 1表示未读 0表示已读 －1表示删除
    public function changeStatusAction()
    {
        $status = 0;

        $id = arRequest('msg_id');

        if (isset($id)) {
            $status = U_messageModel::model()->changeStatus($id); 
        } else {
            $this->showJsonError('没有传入参数msg_id', 1105);
            return;
        }

        if (!$status) {
            $this->showJsonError('没有该条消息或改变信息状态失败！', 1106);
        } else {
            $this->showJsonSuccess('改变信息状态成功！');
        }

    }


    // 未读信息条数
    public function unreadMessageAction()
    {
        $num = U_messageModel::model()->unreadMessage();

        if (!isset($num)) {
            $this->showJsonError('获取未读信息条数失败！', 1107);
        } else {
            $this->showJson($num);
        }

    }


    // 删除消息
    public function deleteMessageAction()
    {
        $id = arRequest('msg_id');

        if (isset($id)) {
            $row = U_messageModel::model()->deleteMessage($id);
        } else {
            $this->showJsonError('没有传入参数msg_id', 1108);
            return;
        }

        if (!$row) {
            $this->showJsonError('没有该条消息或删除消息失败！', 1109);
        } else {
            $this->showJsonSuccess('删除消息成功！');
        }

    }


    // 列出模糊查询用户名
    public function listNameAction()
    {
        $receiver = arRequest('receiver');

        if (isset($receiver)) {
            $rows = U_messageModel::model()->listName($receiver);
        } else {
            $this->showJsonError('没有传入参数receiver！', 1110);
            return;
        }

        if ($rows) {
            $this->showJson($rows);
        } else {
            $this->showJsonError('用户名不存在！', 1111);
        }

    }


}