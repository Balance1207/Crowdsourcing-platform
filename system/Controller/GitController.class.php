<?php

/**
 * Created by PhpStorm.
 * User: LW
 * Date: 2017/1/11
 * Time: 10:57
 * 仓库管理
 */
class GitController extends ArController
{
    // 初始化方法
    public function init()
    {
        // 调用layer msg cart插件
        arSeg(array(
                'loader' => array(
                    'plugin' => '',
                    'this' => $this
                )
            )
        );

    }

    // 仓库列表
    public function showListAction()
    {
        // 搜索仓库
        $keyword = arGet('keyword');
        // 查询条件
        $condition = array('name like ' => '%' . $keyword . '%','audit <' => 2);

        $result = U_item_gitModel::model()->showList($condition);
        $this->assign(array('title' => '仓库列表'));
        $this->assign(array('keyword' => $keyword));
        $this->assign(array('gits' => $result['git']));
        $this->assign(array('totalCount' => $result['totalCount']));
        $this->assign(array('pageHtml' => $result['pageHtml']));

        $this->display();
    }

    // 修改仓库信息
    public function updateGitAction()
    {
        $post = arRequest();

        // 显示修改前的信息
        if (!isset($post['gitId'])) {
            $gitInfos = U_item_gitModel::model()->getGitInfo($post['id']);
            $this->assign(array('gitInfo' => $gitInfos));
            $this->assign(array('title' => '修改仓库信息'));

            $this->display();
        } else {
            // 修改信息
            $result = U_item_gitModel::model()->updateGit($post);

            if ($result) {
                $this->showJson(array('ret_msg' => '修改成功！', 'ret_code' => '1000', 'success' => "1", 'url' => arU('Git/showList', array('type' => 1))));
            }
        }

    }

    // 审核仓库申请
    public function changeStatusAction()
    {
        $post = arRequest();

        $result = U_item_gitModel::model()->changeStatus($post);

        if ($result) {
            $this->showJson(array('ret_msg' => '操作成功！', 'ret_code' => '1000', 'success' => "1", 'url' => arU('Git/showList', array('type' => 1))));
        }

    }

    // 管理员禁用仓库
    public function disableGitAction()
    {
        $post = arRequest();

        $result = U_item_gitModel::model()->disableGit($post);

        if ($result) {
            $this->showJson(array('ret_msg' => '操作成功！', 'ret_code' => '1000', 'success' => "1", 'url' => arU('Git/showList', array('type' => 1))));
        }
    }

    // 删除仓库
    public function delGitAction()
    {
        $gitId = arRequest('id');

        // 判断是否批量删除
        if (is_array($gitId)) {
            foreach ($gitId as $value) {
                $result = U_item_gitModel::model()->delGit($value);
            }          
        } else {
            $result = U_item_gitModel::model()->delGit($gitId);
        } 
        
        if ($result) {
            $this->showJson(array('ret_msg' => '操作成功！', 'ret_code' => '1000', 'success' => "1", 'url' => arU('Git/showList', array('type' => 1))));
        }
    }

}