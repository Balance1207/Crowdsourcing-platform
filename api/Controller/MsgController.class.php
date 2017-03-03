<?php
/**
 * Powerd by ArPHP.
 *
 * Controller.
 *
 * @author ycassnr <ycassnr@gmail.com>
 */

/**
 * 发送消息相关接口
 */
class MsgController extends BaseController
{
    // 初始化
    public function init()
    {
        if (!arModule('Lib.User')->isLogin()) :
            $this->showJsonError('尚未登录');
            exit;
        endif;

    }

    // 发送消息(通过用户id)
    public function sendMsgAction()
    {
        if ($recUid = arRequest('uid')) :
            if ($content = arRequest('content')) :
                $sendUid = 83;
            //arModule('Lib.User')->getUid();
                if ($recUid != $sendUid) :
                    $msg = arModule('Lib.Msg')->sendNormalMsg($sendUid, $recUid, $content);
                    if ($msg) :
                        $this->showJsonSuccess('发送成功');
                    else :
                        $this->showJsonError('发送失败');
                    endif;
                else :
                    $this->showJsonError('不能发送消息给自己');
                endif; 
            else :
                $this->showJsonError('参数错误:content丢失');
            endif;
        else :
            $this->showJsonError('参数错误:uid丢失');
        endif;

    }

}
