<?php

namespace App\Contracts;

use App\Support\Resources\CustomAnonymousResourceCollection;
use App\Support\Resources\CustomPaginatedResourceResponse;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Pagination\AbstractPaginator;

/**
 * Base class for a resource/response
 */
class Resource extends JsonResource
{
    /**
     * Iterate through the list of $fields and check if they're a "Unit"
     * If they are, then add the response
     *
     * @param       $response
     * @param array $fields
     */
    public function checkUnitFields(&$response, array $fields): void
    {
        foreach ($fields as $f) {
            if ($this->{$f} instanceof Unit) {
                $response[$f] = $this->{$f}->getResponseUnits();
            } else {
                $response[$f] = $this->{$f};
            }
        }
    }

    /**
     * Customize the response to exclude all the extra data that isn't used. Based on:
     * https://gist.github.com/derekphilipau/4be52164a69ce487dcd0673656d280da
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function toResponse($request)
    {
        return $this->resource instanceof AbstractPaginator
                    ? (new CustomPaginatedResourceResponse($this))->toResponse($request)
                    : parent::toResponse($request);
    }

    public static function collection($resource)
    {
        return tap(new CustomAnonymousResourceCollection($resource, static::class), function ($collection) {
            if (property_exists(static::class, 'preserveKeys')) {
                $collection->preserveKeys = (new static([]))->preserveKeys === true;
            }
        });
    }
}
