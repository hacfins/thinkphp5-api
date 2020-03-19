<?php

namespace app\common\middleware;

use app\api\controller\BaseController;
use app\api\controller\traits\DataResponse;
use app\api\logic\UserLogic;
use app\api\logic\UserLoginLogic;
use app\common\facade\Request;
use think\facade\
{
    Cache, Cookie, Session
};

/**
 * 控制层 - 单点登录
 *
 * Class CSSO
 *
 * @package app\http\middleware
 */
class CSSOClient
{
    use DataResponse;

    public function handle(Request $request, \Closure $next)
    {
        $bSync = false;
        //【4.0】 单点登录
        do
        {
            $sessionToken = Session::get(SESSIONID_USER_TOKEN);
            $cookieToken  = Cookie::get(Cookie_SSO_UTOKEN, '');

            //未登录
            if (!$cookieToken)
            {
                Session::clear();
                break;
            }

            $userLogin = new UserLoginLogic();

            //未连接用户中心 || 共享的cookie被篡改
            if (!$sessionToken || ($sessionToken != $cookieToken))
            {
                Session::clear();

                if (!$userLogin->SSO_GetUserInfo_Ex($cookieToken))
                {
                    $this->R();
                }
                $bSync = true;

                break;
            }

            //10 分钟链接下用户中心
            if ($sessionToken == $cookieToken)
            {
                $key = 'login_ticket_' . $sessionToken;

                $cacheTime = Cache::get($key);
                if (!$cacheTime || $cacheTime < time())
                {
                    if (!$userLogin->SSO_GetUserInfo_Ex($cookieToken))
                    {
                        $this->R();
                    }
                    $bSync = true;
                    Cache::set($key, time() + 600, 640); //缓存10分钟
                    break;
                }
            }

        } while (0);

        //供业务逻辑使用
        $info = Session::get(SESSIONID_USER_INFO);
        if ($info)
        {
            $GLOBALS['uinfo']     = BaseController::$_uinfo = $info;
            $GLOBALS['user_name'] = BaseController::$_uname = strtolower($info['user_name']);

            //同步用户信息
            if ($bSync)
            {
                (new UserLogic())->SyncUserInfo($info['user_name'], $info['nick_name'], $info['full_name'], $info['sex'],
                    $info['birthday'], $info['adcode'], $info['company'], $info['avator'],
                    YES, $info['description']);
            }
        }
        else
        {
            $GLOBALS['uinfo']     = BaseController::$_uinfo = false;
            $GLOBALS['user_name'] = BaseController::$_uname = false;
        }

        return $next($request);
    }
}
