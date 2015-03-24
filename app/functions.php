<?php

/*
|--------------------------------------------------------------------------
| Override Official Functions Library
|--------------------------------------------------------------------------
|
| Official functions library path
| Illuminate/Support/helpers.php
|
*/

/**
 * Generate a URL to a named route.
 *
 * @param  string  $route
 * @param  string  $parameters
 * @return string
 */
function route($route, $parameters = array())
{
	if (Route::getRoutes()->hasNamedRoute($route))
		return app('url')->route($route, $parameters);
	else
		return 'javascript:void(0)';
}

/**
 * Generate a HTML link to a named route.
 *
 * @param  string  $name
 * @param  string  $title
 * @param  array   $parameters
 * @param  array   $attributes
 * @return string
 */
function link_to_route($name, $title = null, $parameters = array(), $attributes = array())
{
	if (Route::getRoutes()->hasNamedRoute($name))
		return app('html')->linkRoute($name, $title, $parameters, $attributes);
	else
		return '<a href="javascript:void(0)"'.HTML::attributes($attributes).'>'.$name.'</a>';
}


/*
|--------------------------------------------------------------------------
| Extends Custom Configuration File
|--------------------------------------------------------------------------
|
*/

/**
 * Style Sheet Alias Load (Batches load support)
 * @param  string|array $aliases    Alias in configuration files
 * @param  array        $attributes Add param array in tags required
 * @return string
 */
function style($aliases, $attributes = array(), $interim = '')
{
	if (is_array($aliases)) {
		foreach ($aliases as $key => $value) {
			$interim .= (is_int($key)) ? style($value, $attributes, $interim) : style($key, $value, $interim);
		}
		return $interim;
	}
	$cssAliases = Config::get('extend.webAssets.cssAliases');
	$url        = isset($cssAliases[$aliases]) ? $cssAliases[$aliases] : $aliases;
	return HTML::style($url, $attributes);
}

/**
 * Script Alias Load (Batches load support)
 * @param  string|array $aliases    Alias in configuration files
 * @param  array        $attributes Add param array in tags required
 * @return string
 */
function script($aliases, $attributes = array(), $interim = '')
{
	if (is_array($aliases)) {
		foreach ($aliases as $key => $value) {
			$interim .= (is_int($key)) ? script($value, $attributes, $interim) : script($key, $value, $interim);
		}
		return $interim;
	}
	$jsAliases = Config::get('extend.webAssets.jsAliases');
	$url       = isset($jsAliases[$aliases]) ? $jsAliases[$aliases] : $aliases;
	return HTML::script($url, $attributes);
}

/**
 * Script Alias Load (Supplement) For document.write(）in JavaScript
 * @param  string $aliases    Alias in configuration files
 * @param  array  $attributes Add param array in tags required
 * @return string
 */
function or_script($aliases, $attributes = array())
{
	$jsAliases         = Config::get('extend.webAssets.jsAliases');
	$url               = isset($jsAliases[$aliases]) ? $jsAliases[$aliases] : $aliases;
	$attributes['src'] = URL::asset($url);
	return "'<script".HTML::attributes($attributes).">'+'<'+'/script>'";
}

/*
|--------------------------------------------------------------------------
| Custom Core Functions
|--------------------------------------------------------------------------
|
*/

/**
 * Batches Define Constant
 * @param  array  $define Constant and array of value
 * @return void
 */
function define_array($define = array())
{
	foreach ($define as $key => $value)
		defined($key) OR define($key, $value);
}

/**
 * Friendly date output
 * @param  string|\Carbon\Carbon $theDate Date string before handle | \Carbon\Carbon
 * @return string                         Friendly date string
 */
function friendly_date($theDate)
{
	// 获取待处理的日期对象
	if (! $theDate instanceof \Carbon\Carbon)
		$theDate = \Carbon\Carbon::createFromTimestamp(strtotime($theDate));
	// 取得英文日期描述
	$friendlyDateString = $theDate->diffForHumans(\Carbon\Carbon::now());
	// 本地化
	$friendlyDateArray  = explode(' ', $friendlyDateString);
	$friendlyDateString = $friendlyDateArray[0]
		.Lang::get('friendlyDate.'.$friendlyDateArray[1])
		.Lang::get('friendlyDate.'.$friendlyDateArray[2]);
	// Return data
	return $friendlyDateString;
}

/**
 * 拓展分页输出，支持临时指定分页模板
 * @param  Illuminate\Pagination\Paginator $paginator 分页查询结果的最终实例
 * @param  string                          $viewName  分页视图名称
 * @return \Illuminate\View\View
 */
function pagination(Illuminate\Pagination\Paginator $paginator, $viewName = null)
{
	$viewName = $viewName ?: Config::get('view.pagination');
	$paginator->getFactory()->setViewName($viewName);
	return $paginator->links();
}

/**
 * 反引用一个经过 e（htmlentities）和 addslashes 处理的字符串
 * @param  string $string String before convert
 * @return String after convert
 */
function strip($string)
{
	return stripslashes(HTML::decode($string));
}


/*
|--------------------------------------------------------------------------
| Public Function Library
|--------------------------------------------------------------------------
|
*/

/**
 * Closing HTML tag (this function is still flawed, unable to process incomplete labels, there is no better plan, caution)
 * @param  string $html HTML String
 * @return string
 */
function close_tags($html)
{
	// Labels needn't to complete
	$arr_single_tags = array('meta', 'img', 'br', 'link', 'area');
	// Match the start tag
	preg_match_all('#<([a-z1-6]+)(?: .*)?(?<![/|/ ])>#iU', $html, $result);
	$openedtags = $result[1];
	// Match the close tag
	preg_match_all('#</([a-z]+)>#iU', $html, $result);
	$closedtags = $result[1];
	// Close opened tab quantity is calculated, and if the same return HTML data
	if (count($closedtags) === count($openedtags)) return $html;
	// Reverse sort the array, the last open tab at the top
	$openedtags = array_reverse($openedtags);
	// Traversing the open tags array
	foreach ($openedtags as $key => $value) {
		// Skip without closing tags
		if (in_array($value, $arr_single_tags)) continue;
		// Started complete
		if (in_array($value, $closedtags)) {
			unset($closedtags[array_search($value, $closedtags)]);
		} else {
			$html .= '</'.$value.'>';
		}
	}
	return $html;
}

/**
 * Resources list sort
 * @param  string $columnName Column name
 * @param  string $default    If the default sort column，up Default Ascending, down Default descending
 * @return string             a Tag sort icon
 */
function order_by($columnName = '', $default = null)
{
	$sortColumnName = Input::get('sort_up', Input::get('sort_down', false));
	if (Input::get('sort_up')) {
		$except = 'sort_up'; $orderType = 'sort_down';
	} else {
		$except = 'sort_down' ; $orderType = 'sort_up';
	}
	if ($sortColumnName == $columnName) {
		$parameters = array_merge(Input::except($except), array($orderType => $columnName));
		$icon       = Input::get('sort_up') ? 'chevron-up' : 'chevron-down' ;
	} elseif ($sortColumnName === false && $default == 'asc') {
		$parameters = array_merge(Input::all(), array('sort_down' => $columnName));
		$icon       = 'chevron-up';
	} elseif ($sortColumnName === false && $default == 'desc') {
		$parameters = array_merge(Input::all(), array('sort_up' => $columnName));
		$icon       = 'chevron-down';
	} else {
		$parameters = array_merge(Input::except($except), array('sort_up' => $columnName));
		$icon       = 'random';
	}
	$a  = '<a href="';
	$a .= action(Route::current()->getActionName(), $parameters);
	$a .= '" class="glyphicon glyphicon-'.$icon.'"></a>';
	return $a;
}

/*
|--------------------------------------------------------------------------
| Server Function
|--------------------------------------------------------------------------
|
*/

/**
 * @param  memory usage
 * @return string
 */

function memory_usage()
{
	$memory  = ( ! function_exists('memory_get_usage')) ? '0' : round(memory_get_usage()/1024/1024, 2).'MB';
	return $memory;
}

/**
 * @param  Timing
 * @return string
 */

function microtime_float()
{
	$mtime = microtime();
	$mtime = explode(' ', $mtime);
	return $mtime[1] + $mtime[0];
}

/**
 * @param  Unit Conversion
 * @return string
 */

function formatsize($size)
{
	$danwei		= array(' B ',' K ',' M ',' G ',' T ');
	$allsize	= array();
	$i			= 0;
	for($i = 0; $i <5; $i++)
	{
		if(floor($size/pow(1024,$i))==0){break;}
	}
	for($l = $i-1; $l >=0; $l--)
	{
		$allsize1[$l]=floor($size/pow(1024,$l));
		$allsize[$l]=$allsize1[$l]-$allsize1[$l+1]*1024;
	}
	$len=count($allsize);
	for($j = $len-1; $j >=0; $j--)
	{
		$fsize=$fsize.$allsize[$j].$danwei[$j];
	}
	return $fsize;
}

function valid_email($str)
{
	return ( ! preg_match("/^([a-z0-9\+_\-]+)(\.[a-z0-9\+_\-]+)*@([a-z0-9\-]+\.)+[a-z]{2,6}$/ix", $str)) ? FALSE : TRUE;
}

/**
 * @param  Detection PHP setting parameters
 * @return string
 */

function show($varName)
{
	switch($result = get_cfg_var($varName))
	{
		case 0:
			return '<font color="red">×</font>';
		break;
		case 1:
			return '<font color="#45bf7b">√</font>';
		break;
		default:
			return $result;
		break;
	}
}

/**
 * @param Detection Function Support
 * @return string
 */

function isfun($funName = '')
{
	if (!$funName || trim($funName) == '' || preg_match('~[^a-z0-9\_]+~i', $funName, $tmp)) return '错误';
	return (false !== function_exists($funName)) ? '<font color="#45bf7b">√</font>' : '<font color="red">×</font>';
}

function isfun1($funName = '')
{
	if (!$funName || trim($funName) == '' || preg_match('~[^a-z0-9\_]+~i', $funName, $tmp)) return '错误';
	return (false !== function_exists($funName)) ? '<font color="#45bf7b">√</font>' : '×';
}

/**
 * @param  Linux Detects
 * @return string
 */

function sys_linux()
{
	// CPU
	if (false === ($str = @file("/proc/cpuinfo"))) return false;
	$str = implode("", $str);
	@preg_match_all("/model\s+name\s{0,}\:+\s{0,}([\w\s\)\(\@.-]+)([\r\n]+)/s", $str, $model);
	@preg_match_all("/cpu\s+MHz\s{0,}\:+\s{0,}([\d\.]+)[\r\n]+/", $str, $mhz);
	@preg_match_all("/cache\s+size\s{0,}\:+\s{0,}([\d\.]+\s{0,}[A-Z]+[\r\n]+)/", $str, $cache);
	@preg_match_all("/bogomips\s{0,}\:+\s{0,}([\d\.]+)[\r\n]+/", $str, $bogomips);
	if (false !== is_array($model[1]))
	{
		$res['cpu']['num'] = sizeof($model[1]);
		/*
		for($i = 0; $i < $res['cpu']['num']; $i++)
		{
			$res['cpu']['model'][] = $model[1][$i].'&nbsp;('.$mhz[1][$i].')';
			$res['cpu']['mhz'][] = $mhz[1][$i];
			$res['cpu']['cache'][] = $cache[1][$i];
			$res['cpu']['bogomips'][] = $bogomips[1][$i];
		}*/
		if($res['cpu']['num']==1)
			$x1 = '';
		else
			$x1 = ' ×'.$res['cpu']['num'];
		$mhz[1][0]				= ' | ' . Lang::get('admin/server/index.cpu_frequency') . ':' .$mhz[1][0];
		$cache[1][0]			= ' | ' . Lang::get('admin/server/index.cpu_l2_cache') . ':'.$cache[1][0];
		$bogomips[1][0]			= ' | Bogomips:'.$bogomips[1][0];
		$res['cpu']['model'][]	= $model[1][0].$mhz[1][0].$cache[1][0].$bogomips[1][0].$x1;
		if (false !== is_array($res['cpu']['model'])) $res['cpu']['model'] = implode("<br />", $res['cpu']['model']);
		if (false !== is_array($res['cpu']['mhz'])) $res['cpu']['mhz'] = implode("<br />", $res['cpu']['mhz']);
		if (false !== is_array($res['cpu']['cache'])) $res['cpu']['cache'] = implode("<br />", $res['cpu']['cache']);
		if (false !== is_array($res['cpu']['bogomips'])) $res['cpu']['bogomips'] = implode("<br />", $res['cpu']['bogomips']);
	}
	// Network
	// Uptime
	if (false === ($str = @file("/proc/uptime"))) return false;
	$str	= explode(" ", implode("", $str));
	$str	= trim($str[0]);
	$min	= $str / 60;
	$hours	= $min / 60;
	$days	= floor($hours / 24);
	$hours	= floor($hours - ($days * 24));
	$min	= floor($min - ($days * 60 * 24) - ($hours * 60));
	if ($days !== 0) $res['uptime'] = $days.Lang::get('admin/server/index.day');
	if ($hours !== 0) $res['uptime'] .= $hours.Lang::get('admin/server/index.hour');
	$res['uptime'] .= $min. Lang::get('admin/server/index.minute');
	// MEMORY
	if (false === ($str = @file("/proc/meminfo"))) return false;
	$str = implode("", $str);
	preg_match_all("/MemTotal\s{0,}\:+\s{0,}([\d\.]+).+?MemFree\s{0,}\:+\s{0,}([\d\.]+).+?Cached\s{0,}\:+\s{0,}([\d\.]+).+?SwapTotal\s{0,}\:+\s{0,}([\d\.]+).+?SwapFree\s{0,}\:+\s{0,}([\d\.]+)/s", $str, $buf);
	preg_match_all("/Buffers\s{0,}\:+\s{0,}([\d\.]+)/s", $str, $buffers);
	$res['memTotal']			= round($buf[1][0]/1024, 2);
	$res['memFree']				= round($buf[2][0]/1024, 2);
	$res['memBuffers']			= round($buffers[1][0]/1024, 2);
	$res['memCached']			= round($buf[3][0]/1024, 2);
	$res['memUsed']				= $res['memTotal']-$res['memFree'];
	$res['memPercent']			= (floatval($res['memTotal'])!=0)?round($res['memUsed']/$res['memTotal']*100,2):0;
	$res['memRealUsed']			= $res['memTotal'] - $res['memFree'] - $res['memCached'] - $res['memBuffers']; // Real Memory Usage
	$res['memRealFree']			= $res['memTotal'] - $res['memRealUsed']; // Real Memory Free
	$res['memRealPercent']		= (floatval($res['memTotal'])!=0)?round($res['memRealUsed']/$res['memTotal']*100,2):0; // Real Memory Usage Ratio
	$res['memCachedPercent']	= (floatval($res['memCached'])!=0)?round($res['memCached']/$res['memTotal']*100,2):0; // Cached Memory Usage Ratio
	$res['swapTotal']			= round($buf[4][0]/1024, 2);
	$res['swapFree']			= round($buf[5][0]/1024, 2);
	$res['swapUsed']			= round($res['swapTotal']-$res['swapFree'], 2);
	$res['swapPercent']			= (floatval($res['swapTotal'])!=0)?round($res['swapUsed']/$res['swapTotal']*100,2):0;
	// Load Avg
	if (false === ($str = @file("/proc/loadavg"))) return false;
	$str			= explode(" ", implode("", $str));
	$str			= array_chunk($str, 4);
	$res['loadAvg']	= implode(" ", $str[0]);
	return $res;
}

/**
 * @param  FreeBSD Detects
 * @return string
 */

function sys_freebsd()
{
	// CPU
	if (false === ($res['cpu']['num'] = get_key("hw.ncpu"))) return false;
	$res['cpu']['model'] = get_key("hw.model");
	// Load Avg
	if (false === ($res['loadAvg'] = get_key("vm.loadavg"))) return false;
	// Uptime
	if (false === ($buf = get_key("kern.boottime"))) return false;
	$buf 		= explode(' ', $buf);
	$sys_ticks	= time() - intval($buf[3]);
	$min		= $sys_ticks / 60;
	$hours		= $min / 60;
	$days		= floor($hours / 24);
	$hours		= floor($hours - ($days * 24));
	$min		= floor($min - ($days * 60 * 24) - ($hours * 60));
	if ($days !== 0) $res['uptime'] = $days."天";
	if ($hours !== 0) $res['uptime'] .= $hours."小时";
	$res['uptime'] .= $min."分钟";
	// Memory
	if (false === ($buf = get_key("hw.physmem"))) return false;
	$res['memTotal'] = round($buf/1024/1024, 2);
	$str = get_key("vm.vmtotal");
	preg_match_all("/\nVirtual Memory[\:\s]*\(Total[\:\s]*([\d]+)K[\,\s]*Active[\:\s]*([\d]+)K\)\n/i", $str, $buff, PREG_SET_ORDER);
	preg_match_all("/\nReal Memory[\:\s]*\(Total[\:\s]*([\d]+)K[\,\s]*Active[\:\s]*([\d]+)K\)\n/i", $str, $buf, PREG_SET_ORDER);
	$res['memRealUsed'] = round($buf[0][2]/1024, 2);
	$res['memCached'] = round($buff[0][2]/1024, 2);
	$res['memUsed'] = round($buf[0][1]/1024, 2) + $res['memCached'];
	$res['memFree'] = $res['memTotal'] - $res['memUsed'];
	$res['memPercent'] = (floatval($res['memTotal'])!=0)?round($res['memUsed']/$res['memTotal']*100,2):0;
	$res['memRealPercent'] = (floatval($res['memTotal'])!=0)?round($res['memRealUsed']/$res['memTotal']*100,2):0;
	return $res;
}

/**
 * @param  Obtain parameter values FreeBSD
 * @return string
 */

function get_key($keyName)
{
	return do_command('sysctl', "-n $keyName");
}
// Determine the location of the executable file FreeBSD
function find_command($commandName)
{
	$path = array('/bin', '/sbin', '/usr/bin', '/usr/sbin', '/usr/local/bin', '/usr/local/sbin');
	foreach($path as $p)
	{
		if (@is_executable("$p/$commandName")) return "$p/$commandName";
	}
	return false;
}

/**
 * @param  Execute System Commands FreeBSD
 * @return string
 */

function do_command($commandName, $args)
{
	$buffer = "";
	if (false === ($command = find_command($commandName))) return false;
	if ($fp = @popen("$command $args", 'r'))
	{
		while (!@feof($fp))
		{
			$buffer .= @fgets($fp, 4096);
		}
		return trim($buffer);
	}
	return false;
}

/**
 * @param  Windows Detects
 * @return string
 */

function sys_windows()
{
	if (PHP_VERSION >= 5) {
		$objLocator	= new COM("WbemScripting.SWbemLocator");
		$wmi		= $objLocator->ConnectServer();
		$prop		= $wmi->get("Win32_PnPEntity");
	} else {
		return false;
	}
	// CPU
	$cpuinfo = GetWMI($wmi,"Win32_Processor", array("Name","L2CacheSize","NumberOfCores"));
	$res['cpu']['num'] = $cpuinfo[0]['NumberOfCores'];
	if (null == $res['cpu']['num']) {
		$res['cpu']['num'] = 1;

	}
	/*
		for ($i=0;$i<$res['cpu']['num'];$i++)
		{
			$res['cpu']['model'] .= $cpuinfo[0]['Name']."<br />";
			$res['cpu']['cache'] .= $cpuinfo[0]['L2CacheSize']."<br />";
		}
	*/
	$cpuinfo[0]['L2CacheSize'] = ' ('.$cpuinfo[0]['L2CacheSize'].')';
	if($res['cpu']['num']==1)
		$x1 = '';
	else
		$x1 = ' ×'.$res['cpu']['num'];
	$res['cpu']['model'] 		= $cpuinfo[0]['Name'].$cpuinfo[0]['L2CacheSize'].$x1;
	// SYSINFO
	$sysinfo					= GetWMI($wmi,"Win32_OperatingSystem", array('LastBootUpTime','TotalVisibleMemorySize','FreePhysicalMemory','Caption','CSDVersion','SerialNumber','InstallDate'));
	$sysinfo[0]['Caption']		=iconv('GBK', 'UTF-8',$sysinfo[0]['Caption']);
	$sysinfo[0]['CSDVersion']	=iconv('GBK', 'UTF-8',$sysinfo[0]['CSDVersion']);
	$res['win_n']				= $sysinfo[0]['Caption']." ".$sysinfo[0]['CSDVersion']." 序列号:{$sysinfo[0]['SerialNumber']} 于".date('Y年m月d日H:i:s',strtotime(substr($sysinfo[0]['InstallDate'],0,14)))."安装";
	// Uptime
	$res['uptime']				= $sysinfo[0]['LastBootUpTime'];
	$sys_ticks					= 3600*8 + time() - strtotime(substr($res['uptime'],0,14));
	$min						= $sys_ticks / 60;
	$hours						= $min / 60;
	$days						= floor($hours / 24);
	$hours						= floor($hours - ($days * 24));
	$min						= floor($min - ($days * 60 * 24) - ($hours * 60));
	if ($days !== 0) $res['uptime'] = $days."天";
	if ($hours !== 0) $res['uptime'] .= $hours."小时";
	$res['uptime'] .= $min."分钟";
	// Memory
	$res['memTotal']	= round($sysinfo[0]['TotalVisibleMemorySize']/1024,2);
	$res['memFree']		= round($sysinfo[0]['FreePhysicalMemory']/1024,2);
	$res['memUsed']		= $res['memTotal']-$res['memFree']; // 1024 has been divided by two lines of the above, in addition to this line no longer.
	$res['memPercent']	= round($res['memUsed'] / $res['memTotal']*100,2);
	$swapinfo			= GetWMI($wmi,"Win32_PageFileUsage", array('AllocatedBaseSize','CurrentUsage'));
	// LoadPercentage
	$loadinfo			= GetWMI($wmi,"Win32_Processor", array("LoadPercentage"));
	$res['loadAvg']		= $loadinfo[0]['LoadPercentage'];
	return $res;
}

function GetWMI($wmi,$strClass, $strValue = array())
{
	$arrData	= array();
	$objWEBM	= $wmi->Get($strClass);
	$arrProp	= $objWEBM->Properties_;
	$arrWEBMCol	= $objWEBM->Instances_();
	foreach($arrWEBMCol as $objItem)
	{
		@reset($arrProp);
		$arrInstance = array();
		foreach($arrProp as $propItem)
		{
			eval("\$value = \$objItem->" . $propItem->Name . ";");
			if (empty($strValue))
			{
				$arrInstance[$propItem->Name] = trim($value);
			}
			else
			{
				if (in_array($propItem->Name, $strValue))
				{
					$arrInstance[$propItem->Name] = trim($value);
				}
			}
		}
		$arrData[] = $arrInstance;
	}
	return $arrData;
}

/**
 * Get user constellation
 * @param  init $constellation User constellation code
 * @return array               User constellation
 */
function getConstellation($constellation)
{
	if($constellation == NULL)
	{
		$constellationInfo = array(
			'icon' => 'default.png',
			'name' => Lang::get('account/constellation.unset')
		);
		return $constellationInfo;
	} else {
		switch ($constellation)
		{
			case "1":
				$constellationIcon = 'shuipin.png';
				$constellationName = Lang::get('account/constellation.aquarius');
			break;
			case "2":
				$constellationIcon = 'shuangyu.png';
				$constellationName = Lang::get('account/constellation.pisces');
			break;
			case "3":
				$constellationIcon = 'baiyang.png';
				$constellationName = Lang::get('account/constellation.aries');
			break;
			case "4":
				$constellationIcon = 'jinniu.png';
				$constellationName = Lang::get('account/constellation.taurus');
			break;
			case "5":
				$constellationIcon = 'shuangzi.png';
				$constellationName = Lang::get('account/constellation.gemini');
			break;
			case "6":
				$constellationIcon = 'juxie.png';
				$constellationName = Lang::get('account/constellation.cancer');
			break;
			case "7":
				$constellationIcon = 'shizi.png';
				$constellationName = Lang::get('account/constellation.leo');
			break;
			case "8":
				$constellationIcon = 'chunv.png';
				$constellationName = Lang::get('account/constellation.virgo');
			break;
			case "9":
				$constellationIcon = 'tiancheng.png';
				$constellationName = Lang::get('account/constellation.libra');
			break;
			case "10":
				$constellationIcon = 'tianxie.png';
				$constellationName = Lang::get('account/constellation.scorpio');
			break;
			case "11":
				$constellationIcon = 'sheshou.png';
				$constellationName = Lang::get('account/constellation.sagittarius');
			break;
			case "12":
				$constellationIcon = 'mojie.png';
				$constellationName = Lang::get('account/constellation.capricorn');
			break;
			default:
				$constellationIcon = 'default.png';
				$constellationName = Lang::get('account/constellation.unset');
		}

		$constellationInfo = array(
			'icon' => $constellationIcon,
			'name' => $constellationName
		);
		return $constellationInfo;
	}
}

/**
 * Get user tag
 * @param  string $tag User tag in database
 * @return string      Tag name
 */
function getTagName($tag)
{
	switch ($tag)
	{
		case "1":
			$tagName = '高冷';
		break;
		case "2":
			$tagName = '颜控';
		break;
		case "3":
			$tagName = '女神';
		break;
		case "4":
			$tagName = '萌萌哒';
		break;
		case "5":
			$tagName = '治愈系';
		break;
		case "6":
			$tagName = '小清新';
		break;
		case "7":
			$tagName = '女王范';
		break;
		case "8":
			$tagName = '天然呆';
		break;
		case "9":
			$tagName = '萝莉';
		break;
		case "10":
			$tagName = '静待缘分';
		break;
		case "11":
			$tagName = '减肥ing';
		break;
		case "12":
			$tagName = '戒烟ing';
		break;
		case "13":
			$tagName = '缺爱ing';
		break;
		case "14":
			$tagName = '暖男';
		break;
		case "15":
			$tagName = '创业者';
		break;
		case "16":
			$tagName = '直率';
		break;
		case "17":
			$tagName = '懒';
		break;
		case "18":
			$tagName = '感性';
		break;
		case "19":
			$tagName = '理性';
		break;
		case "20":
			$tagName = '温柔细心';
		break;
		case "21":
			$tagName = '暴脾气';
		break;
		case "22":
			$tagName = '技术宅';
		break;
		case "23":
			$tagName = '文艺病';
		break;
		case "24":
			$tagName = '爱旅行';
		break;
		case "25":
			$tagName = '健身狂魔';
		break;
		case "26":
			$tagName = '考研ing';
		break;
		case "27":
			$tagName = '吃货';
		break;
		case "28":
			$tagName = '长腿欧巴';
		break;
		case "29":
			$tagName = '街舞solo';
		break;
		case "30":
			$tagName = '爱音乐';
		break;
		case "31":
			$tagName = '幽默';
		break;
		case "32":
			$tagName = '乐观';
		break;
		case "33":
			$tagName = '事业型';
		break;
		case "34":
			$tagName = '完美主义';
		break;
		case "35":
			$tagName = '情商略高';
		break;
		case "36":
			$tagName = '阳光';
		break;
		case "37":
			$tagName = '学霸';
		break;
		case "38":
			$tagName = '执着';
		break;
		case "39":
			$tagName = '自信';
		break;
		case "40":
			$tagName = '独立型';
		break;
		default:
			$tagName = '无标签';
		break;
	}
	return $tagName;
}

/**
 * Easemob Web IM API
 * @return object easemob configure
 */
function getEasemob()
{
	$easemob			= System::where('name', 'easemob')->first(); // Get easemod API config
	$nowTime			= new DateTime(); // Now time
	$easemobUpdated		= $nowTime->getTimestamp() - strtotime($easemob->updated_at); // Calculate last update timestamp
	// Get token
	if($easemob->token == NULL) // First get token
	{
		$accessToken 	= cURL::newJsonRequest('post', 'https://a1.easemob.com/jinglingkj/pinai/token', ['grant_type' => 'client_credentials','client_id' => $easemob->sid, 'client_secret' => $easemob->secret])->setHeader('content-type', 'application/json')->send(); // Send cURL
		$accessToken	= json_decode($accessToken->body, true); // Json decode
		$easemob->token	= $accessToken['access_token'];
		$easemob->save(); // Save access token
	} elseif($easemobUpdated < 172800) // Last update timestamp 2 Days (201600 - 3 days)
	{
		$accessToken 	= cURL::newJsonRequest('post', 'https://a1.easemob.com/jinglingkj/pinai/token', ['grant_type' => 'client_credentials','client_id' => $easemob->sid, 'client_secret' => $easemob->secret])->setHeader('content-type', 'application/json')->send(); // Send cURL
		$accessToken	= json_decode($accessToken->body, true); // Json decode
		$easemob->token	= $accessToken['access_token'];
		$easemob->save(); // Save access token
	}
	return $easemob;
}

/**
 * Create Notification
 * @param Integer $category  	Category code
 * @param Integer $receiverId 	Receiver ID
 * @param Integer $senderId  	Sender ID
 */
function Notification($category, $senderId, $receiverId)
{
	$notification				= new Notification;
	$notification->sender_id	= $senderId;
	$notification->receiver_id	= $receiverId;
	$notification->category 	= $category;
	$notification->save();

	return $notification;
}

/**
 * Create Notifications
 * @param Integer $category  	Category code
 * @param Integer $receiverId 	Receiver ID
 * @param Integer $senderId  	Sender ID
 */
function Notifications($category, $senderId, $receiverId, $category_id, $post_id, $comment_id, $reply_id)
{
	$notification				= new Notification;
	$notification->sender_id	= $senderId;
	$notification->receiver_id	= $receiverId;
	$notification->category 	= $category;
	if($category_id){
		$notification->category_id 	= $category_id;
	}
	if($post_id){
		$notification->post_id 		= $post_id;
	}
	if($comment_id){
		$notification->comment_id 	= $comment_id;
	}
	if($reply_id){
		$notification->reply_id 	= $reply_id;
	}
	$notification->save();

	return $notification;
}

/**
 * Get Notification Content
 * @param Integer $catrgory  	Category code
 * @param Integer $sender_id 	Sender ID
 * @return Array            	Notification title and content
 */
function getNotification($category, $sender_id)
{
	$sender = User::where('id', $sender_id)->first();
	switch ($category) {
		case '1':
			$notificationTitle 		= '好友请求消息';
			$notificationContent	= $sender->nickname.'追你';
			break;
		case '2':
			$notificationTitle 		= '好友请求消息';
			$notificationContent 	= $sender->nickname.'再次追你';
			break;
		case '3':
			$notificationTitle 		= '好友请求消息';
			$notificationContent	= $sender->nickname.'接受了你的邀请';
			break;
		case '4':
			$notificationTitle 		= '好友请求消息';
			$notificationContent 	= $sender->nickname.'拒绝了你的邀请';
			break;
		case '5':
			$notificationTitle 		= '好友关系提醒';
			$notificationContent 	= $sender->nickname.'将你加入了黑名单';
			break;
		case '6':
			$notificationTitle 		= '论坛消息';
			$notificationContent 	= $sender->nickname.'评论了你发布帖子，快去看看吧';
			break;
		case '7':
			$notificationTitle 		= '论坛消息';
			$notificationContent 	= $sender->nickname.'评回复了你的评论，快去看看吧';
			break;
		case '8':
			$notificationTitle 		= '系统消息';
			$notificationContent 	= '系统消息';
			break;
		case '9':
			$notificationTitle 		= '系统消息';
			$notificationContent 	= '系统消息';
			break;
		case '10':
			$notificationTitle 		= '好友关系提醒';
			$notificationContent 	= $sender->nickname.'解除了对你的拉黑';
			break;
	}
	$notification = array(
		'title'		=> $notificationTitle,
		'content'	=> $notificationContent
	);
	return $notification;
}

/**
 * Calculate diff between two days
 * @param  date $day1 format:Y-m-d
 * @param  date $day2 format:Y-m-d
 * @return int
 */
function diffBetweenTwoDays ($day1, $day2)
{
	$second1 = strtotime($day1);
	$second2 = strtotime($day2);

	if ($second1 < $second2) {
		$tmp = $second2;
		$second2 = $second1;
		$second1 = $tmp;
	}

	return ($second1 - $second2) / 86400;
}

/**
 * Get plain text intro from html
 * @param  string $html     HTML code
 * @param  int $numchars 	Abstract of the number of characters
 * @return string          	Pain text intro from html
 */
function getplaintextintrofromhtml($html, $numchars) {
	// Remove the HTML tags
	$html = strip_tags($html);

	// Convert HTML entities to single characters
	//
	$html = html_entity_decode($html, ENT_QUOTES, 'UTF-8');

	// Make the string the desired number of characters
	// Note that substr is not good as it counts by bytes and not characters

	$html = mb_substr($html, 0, $numchars, 'UTF-8');

	// Add an elipsis
	$html .= "…";
	return $html;
}

/**
 * Convert br
 * @param  string $string Before convert string
 * @return string         After convert string
 */
function convertBr($string) {
	$breaks	= array("<br />","<br>","<br/>");
	$string	= str_ireplace($breaks, "\n", $string);
	return $string;
}

/**
 * Bad words filter
 * @param  string $text Before filter bad words
 * @return string       After filter bad words
 */
function badWordsFilter($text) {
	require __DIR__.'/api/wordfilter/badword.src.php';
	$text = strtr($text, array_combine($badword,array_fill(0,count($badword),'*')));
	return $text;
}

/**
 * String to array
 * @param  string 		App input string filter
 * @return string       result
 */
function app_input_filter($input_filter_string) {
	return badWordsFilter(strip_tags(trim(nl2br($input_filter_string), true), '<img><br>'));
}

/**
 * String to array
 * @param  string 		App output string filter
 * @return string 		result
 */
function app_out_filter($out_filter_string) {
	// Define breaks convert rules
	$breaks		= array("<br />", "<br>", "<br/>", "<p>", "</p>");
	return strip_tags(html_entity_decode(e(badWordsFilter(str_replace("&nbsp;", " ", str_ireplace($breaks, '\\n', $out_filter_string)))), ENT_QUOTES, 'utf-8'), '<img>');
}