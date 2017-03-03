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
class U_usersModel extends ArModel
{

    static public function model($class = __CLASS__)
    {
        return parent::model($class);

    }

    // 表名
    public $tableName = 'u_users';

    CONST STATUS_APPLYING = 0;
    CONST STATUS_APPROVE = 1;
    CONST STATUS_FORBID = 2;

    // 状态
    static $STATUS_MAP = array(
        0 => '审核中',
        1 => '通过',
        2 => '禁止',
    );

    // 加密
    public function pwd($str = '123')
    {
        return md5(substr(md5(md5($str) . 'listen'), 6, 6));
    }

    // 用户登录
    public function login($post)
    {
        // 验证验证码
        if ($post['verify'] == $_SESSION['code']) {

            // 验证用户名是否正确
            $result = U_usersModel::model()->getDb()
                ->where(array('tel' => $post['username']))
                ->select('id,nickname,tel,password')
                ->queryRow();
            if ($result) {
                if ($this->pwd($post['password']) == $result['password']) {
                    arModule('Lib.User')->setSession($result);
                    return true;
                } else {
                    return '密码错误';
                }
            } else {
                return '账号错误';
            }
        } else {
            return '验证码错误';
        }

    }

    // 用户注册
    public function register()
    {
        $post = arRequest();

        // 验证验证码
        if ($post['verify'] == $_SESSION['code']) {
            if ((strlen($post['username']) >= 4) && (strlen($post['password'])) >= 5) {
                if ($post['password'] == $post['repassword']) {

                    // 验证账号是否已经注册
                    $username = U_usersModel::model()->getDb()
                        ->where(array('tel' => $post['username']))
                        ->select('tel')
                        ->queryColumn();
                    if (!$username) {
                        $result = U_usersModel::model()->getDb()
                            ->insert(array(
                                'tel'          => $post['username'],
                                'password'     => $this->pwd($post['password']),
                                'registerDate' => date('Y-m-d H:i:s'),
                            ));
                        if ($result) {
                            return true;
                        } else {
                            return false;
                        }
                    } else {
                        return '该账号已经注册了';
                    }
                } else {
                    return '两次输入的密码不一致';
                }
            } else {
                return '账号或密码格式错误';
            }
        } else {
            return '验证码错误';
        }
    }

    // 根据用户id获取用户名
    public function getPublisher($publisherId)
    {
        $result = U_usersModel::model()->getDb()
            ->select('nickname')
            ->where(array('id' => $publisherId))
            ->queryRow();

        return $result['nickname'];
    }

    //根据项目成员id获取用户信息
    public function getUserInfo($usersId)
    {
        foreach ($usersId as $key => $value) {
            $result[$key] = U_usersModel::model()->getDb()
                ->where(array('id' => $value))
                ->select('id,nickname,photo,tel')
                ->queryRow();

            //用户头像
            if ($result[$key]['photo']) {
                if (strpos($result[$key]['photo'], 'http') === false) :
                    $result[$key]['photo'] = arCfg('UPLOAD_FILE_SERVER_PATH') . $result[$key]['photo'];
                endif;
            } else {
                $result[$key]['photo'] = arCfg('DEFAULT_USER_LOG');
            }
        }

        return $result;
    }

    //查询当前登录用户的信息
    public function getLoginInfo($loginId)
    {
        $result = U_usersModel::model()->getDb()
                ->where(array('id' => $loginId))
                ->select('id,nickname,tel')
                ->queryRow();

        return $result;
    }

    //查询用户id
    public function getId($tel)
    {
        $result = U_usersModel::model()->getDb()
            ->select('id')
            ->where(array('tel' => $tel))
            ->queryRow();
        $id = $result['id'];

        return $id;
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
            // 微信信息
            $bundle['wechat'] = \UserWechatModel::model()->getDb()
            ->where(array('uid' => $bundle['id']))
            ->queryRow();

            if ($bundle['photo']) :
                if (strpos($bundle['photo'], 'http://') === false) :
                    $bundle['photo'] = arCfg('UPLOAD_FILE_SERVER_PATH') . $bundle['photo'];
                endif;
            else :
                $bundle['photo'] = arCfg('DEFAULT_USER_LOG');
            endif;

            // 职位信息
            // if (isset($bundle['job'])) :
            //     $jobids = explode(',', $bundle['job']);
            //     $jobs = U_department_jobsModel::model()
            //             ->getDb()
            //             ->where(array('jid' => $jobids))
            //             ->queryAll();
            //     // 包含权限的职位
            //     $jobs = U_department_jobsModel::model()->getDetailInfo($jobs);
            //     $memberAuths = array();
            //     foreach ($jobs as $job) :
            //         foreach ($job['auths'] as $pauth) :
            //             $memberAuths[] = $pauth;
            //         endforeach;
            //     endforeach;
            //     $bundle['jobs'] = $jobs;
            //     $bundle['auths'] = $memberAuths;
            // endif;


            return $bundle;
        endif;

        return $bundles;

    }

    // 获取用户详细信息
    public function getUser($uid)
    {
        $user = U_usersModel::model()->getDb()
            ->where(array('id' => $uid))
            ->queryRow();
        $user = $this->getDetailInfo($user);
        return $user;

    }

    //查询用户的部门
    public function getDept($id){
        $data = U_usersModel::model()->getDb()->select('department')->where(array('id' => $id))->queryRow();
        return $data;
    }
    //查询用户信息
    public function getInfo($id){
        $data = U_usersModel::model()->getDb()->where(array('id' => $id))->queryRow();
        return $data;
    }

    //修改用户部门
    public function update($datas){
        $data = U_usersModel::model()->getDb()->where(array('id' => $datas['id']))->update(array(
            'id' => $datas['id'],
            'department' => $datas['idss'],
        ));
        if (isset($data)) {
            return true;
        }
    }

     //查询用户的职位
    public function getJob($id){
        $data = U_usersModel::model()->getDb()->select('job')->where(array('id' => $id))->queryRow();
        return $data;
    }

     //修改用户职位
    public function updateJob($datas){
        $data = U_usersModel::model()->getDb()->where(array('id' => $datas['id']))->update(array(
            'id' => $datas['id'],
            'job' => $datas['idss'],
            ));
        if (isset($data)) {
            return true;
        }
    }

}
