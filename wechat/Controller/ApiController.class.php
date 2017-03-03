<?php
class ApiController extends ArController
{
    // 二维码生成
    public function qrcodeAction()
    {
        if ($data = arRequest('data')) :
            $data = urldecode($data);
            $size = arRequest('size', 5);
            return arModule('wechat.Qrcode')->png($data, $size, false);
        else :
            $this->showJsonError();
        endif;

    }

}
