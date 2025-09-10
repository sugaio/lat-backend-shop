@extends('layouts.app', ['title' => 'Users'])

@section('content')
    <!-- Begin Page Content -->
    <div class="container-fluid">

        <!-- Page Heading -->
        <div class="row">
            <div class="col-md-12">
                <div class="card border-0 shadow">
                    <div class="card-header bg-primary text-white">
                        <h6 class="m-0 font-weight-bold"><i class="fas fa-user-circle"></i> USERS</h6>
                    </div>

                    <div class="card-body">
                        <form action="{{ route('admin.user.index') }}" method="GET">
                            <div class="form-group">
                                <div class="input-group mb-3">
                                    <div class="input-group-prepend">
                                        <a href="{{ route('admin.user.create') }}" class="btn btn-primary btn-sm"
                                            style="padding-top: 10px;"><i class="fa fa-plus-circle"></i> TAMBAH</a>
                                    </div>
                                    <input type="text" class="form-control" name="q"
                                        placeholder="cari berdasarkan nama user">
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
                                        <th scope="col">NAMA USER</th>
                                        <th scope="col">EMAIL</th>
                                        <th scope="col" style="width: 15%;text-align: center">AKSI</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($users as $no => $user)
                                        <tr>
                                            <th scope="row" style="text-align: center">
                                                {{ ++$no + ($users->currentPage() - 1) * $users->perPage() }}</th>
                                            <td>{{ $user->name }}</td>
                                            <td>{{ $user->email }}</td>
                                            <td class="text-center">
                                                <a href="{{ route('admin.user.edit', $user->id) }}"
                                                    class="btn btn-sm btn-primary">
                                                    <i class="fa fa-pencil-alt"></i>
                                                </a>

                                                @if (auth()->user()->id !== $user->id)
                                                    <a href="{{ route('admin.user.destroy', $user->id) }}"
                                                        class="btn btn-sm btn-danger" data-confirm-delete="true">
                                                        <i class="fa fa-trash"></i>
                                                    </a>
                                                @endif
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
                                {{ $users->links('vendor.pagination.bootstrap-4') }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
@endsection
