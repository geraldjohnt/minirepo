<?php

namespace App\Console\Commands;
use Illuminate\Console\Command;
use Mockery\Exception;
use Log;
use Storage;
use Artisan;

class FileEncrypt extends Command
{

  protected $storage;
  protected $signature = 'encrypt-files';

  protected $description = ' Make files encrypted saved with .dat extension';

  public function __construct()
  {
    $this->storage = Storage::disk('public');
    parent::__construct();
  }

  public function handle()
  {
    try{

      $storageFiles = [];
      $allowedExtension = implode(',',["ppt","pptx", "xlsx", "pdf", "doc", "xls", "docx", "txt"]);
      $toEncryptFilePaths = [
        \Config::get('proto.uploads.staff_docs'),
        \Config::get('proto.uploads.staff_notes'),
        \Config::get('proto.uploads.client_docs'),
      ];

      foreach($toEncryptFilePaths as $toEncryptFilePath) {
        $path = storage_path(sprintf('app/public/%s', $toEncryptFilePath . '/'));
        $getFiles = glob($path."*.{{$allowedExtension}}", GLOB_BRACE);
        $storageFiles[$toEncryptFilePath] = array_unique($getFiles);
      }

      foreach($storageFiles as $storagePath => $files)
      {
        foreach($files as $file)
        {
          $ext = pathinfo($file,PATHINFO_EXTENSION);

          $fullPath = storage_path(sprintf('app/public/%s/', $storagePath));

          $filename = str_replace($fullPath,"",$file);

          $encryptedFileContent = encrypt($this->storage->get($storagePath.'/'.$filename));
          $filename = str_replace($ext,'dat',$filename);

          $this->storage->put($storagePath.'/'.$filename,$encryptedFileContent);

          unlink($file);
        }
      }

      print "All files are now ecnrypted. \r\n";
    }catch (Exception $exception){
      Artisan::call('sendToSlack:message', [
        'args' => array(
          'location' => 'Commands/FileEncrypt:handle',
          'messages' => $exception
        )
      ]);
    }


  }

}