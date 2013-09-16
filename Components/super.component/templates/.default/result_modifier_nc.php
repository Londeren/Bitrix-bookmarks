<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
/** @var CBitrixComponent $this */
/** @var array $arParams */
/** @var array $arResult */
/** @global CUser $USER */
global $USER;
/** @global CMain $APPLICATION */
global $APPLICATION;



// result modifier nocache
/*
$APPLICATION->SetTitle($arResult["NAME"]);


if($GLOBALS["APPLICATION"]->GetShowIncludeAreas())
{
	if (CModule::IncludeModule("iblock"))
	{
		$this->AddIncludeAreaIcons(
			CIBlock::ShowPanel($arResult["IBLOCK_ID"], 
			$arResult["ID"], 
			$arResult["IBLOCK_SECTION_ID"], 
			$arParams["IBLOCK_TYPE"], true)
		);
	}
}
	
return $arResult["ID"];
*/