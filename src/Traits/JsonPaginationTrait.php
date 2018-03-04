<?php

namespace App\Traits;

use Cake\Http\ServerRequest;
use Cake\Http\Response;
use Cake\ORM\Query;

/** Check if Trait already declared */
if (!trait_exists('JsonPaginationTrait')) {
    /**
     * Trait JsonPaginationTrait
     * @package App\Traits
     * @property ServerRequest $request
     * @property Response $response
     */
    trait JsonPaginationTrait
    {

        /**
         * Total records count
         *
         * @var int
         */
        private $recordsCount = 0;

        /**
         * Returns current page
         *
         * @return int
         */
        protected function getPage()
        {
            return $this->request->getQuery('page') ? intval($this->request->getQuery('page')) : 1;
        }

        /**
         * Returns limit of data to respond
         *
         * @return int
         */
        protected function getLimit()
        {
            return $this->request->getQuery('limit') ? intval($this->request->getQuery('limit')) : 12;
        }

        /**
         * Set records count regarding to Query
         *
         * @param Query $query
         * @return int|null
         */
        private function setRecordsCount(Query $query)
        {
            return $this->recordsCount = $query->count();
        }

        /**
         * Get records count
         *
         * @return int
         */
        private function getRecordsCount()
        {
            return $this->recordsCount;
        }

        /**
         * Get total pages count
         *
         * @return float
         */
        private function getPagesCount()
        {
            return floor($this->getRecordsCount() / $this->getLimit());
        }

        /**
         * Check if next page is available
         *
         * @return bool
         */
        private function getNext()
        {
            return $this->getPage() < $this->getPagesCount();
        }

        /**
         * Check if previous page is available
         *
         * @return bool
         */
        private function getPrev()
        {
            return $this->getPage() > 1 && $this->getPage() <= $this->getPagesCount();
        }

        /**
         * Returns links array
         *
         * @return array
         */
        private function getLinks()
        {

            $uri = $this->request->getEnv('REQUEST_URI');
            $page = $this->getPage();
            $pages = $this->getPagesCount();

            return [
                'first' => preg_replace('/page=(\d+)/', "page=1", $uri),
                'last' => preg_replace('/page=(\d+)/', "page={$this->getPagesCount()}", $uri),
                'prev' => $page > 1 && $page <= $pages ? preg_replace('/page=(\d+)/', 'page=' . ($this->getPagesCount() - 1), $uri) : null,
                'next' => $page < $pages ? preg_replace('/page=(\d+)/', 'page=' . ($this->getPagesCount() + 1), $uri) : null,
            ];
        }

        private function encodeData($data = null)
        {
            return json_encode($data);
        }

        /**
         * Paginator
         * Returns array of records with:
         * - alias
         * - records count
         * - current page
         * - total pages
         * - display limit
         * - next page availability
         * - previous page availability
         * - links array
         *
         * @param Query $query
         * @param string $alias
         * @return \Cake\Http\Response
         */
        public function j_paginate(Query $query, $alias = 'data')
        {

            $this->setRecordsCount($query);
            $this->response = $this->response
                ->withType('json')
                ->withStringBody(
                    $this->encodeData([
                        'data' => [
                            $alias => $this->getPage() > $this->getPagesCount() || $this->getPage() <= $this->getPagesCount() ?
                                $query->page($this->getPage(), $this->getLimit())->toArray() : [],
                            'records' => $this->getRecordsCount(),
                            'page' => $this->getPage(),
                            'pages' => $this->getPagesCount(),
                            'limit' => $this->getLimit(),
                            'nextPage' => $this->getNext(),
                            'prevPage' => $this->getPrev(),
                            'links' => $this->getLinks()
                        ]
                    ])
                );

            return $this->response;
        }
    }
}
