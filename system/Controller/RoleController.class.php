<?php

/**
 * Created by PhpStorm.
 * User: HP
 * Date: 2016/12/22
 * Time: 15:40
 */
class RoleController extends BaseController
{
    // 初始化方法
    public function init()
    {
        parent::init();

        // 调用layer msg cart插件
        arSeg(array(
                'loader' => array(
                    'plugin' => '',
                    'this' => $this
                )
            )
        );

    }

    // 展示角色列表
    public function adminRoleAction()
    {
        $this->assign(array('title' => '角色列表'));
        $this->display();
    }


}