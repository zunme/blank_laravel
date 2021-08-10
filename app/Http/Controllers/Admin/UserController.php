<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Validator;

use Yajra\DataTables\Facades\DataTables;
use App\Models\User;


class UserController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request)
    {
        $levels = config('ext.level') ;
        $langs = config('ext.lang') ;
        return view('admin.user', compact(['levels','langs']));
    }
    public function list(Request $request) {
        $data = User::with(['parent','children'])->select('*');
        return Datatables::of($data)
                ->make(true);
    }
   
    public function save(Request $request) {
        $user = User::findOrFail( $request->id);
        try{
            $user->update(['tel'=>$request->tel, 'national'=>$request->national,'user_level'=>$request->user_level]);
            return response()->json(['result'=>'OK','data'=>$user], 200);       
        } catch ( \Exception $e) {
            return response()->json(['errors' => ['system' => ['시스템 오류입니다.'], 'e'=>$e,'data'=>$request->all() ]], 422);
        }
    }







    public function adduser(Request $request) {
        $messages = [
            'email.unique' => '이미 사용중인 이메일입니다.',
            'email.*' => '이메일은 적어주세요.',
            'password.*' => '6자 이상의 패스워드를 적어주세요.',
            'name.*' => '이름을 적어주세요.',
            'isAdmin.*' => '어드민 여부를 선택해주세요',
        ];

        $data = $request->validate([
            'email' => 'bail|required|email|unique:users',
            'password' => 'bail|required|string|min:6',
            'name' => 'bail|required|string|max:50',
            'isAdmin' => 'required|in:Y,N',
        ], $messages);

        if( $data['isAdmin']== 'Y') $data['user_level'] = 1025;
        else $data['user_level'] = 10;

        if( $request->password ) $data['password'] = \Hash::make($data['password']);
        try{
            $user = User::create( $data ); 
            return response()->json(['result'=>'OK','data'=>$user], 200);       
        } catch ( \Exception $e) {
            return response()->json(['errors' => ['system' => ['시스템 오류입니다.'], 'e'=>$e,'data'=>$request->all() ]], 422);
        }

    }
    public function changepwd(Request $request) {
        $messages = [
            'email.*' => '변경할 이메일은 적어주세요.',
            'password.*' => '6자 이상의 패스워드를 적어주세요.',
        ];

        $data = $request->validate([
            'email' => 'bail|required|email',
            'password' => 'bail|required|string|min:6',
        ], $messages);

        $newpassword =  \Hash::make($data['password']);
        $user = User::where('email',$request->email)->first();
        if( $user ){
            try{
                $user->password = $newpassword;
                $user->save();
                return response()->json(['result'=>'OK','data'=>$user], 200);
            }catch ( \Exception $e) {
                return response()->json(['errors' => ['system' => ['시스템 오류입니다.'], 'e'=>$e,'data'=>$request->all() ]], 422);
            }
            
        }else  return response()->json(['errors' => ['system' => ['이메일을 찾을 수 없습니다.'], 'e'=>$e,'data'=>$request->all() ]], 422);
    }
    public function changegrant(Request $request) {
        $messages = [
            'email.*' => '변경할 이메일이 필요합니다.',
            'isAdmin.*' => '어드민 여부를 선택해주세요',
        ];

        $data = $request->validate([
            'email' => 'bail|required|email',
            'isAdmin' => 'required|in:Y,N',
        ], $messages);
        $user = User::where('email',$request->email)->first();

        if( $data['isAdmin']== 'Y') $data['user_level'] = 1025;
        else $data['user_level'] = 10;        

        if( $user ){
            try{
                $user->user_level = $data['user_level'];
                $user->save();
                return response()->json(['result'=>'OK','data'=>$user], 200);
            }catch ( \Exception $e) {
                return response()->json(['errors' => ['system' => ['시스템 오류입니다.'], 'e'=>$e,'data'=>$request->all() ]], 422);
            }
            
        }else  return response()->json(['errors' => ['system' => ['이메일을 찾을 수 없습니다.'], 'e'=>$e,'data'=>$request->all() ]], 422);        
    }
    public function destroy(Request $request){
        $user = User::where('email',$request->email)->first();
        if( $user){
            try{
                $user->delete();
                return response()->json(['result'=>'OK','data'=>$user], 200);
            }catch ( \Exception $e) {
                return response()->json(['errors' => ['system' => ['시스템 오류입니다.'], 'e'=>$e,'data'=>$request->all() ]], 422);
            }
        }else {
            return response()->json(['errors' => ['system' => ['이메일을 찾을 수 없습니다.'], 'e'=>$e,'data'=>$request->all() ]], 422);           
        }
    }
}