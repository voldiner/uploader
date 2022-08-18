<?php
return
[                      // папки за якими ведеться моніторинг
    [
        'alias' => 'city',
        'copyToFail' => true,      // чи треба в разі невдачі копіювати файл у папку fail
        'name_folder' => 'f:\vopas\city\\',
        'ftp_login' => 'admin_city_upl',
        'ftp_password' => 'N97gDDmJuu',
        'ftp_hostname' => '194.247.12.241',
        'ftp_folder' => '/',                        // ftp folder + /
        'copy_folder' => false,                    // false ->не копіювати, або вказати шлях куди скопіювати файл +\
        'copy_folder_data_time' => false,
        'uri' => 'https://vopas.com.ua/module/upl_city.php',      // vol-vol
        'count_to_send' => 0,        // must be 0 !!!
        'files' => [],              // must be [] !!!
        'errors' => [                 // возможные коды ошибок возвращаемые скриптом синхронизации
            "No file",
            "Error open file",        // пример для upload
        ],
    ],
    [
        'alias' => 'ftp',
        'copyToFail' => true,
        'name_folder' => 'f:\vopas\ftp\\',
        'ftp_login' => 'admin_vladimirv',
        'ftp_password' => 'ipx8Qt6BUw',
        'ftp_hostname' => false,
        'ftp_folder' => '/',                    // ftp folder + /
        'copy_folder' => false,                    // false ->не копіювати, або вказати шлях куди скопіювати файл + \
        'copy_folder_data_time' => false,
        'uri' => 'https://www.vopas.com.ua/module/wDF0jMypH3.php',
        'include' => 'include/test.php',
        'count_to_send' => 0,
        'files' => [],
        'errors' => [                 // возможные коды ошибок возвращаемые скриптом синхронизации
            "Error open file",
            "No file",
            // пример для update
        ],
    ],

    [
        'alias' => 'mail',
        'copyToFail' => true,
        'name_folder' => 'f:\vopas\email\\',
        'ftp_login' => '',
        'ftp_password' => '',
        'ftp_hostname' => false,
        'ftp_folder' => '',         // ftp folder + /
        'copy_folder' => 'f:\vopas\archives\email\\',                     // false ->не копіювати, або вказати шлях куди скопіювати файл
        'copy_folder_data_time' => true,
        'uri' => 'http://uploader:81/sendMail.php',
        'include' => 'include/stopSale.php',
        'count_to_send' => 0,
        'files' => [],
        'errors' => [                 // возможные коды ошибок возвращаемые скриптом синхронизации
            "Error open file",
            "No file",
            // пример для update
        ],
    ],

    [
        'alias' => 'zriv',
        'copyToFail' => true,
        'name_folder' => 'f:\vopas\zriv\\',
        'ftp_login' => '',
        'ftp_password' => '',
        'ftp_hostname' => false,
        'ftp_folder' => '',         // ftp folder + /
        'copy_folder' => 'f:\vopas\archives\zriv\\',                     // false ->не копіювати, або вказати шлях куди скопіювати файл
        'copy_folder_data_time' => true,
        'uri' => 'https://vopas.com.ua/fastCanceled.php',
        'include' => 'include/zriv.php',
        'count_to_send' => 0,
        'files' => [],
        'errors' => [                 // возможные коды ошибок возвращаемые скриптом синхронизации
            "Error open file",
            "No file",
            // пример для update
        ],
    ],

];
