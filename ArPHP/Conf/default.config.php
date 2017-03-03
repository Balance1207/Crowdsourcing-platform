<?php
/**
 * ArPHP A Strong Performence PHP FrameWork ! You Should Have.
 *
 * PHP version 5
 *
 * @category PHP
 * @package  Core.Conf
 * @author   yc <ycassnr@gmail.com>
 * @license  http://www.arphp.org/licence MIT Licence
 * @version  GIT: 1: coding-standard-tutorial.xml,v 1.0 2014-5-01 18:16:25 cweiske Exp $
 * @link     http://www.arphp.org
 */
return array(
    // path
    'PATH' => array(
        // Web服务器地址
        'APP_SERVER_PATH' => AR_SERVER_PATH . arCfg('requestRoute.a_m') . '/',
        'PUBLIC' => AR_SERVER_PATH . arCfg('requestRoute.a_m') . '/Public/',
        'GPUBLIC' => AR_SERVER_PATH . 'Public/',
        // 兼容以前
        'CACHE' => AR_ROOT_PATH . arCfg('requestRoute.a_m') . DS . 'Cache' . DS,
        'LOG' => AR_ROOT_PATH . arCfg('requestRoute.a_m') . DS . 'Log' . DS,
        'VIEW' => AR_ROOT_PATH . arCfg('requestRoute.a_m') . DS . 'View' . DS,
        'UPLOAD' => AR_ROOT_PATH . arCfg('requestRoute.a_m') . DS . 'Upload' . DS,
        'EXT' => AR_ROOT_PATH . arCfg('requestRoute.a_m') . DS . 'Ext' . DS,
    ),

    // dir
    'DIR' => array(
        'CACHE' => AR_ROOT_PATH . arCfg('requestRoute.a_m') . DS . 'Cache' . DS,
        'LOG' => AR_ROOT_PATH . arCfg('requestRoute.a_m') . DS . 'Log' . DS,
        'VIEW' => AR_ROOT_PATH . arCfg('requestRoute.a_m') . DS . 'View' . DS,
        'UPLOAD' => AR_ROOT_PATH . arCfg('requestRoute.a_m') . DS . 'Upload' . DS,
        'EXT' => AR_ROOT_PATH . arCfg('requestRoute.a_m') . DS . 'Ext' . DS,
        // 片段目录
        'SEG' => AR_PUBLIC_CONFIG_PATH . 'Seg' . DS,
    ),

    // url
    'URL_MODE' => 'PATH',
    // 全局url 贪婪模式
    'URL_GREEDY' => false,

    // debug
    'DEBUG_SHOW_TRACE' => true,
    'DEBUG_SHOW_ERROR' => true,
    'DEBUG_SHOW_EXCEPTION' => true,

    // 是否调试信息到日志文件
    'DEBUG_LOG' => false,

    // 默认的模板后缀
    'TPL_SUFFIX' => 'php',

    // 路由规则
    'URL_ROUTE_RULES' => array(
        // 'index/index' => array(
        //     'mode' => 'testindex:a:/:b:', // url will be localhost/testindex123/456   a will be 123 b 456
        // ),
    ),

);
