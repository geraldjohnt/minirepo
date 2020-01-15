<?php

namespace App\Transformers;

use League\Fractal\TransformerAbstract;
use App\StaffDocumentPage;
use Storage;

class StaffDocumentPageTransformer extends TransformerAbstract
{

    /**
     * Turn this item object into a generic array
     *
     * @return array
     */
    public function transform(StaffDocumentPage $page)
    {
        return [
            'page_id'            => $page->id,
            'staff_document_id'  => $page->staff_document_id,
            'annotation'         => $page->annotation,
            'image_url'          => !$page->image_url ? '' : env('APP_URL').Storage::url($page->image_url),
        ];
    }

}