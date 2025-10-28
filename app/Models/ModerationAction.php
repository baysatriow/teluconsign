<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ModerationAction extends Model
{
    use HasFactory;

    protected $primaryKey = 'moderation_action_id';
    public $incrementing = true;
    protected $keyType = 'int';

    protected $fillable = [
        'admin_id',
        'target_type',
        'target_id',
        'action',
        'reason'
    ];

    public function admin()
    {
        return $this->belongsTo(User::class, 'admin_id');
    }

    public function logAction(int $admin_id, string $target_type, int $target_id, string $action, string $reason): bool
    {
        return $this->create([
            'admin_id' => $admin_id,
            'target_type' => $target_type,
            'target_id' => $target_id,
            'action' => $action,
            'reason' => $reason
        ]) ? true : false;
    }

    public function getActionsByTarget(string $target_type, int $target_id)
    {
        return $this->where('target_type', $target_type)
                    ->where('target_id', $target_id)
                    ->orderBy('created_at', 'desc')
                    ->get();
    }

    public function revertAction(int $id): void
    {
        $action = $this->find($id);
        if ($action) {
            $reverse = [
                'takedown' => 'restore',
                'suspend' => 'restore',
                'hide' => 'unhide',
                'unhide' => 'hide'
            ];

            if (isset($reverse[$action->action])) {
                $action->update(['action' => $reverse[$action->action]]);
            }
        }
    }
}
