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

namespace app\adminapi\logic\setting;

use app\common\cache\KeyPoolCache;
use app\common\enum\ChatEnum;
use app\common\enum\draw\DrawEnum;
use app\common\enum\MusicEnum;
use app\common\enum\PoolEnum;
use app\common\enum\SearchEnum;
use app\common\enum\VideoEnum;
use app\common\enum\VoiceEnum;
use app\common\logic\BaseLogic;
use app\common\model\chat\KeyPool;
use app\common\model\chat\Models;
use app\common\model\chat\ModelsCost;
use app\common\service\UploadService;
use Exception;
use PhpOffice\PhpSpreadsheet\IOFactory;

/**
 * Key池逻辑类
 */
class KeyPoolLogic extends BaseLogic
{
    /**
     * @param int $type
     * @return array|int[]
     * @throws @\think\db\exception\DataNotFoundException
     * @throws @\think\db\exception\DbException
     * @throws @\think\db\exception\ModelNotFoundException
     */
    public static function models(int $type): array
    {
        switch ($type) {
            case PoolEnum::TYPE_CHAT:
                return (new Models())
                    ->field('id,channel,name')
                    ->where(['type' => $type])
                    ->order('sort asc, id desc')
                    ->select()->toArray();
            case PoolEnum::TYPE_EMB:
                $lists = (new Models())
                    ->field('id,channel,name')
                    ->where(['type' => $type])
                    ->order('sort asc, id desc')
                    ->select()->toArray();

                if ($lists) {
                    $ids = array_column($lists, 'id');
                    $sub = (new ModelsCost())->whereIn('model_id', $ids)->column('name', 'model_id');
                    foreach ($lists as &$item) {
                        $item['model'] = $sub[$item['id']];
                    }
                }
                return $lists;
            case PoolEnum::TYPE_VOICE_OUTPUT:
            case PoolEnum::TYPE_VOICE_INPUT:
                return VoiceEnum::getChannel();
            case PoolEnum::TYPE_MUSIC:
                return MusicEnum::getChannel();
            case PoolEnum::TYPE_VIDEO:
                return VideoEnum::getChannel();
            case PoolEnum::TYPE_SEARCH:
                return SearchEnum::getChannel();
            case PoolEnum::TYPE_DRAW:
                return DrawEnum::getAiModelName();
            default:
                return [];
        }
    }

    /**
     * @notes Key详情
     * @param int $id
     * @return array
     * @author fzr
     */
    public static function detail(int $id): array
    {
        $model = new KeyPool();
        return $model
            ->withoutField('delete_time')
            ->where(['id'=>$id])
            ->findOrEmpty()
            ->toArray();
    }

    /**
     * @notes Key新增
     * @param array $post
     * @return bool
     * @author fzr
     */
    public static function add(array $post): bool
    {
        try {
            if (in_array($post['type'], [ChatEnum::MODEL_TYPE_CHAT, ChatEnum::MODEL_TYPE_EMB])) {
                if (empty($post['model_id'])) {
                    throw new Exception('model_id参数缺失');
                }
                if ((new Models())->where(['id'=>$post['model_id']])->findOrEmpty()->isEmpty()) {
                    throw new Exception('关联渠道已不存在');
                }
            }

             KeyPool::create([
                 'model_id'    => intval($post['model_id'] ?? 0),
                 'type'        => $post['type']    ?? 1,
                 'channel'     => $post['channel'] ?? '',
                 'key'         => $post['key']     ?? '',
                 'appid'       => $post['appid']   ?? '',
                 'secret'      => $post['secret']  ?? '',
                 'status'      => $post['status']  ?? 0,
                 'remark'      => $post['remark'] ?? '',
                 'create_time' => time(),
                 'update_time' => time()
             ]);

             (new KeyPoolCache($post['channel']))->delKey();
             return true;
        } catch (Exception $e) {
            self::setError($e->getMessage());
            return false;
        }
    }

    /**
     * @notes Key编辑
     * @param array $post
     * @return bool
     * @author fzr
     */
    public static function edit(array $post): bool
    {
        try {
            $keyPool = (new KeyPool())->where(['id'=>intval($post['id'])])->findOrEmpty();
            if (!$keyPool) {
                throw new Exception('密钥不存在了!');
            }

            if (in_array($post['type'], [ChatEnum::MODEL_TYPE_CHAT, ChatEnum::MODEL_TYPE_EMB])) {
                if (empty($post['model_id'])) {
                    throw new Exception('model_id参数缺失');
                }
                if ((new Models())->where(['id'=>$post['model_id']])->findOrEmpty()->isEmpty()) {
                    throw new Exception('关联渠道已不存在');
                }
            }

            $api = $keyPool->api;
            $notice = $keyPool->notice;
            if (($post['status'] ??0)) {
                $api = '';
                $notice = '';
            }

            KeyPool::update([
                'type'    => $post['type']    ?? 1,
                'model_id' => $post['model_id']    ?? 0,
                'channel' => $post['channel'] ?? '',
                'key'     => $post['key']     ?? '',
                'appid'   => $post['appid']   ?? '',
                'secret'  => $post['secret']  ?? '',
                'status'  => $post['status']  ?? 0,
                'remark'  => $post['remark'] ?? '',
                'api'     => $api,
                'notice'  => $notice
            ], ['id'=>intval($post['id'])]);

            (new KeyPoolCache($post['channel']))->delKey();
            return true;
        } catch (Exception $e) {
            self::setError($e->getMessage());
            return false;
        }
    }

    /**
     * @notes Key删除
     * @param int $id
     * @return bool
     */
    public static function del(int $id): bool
    {
        try {
            $keyPool = (new KeyPool())->where(['id'=>$id])->findOrEmpty();
            $channel = $keyPool->channel;
            $keyPool->delete();

            (new KeyPoolCache($channel))->delKey();
            return true;
        } catch (Exception $e) {
            self::setError($e->getMessage());
            return false;
        }
    }

    /**
     * @notes Key状态
     * @param int $id
     * @return bool
     * @author fzr
     */
    public static function status(int $id): bool
    {
        try {
            $keyPool = (new KeyPool())->where(['id'=>$id])->findOrEmpty();
            $keyPool->status = $keyPool->status ? 0 : 1;
            if ($keyPool->status) {
                $keyPool->api = '';
                $keyPool->notice = '';
                self::setError('启用成功');
            } else {
                self::setError('禁用成功');
            }
            $keyPool->save();

            (new KeyPoolCache($keyPool->channel))->delKey();
            return true;
        } catch (Exception $e) {
            self::setError($e->getMessage());
            return false;
        }
    }

    /**
     * @notes excel导入
     * @param $file
     * @param $post
     * @return false|string
     * @author ljj
     * @date 2024/4/15 10:28 上午
     */
    public function import($file,$post): bool|string
    {
        try {
            $filePath = '';
            if(empty($file)){
                throw new Exception('请上传文件');
            }
            $result = UploadService::saveFileStorage();
            $filePath = $result['url'];
            //解析验证数据
            $reader = IOFactory::createReaderForFile($filePath);
            $spreadsheet = $reader->load($filePath);
            $sheet = $spreadsheet->getActiveSheet();
            $sheetIterator = $sheet->getRowIterator();
            //获取总行数
            $highestRow = $sheet->getHighestRow();
            //校验文档数据
            $excelData = $this->checkExcelContent($sheetIterator,$highestRow,$post['type']);
            //导入数据库
            (new KeyPool())->saveAll(array_values($excelData));
            //删除excel文件
            unlink($filePath);
            return '导入成功,一共导入'.count($excelData).'条数据';
        }catch (\Exception $e) {
            @unlink($filePath);
            self::$error = $e->getMessage();
            return false;
        }
    }

    /**
     * @notes 校验文档数据
     * @param $sheetIterator
     * @param $highestRow
     * @param $type
     * @return array
     * @throws Exception
     * @author ljj
     * @date 2024/4/15 10:25 上午
     */
    public function checkExcelContent($sheetIterator,$highestRow,$type): array
    {
        $excelData = [];
        foreach ($sheetIterator as $rowIndex => $row){
            //超出总行数，跳出循环
//            if ($highestRow <= $rowIndex) {
//                break;
//            }
            //第一行是标题,跳过
            if(1 == $rowIndex){
                continue;
            }
            // 获取当前行的单元格迭代器
            $cellIterator = $row->getCellIterator();
            // 设置迭代器以遍历此行的所有单元格，即使它们未被设置（空白）
            $cellIterator->setIterateOnlyExistingCells(false);
            $data = [
                'type'              => $type,
                'channel'            => '',
                'key'               => '',
                'appid'             => '',
                'secret'            => '',
                'status'            => 1,
                'remark'            => ''
            ];
            foreach ($cellIterator as $cellIndex => $cell) {
                $content = $cell->getValue();
                $tipsIndex = $cellIndex.$rowIndex;
                switch ($cellIndex){
                    //接口类型
                    case 'A':
                        if(empty($content)){
                            throw new Exception($tipsIndex.'单元格的接口类型缺少');
                        }
                        $getChatName = ChatEnum::getAiModelName(true);
                        $key = array_search($content, $getChatName);
                        if($key === false) {
                            throw new Exception($tipsIndex.'单元格的接口类型错误');
                        }
                        $data['channel'] = $key;
                        break;
                    //APIKey
                    case 'B':
                        if(empty($content)){
                            throw new Exception($tipsIndex.'单元格的APIKey缺少');
                        }
                        $data['key'] = $content;
                        break;
                    //APPID
                    case 'C':
//                        if(empty($content) && ($data['ai_key'] == ChatEnum::XUNFEI || $data['ai_key'] == ChatEnum::HUNYUAN || $data['ai_key'] == ChatEnum::MINIMAX)){
//                            throw new Exception($tipsIndex.'单元格的APPID缺少');
//                        }
                        if(empty($content) && ($data['channel'] == ChatEnum::XUNFEI)){
                            throw new Exception($tipsIndex.'单元格的APPID缺少');
                        }
                        $data['appid'] = $content;
                        break;
                    //APISecret
                    case 'D':
                        if(empty($content) && ($data['channel'] == ChatEnum::XUNFEI || $data['channel'] == ChatEnum::BAIDU)){
                            throw new Exception($tipsIndex.'单元格的APISecret缺少');
                        }
                        $data['secret'] = $content;
                        break;
                    //备注
                    case 'E':
                        $data['remark'] = $content;
                        break;
                    //状态
                    case 'F':
                        $data['status'] = (int)$content;
                        break;
                }
            }
            $excelData[$rowIndex] = $data;
        }
        return $excelData;
    }
}