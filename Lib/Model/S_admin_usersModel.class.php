<?php
/**
 * Powerd by ArPHP.
 *
 * Model.
 *
 * @author zjd
 */

/**
 * Default Model of webapp.
 */
class S_admin_usersModel extends ArModel
{

    static public function model($class = __CLASS__)
    {
        return parent::model($class);

    }


    // 表名
    public $tableName = 's_admin_users';

    // 未分配
    CONST ROLE_NOTASSIGNED = 0;
    // system privileges
    CONST ROLE_PLATFORM_ADMIN = 16;
    CONST ROLE_SUPER_ADMIN = 32;
    // 40 司令
    CONST ROLE_SYSTEM_ADMIN = 40;
    // 类型
    static $ROLE_MAP = array(
        0 => '未分配',
        16 => '运营',
        32 => '超级管理员',
        40 => '系统管理员',
    );

    // 注册时检测数据是否已存在
    public function check($param)
    {
        // 得到键
        $keys = array_keys($param);
        $key = $keys[1];

        // 查询数据库中是否存在该值
        $count = S_admin_usersModel::model()->getDb()
            ->where(array($key => $param[$key]))
            ->count();

        return $count;

    }


    // 后台登录功能
    public function login($param)
    {
        $password = $param['password'];
        $username = $param['username'];

        // 判断用户名是否存在
        $row = S_admin_usersModel::model()->getDb()
            ->where(array('username' => $username))
            ->count();
        if (0 == $row) {
            return '用户名不存在！';
        }

        $rows = S_admin_usersModel::model()->getDb()
            ->select('id,username,password,salt,status')
            ->where(array('username' => $username))
            ->queryRow();

        // 判断用户账号是否已审核
        if (0 == $rows['status']) {
            return '用户名未审核！';
        }

        // 判断密码是否正确
        $password = md5(md5($password) . $rows['salt']);
        if ($rows['password'] != $password) {
            return '密码错误！';
        }

        // 加入登录时间，ip
        $registerIp = ip2long(arComp('tools.util')->getClientIp());
        $row2 = S_admin_usersModel::model()->getDb()
            ->where(array('username' => $username))
            ->update(array(
                'last_login' => time(),
                'last_ip' => $registerIp,
            ));

        if (isset($row2)) {
            arModule('Lib.Auth')->setSysSession($rows);
            return true;
        }

    }


    // 后台注册功能
    public function register($param)
    {
        $salt = arComp('tools.util')->randpw(6);
        $registerIp = ip2long(arComp('tools.util')->getClientIp());
        $password = md5(md5($param['password']) . $salt);

        $row = S_admin_usersModel::model()->getDb()
            ->insert(array(
                'username' => $param['username'],
                'password' => $password,
                'salt' => $salt,
                'register_login' => time(),
                'register_ip' => $registerIp,
                'role_id' => $param['role_id'],
            ));

        // 取出当前生成id
        $id = S_admin_usersModel::model()->getDb()
            ->where(array('username' => $param['username']))
            ->queryColumn('id');

        // 计算员工编号
        $usernum = 'WM' . str_pad($id, 5, "0", STR_PAD_LEFT);

        // 保存员工编号
        $usernum = S_admin_usersModel::model()->getDb()
            ->where(array('id' => $id))
            ->update(array('usernum' => $usernum));

        if (isset($row)) {
            return '注册成功，待管理员审核后即可登录!';
        } else {
            return '注册失败！';
        }

//        try {
//            // 开启事物
//            arComp('db.mysql')->transBegin();
//
//            // 数据库业务逻辑代码。。。。。。。。
//
//            // 提交
//            arComp('db.mysql')->transCommit();
//        } catch (Exception $e) {
//            // 回滚
//            arComp('db.mysql')->transRollBack();
//        }

    }


    // 审核用户
    public function changeStatus($id, $status = 1)
    {
        if (!$this->hasRightOperateSysUser($id)) :
            return 'forbiden';
        endif;

        // 检查是否是超级管理员
        $adminId = S_admin_usersModel::model()->getDb()
            ->where(array('username' => 'admin'))
            ->queryRow();


        if ($adminId['id'] == $id) {
            return 'forbiden';
        }

        $row = S_admin_usersModel::model()->getDb()
            ->where(array('id' => $id))
            ->update(array('status' => $status));

        if ($row) {
            return '操作成功！';
        }

    }


    // 展示后台管理员列表
    public function adminList($condition = array())
    {
        //不查询admin
        $condition['id >'] = 1;
        // 对数据进行分页
        $totalCount = S_admin_usersModel::model()->getDb()
            ->where($condition)
            ->count();
        $page = new Page($totalCount, 10);

        $rows = S_admin_usersModel::model()->getDb()
            ->where($condition)
            ->limit($page->limit())
            ->order('status asc,id asc')
            ->queryAll();

        foreach ($rows as $index => $value) {
            $rows[$index]['register_login'] = date('Y-m-d H:i:s', $value['register_login']);
            $rows[$index]['last_login'] = date('Y-m-d H:i:s', $value['last_login']);
            $rows[$index]['register_ip'] = long2ip($value['register_ip']);
            $rows[$index]['last_ip'] = long2ip($value['last_ip']);
        }

        $pageHtml = $page->show();

        return array('rows' => $rows, 'pageHtml' => $pageHtml, 'totalCount' => $totalCount);

    }


    // 修改后台管理员功能
    public function modify($param)
    {
        if (!$this->hasRightOperateSysUser($param['id'], $param['role_id'])) :
            return 'forbiden';
        endif;

        if ($param['password'] != $param['repassword']) {
            return '确认密码和密码不一致！';
        }

        $row = S_admin_usersModel::model()->getDb()
            ->where(array('username' => $param['username']))
            ->select('id')
            ->queryRow();

        // 如果数据库有数据且传入id和查询id不等，则不重复
        if ($row) {
            if ($param['id'] != $row['id']) {
                return '用户名已存在！';
            }
        }

        $salt = arComp('tools.util')->randpw(6);
        $password = md5(md5($param['password']) . $salt);
        $row = S_admin_usersModel::model()->getDb()
            ->where(array('id' => $param['id']))
            ->update(array(
                'username' => $param['username'],
                'password' => $password,
                'salt' => $salt,
                'role_id' => $param['role_id'],
            ));

        if (isset($row)) {
            return '修改成功！';
        } else {
            return '修改失败！';
        }

    }

    // 是否有权限操作用户
    public function hasRightOperateSysUser($id, $toRoleId = '')
    {
        if (!$id) :
            return false;
        endif;

        $sysUser = S_admin_usersModel::model()
            ->getDb()
            ->where(array('id' => $id))
            ->queryRow();
        if ($sysUser['role_id'] >= arCfg('system_member.role_id')) :
            return false;
        else :
            if ($toRoleId && $toRoleId >= arCfg('system_member.role_id')) :
                return false;
            else :
                return true;
            endif;
        endif;

    }

    // 修改角色信息
    public function editAdminRole($param)
    {
        if (!$this->hasRightOperateSysUser($param['id'], $param['role_id'])) :
            return 'forbiden';
        endif;

        $result = S_admin_usersModel::model()->getDb()
            ->where(array('id' => $param['id']))
            ->update(array('role_id' => $param['role_id']));

        return $result;

    }


    // 提示未审核消息数
    public function uncheckedNum()
    {
        $num = S_admin_usersModel::model()->getDb()
            ->where(array('status' => 0))
            ->count();

        return $num;

    }


    // 删除后台管理员
    public function deleteAdminUsers($id)
    {
        if (!$this->hasRightOperateSysUser($id)) :
            return 'forbiden';
        endif;


        $row = S_admin_usersModel::model()->getDb()
            ->where(array('id' => $id))
            ->delete();

        return $row;

    }

    // 根据管理员查看名称
    public function getName($id)
    {
        $user = S_admin_usersModel::model()->getDb()
            ->select('username')
            ->where(array('id' => $id))
            ->queryRow();
        return $user['username'];

    }

}