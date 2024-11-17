<?php

namespace App\Http\Controllers;

use App\Models\Retur;
use App\Models\ReturDetail;
use App\Models\Penjualan;
use App\Models\Produk;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use PDF; // Pastikan Anda telah menginstal package PDF, misalnya barryvdh/laravel-dompdf
use Carbon\Carbon;

class ReturController extends Controller
{
    /**
     * Menampilkan daftar Retur.
     */
    public function index()
    {
        return view('retur.index');
    }

    /**
     * Menyediakan data untuk DataTables.
     */
    public function data()
{
    \Log::info('Route retur.data is being accessed');

    $retur = Retur::with('penjualan.member')->orderBy('id_retur', 'desc')->get();

    return datatables()
        ->of($retur)
        ->addIndexColumn()
        ->addColumn('tanggal_retur', function ($retur) {
            return \Carbon\Carbon::parse($retur->tanggal_retur)->translatedFormat('d F Y');
        })
        ->addColumn('total_retur', function ($retur) {
            return 'Rp. ' . number_format($retur->total_retur, 0, ',', '.');
        })
        ->addColumn('nilai_neto', function ($retur) {
            return 'Rp. ' . number_format($retur->nilai_neto, 0, ',', '.');
        })
        ->addColumn('aksi', function ($retur) {
            return '
                <div class="btn-group">
                    <a href="'. route('retur.show', $retur->id_retur) .'" class="btn btn-xs btn-info btn-flat"><i class="fa fa-eye"></i></a>
                    <button onclick="deleteRetur(`'. route('retur.destroy', $retur->id_retur) .'`)" class="btn btn-xs btn-danger btn-flat"><i class="fa fa-trash"></i></button>
                    <a href="'. route('retur.pdf', $retur->id_retur) .'" class="btn btn-xs btn-success btn-flat" target="_blank"><i class="fa fa-print"></i></a>
                </div>
            ';
        })
        ->rawColumns(['aksi'])
        ->make(true);
}



    /**
     * Menampilkan form untuk membuat Retur baru.
     */
    public function create()
    {
        // Mengambil penjualan dalam 1 tahun terakhir
        $penjualan = Penjualan::with('penjualanDetail.produk')
            ->whereDate('created_at', '>=', now()->subYear())
            ->get();

        return view('retur.create', compact('penjualan'));
    }

    /**
     * Menyimpan Retur baru ke database.
     */
    public function store(Request $request)
    {
        // Validasi data
        $request->validate([
            'id_penjualan' => 'required|exists:penjualan,id_penjualan',
            'tanggal_retur' => 'required|date',
            'retur_detail.*.id_produk' => 'required|exists:produk,id_produk',
            'retur_detail.*.jumlah_retur' => 'required|integer|min:1',
            'retur_detail.*.harga_retur' => 'required|numeric|min:2000000|max:5000000',
            'retur_detail.*.alasan_retur' => 'required|string|max:255',
        ]);

        DB::beginTransaction();
        try {
            // Buat Retur
            $retur = Retur::create([
                'id_penjualan' => $request->id_penjualan,
                'tanggal_retur' => $request->tanggal_retur,
                'total_retur' => 0, // Akan dihitung nanti
                'nilai_neto' => 0, // Akan dihitung nanti
            ]);

            $total_retur = 0;

            foreach ($request->retur_detail as $detail) {
                // Validasi jumlah retur tidak melebihi jumlah terjual
                $penjualanDetail = $retur->penjualan->penjualanDetail()
                    ->where('id_produk', $detail['id_produk'])
                    ->first();

                if (!$penjualanDetail) {
                    throw new \Exception('Produk tidak ditemukan dalam penjualan.');
                }

                $total_retur_sekarang = ReturDetail::where('id_retur', $retur->id_retur)
                    ->where('id_produk', $detail['id_produk'])
                    ->sum('jumlah_retur');

                if (($total_retur_sekarang + $detail['jumlah_retur']) > $penjualanDetail->jumlah) {
                    throw new \Exception('Jumlah retur untuk produk ' . $penjualanDetail->produk->nama_produk . ' melebihi jumlah terjual.');
                }

                // Buat Retur Detail
                ReturDetail::create([
                    'id_retur' => $retur->id_retur,
                    'id_produk' => $detail['id_produk'],
                    'jumlah_retur' => $detail['jumlah_retur'],
                    'harga_retur' => $detail['harga_retur'],
                    'alasan_retur' => $detail['alasan_retur'],
                    'subtotal_retur' => $detail['jumlah_retur'] * $detail['harga_retur'],
                ]);

                // Update Stok Produk
                $produk = Produk::find($detail['id_produk']);
                $produk->stok += $detail['jumlah_retur'];
                $produk->save();

                // Tambahkan ke total_retur
                $total_retur += $detail['jumlah_retur'] * $detail['harga_retur'];
            }

            // Hitung nilai neto
            $total_penjualan = $retur->penjualan->total_harga;
            $nilai_neto = $total_penjualan - $total_retur;

            // Update total_retur dan nilai_neto
            $retur->update([
                'total_retur' => $total_retur,
                'nilai_neto' => $nilai_neto,
            ]);

            DB::commit();

            return redirect()->route('retur.index')->with('success', 'Retur berhasil dibuat.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => $e->getMessage()])->withInput();
        }
    }

    /**
     * Menampilkan detail Retur.
     */
    public function show(Retur $retur)
    {
        $retur->load('penjualan.member', 'returDetail.produk');
        return view('retur.show', compact('retur'));
    }

    /**
     * Menghapus Retur.
     */
    public function destroy(Retur $retur)
    {
        DB::beginTransaction();
        try {
            // Kembalikan stok
            foreach ($retur->returDetail as $detail) {
                $produk = Produk::find($detail->id_produk);
                $produk->stok -= $detail->jumlah_retur;
                $produk->save();
            }

            // Hapus Retur Detail dan Retur
            $retur->returDetail()->delete();
            $retur->delete();

            DB::commit();
            return response()->json(['success' => 'Retur berhasil dihapus.']);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => 'Gagal menghapus retur.'], 500);
        }
    }

    /**
     * Mengenerate PDF Retur.
     */
    public function generatePDF(Retur $retur)
    {
        $retur->load('penjualan.member', 'returDetail.produk');
        $pdf = PDF::loadView('retur.pdf', compact('retur'));
        return $pdf->download('Retur-' . $retur->id_retur . '.pdf');
    }
}
