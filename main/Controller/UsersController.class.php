<?php

/**
 * Created by PhpStorm.
 * User: HP
 * Date: 2016/12/7
 * Time: 16:45
 */
class UsersController extends ArController
{
    // 初始化方法
    public function init()
    {
        // 调用layer msg cart插件
        arSeg(array(
                'loader' => array(
                    'plugin' => 'layer',
                    'this' => $this
                )
            )
        );

        $this->assign(array('cssInsertBundles' => array('bootstrap.min')));
        $this->assign(array('cssInsertBundles' => array('style')));
        $this->assign(array('jsInsertBundles' => array('bootstrap.min')));
        $this->assign(array('jsInsertBundles' => array('script', 'personalMsg')));

    }


    // 注册时检测数据是否已存在
    public function checkAction()
    {
        $post = arRequest();

        if (isset($post['nickname']) || isset($post['tel'])) {
            $row = U_usersModel::model()->check($post);
        } else {
            $this->showJsonError('没有传入参数nickname或tel！', 1200);
            return;
        }

        if (!$row) {
            if (arRequest('tel')) {
                $this->showJsonError('手机号码不存在！', 1201);
            } elseif (arRequest('nickname')) {
                $this->showJsonError('用户名不存在！', 1202);
            }
        } else {
            if (arRequest('tel')) {
                $this->showJsonError('手机号码已存在！', 1203);
            } elseif (arRequest('nickname')) {
                $this->showJsonError('用户名已存在！', 1204);
            }
        }

    }


    // 后台直接添加用户
    public function addUserAction()
    {
        $this->display('add');
    }

    // 个人主页
    // public function userAction()
    // {
    //     $this->assign(array('page_title' => '个人主页'));
    //     $this->display();
    // }

    // 用户修改个人信息
    // public function updateInfoAction()
    // {
    //     $this->assign(array('page_title' => '修改个人信息'));
    //     $this->display(); 
    // }


}