<?php

/**
 * Created by PhpStorm.
 * User: HP
 * Date: 2016/12/22
 * Time: 17:11
 */
class IndexModel extends ArModel
{
    static public function model($class = __CLASS__)
    {
        return parent::model($class);

    }


    // 表名
    public $tableName = 's_admin_users';

    // 获取登录信息
    public function getInfo()
    {
        $adminUser = arComp('list.session')->get('adminUser');

        $rows = IndexModel::model()->getDb()
            ->where(array('username' => $adminUser))
            ->queryAll();

        foreach ($rows as $index => $value) {
            $rows[$index]['register_login'] = date('Y-m-d H:i:s', $value['register_login']);
            $rows[$index]['last_login'] = date('Y-m-d H:i:s', $value['last_login']);
            $rows[$index]['register_ip'] = long2ip($value['register_ip']);
            $rows[$index]['last_ip'] = long2ip($value['last_ip']);
        }

        return $rows;

    }


}