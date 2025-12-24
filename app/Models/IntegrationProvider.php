<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class IntegrationProvider extends Model
{
    use HasFactory;

    protected $primaryKey = 'integration_provider_id';

    protected $fillable = [
        'code',
        'name',
    ];

    public $timestamps = false;

    protected $casts = [
        'created_at' => 'datetime',
    ];

    public function addProvider(string $code, string $name): bool
    {
        return $this->create([
            'code' => $code,
            'name' => $name,
        ]) ? true : false;
    }

    public function updateProvider(int $id, string $name): bool
    {
        return $this->where('integration_provider_id', $id)
            ->update(['name' => $name]);
    }

    public function deleteProvider(int $id): bool
    {
        return $this->where('integration_provider_id', $id)
            ->delete();
    }

    public function getActiveKeys()
    {
        return IntegrationKey::where('provider_id', $this->integration_provider_id)
            ->where('is_active', 1)
            ->get();
    }
}
