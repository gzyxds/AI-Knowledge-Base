<?php

use JetBrains\PhpStorm\ArrayShape;
use JetBrains\PhpStorm\Pure;

/** 安装界面需要的各种模块 */

class installModel
{
    private mixed $host;
    private mixed $name;
    private mixed $user;
    private mixed $encoding;
    private mixed $password;
    private mixed $port;
    private mixed $prefix;
    private mixed $successTable = [];

    private mixed $allowNext = true;

    private mixed $dbh = null;

    private mixed $clearDB = false;

    /**
     * Notes: php版本
     * @author luzg(2020/8/25 9:56)
     * @return string
     */
    public function getPhpVersion(): string
    {
        return PHP_VERSION;
    }

    /**
     * Notes: 当前版本是否符合
     * @author luzg(2020/8/25 9:57)
     * @return string
     */
    public function checkPHP(): string
    {
        return $result = version_compare(PHP_VERSION, '8.0.0') >= 0 ? 'ok' : 'fail';
    }

    /**
     * Notes: 是否有PDO
     * @author luzg(2020/8/25 9:57)
     * @return string
     */
    public function checkPDO(): string
    {
        return extension_loaded('pdo') ? 'ok' : 'fail';
    }

    /**
     * Notes: 是否有PDO::MySQL
     * @author luzg(2020/8/25 9:58)
     * @return string
     */
    public function checkPDOMySQL(): string
    {
        return extension_loaded('pdo_mysql') ? 'ok' : 'fail';
    }

    /**
     * Notes: 是否有PDO::Pgsql
     * @author luzg(2020/8/25 9:58)
     * @return string
     */
    public function checkPDOPgSql(): string
    {
        return extension_loaded('pdo_pgsql') ? 'ok' : 'fail';
    }

    /**
     * Notes: 是否支持JSON
     * @author luzg(2020/8/25 9:58)
     * @return string
     */
    public function checkJSON(): string
    {
        return extension_loaded('json') ? 'ok' : 'fail';
    }

    /**
     * Notes: 是否支持openssl
     * @author luzg(2020/8/25 9:58)
     * @return string
     */
    public function checkOpenssl(): string
    {
        return extension_loaded('openssl') ? 'ok' : 'fail';
    }

    /**
     * Notes: 是否支持mbstring
     * @author luzg(2020/8/25 9:58)
     * @return string
     */
    public function checkMbstring(): string
    {
        return extension_loaded('mbstring') ? 'ok' : 'fail';
    }

    /**
     * Notes: 是否支持zlib
     * @author luzg(2020/8/25 9:59)
     * @return string
     */
    public function checkZlib(): string
    {
        return extension_loaded('zlib') ? 'ok' : 'fail';
    }

    /**
     * Notes: 是否支持curl
     * @author luzg(2020/8/25 9:59)
     * @return string
     */
    public function checkCurl(): string
    {
        return extension_loaded('curl') ? 'ok' : 'fail';
    }

    /**
     * Notes: 检查GD2扩展
     * @author luzg(2020/8/26 9:59)
     * @return string
     */
    public function checkGd2(): string
    {
        return extension_loaded('gd') ? 'ok' : 'fail';
    }

    /**
     * Notes: 检查Dom扩展
     * @author luzg(2020/8/26 9:59)
     * @return string
     */
    public function checkDom(): string
    {
        return extension_loaded('dom') ? 'ok' : 'fail';
    }

    /**
     * Notes: 检查Redis扩展
     * @author luzg(2020/8/26 9:59)
     * @return string
     */
    public function checkRedis(): string
    {
        return extension_loaded('redis') ? 'ok' : 'fail';
    }

    /**
     * Notes: 是否支持filter
     * @author luzg(2020/8/25 9:59)
     * @return string
     */
    public function checkFilter(): string
    {
        return extension_loaded('filter') ? 'ok' : 'fail';
    }

    /**
     * Notes: 是否支持iconv
     * @author luzg(2020/8/25 9:59)
     * @return string
     */
    public function checkIconv(): string
    {
        return extension_loaded('iconv') ? 'ok' : 'fail';
    }

    /**
     * Notes: 检查fileinfo扩展
     * @author 段誉(2021/6/28 11:03)
     * @return string
     */
    public function checkFileInfo(): string
    {
        return extension_loaded('fileinfo') ? 'ok' : 'fail';
    }

    /**
     * @notes 检查swoole_loader扩展
     * @return string
     * @author cjhao
     * @date 2023/5/10 16:09
     */
    public function checkSwooleLoader(): string
    {
        return extension_loaded('swoole_loader') ? 'ok' : 'fail';
    }

    /**
     * @notes 获取swoole_loader扩展的线程安装
     * @return string
     * @author cjhao
     * @date 2023/5/10 16:16
     */
    public function checkSwooleLoaderRemark(): string
    {
        return $this->swooleLoaderIsThreadSafety() ? '线程安全扩展' : '非线程安全扩展';
    }

    /**
     * Notes: 取得临时目录路径
     * @author luzg(2020/8/25 10:05)
     * @return array
     */
    #[ArrayShape(['path' => "string", 'exists' => "bool", 'writable' => "bool"])]
    public function getTmpRoot(): array
    {
        $path = $this->getAppRoot() . '/runtime';
        return [
            'path'     => $path,
            'exists'   => is_dir($path),
            'writable' => is_writable($path),
        ];
    }

    /**
     * Notes: 检查临时路径
     * @author luzg(2020/8/25 10:06)
     * @return string
     */
    public function checkTmpRoot(): string
    {
        $tmpRoot = $this->getTmpRoot()['path'];
        return (is_dir($tmpRoot) and is_writable($tmpRoot)) ? 'ok' : 'fail';
    }

    /**
     * Notes: SESSION路径是否可写
     * @author luzg(2020/8/25 10:06)
     */
    #[ArrayShape(['path' => "mixed", 'exists' => "bool", 'writable' => "bool"])]
    public function getSessionSavePath(): array
    {
        $sessionSavePath = preg_replace("/\d;/", '', session_save_path());

        return [
            'path'     => $sessionSavePath,
            'exists'   => is_dir($sessionSavePath),
            'writable' => is_writable($sessionSavePath),
        ];
    }

    /**
     * Notes: 检查session路径可写状态
     * @author luzg(2020/8/25 10:13)
     * @return string
     */
    public function checkSessionSavePath(): string
    {
        $sessionSavePath = preg_replace("/\d;/", '', session_save_path());
        $result = (is_dir($sessionSavePath) and is_writable($sessionSavePath)) ? 'ok' : 'fail';
        if ($result == 'fail') return $result;

        file_put_contents($sessionSavePath . '/zentaotest', 'zentao');
        $sessionContent = file_get_contents($sessionSavePath . '/zentaotest');
        if ($sessionContent == 'zentao') {
            unlink($sessionSavePath . '/zentaotest');
            return 'ok';
        }
        return 'fail';
    }

    /**
     * Notes: 取得data目录是否可选
     * @author luzg(2020/8/25 10:58)
     * @return array
     */
    #[Pure] #[ArrayShape(['path' => "string", 'exists' => "bool", 'writable' => "bool"])]
    public function getDataRoot(): array
    {
        $path = $this->getAppRoot();
        return [
            'path'     => $path . 'www' . DS . 'data',
            'exists'   => is_dir($path),
            'writable' => is_writable($path),
        ];
    }

    /**
     * Notes: 取得root路径
     * @author luzg(2020/8/25 11:02)
     * @return string
     */
    #[Pure] public function checkDataRoot(): string
    {
        $dataRoot = $this->getAppRoot() . 'www' . DS . 'data';
        return (is_dir($dataRoot) and is_writable($dataRoot)) ? 'ok' : 'fail';
    }

    /**
     * Notes: 取得php.ini信息
     * @author luzg(2020/8/25 11:03)
     * @return string
     */
    public function getIniInfo(): string
    {
        $iniInfo = '';
        ob_start();
        phpinfo();
        $lines = explode("\n", strip_tags(ob_get_contents()));
        ob_end_clean();
        foreach ($lines as $line) if (str_contains($line, 'ini')) $iniInfo .= $line . "\n";
        return $iniInfo;
    }

    /**
     * Notes: 创建安装锁定文件
     * @author luzg(2020/8/28 11:32)
     * @return bool
     */
    public function mkLockFile(): bool
    {
        return touch($this->getAppRoot() . '/config/install.lock');
    }

    /**
     * Notes: 检查之前是否有安装
     * @author luzg(2020/8/28 11:36)
     */
    #[Pure]
    public function appIsInstalled(): bool
    {
        return file_exists($this->getAppRoot() . '/config/install.lock');
    }

    public function checkConnection(string $dbName, $connectionInfo): stdclass
    {
        $return = new stdclass();
        $return->result = 'ok';

        if (str_contains($connectionInfo['name'], '-') || str_contains($connectionInfo['pg_name'], '-')) {
            $return->result = 'fail';
            $return->error = "数据库名称不能含 - 字符。";
            return $return;
        }

        if (str_contains($connectionInfo['user'], '-') || str_contains($connectionInfo['pg_user'], '-')) {
            $return->result = 'fail';
            $return->error = "数据库用户不能含 - 字符。";
            return $return;
        }

        // 验证mysql信息
        $this->setDBParam($connectionInfo);
        $this->dbh = $this->connectDB();
        if (str_contains($dbName, '.')) {
            $return->result = 'fail';
            $return->error = '没有发现数据库信息!';
            return $return;
        }

        if (!is_object($this->dbh)) {
            $return->result = 'fail';
            $return->error = '安装错误，请检查连接信息:'.mb_strcut($this->dbh,0,30).'...';
            return $return;
        }

        // 验证redis信息
        try {
            $redis = new Redis();
            $redis->connect($connectionInfo['redis_host'], $connectionInfo['redis_port']);
            if ($connectionInfo['redis_password'] && !$redis->auth($connectionInfo['redis_password'])) {
                $return->result = 'fail';
                $return->error = '无法验证 Redis 密码...';
                return $return;
            }
        } catch (Exception $e) {
            $return->result = 'fail';
            $return->error = 'Redis连接失败，请检查连接信息:'.mb_strcut($e->getMessage(),0,30).'...';
            return $return;
        }

        // 验证Pgsql信息
        try {
            $port     = $connectionInfo['pg_port'] ?? '';
            $host     = $connectionInfo['pg_host'] ?? '';
            $dbname   = $connectionInfo['pg_name'] ?? '';
            $username = $connectionInfo['pg_user'] ?? '';
            $password = $connectionInfo['pg_password'] ?? '';

            $pdo = new PDO("pgsql:host=$host;port=$port;dbname=$dbname", $username, $password);
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            if (!is_object($pdo)) {
                $return->result = 'fail';
                $return->error = 'Postgresql库连接失败,请检查连接信息...';
                return $return;
            }

            if ($connectionInfo['clear_db'] !== 'on') {
                $tableCount = $pdo->query("SELECT COUNT(*) FROM information_schema.tables WHERE table_schema = 'public';");
                if ($tableCount->fetch()['count'] ?? 0) {
                    $return->result = 'fail';
                    $return->error = 'Postgresql数据表已存在，您之前可能已安装本系统，如需继续安装请选择清空现有数据。...';
                    return $return;
                }
            }

            $resVector = $pdo->query("SELECT installed_version FROM pg_available_extensions WHERE name = 'vector';");
            $fetVector = $resVector->fetch();
            if (!$fetVector || empty($fetVector['installed_version'])) {
                try {
                    $pdo->exec('CREATE EXTENSION vector;');
                } catch (Exception) {
                    $return->result = 'fail';
                    $return->error = 'Postgresql启用vector扩展异常,请检查是否已安装...';
                    return $return;
                }
            }

            // 移除类型和函数,不移除会报错
            $tableStructCount = $pdo->query("SELECT COUNT(*) FROM pg_type WHERE typname = 'tablestruct';");
            if ($tableStructCount->fetch()['count']??0) {
                try {
                    $pdo->exec('DROP FUNCTION table_msg("a_schema_name" "pg_catalog"."varchar", "a_table_name" "pg_catalog"."varchar") CASCADE;');
                } catch (Exception) {}

                try {
                    $pdo->exec('DROP FUNCTION table_msg(a_table_name varchar) CASCADE;');
                } catch (Exception) {}

                try {
                    $pdo->exec('DROP FUNCTION pgsql_type(a_type varchar) CASCADE;');
                } catch (Exception) {}

                try {
                    $pdo->exec('DROP TYPE tablestruct;');
                } catch (Exception) {}
            }
        } catch (Exception $e) {
            $return->result = 'fail';
            $return->error = $e->getMessage();
            return $return;
        }

        return $return;
    }

    /**
     * Notes: 取得配置信息
     * @param string $dbName 数据库名称
     * @param array $connectionInfo 连接信息
     * @return stdclass
     * @throws @Exception
     * @author luzg(2020/8/25 11:05)
     */
    public function checkConfig(string $dbName, array $connectionInfo): stdclass
    {
        $return = new stdclass();
        $return->result = 'ok';

        /* Connect to database. */
        $this->setDBParam($connectionInfo);
        $this->dbh = $this->connectDB();
        if (str_contains($dbName, '.')) {
            $return->result = 'fail';
            $return->error = '没有发现数据库信息';
            return $return;
        }
        if (!is_object($this->dbh)) {
            $return->result = 'fail';
            $return->error = '安装错误，请检查连接信息:'.mb_strcut($this->dbh,0,30).'...';
            echo $this->dbh;
            return $return;
        }

        $version = $this->getMysqlVersion();

        if ( !$this->dbExists()) {
            if ( !$this->createDB($version)) {
                $return->result = 'fail';
                $return->error = '创建数据库错误';
                return $return;
            }
        } elseif ($this->tableExits() and $this->clearDB == false) {
            $return->result = 'fail';
            $return->error = '数据表已存在，您之前可能已安装本系统，如需继续安装请选择新的数据库。';
            return $return;
        } elseif ($this->dbExists() and $this->clearDB == true) {
            if (!$this->dropDb($connectionInfo['name'])) {
                $return->result = 'fail';
                $return->error = '数据表已经存在，删除已存在库错误,请手动清除';
                return $return;
            } else {
                if ( !$this->createDB($version)) {
                    $return->result = 'fail';
                    $return->error = '创建数据库错误!';
                    return $return;
                }
            }
        }

        /* Create tables. */
        if (!$this->createTable($version, $connectionInfo)) {
            $return->result = 'fail';
            $return->error = '创建表格失败';
            return $return;
        }

        return $return;
    }

    /**
     * Notes: Pgsql配置
     * @param $connectionInfo
     * @return stdclass
     */
    public function checkPgConfig($connectionInfo): stdclass
    {
        $return = new stdclass();
        $return->result = 'ok';

        try {
            $host     = $connectionInfo['pg_host']??'';
            $port     = $connectionInfo['pg_port']??'';
            $dbname   = $connectionInfo['pg_name']??'';
            $username = $connectionInfo['pg_user']??'';
            $password = $connectionInfo['pg_password']??'';
            $prefix   = trim($connectionInfo['pg_prefix']??'');

            $pdo = new PDO("pgsql:host=$host;port=$port;dbname=$dbname", $username, $password);
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            if (!is_object($pdo)) {
                $return->result = 'fail';
                $return->error = 'Postgresql数据库连接失败,请检查连接信息...';
                return $return;
            }

            if ($this->clearDB == true) {
                $tables = $pdo->query("SELECT table_name FROM information_schema.tables WHERE table_schema = 'public'");
                while ($row = $tables->fetch(PDO::FETCH_ASSOC)) {
                    $tableName = $row['table_name'];
                    try {
                        $pdo->exec("DROP TABLE IF EXISTS $tableName CASCADE");
                    } catch (PDOException $e) {
                        $return->result = 'fail';
                        $return->error = 'Postgresql清空所有表出错:'.$e->getMessage();
                    }
                }
            }

            $dbFile  = $this->getInstallRoot() . '/db/pg.sql';
            $content = str_replace(";\r\n", ";\n", file_get_contents($dbFile));
            $content = str_replace('cw_', $prefix, $content);
            $pdo->exec($content);
        } catch (PDOException $e) {
            $return->result = 'fail';
            $return->error = $e->getMessage();
        }

        return $return;
    }

    /**
     * Notes: 设置数据库相关信息
     * @author luzg(2020/8/25 11:17)
     * @param $post
     */
    public function setDBParam($post)
    {
        $this->host = $post['host'];
        $this->name = $post['name'];
        $this->user = $post['user'];
        $this->encoding = 'utf8mb4';
        $this->password = $post['password'];
        $this->port = $post['port'];
        $this->prefix = $post['prefix'];
        $this->clearDB = $post['clear_db'] == 'on';
    }

    /**
     * Notes: 连接数据库
     * @author luzg(2020/8/25 11:56)
     * @return PDO|string
     */
    public function connectDB(): PDO|string
    {
        $dsn = "mysql:host={$this->host}; port={$this->port}";
        try {
            $dbh = new PDO($dsn, $this->user, $this->password);
            $dbh->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_OBJ);
            $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $dbh->exec("SET NAMES {$this->encoding}");
            $dbh->exec("SET NAMES {$this->encoding}");
            try{
                $dbh->exec("SET GLOBAL sql_mode='STRICT_TRANS_TABLES,ERROR_FOR_DIVISION_BY_ZERO,NO_AUTO_CREATE_USER,NO_ENGINE_SUBSTITUTION';");
            }catch (Exception){

            }
            return $dbh;
        } catch (PDOException $exception) {
            return $exception->getMessage();
        }
    }

    /**
     * Notes: 检查数据库是否存在
     * @author luzg(2020/8/25 11:56)
     * @return mixed
     */
    public function dbExists(): mixed
    {
        $sql = "SHOW DATABASES like '{$this->name}'";
        return $this->dbh->query($sql)->fetch();
    }

    /**
     * Notes: 检查表是否存在
     * @author luzg(2020/8/25 11:56)
     * @return mixed
     */
    public function tableExits(): mixed
    {
        $configTable = sprintf("'%s'", $this->prefix . TESTING_TABLE);
        $sql = "SHOW TABLES FROM {$this->name} like $configTable";
        return $this->dbh->query($sql)->fetch();
    }

    /**
     * Notes: 获取mysql版本号
     * @author luzg(2020/8/25 11:56)
     * @return string
     */
    public function getMysqlVersion(): string
    {
        $sql = "SELECT VERSION() AS version";
        $result = $this->dbh->query($sql)->fetch();
        return substr($result->version, 0, 3);
    }

    /**
     * @notes 检测数据库sql_mode
     * @param $version
     * @return bool
     * @author 段誉
     * @date 2021/8/27 17:17
     */
    public function checkSqlMode($version): bool
    {
        $sql = "SELECT @@global.sql_mode";
        $result = $this->dbh->query($sql)->fetch();
        $result = (array)$result;

        if ($version >= 5.7 && $version < 8.0) {
            if ((str_contains($result['@@global.sql_mode'], 'NO_AUTO_CREATE_USER'))
                && (str_contains($result['@@global.sql_mode'], 'NO_ENGINE_SUBSTITUTION'))) {
                return true;
            }
            return false;
        }
        return true;
    }

    /**
     * Notes: 创建数据库
     * @author luzg(2020/8/25 11:57)
     * @param $version
     * @return mixed
     */
    public function createDB($version): mixed
    {
        $sql = "CREATE DATABASE `{$this->name}`";
        if ($version > 4.1) $sql .= " DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci";
        return $this->dbh->query($sql);
    }

    /**
     * Notes: 创建表
     * @author luzg(2020/8/25 11:57)
     * @param $version
     * @param $post
     * @return bool
     * @throws Exception
     */
    public function createTable($version, $post): bool
    {
        $dbFile = $this->getInstallRoot() . '/db/like.sql';
        //file_put_contents($dbFile, $this->initAccount($post), FILE_APPEND);
        $content = str_replace(";\r\n", ";\n", file_get_contents($dbFile));
        $tables = explode(";\n", $content);
        $tables[] = $this->initAccount($post);
        $installTime = microtime(true) * 10000;

        foreach ($tables as $table) {
            $table = trim($table);
            if (empty($table)) continue;

            if (str_contains($table, 'CREATE') and $version <= 4.1) {
                $table = str_replace('DEFAULT CHARSET=utf8', '', $table);
            }

            /* Skip sql that is note. */
            if (str_starts_with($table, '--')) continue;

            $table = str_replace('`cw_', $this->name . '.`cw_', $table);
            $table = str_replace('`cw_', '`' . $this->prefix, $table);

            if (str_contains($table, 'CREATE')) {
                $tableName = explode('`', $table)[1];
                $installTime += random_int(3000, 7000);
                $this->successTable[] = [$tableName, date('Y-m-d H:i:s', $installTime / 10000)];
            }

            try {
                if ( !$this->dbh->query($table)) return false;
            } catch (Exception $e) {
                echo 'error sql: ' . $table . "<br>";
                echo $e->getMessage() . "<br>";
                return false;
            }
        }
        // 移动图片资源
        $this->cpFiles($this->getInstallRoot().'/uploads', $this->getAppRoot().'/public/uploads/images');
        return true;
    }

    /**
     * Notes: 删除数据库
     * @param $db
     * @return false|PDOStatement
     */
    public function dropDb($db): bool|PDOStatement
    {
        $sql = "drop database $db;";
        return $this->dbh->query($sql);
    }

    /**
     * Notes: 取得安装成功的表列表
     * @author luzg(2020/8/26 18:28)
     * @return array
     */
    public function getSuccessTable(): array
    {
        return $this->successTable;
    }

    /**
     * Notes: 创建演示数据
     * @author luzg(2020/8/25 11:58)
     * @return bool
     */
    public function importDemoData(): bool
    {
        $demoDataFile = 'ys.sql';
        $demoDataFile = $this->getInstallRoot() . '/db/' . $demoDataFile;
        if (!is_file($demoDataFile)) {
            echo "<br>";
            echo 'no file:' .$demoDataFile;
            return false;
        }
        $content = str_replace(";\r\n", ";\n", file_get_contents($demoDataFile));
        $insertTables = explode(";\n", $content);
        foreach ($insertTables as $table) {
            $table = trim($table);
            if (empty($table)) continue;

            $table = str_replace('`cw_', $this->name . '.`cw_', $table);
            $table = str_replace('`cw_', '`' .$this->prefix, $table);
            if ( !$this->dbh->query($table)) return false;
        }

        // 移动图片资源
        $this->cpFiles($this->getInstallRoot().'/uploads', $this->getAppRoot().'/public/uploads/images');

        return true;
    }

    /**
     * 将一个文件夹下的所有文件及文件夹
     * 复制到另一个文件夹里（保持原有结构）
     *
     * @param <string> $rootFrom 源文件夹地址（最好为绝对路径）
     * @param <string> $rootTo 目的文件夹地址（最好为绝对路径）
     */
    function cpFiles($rootFrom, $rootTo){

        $handle = opendir($rootFrom);
        while (false !== ($file = readdir($handle))) {
            //DIRECTORY_SEPARATOR 为系统的文件夹名称的分隔符 例如：windos为'/'; linux为'/'
            $fileFrom = $rootFrom . DIRECTORY_SEPARATOR . $file;
            $fileTo = $rootTo . DIRECTORY_SEPARATOR . $file;
            if ($file == '.' || $file == '..') {
                continue;
            }

            if (is_dir($fileFrom)) {
                if (!is_dir($fileTo)) { //目标目录不存在则创建
                    mkdir($fileTo, 0777,true);
                }
                $this->cpFiles($fileFrom, $fileTo);
            } else {
                if (!file_exists($fileTo)) {
                    @copy($fileFrom, $fileTo);
                    if (strstr($fileTo, "access_token.txt")) {
                        chmod($fileTo, 0777);
                    }
                }
            }

        }
    }

    /**
     * Notes: 当前应用程序的相对路径
     * @author luzg(2020/8/25 10:55)
     * @return string
     */
    #[Pure]
    public function getAppRoot(): string
    {
        return realpath($this->getInstallRoot() . '/../../');
    }

    /**
     * Notes: 获取安装目录
     * @author luzg(2020/8/26 16:15)
     * @return string
     */
    public function getInstallRoot(): string
    {
        return INSTALL_ROOT;
    }

    /**
     * Notes: 目录的容量
     * @author luzg(2020/8/25 15:21)
     * @return string
     */
    public function freeDiskSpace(): string
    {
        // M
        $freeDiskSpace = disk_free_space(realpath(__DIR__)) / 1024 / 1024;

        // G
        if ($freeDiskSpace > 1024) {
            return number_format($freeDiskSpace / 1024, 2) . 'G';
        }

        return number_format($freeDiskSpace, 2) . 'M';
    }

    /**
     * Notes: 获取状态标志
     * @author luzg(2020/8/25 16:10)
     * @param $statusSingle
     * @return string
     */
    public function correctOrFail($statusSingle): string
    {
        if ($statusSingle == 'ok')
            return '<td class="layui-icon green">&#xe605;</td>';
        $this->allowNext = false;
        return '<td class="layui-icon wrong">&#x1006;</td>';
    }

    /**
     * Notes: 是否允许下一步
     * @author luzg(2020/8/25 17:29)
     * @return bool
     */
    public function getAllowNext(): bool
    {
        return $this->allowNext;
    }

    /**
     * Notes: 检查session auto start
     * @author luzg(2020/8/25 16:55)
     * @return string
     */
    public function checkSessionAutoStart(): string
    {
        return ini_get('session.auto_start') == '0' ? 'ok' : 'fail';
    }

    /**
     * Notes: 检查auto tags
     * @author luzg(2020/8/25 16:55)
     * @return string
     */
    public function checkAutoTags(): string
    {
        return ini_get('session.auto_start') == '0' ? 'ok' : 'fail';
    }

    /**
     * Notes: 检查目录是否可写
     * @param string $dir
     * @return string
     */
    #[Pure]
    public function checkDirWrite(string $dir=''): string
    {
        $route = $this->getAppRoot().'/'.$dir;
        return is_writable($route) ? 'ok' : 'fail';
    }

    /**
     * Notes: 检查目录是否可写
     * @param string $dir
     * @return string
     */
    #[Pure]
    public function checkSuperiorDirWrite(string $dir=''): string
    {
        $route = $this->getAppRoot().'/'.$dir;
        return is_writable($route) ? 'ok' : 'fail';
    }

    /**
     * Notes: 初始化管理账号
     * @param $post
     * @return string
     */
    public function initAccount($post): string
    {
        $time = time();
        $salt = substr(md5($time . $post['admin_user']), 0, 4);//随机4位密码盐

        global $uniqueSalt;
        $uniqueSalt = $salt;

        $password = $this->createPassword($post['admin_password'], $salt);

        // 超级管理员
        $sql = "INSERT INTO `cw_admin`(`id`, `root`, `name`, `avatar`, `account`, `password`, `login_time`, `login_ip`, `multipoint_login`, `disable`, `create_time`, `update_time`, `delete_time`) VALUES (1, 1, '{$post['admin_user']}', '', '{$post['admin_user']}', '{$password}','{$time}', '', 1, 0, '{$time}', '{$time}', NULL);";
        // 超级管理员关联部门
        $sql .= "INSERT INTO `cw_admin_dept` (`admin_id`, `dept_id`) VALUES (1, 1);";

        return $sql;
    }

    /**
     * Notes: 生成密码密文
     * @param $pwd
     * @param $salt
     * @return string
     */
    public function createPassword($pwd, $salt): string
    {
        return md5($salt . md5($pwd . $salt));
    }

    /**
     * @notes 恢复admin,mobile index文件
     * @author 段誉
     * @date 2021/9/16 15:51
     */
    public function restoreIndexLock()
    {
        $this->checkIndexFile($this->getAppRoot().'/public/mobile');
        $this->checkIndexFile($this->getAppRoot().'/public/admin');
    }

    public function checkIndexFile($path)
    {
        if(file_exists($path.'/index_lock.html')) {
            // 删除提示文件
            unlink($path.'/index.html');
            // 恢复原入口
            rename($path.'/index_lock.html', $path.'/index.html');
        }
    }

    /**
     * @notes 检查swoole_loader扩展的线程安装
     * @return bool
     * @author cjhao
     * @date 2023/5/10 16:20
     */
    function swooleLoaderIsThreadSafety(): bool
    {
        ob_start();
        phpinfo();
        $phpInfo = strip_tags(ob_get_contents());
        ob_end_clean();
        if (php_sapi_name() == 'cli') {
            return !preg_match_all('#Thread\s+Safety\s+\=\>\s+disabled#i', $phpInfo, $match);
        } else {
            return !preg_match_all('#Thread\s+Safety\s+disabled#i', $phpInfo, $match);
        }
    }

    /**
     * @notes 安装记录
     * @return bool
     * @author mjf
     * @date 2024/7/3 17:44
     */
    public function installLog(): bool
    {
        try {
            $code = 'c46ddcee63781baf994ff2ac4fcd1c9d';
            $data = $this->dataEncrypt($this->getHost(), $code);
            $url  = "https://server.chatmoney.cn/indexapi/version/installLog";

            $curl = curl_init();
            curl_setopt_array($curl, [
                    CURLOPT_URL            => $url,
                    CURLOPT_RETURNTRANSFER => true,
                    CURLOPT_ENCODING       => '',
                    CURLOPT_MAXREDIRS      => 10,
                    CURLOPT_TIMEOUT        => 0,
                    CURLOPT_SSL_VERIFYPEER => false,
                    CURLOPT_SSL_VERIFYHOST => false,
                    CURLOPT_FOLLOWLOCATION => true,
                    CURLOPT_HTTP_VERSION   => CURL_HTTP_VERSION_1_1,
                    CURLOPT_CUSTOMREQUEST  => 'POST',
                    CURLOPT_POSTFIELDS     => json_encode([
                        'code' => $code,
                        'data' => $data
                    ], true),
                    CURLOPT_HTTPHEADER     => ['Content-Type: application/json']
                ]
            );
            curl_exec($curl);
            curl_close($curl);

            return true;
        } catch (Exception $e) {
            return false;
        }
    }

    public function getHost(): string
    {
        $host = strval($_SERVER['HTTP_X_FORWARDED_HOST'] ?? $_SERVER['HTTP_HOST']);
        return strpos($host, ':') ? strstr($host, ':', true) : $host;
    }

    public function dataEncrypt($string, $key): string
    {
        $result    = '';
        $keyLength = strlen($key);
        for ($i = 0; $i < strlen($string); $i++) {
            $char    = $string[$i];
            $keyChar = $key[$i % $keyLength];
            $result  .= chr(ord($char) ^ ord($keyChar));
        }
        return base64_encode($result);
    }
}
