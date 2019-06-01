<?php
/**
 * Class ${NAME}
 * Author:huangzhongxi@rockfintech.com
 * Date: 2019/5/31 3:47 PM
 */
require_once 'Configs.php';
require_once 'LogService.php';

class BackupService
{
    protected  $config;

    public function __construct() {
        $this->config = new Configs;
    }


    public function backup($fileName)
    {
        $username = $this->config->getUserName();
        $password = $this->config->getPassword();
        $db = $this->config->getDatabaseName();

        if(empty($username) || empty($password) || empty($db)){
            LogService::info('数据库配置不能为空');
            exit('数据库配置不能为空');
        }
        $file = $this->getFile($fileName);

        if(file_exists($file)){
            unlink($file);
        }

        try {
            $cmd = "mysqldump -u".$username." -p".$password." ".$db." > ".$file;
            exec($cmd);
            LogService::info('导出数据文件成功 file:'.$file);
        }catch(\Exception $e){
            LogService::info('导出数据文件失败 原因:'.$e->getMessage());
        }

    }


    public function encryptOrDecryptFile($fileName, $encryptFileName, $type='encrypt')
    {
        try {
            $file = $this->getFile($fileName);
            $encryptFile = $this->getFile($encryptFileName);

            if(file_exists($encryptFile)){
                unlink($encryptFile);
            }

            if (!file_exists($file)) {
                LogService::info('文件不存在，不处理');
                exit('文件不存在，不处理');
            }



            echo $file.PHP_EOL;
            echo $encryptFile.PHP_EOL;

            $handle      = fopen($file, 'rb');
            $handleWrite = fopen($encryptFile, 'a');
            if($type == 'encrypt') {
                $length = 8192;
            }else{
                $length = 11008;
            }

            while (!feof($handle)) {
                $content = fread($handle, $length);


                if($type == 'encrypt') {
                    $encrypt_content = $this->encrypt($content);
                }else{
                    $encrypt_content = $this->decrypt($content);
                }

                if($encrypt_content) {
                    fwrite($handleWrite, $encrypt_content);
                }

                 flush();
                 ob_flush();
            }

            fclose($handle);
            fclose($handleWrite);

            if($type == 'encrypt'){
                LogService::info('加密文件成功 file:'.$encryptFile);
                echo '加密文件成功 file:'.$encryptFile.PHP_EOL;
            }else{
                LogService::info('解密文件成功 file:'.$encryptFile);
                echo '解密文件成功 file:'.$encryptFile.PHP_EOL;
            }

        }catch(\Exception $e){
            if($type == 'encrypt'){
                LogService::info('加密文件内容失败原因:'.$e->getMessage());
            }else{
                LogService::info('解密文件内容失败原因:'.$e->getMessage());
            }
        }

    }


    public function encrypt($data)
    {
        $key = $this->config->getSecret();
        $cipher = $this->config->getCipher();

        $ivlen = openssl_cipher_iv_length($cipher);
        $iv = openssl_random_pseudo_bytes($ivlen);

        $ciphertext_raw = openssl_encrypt($data, $cipher, $key, $options=OPENSSL_RAW_DATA, $iv);
        $hmac = hash_hmac('sha256', $ciphertext_raw, $key, $as_binary=true);
        $res = base64_encode( $iv.$hmac.$ciphertext_raw );

        return $res;
    }


    public function decrypt($data)
    {
        $key = $this->config->getSecret();
        $cipher = $this->config->getCipher();

        $c = base64_decode($data);
        $ivlen = openssl_cipher_iv_length($cipher);
        $iv = substr($c, 0, $ivlen);
        $hmac = substr($c, $ivlen, $sha2len=32);
        $ciphertext_raw = substr($c, $ivlen+$sha2len);
        $original_plaintext = openssl_decrypt($ciphertext_raw, $cipher, $key, $options=OPENSSL_RAW_DATA, $iv);
        $calcmac = hash_hmac('sha256', $ciphertext_raw, $key, $as_binary=true);
        if (hash_equals($hmac, $calcmac))//PHP 5.6+ timing attack safe comparison
        {
            return $original_plaintext;
        }

        LogService::info('解密失败');
       return null;
    }


    private function getFile($fileName)
    {
        $path = $this->config->getBackupPath();

        if(!is_dir($path)){
            mkdir($path, 0777, true);
        }

        $file = $path.$fileName;

        return $file;
    }


    public function downloadFile($localFileName, $remoteFileName, $date)
    {
        try {
            $domain = $this->config->getDomain();

            $path = $this->config->getKeysPath() . '/' . $date . '/' . $localFileName;

            $remoteFile = $this->getFile($remoteFileName);

            $handle      = fopen($domain . $path, 'rb');
            $handleWrite = fopen($remoteFile, 'a');

            while (!feof($handle)) {
                $content = fread($handle, 8192);

                fwrite($handleWrite, $content);
            }

            fclose($handle);
            fclose($handleWrite);
        }catch (\Exception $e){
            LogService::info('下载文件失败原因:'.$e->getMessage());
        }
    }

    public function restore($restoreFileName)
    {
        $username = $this->config->getUserName();
        $password = $this->config->getPassword();
        $db = $this->config->getDatabaseName();

        if(empty($username) || empty($password) || empty($db)){
            LogService::info('数据库配置不能为空');
            exit('数据库配置不能为空');
        }

        $restoreFile = $this->getFile($restoreFileName);

        if(!file_exists($restoreFile)){
            LogService::info('恢复文件不存在，不处理');
            exit('恢复文件不存在，不处理');
        }

        try{
            $cmd = 'mysql -u'.$username.' -p'.$password.' '.$db.' < '.$restoreFile;
            exec($cmd);
            LogService::info('数据恢复成功 file:'.$restoreFile);

        }catch(\Exception $e){
            LogService::info('数据恢复失败 原因:'.$e->getMessage());
        }
    }
}
