<?php
/**
 * Powerd by ArPHP.
 *
 * Date: 2017/2/17
 *
 * @author wdn
 */

class AuthGroupController extends BaseController {
    // 初始化方法
    public function init() {
        parent::init();

        // 调用layer msg cart插件
        arSeg(array(
                'loader' => array(
                    'plugin' => '',
                    'this' => $this
                )
            )
        );

    }

    //权限组列表
    public function authGroupListAction() {
        $result = U_authority_setModel::model()->authGroupList();

        foreach ($result['data'] as $key => $value) {
            //判断是否有子权限组
            $data = U_authority_setModel::model()->secAuthGroup($value);
            $this->assign(array('data' => $data));
        }
        $this->assign(array('result' => $result));
        $this->assign(array('title' => '权限列表'));
        $this->display();
    }

    //修改权限组
    public function updateAction() {
        //修改
        if (arPost()) {
            $post = arPost();
            $result = U_authority_setModel::model()->update($post); 
            if ($result) {
                $this->showJson(array('ret_msg' => '修改成功！', 'ret_code' => '1000', 'success' => "1", 'url' => 
                    arU('AuthGroup/authGroupList')));
            }
        } else {//回显
            $id = arGet('sid');
            $data = U_authority_setModel::model()->show($id);
            $info = U_authority_setModel::model()->selectName($id);
            $this->assign(array('data' => $data));
            $this->assign(array('info' => $info));
            $this->assign(array('title' => '修改权限组'));
            $this->display();
        }
       
    }

    // //删除权限组
    // public function delAction() {
    //     $id = arGet('sid');
    //     $result = U_authority_setModel::model()->del($id);
    //     if ($result) {
    //          $this->showJson(array('ret_msg' => '删除成功！', 'ret_code' => '1000', 'success' => "1", 'url' => 
    //                 arU('AuthGroup/authGroupList')));
    //     }

    // }

    //删除权限组
    public function delAction() {
        $id = arGet('sid');
        $result = U_authority_setModel::model()->del($id);
        if ($result) {
             $this->showJson(array('ret_msg' => '删除成功！', 'ret_code' => '1000', 'success' => "1", 'url' => 
                    arU('AuthGroup/authGroupList')));
        } else {
            var_dump('权限组下有权限不能删除');

        }

    }

    //添加权限组
    public function addAction() {
        if (arPost()) {
            $post = arPost();
            $result = U_authority_setModel::model()->add($post);
            if ($result) {
                $this->showJson(array('ret_msg' => '添加成功！', 'ret_code' => '1000', 'success' => "1", 'url' => 
                    arU('AuthGroup/authGroupList')));
            }
        } else {
            if (arGet('psid')) {
                $psid = arGet('psid');
                $this->assign(array('psid' => $psid));
            }
            $info = U_authority_setModel::model()->topAuthGroup();
            $this->assign(array('info' => $info));
            
            $this->assign(array('title' => '添加权限组'));
            $this->display();
        }
       
       
    }    

}
