# level 
    config/ext
    
# 어드민 메뉴
    App\Http\ViewComposers\AdminMenuComposer

# laravel echo 

    레디스 설치 apt install php7.4-redis

    event/sendmessage
    test 
        htp://{{url}}/t

    javascript 
        resource/js/laravelecho.js
        채널명 확인 필요

    pm2
        pm2 start roomecho.json
        
# datatable
    https://github.com/yajra/laravel-datatables

# visitor

# query
room_id, room_no 로 빈 번호 찾기
    SELECT 
    1 AS room_no, a.id AS pos_no
    from test_persons a
    LEFT JOIN waiting b ON a.id = b.pos_no AND b.room_id = 1 AND b.room_no = 1
    WHERE a.id <= 5
    AND b.id IS null


