<?php
// php artisan make:controller Admin/DocumentController --resource

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Document;
use App\Models\Employee;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class DocumentController extends Controller
{
    public function index(Request $request)
    {
        $query = Document::with(['employee.user']);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhereHas('employee', function ($empQuery) use ($search) {
                        $empQuery->where('employee_name', 'like', "%{$search}%");
                    });
            });
        }

        if ($request->filled('employee')) {
            $query->where('employee_id', $request->employee);
        }

        if ($request->filled('expiring_soon')) {
            $query->where('expiry_date', '<=', now()->addDays(30))
                ->where('expiry_date', '>=', now());
        }

        $documents = $query->paginate($request->get('per_page', 15));

        return inertia('Admin/Documents/Index', [
            'documents' => $documents,
            'employees' => Employee::with('user')->get(),
            'filters' => $request->only(['search', 'employee', 'expiring_soon', 'per_page']),
        ]);
    }

    public function create()
    {
        return inertia('Admin/Documents/Create', [
            'employees' => Employee::with('user')->get(),
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'title' => 'required|string|max:255',
            'file' => 'required|file|mimes:jpg,jpeg,png,pdf,doc,docx|max:2048',
            'expiry_date' => 'nullable|date|after:today',
        ]);

        if ($request->hasFile('file')) {
            $validated['file_path'] = $request->file('file')->store('documents', 'public');
        }

        unset($validated['file']);
        Document::create($validated);

        return redirect()->route('admin.documents.index')
            ->with('success', 'Document uploaded successfully.');
    }

    public function show(Document $document)
    {
        $document->load(['employee.user']);

        return inertia('Admin/Documents/Show', [
            'document' => $document,
        ]);
    }

    public function edit(Document $document)
    {
        return inertia('Admin/Documents/Edit', [
            'document' => $document,
            'employees' => Employee::with('user')->get(),
        ]);
    }

    public function update(Request $request, Document $document)
    {
        $validated = $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'title' => 'required|string|max:255',
            'file' => 'nullable|file|mimes:jpg,jpeg,png,pdf,doc,docx|max:2048',
            'expiry_date' => 'nullable|date',
        ]);

        if ($request->hasFile('file')) {
            if ($document->file_path) {
                Storage::disk('public')->delete($document->file_path);
            }
            $validated['file_path'] = $request->file('file')->store('documents', 'public');
        }

        unset($validated['file']);
        $document->update($validated);

        return redirect()->route('admin.documents.index')
            ->with('success', 'Document updated successfully.');
    }

    public function destroy(Document $document)
    {
        if ($document->file_path) {
            Storage::disk('public')->delete($document->file_path);
        }

        $document->delete();

        return redirect()->route('admin.documents.index')
            ->with('success', 'Document deleted successfully.');
    }

    public function download(Document $document)
    {
        if (!$document->file_path || !Storage::disk('public')->exists($document->file_path)) {
            abort(404, 'File not found');
        }

        return Storage::disk('public')->download($document->file_path, $document->title);
    }
}