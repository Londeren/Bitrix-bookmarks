<?php
namespace IBlock;


/**
 * Class IblockObject
 * @package Cetera
 *
 * Абстрактный объект-элемент инфоблока
 *
 * @example
 * class Product extends IblockObject
 * {
 *   protected $iblockId = 3; // ID инфоблока
 * }
 *
 * $product = Product::fromId(25);
 * echo $product->PROPERTY_ARTICLE['VALUE'];
 * echo $product->printable_PROPERTY_ARTICLE;
 * echo $product->NAME;
 *
 */
abstract class IblockObject implements \ArrayAccess
{
  /**
   * @var int id объекта
   */
  protected $id = 0;

  /**
   * @var array параметры объекта
   */
  protected $params = array();

  /**
   * @var int ID инфоблока объекта
   */
  protected $iblockId;


  public function __construct()
  {

  }

  /**
   * Геттер
   * @param $var
   * @return int
   */
  public function __get($var)
  {
    if(strpos($var, 'printable_') === 0)
    {
      $varName = substr($var, strlen('printable_'));
      $param = $this->getParam($varName);

      if(!is_array($param) || !isset($param['PROPERTY_TYPE']))
        return $param;

      switch($param['PROPERTY_TYPE'])
      {
        case 'G':
          $printable = \CIBlockSection::GetList(Array(), array('ID' => $param['VALUE']), false, array("NAME"))->GetNext();
          return $printable['NAME'];
        default:
          $printable = \CIBlockFormatProperties::GetDisplayValue(array('NAME' => ''), $param, "");
          return $printable['DISPLAY_VALUE'];
      }
    }
    else
    {
      switch($var)
      {
        case 'id':
        case 'ID':
          return $this->id;
        case 'IBLOCK_ID':
        case 'iblockId':
          return $this->iblockId;
        default:
          return $this->getParam($var);
      }
    }
  }

  public function getIblockId()
  {
    return $this->iblockId;
  }

  public function setId($id)
  {
    $this->id = $id;

    return $this;
  }

  public function getId()
  {
    return $this->id;
  }

  /**
   * @param string $param
   * @throws \Exception
   * @return mixed
   */
  public function getParam($param = '')
  {
    if(empty($this->params))
    {
      $objectParams = \CIBlockElement::GetList(
        array("SORT" => "ASC"),
        array(
          "ID" => $this->id,
          "IBLOCK_ID" => $this->iblockId,
          "CHECK_PERMISSIONS" => "N",
        ),
        false,
        false,
        Array(
          "ID",
          "IBLOCK_ID",
          "IBLOCK_SECTION_ID",
          "NAME",
          "ACTIVE",
          "XML_ID",
          "CODE",
          "ACTIVE_FROM",
          "PREVIEW_TEXT",
          "DETAIL_TEXT",
          "DETAIL_TEXT_TYPE",
          "LIST_PAGE_URL",
          "TIMESTAMP_X",
          "DETAIL_PAGE_URL",
          "IBLOCK_NAME",
          "PROPERTY_*",
        )
      )->GetNextElement();
      if($objectParams)
      {
        $this->params = $objectParams->GetFields();
        $props = $objectParams->GetProperties();

        foreach($props as $propCode => $prop)
          $this->params["PROPERTY_{$propCode}"] = $prop;

      }
    }

    if($param && array_key_exists($param, $this->params))
      return $this->params[$param];
    elseif(!$param)
      return $this->params;
    else
      throw new \Exception("Param {$param} not found");
  }

  /**
   * скинуть параметры
   * например, если обновили данные через bitrix api и надо обновить параметры
   * @return self
   */
  public function flushParams()
  {
    $this->params = array();
    return $this;
  }

  /**
   * (PHP 5 &gt;= 5.0.0)<br/>
   * Whether a offset exists
   * @link http://php.net/manual/en/arrayaccess.offsetexists.php
   * @param mixed $offset <p>
   * An offset to check for.
   * </p>
   * @return boolean true on success or false on failure.
   * </p>
   * <p>
   * The return value will be casted to boolean if non-boolean was returned.
   */
  public function offsetExists($offset)
  {
    try
    {
      $this->getParam($offset);
    }
    catch(\Exception $e)
    {
      return false;
    }

    return true;
  }

  /**
   * (PHP 5 &gt;= 5.0.0)<br/>
   * Offset to retrieve
   * @link http://php.net/manual/en/arrayaccess.offsetget.php
   * @param mixed $offset <p>
   * The offset to retrieve.
   * </p>
   * @return mixed Can return all value types.
   */
  public function offsetGet($offset)
  {
    return $this->getParam($offset);
  }

  /**
   * (PHP 5 &gt;= 5.0.0)<br/>
   * Offset to set
   * @link http://php.net/manual/en/arrayaccess.offsetset.php
   * @param mixed $offset <p>
   * The offset to assign the value to.
   * </p>
   * @param mixed $value <p>
   * The value to set.
   * </p>
   * @return void
   */
  public function offsetSet($offset, $value)
  {
    $this->params[$offset] = $value;
  }

  /**
   * (PHP 5 &gt;= 5.0.0)<br/>
   * Offset to unset
   * @link http://php.net/manual/en/arrayaccess.offsetunset.php
   * @param mixed $offset <p>
   * The offset to unset.
   * </p>
   * @return void
   */
  public function offsetUnset($offset)
  {
    unset($this->params[$offset]);
  }

  /**
   * @param $id
   * @param array $params
   * @return static
   */
  public function fromParams($id, $params = array())
  {
    $id = intval($id);

    if($id < 1 && !isset($params['ID']))
      return $this;

    if($id > 0)
      $this->id = $id;
    else
      $this->id = intval($params['ID']);

    if(!empty($params))
    {
      if(!empty($params['PROPERTIES']))
      {
        foreach($params['PROPERTIES'] as $propCode => $prop)
          $params["PROPERTY_{$propCode}"] = $prop;

        unset($params['PROPERTIES']);
      }

      $this->params = $params;
    }

    return $this;
  }

  /**
   * Существует ли такой элемент?
   * При создании из массива
   * @return bool
   */
  public function exists()
  {
    try
    {
      return intval($this->id) === intval($this->getParam('ID'));
    }
    catch(\Exception $err)
    {
      return false;
    }
  }

  public static function fromId($id)
  {
    $class = get_called_class();
    /** @var $instance self */
    $instance = new $class();
    return $instance->fromParams($id);
  }



}
