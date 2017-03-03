<?php
/**
 * Powerd by ArPHP.
 *
 * Controller.
 *
 * @author ycassnr <ycassnr@gmail.com>
 */

/**
 * 项目相关接口
 */
class ItemController extends BaseController
{
    // 初始化
    public function init()
    {
        if (!arModule('Lib.User')->isLogin()) :
            $this->showJsonError('尚未登录');
            exit;
        endif;

    }

    // 是否加入用户
    public function isJoinAction()
    {
        if ($iid = arRequest('iid')) :
            $uid = arModule('Lib.User')->getUid();
            if (arModule('Lib.Item')->hasUser($iid, $uid)) :
                $this->showJsonSuccess();
            else :
                $this->showJsonError('该项目用户不存在');
            endif;
        else :
            $this->showJsonError('参数错误:iid丢失');
        endif;

    }

    // 申请加入项目
    public function applyAction()
    {
        if ($iid = arRequest('iid')) :
            if ($msg = arRequest('msg')) :
                $uid = arModule('Lib.User')->getUid();
                $hasIn = arModule('Lib.Item')->hasUser($iid, $uid);
                if (!$hasIn) :
                    if (!arModule('Lib.Item')->hasApply($iid, $uid)) :
                        $res = arModule('Lib.Item')->addApply($iid, $uid, $msg);
                        if ($res) :
                            $this->showJsonSuccess(
                                '项目申请请求已提交成功，请耐心等待管理员审核',
                                '2000',
                                array('url' => arU('/main/Index/pd_item', array('id' => $iid)))
                            );
                        else :
                            $this->showJsonError('');
                        endif;
                    else :
                        $this->showJsonError('项目已申请，请勿重复申请, 请耐心等待管理员审核');
                    endif;
                else :
                    $this->showJsonError('已加入项目，不能重复申请');
                endif;
            else :
                $this->showJsonError('参数错误:msg丢失');
            endif;
        else :
            $this->showJsonError('参数错误:iid丢失');
        endif;

    }

    // 撤销申请
    public function applyCancelAction()
    {
        if ($iid = arRequest('iid')) :
            $uid = arModule('Lib.User')->getUid();
            $hasIn = arModule('Lib.Item')->hasUser($iid, $uid);
            if (!$hasIn) :
                if (arModule('Lib.Item')->hasApply($iid, $uid)) :
                    $res = arModule('Lib.Item')->applyCancel($iid, $uid);
                    if ($res) :
                        $this->showJsonSuccess('项目申请已撤销成功，以后可以继续申请', '2000', array('url' => arU('/main/Index/pd_item', array('id' => $iid))));
                    else :
                        $this->showJsonError('你没有申请过该项目, 请勿重复操作', '2001');
                    endif;
                else :
                    $this->showJsonError('你没有申请过该项目', '2002');
                endif;
            else :
                $this->showJsonError('已加入项目，不能执行此操作', '3001');
            endif;
        else :
            $this->showJsonError('参数错误:iid丢失', '4001');
        endif;

    }

    // 退出项目
    public function quitAction()
    {
        if ($iid = arRequest('iid')) :
            $uid = arModule('Lib.User')->getUid();
            $hasIn = arModule('Lib.Item')->hasUser($iid, $uid);
            if ($hasIn) :
                $res = arModule('Lib.Item')->quit($iid, $uid);
                if ($res) :
                    $this->showJsonSuccess('项目组已退出成功，以后可以继续申请', '2000', array('url' => arU('/main/Index/pd_item', array('id' => $iid))));
                else :
                    $this->showJsonError('你没有该项目, 请勿重复操作', '2001');
                endif;
            else :
                $this->showJsonError('尚未加入此项目', '3001');
            endif;
        else :
            $this->showJsonError('参数错误:iid丢失', '4001');
        endif;

    }

    // 项目群组(任务+用户)
    public function groupAction()
    {
        if ($iid = arRequest('iid')) :
            $groupInfo = arModule('Lib.Item')->group($iid);
            if ($groupInfo) :
                $this->showJson($groupInfo);
            else :
                $this->showJsonError('没有该项目','3002');
            endif;
        else :
            $this->showJsonError('参数错误:iid丢失','4001');
        endif;

    }

    // 项目成员
    public function usersAction()
    {
        if ($iid = arRequest('iid')) :
            $groupInfo = arModule('Lib.Item')->group($iid);
            if ($groupInfo) :
                $users = $groupInfo['users'];
                if (!$users) :
                    $users = array();
                endif;
                $this->showJson($users);
            else :
                $this->showJsonError('没有该项目','3002');
            endif;
        else :
            $this->showJsonError('参数错误:iid丢失','4001');
        endif;

    }

    // 项目详情
    public function itemInfoAction()
    {
        if ($iid = arRequest('id')) :
            $itemInfo = arModule('Lib.Item')->info($iid);
            if ($itemInfo) :
                $this->showJson($itemInfo);
            else :
                $this->showJsonError('数据为空');
            endif;
        else :
            $this->showJsonError('参数错误:iid丢失','4001');
        endif;
    }

    // 用户参与的项目列表
    public function myItemsAction()
    {
        if (arRequest('u_id')) :
            $uid = arRequest('u_id');
        else :
            $uid = arModule('Lib.User')->getUid();
        endif;

        if ($uid) :
            $myItems = arModule('Lib.Item')->myItems($uid);
            if ($myItems) :
                $this->showJson($myItems);
            else :
                $this->showJsonError('数据为空');
            endif;
        else :
            $this->showJsonError('参数错误:uid丢失');
        endif;

    }

}
