<?php
/**
 * User: wangchuang
 * Date: 2017-04-05
 * Time: 10:23
 */
namespace app\index\controller;

use app\index\common\Base;
use app\index\model\WebAboutus;
use think\Db;

class Company extends Base {

    // 获取关于我们
    public function aboutUs(WebAboutus $webAboutUs) {
        $data['content'] = $webAboutUs->getAboutUsInfo();

        return $data;
    }

    // 获取联系我们
    public function contactUs(WebAboutus $webAboutUs) {
        $data['content'] = $webAboutUs->getcontactUsInfo();

        return $data;
    }

    // 获取商务合作
    public function businessInfo(WebAboutus $webAboutUs) {
        $data['content'] = $webAboutUs->getBusinessInfo();

        return $data;
    }

    // 获取公司的联系方式和地址
    public function getCompanyInfo() {
        $contact =  Db::name('game_contact')->where("app_id", 0)->find();

        $data =  parent::getCompanyInfo();
        $companyInfo['company_phone'] = $contact['tel'];
        //$email = Db::name('game_contact')->where('app_id', 0)->value('email');
        $companyInfo['company_email'] = $contact['email'];
        //$companyInfo['company_name'] = $data['COMPANY_NAME'];
        //$companyInfo['company_addr'] = $data['COMPANY_ADDR'];

        $companyInfo['jhr_url'] = WEBSITE."/public/web/images/jhr.doc";
        $companyInfo['bjhr_url'] = WEBSITE."/public/web/images/bjhr.doc";
        $companyInfo['wcnjzjh_url'] = WEBSITE."/public/web/images/wcnjzjh.doc";
        $companyInfo['sqbd_url'] = WEBSITE."/public/web/images/sqbd.zip";

        return $companyInfo;
    }
}