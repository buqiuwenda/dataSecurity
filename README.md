# 简介
 基于PHP开发mysql数据库备份加密和解密恢复。加密算法`AES-256-CBC`，七牛云存储下载和上传。希望对大家工作有所帮助，最后希望大家帮忙打星。

## 使用步骤
 * 下载程序 `git clone git@github.com:buqiuwenda/dataSecurity.git` && 执行`composer install`
 * 修改相关配置`configs.php`，如数据库连接配置，七牛密钥配置
 * 数据备份项目目录下执行`php Console.php test.sql` ，也可以加入crontab定时执行
 * 数据恢复项目目录下执行`php Console.php encrypt_test.sql decrypt {YYYYmmdd}`。最后是日期参数20190531
 

## Security Vulnerabilities
- [WenDa](http://github.com/buqiuwenda)

## Thanks

- [PHP manual](https://php.net/manual/zh/)
- [qiniu cloud](https://developer.qiniu.com/)

## Server Requirements
- PHP >= 7.1.0
- Mysql = 5.7
- Centos = 7.0

## License

The Lumen framework is open-sourced software licensed under the [MIT license](http://opensource.org/licenses/MIT)
