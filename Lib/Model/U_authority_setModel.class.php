<?php
/**
 * Powerd by ArPHP.
 *
 * Model.
 *
 * Date: 2017/2/16
 *
 * @author wdn
 */

class U_authority_setModel extends ArModel {
    static public function model($class = __CLASS__)
    {
        return parent::model($class);

    }

    // 表名
    public $tableName = 'u_authority_set';

    //获取权限所属权限组
    public function authGroupName($value = array()) {
        $info = U_authority_setModel::model()->getDb()->where(array('sid' => $value))->queryRow();
        return $info;
    }

    //所有权限组
    public function allAuthGroupName() {
        $data = U_authority_setModel::model()->getDb()->queryAll();
        return $data;
    }

    //权限组列表
    public function authGroupList() {
        $dataNum = U_authority_setModel::model()->getDb()->where(array('psid' => 0))->count();
        $page = new Page($dataNum,10);
        $data = U_authority_setModel::model()->getDb()->limit($page->limit())->where(array('psid' => 0))->queryAll();
        $pageHtml = $page->show();
        return array('pageHtml' => $pageHtml,'data' => $data);

    }

    //子权限组
    public function secAuthGroup($value) {
        $data = U_authority_setModel::model()->getDb()->where(array('psid' => $value['sid']))->queryAll();
        return $data;
    }

    //回显
    public function show($id) {
        $data = U_authority_setModel::model()->getDb()->where(array('sid' => $id))->queryRow();
        return $data;
    }

    // 修改时的下拉列表
    public function selectName($id) {
        $data = U_authority_setModel::model()->getDb()->where('sid !=' .$id)->queryAll();
         foreach ($data as $key => $value) {
            if($value['psid'] == 0){
                $result[$key] = $value;
            }   
        }
        return $result;

    }

    //修改权限组
    public function update($post) {
        $result = U_authority_setModel::model()->getDb()->where(array('sid' => $post['sid']))->update(array(
            'sid' => $post['sid'],
            'name' => $post['name'],
            'status' => $post['status'],
            'des' => $post['des'],
            'sorder' => $post['sorder'],
            'psid' => $post['psid'],
            ));
        if (isset($result)) {
            return true;
        }
    }

    // //删除权限组 
    // public function del($id) {
    //     $ids = U_authority_setModel::model()->getDb()->select('sid')->where(array('psid' => $id))->queryAll();
    //     if (is_array($ids)) {
    //         $ids[] = $id;
    //         foreach ($ids as $vol) {
    //             $result = U_authority_setModel::model()->getDb()->where(array('sid' => $vol))->delete();
    //         }
    //     } else {
    //          $result = U_authority_setModel::model()->getDb()->where(array('sid' => $id))->delete();
    //     }
    //     return $result;
    // }

      //删除权限组 
    public function del($id) {
        //权限组下是否子权限组
        $ids = U_authority_setModel::model()->getDb()->select('sid')->where(array('psid' => $id))->queryAll();
        if (is_array($ids)) {
            $ids[] = $id;
            foreach ($ids as $vol) {
                // $result = U_authority_setModel::model()->getDb()->where(array('sid' => $vol))->delete();
                //判断权限组下是否有权限
                $data = U_authority_listModel::model()->authInGroup($vol);
                if (empty($data)) {
                    //权限组下没有权限可删除权限组
                    $result = U_authority_setModel::model()->getDb()->where(array('sid' => $vol))->delete();
                } else {
                    //权限组下有权限不能删除权限组
                    $result = false;
                }
            }
        } else {
             // $result = U_authority_setModel::model()->getDb()->where(array('sid' => $id))->delete();
            $data = U_authority_listModel::model()->authInGroup($id);
            //判断部门下是否有职位
            if (empty($data)) {
                $result = U_authority_setModel::model()->getDb()->where(array('sid' => $id))->delete();
            } else {
                $result = false;
            }
        }
        return $result;
    }

    //所有顶级权限组
    public function topAuthGroup() {
        $data = U_authority_setModel::model()->getDb()->where(array('psid' => 0))->queryAll();
        return $data;
    }

    //添加权限组
    public function add($post) {
        $data = U_authority_setModel::model()->getDb()->insert(array(
            'name' => $post['name'],
            'status' => $post['status'],
            'des' => $post['des'],
            'sorder' => $post['sorder'],
            'psid' => $post['psid'],
        ));
        return $data;
    }

}
