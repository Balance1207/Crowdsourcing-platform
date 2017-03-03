<?php
// 项目类控制器端
class ItemController extends ArController
{
    // 初始化方法
    public function init()
    {

    }

    // 查看项目详情成员分组
    public function groupAction()
    {
        $this->assign(array('page_title' => '项目群组'));
        if ($iid = arRequest('iid')) :
            $this->display();
        else :
            $this->redirectError('Index/pm', '参数错误:iid');
        endif;

    }

    // 查询当前登录用户的所有项目
    public function listItemAction()
    {
        arCfg('DEBUG_LOG', true);
        $uid = $_SESSION['u_id'];

        if (isset($uid)) {

            $items = U_item_taskModel::model()->listItem($uid);

            if ($items) {
                $this->showJson($items);
            } else {
                $this->showJsonError('当前用户没有项目！', 1301);
            }
        } else {
            $this->showJsonError('请登录！', 1302);
        }

    }

    //查看项目详情
    public function itemInfoAction()
    {
        $post = arRequest();

        if (!isset($post['id'])) {
            $this->showJsonError('没有传入参数项目id', 1303);
            return;
        } else {
            $info = U_itemsModel::model()->getInfo($post);
        }

        if ($info) {
            $this->assign(array('itemInfos',$info));
            $this->display('Index/um_project_info');

        } else {
            $this->showJsonError('查询失败！', 1304);
        }
    }

    //添加项目成员
    public function addUserAction()
    {
        $post = arRequest();

        if (!isset($post['i_id'])) {
            $this->showJsonError('没有传入参数i_id', 1303);
            return;
        } elseif (!isset($post['u_id'])) {
            $this->showJsonError('没有传入参数u_id', 1304);
            return;
        } else {
            $result = U_item_taskModel::model()->addUser($post);
        }

        if ($result) {
            $this->showJson('添加成功！');
        } else {
            $this->showJsonError('添加失败！', 1305);
        }

    }

    //删除项目成员
    public function delUserAction()
    {
        $post = arRequest();

        if (!isset($post['i_id'])) {
            $this->showJsonError('没有传入参数i_id', 1306);
            return;
        } elseif (!isset($post['u_id'])) {
            $this->showJsonError('没有传入参数u_id', 1307);
            return;
        } else {
            $result = U_item_taskModel::model()->delUser($post);
        }

        if ($result) {
            $this->showJson('删除成功！');
        } else {
            $this->showJsonError('删除失败！', 1308);
        }

    }

    //项目成员主动退出项目
    public function quitAction()
    {
        $post = arRequest();

        if (!isset($post['i_id'])) {
            $this->showJsonError('没有传入参数i_id', 1309);
            return;
        } elseif (!isset($post['u_id'])) {
            $this->showJsonError('没有传入参数u_id', 1310);
            retun;
        } else {
            $result = U_item_taskModel::model()->quit($post);
        }

        if ($result) {
            $this->showJson('退出成功！');
        } else {
            $this->showJsonError('退出失败！', 1311);
        }

    }

    //用户申请加入项目
    public function applyAction()
    {
        $post = arRequest();

        if (!isset($post['i_id'])) {
            $this->showJsonError('没有传入参数i_id', 1312);
            return;
        } elseif (!isset($post['u_id'])) {
            $this->showJsonError('没有传入参数u_id', 1313);
            return;
        } elseif (!isset($post['type'])) {
            $this->showJsonError('没有传入参数type', 1314);
            return;
        } else {
            $result = U_item_task_apply_Model::model()->apply($post);
        }

        if ($result) {
            $this->showJson('申请成功，等待审核...', 1315);
        } else {
            $this->showJsonError('申请失败！', 1316);
        }

    }

    //审核用户申请
    public function checkApplyAction()
    {
        $post = arRequest();

        if (!isset($post['i_id'])) {
            $this->showJsonError('没有传入参数i_id', 1317);
            return;
        } elseif (!isset($post['u_id'])) {
            $this->showJsonError('没有传入参数u_id', 1318);
            return;
        } elseif (!isset($post['flag'])) {
            $this->showJsonError('没有传入参数flag', 1319);
            return;
        } else {
            $result = U_item_task_apply_Model::model()->check($post);
        }

        // if ($result) {
        //     $this->showJson('')
        // }
    }

}
