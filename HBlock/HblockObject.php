<?php
namespace HBlock;

use Bitrix\Highloadblock\HighloadBlockTable;
use Bitrix\Main\Entity\Query;

/**
 * Class HblockObject
 *
 * Абстрактный объект Highload инфоблока
 *
 * Можно отнаследоваться от этого класса, чтобы удобно работать с сущностями из нужного HL блока, например:
 * @example
 * Class Operations extends HblockObject
 * {
 *   protected $hblockId = 2; // id HL блока
 *
 *    // methods
 * }
 *
 * @method \Bitrix\Main\DB\Result getList(array $parameters = array()) @see \Bitrix\Main\Entity\DataManager::getList()
 * @method \Bitrix\Main\DB\Result getById($id) @see \Bitrix\Main\Entity\DataManager::getById()
 * @method \Bitrix\Main\DB\Result getRow(array $parameters = array()) @see \Bitrix\Main\Entity\DataManager::getRow()
 * @method \Bitrix\Main\Entity\AddResult add(array $data) @see \Bitrix\Main\Entity\DataManager::add()
 * @method \Bitrix\Main\Entity\DeleteResult delete($primary) @see \Bitrix\Main\Entity\DataManager::delete()
 * @method \Bitrix\Main\Entity\UpdateResult update($primary, array $data) @see \Bitrix\Main\Entity\DataManager::update()
 */
abstract class HblockObject implements \ArrayAccess
{
    /**
     * @var int id объекта
     */
    protected $id = null;
    /**
     * @var array параметры объекта
     */
    protected $params;

    /**
     * @var int ID hl блока
     */
    protected $hblockId;

    /**
     * @var \Bitrix\Main\Entity\Base
     */
    protected $hblockEntity;

    /**
     * @var \Bitrix\Main\Entity\Base[]
     */
    protected static $hblockEntities = array();

    /**
     * @var array
     */
    protected static $hblockEntityFields = array();

    public function __construct()
    {
        if (is_null($this->hblockId))
            throw new \Exception('hblockId required');

        $this->hblockEntity = self::getEntity($this->hblockId);
    }

    /**
     * @param $hblockId
     * @return \Bitrix\Main\Entity\Base
     * @throws \Exception
     */
    protected static function getEntity($hblockId)
    {
        $hblockId = intval($hblockId);

        if (self::$hblockEntities[$hblockId])
            return self::$hblockEntities[$hblockId];

        $hlData = HighloadBlockTable::getById($hblockId)->fetch();
        if (!$hlData)
            throw new \Exception("Hblock {$hblockId} not found");

        // описание полей hl блока
        $obUserField = new \CUserTypeManager;
        self::$hblockEntityFields[$hblockId] = $obUserField->GetUserFields('HLBLOCK_' . $hlData['ID'], 0, LANGUAGE_ID);

        return self::$hblockEntities[$hblockId] = HighloadBlockTable::compileEntity($hlData);
    }


    public function __get($var)
    {
        if (strpos($var, 'printable_') === 0)
        {
            $varName = substr($var, strlen('printable_'));
            $param = $this->getParam($varName);

            $entityField = self::$hblockEntityFields[$this->hblockId][$varName];

            // для простых типов вернуть их значение
            if (in_array($entityField['USER_TYPE_ID'], array('string')))
                return $param;

            switch ($entityField['USER_TYPE_ID'])
            {
                case 'iblock_section':
                    $printable = \CIBlockSection::GetList(Array(), array('ID' => $param), false, array("NAME"))->GetNext();
                    return $printable['NAME'];
                case 'iblock_element':
                    $printable = \CIBlockElement::GetList(Array(), array('ID' => $param), false, array("NAME"))->GetNext();
                    return $printable['NAME'];
                default:
                    return $param;
            }
        } else
        {
            switch ($var)
            {
                case 'id':
                case 'ID':
                    return $this->id;
                case 'HBLOCK_ID':
                case 'hblockId':
                    return $this->hblockId;
                default:
                    return $this->getParam($var);
            }
        }
    }

    /**
     * Вызов методов \Bitrix\Main\Entity\DataManager
     * @see \Bitrix\Main\Entity\DataManager
     * @param $method
     * @param $arguments
     * @return mixed
     * @throws Exception
     */
    public function __call($method, $arguments)
    {
        $allowedMethods = array(
            'getList',
            'add',
            'update',
            'getRow',
            'getById',
            'delete'
        );

        $allowedMethods = array_map('ToLower', $allowedMethods);

        if (!in_array(ToLower($method), $allowedMethods))
            throw new \Exception("Unsupported method {$method}");

        /** @var $className \Bitrix\Main\Entity\DataManager */
        $className = $this->hblockEntity->getDataClass();

        return call_user_func_array("{$className}::{$method}", $arguments);
    }

    public function getArray()
    {
        return $this->getParam();
    }

    public function getParam($param = '')
    {
        if (is_null($this->params))
        {
            $this->params = array();

            $query = new Query($this->hblockEntity);
            $query->setSelect(array('*'));
            $query->setFilter(array('=ID' => $this->id));

            $result = $query->exec();
            $result = new \CDBResult($result);
            $this->params = $result->Fetch();
        }

        if ($param && array_key_exists($param, $this->params))
            return $this->params[$param];
        elseif (!$param)
            return $this->params;
        throw new \Exception("Param {$param} not found");
    }

    /**
     * скинуть параметры
     * например, если обновили данные через bitrix api и надо обновить параметры
     * @return self
     */
    public function flushParams()
    {
        $this->params = null;

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
        } catch (\Exception $e)
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


    public function exists()
    {
        try
        {
            return intval($this->id) === intval($this->getParam('ID'));
        }
        catch (\Exception $err)
        {
            return false;
        }
    }

    /**
     * @param $id
     * @param array $params
     * @return static
     */
    public function fromParams($id, $params = array())
    {
        $id = intval($id);

        if ($id < 1 && !isset($params['ID']))
            return $this;

        if ($id > 0)
            $this->id = $id;
        else
            $this->id = intval($params['ID']);

        if (!empty($params))
        {
            $this->params = $params;
        }

        return $this;
    }

    /**
     * @param $id
     * @return static
     */
    public static function fromId($id)
    {
        $instance = new static();

        /** @var $instance self */
        return $instance->fromParams($id);
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
     * @return \Bitrix\Main\Entity\Base
     */
    public function getHblockEntity()
    {
        return $this->hblockEntity;
    }

    /**
     * @return mixed
     */
    public function getHblockEntityFields()
    {
        return self::$hblockEntityFields[$this->hblockId];
    }


}
