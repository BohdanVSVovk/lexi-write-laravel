<?php
/**
 * @package PlanResource
 * @author TechVillage <support@techvill.org>
 * @contributor Al Mamun <[almamun.techvill@gmail.com]>
 * @created 26-07-2023
 */
namespace Modules\Subscription\Http\Resources;

use App\Services\SubscriptionService;
use Illuminate\Http\Resources\Json\JsonResource;
use Modules\Subscription\Entities\Package;

class PlanResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request = [])
    {
        $plan = Package::find($this->id);

        return [
            "id" => $this->id,
            "creator_id" => $this->user_id,
            "creator_name" => $this?->user?->name,
            "creator_image" => $this->user ? $this->user->fileUrl() : null,
            "name" => $this->name,
            "code" => $this->code,
            "short_description" => $this->short_description,
            "sale_price" => formatNumber($this->sale_price),
            "discount_price" => formatNumber($this->discount_price),
            "billing_cycle" => ucfirst($this->billing_cycle),
            "duration" => $this->duration,
            "sort_order" => $this->sort_order,
            "trial_day" => $this->trial_day,
            "renewable" => boolval($this->renewable),
            "status" => $this->status,
            "features" => SubscriptionService::getFeatures($plan, false)
        ];
    }
}
