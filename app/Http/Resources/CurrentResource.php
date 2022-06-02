<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Request;

class CurrentResource extends JsonResource
{

  /**
   * Transform the resource into an array.
   *
   * @param  Request  $request
   *
   * @return array
   */
  public function toArray($request): array
  {
    return json_decode($this->weather_info, true);
  }

}
