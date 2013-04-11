<?php
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");

/**
 * Пример подключения компоненты
 */
$APPLICATION->IncludeComponent(
  "site:sample",
  "",
  Array(
    "SET_STATUS_404" => "Y",
    "SEF_MODE" => "Y",
    "SEF_FOLDER" => '/articles/',
    "SEF_URL_TEMPLATES" => Array(
      "list" => "",
      "edit" => "#ELEMENT_ID#/",
    ),
  ),
  null,
  array("HIDE_ICONS"=>"Y")
);


require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");