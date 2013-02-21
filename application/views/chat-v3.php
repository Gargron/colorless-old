<!DOCTYPE html>
    <html>
        <head>
            <title>The Colorless: Chat (Dollars/Durarara!!) V3</title>
            <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
            <script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.5.1/jquery.min.js"></script>
            <script type="text/javascript" src="http://thecolorless.net:90/socket.io/socket.io.js"></script>
            <script type="text/javascript" src="/js/plugins/json2.js"></script>
            <script type="text/javascript" src="/js/plugins/jquery.backgroundPosition.js"></script>
            <script type="text/javascript">
            var client_id = <?php echo ($sid ? "'$sid'" : "null"); ?>;
            </script>
            <script type="text/javascript" src="/js/aspects/chat-v3.js"></script>
            <link rel="stylesheet" type="text/css" href="/css/reset.css" />
            <style type="text/css">
body {
    background: #111112;
    color: #aaa;
    text-shadow: #000 0 0 0;
    font: normal normal 400 13px/18px "Helvetica Neue", Helvetica, Arial, sans-serif;
}

header {
    width: 100%;
    display: block;
    background: #ededed url('/images/rainbow_glitter.jpg') repeat-x;
    background-position: 0 -280px;
    border: 1px solid #000;
    border-left: 0;
    border-right: 0;
    box-shadow: inset 0 1px 1px 0 #fff, 0 10px 20px #000;
    position: relative;
    z-index: 2;
}

header nav {
    display: block;
    width: 620px;
    margin: 0 auto;
    padding: 0 20px;
    background: #444445;
    border: 1px solid #111;
    border-top: 0;
    border-bottom-left-radius: 5px;
    border-bottom-right-radius: 5px;
    box-shadow: inset 0 1px 1px #555, 0 0 5px #aaa;
}

header nav>ul:after {
    content: ' ';
    display: block;
    clear: both;
}

header nav>ul>li {
    float: left;
    padding: 5px 0;
    margin-right: 10px;
    font-size: 13px;
    font-weight: bold;
    color: #ddd;
}

header nav>ul>li a {
    color: #ddd;
    text-decoration: none;
    display: block;
    padding: 4px 0;
}

header nav>ul>li a:hover {
    color: #fff;
    text-decoration: underline;
}

#chat_name {
    display: inline-block;
    border: 1px solid #111;
    background: #ddd;
    border-radius: 3px;
    padding: 3px;
    color: #000;
    box-shadow: inset 0 -1px 5px #eee, inset 0 2px 5px #aaa;
}

#chat_color {
    display: block;
    background: #222;
    color: #fff;
    cursor: pointer;
    text-shadow: none;
    border-radius: 3px;
    overflow: hidden;
    position: absolute;
    width: 120px;
    top: 7px;
    z-index: 3;
    font-weight: normal;
}

#chat_color li {
    padding: 3px 5px;
    display: none;
    background: #222;
    text-align: left;
}

#chat_color li:last-child {
    border-top-left-radius: 0px;
    border-top-right-radius: 0px;
    border-bottom-right-radius: 3px;
    border-bottom-left-radius: 3px;
}

#chat_color:hover li {
    display: block;
}

#chat_color li:hover {
    background-color: #333;
}

#chat_color #active_color {
    display: block;
    padding-right: 20px;
    background: url("http://imgur.com/EcmWl.png") no-repeat center right;
}

#active_color span {
    font-weight: bold;
}

form#chat_form {
    display: block;
    width: 620px;
    margin: 0 auto;
    padding: 20px;
}

form#chat_form ul li:not(:last-child) {
    margin-bottom: 10px;
}

form#chat_form ul li input#chat_input {
    display: block;
    width: 100%;
    font-size: 16px;
    border: 2px solid #111;
    padding: 3px;
    border-radius: 3px;
    box-sizing: border-box;
    font-weight: bold;
    color: #555;
    box-shadow: inset 0 2px 5px #ddd;
}

form#chat_form ul li button {
    display: block;
    margin: 0 auto;
    position: relative;
    width: 100px;
    border: 2px solid #111;
    text-transform: uppercase;
    font-weight: bold;
    font-size: 16px;
    background: #ddd;
    text-align: center;
    border-radius: 5px;
    box-shadow: inset 0 10px 0 #fff;
}

form#chat_form ul li button:active {
    top: 1px;
    background: #bbb;
    box-shadow: inset 0 10px 0 #ddd, inset 0 2px 5px #aaa;
}

article#chat_wrap {
    display: block;
    width: 100%;
    height: 500px;
    overflow: hidden;
    overflow-y: scroll;
}

ul#chat_area {
    width: 620px;
    padding: 20px 0;
    margin: 0 auto;
}

ul#chat_area li {
    display: block;
    clear: both;
    padding: 0 20px;
    overflow: hidden;
    *zoom: 1;
    margin-bottom: 20px;
}

ul#chat_area li .item_meta, ul#chat_area li .item_bubble {
    display: inline;
}

ul#chat_area li.item_action {
    color: #fff;
}

ul#chat_area li.item_action .item_meta:before, ul#chat_area li.item_status .item_bubble:before {
    content: '\2013\00A0\2013\00A0';
}

ul#chat_area li.item_action .item_meta img {
    display: none;
}

ul#chat_area li.item_message .item_meta {
    display: block;
    float: left;
    width: 80px;
    overflow: hidden;
}

ul#chat_area li.item_message .item_meta img {
    border: 2px solid #fff;
    display: block;
    margin-bottom: 5px;
}

ul#chat_area li.item_message .item_meta .name {
    display: block;
    width: 80px;
    white-space: nowrap;
    overflow: hidden;
    font-weight: normal;
    text-overflow: ellipsis;
}

ul#chat_area li.item_message .item_bubble {
    display: block;
    float: left;
    color: #fff;
    max-width: 474px;
    line-height: 24px;
    min-height: 24px;
    font-weight: normal;
    background: #222223;
    border: 2px solid #fff;
    border-radius: 16px;
    padding: 8px 10px;
    box-shadow: inset 0 1px 0 rgba(0, 0, 0, 0.5), inset 0 2px 0 rgba(150, 150, 150, 0.8), inset 0 10px 20px rgba(0, 0, 0, 0.2);
    text-shadow: rgba(0, 0, 0, 0.3) 1px 1px 1px;
    position: relative;
}

ul#chat_area li.item_message .item_bubble:before {
    display: block;
    content: ' ';
    width: 0;
    height: 0;
    border-top: 6px solid transparent;
    border-bottom: 6px solid transparent;
    border-right: 12px solid #fff;
    position: absolute;
    top: 12px;
    left: -12px;
}

ul#chat_area li.item_message .item_bubble:after {
    display: block;
    content: ' ';
    width: 0;
    height: 0;
    border-top: 4px solid transparent;
    border-bottom: 4px solid transparent;
    border-right: 10px solid #222223;
    position: absolute;
    top: 14px;
    left: -10px;
}
            </style>
            <style id="colors"></style>
        </head>
        <body>
            <header>
                <nav>
                    <ul>
                        <li>
                            <input type="text" id="chat_name" placeholder="Name:" maxlength="30" <?php echo (!empty($this->user->name) ? 'value="'.$this->user->name.'" ' : NULL); ?>/>
                        </li>
                        <li style="position:relative;width:120px">
                            <ul id="chat_color">
                                <li id="active_color">Color: <span>Black</span></li>
                                <li data-value="black">Black</li>
                                <li data-value="purple">Purple</li>
                                <li data-value="lime_green">Lime green</li>
                                <li data-value="darkblue">Dark Blue</li>
                                <li data-value="orange">Orange</li>
                                <li data-value="blue">Blue</li>
                                <li data-value="red">Red</li>
                                <li data-value="magenta">Magenta</li>
                                <li data-value="green">Green</li>
                                <li data-value="grey">Grey</li>
                                <li data-value="mud_green">Mud Green</li>
                                <li data-value="gravatar">Gravatar</li>
                            </ul>
                        </li>
                        <li>
                            <a href="javascript:;">Chatlog</a>
                        </li>
                        <li>
                            <a href="javascript:;">
                                <span id="chat_online_count">0</span>
                                people online
                            </a>
                        </li>
                    </ul>
                </nav>
                <form action="/" method="post" id="chat_form">
                    <ul>
                        <li>
                            <input type="text" autocomplete="off" id="chat_input" maxlength="300" />
                        </li>
                        <li>
                            <button type="submit">Post!</button>
                        </li>
                    </ul>
                </form>
            </header>
            <article id="chat_wrap">
                <ul id="chat_area">
                    <!-- Hi -->
                </ul>
            </article>
            <div style="visibility:hidden">
                <audio id="drama" src="/images/drama.mp3" type="audio/mp3" />
            </div>
        </body>
    </html>