@extends('layouts.app')

@section('title', 'إدارة الخدمات')

@section('content')

<section class="!pl-0 p-3 sm:p-5">
    <x-breadcrumb first="الرئيسية" link="{{ route('dashboard.my_service.index') }}" second="إدارة الخدمات" />

    <div class="mx-auto w-full">
        <!-- Header -->
        <div class="bg-gray-600 rounded-lg shadow-lg p-6 mb-6 text-white">
            <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
                <div>
                    <h1 class="text-2xl md:text-3xl font-bold mb-2">إدارة الخدمات</h1>
                    <p class="text-blue-100">إدارة وتعديل الخدمات المتاحة للمستخدمين</p>
                </div>
                <a href="{{ route('dashboard.available_services.create') }}"
                    class="bg-white text-gray-600 px-5 py-2.5 rounded-lg font-bold hover:bg-gray-50 inline-flex items-center shadow-lg">
                    <i class="fas fa-plus ml-2"></i>
                    إضافة خدمة جديدة
                </a>
            </div>
        </div>

        @if(session('success'))
        <div class="mb-4 p-4 text-sm text-green-800 rounded-lg bg-green-50 border border-green-300">
            <i class="fas fa-check-circle ml-2"></i>{{ session('success') }}
        </div>
        @endif

        <!-- Services Table -->
        <div class="bg-white rounded-lg shadow-md overflow-hidden border border-gray-200">
            <div class="overflow-x-auto">
                <table class="w-full text-sm text-right text-gray-700">
                    <thead class="text-xs text-white uppercase bg-gray-600">
                        <tr>
                            <th scope="col" class="px-6 py-3">#</th>
                            <th scope="col" class="px-6 py-3">معاينة</th>
                            <th scope="col" class="px-6 py-3">اسم الخدمة</th>
                            <th scope="col" class="px-6 py-3">الوصف</th>
                            <th scope="col" class="px-6 py-3">الحالة</th>
                            <th scope="col" class="px-6 py-3">الترتيب</th>
                            <th scope="col" class="px-6 py-3">الإجراءات</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($services as $service)
                        <tr class="border-b hover:bg-gray-50">
                            <td class="px-6 py-4 font-medium text-gray-900">{{ $service->id }}</td>
                            <td class="px-6 py-4">
                                <div
                                    class="w-10 h-10 bg-gray-600 rounded-full flex items-center justify-center text-white shadow">
                                    <i class="fas {{ $service->icon }}"></i>
                                </div>
                            </td>
                            <td class="px-6 py-4 font-bold text-gray-900">{{ $service->name }}</td>
                            <td class="px-6 py-4 text-gray-600">
                                {{ Str::limit($service->description, 50) }}
                            </td>
                            <td class="px-6 py-4">
                                @if($service->is_active)
                                <span class="px-3 py-1 bg-green-100 text-green-800 rounded-full text-xs font-medium">
                                    <i class="fas fa-check-circle ml-1"></i>
                                    نشطة
                                </span>
                                @else
                                <span class="px-3 py-1 bg-red-100 text-red-800 rounded-full text-xs font-medium">
                                    <i class="fas fa-times-circle ml-1"></i>
                                    غير نشطة
                                </span>
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                <span class="px-2 py-1 bg-gray-100 rounded text-xs font-bold">
                                    {{ $service->order }}
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-2">
                                    <a href="{{ route('dashboard.available_services.edit', $service->id) }}"
                                        class="px-3 py-1.5 bg-gray-600 text-white rounded hover:bg-blue-700 text-xs font-medium">
                                        <i class="fas fa-edit ml-1"></i>
                                        تعديل
                                    </a>
                                    <form
                                        action="{{ route('dashboard.available_services.destroy', $service->id) }}"
                                        method="POST" onsubmit="return confirm('هل أنت متأكد من حذف هذه الخدمة؟')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                            class="px-3 py-1.5 bg-black text-white rounded hover:bg-red-700 text-xs font-medium">
                                            <i class="fas fa-trash ml-1"></i>
                                            حذف
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center py-12">
                                <i class="fas fa-inbox text-6xl text-gray-300 mb-3"></i>
                                <p class="text-gray-500 font-medium">لا توجد خدمات حالياً</p>
                                <a href="{{ route('dashboard.available_services.create') }}"
                                    class="inline-block mt-4 px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-blue-700">
                                    إضافة أول خدمة
                                </a>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            @if($services->hasPages())
            <div class="p-4 border-t">
                {{ $services->links() }}
            </div>
            @endif
        </div>
    </div>
</section>

@endsection