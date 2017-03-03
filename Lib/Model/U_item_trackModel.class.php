<?php
/**
 * Powerd by ArPHP.
 *
 * Model.
 *
 * @author LW
 */

/**
 * Default Model of webapp.
 */
class U_item_trackModel extends ArModel
{

    static public function model($class = __CLASS__)
    {
        return parent::model($class);

    }

    // 表名
    public $tableName = 'u_item_track';

    // 任务开发状态
    static $TYPE = array(
    	0 => '未分配',
        1 => '指派中',
    	2 => '已分配，等待开发',
    	3 => '开发中',
    	4 => '开发完待测试',
    	5 => '测试中',
    	6 => '测试未通过已返回',
    	7 => '测试通过待审核',
    	8 => '审核未通过已返回',
    	9 => '审核通过开发完成 待发布',
    	10 => '已发布完成'
    );

    const STATUS_APPROVING = 0;
    const STATUS_APPROVED = 1;
    const STATUS_FORBIDEN = 2;
    // 任务审核状态
    static $STATUS_MAP = array(
        0 => '未审核',
        1 => '审核通过',
        2 => '审核失败',
    );

    // 任务级别
    static $LEVEL_MAP = array(
        0 => 'require', //需求
        1 => 'develop', //开发
        6 => 'bug', //线上bug
        7 => 'emerg', //紧急

    );

    // 发布任务
    public function releaseTask($tname,$iid,$content)
    {
    	$data = array(
    		'tname' => $tname,
    		'iid' => $iid,
    		'content' => $content
    		);
    	$result = U_item_trackModel::model()->getDb()
    		->insert($data);
    	return $result;

    }

    // 所有任务列表
    public function listTask($condition=array(), $status = U_item_trackModel::STATUS_APPROVED)
    {
        $condition['status'] = $status;
        // 数据总的条数
        $totalCount = U_item_trackModel::model()->getDb()
            ->where(array('iid' => $condition['iid']))
            ->count();
        // 将查询数据集并分页
        $page = new Page($totalCount, 10);
        $task = U_item_trackModel::model()->getDb()
            ->limit($page->limit())
            ->order('tid desc')
            ->where($condition)
            ->queryAll();

        foreach ($task as $key => $value) {
            $task[$key]['iid'] = U_itemsModel::model()->getInfo($value['iid']);
            $task[$key]['item'] = $task[$key]['iid']['i_name'];
            // $task[$key]['users'] = $this->set($value['users']);
            $douser = U_item_track_userModel::model()->getUser($value['tid']);
            $task[$key]['douser'] = $douser['name'];
            $task[$key]['dev_status'] = self::$TYPE[$value['dev_status']];
            $task[$key]['level'] = self::$LEVEL_MAP[$value['level']];
            $task[$key]['num'] = U_item_track_applyModel::model()->applyNum($value['tid']);

        }

        $pageHtml = $page->show();

        return array('task'=>$task, 'totalCount' => $totalCount, 'pageHtml' => $pageHtml);

    }

    // 任务详情
    public function taskInfo($tid)
    {
    	$result = U_item_trackModel::model()->getDb()
    		->where(array('tid' => $tid))
    		->queryRow();

        $userInfo = U_item_track_userModel::model()->getDb()
            ->select('uid')
            ->where(array('tid' => $tid))
            ->queryAll();

        foreach ($userInfo as $key => $value) {
            $userInfo[$key] = $value['uid'];
        }
        $result['uid'] = $userInfo;

    	return $result;

    }

    // 用户任务详情
    public function taskmy($tid)
    {
        $result = U_item_trackModel::model()->getDb()
            ->where(array('tid' => $tid))
            ->queryRow();
        // 任务状态
        $result['dev_status'] = self::$TYPE[$result['dev_status']];

        // 任务成员
        // if ($result['users']) {
        //     $uids = explode(',', $result['users']);
        //         foreach ($uids as $key => $value) {
        //             $uids[$key] = $value;
        //             $userName[$value] = U_usersModel::model()->getPublisher($uids[$key]);
        //         }
        //         $result['users'] = $userName;
        // } else {
        //     $result['users'] = NULL;
        // }

        // 任务所属项目名称
        $item = U_itemsModel::model()->getInfo($result['iid']);
        $result['i_name'] = $item['i_name'];

        // 任务当前处理人
        $result['doUser'] = U_item_track_userModel::model()->getUser($tid);
        return $result;
    }

    // 审核任务
    public function checkTask($tid,$status)
    {
        $result = U_item_trackModel::model()->getDb()
            ->where(array('tid' => $tid))
            ->update(array('dev_status' => $dev_status));

        return $result;
    }

    // 删除任务
    public function delTask($tid)
    {
        $result = U_item_trackModel::model()->getDb()
            ->where(array('tid' => $tid))
            ->update(array('status' => self::STATUS_FORBIDEN));

        return $result;
    }

    // 修改任务信息
    public function updateTask($tname,$content,$dev_status,$tid,$taskId,$status,$level)
    {
        $data = array(
            'tname' => $tname,
            'content' => $content,
            'dev_status' => $dev_status,
            'level' => $level,
            'status' => $status
            );

        // 判断该任务是否分配了
        $task = U_item_track_userModel::model()->getDb()
            ->where(array('tid' => $tid))
            ->queryRow();

        $nowuser = U_item_track_userModel::model()->getDb()
            ->select('uid')
            ->where(array('tid' => $tid,'touid' => '0'))
            ->queryRow();

        // 查询执行任务人员的姓名
        $result['nowuser'] = U_usersModel::model()->getDb()
            ->select('nickname')
            ->where(array('id' => $nowuser))
            ->queryRow();

        // 更新任务数据表
        $result['track'] = U_item_trackModel::model()->getDb()
            ->where(array('tid' => $taskId))
            ->update($data);
        // U_item_track_logModel::model()->taskLog($post);

        return $result;
    }

    // 项目成员数据处理
    public function set($row)
    {
        $users = explode(',', $row);

        foreach ($users as $key=>$value) {
            $user[$key] = $value;
            $trackUser[$key] = U_usersModel::model()->getPublisher($user[$key]);

        }
        $trackUsers = implode(",", $trackUser);
        return $trackUsers;
    }

    // 根据任务tid获取任务名称
    public function getTname($tid)
    {
        $result = U_item_trackModel::model()->getDb()
            ->select('tname,iid')
            ->where(array('tid' => $tid))
            ->queryRow();
        return $result;

    }

    // 获取bundle详细信息 万能方法
    public function getDetailInfo(array $bundles)
    {
        // 递归遍历所有产品信息
        if (arComp('validator.validator')->checkMutiArray($bundles)) :
            foreach ($bundles as &$bundle) :
                $bundle = $this->getDetailInfo($bundle);
            endforeach;
        else :
            $bundle = $bundles;

            // 任务地址
            $bundle['track_url'] = arU('/main/track/detail', array('tid' => $bundle['tid']));
            // 任务状态
            $bundle['dev_status'] = self::$TYPE[$bundle['dev_status']];
            // 任务级别
            $bundle['level'] = isset(self::$LEVEL_MAP[$bundle['level']]) ? self::$LEVEL_MAP[$bundle['level']] : 'develop';
            switch ($bundle['level']) {
                case 'emerg':
                    $bundle['level'] = 'label-danger';
                    break;
                case 'bug':
                    $bundle['level'] = 'label-warning';
                    break;
                case 'develop':
                    $bundle['level'] = 'label-info';
                    break;
                case 'require':
                    $bundle['level'] = 'label-success';
                    break;

                default:
                    $bundle['level'] = 'label-primary';
                    break;
            }
            $item = U_itemsModel::model()
                ->getDb()
                ->where(array('id' => $bundle['iid']))
                ->queryRow();
            $item = U_itemsModel::model()->getDetailInfo($item);
            $bundle['item'] = $item;
            return $bundle;
        endif;

        return $bundles;

    }

}
