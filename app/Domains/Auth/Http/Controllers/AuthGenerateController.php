<?php

namespace App\Domains\Auth\Http\Controllers;

use App\Domains\Auth\Businesses\AuthGenerateBusiness;
use App\Http\Controllers\BaseController;
use Illuminate\Http\Request;
use ResponseJson\ResponseJson;

class AuthGenerateController extends BaseController
{
    private $authGenerateBusiness;
    private $response;

    /**
     * constructor
     * @param AuthGenerateBusiness $authGenerateBusiness
     * @param ResponseJson $response
     * @return void
     */
    public function __construct(
        AuthGenerateBusiness $authGenerateBusiness,
        ResponseJson $response
    ) {
        $this->authGenerateBusiness = $authGenerateBusiness;
        $this->response = $response;
    }

    /**
     * process the request
     * @param Request $request
     * @return JsonObject
     */
    public function process(
        Request $request
    ) {
        $data = $request->only($request->validFields);
        $dataResponse = $this->authGenerateBusiness->process(
            $data
        );

        $result = $this->response->response(
            $request->requestId,
            $request->startProfile,
            $request->jwtToken,
            $dataResponse
        );

        return response()->json($result, 200);
    }
}
