<?php

/**
 * @package PreferenceTrait
 * @author TechVillage <support@techvill.org>
 * @contributor Md. Khayeruzzaman <[shakib.techvill@gmail.com]>
 * @created 19-05-2023
 */

namespace Modules\OpenAI\Traits;

use Modules\OpenAI\Entities\ContentType;

trait PreferenceTrait
{
    /**
     * Get data
     * @param string $name
     * @return mixed
     */
    public function __get($name)
    {
        $val = parent::__get($name);

        if ($val <> null) {
            return $val;
        }

        $data = $this->metaData()->where('key', $name)->first();

        if ($data) {
            return $data->value;
        }
    }

    /**
     * get meta data
     *
     * @param array $data
     * @return array
     */
    public static function getData($slug = null)
    {
        $data = [];
        $prefArr = [ 'document', 'image_maker', 'code_writer'];

        if ( in_array($slug, $prefArr) ) {
            $data = ContentType::with('metaData')->where('slug', $slug)->first();
        }
        
        return $data;
    }
}
