<?php

use Carbon\Carbon;

class HackerNewsItemModel
{
    private $id;
    private $type;
    private $by;
    private $time;
    private $text;
    private $kids = [];
    private $url;
    private $score;
    private $title;
    private $descendants;

    public function __construct(array $array)
    {
        $this->id = array_key_exists("id", $array)? $array["id"] : null;
        $this->type = array_key_exists("type", $array)? $array["type"] : null;
        $this->by = array_key_exists("by", $array)? $array["by"] : null;
        $this->time = array_key_exists("time", $array)? Carbon::createFromTimestamp((int) $array['time']) : null;
        $this->url = array_key_exists("url", $array)? $array["url"] : null;
        $this->score = array_key_exists("score", $array)? $array["score"] : 0;
        $this->title = array_key_exists("title", $array)? $array["title"] : null;
        $this->text = array_key_exists("text", $array)? $array["text"] : null;
        $this->descendants = array_key_exists("descendants", $array) ? $array["descendants"] : 0;
    }
    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return mixed
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @return mixed
     */
    public function getBy()
    {
        return $this->by;
    }

    /**
     * @return mixed
     */
    public function getTime()
    {
        return $this->time;
    }

    /**
     * @return string
     * Gets the time difference from the current time to the time it was given, days, hours or minutes
     */
    public function getTimeDifference(){
        return $this->time->diffForHumans();
    }

    /**
     * @return mixed
     */
    public function getText()
    {
        return $this->text;
    }

    public function setKid(HackerNewsItemModel $kid){
        array_push($this->kids, $kid);
    }

    /**
     * @return array
     */
    public function getKids()
    {
        return $this->kids;
    }

    /**
     * @return mixed
     */
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * @return mixed
     */
    public function getScore()
    {
        return $this->score;
    }

    /**
     * @return mixed
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * @return mixed
     */
    public function getDescendants()
    {
        return $this->descendants;
    }


}