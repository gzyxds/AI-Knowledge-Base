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

namespace app\adminapi\logic;

use app\common\enum\PayEnum;
use app\common\logic\BaseLogic;
use app\common\model\kb\KbKnow;
use app\common\model\kb\KbRobot;
use app\common\model\member\MemberOrder;
use app\common\model\recharge\RechargeOrder;
use app\common\model\user\User;
use app\common\model\Visitor;
use app\common\service\ConfigService;
use app\common\service\FileService;

/**
 * 工作台
 */
class WorkbenchLogic extends BaseLogic
{
    /**
     * @notes 工作套
     * @return array
     * @author 段誉
     * @date 2021/12/29 15:58
     */
    public static function index(): array
    {
        return [
            // 版本信息
            'version' => self::versionInfo(),
            // 今日数据
            'today'   => self::today(),
            // 待办事项
            'wait' => self::wait(),
            // 常用功能
            'menu'    => self::menu(),
            // 近15日访客数
            'visitor' => self::visitor(),
            // 近15日销售量
            'sales' => self::sales()
        ]??[];
    }

    /**
     * @notes 待办事项
     * @return string[]
     * @throws @\think\db\exception\DbException
     * @author ljj
     * @date 2023/5/8 2:38 下午
     */
    public static function wait(): array
    {
        return [
            'robot'       => (new KbRobot())->count(),  //机器人
            'know'        => (new KbKnow())->count(),   //知识库
            'today_robot' => (new KbRobot)->whereDay('create_time')->count(),
            'today_know'  => (new KbKnow())->whereDay('create_time')->count(),
        ]??[];
    }

    /**
     * @notes 常用功能
     * @return array[]
     * @author 段誉
     * @date 2021/12/29 16:40
     */
    public static function menu(): array
    {
        return [
            [
                'name'  => '机器人应用',
                'image' => FileService::getFileUrl(config('project.default_image.menu_type')),
                'url'   => '/konwledge_base/robot/application'
            ],
            [
                'name' => '知识库管理',
                'image' => FileService::getFileUrl(config('project.default_image.menu_make')),
                'url' => '/konwledge_base/knowledge_base/knowledge_base'
            ],
            [
                'name' => '订单管理',
                'image' => FileService::getFileUrl(config('project.default_image.menu_order')),
                'url' => '/order/recharge'
            ],
            [
                'name'  => '用户管理',
                'image' => FileService::getFileUrl(config('project.default_image.menu_user')),
                'url'   => '/consumer/lists'
            ],
            [
                'name'  => '财务中心',
                'image' => FileService::getFileUrl(config('project.default_image.menu_finance')),
                'url'   => '/finance/center'
            ]
        ]??[];
    }

    /**
     * @notes 版本信息
     * @return array
     * @author 段誉
     * @date 2021/12/29 16:08
     */
    public static function versionInfo(): array
    {
        return [
            'version' => config('project.version'),
            'website' => config('project.website.url'),
            'name'    => ConfigService::get('website', 'name')
        ]??[];
    }

    /**
     * @notes 今日数据
     * @return int[]
     * @throws @\think\db\exception\DbException
     * @author 段誉
     * @date 2021/12/29 16:15
     */
    public static function today(): array
    {
        $modelVisitor = new Visitor();
        $modelRechargeOrder = new RechargeOrder();
        $todayOrderNum     = $modelRechargeOrder->where(['pay_status'=>PayEnum::ISPAID])->whereDay('create_time')->count();
        $yesterdayOrderNum = $modelRechargeOrder->where(['pay_status'=>PayEnum::ISPAID])->whereDay('create_time', 'yesterday')->count();


        $memberTodayOrderNum     = (new MemberOrder())->where(['pay_status'=>PayEnum::ISPAID])->whereDay('create_time')->count();
        $memberYesterdayOrderNum = (new MemberOrder())->where(['pay_status'=>PayEnum::ISPAID])->whereDay('create_time', 'yesterday')->count();

        $todayOrderAmount     = $modelRechargeOrder->where(['pay_status'=>PayEnum::ISPAID])->whereDay('create_time')->sum('order_amount');
        $yesterdayOrderAmount = $modelRechargeOrder->where(['pay_status'=>PayEnum::ISPAID])->whereDay('create_time', 'yesterday')->sum('order_amount');

        $memberTodayOrderAmount     = (new MemberOrder())->where(['pay_status'=>PayEnum::ISPAID])->whereDay('create_time')->sum('order_amount');
        $memberYesterdayOrderAmount = (new MemberOrder())->where(['pay_status'=>PayEnum::ISPAID])->whereDay('create_time', 'yesterday')->sum('order_amount');


        $modelUser = new User();
        $todayUserNum     = $modelUser->whereDay('create_time')->count();
        $yesterdayUserNum = $modelUser->whereDay('create_time', 'yesterday')->count();

        $todayVisitor = count(array_unique($modelVisitor->whereDay('create_time')->column('ip')));
        $yesterdayVisitor = count(array_unique($modelVisitor->whereDay('create_time', 'yesterday')->column('ip')));

        return [
            'time' => date('Y-m-d H:i:s'),
            // 今日收入
            'today_amount'     => round($todayOrderAmount+$memberTodayOrderAmount,2),     // 今日收入
            'yesterday_amount' => round($yesterdayOrderAmount+$memberYesterdayOrderAmount,2), // 昨天收入
            'contrast_amount'  => round(($todayOrderAmount+$memberTodayOrderAmount) - ($yesterdayOrderAmount+$memberYesterdayOrderAmount), 2),

            // 订单笔数
            'today_order'     => $todayOrderNum+$memberTodayOrderNum,     // 今日订单数
            'yesterday_order' => $yesterdayOrderNum+$memberYesterdayOrderNum, // 昨天订单数
            'contrast_order'  => $todayOrderNum+$memberTodayOrderNum - $yesterdayOrderNum+$memberYesterdayOrderNum,

            // 新增用户
            'today_user'      => $todayUserNum,     // 今日新增用户
            'yesterday_user'  => $yesterdayUserNum, // 昨天新增用户
            'contrast_user'   => $todayUserNum - $yesterdayUserNum,

            // 今日访问量
            'today_visitor'     => $todayVisitor,     // 今日新增访问量
            'yesterday_visitor' => $yesterdayVisitor,   // 昨天新增访问量
            'contrast_visitor'  => $todayVisitor - $yesterdayUserNum,   // 对比昨天访问量
        ]??[];
    }

    /**
     * @notes 访问数 (近15天)
     * @return array
     * @author 段誉
     * @date 2021/12/29 16:57
     */
    public static function visitor(): array
    {
        $time15 = date("Y-m-d",strtotime ("-14 day")).' 00:00:00';
        $time   = date("Y-m-d",time()). ' 23:59:59';

        $db = app('db');
        $subQuery = $db->name('visitor')
            ->distinct(true)
            ->field("FROM_UNIXTIME(create_time,'%Y%m%d') as day,ip")
            ->whereTime('create_time', 'between', [$time15, $time])
            ->buildSql();

        $lists = $db->table($subQuery . ' a')
            ->field("day,count(ip) as count")
            ->group('day')
            ->select()
            ->toArray();

        $lists = array_column($lists, 'count', 'day');

        $date = [];
        $userData = [];
        for($i = 0; $i < 15; $i ++) {
            $time = date("Ymd",strtotime ("-$i day"));
            $date[] = $time;
            $userData[] = $lists[$time] ?? 0;
        }

        return [
            'date' => $date,
            'list' => [
                ['name' => '访客数', 'data' => $userData]
            ]
        ]??[];
    }

    /**
     * @notes 销售量 (近15天)
     * @return array
     * @author fzr
     */
    public static function sales(): array
    {
        $time15 = date("Y-m-d",strtotime ("-14 day")).' 00:00:00';
        $time   = date("Y-m-d",time()). ' 23:59:59';

        $db = app('db');
        $subQuery = $db->name('recharge_order')
            ->distinct(true)
            ->field("FROM_UNIXTIME(create_time,'%Y%m%d') as day,order_amount")
            ->whereTime('create_time', 'between', [$time15, $time])
            ->buildSql();

        $rechargeLists = $db->table($subQuery . ' a')
            ->field("day,order_amount")
            ->group('day')
            ->select()
            ->toArray();

        $rechargeLists = array_column($rechargeLists, 'order_amount', 'day');

        $memberSubQuery = $db->name('member_order')
            ->distinct(true)
            ->field("FROM_UNIXTIME(create_time,'%Y%m%d') as day,order_amount")
            ->whereTime('create_time', 'between', [$time15, $time])
            ->buildSql();
        $memberLists = $db->table($memberSubQuery . ' a')
            ->field("day,order_amount")
            ->group('day')
            ->select()
            ->toArray();

        $memberLists = array_column($memberLists, 'order_amount', 'day');

        $date = [];
        $orderData = [];
        for($i = 0; $i < 15; $i ++) {
            $time = date("Ymd",strtotime ("-$i day"));
            $rechargeAmount = $rechargeLists[$time] ?? 0;
            $memberAmount = $memberLists[$time] ?? 0;
            $orderData[] = round($rechargeAmount+$memberAmount,2);
            $date[] = $time;
        }

        return [
            'date' => $date,
            'list' => [
                ['name' => '销售量', 'data' => $orderData]
            ]
        ]??[];
    }
}