<?php

namespace App\Transformers;

use League\Fractal\TransformerAbstract;
use App\User;
use Storage;

class UserTransformer extends TransformerAbstract
{

    /**
     * Turn this item object into a generic array
     *
     * @return array
     */
    public function transform(User $user)
    {
        return [
            'first_name'    => $user->first_name,
            'last_name'     => $user->last_name,
            'email'         => $user->email,
            'company'       => $user->company,
            'department'    => $user->department,
            'post'          => $user->post,
            'pic_url'       => !$user->pic_url ? '' : env('APP_URL').Storage::url($user->pic_url),
            'phone_number'  => $user->phone_number,
        ];
    }

}