<?php
/**
 * Created by PhpStorm.
 * User: LOGISTICA_1
 * Date: 25/07/2017
 * Time: 17:02
 */

return [
  'sites'=>[
      'nencinisport'=>[
          'DOMAIN_NAME'=>env('NENCINISPORT_DOMAINNAME','localhost'),
          'FTP_SERVER_IP'=>env('NENCINISPORT_FTP_SERVER_IP','95.110.192.207'),
          'GIT_LOCAL_PATH'=>env('NENCINISPORT_GIT_LOCAL_PATH','C:/xampp/htdocs/nencinisport.it/'),
          'FTP_USR'=>env('NENCINISPORT_FTP_USR', 'andrea3'),
          'FTP_PWD'=>env('NENCINISPORT_FTP_PWD', 'tequila77'),
          'FTP_MOD_PASSIVE'=>env('NENCINISPORT_FTP_MOD_PASSIVE', 'true'),
          'FTP_HTDOCS'=>env('NENCINISPORT_FTP_HTDOCS', 'Backup/test2'),
          'CURL_URL'=>env('NENCINISPORT_CURL_URL', 'localhost'),
          'CURL_USR'=>env('NENCINISPORT_CURL_USR', 'andrea'),
          'CURL_PWD'=>env('NENCINISPORT_CURL_PWD', 'my_pwd')
      ],
      'patavarnuzze'=>[
          'DOMAIN_NAME'=>env('PATAVARNUZZE_DOMAINNAME','localhost'),
          'FTP_SERVER_IP'=>env('PATAVARNUZZE_FTP_SERVER_IP','95.110.192.207'),
          'GIT_LOCAL_PATH'=>env('PATAVARNUZZE_GIT_LOCAL_PATH','C:/xampp/htdocs/patavarnuzze/'),
          'FTP_USR'=>env('PATAVARNUZZE_FTP_USR', 'andrea3'),
          'FTP_PWD'=>env('PATAVARNUZZE_FTP_PWD', 'tequila77'),
          'FTP_MOD_PASSIVE'=>env('PATAVARNUZZE_FTP_MOD_PASSIVE', 'true'),
          'FTP_HTDOCS'=>env('PATAVARNUZZE_FTP_HTDOCS', 'Backup/test2'),
          'CURL_URL'=>env('PATAVARNUZZE_CURL_URL', 'localhost/test_curl.php'),
          'CURL_USR'=>env('PATAVARNUZZE_CURL_USR', 'andrea'),
          'CURL_PWD'=>env('PATAVARNUZZE_CURL_PWD', 'my_pwd')
      ]
  ]
];
