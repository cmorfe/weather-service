<?php

namespace App\Http\Controllers;

use App\Http\Requests\CurrentRequest;
use App\Http\Resources\CurrentResource;
use App\Models\Current;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Response;

class CurrentController extends Controller
{

  /**
   * Handle the incoming request.
   *
   * @param  CurrentRequest  $request
   *
   * @return JsonResponse
   */
  public function __invoke(CurrentRequest $request): JsonResponse
  {
    $query = $request->query('query');

    try {
      $current = Current::findOrFail($query);
    } catch (ModelNotFoundException) {
      $current = $this->getWeatherInfo($query);
    }

    return Response::json(new CurrentResource($current));
  }

  private function getWeatherInfo(string $query): string
  {
    return $query;
  }

}
