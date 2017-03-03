<?php
/**
 * Powerd by ArPHP.
 *
 * Model.
 *
 * Date: 2017/2/17
 *
 * @author wdn
 */

class U_job_authorityModel extends ArModel{
    static public function model($class = __CLASS__)
    {
        return parent::model($class);

    }

    // 表名
    public $tableName = 'u_job_authority';

   //添加权限
    public function add($data){
        $result = U_job_authorityModel::model()->getDb()->insert(array(
            'jid' => $data['jid'],
            'lids' => $data['lids'],
            ));
        return $data;
    }

    //查询是否存在部门对应的权限
    public function findAuth($jid){
        $data = U_job_authorityModel::model()->getDb()->where(array('jid' => $jid))->queryRow();
        return $data;
    }

    //修改
    public function update($data){
        $result = U_job_authorityModel::model()->getDb()->where(array('jid' => $data['jid']))->update(array(
            'jid' => $data['jid'],
            'lids' => $data['lids'],
            ));
        if (isset($result)) {
            return true;
        }
    }
    

}