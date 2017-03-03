<?php

/**
 * Created by PhpStorm.
 * User: HP
 * Date: 2016/12/20
 * Time: 15:54
 */
class S_admin_roleModel extends ArModel
{
    static public function model($class = __CLASS__)
    {
        return parent::model($class);

    }


    // 表名
    public $tableName = 's_admin_role';


    // 展示角色信息
    public function adminRole()
    {
        $roles = S_admin_roleModel::model()->getDb()
            ->select('role_id,role_name')
            ->queryAll();

        return $roles;

    }


}