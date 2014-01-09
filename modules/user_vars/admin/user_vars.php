<?
require_once($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_admin_before.php");
IncludeModuleLangFile(__FILE__);
ClearVars();

require($_SERVER["DOCUMENT_ROOT"] . BX_ROOT . "/modules/main/include/prolog_admin_after.php");
CModule::IncludeModule('user_vars');
global $DB, $APPLICATION;


$APPLICATION->SetTitle(GetMessage("PS_TITLE"));

function isVarNameValid($name)
{
  return 1 == preg_match("~^[a-z0-9_]+$~iu", $name);
}


/**
 * сохранение
 */
if(isset($_POST['save']))
{
  $varsToSave = array();

  /**
   * переменные
   */
  if(isset($_POST['variable']) && !empty($_POST['variable']))
    foreach($_POST['variable'] as $var)
    {
      if(isVarNameValid($var['name']) && !isset($var['del']))
      {
        $varsToSave[$var['name']] = array(
          'NAME' => $var['name'],
          'VALUE' => $var['value'],
          'DESCRIPTION' => $var['description'],
        );
      }
    }

  UserVars::ClearOptions(); // очистить все опции

  /**
   * сохранить
   */
  if(!empty($varsToSave))
  {
    foreach($varsToSave as $var)
      UserVars::SetVar($var['NAME'], $var['VALUE'], $var['DESCRIPTION']);
  }

  LocalRedirect("/bitrix/admin/user_vars.php");

}

$userVars = UserVars::GetList();


?>
<form name="form-cats" method="POST" action="" id="user_vars_form">
  <?=bitrix_sessid_post()?>
  <?
  $aTabs = array(
    array("DIV" => "ps_settings_filter", "TAB" => GetMessage("UV_TITLE"), "ICON" => "icon_16", "TITLE" => GetMessage("UV_TITLE")),
  );

  $tabControl = new CAdminTabControl("tabControl", $aTabs);
  $tabControl->Begin();

  // ====================== TAB 1 =======================
  ?>
  <?$tabControl->BeginNextTab();?>
  <tr>
    <td style="text-align:left; vertical-align:top;">

      <table cellspacing="0" cellpadding="0" border="0" align="center" id="ib_prop_list" class="internal">
        <tbody>
        <tr class="heading">
          <td><?=GetMessage("UV_TABLE_HEAD_NAME")?><sup><a href="#n1d">1</a></sup></td>
          <td><?=GetMessage("UV_TABLE_HEAD_VALUE")?></td>
          <td><?=GetMessage("UV_TABLE_HEAD_DESCRIPTION")?></td>
          <td><?=GetMessage("UV_TABLE_HEAD_DEL")?></td>
        </tr>
        <?
        $i = 0;
        foreach($userVars as $uVar){
          $i++;
          ?>
          <tr>
            <td>
              <input type="text" value="<?=htmlspecialcharsEx($uVar['NAME'])?>" name="variable[<?=$uVar['NAME']?>][name]" maxlength="100" size="50">
            </td>
            <td>
              <textarea name="variable[<?=$uVar['NAME']?>][value]" cols="50"><?=htmlspecialcharsEx($uVar['VALUE'])?></textarea>
            </td>
            <td>
              <textarea name="variable[<?=$uVar['NAME']?>][description]" cols="50"><?=htmlspecialcharsEx($uVar['DESCRIPTION'])?></textarea>
            </td>
            <td style="text-align: center;"><input type="checkbox" value="1" name="variable[<?=$uVar['NAME']?>][del]"></td>
          </tr>
        <?}
        $k = $i;
        for(; $i < $k  + 5; $i++){?>
        <tr>
          <td>
            <input type="text" name="variable[<?=$i?>][name]" maxlength="100" size="50">
          </td>
          <td>
            <textarea name="variable[<?=$i?>][value]" cols="50" style="height:22px;"></textarea>
          </td>
          <td>
            <textarea name="variable[<?=$i?>][description]" cols="50" style="height:22px;"></textarea>
          </td>
          <td style="text-align: center;">&nbsp;</td>
        </tr>
        <?}?>
        </tbody>
      </table>

      <?echo BeginNote('width="100%"');?>
      <div id="n1d"><sup>1</sup> <?=GetMessage("UV_TABLE_HEAD_NAME_NOTICE")?></div>
      <?echo EndNote(); ?>
    </td>
    <td style="text-align:left; vertical-align:top;">
      <div></div>
    </td>
  </tr>

  <?$tabControl->EndTab();?>

  <?
  $tabControl->Buttons();
  ?>
  <input type="submit" name="save" value="<?=GetMessage("UV_BUTTON_SAVE")?>">
  <?$tabControl->End();?>
</form>
<?


require($_SERVER["DOCUMENT_ROOT"] . BX_ROOT . "/modules/main/include/epilog_admin.php");
