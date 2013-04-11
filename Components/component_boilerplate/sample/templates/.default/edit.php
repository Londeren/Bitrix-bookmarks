<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();


$ElementID = $APPLICATION->IncludeComponent(
  "site:sample.edit",
  "",
  array(
    'PARAM' => 'VALUE',
  ),
  $component,
  array("HIDE_ICONS"=>"Y")
);
