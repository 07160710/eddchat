<?php if (!defined('THINK_PATH')) exit(); /*a:1:{s:57:"C:\code_edd\201707\chat/app/home\view\chat\home_page.html";i:1500910517;}*/ ?>
<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title>EddChat_畅想吧</title>
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
            <div class="uface"><img src="__HOME__/chat/images/hetu.jpg" width="40" height="40"  alt=""/></div>
            <div class="uname">
                <span id="self">加载中...</span><i class="fontico down"></i>
                <ul class="managerbox">
                    <!--<li><a href="#"><i class="fontico lock"></i>修改密码</a></li>-->
                    <li><a href="#"><i class="fontico logout"></i>退出登录</a></li>
                </ul>
            </div>
        </div>
    </div>
    <div class="chat_message fn-clear">
        <div class="chat_left">
            <div class="message_box" id="message_box">

            </div>
            <div class="write_box">
                <textarea id="message" name="message" class="write_area" placeholder="说点啥吧..."></textarea>
                <input type="hidden" name="fromname" id="fromname" value="河图" />
                <input type="hidden" name="to_uid" id="to_uid" value="0">
                <div class="facebox fn-clear">
                    <div class="expression"></div>
                    <div class="chat_type" id="chat_type">群聊</div>
                    <button name="" class="sub_but">提 交</button>
                </div>
            </div>
        </div>
        <div class="chat_right">
            <ul class="user_list" title="双击用户私聊">
                <li class="fn-clear selected"><em>所有用户</em></li>
                <?php if(is_array($clients) || $clients instanceof \think\Collection || $clients instanceof \think\Paginator): $i = 0; $__LIST__ = $clients;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$v): $mod = ($i % 2 );++$i;if(or_online($v['client_name']) == 'online'): ?>
                     <li class="fn-clear" data-id="1"><span><img src="__HOME__/chat/images/hetu.jpg" width="30" height="30"  alt=""/></span><em><?php echo $v['client_name']; ?></em><small id="<?php echo $v['client_name']; ?>" class="<?php echo or_online($v['client_name']); ?>" title="在线"></small></li>
                    <?php endif; endforeach; endif; else: echo "" ;endif; ?>
            </ul>
        </div>
    </div>
</div>
<script type="text/javascript">
    var fromname = $('#fromname').val();
    var to_uid   = 0; // 默认为0,表示发送给所有用户
    var to_uname = '';

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


        $('.sub_but').click(function(){
            var msg = $("#message").val();
            $.post("<?php echo url('home/chat/send_message'); ?>", {
                self: name,
                msg:msg ,
                type:'all'
            }, function (res) {
                if(res.code) {
                    sendMessage(msg, name, res.msg);
                }else{
                    layer.msg(res.msg);
                }
            },'json');
        });
    });

    var name;
    ws = new WebSocket("ws://127.0.0.1:8282");
    ws.onopen = function () {
        name = prompt('请输入你的昵称');
        $('#self').html(name);
    };
    // 服务端主动推送消息时会触发这里的onmessage
    var new_client;
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
                        add_client(name + '(我)');
                    }else{
                        layer.msg('该用户已经注册过');
                    }
                }, 'json');
                break;
                //用户接入触发
            case 'add_client':
                add_client(data.new_client);
                break;
                //用户断开触发
            case 'close':
                $.post("<?php echo url('home/chat/find_client_name'); ?>", {
                    client_id: data.client_id,
                }, function(res){
                    $('#' + res).attr('class', 'offline');
                }, 'json');
                break;
                //发送消息触发
            default :
                sendMessage(data.msg, data.from_client_name, data.date);
        }
    };


    function sendMessage(msg, from_name, time){
        var htmlData =   '<div class="msg_item fn-clear">'
            + '   <div class="uface"><img src="__HOME__/chat/images/53f442834079a.jpg" width="40" height="40"  alt=""/></div>'
            + '   <div class="item_right">'
            + '     <div class="msg own">' + msg + '</div>'
            + '     <div class="name_time">' + from_name + '.'+time+'</div>'
            + '   </div>'
            + '</div>';
        $("#message_box").append(htmlData);
        $('#message_box').scrollTop($("#message_box")[0].scrollHeight + 20);
        $("#message").val('');
    }

    //添加用户
    function add_client(new_client) {
        new_client = '<li class="fn-clear" data-id="1"><span><img src="__HOME__/chat/images/hetu.jpg" width="30" height="30"  alt=""/></span><em>' + new_client + '</em><small id="'+new_client+'" class="online" title="在线"></small></li>';
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
</script>
</body>
</html>
