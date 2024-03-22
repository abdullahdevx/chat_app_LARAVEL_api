<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;
use App\Models\Conversation;


class Message extends Model
{
    use HasFactory;
    protected $guarded = [];


    //here
    public function conversationInverseRelation()
    {
        return $this->belongsTo(Conversation::class, 'conversation_id');
    }

    public function userInverseRelation()
    {
        return $this->belongsTo(User::class, 'sender_id');
    }

}
