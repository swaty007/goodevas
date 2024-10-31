<?php

namespace Brackets\CraftablePro\Http\Controllers;

use Brackets\CraftablePro\Http\Requests\Media\DestroyMediaRequest;
use Brackets\CraftablePro\Http\Requests\Media\UploadMediaRequest;
use Brackets\CraftablePro\Models\UnassignedMedia;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller as BaseController;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class UnassignedMediaController extends BaseController
{
    use AuthorizesRequests;
    use DispatchesJobs;
    use ValidatesRequests;

    /**
     * @param UploadMediaRequest $request
     * @return JsonResponse
     */
    public function upload(UploadMediaRequest $request): JsonResponse
    {
        if ($request->has('default')) {
            $media = UnassignedMedia::create();

            return response()->json(['media' => $media->getFirstMedia('default')], 200);
        }

        return response()->json(___('craftable-pro', 'File not provided'), 422);
    }

    /**
     * @param DestroyMediaRequest $request
     * @param $id
     * @return JsonResponse
     */
    public function destroy(DestroyMediaRequest $request, $id): JsonResponse
    {
        $media = Media::findOrFail($id);

        $media->delete();

        return response()->json(___('craftable-pro', 'Successfully deleted'), 200);
    }
}
