

# Про Uploader


Uploader - сервіс, створений для моніторингу визначених папок і при появі в них файлів завантаження цих файлів на FTP та виклик
скриптів для їх обробки.

Uploader створений на PHP, може працювати на версії від 5.4 і вище.  

Принцип роботи сервіса полягає в запуску безкінечного циклу і в межах цього циклу виконання всіх необхідних функцій. Сканування папок
 для моніторингу відбувається через вказаний в параметрах період часу

Скрипт можна викликати через браузер або з командної строки. При виклику через браузер оптціонально скрипт виводить інформацію про його роботу.

Так як скрипт може інколи аварійно закінчувати свою роботу, то передбачено механізм відновлення його роботи через cron, в якому задається
задача повторного запуску даного скрипта. Щоб уникнути запуску декількох копій скрипта, при свому запуску скрипт створює файл, в який при кожній
ітерації циклу записує мітку - timestamp. При запуску скрипта відбувається перевірка наявності файлу та зчитування мітки. Якщо мітка не 
поновлювалася певний визначений час, то вважається, що скрипт не працює і можна запускати його знову. 

Для зупинки скрипта необхідно видалити файл stop.txt

З метою контролю за роботою скрипта ведеться докладний лог його роботи.

Uploader може використовуватись для взаємодії між веб-сервісами та локальними програмами, які з різних причин не можуть обмінюватись 
інформацією напряму.

## Дії, що виконує скрипт при виявленні файлів у визначеній для моніторингу папці.

1. ### Постановка файлів в чергу на завантаження. 
    При виявленні файлів їх завантаження на FTP відбувається не одразу, а через визначену в налаштуваннях паузу. З цією метою файли
    ставляться в чергу на завантаження.
        
2. ### Відправка файлів на FTP.
    - відбувається спроба передати файли на FTP, необхідні для передачі параметри беруться з файлу налаштувань
    - якщо спроба невдала, то при активації в файлі налаштувань відповідного параметру файли зберігаються в локальній папці failed,
      при цому при необхідності до імені файлу додається префікс.
    - інформація про помилку відправляється в [Megalog](https://github.com/voldiner/megalog) з використанням АРІ Megalog. 
    - в разі помилки запуск скрипта не проводиться.
    
3. ### Запуск скрипта.

    В разі успішної передачі файлів відбувається:
    - якщо існує параметер 'include' то виконується код, який містить відповідний файл
    - якщо параметер 'include' не заданий, то відбувається виклик скрипта вказаного в параметрі 'uri'
    - якщо відомий перелік помилок, що вертає скрипт вказаний в параметрі 'uri', то можлива їх обробка.
    - помилки запуску чи повідомлення про успішний запуск скрипта [Megalog](https://github.com/voldiner/megalog) з використанням АРІ Megalog.

4. ### Копіювання файлу в папку (створення архіву файлів).
    Якщо параметр "copy_folder" не рівний false, натомість вказаний шлях до папки, то після обробки файли будуть скопійовані
    у вазану папку, таким чином в подальшому можна буде передивитися ці файли. Можливо для цих файлів створювати префікс у вигляді
    дати - часу створення файлу. За це відповідає параметр 'copy_folder_data_time' => true.

5. ### Видалення успішно опрацьованих файлів.

6. ### Константи необхідні для роботи скрипта, описані в файлі index.php.

7. ### Опис конфігураційного файлу.
    Файл знаходиться config/config.php
   Це масив, кожен елемент якого описує парметри для моніторинга конкретної папки.
   [
           'alias' => 'mail',                       // для megaloga
           'copyToFail' => true,                   // якщо true то в разі помилки копійувати в папку fail
           'name_folder' => 'f:\vopas\email\\',   // папка для моніторингу
           'ftp_login' => '',
           'ftp_password' => '',
           'ftp_hostname' => false,   // якщо false, то файл по FTP не передавати, або вказати назву хоста FTP
           'ftp_folder' => '',                    // ftp folder + /
           'copy_folder' => false,                    // false ->не копіювати, або вказати шлях куди скопіювати файл після завершення обробки + /
           'copy_folder_data_time' => false,
           'uri' => 'http://uploader/sendMail.php', // якщо цей параметер заданий, то замість виклику скрипта буде виконаний код
                                                    // з цього файлу
           'include' => 'include/stopSale.php',   // скрипт, який необхідно викликати після завантаження файлу на FTP
           'count_to_send' => 0,          // технічний параметер для роботи скрипта  
           'files' => [],                 // пустий масив для роботи скрипта
           'errors' => [                 // можливі відомі коды помилок що вертає скрипт
               "Error open file",
               "No file",
          
           ],
       ]  
       
8. ### Підключення власного обробника помилок.
    Його задача записати помилку в лог і передати управління внутрішньому обробнику помилок PHP/
9. ### Підключення власного обробника помилок.    
    Він записує інформацію про причину закінчення роботи скрипта в лог та видаляє файл id.txt.
10.  ### Додатковий модуль.
    В проекті присутній файл sendMail.php. Це зразок  файлу який можна підключити згідно п.3 і виконати даний код замість виклику
    іншого скрипта. Тут реалізована можливість читання і парсингу JSON строки з файлу, створення XML документу на основі отриманих
    даних та відправка e-mail повідомлення з цим документом (з викристанням бібліотеки libmail.php). 
