<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Profile extends Model
{
    use HasFactory;

    protected $primaryKey = 'user_id';
    public $incrementing = false;
    public $timestamps = false;

    protected $fillable = [
        'user_id',
        'phone',
        'bio',
        'photo_url',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id', 'user_id');
    }

    public function updateProfile(Profile $data): bool
    {
        $this->fill($data->only($this->fillable));
        return $this->save();
    }

    public function uploadPhoto(string $photo_path): void
    {
        $this->photo_url = $photo_path;
        $this->save();
    }

    public function getProfileSummary(): string
    {
        return trim(($this->phone ?? '') . ' ' . ($this->bio ?? ''));
    }
}
