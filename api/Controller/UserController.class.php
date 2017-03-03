<?php
/**
 * Powerd by ArPHP.
 *
 * Controller.
 *
 * @author ycassnr <ycassnr@gmail.com>
 */

/**
 * 用户相关接口
 */
class UserController extends BaseController
{
    // 是否登录
    public function isLoginAction()
    {
        if (arModule('Lib.User')->isLogin()) :
            $this->showJsonSuccess();
        else :
            $this->showJsonError();
        endif;

    }

    // 用户详细信息
    public function userInfoAction()
    {
        if (arRequest('u_id')) :
            $uid = arRequest('u_id');
        else :
            $uid = arModule('Lib.User')->getUid();
        endif;

    	if ($uid) :
    		$userInfo = arModule('Lib.User')->userInfo($uid);
    		if ($userInfo) :
    			$this->showJson($userInfo);
    		else :
    			$this->showJsonError('数据为空');
    		endif;
    	else :
    		$this->showJsonError('参数错误:uid丢失');
    	endif;

    }

    // 自动登录
    public function autoLoginAction()
    {
        if ($uid = arModule('Lib.Session')->bindId()) :
            arModule('Lib.User')->setSession($uid);
            arModule('Lib.Session')->flushBid($uid);
            if ($url = arComp('list.session')->get('ar_back_url')) :
                arComp('list.session')->set('ar_back_url', null);
            else :
                $url = arU('/main/Index/index');
            endif;
            $this->showJsonSuccess('wx code login success', 1006, array('url' => $url));
        else :
            $this->showJsonError('login failed');
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
            $myItems = arModule('Lib.User')->myItems($uid);
            if ($myItems) :
                $this->showJson($myItems);
            else :
                $this->showJsonError('数据为空');
            endif;
        else :
            $this->showJsonError('参数错误:uid丢失');
        endif;

    }

    // 用户修改个人信息
    public function updateInfoAction()
    {
        if ($uid = arModule('Lib.User')->getUid()) :
            $nickname = arRequest('nickname');
            $qq = arRequest('qq');
            $email = arRequest('email');
            $result = arModule('Lib.User')->updateInfo($uid, $nickname, $qq, $email);
            if ($result) :
                $this->showJsonSuccess('修改成功', '1000', array('url' => arU('/main/Index/updateInfo')));
            else :
                $this->showJsonError('修改失败');
            endif;
        else :
            $this->showJsonError('请先登录');
        endif;
    }

}
