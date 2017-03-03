<?php
/**
 * Powerd by ArPHP.
 *
 * Date: 2017/2/14
 *
 * @author wdn
 */

class DeptController extends BaseController{
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

    // 部门列表
    public function deptListAction() {
        $result = U_departmentModel::model()->deptList();
        foreach ($result['data'] as $key => $value) {
            $info = U_departmentModel::model()->secDept($value);
            $this->assign(array('info' => $info));
        }

        $this->assign(array('result' => $result));
        $this->assign(array('title' => '部门列表'));
        $this->display();

    }

    //添加部门
    public function addAction(){
         if (arPost()) {
            $post = arPost();
            $result = U_departmentModel::model()->add($post);
            if ($result) {
                $this->showJson(array('ret_msg' => '添加成功！', 'ret_code' => '1000', 'success' => "1", 'url' => arU('Dept/deptList')));
            }

        } else {
            $data = U_departmentModel::model()->topDept();
            $this->assign(array('data'=>$data));
            $this->assign(array('title' => '添加分类'));
            $this->display();
        }
    }

    //修改部门
    public function updateAction(){
        if (arPost()) {
            $post = arPost();
            $result = U_departmentModel::model()->update($post);
            if($result === true){
                $this->showJson(array('ret_msg' => '修改成功！', 'ret_code' => '1000', 'success' => "1", 'url' =>
                    arU('Dept/deptList')));
            }
        }else{
            $id = arGet('id');
            $data = U_departmentModel::model()->show($id);
            $result = U_departmentModel::model()->selectName($id);

            $this->assign(array('data' => $data));
            $this->assign(array('result' => $result));
            $this->assign(array('title' => '修改部门'));
            $this->display();
        }
    }

    // 删除部门
    public function delAction() {
        $id = arRequest('id');
        if (is_array($id)) {
            foreach ($id as $vol) {
                $result = U_departmentModel::model()->del($vol);
            }
        } else {
            $result = U_departmentModel::model()->del($id);
        }

        if ($result) {
            $this->showJson(array('ret_msg' => '删除成功！', 'ret_code' => '1000', 'success' => "1", 'url' => arU('Dept/deptList')));
        } else {
            var_dump('部门下有职位不能删除');

        }

    }

}
