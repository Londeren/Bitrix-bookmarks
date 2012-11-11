<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

$arComponentDescription = array(
	"NAME" => GetMessage("MAIN_SORTING_NAME"),
	"DESCRIPTION" => GetMessage("MAIN_SORTING_DESC"),
	"ICON" => "/images/sorting.gif",
	"PATH" => array(
    "ID" => "ergeslab",
    "NAME" => "ErgesLab",
		"CHILD" => array(
			"ID" => "navigation",
			"NAME" => GetMessage("MAIN_NAVIGATION_SERVICE")
		)
	),
);

?>