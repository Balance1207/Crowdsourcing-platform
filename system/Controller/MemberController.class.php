<?php

/**
 * Created by PhpStorm.
 * User: HP
 * Date: 2016/12/27
 * Time: 10:05
 */
class MemberController extends BaseController
{
    // 初始化方法
    public function init()
    {
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

    // 用户列表
    public function memberListAction()
    {
        // 搜索条件
        $keywordNickname = arGet('keywordNickname');
        $keywordTel = arGet('keywordTel');

        // 查询条件
        $condition = array();
        if ($keywordNickname != '') {
            $condition[] = array('nickname like' => '%' . $keywordNickname . '%');
        }
        if ($keywordTel != '') {
            $condition[] = array('tel like' => '%' . $keywordTel . '%');
        }

        $result = MemberModel::model()->memberList($condition);

        $this->assign(array('cssInsertBundles' => array('page')));
        $this->assign(array('rows' => $result['rows']));
        $this->assign(array('pageHtml' => $result['pageHtml']));
        $this->assign(array('totalCount' => $result['totalCount']));
        $this->assign(array('keywordTel' => $keywordTel, 'keywordNickname' => $keywordNickname));

        // 权限级别
        $level = array(0 => "未分配", 1 => "超级管理员", 2 => "管理员", 3 => "普通人员");
        $this->assign(array('level' => $level));

        $this->assign(array('title' => '用户列表'));
        $this->display();

    }


    // 删除用户
    public function deleteMemberAction()
    {
        $id = arRequest();

        // 批量删除
        if (is_array($id)) {
            foreach ($id as $value) {
                $result = MemberModel::model()->deleteMember($value);
            }
        } else {
            $result = MemberModel::model()->deleteMember($id);
        }

        if ($result) {
            $this->showJson(array('ret_msg' => '删除成功！', 'ret_code' => '1000', 'success' => "1", 'url' => arU('Member/memberList')));
        }

    }


    // 添加用户
    public function addMemberAction()
    {
        $param = arPost();

        if (arPost()) {
            // 编辑回显
            if (arPost('id')) {
                $result = MemberModel::model()->updateMember($param);

                if ($result) {
                    $this->showJson(array('ret_msg' => '修改用户成功！', 'ret_code' => '1000', 'success' => "1", 'url' => arU('Member/memberList')));
                }
            } else {
                $result = MemberModel::model()->addMember($param);

                if ($result) {
                    $this->showJson(array('ret_msg' => '添加用户成功！', 'ret_code' => '1000', 'success' => "1", 'url' => arU('Member/memberList')));
                }
            }
        } else {
            if (arGet('id')) {
                // 编辑回显
                $result = MemberModel::model()->checkMember();
                $preList = MemberModel::model()->preShowList(arGet('id'));

                $this->assign(array('preList' => $preList));
                $this->assign(array('title' => '编辑用户'));
                $this->assign(array('departments' => $result['departments'], 'departmentJobs' => $result['departmentJobs']));

                $this->assign(array('cssInsertBundles' => array('bootstrap.min', 'bootstrap-select')));
                $this->assign(array('jsInsertBundles' => array('bootstrap.min', 'bootstrap-select')));

                $this->display();
            } else {
                $this->assign(array('title' => '添加用户'));
                // 显示部门和职位
                $result = MemberModel::model()->checkMember();
                $this->assign(array('departments' => $result['departments'], 'departmentJobs' => $result['departmentJobs']));

                $this->assign(array('cssInsertBundles' => array('bootstrap.min', 'bootstrap-select')));
                $this->assign(array('jsInsertBundles' => array('bootstrap.min', 'bootstrap-select')));

                $this->display();
            }
        }

    }

    // 修改密码
    public function changePasswordAction()
    {
        $param = arPost();

        if (arPost()) {
            if (arPost('id')) {
                $result = MemberModel::model()->changePassword($param);
                if ($result) {
                    $this->showJson(array('ret_msg' => '修改密码成功！', 'ret_code' => '1000', 'success' => "1", 'url' => arU('Member/memberList')));
                }
            } else {
                $this->showJsonError('修改密码错误!', 1001);
            }
        } else {
            if (arGet('id')) {
                // 编辑回显
                $preList = MemberModel::model()->preShowList(arGet('id'));
                $this->assign(array('preList' => $preList));
                $this->assign(array('title' => '修改密码'));
                $this->display();
            } else {
                $this->assign(array('title' => '修改密码'));
                $this->display();
            }
        }

    }

    //部门列表
    public function selectDeptAction(){
        $id = arRequest('id');
        //获取所有部门
        $result = U_departmentModel::model()->selectAll();
        //用户是否已经存在部门
        $datas = U_usersModel::model()->getDept($id);
        if ($datas) {
            //存在
            $info = explode(',',$datas['department']);
            $this->assign(array('info' => $info));
        }

        $this->assign(array('result' => $result));
        $this->assign(array('id' => $id));
        $this->assign(array('title' => '部门列表'));
        $this->display();

    }

    //保存部门
    public function saveDeptAction(){
        $id = arRequest('id');
        $ids = arRequest('ids');
        $idss = implode(',',$ids);
        $datas = array('id' => $id,'idss' => $idss);
        $result = U_usersModel::model()->getInfo($id);
        if($result){
            //存在
            $data = U_usersModel::model()->update($datas);
            if($data){
                $this->showJson('修改成功！');
            }
        }

    }

    //职位列表
    public function selectJobAction(){
        $id = arRequest('id');
        //获取所有职位
        $result = U_department_jobsModel::model()->selectAll();
        //用户是否已经存在职位
        $datas = U_usersModel::model()->getJob($id);

        if ($datas) {
            //存在
            $info = explode(',',$datas['job']);
            $this->assign(array('info' => $info));
        }

        $this->assign(array('result' => $result));
        $this->assign(array('id' => $id));
        $this->assign(array('title' => '职位列表'));
        $this->display();

    }
    //保存职位
    public function saveJobAction(){
        $id = arRequest('id');
        $ids = arRequest('ids');
        $idss = implode(',',$ids);
        $datas = array('id' => $id,'idss' => $idss);
        $result = U_usersModel::model()->getInfo($id);

        if($result){
            //存在
            $data = U_usersModel::model()->updateJob($datas);
            if($data){
            $this->showJson('修改成功！');
            }
        }

    }

}