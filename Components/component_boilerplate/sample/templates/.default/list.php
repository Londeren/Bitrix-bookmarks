<? if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

$APPLICATION->IncludeComponent(
  "site:sample.list",
  '',
  Array(
    'PARAM' => 'VALUE',
  ),
  $component,
  array("HIDE_ICONS" => "Y")
);
