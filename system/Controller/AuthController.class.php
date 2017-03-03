<?php

/**
 * Created by PhpStorm.
 * User: HP
 * Date: 2016/12/22
 * Time: 14:50
 */
class AuthController extends BaseController
{
    // 初始化方法
    public function init()
    {
        parent::init();

        // 调用layer msg cart插件
        arSeg(array(
                'loader' => array(
                    'plugin' => '',
                    'this' => $this
                )
            )
        );

    }

}