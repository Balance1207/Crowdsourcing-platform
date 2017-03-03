<?php
/**
 * Powerd by ArPHP.
 *
 * Date: 2017/2/14
 *
 * @author wdn
 */

class CategoryController extends BaseController{
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

    // 分类列表
    public function categoryListAction() {
        $catename = arGet('catename');
        $condition = array();
        if ($catename != '') {
            $condition[] = array('cate_name like' => '%' . $catename . '%');
        }
        
        $result = S_admin_categoryModel::model()->categoryList($condition); 
        if ($catename == '') {
            foreach ($result['rows'] as $key => $value) {
                $data = S_admin_categoryModel::model()->secCategory($value);
                $this->assign(array('data' => $data));
            }
        }
        
        $this->assign(array('cssInsertBundles' => array('page')));
        $this->assign(array('result' => $result));
        $this->assign(array('catename' => $catename));

        $this->assign(array('title' => '分类列表'));
        $this->display();

    }

    //修改分类
    public function updateAction() {
        if(arPost()) {
            $data = arPost();
            $result = S_admin_categoryModel::model()->update($data);
            if ($result === true) {
                $this->showJson(array('ret_msg' => '修改成功！', 'ret_code' => '1000', 'success' => "1", 'url' => 
                    arU('Category/categoryList')));
            }
        } else {
            $id = arGet('id');
            $result = S_admin_categoryModel::model()->show($id);
            $data = S_admin_categoryModel::model()->selectName($id);

            $this->assign(array('result'=>$result));
            $this->assign(array('data'=>$data));
            $this->assign(array('title' => '修改分类')); 
            $this->display();

        }
    }

    //删除分类
    public function deleteAction() {
        $id = arRequest();
        if(is_array($id)) {
            foreach ($id as $vol) {
                $result = S_admin_categoryModel::model()->del($vol);
            }
        } else {
            $result = S_admin_categoryModel::model()->del($id);    
        }
        if ($result) {
            $this->showJson(array('ret_msg' => '删除成功！', 'ret_code' => '1000', 'success' => "1", 'url' => arU('Category/categoryList')));
        }

    }

    //添加分类
    public function addAction() {
        if (arPost()) {
            $post = arPost();
            $result = S_admin_categoryModel::model()->add($post);
            if ($result) {
                $this->showJson(array('ret_msg' => '添加成功！', 'ret_code' => '1000', 'success' => "1", 'url' => arU('Category/categoryList')));
            }

        } else {
            $data = S_admin_categoryModel::model()->topCategory();
            $this->assign(array('data'=>$data));
            $this->assign(array('title' => '添加分类'));  
            $this->display();
        }
        
    }

}