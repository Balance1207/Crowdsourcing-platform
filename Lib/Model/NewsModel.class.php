<?php

/**
 * Created by PhpStorm.
 * User: HP
 * Date: 2016/12/26
 * Time: 15:27
 */
class NewsModel extends ArModel
{
    // 兼容低版本
    static public function model($class = __CLASS__)
    {
        return parent::model($class);

    }

    // 表名
    public $tableName = 'u_message';


    // 展示消息列表
    public function newsList($condition = array(), $type)
    {
        // 对数据进行分页
        if ($type == 1) {
            $condition['type'] = 1;
        } else {
            $condition['type'] = 2;
        }

        $condition['p_id'] = 0; // 查询主消息
        $totalCount = NewsModel::model()->getDb()
            ->where($condition)
            ->count();

        $page = new Page($totalCount, 10);

        $rows = NewsModel::model()->getDb()
            ->limit($page->limit())
            ->where($condition)
            ->order('msg_id desc')
            ->queryAll();

        // 数据预处理
        $rows = $this->preEditNews($rows);

        // 在数据中加入子消息条数
        foreach ($rows as $index => $value) {
            $children = $this->newsChildrenList($value['msg_id']);
            $rows[$index]['childrenCount'] = $children['childrenCount'];
        }

        $pageHtml = $page->show();

        return array('rows' => $rows, 'pageHtml' => $pageHtml, 'totalCount' => $totalCount);

    }


    // 查询子消息
    public function newsChildrenList($pId)
    {
        // 对数据进行分页
        $childrenCount = NewsModel::model()->getDb()
            ->where(array('p_id' => $pId))
            ->count();

        $children = NewsModel::model()->getDb()
            ->where(array('p_id' => $pId))
            ->order('msg_id asc')
            ->queryAll();

        // 数据预处理
        $children = $this->preEditNews($children);

        return array('children' => $children, 'childrenCount' => $childrenCount);

    }


    // 预处理消息格式
    public function preEditNews(array $rows)
    {
        // 数据预处理
        foreach ($rows as $index => $value) {
            $rows[$index]['send_time'] = date('Y-m-d  H:i:s', $value['send_time']);

            // 关联发件人 收件人
            $sender = $this->names($rows[$index]['sender']);
            $receiver = $this->names($rows[$index]['receiver']);
            $type = $this->type($rows[$index]['type']);

            $rows[$index]['sendername'] = $sender['nickname'];
            $rows[$index]['receivername'] = $receiver['nickname'];
            $rows[$index]['type'] = $type['type'];
        }

        return $rows;

    }

    // 关联查询收件人 发件人
    public function names($name)
    {
        $names = U_usersModel::model()->getDb()
            ->select('nickname')
            ->where(array('id' => $name))
            ->queryRow();

        return $names;

    }


    // 关联查询消息状态
    public function type($id)
    {
        $type = U_message_typeModel::model()->getDb()
            ->select('type')
            ->where(array('type_id' => $id))
            ->queryRow();

        return $type;

    }


    // 删除消息
    public function deleteNews($id)
    {
        $row = U_messageModel::model()->getDb()
            ->where(array('msg_id' => $id))
            ->delete();

        // 同时删除所有子消息
        $list = U_messageModel::model()->getDb()
            ->select('msg_id')
            ->where(array('p_id' => $id))
            ->queryAll();

        foreach ($list as $index => $value) {
            U_messageModel::model()->getDb()
                ->where(array('msg_id' => $value['msg_id']))
                ->delete();
        }

        return $row;

    }


    // 发送消息
    public function sendNews($param)
    {
        // 判断是否发送给全体成员
        if ($param['id'] == 'all') {
            // 添加主消息
            $pNews = NewsModel::model()->getDb()->insert(array(
                'sender' => arModule('Lib.Auth')->getSysUid(),
                'content' => $param['content'],
                'receiver' => 2, // 2表示全体成员
                'send_time' => time(),
                'p_id' => 0,
                'type' => 2,
            ));

            // 查询出当前主消息的id
            $msgID = NewsModel::model()->getDb()
                ->where(array('content' => $param['content']))
                ->queryColumn('msg_id');

            // 遍历出所有的用户
            $users = U_usersModel::model()->getDb()
                ->where(array('id >' => 2))
                ->select('id')
                ->queryAll();

            // 循环发送消息
            foreach ($users as $index => $value) {
                NewsModel::model()->getDb()
                    ->insert(array(
                        'sender' => arModule('Lib.Auth')->getSysUid(),
                        'content' => $param['content'],
                        'receiver' => $value['id'],
                        'send_time' => time(),
                        'p_id' => $msgID,
                        'type' => 2,
                    ));
            }
        } else {
            $pNews = NewsModel::model()->getDb()->insert(array(
                'sender' => arModule('Lib.Auth')->getSysUid(),
                'content' => $param['content'],
                'receiver' => $param['id'],
                'send_time' => time(),
                'p_id' => 0,
                'type' => 2,
            ));
        }

        if ($pNews) {
            return true;
        }

    }

    // 动态展示收件人信息
    public function receiverList($receiver)
    {
        $condition = array('nickname like ' => '%' . $receiver . '%');  // 注意%两边不能有空格

        $receivers = U_usersModel::model()->getDb()
            ->select('nickname,tel,id')
            ->where($condition)
            ->queryAll();

        return $receivers;

    }

}