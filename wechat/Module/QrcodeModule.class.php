<?php
namespace wechat\Module;
// 二维码类
class QrcodeModule
{
    // 初始化
    public function initModule()
    {
        // 引入二维码类库
        include AR_ROOT_PATH . 'Lib/Ext' . DS . 'phpqrcode' . DS . 'qrlib.php';

    }

    // 生成png图片 arModule('wechat.Qrcode')->png($data, $size, false);
    public function png($data, $size = 2, $fileName = false)
    {
        // 生成图片
        return \QRcode::png($data, $fileName, 'H', $size);

    }

}
