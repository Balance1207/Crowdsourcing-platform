<?php
/**
 * Powerd by ArPHP.
 *
 * Model.
 *
 * @author ycassnr <ycassnr@gmail.com>
 */

/**
 * Default Model of webapp.
 */
class U_department_jobsModel extends ArModel
{

    static public function model($class = __CLASS__)
    {
        return parent::model($class);

    }

    // 表名
    public $tableName = 'u_department_jobs';

    //查询部门下的职位
    public function jobDept($id){
        $data = U_department_jobsModel::model()->getDb()->where(array('j_did' => $id))->queryAll();
        return $data;
    }

    // 获取详细信息
    public function getDetailInfo(array $bundles)
    {
        // 递归遍历所有产品信息
        if (arComp('validator.validator')->checkMutiArray($bundles)) :
            foreach ($bundles as &$bundle) :
                $bundle = $this->getDetailInfo($bundle);
            endforeach;
        else :
            $bundle = $bundles;
            /**
             * to do
             */

            // 查询权限
            $bundle['lids'] = JobAuthorityModel::model()
                ->getDb()
                ->where(array('jid' => $bundle['jid']))
                ->queryColumn('lids');

             // 权限id
            if ($bundle['lids']) :
                $lids = explode(',', $bundle['lids']);
                // 返回键值为action二维数组
                $auths = AuthListModel::model()->getDb()->where(array('lid' => $lids))->queryAll('action');
            else :
                $auths = array();
            endif;
            $bundle['auths'] = $auths;
            return $bundle;
        endif;

        return $bundles;

    }

    //职位列表
    public function jobList(){
        $dataNum = U_department_jobsModel::model()->getDb()->count();
        $page = new Page($dataNum,10);
        $data = U_department_jobsModel::model()->getDb()->limit($page->limit())->queryAll();
        $pageHtml = $page->show();
        // return $data;
        return array('data' => $data,'pageHtml' => $pageHtml);
    }

    //获取职位所属的部门名称
    public function dName($value){
        $info = U_departmentModel::model()->getDb()->select('d_name')->where(array('id' => $value['j_did']))->queryRow();
        return $info;
    }

    //回显
    public function show($id){
        $data = U_department_jobsModel::model()->getDb()->where(array('id' => $id))->queryRow();
        return $data;
    }

    //修改时的下拉列表
    public function selectDept(){
        $data = U_departmentModel::model()->getDb()->queryAll();
        return $data;
    }

    //修改
    public function update($post){
        $result = U_department_jobsModel::model()->getDb()->where(array('id' => $post['id']))->update(array(
            'id' => $post['id'],
            'j_name' => $post['j_name'],
            'j_did' => $post['j_did'],
            'i_id' => $post['i_id'],
            ));
         if(isset($result)) {
            return true;
        }
    }

    //删除
    public function del($id){
        $result = U_department_jobsModel::model()->getDb()->where(array('id' => $id))->delete();
        return $result;
    }

    //增加
    public function add($post){
        $result = U_department_jobsModel::model()->getDb()->insert(array(
            'j_name' => $post['j_name'],
            'j_did' => $post['j_did'],
            'i_id' => $post['i_id'],
            ));
        return $result;
    }

     //获取所有职位
    public function selectAll() {
        $row = U_department_jobsModel::model()->getDb()->queryAll();
        foreach ($row as $key => $value) {
            $info = U_departmentModel::model()->getDb()->where(array('id' => $value['j_did']))->queryRow();
            $row[$key]['dname'] = $info['d_name'];
        }
        return $row;

    }


}