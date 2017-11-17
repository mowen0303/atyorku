<?php

/**
 * Created by PhpStorm.
 * User: Jerry
 * Date: 2017-11-06
 * Time: 8:03 PM
 */
class BasicTool
{
    public static function post($name, $notNullMessage = false)
    {
        if (isset($_POST[$name])) {
            $v = $_POST[$name];
            if ($v === "" && $notNullMessage) {
                throw new Exception($notNullMessage);
            }
            if (is_string($v)) {
                $v = addslashes(trim($v));
            } elseif (is_array($v)) {
                if (!function_exists('__addslashes')) {
                    function __addslashes(&$v, $k)
                    {
                        $v = addslashes(trim($v));
                    }
                }
                array_walk_recursive($v, '__addslashes');
            }
            return $v;
        } else {
            if ($notNullMessage != false) {
                self::throwException($notNullMessage);
            } else {
                return null;
            }
        }
    }

    public static function get($name, $notNullMessage = false)
    {
        if (isset($_GET[$name])) {
            $v = $_GET[$name];
            if ($v === "" && $notNullMessage) {
                throw new Exception($notNullMessage);
            }
            if (is_string($v)) {
                $v = addslashes(trim($v));
            } elseif (is_array($v)) {
                if (!function_exists('__addslashes')) {
                    function __addslashes(&$v, $k)
                    {
                        $v = addslashes(trim($v));
                    }
                }
                array_walk_recursive($v, '__addslashes');
            }
            return $v;
        } else {
            if ($notNullMessage != false) {
                self::throwException($notNullMessage);
            } else {
                return null;
            }
        }
    }

    public static function throwException($message)
    {
        throw new Exception($message);
    }

    public static function echoJson($code, $message=null, $result=null)
    {
        echo json_encode(array('code' => $code, 'message' => $message, 'result' => $result));
    }

    public static function echoMessage($title,$message){
        require_once $_SERVER["DOCUMENT_ROOT"]."/a2/page/frame/resultPage.php";
        die();
    }

    public static function echoImage($img){
        $imgData = @file_get_contents($img);
        $base64 = "data:image/png;base64,".base64_encode($imgData);
        echo '<img class="protectedImg" data-src="'.$base64.'" src="/a2/asset/image/0.png"/>';
    }

}