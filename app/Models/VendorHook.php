<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class VendorHook extends Model {
    protected $fillable = ['brain_cycle_id', 'vendor_name', 'hook_text', 'status'];
}
