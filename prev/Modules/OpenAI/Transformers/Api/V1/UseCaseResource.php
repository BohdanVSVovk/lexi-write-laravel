<?php

namespace Modules\OpenAI\Transformers\Api\V1;

use Illuminate\Http\Resources\Json\JsonResource;

class UseCaseResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'description' => $this->description,
            'slug' => $this->slug,
            'prompt' => $this->prompt,
            'creator_type' => $this->creator_type,
            'creator_id' => $this->creator_id,
            'status' => $this->status,
            'image_url' => $this->fileUrl(),
            'categories' => $this->whenLoaded('useCaseCategories', function() {
                return UseCaseCategoryResource::collection($this->useCaseCategories);
            }),
            'option' => OptionResource::collection($this->option),
        ]; 
    }
}
