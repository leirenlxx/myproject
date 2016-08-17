<?php

namespace Lapp\Controller;

use Think\Controller;

class User extends Controller
{

    /**
     * 默认模板
     * @author   lixiaoxian
     * @datetime 2016-05-23T11:43:26+0800
     * @return   [type]                   [description]
     */
    public function index()
    {
        $this->display();
    }

    /**
     * 注册
     * @author   lixiaoxian
     * @datetime 2016-05-16T10:23:48+0800
     * @return   [type]                   [description]
     */
    public function register()
    {
        if (IS_POST) {
            $data['username']   = I('post.username', '', 'trim');
            $data['password']   = I('post.password', '', 'trim');
            $data['repassword'] = I('post.repassword', '', 'trim');
            $data['email']      = I('post.email', '', 'trim');
            $data['mobile']     = I('post.mobile', '', 'trim');
            $data['verify']     = I('post.verify');

            //检测验证码
            if (!check_verify($data['verify'])) {
                $this->error('验证码错误');
            }

            // 检测密码
            if ($data['password'] != $data['repassword']) {
                $this->error('两次密码不一致');
            }

            //实例化User
            $User = D('User');
            //注册
            $result = $User->register($data);
            if ($result) {
                $this->success('恭喜您，注册成功');
            } else {
                $this->error($result);
            }
        }
    }

    /**
     * 登录
     * @author   lixiaoxian
     * @datetime 2016-05-23T15:26:04+0800
     * @return   [type]                   [description]
     */
    public function login()
    {
        if (IS_POST) {
            $data['username'] = I('post.username', '', 'trim');
            $data['password'] = I('post.password', '', 'md5');
            $data['verify']   = I('post.verify');
            $User             = D('User');
            //检测验证码
            if (!check_verify($data['verify'])) {
                $this->error('验证码错误');
            }
            //验证密码
            $userinfo = $User->getUserInfo($data);
            if (!empty($userinfo)) {
                if ($userinfo['password'] == $data['password']) {
                    $this->success('恭喜您，登陆成功');
                    //记录登录信息
                    $User->login($data['username']);
                    // 记录登录SESSION
                    $auth = array(
                        'uid'      => $userinfo['uid'],
                        'username' => $userinfo['username'],
                    );
                    session('user', $auth);
                    session('user_sign', data_auth_sign($auth));
                } else {
                    $this->error('用户名或密码错误，请重试');
                }
            } else {
                $this->error('用户名不存在，请重试');
            }
        } else {
            $this->display();
        }
    }

    /**
     * 修改密码
     * @author   lixiaoxian
     * @datetime 2016-05-23T15:33:45+0800
     * @return   [type]                   [description]
     */
    public function updatePwd()
    {
        if (!is_login()) {
            $re['status'] = 'fail';
            $re['msg']    = '你还没登录';
        }
        if (IS_POST) {
            //获取参数
            $uid                 = is_login();
            $data['password']    = I('post.password');
            $data['repassword']  = I('post.repassword');
            $data['newpassword'] = I('post.newpassword');
            if (empty($data['password'])) {
                $re['msg'] = '请输入原密码';
            }
            if (empty($data['repassword'])) {
                $re['msg'] = '请输入新密码';
            }
            if (empty($data['newpassword'])) {
                $re['msg'] = '请确认新密码';
            }
            if ($data['newpassword'] !== $data['repassword']) {
                $re['msg'] = '您输入的新密码与确认密码不一致';
            }
            $resule = D('User')->updatePwd($uid, $data['newpassword']);
            if ($result) {
                $re['status'] = 'success';
                $re['msg']    = '修改成功';
            } else {
                $re['status'] = 'fail';
                $re['msg']    = '修改失败';
            }
        } else {
            $this->display();
        }
        $this->ajaxReturn($re);
    }

    /**
     * 退出登录
     * @author   lixiaoxian
     * @datetime 2016-05-16T15:53:21+0800
     * @return   [type]                   [description]
     */
    public function logout()
    {
        if (session('uid')) {
            //清空session
            session('user', null);
            session('user_sign', null);
            session('[destroy]');
            $this->success('退出成功');
        } else {
            $this->redirect('login');
        }
    }

    /**
     * 找回密码
     * @author   lixiaoxian
     * @datetime 2016-05-23T17:12:33+0800
     * @return   [type]                   [description]
     */
    public function getBackPwd()
    {
        if (IS_POST) {
            $data['username'] = I('post.username', '', 'trim');
            $data['email']    = I('post.email', '', 'trim');

            $User     = D('User');
            $userinfo = $User->getUserInfo($data['username']);
            if (!empty($userinfo)) {
                if ($userinfo['email'] == $data['email']) {
                    $data['password']   = I('post.password', '', 'trim');
                    $data['repassword'] = I('post.repassword', '', 'trim');
                    if ($data['password'] == $data['repassword']) {
                        //修改密码
                        $result = $User->updatePwd($userinfo['uid'], $data['password']);
                        if ($result) {
                            $re['status'] = 'success';
                            $re['msg']    = '修改成功';
                        } else {
                            $re['status'] = 'fail';
                            $re['msg']    = '请重试';
                        }
                    } else {
                        $re['status'] = 'fail';
                        $re['msg']    = '两次密码不一致';
                    }
                } else {
                    $re['status'] = 'fail';
                    $re['msg']    = '用户名或邮箱错误';
                }
            } else {
                $re['status'] = 'fail';
                $re['msg']    = '用户不存在或被禁用';
            }
        }
    }

    /**
     * 生成验证码
     * @author   lixiaoxian
     * @datetime 2016-05-17T16:05:35+0800
     * @return   [type]                   [description]
     */
    public function verify()
    {
        $Verify           = new \Think\Verify();
        $Verify->fontSize = 16;
        $Verify->length   = 4;
        $Verify->useNoise = false;
        $Verify->codeSet  = '0123456789';
        $Verify->imageW   = 160;
        $Verify->imageH   = 50;
        $Verify->entry();
    }

}
