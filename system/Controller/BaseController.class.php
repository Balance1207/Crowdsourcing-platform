<?php

/**
 * Created by PhpStorm.
 * User: HP
 * Date: 2016/12/7
 * Time: 15:40
 */
class BaseController extends ArController
{
    public $adminUser;

    public function init()
    {
        $this->isLogin();
        $sysUser = S_admin_usersModel::model()
            ->getDb()
            ->where(array('id' => arModule('Lib.Auth')->getSysUid()))
            ->queryRow();
        $this->adminUser = $sysUser;
        Ar::setConfig('system_member', $sysUser);

    }

    // 判断用户是否登陆的跳转页面
    public function isLogin()
    {
        if (!arModule('Lib.Auth')->isLoginAdmin()) :
            $this->redirect('Users/login');
        endif;

    }

}



