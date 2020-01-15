<?php

namespace App\Transformers;

use League\Fractal\TransformerAbstract;
use App\StaffDocument;
use Storage;

class StaffDocumentTransformer extends TransformerAbstract
{

    /**
     * Turn this item object into a generic array
     *
     * @return array
     */
    public function transform(StaffDocument $document)
    {
        $pages = [];
        foreach($document->pages as $page) {
            $pages[] = [
                'page_id'            => $page->id,
                'staff_document_id'  => $page->staff_document_id,
                'annotation'         => $page->annotation,
                'image_url'          => !$page->image_url ? '' : env('APP_URL').Storage::url($page->image_url),
            ];
        }
        
        return [
            'document_id'    => $document->id,
            'staff_id'       => $document->staff_id,
            'title'          => $document->title,
            'description'    => $document->description,
            'file_url'       => !$document->file_url ? '' : env('APP_URL').Storage::url($document->file_url),
            'file_name'      => $document->file_name,
            'allow_download' => $document->allow_download,
            'created_by'     => $document->created_by,
            'created_at'     => $document->created_at,
            'size'           => $document->size,
            'pages'          => $pages,
            'folder_id'      => $document->folder_id
        ];
    }

}