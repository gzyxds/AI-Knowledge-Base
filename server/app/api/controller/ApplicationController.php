<?php
namespace app\api\controller;
use DOMDocument;
use DOMXPath;

/**
 * 应用管理
 */
class ApplicationController extends BaseApiController
{
    public array $notNeedLogin = ['xiaohongshu'];

    public function xiaohongshu(){
        $content = '';
        //设置请求超时时间为60秒
//        $httpClient = HttpClient::create(['timeout' => 60]);
//        $client = new HttpBrowser($httpClient);
//        $client->setServerParameter('HTTP_USER_AGENT','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/125.0.0.0 Safari/537.36');
////        $crawler = $client->request('GET', 'https://www.xiaohongshu.com/explore/66501b4d0000000016012e4a');
////        $crawler = $client->request('GET', 'https://www.xiaohongshu.com/explore/6642c980000000001e03ad2c');
//        $crawler = $client->request('GET', 'https://www.xiaohongshu.com/explore/6648e7ab0000000014018c7c');
//        // 获取标题
//        $titleNodeList = $crawler->filter('title');
//        dd($titleNodeList);
//        if($titleNodeList->count() > 0 && $titleNodeList->text()){
//            $content = $titleNodeList->text() . PHP_EOL;
//        }
//        $detailDescContent = '';
//        //去掉爬取到的js部分
////        $crawler->filter('#detail-desc')->each(function ($node) use (&$detailDescContent) {
////            // 获取元素内的所有文本内容，并去除空白字符
////            $textContent = trim(preg_replace('/\s+/', ' ',$node->text()));
////            $detailDescContent .=$textContent . PHP_EOL;
////        });
////        dd($detailDescContent);
//        $crawler->filter('body')
//            ->filter('script')->each(function ($node) {
//                $node->getNode(0)->parentNode->removeChild($node->getNode(0));
//            });
//
////        $content .= $crawler->filter('detail-desc')->text();
////        $content .= $crawler->filterXPath('detail-desc')->text();
//        $title =$crawler->filterXPath('//div[@id="detail-title"]')->text();
//        $content =$crawler->filterXPath('//div[@id="detail-desc"]')->text();
//        $content =$crawler->filterXPath('notice')->text();
//        dd($content);
////        $content = $crawler->filter('#detail-desc')->html();
//        dd($title.PHP_EOL.$content);
//        return $title.PHP_EOL.$content;
        $ch = curl_init();

//        curl_setopt($ch, CURLOPT_URL, 'https://www.xiaohongshu.com/explore/6642c980000000001e03ad2c');
//        curl_setopt($ch, CURLOPT_URL, 'https://www.xiaohongshu.com/explore/66501b4d0000000016012e4a');
//        curl_setopt($ch, CURLOPT_URL, 'https://www.xiaohongshu.com/explore/6648e7ab0000000014018c7c');
//        curl_setopt($ch, CURLOPT_URL, 'https://www.xiaohongshu.com/explore/6646a093000000001e038868');
        curl_setopt($ch, CURLOPT_URL, 'https://www.xiaohongshu.com/explore/6646c233000000001e027478');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); // 将响应作为字符串返回
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // 禁止cURL验证对等证书
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true); // 允许请求跟随重定向

// 设置头信息，模拟浏览器访问
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/125.0.0.0 Safari/537.36'
        ));

// 执行cURL会话
        $response = curl_exec($ch);

// 检查是否有错误发生
        if(curl_errno($ch)) {
            echo 'Curl error: ' . curl_error($ch);
        } else {
            $dom = new DOMDocument();
            @$dom->loadHTML($response);

// 初始化一个变量来存储__INITIAL_STATE__的数据
            $initialState = '';

// 使用XPath来查找包含__INITIAL_STATE__的<script>标签
            $xpath = new DOMXPath($dom);
            $scripts =$xpath->query('//script');
            foreach ($scripts as$script) {
                $text =$script->nodeValue;
                if (strpos($text, 'window.__INITIAL_STATE__') !== false) {
                    $data = str_replace('window.__INITIAL_STATE__=','',$text);
                    $data = str_replace('undefined', "\"" . "\"",$data);
                    $data = json_decode($data,true);
                    $xhsData = $data['note'];
                    $firstNoteId = $xhsData['firstNoteId'];
                    $noteDetailMap = $xhsData['noteDetailMap'][$firstNoteId];
                    $notice = $noteDetailMap['note'];
                    dd($notice,$notice['desc'],$notice['title']);
                }
            }
            dd($scripts);
            preg_match('/window\.__INITIAL_STATE__\s*=\s*(.*?);<\/script>/', $response,$matches);
            dd($matches);
            if ($matches) {
                // 获取__INITIAL_STATE__的内容
                $initialState = json_decode($matches[1], true);

                // 打印结果
                print_r($initialState);
            } else {
                echo "没有找到window.__INITIAL_STATE__的定义。";
            }
        }

// 关闭cURL会话
        curl_close($ch);
    }
}