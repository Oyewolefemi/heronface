<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class CollisionPoint extends Model {
    protected $fillable = ['brain_cycle_id', 'signal_context', 'platform_factor', 'inference', 'severity'];
}
