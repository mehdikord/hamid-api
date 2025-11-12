<?php
/*
 * All Core functions is here ...
 */

function helper_core_code_generator($unique = 1, $count = 10): string
{
    $length = $count - strlen($unique) ;
    $start =1;
    $end = 9;
    for($i=1;$i<$length;$i++){
        $start.=0;
        $end.=9;
    }
    return $unique.random_int($start,$end);
}

function helper_core_get_user_customer_access($customer): array
{

    return $customer->projects()->whereHas('user',function($user){
        $user->where('user_id',auth()->user()->id);
    })->whereHas('project',function($project){
        $project->where('is_active',true);
    })->pluck('id')->toArray();

}

function helper_core_send_post_request($url, $data = []): array
{
    try {
        $client = new \GuzzleHttp\Client();

        $response = $client->post($url, [
            'json' => $data,
            'timeout' => 30,
            'headers' => [
                'Content-Type' => 'application/json',
                'Accept' => 'application/json'
            ]
        ]);

        $statusCode = $response->getStatusCode();
        $body = $response->getBody()->getContents();
        return [
            'success' => true,
            'status_code' => $statusCode,
            'data' => json_decode($body, true) ?? $body,
            'message' => 'Request completed successfully'
        ];

    } catch (\GuzzleHttp\Exception\ClientException $e) {
        $response = $e->getResponse();
        $statusCode = $response->getStatusCode();
        $body = $response->getBody()->getContents();

        return [
            'success' => false,
            'status_code' => $statusCode,
            'data' => null,
            'message' => 'Client error: ' . $e->getMessage(),
            'error_body' => json_decode($body, true) ?? $body
        ];

    } catch (\GuzzleHttp\Exception\ServerException $e) {
        $response = $e->getResponse();
        $statusCode = $response->getStatusCode();
        $body = $response->getBody()->getContents();

        return [
            'success' => false,
            'status_code' => $statusCode,
            'data' => null,
            'message' => 'Server error: ' . $e->getMessage(),
            'error_body' => json_decode($body, true) ?? $body
        ];

    } catch (\GuzzleHttp\Exception\ConnectException $e) {
        return [
            'success' => false,
            'status_code' => 0,
            'data' => null,
            'message' => 'Connection error: ' . $e->getMessage()
        ];

    } catch (\Exception $e) {
        return [
            'success' => false,
            'status_code' => 0,
            'data' => null,
            'message' => 'General error: ' . $e->getMessage()
        ];
    }
}

// Convert Persian numbers to English numbers
function helper_core_convert_persian_to_english_numbers($string): string
{
    $persianNumbers = ['۰', '۱', '۲', '۳', '۴', '۵', '۶', '۷', '۸', '۹'];
    $arabicNumbers = ['٠', '١', '٢', '٣', '٤', '٥', '٦', '٧', '٨', '٩'];
    $englishNumbers = ['0', '1', '2', '3', '4', '5', '6', '7', '8', '9'];

    // Replace Persian numbers
    $string = str_replace($persianNumbers, $englishNumbers, $string);

    // Replace Arabic numbers
    $string = str_replace($arabicNumbers, $englishNumbers, $string);

    return $string;
}

// Format mobile number to standard format (09124435544)
function helper_core_format_mobile_number($phone): string
{
    // Convert Persian/Arabic numbers to English
    $phone = helper_core_convert_persian_to_english_numbers($phone);

    // Remove all non-numeric characters
    $phone = preg_replace('/[^0-9]/', '', $phone);

    // Remove country codes and replace with 0
    if (str_starts_with($phone, '0098')) {
        $phone = '0' . substr($phone, 4);
    } elseif (str_starts_with($phone, '98')) {
        $phone = '0' . substr($phone, 2);
    } elseif (str_starts_with($phone, '+98')) {
        $phone = '0' . substr($phone, 3);
    }elseif (!str_starts_with($phone, '0')) {
        $phone = '0'.$phone;
    }

    return $phone;

}

// Convert Jalali date to Carbon date for use in Eloquent queries
function helper_core_jalali_to_carbon($jalaliDate): \Carbon\Carbon
{
    // Convert Persian/Arabic numbers to English
    $jalaliDate = helper_core_convert_persian_to_english_numbers($jalaliDate);

    // Remove any whitespace
    $jalaliDate = trim($jalaliDate);

    // Determine the separator and format
    $separator = '/';
    if (strpos($jalaliDate, '-') !== false) {
        $separator = '-';
    }

    // Convert Jalali date to Carbon using morilog/jalali package
    $jalalian = \Morilog\Jalali\Jalalian::fromFormat('Y' . $separator . 'm' . $separator . 'd', $jalaliDate);
    return $jalalian->toCarbon();
}









