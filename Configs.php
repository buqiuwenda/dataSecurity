<?php
/**
 * Class ${NAME}
 * Author:huangzhongxi@rockfintech.com
 * Date: 2019/5/31 11:13 AM
 */
final class Configs
{
    /**
     * getAccessKey 七牛上传access_key
     * @return string
     */
    public function getAccessKey()
    {
        return 'access';
    }

    /**
     * getSecretKey 七牛上传secret_key
     * @return string
     */
    public function getSecretKey()
    {
        return 'secret';
    }

    /**
     * getBackupPath 备份路径
     * @return string
     */
    public function getBackupPath()
    {
        return __DIR__.'/storage/backup/';
    }

    /**
     * getBucket 七牛空间名
     * @return string
     */
    public function getBucket()
    {
        return 'bucket';
    }
    /**
     * RandomString 获取随机字符
     * @param int $length
     * @return bool|string
     */
    public function RandomString($length=20)
    {
        $str='ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
        $random_str=str_shuffle(str_repeat($str,5));
        $start_string=mt_rand(0,(strlen($str)-$length));
        return substr($random_str,$start_string,$length);
    }


    /**
     * getBackupPath 备份路径
     * @return string
     */
    public function getLogPath()
    {
        return __DIR__.'/storage/logs/';
    }

    public function getKeysPath()
    {
        return 'database/'.date('Ymd').'/wdblog.sql';
    }


    public function getUserName()
    {
        return 'test';
    }

    public function getPassword()
    {
        return 'test';
    }


    public function getDatabaseName()
    {
        return 'test';
    }


    public function getSecret()
    {
        return '1j6ZlOrNqKqNvgHa38AeOyp9HlJ==';
    }

    public function getCipher()
    {
        return 'AES-256-CBC';
    }


    public function getDomain()
    {
        return 'https://portal.qiniu.com/bucket/wd-http-bucket/index';
    }

}


