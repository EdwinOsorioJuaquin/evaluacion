<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Reporte de Auditoría - {{ $audit->objective }}</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <style>
    /* ========== CONFIGURACIÓN DE PÁGINA PARA DOMPDF ========== */
    @page {
      margin: 80px 30px 70px 30px;
    }

    /* Header fijo para todas las páginas */
    .header {
      position: fixed;
      top: -60px;
      left: 0;
      right: 0;
      height: 60px;
      background: #1e3a8a;
      color: #ffffff;
      padding: 8px 20px;
      border-bottom: 3px solid #3b82f6;
    }

    /* Footer fijo para todas las páginas */
    .footer {
      position: fixed;
      bottom: -40px;
      left: 0;
      right: 0;
      height: 40px;
      background: #f1f5f9;
      border-top: 2px solid #e2e8f0;
      padding: 8px 20px;
      font-size: 10px;
      color: #475569;
    }

    .footer .page-number:before {
    content: counter(page);
}

.footer .page-count:before {
    content: counter(pages);
}

    body {
      font-family: DejaVu Sans, Arial, sans-serif;
      font-size: 12px;
      color: #334155;
      background-color: #ffffff;
      line-height: 1.4;
      font-weight: 400;
      margin: 0;
      padding: 0;
    }

    /* ================= CONTENIDO PRINCIPAL ================= */
    .content {
      padding: 10px 0;
    }

    /* ================= TITULARES ================= */
    h2.section-title {
      background: #eff6ff;
      border-left: 5px solid #2563eb;
      color: #1e40af;
      padding: 10px 14px;
      border-radius: 4px;
      margin-top: 25px;
      margin-bottom: 15px;
      font-size: 15px;
      font-weight: 600;
    }

    /* ================= TABLAS ================= */
    table {
      width: 100%;
      border-collapse: collapse;
      margin-bottom: 12px;
      page-break-inside: auto;
    }

    .kv td, .tbl th, .tbl td {
      border: 1px solid #d1d5db;
      padding: 8px 10px;
      vertical-align: top;
    }

    .kv td.k {
      width: 22%;
      background: #f8fafc;
      font-weight: 600;
      color: #374151;
    }

    .tbl thead th {
      background: #f8fafc;
      color: #1f2937;
      font-weight: 600;
      border-bottom: 2px solid #9ca3af;
      font-size: 11px;
    }

    /* ================= CAJAS / BLOQUES ================= */
    .box {
      border: 1px solid #d1d5db;
      border-radius: 6px;
      padding: 15px;
      margin-top: 18px;
      background: #ffffff;
      page-break-inside: avoid;
    }

    /* ================= ETIQUETAS ================= */
    .chip {
      display: inline-block;
      border-radius: 12px;
      padding: 4px 10px;
      font-size: 9px;
      font-weight: 600;
      border: 1px solid transparent;
      text-transform: uppercase;
    }

    .chip-planned { background: #fef3c7; color: #92400e; border-color: #f59e0b; }
    .chip-inprog  { background: #dbeafe; color: #1e40af; border-color: #3b82f6; }
    .chip-compl   { background: #dcfce7; color: #166534; border-color: #16a34a; }
    .chip-cancel  { background: #fee2e2; color: #991b1b; border-color: #ef4444; }

    .sev-low    { background: #dcfce7; color: #15803d; border-color: #22c55e; }
    .sev-medium { background: #fef3c7; color: #92400e; border-color: #f59e0b; }
    .sev-high   { background: #fee2e2; color: #b91c1b; border-color: #ef4444; }

    /* ================= EVIDENCIAS ================= */
    .evidence img {
      max-width: 120px;
      border: 1px solid #d1d5db;
      border-radius: 4px;
      margin-top: 4px;
    }

    /* ================= FIRMAS ================= */
    .sign-grid {
      width: 100%;
      margin-top: 25px;
      border-collapse: collapse;
    }
    .sign-cell {
      border: 1px solid #d1d5db;
      border-radius: 6px;
      height: 90px;
      background: #f8fafc;
      text-align: center;
      vertical-align: bottom;
      padding: 12px;
    }
    .sign-line {
      border-top: 1px solid #94a3b8;
      font-size: 12px;
      color: #1e293b;
      padding-top: 8px;
      font-weight: 600;
    }
    .sign-role {
      font-size: 10px;
      color: #64748b;
      margin-top: 4px;
    }

    /* ================= UTILIDADES ================= */
    .muted { color: #6b7280; }
    .small { font-size: 10px; }
    .xs { font-size: 9px; }
    .page-break { page-break-after: always; }
    
    /* Filas alternas para mejor legibilidad */
    .kv tr:nth-child(even) {
      background-color: #f9fafb;
    }
    
    .tbl tbody tr:nth-child(even) {
      background-color: #f9fafb;
    }
    
    /* Separadores visuales */
    .section-divider {
      height: 1px;
      background: #e5e7eb;
      margin: 18px 0;
    }
    
    /* Numeración de páginas */
    .page-number {
      text-align: center;
      font-size: 10px;
      color: #64748b;
      margin-top: 5px;
    }
    
    /* Header y footer content */
    .header-table, .footer-table {
      width: 100%;
      border-collapse: collapse;
    }
    .header-table td, .footer-table td {
      border: none;
      vertical-align: middle;
    }
    .header-logo { height: 35px; }
    
    .header-title {
      font-size: 14px;
      font-weight: 700;
      text-align: center;
    }
    .header-sub {
      font-size: 9px;
      color: #e2e8f0;
      text-align: center;
    }
    
    .header-info {
      text-align: right;
      font-size: 9px;
      line-height: 1.3;
    }
  </style>
</head>
<body>

  <!-- Header fijo -->
  <div class="header">
    <table class="header-table">
      <tr>
        <td style="width: 20%; text-align: left;">
          <img src="data:image/png;base64,{{ $logo }}" alt="Logo" class="header-logo">
        </td>
        <td style="width: 60%; text-align: center;">
          <div class="header-title">INCADEV — INFORME DE AUDITORÍA</div>
          <div class="header-sub">Instituto de Capacitación y Desarrollo Virtual</div>
        </td>
        <td style="width: 20%; text-align: right;" class="header-info">
          <div><strong>Versión:</strong> 1.0</div>
          <div><strong>Código:</strong> CA{{ $audit->id }}</div>
          <div class="">Generado: {{ now()->format('d/m/Y') }}</div>
        </td>
      </tr>
    </table>
  </div>

  <!-- Footer fijo -->
  <div class="footer">
    <table class="footer-table">
      <tr>
        <td style="width: 33%; text-align: left;" class="xs">
          INCADEV • Informe de Auditoría
        </td>
        <td style="width: 34%; text-align: center;" class="xs">
          CA{{ $audit->id }} • {{ $audit->objective }}
        </td>
         <td class="footer-right" style="width: 33%; text-align: right;" class="xs">
  <script type="text/php" >
    if (isset($pdf)) {
      $text = "Página {PAGE_NUM} de {PAGE_COUNT}";
      $size = 8;
      $font = $fontMetrics->getFont("DejaVu Sans");
      $width = $fontMetrics->get_text_width($text, $font, $size);
      $x = $pdf->get_width() - $width - 2; // 2px desde el borde derecho
      $y = $pdf->get_height() - 58;
      $pdf->page_text($x, $y, $text, $font, $size, '#334155');
    }
  </script>
</td>
      </tr>
    </table>
  </div>

  <!-- Contenido principal -->
  <div class="content">
    <!-- Información General -->
    <h2 class="section-title">Información General</h2>
    <table class="kv">
      <tr>
        <td class="k">Nombre de la auditoría</td>
        <td colspan="3">{{ $audit->objective }}</td>
      </tr>
      <tr>
        <td class="k">Área</td>
        <td>{{ $audit->area }}</td>
        <td class="k">Tipo</td>
        <td>{{ ucfirst($audit->type) }}</td>
      </tr>
      <tr>
        <td class="k">Estado</td>
        <td>
          @php
            $chip = 'chip';
            if($audit->state === 'planned') $chip .= ' chip-planned';
            elseif($audit->state === 'in_progress') $chip .= ' chip-inprog';
            elseif($audit->state === 'completed') $chip .= ' chip-compl';
            elseif($audit->state === 'cancelled') $chip .= ' chip-cancel';
          @endphp
          <span class="{{ $chip }}">{{ ucfirst(str_replace('_',' ', $audit->state)) }}</span>
        </td>
        <td class="k">Responsable</td>
        <td>{{ optional($audit->user)->full_name ?? optional($audit->user)->name ?? 'N/A' }}</td>
      </tr>
      <tr>
        <td class="k">Fecha de inicio</td>
        <td>{{ $audit->start_date?->format('d/m/Y') ?? '—' }}</td>
        <td class="k">Fecha de fin</td>
        <td>{{ $audit->end_date?->format('d/m/Y') ?? '—' }}</td>
      </tr>
    </table>

    <div class="section-divider"></div>

    <!-- Hallazgos -->
    <h2 class="section-title">Hallazgos</h2>

    @forelse($audit->findings as $finding)
      <div class="box">
        <table class="kv" style="margin-bottom:8px;">
          <tr>
            <td class="k">ID Hallazgo</td>
            <td><strong>#{{ $finding->id }}</strong></td>
          </tr>
          <tr>
            <td class="k">Descripción</td>
            <td>{{ $finding->description }}</td>
          </tr>
          <tr>
            <td class="k">Clasificación</td>
            <td>{{ ucfirst($finding->classification) }}</td>
          </tr>
          <tr>
            <td class="k">Severidad</td>
            <td>
              @php
                $sev = strtolower($finding->severity ?? 'low');
                $sevClass = 'chip';
                if($sev === 'low') $sevClass .= ' sev-low';
                elseif($sev === 'medium') $sevClass .= ' sev-medium';
                elseif($sev === 'high') $sevClass .= ' sev-high';
              @endphp
              <span class="{{ $sevClass }}">{{ ucfirst($finding->severity) }}</span>
            </td>
          </tr>
          <tr>
            <td class="k">Fecha de detección</td>
            <td>{{ $finding->discovery_date ? \Carbon\Carbon::parse($finding->discovery_date)->format('d/m/Y') : 'N/A' }}</td>
          </tr>
          <tr>
            <td class="k">Evidencia</td>
            <td class="evidence">
              @if ($finding->evidence)
                @php $ext = strtolower(pathinfo($finding->evidence, PATHINFO_EXTENSION)); @endphp
                @if (in_array($ext, ['jpg','jpeg','png','gif']))
                  <img src="{{ asset('storage/' . $finding->evidence) }}" alt="Evidencia">
                @else
                  <span class="small">Archivo adjunto: {{ $finding->evidence }}</span>
                @endif
              @else
                <span class="muted small">No se ha registrado evidencia.</span>
              @endif
            </td>
          </tr>
        </table>

        <!-- Acciones Correctivas -->
        @if($finding->correctiveActions->count())
          <table class="tbl small">
            <thead>
              <tr>
                <th style="width:55%;">Acción Correctiva</th>
                <th style="width:15%;">Estado</th>
                <th style="width:15%;">Compromiso</th>
                <th style="width:15%;">Vencimiento</th>
              </tr>
            </thead>
            <tbody>
              @foreach($finding->correctiveActions as $action)
                <tr>
                  <td>{{ $action->description }}</td>
                  <td>{{ ucfirst(str_replace('_',' ', $action->status)) }}</td>
                  <td>{{ \Carbon\Carbon::parse($action->engagement_date)->format('d/m/Y') }}</td>
                  <td>
                    {{ \Carbon\Carbon::parse($action->due_date)->format('d/m/Y') }}
                    @if($action->completion_date)
                      <div class="muted xs">Completada: {{ \Carbon\Carbon::parse($action->completion_date)->format('d/m/Y') }}</div>
                    @endif
                  </td>
                </tr>
              @endforeach
            </tbody>
          </table>
        @else
          <p class="small muted" style="margin-top:6px;">No se registraron acciones correctivas para este hallazgo.</p>
        @endif
      </div>
    @empty
      <p class="muted">No se encontraron hallazgos registrados para esta auditoría.</p>
    @endforelse

    <div class="section-divider"></div>

    <!-- Recomendaciones Finales -->
    <h2 class="section-title">Recomendaciones Finales</h2>
    <table class="kv">
      <tr>
        <td class="k">Resumen</td>
        <td>{{ $report->resume ?? 'No se ha registrado resumen.' }}</td>
      </tr>
      <tr>
        <td class="k">Recomendaciones</td>
        <td>{{ $report->recommendations ?? 'No se han registrado recomendaciones.' }}</td>
      </tr>
      @if(!empty($report?->indicators))
        <tr>
          <td class="k">Indicadores</td>
          <td>{{ $report->indicators }}</td>
        </tr>
      @endif
    </table>

    <div class="section-divider"></div>

    <!-- Firmas -->
    <h2 class="section-title">Firmas</h2>
    <table class="sign-grid">
      <tr>
        <td class="sign-cell" style="width:50%;">
          <div class="sign-line"><strong>{{ $audit->user->full_name ?? '____________________________' }}</strong></div>
          <div class="sign-role">Jefe de Auditoría</div>
        </td>
        <td style="width:10%; border:none;"></td>
        <td class="sign-cell" style="width:40%;">
          <div class="sign-line"><strong>{{ optional($audit->assignee)->full_name ?? optional($audit->assignee)->name ?? '____________________________' }}</strong></div>
          <div class="sign-role">Auditor Responsable</div>
        </td>
      </tr>
    </table>
  </div>

</body>
</html>