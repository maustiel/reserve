<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Equipment;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Inertia\Inertia;
use Inertia\Response;

class EquipmentController extends Controller
{
    public function index(Request $request): Response
    {
        $query = Equipment::query()
            ->with(['category', 'openLoan.user'])
            ->orderBy('inventory_code');

        if ($request->filled('category_id')) {
            $query->where('category_id', $request->integer('category_id'));
        }

        if ($request->filled('condition')) {
            $query->where('condition', $request->string('condition')->toString());
        }

        return Inertia::render('manager/EquipmentIndex', [
            'equipment' => $query->get(),
            'categories' => Category::query()->orderBy('name')->get(),
            'filters' => [
                'category_id' => $request->filled('category_id') ? $request->integer('category_id') : '',
                'condition' => $request->filled('condition') ? $request->string('condition')->toString() : '',
            ],
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'inventory_code' => ['required', 'string', 'max:50', 'unique:equipment,inventory_code'],
            'category_id' => ['required', 'integer', 'exists:categories,id'],
            'name' => ['required', 'string', 'max:255'],
            'condition' => ['required', 'in:good,worn,damaged,out_of_service'],
            'location' => ['required', 'string', 'max:100'],
            'default_accessories' => ['nullable', 'string'],
            'notes' => ['nullable', 'string'],
        ]);

        Equipment::create($validated);

        return redirect()->route('manager.equipment.index');
    }

    public function update(Request $request, Equipment $equipment): RedirectResponse
    {
        $validated = $request->validate([
            'inventory_code' => ['required', 'string', 'max:50', Rule::unique('equipment', 'inventory_code')->ignore($equipment->id)],
            'category_id' => ['required', 'integer', 'exists:categories,id'],
            'name' => ['required', 'string', 'max:255'],
            'condition' => ['required', 'in:good,worn,damaged,out_of_service'],
            'location' => ['required', 'string', 'max:100'],
            'default_accessories' => ['nullable', 'string'],
            'notes' => ['nullable', 'string'],
        ]);

        $equipment->update($validated);

        return redirect()->route('manager.equipment.index');
    }
}
