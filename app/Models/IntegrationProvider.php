<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class IntegrationProvider extends Model
{
    use HasFactory;

    private $primaryKey = 'integration_provider_id';
    private $fillable = [
        'code',
        'name'
    ];

    public function keys()
    {
        return $this->hasMany(IntegrationKey::class, 'integration_provider_id');
    }

    public function addProvider(string $code, string $name): bool
    {
        return $this->create([
            'code' => $code,
            'name' => $name
        ]) ? true : false;
    }

    public function updateProvider(int $id, string $name): bool
    {
        return $this->where('integration_provider_id', $id)->update(['name' => $name]);
    }

    public function deleteProvider(int $id): bool
    {
        return $this->where('integration_provider_id', $id)->delete();
    }

    public function getActiveKeys()
    {
        return $this->keys()->where('is_active', 1)->get();
    }
}
