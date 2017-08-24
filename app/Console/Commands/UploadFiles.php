<?php

namespace App\Console\Commands;

use App\Actions;
use App\Prenotazioni;
use App\Prestazioni;
use App\Providers;
use App\Tariffe;
use App\Transactions;
use App\User;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Console\Command;
use Symfony\Component\Process\Process;

class UploadFiles extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'upload:files';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = "Reads modified files and uploads them to the server via ftp";

    protected $output_body = [];
    protected $ok = true;
    protected $forced_date = false;

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $this->hardWork($this->argument(), $this->option());
    }

    /**
     * @param $argument
     * @param $option
     */
    private function hardWork($argument, $option)
    {
        try {
            //Log::info($prenotazioni);
            echo PHP_EOL . " domini disponibili:" . PHP_EOL . PHP_EOL;
            $domainList = UploadFilesConfig::getIstance()->getDomainList();
            //dd($domainList);
            $domainIndex = 0;
            foreach ($domainList as $domain) {
                echo " " . $domainIndex . " - " . $domain['DOMAIN_NAME'] . PHP_EOL;
                $domainIndex++;
            }
            $domainIndex--;
            $prjNumber = $this->ask("digita il numero del progetto da mettere online (0-$domainIndex)");
            if ($prjNumber < 0 || $prjNumber > $domainIndex) {
                echo PHP_EOL . " il progetto scelto non esiste" . PHP_EOL . PHP_EOL;
                exit;
            }
            $commitNumber = $this->ask("quanti commit indietro vuoi andare?");
            if ($commitNumber <= 0) {
                echo PHP_EOL . " numero di commit non valido, deve essere maggiore di 0" . PHP_EOL . PHP_EOL;
                exit;
            }

            $domainName = $domainList[$prjNumber]['DOMAIN_NAME'];
            $gitLocalPath = $domainList[$prjNumber]['GIT_LOCAL_PATH'];
            $ftpServerIp = $domainList[$prjNumber]['FTP_SERVER_IP'];
            $ftpUser = $domainList[$prjNumber]['FTP_USR'];
            $ftpPwd = $domainList[$prjNumber]['FTP_PWD'];
            $ftpMod = $domainList[$prjNumber]['FTP_MOD_PASSIVE'];
            $ftpHtdocs = $domainList[$prjNumber]['FTP_HTDOCS'];
            $curlUrl = $domainList[$prjNumber]['CURL_URL'];
            $curlUsr = $domainList[$prjNumber]['CURL_USR'];
            $curlPwd = $domainList[$prjNumber]['CURL_PWD'];

            //echo "$prjNumber gitLocalPath ".("'".$gitLocalPath."''");

            //d* esclude i files cancellati nella dir corrente e in tutte le sotto dir
            $process = new Process("git diff --diff-filter=d* --name-only HEAD~$commitNumber HEAD");
            $process->enableOutput();
            $process->setWorkingDirectory($gitLocalPath);
            $process->run();
            $output = $process->getOutput();
            //echo $output;
            Storage::disk('local')->put('deploy_files_list.txt', $output);

            echo "Questi sono i files che andranno online: " . PHP_EOL;
            echo $output . PHP_EOL . PHP_EOL;

            if ($this->confirm('Vuoi procedere?')) {
                $connId = ftp_connect($ftpServerIp);
                $loginResult = ftp_login($connId, $ftpUser, $ftpPwd);

                ftp_pasv($connId, $ftpMod);

                if ((!$connId) or (!$loginResult)) {
                    echo "Connessione fallita a " . $domainName . "!";
                } else {
                    echo "Connesso a " . $domainName . " con l'utente " . $ftpUser . PHP_EOL . PHP_EOL;

                    ftp_chdir($connId, $ftpHtdocs);

                    $content = file(storage_path() . '\app\deploy_files_list.txt');

                    $destinationFile = "";

                    foreach ($content as $fileName) {
                        //echo "filename: '".trim($fileName) ."'". PHP_EOL;
                        //rimuovo i caratteri di 'a capo'
                        $fileName = preg_replace('/[\r\n]+/', '', $fileName);
                        echo "file da uploadare: " . $fileName . PHP_EOL;
                        $sourceFile = $gitLocalPath . $fileName;
                        $sourceFile = str_replace("\\", "/", $sourceFile);

                        $fileNameArray = explode("/", $fileName);
                        $counter = 0;
                        foreach ($fileNameArray as $dirName) {
                            if (strpos($dirName, '.php') == false) {
                                //$ftp_files = ftp_nlist($connId, ".");
                                /*
                                echo "ftp_files "  . PHP_EOL;
                                var_dump($ftp_files);
                                echo "dirName ".$dirName . PHP_EOL;
                                echo "in_array(dirName, ftp_files): ".in_array($dirName, $ftp_files).PHP_EOL;
                                */
                                //if (!in_array($dirName, $ftp_files,true)) {
                                //$ftp_mkdir = ftp_mkdir($connId, $dirName);

                                //if ($ftp_mkdir === false) {
                                //echo ('Unable to create directory: ' . $dirName).PHP_EOL;
                                //}
                                //}
                                //echo PHP_EOL  . PHP_EOL. PHP_EOL;
                                try {
                                    ftp_mkdir($connId, $dirName);
                                    Log::info("creo la dir: " . $dirName);
                                } catch (Exception $e) {

                                }
                                ftp_chdir($connId, $dirName);
                                $counter++;
                            } else
                                $destinationFile = $dirName;


                        }

                        if (!empty($destinationFile)) {
                            //echo PHP_EOL ."soucer: " . $sourceFile.  PHP_EOL;
                            //echo PHP_EOL ."dest: " . $destinationFile.  PHP_EOL;
                            Log::info("upload del file: " . $destinationFile);
                            $upload = ftp_put($connId, $destinationFile, $sourceFile, FTP_BINARY);

                            $msg = "Operazione riuscita :-)";
                            if (!$upload) {
                                $msg = "Non riuscito :-(";
                                echo $msg . PHP_EOL;
                            } else {
                                echo $msg . PHP_EOL;
                            }
                            Log::info($msg);
                            //echo "count: ".$counter .  PHP_EOL;
                            for ($i = 0; $i < $counter; $i++) {
                                //echo "i: ".$i .  PHP_EOL;
                                try {
                                    ftp_cdup($connId);

                                } catch (Exception $e) {
                                    //echo "current dir temp: ".ftp_pwd ( $connId ).  PHP_EOL;
                                }

                            }
                            //echo "current dir: ".ftp_pwd ( $connId ).  PHP_EOL.  PHP_EOL.  PHP_EOL;
                        }

                    }

                    ftp_close($connId);
                }


            }

            /********* controllo se ci fossero da fare delle migrations */
            $content = file(storage_path() . '\app\deploy_files_list.txt');

            foreach ($content as $fileName) {
                if (strpos($fileName, 'migrations') !== false) {
                    //$msg =  PHP_EOL."trovato file di migration: " . $fileName;
                    //Log::info($msg);
                    if ($this->confirm("trovato 1 o piu' files di migration, vuoi eseguirlo/i?")) {
                        $this->doMigration($fileName,$curlUrl, $curlUsr, $curlPwd);
                    }
                    break;
                }

            }


            //$output = shell_exec("git diff --name-only HEAD~2 HEAD");
            //$output = exec("cd C:\xampp\htdocs\nencinisport.it; git diff --name-only HEAD~2 HEAD");
            //echo "<pre>" . $output . "</pre>";
        } catch (Exception $e) {
            echo "errore: " . $e->getMessage();
        }
    }

    private function doMigration($fileName,$curlUrl, $curlUsr, $curlPwd)
    {
        try {
            $msg = "url ws migration: $curlUrl ";
            echo $msg.PHP_EOL;

            $ch = curl_init();

            curl_setopt($ch, CURLOPT_URL, $curlUrl);
            curl_setopt($ch, CURLOPT_USERPWD, $curlUsr . ":" . $curlPwd);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

            $result = curl_exec($ch);//es. di result: '+Ok 100000000 bool(true)'

            curl_close($ch);

            //$result = strip_tags($result);
            $msg = "Risultato curl al ws migration: ";
            echo $msg.PHP_EOL;
            var_dump($result);
            //echo $msg.PHP_EOL;
            if ($result == 1) {
                $msg = "Migration ok ";
            } else {
                $msg = "Migration fails ";
            }
            echo $msg.PHP_EOL;
            Log::info($msg);
        } catch (Exception $e) {
            echo "errore curl: ".$e->getMessage();
        }

    }

}
