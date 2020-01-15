<?php
namespace App\Traits;
use Illuminate\Http\Request;
use ImalH\PDFLib\PDFLib;
use Artisan;
use CloudConvert;
use File;
use Log;
use App\StaffDocument;

trait Filetrait {
    /**
     * Upload User Avatar
     *
     * @param Request
     * @return path
     */
    
    public function uploadFile(Request $request, $field_key)
    {
        try {
            $file_key = $request->file_key;
            $a_file = [];

            if ($request->hasFile($file_key))
            {
                $file_prefix = $request->has('file_prefix') ? $request->input('file_prefix') : 'file';
                $file_path = $request->has('file_path') ? $request->input('file_path') : 'uploads/';
                $file = $request->file($file_key);
            $a_file['file_name'] = $file_prefix.time().'-'.rand(111,999).'.'.$file->getClientOriginalExtension();
            //removed original file name temporarily due to saving filename character bug
            // $a_file['file_name'] = $file_prefix.time().'-'.rand(111,999).$file->getClientOriginalName();

                $a_file['mime_type'] = $file->getClientMimeType();
                $a_file['size'] = $file->getClientSize();
                $a_file[$field_key] = $file_path.'/'.$a_file['file_name'];

                $this->storage->put(
                    $a_file[$field_key],
                File::get($file->getrealpath())
                );

                if($request->has('current_file_url')) {
                    $this->storage->readAndDelete($request->input('current_file_url'));
                }
            }
            return $a_file;
        } catch(Exception $e) {
            Artisan::call('sendToSlack:message', [
                'args' => array(
                    'location' => 'FileTrait:uploadFile',
                    'messages' => $e
                )
            ]);
        }
    }
    /**
     * Show File
     *
     * @param Image path
     * @return image
     */
    
    public function showFile($file_path, $file_name)
    {
        try {
            $a_file_details = $this->checkFile($file_path, $file_name);
            if(!is_array($a_file_details)) {
                return $a_file_details;
            }
            // return response()->download($path, $filename);
            return response()->make(File::get($a_file_details['path']), 200)->header('Content-type', $a_file_details['mime']);
        } catch(Exception $e) {
            Artisan::call('sendToSlack:message', [
                'args' => array(
                    'location' => 'FileTrait:showFile',
                    'messages' => $e
                )
            ]);
        }
    }
    /**
     * Convert File to Image(jpg)
     *
     * @param File path
     * @return image
     */
    
    public function convertFileToJpg($file_path, $file_name, $output_path)
    {
        try {
            $a_file_details = $this->checkFile($file_path, $file_name);

            if(!is_array($a_file_details)) return $a_file_details;

            if(!File::exists($output_path)) File::makeDirectory($output_path);

            $filename = $a_file_details['no_ext'];
            $doc_path = str_replace("/pages","",$output_path);
            $doc_file = $a_file_details['path'];
            $pdf_file = $doc_path.'/'.$filename.'.pdf';
            $ext_file = $a_file_details['mime'];

            // office documents to pdf process
            $ext = explode("/",$ext_file)[1];
            if(File::exists($doc_file)) {
              if($ext != 'pdf') {
                if ( strtoupper(substr(PHP_OS, 0, 3)) === 'WIN' ) {
                    $scriptName = "start /B " .'soffice --invisible --convert-to pdf:writer_pdf_Export --outdir "'.$doc_path.'" "'.$doc_file.'"';
                    pclose( popen( $scriptName , "r" ) );
                } else {
                  shell_exec('export HOME=/tmp/ && soffice --headless --convert-to pdf:writer_pdf_Export --outdir '.$doc_path.' '.$doc_file.'  > /dev/null &');
                }
                // check pdf file is created and loop until it exist
                while( !File::exists($pdf_file) ) {
                    sleep(5);
                }
              }
            }

          // pdf to images process
            if($pdf_file) {
                $pdf = (new PDFLib())
                    ->setPdfPath($pdf_file)
                    ->setOutputPath($output_path)
                    ->setImageFormat(PDFLib::$IMAGE_FORMAT_PNG)
                    ->setDPI(250);
                $pdf->setPageRange(1,$pdf->getNumberOfPages());
                $pdf->setFilePrefix("$filename-");
                $converted = $pdf->convert();

            } else $converted = '';

            // get the converted files
            if($converted) {

              $files = File::files($output_path);

              natsort($files);
              $a_files = [];
              foreach($files as $key => $file) {
                  if( preg_match('/'.$a_file_details['no_ext'].'/', $file) ) {
                      $a_file_path = explode('/app/public/',$file);
                      $a_files[] = $a_file_path[1];
                  }
              }

              $dFilename = \Config::get('proto.uploads.staff_docs').'/'.$filename;
              $encryptedContent = encrypt($this->storage->get($dFilename.'.'.$a_file_details['ext']));
              $this->storage->put($dFilename.'.dat',$encryptedContent);
              $this->storage->delete($dFilename.'.pdf');
              $this->storage->delete($dFilename.'.'.$a_file_details['ext']);

              return $a_files;
            }
            return $output_path;
        } catch(Exception $e) {
            Artisan::call('sendToSlack:message', [
                'args' => array(
                    'location' => 'FileTrait:convertFileToJpg',
                    'messages' => $e
                )
            ]);
        }
    }
    /**
     * Check File Exist
     *
     * @param File path
     * @return path, file, filename, mime
     */
    
    public function checkFile($file_path, $file_name = null)
    {
        try {
            // For Dev
            $exists = $this->storage->has($file_path);
            $path = storage_path(sprintf('app/public/%s', $file_path));
            $file_info = pathinfo($path);
            $filename = $file_info['basename'];
            if (!$exists || (!is_null($file_name) && $file_name != $filename) ) {
                return response()->make(['message' => 'File not found.', 'file_path' => $file_path, 'path' => $path], 404);
            }

            $file = $this->storage->get($file_path);
            $ext  = pathinfo($file_path,PATHINFO_EXTENSION);
            $mime = $this->storage->mimeType($file_path);
            $size = $this->storage->size($file_path);

            $no_ext = $file_info['filename'];
            return compact('path','filename','file','mime','size','no_ext','ext');
        } catch(Exception $e) {
            Artisan::call('sendToSlack:message', [
                'args' => array(
                    'location' => 'FileTrait:checkFile',
                    'messages' => $e
                )
            ]);
        }
    }

    public function mimeToExt($mime) {
      $mime_map = [
        'application/msword'                                                        => 'doc',
        'application/vnd.openxmlformats-officedocument.wordprocessingml.document'   => 'docx',
        'application/vnd.openxmlformats-officedocument.presentationml.presentation' => 'pptx',
        'application/excel'                                                         => 'xl',
        'application/msexcel'                                                       => 'xls',
        'application/x-msexcel'                                                     => 'xls',
        'application/x-ms-excel'                                                    => 'xls',
        'application/x-excel'                                                       => 'xls',
        'application/x-dos_ms_excel'                                                => 'xls',
        'application/xls'                                                           => 'xls',
        'application/x-xls'                                                         => 'xls',
        'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'         => 'xlsx',
        'application/vnd.ms-excel'                                                  => 'xls',
        'application/powerpoint'                                                    => 'ppt',
        'application/vnd.ms-powerpoint'                                             => 'ppt',
        'application/vnd.ms-office'                                                 => 'ppt',
        'application/msword'                                                        => 'ppt',
        'application/pdf'                                                           => 'pdf',
        'application/octet-stream'                                                  => 'pdf',
      ];

      return isset($mime_map[$mime]) === true ? $mime_map[$mime] : false;
    }
}
