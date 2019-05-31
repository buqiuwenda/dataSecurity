<?php
/**
 * Class ${NAME}
 * Author:huangzhongxi@rockfintech.com
 * Date: 2019/5/31 1:46 PM
 */
require_once 'vendor/autoload.php';
require_once 'Configs.php';
require_once 'LogService.php';
use Qiniu\Auth;
use Qiniu\Storage\UploadManager;


class QiniuService
{
    private $fileName;
    protected $config;

    protected $auth;
    protected $uploadManager;
    protected $keys;

    public function __construct($fileName) {
        $this->fileName = $fileName;
        $this->config = new Configs();

        $accessKey = $this->config->getAccessKey();
        $secretKey = $this->config->getSecretKey();
        $this->auth = new Auth($accessKey, $secretKey);

        $this->uploadManager = new UploadManager();

        $this->keys = $this->config->getKeysPath().'/'.date('Ymd').'/'.$fileName;
    }



    public function upload()
    {

        $filePath = $this->config->getBackupPath();

        if(!is_dir($filePath)){
            mkdir($filePath, 0777, true);
        }
        $file = $filePath.$this->fileName;

        if(!file_exists($file)){
            LogService::info('文件不存在，不处理');
            exit('文件不存在，不处理');
        }

        $token = $this->auth->uploadToken($this->config->getBucket());

        list($ret, $err) = $this->uploadManager->putFile($token, $this->keys, $file);
        echo "\n====> putFile result: \n";
        if ($err !== null) {
            LogService::info($err);
            var_dump($err);
        } else {
            LogService::info($ret);
            var_dump($ret);
        }
    }

}