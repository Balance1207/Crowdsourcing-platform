<?php
/**
 * Powerd by ArPHP.
 *
 * Model.
 *
 * @author ycassnr <ycassnr@gmail.com>
 */

/**
 * Default Model of webapp.
 */
class U_itemsModel extends ArModel
{

    static public function model($class = __CLASS__)
    {
        return parent::model($class);

    }

    // 表名
    public $tableName = 'u_items';


    const STATUS_APPROVING = 0;
    const STATUS_APPROVED = 1;
    const STATUS_FORBIDEN = 2;
    // 审核状态
    static $STATUS_MAP = array(
        0 => '未审核',
        1 => '审核通过',
        2 => '审核失败',
    );

    // 项目审核状态
    static $TYPE = array(
        0 => '未审核',
        1 => '已审核',
    );

    // 展示项目列表
    public function showList()
    {
        $items = U_itemsModel::model()->getDb()
        // ->where(array('u_id ' => $uid))
            ->queryAll();
        return $items;

    }

    // 查询项目详情
    public function getInfo($iid)
    {
        $result = U_itemsModel::model()->getDb()
            ->select('id,i_name,img,audit,publisher,money,contractDate,money,releaseDate,requirement,days')
            ->where(array('id' => $iid))
            ->queryRow();

        // 项目成员
        $condition = array(
            'i_id' => $iid,
            'stay' => self::STATUS_APPROVED
        );

        $itemUsers = U_item_taskModel::model()->getDb()
            ->select('u_id')
            ->where($condition)
            ->queryAll();
        $result['itemUsers'] = U_usersModel::model()->getDetailInfo($itemUsers);
        // var_dump($itemUsers);
        // exit;
        // 查询项目发布人
        $result['publisher'] = U_usersModel::model()->getPublisher($result['publisher']);

        //项目二维码图片
        if ($result['img']) {
            $result['img'] = arCfg('UPLOAD_FILE_SERVER_PATH') . $result['img'];
        } else {
            $result['img'] = arCfg('DEFAULT_USER_LOG');
        }

        // 项目审核状态
        $result['audit'] = self::$TYPE[$result['audit']];

        return $result;

    }

    //查看所有项目
    public function getItem($status)
    {
        $condition = array(
            'status' => $status,
        );
        //数据分页
        $count = U_itemsModel::model()->getDb()
            ->where($condition)
            ->count();
        $page = new Page($count, 10);

        $results = U_itemsModel::model()->getDb()
            ->select('id,i_name,users,img,audit')
            ->limit($page->limit())
            ->where($condition)
            ->queryAll();

        foreach ($results as $key => $value) {
            $result[$key]['id']     = $value['id'];
            $result[$key]['i_name'] = $value['i_name'];
            $result[$key]['users']  = $value['users'];
            $result[$key]['img']    = $value['img'];
            $result[$key]['audit']  = $value['audit'];

            // 参与项目人数
            if ($result[$key]['users']) {
                $users                    = explode(',', $result[$key]['users']);
                $result[$key]['usersNum'] = count($users);
            } else {
                $result[$key]['usersNum'] = 0;
            }

            // 二维码图片
            if ($result[$key]['img']) {
                $result[$key]['img'] = arCfg('UPLOAD_FILE_SERVER_PATH') . $result[$key]['img'];
            } else {
                $result[$key]['img'] = arCfg('DEFAULT_USER_LOG');
            }

            // 项目审核状态
            $result[$key]['audit'] = self::$TYPE[$result[$key]['audit']];
        }
        $totalcount = ceil($count / 10);
        $data       = array('result' => $result, 'count' => $totalcount);
        return $data;

    }

    //根据项目id查询项目成员
    public function getUsers($post)
    {
        $users = U_itemsModel::model()->getDb()
            ->select('users')
            ->where(array('id' => $post['id']))
            ->queryRow();

        $usersId  = explode(',', $users['users']);
        $userInfo = U_usersModel::model()->getUserInfo($usersId);

        return $userInfo;

    }

    //判断项目是否有仓库
    public function checkGit($post)
    {
        $result = U_itemsModel::model()->getDb()
            ->select('git')
            ->where(array('id' => $post['i_id']))
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

            // 项目地址
            $bundle['items_url'] = arU('/main/Index/pd_item', array('id' => $bundle['id']));
            $bundle['url'] = arU('/wechat/Api/qrcode', array('data' => urlencode($bundle['items_url'])), 'FULL');
            if (isset($bundle['i_name'])&&(strlen($bundle['i_name']) > 12)) :
                $bundle['i_name'] = substr($bundle['i_name'],0,12) .'...';
            else :
                $bundle['i_name'] = U_itemsModel::model()->getDb()
                    ->where(array('id' => $bundle['id']))
                    ->queryColumn('i_name');
            endif; 
            return $bundle;
        endif;

        return $bundles;

    }

}
