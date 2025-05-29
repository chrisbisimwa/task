<?php

namespace App\Http\Controllers\Api;

use App\Models\AccessToken;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Http\Controllers\Controller;
use App\Http\Resources\AccessTokenResource;
use App\Http\Resources\AccessTokenCollection;
use App\Http\Requests\AccessTokenStoreRequest;
use App\Http\Requests\AccessTokenUpdateRequest;

class AccessTokenController extends Controller
{
    public function index(Request $request): AccessTokenCollection
    {
        $this->authorize('view-any', AccessToken::class);

        $search = $request->get('search', '');

        $accessTokens = AccessToken::search($search)
            ->latest()
            ->paginate();

        return new AccessTokenCollection($accessTokens);
    }

    public function store(AccessTokenStoreRequest $request): AccessTokenResource
    {
        $this->authorize('create', AccessToken::class);

        $validated = $request->validated();

        $accessToken = AccessToken::create($validated);

        return new AccessTokenResource($accessToken);
    }

    public function show(
        Request $request,
        AccessToken $accessToken
    ): AccessTokenResource {
        $this->authorize('view', $accessToken);

        return new AccessTokenResource($accessToken);
    }

    public function update(
        AccessTokenUpdateRequest $request,
        AccessToken $accessToken
    ): AccessTokenResource {
        $this->authorize('update', $accessToken);

        $validated = $request->validated();

        $accessToken->update($validated);

        return new AccessTokenResource($accessToken);
    }

    public function destroy(
        Request $request,
        AccessToken $accessToken
    ): Response {
        $this->authorize('delete', $accessToken);

        $accessToken->delete();

        return response()->noContent();
    }
}
