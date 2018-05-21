<?php
namespace admin\productTransaction;

//产品交易配置
class ProductTransactionSectionConfig {

    public static $sections = [
        book => [
            daysOfPayment=> 3,
            daysOfRefund=> 3
        ]
    ];
}
?>
