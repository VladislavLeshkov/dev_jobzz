<?php

namespace App\Events;

use App\Models\Job\Payout;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class PayoutCreated implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public Payout $payout;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(Payout $payout)
    {
        //
        $this->payout = $payout;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        return [
            new PrivateChannel('App.Models.User.'.$this->payout->recruiter_id),
            new PrivateChannel('Admin.Notification'),
        ];
    }
}
