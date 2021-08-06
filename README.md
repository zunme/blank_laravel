# 어드민 메뉴
App\Http\ViewComposers\AdminMenuComposer

#laravel echo 

레디스 설치 apt install php7.4-redis

event/sendmessage
test 
    -- /t

채널명 확인 필요


<script>
    window.laravel_echo_port='{{env("LARAVEL_ECHO_PORT")}}';
</script>

<script src="//{{ Request::getHost() }}:{{env('LARAVEL_ECHO_PORT')}}/socket.io/socket.io.js"></script>
<script src="{{ asset('js/laravelecho.js') }}" defer></script>
    
<script type="text/javascript">
document.addEventListener("DOMContentLoaded", function(){
    var i = 0;
    //App\Events\SendMessage에 정의되어있는 수신받을 채널명.
    window.Echo.channel('room_database_roominfo')
    //수신받을 클래스 명.
        .listen('SendMessage', (data) => {
        i++;
        $("#notification").append('<div class="alert alert-success">'+i+'.'+data.title+'</div>');
        console.log(data);
    });
});
</script>