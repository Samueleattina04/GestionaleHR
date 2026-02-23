<?php

namespace App\Http\Controllers;

use App\Models\Document;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class DocumentController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();
        $query = Document::with(['user', 'uploader']);

        if ($user->isEmployee()) {
            $query->where('user_id', $user->id);
        }

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        if ($request->filled('user_id') && !$user->isEmployee()) {
            $query->where('user_id', $request->user_id);
        }

        $documents = $query->orderBy('created_at', 'desc')->paginate(20)->withQueryString();
        return view('documents.index', compact('documents'));
    }

    public function create()
    {
        $employees = User::where('status', 'active')->orderBy('surname')->get();
        return view('documents.create', compact('employees'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'title' => 'required|string|max:255',
            'type' => 'required|in:contract,payslip,certificate,id_document,tax_document,medical,training,other',
            'file' => 'required|file|max:20480',
            'description' => 'nullable|string',
            'expiry_date' => 'nullable|date',
            'is_private' => 'boolean',
        ]);

        $file = $request->file('file');
        $path = $file->store('documents/' . $validated['user_id'], 'public');

        Document::create([
            'user_id' => $validated['user_id'],
            'title' => $validated['title'],
            'type' => $validated['type'],
            'file_path' => $path,
            'file_name' => $file->getClientOriginalName(),
            'mime_type' => $file->getMimeType(),
            'file_size' => $file->getSize(),
            'description' => $validated['description'] ?? null,
            'uploaded_by' => auth()->id(),
            'expiry_date' => $validated['expiry_date'] ?? null,
            'is_private' => $request->boolean('is_private'),
        ]);

        return redirect()->route('documents.index')->with('success', 'Documento caricato con successo.');
    }

    public function download(Document $document)
    {
        $user = auth()->user();
        if ($user->isEmployee() && $document->user_id !== $user->id) abort(403);
        return Storage::disk('public')->download($document->file_path, $document->file_name);
    }

    public function destroy(Document $document)
    {
        Storage::disk('public')->delete($document->file_path);
        $document->delete();
        return redirect()->route('documents.index')->with('success', 'Documento eliminato.');
    }
}
