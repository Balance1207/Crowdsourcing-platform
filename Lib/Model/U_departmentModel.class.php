<?php
/**
 * Powerd by ArPHP.
 *
 * Model.
 *
 * Date: 2017/2/14
 *
 * @author wdn
 */
class U_departmentModel extends ArModel {
    static public function model($class = __CLASS__)
    {
        return parent::model($class);

    }

    // 表名
    public $tableName = 'u_department';

    //部门列表
    public function deptList() {
        $dataNum = U_departmentModel::model()->getDb()->where(array('pid' => 0))->count();
        $page = new Page($dataNum,10);
        $data = U_departmentModel::model()->getDb()->limit($page->limit())->where(array('pid' => 0))->queryAll();
        $pageHtml = $page->show();
        return array('data' => $data,'pageHtml' => $pageHtml);
    }

    //获取子部门
    public function secDept($parent) {
        $data = U_departmentModel::model()->getDb()->where(array('pid' => $parent['id']))->queryAll();
        foreach ($data as $key => $value) {
            $data[$key]['pname'] = $parent['d_name'];
        }
        return $data;
    }

    //回显
    public function show($id) {
        $data = U_departmentModel::model()->getDb()->where(array('id' => $id))->queryRow();
        return $data;
    }

    //修改时的下拉列表
    public function selectName($id) {
        $data = U_departmentModel::model()->getDb()->where('id !=' .$id)->queryAll();
         foreach ($data as $key => $value) {
            if ($value['pid'] == 0) {
                $result[$key] = $value;
            }
        }
        return $result;

    }

    //修改
    public function update($post = array()) {
        $result = U_departmentModel::model()->getDb()->where(array('id' => $post['id']))->update(
            array(
                'id' => $post['id'],
                'd_name' => $post['d_name'],
                'd_number' => $post['d_number'],
                'd_address' => $post['d_address'],
                'pid' => $post['pid'],
                'sort' => $post['sort'],
                )
        );
        if (isset($result)) {
            return true;
        }
    }

    //删除
    public function del($id) {
        //查询是否有子部门
        $ids = U_departmentModel::model()->getDb()->select('id')->where(array('pid' => $id))->queryAll();
        //有子部门
        if (!empty($ids)) {
            $ids[] = $id;
            foreach ($ids as $vol) {            
                //判断部门下是否有职位
                $data = U_department_jobsModel::model()->jobDept($vol);
                if (empty($data)) {
                    //部门下没有职位可删除部门
                    $result = U_departmentModel::model()->getDb()->where(array('id' => $vol))->delete();
                } else {
                    //部门下有职位不能删除部门
                    $result = false;
                }

            }
        } else {//没有子部门
            $data = U_department_jobsModel::model()->jobDept($id);
            //判断部门下是否有职位
            if (empty($data)) {
                $result = U_departmentModel::model()->getDb()->where(array('id' => $id))->delete();
            } else {
                $result = false;
            }
        }

        return $result;
    }

    //增加
    public function add($post) {
        $result = U_departmentModel::model()->getDb()->insert(array(
                "d_name" => $post['d_name'],
                'd_number' => $post['d_number'],
                'd_address' => $post['d_address'],
                "pid" => $post['pid'],
                "sort" => $post['sort'],
                ));
        return $result;
    }

    //获取所有顶级部门
    public function topDept() {
        $data = U_departmentModel::model()->getDb()->where(array('pid' => 0))->queryAll();
        return $data;
    }

    //获取所有部门
    public function selectAll() {
        $row = U_departmentModel::model()->getDb()->queryAll();
        foreach ($row as $key => $value) {
            if ($value['pid'] == 0) {
                $row[$key]['pname'] = '顶级部门';
            } else {
                $info = U_departmentModel::model()->getDb()->where(array('id' => $value['pid']))->queryRow();
                $row[$key]['pname'] = $info['d_name'];
            }
        }
        return $row;

    }


}