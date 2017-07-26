<?php

namespace App\Console\Commands;

class UploadFilesConfig
{

    private $domainList = array();
    private static $istance=null;

    /**
     * @return UploadFilesConfig|null
     */
    public static function getIstance(){
        if (!is_null(self::$istance)){
            return self::$istance;
        }
        self::$istance=new UploadFilesConfig();
        return self::$istance;
    }



    public function getDomainList() {
        $configured_domains=config('upload_files.sites');
        //dd($configured_domains);
        foreach ($configured_domains as $domain=>$domain_params){
            $this->domainList[]=$domain_params;

        }
        return $this->domainList;
    }




}
