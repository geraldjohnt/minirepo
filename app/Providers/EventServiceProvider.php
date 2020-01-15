<?php

namespace App\Providers;

use Illuminate\Contracts\Events\Dispatcher as DispatcherContract;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

use App\User;
use App\Admin;
use App\Staff;
use App\Notice;
use App\StaffNotice;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [
        'App\Events\SomeEvent' => [
            'App\Listeners\EventListener',
        ],
    ];

    /**
     * Register any other events for your application.
     *
     * @param  \Illuminate\Contracts\Events\Dispatcher  $events
     * @return void
     */
    public function boot(DispatcherContract $events)
    {
        parent::boot($events);

        User::created(function($user) {
            if($user->isAdmin()) {
                Admin::create([
                    'user_id' => $user->id,
                    'title' => 'Administrator'
                ]);
            }
            if($user->isStaff()) {
                Staff::create([
                    'user_id' => $user->id,
                    'title' => 'Staff'
                ]);
            }
        });

        Notice::created(function($notice) {
            $a_staff = Staff::get();
            foreach($a_staff as $staff) {
                StaffNotice::create([
                    'notice_id' => $notice->id,
                    'staff_id' => $staff->id,
                    'read' => false
                ]);
            }
        });

        Staff::created(function($staff) {
            $a_notices = Notice::isBroadcast()->get();
            foreach($a_notices as $notice) {
                StaffNotice::create([
                    'notice_id' => $notice->id,
                    'staff_id' => $staff->id,
                    'read' => false
                ]);
            }
        });

        Staff::deleted(function($staff) {
            $staff->user->delete();
        });
    }
}
