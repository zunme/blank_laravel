<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

use Illuminate\Support\Carbon;
//use Illuminate\Support\Facades\Auth;
use Validator;

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
    public function test(Request $request){
        /*
        $this->callQue(1, 1);
        return;
      */
    }
    public function index()
    {
        return view('admin.room');
    }
    public function list()
    {
        $data = Room::select('*');
        return Datatables::of($data)
                ->make(true);
    }
    public function save(Request $request){
        $data = $request->validate([
            'name'=>'required|string|min:2|max:30',
            'num_of_rooms'=>'required|digits_between:1,2',
            'member_per_room'=>'required|digits_between:1,2',
            'num_of_winners'=>'required|digits:1|max:1|lt:member_per_room',
            'admission_fee'=>'required|numeric',
            'cancellation_fee'=>'required|numeric',
            'winnings'=>'required|numeric',
            'marketing_allowance'=>'required|numeric',
            'plan_allowance'=>'required|numeric',
            'interval_min'=>'required|digits_between:1,4',
            'is_use'=>'required|in:Y,N',
            'next_game_at' => 'required|date_format:"Y-m-d H:i"|after_or_equal:'.Carbon::now(),
        ]);
        try{
            $room = Room::updateOrCreate( ['id'=>$request->id], $data );
            $this->changeUse($room);
            
            //$room->update( $data);
            //$room->save();
            return response()->json(['result'=>'OK','data'=>$room], 200); 
        }catch ( \Exception $e ){
            return response()->json(['errors' => ['system' => ['시스템 오류입니다.'], 'e'=>$e,'data'=>$request->all() ]], 422);
        }

    }     
}