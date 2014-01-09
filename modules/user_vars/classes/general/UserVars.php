<?php
/**
 * Класс для управления пользовательскими свойствами
 *
 *  Пример использования:
 * <?php
 * CModule::IncludeModule('user_vars'); // подключить модуль, например в init.php
 *
 * // ....
 *
 * $defaultCity = UserVars::GetVar('LANDING_PAGE_URL'); // получить значение пользовательской переменной, с названием LANDING_PAGE_URL
 *
 */
class UserVars extends CAllOption
{
  const MODULE_NAME = 'user_vars';

  public static function GetList()
  {
    global $DB;

    $optionList = array();

    $res = $DB->Query(
      "SELECT SITE_ID, NAME, VALUE, DESCRIPTION ".
        "FROM b_option ".
        "WHERE MODULE_ID='".$DB->ForSql(self::MODULE_NAME)."'"
    );

    while($ar = $res->Fetch())
      $optionList[] = $ar;

    return $optionList;
  }

  public static function ClearOptions()
  {
    parent::RemoveOption(self::MODULE_NAME);
  }

  public static function SetVar($name, $value="", $desc=false, $site="")
  {
    return parent::SetOptionString(self::MODULE_NAME, $name, $value, $desc, $site);
  }

  /**
   * @param $name string название переменной
   * @param string $def значение по умолчанию
   * @param bool $site сайт
   * @param bool $bExactSite
   * @return bool|string
   */
  public static function GetVar($name, $def="", $site=false, $bExactSite=false)
  {
    return parent::GetOptionString(self::MODULE_NAME, $name, $def, $site, $bExactSite);
  }
}
