<?php

namespace BeeperApi\Services;

class MicroPaginator
{
    /**
     * @param array $items - an array of items to paginate
     * @param int $page - which page to retrieve
     * @param int $perPage - how many results per page
     *
     * @return array
     */
    public function paginate($items, $page = 1, $perPage = 15)
    {
        $currentItems = array_slice($items, ($page-1) * $perPage, $perPage);

        //remove MicroDB indexes
        $currentItems = array_values($currentItems);

        $totalItems = count($items);

        return [
            'data' => $currentItems,
            'current_page' => (int) $page,
            'last_page' => ceil($totalItems / $perPage),
            'total' => $totalItems
        ];
    }
}
