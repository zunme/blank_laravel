<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Validator;

use App\Models\User;
use App\Models\Room;

use App\Exceptions\CustomException;
use App\Http\Traits\RoomsTrait;
use App\Http\Traits\ApiResponser;


class RoomController extends Controller
{
    use RoomsTrait;
    use ApiResponser;

    public function __construct()
    {
        $this->middleware('auth');
    }
    //입장
    public function entrance(Request $request){
        //dd($this->timeAvailCheck());return;
        $user = Auth::user();
        $room_id = 1;
        $room_no = 2;
        try{
            $this->entranceWaitingRoom($room_id, $user->id, $room_no); //방정보, 유저아이디, 방번호
            $data = User::select('points','reservation')->where(['id'=>$user->id])->first();
            return $this->success($data);
        } catch (\Exception $e ){
            return $this->error($e->getMessage(), 422);
        }
    }
    //퇴장
    public function xit(Request $request){
        $user = Auth::user();
        $room_id = 1;
        $room_no = 2;        
        try{
            $ret = $this->leavingWaitingRoom($room_id, $user->id, $room_no); //방정보, 유저아이디, 방번호
            $data = User::select('points','reservation')->where(['id'=>$user->id])->first();

            if( $ret) return $this->success($data);
            else $this->error('ERROR OUUCRED', 500);
        } catch (\Exception $e ){
            return $this->error($e->getMessage(), 422);
        }        
    }
}