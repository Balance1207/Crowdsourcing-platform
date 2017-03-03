<?php

/**
 * Created by PhpStorm.
 * User: HP
 * Date: 2016/12/22
 * Time: 15:42
 */
class S_admin_authModel extends ArModel
{
    static public function model($class = __CLASS__)
    {
        return parent::model($class);

    }


    // 表名
    public $tableName = 's_admin_auth';


}