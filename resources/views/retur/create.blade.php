@extends('layouts.master')

@section('title')
    Tambah Retur
@endsection

@section('breadcrumb')
    @parent
    <li><a href="{{ route('retur.index') }}">Retur</a></li>
    <li class="active">Tambah Retur</li>
@endsection

@section('content')
<div class="row">
    <div class="col-lg-12">
        <div class="box">
            <div class="box-header with-border">
                <h3 class="box-title">Tambah Retur</h3>
            </div>
            <div class="box-body">
                @if ($errors->any())
                    <div class="alert alert-danger">
                        <ul>
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif
                <form action="{{ route('retur.store') }}" method="POST">
                    @csrf
                    <div class="form-group">
                        <label>ID Penjualan</label>
                        <select name="id_penjualan" class="form-control" required>
                            <option value="">-- Pilih Penjualan --</option>
                            @foreach($penjualan as $pj)
                                <option value="{{ $pj->id_penjualan }}">ID: {{ $pj->id_penjualan }}, Member: {{ $pj->penjualan->member->nama_perusahaan ?? 'Non-Member' }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Tanggal Retur</label>
                        <input type="date" name="tanggal_retur" class="form-control" required>
                    </div>
                    <hr>
                    <h4>Detail Retur</h4>
                    <table class="table table-bordered" id="detail-retur">
                        <thead>
                            <tr>
                                <th>Produk</th>
                                <th>Jumlah Retur</th>
                                <th>Harga Retur</th>
                                <th>Alasan Retur</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>
                                    <select name="retur_detail[0][id_produk]" class="form-control" required>
                                        <option value="">-- Pilih Produk --</option>
                                        @foreach($penjualan as $pj)
                                            @foreach($pj->penjualanDetail as $detail)
                                                <option value="{{ $detail->id_produk }}">{{ $detail->produk->nama_produk }}</option>
                                            @endforeach
                                        @endforeach
                                    </select>
                                </td>
                                <td>
                                    <input type="number" name="retur_detail[0][jumlah_retur]" class="form-control" min="1" required>
                                </td>
                                <td>
                                    <input type="number" name="retur_detail[0][harga_retur]" class="form-control" min="2000000" max="5000000" required>
                                </td>
                                <td>
                                    <input type="text" name="retur_detail[0][alasan_retur]" class="form-control" required>
                                </td>
                                <td>
                                    <button type="button" class="btn btn-sm btn-danger remove-row"><i class="fa fa-trash"></i></button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                    <button type="button" id="add-row" class="btn btn-sm btn-primary">Tambah Detail</button>
                    <hr>
                    <button type="submit" class="btn btn-success">Simpan Retur</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    $(document).ready(function(){
        let rowIdx = 1;

        // Event untuk menambah baris
        $('#add-row').on('click', function(){
            $('#detail-retur tbody').append(`
                <tr>
                    <td>
                        <select name="retur_detail[${rowIdx}][id_produk]" class="form-control" required>
                            <option value="">-- Pilih Produk --</option>
                            @foreach($penjualan as $pj)
                                @foreach($pj->penjualanDetail as $detail)
                                    <option value="{{ $detail->id_produk }}">{{ $detail->produk->nama_produk }}</option>
                                @endforeach
                            @endforeach
                        </select>
                    </td>
                    <td>
                        <input type="number" name="retur_detail[${rowIdx}][jumlah_retur]" class="form-control" min="1" required>
                    </td>
                    <td>
                        <input type="number" name="retur_detail[${rowIdx}][harga_retur]" class="form-control" min="2000000" max="5000000" required>
                    </td>
                    <td>
                        <input type="text" name="retur_detail[${rowIdx}][alasan_retur]" class="form-control" required>
                    </td>
                    <td>
                        <button type="button" class="btn btn-sm btn-danger remove-row"><i class="fa fa-trash"></i></button>
                    </td>
                </tr>
            `);
            rowIdx++;
        });

        // Event untuk menghapus baris
        $('#detail-retur').on('click', '.remove-row', function(){
            $(this).closest('tr').remove();
        });
    });
</script>
@endpush
