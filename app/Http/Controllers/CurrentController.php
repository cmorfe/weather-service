<?php

namespace App\Http\Controllers;

use App\Http\Requests\CurrentRequest;
use App\Models\Current;
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
    $current = Current::find();
    return Response::json();
  }

}
