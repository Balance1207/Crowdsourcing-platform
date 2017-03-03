<?php

/**
 * Created by PhpStorm.
 * User: HP
 * Date: 2016/12/14
 * Time: 16:25
 * 管理员管理
 */
class UsersManageController extends BaseController
{
    // 初始化方法
    public function init()
    {
        parent::init();
        if (!arModule('Lib.Auth')->hasSysRights(S_admin_usersModel::ROLE_SUPER_ADMIN)) :
            // $this->redirectError('Index/index', '权限不足');
        endif;

    }

    // 展示后台管理员列表
    public function adminListAction()
    {
        // 搜索用户
        $keyword = arGet('keyword');
        // 查询条件
        $condition = array('username like ' => '%' . $keyword . '%'); // 注意%两边不能有空格
        $condition['role_id <='] = $this->adminUser['role_id'];
        $result = S_admin_usersModel::model()->adminList($condition);

        $this->assign(array('rows' => $result['rows']));
        $this->assign(array('pageHtml' => $result['pageHtml']));
        $this->assign(array('totalCount' => $result['totalCount']));
        $this->assign(array('keyword' => $keyword));

        // 对显示的状态做预处理
        $handleRole = S_admin_usersModel::$ROLE_MAP;
        $this->assign(array('handleRole' => $handleRole));

        $this->assign(array('cssInsertBundles' => array('page', 'validate_jd')));

        $this->assign(array('title' => '后台管理员列表'));
        $this->display('adminList');

    }


    // 修改后台管理员
    public function modifyAction()
    {
        $param = arPost();

        if (arPost()) {
            if ($param) {
                $result = S_admin_usersModel::model()->modify($param);
            }

            if ($result == '修改成功！') {
                $this->showJsonError($result, 1200);
            } else {
                $this->showJsonError($result, 1201);

            }
        } else {
            $id = arGet('id');
            $row = S_admin_usersModel::model()->getDb()
                ->where(array('id' => $id))
                ->select('role_id,username,id,usernum')
                ->queryRow();

            $this->showJson($row);
        }

    }


    // 添加后台管理员
    public function addAdminUsersAction()
    {
        $param = arRequest();
        if ($param['role_id'] >= $this->adminUser['role_id']) :
            return $this->showJsonError('权限不足', 1403);
        endif;

        if (arPost()) {
            $result = S_admin_usersModel::model()->register($param);
            if ($result == '注册成功，待管理员审核后即可登录!') {
                $this->showJsonError($result, 1200);
            } else {
                $this->showJsonError($result, 1201);
            }
        } else {
            $this->assign(array('cssInsertBundles' => array('bootstrap', 'add', 'dropify.min')));
            $this->assign(array('title' => '添加管理员'));
            $this->display('addAdminUsers');
        }

    }


    // 提示未审核消息数
    public function uncheckedNumAction()
    {
        $num = S_admin_usersModel::model()->uncheckedNum();
        $this->showJson($num);
    }


    // 展示前台注册用户列表
    public function addAction()
    {
        $this->assign(array('meta_title' => '前台注册用户列表'));
        $this->display('add');

    }


    // 审核用户
    public function changeStatusAction()
    {
        $id = arRequest('id');
        $status = arRequest('status');
        // 批量删除
        if (is_array($id)) {
            foreach ($id as $value) {
                $result = S_admin_usersModel::model()->changeStatus($value);
            }
        } elseif (isset($id) && isset($status)) {
            $result = S_admin_usersModel::model()->changeStatus($id, $status);
        } else {
            $this->showJsonError('没有传入参数id,status');
            return;
        }

        if ($result == 'forbiden') {
            $this->showJsonError('不允许对 admin 执行该操作！');
        } else {
            $this->showJson(array('ret_msg' => '操作成功！', 'ret_code' => '1000', 'success' => "1", 'url' => arU('UsersManage/adminList')));
        }

    }


    // 展示角色信息
    public function adminRoleAction()
    {
        $roles = S_admin_usersModel::$ROLE_MAP;
        $this->showJson($roles);

    }


    // 修改角色信息
    public function editAdminRoleAction()
    {
        $param = arRequest();

        $result = S_admin_usersModel::model()->editAdminRole($param);

        if ($result) {
            $this->showJson(array('ret_msg' => '修改成功！', 'ret_code' => '1000', 'success' => "1", 'url' => arU('UsersManage/adminList')));
        }

    }


    // 删除后台管理员
    public function deleteAdminUsersAction()
    {
        $id = arRequest();

        // 批量删除
        if (is_array($id)) {
            foreach ($id as $value) {
                $result = S_admin_usersModel::model()->deleteAdminUsers($value);
            }
        } else {
            $result = S_admin_usersModel::model()->deleteAdminUsers($id);
        }

        if ($result == 'forbiden') {
            $this->showJsonError('不允许对 admin 执行该操作！');
        } else {
            $this->showJson(array('ret_msg' => '删除成功！', 'ret_code' => '1000', 'success' => "1", 'url' => arU('UsersManage/adminList')));
        }

    }


}