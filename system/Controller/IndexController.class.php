<?php
/**
 * Powerd by ArPHP.
 *
 * Controller.
 *
 * @author ycassnr <ycassnr@gmail.com>
 */

/**
 * Default Controller of webapp.
 */
class IndexController extends BaseController
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


    // 整体框架
    public function indexAction()
    {
        // $uName = arComp('list.session')->get('adminUser');
        // $this->assign(array('adminUser' => $uName));
        $this->assign(array('title' => '后台登录首页'));
        $this->display('index');

    }


    // 欢迎页面
    public function welcomeAction()
    {
        $rows = IndexModel::model()->getInfo();

        // 获取所有phpinfo()的信息
//        $phpInfo= ini_get_all();
//        $phpInfo= phpinfo();
//
//        echo '<pre>';
//        var_dump($phpInfo);
//        echo "</pre>";
//        exit;

        $this->assign(array('title' => '我的桌面'));
        $this->assign(array('rows' => $rows));
//        $this->assign(array('phpInfo' => $phpInfo));
        $this->display('welcome');

    }


}