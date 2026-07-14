<?php // Supplier model — represents a vendor/supplier in Accounts Payable. Stores name, email, phone, and address.
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Supplier extends Model
{
    protected $fillable = [
        'name',
        'email',
        'phone',
        'address',
    ];
}
