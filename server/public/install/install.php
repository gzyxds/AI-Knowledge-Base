<?php
// error_reporting(0);
include "model.php";
include "YxEnv.php";

const install = true;
const INSTALL_ROOT = __DIR__;
const TESTING_TABLE = 'config';
const DS = DIRECTORY_SEPARATOR;

$step = $_GET['step'] ?? 1;

$installDir = "install";
$modelInstall = new installModel();

// Env设置
$yxEnv = new YxEnv();

// 检查是否有安装过
$envFilePath = $modelInstall->getAppRoot() . '/.env';
if ($modelInstall->appIsInstalled() && in_array($step, [1, 2, 3, 4])) {
    die('可能已经安装过本系统了，请删除配置目录下面的install.lock文件再尝试');
}

// 加载Example文件
$yxEnv->load($modelInstall->getAppRoot() . '/.example.env');

//尝试生成.env
$yxEnv->makeEnv($modelInstall->getAppRoot() . '/.env');

$post = [
    'host'                   => trim($_POST['host']     ?? 'chatmoney-mysql'),
    'port'                   => trim($_POST['port']     ?? '3306'),
    'user'                   => trim($_POST['user']     ?? 'root'),
    'password'               => trim($_POST['password'] ?? ''),
    'name'                   => trim($_POST['name']     ?? 'chatmoney'),
    'prefix'                 => trim($_POST['prefix']   ?? 'cm_'),

    'pg_host'                => trim($_POST['pg_host']     ?? 'chatmoney-postgres'),
    'pg_port'                => trim($_POST['pg_port']     ?? '5432'),
    'pg_name'                => trim($_POST['pg_name']     ?? 'postgres'),
    'pg_user'                => trim($_POST['pg_user']     ?? 'postgres'),
    'pg_password'            => trim($_POST['pg_password'] ?? ''),
    'pg_prefix'              => trim($_POST['pg_prefix']   ?? 'cm_'),

    'redis_host'             => trim($_POST['redis_host']  ?? 'chatmoney-redis'),
    'redis_port'             => trim($_POST['redis_port']  ?? '6379'),
    'redis_password'         => trim($_POST['redis_password'] ?? ''),

    'admin_user'             => trim($_POST['admin_user']             ?? ''),
    'admin_password'         => trim($_POST['admin_password']         ?? ''),
    'admin_confirm_password' => trim($_POST['admin_confirm_password'] ?? ''),

    'import_test_data'       => $_POST['import_test_data'] ?? 'off',
    'clear_db'               => $_POST['clear_db'] ?? 'off',
];

$message = '';

// 检查数据库正确性
if ($step == 4) {
    $canNext = true;
    if (empty($post['prefix'])) {
        $canNext = false;
        $message = '数据表前缀不能为空';
    } elseif ($post['admin_user'] == '') {
        $canNext = false;
        $message = '请填写管理员用户名';
    } elseif (empty(trim($post['admin_password']))) {
        $canNext = false;
        $message = '管理员密码不能为空';
    } elseif ($post['admin_password'] != $post['admin_confirm_password']) {
        $canNext = false;
        $message = '两次密码不一致';
    } elseif (mb_strlen($post['admin_password']) < 6) {
        $canNext = false;
        $message = '密码不能少于6位数';
    } elseif (mb_strlen($post['admin_password']) > 32) {
        $canNext = false;
        $message = '密码不能大于32位数';
    } else {
        // 检查数据库连接
        $connResult = $modelInstall->checkConnection($post['name'], $post);
        if ($connResult->result == 'fail') {
            $canNext = false;
            $message = $connResult->error;
        }

        if ($connResult->result != 'fail') {
            // 检查Mysql和安装
            $result = $modelInstall->checkConfig($post['name'], $post);
            if ($result->result == 'fail') {
                $canNext = false;
                $message = $result->error;
            }

            // 检查PG数据库和安装
            if ($result->result != 'fail') {
                $pgResult = $modelInstall->checkPgConfig($post);
                if ($pgResult->result == 'fail') {
                    $canNext = false;
                    $message = $pgResult->error;
                }
            }

            // 导入测试数据
            if ($canNext == true && $post['import_test_data'] == 'on') {
                if (!$modelInstall->importDemoData()) {
                    $canNext = false;
                    $message = '导入测试数据错误';
                }
            }

            // 写配置文件
            if ($canNext) {
                $yxEnv->putEnv($envFilePath, $post);
                $modelInstall->mkLockFile();
            }

            // 恢复admin和index入口
            if ($canNext) {
                $modelInstall->restoreIndexLock();
            }
        }
    }

    if (!$canNext)
        $step = 3;
}

if ($step == 5) {
    $modelInstall->installLog();
}

// 取得安装成功的表
$successTables = $modelInstall->getSuccessTable();

$nextStep = $step + 1;
include __DIR__ . "/template/main.php";