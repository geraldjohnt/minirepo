<?php

namespace App\Transformers;

use League\Fractal\TransformerAbstract;
use App\StaffNotice;
use Storage;

class StaffNoticeTransformer extends TransformerAbstract
{

    /**
     * Turn this item object into a generic array
     *
     * @return array
     */
    public function transform(StaffNotice $staffnotice)
    {
        return [
            'notice_id'           => $staffnotice->notice_id,
            'notice_details'      => [
                'title'         => $staffnotice->notice->title,
                'content'       => $staffnotice->notice->content,
                'broadcast'     => $staffnotice->notice->broadcast,
                'cover_url'     => !$staffnotice->notice->cover_url ? '' : env('APP_URL').Storage::url($staffnotice->notice->cover_url),
                'created_by'    => $staffnotice->notice->created_by,
                'created_at'    => $staffnotice->notice->created_at
            ]
        ];
    }

}