<?php
/*
 * All Response functions is here ...
 */

//Main response generator
function helper_response_main($message=null,$result=null,$error=null,$status=200): \Illuminate\Http\JsonResponse
{
    return response()->json([
        'result' => $result,
        'message' => $message,
        'error' => $error,
    ],$status);
}

//success fetching response
function helper_response_fetch($result=[]): \Illuminate\Http\JsonResponse
{
    return helper_response_main('fetch success !',$result);
}

//success create data response
function helper_response_created($result): \Illuminate\Http\JsonResponse
{
    return helper_response_main('item created success !',$result,'',201);
}

//success updated data response
function helper_response_updated($result): \Illuminate\Http\JsonResponse
{
    return helper_response_main('item updated success !',$result,'',202);
}

//success deleted data response
function helper_response_deleted(): \Illuminate\Http\JsonResponse
{
    return helper_response_main('item deleted success !');
}

//success restore data response
function helper_response_restored(): \Illuminate\Http\JsonResponse
{
    return helper_response_main('item restored success !');
}



//Unauthorized
function helper_response_unauthorized(): \Illuminate\Http\JsonResponse
{
    return helper_response_main('','','Unauthorized',401);

}

//Access Denied
function helper_response_access_denied(): \Illuminate\Http\JsonResponse
{
    return helper_response_main('','','access denied',403);

}

//Custom error message
function helper_response_error($message): \Illuminate\Http\JsonResponse
{
    return helper_response_main(null,null,$message,409);
}




