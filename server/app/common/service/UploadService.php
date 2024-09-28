<?php
// +----------------------------------------------------------------------
// | likeadmin快速开发前后端分离管理后台（PHP版）
// +----------------------------------------------------------------------
// | 欢迎阅读学习系统程序代码，建议反馈是我们前进的动力
// | 开源版本可自由商用，可去除界面版权logo
// | gitee下载：https://gitee.com/likeshop_gitee/likeadmin
// | github下载：https://github.com/likeshop-github/likeadmin
// | 访问官网：https://www.likeadmin.cn
// | likeadmin团队 版权所有 拥有最终解释权
// +----------------------------------------------------------------------
// | author: likeadminTeam
// +----------------------------------------------------------------------

namespace app\common\service;

use app\common\enum\FileEnum;
use app\common\model\file\File;
use app\common\service\storage\Driver as StorageDriver;
use Exception;

class UploadService
{
    /**
     * @notes 上传图片
     * @param $cid
     * @param int $sourceId
     * @param int $source
     * @param string $saveDir
     * @return array
     * @throws Exception
     * @author 段誉
     * @date 2021/12/29 16:30
     */
   public static function image($cid, int $sourceId = 0, int $source = FileEnum::SOURCE_ADMIN, string $saveDir = 'uploads/images'): array
   {
        try {
            $config = [
                'default' => ConfigService::get('storage', 'default', 'local'),
                'engine'  => ConfigService::get('storage') ?? ['local'=>[]],
            ];

            // 2、执行文件上传
            $StorageDriver = new StorageDriver($config);
            $StorageDriver->setUploadFile('file');
            $fileName = $StorageDriver->getFileName();
            $fileInfo = $StorageDriver->getFileInfo();

            // 校验上传文件后缀
            if (!in_array(strtolower($fileInfo['ext']), config('project.file_image'))) {
                throw new Exception("上传图片不允许上传". $fileInfo['ext'] . "文件");
            }

            // 上传文件
            $saveDir = $saveDir . '/' .  trim(date('Ymd'));
            if (!$StorageDriver->upload($saveDir)) {
                throw new Exception($StorageDriver->getError());
            }

            // 3、处理文件名称
            if (strlen($fileInfo['name']) > (129 - 1)) {
                $name = substr($fileInfo['name'], 0, 123);
                $nameEnd = substr($fileInfo['name'], strlen($fileInfo['name'])-5, strlen($fileInfo['name']));
                $fileInfo['name'] = $name . $nameEnd;
            }

            // 4、写入数据库中
            $file = File::create([
                'cid'         => $cid,
                'type'        => FileEnum::IMAGE_TYPE,
                'name'        => trim($fileInfo['name']),
                'uri'         => $saveDir . '/' . str_replace("\\","/", $fileName),
                'source'      => $source,
                'source_id'   => $sourceId,
                'create_time' => time(),
            ]);

            // 5、返回结果
            return [
                'id'   => $file['id'],
                'cid'  => $file['cid'],
                'type' => $file['type'],
                'name' => $file['name'],
                'uri'  => FileService::getFileUrl($file['uri']),
                'url'  => $file['uri']
            ]??[];

        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }

    /**
     * @notes 视频上传
     * @param $cid
     * @param int $sourceId
     * @param int $source
     * @param string $saveDir
     * @return array
     * @throws Exception
     * @author 段誉
     * @date 2021/12/29 16:32
     */
    public static function video($cid, int $sourceId = 0, int $source = FileEnum::SOURCE_ADMIN, string $saveDir = 'uploads/video'): array
    {
        try {
            $config = [
                'default' => ConfigService::get('storage', 'default', 'local'),
                'engine'  => ConfigService::get('storage') ?? ['local'=>[]],
            ];

            // 2、执行文件上传
            $StorageDriver = new StorageDriver($config);
            $StorageDriver->setUploadFile('file', FileEnum::VIDEO_TYPE);
            $fileName = $StorageDriver->getFileName();
            $fileInfo = $StorageDriver->getFileInfo();

            // 校验上传文件后缀
            if (!in_array(strtolower($fileInfo['ext']), config('project.file_video'))) {
                throw new Exception("上传视频不允许上传". $fileInfo['ext'] . "文件");
            }

            // 上传文件
            $saveDir = $saveDir . '/' .  date('Ymd');
            if (!$StorageDriver->upload($saveDir)) {
                throw new Exception($StorageDriver->getError());
            }

            // 3、处理文件名称
            if (strlen($fileInfo['name']) > 128) {
                $name = substr($fileInfo['name'], 0, 123);
                $nameEnd = substr($fileInfo['name'], strlen($fileInfo['name'])-5, strlen($fileInfo['name']));
                $fileInfo['name'] = $name . $nameEnd;
            }

            // 4、写入数据库中
            $file = File::create([
                'cid'         => $cid,
                'type'        => FileEnum::VIDEO_TYPE,
                'name'        => $fileInfo['name'],
                'uri'         => $saveDir . '/' . str_replace("\\","/", $fileName),
                'source'      => $source,
                'source_id'   => $sourceId,
                'create_time' => time(),
            ]);

            // 5、返回结果
            return [
                'id'   => $file['id'],
                'cid'  => $file['cid'],
                'type' => $file['type'],
                'name' => $file['name'],
                'uri'  => FileService::getFileUrl($file['uri']),
                'url'  => $file['uri']
            ]??[];
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }

    /**
     * @notes 文件上传
     * @param $cid
     * @param int $sourceId
     * @param int $source
     * @param string $saveDir
     * @return array
     * @throws Exception
     * @author fzr
     */
    public static function files($cid, int $sourceId = 0, int $source = FileEnum::SOURCE_ADMIN, string $saveDir = 'uploads/files'): array
    {
        try {
            $config = [
                'default' => ConfigService::get('storage', 'default', 'local'),
                'engine'  => ConfigService::get('storage') ?? ['local'=>[]],
            ];

            // 2、执行文件上传
            $StorageDriver = new StorageDriver($config);
            $StorageDriver->setUploadFile('file', FileEnum::FILE_TYPE);
            $fileName = $StorageDriver->getFileName();
            $fileInfo = $StorageDriver->getFileInfo();

            // 上传文件
            $saveDir = $saveDir . '/' .  date('Ymd');
            if (!$StorageDriver->upload($saveDir)) {
                throw new Exception($StorageDriver->getError());
            }

            // 3、处理文件名称
            if (strlen($fileInfo['name']) > 128) {
                $name = substr($fileInfo['name'], 0, 124);
                $nameEnd = substr($fileInfo['name'], strlen($fileInfo['name'])-5, strlen($fileInfo['name']));
                $fileInfo['name'] = $name . $nameEnd;
            }

            // 4、写入数据库中
            $file = File::create([
                'cid'         => $cid,
                'name'        => $fileInfo['name'],
                'type'        => FileEnum::FILE_TYPE,
                'uri'         => $saveDir . '/' . str_replace("\\","/", $fileName),
                'source_id'   => $sourceId,
                'source'      => $source,
                'create_time' => time(),
            ]);

            // 5、返回结果
            return [
                    'id'   => $file['id'],
                    'cid'  => $file['cid'],
                    'type' => $file['type'],
                    'name' => $file['name'],
                    'uri'  => FileService::getFileUrl($file['uri']),
                    'url'  => $file['uri']
                ]??[];

        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }

    /**
     * @notes 音频上传
     * @param $cid
     * @param int $sourceId
     * @param int $source
     * @param string $saveDir
     * @return array
     * @throws Exception
     * @author cjhao
     * @date 2023/11/9 19:14
     */
    public static function audio($cid,int $sourceId = 0, int $source = FileEnum::SOURCE_ADMIN, string $saveDir = 'uploads/audio'):array
    {
        try {
            $config = [
                'default' => ConfigService::get('storage', 'default', 'local'),
                'engine'  => ConfigService::get('storage') ?? ['local'=>[]],
            ];

            // 2、执行文件上传
            $StorageDriver = new StorageDriver($config);
            $StorageDriver->setUploadFile('file');
            $fileName = $StorageDriver->getFileName();
            $fileInfo = $StorageDriver->getFileInfo();

            // 校验上传文件后缀
            if (!in_array(strtolower($fileInfo['ext']), config('project.file_audio'))) {
                throw new Exception("上传音频不允许上传". $fileInfo['ext'] . "文件");
            }

            // 上传文件
            $saveDir = $saveDir . '/' .  date('Ymd');
            if (!$StorageDriver->upload($saveDir)) {
                throw new Exception($StorageDriver->getError());
            }

            // 3、处理文件名称
            if (strlen($fileInfo['name']) > 128) {
                $name = substr($fileInfo['name'], 0, 122);
                $nameEnd = substr($fileInfo['name'], strlen($fileInfo['name'])-5, strlen($fileInfo['name']));
                $fileInfo['name'] = $name . $nameEnd;
            }

            // 4、写入数据库中
            $file = File::create([
                'cid'         => $cid,
                'name'        => $fileInfo['name'],
                'type'        => FileEnum::AUDIO_TYPE,
                'uri'         => $saveDir . '/' . str_replace("\\","/", $fileName),
                'source'      => $source,
                'source_id'   => $sourceId,
                'create_time' => time(),
            ]);

            // 5、返回结果
            return [
                    'id'   => $file['id'],
                    'cid'  => $file['cid'],
                    'type' => $file['type'],
                    'name' => $file['name'],
                    'uri'  => FileService::getFileUrl($file['uri']),
                    'url'  => $file['uri']
                ]??[];

        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }


    /**
     * @notes 保存文件到指定存储
     * @param string $saveDir
     * @param string $storage
     * @return array
     * @throws Exception
     * @author cjhao
     * @date 2024/4/10 16:48
     */
    public static function saveFileStorage(string $saveDir = 'uploads/file',string $storage = 'local'): array
    {
        try {
            $config = [
                'default' => $storage,
                'engine' => ConfigService::get('storage') ?? ['local' => []],
            ];
            // 2、执行文件上传
            $StorageDriver = new StorageDriver($config,$storage);
            $StorageDriver->setUploadFile('file',FileEnum::FILE_TYPE);
            if (!$StorageDriver->upload($saveDir)) {
                throw new Exception($StorageDriver->getError());
            }

            $fileName = $StorageDriver->getFileName();
            $fileInfo = $StorageDriver->getFileInfo();

            // 3、处理文件名称
            if (strlen($fileInfo['name']) > 128) {
                $file_name = substr($fileInfo['name'], 0, 123);
                $file_end = substr($fileInfo['name'], strlen(trim($fileInfo['name'])) - 5, strlen($fileInfo['name']));
                $fileInfo['name'] = $file_name . $file_end;
            }
            $uri = $saveDir . '/' . str_replace("\\", "/", $fileName);

            // 5、返回结果
            return [
                'uri' => FileService::getFileUrl($uri),
                'url' => $uri
            ];

        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }

    /**
     * @notes 判断当前存储配置(是否需要保存到oss)
     * @param $url
     * @return true
     * @throws Exception
     * @author fzr
     */
    public static function saveOssFile($url): bool
    {
        $config = [
            'default' => ConfigService::get('storage', 'default', 'local'),
            'engine' => ConfigService::get('storage'),
        ];

        if('local' == $config['default']){
            return true;
        }

        // 这里不使用https,部分用户可能因为证书问题报错
        $localFileUrl = request()->domain(true).'/'.$url;
        $StorageDriver = new StorageDriver($config);
        if (!$StorageDriver->fetch($localFileUrl, $url)) {
            throw new Exception('文件保存失败:' . $StorageDriver->getError());
        }

        // 如果是oss,删除本地文件
        unlink($url);
        return true;
    }


    /**
     * @notes 根据oss下载文件
     * @param $teFileUrl
     * @param $savePath
     * @param $fileName
     * @return string
     * @throws Exception
     * @author cjhao
     * @date 2024/4/15 10:34
     */
    public static function baseOssSaveFile($teFileUrl,$savePath,$fileName): string
    {
        $config = [
            'default' => ConfigService::get('storage', 'default', 'local'),
            'engine' => ConfigService::get('storage')
        ];
        if ($config['default'] == 'local') {
            // 本地存储
            $filePath = download_file($teFileUrl,$savePath,$fileName);
        } else {
            // 第三方存储
            $StorageDriver = new StorageDriver($config);
            if (!$StorageDriver->fetch($teFileUrl,$savePath.$fileName)) {
                throw new Exception('图标下载失败:' . $StorageDriver->getError());
            }
        }
        return $savePath.$fileName;
    }
}