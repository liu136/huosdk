<?php
/**
 * GameAfficheController.class.php UTF-8
 * app游戏管理
 *
 * @date    : 2016年9月2日下午11:01:47
 *
 * @license 这不是一个自由软件，未经授权不许任何使用和传播。
 * @author  : wuyonghong <wyh@huosdk.com>
 * @version : H5 2.0
 */
namespace Newapp\Controller;

use Common\Controller\AdminbaseController;

class GameAfficheController extends AdminbaseController {
    protected $model;
    function _initialize() {
        parent::_initialize();
        $this->model = M("game_affiche");
    }

    function index() {
        $this->_game();
        $where_ands = array(
            // " p.status = 1"
        );
        $fields = array(
            'start_time' => array(
                "field"    => "p.create_time",
                "operator" => ">"
            ),
            'end_time'   => array(
                "field"    => "p.create_time",
                "operator" => "<"
            ),
            'keyword'    => array(
                "field"    => "p.title",
                "operator" => "like"
            ),
            'appid'      => array(
                "field"    => "p.app_id",
                "operator" => "="
            )
        );
        if (IS_POST) {
            foreach ($fields as $param => $val) {
                if (isset($_POST[$param]) && !empty($_POST[$param])) {
                    $operator = $val['operator'];
                    $field = $val['field'];
                    $get = trim($_POST[$param]);
                    $_GET[$param] = $get;
                    if ('start_time' == $param) {
                        $get = strtotime($get);
                    } else if ('end_time' == $param) {
                        $get .= " 23:59:59";
                        $get = strtotime($get);
                    }
                    if ($operator == "like") {
                        $get = "%$get%";
                    }
                    array_push($where_ands, "$field $operator '$get'");
                }
            }
        } else {
            foreach ($fields as $param => $val) {
                if (isset($_GET[$param]) && !empty($_GET[$param])) {
                    $operator = $val['operator'];
                    $field = $val['field'];
                    $get = trim($_GET[$param]);
                    if ('start_time' == $param) {
                        $get = strtotime($get);
                    } else if ('end_time' == $param) {
                        $get .= " 23:59:59";
                        $get = strtotime($get);
                    }
                    if ($operator == "like") {
                        $get = "%$get%";
                    }
                    array_push($where_ands, "$field $operator '$get'");
                }
            }
        }
        $where = join(" AND ", $where_ands);
        $count = $this->model->where($where)->alias('p')->field("p.*, g.name gamename")->join(
            "left join ".C('DB_PREFIX')."game g ON p.app_id=g.id"
        )->count();
        $page = $this->page($count, 20);
        $posts = $this->model->alias('p')->field("p.*, g.name gamename")->join(
            "left join ".C('DB_PREFIX')."game g ON p.app_id=g.id"
        )->where($where)->limit(
            $page->firstRow.','.$page->listRows
        )->order("p.listorder desc,p.create_time desc")->select();

        $this->assign("Page", $page->show('Admin'));
        $this->assign("formget", $_GET);
        $this->assign("posts", $posts);
        $this->display();
    }

    function add() {
        $this->_game();
        $this->display();
    }

    function add_post() {
        if (IS_POST) {
            $_POST['post']['create_time'] = time();
            $_POST['post']['update_time'] = time();
            $_POST['post']['status'] = 0;
            $_POST['post']['author'] = get_current_admin_id();
            $page = I("post.post");
            $page['content'] = htmlspecialchars_decode($page['content']);
            $result = $this->model->add($page);
            if ($result) {
                $this->success("添加成功！");
            } else {
                $this->error("添加失败！");
            }
        }
    }

    public function edit() {
        $this->_game();
        $id = intval(I("get.id"));
        $post = $this->model->where("id=$id")->find();
        $this->assign("post", $post);
        $this->assign("author", "1");
        $this->display();
    }

    public function edit_post() {
        if (IS_POST) {
            $_POST['post']['update_time'] = time();
            $page = I("post.post");
            $page['content'] = htmlspecialchars_decode($page['content']);
            $result = $this->model->save($page);
            if ($result !== false) {
                $this->success("保存成功！");
            } else {
                $this->error("保存失败！");
            }
        }
    }

    function delete() {
        if (isset($_POST['ids'])) {
            $ids = implode(",", $_POST['ids']);
            $data = array(
                "status" => "0"
            );
            if ($this->model->where("id in ($ids)")->delete()) {
                $this->success("删除成功！");
            } else {
                $this->error("删除失败！");
            }
        } else {
            if (isset($_GET['id'])) {
                $id = I("get.id/d");
                $data = array(
                    "id"          => $id,
                    "status" => "0"
                );
                if ($this->model->where("id in ($id)")->delete()) {
                    $this->success("删除成功！");
                } else {
                    $this->error("删除失败！");
                }
            }
        }
    }

    function update_status() {
        if (isset($_POST['ids'])) {
            $ids = implode(",", $_POST['ids']);
            $status = $_POST['status'];
            $app_id = $_POST['app_id'];
            if($status == 1){
                $main_status=0;

            }else{
                 $main_status=1;
            }

            $main_data = array(
                "status" =>  $main_status,
                "update_time" => time()
            );

            $data = array(
                "status" =>  0,
            
            );
            if ($this->model->where("id in ($ids)")->save($main_data)  ) {
                $this->model->where("id not in ($ids) and app_id = $app_id")->save($data);
                $this->success("修改成功！");
            } else {
                $this->error("修改失败！");
            }
        } else {
            if (isset($_GET['id'])) {
                $id = I("get.id/d");
                $status = I("get.status/d");
                 $app_id = I("get.app_id/d");
                 if($status == 1){
                    $main_status=0;
                 }else{
                    $main_status=1;
                }

                $main_data = array(
                    "id"          => $id,
                    "status" =>  $main_status,
                  "update_time" => time()
                );
                 $data = array(
                    "status" =>  0,
                );
                if ($this->model->save($main_data) ) {
                     $this->model->where("id != $id and app_id = $app_id")->save($data);
                    $this->success("修改成功！");
                } else {
                    $this->error("修改失败！");
                }
            }
        }
    }

    function restore() {
        if (isset($_GET['id'])) {
            $id = intval(I("get.id"));
            $data = array(
                "id"          => $id,
                "status" => "1"
            );
            if ($this->model->save($data)) {
                $this->success("还原成功！");
            } else {
                $this->error("还原失败！");
            }
        }
    }

    function clean() {
        if (isset($_POST['ids'])) {
            $ids = implode(",", $_POST['ids']);
            if ($this->model->where("id in ($ids)")->delete() !== false) {
                $this->success("删除成功！");
            } else {
                $this->error("删除失败！");
            }
        }
        if (isset($_GET['id'])) {
            $id = intval(I("get.id"));
            if ($this->model->delete($id) !== false) {
                $this->success("删除成功！");
            } else {
                $this->error("删除失败！");
            }
        }
    }

}