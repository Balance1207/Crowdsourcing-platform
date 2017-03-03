<?php
/**
 * Powerd by ArPHP.
 *
 * Date: 2017/2/16
 *
 * @author wdn
 */

class AuthorityController extends BaseController{
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

    //权限列表
    public function authListAction() {
        $result = U_authority_listModel::model()->authList();
        $this->assign(array('result' => $result));
        $this->assign(array('title' => '权限列表'));
        $this->display();
    }

    //修改权限
    public function updateAction() {
        //修改
        if (arPost()) {
            $post = arPost();
            $result = U_authority_listModel::model()->update($post); 
            if ($result) {
                $this->showJson(array('ret_msg' => '修改成功！', 'ret_code' => '1000', 'success' => "1", 'url' => 
                    arU('Authority/authList')));
            }
        } else {//回显
            $id = arGet('lid');
            $data = U_authority_listModel::model()->show($id);
            $info = U_authority_setModel::model()->allAuthGroupName();
            $this->assign(array('data' => $data));
            $this->assign(array('info' => $info));
            $this->assign(array('title' => '修改权限'));
            $this->display();
        }
       
    }

    //删除权限
    public function delAction(){
        $id = arGet('lid');
        $result = U_authority_listModel::model()->del($id);
        if ($result) {
             $this->showJson(array('ret_msg' => '删除成功！', 'ret_code' => '1000', 'success' => "1", 'url' => 
                    arU('Authority/authList')));
        }

    }

    //添加权限
    public function addAction(){
        if (arPost()) {
            $post = arPost();
            $result = U_authority_listModel::model()->add($post);
            if($result){
                $this->showJson(array('ret_msg' => '添加成功！', 'ret_code' => '1000', 'success' => "1", 'url' => 
                    arU('Authority/authList')));
            }
        } else {
            $info = U_authority_setModel::model()->allAuthGroupName();
            $this->assign(array('info' => $info));
            $this->assign(array('title' => '添加权限'));
            $this->display();
        }
       
       
    }

    

}
