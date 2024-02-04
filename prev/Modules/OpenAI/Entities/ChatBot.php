<?php

namespace Modules\OpenAI\Entities;

use App\Models\Model;
use App\Traits\ModelTraits\hasFiles;
use Modules\MediaManager\Http\Models\ObjectFile;

class ChatBot extends Model
{
    use hasFiles;
    protected $table = 'chat_bots';

    /**
     * Fillable
     *
     * @var array
     */
    protected $fillable = [
        'name'
    ];

    /**
     * Store Chat Bot
     *
     * @param array $data
     * @return bool
     */
    public function store($data) {
        if (empty($data['name'])) {
            $data['name'] = 'Genie';
        }

        if (parent::first()->update($data)) {
            if (request()->has('file_id') && !empty(request()->file_id)) {
                parent::first()->updateFiles();
            } else {
                parent::first()->deleteFromMediaManager();
            }

            return true;
        }

        return false;
    }
}
