<?php
/**
 * Powerd by ArPHP.
 *
 * Controller.
 *
 * @author ycassnr <ycassnr@gmail.com>
 */

class IndexController extends ArController
{
    // 初始化方法
    public function init()
    {
        // 调用layer msg cart插件
        arSeg(array(
            'loader' => array(
                'plugin' => '',
                'this'   => $this,
            ),
        )
        );

    }

	//首页
	public function indexAction()
	{
		$this->display('index');
	}

    // 当前用户的项目列表
    public function showlistAction()
    {
        $resultItem = U_itemsModel::model()->showList();

        // if ($resultItem) {
        //     $this->showJson('$resultItem');
        // } else {
        //     $this->showJsonError('查询失败',1401);
        // }
        $this->assign(array('resultItems' => $resultItem));
        $this->display();
    }

    // 项目列表
    public function itemsAction()
    {
        $resultItem = U_itemsModel::model()->showList();

        $this->assign(array('resultItems' => $resultItem));
        $this->display();

    }

    // 生成验证码
    public function confirmAction()
    {
        session_start();
        $_vc = new ValidateCode(); // 实例化一个对象
        $_vc->doimg();
        $_SESSION['code'] = $_vc->getCode(); // 验证码保存到SESSION中

    }

    // 用户登录
    public function loginAction()
    {
        $post = arRequest();
        if (arPost()) {
            $user = U_usersModel::model()->login($post);
            if ($user === true) {
                $this->showJson(array('ret_msg' => '登录成功', 'ret_code' => '1000', 'success' => "1", 'url' => arU('Index/showlist')));
                return;
            } else {
                $this->showJsonError($user);
                return;
            }
        }
        $this->setLayoutFile('');
        $this->assign(array('cssInsertBundles' => array('user')));
        $this->assign(array('jsInsertBundles' => array('user')));
        $this->display();
    }

    // 用户注册
    public function registerAction()
    {
        if (arPost()) {
            $user = U_usersModel::model()->register();
            if ($user === true) {
                $this->showJson(array('ret_msg' => '注册成功', 'ret_code' => '1000', 'success' => "1", 'url' => arU('Index/login')));
                return;
            } else {
                $this->showJsonError($user);
                return;
            }
        }
        $this->setLayoutFile('');
        $this->assign(array('cssInsertBundles' => array('user')));
        $this->assign(array('jsInsertBundles' => array('user')));
        $this->display();
    }

    // 退出登录
    public function logOutAction()
    {
        arComp('list.session')->flush();
        $this->redirect('login');
    }

    // 展示项目详情
    public function productinfoAction()
    {
        $post      = arRequest();
        $this->display();
    }

}
