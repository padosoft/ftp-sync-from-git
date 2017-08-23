## docs del comando upload:files

Comando: php artisan upload:files

File di configurazione: .env

Questo comando permette di inviare su un server remoto una lista di files associati ad un commit. 

Per ogni progetto che vogliamo gestire bisogna specificare le seguenti variabili nel file .env

NENCINISPORT_DOMAINNAME=nencini
NENCINISPORT_FTP_SERVER_IP=95.110.192.207
NENCINISPORT_GIT_LOCAL_PATH=C:/xampp/htdocs/nencinisport.it/
NENCINISPORT_FTP_USR=andrea3
NENCINISPORT_FTP_PWD=tequila77
NENCINISPORT_FTP_MOD_PASSIVE=true
NENCINISPORT_FTP_HTDOCS=Backup/test2

DOMAINNAME è una stringa che identifica il nome del progetto
FTP_SERVER_IP è l'ip del server dove risiede il prj
GIT_LOCAL_PATH è la dir dove si trova il prj nel pc locale
FTP_HTDOCS è la dir di partenza, sul server remoto, da dove iniziare a copiare i files

Una volta eseguito il comando php artisan upload:files, viene visualizzate queste domande:
1) Scegliere il nome del progetto
2) Quanti commit vuoi gestire
3) Sei sicuro di voler procedere?
4) Per ogni file di migration trovato, viene chiesto se vogliamo eseguirlo

I files inviati vengono scritti in storage/app/deploy_files_list.txt