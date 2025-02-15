<?php

namespace App\Http\Controllers\Api;

use Exception;
use App\Models\Translation;
use App\Helpers\ResponseHandler;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Http\Resources\TranslationResource;
use App\Http\Requests\StoreTranslationRequest;
use App\Http\Requests\UpdateTranslationRequest;

class TranslationController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $translations = Translation::all();
        

        return ResponseHandler::success(TranslationResource::collection($translations));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreTranslationRequest $request)
    {
        try {

            DB::beginTransaction();

            $translation = Translation::create($request->all());
            $translation->translationTag()->create(['tag_id' => $request->tag_id]);

            DB::commit();

            return ResponseHandler::success(new TranslationResource($translation));
        } catch (Exception $e) {
            DB::rollBack();
            return ResponseHandler::failure($e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Translation $translation)
    {
        return new TranslationResource($translation);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateTranslationRequest $request, Translation $translation)
    {
        try {

            DB::beginTransaction();

            $translation->update($request->all());

            if ($request->tag_id) {
                $translation->translationTag()->update(['tag_id' => $request->tag_id]);
            }

            DB::commit();

            return ResponseHandler::success(new TranslationResource($translation));
        } catch (Exception $e) {

            DB::rollBack();
            return ResponseHandler::failure($e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Translation $translation)
    {

        try {

            DB::beginTransaction();
            $translation->delete();
            $translation->translationTag()->delete();
            DB::commit();

            return ResponseHandler::success();
        } catch (Exception $e) {
            DB::rollBack();
            return ResponseHandler::failure($e->getMessage());
        }
    }
}
