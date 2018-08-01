<?php
namespace admin\productTransaction;

//产品交易配置
class ProductTransactionSectionConfig {

    public static $sections = [
        book => [
            daysOfPayment => 3,
            daysOfRefund => 3
        ],
        video => [
            daysOfPayment => 1000, //TODO: so far we don't have a forever days of payment mechanism
            daysOfRefund => 3
        ]
    ];
}
?>
