<?
/**
 * Логическое
 * Пользовательское свойство инфоблока
 * Особенность работы битрикса - если значение данного свойства установлено в false, то в списке свойств элемента инфоблока вместо false будет '' (пустая строка).
 * @link http://dev.1c-bitrix.ru/community/webdev/user/16182/blog/6135/
 * @link http://dev.1c-bitrix.ru/learning/course/index.php?COURSE_ID=43&LESSON_ID=2832
 * @link http://asvavilov.blogspot.ru/2012/08/1.html
 */
class CUserTypeBool extends CUserTypeInteger
{

  // инициализация пользовательского свойства для главного модуля
  function GetUserTypeDescription()
  {
    return array(
      "USER_TYPE_ID" => "bool",
      "CLASS_NAME" => "CUserTypeBool",
      "DESCRIPTION" => "Логическое",
      "BASE_TYPE" => "int",
    );
  }

  // инициализация пользовательского свойства для инфоблока
  function GetIBlockPropertyDescription()
  {
    return array(
      "PROPERTY_TYPE" => "B",
      "USER_TYPE" => "bool",
      "DESCRIPTION" => "Логическое",
      'GetPropertyFieldHtml' => array('CUserTypeBool', 'GetPropertyFieldHtml'),
      'GetAdminListViewHTML' => array('CUserTypeBool', 'GetAdminListViewHTML'),
      "ConvertFromDB" => array("CUserTypeBool", "ConvertFromDB"),
    );
  }

  // представление свойства
  function getViewHTML($name, $value)
  {
    $checked = '';
    if(!!$value)
      $checked = ' checked';

    return "<input type=\"checkbox\" {$checked} disabled/>";
  }

  // редактирование свойства
  function getEditHTML($name, $value, $is_ajax = false)
  {
    $checked = '';
    if(!!$value)
      $checked = ' checked';

    return <<<HTML
      <input type="hidden" value="0" style="display: none;" name="{$name}">
      <input type="checkbox" {$checked} value="1" name="{$name}"/>
HTML;
  }

  // редактирование свойства в форме (главный модуль)
  function GetEditFormHTML($arUserField, $arHtmlControl)
  {
    return self::getEditHTML($arHtmlControl['NAME'], $arHtmlControl['VALUE'], false);
  }

  // редактирование свойства в списке (главный модуль)
  function GetAdminListEditHTML($arUserField, $arHtmlControl)
  {
    return self::getViewHTML($arHtmlControl['NAME'], $arHtmlControl['VALUE'], true);
  }

  // представление свойства в списке (главный модуль, инфоблок)
  function GetAdminListViewHTML($arProperty, $value, $strHTMLControlName)
  {
    return self::getViewHTML($strHTMLControlName['VALUE'], $value['VALUE']);
  }

  // редактирование свойства в форме и списке (инфоблок)
  function GetPropertyFieldHtml($arProperty, $value, $strHTMLControlName)
  {
    return $strHTMLControlName['MODE'] == 'FORM_FILL'
      ? self::getEditHTML($strHTMLControlName['VALUE'], $value['VALUE'], false)
      : self::getViewHTML($strHTMLControlName['VALUE'], $value['VALUE']);
  }

  function ConvertFromDB($arProperty, $arValue)
  {
    return array(
      'VALUE' => !!$arValue['VALUE'],
      'DESCRIPTION' => $arValue['DESCRIPTION'],
    );
  }

  function ConvertToDB($arProperty, $arValue)
  {
    return array(
      'VALUE' => intval($arValue['VALUE']),
      'DESCRIPTION' => $arValue['DESCRIPTION'],
    );
  }

}

// добавляем тип для инфоблока
AddEventHandler("iblock", "OnIBlockPropertyBuildList", array("CUserTypeBool", "GetIBlockPropertyDescription"));
/*// добавляем тип для главного модуля
AddEventHandler("main", "OnUserTypeBuildList", array("CUserTypeBool", "GetUserTypeDescription"));*/
