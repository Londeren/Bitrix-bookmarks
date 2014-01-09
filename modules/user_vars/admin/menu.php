<?
IncludeModuleLangFile(__FILE__);
CModule::IncludeModule("iblock");


$aMenu[] = array(
		"parent_menu" => "global_menu_settings",
		"section" => "user_vars",
		"sort" => 1750,
		"url" => "user_vars.php",
		"text" => GetMessage('UV_TITLE'),
		"title" => GetMessage('UV_TITLE'),
		"icon" => "user_vars_menu_icon",
		"page_icon" => "user_vars_menu_icon",
		"module_id" => "user_vars",
	);

return $aMenu;