<?php
// +----------------------------------------------------------------------
// | likeshop100%开源免费商用商城系统
// +----------------------------------------------------------------------
// | 欢迎阅读学习系统程序代码，建议反馈是我们前进的动力
// | 开源版本可自由商用，可去除界面版权logo
// | 商业版本务必购买商业授权，以免引起法律纠纷
// | 禁止对系统程序代码以任何目的，任何形式的再发布
// | gitee下载：https://gitee.com/likeshop_gitee
// | github下载：https://github.com/likeshop-github
// | 访问官网：https://www.likeshop.cn
// | 访问社区：https://home.likeshop.cn
// | 访问手册：http://doc.likeshop.cn
// | 微信公众号：likeshop技术社区
// | likeshop团队 版权所有 拥有最终解释权
// +----------------------------------------------------------------------
// | author: likeshopTeam
// +----------------------------------------------------------------------
namespace app\adminapi\logic\skill;
use app\common\enum\ChatEnum;
use app\common\logic\ExportLogic;
use app\common\model\skill\Skill;
use app\common\model\skill\SkillCategory;
use app\common\service\ConfigService;
use app\common\service\FileService;
use app\common\service\UploadService;
use Exception;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;
use think\facade\Db;

/**
 * 技能逻辑类
 * Class CreationModelLogic
 * @package app\adminapi\logic\reation
 */
class SkillLogic
{

    /**
     * @notes 添加技能
     * @param array $post
     * @return bool
     * @author cjhao
     * @date 2023/4/14 14:57
     */
    public function add(array $post): bool
    {
        Skill::create($post);
        return true;
    }


    /**
     * @notes 修改
     * @param array $post
     * @return bool
     * @author cjhao
     * @date 2023/4/14 15:05
     */
    public function edit(array $post): bool
    {
        Skill::update($post);
        return true;
    }


    /**
     * @notes 删除
     * @param $id
     * @return bool|string
     * @author cjhao
     * @date 2023/4/14 15:23
     */
    public function del($id)
    {
        Skill::where(['id'=>$id])->delete();
        return true;
    }

    /**
     * @notes 获取详情
     * @param int $id
     * @return array
     * @author cjhao
     * @date 2023/4/17 10:14
     */
    public function detail(int $id):array
    {
//        $detail = Skill::withoutField('update_time,delete_time')->with(['category' => function ($query) {
//            $query->bind(['name']);
//        }])->where(['id'=>$id])->findOrEmpty()->toArray();
        $detail = Skill::withoutField('update_time,delete_time')->with(['category'])->where(['id'=>$id])->findOrEmpty()->toArray();
        // 读取默认数据
//        $channel = ConfigService::get('aiModel',  'channel', 'openai');
//        $defaultConfig = config('ai.ChatModels')[$channel];;
//        $detail['presence_penalty']  < 0 && $detail['presence_penalty']  = $defaultConfig['presence_penalty']  ?? 0.0;
//        $detail['frequency_penalty'] < 0 && $detail['frequency_penalty'] = $defaultConfig['frequency_penalty'] ?? 0.0;
//        $detail['n'] < 0 &&  $detail['n'] = $defaultConfig['n'] ?? '';

//        if(ChatEnum::ZHUPI_GLM_STD == $type ||  ChatEnum::ZHUPI_GLM_LITE == $type ){
//            $detail['presence_penalty'] = '';
//            $detail['frequency_penalty'] = '';
//            $detail['n'] = '';
//        }
        return $detail;
    }


    /**
     * @notes 修改状态
     * @param int $id
     * @return bool
     * @author cjhao
     * @date 2023/4/14 15:33
     */
    public function status(int $id){
        $skill = Skill::where(['id' => $id])->findOrEmpty();
        if($skill->isEmpty()){
            return true;
        }
        $skill->status = $skill->status ? 0 : 1;
        $skill->save();
        return true;
    }

    /**
     * @notes 技能导入
     * @param $file
     * @return false|string
     * @author cjhao
     * @date 2024/4/12 18:40
     */
    public function import($file)
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
            $drawings = $sheet->getDrawingCollection();
            $sheetIterator = $sheet->getRowIterator();
            //验证封装数据
            $excelData = $this->checkExcelContent($sheetIterator);
            //验证封装图片
            $this->checkExcelImage($drawings,$excelData);
            //导入数据库
            $this->importDb($excelData);
            //删除excel文件
            unlink($filePath);
            return '导入成功,一共导入'.count($excelData).'条数据';
        }catch (Exception $e) {
            @unlink($filePath);
            self::$error = $e->getMessage();
            return false;
        }
    }

    /**
     * @notes 验证封装文本内容
     * @param $cellIterator
     * @return array
     * @author cjhao
     * @date 2024/4/11 10:37
     */
    public function checkExcelContent($sheetIterator)
    {
        $excelData = [];
        foreach ($sheetIterator as $rowIndex => $row){
            //第一行是标题,跳过
            if(1 == $rowIndex){
                continue;
            }
            // 获取当前行的单元格迭代器
            $cellIterator = $row->getCellIterator();
            // 设置迭代器以遍历此行的所有单元格，即使它们未被设置（空白）
            $cellIterator->setIterateOnlyExistingCells(false);
            $data = [
                'status'            => 1,
                'sort'              => 0,
                'top_p'             => 0.9,
                'presence_penalty'  => 0.5,
                'n'                 => 1,
                'temperature'       => 0.6,
                'image'             => 'resource/image/adminapi/default/cs_app.png',
            ];
            $form = [];
            foreach ($cellIterator as $cellIndex => $cell) {
                $tipsIndex = $cellIndex.$rowIndex;
                $content = $cell->getValue();
                //图片格式内容
                if($cellIndex == 'B' && null != $content ){
                    if(false === filter_var($content)){
                        throw new Exception($tipsIndex.'单元格的模板链接并不是有效链接');
                    }
                    //不为空，说明读取的是链接，下载链接的数据
                    $imageFilePath = 'uploads/images/'.date('Ymd').'/';
                    $extension = pathinfo($content, PATHINFO_EXTENSION);
                    $imageFileName = md5($tipsIndex.microtime(true).rand(1,10000)).'.'.$extension;
                    $fileUrl = UploadService::baseOssSaveFile($content,$imageFilePath,$imageFileName);
                    $data['image'] = $fileUrl;
                }
                switch ($cellIndex){
                    //名称
                    case 'A':
                        if(empty($content)){
                            throw new Exception($tipsIndex.'单元格的模板名称缺少');
                        }
                        $data['name'] = $content;
                        break;
                    //描述
                    case 'C':
                        $data['describe'] = $content;
                        break;
                    //所属分类
                    case 'D':
                        if(empty($content)){
                            throw new Exception($tipsIndex.'单元格的所属分类缺少');
                        }
                        $data['category_name'] = $content;
                        break;
                    //调教文案
                    case 'E':
                        if(empty($content)){
                            throw new Exception($tipsIndex.'单元格的调教文案缺少');
                        }
                        $data['content'] = $content;
                        break;
                    //提示文字
                    case 'F':
                        if(empty($content)){
                            throw new Exception($tipsIndex.'单元格的提示文字缺少');
                        }
                        $data['tips'] = $content;
                        break;
                    //回复条数
                    case 'G':
                        $n = (int)$content;
                        if($n <= 0 || $n>= 5){
                            $n = 1;
                        }
                        $data['n'] = $n;
                        break;
                    //词汇属性
                    case 'H':
                        $temperature = (float)$content;
                        if($temperature <= 0 || $temperature >= 1){
                            $temperature = 0.6;
                        }
                        $data['temperature'] = $temperature;
                        break;
                    //随机属性
                    case 'I':
                        $topP = (float)$content;
                        if($topP <= 0 || $topP >= 1){
                            $topP = 0.9;
                        }
                        $data['top_p'] = $topP;
                        break;
                    //话题属性
                    case 'J':
                        $presencePenalty = (float)$content;
                        if($presencePenalty <= 0 || $presencePenalty >= 1){
                            $presencePenalty = 0.5;
                        }
                        $data['presence_penalty'] = $presencePenalty;
                        break;

                }
            }
            $excelData[$rowIndex] = $data;
        }
        return $excelData;
    }


    /**
     * @notes 验证封装图片
     * @param $drawings
     * @param $data
     * @return void
     * @author cjhao
     * @date 2024/4/11 14:59
     */
    public function checkExcelImage($drawings,&$excelData)
    {
        $imageFilePath = 'uploads/images/'.date('Ymd');
        // 目录不存在就新建
        if (!is_dir($imageFilePath)) {
            mkdir($imageFilePath, 0777, true);
        }
        foreach ($drawings as $index => $drawing){
            if (!($drawing instanceof Drawing)) {
                continue;
            }
            $cellCoordinate = $drawing->getCoordinates();
            $number = substr($cellCoordinate, 1);
            $data = $excelData[$number] ?? [];
            if(empty($data)){
                throw new Exception($cellCoordinate.'单元格图片位置错误，无法导入图片');
            }

            $sourcePath = $drawing->getPath();
            $extension = $drawing->getExtension();
            $imageFileName = md5($sourcePath.$index).'.'.$extension;
            $destination = $imageFilePath.'/'.$imageFileName;
            copy($sourcePath, $destination);          //保存图片
            $data['image'] = $destination;
            $excelData[$number] = $data;
            //如果是oss，将图片上传到oss上
            UploadService::saveOssFile($data['image']);
        }
    }

    /**
     * Todo 暂不考虑excel数据多情况
     * @notes 将excel导入数据库
     * @param $importDb
     * @return true
     * @author cjhao
     * @date 2024/4/11 15:20
     */
    public function importDb($excelData)
    {
        Db::startTrans();
        try {

            $categoryLists = SkillCategory::column('id', 'name');
            $nameLists = Skill::column('name');
            $repetitionName = [];
            foreach ($excelData as $excelKey => $data) {
                $categoryId = $categoryLists[$data['category_name']] ?? '';
                if (empty($categoryId)) {
                    $category = SkillCategory::create([
                        'name' => $data['category_name'],
                        'status' => 1,
                        'sort' => 0,
                    ]);
                    $categoryId = $category->id;
                    $categoryLists[$data['category_name']] = $category->id;
                }
                $excelData[$excelKey]['category_id'] = $categoryId;
                unset($excelData[$excelKey]['category_name']);
                if(in_array($data['name'],$nameLists)){
                    $number = $repetitionName[$data['name']] ?? 0;
                    $number++;
                    $excelData[$excelKey]['name'] = $data['name'].'-'.$number;
                    $repetitionName[$data['name']] = $number;
                }
            }
            (new Skill())->saveAll(array_values($excelData));
            Db::commit();
            return true;
        }catch (Exception $e) {
            Db::rollback();
            throw new Exception($e->getMessage());
        }

    }


    /**
     * @notes 创作模板下载
     * @return \think\response\File
     * @author cjhao
     * @date 2024/4/15 11:14
     */
    public function downExcelTemplate()
    {

        $filePath = 'resource/demo/技能模板.xlsx';
        $fileName = '技能模板.xlsx';

        return download($filePath,$fileName);
    }


    /**
     * @notes 技能导出
     * @param array $params
     * @return array
     * @author cjhao
     * @date 2024/4/12 11:50
     */
    public function export(array $params)
    {
        $categoryId = $params['id'] ?? '';
        $keyword = $params['keyword'] ?? '';
        $where = [];
        if($categoryId){
            $where[] = ['category_id','=',$categoryId];
        }
        if($keyword){
            $where[] = ['name','like','%'.$keyword.'%'];
        }
        $skillLists = Skill::where(['status'=>1])
            ->where($where)
            ->field('id,name,image,category_id,tips,describe,content,n,temperature,top_p,presence_penalty')
            ->order('sort desc,id desc')
            ->select()->toArray();

        $categoryIds = array_column($skillLists,'category_id');
        $categoryLists = SkillCategory::where(['id'=>$categoryIds])
            ->column('name','id');
        $exportTitle = [
            'name'              => '技能名称',
            'image'             => '技能图标',
            'describe'          => '技能描述',
            'category_name'     => '所属分类',
            'content'           => '调教文案',
            'tips'              => '提示文字',
            'n'                 => '回复条数',
            'temperature'       => '词汇属性',
            'top_p'             => '随机属性',
            'presence_penalty'  => '话题属性',
        ];
        $exportData = [];
        foreach ($skillLists as $skill){
            $exportData[] = [
                'name'              => $skill['name'],
                'image'             => $skill['image'],
                'describe'          => $skill['describe'],
                'category_name'     => $categoryLists[$skill['category_id']] ?? '',
                'content'           => $skill['content'],
                'tips'              => $skill['tips'],
                'n'                 => $skill['n'],
                'temperature'       => $skill['temperature'],
                'top_p'             => $skill['top_p'],
                'presence_penalty'  => $skill['presence_penalty'],
            ];
        }
        $data = ExportLogic::exportData($exportTitle,$exportData,'技能提示词');
        return ['url'=>$data];

    }
}