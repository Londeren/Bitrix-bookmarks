Модули
============

  1. user_vars - пользовательские переменые. Позволяет задать произвольные переменные для сайта, и использовать их в дальнейшем в коде.
     [Скачать модуль в Битрикс.Маркетплейс](http://marketplace.1c-bitrix.ru/solutions/ceteralabs.uservars/)

    **Пример использования:**

    ![user_vars - пользовательские переменые](https://raw2.github.com/Londeren/Bitrix/master/modules/user_vars/user_vars.png "user_vars - пользовательские переменые")

    ```php
    <?php
      CModule::IncludeModule('user_vars'); // подключить модуль, например в init.php

      // ....

      $defaultCity = UserVars::GetVar('LANDING_PAGE_URL'); // получить значение пользовательской переменной, с названием LANDING_PAGE_URL


    ```