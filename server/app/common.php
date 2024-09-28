<?php

// 应用公共文件
use app\common\enum\ChatEnum;
use app\common\model\chat\ModelsCost;
use app\common\service\FileService;
use Gioni06\Gpt3Tokenizer\Gpt3Tokenizer;
use Gioni06\Gpt3Tokenizer\Gpt3TokenizerConfig;
use think\helper\Str;

/**
 * @notes 生成密码加密密钥
 * @param string $plaintext
 * @param string $salt
 * @return string
 * @author 段誉
 * @date 2021/12/28 18:24
 */
function create_password(string $plaintext, string $salt) : string
{
    return md5($salt . md5($plaintext . $salt));
}


/**
 * @notes 随机生成token值
 * @param string $extra
 * @return string
 * @author 段誉
 * @date 2021/12/28 18:24
 */
function create_token(string $extra = '') : string
{
    $salt = env('project.unique_identification', 'likeadmin');
    $encryptSalt = md5( $salt . uniqid());
    return md5($salt . $extra . time() . $encryptSalt);
}


/**
 * @notes 截取某字符字符串
 * @param $str
 * @param string $symbol
 * @return string
 * @author 段誉
 * @date 2021/12/28 18:24
 */
function substr_symbol_behind($str, string $symbol = '.') : string
{
    $result = strripos($str, $symbol);
    if ($result === false) {
        return $str;
    }
    return substr($str, $result + 1);
}


/**
 * @notes 对比php版本
 * @param string $version
 * @return bool
 * @author 段誉
 * @date 2021/12/28 18:27
 */
function compare_php(string $version) : bool
{
    return version_compare(PHP_VERSION, $version) >= 0;
}


/**
 * @notes 检查文件是否可写
 * @param string $dir
 * @return bool
 * @author 段誉
 * @date 2021/12/28 18:27
 */
function check_dir_write(string $dir = '') : bool
{
    $route = root_path() . '/' . $dir;
    return is_writable($route);
}


/**
 * 多级线性结构排序
 * 转换前：
 * [{"id":1,"pid":0,"name":"a"},{"id":2,"pid":0,"name":"b"},{"id":3,"pid":1,"name":"c"},
 * {"id":4,"pid":2,"name":"d"},{"id":5,"pid":4,"name":"e"},{"id":6,"pid":5,"name":"f"},
 * {"id":7,"pid":3,"name":"g"}]
 * 转换后：
 * [{"id":1,"pid":0,"name":"a","level":1},{"id":3,"pid":1,"name":"c","level":2},{"id":7,"pid":3,"name":"g","level":3},
 * {"id":2,"pid":0,"name":"b","level":1},{"id":4,"pid":2,"name":"d","level":2},{"id":5,"pid":4,"name":"e","level":3},
 * {"id":6,"pid":5,"name":"f","level":4}]
 * @param array $data 线性结构数组
 * @param string $sub_key_name
 * @param string $id_name 数组id名
 * @param string $parent_id_name 数组祖先id名
 * @param int $parent_id 此值请勿给参数
 * @return array
 */
function linear_to_tree(mixed $data, string $sub_key_name = 'sub', string $id_name = 'id', string $parent_id_name = 'pid', int $parent_id = 0): array
{
    $tree = [];
    foreach ($data as $row) {
        if ($row[$parent_id_name] == $parent_id) {
            $temp = $row;
            $child = linear_to_tree($data, $sub_key_name, $id_name, $parent_id_name, $row[$id_name]);
            if ($child) {
                $temp[$sub_key_name] = $child;
            }
            $tree[] = $temp;
        }
    }
    return $tree;
}


/**
 * @notes 删除目标目录
 * @param $path
 * @param $delDir
 * @return bool|void
 * @author 段誉
 * @date 2022/4/8 16:30
 */
function del_target_dir($path, $delDir)
{
    //没找到，不处理
    if (!file_exists($path)) {
        return false;
    }

    //打开目录句柄
    $handle = opendir($path);
    if ($handle) {
        while (false !== ($item = readdir($handle))) {
            if ($item != "." && $item != "..") {
                if (is_dir("$path/$item")) {
                    del_target_dir("$path/$item", $delDir);
                } else {
                    unlink("$path/$item");
                }
            }
        }
        closedir($handle);
        if ($delDir) {
            return rmdir($path);
        }
    } else {
        if (file_exists($path)) {
            return unlink($path);
        }
        return false;
    }
}


/**
 * @notes 下载文件
 * @param $url
 * @param $saveDir
 * @param $fileName
 * @return string
 * @author 段誉
 * @date 2022/9/16 9:53
 */
function download_file($url, $saveDir, $fileName, $ssl = true, $header = []): string
{
    if (!file_exists($saveDir)) {
        mkdir($saveDir, 0775, true);
    }
    $fileSrc = $saveDir . $fileName;
    file_exists($fileSrc) && unlink($fileSrc);
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 30);

    if ($ssl === false) {
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
    }

    if (!empty($header)) {
        curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
    }

    $file = curl_exec($ch);
    curl_close($ch);
    $resource = fopen($fileSrc, 'a');
    fwrite($resource, $file);
    fclose($resource);
    if (filesize($fileSrc) == 0) {
        unlink($fileSrc);
        return '';
    }
    return $fileSrc;
}

/**
 * @notes 去除内容图片域名
 * @param $content
 * @return array|string
 * @author 段誉
 * @date 2022/9/26 10:43
 */
function clear_file_domain($content): array|string
{
    $fileUrl = FileService::getFileUrl();
    return str_replace($fileUrl, '/', $content);
}

/**
 * @notes 设置内容图片域名
 * @param $content
 * @return array|string|null
 * @author 段誉
 * @date 2022/9/26 10:43
 */
function get_file_domain($content): array|string|null
{
    $preg = '/(<img .*?src=")[^https|^http](.*?)(".*?>)/is';
    $fileUrl = FileService::getFileUrl();
    return preg_replace($preg, "\${1}$fileUrl\${2}\${3}", $content);
}

/**
 * @notes uri小写
 * @param $data
 * @return array
 * @author 段誉
 * @date 2022/7/19 14:50
 */
function lower_uri($data): array
{
    if (!is_array($data)) {
        $data = [$data];
    }
    return array_map(function ($item) {
        return strtolower(Str::camel($item));
    }, $data);
}

/**
 * @notes 数字转中文数字
 * @param int|string $number
 * @return string
 * @author fzr
 */
function arabic_to_chinese(int|string $number): string
{
    $chineseNumbers = array('零', '一', '二', '三', '四', '五', '六', '七', '八', '九');
    $chineseUnits = array('', '十', '百', '千');

    if ($number >= 0 && $number < 10) {
        return $chineseNumbers[$number];
    } else {
        $chinese = '';
        $i = 0;

        while ($number > 0) {
            $digit = $number % 10;

            if ($digit != 0) {
                $chinese = $chineseNumbers[$digit] . $chineseUnits[$i] . $chinese;
            }

            $number = (int)($number / 10);
            $i++;
        }

        return rtrim($chinese, '零');
    }
}

/**
 * @notes 获取无前缀数据表名
 * @param $tableName
 * @return mixed
 * @author 段誉
 * @date 2022/12/12 15:23
 */
function get_no_prefix_table_name($tableName): mixed
{
    $tablePrefix = config('database.connections.mysql.prefix');
    $prefixIndex = strpos($tableName, $tablePrefix);
    if ($prefixIndex !== 0) {
        return $tableName;
    }
    $tableName = substr_replace($tableName, '', 0, strlen($tablePrefix));
    return trim($tableName);
}


/**
 * @notes 生成编码
 * @param $table
 * @param $field
 * @param string $prefix
 * @param int $randSuffixLength
 * @param array $pool
 * @return string
 * @author 段誉
 * @date 2023/2/23 11:35
 */
function generate_sn($table, $field, mixed $prefix = '', mixed $randSuffixLength = 4, mixed $pool = []) : string
{
    $suffix = '';
    for ($i = 0; $i < $randSuffixLength; $i++) {
        if (empty($pool)) {
            $suffix .= rand(0, 9);
        } else {
            $suffix .= $pool[array_rand($pool)];
        }
    }
    $sn = $prefix . date('YmdHis') . $suffix;
    if (app()->make($table)->where($field, $sn)->find()) {
        return generate_sn($table, $field, $prefix, $randSuffixLength, $pool);
    }
    return $sn;
}

/**
 * @notes 生成随机字符串
 * @param $length
 * @return string
 * @author fzr
 */
function generate_random_str($length): string
{
    $chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
    $str = '';
    $charCount = strlen($chars);

    for ($i = 0; $i < $length; $i++) {
        $randomIndex = rand(0, $charCount - 1);
        $str .= $chars[$randomIndex];
    }

    return $str;
}

/**
 * @notes 计算gpt3.5的token数
 * @param string $text
 * @return int
 */
function gpt_tokenizer_count(string $text): int
{
    $config = new Gpt3TokenizerConfig();
    $tokenizer = new Gpt3Tokenizer($config);
    return $tokenizer->count($text);
}

/**
 * @notes tokens转价格
 * @param string $type  (类型: chat=对话, emb=向量)
 * @param int $modelId (子模型的ID)
 * @param int $tokens   (tokens数量)
 * @return float|string
 */
function tokens_price(string $type, int $modelId, int $tokens): float|string
{
    $e = ['chat'=>ChatEnum::MODEL_TYPE_CHAT, 'emb'=>ChatEnum::MODEL_TYPE_EMB];
    $modelModelsCost = new ModelsCost();
    $price = $modelModelsCost
        ->where(['type'=>$e[$type]??1])
        ->where(['id'=>$modelId])
        ->value('price') ?? 0;

    // 每1千tokens需要多少钱
    if (!$price) {
        return 0;
    }

    // 计算出1个token等于多少钱
    $costPerToken = $price / 1000;
    //$costPerToken = sprintf("%.7f", $costPerToken);
    //$costPerToken = format_amount_zero($costPerToken);

    // 消耗的费用(每个token多少钱): token单价 * tokens数量
    $fee = $costPerToken * $tokens;
    $fee = number_format($fee, 7, '.', '');
    return format_amount_zero($fee);
}

/***
 * @notes 格式化金额去除右边多余的0
 * @param $value
 * @return string
 */
function format_amount_zero($value): string
{
    $value = strval($value);
    if (str_contains($value, '.')) {
        $value = rtrim($value, '0');
        return rtrim($value, '.');
    }
    return $value;
}

/**
 * @notes 格式化金额
 * @param $float
 * @return mixed
 * @author 段誉
 * @date 2023/2/24 11:20
 */
function format_amount($float): mixed
{
    if ($float == intval($float)) {
        return intval($float);
    } elseif ($float == sprintf('%.1f', $float)) {
        return sprintf('%.1f', $float);
    }
    return $float;
}

/**
 * @notes 获取文件扩展名
 * @param $file
 * @return array|string
 * @author 段誉
 * @date 2021/8/14 15:24
 */
function get_extension($file): array|string
{
    return pathinfo($file, PATHINFO_EXTENSION);
}

/**
 * @notes 遍历指定目录下的文件(目标目录,排除文件)
 * @param mixed $dir (目标文件)
 * @param string $exclude_file (要排除的文件)
 * @param string $target_suffix (指定后缀)
 * @return array|false
 * @author 段誉
 * @date 2021/8/14 14:44
 */
function get_scanDir(mixed $dir, string $exclude_file = '', string $target_suffix = ''): bool|array
{
    if (!file_exists($dir) || empty(trim($dir))) {
        return [];
    }

    $files = scandir($dir);
    $res = [];
    foreach ($files as $item) {
        if ($item == "." || $item == ".." || $item == $exclude_file) {
            continue;
        }
        if (!empty($target_suffix)) {
            if (get_extension($item) == $target_suffix) {
                $res[] = $item;
            }
        } else {
            $res[] = $item;
        }
    }

    if (empty($item)) {
        return false;
    }
    return $res;
}

/**
 * @notes 解压压缩包
 * @param $file
 * @param $save_dir
 * @return bool
 * @author 段誉
 * @date 2021/8/14 15:27
 */
function unzip($file, $save_dir): bool
{
    if (!file_exists($file)) {
        return false;
    }
    $zip = new ZipArchive();
    if ($zip->open($file) !== TRUE) {//中文文件名要使用ANSI编码的文件格式
        return false;
    }
    $zip->extractTo($save_dir);
    $zip->close();
    return true;
}

/**
 * @notes 本地版本
 * @return mixed
 * @author 段誉
 * @date 2021/8/14 15:33
 */
function local_version(): mixed
{
    if (!file_exists('./upgrade/')) {
        // 若文件夹不存在，先创建文件夹
        mkdir('./upgrade/', 0777, true);
    }
    if (!file_exists('./upgrade/version.json')) {
        // 获取本地版本号
        $version = config('project.version');
        $data = ['version' => $version];
        $src = './upgrade/version.json';
        // 新建文件
        file_put_contents($src, json_encode($data, JSON_UNESCAPED_UNICODE));
    }

    $json_string = file_get_contents('./upgrade/version.json');
    // 用参数true把JSON字符串强制转成PHP数组
    return json_decode($json_string, true);
}

function is_cli(): bool
{
    return (php_sapi_name() === 'cli');
}

/**
 * @notes 图片转换base64
 * @param $imgUrl
 * @param bool $hasPre
 * @param bool $isChunk
 * @return false|string
 * @author JXDN
 * @date 2024/05/30 17:09
 */
function imgToBase64($imgUrl, bool $hasPre = true, bool $isChunk = true): bool|string
{
    $imageInfo = getimagesize($imgUrl);
    if (!$imageInfo) {
        return false;
    }

    $imgBase64 = base64_encode(file_get_contents($imgUrl));

    if ($isChunk) {
        $imgBase64 = trim(chunk_split($imgBase64));
    }

    if ($hasPre) {
        $imgBase64 = 'data:' . $imageInfo['mime'] . ';base64,' . $imgBase64;
    }
    return $imgBase64;
}

/**
 * @notes 浮点数去除无效的0
 * @param $float
 * @return int|mixed|string
 * @author Tab
 * @date 2021/8/11 10:17
 */
function clear_zero($float)
{
    if($float == intval($float)) {
        return intval($float);
    }else if($float == sprintf('%.1f', $float)) {
        return sprintf('%.1f', $float);
    }
    return $float;
}
/**
 * @notes 卡密编码
 * @param $table
 * @param $field
 * @param string $prefix
 * @param int $randSuffixLength
 * @param int $ruleType 生成规则：1-批次编号+随机字母；2-批次编号+随机数字；
 * @return string
 * @author ljj
 * @date 2024/5/8 10:14 上午
 */
function card_sn($table, $field, $prefix = 'K', $randSuffixLength = 6, $ruleType = 2) : string
{
    $string = $prefix;
    $letters = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    for ($i = 0; $i < $randSuffixLength; $i++) {
        if ($ruleType == 1) {
            $string .= $letters[rand(0, strlen($letters) - 1)];
        }
        if ($ruleType == 2) {
            $string .= rand(0, 9);
        }
    }
    if (app()->make($table)->where($field, $string)->find()) {
        return generate_sn($table, $field, $prefix, $randSuffixLength, $ruleType);
    }
    return $string;
}

/**
 * @notes 判断远程文件是否存在
 * @param string $url
 * @return bool
 * @author cjhao
 * @date 2023/10/11 9:52
 */
function getRemoteFileExists(string $url)
{
    $headers = @get_headers($url);
    if($headers && false !== strpos($headers[0], '200')) {
        return true;
    }
    return false;

}
