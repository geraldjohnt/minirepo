<?php

namespace App\Transformers;

use League\Fractal\TransformerAbstract;
use App\Notice;
use Storage;

class NoticeTransformer extends TransformerAbstract
{

    /**
     * Turn this item object into a generic array
     *
     * @return array
     */
    public function transform(Notice $notice)
    {
        return [
            'notice_id'     => $notice->id,
            'title'         => $notice->title,
            'content'       => $notice->content,
            'broadcast'     => $notice->broadcast,
            'cover_url'     => !$notice->cover_url ? '' : env('APP_URL').Storage::url($notice->cover_url),
            'created_by'    => $notice->created_by,
        ];
    }

}