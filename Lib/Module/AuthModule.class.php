<?php
/**
 * Powerd by ArPHP.
 *
 * Module.
 *
 * @author ycassnr <ycassnr@gmail.com>
 */
namespace Lib\Module;
/**
 * useage arModule('Lib.Auth')->isLoginAdmin();
*/
// 验证权限类
class AuthModule
{
    // 是否登录管理员 arModule('Lib.Auth')->isLoginAdmin();
    public function isLoginAdmin()
    {
        // return !!arComp('list.session')->get('adminUser');
        return !!$this->getSysUid();

    }

    // 用户是否登录 arModule('Lib.Auth')->isLoginUser();
    public function isLoginUser()
    {
        return arModule('Lib.User')->isLogin();

    }

    // 检查用户是否正常状态 arModule('Lib.Auth')->isLoginUser();
    public function isValidUser($uid)
    {
        $member = \U_usersModel::model()->getDb()
            ->where(array('id' => $uid))
            ->queryRow();
        if ($member['status'] == \U_usersModel::STATUS_APPROVE) :
            return true;
        else :
            return false;
        endif;

    }

    // 获取用户权限 arModule('Lib.Auth')->isLoginUser();
    public function getUserAllAuthOrity($uid)
    {
        $member = \U_usersModel::model()->getDb()
            ->where(array('id' => $uid))
            ->queryRow();
        $memberDetail = \U_usersModel::model()->getDetailInfo($member);
        return $memberDetail['jobs']['auths'];

    }

    // 是否有权限 arModule('Lib.Auth')->isLoginUser();
    public function hasRights($action = '', $auths = '', $checkAdmin = false)
    {
        // var_dump($action, $auths);
        // exit;
        if ($checkAdmin) :
            $ROLE_ID = arComp('list.session')->get('member.rgid');
            switch ($ROLE_ID) {
                case \S_admin_usersModel::ROLE_PLATFORM_ADMIN:
                case \S_admin_usersModel::ROLE_SUPER_ADMIN:
                case \S_admin_usersModel::ROLE_SYSTEM_ADMIN:
                    return true;
                    break;
                default:
                    break;
            }
        endif;

        $hasResult = false;
        if (!$action) :
            $action = arCfg('requestRoute.a_m') . '/'
                    . arCfg('requestRoute.a_c') . '/'
                    . arCfg('requestRoute.a_a');
        endif;
        if (is_array($auths)) :
            foreach ($auths as $auth) :
                if ($auth['action'] == $action) :
                    $hasResult = true;
                    break;
                endif;
            endforeach;
        else :
        endif;
        return $hasResult;

    }

    // 检查是否有系统权限 arModule('Lib.Auth')->isLoginUser();
    public function hasSysRights($rightLevel = '', $only = false)
    {
        if (!$rightLevel) :
            $rightLevel = \S_admin_usersModel::ROLE_PLATFORM_ADMIN;
        endif;

        if ($only) :
            if (arCfg('system_member.role_id') == $rightLevel) :
                return true;
            else :
                return false;
            endif;
        else:
            if (arCfg('system_member.role_id') >= $rightLevel) :
                return true;
            else :
                return false;
            endif;

        endif;

    }

    // 是否系统角色 arModule('Lib.Auth')->isSysRole();
    public function isSysRole()
    {
        if (arCfg('system_member.role_id') >= \S_admin_usersModel::ROLE_PLATFORM_ADMIN):
            return true;
        else :
            return false;
        endif;

    }

    // 是否用户角色 arModule('Lib.Auth')->isUserRole();
    public function isUserRole()
    {
        return !$this->isSysRole();

    }

    // 获取系统用户uid arModule('Lib.Auth')->getSysUid();
    public function getSysUid()
    {
        return arComp('list.session')->get('adminuid');

    }

    /**
     * 设置用户系统session
     * usage arModule('Lib.Auth')->setSysSession($sysuser);
     */
    public function setSysSession($sysuser)
    {
        if (!$sysuser) :
            return false;
        endif;
        if (is_numeric($sysuser)) :
            $sysuser = \S_admin_usersModel::model()->getDb()
                ->where(array('id' => $sysuser))
                ->queryRow();
            if (!$sysuser) :
                return false;
            endif;
        endif;
        arComp('list.session')->set('adminuid', $sysuser['id']);
        return true;

    }

    /**
     * 清除session
     * usage arModule('Lib.Auth')->clearSysSession()
     */
    public function clearSysSession()
    {
        arComp('list.session')->set('adminuid', null);

    }

}
