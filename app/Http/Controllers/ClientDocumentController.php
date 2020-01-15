<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Http\Requests;
use Artisan;
use Storage;
use File;
use CloudConvert;

use Log;
// use Spatie\PdfToImage\Pdf;
use ImalH\PDFLib\PDFLib;

class ClientDocumentController extends Controller
{
    public function uploadDocument(Request $request){
        try {
            $document = $this->moveDocumentToStorage($request);
            $orig_filename = $request->filename;
            $page_path = storage_path(sprintf('app/public/%s', \Config::get('proto.uploads.client_doc_pages')));
            $return_statement = [];
            $converted = $this->convertFileToJpg($document['file_url'], $document['file_name'], $page_path);
            $page_url = [];
            $imageurl = [];
            if($converted) {
                foreach($converted as $file) {
                    array_push($imageurl, env('APP_URL').Storage::url($file));

                    $file = str_replace('app/public/','',$file);
                    array_push($page_url, $file);
                }
                $return_statement = [
                    'document_url' => $document['file_url'],
                    'docpage_url' => $page_url,
                    'image_title' => $orig_filename,
                    'image_url' => $imageurl,
                ];
            }
            return $return_statement;
        } catch(Exception $e) {
            Artisan::call('sendToSlack:message', [
                'args' => array(
                    'location' => 'ClientDocumentController:uploadDocument',
                    'messages' => $e
                )
            ]);
        }
    }
    public function moveDocumentToStorage($request){
        try {
            $storage = Storage::disk('public');
            $field_key = 'file_url';
            $a_file = [];
            $client_id = $request->client_id;
            $ctr = $request->convert_ctr;
            $file_prefix = 'document';
            $file_path = \Config::get('proto.uploads.client_docs');
            $file = $request->file('file');
            $a_file['file_name'] = $file_prefix.''.$client_id.''.$ctr.'.'.$file->getClientOriginalExtension();
            $a_file['mime_type'] = $file->getClientMimeType();
            $a_file['size'] = $file->getClientSize();
            $a_file[$field_key] = $file_path.'/'.$a_file['file_name'];

            $storage->put(
                $a_file[$field_key],
                File::get($file->getrealpath())
            );

            if($request->has('current_file_url')) {
                $storage->readAndDelete($request->input('current_file_url'));
            }

            return $a_file;
        } catch(Exception $e) {
            Artisan::call('sendToSlack:message', [
                'args' => array(
                    'location' => 'StaffController:moveDocumentToStorage',
                    'messages' => $e
                )
            ]);
        }
    }
    public function convertFileToJpg($file_path, $file_name, $output_path)
    {
        try {
          $storage = Storage::disk('public');
            $a_file_details = $this->checkFile($file_path, $file_name);

            if(!is_array($a_file_details)) {
                return $a_file_details;
            }

            if(!File::exists($output_path)) {
                File::makeDirectory($output_path);
            }

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

            // // $convert = CloudConvert::file($a_file_details['path'])->to($output_path.'/'.$a_file_details['no_ext'].'.jpg');

            // get the converted files
            if($converted) {

                $files = File::files($output_path);
                natsort($files);
                $a_files = [];
                foreach($files as $key => $file) {
                    if( preg_match('/'.$a_file_details['no_ext'].'/', $file) ) {
                        $a_file_path = explode('/app/public/',$file);
                        // for prod
                        $a_files[] = $a_file_path[1];
                        // Only for local
                        // $a_files[] = str_replace('D:\xampp\htdocs\mee2box\storage\a','a',$a_file_path[0]);
                    }
                }

                $dFilename = \Config::get('proto.uploads.client_docs').'/'.$filename;
                $encryptedContent = encrypt($storage->get($dFilename.'.'.$a_file_details['ext']));
                $storage->put($dFilename.'.dat',$encryptedContent);
                $storage->delete($dFilename.'.pdf');
                $storage->delete($dFilename.'.'.$a_file_details['ext']);

              return $a_files;
            }
            return $converted;
        } catch(Exception $e) {
            Artisan::call('sendToSlack:message', [
                'args' => array(
                    'location' => 'ClientDocumentController:convertFileToJpg',
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
            $storage = Storage::disk('public');
            // For Dev
            $exists = $storage->has($file_path);
            $path = storage_path(sprintf('app/public/%s', $file_path));
            $file_info = pathinfo($path);
            $filename = $file_info['basename'];

            if (!$exists || (!is_null($file_name) && $file_name != $filename) ) {
                return response()->make(['message' => 'File not found.', 'file_path' => $file_path, 'path' => $path], 404);
            }

            $file = $storage->get($file_path);

            $ext  = pathinfo($file_path,PATHINFO_EXTENSION);
            $mime = $storage->mimeType($file_path);
            $size = $storage->size($file_path);
            $no_ext = $file_info['filename'];
            return compact('path','filename','file','mime','size','no_ext','ext');
        } catch(Exception $e) {
            Artisan::call('sendToSlack:message', [
                'args' => array(
                    'location' => 'ClientDocumentController:checkFile',
                    'messages' => $e
                )
            ]);
        }
    }
    public function removeDocuments(Request $request){
        try {
            $storage = Storage::disk('public');
            $docpages = $storage->delete($request->docpages);
            $documents = $storage->delete($request->documents);
            return compact('docpages','documents','request');
        } catch(Exception $e) {
            Artisan::call('sendToSlack:message', [
                'args' => array(
                    'location' => 'ClientDocumentController:removeDocuments',
                    'messages' => $e
                )
            ]);
        }
    }
}
