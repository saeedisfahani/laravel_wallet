<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Payment Language Lines
    |--------------------------------------------------------------------------
    |
    |
    */

    'enumes' => [],
    'messages' => [
        'create_successfull' => 'پرداخت با موفقیت ایجاد شد',
        'delete_successfull' => 'پرداخت با موفقیت حذف شد',
        'approve_successfull' => 'پرداخت با موفقیت تایید شد',
        'reject_successfull' => 'پرداخت با موفقیت رد شد',
        'found_successfull' => 'پرداخت با موفقیت پیدا شد'
    ],
    'validations' => [],
    'errors' => [
        'not_found' => 'پرداخت پیدا نشد',
        'not_pending' => 'پرداخت در وضعیت معلق نیست',
        'payment_creation_time_limit' => 'یک پرداخت در واحد :currency در کمتر از :minute دقیقه پیش برای این کاربر ثبت شده است, لطفا بعدا دوباره تلاش کنید',
        'currency_key_not_found_or_deactive' => 'واحد پول ارسال شده پیدا نشد یا غیر فعال است',
        'payment_cant_delete' => 'امکان حذف پرداخت وجود ندارد'
    ],

];