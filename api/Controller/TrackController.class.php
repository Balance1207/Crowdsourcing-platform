<?php
/**
 * Powerd by ArPHP.
 *
 * Controller.
 *
 * @author ycassnr <ycassnr@gmail.com>
 */

/**
 * 任务相关接口
 */
class TrackController extends BaseController
{
    // 我的任务
    public function myListAction()
    {
        $uid = arModule('Lib.User')->getUid();
        $lists = arModule('Lib.Track')->userList($uid);

        if ($lists) :
            $this->showJson($lists);
        else :
            $this->showJsonError('数据为空');
        endif;

    }

    // 项目的任务
    public function itemListAction()
    {
        if ($iid = arRequest('iid')) :
            $uid = arModule('Lib.User')->getUid();
            if (!arModule('Lib.Item')->hasUser($iid, $uid)) :
                $this->showJsonError('请先加入项目再查看任务列表');
            else :
                $lists = arModule('Lib.Track')->itemList($iid);
                if ($lists) :
                    $this->showJson($lists);
                else :
                    $this->showJsonError('数据为空');
                endif;
            endif;
        else :
            $this->showJsonError('参数错误：iid丢失');
        endif;

    }

    // 任务详情
    public function detailAction()
    {
        if ($tid = arRequest('tid')) :
            $detail = arModule('Lib.Track')->detail($tid);
            if ($detail) :
                $this->showJson($detail);
            else :
                $this->showJsonError('数据为空');
            endif;
        else :
            $this->showJsonError('参数错误：tid丢失');
        endif;

    }

    // 任务日志列表
    public function logListAction()
    {
        $tid = arRequest('tid');
        if ($tid) :
            $logs = arModule('Lib.Track')->logs($tid);
            if ($logs) :
                $this->showJson($logs);
            else :
                $this->showJsonError('数据为空');
            endif;
        else :
            $this->showJsonError('参数错误：tid丢失');
        endif;

    }

    // 指派任务
    public function assignAction()
    {
        $tid = arRequest('tid');
        if ($tid) :
            $touid = arRequest('touid');
            if ($touid) :
                $uid = arModule('Lib.User')->getUid();
                if ($uid) :
                    if (arModule('Lib.Track')->isOpuser($tid, $uid)) :
                        $result = arModule('Lib.Track')->assign($tid, $touid, $uid);
                        if ($result) :
                            $this->showJsonSuccess('指派成功', '2000', array('url' => arU('/main/Track/detail', array('tid' => $tid))));
                        else :
                            $this->showJsonError('');
                        endif;
                    else :
                        $this->showJsonError('当前任务权限越权');
                    endif;
                else :
                    $this->showJsonError('参数错误：uid丢失');
                endif;
            else :
                $this->showJsonError('参数错误：touid丢失');
            endif;
        else :
            $this->showJsonError('参数错误：tid丢失');
        endif;

    }

    // 任务说明
    public function trackNoteAction()
    {
        if ($tid = arRequest('tid')) :
            if ($content = arRequest('content')) :
                if ($uid = arModule('Lib.User')->getUid()) :
                    $result = arModule('Lib.Track')->trackNote($tid, $content, $uid);
                    if ($result) :
                        $this->showJsonSuccess('任务说明提交成功',
                                '1000',
                                array('url' => arU('/main/Track/detail', array('tid' => $tid))));
                    else :
                        $this->showJsonError('');
                    endif;
                else :
                    $this->showJsonError('参数错误：uid丢失');
                endif;
            else :
                $this->showJsonError('参数错误：content丢失');
            endif;
        else :
            $this->showJsonError('参数错误：tid丢失');
        endif;

    }

    // 申请加入任务
    public function applyAction()
    {
        if ($tid = arRequest('tid')) :
            if ($apply_msg = arRequest('msg')) :
                if ($uid = arModule('Lib.User')->getUid()) :
                    $result = arModule('Lib.Track')->apply($tid, $apply_msg, $uid);
                    if ($result) :
                        $this->showJsonSuccess('申请提交成功，等待管理员审核！');
                    else :
                        $this->showJsonError('申请失败！');
                    endif;
                else :
                    $this->showJsonError('参数错误：uid丢失');
                endif;
            else :
                $this->showJsonError('参数错误：apply_msg丢失');
            endif;
        else :
            $this->showJsonError('参数错误：tid丢失');
        endif;

    }

    // 审核任务申请
    public function checkAction()
    {
        if ($tid = arRequest('tid')) :
            if ($uid = arRequest('uid')) :
                if ($type = arRequest('type')) :
                    $result = arModule('Lib.Track')->check($tid, $uid, $type);
                    if ($result) :
                        $this->showJsonSuccess('');
                    else :
                        $this->showJsonError('');
                    endif;
                else :
                    $this->showJsonError('参数错误：type丢失');
                endif;
            else :
                $this->showJsonError('参数错误：uid丢失');
            endif;
        else :
            $this->showJsonError('参数错误：tid丢失');
        endif;
    }

    // 退出任务
    public function quitAction()
    {
        if ($tid = arRequest('tid')) :
            $uid = arModule('Lib.User')->getUid();
            $hasIn = arModule('Lib.Track')->hasUser($tid, $uid);
            if ($hasIn) :
                $res = arModule('Lib.Track')->quit($tid, $uid);
                if ($res) :
                    $this->showJsonSuccess('已退出成功，以后可以继续申请', '2000', array('url' => arU('/main/Track/detail', array('tid' => $tid))));
                else :
                    $this->showJsonError('你没有该任务, 请勿重复操作', '2001');
                endif;
            else :
                $this->showJsonError('尚未加入此任务', '3001');
            endif;
        else :
            $this->showJsonError('参数错误:tid丢失', '4001');
        endif;

    }

    // 撤销任务申请
    public function applyCancelAction()
    {
        if ($tid = arRequest('tid')) :
            $uid = arModule('Lib.User')->getUid();
            $hasIn = arModule('Lib.Track')->hasUser($tid, $uid);
            if (!$hasIn) :
                if (arModule('Lib.Track')->hasApply($tid, $uid)) :
                    $res = arModule('Lib.Track')->applyCancel($tid, $uid);
                    if ($res) :
                        $this->showJsonSuccess('任务申请已撤销成功，以后可以继续申请', '2000', array('url' => arU('/main/Track/detail', array('tid' => $tid))));
                    else :
                        $this->showJsonError('你没有申请过该任务, 请勿重复操作', '2001');
                    endif;
                else :
                    $this->showJsonError('你没有申请过该任务', '2002');
                endif;
            else :
                $this->showJsonError('已加入任务，不能执行此操作', '3001');
            endif;
        else :
            $this->showJsonError('参数错误:tid丢失', '4001');
        endif;

    }

    // 获取开发者
    public function developersAction()
    {
        if ($tid = arRequest('tid')) :
            $detail = arModule('Lib.Track')->detail($tid);
            $this->showJson($detail['users']);
        else :
            $this->showJsonError('参数错误:tid丢失', '4001');
        endif;

    }

    // 用户参与的任务
    public function myTracksAction()
    {
        if ($uid = arModule('Lib.User')->getUid()) :
            $tracks = arModule('Lib.Track')->userList($uid);
            if ($tracks) :
                $this->showJson($tracks);
            else :
                $this->showJsonError('数据为空');
            endif;
        else :
            $this->showJsonError('参数错误：uid丢失');
        endif;
    }

}

