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
          <li><a href="/planes" class="hover:text-indigo-500 transition">Planes</a></li>
          <li><a href="/centers" class="text-indigo-600 font-semibold">Centros</a></li>
          <li><a href="/admin" class="hover:text-indigo-500 transition">Ingresar</a></li>
        </ul>
      </nav>
    </header>

    <!-- Banner Starts Here -->
    <div class="bg-gray-50 text-center py-12">
      <section class="max-w-7xl mx-auto px-4">
        <a href="/centers">
          <h4 class="text-indigo-600 text-lg hover:underline font-semibold mb-2">Centros de interés</h4>
        </a>
        <h2 class="text-4xl font-bold">{{ $center->name }}</h2>
      </section>
    </div>
    <!-- Banner Ends Here -->

    <section class="max-w-7xl mx-auto px-4 py-10">
      <div class="w-full">
            <article class="bg-white shadow-md rounded-lg overflow-hidden w-full">
                <img src="/storage/{{ $center->image_path }}" alt="" class="w-full h-48 object-cover">
                <div class="p-6">
                    <span class="text-indigo-600 text-sm font-semibold">{{ $center->academic_year }}</span>
                    <h4 class="block mt-2 mb-3 transition text-xl font-semibold">{{ $center->name }}</h4>
                    <ul class="flex space-x-4 text-sm text-gray-500 mb-4">
                      <li class="text-indigo-500 transition">
                        Docentes del centro:
                        @if($center->teachers->count())
                            {{ $center->teachers->pluck('full_name')->join(', ') }}
                        @else
                            No hay docentes asignados.
                        @endif
                    </li>
                    </ul>
                    <div x-data="{ tab: 'description' }">
                        <div class="flex flex-wrap border-b border-gray-200 mb-6 gap-2 w-full">
                            <button @click="tab = 'description'"
                                :class="tab === 'description' ? 'border-b-2 border-indigo-500 text-indigo-500' : 'text-gray-500 hover:text-indigo-500'"
                                class="flex-1 px-4 py-2 focus:outline-none min-w-[120px] text-center">
                                Descripción
                            </button>
                            <button @click="tab = 'objective'"
                                :class="tab === 'objective' ? 'border-b-2 border-indigo-500 text-indigo-500' : 'text-gray-500 hover:text-indigo-500'"
                                class="flex-1 px-4 py-2 focus:outline-none min-w-[120px] text-center">
                                Objetivo
                            </button>
                            <button @click="tab = 'students'"
                                :class="tab === 'students' ? 'border-b-2 border-indigo-500 text-indigo-500' : 'text-gray-500 hover:text-indigo-500'"
                                class="flex-1 px-4 py-2 focus:outline-none min-w-[120px] text-center">
                                Estudiantes
                            </button>
                            <button @click="tab = 'activities'"
                                :class="tab === 'activities' ? 'border-b-2 border-indigo-500 text-indigo-500' : 'text-gray-500 hover:text-indigo-500'"
                                class="flex-1 px-4 py-2 focus:outline-none min-w-[120px] text-center">
                                Actividades
                            </button>
                            <button @click="tab = 'budgets'"
                                :class="tab === 'budgets' ? 'border-b-2 border-indigo-500 text-indigo-500' : 'text-gray-500 hover:text-indigo-500'"
                                class="flex-1 px-4 py-2 focus:outline-none min-w-[120px] text-center">
                                Recursos
                            </button>
                        </div>

                        <div>
                            <div x-show="tab === 'description'" x-cloak>
                                <h2 class="text-xl font-semibold mb-4">Descripción</h2>
                                <div class="prose max-w-none">
                                    {!! $center->description !!}
                                </div>
                            </div>

                            <div x-show="tab === 'objective'" x-cloak>
                                <h2 class="text-xl font-semibold mb-4">Objetivo</h2>
                                <div class="prose max-w-none">
                                    {!! $center->objective !!}
                                </div>
                            </div>

                            <div x-show="tab === 'students'" x-cloak>
                              <h2 class="text-xl font-semibold mb-4">Estudiantes</h2>
                              @if ($center->students->count())
                                  <div class="overflow-x-auto">
                                      <table class="min-w-full bg-white border border-gray-200">
                                          <thead>
                                              <tr>
                                                  <th class="px-4 py-2 border-b text-left text-sm font-semibold text-gray-700">Curso</th>
                                                  <th class="px-4 py-2 border-b text-left text-sm font-semibold text-gray-700">Nombre</th>
                                                  <th class="px-4 py-2 border-b text-left text-sm font-semibold text-gray-700">Documento</th>
                                              </tr>
                                          </thead>
                                          <tbody>
                                              @foreach ($center->students as $student)
                                                  <tr class="hover:bg-gray-50">
                                                      <td class="px-4 py-2 border-b text-sm text-gray-700">{{ $student->grade }}</td>
                                                      <td class="px-4 py-2 border-b text-sm text-gray-700">{{ $student->full_name }}</td>
                                                      <td class="px-4 py-2 border-b text-sm text-gray-700">{{ $student->identification }}</td>
                                                  </tr>
                                              @endforeach
                                          </tbody>
                                      </table>
                                  </div>
                              @else
                                  <p class="text-gray-500">No hay estudiantes registrados para este centro de interés.</p>
                              @endif
                            </div>

                            <div x-show="tab === 'activities'" x-cloak>
                              <h2 class="text-xl font-semibold mb-4">Actividades</h2>
                              @if ($center->activities->count())
                                  <div class="overflow-x-auto">
                                      <table class="min-w-full bg-white border border-gray-200">
                                          <thead>
                                              <tr>
                                                  <th class="px-4 py-2 border-b text-left text-sm font-semibold text-gray-700">Fecha</th>
                                                  <th class="px-4 py-2 border-b text-left text-sm font-semibold text-gray-700">Actividad</th>
                                                  <th class="px-4 py-2 border-b text-left text-sm font-semibold text-gray-700">Objetivo</th>
                                                  <th class="px-4 py-2 border-b text-left text-sm font-semibold text-gray-700">Metodología</th>
                                                  <th class="px-4 py-2 border-b text-left text-sm font-semibold text-gray-700">Materiales</th>
                                              </tr>
                                          </thead>
                                          <tbody>
                                              @foreach ($center->activities as $activity)
                                                  <tr class="hover:bg-gray-50">
                                                      <td class="px-4 py-2 border-b text-sm text-gray-700 align-top">{{ \Carbon\Carbon::parse($activity->week)->translatedFormat('F d') }}</td>
                                                      <td class="px-4 py-2 border-b text-sm text-gray-700 align-top">{{ $activity->activity }}</td>
                                                      <td class="px-4 py-2 border-b text-sm text-gray-700 align-top">{{ $activity->objective }}</td>
                                                      <td class="px-4 py-2 border-b text-sm text-gray-700 align-top">{!! $activity->methodology !!}</td>
                                                      <td class="px-4 py-2 border-b text-sm text-gray-700 align-top">{!! $activity->materials !!}</td>
                                                  </tr>
                                              @endforeach
                                          </tbody>
                                      </table>
                                  </div>
                              @else
                                  <p class="text-gray-500">No hay actividades registradas para este centro de interés.</p>
                              @endif
                            </div>

                            <div x-show="tab === 'budgets'" x-cloak>
                              <h2 class="text-xl font-semibold mb-4">Recursos</h2>
                              @if ($center->budgets->count())
                                  <div class="overflow-x-auto">
                                      <table class="min-w-full bg-white border border-gray-200">
                                          <thead>
                                              <tr>
                                                  <th class="px-4 py-2 border-b text-right text-sm font-semibold text-gray-700">Cantidad</th>
                                                  <th class="px-4 py-2 border-b text-left text-sm font-semibold text-gray-700">Item</th>
                                                  <th class="px-4 py-2 border-b text-right text-sm font-semibold text-gray-700">Valor Unitario</th>
                                                  <th class="px-4 py-2 border-b text-right text-sm font-semibold text-gray-700">Total</th>
                                                  <th class="px-4 py-2 border-b text-left text-sm font-semibold text-gray-700">Observaciones</th>
                                              </tr>
                                          </thead>
                                          <tbody>
                                              @foreach ($center->budgets as $budget)
                                                  <tr class="hover:bg-gray-50">
                                                      <td class="px-4 py-2 border-b text-sm text-gray-700 text-right">{{ $budget->quantity }}</td>
                                                      <td class="px-4 py-2 border-b text-sm text-gray-700">{{ $budget->item }}</td>
                                                      <td class="px-4 py-2 border-b text-sm text-gray-700 text-right">
                                                        {{ '$' . number_format($budget->unit_value, 0, ',', '.') }}
                                                      </td>
                                                      <td class="px-4 py-2 border-b text-sm text-gray-700 text-right">
                                                        {{ '$' . number_format($budget->quantity * $budget->unit_value, 0, ',', '.') }}
                                                      </td>
                                                      <td class="px-4 py-2 border-b text-sm text-gray-700">{{ $budget->observations }}</td>
                                                  </tr>
                                              @endforeach
                                          </tbody>
                                      </table>
                                  </div>
                              @else
                                  <p class="text-gray-500">No hay recursos registrados para este centro de interés.</p>
                              @endif
                            </div>

                        </div>
                    </div>
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