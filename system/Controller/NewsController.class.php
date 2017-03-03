<?php

/**
 * Created by PhpStorm.
 * User: HP
 * Date: 2016/12/26
 * Time: 15:11
 */
class NewsController extends BaseController
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


    // 消息列表
    public function newsListAction()
    {
        // 搜索条件
        $keywordSender = arGet('keywordSender');
        $keywordReceiver = arGet('keywordReceiver');
        $keywordContent = arGet('keywordContent');
        // 接收消息类型
        $type = arGet('type');

        // 默认为普通消息列表
        if ($type === "") {
            $type = 1;
        }

        // 查询条件
        $condition = array();
        if ($keywordSender != '') {
            $condition[] = array('sender' => $keywordSender);
        }
        if ($keywordReceiver != '') {
            $condition[] = array('receiver' => $keywordReceiver);
        }
        if ($keywordContent != '') {
            $condition[] = array('content like ' => '%' . $keywordContent . '%');
        }

        $result = NewsModel::model()->newsList($condition, $type);

        $this->assign(array('cssInsertBundles' => array('page')));
        $this->assign(array('rows' => $result['rows']));
        $this->assign(array('pageHtml' => $result['pageHtml']));
        $this->assign(array('totalCount' => $result['totalCount']));
        $this->assign(array('keywordSender' => $keywordSender));
        $this->assign(array('keywordReceiver' => $keywordReceiver));
        $this->assign(array('keywordContent' => $keywordContent));

        $this->assign(array('title' => '消息列表'));
        $this->display();

    }

    // 查询子消息
    public function newsChildrenListAction()
    {
        $pId = arGet('msg_id');

        $result = NewsModel::model()->newsChildrenList($pId);

        $this->showJson($result);

    }


    // 删除消息
    public function deleteNewsAction()
    {
        $id = arRequest('id');

        // 批量删除
        if (is_array($id)) {
            foreach ($id as $value) {
                $result = NewsModel::model()->deleteNews($value);
            }
        } else {
            $result = NewsModel::model()->deleteNews($id);
        }

        if ($result) {
            $this->showJson(array('ret_msg' => '删除成功！', 'ret_code' => '1000', 'success' => "1", 'url' => arU('News/newsList')));
        }

    }


    // 发送消息
    public function sendNewsAction()
    {
        $param = arPost();

        if (arPost()) {
            $result = NewsModel::model()->sendNews($param);

            if ($result) {
                $this->showJson(array('ret_msg' => '发送成功！', 'ret_code' => '1000', 'success' => "1", 'url' => arU('News/newsList')));
            }
        } else {
            if (arGet()) {
                $nickname = urldecode(arGet('nickname'));

                $this->assign(array('id' => arGet('id'), 'nickname' => $nickname));
                $this->assign(array('title' => '发送消息'));
                $this->display();
            } else {
                $this->assign(array('title' => '发送消息'));
                $this->display();
            }
        }

    }


    // 动态展示收件人信息
    public function receiverListAction()
    {
        $receiver = arRequest('receiver');

        $result = NewsModel::model()->receiverList($receiver);

        $this->showJson($result);

    }

}