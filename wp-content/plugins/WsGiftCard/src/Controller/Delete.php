<?php
namespace WS\Controller;
use WS\Model\Egift;

/**
 * Class Delete
 * @package WS\Controller
 */
class Delete extends Egift
{


    /**
     * @return false|int|string
     */
    public function __construct()
    {
        if(!isset($_POST['delete'])){
            echo 'dd';
            return;
        };
        return $this->remove((int)$_POST['id']);
    }
}