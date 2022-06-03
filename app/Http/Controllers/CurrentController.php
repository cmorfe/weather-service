<?php

namespace App\Http\Controllers;

use App\Http\Requests\CurrentRequest;
use App\Http\Resources\CurrentResource;
use App\Models\Current;
use Exception;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ConnectException;
use GuzzleHttp\Exception\GuzzleException;
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
      try {
        $current = $this->getWeatherInfo($query);
      } catch (ConnectException) {
        return Response::json(__('error.api_url'), 500);
      } catch (Exception|GuzzleException $e) {
        $message = $e->getMessage();

        return Response::json(compact('message'), $e->getCode());
      }
    }

    return Response::json(new CurrentResource($current));
  }


  /**
   * @param  string  $query
   *
   * @return Current
   * @throws GuzzleException|Exception
   */
  private function getWeatherInfo(string $query): Current
  {
    $client = new Client(['base_uri' => env('WEATHERSTACK_API_URL')]);

    $request = $client->get('current', $this->setOptions($query));

    $weather_info = $request->getBody()->getContents();

    $this->validateSuccess($weather_info);

    return Current::withoutGlobalScope('recent')
      ->updateOrCreate(
        compact('query'),
        compact('weather_info')
      );
  }

  /**
   * @param  string  $query
   *
   * @return array
   */
  private function setOptions(string $query): array
  {
    $access_key = env('WEATHERSTACK_API_ACCESS_KEY');

    return [
      'headers' => [
        'Accept' => 'application/json',
      ],
      'query'   => compact('access_key', 'query'),
    ];
  }

  /**
   * @throws Exception
   */
  protected function validateSuccess(string $response): void
  {
    $response = json_decode($response);

    if (isset($response->success) && !$response->success) {
      $code = $response->error->code;

      $message = match ($code) {
        101 => __('error.api_access_key'),
        615 => __('error.query'),
        default => 'Unexpected error'
      };

      throw new Exception($message, $code == 615 ? 400 : 500);
    }
  }

}
