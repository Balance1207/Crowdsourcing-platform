<?php

/**
 * Created by PhpStorm.
 * User: HP
 * Date: 2016/12/26
 * Time: 16:19
 */
class U_message_typeModel extends ArModel
{
    static public function model($class = __CLASS__)
    {
        return parent::model($class);

    }


    // 表名
    public $tableName = 'u_message_type';


}