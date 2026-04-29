<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
class BrainCycle extends Model {
    protected $fillable = ['cycle_number', 'directive_used', 'status'];
    public function marketSignals(): HasMany { return $this->hasMany(MarketSignal::class); }
    public function collisionPoints(): HasMany { return $this->hasMany(CollisionPoint::class); }
    public function vendorHooks(): HasMany { return $this->hasMany(VendorHook::class); }
}
