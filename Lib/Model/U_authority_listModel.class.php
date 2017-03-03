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

class U_authority_listModel extends ArModel{
    static public function model($class = __CLASS__)
    {
        return parent::model($class);

    }

    // 表名
    public $tableName = 'u_authority_list';

    //权限列表
    public function authList() {
    	$dataNum = U_authority_listModel::model()->getDb()->count();
    	$page = new Page($dataNum,10);
    	$data = U_authority_listModel::model()->getDb()->limit($page->limit())->queryAll();
    	foreach ($data as $key => $value) {
    		//查询权限所在权限组
    		$info = U_authority_setModel::model()->authGroupName($value['sid']);
    		$data[$key]['pname'] = $info['name'];
    		$data[$key]['des'] = $info['des'];	
    	}
    	$pageHtml = $page->show();
    	
    	return array('pageHtml' => $pageHtml,'data' => $data);
    }

    

    //修改时回显
    public function show($id){
    	$data = U_authority_listModel::model()->getDb()->where(array('lid' => $id))->queryRow();
    	return $data;
    }

    //修改权限
    public function update($post){
    	$result = U_authority_listModel::model()->getDb()->where(array('lid' => $post['lid']))->update(array(
    			'lid' => $post['lid'],
    			'name' => $post['name'],
    			'action' => $post['action'],
    			'sorder' => $post['sorder'],
    			'sid' => $post['sid'],
    		));

    	if (isset($result)) {
    		return true;
    	}

    }

    //删除权限
    public function del($id){
    	$result = U_authority_listModel::model()->getDb()->where(array('lid' => $id))->delete();
    	return $result;
    }

    //权限组下的权限
    public function authInGroup($sid){
        $data = U_authority_listModel::model()->getDb()->where(array('sid' => $sid))->queryAll();
        return $data;

    }

    //添加权限
    public function add($post){
    	$result = U_authority_listModel::model()->getDb()->insert(array(
    		'name' => $post['name'],
    		'action' => $post['action'],
    		'sorder' => $post['sorder'],
    		'sid' => $post['sid'],
    		));
    	return $result;
    }
    

}