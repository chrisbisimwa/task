<?php

namespace App\Http\Controllers\Api;

use App\Models\Employee;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\AccessTokenResource;
use App\Http\Resources\AccessTokenCollection;

class EmployeeAccessTokensController extends Controller
{
    public function index(
        Request $request,
        Employee $employee
    ): AccessTokenCollection {
        $this->authorize('view', $employee);

        $search = $request->get('search', '');

        $accessTokens = $employee
            ->accessTokens()
            ->search($search)
            ->latest()
            ->paginate();

        return new AccessTokenCollection($accessTokens);
    }

    public function store(
        Request $request,
        Employee $employee
    ): AccessTokenResource {
        $this->authorize('create', AccessToken::class);

        $validated = $request->validate([
            'token' => [
                'required',
                'unique:access_tokens,token',
                'max:255',
                'string',
            ],
            'expires_at' => ['required', 'date'],
        ]);

        $accessToken = $employee->accessTokens()->create($validated);

        return new AccessTokenResource($accessToken);
    }
}
