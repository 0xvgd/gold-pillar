<?php

namespace App\Controller\Traits;

use Doctrine\ORM\Query;
use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

trait DatatableTrait
{
    protected function searchValues(Request $request): array
    {
        $search = $request->get('search');
        if (!is_array($search)) {
            parse_str($search, $search);
        }

        return $search;
    }

    protected function addOrderBy(Request $request, QueryBuilder $qb, array $columns)
    {
        $orders = (array) $request->get('order');

        foreach ($orders as $order) {
            $column = $order['column'] ?? 0;
            $dir = $order['dir'] ?? 'ASC';
            $field = $columns[$column] ?? null;

            if ($field) {
                $qb->orderBy($field, $dir);
            }
        }
    }

    /**
     * Manipulate a request from jQuery DataTable and return the JSON required
     * to display the table.
     *
     * @return JsonResponse
     */
    protected function dataTable(Request $request, Query $query, bool $arrayResult = true, array $context = [])
    {
        $maxResults = (int) $request->get('length');
        if ($maxResults <= 0 || $maxResults > 1000) {
            $maxResults = 10;
        }
        $offset = (int) $request->get('start');
        if ($offset < 0) {
            $offset = 0;
        }
        $paginator = new Paginator($query, false);
        $query
            ->setFirstResult($offset)
            ->setMaxResults($maxResults)
        ;
        if ($arrayResult) {
            $result = $query->getArrayResult();
        } else {
            $result = $query->getResult();
        }
        $content = [
            'recordsTotal' => sizeof($paginator),
            'recordsFiltered' => sizeof($paginator),
            'data' => $result,
        ];

        return $this->json($content, 200, [], $context);
    }
}
