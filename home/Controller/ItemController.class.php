<?php

/**
 * Created by PhpStorm.
 * User: LW
 * Date: 2017/1/5
 */
class ItemController extends ArController
{
    // 初始化方法
    public function init()
    {
        // 调用layer msg cart插件
        arSeg(array(
            'loader' => array(
                'plugin' => '',
                'this'   => $this,
            ),
        )
        );
        Ar::setConfig('DEBUG_LOG', true);

    }

    // 判断用户是否登录
    public function isLogin()
    {
        if (!isset($_SESSION['u_id'])) {
            $this->arU('Index/login');
        }

    }

    // 查询当前登录用户的所有项目
    public function listItemAction()
    {
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

    // 查看项目详情
    public function itemInfoAction()
    {
        $post = arRequest();

        if (!isset($post['id'])) {
            $this->showJsonError('没有传入参数项目id', 1303);
            return;
        } else {
            $info = U_itemsModel::model()->getInfo($post['id']);
        }

        if ($info) {
            $this->showJson($info);
        } else {
            $this->showJsonError('查询失败！', 1304);
        }

    }

    // 添加项目成员
    public function addUserAction()
    {
        $post = arRequest();

        if (!isset($post['i_id'])) {
            $this->showJsonError('没有传入参数i_id', 1305);
            return;
        } elseif (!isset($post['u_id'])) {
            $this->showJsonError('没有传入参数u_id', 1306);
            return;
        } else {
            $result = U_item_taskModel::model()->addUser($post['i_id'],$post['u_id']);
        }

        if ($result) {
            $this->showJson('添加成功！');
        } else {
            $this->showJsonError('添加失败！', 1307);
        }

    }

    // 删除项目成员
    public function delUserAction()
    {
        $post = arRequest();

        if (!isset($post['i_id'])) {
            $this->showJsonError('没有传入参数i_id', 1308);
            return;
        } elseif (!isset($post['u_id'])) {
            $this->showJsonError('没有传入参数u_id', 1309);
            return;
        } else {
            $result = U_item_taskModel::model()->delUser($post);
        }

        if ($result) {
            $this->showJson('删除成功！');
        } else {
            $this->showJsonError('删除失败！', 1310);
        }

    }

    // 项目成员主动退出项目
    public function quitAction()
    {
        $post = arRequest();

        if (!isset($post['i_id'])) {
            $this->showJsonError('没有传入参数i_id', 1311);
            return;
        } elseif (!isset($post['u_id'])) {
            $this->showJsonError('没有传入参数u_id', 1312);
            retun;
        } else {
            $result = U_item_taskModel::model()->quit($post);
        }

        if ($result) {
            $this->showJson('退出成功！');
        } else {
            $this->showJsonError('退出失败！', 1313);
        }

    }

    // 用户申请加入项目
    public function applyAction()
    {
        $post = arRequest();

        if (!isset($post['i_id'])) {
            $this->showJsonError('没有传入参数i_id', 1314);
            return;
        } elseif (!isset($post['u_id'])) {
            $this->showJsonError('没有传入参数u_id', 1315);
            return;
        } elseif (!isset($post['type'])) {
            $this->showJsonError('没有传入参数type', 1316);
            return;
        } else {
            $result = U_item_task_applyModel::model()->apply($post);
        }

        if ($result) {
            $this->showJson('申请成功，等待审核...');
        } else {
            $this->showJsonError('申请失败！', 1317);
        }

    }

    // 审核用户申请
    public function checkApplyAction()
    {
        $post = arRequest();

        if (!isset($post['i_id'])) {
            $this->showJsonError('没有传入参数i_id', 1318);
            return;
        } elseif (!isset($post['u_id'])) {
            $this->showJsonError('没有传入参数u_id', 1319);
            return;
        } elseif (!isset($post['flag'])) {
            $this->showJsonError('没有传入参数flag', 1320);
            return;
        } else {
            $result = U_item_task_apply_Model::model()->check($post);
        }

        if ($result) {
            $this->showJson('审核成功！');
        } else {
            $this->showJsonError('审核失败', 1321);
        }

    }

    // 根据项目id查询成员信息
    public function getUserInfoAction()
    {
        $post = arRequest();

        if (!isset($post['id'])) {
            $this->showJsonError('没有传入参数id', 1322);
            return;
        } else {
            $userInfo = U_itemsModel::model()->getUsers($post);
        }

        if ($userInfo) {
            $this->showJson($userInfo);
        } else {
            $this->showJsonError('查询失败！', 1323);
        }

    }

    // 查看所有项目
    public function getItemAction()
    {
        $items = U_itemsModel::model()->getItem(U_itemsModel::STATUS_APPROVED);

        if ($items) {
            $this->showJson($items);
        } else {
            $this->showJsonError('没有项目！', 1324);
        }

    }

    //获取登录用户的信息
    public function getLoginUserAction()
    {
        //获取保存在SESSION中的u_id
        $loginId = $_SESSION['u_id'];

        if (isset($loginId)) {

            $userInfo = U_usersModel::model()->getLoginInfo($loginId);

            if ($userInfo) {
                $this->showJson($userInfo);
            } else {
                $this->showJsonError('获取登录用户信息失败！', 1325);
            }
        } else {
            $this->showJsonError('请登录！', 1326);
        }

    }

    // 提交项目日志
    public function sendAssessAction()
    {
        $post = arRequest();
        $post['u_id'] = $_SESSION['u_id'];

        if (!isset($post['id'])) {
            $this->showJsonError('没有传入参数项目id', 1327);
            return;
        } elseif (!isset($post['content'])) {
            $this->showJsonError('没有传入日志的内容content', 1328);
            return;
        } else {
            $result = U_item_develop_logModel::model()->sendAssess($post);
        }

        if ($result) {
            $this->showJson('提交成功！');
        } else {
            $this->showJsonError('提交失败！', 1329);
        }

    }

    // 用户之间发送消息
    public function sendMsgByUidAction()
    {
        $post = arRequest();
        $post['u_id'] = $_SESSION['u_id'];

        // 判断传入的是id还是tel
        if (!isset($post['tel'])) {
            $post['rec_id'] = $post['rec_id'];
        } else {
            $post['rec_id'] = U_usersModel::model()->getId($post['tel']);
        }

        // 消息接收发送者id
        if (!isset($post['rec_id'])) {
            $this->showJsonError('没有传入消息接收者！', 1330);
            return;
        } elseif (!isset($post['content'])) {
            $this->showJsonError('没有传入消息内容！', 1331);
            return;
        } elseif (!isset($post['u_id'])) {
            $this->showJsonError('请先登录', 1332);
        } else {
            $result = U_messageModel::model()->sendMsgByUid($post);
        }

        if ($result) {
            $this->showJson('发送成功！');
        } else {
            $this->showJsonError('发送失败！', 1333);
        }

    }

    //判断用户是否是项目成员
    public function judgeAction()
    {
        $post = arRequest();
        $post['u_id'] = $_SESSION['u_id'];

        if (!isset($post['i_id'])) {
            $this->showJsonError('没有传入参数项目i_id', 1334);
        } elseif (!isset($post['u_id'])) {
            $this->showJsonError('请登录！', 1335);
        } else{
            $result = U_item_taskModel::model()->judge($post);
        }

        if ($result) {
            $this->showJson('你已经是项目成员了！');
        } else {
            $this->showJsonError('你还不会是该项目的成员，请先申请加入项目！',1336);
        }

    }

    //用户申请仓库
    public function applyGitAction()
    {
        $post = arRequest();
        $post['u_id'] = $_SESSION['u_id'];

        if (!isset($post['i_id'])) {
            $this->showJsonError('没有传入项目i_id',1338);
        } elseif (!isset($post['name'])) {
            $this->showJsonError('没有传入仓库名称name',1339);
        } elseif (!isset($post['description'])) {
            $this->showJsonError('没有传入仓库描述description',1340);
        } elseif (!isset($post['content'])) {
            $this->showJsonError('没有传入申请信息content',1341);
        } else {
            $result = U_item_gitModel::model()->applyGit($post);
        }

        if ($result) {
            $this->showJson('申请成功！');
        } else {
            $this->showJsonError('申请失败！',1342);
        }
    }

    //仓库创建者给用户分配仓库账号
    public function assignAction()
    {
        $post = arRequest();
        $post['u_id'] = $_SESSION['u_id'];

        //u_id 仓库创建者id ,uid 分配账号用户id
        if (!isset($post['u_id'])) {
            $this->showJsonError('请先登录！',1343);
        } elseif (!isset($post['uid'])) {
            $this->showJsonError('没有传入用户uid',1344);
        } elseif (!isset($post['id'])) {
            $this->showJsonError('没有传入仓库id',1345);
        } else {
            $result = U_git_usersModel::model()->assign($post);
        }

        if ($result) {
            $this->showJson('分配成功！');
        } else {
            $this->showJsonError('分配失败！',1346);
        }
    }

    //用户停用仓库
    public function disableAction()
    {
        $post = arRequest();
        $post['u_id'] = 71;//$_SESSION['u_id'];

        if (!isset($post['id'])) {
            $this->showJsonError('没有传入仓库id',1347);
        } elseif (!isset($post['u_id'])) {
            $this->showJsonError('请先登录！',1348);
        } else {
            $result = U_item_gitModel::model()->disable($post);
        }

        if ($result) {
            $this->showJson('成功停用仓库！');
        } else {
            $this->showJsonError('停用仓库失败！',1349);
        }
    }

    //判断项目是否有仓库
    public function checkGitAction()
    {
        $post = arRequest();

        if (!isset($post['i_id'])) {
            $this->showJsonError('没有传入项目id', 1350);
        } else {
            $result = U_itemsModel::model()->checkGit($post);
        }

        if (isset($result)) {
            $this->showJson($result);
        } else {
            $this->showJsonError('查询失败！',1351);
        }

    }

    //用户仓库列表
    public function listGitAction()
    {
        $uid = 71;//$_SESSION['u_id'];

        if ($uid) {
            $result = U_item_gitModel::model()->listGit($uid);
        }

        if ($result) {
            $this->showJson($result);
        } else {
            $this->showJsonError('查看仓库列表失败！', 1352);
        }

    }

    //查看仓库详情
    public function getGitInfoAction()
    {
        $g_id = arRequest();

        if (!isset($g_id)) {
            $this->showJsonError('没有传入仓库id', 1353);
        } else {
            $result = U_item_gitModel::model()->getGitInfo($g_id);
        }

        if ($result) {
            $this->showJson($result);
        } else {
            $this->showJsonError('查看仓库详情失败！', 1354);
        }

    }



    //发布任务
    public function releaseTaskAction()
    {
        $post = arRequest();

        if (!isset($post['id'])) {
            $this->showJsonError('没有传入项目id',1357);
        } elseif (!isset($post['content'])) {
            $this->showJsonError('没有传入任务内容content',1358);
        } else {
            $result = U_item_trackModel::model()->releaseTask($post);
        }

        if ($result) {
            $this->showJson('发布成功！');
        } else {
            $this->showJsonError('发布失败！',1359);
        }

    }

    //分配任务
    public function assignTaskAction()
    {
        $post = arRequest();
        // $post['fromid'] = $_SESSION['u_id'];
        $post['uid'] = $_SESSION['u_id'];

        if (!isset($post['tid'])) {
            $this->showJsonError('没有传入任务tid',1360);
        } elseif (!isset($post['touid'])) {
            $this->showJsonError('没有传入接收任务人touid',1361);
        } else {
            $result = U_item_track_userModel::model()->assignTask($post);
        }

        if ($result) {
            $post['content'] = '分配成功！';
            U_item_track_logModel::model()->releaseTaskLog($post);
            $this->showJson('分配成功！');
        } else {
            $post['content'] = '分配失败！';
            U_item_track_logModel::model()->releaseTaskLog($post);
            $this->showJsonError('分配失败！',1362);
        }

    }

    //分配任务日志
    // public function releaseTaskLogAction()
    // {
    //     $post = arRequest();
    //     $post['opuid'] = $_SESSION['u_id'];

    //     if (!isset($post['tid'])) {
    //         $this->showJsonError('没有传入任务tid',1363);
    //     } elseif (!isset($post['content'])) {
    //         $this->showJsonError('没有传入内容content',1364);
    //     } else {
    //         $result = U_item_track_logModel::model()->releaseTaskLog($post);
    //     }

    //     if ($result) {
    //         $this->showJson('操作成功！');
    //     } else {
    //         $this->showJsonError('操作失败！',1365);
    //     }
    // }

    //任务列表
    public function listTaskAction()
    {
        $uid = $_SESSION['u_id'];
        if (!isset($uid)) {
            $this->showJsonError('没有登录',1366);
        } else {
            $result = U_item_track_userModel::model()->myTask($uid);
        }

        if ($result) {
            $this->showJson($result);
        } else {
            $this->showJsonError('查看失败！',1367);
        }
    }

    // 任务详情
    public function taskInfoAction()
    {
        $post = arRequest();

        if (!isset($post['tid'])) {
            $this->showJsonError('没有传入任务tid',1368);
        } else {
            $result = U_item_trackModel::model()->taskmy($post['tid']);
        }

        if ($result) {
            $this->showJson($result);
        } else {
            $this->showJsonError('查看失败！',1369);
        }

    }

}
