<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

// алиасы для направлений сортировки
if(!is_array($arParams['ORDER_ALIASES']))
  $arParams['ORDER_ALIASES'] = array();
$arParams['ORDER_ALIASES'] = array_merge(array(
  'ASC' => 'A',
  'DESC' => 'D',
), $arParams['ORDER_ALIASES']);

$arParams['ORDER_ALIASES_KEYS'] = array_flip($arParams['ORDER_ALIASES']);
/*
 array(
   'поле сортировки' => array(
    'DEFAULT' => 'Y|N', // по умолчанию?
    'DEFAULT_ORDER' => 'ASC|DESC', // направление сортировки по умолчанию
    'CODE' => 'алиас для сортировки',
    'NAME' => 'Название пункта'
   )
 )
*/
if(empty($arParams['SORTING']))
  $arParams['SORTING'] = array(
    'NAME' => array(
      'DEFAULT' => 'Y',
      'DEFAULT_ORDER' => 'ASC',
      'CODE' => 'name',
      'NAME' => GetMessage("SORTING_NAME_NAME"),
    ),
    'SORT' => array(
      'DEFAULT' => 'Y',
      'DEFAULT_ORDER' => 'DESC',
      'CODE' => 'sort',
      'NAME' => GetMessage("SORTING_NAME_SORT"),
    ),
  );

/**
 * компонента возвращает не только параметры сортировки, но и сгенерированный шаблон
 */
$arParams['GET_LAYOUT'] = $arParams['GET_LAYOUT'] == 'Y';

// название GET переменной, в которой передаются параметры сортировки SORT[NAME]=D
if(strlen($arParams["SORTING_VARIABLE"]) <= 0 || !preg_match("/^[A-Za-z_][A-Za-z01-9_]*$/", $arParams["SORTING_VARIABLE"]))
  $arParams["SORTING_VARIABLE"] = "SORT";

$arResult['SORTING'] = $_GET[$arParams["SORTING_VARIABLE"]];
if(empty($arResult['SORTING']))
  $arResult['SORTING'] = array();

$codes = array();
foreach($arParams['SORTING'] as $sortName => $arSort)
{
  // если названия нет, то найти в lang файле
  if(!$arSort['NAME'])
    $arParams['SORTING'][$sortName]['NAME'] = GetMessage("SORTING_NAME_{$sortName}");
  // привести направление сортировки
  if(ToLower($arSort['DEFAULT_ORDER']) != 'asc')
    $arParams['SORTING'][$sortName]['DEFAULT_ORDER'] = 'DESC';
  // если CODE нет, то взять из ключа
  if(!$arSort['CODE'])
    $arSort['CODE'] = $arParams['SORTING'][$sortName]['CODE'] = $sortName;

  // если код уже есть, то изменить название
  if(in_array($arSort['CODE'], $codes))
    $arSort['CODE'] = $arParams['SORTING'][$sortName]['CODE'] = $arSort['CODE'] . substr(md5($arSort['CODE']), 0, 2);

  array_push($codes, $arSort['CODE']);
}

$return = array();
/**
 * сортировки, которые возвращает компонент
 */
$arResult['SORT'] = array();
/**
 * сортировки, по которым отрисовывается шаблон
 */
$arResult['SORT_LAYOUT'] = array();

/**
 * не сортировали
 */
if(empty($arResult['SORTING']))
{
  foreach($arParams['SORTING'] as $sortName => $arSort)
  {
    if($arSort['DEFAULT'] == 'Y')
      $arResult['SORT'][] = array(
        'BY' => $sortName,
        'ORDER' => $arSort['DEFAULT_ORDER'],
      );

    $arResult['SORT_LAYOUT'][$sortName] = array(
      'NAME' => $arSort['NAME'],
      'ACTIVE_ORDER' => ($arSort['DEFAULT'] ? $arSort['DEFAULT_ORDER'] : ''),
      'ASC_ORDER_LINK' => htmlspecialchars($APPLICATION->GetCurPageParam("{$arParams["SORTING_VARIABLE"]}[{$arSort['CODE']}]={$arParams['ORDER_ALIASES']['ASC']}", array($arParams["SORTING_VARIABLE"]))),
      'DESC_ORDER_LINK' => htmlspecialchars($APPLICATION->GetCurPageParam("{$arParams["SORTING_VARIABLE"]}[{$arSort['CODE']}]={$arParams['ORDER_ALIASES']['DESC']}", array($arParams["SORTING_VARIABLE"]))),
    );
  }
}
else
{

  foreach($arParams['SORTING'] as $sortName => $arSort)
  {
    $thisSort = isset($arResult['SORTING'][$arSort['CODE']]);
    if($thisSort)
      $arResult['SORT'][] = array(
        'BY' => $sortName,
        'ORDER' => $arParams['ORDER_ALIASES_KEYS'][$arResult['SORTING'][$arSort['CODE']]],
      );

    $arResult['SORT_LAYOUT'][$sortName] = array(
      'NAME' => $arSort['NAME'],
      'ACTIVE_ORDER' => ($thisSort ? $arParams['ORDER_ALIASES_KEYS'][$arResult['SORTING'][$arSort['CODE']]] : ''),
      'ASC_ORDER_LINK' => htmlspecialchars($APPLICATION->GetCurPageParam("{$arParams["SORTING_VARIABLE"]}[{$arSort['CODE']}]={$arParams['ORDER_ALIASES']['ASC']}", array($arParams["SORTING_VARIABLE"]))),
      'DESC_ORDER_LINK' => htmlspecialchars($APPLICATION->GetCurPageParam("{$arParams["SORTING_VARIABLE"]}[{$arSort['CODE']}]={$arParams['ORDER_ALIASES']['DESC']}", array($arParams["SORTING_VARIABLE"]))),
    );
  }
}

$return['SORT'] = $arResult['SORT'];

if($arParams['GET_LAYOUT'])
{
  ob_start();
  $this->IncludeComponentTemplate();
  $return['LAYOUT'] = ob_get_contents();
  ob_end_clean();
}
else
  $this->IncludeComponentTemplate();

return $return;