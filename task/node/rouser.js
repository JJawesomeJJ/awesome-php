'use strict'
var ws=require("ws");
var websocekt=new ws("ws://127.0.0.1:9502");
websocekt.on("open",function () {
    websocekt.send(JSON.stringify({
            'message':"handle_task",
            'type': 'chat',
            'message_type': 'text',
            'source': "system",
            'to':"server",
            "check_code":"13036591969"
        })
    );
    websocekt.close();
    process.exit();
});//server websocket start try to connect php_swoole_websocket_server as admin it will send a message to tell server a new task you should handle
websocekt.on("error",function (err) {
    console.log(err);
});
websocekt.on("close",function () {
    console.log("admin_user_has_been_closed");
});
websocekt.on("message",function (data) {

});

