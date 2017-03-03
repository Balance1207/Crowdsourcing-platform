<?php

class GitController extends ArController
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

    //申请仓库
    public function applyGitAction()
    {
        $this->display();
    }

    //仓库列表
    public function listGitAction()
    {
        $this->assign(array('page_title' => '我的仓库'));
        $this->display();
    }

    //仓库详细信息
    public function gitInfoAction()
    {
        $this->display();
    }

}