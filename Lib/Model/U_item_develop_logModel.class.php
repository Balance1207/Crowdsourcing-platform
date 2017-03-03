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
class U_item_develop_logModel extends ArModel
{

    static public function model($class = __CLASS__)
    {
        return parent::model($class);

    }

    // 表名
    public $tableName = 'u_item_develop_log';


    //用户提交项目评估
    public function sendAssess($post)
    {
        $data = array(
            'u_id' => $post['u_id'],
            'i_id' => $post['id'],
            'content' => $post['content'],
            'Date' => date('y-m-d h:i:s',time()),
            'type' => 6
            );

        $Msg = U_item_develop_logModel::model()->getDb()
            ->insert($data);

        return $Msg;

    }



}