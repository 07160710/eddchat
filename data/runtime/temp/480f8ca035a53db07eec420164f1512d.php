<?php if (!defined('THINK_PATH')) exit(); /*a:1:{s:69:"C:\Users\Administrator\Desktop\chat/app/home\view\chat\home_page.html";i:1500953501;}*/ ?>
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
    <script type="text/javascript" src="__HOME__/main.js"></script>
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
                    <li><a href="<?php echo url('home/chat/login_out'); ?>"><i class="fontico logout"></i>退出登录</a></li>
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
                <?php if(is_array($clients) || $clients instanceof \think\Collection || $clients instanceof \think\Paginator): $i = 0; $__LIST__ = $clients;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$v): $mod = ($i % 2 );++$i;if(or_online($v['user_login']) == 'online'): ?>
                     <li id="<?php echo $v['user_login']; ?>" class="fn-clear" data-id="1"><span><img src="<?php echo $v['avatar']; ?>" width="30" height="30"  alt=""/></span><em><?php echo $v['user_login']; ?></em><small class="<?php echo or_online($v['user_login']); ?>" title="在线"></small></li>
                    <?php endif; endforeach; endif; else: echo "" ;endif; ?>
            </ul>
        </div>
    </div>
</div>
<script type="text/javascript">

</script>
</body>
</html>
