<?php
/**
 * Powerd by ArPHP.
 *
 * Date: 2017/2/15
 *
 * @author wdn
 */

class JobController extends BaseController{
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

    // 职位列表
    public function jobListAction() {
        $result = U_department_jobsModel::model()->jobList();
        foreach ($result['data'] as $key => $value) {
            $info = U_department_jobsModel::model()->dName($value);
            $result['data'][$key]['dName'] = $info['d_name'];
        }

        $this->assign(array('result' => $result));
        $this->assign(array('title' => '职位列表'));
        $this->display();

    }

    //添加职位
    public function addAction(){
        if (arPost()) {
            $post = arPost();
            $result = U_department_jobsModel::model()->add($post);
            if ($result) {
                $this->showJson(array('ret_msg' => '添加成功！', 'ret_code' => '1000', 'success' => "1", 'url' => 
                    arU('Job/jobList')));
            }
        }else{
            $data = U_department_jobsModel::model()->selectDept();
            $this->assign(array('data' => $data));
            $this->assign(array('title' => '添加职位'));
            $this->display();
        }
        
    }

    //修改职位
    public function updateAction(){
        if (arPost()) {
            $post = arPost();
            $result = U_department_jobsModel::model()->update($post);
            if ($result === true) {
                $this->showJson(array('ret_msg' => '修改成功！', 'ret_code' => '1000', 'success' => "1", 'url' => 
                    arU('Job/jobList')));
            }
        }else{
            $id = arGet('id');
            $data = U_department_jobsModel::model()->show($id);
            $deptNames = U_department_jobsModel::model()->selectDept();
            $this->assign(array('data' => $data));
            $this->assign(array('deptNames' => $deptNames));
            $this->assign(array('title' => '修改职位'));
            $this->display();
        }
       
    }

    //删除职位
    public function delAction(){
        $ids = arRequest();
        if (is_array($ids)) {
            foreach ($ids as $key => $id) {
                $result = U_department_jobsModel::model()->del($id);
            }
        }else{
            $result = U_department_jobsModel::model()->del($ids);
        }
        if ($result) {
            $this->showJson(array('ret_msg' => '删除成功！', 'ret_code' => '1000', 'success' => "1", 'url' => arU('Job/jobList')));
        }
    }


    //权限列表
    public function showAuthAction(){
        //部门id
        $jid = arGet('id');
        $result = U_authority_listModel::model()->authList();
        //是否已经存在此部门id对应的权限
        $datas = U_job_authorityModel::model()->findAuth($jid);
        if ($datas) {
            //存在
            $info = explode(',',$datas['lids']);
            $this->assign(array('info' => $info));
        }
        $this->assign(array('result' => $result));
        $this->assign(array('jid' => $jid));
        $this->assign(array('title' => '分配权限'));
        $this->display();
    }

    //添加权限
    public function addAuthAction(){
        $lid = arRequest('lid');
        $lids = implode(',',$lid);
        $jid = arRequest('jid');
        $data = array('jid' => $jid,'lids' => $lids);
        $datas = U_job_authorityModel::model()->findAuth($jid);
        if ($datas) {
            //存在
            $data = U_job_authorityModel::model()->update($data);
            if($data === true){
            $this->showJson(array('ret_msg' => '修改权限成功！', 'ret_code' => '1000', 'success' => "1", 'url' => arU('Job/jobList')));
            }
        }else{
            $data = U_job_authorityModel::model()->add($data);
             if($data){
            $this->showJson(array('ret_msg' => '分配权限成功！', 'ret_code' => '1000', 'success' => "1", 'url' => arU('Job/jobList')));
            }
        }
    }
  
}