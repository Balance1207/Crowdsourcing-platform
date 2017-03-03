<?php

/**
 * Created by PhpStorm.
 * User: HP
 * Date: 2016/12/12
 * Time: 12:02
 */
class UsersController extends ArController
{
    // 初始化方法
    public function init()
    {
        // 调用layer msg cart插件
        arSeg(array(
                'loader' => array(
                    'plugin' => '',
                    'this' => $this
                )
            )
        );

    }


    // 后台登录
    public function loginAction()
    {
        $param = arRequest();
        if (arPost()) {
            if (strtolower($param['verify']) != $_SESSION['code']) {
                $result = '验证码错误';
                $this->showJsonError($result);
                return;
            }

            $result = S_admin_usersModel::model()->login($param);

            if ($result === true) {
                $this->showJson(array('ret_msg' => '登录成功', 'ret_code' => '1000', 'success' => "1", 'url' => arU('Index/index')));
            } else {
                $this->showJsonError($result);
                return;
            }
        } else {
            arModule('Lib.Auth')->clearSysSession();
            $this->assign(array('title' => '欢迎登录后台管理系统'));
            $this->display('login');
        }

    }


    // 后台注册
    public function registerAction()
    {
        $param = arRequest();
        if (arPost()) {
            if (strtolower($param['verify']) != $_SESSION['code']) {
                $result = '验证码错误';
                $this->showJsonError($result);
                return;
            }

            $result = S_admin_usersModel::model()->register($param);
            if ($result === '注册成功，待管理员审核后即可登录!') {
                $this->showJson(array('ret_msg' => $result, 'ret_code' => '1000', 'success' => "1", 'url' => arU('Users/login')));
            } else {
                $this->showJsonError($result);
            }
        } else {
            $this->assign(array('cssInsertBundles' => array('validate_jd')));
            $this->assign(array('title' => '注册管理员'));
            $this->display('register');
        }

    }


    // 注册时检测数据是否已存在
    public function checkAction()
    {
        $post = arRequest();

        if (isset($post['username'])) {
            $row = S_admin_usersModel::model()->check($post);
        } else {
            $this->showJsonError('没有传入参数username！', 1100);
            return;
        }

        if (!$row) {
//            $this->showJsonError('用户名不存在！', 1101);
            echo "true";

        } else {
//            $this->showJsonError('用户名已存在！', 1102);
            echo "false";
        }

    }


    // 生成验证码
    public function confirmAction()
    {
        $_vc = new ValidateCode(); // 实例化一个对象
        $_vc->doimg();
        $_SESSION['code'] = $_vc->getCode();// 验证码保存到SESSION中

    }

}