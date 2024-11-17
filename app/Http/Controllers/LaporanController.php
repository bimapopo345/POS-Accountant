<?php

namespace App\Http\Controllers;

use App\Models\Pembelian;
use App\Models\Pengeluaran;
use App\Models\Penjualan;
use App\Models\Retur; // Tambahkan ini
use Illuminate\Http\Request;
use PDF;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class LaporanController extends Controller
{
    /**
     * Menampilkan halaman laporan dengan periode awal dan akhir.
     */
    public function index(Request $request)
    {
        // Default tanggal awal: hari pertama bulan ini
        $tanggalAwal = Carbon::now()->startOfMonth()->toDateString();
        $tanggalAkhir = Carbon::now()->toDateString();

        // Jika tanggal dikirim dari form
        if ($request->has('tanggal_awal') && $request->tanggal_awal != "" && $request->has('tanggal_akhir') && $request->tanggal_akhir != "") {
            $tanggalAwal = $request->tanggal_awal;
            $tanggalAkhir = $request->tanggal_akhir;
        }

        return view('laporan.index', compact('tanggalAwal', 'tanggalAkhir'));
    }

    /**
     * Mengambil data laporan berdasarkan periode tanggal.
     */
    public function getData($awal, $akhir)
{
    $no = 1;
    $data = [];
    $total_pendapatan = 0;

    // Validasi rentang tanggal agar tidak terlalu panjang (maksimal 31 hari)
    $start = Carbon::parse($awal);
    $end = Carbon::parse($akhir);
    $selisih_hari = $start->diffInDays($end);
    if ($selisih_hari > 31) {
        return back()->withErrors(['error' => 'Rentang tanggal tidak boleh lebih dari 31 hari']);
    }

    // Ambil data penjualan, pembelian, pengeluaran, dan retur dalam grup tanggal
    $data_penjualan = Penjualan::selectRaw('DATE(created_at) as tanggal, SUM(bayar) as total_penjualan')
        ->whereDate('created_at', '>=', $awal)
        ->whereDate('created_at', '<=', $akhir)
        ->groupBy('tanggal')
        ->get()
        ->keyBy('tanggal');

    $data_pembelian = Pembelian::selectRaw('DATE(created_at) as tanggal, SUM(bayar) as total_pembelian')
        ->whereDate('created_at', '>=', $awal)
        ->whereDate('created_at', '<=', $akhir)
        ->groupBy('tanggal')
        ->get()
        ->keyBy('tanggal');

    $data_pengeluaran = Pengeluaran::selectRaw('DATE(created_at) as tanggal, SUM(nominal) as total_pengeluaran')
        ->whereDate('created_at', '>=', $awal)
        ->whereDate('created_at', '<=', $akhir)
        ->groupBy('tanggal')
        ->get()
        ->keyBy('tanggal');

    // Ambil data retur
    $data_retur = Retur::selectRaw('DATE(tanggal_retur) as tanggal, SUM(total_retur) as total_retur')
        ->whereDate('tanggal_retur', '>=', $awal)
        ->whereDate('tanggal_retur', '<=', $akhir)
        ->groupBy('tanggal')
        ->get()
        ->keyBy('tanggal');

    // Inisialisasi total keseluruhan
    $total_penjualan_all = 0;
    $total_retur_all = 0;
    $total_net_penjualan_all = 0;
    $total_pembelian_all = 0;
    $total_pengeluaran_all = 0;
    $total_pendapatan_all = 0;

    // Iterasi tanggal dari awal hingga akhir
    $current = $start->copy();
    while ($current->lte($end)) {
        $tanggal = $current->toDateString();

        $total_penjualan = $data_penjualan->has($tanggal) ? $data_penjualan[$tanggal]->total_penjualan : 0;
        $total_pembelian = $data_pembelian->has($tanggal) ? $data_pembelian[$tanggal]->total_pembelian : 0;
        $total_pengeluaran = $data_pengeluaran->has($tanggal) ? $data_pengeluaran[$tanggal]->total_pengeluaran : 0;
        $total_retur = $data_retur->has($tanggal) ? $data_retur[$tanggal]->total_retur : 0;

        // Kurangi total penjualan dengan total retur
        $net_penjualan = $total_penjualan - $total_retur;

        // Akumulasi total keseluruhan
        $total_penjualan_all += $total_penjualan;
        $total_retur_all += $total_retur;
        $total_net_penjualan_all += $net_penjualan;
        $total_pembelian_all += $total_pembelian;
        $total_pengeluaran_all += $total_pengeluaran;

        $pendapatan = $net_penjualan - $total_pembelian - $total_pengeluaran;
        $total_pendapatan_all += $pendapatan;

        $data[] = [
            'DT_RowIndex' => $no++,
            'tanggal' => $this->tanggal_indonesia($tanggal, false),
            'penjualan' => $this->format_uang($total_penjualan),
            'retur' => $this->format_uang($total_retur),
            'net_penjualan' => $this->format_uang($net_penjualan),
            'pembelian' => $this->format_uang($total_pembelian),
            'pengeluaran' => $this->format_uang($total_pengeluaran),
            'pendapatan' => $this->format_uang($pendapatan),
        ];

        $current->addDay();
    }

    // Tambahkan total keseluruhan di akhir
    $data[] = [
        'DT_RowIndex' => '',
        'tanggal' => '',
        'penjualan' => 'Total',
        'retur' => $this->format_uang($total_retur_all),
        'net_penjualan' => $this->format_uang($total_net_penjualan_all),
        'pembelian' => $this->format_uang($total_pembelian_all),
        'pengeluaran' => $this->format_uang($total_pengeluaran_all),
        'pendapatan' => $this->format_uang($total_pendapatan_all),
    ];

    return $data;
}


    /**
     * Menyediakan data untuk DataTables.
     */
    public function data($awal, $akhir)
    {
        $data = $this->getData($awal, $akhir);

        return datatables()
    ->of($data)
    ->addColumn('retur', function($row) {
        return $row['retur'];
    })
    ->addColumn('net_penjualan', function($row) {
        return $row['net_penjualan'];
    })
    ->make(true);

    }

    /**
     * Mengekspor laporan ke format PDF.
     */
    public function exportPDF($awal, $akhir)
    {
        // Tingkatkan batas waktu eksekusi dan batas memori
        ini_set('max_execution_time', 300); // 5 menit
        ini_set('memory_limit', '512M'); // 512 MB

        Log::info("Mulai proses export PDF: $awal s/d $akhir");

        try {
            // Ambil data laporan
            $data = $this->getData($awal, $akhir);

            // Muat template PDF
            $pdf = PDF::loadView('laporan.pdf', compact('awal', 'akhir', 'data'));
            $pdf->setPaper('a4', 'portrait');

            Log::info("PDF berhasil dibuat: $awal s/d $akhir");

            // Kirim PDF ke browser
            return $pdf->stream('Laporan-pendapatan-' . date('Y-m-d-His') . '.pdf');
        } catch (\Exception $e) {
            Log::error("Error saat export PDF: " . $e->getMessage());
            return back()->withErrors(['error' => 'Terjadi kesalahan saat membuat PDF. Silakan coba lagi.']);
        }
    }

    /**
     * Mengonversi tanggal ke format Indonesia.
     */
    private function tanggal_indonesia($tanggal, $cetak_hari = true)
    {
        $hari = ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
        $bulan = [
            1 => 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
            'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'
        ];

        $split = explode('-', $tanggal);
        $tgl = $split[2];
        $bln = $bulan[(int)$split[1]];
        $thn = $split[0];

        $waktu = strtotime($tanggal);
        $hari_ini = date('w', $waktu);

        if ($cetak_hari) {
            return $hari[$hari_ini] . ', ' . $tgl . ' ' . $bln . ' ' . $thn;
        }

        return $tgl . ' ' . $bln . ' ' . $thn;
    }

    /**
     * Mengonversi angka ke format uang Indonesia.
     */
    private function format_uang($angka)
    {
        return 'Rp ' . number_format($angka, 2, ',', '.');
    }
}
