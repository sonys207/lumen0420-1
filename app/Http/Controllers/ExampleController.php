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
    
    public function getsbm(Request $Request)
    {
    

        $la_paras = $Request->json()->all();
        file_put_contents("php://stdout", '20220503:receive message action start '.$la_paras['messagea_count']."\r\n"); 
        
        //$la_paras['messagea_count']获取service bus queue中active message的数量，由此决定进行多少次的循环-
        //当logic app调用此接口时，access token拿一次就好

        //get token
        $postData = array (
            'client_id' => 'cd2dad89-5fd5-48a3-9228-84db77502b04',
            'client_secret' => 'VMT8Q~kp.ygb3OzUJsn2bQoJMQIytZLE1Q2M4cYV',
            'grant_type' => 'client_credentials',
            'resource' => 'https://servicebus.azure.net'
        );
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL,"https://login.microsoftonline.com/2422ca93-d116-466c-b852-1e25f6301034/oauth2/token");
        //本例$access_token从curl_exec($ch)的返回值中获得！！！
        //需要设置curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1)！！！
        //否则curl的结果直接输出到屏幕上，curl_exec($ch)的返回值是true，而不是数组！！！
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $postData); 
        $json_response_data = curl_exec($ch);
        curl_close($ch);
        //dd($json_response_data);
       // print_r(json_decode($json_response_data, true));
        $access_token=json_decode($json_response_data, true)['access_token'];
       // print_r($access_token);
       file_put_contents("php://stdout", '20220503:receive token is '.$access_token."\r\n"); 


        //receive peek-lock message from service bus with token
        $cURL = curl_init();
        $header=array(
             'Authorization:bearer '.$access_token,
             'Content-Length: 1000'
         );
       
        curl_setopt($cURL, CURLOPT_URL, "https://tie0502.servicebus.windows.net/magentoq/messages/head");
        curl_setopt($cURL, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($cURL, CURLOPT_HTTPHEADER, $header); 
        curl_setopt($cURL, CURLOPT_HEADER, 1);
        curl_setopt($cURL, CURLOPT_POST, true);
        $json_response_data1 = curl_exec($cURL);
        
        $info = curl_getinfo($cURL);
        $header_size = curl_getinfo($cURL, CURLINFO_HEADER_SIZE);
        curl_close($cURL);
       // dd($json_response_data1);
        file_put_contents("php://stdout", $json_response_data1);
        
    //对包含header和body的结果进行数据格式化处理---开始
        //拆分出response中body的信息
        $body = substr($json_response_data1, $header_size);
        $body_array=json_decode($body);
        // $body_array的类型是object，获取键对应值的方式$body_array->value！！！
        //dd(gettype($body_array),$body_array->value);
        
        //拆分出response中header的信息
        $header = substr($json_response_data1, 0, $header_size);
        $header_array = preg_split('/(\r?\n)/', $header);
        //获取包含“BrokerProperties”字符串的数组元素所在位置，结果为7
        //BrokerProperties中包含了MessageId、lockToken
        $j=0;
        for ($i = 0; $i < count($header_array); ++$i) {
            
            if(strpos($header_array[$i],'BrokerProperties')!== false){ 
                $j=$i;
                break; 
               }
        }
        //把数组中的7号元素通过":"拆分成2个元素---第2个元素为json，可以decode后获取某个键对应的值
        $BrokerProperties=explode(":",$header_array[$j],2);
        $BrokerProperties_Array=json_decode($BrokerProperties[1],true);
        $BrokerProperties_Array_MessageId=$BrokerProperties_Array['MessageId'];
        $BrokerProperties_Array_LockToken=$BrokerProperties_Array['LockToken'];
        file_put_contents("php://stdout", "The MessageId is ".$BrokerProperties_Array_MessageId."\r\n");
        file_put_contents("php://stdout", "The LockToken is ".$BrokerProperties_Array_LockToken."\r\n");
    //对包含header和body的结果进行数据格式化处理---结束

        //echo "<pre>";//输出换行，等同于键盘ctrl+u
       // print_r("The sending message is ".json_decode($json_response_data1, true)['value']);
        file_put_contents("php://stdout", "The sending message response code is ".$info['http_code']."\r\n");
        //print_r("The sending message response code is ".$info['http_code']); 
        return 123;
    
    }


    public function sendsbm(Request $Request)
    {
        //get token
        $postData = array (
            'client_id' => '1abda0fc-cc2d-4c44-8518-4d856e8d7034',
            'client_secret' => 'di88Q~FZ5c2.0YEwfGbq7HmuMtk4RAnHSTLOrbiy',
            'grant_type' => 'client_credentials',
            'resource' => 'https://servicebus.azure.net'
        );
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL,"https://login.microsoftonline.com/2422ca93-d116-466c-b852-1e25f6301034/oauth2/token");
        //本例$access_token从curl_exec($ch)的返回值中获得！！！
        //需要设置curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1)！！！
        //否则curl的结果直接输出到屏幕上，curl_exec($ch)的返回值是true，而不是数组！！！
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $postData); 
        $json_response_data = curl_exec($ch);
        curl_close($ch);
        //dd($json_response_data);
       // print_r(json_decode($json_response_data, true));
        $access_token=json_decode($json_response_data, true)['access_token'];
        print_r($access_token);


        //send message to service bus with token
        $cURL = curl_init();
        $header=array(
             'Content-Type:application/atom+xml;type=entry;charset=utf-8',
             'Authorization:bearer '.$access_token,
           //  'BrokerProperties:{"Label":"M22","State":"Active","TimeToLive":3600}'
         );
         //message content
         $postdata2 = [
            'type'=>'order_info', 
            'alg'=>'RSA-OAEP-512',
            'value'=>"This is a audi Q8 from Tie!!!"
        ];
        //转换为json格式
        $postdatajson = json_encode($postdata2);
        curl_setopt($cURL, CURLOPT_URL, "https://tie0502.servicebus.windows.net/magentoq/messages");
        curl_setopt($cURL, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($cURL, CURLOPT_HTTPHEADER, $header); 
        curl_setopt($cURL, CURLOPT_POSTFIELDS, $postdatajson);
        curl_setopt($cURL, CURLOPT_POST, true);
        $json_response_data1 = curl_exec($cURL);
        $info = curl_getinfo($cURL);
        curl_close($cURL);
        echo "<pre>";//输出换行，等同于键盘ctrl+u
        print_r($info);
        print_r("The sending message response code is ".$info['http_code']); 
        //如果发送失败，将发送失败的信息（json格式）存入log。
        //页面提供一个功能，将json格式的信息黏贴进去，点击发送可以trigger这段代码再次发送message到service bus queue
        file_put_contents("php://stdout", 'Error(send message failure):  '.$postdatajson."\r\n");
        return 123;
    }


    public function testdeleteSBM(Request $Request)
    {
        $cURL = curl_init();
        $header=array(
             'Authorization:bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiIsIng1dCI6ImpTMVhvMU9XRGpfNTJ2YndHTmd2UU8yVnpNYyIsImtpZCI6ImpTMVhvMU9XRGpfNTJ2YndHTmd2UU8yVnpNYyJ9.eyJhdWQiOiJodHRwczovL3NlcnZpY2VidXMuYXp1cmUubmV0IiwiaXNzIjoiaHR0cHM6Ly9zdHMud2luZG93cy5uZXQvNGViNmIyZjAtODI2Mi00ZjdjLWFmZjQtMWE4OGQ1YTllMzI1LyIsImlhdCI6MTY1MDkwMTM3MiwibmJmIjoxNjUwOTAxMzcyLCJleHAiOjE2NTA5MDUyNzIsImFpbyI6IkUyWmdZRmlyMVBic1g3TFIvTGxzaWRIZnk3b1lBQT09IiwiYXBwaWQiOiI0YmY5YWJkOS1hNmI2LTQxMzMtYTgxYi0xY2JiNmU3YjhhYTUiLCJhcHBpZGFjciI6IjEiLCJpZHAiOiJodHRwczovL3N0cy53aW5kb3dzLm5ldC80ZWI2YjJmMC04MjYyLTRmN2MtYWZmNC0xYTg4ZDVhOWUzMjUvIiwib2lkIjoiODJiZDRjMDItMWE5NC00MDA0LWE3M2ItZTNlOTE2OWJhMWE3IiwicmgiOiIwLkFYMEE4TEsyVG1LQ2ZFLXY5QnFJMWFuakpma09vWUJvZ1QxSnFfa3lsOFR2Ymp5YUFBQS4iLCJzdWIiOiI4MmJkNGMwMi0xYTk0LTQwMDQtYTczYi1lM2U5MTY5YmExYTciLCJ0aWQiOiI0ZWI2YjJmMC04MjYyLTRmN2MtYWZmNC0xYTg4ZDVhOWUzMjUiLCJ1dGkiOiJZNEZhNk5zcG9VZVVKTnVJMnNWZUFRIiwidmVyIjoiMS4wIn0.Zxy75KyCXxj1g9yiNa8w060YPCHyZm8vn6XtueJeluuK8w5LERoGROhsdzm3NKphpTqVKBT0fAR08czlvSOvbBQW9_7LbB3LVNOS-pZvbzGQtOBu8WJ6tnxvyYtKNadNSN89M-aV1WKUrsDeGd9AUf3-mPSxBEl3c3NWSx38AjRFhdeVM93MF0uW_bvfUtm42dxi34Z7DGHyFWL6CyO14NHsDs037xBrF-6n4fmJq_R8nrPcK60GCnDxMsXYuZsv1jnpfCLwaeDvzU67y79BPJh2GvS6fyvudm6QO4by03Km6P2XA0teB_jpMrZtB0eaXVcsXk_aZYQ7NNoq5VTxdA', 
         );
         $sequenceID='33';
         $LockToken="48eb0dac-9a33-44f5-9060-d6be00f4b289";
         curl_setopt($cURL, CURLOPT_URL, "https://tie0418.servicebus.windows.net/tonysq/messages/".$sequenceID."/".$LockToken);
         curl_setopt($cURL, CURLOPT_RETURNTRANSFER, true);
         curl_setopt($cURL, CURLOPT_HTTPHEADER, $header); 
         curl_setopt($cURL, CURLOPT_CUSTOMREQUEST, "DELETE");
         $json_response_data1 = curl_exec($cURL);
         $info = curl_getinfo($cURL);
         curl_close($cURL);
         print_r("The sending message response code is ".$info['http_code']);
        return 'successfully';
    }

    public function test(Request $Request)
    {
		 $la_paras = $Request->json()->all();
		
		 //solution1 
		 error_log($la_paras['Properties']['Postman-Token'],0);
		//solution2
		 file_put_contents("php://stdout", '1-'.$la_paras['ContentData']."\r\n");
		 return $la_paras['Properties']['Size'];
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
		 echo("<script>console.log('1234567890');</script>"); 
		 echo '<script>console.log("'.$la_paras['ContentData'].'");</script>';
		  file_put_contents("php://stdout", '2-'.$la_paras['ContentData']."\r\n");
		 return $la_paras['ContentData'];
       // $file_path1 = base_path('tmp/trace.log');
        //error_log($la_paras,3,$file_path1);
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
