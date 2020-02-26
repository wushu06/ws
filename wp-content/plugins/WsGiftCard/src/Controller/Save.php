<?php
namespace WS\Controller;
use WS\Model\Egift;

/**
 * Class Save
 * @package WS\Controller
 */
class Save extends Egift
{


    /**
     * @return false|int|string
     */
    public function __construct()
    {
        if(!empty($_POST) && isset($_POST['save'])) {


            $data = array(
                'email' => $_POST['email'],
                'date' => $_POST['date'], //date('Y-m-d', strtotime('2012-08-14')),
                'message' => $_POST['message'],
                'product_name' => $_POST['product_name'],
                'product_price' => $_POST['product_price'],
                'barcode' => $_POST['barcode'],
                'pin' => $_POST['pin']
            );

            return $this->insert($data);
        }

    }
}