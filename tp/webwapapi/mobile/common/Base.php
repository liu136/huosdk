<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017-03-17
 * Time: 13:59
 */
namespace app\mobile\common;

use think\Config;
use think\Controller;

class Base extends Controller {
    protected $row;
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

        Config::set('session', $this->session_config);
        \think\Session::init($this->session_config);
    }

    public function _initialize() {
        header("Access-Control-Allow-Origin: *");
        header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
        header("Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept, Authorization");
        parent::_initialize();
        $this->row = 10;
        //Config::set('default_return_type','json');
       // Config::set('session', $this->session_config);
        //\think\Session::init($this->session_config);
//        $this->btpname = $this->company['PTBNAME'];
    }
}