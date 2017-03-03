<?php
/**
 * ArPHP A Strong Performence PHP FrameWork ! You Should Have.
 *
 * PHP version 5
 *
 * @category PHP
 * @package  Core.base
 * @author   yc <ycassnr@gmail.com>
 * @license  http://www.arphp.org/licence MIT Licence
 * @version  GIT: : coding-standard-tutorial.xml,v 1.0 2014-5-01 18:16:25 cweiske Exp $
 * @link     http://www.arphp.org
 */
// 启动时间
defined('AR_START_TIME') or define('AR_START_TIME', microtime(true));
// 开启调试 是
defined('AR_DEBUG') or define('AR_DEBUG', true);
// 外部启动 否 默认管理目录ArMan
defined('AR_OUTER_START') or define('AR_OUTER_START', false);
// 自启动session
defined('AR_AUTO_START_SESSION') or define('AR_AUTO_START_SESSION', true);
// 作为外部框架加载 可嵌入其他框架
defined('AR_AS_OUTER_FRAME') or define('AR_AS_OUTER_FRAME', false);
// 内部实现http webservice 多套 arphp程序互调接口
defined('AR_RUN_AS_SERVICE_HTTP') or define('AR_RUN_AS_SERVICE_HTTP', false);
// 实现 cmd socket 编程
defined('AR_AS_CMD') or define('AR_AS_CMD', false);
// web application 默认方式
defined('AR_AS_WEB') or define('AR_AS_WEB', true);
// app名 main
defined('AR_DEFAULT_APP_NAME') or define('AR_DEFAULT_APP_NAME', 'main');
// 默认的控制器名
defined('AR_DEFAULT_CONTROLLER') or define('AR_DEFAULT_CONTROLLER', 'Index');
// 默认的Action
defined('AR_DEFAULT_ACTION') or define('AR_DEFAULT_ACTION', 'index');
// 目录分割符号
defined('DS') or define('DS', DIRECTORY_SEPARATOR);
// ar框架目录
defined('AR_FRAME_PATH') or define('AR_FRAME_PATH', dirname(__FILE__) . DS);
// 项目根目录
defined('AR_ROOT_PATH') or define('AR_ROOT_PATH', realpath(dirname($_SERVER['SCRIPT_FILENAME'])) . DS);
// 核心目录
defined('AR_CORE_PATH') or define('AR_CORE_PATH', AR_FRAME_PATH . 'Core' . DS);
// 配置目录
defined('AR_CONFIG_PATH') or define('AR_CONFIG_PATH', AR_FRAME_PATH . 'Conf' . DS);
// 扩展目录
defined('AR_EXT_PATH') or define('AR_EXT_PATH', AR_FRAME_PATH . 'Extensions' . DS);
// 模块目录
defined('AR_COMP_PATH') or define('AR_COMP_PATH', AR_FRAME_PATH . 'Components' . DS);
// 服务地址
defined('AR_SERVER_PATH') or define('AR_SERVER_PATH', ($dir = dirname($_SERVER['SCRIPT_NAME'])) == DS ? '/' : str_replace(DS, '/', $dir) . '/');
// 默认配置文件
defined('AR_PUBLIC_CONFIG_FILE') or define('AR_PUBLIC_CONFIG_FILE', '');

require_once AR_CORE_PATH . 'Ar.class.php';

spl_autoload_register('Ar::autoLoader');

if (AR_OUTER_START) :
    defined('AR_MAN_NAME') or define('AR_MAN_NAME', 'Arman');
    defined('AR_MAN_PATH') or define('AR_MAN_PATH', AR_ROOT_PATH . AR_MAN_NAME . DS);
elseif (AR_AS_CMD) :
    defined('AR_CMD_PATH') or define('AR_CMD_PATH', AR_ROOT_PATH . AR_DEFAULT_APP_NAME . DS);
else :
    set_exception_handler('Ar::exceptionHandler');
    set_error_handler('Ar::errorHandler');
    register_shutdown_function('Ar::shutDown');
endif;
Ar::init();
