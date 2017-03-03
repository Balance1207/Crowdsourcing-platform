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
class UserWechatModel extends ArModel
{
    // 模型初始化
    static public function model($class = __CLASS__)
    {
        return parent::model($class);

    }

    // 表名
    public $tableName = 'u_users_wechat';

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

}
