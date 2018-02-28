WS_STATUS={
    init:10,
    connect:0,
    open:1,
    error:2,
    close:3,
    authsucc:5,
    authfail:6
}

$(function () {
    $("._debug").height(window.innerHeight);
})
function setWsStatus(status,msg)
{
    if(!msg)  msg='';
    switch (status)
    {
        case WS_STATUS.init:
            html = '<span style="color:green">未初始化</span>';
            console.log(html)
            $('.ws_status').html( html );
            break;
        case WS_STATUS.connect:
            html = '<span style="color:green">正在连接</span>';
            $('.ws_status').html( html );
            break;
        case WS_STATUS.open:
            html = '<span style="color:green">连接成功</span>';
            $('.ws_status').html( html );
            break;
        case WS_STATUS.close:
            html = '<span style="color:black">关闭</span>';
            $('.ws_status').html( html );
            break;
        case WS_STATUS.error:
            html = '<span style="color:red">出错'+msg+'</span>';
            $('.ws_status').html( html );
            break;
        case WS_STATUS.authsucc:
            html = '<span style="color:green">授权成功，开始接收日志。</span>';
            $('.ws_status').html( html );
            break;
        case WS_STATUS.authfail:
            html = '<span style="color:red">授权失败'+msg+'</span>';
            $('.ws_status').html( html );
            break;
        default: console.log('WS_STATUS没有这个状态');
    }
}

setWsStatus(WS_STATUS.init)

//消息组
var chatGroup  = [];
var chatGroupDisplay = [];
var chat = null;
var flag_stop = false;

function WsScoket(host,port,token) {
    this.host = host
    this.port = port
    this.token = token
    this.is_connect = false
    this.connection = null
    this.msg_length = 0;
    var that = this;

    this.connect =  function ()
    {
        var server = 'ws://'+this.host+":"+this.port;
        var ws = new WebSocket(server);
        this.connection = ws;
        var tryIntval = 2000
        console.log('连接到服务器')
        ws.onopen = function () {
            setWsStatus(WS_STATUS.open)
            that.is_connect = true;
            that.send( '/debug/begin' ,[] );
            tryIntval  = 1000;

        },

        ws.onmessage =function (frame) {
            that.onmessage(frame)
        }

        //断线重新连接
        ws.onclose = function () {
            console.log('Ws : onclose')
            setWsStatus(WS_STATUS.close)
            that.is_connect = false
            function tryReConnect() {
                console.log("Status :",that.connection.readyState)
                if(that.connection.readyState!=1) {
                    that.connect()
                }
            }
            setTimeout(tryReConnect,tryIntval);
            tryIntval+= 2000
        }

        ws.onerror = function(response ,errno){
            console.log('Ws : onerror')
            setWsStatus(WS_STATUS.error)
            that.is_connect = false
            //ws.close();
        }

    }

    setInterval(function () {
        if(that.connection.readyState!=1){
            console.log('连接异常'+that.connection.readyState)
            setTimeout(function () {
                if(that.connection.readyState!=1){
                    //that.connect()
                }
            },2000)
        }
    },2000)
}
WsScoket.prototype  = {
    constructor :WsScoket,
    onerror: function () {
        setWsStatus(WS_STATUS.error)
    },

    appendLog:function (frame) {
        console.log('falg_stop: '+debug.flag_stop)
        if(debug.flag_stop){
            return;
        }

        frame.data = frame.data ? frame.data : '';
        if(!filterFrame(frame.data)) {
            console.log( 'filter frame:',frame.data );
            return ;
        }

        if( !chatGroup[frame.group] ){
            chatGroup[frame.group] = [];
            chatGroupDisplay[frame.group] = true;
            var group_key = 'group_list_'+frame.group;
            var str = '<label class="'+group_key+' '+frame.group+'" data-group="'+frame.group+'" for="'+group_key+'"> '+frame.group+' <input type="checkbox" name="'+group_key+'" id="'+group_key+'" checked> </label>';
            $('#group_list').append(str)
            console.log('bind function' , chatGroupDisplay[frame.group])

            $('#'+group_key).change(function () {
                var data_group =  $(this).parent().attr('data-group')
                console.log(data_group , chatGroupDisplay[frame.group])
                if( chatGroupDisplay[frame.group]) {
                    chatGroupDisplay[frame.group] =false
                    $('#logger .'+data_group).hide()
                } else{
                    chatGroupDisplay[frame.group] =true
                    $('#logger .'+data_group).show()
                }

            })
        }



        var  tpl = "<p class='"+frame.group+"'  group='"+frame.group+"'> {0}</p>"  ;

        var  time = new Date();
        var  timestr = time.getHours()+"-"+time.getMinutes()+"-"+time.getSeconds();
        var  rand = Math.floor(Math.random()*10000)
        var  span_id = "log_"+timestr+'-'+time.getMilliseconds()+rand;
        var  tpl2 = "<small>"+timestr + " ["+frame.group+"]"+ " :</small><span id='"+span_id+"'>{0}</span>";

        if(frame.contentType=='json') {
            var tpl = tpl.format(tpl2.format('')) ;
            $('.logger_container').first().prepend( tpl )

            $('#'+span_id).JSONView(frame.data ,{ collapsed: true});

        }else{
            tpl2 =tpl2.format(frame.data);
            $('.logger_container').first().prepend(  tpl.format(tpl2) )
        }
        //
        if(!chatGroupDisplay[frame.group]){
            $('#logger .'+frame.group).hide()
        }

        this.msg_length ++;
        if(this.msg_length>1000){
            $('.logger_container p').last().remove()
        }

        //console.log( str );
        var  container  = $('.logger_container')[0];// console.log(containers);
        $(container).scrollTop( container.scrollHeight-container.offsetHeight +80);
    },
    onSysMessage:function (frame) {
        if(frame.msg == '认证成功') {
            setWsStatus(WS_STATUS.authsucc)
            chat.is_connect = true;
        }else{
            setWsStatus(WS_STATUS.authfail , frame.msg)
            // 重置按钮状态
            $('#btn_start').text('开始').removeClass('stop').addClass('start');
            // 重置接受消息状态
            flag_stop = false;
            //关闭连接
            chat.close();
        }
        return;
    },
    onmessage : function (frame) {
        console.log('Ws :onmessage:')
        frame = JSON.parse(frame.data)
        console.log(frame)
        //if(flag_stop)  return;
        switch (frame.type){
            case 'sys':  this.onSysMessage(frame); break;
            case 'log':  this.appendLog(frame); break;
            default: console.log('未定义的消息类型');
        }
        //
    },
    send:function (url,centent ,type ,group ) {
        var  data = {};
        type  ? data.type  = type : data.type = 'info';
        group ? data.group = type : data.group ='';
        data.access_token  =  this.token
        data.pathinfo= url
        if(typeof centent =="array"||typeof centent == 'object'){
            data.contentType = 'json';
        } else{
            data.contentType = 'text';
        }
        data.data = centent;
        console.log(JSON.stringify( data))
        this.connection.send( JSON.stringify( data) );
    }
}

var  host = document.location.host;
var  port = 82;
var  token = getCookie('SMALLAPI_SES');
var chat = new WsScoket(host,port,token);
chat.connect()

debug = {
    flag_stop: false,
    init :function () {
      $('#btn_start').click(debug.toggle_start_stop)
    },
    toggle_start_stop: function () {
        debug.flag_stop =!debug.flag_stop
        console.log(debug.flag_stop)
        if(debug.flag_stop){
            $('#btn_start').text('开始')
        }else{
            $('#btn_start').text('暂停')
        }
    },

}
debug.init();

// 过滤
function filterFrame(data)
{
    if($('#input_filter_word_flag')[0].checked)
    {
        var filter_word = $('#input_filter_word').val();
        if(data.indexOf(filter_word)!=-1)
        return false
    }
    if($('#input_need_word_flag')[0].checked)
    {
        var need_word = $('#input_need_word').val();
        if(data.indexOf(need_word)==-1)
        return false;
    }
    return true;
}



/***
 * reload
 */
function reload() {
    $.ajax({
        url:'/debug/restart',
        success:function (data) {
            console.log(data)
        }
    })
}
$('.template').hide();

$('#setting_modal_btn').click(function () {
    $.ajax({
        url: '/debug/config',
        success:function (response) {
            console.log(response)
            response = JSON.parse(response)
            if(response.code!=0){
                alert('服务器异常');
            }
            var params = response.data
            $('.new_input').remove();
            for (var key in params) {
                var template  = $('#temp_1').clone()

                template.find('.control-label').text(key);
                template.find('.input').val(params[key]);
                template.find('.input').attr('name',key);
                template.insertBefore('#temp_1')
                template.removeAttr('id')
                template.addClass('new_input')
                template.show()
            }

        }
    })
})

$('#modify_btn').click(function () {
    console.log('modify_btn')
    var inputs = $('#setting_modal').find('form').serialize()
    console.log(inputs)
    $.ajax({
        'url':'/debug/modifyConfig',
        'type':'post',
        'data':inputs,
        'success':function (response) {
            response  = JSON.parse(response)
            console.log(response)
            if(response.code==0){
                alert('修改成功');
            }else{
                alert(response.msg)
            }
        }
    })
})

setInterval(function () {
    $.ajax({
        url: '/debug/sysinfo',
        success:function (response) {
            response = JSON.parse(response)
            if(response.code==0) {
                $('#sys_status table').empty()

                var data = response.data
                var tr = "<tr>";
                for (var index in data.header) {
                    var  field  = data.header[index]
                    tr +="<td>"+field+"</td>"
                }
                tr+="</tr>";
                $('#sys_status table ').append(tr)

                for (var index in data.body) {
                    var  fields  = data.body[index]
                    var tr = "<tr>";
                    for (var ii in fields){
                        tr += "<td>"+fields[ii]+"</td>";
                    }
                    tr+="</tr>";
                    $('#sys_status table ').append(tr)
                }
                $('#sys_status .total ').empty()
                for (var index in data.total) {
                    var  total  = data.total[index]
                    $('#sys_status .total ').append("<span>"+total+"</span><br>")
                }
            }
        }

    })
},2000);

$.ajax({
    url: '/debug/phpinfo',
    success:function (response) {
        response = JSON.parse(response)
        if(response && response.code == 0){
            $('#sys_status .phpinfo').append(response.data)
        }
    }
})



