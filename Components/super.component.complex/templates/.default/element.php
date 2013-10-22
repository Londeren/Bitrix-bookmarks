<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

/** @var array $arParams */
/** @var array $arResult */
/** @global CUser $USER */
global $USER;
/** @global CMain $APPLICATION */
global $APPLICATION;

$APPLICATION->IncludeComponent(
	"vidicom:super.component",
	"element",
	Array(
		"IBLOCK_TYPE"	=>	$arParams["IBLOCK_TYPE"],
		"IBLOCK_ID"		=>	$arParams["IBLOCK_ID"],
		"CACHE_TYPE"	=>	$arParams["CACHE_TYPE"],
		"CACHE_TIME"	=>	$arParams["CACHE_TIME"],
 		"ELEMENT_ID"	=>  $arResult["VARIABLES"]["ELEMENT_ID"],
 		"ELEMENT_CODE"	=>  $arResult["VARIABLES"]["ELEMENT_CODE"],
	),
	$component
);