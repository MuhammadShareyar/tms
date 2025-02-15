<?php

namespace App\Http\Controllers;


/**
 * @OA\Info(
 *     version="1.0",
 *     title="API Documentation for TMS",
 * ),
 * 
 * @OA\SecurityScheme(
 *     type="apiKey",
 *     in="header",
 *     securityScheme="apiKey",
 *     scheme="bearer",
 *     name="Authorization"
 * )
 */


abstract class Controller {}
