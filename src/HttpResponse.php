<?php

namespace BeeperApi;

class HttpResponse extends \Http\HttpResponse
{
    public function setContent($content)
    {
        parent::setContent(json_encode($content));
    }
}