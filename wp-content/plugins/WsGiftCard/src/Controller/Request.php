<?php
namespace WS\Controller;
use WS\Model\Egift;

/**
 * Class Request
 * @package WS\Controller
 */
class Request extends Egift
{
    /**
     * @var Save
     */
    private $save;
    /**
     * @var Delete
     */
    private $delete;

    public function __construct(Save $save, Delete $delete)
    {
        parent::__construct();
        $this->save = $save;
        $this->delete = $delete;
    }


    public function save()
    {
        return $this->save->execute();
    }

    public function delete()
    {
        return $this->delete->execute();
    }

}