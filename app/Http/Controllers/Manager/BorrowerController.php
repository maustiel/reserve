<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Inertia\Inertia;
use Inertia\Response;

class BorrowerController extends Controller
{
    public function index(): Response
    {
        $borrowers = User::query()
            ->where('role', 'borrower')
            ->with(['loans.equipment'])
            ->withCount('loans')
            ->orderBy('name')
            ->get();

        return Inertia::render('manager/BorrowersIndex', [
            'borrowers' => $borrowers,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'phone' => ['required', 'string', 'max:50'],
            'borrower_type' => ['required', 'in:student,staff,other'],
            'section_or_service' => ['required', 'string', 'max:255'],
            'password' => ['required', 'string', 'min:8'],
        ]);

        User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'],
            'borrower_type' => $validated['borrower_type'],
            'section_or_service' => $validated['section_or_service'],
            'role' => 'borrower',
            'password' => Hash::make($request->string('password')->toString()),
        ]);

        return redirect()->route('manager.borrowers.index');
    }
}
