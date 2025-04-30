<!DOCTYPE html>
<html lang="en">

  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>GESTIÓN ACADÉMICA</title>
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
          <li><a href="/" class="hover:text-indigo-500 transition">Inicio</a></li>
          <li><a href="/admin" class="hover:text-indigo-500 transition">Ingresar</a></li>
        </ul>
      </nav>
    </header>

    <!-- Banner Starts Here -->
    <div class="bg-gray-50 text-center py-12">
      <section class="max-w-7xl mx-auto px-4">
        <h4 class="text-indigo-600 text-lg font-semibold mb-2">Plan de área</h4>
        <h2 class="text-4xl font-bold">{{ $plan->name }}</h2>
      </section>
    </div>
    <!-- Banner Ends Here -->

    <section class="max-w-7xl mx-auto px-4 py-10">
      <div class="w-full">
            <article class="bg-white shadow-md rounded-lg overflow-hidden w-full">
                <a href="/{{ $plan->id }}" class="hover:opacity-75">
                    <img src="/storage/{{ $plan->cover }}" alt="" class="w-full h-48 object-cover">
                </a> 
                <div class="p-6">
                    <span class="text-indigo-600 text-sm font-semibold">{{ $plan->year }}</span>
                    <a href="/{{ $plan->id }}" class="block mt-2 mb-3 hover:text-indigo-500 transition">
                        <h4 class="text-xl font-semibold">{{ $plan->name }}</h4>
                    </a>
                    <ul class="flex space-x-4 text-sm text-gray-500 mb-4">
                      <li><a href="#" class="hover:text-indigo-500 transition">Docentes del área: {{ $plan->users->pluck('name')->join(', ') }}.</a></li>
                    </ul>
                    <p class="text-gray-600 mb-4">{!! $plan->justification !!}</p>
                </div>
            </article>
      </div>
    </section>

    <footer class="bg-gray-900 text-gray-300 py-6">
      <div class="max-w-7xl mx-auto text-center text-sm">
        <p>Copyright 2025. IED Agropecuaria José María Herrera - Pivijay | Diseño <a rel="nofollow" href="https://asyservicios.com" target="_blank" class="text-indigo-500 hover:underline">AS&Servicios.com</a></p>
      </div>
    </footer>

</body>
    <script src="//unpkg.com/alpinejs" defer></script>
</html>