<?php
/**
 * Powerd by ArPHP.
 *
 * Model.
 *
 * @author LW
 */

/**
 * Default Model of webapp.
 */
class U_git_usersModel extends ArModel
{

    static public function model($class = __CLASS__)
    {
        return parent::model($class);

    }

    // 表名
    public $tableName = 'u_git_users';

    //给用户分配仓库账号
    public function assign($post)
    {
        //判断操作者是否是仓库管理员
        $setId = U_item_gitModel::model()->check($post['id']);

        if ($setId == $post['u_id']) {

          $data = array(
            'g_id' => $post['id'],
            'u_id' => $post['uid'],
            'pullaccount' => 1,
            'pullpwd' => 1
            );

        $result = U_git_usersModel::model()->getDb()
            ->insert($data);

        arModule('Lib.Msg')->sendSystemMsg($post['uid'], '你可以登录仓库了,账号：1,密码：1');
        return $result;

        }

    }


}