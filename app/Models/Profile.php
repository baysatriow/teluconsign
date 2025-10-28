<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\Storage;

class Profile extends Model
{
    use HasFactory;

    protected $primaryKey = 'user_id';
    public $incrementing = false;
    protected $keyType = 'int';

    protected $fillable = [
        'user_id',
        'phone',
        'address',
        'bio',
        'photo_url'
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id')->withDefault();
    }

    public function updateProfile(Profile $data): bool
    {
        return $this->update($data->toArray());
    }

    public function uploadPhoto(string $photo_path): void
    {
        $path = Storage::disk('public')->put('profile_photos', $photo_path);
        $this->photo_url = $path;
        $this->save();
    }

    public function getProfileSummary(): string
    {
        return $this->phone . ' - ' . $this->address;
    }
}
