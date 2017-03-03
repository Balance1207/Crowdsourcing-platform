<?php
/**
 * Powerd by ArPHP.
 *
 * Model.
 *
 * @author ycassnr <ycassnr@gmail.com>
 */

/**
 * Default Model of webapp.
 */
class WechatConfigModel extends ArModel
{
    // 模型初始化
    static public function model($class = __CLASS__)
    {
        return parent::model($class);

    }

    // 表名
    public $tableName = 'wechat_config';

    // 获取bundle详细信息 万能方法
    public function getDetailInfo(array $bundles)
    {
        // 递归遍历所有信息
        if (arComp('validator.validator')->checkMutiArray($bundles)) :
            foreach ($bundles as &$bundle) :
                $bundle = $this->getDetailInfo($bundle);
            endforeach;
        else :
            $bundle = $bundles;
            /**
             * to do what you want
             * $bundle['????'] = '???';
             */
            return $bundle;
        endif;

        return $bundles;

    }

    // 获取默认配置
    public function getDefaultConfig()
    {
        return self::model()->getDb()->where(array('cid' => 1))->queryRow();

    }

    // 设置配置
    public function setConfig($conifg = array())
    {
        if (empty($config)) {
            $config = $this->getDefaultConfig();
        }
        // 填充默认配置 也可以写在配置文件
        Ar::setConfig('components.ext.weixin.config.APPID', $config['appid']);
        Ar::setConfig('components.ext.weixin.config.APPSECRET', $config['appsecret']);
        Ar::setConfig('components.ext.weixin.config.TOKEN', $config['token']);
        Ar::setConfig('components.ext.weixin.config.notCheckSign', false);

    }

}
