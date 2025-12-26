<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class IntegrationKey extends Model
{
    use HasFactory;
    public $timestamps = false;
    protected $primaryKey = 'integration_key_id';

    protected $fillable = [
        'provider_id',
        'label',
        'is_active',
        'public_k',
        'encrypted_k',
        'meta_json',
    ];

    protected $casts = [
        'meta_json' => 'array',
        'created_at' => 'datetime',
    ];

    public function provider()
    {
        return $this->belongsTo(IntegrationProvider::class, 'provider_id', 'integration_provider_id');
    }

    public function addKey(int $provider_id, string $label, string $key): bool
    {
        return (bool) $this->create([
            'provider_id' => $provider_id,
            'label' => $label,
            'public_k' => substr($key, 0, 6),
            'encrypted_k' => $this->encryptKey($key),
            'is_active' => 1,
            'meta_json' => [],
        ]);
    }

    public function deactivateKey(int $id): void
    {
        $this->where('integration_key_id', $id)->update([
            'is_active' => 0,
        ]);
    }

    public function encryptKey(string $raw_key): string
    {
        return base64_encode($raw_key);
    }

    public function validateKey(string $key): bool
    {
        return $key !== '';
    }
}
