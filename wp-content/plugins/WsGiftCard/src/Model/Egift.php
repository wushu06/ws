<?php
namespace WS\Model;

/**
 * Class Egift
 * @package WS\Model
 */
class Egift
{
    /**
     *
     */
    CONST TABLE_NAME = 'egits_emails';

    /**
     * @var \wpdb
     */
    protected $wpdb;

    /**
     * Egift constructor.
     */
    public function __construct()
    {
        global $wpdb;
        $this->wpdb = $wpdb;
    }

    /**
     * @param $data
     * @return int
     */
    public function insert($data)
    {
        $table = $this->wpdb->prefix.self::TABLE_NAME;
        $format = array('%s','%s','%s','%s','%s','%s','%s');
        $this->wpdb->insert($table,$data,$format);
        return $this->wpdb->insert_id;
    }

    /**
     * @param $id
     * @return false|int
     */
    public function remove($id)
    {
        $table = $this->wpdb->prefix.self::TABLE_NAME;
       return  $this->wpdb->delete($table, array( 'id' => $id ));
    }
}