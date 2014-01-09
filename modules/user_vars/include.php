<?php
/**
* модуль user_vars Пользовательские переменные
*/

global $DB, $MESS, $APPLICATION;

IncludeModuleLangFile(__FILE__);

$DBType = strtolower($DB->type);

$arClassesList = array(
  "UserVars" => "classes/general/UserVars.php",
);

CModule::AddAutoloadClasses(
  "user_vars",
  $arClassesList
);