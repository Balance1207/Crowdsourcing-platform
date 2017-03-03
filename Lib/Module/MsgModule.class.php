<?php
namespace Lib\Module;
// 消息模块
class MsgModule
{
	// 发送系统消息 arModule('Lib.Msg')->sendSystemMsg($recUid, $content, $url)
	public function sendSystemMsg($recUid, $content, $url = '')
	{
		$msgData = array(
			'receiver' => $recUid,
			'content' => $content,
			'send_time' => time(),
			'url' => $url,
			'type' => \U_messageModel::TYPE_SYSTEM,
			'status' => \U_messageModel::STATUS_NOTREADED,
		);
		return \U_messageModel::model()->getDb()->insert($msgData);

	}

	// 用户发送消息 arModule('Lib.Msg')->sendNormalMsg($sendUid, $recUid, $content)
	public function sendNormalMsg($sendUid, $recUid, $content)
	{
		$msgData = array(
			'sender' => $sendUid,
			'receiver' => $recUid,
			'content' => $content,
			'send_time' => time(),
			'type' => \U_messageModel::TYPE_NORMAL,
			'status' => \U_messageModel::STATUS_NOTREADED,
		);

		return \U_messageModel::model()->getDb()->insert($msgData);

	}

}