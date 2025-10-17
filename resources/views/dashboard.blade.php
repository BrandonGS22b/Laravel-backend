<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-2xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Gestión de Contribuyentes') }}
        </h2>
    </x-slot>

    <meta name="csrf-token" content="{{ csrf_token() }}">

    {{-- 1. Dependencias y Estilos --}}
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.5/css/jquery.dataTables.min.css"/>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.5/js/jquery.dataTables.min.js"></script>

    <style>
        .dataTables_wrapper .dataTables_length,
        .dataTables_wrapper .dataTables_filter,
        .dataTables_wrapper .dataTables_info,
        .dataTables_wrapper .dataTables_processing,
        .dataTables_wrapper .dataTables_paginate {
            color: #E5E7EB;
            font-size: 0.875rem;
        }
        .dataTables_wrapper .dataTables_paginate .paginate_button {
            color: #E5E7EB !important;
            border-radius: 0.5rem;
            margin: 0 0.25rem;
            padding: 0.5rem 1rem;
            transition: all 0.15s ease-in-out;
        }
        .dataTables_wrapper .dataTables_paginate .paginate_button:hover:not(.current) {
            background-color: #4B5563 !important; /* Gris intermedio al hacer hover */
            border-color: #4B5563 !important;
        }
        .dataTables_wrapper .dataTables_paginate .paginate_button.current {
            background-color: #374151 !important; /* Gris oscuro para selección */
            border-color: #374151 !important;
            color: #F9FAFB !important;
            font-weight: 700;
        }

        /* Campos de Input/Select de DataTables */
        .dataTables_wrapper .dataTables_filter input,
        .dataTables_wrapper select {
            border-radius: 0.5rem;
            padding: 0.5rem 0.75rem;
            border: 1px solid #4B5563; /* Borde oscuro */
            background-color: #4B5563; /* Fondo oscuro */
            color: #F9FAFB; /* Texto claro */
        }
        /* Ocultar flecha del select (Mejora la estética en DataTables) */
        .dataTables_wrapper select {
            -webkit-appearance: none;
            -moz-appearance: none;
            appearance: none;
            background-image: none !important;
        }
        .dataTables_wrapper .dataTables_filter input:focus,
        .dataTables_wrapper select:focus {
            outline: none;
            border-color: #6366F1; /* Indigo focus */
            box-shadow: 0 0 0 1px #6366F1;
        }

        /* Animación para el modal */
        .modal-overlay {
            background-color: rgba(0, 0, 0, 0.6);
        }
        .animate-modal {
            animation: fadeInUp 0.3s ease-out;
        }
        @keyframes fadeInUp {
            0% { opacity: 0; transform: translateY(20px); }
            100% { opacity: 1; transform: translateY(0); }
        }
        /* Estilo para readonly */
        .is-readonly {
            background-color: #f3f4f6 !important; /* Fondo gris claro */
            color: #4b5563 !important; /* Texto gris oscuro */
            cursor: not-allowed;
        }
        .dark .is-readonly {
            background-color: #374151 !important; /* Fondo oscuro */
            color: #9ca3af !important; /* Texto gris claro */
        }
    </style>

    {{-- 2. Contenido Principal: Botón Crear y Tabla --}}
    <div class="py-12 bg-gray-50 dark:bg-gray-900 min-h-screen">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            
            {{-- Botón Crear (Visible solo para rol 2) --}}
            @auth
                @if(Auth::user()->role_id == 2)
                    <div class="flex justify-end">
                        <button id="btnCrear"
                            class="flex items-center space-x-2 bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-xl shadow-lg transition transform hover:scale-[1.02] active:scale-[0.98] font-semibold">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M10 5a1 1 0 011 1v3h3a1 1 0 110 2h-3v3a1 1 0 11-2 0v-3H6a1 1 0 110-2h3V6a1 1 0 011-1z" clip-rule="evenodd" /></svg>
                            <span>Crear Contribuyente</span>
                        </button>
                    </div>
                @endif
            @endauth

            {{-- Tabla de Contribuyentes --}}
            <div class="bg-white dark:bg-gray-800 shadow-xl rounded-2xl overflow-x-auto p-6 transition duration-300">
                <table id="tablaContribuyentes" class="w-full text-sm divide-y divide-gray-300 dark:divide-gray-700 text-gray-800 dark:text-gray-200">
                    <thead class="bg-gray-100 dark:bg-gray-700">
                        <tr>
                            <th class="px-4 py-3 text-left font-semibold">Tipo Documento</th>
                            <th class="px-4 py-3 text-left font-semibold">Documento</th>
                            <th class="px-4 py-3 text-left font-semibold">Nombres</th>
                            <th class="px-4 py-3 text-left font-semibold">Apellidos</th>
                            <th class="px-4 py-3 text-left font-semibold">Teléfono</th>
                            <th class="px-4 py-3 text-center font-semibold">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        {{-- Los datos se cargan aquí con DataTables --}}
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    
    {{-- 3. Modal Contribuyente (Crear/Ver/Editar) --}}
    <div id="modalContribuyente" class="hidden fixed inset-0 flex justify-center items-center z-50 p-4 transition-opacity duration-300 backdrop-blur-sm modal-overlay">
        <div class="bg-white dark:bg-gray-800 w-full max-w-4xl p-6 sm:p-10 rounded-3xl shadow-2xl overflow-y-auto max-h-[95vh] animate-modal border border-gray-100 dark:border-gray-700/50">
            
            <h3 class="text-3xl font-extrabold mb-8 text-gray-900 dark:text-white border-b pb-4 border-gray-200 dark:border-gray-700/70 flex justify-between items-center">
                <span id="modalTitle">Detalle Contribuyente</span>
                <button type="button" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 transition" id="btnCerrarTop">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                </button>
            </h3>

            {{-- **CORRECCIÓN 1: Agregar novalidate al formulario para que JavaScript controle la validación** --}}
            <form id="formContribuyente" class="space-y-8" novalidate>
                @csrf
                <input type="hidden" id="contribuyenteId" name="contribuyenteId">

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    
                    {{-- Tipo Documento --}}
                    <div>
                        <label for="tipo_documento" class="block text-sm text-gray-700 dark:text-gray-300 font-semibold mb-1">Tipo Documento <span class="text-red-500">*</span></label>
                        <select id="tipo_documento" name="tipo_documento"
                            class="mt-1 w-full border border-gray-200 dark:border-gray-700 rounded-xl px-4 py-2.5 bg-white dark:bg-gray-700 dark:text-gray-100 focus:ring-indigo-500 focus:border-indigo-500 transition duration-150 shadow-sm">
                            <option value="">Seleccione...</option>
                            <option value="CC">Cédula de Ciudadanía (CC)</option>
                            <option value="NIT">NIT / RUC</option>
                            <option value="Pasaporte">Pasaporte</option>
                        </select>
                        <span class="text-red-500 text-xs mt-1 error-text block" id="error_tipo_documento"></span>
                    </div>

                    {{-- Documento --}}
                    <div>
                        <label for="documento" class="block text-sm text-gray-700 dark:text-gray-300 font-semibold mb-1">Documento <span class="text-red-500">*</span></label>
                        <input id="documento" name="documento" type="text"
                            class="mt-1 w-full border border-gray-200 dark:border-gray-700 rounded-xl px-4 py-2.5 bg-white dark:bg-gray-700 dark:text-gray-100 focus:ring-indigo-500 focus:border-indigo-500 transition duration-150 shadow-sm" placeholder="ID o RUC">
                        <span class="text-red-500 text-xs mt-1 error-text block" id="error_documento"></span>
                    </div>

                    {{-- Nombres --}}
                    <div>
                        <label for="nombres" class="block text-sm text-gray-700 dark:text-gray-300 font-semibold mb-1">Nombres <span class="text-red-500">*</span></label>
                        <input id="nombres" name="nombres" type="text"
                            class="mt-1 w-full border border-gray-200 dark:border-gray-700 rounded-xl px-4 py-2.5 bg-white dark:bg-gray-700 dark:text-gray-100 focus:ring-indigo-500 focus:border-indigo-500 transition duration-150 shadow-sm" placeholder="Nombre(s)">
                        <span class="text-red-500 text-xs mt-1 error-text block" id="error_nombres"></span>
                    </div>

                    {{-- Apellidos --}}
                    <div>
                        <label for="apellidos" class="block text-sm text-gray-700 dark:text-gray-300 font-semibold mb-1">Apellidos <span class="text-red-500">*</span></label>
                        <input id="apellidos" name="apellidos" type="text"
                            class="mt-1 w-full border border-gray-200 dark:border-gray-700 rounded-xl px-4 py-2.5 bg-white dark:bg-gray-700 dark:text-gray-100 focus:ring-indigo-500 focus:border-indigo-500 transition duration-150 shadow-sm" placeholder="Apellido(s)">
                        <span class="text-red-500 text-xs mt-1 error-text block" id="error_apellidos"></span>
                    </div>

                    {{-- Nombre Completo --}}
                    <div>
                        <label for="nombre_completo" class="block text-sm text-gray-700 dark:text-gray-300 font-semibold mb-1">Nombre Completo <span class="text-red-500">*</span></label>
                        <input id="nombre_completo" name="nombre_completo" type="text"
                            class="mt-1 w-full border border-gray-200 dark:border-gray-700 rounded-xl px-4 py-2.5 bg-white dark:bg-gray-700 dark:text-gray-100 focus:ring-indigo-500 focus:border-indigo-500 transition duration-150 shadow-sm" placeholder="Nombre completo">
                        <span class="text-red-500 text-xs mt-1 error-text block" id="error_nombre_completo"></span>
                    </div>

                    {{-- Celular --}}
                    <div>
                        <label for="celular" class="block text-sm text-gray-700 dark:text-gray-300 font-semibold mb-1">Celular</label>
                        <input id="celular" name="celular" type="text" class="mt-1 w-full border border-gray-200 dark:border-gray-700 rounded-xl px-4 py-2.5 bg-white dark:bg-gray-700 dark:text-gray-100 focus:ring-indigo-500 focus:border-indigo-500 transition duration-150 shadow-sm" placeholder="Ej: +57 300 123 4567">
                        <span class="text-red-500 text-xs mt-1 error-text block" id="error_celular"></span>
                    </div>

                    {{-- Email --}}
                    <div>
                        <label for="email" class="block text-sm text-gray-700 dark:text-gray-300 font-semibold mb-1">Email <span class="text-red-500">*</span></label>
                        <input id="email" name="email" type="email" class="mt-1 w-full border border-gray-200 dark:border-gray-700 rounded-xl px-4 py-2.5 bg-white dark:bg-gray-700 dark:text-gray-100 focus:ring-indigo-500 focus:border-indigo-500 transition duration-150 shadow-sm" placeholder="contacto@ejemplo.com">
                        <span class="text-red-500 text-xs mt-1 error-text block" id="error_email"></span>
                    </div>

                    {{-- Usuario --}}
                    <div>
                        <label for="usuario" class="block text-sm text-gray-700 dark:text-gray-300 font-semibold mb-1">Usuario</label>
                        <input id="usuario" name="usuario" type="text" class="mt-1 w-full border border-gray-200 dark:border-gray-700 rounded-xl px-4 py-2.5 bg-white dark:bg-gray-700 dark:text-gray-100 focus:ring-indigo-500 focus:border-indigo-500 transition duration-150 shadow-sm" placeholder="Nombre de usuario (opcional)">
                        <span class="text-red-500 text-xs mt-1 error-text block" id="error_usuario"></span>
                    </div>

                    {{-- Dirección --}}
                    <div>
                        <label for="direccion" class="block text-sm text-gray-700 dark:text-gray-300 font-semibold mb-1">Dirección</label>
                        <input id="direccion" name="direccion" type="text" class="mt-1 w-full border border-gray-200 dark:border-gray-700 rounded-xl px-4 py-2.5 bg-white dark:bg-gray-700 dark:text-gray-100 focus:ring-indigo-500 focus:border-indigo-500 transition duration-150 shadow-sm" placeholder="Calle/Carrera/Piso/Oficina">
                        <span class="text-red-500 text-xs mt-1 error-text block" id="error_direccion"></span>
                    </div>

                    {{-- Teléfono (Fijo) --}}
                    <div>
                        <label for="telefono" class="block text-sm text-gray-700 dark:text-gray-300 font-semibold mb-1">Teléfono (Fijo)</label>
                        <input id="telefono" name="telefono" type="text"
                            class="mt-1 w-full border border-gray-200 dark:border-gray-700 rounded-xl px-4 py-2.5 bg-white dark:bg-gray-700 dark:text-gray-100 focus:ring-indigo-500 focus:border-indigo-500 transition duration-150 shadow-sm" placeholder="Ej: 601 555-1234">
                        <span class="text-red-500 text-xs mt-1 error-text block" id="error_telefono"></span>
                    </div>
                </div>

                {{-- Campos de Auditoría --}}
                <hr class="my-6 border-gray-200 dark:border-gray-700">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 pt-4">
                    
                    {{-- Fechas de Auditoría --}}
                    <div>
                        <label class="block text-sm text-gray-700 dark:text-gray-300 font-semibold">Fecha de Creación</label>
                        <input type="text" id="created_at" class="mt-1 block w-full border border-gray-200 dark:border-gray-700 rounded-xl px-4 py-2.5 bg-gray-100 dark:bg-gray-700/80 dark:text-gray-400 cursor-not-allowed shadow-inner text-sm" disabled>
                    </div>
                    <div>
                        <label class="block text-sm text-gray-700 dark:text-gray-300 font-semibold">Fecha de Actualización</label>
                        <input type="text" id="updated_at" class="mt-1 block w-full border border-gray-200 dark:border-gray-700 rounded-xl px-4 py-2.5 bg-gray-100 dark:bg-gray-700/80 dark:text-gray-400 cursor-not-allowed shadow-inner text-sm" disabled>
                    </div>
                    
                    {{-- Frecuencia de Letras (Propiedad personalizada) --}}
                    <div class="md:col-span-2">
                        <h4 class="font-bold text-sm text-gray-700 dark:text-gray-300 mb-2">Frecuencia de Letras en Nombres/Apellidos 📊</h4>
                        <pre id="frecuenciaLetras" class="text-sm bg-gray-50 dark:bg-gray-900 dark:text-green-400 text-gray-800 p-4 rounded-xl overflow-auto max-h-48 shadow-inner border border-gray-200 dark:border-gray-700/80 transition duration-150">La frecuencia de letras se mostrará al ver/editar un contribuyente existente o al guardar uno nuevo.</pre>
                    </div>
                </div>

                <hr class="my-6 border-gray-200 dark:border-gray-700">

                {{-- Botones de Acción --}}
                <div class="flex justify-end gap-4 pt-4">
                    <button type="button" id="btnCerrar" class="flex items-center space-x-1 bg-gray-200 dark:bg-gray-700 hover:bg-gray-300 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-200 px-6 py-2.5 rounded-xl transition transform hover:scale-[1.02] active:scale-[0.98] shadow-md font-semibold">
                        <span>Cerrar</span>
                    </button>
                    @if(Auth::check() && Auth::user()->role_id == 2)
                        <button type="submit" id="btnGuardar" class="flex items-center space-x-1 bg-indigo-600 hover:bg-indigo-700 text-white px-6 py-2.5 rounded-xl transition transform hover:scale-[1.02] active:scale-[0.98] shadow-lg shadow-indigo-500/30 font-semibold">
                            <span>Guardar</span>
                        </button>
                    @endif
                </div>
            </form>
        </div>
    </div>

    {{-- 4. Modal Confirmación de Eliminación --}}
    <div id="modalConfirmacion" class="hidden fixed inset-0 modal-overlay flex justify-center items-center z-50 p-4 transition-opacity duration-300 backdrop-blur-sm">
        <div class="bg-white dark:bg-gray-800 p-8 rounded-xl shadow-2xl w-full max-w-sm animate-modal border border-gray-100 dark:border-gray-700/50">
            <h4 class="text-2xl font-bold mb-4 text-gray-800 dark:text-gray-200">Confirmar Eliminación ⚠️</h4>
            <p class="mb-8 text-gray-700 dark:text-gray-300">¿Estás seguro de eliminar este contribuyente? **Esta acción no se puede deshacer**.</p>
            <div class="flex justify-end gap-3">
                <button type="button" id="btnCancelarEliminar" class="bg-gray-500 hover:bg-gray-600 text-white px-5 py-2 rounded-xl transition active:scale-[0.98] font-semibold">Cancelar</button>
                <button type="button" id="btnConfirmarEliminar" class="bg-red-600 hover:bg-red-700 text-white px-5 py-2 rounded-xl transition active:scale-[0.98] font-semibold">Eliminar</button>
            </div>
        </div>
    </div>

    {{-- 5. Lógica JavaScript (Corregida y Optimizada) --}}
    <script>
    let tabla;
    const userRoleId = {{ Auth::check() ? Auth::user()->role_id : 0 }};
    const canEdit = (userRoleId === 2);
    let contribuyenteIdToDelete = null;

    // --- FUNCIONES CENTRALES DE LÓGICA DE NEGOCIO ---

    /**
     * Valida el formato del email usando una expresión regular.
     * @param {string} email
     * @returns {boolean}
     */
    function isValidEmail(email) {
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/; 
        return emailRegex.test(email);
    }

    /**
     * Aplica la lógica de Razón Social (NIT) o Nombre Completo (CC/Pasaporte)
     * y maneja el estado (readonly) de los campos en el modal.
     */
    function aplicarLogicaNombres() {
        const tipoDocumento = $('#tipo_documento').val();
        const nombresInput = $('#nombres');
        const apellidosInput = $('#apellidos');
        const nombreCompletoInput = $('#nombre_completo');

        // Elimina las clases de readonly y restablece el estado de los campos
        nombresInput.removeClass('is-readonly').prop('readonly', false);
        apellidosInput.removeClass('is-readonly').prop('readonly', false);
        nombreCompletoInput.removeClass('is-readonly').prop('readonly', false);

        if (tipoDocumento === 'NIT') {
            // Caso 1: NIT (Razón Social). El usuario edita Nombre Completo.
            const razonSocial = nombreCompletoInput.val().trim();
            const palabras = razonSocial.split(/\s+/).filter(p => p.length > 0);
            
            let nombres = '';
            let apellidos = '';

            // Lógica de separación: Primera palabra para Nombres, el resto para Apellidos
            if (palabras.length >= 1) {
                nombres = palabras[0];
                apellidos = palabras.slice(1).join(' ');
            }
            
            nombresInput.val(nombres);
            apellidosInput.val(apellidos);
            
            // Bloqueo de Nombres y Apellidos
            nombresInput.addClass('is-readonly').prop('readonly', true);   
            apellidosInput.addClass('is-readonly').prop('readonly', true); 
            // nombreCompletoInput sigue editable (readonly: false)

        } else if (tipoDocumento === 'CC' || tipoDocumento === 'Pasaporte') {
            // Caso 2: CC/Pasaporte (Persona Natural). El usuario edita Nombres y Apellidos.
            const nombres = nombresInput.val().trim();
            const apellidos = apellidosInput.val().trim();
            
            // Concatenar Nombres y Apellidos en Nombre Completo
            nombreCompletoInput.val(`${nombres} ${apellidos}`.trim());
            
            // Bloqueo de Nombre Completo
            nombreCompletoInput.addClass('is-readonly').prop('readonly', true);
            // nombresInput y apellidosInput siguen editables (readonly: false)
        }
        // Si tipoDocumento es "", todos los campos quedan editables para que el usuario elija.
    }

    // --- FUNCIONES AUXILIARES ---
    
    function limpiarErrores() { 
        $('.error-text').text(''); 
    }
    
    function formatReadableDate(isoString) {
        return isoString ? new Date(isoString).toLocaleString('es-CO', {
            year: 'numeric', month: 'short', day: 'numeric',
            hour: '2-digit', minute: '2-digit', hour12: true
        }) : 'N/A';
    }

    function setModalState(isEdit = false, isView = false) {
        // Habilita/Deshabilita campos del formulario (para evitar que se edite en 'Ver')
        const isFormEditable = isEdit && canEdit;
        
        // Habilita o deshabilita todos los campos, la función aplicarLogicaNombres
        // se encarga de los readonly específicos para la lógica NIT/CC.
        $('#formContribuyente input:not(#created_at, #updated_at), #formContribuyente select').prop('disabled', !isFormEditable);
        
        // Aplica la lógica de readonly/rellenado al final del cambio de estado
        if (isFormEditable) {
            // Solo aplicamos la lógica si se puede editar, sino todos quedan readonly/disabled (por la línea de arriba)
            aplicarLogicaNombres(); 
        }

        // Mostrar/Ocultar botón de guardar
        $('#btnGuardar').toggle(isEdit && canEdit);
        
        // Establecer título del modal
        let title = 'Detalle Contribuyente';
        if (isEdit) title = 'Editar Contribuyente';
        else if (!isView) title = 'Crear Contribuyente'; 
        $('#modalTitle').text(title);
    }
    
    // --- INICIALIZACIÓN Y EVENTOS ---

    $(document).ready(function() {
        // 1. Inicialización de DataTables
        // ... (El código de inicialización de DataTables no cambia) ...
        tabla = $('#tablaContribuyentes').DataTable({
            processing: true, 
            serverSide: true, 
            ajax: '{{ route("contribuyentes.data") }}',
            columns: [
                { data: 'tipo_documento', name: 'tipo_documento' },
                { data: 'documento', name: 'documento' },
                { data: 'nombres', name: 'nombres' },
                { data: 'apellidos', name: 'apellidos' },
                { data: 'telefono', name: 'telefono' },
                {
                    data: 'id',
                    name: 'acciones',
                    className: 'text-center whitespace-nowrap', 
                    orderable: false,
                    searchable: false,
                    render: function(data) {
                        let btns = `<button class='btnView text-blue-500 hover:text-blue-700 dark:hover:text-blue-400 mx-1 transition-colors' data-id='${data}' title="Ver Detalle">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 inline" viewBox="0 0 20 20" fill="currentColor"><path d="M10 12a2 2 0 100-4 2 2 0 000 4z"/><path fill-rule="evenodd" d="M.458 10C1.732 5.943 5.522 3 10 3s8.268 2.943 9.542 7c-1.274 4.057-5.064 7-9.542 7S1.732 14.057.458 10zM14 10a4 4 0 11-8 0 4 4 0 018 0z" clip-rule="evenodd"/></svg>
                                    </button>`;
                        if (canEdit) {
                            btns += `<button class='btnEdit text-yellow-500 hover:text-yellow-600 dark:hover:text-yellow-400 mx-1 transition-colors' data-id='${data}' title="Editar">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 inline" viewBox="0 0 20 20" fill="currentColor"><path d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zm-3.5 3.5a1 1 0 000 1.414L10.586 10l-4 4-4-4 4-4 4-4zM6 16.5V18a2 2 0 002 2h9a2 2 0 002-2v-9a2 2 0 00-2-2h-1.5v-1.5a.5.5 0 00-1 0V6H8a2 2 0 00-2 2v2.586l-2-2L0 13l4 4 4-4-2.586-2.586z"/></svg>
                                    </button>`;
                            btns += `<button class='btnDelete text-red-500 hover:text-red-600 dark:hover:text-red-400 mx-1 transition-colors' data-id='${data}' title="Eliminar">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 inline" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm4 0a1 1 0 10-2 0v6a1 1 0 102 0V8z" clip-rule="evenodd"/></svg>
                                    </button>`;
                        }
                        return btns;
                    }
                }
            ],
            language: {
                search: "_INPUT_",
                searchPlaceholder: "Buscar contribuyente...",
                lengthMenu: "Mostrar _MENU_ registros",
                info: "Mostrando _START_ a _END_ de _TOTAL_ registros",
                paginate: { previous: "Anterior", next: "Siguiente" },
                zeroRecords: "No se encontraron registros",
                infoEmpty: "Mostrando 0 a 0 de 0 registros",
                infoFiltered: "(filtrado de _MAX_ registros totales)"
            },
            dom: '<"flex flex-col sm:flex-row justify-between items-center mb-4 gap-4"lf>t<"mt-4 flex flex-col sm:flex-row justify-between items-center gap-4"ip>',
            responsive: true,
            initComplete: function () {
                $('#tablaContribuyentes_filter input').addClass('dark:bg-gray-700 dark:text-gray-100');
                $('#tablaContribuyentes_length select').addClass('dark:bg-gray-700 dark:text-gray-100');
            }
        });

        // 2. Eventos de Lógica (CRUCIALES)
        
        // Aplicar la lógica de nombres al cambiar el tipo de documento.
        $('#tipo_documento').on('change', function() {
            // Al cambiar, limpiamos nombre completo para forzar la re-entrada si es NIT
            $('#nombre_completo').val('');
            $('#nombres').val('');
            $('#apellidos').val('');
            aplicarLogicaNombres();
        });

        // Aplicar la lógica de nombres al editar los campos relevantes.
        $('#nombres, #apellidos, #nombre_completo').on('input', aplicarLogicaNombres);


        // 3. Manejo de Modales (Crear)
        $('#btnCrear').click(function() {
            $('#formContribuyente')[0].reset();
            $('#contribuyenteId').val('');
            $('#frecuenciaLetras').text('La frecuencia de letras se mostrará al editar o ver el contribuyente.');
            $('#created_at, #updated_at').val('N/A');
            
            limpiarErrores();
            setModalState(true, false); 
            $('#btnGuardar').text('Crear');
            $('#modalContribuyente').removeClass('hidden');
            // Ejecutar la lógica para establecer el estado inicial de los campos (editable CC/Pasaporte)
            aplicarLogicaNombres(); 
        });

        // Botones de cierre
        $('#btnCerrar, #btnCerrarTop').click(() => {
            $('#modalContribuyente').addClass('hidden');
            // Opcional: limpiar los valores readonly al cerrar
            // $('#nombres, #apellidos, #nombre_completo').removeClass('is-readonly').prop('readonly', false);
        });


        // 4. Manejo de Ver y Editar
        $('#tablaContribuyentes').on('click', '.btnView, .btnEdit', function() {
            const id = $(this).data('id');
            const isEdit = $(this).hasClass('btnEdit') && canEdit;
            const isView = $(this).hasClass('btnView');

            setModalState(isEdit, isView);
            limpiarErrores();
            $('#btnGuardar').text('Guardar'); 

            $.get(`/contribuyentes/${id}`, function(res) {
                const contribuyente = res.contribuyente;
                // Llenar Formulario
                $('#contribuyenteId').val(contribuyente.id);
                $('#tipo_documento').val(contribuyente.tipo_documento);
                $('#documento').val(contribuyente.documento);
                $('#nombres').val(contribuyente.nombres);
                $('#apellidos').val(contribuyente.apellidos);
                $('#nombre_completo').val(contribuyente.nombre_completo);
                // ... (otros campos) ...
                $('#celular').val(contribuyente.celular); 
                $('#email').val(contribuyente.email);
                $('#usuario').val(contribuyente.usuario); 
                $('#direccion').val(contribuyente.direccion); 
                $('#telefono').val(contribuyente.telefono);
                
                // Campos de Auditoría
                $('#created_at').val(formatReadableDate(contribuyente.created_at));
                $('#updated_at').val(formatReadableDate(contribuyente.updated_at));
                
                // Propiedad Personalizada
                $('#frecuenciaLetras').text(JSON.stringify(res.frecuencia, null, 2));

                $('#modalContribuyente').removeClass('hidden');
                
                // **CRUCIAL EN EDICIÓN/VER**: Aplicar la lógica después de cargar los datos
                // Esto asegura que los campos readonly/editables sean correctos.
                aplicarLogicaNombres(); 
            }).fail((err) => {
                console.error('Error al obtener contribuyente:', err);
            });
        });

        // 5. Manejo de Guardado/Actualización (Formulario)
        $('#formContribuyente').submit(function(e) {
            e.preventDefault();
            if (!canEdit) return; 

            // **PASO 1: Ejecutar la lógica de nombres por última vez (clave para NIT)**
            aplicarLogicaNombres(); 

            // **PASO 2: Validación de Email (Frontend)**
            const email = $('#email').val().trim();
            limpiarErrores();
            
            if (!isValidEmail(email)) {
                $('#error_email').text('El formato del correo electrónico no es válido.');
                $('#btnGuardar').prop('disabled', false).html(`<span>${$('#contribuyenteId').val() ? 'Guardar' : 'Crear'}</span>`); 
                return; // Detiene el envío
            }
            
            // **PASO 3: Envío AJAX**
            const id = $('#contribuyenteId').val();
            const url = id ? `/contribuyentes/${id}` : '{{ route("contribuyentes.store") }}';
            const method = id ? 'PUT' : 'POST';
            // Serializa el formulario. Los campos 'readonly' sí se incluyen en el serialize.
            const formData = $(this).serialize() + `&_method=${method}`; 

            $('#btnGuardar').prop('disabled', true).html('<svg class="animate-spin h-5 w-5 mr-3" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" fill="none" class="opacity-25"></circle><path fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z" class="opacity-75"></path></svg><span>Guardando...</span>'); 

            $.ajax({
                url: url,
                type: 'POST', 
                data: formData, 
                headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                success: () => {
                    tabla.ajax.reload(null, false);
                    $('#modalContribuyente').addClass('hidden');
                },
                error: function(err) {
                    if (err.responseJSON && err.responseJSON.errors) {
                        for (let campo in err.responseJSON.errors) {
                            $(`#error_${campo}`).text(err.responseJSON.errors[campo][0]);
                        }
                    } else {
                        console.error('Error al guardar:', err);
                    }
                },
                complete: function() {
                    $('#btnGuardar').prop('disabled', false).html(`<span>${id ? 'Guardar' : 'Crear'}</span>`);
                }
            });
        });

        // 6. Manejo de Eliminación
        // ... (El código de eliminación no cambia) ...
        $('#tablaContribuyentes').on('click', '.btnDelete', function() {
            if (!canEdit) return;
            contribuyenteIdToDelete = $(this).data('id');
            $('#modalConfirmacion').removeClass('hidden');
        });

        $('#btnCancelarEliminar').click(() => {
            $('#modalConfirmacion').addClass('hidden');
            contribuyenteIdToDelete = null; 
        });

        $('#btnConfirmarEliminar').click(function() {
            if (!contribuyenteIdToDelete) return;

            const $btn = $(this);
            $btn.prop('disabled', true).text('Eliminando...');

            $.ajax({
                url: `/contribuyentes/${contribuyenteIdToDelete}`,
                type: 'POST',
                headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                data: { _method: 'DELETE' },
                success: () => {
                    tabla.ajax.reload(null, false);
                    $('#modalConfirmacion').addClass('hidden');
                },
                error: err => console.error('Error al eliminar:', err),
                complete: function() {
                    $btn.prop('disabled', false).text('Eliminar');
                    contribuyenteIdToDelete = null;
                }
            });
        });
    });
    </script>
</x-app-layout>