<?php
namespace Lib\Module;
// 用户模块中间件
class UserModule
{
    /**
     *设置用户session
     * usage arModule('Lib.User')->setSession($user);
     */
    public function setSession($user)
    {
        if (!$user) :
            return false;
        endif;
        if (is_numeric($user)) :
            $user = \U_usersModel::model()->getDb()
                ->where(array('id' => $user))
                ->queryRow();
            if (!$user) :
                return false;
            endif;
        endif;
        arComp('list.session')->set('user', $user['nickname']);
        arComp('list.session')->set('u_id', $user['id']);
        return true;

    }

    /**
     * 清除session
     * usage arModule('Lib.User')->clearSession()
     */
    public function clearSession()
    {
        arComp('list.session')->set('user', null);
        arComp('list.session')->set('u_id', null);

    }

    // 用户是否登录 arModule('Lib.User')->isLogin()
    public function isLogin()
    {
        return !!$this->getUid();

    }

    // 获取uid arModule('Lib.User')->getUid()
    public function getUid()
    {
        return arComp('list.session')->get('u_id');

    }

    // 是否绑定微信 arModule('Lib.User')->hasBindWechat()
    public function hasBindWechat($uid = '')
    {
        if (!$uid) :
            $uid = $this->getUid();
        endif;
        return (\UserWechatModel::model()->getDb()->where(array('uid' => $uid))->count() > 0);

    }

    // 用户详细信息 arModule('Lib.User')->userInfo($uid);
    public function userInfo($uid)
    {
        $condition = array('id' => $uid);

        $userInfo = \U_usersModel::model()->getDb()
            ->select('id,nickname,tel,photo,weixin,email,job,qq')
            ->where($condition)
            ->queryRow();
        if ($userInfo['photo']) :
            if (strpos($userInfo['photo'], 'http://') === false) :
                $userInfo['photo'] = arCfg('UPLOAD_FILE_SERVER_PATH') . $userInfo['photo'];
            endif;
        else :
            $userInfo['photo'] = arCfg('DEFAULT_USER_LOG');
        endif;
        return $userInfo;

    }

    // 修改用户个人信息
    public function updateInfo($uid, $nickname, $qq, $email)
    {
        $data = array(
            'nickname' => $nickname,
            'qq' => $qq,
            'email' => $email,
        );

        return \U_usersModel::model()->getDb()
            ->where(array('id' => $uid))
            ->update($data);

    }

}
