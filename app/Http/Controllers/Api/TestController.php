<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\UserModel;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Redis;

class TestController extends Controller
{
    function test(){
    	echo '<pre>';print_r($_SERVER);echo '</pre>';
    }

    //用户注册
    function reg(Request $request){
    	echo '<pre>';print_r(request()->input());echo '</pre>';
    	$pass1 = request()->input('pass1');
    	$pass2 = request()->input('pass2');
    	if($pass1 != $pass2){
    		die("两次输入的密码不一致");
    	}

    	$password = password_hash($pass1,PASSWORD_BCRYPT);
    	$data = [
    		'email' =>request()->input('email'),
    		'name' => request()->input('name'),
    		'password' =>$password,
    		'mobile' =>request()->input('mobile'),
    		'last_login' =>time(),
    		'last_ip' =>$_SERVER['REMOTE_ADDR'], //获取远程IP
    	];
    	$uid=UserModel::insertGetId($data);
    	var_dump($uid);die;
    }

    //用户登录接口
    function login(Request $request){
    	$name = request()->input('name');
    	$pass = request()->input('pass');
    	//echo "pass: ".$pass;echo '</br>';

    	$u=UserModel::where(['name'=>$name])->first();
    	if($u){
    		//echo '<pre>';print_r($u->toArray());echo '</pre>';
    		//验证密码
    		if(password_verify($pass,$u->password)){
    			//登陆成功
    			echo "登陆成功";
    			//生成token
    			$token = str::random(32);
    			$response = [
    				'errno'=>0,
    				'msg'=>'ok',
    				'data'=>[
    					'token'=>$token
    				]
    			];
    		}else{
    			$response = [
    				'errno'=> 400003,
    				'msg'=> '密码不正确'
    			];
    		}
    	}else{
    		$response = [
    			'errno'=> 400004,
    			'msg'=> '用户不存在'
    		];
    	}
    	return $response;
    }

    //获取用户列表
    function userlist(){
        $user_token = $_SERVER['HTTP_TOKEN'];
        echo 'user_token: '.$user_token;echo '</br>';

    	$current_url=$_SERVER['REQUEST_URI'];
    	echo "当前URL: ".$current_url;echo "<hr>";

    	//echo '<pre>';print_r($_SERVER);echo '</pre>';
    	//$url = $_SERVER[''] . $_SERVER[''];
    	$redis_key = 'str:count:u:'.$user_token.':url:'.md5($current_url);
    	echo "redis key: ".$redis_key;echo "</br>";

        $count = Redis::get($redis_key); //获取接口的访问
        echo "接口的访问次数: ".$count;echo '</br>';

        if($count >= 20){
            echo "请不要频繁访问此接口,访问次数以上限,请稍后再试";
            Redis::expire($redis_key,60);
            die;
        }

    	$count=Redis::incr($redis_key);
    	echo 'count: '.$count;
    }

   

}
