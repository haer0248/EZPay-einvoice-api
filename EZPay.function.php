<?php 
// API docuemnt : https://inv.ezpay.com.tw/dw_files/info_api/EZP_INVI_1_2_1.pdf

function addpadding($string, $blocksize = 32){
    $len = strlen($string);
    $pad = $blocksize - ($len % $blocksize);
    $string .= str_repeat(chr($pad) , $pad);
    return $string;
}

function curl_work($url = '', $parameter = ''){
    $curl_options = array(
        CURLOPT_URL => $url,
        CURLOPT_HEADER => false,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_USERAGENT => 'ezPay',
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_SSL_VERIFYPEER => false,
        CURLOPT_SSL_VERIFYHOST => false,
        CURLOPT_POST => '1',
        CURLOPT_POSTFIELDS => $parameter
    );    

    $ch = curl_init();
    curl_setopt_array($ch, $curl_options);
    $result = curl_exec($ch);
    $retcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $curl_error = curl_errno($ch);
    curl_close($ch);

    $return_info = array(
        'url' => $url,
        'sent_parameter' => $parameter,
        'http_status' => $retcode,
        'curl_error_no' => $curl_error,
        'web_info' => $result
    );

    return $return_info;
}

$post_data_array = array(
    'RespondType' => 'JSON',
    'Version' => '1.4',
    'TimeStamp' => '1444963784',
    'TransNum' => '',
    'MerchantOrderNo' => '201409170000001',
    'BuyerName' => '王大品',
    'BuyerUBN' => '54352706',
    'BuyerAddress' => '台北市南港區南港路二段97號8樓',
    'BuyerEmail' => '54352706@pay2go.com',
    'Category' => 'B2B',
    'TaxType' => '1',
    'TaxRate' => '5',
    'Amt' => '490',
    'TaxAmt' => '10',
    'TotalAmt' => '500',
    'CarrierType' => '',
    'CarrierNum' => rawurlencode(''), 
    'LoveCode' => '', 
    'PrintFlag' => 'Y', 
    'ItemName' => '商品一|商品二', 
    'ItemCount' => '1|2', 
    'ItemUnit' => '個|個', 
    'ItemPrice' => '300|100', 
    'ItemAmt' => '300|200', 
    'Comment' => '備註', 
    'CreateStatusTime' => '', 
    'Status' => '1'
);

$post_data_str = http_build_query($post_data_array);
$key = 'abcdefghijklmnopqrstuvwxyzabcdef';
$iv = '1234567891234567';
if (phpversion() > 7){
    $post_data = trim(bin2hex(openssl_encrypt(addpadding($post_data_str) , 'AES-256-CBC', $key, OPENSSL_RAW_DATA | OPENSSL_ZERO_PADDING, $iv)));
}else{
    $post_data = trim(bin2hex(mcrypt_encrypt(MCRYPT_RIJNDAEL_128, $key, addpadding($post_data_str) , MCRYPT_MODE_CBC, $iv)));
}

$url = 'https://inv.ezpay.com.tw/Api/invoice_issue';
$MerchantID = '3622183';
$transaction_data_array = array(
    'MerchantID_' => $MerchantID,
    'PostData_' => $post_data
);

$transaction_data_str = http_build_query($transaction_data_array);
$result = curl_work($url, $transaction_data_str);