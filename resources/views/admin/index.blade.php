@extends(config('khdija-support.layouts.admin', 'layouts.app'))

@section('content')
<div class="p-6">
  <h1 class="text-2xl font-bold mb-6 text-gray-800">{{ __('Conversations') }}</h1>

  <form method="GET" action="{{ route('admin.conversations') }}" class="mb-6">
    <div class="flex gap-2">
      <input type="text" name="q" value="{{ request('q') }}" 
             placeholder="{{ __('Search businesses...') }}"
             class="flex-1 border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
      <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700">
        {{ __('Search') }}
      </button>
    </div>
  </form>

  <div class="space-y-6">
    @forelse($items as $row)
      <div class="bg-white p-6 rounded-lg shadow-md hover:shadow-lg transition">
        <a href="{{ route('admin.conversations.show', $row->business->id) }}"
           class="flex items-center justify-between gap-4">
          <div class="flex items-center gap-4 min-w-0">
            @php
              $initial = strtoupper(substr($row->business->name ?? 'B', 0, 1));
              $logoPath = $row->business->logo ?? '';
              $logoUrl = $logoPath ? asset('storage/' . ltrim($logoPath, '/')) : '';
            @endphp

            @if($logoUrl)
              <img src="{{ $logoUrl }}" alt="{{ $row->business->name }}"
                   class="w-10 h-10 rounded-full object-cover border-2 border-gray-300">
            @else
              <div class="w-10 h-10 rounded-full bg-gray-100 flex items-center justify-center text-gray-700 font-semibold border-2 border-gray-300">
                {{ $initial }}
              </div>
            @endif

            <div class="min-w-0">
              <div class="font-semibold text-gray-900">
                {{ $row->business->name ?? __('Business #') . $row->business->id }}
              </div>
              <div class="text-sm text-gray-500 truncate max-w-[70ch]"
                   data-biz-preview="{{ $row->business->id }}">
                {{ $row->last->body ?? __('No messages yet.') }}
              </div>
            </div>
          </div>

          <span data-biz-badge="{{ $row->business->id }}"
                class="inline-flex items-center justify-center w-7 h-7 rounded-full text-xs font-semibold text-white bg-red-600"
                style="{{ ($row->unread ?? 0) > 0 ? '' : 'display:none' }}">
            {{ $row->unread ?? 0 }}
          </span>
        </a>
      </div>
    @empty
      <div class="bg-white p-6 rounded-lg shadow-md text-sm text-gray-500">
        {{ __('No conversations.') }}
      </div>
    @endforelse
  </div>
</div>

@push('scripts')
<script>
(async function(){
  async function refreshBizBadges(){
    try{
      const r = await fetch("{{ route('admin.conversations.counters_map') }}", {
        headers: { 'Accept': 'application/json' }
      });
      const j = await r.json();

      if (j && j.businesses){
        Object.entries(j.businesses).forEach(([bizId, n]) => {
          const el = document.querySelector(`[data-biz-badge="${bizId}"]`);
          if (!el) return;

          n = parseInt(n||0,10);
          if (n > 0) {
            el.textContent = n;
            el.style.display = 'inline-flex';
          } else {
            el.textContent = '0';
            el.style.display = 'none';
          }
        });

        if (typeof j.total_unread !== 'undefined'){
          const side = document.querySelector('[data-admin-badge]');
          if (side){
            const n = parseInt(j.total_unread||0,10);
            if (n > 0) {
              side.textContent = n;
              side.style.display = 'inline-flex';
            } else {
              side.textContent = '0';
              side.style.display = 'none';
            }
          }
        }
      }
    }catch(e){}
  }

  refreshBizBadges();
  setInterval(refreshBizBadges, 5000);

  window.addEventListener('counters:refresh', refreshBizBadges);
})();
</script>
@endpush
@endsection