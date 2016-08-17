<?php
/**
 * 轻游戏-火锅英雄
 * @author lixiaoxian <lixiaoxian@ijovo.com>
 * @version $Id: HotpotHero.php 2016-03-18T14:22:33+0800
 */
namespace Lgame\Model;

use Think\Model;

class Lightapp extends Model
{

    public function getAppInfo($app)
    {
        $map['app'] = $app;
        return $this->where($map)->field('starttime,endtime,status')->find();
    }
}
