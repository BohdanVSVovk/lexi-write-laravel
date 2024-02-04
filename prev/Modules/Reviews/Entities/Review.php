<?php

namespace Modules\Reviews\Entities;

use App\Models\{
    Model,
    User
};
use App\Traits\ModelTraits\{Filterable};

class Review extends Model
{
    use Filterable;

    /**
     * Filterable
     *
     * @var array
     */
    protected $fillable = [
        'title',
        'comments',
        'rating',
        'user_id',
        'status'
    ];

    /**
     * Foreign key with User model
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    /**
     * Get Average Rating
     *
     * @return object|null
     */
    public static function getAvgRating(){

        return parent::join('users', 'users.id', 'reviews.user_id')
            ->select(\DB::raw('sum(reviews.rating)/count(reviews.id) as avgRating'), \DB::raw('count(reviews.id) as totalRating'))
            ->where('reviews.status', 'Active')
            ->first();

    }

}
