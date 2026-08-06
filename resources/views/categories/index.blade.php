@extends('layouts.app')
@section('title', __('messages.categories'))
@section('page-title', __('messages.categories'))

@section('content')
<div class="animate-in">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="fw-bold text-slate-900 mb-0"><i class="bi bi-tags-fill me-2 text-indigo-600"></i>{{ __('messages.categories') }}</h4>
        <a href="{{ route('categories.create') }}" class="btn btn-primary"><i class="bi bi-plus-lg me-1"></i>{{ __('messages.add_new') }} تصنيف</a>
    </div>
    <div class="card border-0 shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0 align-middle">
                    <thead><tr><th>{{ __('messages.table.id') }}</th><th>{{ __('messages.table.name') }}</th><th>{{ __('messages.products') }}</th><th>{{ __('messages.table.actions') }}</th></tr></thead>
                    <tbody>
                        @forelse($categories as $cat)
                        <tr>
                            <td class="fw-bold text-slate-500">#{{ $cat->id }}</td>
                            <td><strong class="text-slate-900 fs-6">{{ $cat->name }}</strong></td>
                            <td><span class="badge bg-indigo-50 text-indigo-700 border border-indigo-200 fs-6">{{ $cat->products_count }} منتج</span></td>
                            <td>
                                <div class="d-flex gap-1">
                                    <a href="{{ route('categories.edit', $cat) }}" class="btn btn-outline-secondary btn-sm" title="تعديل"><i class="bi bi-pencil"></i></a>
                                    <form action="{{ route('categories.destroy', $cat) }}" method="POST" onsubmit="return confirm('{{ __('messages.confirm_delete') }}')">@csrf @method('DELETE')<button class="btn btn-danger btn-sm" title="حذف"><i class="bi bi-trash"></i></button></form>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="4"><div class="empty-state py-4"><i class="bi bi-tags text-slate-300"></i><p class="text-slate-500">{{ __('messages.no_results') }}</p></div></td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
