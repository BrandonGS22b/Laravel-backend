<x-app-layout>
    <x-slot name="header">
        <h2 class="font-bold text-3xl text-gray-800 dark:text-gray-100 leading-tight">
            {{ __('Gestión de Contribuyentes') }} 
        </h2>
    </x-slot>

    <meta name="csrf-token" content="{{ csrf_token() }}">

    {{-- 1. Dependencias y Estilos (Se mantienen los DataTables y los estilos personalizados para el modo oscuro) --}}
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.5/css/jquery.dataTables.min.css"/>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.5/js/jquery.dataTables.min.js"></script>

    {{-- Vite se encargará de encontrar y servir los archivos --}}
    @vite(['resources/css/app.css'])

    {{-- 2. Contenido Principal: Botón Crear y Tabla --}}
    <div class="py-12 bg-gray-50 dark:bg-gray-900 min-h-screen">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            
            {{-- Botón Crear (Visible solo para rol 2) --}}
            @auth
                @if(Auth::user()->role_id == 2)
                    <div class="flex justify-end">
                        <button id="btnCrear"
                            class="flex items-center space-x-2 bg-indigo-600 hover:bg-indigo-700 text-white px-6 py-3 rounded-full shadow-lg transition transform hover:scale-[1.02] active:scale-[0.98] font-bold text-base ring-2 ring-indigo-300 dark:ring-indigo-700">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M10 5a1 1 0 011 1v3h3a1 1 0 110 2h-3v3a1 1 0 11-2 0v-3H6a1 1 0 110-2h3V6a1 1 0 011-1z" clip-rule="evenodd" /></svg>
                            <span>Crear Contribuyente</span>
                        </button>
                    </div>
                @endif
            @endauth

            {{-- Tabla de Contribuyentes (Mejorado con ring y hover en filas) --}}
            <div class="bg-white dark:bg-gray-800 shadow-2xl rounded-2xl ring-1 ring-gray-200 dark:ring-gray-700 overflow-x-auto p-6 transition duration-300">
                <table id="tablaContribuyentes" class="w-full text-sm divide-y divide-gray-200 dark:divide-gray-700 text-gray-800 dark:text-gray-200">
                    <thead class="bg-gray-100 dark:bg-gray-700">
                        <tr>
                            <th class="px-4 py-3 text-left font-bold uppercase tracking-wider">Tipo Documento</th>
                            <th class="px-4 py-3 text-left font-bold uppercase tracking-wider">Documento</th>
                            <th class="px-4 py-3 text-left font-bold uppercase tracking-wider">Nombres</th>
                            <th class="px-4 py-3 text-left font-bold uppercase tracking-wider">Apellidos</th>
                            <th class="px-4 py-3 text-left font-bold uppercase tracking-wider">Teléfono</th>
                            <th class="px-4 py-3 text-center font-bold uppercase tracking-wider">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                        {{-- Las filas se cargan aquí con DataTables --}}
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
                <button type="button" class="p-2 -mr-2 rounded-full text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 transition" id="btnCerrarTop">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                </button>
            </h3>

            <form id="formContribuyente" class="space-y-8" novalidate>
                @csrf
                <input type="hidden" id="contribuyenteId" name="contribuyenteId">

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    
                    {{-- Tipo Documento --}}
                    <div>
                        <label for="tipo_documento" class="block text-sm text-gray-700 dark:text-gray-300 font-semibold mb-1">Tipo Documento <span class="text-red-500">*</span></label>
                        <select id="tipo_documento" name="tipo_documento"
                            class="mt-1 w-full border border-gray-300 dark:border-gray-600 rounded-xl px-4 py-2.5 bg-white dark:bg-gray-700 dark:text-gray-100 focus:ring-indigo-500 focus:border-indigo-500 transition duration-150 shadow-sm disabled:bg-gray-100 dark:disabled:bg-gray-700/80 disabled:text-gray-500 dark:disabled:text-gray-400">
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
                            class="mt-1 w-full border border-gray-300 dark:border-gray-600 rounded-xl px-4 py-2.5 bg-white dark:bg-gray-700 dark:text-gray-100 focus:ring-indigo-500 focus:border-indigo-500 transition duration-150 shadow-sm disabled:bg-gray-100 dark:disabled:bg-gray-700/80" placeholder="ID o RUC">
                        <span class="text-red-500 text-xs mt-1 error-text block" id="error_documento"></span>
                    </div>

                    {{-- Nombres --}}
                    <div>
                        <label for="nombres" class="block text-sm text-gray-700 dark:text-gray-300 font-semibold mb-1">Nombres <span class="text-red-500">*</span></label>
                        <input id="nombres" name="nombres" type="text"
                            class="mt-1 w-full border border-gray-300 dark:border-gray-600 rounded-xl px-4 py-2.5 bg-white dark:bg-gray-700 dark:text-gray-100 focus:ring-indigo-500 focus:border-indigo-500 transition duration-150 shadow-sm disabled:bg-gray-100 dark:disabled:bg-gray-700/80" placeholder="Nombre(s)">
                        <span class="text-red-500 text-xs mt-1 error-text block" id="error_nombres"></span>
                    </div>

                    {{-- Apellidos --}}
                    <div>
                        <label for="apellidos" class="block text-sm text-gray-700 dark:text-gray-300 font-semibold mb-1">Apellidos <span class="text-red-500">*</span></label>
                        <input id="apellidos" name="apellidos" type="text"
                            class="mt-1 w-full border border-gray-300 dark:border-gray-600 rounded-xl px-4 py-2.5 bg-white dark:bg-gray-700 dark:text-gray-100 focus:ring-indigo-500 focus:border-indigo-500 transition duration-150 shadow-sm disabled:bg-gray-100 dark:disabled:bg-gray-700/80" placeholder="Apellido(s)">
                        <span class="text-red-500 text-xs mt-1 error-text block" id="error_apellidos"></span>
                    </div>

                    {{-- Nombre Completo --}}
                    <div>
                        <label for="nombre_completo" class="block text-sm text-gray-700 dark:text-gray-300 font-semibold mb-1">Nombre Completo <span class="text-red-500">*</span></label>
                        <input id="nombre_completo" name="nombre_completo" type="text"
                            class="mt-1 w-full border border-gray-300 dark:border-gray-600 rounded-xl px-4 py-2.5 bg-white dark:bg-gray-700 dark:text-gray-100 focus:ring-indigo-500 focus:border-indigo-500 transition duration-150 shadow-sm disabled:bg-gray-100 dark:disabled:bg-gray-700/80" placeholder="Nombre completo">
                        <span class="text-red-500 text-xs mt-1 error-text block" id="error_nombre_completo"></span>
                    </div>

                    {{-- Celular --}}
                    <div>
                        <label for="celular" class="block text-sm text-gray-700 dark:text-gray-300 font-semibold mb-1">Celular</label>
                        <input id="celular" name="celular" type="text" class="mt-1 w-full border border-gray-300 dark:border-gray-600 rounded-xl px-4 py-2.5 bg-white dark:bg-gray-700 dark:text-gray-100 focus:ring-indigo-500 focus:border-indigo-500 transition duration-150 shadow-sm disabled:bg-gray-100 dark:disabled:bg-gray-700/80" placeholder="Ej: +57 300 123 4567">
                        <span class="text-red-500 text-xs mt-1 error-text block" id="error_celular"></span>
                    </div>

                    {{-- Email --}}
                    <div>
                        <label for="email" class="block text-sm text-gray-700 dark:text-gray-300 font-semibold mb-1">Email <span class="text-red-500">*</span></label>
                        <input id="email" name="email" type="email" class="mt-1 w-full border border-gray-300 dark:border-gray-600 rounded-xl px-4 py-2.5 bg-white dark:bg-gray-700 dark:text-gray-100 focus:ring-indigo-500 focus:border-indigo-500 transition duration-150 shadow-sm disabled:bg-gray-100 dark:disabled:bg-gray-700/80" placeholder="contacto@ejemplo.com">
                        <span class="text-red-500 text-xs mt-1 error-text block" id="error_email"></span>
                    </div>

                    {{-- Usuario --}}
                    <div>
                        <label for="usuario" class="block text-sm text-gray-700 dark:text-gray-300 font-semibold mb-1">Usuario</label>
                        <input id="usuario" name="usuario" type="text" class="mt-1 w-full border border-gray-300 dark:border-gray-600 rounded-xl px-4 py-2.5 bg-white dark:bg-gray-700 dark:text-gray-100 focus:ring-indigo-500 focus:border-indigo-500 transition duration-150 shadow-sm disabled:bg-gray-100 dark:disabled:bg-gray-700/80" placeholder="Nombre de usuario (opcional)">
                        <span class="text-red-500 text-xs mt-1 error-text block" id="error_usuario"></span>
                    </div>

                    {{-- Dirección --}}
                    <div>
                        <label for="direccion" class="block text-sm text-gray-700 dark:text-gray-300 font-semibold mb-1">Dirección</label>
                        <input id="direccion" name="direccion" type="text" class="mt-1 w-full border border-gray-300 dark:border-gray-600 rounded-xl px-4 py-2.5 bg-white dark:bg-gray-700 dark:text-gray-100 focus:ring-indigo-500 focus:border-indigo-500 transition duration-150 shadow-sm disabled:bg-gray-100 dark:disabled:bg-gray-700/80" placeholder="Calle/Carrera/Piso/Oficina">
                        <span class="text-red-500 text-xs mt-1 error-text block" id="error_direccion"></span>
                    </div>

                    {{-- Teléfono (Fijo) --}}
                    <div>
                        <label for="telefono" class="block text-sm text-gray-700 dark:text-gray-300 font-semibold mb-1">Teléfono (Fijo)</label>
                        <input id="telefono" name="telefono" type="text"
                            class="mt-1 w-full border border-gray-300 dark:border-gray-600 rounded-xl px-4 py-2.5 bg-white dark:bg-gray-700 dark:text-gray-100 focus:ring-indigo-500 focus:border-indigo-500 transition duration-150 shadow-sm disabled:bg-gray-100 dark:disabled:bg-gray-700/80" placeholder="Ej: 601 555-1234">
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
                        <h4 class="font-bold text-sm text-gray-700 dark:text-gray-300 mb-2 flex items-center space-x-2">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-indigo-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M7 12l2-2 2 2m4-4H9m0 4h6m-4-8h4m-8 4v8m8-4v4m0 0H7m4-4h2m4-4h2m-2 4h2m-2-4h2m-2 4h2m-2 4h2m-2-4h2m-2 4h2m-2-4h2m-2 4h2m-2-4h2m-2 4h2m-2-4h2m-2 4h2m-2-4h2m-2 4h2m-2-4h2m-2 4h2m-2-4h2m-2 4h2m-2-4h2m-2 4h2m-2-4h2m-2 4h2m-2-4h2m-2 4h2m-2-4h2m-2 4h2m-2-4h2m-2 4h2m-2-4h2m-2 4h2m-2-4h2m-2 4h2m-2-4h2m-2 4h2m-2-4h2m-2 4h2m-2-4h2m-2 4h2m-2-4h2m-2 4h2m-2-4h2m-2 4h2m-2-4h2m-2 4h2m-2-4h2m-2 4h2m-2-4h2m-2 4h2m-2-4h2m-2 4h2m-2-4h2m-2 4h2m-2-4h2m-2 4h2m-2-4h2m-2 4h2m-2-4h2m-2 4h2m-2-4h2m-2 4h2m-2-4h2m-2 4h2m-2-4h2m-2 4h2m-2-4h2m-2 4h2m-2-4h2m-2 4h2m-2-4h2m-2 4h2m-2-4h2m-2 4h2m-2-4h2m-2 4h2M12 4v16M4 8h16M4 16h16"></path></svg>
                            <span>Frecuencia de Letras en Nombres/Apellidos</span>
                        </h4>
                        <pre id="frecuenciaLetras" class="text-sm bg-gray-50 dark:bg-gray-900 dark:text-green-400 text-gray-800 p-4 rounded-xl overflow-auto max-h-48 shadow-inner border border-gray-200 dark:border-gray-700/80 transition duration-150">La frecuencia de letras se mostrará al ver/editar un contribuyente existente o al guardar uno nuevo.</pre>
                    </div>
                </div>

                <hr class="my-6 border-gray-200 dark:border-gray-700">

                {{-- Botones de Acción --}}
                <div class="flex justify-end gap-4 pt-4">
                    <button type="button" id="btnCerrar" class="flex items-center space-x-2 bg-gray-200 dark:bg-gray-700 hover:bg-gray-300 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-200 px-6 py-2.5 rounded-xl transition transform hover:scale-[1.02] active:scale-[0.98] shadow-md font-semibold ring-1 ring-gray-300 dark:ring-gray-600">
                        <span>Cerrar</span>
                    </button>
                    @if(Auth::check() && Auth::user()->role_id == 2)
                        <button type="submit" id="btnGuardar" class="flex items-center space-x-2 bg-indigo-600 hover:bg-indigo-700 text-white px-6 py-2.5 rounded-xl transition transform hover:scale-[1.02] active:scale-[0.98] shadow-xl shadow-indigo-500/50 font-bold ring-2 ring-indigo-300 dark:ring-indigo-700">
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
            <h4 class="text-2xl font-bold mb-4 text-gray-800 dark:text-gray-200 flex items-center space-x-2">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-yellow-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.332 16c-.77 1.333.192 3 1.732 3z" /></svg>
                <span>Confirmar Eliminación</span>
            </h4>
            <p class="mb-8 text-gray-700 dark:text-gray-300">¿Estás seguro de eliminar este contribuyente? **Esta acción no se puede deshacer**.</p>
            <div class="flex justify-end gap-3">
                <button type="button" id="btnCancelarEliminar" class="bg-gray-500 hover:bg-gray-600 text-white px-5 py-2 rounded-xl transition transform active:scale-[0.98] font-semibold shadow-md">Cancelar</button>
                <button type="button" id="btnConfirmarEliminar" class="bg-red-600 hover:bg-red-700 text-white px-5 py-2 rounded-xl transition transform active:scale-[0.98] font-semibold shadow-md shadow-red-500/30">Eliminar</button>
            </div>
        </div>
    </div>

    {{-- 5. Lógica JavaScript (Solo se ajusta la función render de DataTables para usar los nuevos botones) --}}
    <script>
    let tabla;
    const userRoleId = {{ Auth::check() ? Auth::user()->role_id : 0 }};
    const canEdit = (userRoleId === 2);
    let contribuyenteIdToDelete = null;

    function isValidEmail(email) {
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/; 
        return emailRegex.test(email);
    }

    function aplicarLogicaNombres() {
        const tipoDocumento = $('#tipo_documento').val();
        const nombresInput = $('#nombres');
        const apellidosInput = $('#apellidos');
        const nombreCompletoInput = $('#nombre_completo');

        // Resetear
        nombresInput.removeClass('is-readonly').prop('readonly', false);
        apellidosInput.removeClass('is-readonly').prop('readonly', false);
        nombreCompletoInput.removeClass('is-readonly').prop('readonly', false);

        if (tipoDocumento === 'NIT') {
            const razonSocial = nombreCompletoInput.val().trim();
            const palabras = razonSocial.split(/\s+/).filter(p => p.length > 0);
            
            let nombres = '';
            let apellidos = '';

            if (palabras.length >= 1) {
                nombres = palabras[0];
                apellidos = palabras.slice(1).join(' ');
            }
            
            nombresInput.val(nombres);
            apellidosInput.val(apellidos);
            
            // Aplicar readonly
            nombresInput.addClass('is-readonly').prop('readonly', true);  
            apellidosInput.addClass('is-readonly').prop('readonly', true); 

        } else if (tipoDocumento === 'CC' || tipoDocumento === 'Pasaporte') {
            
            const nombres = nombresInput.val().trim();
            const apellidos = apellidosInput.val().trim();
            
            nombreCompletoInput.val(`${nombres} ${apellidos}`.trim());
            
            // Aplicar readonly
            nombreCompletoInput.addClass('is-readonly').prop('readonly', true);
        }
    }

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
        const isFormEditable = isEdit && canEdit;
        
        // Deshabilitar/Habilitar todos los campos del formulario (excepto los de auditoría)
        $('#formContribuyente input:not(#created_at, #updated_at), #formContribuyente select').prop('disabled', !isFormEditable);
        
        // Si el formulario es editable, re-aplicamos la lógica de nombres para manejar NIT/CC
        if (isFormEditable) {
            aplicarLogicaNombres(); 
        } else {
            // Si es solo para ver, aseguramos que todos los campos relevantes sean readonly.
            $('#nombres, #apellidos, #nombre_completo').addClass('is-readonly').prop('readonly', true);
        }

        // Mostrar/Ocultar botón Guardar
        $('#btnGuardar').toggle(isEdit && canEdit);
        
        let title = 'Detalle Contribuyente';
        if (isEdit) title = 'Editar Contribuyente';
        else if (!isView) title = 'Crear Contribuyente'; 
        $('#modalTitle').text(title);
    }

    $(document).ready(function() {

        tabla = $('#tablaContribuyentes').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: '{{ route("contribuyentes.data") }}',
                data: function(d) {
                    d.nombres = $('#tablaContribuyentes_filter input').val();
                }
            },
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
                        let btns = `
                            <div class="flex justify-center space-x-2">
                                {{-- Botón Ver --}}
                                <button type="button" title="Ver Detalle" class='btnView text-gray-500 dark:text-gray-400 hover:text-indigo-600 dark:hover:text-indigo-400 transition transform hover:scale-110 active:scale-95 p-1 rounded-full' data-id='${data}'>
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /><path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" /></svg>
                                </button>
                        `;

                        if (canEdit) {
                            btns += `
                                {{-- Botón Editar --}}
                                <button type="button" title="Editar" class='btnEdit text-indigo-600 dark:text-indigo-500 hover:text-indigo-800 dark:hover:text-indigo-300 transition transform hover:scale-110 active:scale-95 p-1 rounded-full' data-id='${data}'>
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-9-4l6 6m2-2l4-4a2 2 0 000-2.828l-2.828-2.828a2 2 0 00-2.828 0l-4 4zm-1 5H8v-2.5l6-6 2.5 2.5-6 6z" /></svg>
                                </button>
                                {{-- Botón Eliminar --}}
                                <button type="button" title="Eliminar" class='btnDelete text-red-600 dark:text-red-500 hover:text-red-800 dark:hover:text-red-300 transition transform hover:scale-110 active:scale-95 p-1 rounded-full' data-id='${data}'>
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                                </button>
                            `;
                        }
                        
                        btns += `</div>`;
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

        // Eventos de Lógica (Se mantienen)
        $('#tipo_documento').on('change', function() {
            $('#nombre_completo').val('');
            $('#nombres').val('');
            $('#apellidos').val('');
            aplicarLogicaNombres(); 
        });
        $('#nombres, #apellidos, #nombre_completo').on('input', aplicarLogicaNombres);

        // Manejo de Modales (Crear)
        $('#btnCrear').click(function() {
            $('#formContribuyente')[0].reset();
            $('#contribuyenteId').val('');
            $('#frecuenciaLetras').text('La frecuencia de letras se mostrará al editar o ver el contribuyente.');
            $('#created_at, #updated_at').val('N/A');
            limpiarErrores();
            setModalState(true, false); 
            $('#btnGuardar').html('<svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor"><path d="M7.707 10.293a1 1 0 10-1.414 1.414L8.586 14 14.293 8.293a1 1 0 00-1.414-1.414L8.586 11.414z" /></svg><span>Crear</span>');
            $('#modalContribuyente').removeClass('hidden');
            aplicarLogicaNombres(); 
        });

        // Botones de cierre
        $('#btnCerrar, #btnCerrarTop').click(() => {
            $('#modalContribuyente').addClass('hidden');
        });

        // Manejo de Ver y Editar
        $('#tablaContribuyentes').on('click', '.btnView, .btnEdit', function() {
            const id = $(this).data('id');
            const isEdit = $(this).hasClass('btnEdit') && canEdit;
            const isView = $(this).hasClass('btnView');

            setModalState(isEdit, isView);
            limpiarErrores();
            $('#btnGuardar').html('<svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor"><path d="M7.707 10.293a1 1 0 10-1.414 1.414L8.586 14 14.293 8.293a1 1 0 00-1.414-1.414L8.586 11.414z" /></svg><span>Guardar</span>');

            $.get(`/contribuyentes/${id}`, function(res) {
                const contribuyente = res.contribuyente;
                $('#contribuyenteId').val(contribuyente.id);
                $('#tipo_documento').val(contribuyente.tipo_documento);
                $('#documento').val(contribuyente.documento);
                $('#nombres').val(contribuyente.nombres);
                $('#apellidos').val(contribuyente.apellidos);
                $('#nombre_completo').val(contribuyente.nombre_completo);
                $('#celular').val(contribuyente.celular); 
                $('#email').val(contribuyente.email);
                $('#usuario').val(contribuyente.usuario); 
                $('#direccion').val(contribuyente.direccion); 
                $('#telefono').val(contribuyente.telefono);
                
                $('#created_at').val(formatReadableDate(contribuyente.created_at));
                $('#updated_at').val(formatReadableDate(contribuyente.updated_at));
                
                $('#frecuenciaLetras').text(JSON.stringify(res.frecuencia, null, 2));

                $('#modalContribuyente').removeClass('hidden');
                
                // CRUCIAL: Aplicar la lógica después de cargar los datos
                aplicarLogicaNombres(); 
            }).fail((err) => {
                console.error('Error al obtener contribuyente:', err);
            });
        });

        // Manejo de Guardado/Actualización (Formulario)
        $('#formContribuyente').submit(function(e) {
            e.preventDefault();
            if (!canEdit) return; 

            aplicarLogicaNombres(); 

            const email = $('#email').val().trim();
            limpiarErrores();
            
            if (!isValidEmail(email)) {
                $('#error_email').text('El formato del correo electrónico no es válido.');
                $('#btnGuardar').prop('disabled', false).html(`<span>${$('#contribuyenteId').val() ? 'Guardar' : 'Crear'}</span>`); 
                return;
            }
            
            const id = $('#contribuyenteId').val();
            const url = id ? `/contribuyentes/${id}` : '{{ route("contribuyentes.store") }}';
            const method = id ? 'PUT' : 'POST';
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

        // Manejo de Eliminación (Se mantiene)
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