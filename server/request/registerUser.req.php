<?php
namespace Request;

use Srv\Core;
use Srv\DB;
use Srv\Config;
use Srv\Mail;
use Schema\User;

class registerUser{
    
    public function __request(){
        $email = getField('email', FIELD_EMAIL);
        if(!$email)
            return Core::setError('errRegisterInvalidEmail');
        $pass = getField('password');
        if(!$pass)
            return Core::setError('errRegisterInvalidPassword');
            
        $time = time();
        
        $exists = DB::table('user')->select()->where('email',$email)->exists();
        if($exists)
            return Core::setError('errRegisterUserAlreadyExists');
        $ssid = md5(microtime());
        $locale = $_COOKIE['web-lang'] ?? 'pl_PL';
        $validLocales = ['pl_PL','en_GB','pt_BR'];
        if(!in_array($locale, $validLocales)) $locale = 'pl_PL';

        $usr = new User([
            'email'=>$email,
            'password_hash'=>Core::passwordHash($pass),
            'ts_creation'=>$time,
            'registration_ip'=>getclientip(),
            'premium_currency'=>Config::get('constants.init_premium_currency'),
            'session_id'=> $ssid,
            'locale'=>$locale
        ]);
        $usr->save();
        setcookie("ssid", $ssid, time() + 63072000, '/');

        if(Config::get('email.notify_welcome'))
            Mail::queue($usr->id, $email, 'Welcome to HeroZero!', 'welcome', ['user_id'=>$usr->id, 'locale'=>$usr->locale]);

        Core::req()->data = (['user'=>$usr,'campaigns'=>[]]);
    }
    
}