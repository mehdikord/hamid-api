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









