<?php
/**
 * Ar default public config file.
 *
 * @author ycassnr <ycassnr@gmail.com>
 */
return array(
    'abc' => 123,
    'DEBUG_SHOW_TRACE' => false,
    // 'DEBUG_LOG' => true,
    'components' => array(
        'ext' => array(
            'lazy' => true,
            'weixin' => array(
                'config' => array(
                    // 'APPID' => 'wx7c694b9d4c867',
                    // 'APPSECRET' => '318c8537168091eaca30dfdf70b238',
                    // 'TOKEN' => 'VZ3B3ucSuN1euN53nU5a4NVNi11Ene',
                    // 不检测签名
                    // 'notCheckSign' => true,
                    'menu' => array(
                        'button' => array(
                            array(
                                'name' => 'coopcoder',
                                'type' => 'click',
                                'key' => 'about',

                                 'sub_button' => array(
/*
                                     array(
                                         'name' => '产品简介',
                                         'type' => 'view',
                                         'url' => arU('Index/click', array('t' => 'cpjj_1_1'), 'FULL'),
                                     ),
                                     array(
                                         'name' => '产品成分',
                                         'type' => 'view',
                                         'url' => arU('Index/click', array('t' => 'cpcf_1_2'), 'FULL'),
                                     ),
                                     array(
                                         'name' => '科技精华',
                                         'type' => 'view',
                                         'url' => arU('Index/click', array('t' => 'kjjh_1_3'), 'FULL'),
                                     ),
                                     array(
                                         'name' => '使用功效',
                                         'type' => 'view',
                                         'url' => arU('Index/click', array('t' => 'sygx_1_4'), 'FULL'),
                                     ),
                                     array(
                                         'name' => '安全与副作用',
                                         'type' => 'view',
                                         'url' => arU('Index/click', array('t' => 'aqyfzy_1_5'), 'FULL'),
                                     ),
*/
                                 ),
                            ),
                            array(
                                'name' => '所有项目',
                                'type' => 'view',
                                //'type' => 'click',
//'key' => 'ceshi',
                                'url' => 'http://www.coopcoder.com/Index/pm',
                                 'sub_button' => array(
/*
                                     array(
                                         'name' => '合作方式',
                                         'type' => 'click',
                                         'key' => 'hzfs_2_1',
                                     ),
                                     array(
                                         'name' => '个人购买',
                                         'type' => 'click',
                                         'key' => 'grgm_2_2',
                                     ),
                                     array(
                                         'name' => '个人直销',
                                         'type' => 'click',
                                         'key' => 'grzx_2_3',
                                     ),
                                     array(
                                         'name' => '企业直销',
                                         'type' => 'click',
                                         'key' => 'qyzx_2_4',
                                     ),
                                     array(
                                         'name' => '联系方式',
                                         'type' => 'click',
                                         'key' => 'lxfs_2_5',
                                     ),
*/
                                 ),
                            ),
                            array(
                                'name' => '我的任务',
                                'type' => 'view',
                                'url' => 'http://www.coopcoder.com/Track/myList',
                                 'sub_button' => array(
/*
                                     array(
                                         'name' => '公司简介',
                                         'type' => 'click',
                                         'key' => 'gsjj_3_1',
                                     ),
                                     array(
                                         'name' => '产品系列',
                                         'type' => 'click',
                                         'key' => 'cpxl_3_2',
                                     ),
                                     array(
                                         'name' => '联系方式',
                                         'type' => 'click',
                                         'key' => 'lxfs_3_3',
                                     ),
*/
                                 ),
                            ),
                        ),
                    ),
                ),
            ),
        ),
    ),
);
