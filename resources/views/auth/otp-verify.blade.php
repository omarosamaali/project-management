@extends('layouts.app')
@section('title', 'تأكيد الحساب')

@section('content')
<div class="min-h-screen bg-gray-50 flex flex-col items-center py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-4xl w-full space-y-8">
        <div class="text-center">
            <h2 class="mt-6 text-3xl font-extrabold text-gray-900">خطوة أخيرة لتأكيد حسابك</h2>
            <p class="mt-2 text-sm text-gray-600">يرجى إدخال الأكواد المرسلة إليك عبر الواتساب والبريد الإلكتروني</p>
        </div>

        <div class="mt-8 grid grid-cols-1 gap-6 md:grid-cols-2">

            <div
                class="bg-white p-8 rounded-2xl shadow-sm border {{ auth()->user()->whatsapp_verified ? 'border-green-500 ring-1 ring-green-500' : 'border-gray-200' }} relative z-10">
               <div class="flex flex-col items-center">
                <div class="w-16 h-16 bg-green-100 text-green-600 rounded-full flex items-center justify-center mb-4">
                    <svg class="w-8 h-8" fill="currentColor" viewBox="0 0 24 24">
                        <path
                            d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.245 2.248 3.481 5.236 3.48 8.414-.003 6.557-5.338 11.892-11.893 11.892-1.99-.001-3.951-.5-5.688-1.448l-6.305 1.654zm6.597-3.807c1.676.995 3.276 1.591 5.392 1.592 5.448 0 9.886-4.434 9.889-9.885.002-5.462-4.415-9.89-9.881-9.892-5.452 0-9.887 4.434-9.889 9.884-.001 2.225.651 3.891 1.746 5.634l-.999 3.648 3.742-.981zm11.387-5.464c-.074-.124-.272-.198-.57-.347-.297-.149-1.758-.868-2.031-.967-.272-.099-.47-.149-.669.149-.198.297-.768.967-.941 1.165-.173.198-.347.223-.644.074-.297-.149-1.255-.462-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.297-.347.446-.521.151-.172.2-.296.3-.495.099-.198.05-.372-.025-.521-.075-.148-.669-1.611-.916-2.206-.242-.579-.487-.501-.669-.51l-.57-.01c-.198 0-.52.074-.792.372s-1.04 1.016-1.04 2.479 1.065 2.876 1.213 3.074c.149.198 2.095 3.2 5.076 4.487.709.306 1.263.489 1.694.626.712.226 1.36.194 1.872.118.571-.085 1.758-.719 2.006-1.413.248-.695.248-1.29.173-1.414z" />
                    </svg>
                </div>
                <h3 class="text-lg font-bold text-gray-900 mb-2">تأكيد الواتساب</h3>
            
                {{-- عرض رسالة النجاح إذا كانت موجودة في السيشين أو إذا كان المستخدم مفعل فعلاً --}}
                @if(session('success_whatsapp') || auth()->user()->whatsapp_verified)
                <div class="flex flex-col items-center space-y-2">
                    <div
                        class="flex items-center space-x-2 text-green-600 bg-green-50 px-4 py-2 rounded-full font-bold border border-green-200">
                        <span>تم التأكيد بنجاح ✅</span>
                    </div>
                    <p class="text-xs text-gray-500">تم ربط رقم الواتساب بحسابك بنجاح</p>
                </div>
                @else
                <form action="{{ route('otp.whatsapp.check') }}" method="POST" class="w-full relative z-20">
                    @csrf
                    <input type="text" name="otp" maxlength="4" placeholder="0000"
                        class="block w-full text-center text-2xl tracking-[1rem] font-bold border-gray-300 rounded-xl focus:ring-blue-500 focus:border-blue-500 mb-4"
                        required>
                    <button type="submit"
                        class="w-full bg-blue-700 text-white py-3 rounded-xl font-bold hover:bg-blue-800 transition-all shadow-md active:scale-95 mb-2">تأكيد
                        الرمز</button>
                </form>
            
                <button id="resend-whatsapp" onclick="handleResend('whatsapp')"
                    class="text-blue-600 text-xs font-semibold hover:underline disabled:text-gray-400 disabled:no-underline">إعادة
                    إرسال الكود <span id="timer-whatsapp"></span></button>
            
                @error('whatsapp_otp') <p class="text-red-500 text-sm mt-2 text-center">{{ $message }}</p> @enderror
                @endif
            </div>
            </div>

            <div
                class="bg-white p-8 rounded-2xl shadow-sm border {{ auth()->user()->email_verified_at ? 'border-green-500 ring-1 ring-green-500' : 'border-gray-200' }} relative z-10">
                <div class="flex flex-col items-center">
                    <div class="w-16 h-16 bg-blue-100 text-blue-600 rounded-full flex items-center justify-center mb-4">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z">
                            </path>
                        </svg>
                    </div>
                    <h3 class="text-lg font-bold text-gray-900 mb-2">تأكيد البريد الإلكتروني</h3>

                    @if(auth()->user()->email_verified_at)
                    <div
                        class="flex items-center text-center space-x-2 text-green-600 bg-green-50 px-4 py-2 rounded-full font-bold">
                        تهانينا! تم تأكيد حسابك وتفعيل البريد الإلكتروني بنجاح ✅
                    </div>
                    
                    @else
                    <form action="{{ route('otp.email.check') }}" method="POST" class="w-full relative z-20">
                        @csrf
                        <input type="text" name="otp" maxlength="4" placeholder="0000"
                            class="block w-full text-center text-2xl tracking-[1rem] font-bold border-gray-300 rounded-xl focus:ring-blue-500 focus:border-blue-500 mb-4"
                            required>
                        <button type="submit"
                            class="w-full bg-blue-700 text-white py-3 rounded-xl font-bold hover:bg-blue-800 transition-all shadow-md active:scale-95 mb-2">تأكيد
                            الرمز</button>
                    </form>

                    <button id="resend-email" onclick="handleResend('email')"
                        class="text-blue-600 text-xs font-semibold hover:underline disabled:text-gray-400 disabled:no-underline">إعادة
                        إرسال الكود <span id="timer-email"></span></button>

                    @error('email_otp') <p class="text-red-500 text-sm mt-2 text-center">{{ $message }}</p> @enderror
                    @endif
                </div>
            </div>
        </div>

        @if(auth()->user()->whatsapp_verified && auth()->user()->email_verified_at)
        <div class="text-center mt-10 animate-bounce">
            <a href="{{ route('dashboard') }}"
                class="inline-flex items-center px-10 py-4 bg-green-600 text-white font-bold text-lg rounded-full hover:bg-green-700 shadow-lg transition-all transform hover:scale-105">متابعة
                إلى لوحة التحكم ✅</a>
        </div>
        @endif

        <div class="text-center mt-6">
            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button type="submit"
                    class="text-gray-500 hover:text-red-600 text-sm font-medium transition-colors">تسجيل الخروج والعودة
                    لاحقاً</button>
            </form>
        </div>
    </div>
</div>

<script>
    async function handleResend(type) {
const btn = document.getElementById(`resend-${type}`);
const timerSpan = document.getElementById(`timer-${type}`);
let timeLeft = 3;

btn.disabled = true;

try {
// نضع قيمة مؤقتة ':type' ليقبلها لارافل، ثم نستبدلها بالجافاسكريبت
const baseUrl = "{{ route('otp.resend', ['type' => ':type']) }}";
const url = baseUrl.replace(':type', type);

const response = await fetch(url, {
method: 'POST',
headers: {
'Content-Type': 'application/json',
'Accept': 'application/json', // يضمن رد السيرفر بـ JSON وليس صفحة خطأ HTML
'X-CSRF-TOKEN': '{{ csrf_token() }}',
'X-Requested-With': 'XMLHttpRequest'
}
});

const result = await response.json();

if (response.ok && result.success) {
alert(result.message);
// بدء العداد التنازلي
const interval = setInterval(() => {
if (timeLeft <= 0) { clearInterval(interval); btn.disabled=false; timerSpan.innerHTML="" ; } else {
    timerSpan.innerHTML=`(${timeLeft}ث)`; timeLeft--; } }, 1000); } else { throw new Error(result.message
    || 'حدث خطأ في السيرفر' ); } } catch (error) { console.error('Error Details:', error); alert('فشل الإرسال: ' + error.message);
        btn.disabled = false;
    }
}
</script>
@endsection