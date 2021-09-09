<?php
return
    [                      // папки за якими ведеться моніторинг
        [
            'alias' => 'city',
            'copyToFail' => true,      // чи треба в разі невдачі копіювати файл у папку fail
            'name_folder' => 'd:\vopas\city\\',
            'ftp_login' => '',
            'ftp_password' => '',
            'ftp_hostname' => '',
            'ftp_folder' => '/',                        // ftp folder + /
            'copy_folder' => false,                    // false ->не копіювати, або вказати шлях куди скопіювати файл +\
            'copy_folder_data_time' => false,
            'uri' => 'https://vopas.com.ua/module/upl_city.php',
            'count_to_send' => 0,        // must be 0 !!!
            'files' => [],              // must be [] !!!
            'errors' => [                 // возможные коды ошибок возвращаемые скриптом синхронизации
                "No file",
                "Error open file",        // пример для upload
            ],
        ],
        [
            'alias' => 'ftp',
            'copyToFail' => false,
            'name_folder' => 'd:\vopas\ftp\\',
            'ftp_login' => '',
            'ftp_password' => '',
            'ftp_hostname' => '',
            'ftp_folder' => '/',                    // ftp folder + /
            'copy_folder' => false,                    // false ->не копіювати, або вказати шлях куди скопіювати файл + \
            'copy_folder_data_time' => false,
            'uri' => 'https://www.vopas.com.ua/module/wDF0jMypH.php',
            'count_to_send' => 0,
            'files' => [],
            'errors' => [                 // возможные коды ошибок возвращаемые скриптом синхронизации
                "Error open file",
                "No file",
                // пример для update
            ],
        ],
        [
            'alias' => 'upload',
            'copyToFail' => true,
            'name_folder' => 'd:\vopas\upload\\',
            'ftp_login' => '',
            'ftp_password' => '',
            'ftp_hostname' => '',
            'ftp_folder' => '/',                    // ftp folder + /
            'copy_folder' => 'd:\vopas\archives\upload\\',                     // false ->не копіювати, або вказати шлях куди скопіювати файл
            'copy_folder_data_time' => true,
            'uri' => 'https://www.vopas.com.ua/module/yD4lsIqNd9.php',
            'count_to_send' => 0,
            'files' => [],
            'errors' => [                 // возможные коды ошибок возвращаемые скриптом синхронизации
                "Error open file",
                "No file",
                // пример для update
            ],
        ],
        [
            'alias' => 'free',
            'copyToFail' => true,
            'name_folder' => 'd:\vopas\free\\',
            'ftp_login' => '',
            'ftp_password' => '',
            'ftp_hostname' => '',
            'ftp_folder' => '/free/',                    // ftp folder + /
            'copy_folder' => false,                    // false ->не копіювати, або вказати шлях куди скопіювати файл
            'copy_folder_data_time' => false,
            'uri' => 'https://www.vopas.com.ua/module/upl_free.php?station=lutsk',
            'count_to_send' => 0,
            'files' => [],
            'errors' => [                 // возможные коды ошибок возвращаемые скриптом синхронизации
                "Error open file",
                "No file",
                // пример для update
            ],
        ],
        [
            'alias' => 'reg',
            'copyToFail' => true,
            'name_folder' => 'd:\vopas\reg\\',
            'ftp_login' => '',
            'ftp_password' => '',
            'ftp_hostname' => '',
            'ftp_folder' => '/regular/',                    // ftp folder + /
            'copy_folder' => false,                    // false ->не копіювати, або вказати шлях куди скопіювати файл
            'copy_folder_data_time' => false,
            'uri' => 'https://www.vopas.com.ua/module/upl_regular.php?station=lutsk',
            'count_to_send' => 0,
            'files' => [],
            'errors' => [                 // возможные коды ошибок возвращаемые скриптом синхронизации
                "Error open file",
                "No file",
                // пример для update
            ],
        ],

        [
            'alias' => 'update',
            'copyToFail' => true,
            'name_folder' => 'd:\vopas\update\\',
            'ftp_login' => '',
            'ftp_password' => '',
            'ftp_hostname' => '',
            'ftp_folder' => '/',                    // ftp folder + /
            'copy_folder' => 'd:\vopas\archives\update\\',                    // false ->не копіювати, або вказати шлях куди скопіювати файл
            'copy_folder_data_time' => false,
            'uri' => 'https://www.vopas.com.ua/module/o9Kho8Erg5.php',
            'count_to_send' => 0,
            'files' => [],
            'errors' => [                 // возможные коды ошибок возвращаемые скриптом синхронизации
                "Error open file",
                "No file",
                // пример для update
            ],
        ],
    ];
