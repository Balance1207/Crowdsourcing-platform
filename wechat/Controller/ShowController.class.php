<?php
class ShowController extends WeChatController
{
    // 绑定
    public function bindAction()
    {
        if ($code = arRequest('code')) :
            try {
                $uid = arModule('Lib.User')->getUid();
                $accessInfo = arModule('wechat.Base')->wechat->getAuthAccessToken($code);
                $userInfo = arModule('wechat.Base')->wechat->getUserInfo($accessInfo['access_token'], $accessInfo['openid']);

                if (arModule('wechat.Base')->hasBind($uid)) :
                    $bindResultUid = $uid;
                else :
                    // 绑定用户
                    $bindResultUid = arModule('wechat.Base')->bindUser($uid, $userInfo);
                endif;
                // 保存登录session
                $setSession = arModule('Lib.User')->setSession($bindResultUid);
                if ($bindResultUid && $setSession) :

                    if ($backUrl = arRequest('back_url')) :
                        $backUrl = urldecode($backUrl);
                    else :
                        $backUrl = '/main/Index/index';
                        // 发送登录信息
                        arModule('wechat.Send')->TplUserLogin($bindResultUid);
                    endif;
                    $this->redirect($backUrl);
                else :
                    $this->redirectError('/main/Index/index', '绑定错误');
                endif;

            } catch (Exception $e) {
                //$this->redirectError('http://mp.weixin.qq.com/s?__biz=MzI3MTI4MjIzNw==&mid=2247483653&idx=1&sn=5d247ecd974a08d4b4c7d3c91d439ab1', '错误原因：' . $e->getMessage() . '请先关注公众号"达传IT"', '5');
                arComp('list.log')->record(array('errMsg' => $e->getMessage(), $uid, $accessInfo, $userInfo), 'wxerr_showBind');
                $this->redirect($backUrl);
            }
        else :
            $this->redirectError('/main/Index/index', '回调参数错误');
        endif;

    }

    // 取消绑定
    public function unBindAction()
    {


    }

    // 登录pc端
    public function loginPcAction()
    {
        if ($accode = arRequest('accode')) :
            $uid = arModule('Lib.User')->getUid();
            if ($uid) :
                arModule('Lib.Session')->bind($accode, $uid);
                $this->redirectSuccess('javascript:coop.close_window();', 'PC端已成功登陆');
            else :
                $this->redirectError('javascript:coop.close_window();', 'PC异端登录uid错误');
            endif;
        else :
            $this->redirectError('javascript:coop.close_window();', 'PC异端登录accode错误');
        endif;

    }

}
