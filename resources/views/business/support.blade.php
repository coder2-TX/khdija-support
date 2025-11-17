@extends(config('khdija-support.layouts.business', 'layouts.app'))

@section('content')
<div class="p-4 sm:p-6 max-w-5xl mx-auto space-y-6">
  <h1 class="text-2xl sm:text-3xl font-bold break-words">{{ __('Contact System Admin') }}</h1>

  <div class="grid grid-cols-1 md:grid-cols-3 gap-4 sm:gap-6">
    @php
      use Illuminate\Support\Facades\Storage;
      use Illuminate\Support\Str;
      $wa = !empty($admin?->phone) ? ltrim(preg_replace('/\s+/', '', $admin->phone), '+') : null;
    @endphp

    <div class="md:col-span-1 bg-white p-4 sm:p-6 rounded-lg shadow-md min-w-0">
      <div class="flex items-center gap-3 mb-4 min-w-0">
        @if(!empty($admin?->profile_photo_path))
          <img
            src="{{ Storage::url($admin->profile_photo_path) }}"
            alt="{{ __('Admin') }}"
            class="w-14 h-14 rounded-full object-cover border-2 border-gray-300">
        @else
          <div
            class="w-14 h-14 rounded-full bg-gray-200 flex items-center justify-center text-gray-600 font-semibold border-2 border-gray-300">
            {{ Str::upper(Str::substr($admin->name ?? 'S', 0, 1)) }}
          </div>
        @endif

        <div>
          <div class="font-semibold text-gray-800">
            {{ $admin->name ?? __('System Owner') }}
          </div>
          <div class="text-xs text-gray-500">{{ __('Super Admin') }}</div>
        </div>
      </div>

      <div class="flex flex-col sm:flex-row flex-wrap gap-2 mb-4">
        @if(!empty($admin?->email))
          <a
            href="mailto:{{ $admin->email }}"
                class="inline-flex items-center justify-center gap-2 border border-gray-300 bg-white rounded-md px-3 py-1.5 text-sm w-full sm:w-auto transition hover:opacity-90 focus:outline-none focus:ring-2 focus:ring-blue-500">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-width="2" d="M4 6h16v12H4z"/><path stroke-width="2" d="m22 6-10 7L2 6"/>
            </svg>
            {{ __('Email') }}
          </a>
        @endif

        @if(!empty($admin?->phone))
          <a
            href="tel:{{ $admin->phone }}"
            class="inline-flex items-center justify-center gap-2 border border-gray-300 bg-white rounded-md px-3 py-1.5 text-sm w-full sm:w-auto transition hover:opacity-90 focus:outline-none focus:ring-2 focus:ring-blue-500">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 24 24" fill="currentColor">
              <path d="M6.62 10.79a15.05 15.05 0 006.59 6.59l2.2-2.2a1 1 0 011.01-.24 11.36 11.36 0 003.56.57 1 1 0 011 1V20a1 1 0 01-1 1A17 17 0 013 4a1 1 0 011-1h2.5a1 1 0 011 1 11.36 11.36 0 00.57 3.56 1 1 0 01-.24 1.01l-2.2 2.2z"/>
            </svg>
            {{ __('Call') }}
          </a>

          <a
            target="_blank" rel="noopener"
            href="https://wa.me/{{ $wa }}"
            class="inline-flex items-center justify-center gap-2 border border-gray-300 bg-white rounded-md px-3 py-1.5 text-sm w-full sm:w-auto transition hover:opacity-90 focus:outline-none focus:ring-2 focus:ring-blue-500"
            aria-label="{{ __('Chat on WhatsApp') }}">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 32 32">
              <path fill="#22C55E" d="M26.7 5.3A13.9 13.9 0 0016 2 14 14 0 002 16c0 2.46.65 4.77 1.79 6.77L2 30l7.4-1.94A14 14 0 1016 2z"/>
              <path fill="#fff" d="M19.11 17.02c-.3-.15-1.77-.87-2.04-.97-.27-.1-.47-.15-.67.15l-.94 1.17c-.17.2-.35.22-.65.07-1.62-.8-2.71-1.89-3.32-2.86-.17-.3-.02-.46.13-.61l.45-.52c.15-.17.2-.3.3-.5.1-.2.05-.37-.02-.52l-.92-2.22c-.24-.58-.49-.5-.67-.51h-1.2c-.2 0-.52.07-.8.37-.27.3-1.04 1.02-1.04 2.48s1.22 3.08 1.22 3.08 2.11 3.22 5.1 4.52c1.72.74 2.57.79 3.56.64.57-.09 1.77-.72 2.02-1.42.25-.7.25-1.29.17-1.42-.07-.13-.27-.2-.57-.35z"/>
            </svg>
            {{ __('WhatsApp') }}
          </a>
        @endif
      </div>

      <div class="space-y-1 text-sm">
        <div>
          {{ __('Email') }}:
          @if(!empty($admin?->email))
            <a href="mailto:{{ $admin->email }}" class="text-blue-600">{{ $admin->email }}</a>
          @else
            <span class="text-gray-500">{{ __('N/A') }}</span>
          @endif
        </div>

        <div>
          {{ __('Phone') }}:
          @if(!empty($admin?->phone))
            <a href="tel:{{ $admin->phone }}" class="text-blue-600">{{ $admin->phone }}</a>
          @else
            <span class="text-gray-500">{{ __('N/A') }}</span>
          @endif
        </div>

        <div>
          {{ __('WhatsApp') }}:
          @if(!empty($admin?->phone))
            <a
              target="_blank" rel="noopener"
              href="https://wa.me/{{ $wa }}"
              class="inline-flex items-center gap-1 text-sm hover:underline"
              style="color:#1A9E51;">
              <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" viewBox="0 0 32 32" fill="currentColor" aria-hidden="true">
                <path d="M26.7 5.3A13.93 13.93 0 0016 2C8.28 2 2 8.28 2 16c0 2.46.65 4.77 1.79 6.77L2 30l7.4-1.94A13.93 13.93 0 0016 30c7.72 0 14-6.28 14-14 0-3.74-1.46-7.15-3.3-9.7z"/>
                <path d="M19.11 17.02c-.3-.15-1.77-.87-2.04-.97-.27-.1-.47-.15-.67.15-.2.3-.77.97-.94 1.17-.17.2-.35.22-.65.07-.3-.15-1.26-.46-2.4-1.47-.89-.79-1.49-1.76-1.66-2.06-.17-.3-.02-.46.13-.61.13-.13.3-.35.45-.52.15-.17.2-.3.3-.5.1-.2.05-.37-.02-.52-.07-.15-.67-1.62-.92-2.22-.24-.58-.49-.5-.67-.51l-.57-.01c-.2 0-.52.07-.8.37-.27.3-1.04 1.02-1.04 2.48 0 1.46 1.07 2.88 1.22 3.08.15.2 2.11 3.22 5.1 4.52.71.31 1.27.5 1.7.64.71.22 1.35.19 1.86.12.57-.09 1.77-.72 2.02-1.42.25-.7.25-1.29.17-1.42-.07-.13-.27-.2-.57-.35z"/>
              </svg>
              {{ __('Open in WhatsApp') }}
            </a>
          @else
            <span class="text-gray-500">{{ __('N/A') }}</span>
          @endif
        </div>
      </div>
    </div>

    <div class="md:col-span-2 bg-white p-4 sm:p-6 rounded-lg shadow-md flex flex-col">
      <div
        id="chatStream"
        class="flex-1 overflow-y-auto space-y-3 max-h-[50vh] sm:max-h-[60vh] border-b border-gray-200 pb-4 rounded"
        style="scroll-behavior:smooth;">
        @foreach ($messages as $m)
          @php
            $isMe = $m->sender_role === 'business';
            $rtl  = app()->isLocale('ar');
            $rowClass = $isMe
                        ? ($rtl ? 'justify-start' : 'justify-end')
                        : ($rtl ? 'justify-end' : 'justify-start');
          @endphp

          <div class="flex {{ $rowClass }}">
            <div class="rounded-2xl px-3 py-2 shadow-sm max-w-[75%] break-words
                        {{ $isMe ? 'bg-blue-600 text-white' : 'bg-gray-100 text-gray-900' }}">
              <div class="text-sm">{{ $m->body }}</div>
              <div class="text-[10px] mt-1 {{ $isMe ? 'text-white/80' : 'text-gray-500' }}">
                {{ $m->created_at->format('Y-m-d H:i') }}
              </div>
            </div>
          </div>
        @endforeach
      </div>

      <form
        method="post"
        action="{{ route('business.support.store') }}"
        class="mt-3 flex flex-col sm:flex-row gap-2 biz-reply-form">
        @csrf
        <input
          name="body"
          class="w-full sm:flex-1 border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-600 focus:border-blue-600"
          placeholder="{{ __('Type your message...') }}">
        <button
          class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded w-full sm:w-auto transition duration-300 ease-in-out">
          {{ __('Send') }}
        </button>
      </form>
    </div>
  </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
  const box = document.getElementById('chatStream');
  if (box) box.scrollTop = box.scrollHeight;
  window.addEventListener('resize', () => { if (box) box.scrollTop = box.scrollHeight; });

  document.addEventListener('submit', async (e) => {
    const form = e.target.closest('.biz-reply-form');
    if (!form) return;
    e.preventDefault();

    const input = form.querySelector('input[name="body"]');
    const text  = (input.value || '').trim();
    if (!text) return;

    try{
      const r = await fetch(form.action, {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'Accept': 'application/json',
          'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
          'X-Requested-With': 'XMLHttpRequest'
        },
        body: JSON.stringify({ body: text })
      });

      const j = await r.json();

      if (j && j.ok && j.item){
        const rtl = document.documentElement.dir === 'rtl';
        const rowClass = rtl ? 'justify-start' : 'justify-end';

        const wrap = document.createElement('div');
        wrap.className = 'flex ' + rowClass;
        wrap.setAttribute('data-mid', j.item.id);
        wrap.innerHTML =
          `<div class="rounded-2xl px-3 py-2 shadow-sm max-w-[75%] break-words bg-blue-600 text-white">
             <div class="text-sm">${j.item.body}</div>
             <div class="text-[10px] mt-1 text-white/80">${j.item.at}</div>
           </div>`;

        box.appendChild(wrap);
        box.scrollTop = box.scrollHeight;
        input.value = '';
      }
    }catch(_){}
  });
});
</script>
@endpush
@endsection