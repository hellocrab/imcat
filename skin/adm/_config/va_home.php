<?php
/*
? 
*/
$_va_home = array(

    'c' => array(
        'vmode' => 'dynamic', //dynamic,catch,close
        '_tabCtrl' => array('homeCtrl','bincCtrl'),
        '_defCtrl' => '_defCtrl', 
        //'stexp' => '12h', //30,60,3h,6h,12h,24h,7d
        //'stext' => '-a.htm',
        'pmods' => array('admin','apis','awex','binc',), // 权限模块
        'close' => array(),
        'imcfg' => array(),
        'extra' => array(), 
    ),
    
    //mod.home模块首页模板
    'm' => 'frame/awtop',
    'uhome'  => 'frame/uhome',

    'login'  => 'frame/login',
    'logout' => 'frame/login',
    'help'  => 'frame/help',
 
);
