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
class U_item_track_applyModel extends ArModel
{

    static public function model($class = __CLASS__)
    {
        return parent::model($class);

    }

    // 表名
    public $tableName = 'u_item_track_apply';

    // 审核申请状态
    static $TYPE = array(
        0 => '已提交申请',
        1 => '申请成功',
        2 => '申请失败',
        3 => '申请撤销',
        4 => '主动退出项目',
    );
    const TYPE_APPLYED = 0;
    const TYPE_APPLY_SUCCESS = 1;
    const TYPE_APPLY_FAIL = 2;
    const TYPE_APPLY_CANCEL = 3;
    const TYPE_USER_EXIT = 4;

    // 任务待审核申请数量
    public function applyNum($tid)
    {
        $condition = array(
            'tid' => $tid,
            'type' => self::TYPE_APPLYED
        );
        return U_item_track_applyModel::model()->getDb()
            ->where($condition)
            ->count();
    }
}
