<?php

/**
 * Created by PhpStorm.
 * User: HP
 * Date: 2016/12/23
 * Time: 10:37
 * 项目管理
 */
class ProductController extends BaseController
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


    // 项目列表
    public function showListAction()
    {
        // 搜索项目
        $keyword = arGet('keyword');
        // 查询条件
        $condition = array('i_name like ' => '%' . $keyword . '%'); // 注意%两边不能有空格

        $result = ProductModel::model()->showList($condition, arRequest('status', U_itemsModel::STATUS_APPROVED));

        // $handleOnline = [-1 => '未选择', 0 => '停用', 1 => '未停用', 2 => '已废弃'];
        $handleOnline = U_itemsModel::$STATUS_MAP;

        $this->assign(array('cssInsertBundles' => array('page')));
        $this->assign(array('rows' => $result['rows']));
        $this->assign(array('pageHtml' => $result['pageHtml']));
        $this->assign(array('totalCount' => $result['totalCount']));
        $this->assign(array('keyword' => $keyword));
        $this->assign(array('title' => '项目列表'));
        $this->assign(array('handleOnline' => $handleOnline));

        $this->display('showList');


    }

    // 发布项目
    public function addProductAction()
    {
        $param = arPost();

        if (arPost()) {
            // 编辑回显
            if (arPost('itemId')) {
                $result = ProductModel::model()->updateProduct($param);

                if ($result) {
                    $this->showJson(array('ret_msg' => '修改项目成功！', 'ret_code' => '1000', 'success' => "1", 'url' => arU('Product/showList', array('type' => 1))));
                }
            } else {
                $result = ProductModel::model()->addProduct($param);

                if ($result) {
                    $this->showJson(array('ret_msg' => '发布项目成功！', 'ret_code' => '1000', 'success' => "1", 'url' => arU('Product/showList', array('type' => 1))));
                }
            }
        } else {
            if (arGet('id')) {
                // 编辑回显
                $result = ProductModel::model()->checkProduct();
                $preList = ProductModel::model()->preShowList(arGet('id'));
                $this->assign(array('preList' => $preList));
                $this->assign(array('title' => '编辑项目'));
                $this->assign(array('statusType' => $result['statusType']));
                $allUsers = U_usersModel::model()->getDb()->queryAll();
                $this->assign(array('allusers' => $allUsers));

                $this->display();
            } else {
                $result = ProductModel::model()->checkProduct();

                $this->assign(array('title' => '添加项目'));
                $this->assign(array('statusType' => $result['statusType']));
                $this->assign(array('users' => $result['users']));

                $this->display();
            }
        }

    }


    // 删除项目
    public function deleteProductAction()
    {
        $id = arRequest('id');

        // 批量删除
        if (is_array($id)) {
            foreach ($id as $value) {
                $result = ProductModel::model()->deleteProduct($value);
            }
        } else {
            $result = ProductModel::model()->deleteProduct($id);
        }

        if ($result) {
            $this->showJson(array('ret_msg' => '删除成功！', 'ret_code' => '1000', 'success' => "1", 'url' => arU('Product/showList', array('type' => -1))));
        }

    }


    // 废弃项目移除功能，－1表废弃，1表上线
    public function removeProductAction()
    {
        $id = arRequest('id');
        $online = arRequest('online');

//        var_dump($online);
//        exit;

        // 批量移除
        if (is_array($id)) {
            foreach ($id as $value) {
                $result = ProductModel::model()->removeProduct($value, $online);
            }
        } else {
            $result = ProductModel::model()->removeProduct($id, $online);
        }

        if ($online === '0') {
            if ($result) {
                $this->showJson(array('ret_msg' => '还原成功！', 'ret_code' => '1000', 'success' => "1", 'url' => arU('Product/showList', array('type' => -1))));
            }
        } elseif ($online === '2') {
            if ($result) {
                $this->showJson(array('ret_msg' => '移除成功！', 'ret_code' => '1000', 'success' => "1", 'url' => arU('Product/showList', array('type' => 1))));
            }
        }

    }


    // 审核项目状态
    public function changeStatusAction()
    {
        $id = arRequest('id');
        $audit = arRequest('audit');
        $iName = arRequest('i_name');
        $publisher = arRequest('publisher');

        // 批量删除
        if (is_array($id)) {
            foreach ($id as $value) {
                $result = ProductModel::model()->changeStatus($value);
            }
        } elseif (isset($id) && isset($audit)) {
            $result = ProductModel::model()->changeStatus($id, $audit, $iName, $publisher);
        } else {
            $this->showJsonError('没有传入参数id,audit');
            return;
        }

        if ($result !== 'A') {
            $this->showJson(array('ret_msg' => '操作成功！', 'ret_code' => '1000', 'success' => "1", 'url' => arU('Product/showList', array('type' => 1))));
        }

    }


    // 提示未审核项目数
    public function uncheckedNumAction()
    {
        $num = ProductModel::model()->uncheckedNum();
        $this->showJson($num);
    }


    // 个人申请项目审核
    public function personalApplyAction()
    {
        // 搜索项目
        $keyword = arGet('keyword');
        $condition = array();

        // 查询条件
        if ($keyword != "") {
            $condition[] = array('i_id' => $keyword);
        }

        $result = ProductModel::model()->personalApply($condition, arRequest('type',U_item_task_applyModel::TYPE_APPLYED));
        $handleFlag = U_item_task_applyModel::$TYPE;

        $this->assign(array('cssInsertBundles' => array('page')));
        $this->assign(array('rows' => $result['rows']));
        $this->assign(array('pageHtml' => $result['pageHtml']));
        $this->assign(array('totalCount' => $result['totalCount']));
        $this->assign(array('keyword' => $keyword));
        $this->assign(array('title' => '个人申请'));
        $this->assign(array('handleFlag' => $handleFlag));

        $this->display();

    }


    // 个人申请项目消息回显
    public function personalApplyViewAction()
    {
        $id = arGet('id');

        $result = ProductModel::model()->personalApplyView($id);

        if ($result) {
            $this->showJson($result);
        }

    }


    // 个人项目审核提交
    public function personalApplySubmitAction()
    {
        $param = arPost();

        if (arPost()) {
            $result = ProductModel::model()->personalApplySubmit($param);
            if ($result) {
                $this->showJson(array('ret_msg' => '审核成功！', 'ret_code' => '1000', 'success' => "1", 'url' => arU('personalApply')));
            }
        }

    }


    // 删除个人项目申请列表
    public function deletePersonalApplyAction()
    {
        $id = arRequest('id');

        // 批量删除
        if (is_array($id)) {
            foreach ($id as $value) {
                $result = ProductModel::model()->deletePersonalApply($value);
            }
        } else {
            $result = ProductModel::model()->deletePersonalApply($id);
        }

        if ($result) {
            $this->showJson(array('ret_msg' => '删除成功！', 'ret_code' => '1000', 'success' => "1", 'url' => arU('Product/personalApply')));
        }

    }


    // 提示未审核个人申请项目数
    public function uncheckedPersonalApplyAction()
    {
        $num = ProductModel::model()->uncheckedPersonalApply();
        $this->showJson($num);
    }

}