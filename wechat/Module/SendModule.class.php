<?php
namespace wechat\Module;
// 发送信息
class SendModule extends BaseModule
{
    // 模板消息 arModule('wechat.Send')->TplUserLogin($uid);
    public function TplUserLogin($uid = 0)
    {
        if (!$uid) :
            return false;
        endif;
        $userWechatInfo = \UserWechatModel::model()->getDb()
            ->where(array('uid' => $uid))
            ->queryRow();
        if ($userWechatInfo) :
            $data = array(
                'first' => array('value'=>'你好:' . $userWechatInfo['nickname'], 'color'=> '#173177'),
                'time' => array('value'=>date('Y-m-d H:i:s', time()), 'color'=>'#173177'),
                'ip' => array('value'=> arComp('tools.util')->getClientIp(), 'color'=>'#173177'),
                'reason' => array('value'=>'登录提醒', 'color'=>'#173177'),
            );
            try {
                $res = arComp('ext.weixin')->sendTemplateMsg($userWechatInfo['openid'], '3XGjvQOJHujK7k853pIO6I2rTMNNZt0yTHgIbEZmZSo', '', $data);
                return $res;

            } catch (\Exception $e) {
                arComp('list.log')->record(array('errMsg' => $e->getMessage(), $userWechatInfo['openid'], '3XGjvQOJHujK7k853pIO6I2rTMNNZt0yTHgIbEZmZSo', '', $data), 'wxerr_TplUserLogin');
            }
        else :
            return false;
        endif;

    }

    // 新任务通知 arModule('wechat.Send')->TplNewTask($uid, $tid, $fromUid = '', $msg = '');
    public function TplNewTask($uid, $tid, $fromUid = '', $msg = ' ')
    {
        $sendUser = \U_usersModel::model()->getUser($uid);
        if (empty($sendUser['wechat'])) :
            return false;
        else :
            $track = \U_item_trackModel::model()->getDb()
                ->where(array('tid' => $tid))
                ->queryRow();

            if ($fromUid) :
                $fromUser = \U_usersModel::model()->getUser($fromUid);
                $fromUserName = $fromUser['nickname'];
            else :
                $fromUserName = '系统管理员';
            endif;

            $userWechatInfo = $sendUser['wechat'];

            if (strlen($track['content']) > 30) :
                $track['content'] = arComp('tools.util')->substr_cut($track['content'], 30) . '...';
            endif;

            $data = array(
                'first' => array('value' => 'track[' . $track['tid'] . ']:' . $track['tname'], 'color'=> '#173177'),
                'keyword1' => array('value' => $track['content'], 'color'=>'#173177'),
                'keyword2' => array('value'=> '参考任务要求', 'color'=>'#173177'),
                'keyword3' => array('value' => $msg, 'color'=>'#173177'),
                'keyword4' => array('value' => '按规定日期', 'color'=>'#173177'),
                'keyword5' => array('value' => $fromUserName, 'color'=>'#173177'),
                'remark' => array('value'=>'查看详情', 'color'=>'#173177'),
            );
            $url = arU('/main/track/detail', array('tid' => $tid), 'FULL');
            try {
                $res = arComp('ext.weixin')->sendTemplateMsg($userWechatInfo['openid'], 'cl6kmJ8K4GgD1Dsjy4283cMFZHyd0wG-nTR01mhbvYE', $url, $data);
                return $res;

            } catch (\Exception $e) {
                arComp('list.log')->record(array('errMsg' => $e->getMessage(), $userWechatInfo['openid'], 'cl6kmJ8K4GgD1Dsjy4283cMFZHyd0wG-nTR01mhbvYE', $url, $data), 'wxerr_TplNewTask');
            }
        endif;

    }

}
