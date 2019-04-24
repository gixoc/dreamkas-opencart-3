<?php
class ControllerExtensionModuleDreamkas extends Controller {

    public function check($data) {}

    public function index($route, $data) {
        if (isset($data[0]) && !empty($data[0])) {
            $this->load->language('extension/module/dreamkas');
            $order_id = $data[0];
            $query = $this->db->query("SELECT order_status_id FROM `" . DB_PREFIX . "order` o WHERE o.order_id = '" . (int) $order_id . "'");
            $status = $query->row['order_status_id'];
            $query = $this->db->query("SELECT payment_code FROM `" . DB_PREFIX . "order` o WHERE o.order_id = '" . (int) $order_id . "'");
            $payment_code = $query->row['payment_code'];

            if ($status == $this->config->get('dreamkas_paid_order') &&
                in_array($payment_code, $this->config->get('dreamkas_payments_ids')))
            {
                $this->load->model('checkout/order');
                $order_info = $this->model_checkout_order->getOrder($order_id);
                $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "order_product WHERE order_id = '" . (int) $order_id . "'");
                $products = $query->rows;
                $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "order_total WHERE order_id = '" . (int) $order_id . "' AND code = 'shipping' ORDER BY sort_order");
                $shipping = $query->rows;
                $tax_type = $this->config->get('dreamkas_tax_type');
                $tax_sum = 0;
                $items = array();
                foreach ($products as $product) {
                    $query = $this->db->query("SELECT dk_tax_type FROM " . DB_PREFIX . "product WHERE product_id = '" . (int) $product['product_id'] . "'");
                    $dk_tax_type = $query->row;
                    $product_tax_type = empty($dk_tax_type['dk_tax_type']) ? $tax_type : $dk_tax_type['dk_tax_type'];
                    $items[] = array(
                        "name" => $product['name'],
                        "type" => "COUNTABLE",
                        "quantity" => $product['quantity'],
                        "price" => ($product['price'] + $product['tax']) * 100,
                        "priceSum" => ($product['total'] + $product['tax'] * $product['quantity']) * 100,
                        "tax" => "$product_tax_type",
                        "taxSum" => 0, //$product['tax']*100*$product['quantity']
                    );
                    $tax_sum += $product['tax'] * $product['quantity'];
                }

                if (!empty($shipping)) {
                    $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "order_total WHERE order_id = '" . (int) $order_id . "' AND code = 'tax' ORDER BY sort_order");
                    $tax_total = reset($query->rows);
                    if (!empty($tax_total['value'])) {
                        $shipping_tax = $tax_total['value'] - $tax_sum;
                    } else {
                        $shipping_tax = 0;
                    }
                    foreach ($shipping as $_shipping) {
                        if ($_shipping['value'] > 0) {
                            $items[] = array(
                                "name" => 'Доставка',
                                "type" => "COUNTABLE",
                                "quantity" => 1,
                                "price" => ($_shipping['value'] + $shipping_tax) * 100,
                                "priceSum" => ($_shipping['value'] + $shipping_tax) * 100,
                                "tax" => "$tax_type",
                                "taxSum" => 0, //$shipping_tax*100
                            );
                        }
                    }
                }

                $request = array(
                    "deviceId" => $this->config->get('dreamkas_device_id'),
                    "type" => "SALE",
                    "timeout" => 180,
                    "taxMode" => $this->config->get('dreamkas_tax_mode'),
                    "positions" => $items,
                    "payments" => array(
                        array(
                            "sum" => $order_info['total'] * 100,
                            "type" => "CASHLESS",
                        ),
                    ),
                    "attributes" => array(
                        "email" => $order_info['email'],
                        "phone" => $order_info['telephone'], //"+71239994499"
                    ),
                    "total" => array(
                        "priceSum" => $order_info['total'] * 100,
                    ),
                );

                $ch = curl_init();
                $access_token = $this->config->get('dreamkas_access_token');
                curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                    "Content-Type: application/json",
                    "Authorization: Bearer $access_token",
                ));
                curl_setopt($ch, CURLOPT_URL, "https://kabinet.dreamkas.ru/api/receipts");
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($ch, CURLOPT_HEADER, false);
                curl_setopt($ch, CURLOPT_POST, true);
                curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($request));
                $response = curl_exec($ch);
                curl_close($ch);

                if (!empty($response)) {
                    $response = json_decode($response, true);
                    if ((substr($response['status'], 0, 1) == 4)) {
                        $this->log->write('Dreamkas debug: ' . json_encode($response));
                    } else {
                        $dk_date = empty($response['createdAt']) ? $response['completedAt'] : $response['createdAt'];
                        $query = $this->db->query("SELECT order_id FROM " . DB_PREFIX . "dreamkas WHERE order_id = '" . (int) $order_id . "'");
                        $exist_order_id = $query->rows;
                        if (empty($exist_order_id)) {
                            $this->db->query("INSERT INTO `" . DB_PREFIX . "dreamkas` SET `order_id` = '" . (int) $order_id . "', `dk_id` = '" . $response['id'] . "', `dk_date` ='" . $dk_date . "', `dk_status` = '" . $response['status'] . "'");
                        } else {
                            $this->db->query("UPDATE `" . DB_PREFIX . "dreamkas` SET `order_id` = '" . (int) $order_id . "', `dk_id` = '" . $response['id'] . "', `dk_date` ='" . $dk_date . "', `dk_status` = '" . $response['status'] . "' WHERE order_id = '" . (int) $order_id . "'");
                        }
                    }
                }
            }
        }
    }

}
