<?php

namespace App\Http\Controllers;
use Auth;
use App\Models\User;
use App\Models\Order;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use App\Exceptions\HcException;

class ExampleController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
     
        // $this->middleware('locale');
    }
    public function save()
    {

        $result=DB::table('users')->insert(
            ["id" => "1b7161ea8542462dbf21db4ca9e66288",
                'name' => 'sam',
                'email' => 'sam@mail.com',
                'password' => Hash::make("sam1"),
            ]
        );
        echo $result;
    }

    public function test(Request $Request)
    {
       // $user = $Request->user();
        $user = Auth::user();
      //  $mgt_uid = app()->make('CSAuth')->getCS($uid)->mgt_uid;
      //dd($this);
         $la_paras = $this->parse_parameters($Request, __FUNCTION__);
       dd($la_paras);
       $user1 = app()->make('tony0127')->auth0127();
       dd($user1);
       dd($user);
        return response()->json(['name' => $Request->input('tony'), 'state' => 'CA']);
    }

    public function userinfo(Request $Request)
    {
      error_log('Some message here.');
      return User::all();
    }

    public function create_order(Request $Request)

    {
		 $la_paras = $Request->json()->all();
        $file_path1 = base_path('tmp/trace.log');
        error_log($la_paras,3,$file_path1);
        //print_r($Request->input());
     
         /*   $this->validate($Request, [
                '*.user_id' => 'required',
                '*.order_number' => 'required|unique:orders,order_number|max:12|distinct'
            ]);*/
        
            $this->validate($Request, [
                '*.user_id' => 'required',
                '*.order_number' => 'required|unique:orders,order_number|max:12|distinct'
            ]);
        
     // return $Request->input();
     print_r ($Request->input());
     dd(123);
     $order=new Order();
     $order->order_number=$Request->input('order_number');
     $order->user_id=$Request->input('user_id');
     $status = $order->save();
     echo $status;
    }
    //
}
