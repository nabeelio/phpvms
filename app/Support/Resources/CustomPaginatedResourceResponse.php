<?php
namespace App\Support\Resources;

use Illuminate\Http\Resources\Json\PaginatedResourceResponse;
use Illuminate\Support\Arr;

class CustomPaginatedResourceResponse extends PaginatedResourceResponse
{
    protected function paginationLinks($paginated)
    {
        return [];
    }

    protected function meta($paginated)
    {
        $meta = ['pagination' => Arr::except($paginated, [
            'data',
            'first_page_url',
            'last_page_url',
            'prev_page_url',
            'next_page_url',
            'links',
        ])];

        $meta['pagination']['total_pages'] = $paginated['last_page'];

        return $meta;
    }
}
