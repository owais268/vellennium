<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class UserInformation extends Model implements HasMedia
{
    use HasFactory,InteractsWithMedia;

    protected $guarded =['íd'];
    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('government_id')
             ->singleFile();

        $this->addMediaCollection('documentation_licensing')
             ->singleFile();
    }
}
