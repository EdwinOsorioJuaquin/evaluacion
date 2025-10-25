<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Pregunta - Evaluación Docente</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        :root {
            --primary: #3498db;
            --secondary: #2c3e50;
            --success: #27ae60;
            --warning: #f39c12;
            --danger: #e74c3c;
            --light: #ecf0f1;
            --dark: #34495e;
        }
        
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            min-height: 100vh;
            padding: 20px;
        }
        
        .container {
            max-width: 800px;
            background: white;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.2);
            padding: 30px;
            margin-top: 20px;
        }
        
        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 2px solid var(--light);
            padding-bottom: 20px;
        }
        
        .header h2 {
            color: var(--secondary);
            font-weight: 700;
            margin-bottom: 5px;
        }
        
        .header p {
            color: #7f8c8d;
            font-size: 1.1rem;
        }
        
        .form-group {
            margin-bottom: 25px;
        }
        
        .form-label {
            font-weight: 600;
            color: var(--dark);
            margin-bottom: 8px;
        }
        
        .btn-container {
            display: flex;
            justify-content: space-between;
            margin-top: 30px;
            padding-top: 20px;
            border-top: 2px solid var(--light);
        }
        
        .preview {
            background: linear-gradient(135deg, var(--light) 0%, #ffffff 100%);
            border-radius: 10px;
            padding: 20px;
            margin-top: 25px;
            border-left: 4px solid var(--primary);
        }
        
        .preview h5 {
            color: var(--secondary);
            font-weight: 600;
            margin-bottom: 15px;
        }
        
        .btn-primary {
            background: var(--primary);
            border: none;
            padding: 10px 25px;
            font-weight: 600;
        }
        
        .btn-primary:hover {
            background: #2980b9;
            transform: translateY(-2px);
            transition: all 0.3s;
        }
        
        .btn-danger {
            background: var(--danger);
            border: none;
            padding: 10px 25px;
            font-weight: 600;
        }
        
        .btn-danger:hover {
            background: #c0392b;
            transform: translateY(-2px);
            transition: all 0.3s;
        }
        
        .btn-secondary {
            background: var(--secondary);
            border: none;
            padding: 10px 25px;
            font-weight: 600;
        }
        
        .btn-secondary:hover {
            background: #2c3e50;
            transform: translateY(-2px);
            transition: all 0.3s;
        }
        
        .form-control:focus {
            border-color: var(--primary);
            box-shadow: 0 0 0 0.2rem rgba(52, 152, 219, 0.25);
        }
        
        .form-select:focus {
            border-color: var(--primary);
            box-shadow: 0 0 0 0.2rem rgba(52, 152, 219, 0.25);
        }
        
        .status-badge {
            background: var(--success);
            color: white;
            padding: 5px 15px;
            border-radius: 20px;
            font-size: 0.9rem;
            font-weight: 600;
        }
        
        .opcion-item {
            margin-bottom: 10px;
        }
        
        .alert-custom {
            background: var(--warning);
            color: white;
            border: none;
            border-radius: 10px;
            font-weight: 600;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h2>✏️ Editar Pregunta</h2>
            <p>Modifica la pregunta y guarda los cambios</p>
            <div class="status-badge">Pregunta Activa</div>
        </div>

        <!-- Alerta de éxito (oculta inicialmente) -->
        <div class="alert alert-success alert-dismissible fade show d-none" role="alert" id="successAlert">
            <strong>¡Éxito!</strong> Los cambios se han guardado correctamente.
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>

        <form id="editQuestionForm">
            <!-- Texto de la pregunta -->
            <div class="form-group">
                <label for="pregunta" class="form-label">Texto de la Pregunta</label>
                <textarea class="form-control" id="pregunta" rows="3" placeholder="Escribe la pregunta aquí">¿El docente explica los temas con claridad y responde adecuadamente a las dudas?</textarea>
            </div>

            <!-- Tipo de pregunta -->
            <div class="form-group">
                <label for="tipo" class="form-label">Tipo de Pregunta</label>
                <select class="form-select" id="tipo">
                    <option value="opcion-multiple" selected>Opción Múltiple</option>
                    <option value="seleccion-unica">Selección Única</option>
                    <option value="texto-libre">Texto Libre</option>
                    <option value="escala">Escala Numérica</option>
                </select>
            </div>

            <!-- Opciones de respuesta -->
            <div class="form-group" id="opciones-container">
                <label class="form-label">Opciones de Respuesta</label>
                <div class="opcion-item">
                    <input type="text" class="form-control" value="Siempre" id="opcion1">
                </div>
                <div class="opcion-item">
                    <input type="text" class="form-control" value="Frecuentemente" id="opcion2">
                </div>
                <div class="opcion-item">
                    <input type="text" class="form-control" value="Ocasionalmente" id="opcion3">
                </div>
                <div class="opcion-item">
                    <input type="text" class="form-control" value="Nunca" id="opcion4">
                </div>
                <button type="button" class="btn btn-outline-primary mt-2" id="addOption">
                    + Agregar Opción
                </button>
            </div>

            <!-- Configuraciones adicionales -->
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" id="requerida" checked>
                            <label class="form-check-label" for="requerida">
                                Pregunta requerida
                            </label>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" id="activa" checked>
                            <label class="form-check-label" for="activa">
                                Pregunta activa
                            </label>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Botones de acción -->
            <div class="btn-container">
                <button type="button" class="btn btn-secondary" id="cancelBtn">
                    ← Cancelar
                </button>
                <div>
                    <button type="button" class="btn btn-danger" id="deleteBtn">
                        🗑️ Eliminar
                    </button>
                    <button type="submit" class="btn btn-primary" id="saveBtn">
                        💾 Guardar Cambios
                    </button>
                </div>
            </div>
        </form>

        <!-- Vista previa -->
        <div class="preview">
            <h5>👁️ Vista Previa de la Pregunta:</h5>
            <p><strong id="previewText">¿El docente explica los temas con claridad y responde adecuadamente a las dudas?</strong></p>
            <div class="form-check">
                <input class="form-check-input" type="radio" name="previewOption" id="preview1">
                <label class="form-check-label" for="preview1" id="previewLabel1">Siempre</label>
            </div>
            <div class="form-check">
                <input class="form-check-input" type="radio" name="previewOption" id="preview2">
                <label class="form-check-label" for="preview2" id="previewLabel2">Frecuentemente</label>
            </div>
            <div class="form-check">
                <input class="form-check-input" type="radio" name="previewOption" id="preview3">
                <label class="form-check-label" for="preview3" id="previewLabel3">Ocasionalmente</label>
            </div>
            <div class="form-check">
                <input class="form-check-input" type="radio" name="previewOption" id="preview4">
                <label class="form-check-label" for="preview4" id="previewLabel4">Nunca</label>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Elementos del DOM
        const form = document.getElementById('editQuestionForm');
        const preguntaInput = document.getElementById('pregunta');
        const previewText = document.getElementById('previewText');
        const cancelBtn = document.getElementById('cancelBtn');
        const deleteBtn = document.getElementById('deleteBtn');
        const saveBtn = document.getElementById('saveBtn');
        const addOptionBtn = document.getElementById('addOption');
        const successAlert = document.getElementById('successAlert');
        
        // Actualizar vista previa cuando cambia el texto de la pregunta
        preguntaInput.addEventListener('input', function() {
            previewText.textContent = this.value || '¿El docente explica los temas con claridad?';
        });
        
        // Actualizar opciones en vista previa
        document.getElementById('opcion1').addEventListener('input', function() {
            document.getElementById('previewLabel1').textContent = this.value;
        });
        
        document.getElementById('opcion2').addEventListener('input', function() {
            document.getElementById('previewLabel2').textContent = this.value;
        });
        
        document.getElementById('opcion3').addEventListener('input', function() {
            document.getElementById('previewLabel3').textContent = this.value;
        });
        
        document.getElementById('opcion4').addEventListener('input', function() {
            document.getElementById('previewLabel4').textContent = this.value;
        });
        
        // Agregar nueva opción
        addOptionBtn.addEventListener('click', function() {
            const opcionesContainer = document.getElementById('opciones-container');
            const newOptionIndex = opcionesContainer.querySelectorAll('.opcion-item').length + 1;
            
            const newOption = document.createElement('div');
            newOption.className = 'opcion-item';
            newOption.innerHTML = `
                <input type="text" class="form-control" placeholder="Nueva opción ${newOptionIndex}" id="opcion${newOptionIndex}">
            `;
            
            opcionesContainer.insertBefore(newOption, addOptionBtn);
            
            // Agregar evento para actualizar vista previa
            newOption.querySelector('input').addEventListener('input', function() {
                // En una implementación real, aquí agregarías la nueva opción a la vista previa
                console.log('Nueva opción:', this.value);
            });
        });
        
        // Mostrar/ocultar opciones según el tipo de pregunta
        document.getElementById('tipo').addEventListener('change', function() {
            const opcionesContainer = document.getElementById('opciones-container');
            if (this.value === 'texto-libre') {
                opcionesContainer.style.display = 'none';
            } else {
                opcionesContainer.style.display = 'block';
            }
        });
        
        // Funcionalidad de los botones
        cancelBtn.addEventListener('click', function() {
            if (confirm('¿Estás seguro de que quieres cancelar? Los cambios no guardados se perderán.')) {
                alert('Edición cancelada');
                // En una aplicación real, aquí redirigirías a la página anterior
            }
        });
        
        deleteBtn.addEventListener('click', function() {
            if (confirm('¿Estás seguro de que quieres eliminar esta pregunta? Esta acción no se puede deshacer.')) {
                alert('Pregunta eliminada');
                // En una aplicación real, aquí enviarías una solicitud para eliminar la pregunta
            }
        });
        
        // Guardar cambios
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            
            // Mostrar animación de carga en el botón
            const originalText = saveBtn.innerHTML;
            saveBtn.innerHTML = '⌛ Guardando...';
            saveBtn.disabled = true;
            
            // Simular guardado (en una aplicación real, aquí enviarías los datos al servidor)
            setTimeout(function() {
                // Restaurar botón
                saveBtn.innerHTML = originalText;
                saveBtn.disabled = false;
                
                // Mostrar alerta de éxito
                successAlert.classList.remove('d-none');
                
                // Ocultar alerta después de 3 segundos
                setTimeout(function() {
                    successAlert.classList.add('d-none');
                }, 3000);
                
                console.log('Pregunta guardada:', {
                    texto: preguntaInput.value,
                    tipo: document.getElementById('tipo').value,
                    requerida: document.getElementById('requerida').checked,
                    activa: document.getElementById('activa').checked
                });
            }, 1500);
        });
    </script>
</body>
</html>