<?php

use HackerNewsItemModel as ItemModel;

class HackerNewsItemMapperModel
{
    private $client;
    private $itemsPerPage;

    public function __construct(HackerNewsClient $hackerNewsClient, $itemsPerPage = 0)
    {
        $this->client = $hackerNewsClient;
        $this->itemsPerPage = $itemsPerPage;
    }

    /**
     * @param array $ids
     * @param int $page
     * @return array
     * Returns the number of article ids based on the $page and number of articles
     */
    public function buildPagination(array $ids, $page=1){

        $offset = 0;

        if($page > 1){
            $offset = ($page - 1) * $this->itemsPerPage;
        }

        return array_slice($ids, $offset, $this->itemsPerPage);
    }

    /**
     * @param $type
     * @param int $page
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @return array $articles
     */
    public function getArticles($type, $page=1)
    {
        $ids = $this->buildPagination($this->client->get($type), $page);
        
        $items = $this->client->getItemsAsync($ids);

        $articles = [];

        foreach ($items as $item) {
            //ignore any deleted items
            if (is_array($item) && (!array_key_exists('deleted', $item) || !$item['deleted'])) {

                $itemModel = new ItemModel($item);
                $articles[$itemModel->getId()] = $itemModel;
            }
        }

        return $articles;
    }

    /**
     * @param $id
     * @return array
     */
    public function getArticleCommentsInATree($id, $page=1)
    {
        $trees = [];
        $tempTree = [];
        $ids = [$id];

        while ($ids) {
            $items = $this->client->getItemsAsync($ids);
            $ids = [];

            foreach ($items as $item) {
                //ignore any deleted items
                if (is_array($item) && (!array_key_exists('deleted', $item) || !$item['deleted'])) {

                    $model = new ItemModel($item);
                    $tempTree[$model->getId()] = $model;

                    if (!array_key_exists('parent', $item)) {
                        $trees[] = $model; //This would be the parent item (article)
                        $item["kids"] = $this->buildPagination($item["kids"], $page); //limit the number of kids from the parent
                    } else {
                        //put the item model in place if it's a child rather than a parent
                        if (array_key_exists($item['parent'], $tempTree)) {
                            $parentModel = $tempTree[$item['parent']];
                            $parentModel->setKid($model);
                        }
                    }

                    //add the kids ids' into the array so we can iterate again to put it in the item tree
                    if (array_key_exists('kids', $item)) {
                        $ids = array_merge($ids, $item['kids']);
                    }
                }
            }
        }

        return $trees;
    }

}