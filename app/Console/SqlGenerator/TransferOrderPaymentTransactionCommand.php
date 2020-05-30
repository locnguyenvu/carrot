<?php
namespace App\Console\SqlGenerator;

use Tikivn\Oms\Order\Model\Order;
use Ramsey\Uuid\Uuid;

class TransferOrderPaymentTransactionCommand extends \Carrot\Console\Command {

    protected static $pattern = 'sql-generator:transfer-order-payment-transaction {originalOrder} {receivedOrder}';

    private $orderRepository;

    protected function init() : void
    {
        $this->orderRepository = app('orderRepository');
    }

    public function exec($originalOrderCode, $receivedOrderCode) {
        $originalOrder = $this->orderRepository->findByCode($originalOrderCode);
        $receivedOrder = $this->orderRepository->findByCode($receivedOrderCode);

        if($originalOrder->getProperty('payment.status') != 'success') {
            self::printErrorAndExit(sprintf('Đơn hàng #%s chưa ghi nhận thanh toán thành công', $originalOrderCode));
        };
        if($receivedOrder->getProperty('payment.status') == 'success') {
            self::printErrorAndExit(sprintf('Đơn hàng #%s đã ghi nhận thanh toán thành công', $receivedOrderCode));
        };
        if ($originalOrder->getProperty('payment.method') != $receivedOrder->getProperty('payment.method')) {
            self::printErrorAndExit(sprintf('Đơn hàng #%s & #%s có phương thức thanh toán khác nhau', $originalOrderCode, $receivedOrderCode));
        };
        if (intval($originalOrder->getGrandTotal()) < intval($receivedOrder->getGrandTotal())) {
            self::printErrorAndExit(sprintf('Đơn hàng #%s có giá trị thanh toán nhỏ hơn #%s', $originalOrderCode, $receivedOrderCode));
        }


        switch($originalOrder->getProperty('payment.method')) {
            case 'momo':
                $sql = self::momoQuery($originalOrder, $receivedOrder);
                break;
            case 'zalopay':
                $sql = self::zaloPayQuery($originalOrder, $receivedOrder);
                break;
            case 'cybersource':
                $sql = self::cybersourceQuery($originalOrder, $receivedOrder);
                break;
            case 'pay123':
                $sql = self::pay123Query($originalOrder, $receivedOrder);
                break;
        }

        echo PHP_EOL.'==== Start of SQL ===='.PHP_EOL.PHP_EOL;
        echo app('console_color')->apply(['bold', 'yellow'], $sql);
        echo PHP_EOL.PHP_EOL.'==== End of SQL ===='.PHP_EOL;
    }


    public static function momoQuery($originalOrder, $receivedOrder) : string {
        $successTransaction = self::getFirstSuccessTransaction($originalOrder);
        if (is_null($successTransaction)) {
            self::printErrorAndExit(sprintf('Không tìm thấy transaction thành công của đơn hàng #%s', $originalOrder->getCode()));
        }

        $receivedTransactionId = sprintf('%sMM-%s-%d', date('ymd'), $receivedOrder->getCode(), strtotime('now'));

        return <<<EOD
INSERT INTO `sales_order_transaction_momo` (`transaction_id`, `order_payment_id`, `order_code`, `mm_transaction`, `response_code`, `type`, `data_request`, `callback_response`, `created_at`, `updated_at`)
SELECT 
    '{$receivedTransactionId}',
    {$receivedOrder->getProperty('payment.payment_id')},
    {$receivedOrder->getCode()},
    `mm_transaction`,
    `response_code`,
    `type`,
    `data_request`,
    `callback_response`,
    NOW(), NOW()
FROM `sales_order_transaction_momo` WHERE id = {$successTransaction['transaction_id']};
EOD;
    }

    public static function zaloPayQuery($originalOrder, $receivedOrder) : string {
        $successTransaction = self::getFirstSuccessTransaction($originalOrder);
        if (is_null($successTransaction)) {
            self::printErrorAndExit(sprintf('Không tìm thấy transaction thành công của đơn hàng #%s', $originalOrder->getCode()));
        }
        $receivedTransactionId = sprintf('%sZP-%s-%d', date('ymd'), $receivedOrder->getCode(), strtotime('now'));

        return <<<EOD
INSERT INTO `sales_order_transaction_zalopay` (`transaction_id`, `order_payment_id`, `order_code`, `zptransid`, `response_code`, `response_message`, `type`, `callback_response`, `statusbyapp_response`, `created_at`, `updated_at`)
SELECT
    '{$receivedTransactionId}',
    {$receivedOrder->getProperty('payment.payment_id')},
    {$receivedOrder->getCode()},
    `zptransid`,
    `response_code`,
    `response_message`,
    `type`,
    `callback_response`,
    `statusbyapp_response`,
    NOW(), NOW()
FROM `sales_order_transaction_zalopay` WHERE id = {$successTransaction['transaction_id']};
EOD;
    }

    public static function cybersourceQuery($originalOrder, $receivedOrder) : string {
        $successTransaction = self::getFirstSuccessTransaction($originalOrder);
        if (is_null($successTransaction)) {
            self::printErrorAndExit(sprintf('Không tìm thấy transaction thành công của đơn hàng #%s', $originalOrder->getCode()));
        }
        $receivedTransactionId = Uuid::uuid4();

        return <<<EOD
INSERT INTO `sales_order_transaction_cybersource` (`transaction_id`, `order_payment_id`, `order_code`, `response_transaction_id`, `original_reference_number`, `reason_code`, `message`, `card_number`, `card_name_holder`, `response_data`, `request_data`, `merchant_supplier`, `created_at`, `updated_at`)
SELECT
    '{$receivedTransactionId}',
    {$receivedOrder->getProperty('payment.payment_id')},
    {$receivedOrder->getCode()},
    `response_transaction_id`,
    `original_reference_number`,
    `reason_code`,
    `message`,
    `card_number`,
    `card_name_holder`,
    `response_data`,
    `request_data`,
    `merchant_supplier`,
    NOW(), NOW()
FROM `sales_order_transaction_cybersource` WHERE id = {$successTransaction['transaction_id']};
EOD;
    }

    public static function pay123Query($originalOrder, $receivedOrder) : string {
        $successTransaction = self::getFirstSuccessTransaction($originalOrder);
        if (is_null($successTransaction)) {
            self::printErrorAndExit(sprintf('Không tìm thấy transaction thành công của đơn hàng #%s', $originalOrder->getCode()));
        }
        $receivedTransactionId = sprintf('%sZP-%s-%d', date('ymd'), $receivedOrder->getCode(), uniqid());

        return <<<EOD
INSERT INTO `sales_order_transaction_123pay` (`merch_ref`, `order_payment_id`, `order_code`, `code`, `status`, `status_message`, `type`, `reponse_data`, `bank_code`, `info_bank`, `merchant_id`, `created_at`, `updated_at`)
SELECT
    '{$receivedTransactionId}',
    {$receivedOrder->getProperty('payment.payment_id')},
    {$receivedOrder->getCode()},
    `code`,
    `status`,
    `status_message`,
    `type`,
    `reponse_data`,
    `bank_code`,
    `info_bank`,
    `merchant_id`,
    NOW(), NOW()
FROM `sales_order_transaction_123pay` WHERE id = {$successTransaction['transaction_id']};
EOD;
    }

    public static function getFirstSuccessTransaction($originalOrder) : ?array {
        $transactions = $originalOrder->getTransactions();
        foreach($transactions as $trans) {
            if ($trans['state'] == 'success') {
                return $trans;
            }
        }
        return null;
    }

}
