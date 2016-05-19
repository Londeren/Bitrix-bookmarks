<?php

namespace HBlock;


/**
 * Class SimpleHblockObject
 *
 * @example
 * Объект любого HL блока
 *
 * $operations = new \HBlock\SimpleHblockObject(MANUFACTURERS_HB_ID);
 * $operations->getList(....)
 */
class SimpleHblockObject extends HblockObject
{
  /**
   * @param $hblockId int ID HL блока
   * @throws \Exception
   */
  public function __construct($hblockId)
  {
    $this->hblockId = $hblockId;

    parent::__construct();
  }

} 
