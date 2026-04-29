<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class MarketSignal extends Model {
    protected $fillable = ['brain_cycle_id', 'title', 'source', 'url', 'explained_content', 'color_tag'];
}
