<?php

/**
 * Created by PhpStorm.
 * User: HP
 * Date: 2016/12/7
 * Time: 15:40
 */
class BaseController extends ArController
{
    public $uid;
    public function init()
    {
        $this->isLogin();
        $this->uid = arComp('list.session')->get('u_id');

    }

    // 判断用户是否登陆的跳转页面
    public function isLogin()
    {
        if (empty($_SESSION['u_id'])) {
            $this->redirect('Index/index');
        }

    }

}
