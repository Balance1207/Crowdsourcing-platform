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
class ProductModel extends ArModel
{
    // 兼容低版本
    static public function model($class = __CLASS__)
    {
        return parent::model($class);

    }

    // 表名
    public $tableName = 'u_items';


    // 展示项目列表
    public function showList($condition = array(), $status = U_itemsModel::STATUS_APPROVED)
    {
        $condition['status'] = $status;

        // 对数据进行分页
        $totalCount = ProductModel::model()->getDb()
            ->where($condition)
            ->count();
        $page = new Page($totalCount, 10);

        $rows = ProductModel::model()->getDb()
            ->limit($page->limit())
            ->order('audit asc,id asc ')
            ->where($condition)
            ->queryAll();

        // 数据预处理
        $rows = $this->preEditProduct($rows);

        $pageHtml = $page->show();

        return array('rows' => $rows, 'pageHtml' => $pageHtml, 'totalCount' => $totalCount);
    }


    // 预处理项目格式
    public function preEditProduct(array $rows)
    {
        // 数据预处理
        foreach ($rows as $index => $value) {
            $rows[$index]['releaseDate'] = date('Y-m-d', strtotime($value['releaseDate']));
            $rows[$index]['contractDate'] = date('Y-m-d', strtotime($value['contractDate']));

            // 关联查询数据
            $rows[$index]['publisherName'] = $this->joinData('U_usersModel', 'id', $value['publisher'], 'nickname');

            // 项目简介长度处理
            if (mb_strlen($value['requirement'], 'utf8') < 20) {
                $rows[$index]['requirement'] = mb_substr($value['requirement'], 0, 20, 'utf-8');
            } else {
                $rows[$index]['requirement'] = mb_substr($value['requirement'], 0, 13, 'utf-8') . '......';
            }

            // 项目开发成员
            $users = U_item_taskModel::model()->getAllUsers($value['id'], $stay);
            $rows[$index]['users'] = $users;

        }

        return $rows;

    }


    // 关联查询数据
    public function joinData($model, $condition, $where, $select)
    {
        $joinData = $model::model()->getDb()
            ->where(array("$condition" => $where))
            ->queryColumn("$select");

        return $joinData;

    }


    // 删除项目
    public function deleteProduct($id)
    {
        $row = ProductModel::model()->getDb()
            ->where(array('id' => $id))
            ->delete();

        // u_item_task中的该项目记录删除
        U_item_taskModel::model()->getDb()
            ->where(array('i_id' => $id))
            ->delete();

        if ($row) {
            return $row;
        }

    }


    // 移除用户,-1表废弃状态
    public function removeProduct($id, $online)
    {
        $row = ProductModel::model()->getDb()
            ->where(array('id' => $id))
            ->update(array('online' => $online));

        if ($row) {
            return $row;
        }

    }


    // 发布项目时查询数据
    public function addProduct($param)
    {
        // 预处理参数
        if (isset($param['users'])) {
            $users = $param['users'];
            $users = array_unique(array_filter($users));
            $users = implode(',', $users);

            // 对新添加的项目成员发送提示消息
            $item = $param['i_name'];
            if (count($param['users']) != 0) {
                $this->noticeMsg($item, $param['users'], 2); // 2表添加
            }
        } else {
            $users = "";
        }

        // 对添加项目时成员为空进行处理
        $add = ProductModel::model()->getDb()
            ->insert(array(
                "i_name" => $param['i_name'],
                "money" => $param['money'],
                "contractDate" => $param['contractDate'],
                "status" => $param['status'],
                "publisher" => $param['publisher'],
                "releaseDate" => $param['releaseDate'],
                "days" => $param['days'],
                "online" => $param['online'],
                "users" => $users,
                "requirement" => $param['content'],
            ));

        // 查出当前项目id
        $itemId = ProductModel::model()->getDb()
            ->where(array("i_name" => $param['i_name'], "publisher" => $param['publisher']))
            ->queryColumn('id');

        // 将当前成员添加到u_item_task中
        if (isset($param['users']) && is_array($param['users'])) :
            foreach ($param['users'] as $value) {
                U_item_taskModel::model()->getDb()
                    ->insert(array(
                        'i_id' => $itemId,
                        'u_id' => $value,
                        'stay' => 1
                    ));
            }
        endif;

        if ($add) {
            return true;
        }
    }


    // 发布项目时查询数据
    public function updateProduct($param)
    {
        if (!isset($param['users'])) :
            $param['users'] = array();
        endif;
        // 更新加入用户信息
        U_item_taskModel::model()->updateUserTask($param['itemId'], $param['users']);

        // 预处理参数
        if (isset($param['users'])) {
            $users = $param['users'];
            $users = array_unique(array_filter($users));
            $users = implode(',', $users);
        } else {
            $users = array();
        }

        // 对新添加的项目成员发送提示消息
        $item = $param['i_name'];

        // 查询修改前的成员
        $preUsers = ProductModel::model()->getDb()
            ->where(array('id' => $param['itemId']))
            ->queryColumn('users');
        $preUsers = explode(',', $preUsers);
        $preUsers = array_unique(array_filter($preUsers));

        // 计算添加，移出的人
        if (isset($param['users'])) {
            $addUsers = (array_values(array_diff($param['users'], $preUsers)));
            $removeUsers = (array_values(array_diff($preUsers, $param['users'])));
        } else {
            $addUsers = (array_values(array_diff($users, $preUsers)));
            $removeUsers = (array_values(array_diff($preUsers, $users)));
        }

        // 将新添加的成员添加到u_item_task中
        // foreach ($addUsers as $value) {
        //     U_item_taskModel::model()->getDb()
        //         ->insert(array(
        //             'i_id' => $param['itemId'],
        //             'u_id' => $value,
        //             'stay' => 1
        //         ));
        // }


        // 将移除的人在u_item_task中状态改为0
        // foreach ($removeUsers as $v) {
        //     U_item_taskModel::model()->getDb()
        //         ->where(array(
        //             'i_id' => $param['itemId'],
        //             'u_id' => $v,
        //         ))
        //         ->update(array('stay' => 0));
        // }

        if (count($addUsers) != 0) {
            $this->noticeMsg($item, $addUsers, 2); // 2表示添加
        }
        if (count($removeUsers) != 0) {
            $this->noticeMsg($item, $removeUsers, 1); // 1表示删除
        }

        // 在更新前将成员字段从数组转为字符串
        if (!isset($param['users'])) {
            $users = "";
        }

        $add = ProductModel::model()->getDb()
            ->where(array('id' => $param['itemId']))
            ->update(array(
                "i_name" => $param['i_name'],
                "money" => $param['money'],
                "contractDate" => $param['contractDate'],
                "status" => $param['status'],
                "publisher" => $param['publisher'],
                "releaseDate" => $param['releaseDate'],
                "days" => $param['days'],
                "online" => $param['online'],
                "users" => $users,
                "requirement" => $param['content'],
            ));

        if ($add) {
            return true;
        }
    }

    // 发布修改项目时成员变动提示信息
    public function noticeMsg($item, $users, $num)
    {
        // 对新添加的项目成员发送提示消息
        foreach ($users as $value) {
            $name = $this->joinData('U_usersModel', 'id', $value, 'nickname');

            // 1表示删除人 2表示添加人
            switch ($num) {
                case 1:
                    $message = "亲爱的(【{$name}】，你已经离开了项目【{$item}】,期待下次合作！";
                    break;
                case 2:
                    $message = "亲爱的【{$name}】，你已经加入了项目【{$item}】,请按时完成任务！";
                    break;
                case 3:
                    $message = "亲爱的【{$name}】，恭喜你，你申请发布项目【{$item}】,已经通过审核！";
                    break;
                case 4:
                    $message = "亲爱的【{$name}】，很遗憾，你申请发布项目【{$item}】,没有通过审核！";
                    break;
                case 5:
                    $message = "亲爱的【{$name}】，很遗憾，你申请加入项目【{$item}】,没有通过审核！";
                    break;
            }

//            NewsModel::model()->getDb()
//                ->insert(array(
//                    'sender' => 1, // 1表示超级管理员
//                    'content' => $message,
//                    'receiver' => $value,
//                    'send_time' => time(),
//                    'p_id' => 0,
//                    'type' => 2,
//                ));

            arModule('Lib.Msg')->sendSystemMsg($value, $message, ''); //arU('/main/msg/sidf',array('id'=>id));

        }

    }

    // 发布项目时查询数据
    public function checkProduct()
    {
        // 查询开发进度
        $statusType = U_item_status_typeModel::model()->getDb()
            ->queryAll();

        // 查询出所有人
        $users = U_usersModel::model()->getDb()
            ->where(array('id>' => 2))
            ->select('nickname,tel,id')
            ->queryAll();

        return array('statusType' => $statusType, 'users' => $users);

    }


    // 回显项目数据
    public function preShowList($id)
    {
        $list = ProductModel::model()->getDb()
            ->where(array('id' => $id))
            ->queryRow();

        $list['publisherName'] = $this->joinData('U_usersModel', 'id', $list['publisher'], 'nickname');
        $list['users'] = array_unique(explode(',', $list['users']));

        return $list;

    }


    // 审核项目状态
    public function changeStatus($id, $audit = 1, $iName, $publisher)
    {
        $row = ProductModel::model()->getDb()
            ->where(array('id' => $id))
            ->update(array('status' => $audit));

        // 将中文乱码还原
        $iName = urldecode($iName);
        $publisherArray = array($publisher);

        if ($audit == 1) {
            $this->noticeMsg($iName, $publisherArray, 3); // 3表示审核项目通过
        } elseif ($audit == 0) {
            $this->noticeMsg($iName, $publisherArray, 4); // 4表示项目未通过审核
        }

        if ($row) {
            return true;
        }

    }


    // 提示未审核项目数
    public function uncheckedNum()
    {
        $num = ProductModel::model()->getDb()
            ->where(array('audit' => 0))
            ->count();

        return $num;

    }


    // 提示未审核个人申请项目数
    public function uncheckedPersonalApply()
    {
        $num = U_item_task_applyModel::model()->getDb()
            ->where(array('flag' => 0))
            ->count();

        return $num;

    }


    // 个人申请项目审核
    public function personalApply($condition = array(), $type)
    {
        $condition['type'] = $type;
        // 对数据进行分页
        $totalCount = U_item_task_applyModel::model()->getDb()
            ->where($condition)
            ->count();
        $page = new Page($totalCount, 10);

        $rows = U_item_task_applyModel::model()->getDb()
            ->limit($page->limit())
            ->order('flag asc,id asc ')
            ->where($condition)
            ->queryAll();

        // 数据预处理
        foreach ($rows as $index => $value) {
            // 关联查询数据
            $rows[$index]['i_id_name'] = $this->joinData('U_usersModel', 'id', $value['u_id'], 'nickname');
            $rows[$index]['u_id_name'] = $this->joinData('U_itemsModel', 'id', $value['i_id'], 'i_name');
        }

        $pageHtml = $page->show();

        return array('rows' => $rows, 'pageHtml' => $pageHtml, 'totalCount' => $totalCount);
    }


    // 个人申请项目消息回显
    public function personalApplyView($id)
    {
        $row = U_item_task_applyModel::model()->getDb()
            ->where(array('id' => $id))
            ->queryRow();

        // 数据预处理
        foreach ($row as $value) {
            // 关联查询数据
            $row['i_id_name'] = $this->joinData('U_usersModel', 'id', $row['u_id'], 'nickname');
            $row['u_id_name'] = $this->joinData('U_itemsModel', 'id', $row['i_id'], 'i_name');
        }

        if ($row) {
            return $row;
        }

    }


    // 个人项目审核提交
    public function personalApplySubmit($param)
    {
        $row = U_item_task_applyModel::model()->getDb()
            ->where(array('id' => $param['id']))
            ->update(array(
                'type' => $param['type'],
                'reply_msg' => $param['reply_msg'],
            ));

        // 处理其他关联表
        $apply = U_item_task_applyModel::model()->getDb()
            ->where(array('id' => $param['id']))
            ->queryRow();

        $item = U_itemsModel::model()->getDb()
            ->where(array('id' => $apply['u_id']))
            ->queryRow();

        //对user字段处理
        $users = explode(',', $item['users']);
        $newUser = explode(',', $apply['i_id']);
        $users = array_filter(array_unique(array_merge($users, $newUser)));
        $users = implode(',', $users);

        // 如果flag为1，在项目中添加成员
        if ($param['type'] == U_item_task_applyModel::TYPE_APPLY_SUCCESS) {
            // 将新申请成员保存到u_item项目中
            U_itemsModel::model()->getDb()
                ->where(array('id' => $apply['u_id']))
                ->update(array('users' => $users));

            // 将新申请成员添加到u_item_task中
            U_item_taskModel::model()->getDb()
                ->insert(array(
                    'i_id' => $apply['i_id'],
                    'u_id' => $apply['u_id'],
                    'stay' => 1
                ));

            // 发送系统提示消息
            $this->noticeMsg($item['i_name'], $newUser, 2);
        } elseif ($param['type'] == U_item_task_applyModel::TYPE_APPLY_FAIL) {
            // 发送系统提示消息
            $this->noticeMsg($item['i_name'], $newUser, 5);
        }

        if (isset($row)) {
            return true;
        }

    }


    // 删除个人项目申请列表
    public function deletePersonalApply($id)
    {
        $row = U_item_task_applyModel::model()->getDb()
            ->where(array('id' => $id))
            ->delete();

        if ($row) {
            return $row;
        }

    }


}