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
namespace app\common\logic;
use app\common\cache\ExportCache;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Spreadsheet;


/**
 * 导出逻辑类
 * Class ExportLogic
 * @package app\common\logic
 */
class ExportLogic extends BaseLogic
{


    /**
     * @notes 导出数据
     * @param $exportTitle 导出标题
     * @param $exportData  导出数据
     * @param $saveFile   导出文件标题
     * @return string
     * @author cjhao
     * @date 2024/4/12 15:20
     */
    public static function exportData(array $exportTitle,array $exportData,string $saveFileName)
    {
        $saveFileName.= '.xlsx';
        $title = array_values($exportTitle);
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $asciiCode = 65;
        foreach ($title as  $value) {
            $character = chr($asciiCode);
            // 单元格内容写入
            $sheet->setCellValue($character.'1',  $value);
            $asciiCode++;
        }
        $row = 2; //从第二行开始
//        $drawing = new Drawing();
        foreach ($exportData as $data){
            $asciiCode = 65;
            foreach ($data as $key =>  $value) {
                $character = chr($asciiCode);
                $asciiCode++;

                $character.= $row;
                $style = $sheet->getStyle($character);
                $sheet->setCellValue($character,  $value);
                //单元格换行
//                $style->getAlignment()->setWrapText(true);
//                if('image' != $key){
                    //单元格内容写入
//                }else{
//                    $drawing = new Drawing();
//                    $drawing->setName('Image')
//                            ->setDescription('Image')
//                            ->setCoordinates('B'.$column)
//                            ->setWidth()
//                            ->setHeight()
////                            ->setPath($value)
//                            ->setPath('uploads/images/20240412/04e616c0fb8f7a4e4158473359542667.jpeg')
//                            ->setWorksheet($sheet);
//                }
            }
            $row++;
        }
        $getHighestRowAndColumn = $sheet->getHighestRowAndColumn();
        $HighestRow = $getHighestRowAndColumn['row'];
        $column = $getHighestRowAndColumn['column'];
        $titleScope = 'A1:' . $column . '1';//第一（标题）范围（例：A1:D1)
        $sheet->getStyle($titleScope)
            ->getFill()
            ->setFillType(Fill::FILL_SOLID) // 设置填充样式
            ->getStartColor()
            ->setARGB('00B0F0');
        // 设置文字颜色为白色
        $sheet->getStyle($titleScope)->getFont()->getColor()
            ->setARGB('FFFFFF');
        $spreadsheet->getActiveSheet()->getColumnDimension('B')->setAutoSize(true);

        $allCope = 'A1:' . $column . $HighestRow;//整个表格范围（例：A1:D5）
        $sheet->getStyle($allCope)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
        $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');

        //创建excel文件
        $exportCache = new ExportCache();
        $src = $exportCache->getSrc();
        if (!file_exists($src)) {
            mkdir($src, 0775, true);
        }
        $writer->save($src . $saveFileName);
        $rootUrl = request()->rootUrl();
        //设置本地excel缓存并返回下载地址
        return (string)(url($rootUrl.'/download/export', ['file' => $exportCache->setFile($saveFileName)], true, true));
    }
}