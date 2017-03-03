<?php

/**
 * Created by PhpStorm.
 * User: LW
 * Date: 2017/1/5
 */
class TaskController extends ArController
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

    }

    // 后台任务列表
    public function showListAction()
    {
        // 关键字搜索任务
        $keyword = arGet('keyword');

        // 项目id
        $iid = arRequest('iid');

        // 搜索条件
        $condition = array('tname like ' => '%' . $keyword . '%','iid' =>$iid);        
        $result = U_item_trackModel::model()->listTask($condition, arRequest('status', U_item_trackModel::STATUS_APPROVED));

        $this->assign(array('title' => '仓库列表'));
        $this->assign(array('iid' => $iid));
        $this->assign(array('keyword' => $keyword));
        $this->assign(array('tasks' => $result['task']));
        $this->assign(array('totalCount' => $result['totalCount']));
        $this->assign(array('pageHtml' => $result['pageHtml']));
        $this->display();

    }

    // 后台发布任务
    public function releaseTaskAction()
    {
        $post = arRequest();

        if (!isset($post['taskId'])) {
            $this->assign(array('taskId' => $post['iid']));
            $this->assign(array('title' => '修改任务信息'));
            $this->display();
        } else {
            $result = U_item_trackModel::model()->releaseTask($post['tname'],$post['taskId'],$post['content']);
            if ($result) {
                $this->showJson(array('ret_msg' => '操作成功！', 'ret_code' => '1000', 'success' => "1", 'url' => arU('Task/showList', array('iid'=>arRequest('iid')))));
            }
        }

    }

    // 审核任务(修改任务状态)
    public function checkTaskAction()
    {
        $post = arRequest();
        $result = U_item_trackModel::model()->checkTask($post['tid'],$post['dev_status']);
        if ($result) {
             $this->showJson(array('ret_msg' => '操作成功！', 'ret_code' => '1000', 'success' => "1", 'url' => arU('Task/showList')));
         }

    }

    // 修改任务信息
    public function updateTaskAction()
    {
        $post = arRequest();

        if (!isset($post['taskId'])) {
            $result = U_item_trackModel::model()->taskInfo($post['tid']);

            // 任务状态
            $trackStatus = U_item_track_status_typeModel::model()->getDb()
                ->queryAll();
            $taskLogs = U_item_track_logModel::model()->getLog($post['tid']);
            // 任务所属的项目id
            $track = U_item_trackModel::model()->getTname($post['tid']);
            $this->assign(array('iid' => $track['iid']));
            $this->assign(array('taskLogs' => $taskLogs));
            $this->assign(array('trackStatus' => $trackStatus));
            $this->assign(array('taskInfo' => $result));
            $this->assign(array('title' => '修改任务信息'));

            $itemUsers = U_item_taskModel::model()->getAllUsers($result['iid']);

            // 获取所有任务当前处理成员
            $trackUsers = U_item_track_userModel::model()->getCurrentOperator(arRequest('tid'));

            $this->assign(array('itemUsers' => $itemUsers, 'trackUsers' => $trackUsers));
            $this->display();
        } else {
            $uid = arComp('list.session')->get('adminuid');
            // 更新前的数据结果
            $resultOld = U_item_trackModel::model()->taskInfo($post['tid']);
            $result['track'] = U_item_trackModel::model()->updateTask($post['tname'],$post['content'],$post['dev_status'],$post['tid'],$post['taskId'],$post['status'],$post['level']);
            $result['user'] = U_item_track_userModel::model()->updateUser($post['tid'],$post['assignuser']);
            // 记录任务变更日志
            if ($result) {
                // 更新后的数据结果 
                $resultNew = U_item_trackModel::model()->taskInfo($post['tid']);
                $content = '';
                foreach ($resultOld as $key => $value) {
                        
                        if ($resultOld[$key] !== $resultNew[$key]) {
                            switch ($key) {
                                case 'tname':
                                    $keyC = '任务名称';
                                    break;
                                case 'content':
                                    $keyC = '任务描述';
                                    break;
                                case 'dev_status':
                                    $keyC = '开发状态';
                                    break;
                                case 'uid':
                                    $keyC = '任务成员';
                                    break;
                                case 'status':
                                    $keyC = '审核状态';
                                    break;
                                case 'level':
                                    $keyC = '任务级别';
                                    break;
            
                                default:
                                    $keyC = '其他';
                                    break;
                            }
                            if ($keyC == '任务描述' ) {
                                $content .= '将'.$keyC.'由'.$resultOld[$key].'修改为'.$resultNew[$key].'。';
                            }else if ($keyC == '开发状态') {
                                $resultOld[$key] = U_item_trackModel::$TYPE[$resultOld[$key]];
                                $resultNew[$key] = U_item_trackModel::$TYPE[$resultNew[$key]];
                                $content .= '将'.$keyC.'由'.$resultOld[$key].'修改为'.$resultNew[$key].'。';
                            }
                            else if ($keyC == '任务成员') {
                                $addUsersId = array_diff($resultNew[$key],$resultOld[$key]);
                                foreach ($addUsersId as $key => $value) {
                                    $addUsersId[$key] = $value;
                                    $addUser[$key] = U_usersModel::model()->getPublisher($value);
                                }
                                $addUser = implode(',', $addUser);
                                $content .= '添加成员'.$addUser.'。';
                            } else if ($keyC == '任务级别') {
                                $resultOld[$key] = U_item_trackModel::$LEVEL_MAP[$resultOld[$key]];
                                $resultNew[$key] = U_item_trackModel::$LEVEL_MAP[$resultNew[$key]];
                                $content .= '将'.$keyC.'由'.$resultOld[$key].'修改为'.$resultNew[$key].'。';
                            } else if ($keyC == '审核状态') {
                                $resultOld[$key] = U_item_trackModel::$STATUS_MAP[$resultOld[$key]];
                                $resultNew[$key] = U_item_trackModel::$STATUS_MAP[$resultNew[$key]];
                                $content .= '将'.$keyC.'由'.$resultOld[$key].'修改为'.$resultNew[$key].'。';
                            } else {
                                $content .= '将'.$keyC.'由'.$resultOld[$key].'修改为'.$resultNew[$key].'。';
                            }                            
                        }
                    }
                    if ($content) {
                        $log = U_item_track_logModel::model()->joinLog($post['tid'], $content, $uid);
                    }                   
            }            
            $this->showJson(array('ret_msg' => '操作成功！', 'ret_code' => '1000', 'success' => "1", 'url' => arU('Task/showList', array('iid'=>$resultNew['iid'])))); 
        }

    }

    // 删除任务
    public function delTaskAction()
    {
        $tid = arRequest('tid');
        // 判断是否批量删除
        if (is_array($tid)) {
            foreach ($tid as $value) {
                $track = U_item_trackModel::model()->getTname($value);
                $result = U_item_trackModel::model()->delTask($value);                
            }
        } else {
                $track = U_item_trackModel::model()->getTname($tid);
                $result = U_item_trackModel::model()->delTask($tid);      
        }

        if ($result) {
             $this->showJson(array('ret_msg' => '删除成功！', 'ret_code' => '1000', 'success' => "1", 'url' => arU('Task/showList', array('iid' => $track['iid']))));
         }
    }

    // 任务变更日志
    public function taskLogAction()
    {
        $tid = arRequest('tid');
        $result = U_item_track_logModel::model()->getLog($tid);
        $this->assign(array('title' => '任务变更日志列表'));
        $this->assign(array('logs' => $result));
        $this->display();
    }

    // 分配任务
    public function assignTaskAction()
    {
        $this->display();

    }

    // 删除成员
    public function deleteUserAction()
    {
        $deleteResult = arModule('system.Track')->deleteUser(arRequest('tid'), arRequest('uid'));
        
        if ($deleteResult) :
            // 任务变更日志
            $user = U_usersModel::model()->getPublisher(arRequest('uid'));
            $content = '删除成员'.$user;
            $uid = arComp('list.session')->get('adminuid');
            $log = U_item_track_logModel::model()->joinLog(arRequest('tid'), $content, $uid);
            if ($log) :
                $this->showJsonSuccess();
            endif;            
        else :
            $this->showJsonError();
        endif;

    }

    // 
    public function checkApplyAction()
    {
        $tid = arRequest('tid');
        // 任务所属的项目
        $track = U_item_trackModel::model()->getTname($tid);
        $users = arModule('Lib.Track')->userApply($tid);
        // var_dump($users);
        // exit;
        $track = U_item_trackModel::model()->getTname($tid);
        $track['tid'] = $tid;
        $this->assign(array('iid' => $track['iid']));
        $this->assign(array('users' => $users));
        $this->assign(array('track' => $track));
        $this->assign(array('title' => '任务申请审核'));
        $this->display();
    }

    // 审核任务申请
    public function checkAction()
    {
        // 审核回复信息
        $msg = arRequest('msg');

        if ($tid = arRequest('tid')) :
            // 任务所属的项目id
            $iid = arModule('Lib.Track')->itemId($tid);        
            if (!is_null($type = arRequest('type'))) :
                if ($uid = arRequest('uid')) :
                    if (is_array($uid)) :
                        foreach ($uid as $value) :
                            $result = arModule('Lib.Track')->checkApply($tid, $type, $value, $msg);
                        endforeach;
                        if ($result) :
                            $this->showJson(array('ret_msg' => '审核成功！', 'ret_code' => '1000', 'success' => "1", 'url' => arU('Task/showList', array('iid' => $iid))));
                        else :
                            $this->showJsonError('审核失败！');
                        endif;
                    else :
                        $result = arModule('Lib.Track')->checkApply($tid, $type, $uid, $msg);
                        if ($result) :
                            $this->showJson(array('ret_msg' => '审核成功！', 'ret_code' => '1000', 'success' => "1", 'url' => arU('Task/showList', array('iid' => $iid))));
                        else :
                            $this->showJsonError('审核失败！');
                        endif;
                    endif;
                else :
                    $this->showJsonError('参数错误：uid丢失');
                endif;
            else :
                $this->showJsonError('参数错误：type丢失');
            endif;
        else :
            $this->showJsonError('参数错误：tid丢失');
        endif;
          
    }

    // 申请任务的用户
    public function userApplyAction()
    {
        if ($tid = arRequest('tid')) :
            $users = arModule('Lib.Track')->userApply($tid);
            $this->showJson($users);
        else :
            $this->showJsonError('参数错误：tid丢失');
        endif;
    }

}
