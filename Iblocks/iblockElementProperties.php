<?php
/**
 * Коллекция функций для работы со свойствами элементов инфоблоков различных типов
 */

/**
 * Тип свойства - список
 * По коду и значению свойства получить id этого значения
 * В случае отсутствия, значение будет создано и будет возвращен его id
 *
 * <code>
 * $propertyCountryValue = getPropertyEnumValueId($IBLOCK_ID, 'COUNTRIES', 'Япония');
 * $be = new CIBlockElement;
 * $be->Add(array(
 *    "ACTIVE" => 'Y',
 *    "IBLOCK_ID" => $IBLOCK_ID,
 *    "NAME" => $name,
 *    "PROPERTY_VALUES" => array(
 *       'COUNTRIES' => $propertyCountryValue,
 *    )
 *  );
 * </code>
 * @param $IBLOCK_ID int id инфоблока, в котором находится данное свойство
 * @param $prop string код свойства
 * @param $value string значение свойства
 * @return bool|int ID значения свойства или false, если не удалось найти свойство с таким кодом или не удалось создать запись с данным значением
 */
function getPropertyEnumValueId($IBLOCK_ID, $prop, $value)
{
  $property = CIBlockProperty::GetByID($prop, $IBLOCK_ID)->Fetch();

  if(!$property)
    return false;

  $ar_enum_list = CIBlockProperty::GetPropertyEnum($prop, array("SORT" => "asc"), Array("IBLOCK_ID" => $IBLOCK_ID, 'VALUE' => $value))->Fetch();

  if(!$ar_enum_list)
  {
    $ibpenum = new CIBlockPropertyEnum;
    if($PropID = $ibpenum->Add(Array('PROPERTY_ID' => $property['ID'], 'VALUE' => $value)))
      $ar_enum_list['ID'] = $PropID;
    else
      return false;
  }

  return $ar_enum_list['ID'];
}


