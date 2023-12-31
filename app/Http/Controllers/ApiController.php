<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ApiController extends Controller
{
    /**
     * @OA\Info(
     *     title="DetskySad.com API",
     *     version="1.0.0",
     *     description="",
     *     @OA\Contact(
     *         email="info@detskysad.com"
     *     ),
     *     @OA\License(
     *         name="Лицензия",
     *         url="http://www.example.com/license"
     *     )
     * )
     * @OA\Get(
     *     path="/v1/test",
     *     summary="Test endpoint",
     *     tags={"Test"},
     *     @OA\Response(
     *         response="200",
     *         description="Successful response",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="key", type="string", example="value"),
     *             @OA\Property(property="another_key", type="string", example="another_value"),
     *         )
     *     )
     * )
     */
    public function test(): \Illuminate\Http\JsonResponse
    {
        $data = [
            'key' => 'value',
            'another_key' => 'another_value',
        ];

        return response()->json($data);
    }
}
