<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();
if(empty($arResult['SORT_LAYOUT']))
  return;
?>
<div class="sorting"><?
  foreach($arResult['SORT_LAYOUT'] as $sortName => $arSort)
  {
    ?>
    <a href="<?=$arSort['DESC_ORDER_LINK']?>"<?if($arSort['ACTIVE_ORDER'] == 'DESC'){?> class="active"<?}?>>▼</a>
    <a href="<?=$arSort['ASC_ORDER_LINK']?>"<?if($arSort['ACTIVE_ORDER'] == 'ASC'){?> class="active"<?}?>>▲</a>
    <a href="<?=($arSort['ACTIVE_ORDER'] == 'ASC' ? $arSort['DESC_ORDER_LINK'] : $arSort['ASC_ORDER_LINK'])?>"<?if($arSort['ACTIVE_ORDER']){?> class="active"<?}?>><?=$arSort['NAME']?></a>
    <?
  }
?></div>