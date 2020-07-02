<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017-03-17
 * Time: 13:59
 */
namespace app\index\common;

use app\index\model\Game;
use think\Config;
use think\Controller;
use think\Db;

class Base extends Controller {
    protected $row;
    protected $btpname;
    protected $company;
    protected $session_config
        = [
            'prefix'     => 'module',
            'type'       => '',
            'auto_start' => true,
        ];

    /**
     * Base constructor.
     */
    public function __construct(){
        Config::set('default_return_type','json');
        $config = dirname(dirname(APP_PATH))."/conf/company.php";
        //获取配置信息
        if (file_exists($config)) {
            $this->company = include $config;
        } else {
            $this->company = array();
        }

        Config::set('session', $this->session_config);
        \think\Session::init($this->session_config);
        $this->btpname = $this->company['PTBNAME'];
    }

    public function _initialize() {
        header("Access-Control-Allow-Origin: *");
        header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
        header("Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept, Authorization");
        parent::_initialize();
        $this->row = 10;
    }

    // 获取PC网站的头部信息
    public function getHeader(Game $game) {
        $game_platform_classify = $this->getHsproduct();
        // 获取热门游戏
        $hot_game = $game->getHotGame();
        // 获取其它产品
        $other_product = $this->getOtherProduct();
        // 搜索热词
        $search_hot_game = $game->getName();
        // 右边导航栏的logo
        $nav_right_app = $this->rightLogo();
        $header_info = array(
            "company_brand"          => $this->company['BRAND_NAME'],
            "game_platform_classify" => $game_platform_classify,
            "hot_game"               => $hot_game,
            "other_product"          => $other_product,
            "service_qq"             => $this->serviceqq(),
            "search_hot_game"        => $search_hot_game,
            "company_logo"           => $this->logo(),
            "nav_right_app"          => $nav_right_app,
        );

        return $header_info;
    }

    // 获取公司的qq
    public function serviceqq() {
        $qq = Db::name('game_contact')->where("app_id", 0)->value('qq');

        return $qq;
    }

    // 获取公司的logo
    public function logo($type_id = 6) {
        //$data['type_id'] = $type_id;
        $data['slide_name'] = '头部LOGO';
        $data['slide_status'] = 2;

        return Db::name('slide')->where($data)->value('slide_pic');
    }

    // 获取文网文标志 微信公众号二维码
    public function www($type_id = 6) {
        //$data['type_id'] = $type_id;
        $data['slide_name'] = '文网文logo';
        $data['slide_status'] = 2;
        $www = Db::name('slide')
                 ->field('slide_pic, slide_url')
                 ->where($data)
                 ->find();

        return $www;
    }

    // 获取文网文标志 微信公众号二维码
    public function wxcode($type_id = 6) {
        //$data['type_id'] = $type_id;
        $data['slide_name'] = '微信公众号二维码';
        $data['slide_status'] = 2;

        return Db::name('slide')->where($data)->value('slide_pic');
    }

    // 获取底部信息
    public function getFooter() {
        $company = $this->getCompanyInfo();
        $www = $this->www();
        $company_info = array(
            "game_certification_icon" => $www['slide_pic'],
            "game_certification_url"  => $www['slide_url'],
            "web_icp"                 => $company['WEB_ICP'],
            "company_name"            => $company['COMPANY_NAME'],
            "copyright"               => $company['COPYRIGHT'],
            "company_wx_code"         => $this->wxcode()
        );

        return $company_info;
    }

    // 获取公司的底部信息
    public function getCompanyInfo() {
        $v_str = Db::name('options')->where("`option_name` = 'company_info'")->value("option_value");
        $data = json_decode($v_str, true);

        return $data;
    }

    // 获取分页类的条件字符串
    public function getPageString($row = 0) {
        $page = input('param.p/d', 1);
        $row = empty($row) ? $this->row : $row;
        $start = ($page - 1) * $row;

        return $start.','.$row;
    }

    // 获取火树产品
    public function getHsproduct() {
        $hsproduct = Db::name('options')
                       ->where(array('option_name' => 'hs_product'))
                       ->value('option_value');
        $hsproductData = json_decode($hsproduct, true);
        // 判断是否为空避免报错
        if (empty($hsproductData)) {
            return false;
        }
        foreach ($hsproductData as $key => $val) {
            $hsproductData[$key]['classify_name'] = $val['name'];
            $hsproductData[$key]['classify_url'] = $val['url'];
        }

        return $hsproductData;
    }

    // 获取其它产品
    public function getOtherProduct() {
        $hsproduct = Db::name('options')
                       ->where(array('option_name' => 'other_product'))
                       ->value('option_value');
        $hsproductData = json_decode($hsproduct, true);
        // 判断是否为空避免报错
        if (empty($hsproductData)) {
            return false;
        }
        foreach ($hsproductData as $key => $val) {
            $otherProductData[$key]['product_name'] = $val['name'];
            $otherProductData[$key]['product_url'] = $val['url'];
        }

        return $otherProductData;
    }

    // 获取右边导航栏的logo
    public function rightLogo() {
        /*
         * ["app_name" => "app",
         * "app_logo" => "http://cdn2.guopan.cn/frontend/pc/static/img/headerCoin_4f464d3.png?__sprite"
         * ,"app_url" => "http://huo.huosdk.com"],
            ["app_name" => "火币充值","app_logo" => "http://cdn2.guopan.cn/frontend/pc/static/img/headerCoin_4f464d3.png?__sprite","app_url" => "http://huo.huosdk.com"],
            ["app_name" => "礼包","app_logo" => "http://cdn2.guopan.cn/frontend/pc/static/img/headerCoin_4f464d3.png?__sprite","app_url" => "http://huo.huosdk.com"],
            ["app_name" => "苹果app","app_logo" => "http://cdn2.guopan.cn/frontend/pc/static/img/headerCoin_4f464d3.png?__sprite","app_url" => "http://huo.huosdk.com"]
         * */
        $field = 'slide_name app_name,slide_pic app_logo, slide_url app_url';
        $rightLogo = Db::name('slide_cat')
                       ->alias('c')
                       ->field($field)
                       ->join('__SLIDE__ s', 's.slide_cid=c.cid', 'LEFT')
                       ->where('cat_name', '官网图片设置')
                       ->where('type_id', 7)
                       ->where('slide_status', 2)
                       ->select();
        foreach ($rightLogo as $key => $value) {
            if ($key == 0) {
                $rightLogo[$key]['app_url'] = $this->getdownurl();
            } else if ($key == 1) {
                $rightLogo[$key]['app_url'] = WEBSITE."/#/user/recharge";
            } else if ($key == 2) {
                $rightLogo[$key]['app_url'] = WEBSITE."/#/package";
            } else if ($key == 3) {
                $rightLogo[$key]['app_url'] = WEBSITE;
            }
        }

        return $rightLogo;
    }

    public function getdownurl() {
        //_gv_map['status'] = 2;
        $_gv_map['app_id'] = 100;
        $_gv_info = Db::name('game_version')->where($_gv_map)->order('id desc')->limit(1)->select();
        if (!empty($_gv_info)) {
            $_downurl = $_gv_info[0]['packageurl'];
        }
        if (empty($_downurl)) {
            return '';
        } else {
            $_downurl = $_downurl;
        }

        return $_downurl;
    }

    // 获取seo信息
    public function getSeo() {
        $seoinfo = Db::name('options')->where(array('option_name' => 'web_seo'))->value('option_value');

        return json_decode($seoinfo, true);
    }
}