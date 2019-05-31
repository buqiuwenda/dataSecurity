<?php
/**
 * Class ${NAME}
 * Author:huangzhongxi@rockfintech.com
 * Date: 2019/5/31 2:14 PM
 */

class LogService
{

    public static function info($context, $prefix = 'log')
    {

        $path =  __DIR__.'/storage/logs/';
        if(empty($context)){
            return null;
        }

        if(!is_dir($path)){
            mkdir($path, 0777, true);
        }

        $file = $path.$prefix.'_'.date('Y-m-d').'.log';

        if(is_array($context)){
            $context = json_encode($context);
        }

        if(is_object($context)){
            $context = json_encode($context);
        }


        $context = "[".date('Y-m-d H:i:s')."]  ".$context.PHP_EOL;
        file_put_contents($file, $context, FILE_APPEND);


        return true;
    }
}