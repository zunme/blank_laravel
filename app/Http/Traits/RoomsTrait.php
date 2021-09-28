<?php

namespace App\Http\Traits;

use Carbon\Carbon;
use Illuminate\Support\Arr;

use App\Models\user;
use App\Models\Room;
use App\Models\Waiting;
use App\Models\Seat;
use App\Models\PointLog;
use App\Models\SiteConfig;

use App\Models\LotteryRoom;
use App\Models\LotteryLog;

use App\Models\Virtualpos;

use App\Exceptions\CustomException;
use App\Jobs\ProcessRoom;

trait RoomsTrait {
    protected $entranceTime = 5; //룸입장시간 n분전
    protected $edtranceType = 'room';//room, grade ( 룸별대기, grade별 대기 )

    function __construct() {
        parent::__construct();
    }
    protected function callQue($room_id, $room_no = 0) {
        $job = new ProcessRoom($room_id, $room_no);
        dispatch($job)->onConnection('database');
    }
    //사이트 사용시간 체크
    protected function timeAvailCheck() {
        $cnf = SiteConfig::where(['config_name'=>'use_time'])->first();
        if( $cnf == null) return true;
        if( $cnf->opt == 'N') return true;
        $now = Carbon::now()->format('H:i');
        if ( $cnf->config_val2 > $cnf->config_val ){ // open 08:00, close 23:00
            if( $now >= $cnf->config_val && $now < $cnf->config_val2) return true;
            else return false;
        }else {
            if( $now >= "00:00" && $now < $cnf->config_val2) return true;
            else if ( $now>= $cnf->config_val) return true;
            else return false;
        }
        return true;

    }

    //cron
    protected function roomPrc(){
        $rooms = Room::where('next_game_at','<=',Carbon::now())->orWhereNull('next_game_at')->get();
        
        foreach( $rooms as $room){

            $room->room_status = 'U';
            $room->save();
            dump( "-- room id ".$room->id);
            if( $room->is_use =='Y') {
                $winning_point = ( (int)($room->admission_fee * $room->winnings) ) /100 ;

                for ( $i =1; $i <= $room->num_of_rooms; $i++ )$array[] = $i;

                if( $room->next_game_at == null ) $startAt = Carbon::now();
                else $startAt = Carbon::parse($room->next_game_at);
                
                $nextAt = $startAt->addMinute( $room->interval_min);
                if( Carbon::now() >= $nextAt) {
                    while(true){
                        $nextAt = $nextAt->addMinute( $room->interval_min);
                        if( Carbon::now() < $nextAt) break;
                    }
                }


                $lotteryrooms = LotteryRoom::where(['room_id'=>$room->id, 'game_at'=>$room->next_game_at])->orderBy('room_no')->get();
                foreach ( $lotteryrooms as $lotteryroom){
                    
                    $temp_nums = explode(',', $lotteryroom->lottery_num); 
                    $winning_nums = Arr::random($array, $room->num_of_winners);     
                    
                    for ( $i = 0; $i < $room->num_of_winners; $i++ ){;
                        if( isset($temp_nums[$i]) && (int)$temp_nums[$i] > 0 &&  (int)$temp_nums[$i] <= $room->num_of_rooms ) {
                            if ( !in_array( $temp_nums[$i], $winning_nums ))$winning_nums[$i] = (int)$temp_nums[$i];
                        }
                    }

                    $lotteryroom->lottery_num = implode(',',$winning_nums);
                    $lotteryroom->save();

                    $seats = Seat::where(['room_id'=>$lotteryroom->room_id, 'room_no'=>$lotteryroom->room_no])->get();
                    $cnt_peoples = $seats->count();
                    //입장수가 안채워졌으면 기록 남기고 패스
                    if( $cnt_peoples < $room->member_per_room) {
                        foreach( $seats as $seat){
                            $member = User::where(['id'=>$seat->user_id])->first();
                            LotteryLog ::create([
                                'lottery_room_id'=>$lotteryroom->id
                                ,'pos_no'=>$seat->pos_no
                                ,'user_id'=>$seat->user_id
                                ,'winning_price'=>$winning_point,'is_winner'=>'P'
                            ]); 
                        }
                        //상태기록
                        $lotteryroom->num_of_member = $cnt_peoples;
                        $lotteryroom->lottery_room_status = 'P';
                        $lotteryroom->save();                        
                        continue;
                    }

                    foreach( $seats as $seat){
                        $member = User::where(['id'=>$seat->user_id])->first();

                        if( in_array($seat->pos_no, $winning_nums)) {
                            $member->points = $member->points + $winning_point;
                            $member->save();
                            PointLog::create( ['user_id'=> $member->id, 'code'=>'winning_point', 'use_points'=> $winning_point, 
                            'etc'=>['room_id'=>$seat->room_id,'room_no'=>$seat->room_no,'pos_no'=>$seat->pos_no,'game_at'=>$room->next_game_at]
                            ]);
                            //TODO 상금자 리스트 추가
                            LotteryLog ::create([
                                'lottery_room_id'=>$lotteryroom->id
                                ,'pos_no'=>$seat->pos_no
                                ,'user_id'=>$seat->user_id
                                ,'winning_price'=>$winning_point,'is_winner'=>'Y'
                            ]);
                        }else {
                            LotteryLog ::create([
                                'lottery_room_id'=>$lotteryroom->id
                                ,'pos_no'=>$seat->pos_no
                                ,'user_id'=>$seat->user_id
                                ,'winning_price'=>$winning_point,'is_winner'=>'N'
                            ]);
                        }
                        try{
                            $member->points = $member->points - $room->admission_fee;
                            $member->save();
                        } catch (\Exception $e ){;}

                        PointLog::create( ['user_id'=> $member->id, 'code'=>'admission_fee', 'use_points'=> $room->admission_fee * -1, 
                            'etc'=>['room_id'=>$seat->room_id,'room_no'=>$seat->room_no,'pos_no'=>$seat->pos_no,'game_at'=>$room->next_game_at]
                        ]);
                        
                        //더이상 참가할 비용이 없을경우
                        if( $member->avail < 0 ) {
                            $member->reservation = $member->reservation - $room->admission_fee;
                            $member->save();
                            $seat->delete();
                            $msgData = ['cmd'=>'outRoom','i'=>$lotteryroom->room_id,'u'=>$member->id,'r'=>$lotteryroom->room_no];
                            event(new \App\Events\SendMessage($msgData));                            
                        }
                        //종료 예약일 경우
                        if( $seat->xit_reserv =='Y') $this->leavingWaitingRoom($lotteryroom->room_id, $member->id, $lotteryroom->room_no);
                    }
                    //상태기록
                    $lotteryroom->num_of_member = $cnt_peoples;
                    $lotteryroom->lottery_room_status = 'Y';
                    $lotteryroom->save();

                }
            }

            \DB::beginTransaction();
            try{
                dump( "next : ". $nextAt);
                $room->next_game_at = $nextAt;
                $room->room_status ='N';
                $room->save();
                \DB::commit();
                if( Carbon::now() < $nextAt) {
                    $this->makeNextRoom($room,$nextAt);
                    $msgData = ['cmd'=>'changeTime','i'=>$room->id,'game_at'=>$room->next_game_at];
                    event(new \App\Events\SendMessage($msgData));                    
                }
                
            }catch ( \Exception $e ){
                \DB::rollback();
                dump( $e->getMessage() );
            }
         
            if( $room->is_use =='Y' ){
                for( $i = 1 ; $i <= $room->num_of_rooms; $i ++ ) {
                    $this->callQue( $room->id, $i);
                };
            }
            
        }
    }
    protected function makeNextRoom(Room $room, $nextAt ){
        if( $room->is_use =='Y') {
            for( $i = 1; $i <= $room->num_of_rooms;$i++){
                $data = ['room_id'=>$room->id,'room_no'=>$i, 'game_at'=>$nextAt];
                try{
                    LotteryRoom::create($data);
                }catch ( \Exception $e ){
                    ;
                }
            }
        }
        $this->autoBan( $room );
        return true;
    }

    /* 방 갯수가 줄었을 경우 자동추방 */
    protected function autoBan(Room $room) {
        $seats = Seat::where('room_id','=',$room->id)
        ->where('room_no',">", $room->num_of_rooms)
        ->get();

        foreach ( $seats as $seat){
            $user = User::where(['id'=>$seat->user_id])->first();
            \DB::beginTransaction();
            try{
                if( $user != null){
                    $user->reservation = $user->reservation - $room->admission_fee;
                    $user->save();
                }
                $seat->delete();
                \DB::commit();
                $msgData = ['cmd'=>'outRoom','i'=>$seat->id,'u'=>$user->id,'r'=>$seat->pos_no];
                event(new \App\Events\SendMessage($msgData));
            } catch ( \Exception $e ){
                \DB::rollback(); 
            }
        }

        $seats = Waiting::where('room_id','=',$room->id)
            ->where('room_no',">", $room->num_of_rooms)
            ->get();
        foreach ( $seats as $seat){
            $user = User::where(['id'=>$seat->user_id])->first();
            
            \DB::beginTransaction();
            try{
                if( $user != null){
                    $user->reservation = $user->reservation - $room->admission_fee;
                    $user->save();
                }
                $seat->delete();
                \DB::commit();
                $msgData = ['cmd'=>'outRoom','i'=>$seat->id,'u'=>$user->id,'r'=>$seat->pos_no];
                event(new \App\Events\SendMessage($msgData));
            } catch ( \Exception $e ){
                \DB::rollback(); 
            }
        
        }
    }




    //사용여부 변경시
    protected function changeUse(Room $room){
        $this->makeNextRoom($room,$room->next_game_at);
    }

    protected function roomList($room_id, $type = 'all')
    {
        $rooms = Room::select('rooms.*')
        ->where(['is_use'=>'Y']);
        if( $room_id !='all') $rooms->where( 'rooms.id','=', $room_id);
        return $rooms->get();
    }
    /* 좌석현황
        $type : empty or all - default all
    */
    protected function roomSeatsInfo($room_id='all',$room_no=0, $type='all'){
        $rooms = Room::select('rooms.*','test_rooms.room_no','test_rooms.pos_no','seats.user_id')
        ->leftJoin('test_rooms', function($q) {
            $q->on('rooms.num_of_rooms', '>=', 'test_rooms.room_no')
              ->on('rooms.member_per_room', '>=', "test_rooms.pos_no");
        })
        ->leftJoin('seats', function($q) {
            $q->on('rooms.id' ,'=', 'seats.room_id')
              ->on('test_rooms.room_no' ,'=', 'seats.room_no')
              ->on('test_rooms.pos_no' ,'=', 'seats.pos_no');
        })
        ->where(['is_use'=>'Y']);
        if( $room_id !='all') $rooms->where( 'rooms.id','=', $room_id);
        if( $room_no > 0 ) $rooms->where( 'test_rooms.room_no','=', $room_no);
        if( $type =='empty') $rooms->whereNull( 'seats.user_id');

        return $rooms->get();
    }
    /* 대기실입장
        - 등급별 입장 : room_no = 0
        - 등급->룸 별 입장 : room_no > 0
    */
    protected function entranceWaitingRoom($room_id, $user_id, $room_no = 0){
        if( $this->timeAvailCheck() == false ) throw new CustomException( __("이용가능한 시간이 아닙니다.") );
        $user = User::where(['id'=>$user_id])->first();

        //유저롤체크
        $role = $this->getRoll($user);
        if( !$role['service'] ||  !$role['login'] ) throw new CustomException( __("서비스가 불가능한 회원입니다.") );
        
        $room = Room::where('id',$room_id)->first();
        if( $room->is_use != 'Y') throw new CustomException( __("잘못된 방정보입니다.") );

        if( $this->edtranceType == 'room'){
            if( (int)$room_no < 1) throw new CustomException(__("잘못된 방번호입니다."));
            if( (int)$room_no > $room->num_of_rooms ) throw new CustomException(__("잘못된 방번호입니다."));
        }

        //방검색
        if( $this->edtranceType == 'room' ){
            $waiting_count = Seat::where(['room_id'=>$room_id, 'room_no'=>$room_no,'user_id'=>$user_id])->count();
            if ( $waiting_count > 0) throw new CustomException(__("이미 입장하신 방입니다."));
        }else {
            $waiting_count = Seat::where(['room_id'=>$room_id,'user_id'=>$user_id])->count();
            if ( $room->is_use != 'Y') throw new CustomException(__("이미 입장하신 방입니다."));
        }

        //대기실 검색
        if( $this->edtranceType == 'room' ){
            $waiting_count = Waiting::where(['room_id'=>$room_id, 'room_no'=>$room_no,'user_id'=>$user_id])->count();
        }else {
            $waiting_count = Waiting::where(['room_id'=>$room_id,'user_id'=>$user_id])->count();
        }
        if ( $waiting_count > 0) throw new CustomException(__("이미 입장대기중인 방입니다."));

        
        //입장가능 방 갯수 체크
        //TODO
        $reservation_max = 3;

        $total_cnt = Waiting::where(['room_id'=>$room_id,'user_id'=>$user_id])->count() + Seat::where(['room_id'=>$room_id,'user_id'=>$user_id])->count();
        if( $total_cnt >= $reservation_max ) throw new CustomException(__("입장가능한 방의 갯수를 초과했습니다."));
        
        //차감금액계산
        if($user->avail < $room->admission_fee) throw new CustomException(__("입장료가 부족합니다."));


        $seat_insert = false;

        //우선 대기실 입장으로 변경하기 위해 블럭
        //$seat_insert = $this->entrance($room, $user, $room_id, $room_no);
  
        if( !$seat_insert){
            \DB::beginTransaction();
            try{
              $user->reservation = $user->reservation + $room->admission_fee;
              $user->save();
              $waiting = Waiting::create(['room_id'=>$room_id, 'room_no'=>$room_no, 'user_id'=>$user_id]);
              \DB::commit();
              //add Msg
              $msgData = ['cmd'=>'insReservation','u'=>$user_id,'r'=>$room_no];
              event(new \App\Events\SendMessage($msgData)); 

              //add Que
              $job = new ProcessRoom($room_id, $room_no);
              dispatch($job)->onConnection('database');
            

            } catch ( \Exception $e ){
                \DB::rollback();
                throw new CustomException( $e->getMessage() );
            }
        }
        if( !$seat_insert) return 'waiting';
        else return 'seat';
    }

    /* 입장 prc */
    protected function entrance($room_id, $room_no = 0){
        if( $this->timeAvailCheck() == false ) return;
        $room = Room::where('id',$room_id)->first();
        
        /* TODO 입장가능시간일 경우에만 */
        if( $room->room_status == 'U' ) return;

        if( $room == null ) return;
        if( $room->is_use != 'Y') return;

        $seats = Virtualpos::select('test_persons.id as pos_no', 'seats.user_id')
                ->leftJoin('seats', function($q) use($room_id, $room_no) {
                    $q->on('test_persons.id' ,'=', 'seats.pos_no')
                    ->where('seats.room_id' ,'=', $room_id)
                    ->where('seats.room_no' ,'=', $room_no );
                })
                ->where('test_persons.id', '<=', $room->member_per_room )
                ->get();

        $notInUsers = ['0'];
        $pos = [];
        $needCount = $room->num_of_rooms;
        foreach ( $seats as $seat){
            if( $seat->user_id == null ) $pos[] = $seat->pos_no;
            else {
                $notInUsers[] = $seat->user_id;
                --$needCount;
            }
        }
        if ( $needCount < 1 ) return;

        $reservUsers = Waiting::select('id', 'user_id')
            ->where(['room_id'=>$room_id, 'room_no'=>$room_no])
            ->whereNotIn( 'user_id', $notInUsers )
            ->orderBy('id')
            ->limit($needCount)->get();
        
        foreach ( $reservUsers as $user){
            $newpos = $pos;
            if( count($newpos) > 0 ) {
                foreach( $newpos as $reserv_pos_no_temp){
                    $reserv_pos_no = array_shift($pos);
                    \DB::beginTransaction();
                    try{
                        $seatData = Seat::create(['room_id'=>$room_id, 'room_no'=>$room_no,'pos_no'=>$reserv_pos_no, 'user_id'=>$user->user_id ]);
                        Waiting::where(['id'=>$user->id])->delete();
                        \DB::commit();
                        $msgData = ['cmd'=>'insRoom','i'=>$room_id,'u'=>$user->user_id,'r'=>$room_no,'p'=>$reserv_pos_no];
                        event(new \App\Events\SendMessage($msgData));
                        break;
                      } catch ( \Exception $e ){
                          \DB::rollback();
                      }
                }
            }

        }
        return;
    }

    /*퇴장*/
    protected function leavingWaitingRoom($room_id, $user_id, $room_no=null){
        $user = User::where(['id'=>$user_id])->first();
        if( !$user) throw new CustomException( __("회원을 찾을 수 없습니다.") );
        $room = Room::where('id',$room_id)->first();

        $reserv = Seat::where(['room_id'=>$room_id, 'room_no'=>$room_no,'user_id'=>$user_id])->first();
        if( !$reserv){
            $reserv = Waiting::where(['room_id'=>$room_id, 'room_no'=>$room_no,'user_id'=>$user_id])->first();
        }
        if( !$reserv) throw new CustomException( __("방에서 이미 나오셨습니다.") );

        \DB::beginTransaction();
        try{
            $user->reservation = $user->reservation - $room->admission_fee;
            if( $reserv->getTable() == 'seats'){
                $cancelfee = $user->points - $room->cancellation_fee < 0 ? 0 : $user->points - $room->cancellation_fee;
                $user->points = $cancelfee;
                PointLog::create( ['user_id'=> $user->id, 'code'=>'cancel_fee', 'use_points'=> $room->cancellation_fee * -1, 
                        'etc'=>['room_id'=>$reserv->room_id,'room_no'=>$reserv->room_no,'pos_no'=>$reserv->pos_no]
                    ]);
            }
            $user->save();
            $res=$reserv->delete();
            $msgData = ['cmd'=>'outRoom','i'=>$room_id,'u'=>$user->id,'r'=>$room_no];
            event(new \App\Events\SendMessage($msgData));

            $job = new ProcessRoom($room_id, $room_no);
            dispatch($job)->onConnection('database');
            \DB::commit();
            return true;
        }catch (\Exception $e){
            \DB::rollback();
            return false;
        }
    }
    /*자리잡기*/
    /*게임나가기*/

    protected function getRoll( $user ){
        $level_roll = config('ext.level');
        return $level_roll['level_'.$user->user_level]['roles'];
    }

}