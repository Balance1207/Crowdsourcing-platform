<?php
namespace wechat\Module;
class BaseModule
{
    // 微信对象
    public $wechat;

    public function initModule()
    {
        \WechatConfigModel::model()->setConfig();
        $this->wechat = arComp('ext.weixin');

    }

    // 是否微信客户端 arModule('wechat.Base')->isWeixin();
    public function isWeixin()
    {
        return $this->wechat->isWeixin();

    }

    // 是否绑定微信  arModule('wechat.Base')->hasBind($uid);
    public function hasBind($uid)
    {
        if ($uid) :
            $condition = array(
                'uid' => $uid,
            );
            $nums = \UserWechatModel::model()->getDb()->where($condition)->count();
            if ($nums > 0) :
                return true;
            else :
                return false;
            endif;
        else :
            return false;
        endif;

    }

    // 绑定用户 arModule('wechat.Base')->bindUser($uid = '', $userInfo);
    public function bindUser($uid = '', $userInfo)
    {
        $openid = $userInfo['openid'];
        $condition = array(
            'openid' => $openid,
            'uid != ' => '',
        );
        $weUser = \UserWechatModel::model()->getDb()
            ->where($condition)
            ->queryRow();

        $user = array(
            'photo' => $userInfo['headimgurl'],
            'sex' => $userInfo['sex'],
        );
        if ($weUser) :
            if ($uid) :
                if ($uid != $weUser['uid']) :
                    $userOrigin = \U_usersModel::model()->getDb()
                        ->where(array('id' => $uid))
                        ->queryRow();
                    $user['tel'] = $userOrigin['tel'];
                    if ($userOrigin['password']) :
                        $user['password'] = $userOrigin['password'];
                    endif;
                    if ($userOrigin['nickname']) :
                        $user['nickname'] = $userOrigin['nickname'];
                    endif;
                    // 删除原来账户
                    \U_usersModel::model()->getDb()
                        ->where(array('id' => $uid))
                        ->delete();
                    // 新账号
                    $uid = $weUser['uid'];
                endif;
            else :
                // 没有账号
                $uid = $weUser['uid'];
            endif;
            // 更新用户表数据
            \U_usersModel::model()->getDb()
                        ->where(array('id' => $uid))
                        ->update($user, true);

            $userInfo['uid'] = $uid;
            $userInfo['updatetime'] = time();
            // 更新用户微信表
            \UserWechatModel::model()->getDb()->where($condition)->update($userInfo, true);
        else :
            if ($uid) :
                \U_usersModel::model()->getDb()
                    ->where(array('id' => $uid))
                    ->update($user, true);
            else :
                $user['tel'] = $userInfo['nickname'];
                $user['nickname'] = $userInfo['nickname'];

                $uid = \U_usersModel::model()->getDb()->insert($user, true);
            endif;

            $userInfo['uid'] = $uid;
            $userInfo['bindtime'] = $userInfo['updatetime'] = time();
            // 写入用户微信表
            \UserWechatModel::model()->getDb()->insert($userInfo, true);
        endif;
        return $uid;

    }

    // 微信登录地址 arModule('wechat.Base')->loginUrl();
    public function loginUrl($param = array())
    {
        return arU('/wechat/Linker/loginToWeixin', $param, 'FULL');

    }

}
