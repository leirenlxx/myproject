<?php
return array(
    /* 数据库设置 */
    'DB_TYPE'           => 'mysql', // 数据库类型
    'DB_HOST'           => '192.168.70.79', // 服务器地址
    'DB_NAME'           => 'jovo', // 数据库名
    'DB_USER'           => 'dev', // 用户名
    'DB_PWD'            => 'Emp9DkRbE1Vh', // 密码
    // 'DB_HOST'           => 'localhost', // 服务器地址
    // 'DB_NAME'           => 'mydb', // 数据库名
    // 'DB_USER'           => 'root', // 用户名
    // 'DB_PWD'            => 'root', // 密码
    'DB_PORT'           => '3306', // 端口
    'DB_PREFIX'         => 'jovo_', // 数据库表前缀
    'DB_PARAMS'         => array(), // 数据库连接参数
    'DB_DEBUG'          => true, // 数据库调试模式 开启后可以记录SQL日志
    'DB_FIELDS_CACHE'   => true, // 启用字段缓存
    'DB_CHARSET'        => 'utf8', // 数据库编码默认采用utf8
    'DB_DEPLOY_TYPE'    => 0, // 数据库部署方式:0 集中式(单一服务器),1 分布式(主从服务器)
    'DB_RW_SEPARATE'    => false, // 数据库读写是否分离 主从式有效
    'DB_MASTER_NUM'     => 1, // 读写分离后 主服务器数量
    'DB_SLAVE_NO'       => '', // 指定从服务器序号

    'h5'                => 'http://h5.ijovo.loc',
    'static'            => '//static.ijovo.loc',
    'img'               => '//img.ijovo.loc',

    //'url'               => 'http://h5.ijovo.loc',

    //邮箱配置
    'EMAIL_HOST'        => 'smtp.163.com',
    'EMAIL_USERNAME'    => 'jxb_wchat@163.com',
    'EMAIL_PWD'         => '66jiangxiaobai',
    'EMAIL_FROMNAME'    => '【江小白】',
    'EMAIL_SUBJECT'     => '【江小白】客户在线申请系统',
    'EMAIL_NICKNAME'    => '亲爱的用户',
    'EMAIL_REPLYTO'     => 'jxb_wchat@163.com',
    'EMAIL_REPLYTONAME' => '【江小白】',
    'EMAIL_CHARSET'     => 'UTF-8',
    'EMAIL_FROM'        => 'jxb_wchat@163.com',
    'reEmail'           => 'jxbzs@ijovo.com', //收件人的邮箱

    // 自定义命名空间
    // 'AUTOLOAD_NAMESPACE' => array(
    //     'Lib' => '/alidata/www/default/Lib',
    // ),
    //
);
