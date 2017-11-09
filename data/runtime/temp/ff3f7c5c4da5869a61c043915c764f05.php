<?php if (!defined('THINK_PATH')) exit(); /*a:1:{s:59:"C:\web\eddchat_wrkb2y\web/app/home\view\chat\home_page.html";i:1501481993;}*/ ?>
<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title>EddChat_欧麦聊天室</title>
    <link rel="shortcut icon" href="favicon.png">
    <link rel="icon" href="favicon.png" type="image/x-icon">
    <link type="text/css" rel="stylesheet" href="__HOME__/chat/css/style.css">
    <script type="text/javascript" src="__HOME__/chat/js/jquery.min.js"></script>
    <script src="__ADMIN__/plugins/layer-v3.0.1/layer/layer.js"></script>
</head>

<body>
<div class="chatbox">
    <div class="chat_top fn-clear">
        <div class="logo"><img src="__HOME__/chat/images/logo.png" width="190" height="60"  alt=""/></div>
        <div class="uinfo fn-clear">
            <div class="uface"><img src="<?php echo $client_img; ?>" width="40" height="40"  alt=""/></div>
            <div class="uname">
                <?php echo $client_name; ?></span><i class="fontico down"></i>
                <ul class="managerbox">
                    <li><a href="#" onclick="send_server()"><i class="fontico"></i>开启广播</a></li>
                    <li><a href="<?php echo url('home/Chat/login_out'); ?>" ><i class="fontico logout"></i>退出登录</a></li>
                </ul>
            </div>
        </div>
    </div>
    <div class="chat_message fn-clear">
        <div class="chat_left">
            <div class="message_box" id="message_box">
                <?php if(is_array($today_mess) || $today_mess instanceof \think\Collection || $today_mess instanceof \think\Paginator): $i = 0; $__LIST__ = $today_mess;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$v): $mod = ($i % 2 );++$i;if($client_id == $v['from_client_id']): ?>
                        <div class="msg_item fn-clear">
                            <div class="uface ufaceright"><img src="<?php echo $client_img; ?>" width="40" height="40"  alt=""/></div>
                            <div class="item_right item_rightright">
                                <div class="msgright own"><?php echo htmlentities($v['content']); ?></div>
                                <div class="name_timeright"><?php echo date('Y-m-d H:i:s',$v['mess_date']); ?>.<?php echo $client_name; ?></div>
                            </div>
                        </div>
                    <?php else: ?>
                        <div class="msg_item fn-clear">
                         <div class="uface"><img src="<?php echo get_users($v['from_client_id'])['avatar']; ?>" width="40" height="40"/></div>
                        <div class="item_right">
                             <div class="msg own"><?php echo htmlentities($v['content']); ?></div>
                                <div class="name_time"><?php echo get_users($v['from_client_id'])['user_login']; ?>.<?php echo date('Y-m-d H:i:s',$v['mess_date']); ?></div>
                            </div>
                        </div>
                    <?php endif; endforeach; endif; else: echo "" ;endif; ?>
            </div>
            <div class="write_box">
                <textarea id="message" name="message" class="write_area" placeholder="说点啥吧..."></textarea>
                <div class="facebox fn-clear">
                    <div class="expression"></div>
                    <div class="chat_type" id="chat_type">群聊</div>
                    <button name="" class="sub_but">提 交 &nbsp ENTER</button>
                </div>
            </div>
        </div>
        <div class="chat_right">
            <ul class="user_list" title="">
                <li class="fn-clear selected"><em>所有用户</em></li>
                <?php if(is_array($clients) || $clients instanceof \think\Collection || $clients instanceof \think\Paginator): $i = 0; $__LIST__ = $clients;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$v): $mod = ($i % 2 );++$i;if(or_online($v['user_login']) == 'online'): ?>
                     <li title="<?php echo $v['user_login']; ?>" id="<?php echo $v['user_login']; ?>" class="fn-clear" data-id="1"><span><img src="<?php echo $v['avatar']; ?>" width="30" height="30"  alt=""/></span><em><?php echo $v['user_login']; ?></em><small class="<?php echo or_online($v['user_login']); ?>" title="在线"></small></li>
                    <?php endif; endforeach; endif; else: echo "" ;endif; ?>
            </ul>
        </div>
    </div>
</div>
<script type="text/javascript">
    var fromname = $('#fromname').val();

    $(document).ready(function(e) {
        $('#message_box').scrollTop($("#message_box")[0].scrollHeight + 20);
        $('.uname').hover(
            function(){
                $('.managerbox').stop(true, true).slideDown(100);
            },
            function(){
                $('.managerbox').stop(true, true).slideUp(100);
            }
        );

        window.onkeydown = function (e) {
            if (e.keyCode == 13) {
                sub();
                return false;
            }
        };

        $('.sub_but').click(function(){
            sub();
        });
    });
    function sub(){
        var msg = $("#message").val();
        $.post("<?php echo url('home/chat/send_message'); ?>", {
            self_name: name,
            msg:msg ,
            type:'all'
        }, function (res) {
            if(res.code) {
                ws.send('hi');
                sendMessageRight(msg, name, res.msg,img);
                $("#message").val('');
            }else{
                layer.msg(res.msg);
            }
        },'json');
    }


    var name;
    ws = new WebSocket("ws://115.28.215.160:8282");

    ws.onopen = function () {
        name = "<?php echo $client_name; ?>";
        img="<?php echo $client_img; ?>"
    };
    // 服务端主动推送消息时会触发这里的onmessage
    ws.onmessage = function(e){
        console.log(e);
        // json数据转换成js对象
        var data;
        var type;
        if(isJSON(e.data)){
            data = JSON.parse(e.data);
            type=data.type;
        }
        switch(type){
            // Events.php中返回的init类型的消息，将client_id发给后台进行uid绑定
                //当前客户初始化绑定
            case 'init':
                $.post("<?php echo url('home/chat/bind'); ?>", {
                    client_id: data.client_id,
                    client_name:name
                }, function(res){
                    if(res.code) {
                        add_client(name + '(我)',img);
                        layer.msg(res.msg)
                    }
                }, 'json');
                break;
                //用户接入触发
            case 'add_client':
                layer.msg(data.new_client + '进入了聊天室');
                add_client(data.new_client,data.new_img);
                break;
                //用户断开触发
            case 'close':
                $.post("<?php echo url('home/chat/find_client_name'); ?>", {
                    client_id: data.client_id,
                }, function(res){
                    layer.msg(res+'离开了聊天室');
                    $('#' + res).remove();
                }, 'json');
                break;
                //发送消息触发
            default :
                if(isMinStatus()) {
                    is_window_msg(data.from_client_name, data.from_client_img, data.msg);
                }
                sendMessage(data.msg, data.from_client_name, data.date,data.from_client_img);
        }
    };

    //开启关闭广播
    function send_server() {
        ws.send('hi');
    }
    //对方发来消息显示
    function sendMessage(msg, from_name, time,img){
        var htmlData = '<div class="msg_item fn-clear">'
            + '   <div class="uface"><img src="'+img+'" width="40" height="40"  alt=""/></div>'
            + '   <div class="item_right">'
            + '     <div class="msg own">' + msg + '</div>'
            + '     <div class="name_time">' + from_name + '.'+time+'</div>'
            + '   </div>'
            + '</div>';
        $("#message_box").append(htmlData);
        $('#message_box').scrollTop($("#message_box")[0].scrollHeight + 20);
    }

    //我方发来消息显示
    function sendMessageRight(msg, from_name, time,img){
        var htmlData = '<div class="msg_item fn-clear">'
            + '   <div class="uface ufaceright"><img src="'+img+'" width="40" height="40"  alt=""/></div>'
            + '   <div class="item_right item_rightright">'
            + '     <div class="msgright own">' + msg + '</div>'
            + '     <div class="name_timeright">' + time+ '.'+from_name +'</div>'
            + '   </div>'
            + '</div>';
        $("#message_box").append(htmlData);
        $('#message_box').scrollTop($("#message_box")[0].scrollHeight + 20);
    }

    //添加用户
    function add_client(new_client,img) {
        new_client = '<li id="'+new_client+'" class="fn-clear" data-id="1"><span><img src="'+img+'"" width="30" height="30"  alt=""/></span><em>' + new_client + '</em><small  class="online" title="在线"></small></li>';
        $('.user_list').append(new_client);
    }
    function isJSON(str) {
        if (typeof str == 'string') {
            try {
                JSON.parse(str);
                return true;
            } catch(e) {
                console.log(e);
                return false;
            }
        }
        console.log('It is not a string!')
    }

    function is_window_msg($client_name,$client_img,$msg) {
        if (!("Notification" in window)) {
            alert("不支持 notification");
        } else if (Notification.permission === "granted") { // 允许通知
            notice($client_name,$client_img,$msg)
        }else if (Notification.permission !== 'denied') { // 用户没有选择是否显示通知，向用户请求许可
            Notification.requestPermission(function(permission) {
                if (permission === "granted") {
                    notice($client_name,$client_img,$msg)
                }
            });
        }
    }
    function notice($client_name,$client_img,$msg) {
        var notification = new Notification($msg,{
            body:$client_name,
            icon:$client_img
        });
        notification.onclick = function(){
            notification.close()
        }
    }

    function isMinStatus() {
        var isMin = false;
        if (window.outerWidth != undefined) {
            isMin = window.outerWidth <= 160 && window.outerHeight <= 27;
        }
        else {
            isMin = window.screenTop < -30000 && window.screenLeft < -30000;
        }
        return isMin;
    }
</script>
</body>
</html>
