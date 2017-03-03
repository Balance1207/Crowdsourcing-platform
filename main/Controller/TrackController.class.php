<?php
// 任务处理
class TrackController extends BaseController
{
    // 初始化
    public function init()
    {
        parent::init();
        $this->assign(array('page_title' => '任务管理'));

    }

    // 我的任务列表
    public function myListAction()
    {
        $this->assign(array('page_title' => '我的任务'));
        $this->display();

    }

    // 详细信息
    public function detailAction()
    {
        if ($tid = arRequest('tid')) :
            $track = arModule('Lib.Track')->detail($tid);
            $this->assign(array('track' => $track));
            $this->display();
        else :
            $this->redirectError('myList', '参数错误：tid');
        endif;


    }

}
