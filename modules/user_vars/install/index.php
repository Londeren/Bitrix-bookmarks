<?
global $MESS;
$PathInstall = str_replace("\\", "/", __FILE__);
$PathInstall = substr($PathInstall, 0, strlen($PathInstall) - strlen("/index.php"));
IncludeModuleLangFile($PathInstall . "/install.php");

if(class_exists("user_vars")) return;

Class user_vars extends CModule
{
  var $MODULE_ID = "user_vars";
  var $MODULE_VERSION;
  var $MODULE_VERSION_DATE;
  var $MODULE_NAME;
  var $MODULE_DESCRIPTION;
  var $MODULE_GROUP_RIGHTS = "Y";

  function user_vars()
  {
    $this->MODULE_VERSION = '1.0.1';
    $this->MODULE_VERSION_DATE = '2014-01-07';
    $this->MODULE_NAME = GetMessage("UV_MODULE_NAME");
    $this->MODULE_DESCRIPTION = GetMessage("UV_MODULE_DESCRIPTION");

    $arModuleVersion = array();

    $path = str_replace("\\", "/", __FILE__);
    $path = substr($path, 0, strlen($path) - strlen("/index.php"));
    include($path."/version.php");

    if (is_array($arModuleVersion) && array_key_exists("VERSION", $arModuleVersion))
    {
      $this->MODULE_VERSION = $arModuleVersion["VERSION"];
      $this->MODULE_VERSION_DATE = $arModuleVersion["VERSION_DATE"];
    }
    else
    {
      $this->MODULE_VERSION = '-';
      $this->MODULE_VERSION_DATE = '-';
    }

    $this->MODULE_NAME = GetMessage("UV_MODULE_NAME");
    $this->MODULE_DESCRIPTION = GetMessage("UV_MODULE_DESCRIPTION");
  }

  function DoInstall()
  {
    global $DOCUMENT_ROOT, $APPLICATION;
    $this->InstallFiles();
    RegisterModule("user_vars");

    $APPLICATION->IncludeAdminFile(GetMessage('UV_INSTALL_TITLE'), $DOCUMENT_ROOT . "/bitrix/modules/user_vars/install/step.php");
  }


  function InstallFiles($arParams = array())
  {

    CopyDirFiles($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/user_vars/install/admin/', $_SERVER['DOCUMENT_ROOT'].'/bitrix/admin');
    CopyDirFiles($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/user_vars/install/images",  $_SERVER["DOCUMENT_ROOT"]."/bitrix/images/user_vars", true, true);
    CopyDirFiles($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/user_vars/install/themes/", $_SERVER["DOCUMENT_ROOT"]."/bitrix/themes", true, true);
    return true;
  }

  function UnInstallFiles()
  {
    DeleteDirFiles($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/support/install/admin/', $_SERVER['DOCUMENT_ROOT'].'/bitrix/admin');

    DeleteDirFiles($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/support/install/themes/.default/', $_SERVER['DOCUMENT_ROOT'].'/bitrix/themes/.default');//css
    DeleteDirFilesEx('/bitrix/themes/.default/icons/user_vars/');//icons
    DeleteDirFilesEx('/bitrix/images/user_vars/');//images
    return true;
  }

  function DoUninstall()
  {
    global $DOCUMENT_ROOT, $APPLICATION;
    $this->UnInstallDB();
    $this->UnInstallFiles();
    UnRegisterModule("user_vars");

    DeleteDirFiles($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/user_vars/install/admin", $_SERVER["DOCUMENT_ROOT"] . "/bitrix/admin");
    DeleteDirFiles($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/user_vars/install/themes/.default/", $_SERVER["DOCUMENT_ROOT"] . "/bitrix/themes/.default"); //css
    DeleteDirFilesEx("/bitrix/themes/.default/icons/user_vars/"); //icons
    DeleteDirFilesEx("/bitrix/images/user_vars/"); //images

    $APPLICATION->IncludeAdminFile(GetMessage('UV_UNINSTALL_TITLE'), $DOCUMENT_ROOT . "/bitrix/modules/user_vars/install/unstep.php");
  }

  function UnInstallDB()
  {
    global $DB, $DBType, $APPLICATION;
    $this->errors = false;

    $this->errors = $DB->RunSQLBatch($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/user_vars/install/db/" . $DBType . "/uninstall.sql");

    if($this->errors !== false)
    {
      $APPLICATION->ThrowException(implode("", $this->errors));
      return false;
    }

    return true;
  }
}

?>