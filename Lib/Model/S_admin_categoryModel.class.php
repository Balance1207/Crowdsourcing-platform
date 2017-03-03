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

class S_admin_categoryModel extends ArModel{
    static public function model($class = __CLASS__)
    {
        return parent::model($class);

    }

    // 表名
    public $tableName = 's_admin_category';

     // 分类列表
    public function categoryList($catename = array()) {   
        //搜索
        if(!empty($catename)) {
            $dataNum = S_admin_categoryModel::model()->getDb()->where($catename)->count();
            $page = new Page($dataNum, 10);
            $rows = S_admin_categoryModel::model()->getDb()
                ->limit($page->limit())
                ->where($catename)
                ->queryAll();

            foreach ($rows as $key => $value) {
                if ($value['pid'] > 0) {
                    $info = S_admin_categoryModel::model()->getDb()->where(array('id' => $value['pid']))->queryRow();
                    $rows[$key]['pname'] = $info['cate_name'];
                }
            }
           
        } else {//直接显示列表
            $dataNum = S_admin_categoryModel::model()->getDb()->where(array('pid' => 0))->count();
            $page = new Page($dataNum, 10);
            $rows = S_admin_categoryModel::model()->getDb()
                ->limit($page->limit())
                ->where(array('pid' => 0))
                ->queryAll();
            
        }
        $pageHtml = $page->show();
        return array('rows' => $rows,'pageHtml' => $pageHtml);

    }

    //获取所有顶级分类
    public function topCategory() {
    	$data = S_admin_categoryModel::model()->getDb()->where(array('pid' => 0))->queryAll();
    	return $data;
    }

    //获取子分类
    public function secCategory($parent) {
    	$data = S_admin_categoryModel::model()->getDb()->where(array('pid' => $parent['id']))->queryAll();
    	foreach ($data as $key => $value) {
    		$data[$key]['pname'] = $parent['cate_name'];
    	}
    	return $data;

    }

    //修改回显
    public function show($id) {
        $data = S_admin_categoryModel::model()->getDb()->where(array('id' => $id))->queryRow();
        return $data;

    }

     //修改时的下拉列表
    public function selectName($id){
        $data = S_admin_categoryModel::model()->getDb()->where('id !=' .$id)->queryAll();
         foreach ($data as $key => $value) {
            if($value['pid'] == 0){
                $result[$key] = $value;
            }   
        }
        return $result;

    }

    //修改
    public function update($data = array()) {
        $result = S_admin_categoryModel::model()->getDb()->where(array('id' => $data['id']))->update(
            array(
                    'id' => $data['id'],
                    'cate_name' => $data['cate_name'],
                    'pid' => $data['pid'],
                    'sort' => $data['sort'],
                    'alias' => $data['alias'],
                )
        ); 
        if(isset($result)) {
            return true;
        }

    }

    //删除分类
    public function del($id) {
    	$ids = S_admin_categoryModel::model()->getDb()->select('id')->where(array('pid' => $id))->queryAll();
    	if (is_array($ids)) {
    		$ids[] = $id;
    		foreach ($ids as $vol) {
    			$result = S_admin_categoryModel::model()->getDb()->where(array('id' => $vol))->delete();
    		}
    	} else {
    		 $result = S_admin_categoryModel::model()->getDb()->where(array('id' => $id))->delete();
    	}
    	return $result;
    }

    //增加分类
    public function add($post) {
    	$result = S_admin_categoryModel::model()->getDb()->insert(array(
                "cate_name" => $post['cate_name'],
                "pid" => $post['pid'],
                "sort" => $post['sort'],
                "alias" => $post['alias'],
                ));
    	return $result;
    }

}