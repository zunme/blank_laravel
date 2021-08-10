<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use App\Models\User;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class RegisterController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Register Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users as well as their
    | validation and creation. By default this controller uses a trait to
    | provide this functionality without requiring any additional code.
    |
    */

    use RegistersUsers;

    /**
     * Where to redirect users after registration.
     *
     * @var string
     */
    protected $redirectTo = RouteServiceProvider::HOME;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest');
    }
    public function showRegistrationForm()
    {
        $langs = config('ext.lang') ;
        return view('auth.register', compact(['langs']));
    }
    /**
     * Get a validator for an incoming registration request.
     *
     * @param  array  $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
    {
        $langs = config('ext.lang') ;
        $langs = implode(',', array_keys($langs));
  
        return Validator::make($data, [
            'name' => ['required', 'string', 'max:255'],
            'user_id' => ['required', 'string', 'min:6','max:20', 'unique:users', "regex:/^[a-zA-Z0-9가-힣]+$/"],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'tel' => ['required','min:10',"regex:/^[0-9-]+$/",'max:20'],
            'national'=>['required','string','min:2','max:2','in:'.$langs],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'recommender'=>['nullable','string','min:5','max:20'],
        ]);
    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param  array  $data
     * @return \App\Models\User
     */
    protected function create(array $data)
    {
        $recommender_id = null;
        while(true){
			//$rcode = md5(uniqid($_POST['email'], true));
			$rcode = substr(base_convert(sha1(uniqid($data['user_id'])), 16, 36), 0, 20);
			$cnt = User::where(['rcmnd_code'=>$rcode])->count();
			if( $cnt < 1) break;
	    }
        if( $data['recommender'] ){
            $recommender = User::where(['user_id'=>$data['recommender']])->first();
            $recommender_id = $recommender->id;
        }
 
        return User::create([
            'user_id'=>$data['user_id'],
            'name' => $data['name'],
            'email' => $data['email'],
            'tel' => $data['tel'],
            'rcmnd_code'=>$rcode,
            'parent_id'=>$recommender_id,
            'recommender'=>$data['recommender'],
            'national'=>$data['national'],
            'tel' => $data['tel'],
            'password' => Hash::make($data['password']),
        ]);
    }
}
