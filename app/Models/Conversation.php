<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;
use App\Models\Message;

class Conversation extends Model
{
    use HasFactory;
    protected $guarded = [];

    public function messages()
    {   
        return $this->hasMany(Message::class);
    }

    public function receiverInverseRelation()
    {
        return $this->belongsTo(User::class, 'receiver_id');

    }
     
    public function senderInverseRelation()
    {
        return $this->belongsTo(User::class, 'sender_id');

    }
}
