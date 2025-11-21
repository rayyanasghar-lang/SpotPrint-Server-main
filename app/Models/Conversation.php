<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Conversation extends Model
{

    protected $table = 'chat_conversations';

    protected $appends = ['users'];

    protected $fillable = [
        'name',
        'is_group',
        'participants'
    ];

    protected $casts = [
        //'participants' => 'array',
    ];

    public function getParticipantsAttribute($value)
    {
        return explode(',', $value);
    }

    public function messages()
    {
        return $this->hasMany(Message::class);
    }

    public function getUsersAttribute()
    {
        $participantIds = $this->participants;
        return User::whereIn('id', $participantIds)->get();
    }
}
