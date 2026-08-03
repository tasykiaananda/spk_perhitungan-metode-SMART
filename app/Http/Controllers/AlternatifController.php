<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Repositories\Contracts\AlternatifRepositoryInterface;
use App\Models\ActivityLog;

class AlternatifController extends Controller
{
    protected $alternatifRepo;

    public function __construct(AlternatifRepositoryInterface $alternatifRepo)
    {
        $this->alternatifRepo = $alternatifRepo;
    }

    public function index()
    {
        $alternatifs = $this->alternatifRepo->all();
        return view('admin.supplier.index', compact('alternatifs'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama' => 'required|string|max:255',
        ]);

        $alternatif = $this->alternatifRepo->create($request->all());

        ActivityLog::log("Menambahkan supplier baru: {$alternatif->nama}");

        return redirect()->route('admin.supplier.index')->with('success', 'Supplier berhasil ditambahkan!');
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'nama' => 'required|string|max:255',
        ]);

        $alternatif = $this->alternatifRepo->update($id, $request->only(['nama']));

        ActivityLog::log("Mengubah nama supplier ID {$id} menjadi: {$alternatif->nama}");

        return redirect()->route('admin.supplier.index')->with('success', 'Supplier berhasil diperbarui!');
    }

    public function destroy($id)
    {
        $alternatif = $this->alternatifRepo->find($id);
        $this->alternatifRepo->delete($id);

        ActivityLog::log("Menghapus supplier: {$alternatif->nama} (ID: {$id})");

        return redirect()->route('admin.supplier.index')->with('success', 'Supplier berhasil dihapus!');
    }

    public function report()
    {
        $alternatifs = $this->alternatifRepo->all();
        ActivityLog::log("Mencetak laporan daftar supplier");
        return view('admin.supplier.report', compact('alternatifs'));
    }
}
