<?php
/**
 * 轻应用-表单配置
 * @author lixiaoxian <lixiaoxian@ijovo.com>
 * @version $Id: FormsConf.php 2016-04-23T14:25:10+0800
 */
namespace Lapp\Model;

use Think\Model;

class FormsConf extends Model
{
    /**
     * 获取表单信息
     * @author   lixiaoxian
     * @datetime 2016-04-14T10:16:19+0800
     * @param    int            $id   活动id
     * @return   array                活动信息
     */
    public function getFormsType($confid)
    {
        $map['id'] = $confid;
        return $this->where($map)->field('type,name')->find();
    }
}
