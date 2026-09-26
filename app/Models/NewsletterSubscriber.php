<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property string $email
 */
class NewsletterSubscriber extends Model
{
    protected $fillable = ['email'];
}
