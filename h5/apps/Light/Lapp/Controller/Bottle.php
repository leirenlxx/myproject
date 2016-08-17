<?php
/**
 * 轻应用-表达瓶
 * @author lixiaoxian <lixiaoxian@ijovo.com>
 * @version $Id: JoyyouthInvest.php 2016-05-30T10:22:40+0800
 */
namespace Lapp\Controller;

use Think\Controller;

class Bottle extends Controller
{
    //状态变量
    private $_status;

    /**
     * 初始化
     * @author   lixiaoxian
     * @datetime 2016-05-30T10:38:05+0800
     * @return   [type]                   [description]
     */
    public function _initialize()
    {
        header("Access-Control-Allow-Origin: *");
        return $this->_status = D('Lapp')->checkApp('bottle');
    }

    /**
     * 主页模板
     * @author   lixiaoxian
     * @datetime 2016-05-30T10:34:35+0800
     * @return   [type]                   [description]
     */
    public function index()
    {
        $code = I('code', '', 'trim');
        //重定向授权
        Vendor('phpRPC.phprpc_client');
        $client = new \PHPRPC_Client('http://10.161.228.164/wechat/api/service/webgrant');
        if (!session('openid')) {
            //tp3.2
            $data = array(
                'app'     => 'jxb',
                'code'    => $code,
                'domain'  => 'h5.ijovo.com',
                'openid'  => '',
                'unionid' => '',
            );
            $res = $client->user($data);

            //判断错误
            if (is_object($res)) {
                exit('error');
            }
            if (empty($code)) {
                /**
                 * :r 当前页面地址 ,全部URL
                 * :app 公众号简写 [域名不一致时需要填写]
                 * @var string
                 */
                $uri = "http://" . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
                $url = str_replace(':r', urlencode($uri), $res['content']['url']);
                $url = str_replace(':app', 'jxb', $url);
                redirect($url);
            }
            $user = $res['content'];
            if (is_array($user)) {
                $BottleUser = D('BottleUser');
                //检查是否存在用户
                $userinfo = $BottleUser->getUserInfo($user['openid']);
                if ($userinfo['id'] == null) {
                    //记录用户
                    $result = $BottleUser->recordUserInfo($user);
                    if ($result) {
                        //保存session
                        session('openid', $user['openid']);
                        session('userinfo', $userinfo);
                        //模板赋值
                        $this->assign('openid', $user['openid']);
                    }
                } else {
                    //保存session
                    session('openid', $user['openid']);
                    $this->assign('openid', $user['openid']);
                }
            }
            //jssdk
            $url1  = 'http://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
            $jssdk = $this->_sdk('jxb', $url1);
            $this->assign('jssdk', $jssdk);
            //模板赋值
            $this->assign('uid', $user['uid']);
            $this->assign('nickname', $user['nickname']);
            $this->display();
        } else {
            $openid = session('openid');
            //记录访问
            $BottleUser           = M('BottleUser');
            $BottleUser->modified = NOW_TIME;
            $BottleUser->where(array('openid' => $openid))->save();
            //jssdk
            $url1  = 'http://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
            $jssdk = $this->_sdk('jxb', $url1);
            $this->assign('jssdk', $jssdk);
            $userinfo = M('BottleUser')->where(array('openid' => $openid))->find();
            //模板赋值
            $this->assign('uid', $userinfo['uid']);
            $this->assign('nickname', $userinfo['nickname']);
            $this->assign('openid', $openid);
            $this->display();
        }
    }

    /**
     * 瓶子列表
     * @author   lixiaoxian
     * @datetime 2016-06-15T16:45:40+0800
     * @return   [type]                   [description]
     */
    public function bolist()
    {
        $uid = I('get.uid');
        if (session('openid')) {
            //jssdk
            $url    = "http://" . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
            $result = $this->_sdk('jxb', $url);
            $this->assign('jssdk', $result);
            $this->display();
        } elseif (!empty($uid)) {
            $url    = "http://" . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
            $result = $this->_sdk('jxb', $url);
            $this->assign('jssdk', $result);
            $this->display();
        } else {
            $this->display('index');
        }
    }

    /**
     * 瓶子编辑
     * @author   lixiaoxian
     * @datetime 2016-06-14T17:57:25+0800
     * @return   [type]                   [description]
     */
    public function draw()
    {
        $uid = I('get.uid');
        if (session('openid')) {
            //jssdk
            $url    = "http://" . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
            $result = $this->_sdk('jxb', $url);
            $this->assign('jssdk', $result);
            $this->display();
        } elseif (!empty($uid)) {
            $url    = "http://" . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
            $result = $this->_sdk('jxb', $url);
            $this->assign('jssdk', $result);
            $this->display();
        } else {
            $this->display('index');
        }
    }

    /**
     * 我的情绪瓶
     * @author   lixiaoxian
     * @datetime 2016-05-30T14:17:42+0800
     * @return   json                操作结果
     */
    public function bottle()
    {
        if ($this->_status) {
            if (IS_POST) {
                $uid     = I('get.uid');
                $appId   = 5;
                $content = I('post.content');
                // 最终图片base64
                $image = I('post.image');
                //自定义上传图base64
                $uploadImg = I('post.uploadimg');
                //瓶身图
                $chooseImg = I('post.chooseimg');
                //背景图
                $backImg = I('post.backimg');
                //字体颜色
                $color  = I('post.color');
                $Bottle = D('Bottle');

                if (!empty($content) && !is_numeric($content)) {
                    $checked = $this->check($content);
                } else {
                    $re['status'] = 'fail';
                    $re['msg']    = '内容不能为空或纯数字';
                    $this->ajaxReturn($re);
                }
                //处理最终图片
                $bottle = base64_decode(substr(strstr($image, ','), 1));
                //最终图名
                $bottleName = 'bottle' . uniqid() . '.jpg';
                //最终图保存路径
                // $bottlePath = '../../../h5/uploads/bottle/' . $bottleName;
                $bottlePath = '/alidata/www/h5/uploads/bottle/' . $bottleName;
                if ($uid) {
                    $check = $Bottle->checkContent($appId, $uid, $checked);
                    if ($checked == $check['content'] && $uid == $check['uid']) {
                        if (NOW_TIME - $check['created'] < 120) {
                            $re['status'] = 'fail';
                            $re['msg']    = '2分钟内请勿提交相同内容';
                        }
                    } else {
                        if (file_put_contents($bottlePath, $bottle)) {
                            $return = $this->upload($bottlePath, $bottleName);
                            if ($return == 1) {
                                $finalimg = 'http://static.ijovo.com/uploads/bottle/' . $bottleName;
                            } else {
                                $re['status'] = 'fail';
                                $re['msg']    = $return;
                                $this->ajaxReturn($re);
                            }
                        } else {
                            $re['status'] = 'fail';
                            $re['msg']    = '保存错误';
                            $this->ajaxReturn($re);
                        }
                        if (!empty($uploadImg)) {
                            $uploadImgPath = $uploadImg;
                            $chooseImg     = '';
                        } else {
                            $uploadImgPath = '';
                        }
                        //保存数据
                        $result = $Bottle->setBottle($appId, $uid, $checked, $uploadImgPath, $color, $chooseImg, $backImg, $finalimg);
                        if (is_numeric($result) && $result > 0) {
                            $re['status']  = 'success';
                            $re['msg']     = '成功';
                            $re['imgurl']  = $finalimg;
                            $re['shareid'] = $result;
                        } else {
                            $re['status'] = 'fail';
                            $re['msg']    = $result;
                        }
                    }
                } else {
                    $re['status'] = 'fail';
                    $re['msg']    = '数据丢失了，请退出重试';
                }
            }
        } else {
            $re['status'] = 'fail';
            $re['msg']    = '活动已经结束';
        }
        $this->ajaxReturn($re);
    }

    /**
     * 裁剪图上传接口
     * @author   lixiaoxian
     * @datetime 2016-06-18T09:42:47+0800
     * @return   [type]                   [description]
     */
    public function cutImg()
    {
        if ($this->_status) {
            $cutImg = I('post.cutimg');
            if (!empty($cutImg)) {
                $upImg     = substr(strstr($cutImg, ','), 1);
                $img       = base64_decode($upImg);
                $imageName = 'upload' . uniqid() . '.jpg';
                //$imagePath = '../../../h5/uploads/bottle/' . $imageName;
                $imagePath = '/alidata/www/h5/uploads/bottle/' . $imageName;
                //写文件
                if (file_put_contents($imagePath, $img)) {
                    if ($this->upload($imagePath, $imageName) == 1) {
                        $re['status'] = 'success';
                        $re['msg']    = '上传成功';
                        $re['url']    = 'http://static.ijovo.com/uploads/bottle/' . $imageName;
                    } else {
                        $re['status'] = 'fail';
                        $re['msg']    = '上传失败';
                        $this->ajaxReturn($re);
                    }
                } else {
                    $re['status'] = 'fail';
                    $re['msg']    = '上传错误';
                    $this->ajaxReturn($re);
                }
            } else {
                $re['status'] = 'fail';
                $re['msg']    = '活动已经结束';
            }
            $this->ajaxReturn($re);
        }
    }

    /**
     * 情绪瓶列表
     * @author   lixiaoxian
     * @datetime 2016-05-30T10:35:19+0800
     * @return   [type]                   [description]
     */
    public function bottleList()
    {
        if ($this->_status) {
            $openid = I('get.openid');
            $appId  = 5;
            if (!empty($openid)) {
                //当前显示页码
                $page   = I('get.page', 1, 'intval');
                $Bottle = D('Bottle');
                // 查询满足要求的总记录数
                $list = $Bottle->getBottleList($appId, $page);
                foreach ($list as $k => $v) {
                    //时间戳转时间格式
                    $list[$k]['created'] = date('Y-m-d H:i:s', $v['created']);
                }
                $re['status'] = 'success';
                $re['data']   = $list;
                $this->ajaxReturn($re);
            } else {
                $this->display('index');
            }
        } else {
            $this->display('error');
        }
    }

    /**
     * 个性化分享接口
     * @author   lixiaoxian
     * @datetime 2016-06-18T21:22:30+0800
     * @return   [type]                   [description]
     */
    public function share()
    {
        $shareid = I('get.shareid');
        $result  = D('Bottle')->getBottleUrl($shareid);
        if ($result) {
            $re['status'] = 'success';
            $re['msg']    = '获取成功';
            $re['url']    = $result['img'];
        } else {
            $re['status'] = 'fail';
            $re['msg']    = '获取失败';
            $re['url']    = '';
        }
        $this->ajaxReturn($re);
    }

    /**
     * 上传到又拍云
     * @author   lixiaoxian
     * @datetime 2016-06-17T10:09:02+0800
     * @param    [type]        $file 文件
     * @param    [type]        $name 文件名
     * @return   [type]                         [description]
     */
    public function upload($file, $name)
    {
        vendor('Ftp.Ftp');
        $ftp              = new \Ftp(); //实例化对象
        $data['server']   = 'v0.ftp.upyun.com'; //服务器地址(IP or domain)
        $data['username'] = 'liuke/static-ijovo'; //ftp帐户
        $data['password'] = 'ijovo123'; //ftp密码
        $data['port']     = 21; //ftp端口,默认为21
        $data['pasv']     = false; //是否开启被动模式,true开启,默认不开启
        $data['ssl']      = false; //ssl连接,默认不开启
        $data['timeout']  = 90; //超时时间,默认60,单位 s
        if ($ftp->start($data)) {
            // 远程连接成功;
            //检测目录&创建目录
            $remotedir = '/uploads/bottle/' . $name;
            if ($ftp->put($remotedir, $file)) {
                return 1;
                //上传文件成功!
            } else {
                return $ftp->get_error(); //错误调试信息
            }
        }
        $ftp->close();
    }

    /**
     * sdk
     * @author   lixiaoxian
     * @datetime 2016-07-04T21:10:02+0800
     * @param    [type]                   $app [description]
     * @param    [type]                   $url [description]
     * @return   [type]                        [description]
     */
    protected function _sdk($app = 'jxb', $url)
    {
        Vendor('phpRPC.phprpc_client');
        $client = new \PHPRPC_Client('http://10.161.228.164/wechat/api/service/webgrant');

        $data = array(
            'app' => $app,
            'url' => $url,
        );
        $res = $client->sdk($data);
        //判断错误
        if (is_object($res)) {
            exit('error');
        }
        return $res['content'];
    }

    /**
     * 敏感词检测
     * @author   lixiaoxian
     * @datetime 2016-07-15T18:40:26+0800
     * @param    [type]                   $content [description]
     * @return   [type]                            [description]
     */
    protected function check($content)
    {
        import('Org.Util.badword');
        $badword  = new \badword();
        $badwords = $badword->badword();
        foreach ($badwords as $key => $value) {
            if (false !== strpos($content, $value)) {
                $re['status'] = fail;
                $re['msg']    = '你的内容包含敏感词汇';
                $this->ajaxReturn($re);
            }
        }
        return $content;
    }

}
