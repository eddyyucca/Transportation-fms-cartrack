@extends('layouts.fleet-adminlte', ['title' => ($unit->exists ? 'Edit' : 'Tambah') . ' Unit P2H', 'selectedDate' => now()->toDateString()])

@section('content')
<div class="content-header">
    <div class="container-fluid">
        <div class="page-title">{{ $unit->exists ? 'Edit' : 'Tambah' }} Master Unit P2H</div>
    </div>
</div>

<section class="content pb-4">
    <div class="container-fluid">
        <form method="POST" action="{{ $unit->exists ? route('fleet.p2h.units.update', $unit) : route('fleet.p2h.units.store') }}">
            @csrf
            @if($unit->exists)
                @method('PUT')
            @endif

            @if($errors->any())
                <div class="alert alert-danger border-0">
                    <ul class="mb-0">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="card">
                <div class="card-header border-0 bg-white">
                    <h3 class="card-title font-weight-bold">Informasi Unit</h3>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3 mb-3">
                            <label class="font-weight-bold">Vendor</label>
                            <input type="text" name="vendor" class="form-control" value="{{ old('vendor', $unit->vendor) }}">
                        </div>
                        <div class="col-md-3 mb-3">
                            <label class="font-weight-bold">Unit Code</label>
                            <input type="text" name="unit_code" class="form-control" value="{{ old('unit_code', $unit->unit_code) }}" required>
                        </div>
                        <div class="col-md-3 mb-3">
                            <label class="font-weight-bold">Head</label>
                            <input type="text" name="head" class="form-control" value="{{ old('head', $unit->head) }}">
                        </div>
                        <div class="col-md-3 mb-3">
                            <label class="font-weight-bold">Department</label>
                            <input type="text" name="department" class="form-control" value="{{ old('department', $unit->department) }}">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="font-weight-bold">Plate No</label>
                            <input type="text" name="plate_no" class="form-control" value="{{ old('plate_no', $unit->plate_no) }}">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="font-weight-bold">PIC Name</label>
                            <input type="text" name="pic_name" class="form-control" value="{{ old('pic_name', $unit->pic_name) }}">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="font-weight-bold">Model Name</label>
                            <input type="text" name="model_name" class="form-control" value="{{ old('model_name', $unit->model_name) }}">
                        </div>
                    </div>
                    <div class="custom-control custom-switch">
                        <input type="checkbox" class="custom-control-input" id="is_active" name="is_active" value="1" @checked(old('is_active', $unit->exists ? $unit->is_active : true))>
                        <label class="custom-control-label" for="is_active">Unit aktif untuk report P2H</label>
                    </div>
                </div>
                <div class="card-footer bg-white">
                    <button type="submit" class="btn btn-primary px-4"><i class="fas fa-save mr-1"></i>Simpan</button>
                    <a href="{{ route('fleet.p2h.units.index') }}" class="btn btn-outline-secondary px-4">Batal</a>
                </div>
            </div>
        </form>
    </div>
</section>
@endsection
