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

namespace app\adminapi\logic\creation;
use app\common\logic\BaseLogic;
use app\common\logic\ExportLogic;
use app\common\model\creation\CreationCategory;
use app\common\model\creation\CreationModel;
use app\common\service\ConfigService;
use app\common\service\FileService;
use app\common\service\storage\Driver as StorageDriver;
use app\common\service\UploadService;
use Exception;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;
use think\facade\Config;
use think\facade\Db;
use think\response\File;

/**
 * 创作模型逻辑类
 */
class CreationModelLogic extends BaseLogic
{
    /**
     * @notes 创作模型详情
     * @param int $id
     * @return array
     * @author fzr
     */
    public static function detail(int $id): array
    {
        $detail = (new CreationModel())
            ->withoutField('create_time,update_time,delete_time')
            ->with(['category'])
            ->where(['id'=>$id])
            ->findOrEmpty()
            ->toArray();

        // 读取默认数据
//        $channel = ConfigService::get('aiModel',  'channel', 'openai');
//        $defaultConfig = config('ai.ChatModels')[$channel];;
//        $detail['presence_penalty']  < 0 && $detail['presence_penalty']  = $defaultConfig['presence_penalty']  ?? 0.0;
//        $detail['frequency_penalty'] < 0 && $detail['frequency_penalty'] = $defaultConfig['frequency_penalty'] ?? 0.0;
//        $detail['n'] < 0 &&  $detail['n'] = $defaultConfig['n'] ?? '';

        $detail['form'] = empty($detail['form']) ? [] : $detail['form'];
        return $detail;
    }

    /**
     * @notes 创作模型新增
     * @param array $post
     * @return bool
     * @author fzr
     */
    public static function add(array $post): bool
    {
        try {
            CreationModel::create([
                'name'              => $post['name'],
                'image'             => $post['image']             ?? '',
                'sort'              => $post['sort']              ?? 0,
                'category_id'       => $post['category_id']       ?? 0,
                'status'            => $post['status']            ?? 0,
                'content'           => $post['content']           ?? '',
                'tips'              => $post['tips']              ?? '',
                'context_num'       => $post['context_num']       ?? 0,
                'n'                 => $post['n']                 ?? 0,
                'top_p'             => $post['top_p']             ?? 0,
                'presence_penalty'  => $post['presence_penalty']  ?? 0,
                'frequency_penalty' => $post['frequency_penalty'] ?? 0,
                'temperature'       => $post['temperature']       ?? 0,
                'max_tokens'        => $post['max_tokens']        ?? 0,
                'virtual_use_num'   => $post['virtual_use_num']   ?? 0,
                'form'              => $post['form'],
                'system'            => $post['system']            ?? '',
            ]);

            return true;
        } catch (Exception $e) {
            self::setError($e->getMessage());
            return false;
        }
    }

    /**
     * @notes 创作模型编辑
     * @param array $post
     * @return bool
     * @author fzr
     */
    public static function edit(array $post): bool
    {
        try {
            CreationModel::update([
                'name'              => $post['name'],
                'image'             => $post['image']             ?? '',
                'sort'              => $post['sort']              ?? 0,
                'category_id'       => $post['category_id']       ?? 0,
                'status'            => $post['status']            ?? 0,
                'content'           => $post['content']           ?? '',
                'tips'              => $post['tips']              ?? '',
                'context_num'       => $post['context_num']       ?? 0,
                'n'                 => $post['n']                 ?? 0,
                'top_p'             => $post['top_p']             ?? 0,
                'presence_penalty'  => $post['presence_penalty']  ?? 0,
                'frequency_penalty' => $post['frequency_penalty'] ?? 0,
                'temperature'       => $post['temperature']       ?? 0,
                'max_tokens'        => $post['max_tokens']        ?? 0,
                'virtual_use_num'   => $post['virtual_use_num']   ?? 0,
                'form'              => $post['form'],
                'system'            => $post['system']            ?? '',
                'update_time'       => time()
            ], ['id'=>intval($post['id'])]);

            return true;
        } catch (Exception $e) {
            self::setError($e->getMessage());
            return false;
        }
    }

    /**
     * @notes 创作模型删除
     * @param int $id
     * @return bool
     * @author fzr
     */
    public static function del(int $id): bool
    {
        try {
            CreationModel::destroy($id);
            return true;
        } catch (Exception $e) {
            self::setError($e->getMessage());
            return false;
        }
    }

    /**
     * @notes 批量删除
     * @param array $ids
     * @return bool
     * @author cjhao
     * @date 2024/7/17 10:29
     */
    public static function batchDel(array $ids){
        try {
            CreationModel::destroy($ids);
            return true;
        } catch (Exception $e) {
            self::setError($e->getMessage());
            return false;
        }
    }

    /**
     * @notes 创作模型状态
     * @param int $id
     * @return bool
     * @author fzr
     */
    public static function status(int $id): bool
    {
        try {
            $creationModel = (new CreationModel())->findOrEmpty($id);
            if($creationModel->isEmpty()){
                return true;
            }
            $creationModel->status = $creationModel->status ? 0 : 1;
            $creationModel->save();
            return true;
        } catch (Exception $e) {
            self::setError($e->getMessage());
            return false;
        }
    }


    /**
     * @notes 保存文件到指定存储
     * @param string $saveDir
     * @return array
     * @throws Exception
     * @author cjhao
     * @date 2024/4/10 16:48
     */
    public static function saveFileStorage(string $saveDir = 'uploads/file',string $storage = 'local')
    {
        try {
            $config = [
                'default' => $storage,
                'engine' => ConfigService::get('storage') ?? ['local' => []],
            ];
            // 2、执行文件上传
            $StorageDriver = new StorageDriver($config,$storage);
            $StorageDriver->setUploadFile('file');
            if (!$StorageDriver->upload($saveDir)) {
                throw new Exception($StorageDriver->getError());
            }

            $fileName = $StorageDriver->getFileName();
            $fileInfo = $StorageDriver->getFileInfo();

            // 3、处理文件名称
            if (strlen($fileInfo['name']) > 128) {
                $file_name = substr($fileInfo['name'], 0, 123);
                $file_end = substr($fileInfo['name'], strlen($fileInfo['name']) - 5, strlen($fileInfo['name']));
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
     * @notes excel导入
     * @param $file
     * @return string|void
     * @throws \PhpOffice\PhpSpreadsheet\Reader\Exception
     * @author cjhao
     * @date 2024/4/11 15:18
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
                $content = $cell->getValue();
                $tipsIndex = $cellIndex.$rowIndex;
                //图片格式
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
                    //模型名称
                    case 'A':
                        if(empty($content)){
                            throw new Exception($tipsIndex.'单元格的模板名称缺少');
                        }
                        $data['name'] = $content;
                        break;
                    //模型描述
                    case 'C':
                        if(empty($content)){
                            throw new Exception($tipsIndex.'单元格的模型描述缺少');
                        }
                        $data['tips'] = $content;
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
                    //单行文本
                    case 'F':
                        if(empty($content)){
                            break;
                        }
                        $dataList = explode('｜',$content);
                        //单行文本数据在4个
                        if(count($dataList) != 4){
                            throw new Exception($tipsIndex.'单元格的单行文本数据错误');
                        }
                        $id =  substr(md5($content.microtime(true)), 0,8);
                        $field = substr(md5($content.rand(1,1000)),0,6);
                        $title = str_replace('【字段标题】','',$dataList[0]);
                        $placeholder = str_replace('【示例文字】','',$dataList[1]);
                        $maxlength = intval(str_replace('【最大输入长度】','',$dataList[2]));
                        $isRequired = str_replace('【是否必填】','',$dataList[3]) == '是' ? true : false;
                        $form[] = [
                            'name'  => 'WidgetInput',
                            'title' => '单行文本',
                            'id'    => $id,
                            'props' => [
                                'title'         => $title,
                                'field'         => $field,
                                'defaultValue'  => '',
                                'placeholder'   => $placeholder,
                                'maxlength'     => $maxlength,
                                'isRequired'    => $isRequired,
                            ],
                            'key'   => 'field1',
                        ];
                        break;
                    //多行文本
                    case 'G':
                        if(empty($content)){
                            break;
                        }
                        $dataList = explode('｜',$content);
                        //多行文本数据在6个
                        if(count($dataList) != 6){
                            throw new Exception($tipsIndex.'单元格的多行文本数据错误');
                        }
                        $id =  substr(md5($content.microtime(true)), 0,8);
                        $field = substr(md5($content.rand(1,1000)),0,6);
                        $title = str_replace('【字段标题】','',$dataList[0]);
                        $placeholder = str_replace('【示例文字】','',$dataList[1]);
                        $row = str_replace('【默认行数】','',$dataList[2]);
                        $maxlength = intval(str_replace('【最大输入长度】','',$dataList[3]));
                        $autosize = str_replace('【高度自适应】','',$dataList[4]) == '是' ? true : false;
                        $isRequired = str_replace('【是否必填】','',$dataList[5]) == '是' ? true : false;
                        $form[] = [
                            'name'  => 'WidgetTextarea',
                            'title' => '多行文本',
                            'id'    => $id,
                            'props' => [
                                'title'         => $title,
                                'field'         => $field,
                                'placeholder'   => $placeholder,
                                'defaultValue'  => '',
                                'rows'          => $row,
                                'maxlength'     => $maxlength,
                                'autosize'      => $autosize,
                                'isRequired'    => $isRequired,
                            ],
                            'key'   => 'field2',
                        ];
                        break;
                    //下拉选项
                    case 'H':
                        if(empty($content)){
                            break;
                        }
                        $dataList = explode('｜',$content);
                        //单行文本数据在4个
                        if(count($dataList) != 4){
                            throw new Exception($tipsIndex.'单元格的下拉选项数据错误');
                        }
                        $id =  substr(md5($content.microtime(true)), 0,8);
                        $field = substr(md5($content.rand(1,1000)),0,6);
                        $title = str_replace('【字段标题】','',$dataList[0]);
                        $options = str_replace('【选项】','',$dataList[1]);
                        $defaultValue = str_replace('【默认值】','',$dataList[2]);
                        $isRequired = str_replace('【是否必填】','',$dataList[3]) == '是' ? true : false;
                        $form[] = [
                            'name'  => 'WidgetSelect',
                            'title' => '下拉选项',
                            'id'    => $id,
                            'props' => [
                                'title'         => $title,
                                'field'         => $field,
                                'options'       => explode('、',$options),
                                'defaultValue'  => $defaultValue,
                                'isRequired'    => $isRequired,
                            ],
                            'key'   => 'field3',
                        ];
                        break;
                    //单选
                    case 'I':
                        if(empty($content)){
                            break;
                        }
                        $dataList = explode('｜',$content);
                        //单行文本数据在4个
                        if(count($dataList) != 4){
                            throw new Exception($tipsIndex.'单元格的单选数据错误');
                        }
                        $id =  substr(md5($content.microtime(true)), 0,8);
                        $field = substr(md5($content.rand(1,1000)),0,6);
                        $title = str_replace('【字段标题】','',$dataList[0]);
                        $options = str_replace('【选项】','',$dataList[1]);
                        $defaultValue = str_replace('【默认值】','',$dataList[2]);
                        $isRequired = str_replace('【是否必填】','',$dataList[3]) == '是' ? true : false;
                        $form[] = [
                            'name'  => 'WidgetRadio',
                            'title' => '单选',
                            'id'    => $id,
                            'props' => [
                                'title'         => $title,
                                'field'         => $field,
                                'options'       => explode('、',$options),
                                'defaultValue'  => $defaultValue,
                                'isRequired'    => $isRequired,
                            ],
                            'key'   => 'field4',
                        ];
                        break;
                    //多选
                    case 'J':
                        if(empty($content)){
                            break;
                        }
                        $dataList = explode('｜',$content);
                        //多选数据在4个
                        if(count($dataList) != 4){
                            throw new Exception($tipsIndex.'单元格的多选数据错误');
                        }
                        $id =  substr(md5($content.microtime(true)), 0,8);
                        $field = substr(md5($content.rand(1,1000)),0,6);
                        $title = str_replace('【字段标题】','',$dataList[0]);
                        $options = str_replace('【选项】','',$dataList[1]);
                        $defaultValue = str_replace('【默认值】','',$dataList[2]);
                        $isRequired = str_replace('【是否必填】','',$dataList[3]) == '是' ? true : false;
                        $form[] = [
                            'name'  => 'WidgetCheckbox',
                            'title' => '单选',
                            'id'    => $id,
                            'props' => [
                                'title'         => $title,
                                'field'         => $field,
                                'options'       => explode('、',$options),
                                'defaultValue'  => explode('、',$defaultValue),
                                'isRequired'    => $isRequired,
                            ],
                            'key'   => 'field4',
                        ];
                        break;
                    //回复条数
                    case 'K':
                        $n = (int)$content;
                        if($n <= 0 || $n>= 5){
                            $n = 1;
                        }
                        $data['n'] = $n;
                        break;
                    //词汇属性
                    case 'L':
                        $temperature = (float)$content;
                        if($temperature <= 0 || $temperature >= 1){
                            $temperature = 0.6;
                        }
                        $data['temperature'] = $temperature;
                        break;
                    //随机属性
                    case 'M':
                        $topP = (float)$content;
                        if($topP <= 0 || $topP >= 1){
                            $topP = 0.9;
                        }
                        $data['top_p'] = $topP;
                        break;
                    //话题属性
                    case 'N':
                        $presencePenalty = (float)$content;
                        if($presencePenalty <= 0 || $presencePenalty >= 1){
                            $presencePenalty = 0.5;
                        }
                        $data['presence_penalty'] = $presencePenalty;
                        break;

                }
            }
            if(empty($form)){
                throw new Exception($cellIndex,'行中的单选文本,多行文本,下拉选项,单选,多选至少有一个内容不能为空');
            }
            $data['form'] = $form;
            $content = $data['content'];

            //处理调教文案内容
            foreach ($form as $formValue){
                $title = $formValue['props']['title'];
                $id = $formValue['props']['field'];
                $searchStr = '${'.$title.'}';
                $replaceStr = '${'.$id.'}';
                $position = strpos($content,$searchStr);
                if(false === $position){
                    throw new Exception('第'.$rowIndex.'行的调教文案内容中未找到【'.$title.'】字段');
                }
                $content = str_replace($searchStr,$replaceStr,$content);
            }
            $data['content'] = $content;
            $excelData[$rowIndex] = $data;
        }
        return $excelData;
    }

    /**
     * @notes 验证封装图片
     * @param mixed $drawings
     * @param mixed $excelData
     * @return void
     * @throws Exception
     * @author cjhao
     * @date 2024/4/11 14:59
     */
    public function checkExcelImage(mixed $drawings, mixed &$excelData)
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
                throw new Exception($cellCoordinate.'单元格图片位置错误,无法导入图片');
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
     * @param mixed $excelData
     * @return true
     * @throws Exception
     * @author cjhao
     * @date 2024/4/11 15:20
     */
    public function importDb(mixed $excelData): bool
    {
        Db::startTrans();
        try {
            $categoryLists = CreationCategory::column('id', 'name');
            $nameLists = array_keys(CreationModel::column('name,id','name'));
            $repetitionName = [];
            foreach ($excelData as $excelKey => $data) {
                $categoryId = $categoryLists[$data['category_name']] ?? '';
                if (empty($categoryId)) {
                    $category = CreationCategory::create([
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
                    $number = $repetitionName[$data['name'.'']] ?? 0;
                    $number++;
                    $excelData[$excelKey]['name'] = $data['name'].'-'.$number;
                    $repetitionName[$data['name']] = $number;

                }
            }
            (new CreationModel())->saveAll(array_values($excelData));
            Db::commit();
            return true;
        }catch (Exception $e) {
            Db::rollback();
            throw new Exception($e->getMessage());
        }
    }

    /**
     * @notes 创作模板下载
     * @return File
     * @author cjhao
     * @date 2024/4/15 11:14
     */
    public function downExcelTemplate(): File
    {

        $filePath = 'static/行业模板.xlsx';
        $fileName = '行业模板.xlsx';

        return download($filePath,$fileName);
    }


    /**
     * @notes 创作导出
     * @param array $params
     * @return array|string
     * @author cjhao
     * @date 2024/4/12 11:50
     */
    public function export(array $params)
    {
        try {
            $categoryId = $params['id'] ?? '';
            $keyword = $params['keyword'] ?? '';
            $pageType = $params['page_type'] ?? 2;
            $pageSizeMax = Config::get('project.lists.page_size_max');
            $pageStart = $params['page_start'] ?? 1;
            $pageEnd = $params['page_end'] ?? '';
            $pageSize = $params['page_size'] ?? '';
            $where = [];
            if($categoryId){
                $where[] = ['category_id','=',$categoryId];
            }
            if($keyword){
                $where[] = ['name','like','%'.$keyword.'%'];
            }

            $creationModel = CreationModel::where(['status'=>1])->where($where);
            //不分页
            if (0 == $pageType) {
                $limitOffset = 1;
                $limitLength = $pageSizeMax;// 直接取最大记录数
            }else{
                //分页
                $limitOffset = $pageStart;
                $limitLength = ($pageEnd - $pageStart + 1) * $pageSize;
            }
            $count = $creationModel->count();

            if (0 == $count || ceil($count / $pageSize) < $pageStart) {
                throw new Exception( $pageType ? '第' . $pageStart . '页到第' . $pageEnd . '页没有数据，无法导出' : '没有数据,无法导出');
            }

            $creationLists = $creationModel
                ->field('id,name,image,category_id,tips,form,content,n,temperature,top_p,presence_penalty')
                ->order('sort desc,id desc')
                ->limit($limitOffset,$limitLength)
                ->select()
                ->toArray();

            $categoryIds = array_column($creationLists,'category_id');
            $categoryLists = CreationCategory::where(['id'=>$categoryIds])
                ->column('name','id');
            $exportTitle = [
                'name'              => '模型名称',
                'image'             => '模型图标',
                'tips'              => '模型描述',
                'category_name'     => '所属分类',
                'content'           => '调教文案',
                'widget_input'      => '单行文本',
                'widget_textarea'   => '多行文本',
                'widget_select'     => '下拉选项',
                'widget_radio'      => '单选',
                'widget_checkbox'   => '多选',
                'n'                 => '回复条数',
                'temperature'       => '词汇属性',
                'top_p'             => '随机属性',
                'presence_penalty'  => '话题属性',
            ];
            $exportData = [];
            //处理数据
            foreach ($creationLists as $creation){
                $data     = [
                    'name'                   => $creation['name'],
                    'image'                  => $creation['image'],
                    'tips'                   => $creation['tips'],
                    'category_name'          => $categoryLists[$creation['category_id']] ?? '',
                    'content'                => '',
                    'widget_input'           => '',
                    'widget_textarea'        => '',
                    'widget_select'          => '',
                    'widget_radio'           => '',
                    'widget_checkbox'        => '',
                    'n'                      => $creation['n'],
                    'temperature'            => $creation['temperature'],
                    'top_p'                  => $creation['top_p'],
                    'presence_penalty'       => $creation['presence_penalty'],
                ];
                $content = $creation['content'];
                $splitSymbol = '｜';
                foreach ($creation['form'] as $form){
                    $replaceStr = '${'.$form['id'].'}';
                    $title = '${'.$form['props']['title'].'}';
                    $content = str_replace($replaceStr,$title,$content);
                    $props = $form['props'];
                    switch ($form['name']){
                        //单行文本
                        case 'WidgetInput':
                            $isRequired = $props['isRequired'] ? '是' : '否';
                            $widgetInput = '【字段标题】'.$props['title'].$splitSymbol.
                                '【示例文字】'.$props['placeholder'].$splitSymbol.
                                '【最大输入长度】'.$props['maxlength'].$splitSymbol.
                                '【是否必填】'.$isRequired;
                            $data['widget_input'] = $widgetInput;
                            break;
                        //多行文本
                        case 'WidgetTextarea':
                            $autosize = $props['autosize']? '是':'否';
                            $isRequired = $props['isRequired'] ? '是' : '否';
                            $rows = $props['row'] ?? $props['rows'];
                            $widgetTextarea = '【字段标题】'.$props['title'].$splitSymbol.
                                '【示例文字】'.$props['placeholder'].$splitSymbol.
                                '【默认行数】'.$rows.$splitSymbol.
                                '【最大输入长度】'.$props['maxlength'].$splitSymbol.
                                '【高度自适应】'.$autosize .$splitSymbol.
                                '【是否必填】'.$isRequired;
                            $data['widget_textarea'] = $widgetTextarea;
                            break;
                        //下拉选项
                        case 'WidgetSelect':
                            $options = implode('、',$props['options']);
                            $isRequired = $props['isRequired'] ? '是' : '否';
                            $widgetselect = '【字段标题】'.$props['title'].$splitSymbol.
                                '【选项】'.$options.$splitSymbol.
                                '【默认值】'.$props['defaultValue'].$splitSymbol.
                                '【是否必填】'.$isRequired;
                            $data['widget_select'] = $widgetselect;
                            break;
                        //单选
                        case 'WidgetRadio':
                            $options = implode('、',$props['options']);
                            $isRequired = $props['isRequired'] ? '是' : '否';
                            $widgetRadio = '【字段标题】'.$props['title'].$splitSymbol.
                                '【选项】'.$options.$splitSymbol.
                                '【默认值】'.$props['defaultValue'].$splitSymbol.
                                '【是否必填】'.$isRequired;
                            $data['widget_radio'] = $widgetRadio;
                            break;
                        //多选
                        case 'WidgetCheckbox':
                            $options = implode('、',$props['options']);
                            $defaultValue = implode('、',$props['defaultValue']);
                            $isRequired = $props['isRequired'] ? '是' : '否';
                            $widgetCheckbox = '【字段标题】'.$props['title'].$splitSymbol.
                                '【选项】'.$options.$splitSymbol.
                                '【默认值】'.$defaultValue.$splitSymbol.
                                '【是否必填】'.$isRequired;
                            $data['widget_checkbox'] = $widgetCheckbox;
                            break;
                    }
                }
                $data['content'] = $content;
                $exportData[] = $data;
            }
            $data = ExportLogic::exportData($exportTitle,$exportData,'创作提示词');
            return ['url'=>$data];
        }catch (\think\Exception $e){
            self::$error = $e->getMessage();
            return false;
        }


    }
}