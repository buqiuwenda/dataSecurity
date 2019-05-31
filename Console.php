<?php
/**
 * Class ${NAME}
 * Author:huangzhongxi@rockfintech.com
 * Date: 2019/5/31 1:29 PM
 */
require_once 'QiniuService.php';
require_once 'BackupService.php';
require_once 'LogService.php';

class Console
{

    public function run($fileName, $method = 'encrypt', $date = '')
    {
        date_default_timezone_set('Asia/Shanghai');
        set_time_limit(0);
        error_reporting(E_ERROR | E_WARNING);

        $start_time = time();

        if($method == 'encrypt'){
            $runName = '备份';
        }else{
            $runName = '恢复';
        }
        echo '数据库'.$runName.'开始时间：'.date('Y-m-d H:i:s').PHP_EOL;
        LogService::info('数据库'.$runName.'开始时间：'.date('Y-m-d H:i:s'));


        $encryptFile = 'encrypt_' . $fileName;
        $backupService = new BackupService();

        if($method == 'encrypt') {
            $backupService->backup($fileName);
            $backupService->encryptOrDecryptFile($fileName, $encryptFile);

            // 上传七牛云存储
            $qiniuService = new QiniuService($encryptFile);
            $qiniuService->upload();
        }else{
            $decryptFile = 'decrypt_'.str_replace('encrypt_','', $fileName);
            $remoteFile = 'qiniu_'.$fileName;
            $backupService->downloadFile($fileName, $remoteFile, $date);

            $backupService->encryptOrDecryptFile($remoteFile, $decryptFile, 'decrypt');

            $backupService->restore($decryptFile);
        }



        $total = time() - $start_time;
        echo '数据库'.$runName.'结束时间：'.date('Y-m-d H:i:s').' 总耗时：'.$total.'秒'.PHP_EOL;
        LogService::info('数据库'.$runName.'结束时间：'.date('Y-m-d H:i:s').' 总耗时：'.$total.'秒');
    }

}

print_r($argv);
$fileName = isset($argv[1]) ? $argv[1] : 'wdblog.sql';
$method = isset($argv[2]) ? $argv[2] : 'encrypt';
$date = isset($argv[3]) ? $argv[3] : date('Ymd');
$console = new Console();

$console->run($fileName, $method, $date);
