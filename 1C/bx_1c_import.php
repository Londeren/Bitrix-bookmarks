<?
////////////////////////////////////////////////////////////
////Created by C_arter {< hello from bitrix's support >}////
//https://dev.1c-bitrix.ru/community/blogs/carter/2285.php//
////////////////////////////////////////////////////////////

global $DBDebugToFile;
$DBDebugToFile = true;

define('ver', '2.7');
define('MENULINES', 5);
$APicture=Array('jpg','jpeg','gif','png');
$UPLOAD_DIR='/upload';
$script_name=$_SERVER['SCRIPT_NAME'];
define("SCRIPT_NAME",$_SERVER['SCRIPT_NAME']);
$catalog_import_path='/bitrix/admin/1c_exchange.php';
$user_import_path='/bitrix/admin/1c_intranet.php';
$is_bitrix_dir=strpos($_SERVER['DOCUMENT_ROOT'].$script_name,$_SERVER['DOCUMENT_ROOT'].'/bitrix');


$mess=Array(
		'OPTIONS'=>'Проверка доступа к методам',
		'PROPFIND'=>'Получение структуры файлов и папок',
		'CREATE_FOLDER'=>'Создание папки',
		'DELETE_FOLDER'=>'Удаление папки',
		'EDIT_FOLDER'=>'Удаление папки',
		'COPY_FOLDER'=>'Копирование папки',
		'CREATE_FILE'=>'Создание файла',
		'COPY_FILE'=>'Копирование файла',
		'DELETE_FILE'=>'Удаление файла',
		'PROPPATCH_FILE'=>'Изменение свойства файла',
		'EDIT_FILE'=>'Удаление файла',
		'PATH'=>'Путь',
		'ROOT'=>'Корень',
		'LOGIN'=>'Логин',
		'PASS'=>'Пароль',
		'SERVER'=>'Сервер',
		'TAB_TEST'=>'Тест',
		'TAB_EXPLORER'=>'Обзор',
		'TAB_XML'=>'XML',
		'TAB_HEADERS'=>'Заголовки',
		'START_TEST'=>'протестировать',
		'TEST_CONTENT'=>'Это тестовый файл, содержащий тестовый контент!',
		);

function __ShowMessage($text,$lamp='red')
{
	if ($lamp=='green')
		return '<span style="font-size:14px;color:green;">'.$text.'</span>';
	else
		return '<span style="color:red;font-size:14px;">'.$text.'</span>';
}

function basename2($path)
{
	$file_array = explode("/",$path);
	$n = count($file_array);
	$file= $file_array[$n-1];
	return $file;
}

class iWebDav
{
	var $debug = false;
	var $fp;
	var $server;
	var $port = 80;
	var $path ='/';
	var $user;
	var $protocol = 'HTTP/1.1';
	var $protocol_s = 'http://';
	var $method = 'GET';
	var $pass;
	var $socket_timeout = 60;
	var $errno;
	var $errstr;
	var $user_agent = 'Microsoft-WebDAV-MiniRedir/6.1.7600';
	var $crlf = "\r\n";
	var $req;
	var $resp_status;
	var $parser;
	var $headers;
	var $body="";
	var $response;
	var $log = true;
	var $logfile="/wd_test_log.log";

	function addHeader($header = false)
	{
		if ($header == false)
			return false;
		$this->headers.=$header.$this->crlf;
	}

	function setServer($server = false)
	{
		$this->server=$server;
	}

	function setPath($path = false)
	{
		$this->path=$path;
	}

	function setMethod($method = false)
	{
		$this->method=$method;
	}

	function request()
	{

		$pr="";
		$AuthData=base64_encode($this->user.":".$this->pass);
		if ($this->port==443)
			$p='ssl://';

		$this->fp = fsockopen($p.$this->server, $this->port, $this->errno, $this->errstr, $this->socket_timeout);
		if ($this->errstr)
		{
			$aResult['error']=$this->errstr;
			return $this->response=$aResult;

		}
		if ($this->fp)
		{
			$this->setPath(str_replace(Array('///','//'),'/',$this->path.'/'));
			$headers=$this->method." ".str_replace(' ','%20',$this->path)." ".$this->protocol.$this->crlf;
			$headers.="Host: ".$this->server.$this->crlf;
			$headers.="User-Agent: ".$this->user_agent.$this->crlf;
			$headers.="Authorization: Basic ".$AuthData.$this->crlf;
			$this->headers=$headers.$this->headers.$this->crlf;
			if (strlen($this->body)>0)
				$this->headers.=$this->body;
			//echo $this->headers;
			fwrite($this->fp, $this->headers);
			while (!feof($this->fp))
			$this->response.=fgets($this->fp, 128);
			fclose($this->fp);
			$response=explode($this->crlf.$this->crlf,$this->response);
			$aResult['headers']=$this->ParseHeaders($response[0]);
			$aResult['~headers']=$response[0];
			$aResult['body']=$response[1];
			//echo $aResult['body'];
			if ($this->log)
			{
				$f = fopen($_SERVER['DOCUMENT_ROOT'].$this->logfile, 'a+');
				fwrite ($f,$this->crlf.'----------'.$this->crlf);
				fwrite ($f,'Запрос:'.$this->crlf);
				fwrite ($f,$this->headers);
				fwrite ($f,$this->crlf.$this->crlf.'Ответ:'.$this->crlf);
				fwrite ($f,print_r($aResult['~headers'],true));
				fclose($f);
			}
		}

		return $this->response=$aResult;
	}

	function ParseHeaders($headers)
	{
		$headers=explode($this->crlf,$headers);
		foreach($headers as $header)
		{
			if (strpos($header,': '))
			{
				$header=explode(': ',$header);
				$arResult[$header[0]]=$header[1];
			}
			else
				$arResult['STATUS']=$header;
		}

		return $arResult;
	}
	function parseURL($url = false)
	{
		if ($url == false)
			return false;
		if (strpos($url,'https://')===0)
		{
			$this->port=443;
			$this->protocol_s="https://";
			$url=str_replace('https://','',$url);
		}
		else
		{
			$this->port=80;
			$this->protocol_s="http://";
			$url=str_replace('http://','',$url);
		}
		$this->server=substr($url,0,strpos($url,'/'));
		$url=str_replace($this->server,'',$url);
		$this->path=$url;
	}
}

class XmlToArray
{

    var $xml='';
    function XmlToArray($xml)
    {
       $this->xml = $xml;
    }

   function _struct_to_array($values, &$i)
    {
        $child = array();
        if (isset($values[$i]['value'])) array_push($child, $values[$i]['value']);

        while ($i++ < count($values)) {
            switch ($values[$i]['type']) {
                case 'cdata':
                array_push($child, $values[$i]['value']);
                break;

                case 'complete':
                    $name = $values[$i]['tag'];
                    if(!empty($name)){
                    $child[$name]= ($values[$i]['value'])?($values[$i]['value']):'';
                    if(isset($values[$i]['attributes'])) {
                        $child[$name] = $values[$i]['attributes'];
                    }
                }
              break;

                case 'open':
                    $name = $values[$i]['tag'];
                    $size = isset($child[$name]) ? sizeof($child[$name]) : 0;
                    $child[$name][$size] = $this->_struct_to_array($values, $i);
                break;

                case 'close':
                return $child;
                break;
            }
        }
        return $child;
    }

    function createArray()
    {
        $xml    = $this->xml;
        $values = array();
        $index  = array();
        $array  = array();
        $parser = xml_parser_create();
        xml_parser_set_option($parser, XML_OPTION_SKIP_WHITE, 1);
        xml_parser_set_option($parser, XML_OPTION_CASE_FOLDING, 0);
        xml_parse_into_struct($parser, $xml, $values, $index);
        xml_parser_free($parser);
        $i = 0;
        $name = $values[$i]['tag'];
        $array[$name] = isset($values[$i]['attributes']) ? $values[$i]['attributes'] : '';
        $array[$name] = $this->_struct_to_array($values, $i);
        return $array;
    }

}

if ((@$_REQUEST['mode']!='query' && @$_REQUEST['mode']!='exchange'))
define('NEED_AUTH',true);
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");



if ($_GET)
{
	if ($_REQUEST['login'])
		setcookie("DV_USER", $_REQUEST['login'],0);
	if ($_REQUEST['pass'])
		setcookie("DV_PASS", $_REQUEST['pass'], 0);
	$_SESSION['DAV_TEST']['DV_CURRENT_PATH']=$_REQUEST['current_path'];
	if ($_REQUEST['server']!=$_REQUEST['current_path'] && $_REQUEST['current_path'])
		$_REQUEST['server']=$_REQUEST['current_path'];
	if ($_REQUEST['server'])
		setcookie("DV_SERVER", $_REQUEST['server'],0);
	/*$_REQUEST['current_path']=trim($_REQUEST['current_path'],'/');
	$_REQUEST['root']=trim($_REQUEST['root'],'/');
	$_REQUEST['server']=trim($_REQUEST['server'],'/');*/
}



if (@$_REQUEST['mode']=='exchange')
{

	$data=CUtil::JsObjectToPhp($_REQUEST['data']);
	$req=new CHTTP;
	$req->SetAuthBasic($data['login'],$data['pass']);
	$URL=$data['url'];

	if (!$data['phpsessid'])
		$URL.='?mode=checkauth&type=catalog';
	else
	{
		$URL.='?mode=import&type=catalog&filename='.$data['filename'];
		$req->additional_headers['Cookie'] = 'PHPSESSID='.$data['phpsessid'].';';
	}
	$arUrl=$req->ParseURL($URL);

	$req->Query('GET',$arUrl['host'],$arUrl['port'],$arUrl['path_query']);

	$body=explode("\n",$req->result);
	if (count($body)>1)
	{

		$response['status']=$body[0];

		if ($response['status']=='success' && $body[1]=='PHPSESSID')
				$response['phpsessid']=$body[2];
		else
		{
			if (ToUpper($req->headers['Content-Type'])!=ToUpper('text/html; charset=utf-8'))
			$body=$APPLICATION->ConvertCharsetArray($body,'windows-1251','UTF-8');
			$response['text']=$body[1];
		}

	}
	else
		$response['error']=$body;
	foreach($req->headers as $key=>$value)
		$response['headers'].="<b>".$key."</b>: ".$value."<br/>";

	echo json_encode($response);
	die();
}

if (@$_REQUEST['action']=='setsession' && $_REQUEST['mode']!='query')
{
	$_SESSION['bx_1c_import'][$_REQUEST['name']]=$_REQUEST['value'];
	echo $_SESSION['bx_1c_import'][$_REQUEST['name']];
	die();
}

if ($_REQUEST['action']=="PROPFIND" || $_REQUEST['action']=="GET" )
{

	$dav=new iWebDav;
	if ($_REQUEST['filename'])
	{
		$path.='/'.$_REQUEST['filename'];
			header('Location: '.$_REQUEST['server'].$path);
			die();
	}

	$dav->setMethod($_REQUEST['action']);
	$dav->user=$_REQUEST['login'];
	$dav->pass=$_REQUEST['pass'];
	$dav->log=false;
	$dav->parseURL($_REQUEST['server']);
	$dav->addHeader('Content-Length: '.strlen($dav->body));
	$dav->addHeader('Connection: Close');
	$result=$dav->request();
	if (!$result['error'])
	{
		if (isset($_REQUEST['filename']))
		{
			echo $result['body'];
			die();
		}
		$parser=xml_parser_create();
		$server=$dav->protocol_s.$dav->server;
		$n=strpos($result['body'],'<?');
		if ($n===0 || $n>0)
		{
			$data=substr($result['body'],$n);
			ob_start();
			highlight_string($data);
			$xml=ob_get_contents();
			ob_end_clean();
			$data=str_replace("D:","",$data);
			$xmlObj= new XmlToArray($data);
			$arrayData = $xmlObj->createArray();
			//echo '<pre>';print_r($arrayData);
			ob_start();

			?><div style="height:400px;overflow:scroll;"> <table  class="list"><?
			if (trim($dav->path,'/')!=trim($_REQUEST['DV_ROOT'],'/'))
				echo "<a href='javascript:GetFileListDav(\"".$dav->protocol_s.$dav->server.dirname($dav->path)."\")' >...</a><br>";

			foreach ($arrayData['multistatus']['response'] as $item)
			{

				if(trim($dav->path,'/')!=trim($item['href'],'/'))
				{

					echo "<tr><td style=\"border-bottom: 1px solid #E7ECF5;\">";

					$type = $item['propstat'][0]['prop'][0]["getcontenttype"];
					if ($item['propstat'][0]['prop'][0]['iscollection']
					|| (
							$type &&  strpos($type,"directory")
						)
					)
					{

						//echo "<img src='http://icons.iconarchive.com/icons/visualpharm/must-have/128/Check-icon.png'";
						//echo $item['href'];

							echo "<img src=\"http://icons.iconarchive.com/icons/gakuseisean/radium/128/My-Documents-icon.png\" width=20 height=20><a href='javascript:GetFileListDav(\"".$dav->protocol_s.$dav->server.urldecode($item['href'])."\")' >".urldecode(basename2($item['href']))."</a>";
					}
					else
					{

						echo "<img src=\"http://icons.iconarchive.com/icons/dario-arnaez/genesis-3G/128/User-Files-icon.png\" width=20 height=20><a href='javascript:GetFile(\"".basename2(urldecode($item['href']))."\")' >".basename2(urldecode($item['href']));
						//echo basename2($item['href'])." - file<br>";
					}
						echo "</td></tr>";
				}
			}
	?>

	</table><div>
			<?
			$file_list=ob_get_contents();
			ob_end_clean();
			$cdata['xml']=$xml;
			$cdata['response_array']=$arrayData;
			$cdata['file_list']=$file_list;


		}
		$cdata['headers']='<pre>'.$result['~headers'].'</pre>';
	}
	else
	{
		$cdata['error']=$result['error'];
		$cdata['file_list']='Ошибка запроса, смотрите раздел "заголовки"';

	}
	echo json_encode($cdata);
die();
}

if ($_REQUEST['mode']=='test')
{
	$cdata['lamp']='red';
	$dav=new iWebDav;
	$dav->user=$_REQUEST['login'];
	$dav->pass=$_REQUEST['pass'];
	$dav->parseURL($_REQUEST['server']);
	$dav->addHeader('Connection: Close');
	switch ($_REQUEST['step'])
	{
		case 'CHECK_METHOD':
			unlink($_SERVER['DOCUMENT_ROOT'].$dav->logfile);
			$cdata['lamp']='green';
			$dav->setMethod('OPTIONS');
			$dav->addHeader("Content-Length: ".strlen($dav->body));;
			$result=$dav->request();
			$NeedMethods=Array('PROPFIND','PUT','PROPPATCH','MKCOL','COPY','DELETE');
			$ServerMethods=explode(",", $result['headers']['Allow']);
			foreach($NeedMethods as $method)
			{
				if (!in_array($method,$ServerMethods))
					$FailMethod[]=$method;
			}
			if (!empty($FailMethod))
			{
				$cdata['lamp']='red';
				$FailMethod=implode(',',$FailMethod);
				$cdata['text']=__ShowMessage($mess['OPTIONS'].": не поддерживаются методы - ".$FailMethod);
			}
			else
				$cdata['text']=__ShowMessage($mess['OPTIONS'].": все методы поддерживаются",'green');

			break;
		case 'CREATE_FILE':
			$dav->setMethod('PUT');
			$dav->setPath($dav->path.'/content.txt');
			$dav->body=$mess['TEST_CONTENT'];
			$dav->addHeader("Content-Length: ".strlen($dav->body));
			$result=$dav->request();
			if (strpos($result['headers']['STATUS'],'201 Created'))
				$cdata['lamp']='green';
			$cdata['text']=__ShowMessage($mess['CREATE_FILE'],$cdata['lamp']);
			break;
		case 'PROPPATCH_FILE':
			$dav->setMethod('PROPPATCH');
			$dav->setPath($dav->path.'/content.txt');
			$dav->body='<?xml version="1.0"?>
	<d:propertyupdate xmlns:d="DAV:" xmlns:o="urn:schemas-microsoft-com:office:office">
	  <d:set>
		<d:prop>
		  <o:Author>support_test</o:Author>
		</d:prop>
	  </d:set>
	</d:propertyupdate>';
			$dav->addHeader("Content-Type: text/xml; charset=\"utf-8\"");
			$dav->addHeader("Content-Length: ".strlen($dav->body));

			$result=$dav->request();

			$data=substr($result['body'],$n);
			$data=str_replace("D:","",$data);
			$xmlObj= new XmlToArray($data);
			$arrayData = $xmlObj->createArray();
			$status=$arrayData['multistatus']['response'][0]['propstat'][0]['status'];
			if (strpos($status,'HTTP/1.1 200')===0)
				$cdata['lamp']='green';
			$cdata['text']=__ShowMessage($mess['PROPPATCH_FILE'],$cdata['lamp']);
				//echo '<div style="padding-left:200px">'.highlight_string($result['body']).'</div>';
			break;
		case 'COPY_FILE':
			$dav->setMethod('COPY');
			$dav->setPath($dav->path.'/content.txt');
			$dav->addHeader('Destination: '.$path.'/content_copy.txt');
			$result=$dav->request();
			if (strpos($result['headers']['STATUS'],'201 Created'))
			{
				$cdata['lamp']='green';
				$dav->setMethod('DELETE');
				$dav->headers="";
				$dav->setPath($dav->path.'/content_copy.txt');
				//$dav->request();
			}
			$cdata['text']=__ShowMessage($mess['COPY_FILE'],$cdata['lamp']);
			break;
		case 'DELETE_FILE':
			$dav->setMethod('DELETE');
			$dav->setPath($dav->path.'/content.txt');
			$result=$dav->request();
			if (strpos($result['headers']['STATUS'],'204 No Content'))
				$cdata['lamp']='green';
			$cdata['text']=__ShowMessage($mess['DELETE_FILE'],$cdata['lamp']);
			break;

		case 'CREATE_FOLDER':
			$dav->setMethod('MKCOL');
			$dav->setPath($dav->path.'/test_folder/');
			$result=$dav->request();
			if (strpos($result['headers']['STATUS'],'201 Created'))
				$cdata['lamp']='green';
			$cdata['text']=__ShowMessage($mess['CREATE_FOLDER'],$cdata['lamp']);
			break;

		case 'COPY_FOLDER':
			$dav->setMethod('COPY');
			$dav->setPath($dav->path.'/test_folder');
			$dav->addHeader('Destination: '.$path.'/_copy_test_folder/');
			$result=$dav->request();
			if (strpos($result['headers']['STATUS'],'201 Created'))
			{
				$cdata['lamp']='green';
				$dav->setMethod('DELETE');
				$dav->parseURL($path);
				$dav->setPath($dav->path.'/_copy_test_folder');
			//	$dav->request();
			}
			$cdata['text']=__ShowMessage($mess['COPY_FOLDER'],$cdata['lamp']);
			break;

		case 'DELETE_FOLDER':
			$dav->setMethod('DELETE');
			$dav->parseURL($path);
			$dav->setPath($dav->path.'/test_folder');
			$result=$dav->request();
			if (strpos($result['headers']['STATUS'],'204 No Content'))
				$cdata['lamp']='green';
			$cdata['text']=__ShowMessage($mess['DELETE_FOLDER'],$cdata['lamp']);
			break;
	}
	$cdata=$APPLICATION->ConvertCharsetArray($cdata,'windows-1251','UTF-8');
	echo json_encode($cdata);
	die();
}

if (@$_REQUEST['type']=='catalog')
{
    AddEventHandler("iblock", "OnAfterIBlockElementAdd",  "WriteElementAddDebug");
    AddEventHandler("iblock", "OnAfterIBlockElementUpdate",  "WriteElementUpdateDebug");

	if ($_SESSION['bx_1c_import']['skipmode']==1)
	{
		AddEventHandler("iblock", "OnBeforeIBlockElementUpdate",  "SkipHandler");
		AddEventHandler("iblock", "OnBeforeIBlockElementAdd",  "SkipHandler");
		function SkipHandler(&$arFields)
		{
			$a=false;
			$b=false;
			if ($_SESSION['bx_1c_import']['xml_id'])
				$a=($arFields['XML_ID']==$_SESSION['bx_1c_import']['xml_id']);
			if ($_SESSION['bx_1c_import']['element_name'])
				$b=($arFields['NAME']==$_SESSION['bx_1c_import']['element_name']);
			if (($a || $b))
			{

				echo "debug:<br>"."<b>Эта информация выводится потому, что включён SkipMode</b><hr><pre style='font-size:11px;font-family:Arial;'>";
					print_r($arFields);
				echo '</pre>';
				die('<hr>');
			}
			else
				return false;
		}
	}

	function WriteElementAddDebug(&$arFields)
	{
		define("LOG_FILENAME", $_SERVER["DOCUMENT_ROOT"]."/import_element_log.txt");
		AddMessage2Log(print_r($arFields,true), "------------ADD-----------");
	}

	function WriteElementUpdateDebug(&$arFields)
	{
		define("LOG_FILENAME", $_SERVER["DOCUMENT_ROOT"]."/import_element_log.txt");
		AddMessage2Log(print_r($arFields,true), "------------UPDATE-----------");
	}


	$APPLICATION->IncludeComponent("bitrix:catalog.import.1c", "", Array(
		"IBLOCK_TYPE" => COption::GetOptionString("catalog", "1C_IBLOCK_TYPE", "-"),
		"SITE_LIST" => array(COption::GetOptionString("catalog", "1C_SITE_LIST", "-")),
		"INTERVAL" => COption::GetOptionString("catalog", "1C_INTERVAL", "-"),
		"GROUP_PERMISSIONS" => explode(",", COption::GetOptionString("catalog", "1C_GROUP_PERMISSIONS", "")),
		"GENERATE_PREVIEW" => COption::GetOptionString("catalog", "1C_GENERATE_PREVIEW", "Y"),
		"PREVIEW_WIDTH" => COption::GetOptionString("catalog", "1C_PREVIEW_WIDTH", "100"),
		"PREVIEW_HEIGHT" => COption::GetOptionString("catalog", "1C_PREVIEW_HEIGHT", "100"),
		"DETAIL_RESIZE" => COption::GetOptionString("catalog", "1C_DETAIL_RESIZE", "Y"),
		"DETAIL_WIDTH" => COption::GetOptionString("catalog", "1C_DETAIL_WIDTH", "300"),
		"DETAIL_HEIGHT" => COption::GetOptionString("catalog", "1C_DETAIL_HEIGHT", "300"),
		"ELEMENT_ACTION" => COption::GetOptionString("catalog", "1C_ELEMENT_ACTION", "D"),
		"SECTION_ACTION" => COption::GetOptionString("catalog", "1C_SECTION_ACTION", "D"),
		"FILE_SIZE_LIMIT" => COption::GetOptionString("catalog", "1C_FILE_SIZE_LIMIT", 200*1024),
		"USE_CRC" => COption::GetOptionString("catalog", "1C_USE_CRC", "Y"),
		"USE_ZIP" => COption::GetOptionString("catalog", "1C_USE_ZIP", "Y"),
		"USE_OFFERS" => COption::GetOptionString("catalog", "1C_USE_OFFERS", "N"),
		"USE_IBLOCK_TYPE_ID" => COption::GetOptionString("catalog", "1C_USE_IBLOCK_TYPE_ID", "N"),
		"USE_IBLOCK_PICTURE_SETTINGS" => COption::GetOptionString("catalog", "1C_USE_IBLOCK_PICTURE_SETTINGS", "N"),
		"TRANSLIT_ON_ADD" => COption::GetOptionString("catalog", "1C_TRANSLIT_ON_ADD", "N"),
		"TRANSLIT_ON_UPDATE" => COption::GetOptionString("catalog", "1C_TRANSLIT_ON_UPDATE", "N"),
		)
	);
	require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/epilog_after.php");

	die();

}
//Готовим кнопки
$MenuArray=Array(
            'main_info'=>Array(
                "msg"=>'откроется окно информации по файлам',
                "title"=>'Поиск',
                "onclick"=>"BX('main_info').style.display='block'",
                "class"=>'small_but'
                ),

            'param'=>Array(
                    "msg"=>'откроется окно параметров выгрузки заказов',
                    "title"=>'Выгрузка заказов',
                    "onclick"=>"BX('param').style.display='block'",
                    "class"=>'small_but'
            ),

            'crtiblock'=>Array(
                        "msg"=>'откроется окно создания типа инфоблока',
                        "title"=>'Создать тип инфоблока',
                        "onclick"=>"AddWindowRequest('".$script_name."?action=createiblocktypeform','custom_windows','iblock');",
                        "class"=>'small_but'
                        ),
			'fileman_but'=>Array(
							"msg"=>'откроется FileMan',
							"title"=>'FileMan (shift+~)',
							"onclick"=>"BX('test_window').style.display='block';GetFileList2('','testfileman');",
							"class"=>'small_but'
							),
            'xmltree'=>Array(
                                    "msg"=>'будет отображено содержимое временной таблицы',
                                    "title"=>'Временная таблица',
                                    "onclick"=>"AjaxRequest('".$script_name."?action=show_bxmltree','log2',false);bxtabs.AlertActiveTab('tab1');",
                                    "class"=>'small_but'
                                    ),

                    );

//  $CustomButton - массив кастомных кнопок
$CustomButton['searchbutton']=Array(
							"msg"=>'произойдёт поиск',
							"title"=>'найти',
							"onclick"=>"searchbyxmlid();",
							"class"=>'small_but_float'
							);
$CustomButton['searchbutton_iblock']=Array(
							"msg"=>'произойдёт поиск',
							"title"=>'найти',
							"onclick"=>"search_iblock_byxmlid();",
							"class"=>'small_but_float'
							);
$CustomButton['change1']=Array(
							"msg"=>'сменится время последнего обмена с 1С, после этого посмотреть список заказов, которые выгрузятся в 1С при следующем обмене',
							"title"=>'Сменить',
							"onclick"=>"ChangeLastMoment();",
							"class"=>'small_but'
							);
$CustomButton['delete']=Array(
							"msg"=>'удалится весь этот скрипт',
							"title"=>'',
							"onclick"=>"delete_file()",
							"class"=>'delete_but light'
							);
$CustomButton['refresh']=Array(
							"msg"=>'обнулится шаг импорта',
							"title"=>'',
							"onclick"=>"reset()",
							"class"=>'refresh_but light'
							);
$CustomButton['cat_imp']=Array(
							"msg"=>"Импорт файла, это импорт каталога",
							"title"=>'Каталог',
							"onclick"=>"ConfirmImport('import.xml');",
							"class"=>'small_but'
							);
$CustomButton['cat_off']=Array(
							"msg"=>"Импорт файла, это импорт предложений",
							"title"=>'Предложения',
							"onclick"=>"ConfirmImport('offers.xml');",
							"class"=>'small_but'
							);
$CustomButton['order_import']=Array(
							"msg"=>"Импорт файла, это импорт заказов",
							"title"=>'Импорт заказов',
							"onclick"=>"OrderImport('ord_imp');",
							"class"=>'small_but'
							);
$CustomButton['cat_comp']=Array(
							"msg"=>"Импорт файла, это импорт сотрудников",
							"title"=>'Сотрудники',
							"onclick"=>"ConfirmImport('company.xml');",
							"class"=>'small_but'
							);
$CustomButton['iblockbut']=Array(
							"msg"=>'создастся тип инфоблока',
							"title"=>'создать',
							"onclick"=>"CreateIBlock();",
							"class"=>'small_but'
							);
$CustomButton['test_123']=Array(
							"msg"=>'откроется FileMan',
							"title"=>'FileMan (shift+~)',
							"onclick"=>"BX('test_window').style.display='block';GetFileList2('','testfileman');",
							"class"=>'small_but'
							);
$CustomButton['crfile']=Array(
							"msg"=>'будем создавть файл',
							"title"=>'создать файл',
							"onclick"=>"CreateFile('cfilename','path_fileman','testfileman')",
							"class"=>'small_but'
							);
$CustomButton['upfile']=Array(
							"msg"=>'будем загружать файл',
							"title"=>'загрузить файл',
							"onclick"=>"BX('upload_file').style.display='block'",
							"class"=>'small_but'
							);
$CustomButton['go']=Array(
							"msg"=>'перейти',
							"title"=>'перейти',
							"onclick"=>"GetFileList('path_fileman','testfileman');",
							"class"=>'small_but'
							);



//пункты контекстого меню
$ContextMenu=Array(
		Array(
				'msg'=>"файл откроется на просмотр",
				'id'=>"view",
				'class'=>"menu",
				'aid'=>"v",
				'point_name'=>"просмотр"
			),
			Array(
				'msg'=>"файл откроется на просмотр в UTF",
				'id'=>"viewu",
				'class'=>"menu",
				'aid'=>"vu",
				'point_name'=>"просмотр utf"
			),
		Array(
				'msg'=>"файл откроется на редактирование",
				'id'=>"edit",
				'class'=>"menu2",
				'aid'=>"e",
				'point_name'=>"правка"
			),

		Array(
				'msg'=>"файл будет удалён",
				'id'=>"del",
				'class'=>"menu_del",
				'aid'=>"d",
				'point_name'=>"удалить"
			),
		Array(
				'msg'=>"это архив и он будет распакован",
				'id'=>"unzip_",
				'class'=>"menu_unzip",
				'aid'=>"u",
				'point_name'=>"распаковать"
			),
		Array(
				'msg'=>"скачается файл",
				'id'=>"down",
				'class'=>"menu_dw",
				'aid'=>"dw",
				'point_name'=>"скачать"
			),

);

foreach ($ContextMenu as $point)
$mainmenu.="var ".$point['id']."=BX('".$point['aid']."');\n";


//описание стилей окон
$DefaultWinStyle=Array(
                "width"=>'40%',
                "border"=>'3px solid #c3d0e9;',
                "background"=>'#b7c8e8',
                "display"=>'none',
                "position"=>'absolute',
                "cursor"=>'hand',
                "left"=>"390px",
                "top"=>"100px",
                "padding"=>"5px",
                "z-index"=>1000,
                "is_moveable"=>'Y',
                "border-radius"=>'3px'
                );
$DefaultWinStyleSmall=Array(
                    "width"=>320,
                    "height"=>200,
                    "border"=>'1px solid black',
                    "background"=>'#FFF8DC',
                    "display"=>'block',
                    "position"=>'fixed',
                    "cursor"=>'hand',
                    "left"=>550,
                    "top"=>250,
                    "padding"=>5,
                    "z-index"=>1001,
                    "is_moveable"=>'Y',
                     "display"=>'none'
                    );

$DefaultFieldStyle=Array(
                "width"=>1000,
                "height"=>660,
                "border"=>'1px solid #c3c6c9',
                "background"=>'#FFF8DC',
                "display"=>'block',
                "position"=>'fixed',
                "cursor"=>'hand',
                "left"=>350,
                "top"=>20,
                "padding"=>"20px",
                "z-index"=>10,
                "workcolor"=>"#EEE8AA",
                "border-radius"=>"3px"
                );

$WinStyleIBlock=Array(
					"width"=>320,
					"height"=>220,
					"border"=>'1px solid #c3c6c9',
					"background"=>'#FFF8DC',
					"display"=>'block',
					"position"=>'fixed',
					"cursor"=>'hand',
					"left"=>550,
					"top"=>250,
					"padding"=>5,
					"z-index"=>1001,
					"is_moveable"=>'Y',
					"border-radius"=>"3px"
					);
$WinStyleIpfs=Array(
					"width"=>'320px',
					//"height"=>'400px',
					"border"=>'1px solid #c3c6c9',
					"background"=>'#a5afd6',
					"display"=>'block',
					"position"=>'fixed',
					"cursor"=>'hand',
					"left"=>'75%',
					"top"=>'62%',
					"padding"=>5,
					"z-index"=>100,
					"is_moveable"=>'Y',
					"border-radius"=>"3px"
					);
$EditStyle=Array(
					"width"=>'70%',
					"height"=>'90%',
					"border"=>'1px solid #c3c6c9',
					"background"=>'#6699CC',
					"display"=>'block',
					"position"=>'fixed',
					"cursor"=>'defult',
					"left"=>350,
					"font-size"=>'14',
					"top"=>20,
					"color"=>"black",
					"padding"=>'10px',
					"z-index"=>10,
					"workcolor"=>"none",
					"is_moveable"=>'N',
					"fileman"=>'Y',
					"border-radius"=>"3px"
					);

//строим меню
function BuildContextMenu()
{
	global $ContextMenu;
	echo '<table class="menu">';
	foreach ($ContextMenu as $point):
		echo '<tr><td class=menu onmouseover=\'LightOn(this,"'.$point['msg'].'")\' onmouseout=LightOff() id="'.$point['id'].'"><a class="'.$point['class'].' point_menu" id="'.$point['aid'].'">'.$point['point_name'].'</a></td></tr>';
	endforeach;
	echo '</table>
	<iframe id="dwframe" style="display:none" src=""></iframe>';
}

//список файлов указаной директории
function ShowFileSelect($listid='test',$Title='undefined',$dir,$ext='xml',$listsize=1,$DblClickAction='')
{
	$ifile=Array();
	if ($handle = opendir($_SERVER['DOCUMENT_ROOT'].$dir))
	{
		while (false !== ($file_1 = readdir($handle)))
		{
			$file_ext=substr(strrchr($file_1, '.'), 1);
			if ($file_ext==$ext)
				$ifile[]=$file_1;
		}
	}
	asort($ifile);
     if ($ifile!=Array())
      {
		echo '<b style="font-size:10" align=\'left\'>'.$Title.'</b><br>';
		echo '<select style="width:100%;font-size:11;" size='.$listsize.' oncontextmenu="return ShowMenuExp(event);"  style="font-size:10" align=\'right\' id='.$listid.' onDblClick='.$DblClickAction.'>';
		$select=false;
		foreach ($ifile as $value):
			if ($select==false)
			{
				$select=true;
				echo '<option  selected oncontextmenu="return ShowMenuExp(event);"  "value="'.$key.'">'.$value.'</option>';
				continue;
			}
		echo '<option "value="'.$key.'">'.$value.'</option>';
		endforeach;
							echo '</select></br>';
	}
}


function ShowMenuWindow($ID,$NAME,$ShowHideSectionID,$content='')
{
    $menu="";

   $menu.='<table id='.$ID.' class=menu_table cellspacing=0 cellpadding=0>
           <tr><td>
        <b class="rtopwin">
    <b class="r1win"></b> <b class="r2win"></b> <b class="r3win"></b> <b class="r4win"></b>
    </b>
    </td></tr>
   <tr><td class=msection>
   <div style="background:#B9D3EE;position:relative;left:10;width:180;color:black">'.$NAME.'</div></td></tr>
   <tr><td class=menu_td>
   <div  id='.$ShowHideSectionID.'_ps style="background:white;padding:10" align=center>'.$content.'</div>
    </td></tr>
    <tr><td>
    <b class="rbottomwin">
    <b class="r4win"></b> <b class="r3win"></b> <b class="r2win"></b> <b class="r1win"></b>
    </b>
    </td></tr>
    </table>';
   echo $menu;
}



function AddButton($value,$mainmenu=false,$returnbutton=false,$MyButtons=false)
{
    global $MenuArray;
    global $CustomButton;
    if (is_array($MyButtons))
    $arButtons=$MyButtons;
    elseif($mainmenu==true)
    $arButtons=$MenuArray; else $arButtons=$CustomButton;
    $but=$value;
    if (!is_array($but))
    {
        $but=$arButtons[$value];
        if (!is_array($but)) return false;
    }

    $Button='<div type=button class="'.$but['class'].'" align="center" OnClick="'.$but['onclick'].'" >'.$but['title'].'</div>';
    if ($returnbutton==false)
    echo $Button;
    else return $Button;
}



function AddWindow($NewId="newwindow",$NewName="NoNameWindow",$WorkID=false,$inner=false,$WinStyle=false,$buttons="",$mainmenu=false,$beforeInner='',$afterInner="")
{
    global $MenuArray;
    global $CustomButton;
    global $DefaultWinStyle;
    if (!is_array($buttons))
    $button=AddButton($buttons,$mainmenu,true);
    else
    foreach ($buttons as $val)
    $button.=AddButton($val,$mainmenu,true);
    if (!$WinStyle)
    $WinStyle=$DefaultWinStyle;

    if (!$inner)
       $inner="<div style='background-color:".$WinStyle['workcolor']."' id=".$WorkID."></div>".$button;
    if ($WinStyle['is_moveable']=='Y')
       $content.='<div id="'.$NewId.'" class="divwin_'.$NewId.' round_win"><b>'.$NewName.'</b><hr>
               <div class="closeButton" OnMouseOver="LightOn(this,\'закроется окно\');" OnMouseOut="LightOff();" onclick="Close(\''.$NewId.'\')">X</div>';
    else
         $content.='<div id="'.$NewId.'" class="divwin_'.$NewId.' round_win"><b>'.$NewName.'</b><hr>';
    $content.=$beforeInner;
    if ($WinStyle['fileman']=='Y')
        $content.=AddButton('test_123',false,true);
    $content.=$inner.$afterInner;
    $content.='</div>';
    $content.='<style>.divwin_'.$NewId.'{';
   foreach ($WinStyle as $atr=>$value)
       $content.=$atr.':'.$value.';';
   $content.='}</style>';
   $content.="<script>
		BX.ready(function()
		{
			dragMaster.makeDraggable(BX('".$NewId."'))
		});
   </script>";
   echo $content;
}

function AddField($NewId="newwindow",$NewName="NoNameWindow",$WorkID=false,$inner=false,$WinStyle=false,$buttons="",$mainmenu=false,$tableft=5)
{
    global $MenuArray;
    global $CustomButton;
    global $DefaultFieldStyle;
    if (!is_array($buttons))
        $button=AddButton($buttons,$mainmenu,true);
    else
    foreach ($buttons as $val)
        $button.=AddButton($val,$mainmenu,true);
    if (!$WinStyle)
        $WinStyle=$DefaultFieldStyle;
    $field_id=$NewId.'_field';
    $tab_id=$NewId.'_tab';
    $content="";
    $content.='<div id='.$NewId.'>';
    $content.='<div id='.$field_id.' style="width:980;padding:5;left:5;height:600;position:absolute;top:55;border:1px solid #00C5CD;z-index:99;">';
    $content.=$inner;
    $content.='</div>';
    $content.='<b><div id='.$tab_id.' onmousedown="ShowField(this,\''.$field_id.'\');"
    style="
    position:absolute;
    left:'.$tableft.';
    height:15;
    top:28;
    border-top:1px solid #00C5CD;
    border-right:1px solid #00C5CD;
    border-left:1px solid #00C5CD;
    border-bottom:2px solid #FFF8DC;
    background:#FFF8DC;
    padding:5;
    margin:0;
    width:100;
    z-index:100;">'.$NewName.'</div></b>';
    $content.='</div>';
    $content.='<script>
    var old_node = BX("'.$NewId.'");
    var oldparentNode = BX("'.$NewId.'").parentNode;
    // alert(oldparentNode);
    var clone = old_node.cloneNode(true);
    var newparentNode = BX("'.$WorkID.'").appendChild(clone);
    oldparentNode.removeChild(old_node);
    </script>';
    echo $content;
}
//удаление скрипта
if (@$_GET['delete']=="Y")
{
    header("Content-type:text/html; charset=windows-1251");
    unlink(__File__);
    echo "<div style='background-color:#B9D3EE;
       border:1px solid red;
       text-align:center;
       color:red;
       height:30;
       z-index:10000;'> Файла теперь нет - он удалён!</div>";
    die();
}

$UPLOAD_DIR="/".COption::GetOptionString("main", "upload_dir");
$interval=COption::GetOptionString("catalog", "1C_INTERVAL", "-");
if ((!$USER->IsAdmin())&&(@($_GET['mode']!='query')))
{
    echo 'Доступ запрещён. Вы не администратор сайта. До свидания.';
    localredirect("/404.php");
}


error_reporting(E_ALL ^ E_NOTICE ^ E_WARNING);
header("Content-type:text/html; charset=windows-1251");
if (@$_GET['action']=="addfield")
{
    AddField('test_123','test','para1','test',false,false,false,5);
    AddField('testfield2','offers.xml','para1','test2',false,false,false,120);
    die();
}

if (@$_GET['action']=="createfile")
{
    if (file_exists($_SERVER['DOCUMENT_ROOT'].$_GET['path']))
    {
        echo 'error001';
        die();
    }

    if ($_GET['isdir']=='Y')
    {
        if (mkdir($_SERVER['DOCUMENT_ROOT'].$_GET['path'], 0, true))
            echo 'success'; else echo 'fail';
    }
    else
    {
        if ($f = fopen ($_SERVER['DOCUMENT_ROOT'].$_GET['path'], 'a+'))
           echo 'success'; else echo 'fail';
        fclose($f);
    }
    die();
}

if (@$_GET['action']=="createiblocktypeform")
{
    $inner='<div id="successiblock"></div>
    Введите ID типа инфоблока:<br>
    <input id="iblocktype" size=45 value="support_test_iblock_type"><br>'.
    'Выгружать в этот тип инфоблока <input type="checkbox" id="1ciblock" checked>'.AddButton('iblockbut',false,true);
    AddWindow('iblock','Создание типа инфоблока',false,$inner,$WinStyleIBlock);
    die();
}


if (@$_GET['action']=="createiblocktype")
{
CModule::IncludeModule('iblock');
$arFields = Array(
    'ID'=>$_GET['iblocktype'],
    'SECTIONS'=>'Y',
    'IN_RSS'=>'N',
    'SORT'=>100,
    'LANG'=>Array(
            'en'=>Array(
                    'NAME'=>'Catalog',
                    'SECTION_NAME'=>'Sections',
                    'ELEMENT_NAME'=>'Products'
                    )
            )
    );

$obBlocktype = new CIBlockType;
$DB->StartTransaction();
$res = $obBlocktype->Add($arFields);
if(!$res)
{
    $DB->Rollback();
    echo '<div style="color:red;border:1px dashed red;padding:5">'.$obBlocktype->LAST_ERROR;
}
else
{
    echo '<div style="color:green;border:1px dashed green;padding:5">Тип инфоблока создан успешно!';
    $DB->Commit();
}
if (@$_GET['USE_IBLOCK_TYPE']=='Y')
{
    COption::SetOptionString("catalog",'1C_IBLOCK_TYPE', $_GET['iblocktype']);
    COption::SetOptionString("catalog", "1C_USE_IBLOCK_TYPE_ID", "Y");
    echo 'Каталог будет выгружаться в тип инфоблока '.$_GET['iblocktype'].'</div></br>';
}
else
    echo '</div></br>';
die();
}

if (@$_GET['action']=='getstep')
{
    echo $_SESSION["BX_CML2_IMPORT"]["NS"]["STEP"];
    die();
}

if (@$_GET['action']=='download')
{
    $filename=$_SERVER["DOCUMENT_ROOT"].$_GET['path'].$_GET['file'];
    $mimetype='application/octet-stream';
    if (file_exists($filename)) {
        header($_SERVER["SERVER_PROTOCOL"] . ' 200 OK');
        header('Content-Type: ' . $mimetype);
        header('Last-Modified: ' . gmdate('r', filemtime($filename)));
        header('ETag: ' . sprintf('%x-%x-%x', fileinode($filename), filesize($filename), filemtime($filename)));
        header('Content-Length: ' . (filesize($filename)));
        header('Connection: close');
        header('Content-Disposition: attachment; filename="' . basename($filename) . '";');
        echo file_get_contents($filename);
    } else {
        header($_SERVER["SERVER_PROTOCOL"] . ' 404 Not Found');
        header('Status: 404 Not Found');
    }
    exit;
}

if ($_REQUEST['action']=='xmlgetinfo')
{
	$filename=$_SERVER['DOCUMENT_ROOT'].$_REQUEST['path'];
    $f=fopen($filename,"a+");
	$string=fgets($f);
	if(preg_match("/<"."\?XML[^>]{1,}encoding=[\"']([^>\"']{1,})[\"'][^>]{0,}\?".">/i", $string, $matches))
	$cdata['encoding']=$matches[1];
	$string=fgets($f);
	$string=$APPLICATION->ConvertCharset($string,$cdata['encoding'],'windows-1251');
	echo $string;
	preg_match('/<КоммерческаяИнформация/is',$string, $matches);
	$cdata['version']=$matches[1];
	print_r($matches);
	die();
}

if(@$_POST['action']=="deletefile")
{
	if (!$_POST['fullpath'])
		$path=$_SESSION['bx_1c_import']['path'];
	else
		$path="";
	if (is_dir($_SERVER['DOCUMENT_ROOT'].$path.$_POST['filename']))
        $res=rmdir($_SERVER['DOCUMENT_ROOT'].$path.$_POST['filename']);
    else
        $res=unlink($_SERVER['DOCUMENT_ROOT'].$path.$_POST['filename']);
	if ($res)
		echo 'success';
	else
		echo 'error';
    die();
}

if(@$_GET['action']=="getfiles")
{
    if (!isset($_GET['path']))
            $urlpath='/'; else $urlpath=$_GET['path'];
    $realpath=str_replace('//','/',$urlpath.'/');
    $_SESSION['bx_1c_import']['path']=$realpath;
    @$_SESSION['bx_1c_import']['filter']=$_GET['like_str'];
    if (isset($_GET['workarea']))
            $wa=$_GET['workarea']; else $wa="minifileman";
    $rows=400;
    $cols=1;
    $dirs=explode('/',$realpath);
    $i=1;
    $full="";
    $el['DIR']='[root]';
    $el['PATH']='/';
    $cat[]=$el;
    while ($i<=count($dirs))
    {
        $el=Array();
        $el['DIR']=$dirs[$i];
        if ($dirs[$i]!='')
        {
                $el['PATH']=$full.$dirs[$i].'/';
                $full.=$dirs[$i].'/';
                $cat[]=$el;
        }
        $i++;
    }
    $link_path="/";
    $id=0;$l=1;
    echo '<div style="font-size:11px;background:#d8dcf0;padding:4px;">';
    foreach ($cat as $el_d)
    {
        $id="p_".$wa.'_'.$l++;
        $func=str_replace('//','/','/'.$el_d["PATH"]);?><a id="<?=$id?>" href="javascript:GetFileList2('<?=$func;?>','<?=$wa?>')"><?=$el_d["DIR"]?></a>/<?
    }
    echo '</div>';
    echo '<div style="overflow:auto;height:200px;width:100%;background:white;">';
    echo '<table style="font-size:9;width:100%">';
    if ($handle = opendir($_SERVER['DOCUMENT_ROOT'].$_GET['path']))
    {
            $i=-1;
            $col=0;
            $fun_str="";
            $q=$_GET['like_str'];
            $IfoundFiles=false;
            if ($q=='') {$q="all";$fun_str="all";}
            $id=0;$l=1;
            $mdir=Array();
            $mfile=Array();
            echo "<tr><td valign='top' style='font-size:12;width:50%;border-right:1px solid #DCDCDC;'>";
            while (false !== ($file_1 = readdir($handle)))
            {
                if (is_dir($_SERVER['DOCUMENT_ROOT'].$_GET['path'].'/'.$file_1)):
                        $mdir[]=$file_1;
                else:
                        $mfile[]=$file_1;
                endif;
            }
            sort($mdir);
            sort($mfile);
            $mdirectory=array_merge($mdir,$mfile);
            $color='#FFF8DC';
            foreach ($mdirectory as $file)
            {
                if ($color=='#FFF8DC') $color='#EEEEE0'; else $color='#FFF8DC';
                if(($file!==".")&&($file!=="..")&&(strpos($file.$fun_str,$q)!==false))
                {
                    $id="f_".$wa.'_'.$l++;
                    if ($i>$rows) {if(++$col==$cols)
                            break;
                    elseif($IfoundFiles==true)
                    {
                            echo '</td><td width=200 valign="top" style="font-size:9">';$IfoundFiles=false;}$i=1;
                    }
                    $IfoundFiles=true;?>

                    <div width=100% style="background:<?=$color?>;">
                    <?

                    if (is_dir($_SERVER['DOCUMENT_ROOT'].$_GET['path'].'/'.$file)):?>

                            <img src='/bitrix/images/fileman/folder.gif'> <a id="<?=$id?>" style="font-size:12;color:blue;" OnMouseOver="LinkLightOn('<?=$id?>','#363636');" OnMouseOut="LinkLightOff();" href="javascript:GetFileList2('<?=str_replace('//','/',$_GET['path'].'/').$file.'/'?>','<?=$wa?>')"><?=$file?></a>
                    <?else:?>

                    <img src='/bitrix/images/fileman/file.gif'>
                    <a id="<?=$id?>" style="font-size:12;" href="javascript:ShowFile('<?=$file?>','<?=$realpath?>','N')" oncontextmenu="return ShowMenu(event);" OnMouseOver="LinkLightOn('<?=$id?>','#1C1C1C');" OnMouseOut="LinkLightOff();" href="#" onmousedown="moveState = false;" onmousemove="moveState = false;"><?if (strlen($file)>50) echo substr($file,0,-(strlen($file)-8))."...".substr($file,-4); else echo $file;?></a>
                    <a  style="color:red;font-size:10;" href=javascript:Delete('<?=$file?>','<?=$wa?>') OnMouseOver="LightOn(this,'! удалится <b> <?=$file?></b> !');" OnMouseOut=LightOff()>[X]</a><a style="color:green;font-size:10;" href=javascript:ShowInfo('<?=$realpath.$file?>') OnMouseOver="LightOn(this,'отобразится информация по <b> <?=$file?></b>');" OnMouseOut=LightOff()>[!]</a>
                    </div>
                    <?endif;?>
                    <?
					$i++;
                 }

            }
            closedir($handle);
    }
    echo '</td></tr></table>';
    echo '</div>';
    die();
}
//распаковка файла
if (@$_POST['action']=="unzip")
{
    $zip = $_POST['filename'];
    CModule::IncludeModule('iblock');
    $result = CIBlockXMLFile::UnZip($zip);
    echo 1;
    die();
}

//грузим  любой файл в указанную папку
if (@$_GET['upload']=="Y")
{
    if(is_array($_FILES['test_file']))
    {
        $tmp_name=$_FILES['test_file']['tmp_name'];
        if( $_SESSION['bx_1c_import']['path']=="")
        $test_file=$UPLOAD_DIR."/".$_FILES['test_file']['name'];
        else $test_file=$_SESSION['bx_1c_import']['path'].$_FILES['test_file']['name'];
        if(is_uploaded_file($tmp_name))
                        {
                                move_uploaded_file($tmp_name,$_SERVER['DOCUMENT_ROOT'].$test_file);
                                echo("<a href='".$test_file."' target='_blank'>".$_FILES['test_file']['name']."</a>");
                        }
        else
                echo "error";
                echo '<br>';
    }
    //форма для загрузки файла на сервер
    if (isset($_POST['test_file']))
       echo "Файл ".$_POST['test_file']." загружен";
    echo "<div style='background-color:#FFE4B5'>
    <form action='".$script_name."?upload=Y' method=post enctype='multipart/form-data'>
    <input onmousedown='moveState = false;' onmousemove='moveState = false;' type='file' name='test_file'>
    <input type='submit' value='загрузить' name='upload_file'>
    </form></div>
    ";
    die();
}
//поиск элемента в файле и на сайте по XML_ID
if (isset($_GET['search']))
{
	if ($_GET['iblock']=="Y")
	{
		  CModule::IncludeModule("iblock");
		$check=CIBlock::GetList(Array(),Array("XML_ID"=>'%'.$_GET['search'].'%'));
		    if (!$check) echo 'на сайте таких нет';
		while($res=$check->Fetch())
		{
	//	/bitrix/admin/iblock_edit.php?type=1c_catalog&ID=23&admin=Y
			echo "Название - <em>".$res['NAME']."</em><br>
			Внешний код - <em>".$res['XML_ID']."</em><br>
			<a style=\"float:right\" href='/bitrix/admin/iblock_edit.php?type=".$res['IBLOCK_TYPE_ID']."&ID=".$res['ID']."&admin=Y'>перейти</a><br clear='both'>
			<hr>";
		}
		die();
	}
    $q=$_GET['search'];
    CModule::IncludeModule("iblock");
    $check=CIBlockElement::GetList(Array(),Array("EXTERNAL_ID"=>$q));
    if (!$check) echo 'на сайте таких нет';
    while($res=$check->Fetch())
    echo 'IBLOCK_ID='.$res["IBLOCK_ID"].' <a href="/bitrix/admin/iblock_element_edit.php?ID='.$res["ID"].'&IBLOCK_ID='.$res["IBLOCK_ID"].'&type='.$res["IBLOCK_TYPE_ID"].'" target="_blank">Перейти</a><br>';
    die();
}

//получение  текста xml-файла, который будет переправлен с сайта в 1С при следующем обмене.
if($_GET["mode"] == "query")
{
    CModule::IncludeModule("sale");
    $arParams=Array(
    "SITE_LIST" => COption::GetOptionString("sale", "1C_SALE_SITE_LIST", ""),
    "EXPORT_PAYED_ORDERS" => COption::GetOptionString("sale", "1C_1C_EXPORT_PAYED_ORDERS", ""),
    "EXPORT_ALLOW_DELIVERY_ORDERS" => COption::GetOptionString("sale", "1C_EXPORT_ALLOW_DELIVERY_ORDERS", ""),
    "EXPORT_FINAL_ORDERS" => COption::GetOptionString("sale", "1C_EXPORT_FINAL_ORDERS", ""),
    "FINAL_STATUS_ON_DELIVERY" => COption::GetOptionString("sale", "1C_FINAL_STATUS_ON_DELIVERY", "F"),
    "REPLACE_CURRENCY" => COption::GetOptionString("sale", "1C_REPLACE_CURRENCY", ""),
    "GROUP_PERMISSIONS" => explode(",", COption::GetOptionString("sale", "1C_SALE_GROUP_PERMISSIONS", "")),
    "USE_ZIP" => COption::GetOptionString("sale", "1C_SALE_USE_ZIP", "Y"));
    $arFilter = Array();
    if($arParams["EXPORT_PAYED_ORDERS"])
        $arFilter["PAYED"] = "Y";
    if($arParams["EXPORT_ALLOW_DELIVERY_ORDERS"]<>"N")
        $arFilter["ALLOW_DELIVERY"] = "Y";
    if(strlen($arParams["EXPORT_FINAL_ORDERS"])>0)
    {
        $bNextExport = false;
        $arStatusToExport = Array();
        $dbStatus = CSaleStatus::GetList(Array("SORT" => "ASC"), Array("LID" => LANGUAGE_ID));
        while ($arStatus = $dbStatus->Fetch())
        {
            if($arStatus["ID"] == $arParams["EXPORT_FINAL_ORDERS"])
                $bNextExport = true;
            if($bNextExport)
                $arStatusToExport[] = $arStatus["ID"];
        }
        $arFilter["STATUS_ID"] = $arStatusToExport;
    }
    if(strlen($arParams["SITE_LIST"])>0)
        $arFilter["LID"] = $arParams["SITE_LIST"];
    if(strlen(COption::GetOptionString("sale", "last_export_time_committed_/bitrix/admin/1c_excha", ""))>0)
    $arFilter[">=DATE_UPDATE"] = ConvertTimeStamp(COption::GetOptionString("sale", "last_export_time_committed_/bitrix/admin/1c_excha", ""), "FULL");
    ob_start();
    CSaleExport::ExportOrders2Xml($arFilter, false, $arParams["REPLACE_CURRENCY"]);
    $xml=ob_get_contents();
    ob_end_clean();
    $dres=CSite::GetList();
    $site=$dres->Fetch();
    if (strtoupper($site['CHARSET'])<>'WINDOWS-1251')
    $xml=$APPLICATION->ConvertCharset($xml,$site['CHARSET'],"WINDOWS-1251");
    if (@$_GET['save']=='Y')
    {
        unlink($_SERVER['DOCUMENT_ROOT'].$UPLOAD_DIR."/bx_orders.xml");
        $f = fopen ($_SERVER['DOCUMENT_ROOT'].$UPLOAD_DIR."/bx_orders.xml", 'a+');
        fwrite ($f,$xml);
        fclose($f);
        $xml=trim($xml);
        echo '<pre style="background:white; text-align:right">текст xml-файла, который будет передан в 1С при следующем обмене</pre>';
        echo '<div onmousedown="moveState = false;" onmousemove="moveState = false;" style="overflow-y:scroll;height:90%;width:100%;background:white;">';
        highlight_string($xml);
        echo '</div>';
    }
    else echo $xml;
    die();
}

if ($_GET["action"]=="show_bxmltree")
{
    CModule::IncludeModule('iblock');
    $xmlfile=new CIBlockXMLFile;
    $dbres=$xmlfile->GetList();
    if (!$dbres)
       die();
    echo '<div  style="overflow:auto;height:100%;width:100%;">';
    echo '<table cellspacing=2 cellpadding=5 style="border:0px solid #E6E6FA;font-size:11px;background:white;">';
    echo '<tr style="background:grey;color:white;">';
    echo '<td>'.'ID'.'</td>';
    echo '<td>'.'PARENT_ID'.'</td>';
    echo '<td>'.'LEFT_MARGIN'.'</td>';
    echo '<td>'.'RIGHT_MARGIN'.'</td>';
    echo '<td>'.'DEPTH_LEVEL'.'</td>';
    echo '<td>'.'NAME'.'</td>';
    echo '<td>'.'VALUE'.'</td>';
    echo '<td>'.'ATTRIBUTES'.'</td>';
    echo '</tr>';

    while($res=$dbres->Fetch())
    {
        echo '<tr>';
        foreach ($res as $value):
        echo '<td valign=top  style="width:50px;border:1px solid #E6E6FA">'.$APPLICATION->ConvertCharset($value,SITE_CHARSET,"windows-1251").'</td>';
        endforeach;
        echo '</tr>';
    }
    echo '</table>';
    echo '<div>';
    die();
}

//вывод содержимого файлов
if ($_GET["mode"]=="show_xml")
{
    $filename=$_SERVER['DOCUMENT_ROOT'].$_GET["path"].$_GET["file"];
    echo '<pre style="background:white; text-align:left">Редактировать: <a href="/bitrix/admin/fileman_file_edit.php?path='.$_GET["path"].$_GET["file"].'&full_src=Y" target="_blank">'.$filename.'</a></pre>';
    if (isset($_GET["path"]))
        $filename=$_SERVER['DOCUMENT_ROOT'].$_GET["path"].$_GET["file"]; else
    $filename=$_SERVER['DOCUMENT_ROOT'].$UPLOAD_DIR."/1c_catalog/".$_GET["file"];
    $file_ext=substr(strrchr($filename, '.'), 1);
    if (in_array($file_ext,$APicture))
    {
        echo "<img src='".$_GET["path"].$_GET["file"]."'>";
        die();
    }
    $xml = file_get_contents($filename);
    if (!$xml)
        echo "Нет такого файла";
    if(@$_GET['isutf']=='Y')
        $xml=$APPLICATION->ConvertCharset($xml,"UTF-8","windows-1251");
    elseif (ToUpper(SITE_CHARSET)!='WINDOWS-1251')
    $xml=$APPLICATION->ConvertCharset($xml,SITE_CHARSET,"windows-1251");

    function callback($buffer)
    {
        if (round(filesize($_SERVER['DOCUMENT_ROOT'].$UPLOAD_DIR."/1c_catalog/".$_GET['offers'])/1024,2)<2000)
        {
            $pattern=Array('/Товар/','/ЗначенияСвойства/');
            $replacements=Array("<b style='color:red'>Товар</b>","<b style='color:green'>ЗначенияСвойства</b>");
            $buffer=preg_replace($pattern, $replacements, $buffer);
        }
        if (!$f=fopen($_SERVER['DOCUMENT_ROOT'].$_GET["path"].$_GET["file"],'a'))
            $WriteError="<p style='font-size:10px;color:red;'>Открыть на запись файл не удастся!</p>";
        fclose($f);
        return '

		<div  onmousedown="moveState = false;" onmousemove="moveState = false;" style="overflow:auto;height:90%;width:100%;background:white;">'.$buffer.'</div>

		';
    }
    ob_start("callback");
    highlight_string($xml);
    ob_end_flush();
    die();
}

if ($_GET["mode"]=="edit")
{

if (isset($_GET["path"]))
    $filename=$_SERVER['DOCUMENT_ROOT'].$_GET["path"].$_GET["file"]; else
$filename=$_SERVER['DOCUMENT_ROOT'].$UPLOAD_DIR."/1c_catalog/".$_GET["file"];
echo '<pre style="background:white; text-align:right"><a href="/bitrix/admin/fileman_file_edit.php?path='.$_GET["path"].$_GET["file"].'&full_src=Y" target="_blank">'.$filename.'</a></pre>';

    $file_ext=substr(strrchr($filename, '.'), 1);
    if (in_array($file_ext,$APicture))
    {
    echo "<img src='".$_GET["path"].$_GET["file"]."'>";  die();
    }
    $xml = file_get_contents($filename);

    if (!$xml) echo "Нет такого файла";
    if(@$_GET['isutf']=='Y')
            $xml=$APPLICATION->ConvertCharset($xml,"UTF-8","windows-1251");
    elseif (ToUpper(SITE_CHARSET)!='WINDOWS-1251')
            $xml=$APPLICATION->ConvertCharset($xml,SITE_CHARSET,"windows-1251");
    ?>


    <div id="sfstatus" onmousedown="moveState = false;" onmousemove="moveState = false;" style="display:none;color:green;border:1px dashed green;padding:5; text-align:center;width:250px;margin:5"></div>
    <table>
    <tr>
            <td>
                    <div onmousedown="moveState = false;" onmousemove="moveState = false;" id="savefile" align="center"  onclick="SaveFile('<?=$_GET["path"].$_GET["file"]?>')" OnMouseOver="LightOn(this,'сделанные изменения будут сохранены');" OnMouseOut="LightOff()"; class="small_but">Сохранить</div>
            </td>
            <td>
                    <div onmousedown="moveState = false;" onmousemove="moveState = false;" id="viewfile" align="center"  onclick="ShowFile('<?=$_GET["file"]?>','<?=$_GET["path"]?>','N')" OnMouseOver="LightOn(this,'переход в режим просмотра текущего файла');" OnMouseOut="LightOff()"; class="small_but">Посмотреть</div>
            </td>
    </tr>
    </table>
    <?
    echo '

	<textarea onmousedown="moveState = false;" onmousemove="moveState = false;" id="textfile" rows="60" cols="119" style="position:absolute;overflow:auto;font-size:16pt;height:82%;width:98%;">'.htmlspecialchars($xml).'</textarea>
	';
    die();
}

//save
	if ($_REQUEST["action"]=="save")
	{
	    $filename=$_SERVER['DOCUMENT_ROOT'].$_REQUEST["filename"];
		$f = fopen($filename, 'w+');
		if (ToUpper(SITE_CHARSET)!='UTF-8')
			$text=$APPLICATION->ConvertCharset($_REQUEST["text"],'UTF-8',SITE_CHARSET);
		if (($f)&&(fwrite($f, $text)!=false))
		echo 'OK'; else echo 'error';
		fclose($f);
		die();
	}
//проверка файла,  не существует или нет прав на чтение?
if (@$_GET['check_file']=="Y")
{
    unset($_SESSION["BX_CML2_IMPORT"]);
    $c=0;
    if(file_exists($_SERVER['DOCUMENT_ROOT'].$UPLOAD_DIR."/1c_catalog/".$_GET['file'])) $c=$c+2;
    else $c=$c+3;
    if($c==2)
        echo "<div style='width:270;font-size:11;border-left:1px solid black;border-right:1px solid black;border-bottom:1px solid black;background-color:FA8072;padding:5'>Нет прав на чтение файла!</div>";
    if ($c==3)
        echo "<div style='width:270;font-size:11;border-left:1px solid black;border-right:1px solid black;border-bottom:1px solid black;background-color:FA8072;padding:5'>Файла ".$_GET['file']." не сущестует!</div>";
    die();
}

$items[0]=Array();
$group[0]=Array();
$c_offers[0]=Array();

//получение  информации по количеству групп, товаров и предложений, путём анализа файлов каталога и предложений
if (@$_GET['info']=="Y")
{
    $content=file_get_contents($_SERVER['DOCUMENT_ROOT'].$_GET['file']);
    $offer=iconv("WINDOWS-1251", "UTF-8", '<Предложение>');
    //	$product=iconv("WINDOWS-1251", "UTF-8", '<Товар>');
    $section=iconv("WINDOWS-1251", "UTF-8", '<Группа>');
    preg_match_all('/'.$product.'/', $content , $items);
    preg_match_all('/'.$section.'/', $content , $group);
    preg_match_all('/'.$offer.'/', $content, $c_offers);
    $file_size=round(filesize($_SERVER['DOCUMENT_ROOT'].$_GET['file'])/1024,2);
    ?>

    <table style="font-size:11;" cellpadding="0"><tr><td align="right">Размер файла: </td><td><b><?=$file_size.' kb';?></b> | </td>
    <td align="right" >Предложений: </td><td><b><?=count($c_offers[0]);?></b> | </td>
    <td align="right">Товаров: </td><td><b><?=count($items[0]);?></b> | </td>
    <td align="right">Групп: </td><td><b><?=count($group[0]);?></b></td></tr>
    </table>
    <?	die();
}

//смена метки времени последнего обмена
if (!$_REQUEST['path1']==''):
    $path_companent = substr($_REQUEST['path1'], 0, 22);
    $full_path=$_REQUEST['path1'];
else:
    $path_companent = substr($catalog_import_path, 0, 22);
    $full_path=$catalog_import_path;
endif;

if((!$_REQUEST['date']=='')&&(isset($_REQUEST['change'])))
{

    if (!file_exists("bx_exchange_date.log"))
    {
        $f = fopen ("bx_exchange_date.log", 'a+');
        fwrite ($f, ConvertTimeStamp(COption::GetOptionString("sale", "last_export_time_committed_".$path_companent, ""), "FULL"));
        fclose($f);
    }

	$current_date=ConvertTimeStamp(COption::GetOptionString("sale", "last_export_time_committed_".$path_companent, ""), "FULL");

    COption::SetOptionString("sale", "last_export_time_committed_".$path_companent, MakeTimeStamp($_REQUEST['date'], "DD.MM.YYYY HH:MI:SS"));
}


if (isset($_REQUEST['AJAX']))
{
	echo $date;
	die();
}

//получнеие списка заказов, которые будут выгружены в 1с при следующем обмене
if (isset($_REQUEST['check'])):
	CModule::IncludeModule("sale");
	$path_companent = substr($_REQUEST['path'], 0, 22);
	if(isset($_REQUEST['PAYED']))
		$arFilter['PAYED']="Y";
	if(isset($_REQUEST['ALLOW_DELIVERY']))
		$arFilter['ALLOW_DELIVERY']="Y";
	$arFilter[">=DATE_UPDATE"] = ConvertTimeStamp(COption::GetOptionString("sale", "last_export_time_committed_".$path_companent, ""), "FULL");
	$change=false;
	$dbOrderList = CSaleOrder::GetList(
						array("ID" => "DESC"),
						$arFilter,
						false,
						$count,
						array("ID", "LID", "PERSON_TYPE_ID", "PAYED", "DATE_PAYED", "EMP_PAYED_ID", "CANCELED", "DATE_CANCELED", "EMP_CANCELED_ID", "REASON_CANCELED", "STATUS_ID", "DATE_STATUS", "PAY_VOUCHER_NUM", "PAY_VOUCHER_DATE", "EMP_STATUS_ID", "PRICE_DELIVERY", "ALLOW_DELIVERY", "DATE_ALLOW_DELIVERY", "EMP_ALLOW_DELIVERY_ID", "PRICE", "CURRENCY", "DISCOUNT_VALUE", "SUM_PAID", "USER_ID", "PAY_SYSTEM_ID", "DELIVERY_ID", "DATE_INSERT", "DATE_INSERT_FORMAT", "DATE_UPDATE", "USER_DESCRIPTION", "ADDITIONAL_INFO", "PS_STATUS", "PS_STATUS_CODE", "PS_STATUS_DESCRIPTION", "PS_STATUS_MESSAGE", "PS_SUM", "PS_CURRENCY", "PS_RESPONSE_DATE", "COMMENTS", "TAX_VALUE", "STAT_GID", "RECURRING_ID")
					);
?>
	Изменения в заказах <hr>
	<div style="font-size:12;padding:3;background: white;"> Дата последнего обмена - <?=$arFilter[">=DATE_UPDATE"]?></div>
	<br>
<?	$n=0;
	echo '<div style="font-size:11;padding:3;background: white;">';
	while($arOrder = $dbOrderList->Fetch())
	{
		$n++;
		echo '<a href="/bitrix/admin/sale_order_detail.php?ID='.$arOrder["ID"].'" target="_blank" >Заказ №'.$arOrder["ID"].'</a>';
		echo ' - дата именения ',$arOrder["DATE_UPDATE"];
		echo '<br>';
		$change=true;
	}
	if (!$change) echo "На сайте нет заказов, изменённых после даты последнего обмена с 1С!!!";
	echo '<br><b>ВСЕГО ЗАКАЗОВ: '.$n.'</b><br>';
	echo "</div>";
	die();
endif;
if (isset($_REQUEST['setstep']))
{
	$_SESSION["BX_CML2_IMPORT"]["NS"]["STEP"]=IntVal($_REQUEST['setstep']);
	echo $_SESSION["BX_CML2_IMPORT"]["NS"]["STEP"];
	die();
}
unset($_SESSION["BX_CML2_IMPORT"]);//сброс шага импорта
$host='http://'.$_SERVER['HTTP_HOST'].$_SERVER['SCRIPT_NAME'];//хост
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">

<html>
<head>
     <link rel="stylesheet" href="http://codemirror.net/lib/codemirror.css">
    <script src="http://codemirror.net/lib/codemirror.js"></script>
    <script src="http://codemirror.net/mode/xml/xml.js"></script>
    <link rel="stylesheet" href="http://codemirror.net/mode/javascript/javascript.css">

    <link rel="stylesheet" href="http://codemirror.net/mode/clike/clike.css">
    <script src="http://codemirror.net/mode/javascript/javascript.js"></script>
    <script src="http://codemirror.net/mode/php/php.js"></script>
    <script src="http://codemirror.net/mode/clike/clike.js"></script>

<?CUtil::InitJSCore(Array('ajax','window'));
$APPLICATION->ShowHeadScripts();
$APPLICATION->ShowHeadStrings();
?>
<style>
.CodeMirror-scroll
{
	height:100%;
}
.CodeMirror {
  overflow: auto;
  background:white;
  height: 85%;
  line-height: 1em;
  font-family: monospace;
  _position: relative; /* IE6 hack */
}


      .CodeMirror pre{

        font-size:14px;
        line-height: 1.3em;
      }
      .activeline {background: #E6E6FA !important;}
body
{
	background:white;
	font-family: Arial,sans-serif,Lucida Sans;
	font-size:12px;
	padding:0px;
	margin:0px;
	margin-left:5px;
}

input
{
	outline: none;
}

.button
{
   background-color:#B9D3EE;
   border:1px solid #ADC3D5;
   width:150;
   height:20px;
   font-size:13;
   color:#2B587A;
   padding-top:5px;
   padding-bottom:2px;
   margin:4px;
}

.field
{
	clear:both;
	height:90%;
	width:95%;
	position:absolute;
	padding:10px;
}

.tab_field_dav
{
	max-height:400px;
	max-width:750px;
	min-width:750px;
	background:white;
}
.button2
{
   background-color:#B9D3EE;
   border:none
   cursor:hand;
   text-align:center;
   width:150;
   height:20px;
   font-size:12;
   color:#2B587A;
   margin:5;
   padding-top:5;
   box-shadow:0px 0px 3px 0px #c3c6c9;
    -webkit-box-shadow:0px 0px 3px 0px #c3c6c9;
    -moz-box-shadow:0px 0px 3px 0px #c3c6c9;
	border-radius:5px;

}


.rtopwin, .rbottomwin{display:block;width:200;}
.rtopwin *,.rbottomwin *{display: block; height: 1px; overflow: hidden;background:#B9D3EE;}
.r1win{margin: 0 5px;}
.r2win{margin: 0 3px;}
.r3win{margin: 0 2px;}
.r4win{margin: 0 1px; height: 2px;}

.msection
{
	width:180;
	font-size:14;
	color:white;
	border-top:none;
	background:#B9D3EE;
	border-bottom:6px solid #B9D3EE;
}



.FrontTab
{
  position:absolute;
  height:15;
  top:30;
  border-top:1px solid black;
  border-right:1px solid black;
  border-left:1px solid black;
  border-bottom:2px solid #FFF8DC;
  background:#FFF8DC;
  padding:5;
  margin:0;
  width:100;
  z-index:100;
}

.message
{
   background-color:#B9D3EE;
   border:2px solid red;
   text-align:center;
   position:absolute;
   height:15px;
   padding:10;
   left:40%;
   top:50%;
   opacity:0.5;
   font-size:12;
   color:red;
   z-index:10000;
}

.tab
{
	border-top-left-radius:5px;
	border-top-right-radius:5px;
	-moz-border-top-left-radius:5px;
	-moz-border-top-right-radius:5px;
	border-right: 1px dotted #B0C4DE;
	border-left: 1px dotted #B0C4DE;
	padding-left:10px;
	padding-right:10px;
	padding-top:5px;
	padding-bottom:5px;
	font-size:16px;
	margin-left:2px;
	float:left;
	background: #d3e1fa;
	cursor:pointer;
	opacity:0.2;
	}
.tab_dav
{
	border-top-left-radius:5px;
	border-top-right-radius:5px;
	-moz-border-top-left-radius:5px;
	-moz-border-top-right-radius:5px;
	border-right: 1px dotted #B0C4DE;
	border-left: 1px dotted #B0C4DE;
	border-top: 1px solid #B0C4DE;
	padding:5px;
	font-size:12px;
	margin-left:2px;
	float:left;
	background: #d3e1fa;
	cursor:pointer;
	opacity:0.2;
	}

.tab_s
{

	border: 1px solid #d3e1fa;
		border-right: 1px dotted #B0C4DE;
	border-left: 1px dotted #B0C4DE;
	padding-left:10px;
	padding-right:10px;
	padding-top:5px;
	padding-bottom:5px;
	font-size:12px;
	margin-left:2px;
	margin-bottom:-2px;
	float:left;
	background: #d3e1fa;
	cursor:pointer;
	opacity:0.2;
}

.ver
{
	border-radius:5px;
	border-bottom-right-radius:5px;
	-moz-border-bottom-left-radius:5px;
	-moz-border-bottom-right-radius:5px;
	border: 1px solid #E7ECF5;
	padding:5px;
	float:right;
	font-size:10px;
	margin-left:2px;
	background: #E7ECF5;
	position:absolute;
	right:2%;
	top:2%;
}

.left_panel
{
	border-top-right-radius:5px;
	border-bottom-right-radius:5px;
	-moz-border-top-left-radius:5px;
	-moz-border-top-right-radius:5px;
	border: 1px solid #d3e1fa;
	padding-left:10px;
	padding-right:10px;
	float:left;
	right:0px;
	max-width:140px;
	padding-top:5px;
	padding-bottom:5px;
	font-size:12px;
	margin-left:2px;
	background: #d3e1fa;
	cursor:pointer;
}

.file_panel
{

	border: 1px dotted #E7ECF5;
	font-size:12px;
	padding:5px;
	background:white;

}


.divwin
{
	width: 300px;
	background: #a3b3cf;
	display: none;
   	cursor:hand;
	left:320px;
	top:160px;
}

.divwin_times
{
	width: 300px;
	background: #a5afd6;
	display: none;
	left:50%;
	top:50%;
}

.divwin_param
{
	width: 300px;
	background: #a3b3cf;
	display: none;
	left:25%;
	top:25%;
}


.divwin_info
{
	width: 320px;
	background: #a5afd6;
	display: none;
	left:7px;
	top:60%;
	left:75%;
}

.divwin_main
{
	border: 1px solid black;
	background: #a5afd6;
	display: block;
	left:10px;
	top:20px;
}

.divwin_custom
{
	width: 500px;
	background: #a5afd6;
	display: block;
	left:225px;
	top:8px;
}

.round_win
{
	border-radius:3px;
	padding:10px 5px 5px 5px;
	position: fixed;
	cursor:default;
	border: 2px solid #c3d0e9;
	z-index:100;
	font-size:14px;
}


.round
{
	border-radius:5px;
	-moz-border-radius:5px;
}

.auth_form
{

	background:white;
	font-size:11px;
	padding:5px;
	width:30%;

}


.auth_form_text input
{

	padding:5px;
	border-radius:3px;
	font-size:13px;
	width:90%;


}

.import_form
{
right:0px;
float:right;
top:10px;
position:absolute;
background:white;
	font-size:11px;
	padding:5px;
	width:30%;

}

.auth_field
{
	font-size:11px;
	clear:both;
	width:99%;

}

.but
{
	border: 1px solid Gray;
	background-color: #DCDCDC;
	padding: 1px 1px 1px 1px;
	margin:1px;
	font-size:12;
	width:150px;
	color:black;
	align:center;
}

.small_but
{

	background-color: white;
	padding:5px;
	width:130px;
	font-size:12px;
	color:black;
	margin:2px;
	text-overflow:ellipsis;
	overflow:hidden;
	clear:both;
	border:1px solid #dae1ed;
	border-radius:5px;
	cursor:pointer;
	color:#777;
	white-space: nowrap;

	background-image: linear-gradient(bottom, rgb(201,191,255) 44%, rgb(206,194,255) 72%, rgb(185,176,255) 0%);
background-image: -o-linear-gradient(bottom, rgb(201,191,255) 44%, rgb(206,194,255) 72%, rgb(185,176,255) 0%);
background-image: -moz-linear-gradient(bottom, rgb(201,191,255) 44%, rgb(206,194,255) 72%, rgb(185,176,255) 0%);
background-image: -webkit-linear-gradient(bottom, rgb(201,191,255) 44%, rgb(206,194,255) 72%, rgb(185,176,255) 0%);
background-image: -ms-linear-gradient(bottom, rgb(201,191,255) 44%, rgb(206,194,255) 72%, rgb(185,176,255) 0%);

background-image: -webkit-gradient(
	linear,
	left bottom,
	left top,
	color-stop(0.44, rgb(201,191,255)),
	color-stop(0.72, rgb(206,194,255)),
	color-stop(0, rgb(185,176,255))
);

}

.small_but_float
{

	background-color: white;
	width:50px;
	font-size:11px;
	color:black;

	border:1px dotted #dae1ed;

}

.small_but_m
{

	background-color: white;
	padding:3px;
	width:130px;
	font-size:11px;
	color:black;
	margin:2px;
	clear:both;
	border:none;

}

.small_but_float:hover
{
		background-color: #e9edf5;
	 box-shadow:inset 0px 0px 4px 0px white;
    -webkit-box-shadow:inset 0px 0px 4px 0px white;
    -moz-box-shadow:inset 0px 0px 4px 0px white;
}

.small_but_m:hover
{
		background-color: #e9edf5;
	 box-shadow:inset 0px 0px 4px 0px white;
    -webkit-box-shadow:inset 0px 0px 4px 0px white;
    -moz-box-shadow:inset 0px 0px 4px 0px white;
}

.small_but:hover
{
		background-color: #e9edf5;
	 box-shadow:inset 0px 0px 2px 0px white;
    -webkit-box-shadow:inset 0px 0px 2px 0px white;
    -moz-box-shadow:inset 0px 0px 2px 0px white;

   background-image: linear-gradient(bottom, rgb(201,191,255) 0%, rgb(206,194,255) 84%, rgb(185,176,255) 45%);
background-image: -o-linear-gradient(bottom, rgb(201,191,255) 0%, rgb(206,194,255) 84%, rgb(185,176,255) 45%);
background-image: -moz-linear-gradient(bottom, rgb(201,191,255) 0%, rgb(206,194,255) 84%, rgb(185,176,255) 45%);
background-image: -webkit-linear-gradient(bottom, rgb(201,191,255) 0%, rgb(206,194,255) 84%, rgb(185,176,255) 45%);
background-image: -ms-linear-gradient(bottom, rgb(201,191,255) 0%, rgb(206,194,255) 84%, rgb(185,176,255) 45%);

background-image: -webkit-gradient(
	linear,
	left bottom,
	left top,
	color-stop(0, rgb(201,191,255)),
	color-stop(0.84, rgb(206,194,255)),
	color-stop(0.45, rgb(185,176,255))
);

}

.closeButton {
	position: absolute;
	top: 0px;
	right: 0px;
	font-weight: bold;
	cursor: pointer;
	z-index:250;
	background: white;
	padding: 2px 5px 2px 5px;
	border-radius:1px;
	margin:3px;
}

.sysbutton
{
	position: absolute;
	top: 2px;
	right: 2px;
	font-size:12;
	border: 1px solid gray;
	cursor: pointer;
	z-index:250;
	background: white;
	padding: 2px 4px 2px 4px;
}

.main_div
{
	width:25%;
   background-color:#FFE4B5;
   border:1px solid #ADC3D5;
   text-align:center;
   position:fixed;
   left:74%;
   top:45px;
   font-size:11;
}

.main_table
{
   width:50%;
   text-align:center;
}

.th_table
{
	border:1px solid #ADC3D5;
	text-align:right;
	font-size:11
}

.th_table2
{
	border:1px solid #ADC3D5;
	text-align:right;
}


table.menu
{
	background-color:white;
   width:110;
   height:40;
   padding:5px;
}

td.menu
{
   background-color:white;
   width:110;
   height:25;
   padding:2px;
   z-index:7000;
}

.point_menu
{
   background-position:left;
   font-size:12px;
   background-repeat: no-repeat;
   padding:5px 10px 5px 20px;
   text-decoration: none;
   color:black;
   position:relative;
}

td.menu:hover
{
		background-color: #f1f4f9;
	 box-shadow:inset 0px 0px 4px 0px white;
    -webkit-box-shadow:inset 0px 0px 4px 0px white;
    -moz-box-shadow:inset 0px 0px 4px 0px white;
}

a.menu
{
   background-image: url(/bitrix/images/fileman/view.gif);
}

a.menu2
{
   background-image: url(/bitrix/images/fileman/edit_text.gif);
}

a.menu_del
{
   background-image: url(/bitrix/images/fileman/htmledit2/c2del.gif);
}

a.menu_unzip
{
	background-image: url(/bitrix/images/fileman/htmledit2/redo.gif);
}

a.menu_dw
{
   background-image: url(/bitrix/images/fileman/types/file.gif);
}

.delete_but
{
   background-image: url(http://icons.iconarchive.com/icons/yusuke-kamiyamane/fugue/16/cross-icon.png);
    background-repeat:no-repeat;
	padding-left:10px;
	border:none;
	background-color:white;
}

 .refresh_but
{

   background-image: url(http://icons.iconarchive.com/icons/kyo-tux/aeon/16/Sign-Refresh-icon.png);
    background-repeat:no-repeat;
	padding-left:10px;
	border:none;
	background-color:white;
}

.light:hover
{
	cursor:pointer;
	background-color: #f1f4f9;
	 box-shadow:inset 0px 0px 4px 0px white;
    -webkit-box-shadow:inset 0px 0px 4px 0px white;
    -moz-box-shadow:inset 0px 0px 4px 0px white;
}

   A {
   text-decoration: none;
   color:#36648B;
   }
.main_div
{
	border-bottom-left-radius:5px;
	border-bottom-right-radius:5px;
	border-top-right-radius:5px;
	-moz-border-bottom-left-radius:5px;
	-moz-border-bottom-right-radius:5px;
	-moz-border-top-right-radius:5px;
	border: 1px solid #E7ECF5;
	padding:10px;
	margin-left:2px;
	z-index:2000;
	width:70%;
	font-size:12px;
	background: #E7ECF5;
}

.authdiv
{
	border: 1px solid #E7ECF5;

	background:white;
	padding:10px;
	margin-bottom:10px;
	font-size:12px;
	width:80%;
	background: #E7ECF5;
}

.round
{
	border-bottom-left-radius:5px;
	border-bottom-right-radius:5px;
	border-top-right-radius:5px;
	border-top-left-radius:5px;
	-moz-border-bottom-left-radius:5px;
	-moz-border-bottom-right-radius:5px;
	-moz-border-top-right-radius:5px;
}




.list
{
	border-radius:5px;
	-moz-border-radius:5px;
	border: 1px solid white;
	background:white;
	padding:10px;
	font-size:12px;
	background:white;
}

.check_point_list
{
	border-bottom: 1px solid #E7ECF5;
	width:100%;
	font-weight:bold;
}
</style>
<script>
var dragObjects=new Array('log3','list','load','param','main_info','test_window','1cbitrix');
<?=$win;?>
</script>
</head>
<body class="body" style='overflow:hidden;' onmousedown="Hide(event)" onkeydown="ShowFileMan(event)">
<div id="custom_windows">

<?
ob_start();
?>
<table class="file_panel" width=100%>
<tr>
<td class="auth_form_text">
<input id=search_str placeholder="фильтр" style='width:90%;margin-bottom:2px;'  name='search_str' OnChange=GetFileList('path_fileman','testfileman') value='<?if(isset($_SESSION['bx_1c_import']['filter'])) echo $_SESSION['bx_1c_import']['filter'].'\'>'; else echo '\'>'?><br>
<input placeholder="путь"  onmousedown="moveState = false;" onmousemove="moveState = false;" OnChange="GetFileList('path_fileman','testfileman');" id="path_fileman" style="width:90%;" name="path_fileman" value='<?if(isset($_SESSION['bx_1c_import']['path'])) echo $_SESSION['bx_1c_import']['path']; else echo $UPLOAD_DIR.'/1c_catalog/';?>'>
</td>
</tr>
<tr><td colspan=2 align=left>
<?=AddButton('go');?>
</td></tr>
</td></tr>
</table>

<?
$beforeInner=ob_get_contents();
ob_end_clean();
$afterinner='<hr><div id="info">----</div>';

$inner='<table cellspacing="0" cellpadding="0" style="width:100%;font-size:10px;"><tr><td>
<div id="testfileman" class="file_panel">загрузка...</disv></td>
<td class="file_panel" valign=top width=100>
<input type=checkbox id=isdir>папку<br>
<input  id=cfilename style="font-size:11px; onmousedown="moveState = false;" onmousemove="moveState = false;" value=\'bx_test.php\'>'.AddButton('crfile',false,true).'<hr>'.AddButton('upfile',false,true).'</td>
</tr>
</table>';

AddWindow("test_window","Файловая структура",'testsfileman',$inner,false,"",false,$beforeInner,$afterinner);
AddWindow("upload_file","Загрузка файла файл",'upload_file_id','<iframe id="file_panel"  height=150 src="'.$script_name.'?upload=Y"></iframe>',$DefaultWinStyleSmall);
?>

</div>



<div id='ind_'style="width:300px;display:none;left:360px;padding:1px;z-index:10000;position:absolute;background-color:#EEE8CD;border:1px solid grey;height:30px;cursor:move;">
<div>Прогресс выполнения шага импорта...</div>
<div id='indicate' style="width:0;background-color:green;border:none;z-index:1;height:10;text-align:center;"></div>
</div><br>
<div id="main_info" class="divwin_info round_win">
<b>Поиск по XML_ID</b><hr>
<div class="closeButton" onclick="Close('main_info')">X</div>
<table>


<tr>
	<td valign="top">
		<table style='width:310px;font-size:11px;border:1px solid #ADC3D5;background-color:white;'>
							<tr>
						<td align="left">
Инфоблок<br>
<input style="font-size:11px" size=40 id="q_iblock" type="text" name="search_iblock" value="XML_ID">
<?AddButton('searchbutton_iblock');?>
						</td>
					</tr>
					<tr>
						<td align="left">
Элемент<br>
										<input style="font-size:11px" size=40 id="q" type="text" name="search" value="XML_ID">
										<?AddButton('searchbutton');?>
						</td>
					</tr>
					<tr>
						<td align="left">
							<div id="result" style="border:1px dashed #ADC3D5;background:white;padding:5px;font-size:12px;font-family:Arial;"></div>
						</td>
					</tr>
				</table>
	</td>
						</tr>
</table>
</div>

<div class="divwin round_win" id="log3">
<b>Лог импорта файла</b>
<hr>
<div class="closeButton" onclick="winClose()">Х</div>
<div id="log" style='font-size:10px;padding:3px;background: white;overflow-y:scroll;height:300px'></div>
<div id="timer" style='font-size:12px;padding:5px;background: white;'></div>
</div>

<div id="param" class="divwin_param round_win" onselectstart="return false" >
<div class="closeButton" onclick="Close('param')">X</div>
<b>Проверка выгрузки заказов</b><hr>
<div style="background-color:white;border-top:1px solid #ADC3D5;font-size:11px;padding:5px;">
<table align="center" width="100%">
<tr><td COLSPAN=2>
<b>Настройки выгрузки</b>
	<hr>
<input id="PAYED" type="checkbox" <?if(isset($_POST['PAYED'])) echo "checked";?> value='Y' name="PAYED"> Оплаченные|<input onmousedown="moveState = false;" onmousemove="moveState = false;" id="DELIVERY" type="checkbox" <?if (isset($_POST['ALLOW_DELIVERY'])) echo "checked";?> value='Y' name="ALLOW_DELIVERY">Доставленные
</td></tr>
<tr>
<td class="auth_form_text">
Путь:<br>
<input id="path"
type="text" size="40" value="<?=$catalog_import_path;?>" name="path"></td>
</tr>


<tr><td COLSPAN=2 align="center">
<input type="button" class="small_but" OnClick="GetOrders()" value="Проверить"></td></tr>

    <tr>

	<td COLSPAN=2 class="auth_form_text">
	<hr>
	<b>Дата последнего обмена</b>
	<hr>

    <input id="date_e" placeholder = "дата поледнего обмена в формате сайта" type="text" size="40" value="<?=ConvertTimeStamp(COption::GetOptionString("sale", "last_export_time_committed_".substr($catalog_import_path, 0, 22), ""), "FULL");?>" name="date_e"></td>
    </tr>
	<tr><td COLSPAN=2 align="center"><?=AddButton('change1',false,true)?></td></tr>
</table>
</div>
<div id="list">
</div>
</div>

		<div id='para1' style='height:80%;top:5px;left:10px;postition:fixed;'>
		<table cellspacing='0' cellpadding='0' width='100%' height='100%' >
		<tr>
			<td id='tab_zone' valign=top height='25px'>
			</td>
			<td>
			<?//=AddButton('test_123',false,true);?>
			</td>
		</tr>
		<tr>
			<td id='field_zone' valign=top >
			</td>
						<td width='26%' align=left valign=top>
						<div id='main_menu_panel' class='left_panel'>
<div align="right">
<?
AddButton('refresh');
AddButton('delete');

?>
</div>
<?
echo '<hr><div  style="padding:4px;text-align:center;background:#d8dcf0;width:124px;">Основное меню</div>';
foreach($MenuArray as $key=>$value)
AddButton($key,true);

?>
<hr>
<div style="padding:4px;text-align:center;background:#d8dcf0">Заказы</div>
<div class="small_but" align=center OnClick="SaveMe('<?=$host;?>')"> XML-файл заказов</div>

   <div class="small_but" align=center onclick="javascript:_BlankXML('<?='view-source:'.$host.'?mode=query'?>')" OnMouseOver="LightOn(this,'будет открыто <b>отдельное</b> окно с текстом xml-файла заказов, которые отдаст сайт 1с-ке при следующем обмене заказаим с 1С')" OnMouseOut="LightOff()">XML в отдельном окне</div>
</div>
<br clear="both" />
<br>
<?if (file_exists($_SERVER["DOCUMENT_ROOT"]."/import_element_log.txt")) $display='block';
else $display='none';?>

<div id="element_log" class='left_panel' style="display:<?=$display?>"><a href='javascript:ShowFile("import_element_log.txt","/","N")'>посмотреть лог</a><span onclick='javascript:Delete("import_element_log.txt","","/");this.parentNode.style.display="none";'> X</span></div>
			</td>
</tr>
	<tr>
			<td>
		<div class='ver'><?echo '1C Diag ver. '.ver;?></div>
			</td>
		</tr>
		</table>
		</div>

<table id="tbl" cellpadding=4 cellspacing=0 style="position:relative;width:70%;z-index:1;left:350;text-align:left;font-size:10pt;">
		<tr><td style='padding-top:45px;text-align:right;'>
		</td>
		<td style='width:100%;'>

		</td></tr>
</table>
<div id="load" align="right" style='border:1px solid black;width:200px;z-index:10000;font-size:15px;position:fixed;top:85%;background-color:white;display:none;'>
Загрузка...
</div>


<div id="menu_1" style="z-index:7000;display:none;border:1px solid #B0C4DE">
<?BuildContextMenu();?>
</div>

<div id="menu_2" style="z-index:7000;display:none;background:white;border:1px solid #B0C4DE;">
	<button class = "small_but_m" OnClick="GetXmlInfo();" style="border:1px solid #B0C4DE" id='checkiblock' >Куда выгружается?</button><br>
	<div style="text-align:center;background:black;color:white;padding:2px;">Шаг импорта</div>
	<button class="small_but_m" OnClick="StartStep(0)">Удаление таблицы</button><br>
	<button class="small_but_m" OnClick="StartStep(1)">Создание таблицы</button><br>
	<button class="small_but_m" OnClick="StartStep(2)">Импорт втаблицу</button><br>
	<button class="small_but_m" OnClick="StartStep(3)">Создание индекса</button><br>
	<button class="small_but_m" OnClick="StartStep(4)">Импорт метаданных</button><br>
	<button class="small_but_m" OnClick="StartStep(5)">Импорт секций</button><br>
	<button class="small_but_m" OnClick="StartStep(6)">Удаление секций</button><br>
	<button class="small_but_m" OnClick="StartStep(7)">Обработка элементов</button><br>
	<button class="small_but_m" OnClick="StartStep(8)">Удаление элементов</button><br>
</div>
<div style="width:100%" id="webdav">
<table height="50%";>
<tr>
	<td colspan=2>
	<div class="authdiv round">
				<span class="auth_form_text" >
				<input style="width:60%;" id="server_" placeholder="<?=$mess['SERVER']?>" name="server" size="30" value="<?=$_COOKIE['DV_SERVER']?>">
				</span>
				<input class="small_but" style="right:2px" type=button onclick="document.getElementById('action_').value='PROPFIND';document.getElementById('current_path_').value=document.getElementById('server_').value;dav_request()" name="post" value="обзор">
				<input class="small_but" type=button onclick="Start_Dav();" value="<?=$mess['START_TEST'];?>">
	 </div>
	 </td>
</tr>
<tr>
	<td colspan=2>
				<div id='dav_tab_zone' style="height:30px">

				</div>
	 </td>
</tr>

<tr>
	<td valign="top">

			<div id="dav_field_zone">

			</div>
	</td>
	<td valign="top">
	 	 <div class="authdiv round auth_form_text" id="log_win" class="divwin_info" style="float:right;">

				<input placeholder="<?=$mess['LOGIN']?>" id="login_" name="login" size=20 value="<?=$_COOKIE['DV_USER']?>"><br/>
				<input id="pass_" placeholder="<?=$mess['PASS']?>" name="pass" size=20 type="password" value="<?=$_COOKIE['DV_PASS']?>">
				<input type='hidden' id="current_path_" name="current_path" size=50 value="<?=$_COOKIE['DV_SERVER']?>">
				<input type='hidden' id="action_" name="action" size=50 value="PROPFIND">

	</div>
	</td>

</tr>
</div>
<table id='ext_import' width='100%' height="99%">
	<tr>
		<td valign="top" id="ext_log" style="position:relative;background:white;width:50%;padding:5px">
		</td>
		<td class="auth_form" valign="top">
		<h3  style="padding:4px;text-align:center;background:#d8dcf0">Данные для авторизации</h3>
		<div class = "auth_form_text">
				Путь<br/>
				<input class="auth_field" id="ext_path" name="current_path" placeholder="http://mysite/bitrix/admin/1c_exchange.php" value=""><br/>
				Имя файла<br/>
				<input  class="auth_field"id="ext_filename"  name="ext_filename" placeholder="import.xml"><br/>
				Логин<br/>
				<input class="auth_field" id="login" name="login" placeholder="admin"><br/>
				<input id="phpsessid" type="hidden" value="">
				Пароль<br/>
				<input class="auth_field" id="pass" name="pass" type="password" value="123456"><br/>
		</div>
				<input class='small_but' type='button' name="start_ext_import" value="начать" onclick="ext_start()"><hr/><h3  style="padding:4px;text-align:center;background:#d8dcf0">Заголовки ответа</h3>
			<div id="ext_headers_log" style="height:40%;overflow:auto;padding:3px;">
			</div>
		</td>
	</tr>
</table>

<?
    $inner ="<div class='import_form round' id='ipfs' style='padding:5px'>";
    $inner.="<div>
	<table width='100%'>
	<tr><td colspan=2>
	Режим диагностики
	<hr>
	</td></tr>
	<tr>
	<td width='15px'>
	<input title='Импорт осуществляется стандартным компонентом + все изменения и добавления товаров пишутся в лог' type='checkbox' id='impself'></td><td align='left' width='100%'><label for='impself'>Импортировать этим скриптом</label></td>
	</tr>
	<tr>
	<td width='15px'>
	<input type='checkbox' OnChange='CheckSkipMode(this)' title='Отладочный режим. Ненужные элементы при импорте будут пропускаться. ' id='skipmode' name='skipmode'></td><td align='left' width='100%'><label for='skipmode' >SkipMode</label>
	</td>
	</tr>
	<tr>
	<td colspan=3 >
	<div id='xml_id_stop' class='auth_form_text' style='display:none;'>
	  Внешний код элемента:<br>
	  <input  OnChange='SetSessionParam(\"xml_id\",this.value)' size='60' value='".$_SESSION['bx_1c_import']['xml_id']."' id='external_id'><br>
	  Имя элемента:<br>
	  <input  OnChange='SetSessionParam(\"element_name\",this.value)' size='60' value='".$_SESSION['bx_1c_import']['element_name']."' id='external_id'>
	</div>
	</td>
	</tr>
	</div>
	</table>
	<hr>";
    $inner.='<a href="javascript:OpenWin(\'/bitrix/admin/1c_admin.php?lang=ru\')">Настройки интеграции</a><hr>';

    ob_start();
    ShowFileSelect('cat_file','Файл каталога в '.$UPLOAD_DIR.'/1c_catalog/:',$UPLOAD_DIR.'/1c_catalog/','xml',2,'ConfirmImport(\'catalog\')');
    //ShowFileSelect('off_file','Файл предложений в /upload/1c_catalog/:','/upload/1c_catalog/','xml',2,'start(\'offers.xml\')');
    ShowFileSelect('order_file','Файл заказов в '.$UPLOAD_DIR.'/1c_exchange/:',$UPLOAD_DIR.'/1c_exchange/','xml',2,'OrderImport(\'hz\')');
    ShowFileSelect('worker','Файл сотрудников в '.$UPLOAD_DIR.'/1c_intranet/:',$UPLOAD_DIR.'/1c_intranet/','xml',2,'ConfirmImport(\'users\')');

    $inner.=ob_get_contents();
     ob_end_clean();

	 $inner.="</div>";
	 echo $inner;
?>
<img id='1cbitrix' style="opacity:0.2;position:fixed;right:0px;bottom:0px;" src="https://1c.1c-bitrix.ru/bitrix/templates/1c.1c-bitrix.ru/images/1c-bitrix-logo.gif">
</body>


</html>

<script>
var steps= new Array('CHECK_METHOD','CREATE_FILE','PROPPATCH_FILE','COPY_FILE','DELETE_FILE','CREATE_FOLDER','COPY_FOLDER','DELETE_FOLDER');
var isfile=false;
var step_count=steps.length;
var step_no=0;
var i,status,des,a
var log=BX("log");
var fileinfo=BX("info");
var result=BX("result");
var timer=BX("timer");
var load=BX("load");
var zup_import=false;
var text_mess=BX('text_mess');
var mess_decorate=BX('mess_decorate');
var load=BX("load");
globalpath='<?=$_SESSION['bx_1c_import']['path']?>';
var ImportStep=0;
var mywindows=new Array("log3","main","list","main_info","bx_main_menu","stepdiag","param");
if (!new_id)
var new_id=new Array();
var moveState = false;
var x0, y0;
var divX0, divY0;
var lastwin="main_info";
var i=1;
var status="continue";
var menu=BX("menu_1");
var menu2=BX("menu_2");
var NewFieldID=1;
var numfile=0;
var filecount=0;
var circule=false;


function CreateFileDialog(Name,where)
{
	var where='testfileman';
	var newP=document.createElement('input');
	//var newP=document.createElement('div');
	var newField=document.createElement('div');
	var FieldID=NewFieldID+'_field';
	var TabID=NewFieldID+'_tab';
		NewFieldID++;
		//alert(TabID);
		//создаём поле для таба
		newField.style.width='350px';
		newField.style.height='80px';
		newField.style.padding='5px';
		newField.style.background = '#FFF8DC';
		newField.style.position='absolute';
		newField.style.top='250px';
		newField.style.left='130px';
		newField.style.display='block';
		newField.style.border='1px solid #00C5CD';
		newField.style.zIndex='99';
		newField.innerHTML='<input type=checkbox id=isdir>Создаём папку, а не файл<br><br>';
		newField.innerHTML+='Имя файла/папки:<br>'+'<input id=cfilename value=\'bx_test.php\'size=40><input type=button value=\'создать\' onclick=CreateFile(\'cfilename\',\'path_fileman\',\'testfileman\')>';
		BX(where).appendChild(newField);

		return newField.id;
}

function CreateIBlock()
{
	var	iblock1c=BX('1ciblock');
	var	iblocktype=BX('iblocktype');
	q="<?=$script_name?>?action=createiblocktype&iblocktype="+iblocktype.value;
	if (iblock1c.value=='on')
	q=q+'&USE_IBLOCK_TYPE=Y';
	AjaxRequest(q,'successiblock',false);
}

function AddWindowRequest(url,id,windowid)
{
	if ((("#" + mywindows.join("#,#") + "#").search("#"+windowid+"#") != -1)||(("#" + new_id.join("#,#") + "#").search("#"+windowid+"#") != -1))
	{
		BX(windowid).style.display="block";
	}
	else
	{
		AjaxRequest(url,id,true);
		new_id[new_id.length]=windowid;
		dragObjects[dragObjects.length]=windowid;

	}
}

function AjaxRequest(url,id,AddResult)
{
	var ajaxreq=createHttpRequest();
	load.style.display="block";
	load.innerHTML=' <img align="center" src="http://vkontakte.ru/images/upload.gif" width="50"/> загрузка...';
	var callback=function(ajaxreq)
	{
		if (ajaxreq.readyState == 4)
		{
			if (AddResult==false)
			{
				BX(id).innerHTML=ajaxreq.responseText;
			}
			else
			{
				BX(id).innerHTML+=ajaxreq.responseText;
			}
		}
		InitMoveableObjects();
	}

	AjaxGet(url,callback)

}

function Download(file,path)
{
	JustHide();
	BX("dwframe").src="<?=$script_name?>?action=download&file="+file+"&path="+path;
}

// создание объекта XMLHttpRequest
function createHttpRequest()

   {
	var httpRequest;
		if (window.XMLHttpRequest)
		httpRequest = new XMLHttpRequest();
		else if (window.ActiveXObject) {
		try {
		httpRequest = new ActiveXObject('Msxml2.XMLHTTP');
		} catch (e){}
		try {
		httpRequest = new ActiveXObject('Microsoft.XMLHTTP');
		} catch (e){}
		}
	return httpRequest;

}
	var	edit=BX('e');
	//var	editutf=BX('eutf');
	var	view=BX('v');
	var	viewutf=BX('vu');
	var	del=BX('d');
	var	unzip=BX('u');
	var	down=BX('dw');
// показываем недоменю
function ShowMenu(event)
{
	<?=$mainmenu;?>
	var evt=fixEvent(event);
	var ext;
	ext=evt.target.textContent.substr(evt.target.textContent.length-4,evt.target.textContent.length);
	menu.style.display="block";
	menu.style.zIndex=10000;
	menu.style.top=evt.clientY+'px';
	menu.style.left=evt.clientX+'px';
	menu.style.position="absolute";
	view.href=evt.target.href;
	viewutf.href="javascript:ShowFile('"+evt.target.textContent+"','"+globalpath+"','Y')";
	//editutf.href="javascript:Showforedit('"+evt.target.textContent+"','"+globalpath+"','Y')";
	edit.href="javascript:Showforedit('"+evt.target.textContent+"','"+globalpath+"','N')";
	del.href="javascript:Delete('"+evt.target.textContent +"','"+evt.target.parentNode.parentNode.parentNode.parentNode.parentNode.id+"')";
	down.href="javascript:Download('"+evt.target.textContent+"','"+globalpath+"')";

	if(ext=='.zip')
	{
		BX("unzip_").style.display='block';
		unzip.href="javascript:UnZip('"+evt.target.textContent+"','"+evt.target.parentNode.parentNode.parentNode.parentNode.parentNode.id+"')";
	} else BX("unzip_").style.display='none';

	   return false;
}

function ShowMenuExp(event)
{
	var evt=fixEvent(event);
	var ext;
	menu2.style.display="block";
	menu2.style.zIndex=10000;
	menu2.style.top=evt.clientY+'px';
	menu2.style.left=evt.clientX+'px';
	menu2.style.position="absolute";
	menu2.style.top=evt.clientY+'px';
	menu2.style.left=evt.clientX+'px';
	   return false;
}

//функция запускает процесс импорта
function CStart()
{
	var path;
	var filecount=BX('cat_file').options.length;
	if (filecount>1)
			circule=true;
		numfile=1;

	CatalogImport();
}


function CatalogImport()
{
	var file=BX('cat_file').options[BX('cat_file').selectedIndex].innerHTML;
	if(!file)
		alert('Не указан файл!');
	path='<?=$script_name?>';
	if(BX('impself').checked==false)
		path='<?=$catalog_import_path?>';
	url=path+"?type=catalog&mode=import&filename="+file;
	if (!BX('log2'))
	{
		var log2=document.createElement('DIV');
		log2.id='log2';
		log2.style.fontSize='15px';
		log2.style.padding='3px';
		log2.style.background='white';
		log2.style.height='95%';
		log2.style.overflowY='scroll';
		log2.style.float='left';
		BX("tab1_field").appendChild(log2);
	}
	log=BX("log2");
	log.innerHTML="<b>Импорт "+file+"</b><hr>";
	load.innerHTML='идёт загрузка...<img align="center" src="http://gifanimation.ru/images/ludi/17_3.gif" width="30"/>';
	query_1c(url);
}

function UserImport()
{
	var file=BX('worker').options[BX('worker').selectedIndex].innerHTML;
	if(!file)
		alert('Не указан файл!');
	else
	{
		if (!BX('log2'))
		{
			var log2=document.createElement('DIV');
			log2.id='log2';
			log2.style.fontSize='15px';
			log2.style.padding='3px';
			log2.style.background='white';
			log2.style.height='95%';
			log2.style.overflowY='scroll';
			log2.style.float='left';
			BX("tab1_field").appendChild(log2);
		}
		log=BX("log2");
		log.innerHTML="<b>Импорт "+file+"</b><hr>";
		load.innerHTML='идёт загрузка...<img align="center" src="http://gifanimation.ru/images/ludi/17_3.gif" width="30"/>';
			path='<?=$user_import_path;?>';
			url=path+"?type=catalog&mode=import&filename="+file;
			query_1c(url);
	}
}



function AjaxPost(url,data,callback)
{
	var obj=createHttpRequest();
	load.style.display="block";
	obj.open("POST", url, true);
	obj.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
	obj.onreadystatechange=function() {callback(obj);};
	obj.send(data);
}

function AjaxGet(url,callback)
{
	var obj=createHttpRequest();
	load.style.display="block";
	load.style.innerHTML='<img align="center" src="http://vkontakte.ru/images/upload.gif" width="50"/>';
	obj.open("GET", url, true);
	obj.onreadystatechange=function()
	{
		callback(obj);
		if (obj.readyState == 4)
			load.style.display="none";
	};
	obj.send(null);
}

function ext_start()
{
	load.innerHTML='Тихо, идёт импорт... <img align="center" src="http://gifanimation.ru/images/ludi/17_3.gif" width="30"/>';
	var login=BX('login').value;
	var filename=BX('ext_filename').value;
	var pass=BX('pass').value;
	var url=BX('ext_path').value;
	var phpsessid=BX('phpsessid').value;
	urldata='{"login":'+login+',"filename":'+filename+',"pass":'+pass+',"url":'+url+',"phpsessid":'+phpsessid+'}';
	AjaxPost("<?=$script_name?>","data="+urldata+"&mode=exchange",ExtImportCallBack);
}


function ExtImportCallBack(ajaxreq)
{

	if (ajaxreq.readyState == 4)
	{
		load.style.display="none";
		try
		{
			json_data=eval("(" +ajaxreq.responseText+")");
			if (json_data.phpsessid && json_data.status=='success')
			{
				BX('phpsessid').value=json_data.phpsessid;

				ext_start();
			}
			else
			{

				BX('ext_log').innerHTML+=json_data.text+'<br>';
				if (json_data.status && json_data.status=='progress')
					ext_start();
				else
				{
					bxtabs.AlertActiveTab('tab1');
				}
			}
			BX('ext_headers_log').innerHTML+=json_data.headers+'<hr>';
		}
		catch(err)
		{
			bxtabs.AlertActiveTab('tab3');
			load.style.display="none";
			BX('ext_log').innerHTML+="Ошибочный ответ сервера:<br>"+ajaxreq.responseText+"<br>";
		}

	}

}
//функция осущетсвляет импорт из файла
function query_1c(url)
		{
		sInd=0;

		BX('indicate').style.width=0;
		var import_1c=createHttpRequest();
		var getstep=createHttpRequest();
		gs="<?=$script_name?>?action=getstep";
		getstep.open('GET',gs,true);

		getstep.onreadystatechange = function()
			{
				if (getstep.readyState == 4)
				{
					ImportStep=getstep.responseText;
					r=url+"&step="+ImportStep;
				//alert(r);
					load.style.display="block";
					import_1c.open("GET", r, true);
				import_1c.onreadystatechange = function()
				{
				a=log.innerHTML;
				if (import_1c.readyState == 4 && import_1c.status == 0)
						{
						error_text="<em>Ошибка в процессе выгрузки</em><div style='width:270px;font-size:11px;border:1px solid 				black;background-color:#ADC3D5;padding:5px'>Сервер упал и не вернул заголовков.</div>"
							log.innerHTML=a+"Шаг "+i+": "+error_text;
							load.style.display="none";
							status="continue"
							alert("Import is crashed!");
						}


				if (import_1c.readyState == 4 && import_1c.status == 200)
							{
							if ((import_1c.responseText.substr(0,8)!="progress")&&(import_1c.responseText.substr(0,7)!="success")&&(import_1c.responseText.substr(0,5)!="debug"))
							{
								error_text="<em>Ошибка в процессе выгрузки</em><div style='font-size:11px;border:1px solid black;background-color:#ADC3D5;padding:5px'>"+import_1c.responseText+"</div>"
								log.innerHTML=a+"Шаг "+i+": "+error_text;
								status="error"
								circul=false;
							}
							else
							{
								if (import_1c.responseText.substr(0,5)=="debug")
								{
									log.innerHTML+=import_1c.responseText;
									load.style.display="none";
								}
								else
								{
									n=import_1c.responseText.lastIndexOf('s')+1;
									l=import_1c.responseText.length;
									mess=import_1c.responseText.substr(n,l);
									log.innerHTML=a+"Шаг "+i+": "+mess+"<br>";
									i++;
								}
							}

							if ((import_1c.responseText.substr(0,7)=="success")||(status=="error"))
							{
							//alert(BX('cat_file').options.length);
							//alert(numfile);
								load.style.display="none";
								load.innerHTML=' <img align="center" src="http://vkontakte.ru/images/upload.gif" width="50"/> загрузка...';
								BX('ind_').style.display='none';
								status="continue"
								proccess="N";
								timer.innerHTML="<hr>Время выгрузки: <b>"+minute+" мин. "+m_second+" сек.</b>";
								//alert(BX('cat_file').options[numfile].text)
								if (circule==true && numfile<=BX('cat_file').options.length-1)
								{
									log.innerHTML+='<br><b>Импорт '+BX('cat_file').options[numfile].text+'</b><hr>';
									query_1c(BX('cat_file').options[numfile].text);
									numfile++;
								}
								else
								{
									numfile=0;
									circule=false;
								}
								bxtabs.AlertActiveTab('tab1');

						if(BX('impself').checked!=false)
							BX('element_log').style.display="block";
							}
							else
							{
                                if (import_1c.responseText.substr(0,5)!="debug")
										query_1c(url);
							}
				}



				};
import_1c.send(null);
	//alert(ImportStep);
				}
			}
		getstep.send(null);
		}


function OrderImport(elem)
{
	var	file=BX('order_file').options[BX('order_file').selectedIndex].innerHTML;
	if (!BX('log2'))
	{
		var log2=document.createElement('DIV');
		log2.id='log2';
		log2.style.fontSize='15px';
		log2.style.padding='3px';
		log2.style.background='white';
		log2.style.height='95%';
		log2.style.overflowY='scroll';
		log2.style.float='left';
		BX("tab1_field").appendChild(log2);
	}
	var log=BX('log2');
	StartTime();
	var callback= function(ajaxreq)
			{
			if (ajaxreq.readyState == 4)
							{
				                log.innerHTML=ajaxreq.responseText;
								proccess='N';
								alert('Длительность импорта заказов: '+seconds+' сек.');
								bxtabs.AlertActiveTab('tab1');
							}

			}
	AjaxGet("<?=$catalog_import_path?>?type=sale&mode=file&filename="+file,callback)
}

//проверка, существует ли файл, права на него
function check_file(file)
{
	var callback= function(ajaxreq)
	{
		if (ajaxreq.readyState == 4 && ajaxreq.status == 200)
		log.innerHTML=log.innerHTML+ajaxreq.responseText;
	};
	AjaxGet("<?=$script_name?>?check_file=Y&file="+file,callback)

}

function Start_Dav()
{
	step_no=0;
	document.getElementById('tab_dav3_field').innerHTML="";
	check();
}

function check()
{
	var login=document.getElementById('login_').value;

	var pass=document.getElementById('pass_').value;
	var server=document.getElementById('server_').value;
	var ajaxreq=createHttpRequest();
	var step=steps[step_no];
	var callback=function(ajaxreq)
	{
		var test=0;

		if (ajaxreq.readyState == 4)
		{

			json_data=eval("(" +ajaxreq.responseText+")");
			var tr=document.createElement("tr");
			var td=document.createElement("td");
			var td2=document.createElement("td");
			td.innerHTML=json_data.text;
			td.className='check_point_list';
			tr.appendChild(td);
			if (json_data.lamp=="red")
				td2.innerHTML='<img src="http://icons.iconarchive.com/icons/taytel/orb/32/cross-icon.png" width=20 height=20 >';
			else
				td2.innerHTML='<img src="http://icons.iconarchive.com/icons/visualpharm/must-have/128/Check-icon.png" width=20 height=20 >';
			tr.appendChild(td2);
			var child=document.getElementById('tab_dav3_field').firstChild;
			if ((child) && (child.tagName=="TBODY"))
			{
				child.appendChild(tr);
			}
			else
			{
				document.getElementById('tab_dav3_field').appendChild(tr);
			}

			if (step_no<(step_count-1))
			{
				step_no++;
				check();
			}

		}
	}
	var q="<?=SCRIPT_NAME?>?login="+login+"&pass="+pass+"&server="+server+"&step="+step+"&mode=test";
	AjaxGet(q,callback)
}

var docframe = false;
function dav_request(filename)
{
	var login=document.getElementById('login_').value;
	var pass=document.getElementById('pass_').value;
	var action=document.getElementById('action_').value;
	var path=document.getElementById('current_path_').value;
	var server=document.getElementById('server_').value;
	var q="<?=SCRIPT_NAME;?>?login="+login+"&pass="+pass+"&post=Y"+"&current_path="+path+"&action="+action+"&server="+server;
	isfile=false;

	var callback=function(ajaxreq)
	{
		var test=0;
	//alert(ajaxreq.responseText);
	//alert(ajaxreq.readyState);
		if (ajaxreq.readyState == 4)
		{
			json_data=eval("(" +ajaxreq.responseText+")");
			if (isfile==false)
			document.getElementById('tab_dav0_field').innerHTML=json_data.file_list;
			document.getElementById('tab_dav2_field').innerHTML=json_data.headers;
			document.getElementById('tab_dav1_field').innerHTML=json_data.xml;
			document.getElementById('tab_dav1_field').style.overflow="auto";


			if (json_data.error)
			{
				document.getElementById('dav_tab2_field').innerHTML=json_data.error;
				alert(json_data.error);
			}
		}
	}

	if (filename)
	{
		isfile=true;
		if(docframe == false)
		{
			docframe = BX.create("iframe");
			document.body.appendChild(docframe);
		}
		docframe.src = path+"/"+filename;
		docframe.style.display= "none";


		//window.open(q,'new','width=1000,height=500, top=100, left=200,toolbar=1 scrollbars=yes');
	}
	else
		AjaxGet(q, callback);

}

function GetFileListDav(path)
{
	document.getElementById('action_').value='PROPFIND';
	document.getElementById('current_path_').value=path;
	dav_request();
	document.getElementById('server_').value=path;
}

function GetFile(filename)
{
	document.getElementById('action_').value='GET';
	dav_request(filename);
}


function StartStep(numstep)
{
	var stepfile=BX('cat_file').options[BX('cat_file').selectedIndex].innerHTML;
	JustHide();
	var callback= function(ajaxreq)
	{
		if (ajaxreq.readyState == 4)
			CatalogImport();
	}

	AjaxGet("<?=$script_name?>?setstep="+numstep,callback)
}

	//отображаем  информацию по товарам, группам и предложениям
function ShowInfo(file)
{
	var fileinfo=BX("info");
	fileinfo.style.opacity=0.4;
	var callback = function(ajaxreq)
			{

				if (ajaxreq.readyState == 4 && ajaxreq.status == 200)
					{

						fileinfo.innerHTML=ajaxreq.responseText;
						fileinfo.style.opacity="";
					}
			};
	AjaxGet("<?=$script_name?>?info=Y&file="+file,callback);
}



	//сбрасываем шаг импорта
function reset()
{
	var callback=function(ajaxreq)
	{
		if (ajaxreq.readyState == 4 && ajaxreq.status == 200)
		alert("Шаг импорта обнулён!");
	}
	AjaxGet("<?=$script_name?>",callback);

}




	//удаляем скрипт
function delete_file()
	{
		if (confirm('Удалить файл?'))
			//edirect("bx_1c_import.php?delete=Y");
			document.location = "<?=$script_name?>?delete=Y";
	}

function ConfirmImport(type)
	{
		if (confirm('Импортировать файл?'))
		{
			if (type=='catalog')
				CatalogImport();
			else
				UserImport();
		}
	}


	//ищем товар по xml_id
function searchbyxmlid()
{
	var qs=BX("q");
	var result=BX("result");
	result.innerHTML=' <img align="center" src="http://vkontakte.ru/images/upload.gif" width="50"/> ';
	var callback = function(ajaxreq)
	{
		if (ajaxreq.readyState == 4 && ajaxreq.status == 200)
				result.innerHTML=ajaxreq.responseText;
	};

		AjaxGet("<?=$script_name?>?search="+qs.value,callback);
}
	//ищем товар по xml_id
function search_iblock_byxmlid()
{
	var qs=BX("q_iblock");
	var result=BX("result");
	result.innerHTML=' <img align="center" src="http://vkontakte.ru/images/upload.gif" width="50"/> ';
	var callback = function(ajaxreq)
	{
		if (ajaxreq.readyState == 4 && ajaxreq.status == 200)
				result.innerHTML=ajaxreq.responseText;
	};

		AjaxGet("<?=$script_name?>?search="+qs.value+"&iblock=Y",callback);
}



var Linkoldelem,Linkoldop,Linkborderold//переменные цвета

function GetXmlInfo()
{
	var file=BX('cat_file').options[BX('cat_file').selectedIndex].innerHTML;
	var xmlpath="/upload/1c_catalog/"+file;
	var callback = function(ajaxreq)
	{
		if (ajaxreq.readyState == 4 && ajaxreq.status == 200)
				alert(ajaxreq.responseText);
	};
	JustHide();

	AjaxGet("<?=$script_name?>?action=xmlgetinfo&path="+xmlpath,callback);
}

//подцветка ссылки
function LinkLightOn(elem,lcolor)
{
	var el=BX(elem);
	Linkoldelem=elem;
	el.style.cursor = 'hand';
	Linkborderold=el.style.color;
	el.style.color=lcolor;

}

function OpenWin(path)
{
	window.open(path,'new','width=1000,height=700, top=100, left=200,toolbar=1 scrollbars=yes');
}

//возвращаение цвета ссылки
function LinkLightOff()
{
	var el= BX(Linkoldelem);
	el.style.color=Linkborderold;
}

// задаём переменные таймера процесса импорта
var m_second=0
var seconds=0
var minute=0
var proccess="Y"
var sInd=0;
var sIntStep=<?=IntVal(300/$interval);?>

//собственно таймер
		function display()
		{
		var indicate=BX('indicate');
			if (m_second==60)
			{
			m_second=0;
			minute+=1;
			}
			if (proccess=="Y")
			{
			seconds+=1;
			m_second+=1;
			//alert(ImportStep);
			if ((ImportStep=='2')||(ImportStep=='7'))
			{
			BX('ind_').style.display='block';
					if (sInd<300)
					{
						sInd=sInd+sIntStep;
						indicate.style.width=sInd +'px';
					} else {sInd=0;}
			}
			else
			{
					sInd=0;
					indicate.style.width=0;
					BX('ind_').style.display='none';
			}
			setTimeout("display()",1000);
		}
		}


		function gotime()
		{
			if (proccess=="Y")
			{
			seconds+=1;
			setTimeout("gotime()",1000);
			}
		}

	function StartTime()
		{
			proccess="Y";
			seconds=0;
			gotime();
		}


//окна дивные
var sStep = 16;
var sTimeout = 15;
var sLeft = 160;
var sRight = 160;
var wObj;

//закрываем окно
function Close(param)
{
BX(param).style.display='none';
}
function winOpen()
{
	wObj.style.display = 'block';
	if (sLeft > 0) {
		sRight += sStep;
		sLeft -= sStep;
		var rect = 'rect(auto, '+ sRight +'px, auto, '+ sLeft +'px)';
		wObj.style.clip = rect;
		setTimeout(winOpen, sTimeout);
	}
}


//закрывем окно красиво
function winClose()
{
	if (sLeft < sRight)
	{
		sRight-=sStep;
		sLeft+= sStep;
		var rect ='rect(auto, '+ sRight +'px, auto, '+ sLeft +'px)';
		wObj.style.clip = rect;
		setTimeout(winClose, sTimeout);
	}
	else wObj.style.display = 'none';
}

var cur="";

var oldindex=false;
var lastwin=false;

////////////////////////////////////////////////////////
////////////////////////////////////////////////////////
////////////////////////Заказы и XML///////////////////
////////////////////////////////////////////////////////
////////////////////////////////////////////////////////

function Showforedit(file,path,is_utf)
{
	JustHide();
    var elem = BX("tab0_field");
    var tb=BX("tbl");
    var callback = function(ajaxreq)
    {
        if (ajaxreq.readyState == 4)
        {
            if (ajaxreq.responseText=='')
            {
                    elem.innerHTML='Файл отсутстует. Произведите выгрузку из 1С.';
                    tb.style.display="block";
            }
            else
            {
                    elem.innerHTML=ajaxreq.responseText;
					var editor = CodeMirror.fromTextArea(document.getElementById("textfile"), {
						lineNumbers: true,
						matchBrackets: true,
						mode: "application/x-httpd-php",
						indentUnit: 8,
						indentWithTabs: true,
						enterMode: "keep",
						tabMode: "classic",
						onCursorActivity: function() {
						editor.setLineClass(hlLine, null);
						hlLine = editor.setLineClass(editor.getCursor().line, "activeline");
						}
					  });
					  var hlLine = editor.setLineClass(0, "activeline");

					bxtabs.AlertActiveTab('tab0');
            }
        }
    };
    AjaxGet("<?=$script_name?>?mode=edit&file="+file+"&path="+path+"&isutf="+is_utf,callback)
}

function SaveFile(file)
{
	var text = encodeURIComponent(BX("textfile").value);
	var sfstatus=BX("sfstatus");
	var save=createHttpRequest();
	load.style.display="block";
	sfstatus.style.display='none';
	save.open("POST", "<?=$script_name?>", true);
	save.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
	save.setRequestHeader("Content-length", text.length);


	save.onreadystatechange = function()
	{
				if (save.readyState == 4)
				{
				//alert(save.responseText);
				if (save.responseText=='OK')
					sfstatus.innerHTML="<b>Изменения в файле сохранены<b>"
					//sfstatus.innerHTML=save.responseText;
					else
					sfstatus.innerHTML="<b style='color:red'>Ошибка при сохранении файла</b>";
					//sfstatus.innerHTML=save.responseText;
					sfstatus.style.display='block';
					load.style.display="none";
				}
			};
	save.send("action=save&filename="+file+"&text="+text);
	}

	function ChangeLastMoment()
	{
	var path1=BX("path").value;
	var date=BX("date_e").value;
	var clastmoment=createHttpRequest();
	load.style.display="block";
	clastmoment.open("POST", "<?=$script_name?>", true);
	clastmoment.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
	clastmoment.onreadystatechange = function()
	{
				if (clastmoment.readyState == 4)
				{
					alert('Теперь дата последнего обмена: '+clastmoment.responseText);
					load.style.display="none";
				}
			};
	clastmoment.send("path1="+path1+"&date="+date+"&change=Y&AJAX=Y");
}

function Delete(file,workarea,full)
{
	var del=createHttpRequest();
	menu.style.display="none";
	if (confirm('Удалить '+file+'?'))
	{
	load.style.display="block";
	del.open("POST", "<?=$script_name?>", true);
	del.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
	del.onreadystatechange = function()
	{
				if (del.readyState == 4)
				{
					if (del.responseText!='success')
						alert("Ошибка удаления файла");
					GetFileList2(globalpath,workarea);
					load.style.display="none";
				}
			};
	q="action=deletefile&filename="+file;
	if (full)
		q=q+"&fullpath="+full;
	del.send(q);
	}
}

function DeleteLog(file,workarea,full)
{
	var del=createHttpRequest();
	menu.style.display="none";
	if (confirm('Удалить '+file+'?'))
	{
	load.style.display="block";
	del.open("POST", "<?=$script_name?>", true);
	del.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
	del.onreadystatechange = function()
	{
				if (del.readyState == 4)
				{
					if (del.responseText!='success')
						alert("Ошибка удаления файла");
					GetFileList2(globalpath,workarea);
				}
			};
	q="action=deletefile&filename="+file;
	if (full)
		q=q+"&fullpath="+full;
	del.send(q);
	}
}

function UnZip(file,workarea)
{
JustHide();
var unzipfile=createHttpRequest();
if (confirm('Распаковать '+file+'?'))
{
menu.style.display="none";
load.style.display="block";
unzipfile.open("POST", "<?=$script_name?>", true);
unzipfile.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
unzipfile.onreadystatechange = function()
{
			if (unzipfile.readyState == 4)
			{
			if (unzipfile.responseText!='1') alert("Ошибка распаковки файла");
			GetFileList2(globalpath,workarea);
			}
		};

unzipfile.send("action=unzip&filename=<?=$_SERVER['DOCUMENT_ROOT']?>"+globalpath+file);
}
}

function ShowHideSection(elem)
		{
			var t='block';
			if(BX(elem).style.display=='block')
			{t='none';}
			BX(elem).style.display=t;
		}

//показываем файлы импорта
function ShowFile(file,path,is_utf,workid)
{
JustHide();
if (!workid)
{
var elem = BX("tab0_field");
} else {var elem = BX(workid);}
var tb=BX("tbl");

if (file=="import")	{file=BX("cat_file").value;}
if (file=="offers") {file=BX("off_file").value;}

var callback= function(ajaxreq)
{
	if (ajaxreq.readyState == 4)
	{
	if (ajaxreq.responseText=='')
		{
		elem.innerHTML='Файл отсутстует или он пустой.';
		tb.style.display="block";
		}
		else
		{
		elem.innerHTML=ajaxreq.responseText;
		tb.style.display="block"
		}
		bxtabs.AlertActiveTab('tab0')

	}
};

AjaxGet("<?=$script_name?>?mode=show_xml&file="+file+"&path="+path+"&isutf="+is_utf+"&target=blank",callback);
}

//сохраняем xml заказов
function SaveMe(path)
{
	var load= BX("load");
	var elem = BX("tab2_field");
	var tb=BX("tbl");
	var callback= function(ajaxreq)
			{
				if (ajaxreq.readyState == 4 && ajaxreq.status == 200)
				{
					if (ajaxreq.responseText=='')
						elem.innerHTML='Ошибка формирования XML';
					else
					{
						elem.innerHTML=ajaxreq.responseText;
						bxtabs.AlertActiveTab('tab2');
						number.innerHTML=" ";
						tb.style.display="block"
					}
				}
			};
	AjaxGet("<?=$script_name?>?mode=query&path="+path+'&save=Y',callback);
	load.style.display="none";
}

//получаем список заказов
function GetOrders()
{

var elema = BX("PAYED");
    elemb = BX("DELIVERY");
	elemc = BX("path");
	elem = BX("list");
var r;
r='<?=$script_name?>?path='+elemc.value+'&check=Y';

if (elema.checked==true) r=r+'&PAYED=Y';
if (elemb.checked==true) r=r+'&ALLOW_DELIVERY=Y';
elem.style.display="block";
elem.innerHTML='Загрузка...<img align="center" src="http://gifanimation.ru/images/ludi/17_3.gif" width="30"/>';
elem.innerHTML='<iframe style="border:none;position:relative;margin-top:5px;font-size:11px;padding:3px;background: white;" width="98%" src="'+r+'"></iframe>';
}

//xml в отдельном окне
function _BlankXML(path)
		{
		//alert(path);
		window.open(path,'new','width=700,height=500,toolbar=1 scrollbars=yes');
		}

// закрыть список заказов
function CloseOrderList()
{
 BX("list").style.display="none";
}

// неважно
function Hide(event)
{
var element;
if (!event)
{
	event = window.event;
	element=event.srcElement;
} else {element=event.target}

//document.write(result);
//alert(event);
idlink=element.id.substr(0,2);
if((idlink!="f_")&&(element.id!='e')&&((element.id!='v'))&&((element.id!='d'))&&((element.id!='u'))&&((element.id!='dw'))&&((element.id!='eutf'))&&((element.id!='vu')))
{
menu.style.display="none";

}

if (element.parentNode.id!="menu_2")
	menu2.style.display="none";

}

function JustHide()
{
menu.style.display="none";
menu2.style.display="none";
}


////////////////////////////////////////////////////////
////////////////////////////////////////////////////////
////////////////////////Mini fileman////////////////////
////////////////////////////////////////////////////////
////////////////////////////////////////////////////////

function CreateFile(name,pathe,workarea)
{
	var pathf=BX(pathe);
	var name=BX(name).value;
	var filelist=createHttpRequest();
	var isdir=BX('isdir').checked;
	globalpath=pathf.value;
	q="<?=$script_name?>?action=createfile&path="+pathf.value+name;
	if (workarea)
	{
		q=q+"&workarea="+workarea;
		fileman=BX(workarea);
	}
	if (isdir==true)
	q=q+"&isdir=Y";
	filelist.open("GET", q, true);
	filelist.onreadystatechange = function()
	{
            if (filelist.readyState == 4 && filelist.status == 200)
            {
                if (filelist.responseText=='error001')
                        alert('Файл/папка уже существует, задайте другое имя!');
                if (filelist.responseText=='fail')
                        alert('Файл/папку создать не удалось:(');
                if (filelist.responseText=='success')
                        GetFileList(pathe,workarea);
                fileman.style.display='block';
                load.style.display="none";
             }

	};
	filelist.send(null);
}


function GetFileList(pathe,workarea)
{
	var fileman=BX("minifileman")
	var pathf=BX(pathe);
	var search_str=BX('search_str');
	globalpath=pathf.value;
	if (workarea)
		fileman=BX(workarea);
	q="<?=$script_name?>?action=getfiles&path="+pathf.value+"&like_str="+search_str.value;
	if (workarea)
	q=q+"&workarea="+workarea;

	var callback = function(ajaxreq)
	{
		if (ajaxreq.readyState == 4 && ajaxreq.status == 200)
		{
		    fileman.innerHTML=ajaxreq.responseText;
			fileman.style.display='block';
		}

	};
	AjaxGet(q,callback);
}



function GetFileList2(pathe,workarea)
{
	var fileman=BX("minifileman");
	var search_str=BX('search_str');
	var pathf=BX("path_fileman").value;
	if (pathe=="")
	pathe=BX("path_fileman").value;
	globalpath=pathe;
	if (workarea)
	fileman=BX(workarea);
	q="<?=$script_name?>?action=getfiles&path="+pathe+"&like_str="+search_str.value;
	if (workarea)
	q=q+"&workarea="+workarea;
	var callback = function(ajaxreq)
		{
			if (ajaxreq.readyState == 4 && ajaxreq.status == 200)
			{
				BX("path_fileman").value=pathe;
				fileman.innerHTML=ajaxreq.responseText;
			}

		};
	AjaxGet(q,callback);
}

function ShowFileMan(event)
{
	if(event.altKey && event.keyCode == 83)
	{
		if (window.document.getSelection) {
		text = document.getSelection();
} else if (document.selection && document.selection.createRange) {
    text = document.selection.createRange().text;
}
	if (text!="")
	{
		  BX('q').value=text;
		  searchbyxmlid();
	}
}
	var dis=BX('test_window').style.display;
    if(event.shiftKey && event.keyCode == 192)
	{
      if (dis=='none'||dis=='')
	  {
			BX('test_window').style.display='block';GetFileList2('','testfileman');
	  }
	  else
	  {
			BX('test_window').style.display='none';
      }
    }
}

function CheckSkipMode(obj)
{
	if (obj.checked == true)
	{
		BX('xml_id_stop').style.display='block';
		BX('impself').checked=true;
		BX('impself').disabled=true;
		SetSessionParam('skipmode',1);

	}
	else
	{
		BX('xml_id_stop').style.display='none';
		BX('impself').disabled=false;
		SetSessionParam('skipmode',0);
	}
}

function SetSessionParam(name,value)
{
	var q="<?=$script_name?>?name="+name+"&value="+value+"&action=setsession";
	var callback = function(ajaxreq)
		{
			//nothing

		};
	AjaxGet(q,callback);
}
function InitTabZone(obj)
{
	for (var childItem in obj.childNodes)
	{
		if (obj.childNodes[childItem].tagName=='DIV')
			alert(object.childNodes[childItem].id);
	}
}

function TabZone(tab_zone_id,field_zone_id,prefix,classtab,classfield, tabclick)
{
	if (!classtab)
		var classtab='tab';
	if (!classfield)
		var classfield=false;
	this.tab_inc=0;
	this.prefix=prefix;
	this.active_tab=this.prefix+this.tab_inc;
	var parent_obj=this;
	this.AlertActiveTab=function(tabid)
	{
		if (this.active_tab!=tabid)
			BX(tabid).style.background='#f5dce1';
	};
	if(!tabclick)
		tabclick = false;
	this.TabCreate=function(tab_name,active)
	{

		var tab=document.createElement("div");
		var tab_field=document.createElement("div");
		if (!active)
		var active=false;
		tab.className=classtab;
		tab.id=parent_obj.prefix+parent_obj.tab_inc;
		parent_obj.tab_inc++;

		tab.style.float='left';
		tab.innerHTML=tab_name;

		if(tabclick == false)
		{

			tab.onclick=function()
			{
				var tab_id=tab.id;
				var active_tab=parent_obj.active_tab;
				if (parent_obj.active_tab!=false)
				{
					BX(parent_obj.active_tab+'_field').style.display="none";
					BX(parent_obj.active_tab).style.background="#B0C4DE";
					BX(parent_obj.active_tab).style.opacity="0.2";

				}
				parent_obj.active_tab=this.id;
				BX(parent_obj.active_tab).style.background="#d3e1fa";
				//BX(parent_obj.active_tab).style.borderRadius="2px";
								//BX(parent_obj.active_tab).style.fontSize="15px";

					BX(parent_obj.active_tab).style.opacity="1";
				if (BX(parent_obj.active_tab+'_field'))
					BX(parent_obj.active_tab+'_field').style.display="block";
			}
		}
		else
			tab.onclick=function()
			{
				var tab_id=tab.id;
				var active_tab=parent_obj.active_tab;
				if (parent_obj.active_tab!=false)
				{
					BX(parent_obj.active_tab+'_field').style.display="none";
					BX(parent_obj.active_tab).style.background="#B0C4DE";
					BX(parent_obj.active_tab).style.opacity="0.5";
					BX(parent_obj.active_tab).style.borderRadius="0px";
					BX(parent_obj.active_tab).style.color="black";

				}
				parent_obj.active_tab=this.id;
				BX(parent_obj.active_tab).style.background="#778899";
				BX(parent_obj.active_tab).style.borderRadius="5px";
				BX(parent_obj.active_tab).style.color="white";

								//BX(parent_obj.active_tab).style.fontSize="15px";

					BX(parent_obj.active_tab).style.opacity="1";
				if (BX(parent_obj.active_tab+'_field'))
					BX(parent_obj.active_tab+'_field').style.display="block";
			}

		tab_field.id=tab.id+'_field';
		if (classfield==false)
		{
			tab_field.style.width='73%';
			tab_field.style.fontSize='12px';
			tab_field.style.height='86%';
			tab_field.style.position='absolute';
			tab_field.style.margin='0px 2px';
			tab_field.style.padding='6px';

			tab_field.style.overflow='none';
			tab_field.style.background="#d3e1fa";
		tab_field.style.clear="both";
		}
		else
			tab_field.className=classfield;

		if (active==false)
		{

				tab.style.background="#B0C4DE";
				tab_field.style.display="none";

		}
		else
		{
			if(!tabclick)
			{
				tab_field.style.display="block";
				tab.style.opacity="1";
			}
			else
			{
				tab_field.style.display="block";
				tab.style.opacity="1";
				tab.style.background="#778899";
				tab.style.borderRadius="5px";
				tab.style.color="white";
			}
		}
		BX(field_zone_id).appendChild(tab_field);
		BX(tab_zone_id).appendChild(tab);

	};


}

var bxtabs=new TabZone('tab_zone','field_zone','tab');
bxtabs.TabCreate('Файлы',true);
bxtabs.TabCreate('Импорт файлов 1C');
bxtabs.TabCreate('Заказы');
bxtabs.TabCreate('WebDav');
//bxtabs.TabCreate('Импорт на удалённом сервере');
//BX('tab1_field').appendChild(BX('ipfs'));
BX('tab3_field').appendChild(BX('webdav'));
var wd_tabs=new TabZone('dav_tab_zone','dav_field_zone','tab_dav','tab_dav','tab_field_dav');
wd_tabs.TabCreate('Обзор',true);
wd_tabs.TabCreate('XML');
wd_tabs.TabCreate('Заголовки');
wd_tabs.TabCreate('Тестирование');


		var log2=document.createElement('DIV');
		log2.id='small_tabs';
		log2.style.fontSize='15px';
		log2.style.padding='3px';


		log2.style.height='20px';
		BX("tab1_field").appendChild(log2);

		var log2=document.createElement('DIV');
		log2.id='small_tabs_fields';
		log2.style.fontSize='15px';
		BX("tab1_field").appendChild(log2);


//tbs
var smtabs=new TabZone('small_tabs','small_tabs_fields','tab_small','tab_s','field',
	function()
	{
		var tab_id=tab.id;
				var active_tab=parent_obj.active_tab;
				if (parent_obj.active_tab!=false)
				{
					BX(parent_obj.active_tab+'_field').style.display="none";
					BX(parent_obj.active_tab).style.background="#B0C4DE";
					BX(parent_obj.active_tab).style.opacity="0.2";

				}
				parent_obj.active_tab=this.id;
				BX(parent_obj.active_tab).style.background="#d3e1fa";
				//BX(parent_obj.active_tab).style.borderRadius="2px";
								//BX(parent_obj.active_tab).style.fontSize="15px";

					BX(parent_obj.active_tab).style.opacity="1";
				if (BX(parent_obj.active_tab+'_field'))
					BX(parent_obj.active_tab+'_field').style.display="block";
	}
);

smtabs.TabCreate('Локальный импорт',true);
smtabs.TabCreate('Удаленный импорт',false);


if (!BX('log2'))
	{
		var log2=document.createElement('DIV');
		log2.style.float ='left';
		log2.id='log2';
		log2.style.padding='3px';
		log2.style.width ='68%';
		log2.style.background='white';
		log2.style.height='90%';
		log2.style.overflowY='scroll';
		BX("tab_small0_field").appendChild(log2);
	}
BX('tab_small0_field').appendChild(BX('ipfs'));
BX('tab_small1_field').appendChild(BX('ext_import'));


function fixEvent(e) {
	e = e || window.event
	if ( e.pageX == null && e.clientX != null ) {
		var html = document.documentElement
		var body = document.body
		e.pageX = e.clientX + (html && html.scrollLeft || body && body.scrollLeft || 0) - (html.clientLeft || 0)
		e.pageY = e.clientY + (html && html.scrollTop || body && body.scrollTop || 0) - (html.clientTop || 0)
	}
	if (!e.which && e.button) {
		e.which = e.button & 1 ? 1 : ( e.button & 2 ? 3 : ( e.button & 4 ? 2 : 0 ) )
	}

	return e
}

var dragMaster = (function() {

	var dragObject
	var mouseOffset

	function getMouseOffset(target, e) {
		var docPos	= getPosition(target)
		return {x:e.pageX - docPos.x, y:e.pageY - docPos.y}
	}

	function mouseUp(){
		dragObject.style.cursor='default';
		dragObject = null
		document.onmousemove = null
		document.onmouseup = null
		document.ondragstart = null
		document.body.onselectstart = null
	}

	function mouseMove(e){
		e = fixEvent(e)

		with(dragObject.style) {
			position = 'fixed'
			cursor='move';
			top = e.pageY - mouseOffset.y + 'px'
			left = e.pageX - mouseOffset.x + 'px'
		}
		return false
	}

	function mouseDown(e) {
		e = fixEvent(e)

		if (e.which!=1 || (("#" + dragObjects.join("#,#") + "#").search("#"+e.target.id+"#") == -1)) return

		dragObject  = this
dragObject.style.cursor='move';

		mouseOffset = getMouseOffset(this, e)
		document.onmousemove = mouseMove
		document.onmouseup = mouseUp
		document.ondragstart = function() { return false }
		document.body.onselectstart = function() { return false }

		return false
	}

	return {
		makeDraggable: function(element){
			element.onmousedown = mouseDown
		}
	}

}())

function getPosition(e){
	var left = 0
	var top  = 0

	while (e.offsetParent){
		left += e.offsetLeft
		top  += e.offsetTop
		e	 = e.offsetParent
	}

	left += e.offsetLeft
	top  += e.offsetTop

	return {x:left, y:top}
}


function InitMoveableObjects()
{
	BX.ready(function()
	{

		for(var i=0; i<dragObjects.length; i++) {
			dragMaster.makeDraggable(BX(dragObjects[i]))
		}
	});
}
InitMoveableObjects();
</script>
