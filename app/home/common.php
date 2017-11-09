<?php
use GatewayClient\Gateway;
use think\cache\driver\Memcache;

function or_online($client_name)
{
    vendor('gateway.gatewayclient.Gateway');
    Gateway::$registerAddress = '127.0.0.1:1238';
    $mem = new Memcache();
    if ($mem->has($client_name)) {
        $client_id = $mem->get($client_name);
        if (Gateway::isOnline($client_id)) {
            return 'online';
        }else{
            return 'offline';
        }
    }else{
        return 'offline';
    }

}

//保存聊天记录
function save_db_msg($from_id,$to_id,$msg)
{
    $map = [
        'mess_date'=>time(),
        'from_client_id'=>$from_id,
        'to_client_id'=>$to_id,
        'content'=>$msg,
    ];
    get_today_msg($map);

    \think\Db::name('mess')->insert($map);
}

//获取当天的聊天记录
function get_today_msg($new_mess='')
{
    $today= $today = strtotime(date('Y-m-d'));
    $tonight = strtotime(date('Y-m-d') )+ 86400;
    $mem = new Memcache();
    if ($mem->has('tonight')) {
        $tonightCache = $mem->get('tonight');
        $todayCache = $mem->get('today');
        $is_today = $tonightCache >= time()&&$todayCache<=time();
        if ($is_today) {
            $messages=$mem->get('allMess');
            if (!empty($new_mess)) {
                array_push($messages, $new_mess);
                $mem->set('allMess', $messages);
            }
        }else{
            $mem->set('today',$today);
            $mem->set('tonight',$tonight);
            $messages=\think\Db::name('mess')
                ->where('mess_date', ['>', $today], ['<', $tonight], 'and')
                ->order('mess_date asc')
                ->select();
            $mem->set('allMess', $messages);
        }
    }else{
        $mem->set('today',$today);
        $mem->set('tonight',$tonight);
        $messages=\think\Db::name('mess')
            ->where('mess_date', ['>', $today], ['<', $tonight], 'and')
            ->order('mess_date asc')
            ->select();
        $mem->set('allMess', $messages);
    }

    return $messages;
}