<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017-03-25
 * Time: 11:00
 */
namespace Web\Controller;

use Common\Controller\AdminbaseController;

class OptionController extends AdminbaseController {
    //设置
    public function webSetting($option_name, $option_value = 0) {
        $m_options = M('options');
        $condition = [
            'option_name' => $option_name
        ];
        $data = $m_options->where($condition)->find();
        if (empty($data)) {
            if (is_array($option_value)) {
                $option_value = json_encode($option_value);
            }
            $data = [
                'option_name'  => $option_name,
                'autoload'     => 1,
                'option_value' => $option_value
            ];
            $result = $m_options->add($data);//->data()
            if (!$result) {
                $this->error("操作失败！");
            }
            $data['option_value'] = json_decode($option_value, true);
        } else {
            if (in_array($option_name, ['hs_product', 'other_product', 'web_seo'])) { //需要数组结构
                if (!empty($data['option_value'])) {
                    $data['option_value'] = json_decode(
                        $data['option_value'], true
                    );//[['name' => 'ios','url'=>'']['name' => 'h5','url'=>'']]
                } else {
                    $data['option_value'] = [];
                }
            }
        }

        return $data;
    }

    //火树产品设置
    public function hsProduct() {
        $data = $this->webSetting('hs_product');
        $option_value = $data['option_value'];
        $other_product = $this->webSetting('other_product');
        $other_option_value = $other_product['option_value'];
        $this->assign("data", $data);
        $this->assign("option_value", $option_value);
        $this->assign("other_product", $other_product);
        $this->assign("other_option_value", $other_option_value);
        $this->display();
    }

    //火树产品添加
    public function hsProductRecord() {
        $this->display();
    }

    //其他产品添加
    public function otherProductRecord() {
        $this->display();
    }

    //设置提交处理接口
    public function operationSetting() {
        if (IS_POST) {
            $option_name = I('option_name');
            $name = I('name');
            $url = I('url');
            $key = I('option_key', ''); //以此决定是否编辑
            if (empty($option_name)) {
                $this->error("参数错误！");
            }
            if (empty($name)) {
                $this->error("名称不得为空！");
            }
            $m_option = M('options');
            $condition = [
                'option_name' => $option_name,
            ];
            $record = $m_option->where($condition)->find();
            if (!empty($record['option_value'])) {
                $option_value = json_decode($record['option_value'], true);
            } else {
                $option_value = [];
            }
            if ($key != '') { //编辑
                if (empty($option_value[$key])) {
                    $this->error("编辑数据有误！");
                }
                $option_value[$key] = ['name' => $name, 'url' => $url];
            } else {//新增
                $option_value[] = ['name' => $name, 'url' => $url];
            }
            $data = [
                'option_value' => json_encode($option_value)
            ];
            if (!$m_option->where($condition)->save($data)) {
                $this->error("操作失败！");
            }
            $this->success("保存成功！", U("Option/hsProduct"));
        }
    }

    //删除产品
    public function deleteProduct() {
        $option_name = I('option_name');
        $name = I('name');
        if (empty($option_name)) {
            $this->error("参数错误！");
        }
        $m_option = M('options');
        $condition = [
            'option_name' => $option_name,
        ];
        $record = $m_option->where($condition)->find();
        if (!empty($record['option_value'])) {
            $option_value = json_decode($record['option_value'], true);
        } else {
            $this->error("数据已删除！");
        }
        foreach ($option_value as $k => $v) {
            if ($v['name'] == $name) {
                unset($option_value[$k]);
            }
        }
        $data = [
            'option_value' => json_encode($option_value)
        ];
        if (!$m_option->where($condition)->save($data)) {
            $this->error("操作失败！");
        }
        $this->success("保存成功！", U("Option/hsProduct"));
    }

    //seo设置
    public function seo_setting() {
        $option_value = [
            'title'       => '',
            'keywords'    => '',
            'description' => ''
        ];
        $data = $this->webSetting('web_seo', $option_value);
        $this->assign("data", $data);
        $this->display();
    }

    //修改seo
    public function seo_edit() {
        $this->display();
    }

    //seo修改提交地址
    public function seo_post() {
        $m_option = M('options');
        $name = I('name');
        $value = I('value');
        $data = $this->webSetting('web_seo');
        if (isset($data['option_value'][$name])) {
            $data['option_value'][$name] = $value;
        }
        $data['option_value'] = json_encode($data['option_value']);
        unset($data['option_name']);
        unset($data['option_id']);
        unset($data['autoload']);
        $condition = [
            'option_name' => 'web_seo',
        ];
        if (!$m_option->where($condition)->save($data)) {
            $this->error("操作失败！");
        }
        $this->success("保存成功！", U("Option/seo_setting"));
    }

    //客服qq设置
    public function service_qq() {
        $data = $this->webSetting('web_service_qq');
        $this->assign("data", $data);
        $this->display();
    }

    //修改客服qq
    public function service_qq_edit() {
        $this->display();
    }

    //客服qq修改提交地址
    public function service_qq_post() {
        $this->base_post('service_qq');
    }

    //公司信息配置
    public function CompanyInfo(){
        redirect(U('Admin/CompanyInfo/edit'));
        exit;
    }

//    //公司名设置
//    public function company_name() {
//        $data = $this->webSetting('web_company_name');
//        $this->assign("data", $data);
//        $this->display();
//    }
//
//    //修改公司名
//    public function company_name_edit() {
//        $this->display();
//    }
//
//    //公司名修改提交地址
//    public function company_name_post() {
//        $this->base_post('company_name');
//    }

//    //公司地址设置
//    public function company_address() {
//        $data = $this->webSetting('web_company_address');
//        $this->assign("data", $data);
//        $this->display();
//    }
//
//    //修改公司地址
//    public function company_address_edit() {
//        $this->display();
//    }
//
//    //公司地址修改提交地址
//    public function company_address_post() {
//        $this->base_post('company_address');
//    }

//    //版权设置
//    public function copyright() {
//        $data = $this->webSetting('web_copyright');
//        $this->assign("data", $data);
//        $this->display();
//    }
//
//    //修改版权
//    public function copyright_edit() {
//        $this->display();
//    }
//
//    //版权修改提交地址
//    public function copyright_post() {
//        $this->base_post('copyright');
//    }
//
//    //备案号设置
//    public function record_number() {
//        $data = $this->webSetting('web_record_number');
//        $this->assign("data", $data);
//        $this->display();
//    }
//
//    //修改备案号
//    public function record_number_edit() {
//        $this->display();
//    }
//
//    //备案号修改提交地址
//    public function record_number_post() {
//        $this->base_post('record_number');
//    }

    //文网文信息设置
//    public function text_snippets() {
//        $data = $this->webSetting('web_text_snippets');
//        $this->assign("data", $data);
//        $this->display();
//    }
//
//    //修改文网文信息
//    public function text_snippets_edit() {
//        $this->display();
//    }
//
//    //文网文信息修改提交地址
//    public function text_snippets_post() {
//        $this->base_post('text_snippets');
//    }

    //关于我们
    public function aboutus() {
        redirect(U('/Web/Aboutus/editAbout/id/1'));
        exit;
    }

    //联系我们
    public function contact() {
        redirect(U('/Web/Aboutus/editAbout/id/4'));
        exit;
    }

    //商务合作
    public function cooperation() {
        redirect(U('/Web/Aboutus/editAbout/id/2'));
        exit;
    }

    //公用数据提交方法
    public function base_post($potion_name) {
        $m_option = M('options');
        $value = I('value');
        $condition = [
            'option_name' => 'web_'.$potion_name,
        ];
        $data['option_value'] = $value;
        if (!$m_option->where($condition)->save($data)) {
            $this->error("操作失败！");
        }
        $this->success("保存成功！", U("Option/".$potion_name));
    }
}