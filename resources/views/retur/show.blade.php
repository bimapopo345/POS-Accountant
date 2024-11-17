@extends('layouts.master')

@section('title')
    Detail Retur
@endsection

@section('breadcrumb')
    @parent
    <li><a href="{{ route('retur.index') }}">Retur</a></li>
    <li class="active">Detail Retur</li>
@endsection

@section('content')
<div class="row">
    <div class="col-lg-12">
        <div class="box">
            <div class="box-header with-border">
                <h3 class="box-title">Detail Retur</h3>
                <div class="box-tools pull-right">
                    <a href="{{ route('retur.pdf', $retur->id_retur) }}" class="btn btn-success btn-sm">Cetak PDF</a>
                </div>
            </div>
            <div class="box-body">
                <h4>Informasi Retur</h4>
                <p><strong>ID Retur:</strong> {{ $retur->id_retur }}</p>
                <p><strong>ID Penjualan:</strong> {{ $retur->id_penjualan }}</p>
                <p><strong>Tanggal Retur:</strong> {{ \Carbon\Carbon::parse($retur->tanggal_retur)->translatedFormat('d F Y') }}</p>
                <p><strong>Total Retur:</strong> Rp. {{ number_format($retur->total_retur, 0, ',', '.') }}</p>
                <p><strong>Nilai Neto:</strong> Rp. {{ number_format($retur->nilai_neto, 0, ',', '.') }}</p>

                <hr>
                <h4>Detail Produk Retur</h4>
                <table class="table table-striped table-bordered">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Kode Produk</th>
                            <th>Nama Produk</th>
                            <th>Jumlah Retur</th>
                            <th>Harga Retur</th>
                            <th>Subtotal Retur</th>
                            <th>Alasan Retur</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($retur->returDetail as $detail)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $detail->produk->kode_produk }}</td>
                            <td>{{ $detail->produk->nama_produk }}</td>
                            <td>{{ $detail->jumlah_retur }}</td>
                            <td>Rp. {{ number_format($detail->harga_retur, 0, ',', '.') }}</td>
                            <td>Rp. {{ number_format($detail->subtotal_retur, 0, ',', '.') }}</td>
                            <td>{{ $detail->alasan_retur }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
