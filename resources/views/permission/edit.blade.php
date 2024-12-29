@extends('layouts.master')
@section('content')
<h4 class="py-2 m-4"><span class="text-muted fw-light">Update Permission</span></h4>

<div class="row mt-5">
    <div class="col-12">
        {{-- Users --}}
        <div class="card">
            <div class="card-header">
                <h5>Update Permission</h5>
            </div>
            <div class="card-body">
            <form action="{{route('permission.update',$permission->id)}}" method="POST">
                @csrf
                @method('PUT')
                <input type="hidden" name="permissionName" value="">
                <div class="mb-3 position-relative">
                    <label for="permissionName">Permission Name</label>
                    <div class="d-flex align-items-center">
                    <input id="permissionName" name="permissionName" type="text" required class="form-control" value="{{ $permission->name }}" placeholder="Title Name">
                    </div>
                </div>
                <a href="{{Route('settings')}}" class="btn btn-secondary">Back</a>
                <button type="submit" class="btn btn-primary">Update Permission</button>
            </form>
            </div>
        </div>
    </div>
</div>
@endsection
