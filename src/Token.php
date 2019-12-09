<?php
namespace Mrlaozhou\Guard;

use Illuminate\Database\Eloquent\Model;

class Token extends Model
{
    protected $primaryKey       =   'token';

    protected $keyType          =   'string';

    protected $table        =   'fool_tokens';

    protected $guarded      =   [];

    protected $casts        =   [
        'expired_at'    =>  'datetime'
    ];

    public function user()
    {
        return $this->belongsTo(config('auth.providers.users.model'), 'user_id', 'id');
    }
}