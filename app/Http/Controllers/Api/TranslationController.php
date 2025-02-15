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
use Illuminate\Support\Facades\Cache;

class TranslationController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {

        $translationData =  Cache::remember('translations', 60, function () {
            $translations = Translation::query();

            // Filter the translations
            $translations->when(request('locale'), function ($query) {
                return $query->locale(request('locale'));
            })->when(request('content'), function ($query) {
                return $query->search(request('content'));
            })->when(request('keys'), function ($query) {
                return $query->keys(request('keys'));
            })->when(request('tags'), function ($query) {
                return $query->tags(request('tags'));
            });

            $translations = $translations->paginate(20);

            return $translations;
        });


        $translationData = TranslationResource::collection($translationData)->response()->getData(true);

        return ResponseHandler::success($translationData);
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

            Cache::forget('translations');

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

            Cache::forget('translations');

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

    public function export()
    {
        try {

            Cache::forget('translations');

            $translations = DB::table('translations')
                ->select(
                    'translations.key',
                    'translations.content',
                    'translations.locale',
                    DB::raw('GROUP_CONCAT(tags.name) as tags')
                )
                ->leftJoin('translation_tags', 'translations.id', '=', 'translation_tags.translation_id')
                ->leftJoin('tags', 'translation_tags.tag_id', '=', 'tags.id')
                ->groupBy('translations.id', 'translations.key', 'translations.content', 'translations.locale')
                ->cursor();

            $result = [];

            foreach ($translations as $t) {

                $result[$t->locale][$t->key] = [
                    'content' => $t->content,
                    'tags'    => $t->tags ? explode(',', $t->tags) : [],
                ];
            }

            return ResponseHandler::success($result);
        } catch (Exception $e) {
            return ResponseHandler::failure($e->getMessage());
        }
    }
}
