<?
global $DB, $MESS, $APPLICATION;
//require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/filter_tools.php");

IncludeModuleLangFile(__FILE__);
//IncludeModuleLangFile($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/form/errors.php");

$DBType = strtolower($DB->type);
