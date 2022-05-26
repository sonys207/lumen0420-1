<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Laravel\Lumen\Routing\Controller as BaseController;

class Controller extends BaseController
{
    //
    public function parse_parameters( $request1, $api_name){
         // dd($api_name);/* 方法名test*/
         return 'controlTest';
    }

    public function sendsbmsasbatch(Request $Request)
    {
         //send message to service bus with token
         $cURL = curl_init();
         $header=array(
              'Content-Type:application/atom+xml;type=entry;charset=utf-8',
              'Authorization:SharedAccessSignature sr=https%3a%2f%2ftie0502.servicebus.windows.net%2fmagentoq&sig=nO39PLmWkAesRLK1VQ8A4NoG2gYcUG7tbHPCgjOYY68%3D&se=2283609685&skn=RootManageSharedAccessKey',
            //  'BrokerProperties:{"Label":"M22","State":"Active","TimeToLive":3600}'
          );
          //message content
          $postdata2 = array(
            array('type'=>'order_info'), 
            array('alg'=>'RSA-OAEP-512-8',
             'value'=>"This is a audi Q8 from Tie!!!")
          );

    
         //转换为json格式
         $postdatajson = json_encode($postdata2);
         dd($postdatajson);
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
    }    

    public function sendsbmsas(Request $Request)
    {
         //send message to service bus with token
         $cURL = curl_init();
         $header=array(
              'Content-Type:application/atom+xml;type=entry;charset=utf-8',
              'Authorization:SharedAccessSignature sr=https%3a%2f%2ftie0502.servicebus.windows.net%2fmagentoq&sig=nO39PLmWkAesRLK1VQ8A4NoG2gYcUG7tbHPCgjOYY68%3D&se=2283609685&skn=RootManageSharedAccessKey',
              'message_type:new_order'
          );
          //message content
          $postdata2 = array(
            'message_type'=>'new_order',
            'message_content'=>array('alg'=>'RSA-OAEP-512-8',
            'value'=>"This is a audi Q8 from Tie234!!!"));
         //转换为json格式
         $postdatajson = json_encode($postdata2);
        // dd($postdatajson);
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
    }    

    public function receivesbmsas(Request $Request)
    {
        $la_paras = $Request->json()->all();
        // dd($la_paras,typeof($la_paras));
        //获取data(需要decode),message id,locktoken
        foreach ($la_paras as $message){
            $decode_ContentData=base64_decode( $message['ContentData'], $strict = true);
            $messageId=$message['Properties']['MessageId'];
            $LockToken=$message['Properties']['LockToken'];
            file_put_contents("php://stdout", 'MessageId is:  '.$messageId."\r\n");
            // dd(count($la_paras),$decode_ContentData,$la_paras[0]['Properties']['LockToken'],$la_paras[0]['Properties']['MessageId']);
            //写入数据库成功后，调用类中定义删除方法
            $this->deletesbmsas($decode_ContentData,$messageId,$LockToken);
       }
    }

    public function deletesbmsas($decode_ContentData,$messageId,$LockToken)
    {
        $cURL = curl_init();
        $header=array(
            'Authorization:SharedAccessSignature sr=https%3a%2f%2ftie0502.servicebus.windows.net%2fmagentoq&sig=nO39PLmWkAesRLK1VQ8A4NoG2gYcUG7tbHPCgjOYY68%3D&se=2283609685&skn=RootManageSharedAccessKey',
         );
         $messageId=$messageId;
         $LockToken=$LockToken;
        // dd($messageId,$LockToken);
         curl_setopt($cURL, CURLOPT_URL, "https://tie0502.servicebus.windows.net/magentoq/messages/".$messageId."/".$LockToken);
         curl_setopt($cURL, CURLOPT_RETURNTRANSFER, true);
         curl_setopt($cURL, CURLOPT_HTTPHEADER, $header); 
         curl_setopt($cURL, CURLOPT_CUSTOMREQUEST, "DELETE");
         $json_response_data1 = curl_exec($cURL);
         $info = curl_getinfo($cURL);
         curl_close($cURL);
         print_r("The delete message response code is ".$info['http_code']."\r\n");
        return 'successfully';
    }

    public function testAES(Request $Request)
    {
       // $plaintext = "message to be encrypted";
      //  $cipher = "aes-256-ctr";
      //  $key="TNT1683394935xxxxxxx";//先被MD5
      //  $ivlen = openssl_cipher_iv_length($cipher);
      //  $iv = openssl_random_pseudo_bytes($ivlen);
      //  $ciphertext = openssl_encrypt($plaintext, $cipher, $key, $options=0, $iv);
     // $token = "eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJ0cyI6IjEyMzQ1Njc4OTAiLCJwcm9qZWN0X25hbWUiOiJUTVMifQ.keYrWYJCykugycAK-hDm_awsuE5TzozuJdVa76scpvs";
     // print_r(json_decode(base64_decode(str_replace('_', '/', str_replace('-','+',explode('.', $token)[1])))));
     $plaintext = "TNT1653438687d3dfc330c54c3f59d3dfc330c54c3f65";
     $cipher = "aes-256-cbc";
     $key="d3dfc330c54c3f59d3dfc330c54c3f65";
   
    $iv = "c54c3f595a4f31e0";
   
    $ciphertext = openssl_encrypt($plaintext, $cipher, $key, false, $iv);
    //dd($ciphertext);
    $original_plaintext = openssl_decrypt($ciphertext, $cipher, $key, $options=0, $iv);
    echo $ciphertext."\n";
    }    

    public function testAES1(Request $Request)
    {
   
        $key="TNT1683394935xxxxxxx";//先被MD5
        $iv = 'b"ê\x17eÉ \e±Ä+ú2£┬\x13\x0E¼"';
        $ciphertext="bRVB9aclD+nflQggLeKXduQaRSQgrwo=";
        $cipher = "aes-256-ctr";
        $original_plaintext = openssl_decrypt($ciphertext, $cipher, $key, $options=0, $iv);
        dd($original_plaintext);
    }    

    public function posttest(Request $Request)
    {
        return 123;
    } 
}
