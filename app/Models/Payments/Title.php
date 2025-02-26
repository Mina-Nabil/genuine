<?php

namespace App\Models\Payments;

use Exception;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Title extends Model
{
    use HasFactory;

    protected $table = 'payment_titles';
    protected $fillable = ['title', 'limit', 'description'];
    public $timestamps = false;

    public static function newTitle($title, $limit, $description = null)
    {
        try {
            return self::create([
                'title' => $title,
                'limit' => $limit,
                'description' => $description,
            ]);
        } catch (Exception $e) {
            report($e);
            return false;
        }
    }

    public function editTitle($title, $limit, $description = null)
    {
        try {
            $this->update([
                'title' => $title,
                'limit' => $limit,
                'description' => $description,
            ]);
        } catch (Exception $e) {
            report($e);
            return false;
        }
    }
}
