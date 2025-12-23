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

    protected $casts = [
        'created_at' => 'datetime'
    ];

    public function logAction(
        int $admin_id,
        string $target_type,
        int $target_id,
        string $action,
        string $reason
    ): bool {
        return (bool) $this->create([
            'admin_id'    => $admin_id,
            'target_type' => $target_type,
            'target_id'   => $target_id,
            'action'      => $action,
            'reason'      => $reason
        ]);
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

        if (! $action) {
            return;
        }

        $reverse = [
            'takedown' => 'restore',
            'suspend'  => 'restore',
            'hide'     => 'unhide',
            'unhide'   => 'hide'
        ];

        if (isset($reverse[$action->action])) {
            $action->update([
                'action' => $reverse[$action->action]
            ]);
        }
    }
}
