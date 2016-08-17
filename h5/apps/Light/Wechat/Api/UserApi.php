<?php
/**
 * 微信-用户接口
 * @author liuke <liuke@ijovo.com>
 * @version $Id: UserApi.php 2016-03-19T16:38:34+0800
 */
namespace Wechat\Api;

use Think\Model;

class UserApi extends Model
{
    protected $tablePrefix  = "ijovo_";
    protected $tableName    = "user";
    protected $dbName       = "public";
    protected $expires_time = 604800; //3600x24x7 (表示一周)

    private $_appId;
    private $_appSecret;

    protected $error;

    private $_source = ''; //来源

    /**
     * 构造函数
     * @author   liuke
     * @datetime 2016-03-21T11:34:21+0800
     */
    public function __construct($appId, $appSecret)
    {
        parent::__construct();
        $this->_appId     = $appId;
        $this->_appSecret = $appSecret;
    }

    public function getUserInfo($source, $uid = '', $openid = '', $code = '')
    {
        $this->_source = $source;

        $WeAct = new \Vendor\Wechat\WechatAct($this->_appId, $this->_appSecret);
        if ($uid === '' && $openid === '') {
            //获取用户
            if ($code === '') {
                //重定向
                $this->getCode();
            }
            //根据CODE抓取用户信息
            $info = $WeAct->getUserByCode($code);
            if (is_numeric($info)) {
                $this->getCode();
            }
            if (is_string($info)) {
                $this->error = '2021';
                // $this->getCode();
                return false;
            }
            //var_dump($info);
            //新增用户信息
            $data          = $this->formatData($info);
            $map['openid'] = $data['openid'];
            $user          = $this->where($map)->find();
            //用户不存在
            if (!$user) {
                $user = $this->updateUser($data);
            }
            return $this->fieldData($user);
        } else {
            //查询用户
            $uid == '' ? $map['openid'] = $openid : $map['uid'] = $uid;
            $user                       = $this->where($map)->find();
            if ($user && $user['update_time'] < time() + $this->expires_time) {
                //信息未过期

                return $this->fieldData($user);
            } else {
                //信息已过期
                //根据OPENID抓取用户信息
                $openid  = $user['openid'];
                $newInfo = $WeAct->getUserById($openid);
                // var_dump($newInfo);
                if (is_string($newInfo)) {
                    $this->error = '2022';
                    return false;
                }
                //更新用户信息
                if ($uid != '') {
                    $data = $this->formatData($newInfo, $uid);
                } else {
                    $data = $this->formatData($newInfo);
                }
                $newUser = $this->updateUser($data);
                return $newUser;
            }
        }
        return false;
    }

    /**
     * 格式化数据
     * @param  object $info 微信操作类返回数据对象
     * @param  string $uid  用户ID
     * @return mixed        格式化后用户数组|false
     */
    protected function formatData($info, $uid = '')
    {
        if ($info) {
            $data = [
                'openid'   => $info->openid,
                'nickname' => $info->nickname,
                'avator'   => $info->headimgurl,
                'sex'      => $info->sex,
                'city'     => $info->city,
                'province' => $info->province,
                'country'  => $info->country,
                'source'   => $this->_source,
            ];
            // if ($info->unionid) {
            //     $data['unionid'] = $info->unionid;
            // }
            if ($uid != '') {
                $data['uid'] = $uid;
            }
            $data['update_time'] = time();
            return $data;
        } else {
            $this->eorror = '2011';
            return false;
        }
    }

    /**
     * 更新用户信息
     * @param  array $data 格式化后的用户数据
     * @return mixed       [更新后的用户信息|false]
     */
    protected function updateUser($data)
    {
        //判断参数$info类型
        if (is_array($data)) {
            //判断是新增或者是更新
            if (isset($data['uid'])) {
                //更新操作
                $map['uid'] = $data['uid'];
                if ($this->where($map)->save($data)) {
                    return $this->fieldData($data);
                } else {
                    $this->eorror = '2001';
                }

            } else {
                //新增操作
                if ($id = $this->add($data)) {
                    $user = $this->field('uid,nickname,avator')->where("id=$id")->find();
                    return $user;
                } else {
                    $this->eorror = '2002';
                }
            }
        }
    }

    /**
     * 重新指定数据字段
     * @author   liuke
     * @datetime 2016-03-21T13:03:03+0800
     * @param    [type]                   $data [description]
     * @return   [type]                         [description]
     */
    public function fieldData($data)
    {
        $newData = [];

        $defaultField = ['uid' => 'd1000001', 'nickname' => '江小白粉丝', 'avator' => '1231sdf', 'sex' => 1, 'openid' => ''];

        foreach ($defaultField as $k => $v) {
            $newData[$k] = isset($data[$k]) ? $data[$k] : $v;
        }
        return $newData;
    }

    /**
     * 重定向获取授权码
     * @author   liuke
     * @datetime 2016-03-21T12:59:59+0800
     * @return   mixed      重定向
     */
    protected function getCode()
    {
        $uri = "http://" . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
        $uri = urlencode($uri);
        $url = "https://open.weixin.qq.com/connect/oauth2/authorize?appid=" . $this->_appId . "&redirect_uri=" . $uri . "&response_type=code&scope=snsapi_userinfo&state=STATE#wechat_redirect";
        redirect($url);
    }
}
