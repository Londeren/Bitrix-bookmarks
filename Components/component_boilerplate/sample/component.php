<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

$arDefaultUrlTemplates404 = array(
  "list" => "",
  "edit" => "#ELEMENT_ID#/",
);

$arComponentVariables = array(
  "ELEMENT_ID",
);

$arDefaultVariableAliases404 = array();

$arDefaultVariableAliases = array();


if($arParams["SEF_MODE"] == "Y")
{
  $arVariables = array();

  $arUrlTemplates = CComponentEngine::MakeComponentUrlTemplates($arDefaultUrlTemplates404, $arParams["SEF_URL_TEMPLATES"]);
  $arVariableAliases = CComponentEngine::MakeComponentVariableAliases($arDefaultVariableAliases404, $arParams["VARIABLE_ALIASES"]);

  $componentPage = CComponentEngine::ParseComponentPath(
    $arParams["SEF_FOLDER"],
    $arUrlTemplates,
    $arVariables
  );

  $b404 = false;
  if(!$componentPage)
  {
    $componentPage = "list";
    $b404 = true;
  }

  if($b404 && $arParams["SET_STATUS_404"] === "Y")
  {
    $folder404 = str_replace("\\", "/", $arParams["SEF_FOLDER"]);
    if($folder404 != "/")
      $folder404 = "/" . trim($folder404, "/ \t\n\r\0\x0B") . "/";
    if(substr($folder404, -1) == "/")
      $folder404 .= "index.php";

    if($folder404 != $APPLICATION->GetCurPage(true))
      CHTTP::SetStatus("404 Not Found");
  }

  CComponentEngine::InitComponentVariables($componentPage, $arComponentVariables, $arVariableAliases, $arVariables);

  $arResult = array(
    "FOLDER" => $arParams["SEF_FOLDER"],
    "URL_TEMPLATES" => $arUrlTemplates,
    "VARIABLES" => $arVariables,
    "ALIASES" => $arVariableAliases,
  );
}
else
{
  $arVariableAliases = CComponentEngine::MakeComponentVariableAliases($arDefaultVariableAliases, $arParams["VARIABLE_ALIASES"]);
  CComponentEngine::InitComponentVariables(false, $arComponentVariables, $arVariableAliases, $arVariables);

  $componentPage = "";

  if(isset($arVariables["ELEMENT_ID"]) && intval($arVariables["ELEMENT_ID"]) > 0)
    $componentPage = "edit";
  else
    $componentPage = "list";

  $arResult = array(
    "FOLDER" => "",
    "URL_TEMPLATES" => Array(
      "list" => htmlspecialcharsbx($APPLICATION->GetCurPage()),
      "edit" => htmlspecialcharsbx($APPLICATION->GetCurPage() . "?" . $arVariableAliases["ELEMENT_ID"] . "=#ELEMENT_ID#"),
    ),
    "VARIABLES" => $arVariables,
    "ALIASES" => $arVariableAliases
  );
}

$this->IncludeComponentTemplate($componentPage);
