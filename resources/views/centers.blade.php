<!DOCTYPE html>
<html lang="en">

  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>GESTIÓN ACADÉMICA</title>
    <link rel="icon" type="image/png" href="{{ asset('images/favicon.png') }}">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
  </head>

  <body class="bg-white text-gray-700">

    <!-- Header -->
    <header class="bg-white shadow-md">
      <nav x-data="{ open: false }" class="max-w-7xl mx-auto px-4 py-4 flex items-center justify-between">
        <a href="/" class="flex items-center space-x-2">
            <img src="{{ asset('images/logo.svg') }}" alt="Logo" class="h-16">
        </a>

        <button @click="open = !open" class="md:hidden text-gray-700 focus:outline-none transition-transform duration-300 transform" :class="{'rotate-45': open}">
          <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path x-show="!open" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
            <path x-show="open" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
          </svg>
        </button>

        <ul
          x-transition:enter="transition ease-out duration-300"
          x-transition:enter-start="opacity-0 transform scale-95"
          x-transition:enter-end="opacity-100 transform scale-100"
          x-transition:leave="transition ease-in duration-200"
          x-transition:leave-start="opacity-100 transform scale-100"
          x-transition:leave-end="opacity-0 transform scale-95"
          :class="{'block': open, 'hidden': !open, 'md:flex': true}"
          class="flex-col md:flex-row space-y-2 md:space-y-0 md:space-x-6 text-gray-700"
        >
          <li><a href="/" class="hover:text-green-500 transition">Inicio</a></li>
          <li><a href="/planes" class="hover:text-green-500 transitio">Planes</a></li>
          <li><a href="/centers" class="text-green-600 font-semibold">Centros</a></li>
          <li><a href="/admin" class="hover:text-green-500 transition">Ingresar</a></li>
        </ul>
      </nav>
    </header>

    <!-- Banner Starts Here -->
    <div class="bg-gray-50 text-center py-12">
      <section class="max-w-7xl mx-auto px-4">
        <h4 class="text-green-600 text-lg font-semibold mb-2">IED Agropecuaria José María Herrera</h4>
        <h2 class="text-4xl font-bold">Nuestros Centros de Interés</h2>
      </section>
    </div>
    <!-- Banner Ends Here -->

    <section class="max-w-7xl mx-auto px-4 py-10">
      <form method="GET" action="{{ route('centers') }}">
          <div class="flex flex-col md:flex-row gap-4 mb-6">
              <div class="flex-1">
                  <input
                      type="text"
                      name="search"
                      value="{{ request('search') }}"
                      placeholder="Buscar centro..."
                      class="border rounded px-4 py-2 w-full"
                  >
              </div>
              <div class="flex flex-col md:flex-row gap-2 md:w-auto w-full">
                  <button type="submit" class="bg-emerald-900 text-white px-4 py-2 rounded w-full md:w-auto">
                      Buscar
                  </button>
                  <a href="{{ route('centers') }}" class="bg-gray-300 text-gray-800 px-4 py-2 rounded hover:bg-gray-400 w-full md:w-auto text-center">
                      Limpiar
                  </a>
              </div>
          </div>
      </form>
      <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
        @foreach ($centers as $center)
            <article class="bg-white shadow-md rounded-lg overflow-hidden">
                <a href="/center/{{ $center->id }}" class="hover:opacity-75">
                    <img src="/storage/{{ $center->image_path }}" alt="" class="w-full h-48 object-cover">
                </a> 
                <div class="p-6">
                    <span class="text-green-600 text-sm font-semibold">{{ $center->academic_year }}</span>
                    <a href="/center/{{ $center->id }}" class="block mt-2 mb-3 hover:text-green-500 transition">
                        <h4 class="text-xl font-semibold">{{ $center->name }}</h4>
                    </a>
                    <ul class="flex space-x-4 text-sm text-gray-500 mb-4">
                      <li class="text-green-500 font-semibold mb-2">
                          Docentes del centro:
                          @if($center->teachers->count())
                              <div class="mt-2 space-y-2">
                                  @foreach ($center->teachers as $teacher)
                                      <div class="flex items-center gap-2 bg-gray-100 rounded-full px-3 py-1">
                                          <img src="{{ $teacher->profile_photo_path ? asset('storage/' . $teacher->profile_photo_path) : asset('images/default-avatar.png') }}"
                                               alt="{{ $teacher->full_name }}"
                                               class="w-8 h-8 rounded-full object-cover">
                                          <span class="text-sm font-normal">{{ $teacher->full_name }}</span>
                                      </div>
                                  @endforeach
                              </div>
                          @else
                              <div class="mt-2">
                                  No hay docentes asignados.
                              </div>
                          @endif
                      </li>
                    </ul>
                    <p class="text-gray-600 mb-4">{!! \Illuminate\Support\Str::limit($center->description, 150, '...') !!}</p>
                    <div>
                        <ul class="flex items-center space-x-2 text-green-600 text-sm font-semibold">
                            <li><i class="fa fa-tags"></i></li>
                            <li><a href="/center/{{ $center->id }}" class="hover:underline">Ver Centro de interés</a></li>
                        </ul>
                    </div>
                </div>
            </article>
        @endforeach
      </div>
      <div class="mt-4 justify-center space-x-3 text-gray-600">
        {{ $centers->links() }}
      </div>
    </section>

    <footer class="bg-gray-900 text-gray-300 py-6">
      <div class="max-w-7xl mx-auto text-center text-sm">
        <p>Copyright 2025. IED Agropecuaria José María Herrera - Pivijay | Diseño <a rel="nofollow" href="https://asyservicios.com" target="_blank" class="text-green-500 hover:underline">AS&Servicios.com</a></p>
      </div>
    </footer>

</body>
    <script src="//unpkg.com/alpinejs" defer></script>
</html>