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
class U_item_task_applyModel extends ArModel
{

    static public function model($class = __CLASS__)
    {
        return parent::model($class);

    }

    // 表名
    public $tableName = 'u_item_task_apply';

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

    // 用户申请加入项目
    public function apply($post)
    {
        $data = array(
            'i_id' => $post['i_id'],
            'u_id' => $post['u_id'],
            'type' => $post['type'],
            'flag' => 0,
        );

        $apply = U_item_task_applyModel::model()->getDb()
            ->insert($data);

        // 查询项目名称
        $itemInfo = U_itemsModel::model()->getInfo($post['i_id']);
        $itemName = $itemInfo['i_name'];

        arModule('Lib.Msg')->sendSystemMsg($post['u_id'], '你申请加入项目'.$itemName.'提交申请成功，请等待审核');
        return $apply;

    }

    // 审核用户申请
    public function check($post)
    {
        $data = array(
            'i_id' => $post['i_id'],
            'u_id' => $post['u_id'],
        );

        $check = U_item_task_applyModel::model()->getDb()
            ->where($data)
            ->update(array('flag' => $post['flag']));

        // 查询项目名称
        $itemInfo = U_itemsModel::model()->getInfo($post['i_id']);
        $itemName = $itemInfo['i_name'];

        // 申请状态
        $flag = self::$TYPE[$post['flag']];

        arModule('Lib.Msg')->sendSystemMsg($post['u_id'], '你申请加入项目'.$itemName.$flag);
        return $check;

    }
}
