<?php

namespace Modules\FAQ\Entities;

use Illuminate\Database\Eloquent\Model;
use App\Traits\ModelTraits\{Filterable};
use App\Traits\ModelTrait;
use Cache;

class Faq extends Model
{
    use ModelTrait, Filterable;

    /**
     * Table
     * @var string
     */
    protected $table = 'faqs';

    /**
     * Fillable
     *
     * @var array
     */
    protected $fillable = [
        'title',
        'layout_id',
        'description',
        'status'
    ];

    /**
     * Get all use cases
     * @return [type]
     */
    public static function getAll()
    {
        $data = Cache::get(config('cache.prefix') . '.faq');
        if (is_null($data) || $data->isEmpty()) {
            $data = parent::all();
            Cache::put(config('cache.prefix') . '.faq', $data, 604800);
        }
        return $data;
    }

}
