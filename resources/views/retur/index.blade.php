@extends('layouts.master')

@section('title')
    Data Retur
@endsection

@section('breadcrumb')
    @parent
    <li><a href="{{ route('retur.index') }}">Retur</a></li>
@endsection

@section('content')
<div class="row">
    <div class="col-lg-12">
        <div class="box">
            <div class="box-header with-border">
                <h3 class="box-title">Data Retur</h3>
                <div class="box-tools pull-right">
                    <a href="{{ route('retur.create') }}" class="btn btn-primary btn-sm">Tambah Retur</a>
                </div>
            </div>
            <div class="box-body table-responsive">
                <table class="table table-bordered table-striped" id="retur-table">
                <thead>
    <tr>
        <th>No</th>
        <th>Tanggal</th>
        <th>Penjualan</th>
        <th>Retur</th>
        <th>Net Penjualan</th>
        <th>Pembelian</th>
        <th>Pengeluaran</th>
        <th>Pendapatan</th>
    </tr>
</thead>

                </table>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
   $(function() {
    $('#retur-table').DataTable({
        processing: true,
        serverSide: true,
        ajax: '{{ route('retur.data') }}',
        columns: [
            { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
            { data: 'id_retur', name: 'id_retur' },
            { data: 'id_penjualan', name: 'id_penjualan' },
            { data: 'tanggal_retur', name: 'tanggal_retur' },
            { data: 'total_retur', name: 'total_retur' },
            { data: 'nilai_neto', name: 'nilai_neto' },
            { data: 'aksi', name: 'aksi', orderable: false, searchable: false },
        ]
    });
});



    // Fungsi untuk menghapus Retur
    function deleteRetur(url) {
        if(confirm("Apakah Anda yakin ingin menghapus Retur ini?")) {
            $.ajax({
                url: url,
                type: 'DELETE',
                data: {
                    "_token": "{{ csrf_token() }}"
                },
                success: function(response) {
                    if(response.success) {
                        toastr.success(response.success);
                        $('#retur-table').DataTable().ajax.reload();
                    } else {
                        toastr.error(response.error);
                    }
                },
                error: function() {
                    toastr.error('Terjadi kesalahan saat menghapus Retur.');
                }
            });
        }
    }
</script>
@endpush
