<?php

namespace App\Http\Controllers\Api;

use App\Helpers\ResponseHandler;
use App\Http\Controllers\Controller;
use App\Http\Resources\TagResource;
use App\Models\Tag;
use Illuminate\Http\Request;

class TagController extends Controller
{
    /**
     * @OA\GET(
     *      path="/api/tags",
     *      operationId="list",
     *      tags={"Tags"},
     *      summary="Return all tags",
     *      security={{"apiKey":{}}},
     * 
     * 
     * 
     *      @OA\Response(
     *          response=200,
     *          description="OK",
     *          ),
     *      @OA\Response(
     *          response=401,
     *          description="Unauthenticated",
     *          ),
     *      @OA\Response(
     *          response=404,
     *          description="Page Not Found"
     *          )
     *      ),
     */

    public function list()
    {
        $tags = Tag::all();

        $tags = TagResource::collection($tags);

        return ResponseHandler::success($tags);
    }
}
