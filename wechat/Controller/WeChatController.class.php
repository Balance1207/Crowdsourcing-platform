<?php
// 微信
class WeChatController extends ArController
{

    // 初始化
    public function init()
    {
        if (!arModule('wechat.Base')->isWeixin()) :
            // $this->redirectError('', '请用微信打开此页面', '100');
        endif;

    }

}
