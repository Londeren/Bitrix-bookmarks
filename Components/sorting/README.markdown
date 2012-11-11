Компонент для сортировки списка элементов инфоблока по [всем возможным](http://dev.1c-bitrix.ru/api_help/iblock/classes/ciblockelement/getlist.php) значениям сортировки.
============

Пример использования:

```php
<?php

$sorting = $APPLICATION->IncludeComponent(
    "ergeslab:sorting",
    ".default",
    Array(
      "GET_LAYOUT" => 'Y', // компонента возвращает не только параметры сортировки, но и сгенерированный шаблон
      "SORTING_VARIABLE" => 'SORT', // название GET переменной, в которой передаются параметры сортировки 
      /**
       * array(
       *  'поле сортировки' => array(
       *   'DEFAULT' => 'Y|N', // по умолчанию?
       *   'DEFAULT_ORDER' => 'ASC|DESC', // направление сортировки по умолчанию
       *   'CODE' => 'алиас для сортировки',
       *   'NAME' => 'Название пункта'
       *  )
       * )
       */
      "SORTING" => array( 
        'NAME' => array(
          'DEFAULT' => 'Y',
          'DEFAULT_ORDER' => 'ASC',
          'CODE' => 'N',
          'NAME' => 'Название',
        ),
        'PROPERTY_AUTHORS.NAME' => array(
          'CODE' => 'A',
          'NAME' => 'Автор',
        ),
        'PROPERTY_YEAR' => array(
          'CODE' => 'Y',
          'NAME' => 'Год',
        ),
      ),
      'ORDER_ALIASES' => array( // алиасы для направлений сортировки
        'ASC' => 'A', // defaults
        'DESC' => 'D', // defaults
      )
    )
  );

/**
 * $sorting = array(
 *  'SORT' => array(...) - массив с сортировками
 *  'LAYOUT' => '...' - сгенерированный шаблоном html код, если "GET_LAYOUT" => 'Y'
 * )
 */
```