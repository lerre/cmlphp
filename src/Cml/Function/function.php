<?php
/* * *********************************************************
 * [cml] (C)2012 - 3000 cml http://cmlphp.51beautylife.com
 * @Author  linhecheng<linhechengbush@live.com>
 * @Date: 14-2-211 下午2:23
 * @version  2.5
 * cml框架公用函数库
 * *********************************************************** */
namespace Cml;

/**
 * 友好的变量输出
 *
 * @param mixed $var 变量
 * @param int $getArgs 获取要打印的值
 *
 * @return string
 */
function dump($var, $getArgs = 0)
{
    if ($GLOBALS['debug']) {
        new \Cml\dBug($var);    //deBug模式直接输出
    } else {
        static $args = array();
        if (($getArgs == 1)) return $args;
        $args[] = $var;//输出到浏览器控制台
    }
    return '';
}

/**
 * 友好的变量输出并且终止程序(只在调试模式下才终止程序)
 *
 * @param mixed $var 变量
 *
 * @return void|string
 */
function dd($var)
{
    dump($var);
    $GLOBALS['debug'] && exit();
}


/**
 * print_r && exit
 *
 * @param mixed $var
 */
function pd($var)
{
    print_r($var);
    exit();
}

/**
 * 自定义异常处理
 *
 * @param string $msg 异常消息
 * @param integer $code 异常代码 默认为0
 *
 * @throws \Exception
 */
function throwException($msg, $code = 0)
{
    throw new \Exception($msg, $code);
}

/**
 * 快速文件数据读取和保存 针对简单类型数据 字符串、数组
 *
 * @param string $name 缓存名称
 * @param mixed $value 缓存值
 * @param string $path 缓存路径
 *
 * @return mixed
 */
function simpleFileCache($name, $value = '', $path = CML_RUNTIME_DATA_PATH)
{
    static $_cache = array();
    $filename = $path . '/'. $name . '.php';
    if ($value !== '') {
        if (is_null($value)) {
            // 删除缓存
            return false !== @unlink($filename);
        } else if (is_array($value)) {
            // 缓存数据
            $dir = dirname($filename);
            // 目录不存在则创建
            is_dir($dir) || mkdir($dir, 0700, true);
            $_cache[$name] = $value;
            return file_put_contents($filename, "<?php\treturn ".var_export($value, true).";?>", LOCK_EX);
        } else {
            return false;
        }
    }
    if (isset($_cache[$name])) return $_cache[$name];
        // 获取缓存数据
    if (is_file($filename)) {
        $value = require $filename;
        $_cache[$name] = $value;
    } else {
        $value = false;
    }
    return $value;
}

/**
 * 生成友好的时间格式
 *
 * @param $from
 *
 * @return bool|string
 */
function  friendlyDate($from){
    static $now = NULL;
    $now == NULL && $now = time();
    ! is_numeric( $from ) && $from = strtotime( $from );
    $seconds = $now - $from;
    $minutes = floor( $seconds / 60 );
    $hours   = floor( $seconds / 3600 );
    $day     = round( ( strtotime( date( 'Y-m-d', $now ) ) - strtotime( date( 'Y-m-d', $from ) ) ) / 86400 );
    if ( $seconds == 0 ) {
        return '刚刚';
    }
    if ( ( $seconds >= 0 ) && ( $seconds <= 60 ) ) {
        return "{$seconds}秒前";
    }
    if ( ($minutes >= 0) && ($minutes <= 60) ) {
        return "{$minutes}分钟前";
    }
    if ( ($hours >= 0) && ($hours <= 24) ){
        return "{$hours}小时前";
    }
    if ( (date('Y') - date('Y', $from)) > 0 ) {
        return date( 'Y-m-d', $from );
    }

    switch ($day){
        case 0:
            return date( '今天H:i', $from );
            break;
        case 1:
            return date( '昨天H:i', $from );
            break;
        default:
            return "{$day} 天前";
    }
}

/**
 * 生成唯一id
 *
 * @return string
 */
function createUnique()
{
    $data = $_SERVER['HTTP_USER_AGENT'].$_SERVER['REMOTE_ADDR'].\Cml\Cml::$nowMicroTime.rand();
    return sha1($data);
}