<?php

namespace app\common\service\storage\engine;

use OSS\OssClient;
use OSS\Core\OssException;

/**
 * 阿里云存储引擎 (OSS)
 * Class Qiniu
 * @package app\common\library\storage\engine
 */
class Aliyun extends Server
{
    private mixed $config;

    /**
     * 构造方法
     * Aliyun constructor.
     * @param $config
     */
    public function __construct($config)
    {
        parent::__construct();
        $this->config = $config;
    }

    /**
     * 执行上传
     * @param $save_dir (保存路径)
     * @return bool
     */
    public function upload($save_dir): bool
    {
        try {
            $ossClient = new OssClient(
                $this->config['access_key'],
                $this->config['secret_key'],
                $this->config['domain'],
                true
            );
            $ossClient->uploadFile(
                $this->config['bucket'],
                $save_dir . '/' . $this->fileName,
                $this->getRealPath()
            );
        } catch (OssException $e) {
            $this->error = $e->getMessage();
            return false;
        }
        return true;
    }

    /**
     * Notes: 抓取远程资源
     * @param $url
     * @param null $key
     * @return bool
     * @author 张无忌(2021/3/2 14:36)
     */
    public function fetch($url, $key = null): bool
    {
        try {
            $ossClient = new OssClient(
                $this->config['access_key'],
                $this->config['secret_key'],
                $this->config['domain'],
                true
            );

            $content = file_get_contents($url);
            $ossClient->putObject(
                $this->config['bucket'],
                $key,
                $content
            );
        } catch (OssException $e) {
            $this->error = $e->getMessage();
            return false;
        }
        return true;
    }

    /**
     * 删除文件
     * @param $fileName
     * @return bool
     */
    public function delete($fileName): bool
    {
        $ossClient = new OssClient(
            $this->config['access_key'],
            $this->config['secret_key'],
            $this->config['domain'],
            true
        );
        $ossClient->deleteObject($this->config['bucket'], $fileName);
        return true;
    }

    /**
     * 返回文件路径
     * @return mixed
     */
    public function getFileName(): mixed
    {
        return $this->fileName;
    }
}
