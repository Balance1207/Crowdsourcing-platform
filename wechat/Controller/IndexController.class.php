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
class IndexController extends WeChatController {

    /**
     * 监听微信服务器推送信息Action.
     *
     * @return void
     */
    public function indexAction()
    {
        // 注册微信服务器基本的几个事件
        arComp('ext.weixin')->registerEvent('click' , array($this, 'click'));
        arComp('ext.weixin')->registerEvent('subscribe' , array($this, 'subscribe'));
        arComp('ext.weixin')->registerEvent('unsubscribe' , array($this, 'unsubscribe'));
        arComp('ext.weixin')->registerEvent('msg_location' , array($this, 'location'));

        // 接收文本消息
        arComp('ext.weixin')->registerEvent('msg_text' , array($this, 'recvtext'));


        // 监听事件
        arComp('ext.weixin')->listen();

    }

    public function recvtext($data)
    {
        arComp('ext.weixin')->response('text', '接收信息' . $data['Content']);

    }

    // 微信Event点击事件触发
    public function click($data)
    {
        // 菜单EventKey
        $tplEventKey = $data['EventKey'];
        // 获取模板内容
        // $tplShowString = $this->display($tplEventKey . '.msg', true);
        arComp('list.log')->record($data, 'clickedata');
        // 回复模板内容到微信用户
        // arComp('ext.weixin')->response('text', $tplShowString);
        switch ($tplEventKey) {
            case 'about':
                arComp('ext.weixin')->response('text', 'coopcoder是代码合作管理开发平台，集需求发布，开发，团队，代码仓库，实时通信，项目跟踪，项目分成为一体的系统，欢迎开发者和公司加入。');
                break;
            default:
                # code...
                arComp('ext.weixin')->response('text', '测试信息' . $tplEventKey);
                break;
        }

    }

    // 浏览器点击链接触发Action
    public function clickAction()
    {
        $type = arGet('t');
        echo '测试信息';
        // $this->display($type);

    }

    // 微信用户订阅触发
    public function subscribe()
    {
        arComp('ext.weixin')->response('text', '欢迎来到达传IT，功能开发测试中。。！');

    }

    // 微信用户取消订阅触发
    public function unsubscribe()
    {
        arComp('ext.weixin')->response('text', '好吧！下次再见！');

    }

    // 微信用户上报地址触发
    public function location()
    {
        arComp('ext.weixin')->response('text', 'hello txd, location');

    }

    // 平台管理自定义菜单
    public function cMenuAction()
    {
        arComp('ext.weixin')->createMenu();
        exit('create menu success');

    }

    public function testAction()
    {
        echo 'logining...';
        $uid = arModule('Lib.User')->getUid();
        // 发送登录信息
        // arModule('wechat.Send')->TplUserLogin($uid);
        $tid = '141';
        arModule('wechat.Send')->TplNewTask($uid, $tid, $fromUid = '', $msg = '');
        // echo 'test';
        // $data = array(
        //     'first' => array('value'=>'你好test12312', 'color'=> '#173177'),
        //     'time' => array('value'=>date('Y-m-d H:i:s', time()), 'color'=>'#173177'),
        //     'ip' => array('value'=>'192.168.1.28', 'color'=>'#173177'),
        //     'reason' => array('value'=>'注意信息', 'color'=>'#173177'),
        // );
        // $res = arComp('ext.weixin')->sendTemplateMsg('oPc77wmh8TB7B-1f7HT9z8HAf9iE', '3XGjvQOJHujK7k853pIO6I2rTMNNZt0yTHgIbEZmZSo', '', $data);
        // var_dump($res);
        // exit;
        // echo arComp('ext.weixin')->authToUrl('test', 'snsapi_userinfo');

        // $code = arRequest('code');
        // $accessInfo = arComp('ext.weixin')->getAuthAccessToken($code);

        // $userInfo = arComp('ext.weixin')->getUserInfo($accessInfo['access_token'], $accessInfo['openid']);


        // var_dump($code, $userInfo);

        // echo arU('ff/ff', array('f' => 1));
        // echo $this->display('', true);

    }

}
