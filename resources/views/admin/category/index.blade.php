@extends('layouts.app', ['title' => 'Kategori'])

@section('content')
    <!-- Begin Page Content -->
    <div class="container-fluid mb-5">

        <!-- Page Heading -->
        <div class="row">
            <div class="col-md-12">
                <div class="card border-0 shadow">
                    <div class="card-header bg-primary text-white">
                        <h6 class="m-0 font-weight-bold"><i class="fas fa-folder"></i> KATEGORI</h6>
                    </div>

                    <div class="card-body">
                        <form action="{{ route('admin.category.index') }}" method="GET">
                            <div class="form-group">
                                <div class="input-group mb-3">
                                    <div class="input-group-prepend">
                                        <a href="{{ route('admin.category.create') }}" class="btn btn-primary btn-sm"
                                            style="padding-top: 10px;"><i class="fa fa-plus-circle"></i> TAMBAH</a>
                                    </div>
                                    <input type="text" class="form-control" name="q"
                                        placeholder="cari berdasarkan nama kategori">
                                    <div class="input-group-append">
                                        <button type="submit" class="btn btn-primary"><i class="fa fa-search"></i> CARI
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </form>
                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th scope="col" style="text-align: center;width: 6%">NO.</th>
                                        <th scope="col">GAMBAR</th>
                                        <th scope="col">NAMA KATEGORI</th>
                                        <th scope="col" style="width: 15%;text-align: center">AKSI</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($categories as $no => $category)
                                        <tr>
                                            <th scope="row" style="text-align: center">
                                                {{ ++$no + ($categories->currentPage() - 1) * $categories->perPage() }}</th>
                                            <td class="text-center">
                                                <img src="{{ asset('storage/categories/' . $category->image) }}"
                                                    style="width:50px">
                                            </td>
                                            <td>{{ $category->name }}</td>
                                            <td class="text-center">
                                                <a href="{{ route('admin.category.edit', $category->id) }}"
                                                    class="btn btn-sm btn-primary">
                                                    <i class="fa fa-pencil-alt"></i>
                                                </a>

                                                <a href="{{ route('admin.category.destroy', $category->id) }}" class="btn btn-sm btn-danger" data-confirm-delete="true">
                                                    <i class="fa fa-trash"></i>
                                                </a>
                                            </td>
                                        </tr>
                                    @empty
                                        <div class="alert alert-danger">
                                            Data Belum Tersedia!
                                        </div>
                                    @endforelse
                                </tbody>
                            </table>
                            <div style="text-align: center">
                                {{ $categories->links('vendor.pagination.bootstrap-4') }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
@endsection
