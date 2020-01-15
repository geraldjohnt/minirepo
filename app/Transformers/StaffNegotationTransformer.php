<?php

namespace App\Transformers;

use League\Fractal\TransformerAbstract;
use App\StaffNegotation;
use Storage;

class StaffNegotationTransformer extends TransformerAbstract
{

    /**
     * Turn this item object into a generic array
     *
     * @return array
     */
    public function transform(StaffNegotation $staffnegotation)
    {
        return [
            'negotation_id'     => $staffnegotation->id,
            'notes'             => $staffnegotation->notes,
            'duration'          => $staffnegotation->duration,
            'created_at'        => $staffnegotation->created_at,
            'voice_report'      => $staffnegotation->voice_report,
            'video_report'      => $staffnegotation->video_report,    
            'video_size'      => $staffnegotation->video_size,   
        ];
    }

}