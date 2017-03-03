<?php
namespace Lib\Module;
// Session 中间件
class SessionModule
{
    // 注册 arModule('Lib.Session')->register('ABCDEABCDE');
    public function register($accode = '')
    {
        $uniqueSid = session_id();
        $sessionCondition = array(
            'ssid' => $uniqueSid,
        );
        $sessionUser = \BaseSessionModel::model()->getDb()->where($sessionCondition)->queryRow();
        if (!$sessionUser) :
            if (!$accode) :
                $accode = arComp('tools.util')->randpw(10, 'CHAR');
                $accode = strtoupper($accode);
            endif;
            $session = array(
                'ssid' => $uniqueSid,
                'accode' => $accode,
            );
            \BaseSessionModel::model()->getDb()->insert($session);
            return $accode;
        else :
            return $sessionUser['accode'];
        endif;

    }

    // 绑定seession信息 arModule('Lib.Session')->bind('ABCDEABCDE', 81)
    public function bind($accode, $uid)
    {
        $sessionCondition = array(
            'accode' => $accode,
        );
        return \BaseSessionModel::model()->getDb()
            ->where($sessionCondition)
            ->update(array('bid' => $uid));

    }

    // 获取绑定id arModule('Lib.Session')->bindId($accode = '')
    public function bindId($accode = '')
    {
        $sessionCondition = array();
        if (!$accode) :
            $sessionCondition['ssid'] = session_id();
        else :
            $sessionCondition['accode'] = $accode;
        endif;
        $sessionCondition['bid != '] = '';

        $sessionUser = \BaseSessionModel::model()->getDb()->where($sessionCondition)->queryRow();
        if ($sessionUser) :
            return $sessionUser['bid'];
        else :
            return false;
        endif;

    }

    // flush bid arModule('Lib.Session')->flushBid($bid)
    public function flushBid($bid = 0)
    {
        if ($bid) :
            return \BaseSessionModel::model()->getDb()->where(array('bid' => $bid))->delete();
        else :
            return false;
        endif;

    }

}
