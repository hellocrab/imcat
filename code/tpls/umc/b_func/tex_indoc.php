<?php
/*
单个模板扩展函数
*/ 
class tex_indoc{ //extends tex_base
	
	#protected $prop1 = array();
	
	static function expwhr($user,$part,$stype,$read=0,$cnt=0){ 
		//print_r($user);
		$db = glbDBObj::dbObj(); 
		if($user->userFlag=='Login'){
			$uname = $user->uinfo['uname'];
			$indep = empty($user->uinfo['indep']) ? '(nologin)' : $user->uinfo['indep'];
		}else{
			$uname = "(nologin)";
			$indep = "(nologin)";
		}
		// exp_xxx
		$whra = array(
			'imy' => " AND touser LIKE '%$uname%'",
			'idep' => " AND todep LIKE '%$indep%'",
			'ipub' => " AND topub='ispub'",
		);
		$where = isset($whra[$part]) ? $whra[$part] : '';
		if(!empty($stype)){
			$where .= " AND catid='$stype'";
		}
		if(!empty($read)){ // in,not
			$dbcfg = glbDBObj::getCfg(); //print_r($dbcfg);
			$subs = "SELECT pid FROM {$db->pre}coms_inread{$db->ext} WHERE auser='$uname'";
			$where .= " AND m.did ".($read=='no' ? 'NOT' : '')." IN($subs)";
		}
		if(!empty($cnt)){
			$where = str_replace('m.did','did',$where);
			return $db->table('docs_indoc')->where(substr($where,5))->count();
		}
		return $where;
	}
	
	// 公开属性修正
	static function fixIspub(&$dop,$isadd){ 
		if($dop->fmv['topub']=='ispub'){
			$dop->fmv['touser'] = $dop->fmv['todep'] = '';
		}
	}
	// 发通知扩展
	static function exNotice($dop,$isadd){ //print_r($dop);
		$db = glbDBObj::dbObj(); 
		$stype = basReq::ark('fm','sendsms') ; 
		//$stype = 'wechat'; 
		if(empty($stype) || $dop->fmv['topub']=='ispub') return;
		$usql = self::tousql($dop->fmv);
		$uarr = self::touarr($usql); 
		$ncfg = array( // sendsms（通知类型）:: nosend=不通知
			'mobmsg' => 'mtel', //短信
			'email' => 'memail', //邮件
			'wechat' => 'wechat', //微信
		);
		if(!isset($ncfg[$stype])) return;
		$method = 'exN'.ucfirst($stype); 
		if(!empty($uarr)){
			$max = 60; $ids = ''; $cnt = 0; // 随机30~80个帐号
			foreach($uarr as $k=>$v){
				if($stype=='wechat'){ 
					$rec = $db->table('users_uppt')->field('pptuid')->where("uname='$k'")->find(); 
					$nid = $rec['pptuid'];
				}else{
					$nid = $v[$ncfg[$stype]]; //[$ncfg[$stype]]
				}
				if($nid){
					$ids .= (empty($ids)?'':',').$nid;
					$cnt++;
				}
				if($cnt>=$max){
					$ids = ''; $cnt = 0;
					if(!empty($ids)) self::$method($dop,$ids); 
				}
			}
			if(!empty($ids)) self::$method($dop,$ids); 
		}
	}
	static function exNMobmsg($dop,$tels){ 
		$info = self::exNInfo($dop); 
		$detail = "您好！您收到一条公文消息：";
		$detail .= "公文主题：{$info['title']}；";
		$detail .= "发布时间：{$info['atime']}；";
		$detail .= "发布人：{$info['auser']}；";
		$detail .= "<a href='{$info['url']}'>请点击查看！</a>";
		$detail .= "【{$info['sys_name']}】";
		$sms = new extSms(); 
		if($sms->isClosed()) return; 
		//dump($detail);
		$res = $sms->sendSMS($tels,$detail,0); 
		if($res[0]!==1){
			$res = implode(':',$res); 
			self::exNLoger($res,$detail);
		}
	}

	static function exNEmail($dop,$mails){ 
		$info = self::exNInfo($dop); 
		$type = 'phpmailer'; //swiftmailer,phpmailer,basReq::val('type');
		$detail = "您好！您收到一条公文消息：\n";
		$detail .= "公文主题：{$info['title']}；<br>\n";
		$detail .= "发布时间：{$info['atime']}；<br>\n";
		$detail .= "发布人：{$info['auser']}；<br>\n";
		$detail .= "<a href='{$info['url']}'>请点击查看！</a><br>\n";
		$detail .= "【{$info['sys_name']}】";
		//dump($detail);
		$mail = new extEmail($type);
		$res = $mail->send($mails,'公文提醒通知',$detail,"{$info['sys_name']}");
		if($res!=='SentOK'){
			$res = strip_tags($res); 
			self::exNLoger($res,$detail);
		}
	}
	static function exNWechat($dop,$opids){ 
		global $_cbase;
		defined('WERR_RETURN') || define('WERR_RETURN',1);
		$info = self::exNInfo($dop); $color = '"color":"#173177"';
		$detail = '"first":{"value":"您好！您收到一条公文消息：",'.$color.'},';
        $detail .= '"title":{"value":"'.$info['title'].'",'.$color.'},';
        $detail .= '"time":{"value":"'.$info['atime'].'",'.$color.'},';
        $detail .= '"auser":{"value":"'.$info['auser'].'",'.$color.'},';
        $detail .= '"remark":{"value":"请点击查看！",'.$color.'}';
        //dump($detail);
		$weixin = new wmpMsgmass(wysBasic::getConfig('admin'));
		$data = $weixin->sendTpl($opids, $_cbase['weixin']['tplidIndoc'], $detail, $info['url']);
		$res = '';
		foreach ($data as $opid => $re1) {
			$res .= "{$re1['errcode']}:{$re1['errmsg']}; ";
			//dump($re1);
		}
		self::exNLoger($res,$detail);
	}
	static function exNInfo($dop){ 
		global $_cbase;
		$re['url'] = $_cbase['run']['rsite'].PATH_PROJ."/?indoc.{$dop->fmu['did']}";
		$re['title'] = $dop->fmv['title'];
		$re['sys_name'] = $_cbase['sys_name'];
		$re['auser'] = $dop->fmv['auser'];
		$re['atime'] = date('Y-m-d H:i',$dop->fmv['atime']);
		return $re;
		/*dop:Array(
			[title] => TestSSS     [color] =>      [show] => 1
			[indep] => intech      [catid] => i1016
			[rdtip] => v48         [topub] => isset
			[todep] => ,inadm,intech,incai,inpro,inren
			[touser] => xgezi,ahong,ruby
			[atime] => 1464740404  [etime] => 1464740404
			[auser] => xgezi       [euser] => xgezi
			[aip] => 127.0.0.1     [eip] => 127.0.0.1
		)*/
	}
	static function exNLoger($info=array(),$detail){
		global $_cbase; 
		if(is_array($info)) $info = implode(':',$info); 
		$info .= "\ndetail:".$detail;
		//dump($info);
		if($_cbase['indoc']['debug']){
			basDebug::bugLogs('indoc',$info,'detmp','db');
		}
		// #register_shutdown_function('shutdown_handler_ys',$dop);
	}
	
	// toshow
	static function toshow($vars){ 
		$re = array(); 
		$re['tod'] = vopCell::cOpt($vars['todep'],'indep',',','');
		$usql = self::tousql(array('todep'=>'','topub'=>'','touser'=>$vars['touser'],));
		$uarr = self::touarr($usql); 
		$re['tou'] = ''; $null = "<span class='cCCC'>(无)</span>";
		if(empty($uarr)){
			$re['tou'] = $null;
		}else{
			foreach($uarr as $k=>$v){
				$re['tou'] .= "".(empty($re['tou'])?"":", ")."".(count($uarr<5)?"($k)":"")."$v[mname]";
			}
		}
		if($vars['topub']=='ispub'){
			$re['tod'] .= "<span class='c33F'>公开</span>";
		}
		if(empty($re['tod'])) $re['tod'] = $null;
		return $re;
	}
	// tousql
	static function tousql($vars){ 
		if($vars['topub']=='ispub'){
			$re = '1=1';
		}else{
			$ru = basSql::whrInids($vars['touser']);
			$rd = basSql::whrInids($vars['todep']);
			if($ru && $rd){
				$re = "(uname IN($ru) OR indep IN($rd))";
			}elseif($ru){
				$re = "uname IN($ru)";	
			}elseif($rd){
				$re = "indep IN($rd)";	
			}else{
				$re = '1=2';	
			}
		}
		return $re;
	}
	// touarr
	static function touarr($usql){ 
		$db = glbDBObj::dbObj(); 
		$list = $db->table('users_inmem')->field('uid,uname,grade,mname,mtel,memail,miuid,indep,`show`')->where($usql)->select();
		$re = array();
		if($list){ foreach($list as $r){
			$re[$r['uname']] = $r;
		}}
		return $re;
	}
	
	// 不能查看的原因
	static function noperm($user,$data=array()){ 
		if($data['topub']=='ispub'){
			$nores = 0; //公开文件,可看
		}elseif(usrPerm::issup()){
			$nores = 0; //超管,可看
		}elseif($user->userFlag!='Login'){
			$nores = 'noLogin'; //未登录不能看
		}else{
			$intou = $intod = 0;
			if(!empty($data['touser'])){
				$intou = strpos('(,'.$data['touser'].',)',','.@$user->uinfo['uname'].',') ? 1 : 0;
			}
			if(!empty($data['todep'])){
				$udep = empty($user->uinfo['indep']) ? '(nodep)' : $user->uinfo['indep'];
				$intod = strpos('(,'.$data['todep'].',)',",$udep,") ? 1 : 0;
			}
			$nores = ($intod || $intou) ? 0 : 'notTo'; 
		} //$nores = 'notTo';
		return $nores;
	}
	
	// 是否已查看:noread,isread,0
	// tpl:<i class='tim-{val}'></i>
	static function isread($user,$did,$tpl="(val)",$flag=1){ 
		$cfg = array(
			'isread1' => '<span style="color: #008000; font-weight : bold;" title="已读">&#10004;</span>',
			'noread1' => '<span style="color: #ff0000; font-weight : bold;" title="未读">&#10008;</span>',
			'isread2' => '<span style="color: #008000; font-weight : bold;">&#10004;已读</span>',
			'noread2' => '<span style="color: #ff0000; font-weight : bold;">&#10008;未读</span>',
		);
		$data = array();
		if($user->userFlag!='Login'){
			$re = 0;
		}else{
			$uname = $user->uinfo['uname'];
			$db = glbDBObj::dbObj(); 
			$data = $db->table('coms_inread')->where("pid='$did' AND auser='$uname'")->find(); 
			$re = empty($data) ? 'noread' : 'isread';
		}
		if($tpl=='arr'){
			$data['_flag'] = $re;
			$re = $data; 
		}elseif(!empty($tpl)){
			$re = $re ? str_replace('(val)',$re,$tpl) : '';
			if(isset($cfg["$re$flag"])) $re = $cfg["$re$flag"];
		}
		return $re;
	}

}

/*

*/
