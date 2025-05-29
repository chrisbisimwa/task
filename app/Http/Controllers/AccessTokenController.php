<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use Illuminate\View\View;
use App\Models\AccessToken;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use App\Http\Requests\AccessTokenStoreRequest;
use App\Http\Requests\AccessTokenUpdateRequest;

class AccessTokenController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): View
    {
        $this->authorize('view-any', AccessToken::class);

        $search = $request->get('search', '');

        $accessTokens = AccessToken::search($search)
            ->latest()
            ->paginate(5)
            ->withQueryString();

        return view(
            'app.access_tokens.index',
            compact('accessTokens', 'search')
        );
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request): View
    {
        $this->authorize('create', AccessToken::class);

        $employees = Employee::pluck('name', 'id');

        return view('app.access_tokens.create', compact('employees'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(AccessTokenStoreRequest $request): RedirectResponse
    {
        $this->authorize('create', AccessToken::class);

        $validated = $request->validated();

        $accessToken = AccessToken::create($validated);

        return redirect()
            ->route('access-tokens.edit', $accessToken)
            ->withSuccess(__('crud.common.created'));
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request, AccessToken $accessToken): View
    {
        $this->authorize('view', $accessToken);

        return view('app.access_tokens.show', compact('accessToken'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Request $request, AccessToken $accessToken): View
    {
        $this->authorize('update', $accessToken);

        $employees = Employee::pluck('name', 'id');

        return view(
            'app.access_tokens.edit',
            compact('accessToken', 'employees')
        );
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(
        AccessTokenUpdateRequest $request,
        AccessToken $accessToken
    ): RedirectResponse {
        $this->authorize('update', $accessToken);

        $validated = $request->validated();

        $accessToken->update($validated);

        return redirect()
            ->route('access-tokens.edit', $accessToken)
            ->withSuccess(__('crud.common.saved'));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(
        Request $request,
        AccessToken $accessToken
    ): RedirectResponse {
        $this->authorize('delete', $accessToken);

        $accessToken->delete();

        return redirect()
            ->route('access-tokens.index')
            ->withSuccess(__('crud.common.removed'));
    }
}
