<?php
/**
 * Ar default public config file.
 *
 * @author ycassnr <ycassnr@gmail.com>
 */
return array(
    // 关闭调试信息
    'DEBUG_SHOW_TRACE'        => false,
    // 设置为1表示浏览器不显示错误，设置为0表示显示错误，默认为0
    // 'DEBUG_LOG'               => 1,
    // 模板后缀
    'TPL_SUFFIX'              => 'html',

    // 上传目录路径前缀
    'UPLOAD_FILE_SERVER_PATH' => AR_SERVER_PATH . 'Upload/',

    // 默认头像
    'DEFAULT_USER_LOG'        => AR_SERVER_PATH . 'Public/images/avatar.jpg',
    // 默认管理员头像
    'DEFAULT_ADMIN_LOG'       => AR_SERVER_PATH . 'Public/images/admin.jpg',

    //  home pc端>nodejs重构，system 后台，wechat微信，api接口，oppt 项目运营平台 ，。。
    'moduleLists'             => array(
        'home', 'main', 'system', 'wechat', 'api', 'oppt'
    ),

    // 组件配置开始
    'components'              => array(
        // 懒惰加载
        'lazy' => true,
        'db'   => array(
            // 懒惰加载
            'lazy'  => true,
            // mysql数据库组件
            'mysql' => array(
                'config' => array(
                    // 读库
                    'read' => array(
                        // 默认读库配置
                        'default' => array(
                            // 'dsn' => 'mysql:host=localhost;dbname=quickoa;port=3306',
                            // 'user' => 'root',
                            // 'pass' => 'root',
                              'dsn' => 'mysql:host=192.168.0.129;dbname=quickoa;port=3306',
                           'dsn'    => 'mysql:host=211.149.195.135;dbname=quickoa;port=9906',
                            'user'   => 'mzmtest',
                            'pass'   => 'mzmtest2016',

                           // 'dsn' => 'mysql:host=127.0.0.1;dbname=quickoa;port=3306',
                           //                             'user' => 'root',
                           //                             'pass' => '123456',

                            'prefix' => '',
                            'option' => array(
                                PDO::ATTR_ERRMODE                  => PDO::ERRMODE_EXCEPTION,
                                PDO::MYSQL_ATTR_USE_BUFFERED_QUERY => true,
                                PDO::MYSQL_ATTR_INIT_COMMAND       => 'SET NAMES \'UTF8\'',
                            ),
                        ),

                    ),

                ),
            ),
        ),
        // mysql 配置结束
    ),

);
